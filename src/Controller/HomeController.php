<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\QueryFlight;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */

    public function __construct() {
        $this->search = new QueryFlight();
    }

    public function index()
    {
        $response = $this->search->searchFrom();
        if( $response != 'error'){
            return $this->render('home/index.html.twig', ['dep_pool' => $response,]);
        }else{
            /** Sometimes the API Client reject the connection */
            return $this->render('home/error.html.twig');
        }
    }

    public function flightroutesReturn()
    {
        $response = $this->search->searchTo($_POST['depcode']);
        return $this->json(['ret_pool' => $response]);
    }

    public function schedules()
    {
        $response = $this->search->searchSchedule($_POST['depcode'], $_POST['retcode']);
        return $this->json([ 'dep_schedule' => $response['dep_schedule'],  'ret_schedule' => $response['ret_schedule'] ]);
    }

    public function searchandbook()
    {
        /** Validate if return date is empty -> one_way */
        $rest_date = isset($_POST['ret_date_select']) ? $_POST['ret_date_select'] : "";
        $response = $this->search->searchRoute($_POST['dep_select'], $_POST['ret_select'], $_POST['dep_date_select'], $rest_date ,$_POST['flight_type']);
        
        /** Validate if RET exist in response -> one_way */
        $flights_ret = isset($response['RET']) ? $response['RET'] : array();

        return $this->render('home/available.html.twig', ['flights_out' => $response['OUT'], 'flights_ret' => $flights_ret , 'passangers' => $_POST['passangers'] ]);
    }


    public function bookflight()
    {
       /** I made the Flight Overview function only to show the info requiered 
        * 
        *  just sending the values directly in radio button to the controller, 
        *  this could be sended in better ways of course like a array object to the controller using form action or ajax function,
        *  calling a new Api function to get only the items selected checked with new GET paramenters, etc.
        *
        * -> date, datetime, depart_airport_name, duration, arrival_airport_name, price
        */

        $total = 0;
        $pfret = array();
        $pass = $_POST['passangers'];
        $pfdep = explode(',', $_POST['purge_flight_dep']);
        $total+= $pfdep[5];
        if(isset($_POST['purge_flight_ret'])){
            $pfret = explode(',', $_POST['purge_flight_ret']);
            $total+= $pfret[5];
        }
        $total*= $pass;
        
        return $this->render('home/resumen.html.twig', ['pfdep' => $pfdep, 
                                                        'pfret' => $pfret,
                                                        'passangers' => $pass,
                                                        'total' => $total ]);


    }

}