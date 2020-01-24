<?php

namespace App\Service;
use Symfony\Component\HttpClient\HttpClient;
use App\Service\ConnApi;

class QueryFlight
{
    public function __construct() {
        $this->url_base = "http://tstapi.duckdns.org/api/json/1F";
        $this->conn = new ConnApi();
    }

    public function searchFrom( )
    {
        $dep_select = array();
        $response = $this->conn->check($this->url_base."/flightroutes");
        if( $response['error'] == FALSE){
            $flightroutes = $response['content']['flightroutes'];
            foreach ($flightroutes as $key => $flight) {
                if(!in_array($flight['DepCode'], $dep_select, true)){
                    $dep_select[$flight['DepCode']] = $flight['DepName'].", ".$flight['DepCountry'];
                }
            }
            return $dep_select;
        }else{
            /** Sometimes the API Client reject the connection */
            return $dep_select = "error";
        }

    }

    public function searchTo( $depcode )
    {
        $ret_select = array();
        $departure = "departureairport=".$depcode;
        $response = $this->conn->check($this->url_base."/flightroutes/?".$departure);
        $flightroutes = $response['content']['flightroutes'];

        foreach ($flightroutes as $key => $flight) {
            if(!in_array($flight['RetCode'], $ret_select, true)){
                $ret_select[$flight['RetCode']] = $flight['RetName'].", ".$flight['RetCountry'];
            }
        }

        return $ret_select;
    }

    public function searchSchedule( $depcode, $retcode )
    {
        
        $url_final = $this->url_base."/flightschedules/?departureairport=".$depcode."&destinationairport=".$retcode."&returndepartureairport=".$retcode."&returndestinationairport=".$depcode;
        $response = $this->conn->check($url_final);

        /** OUT  -> get dates for Departures */
        $out_date = array();
        $out_schedules = $response['content']['flightschedules']['OUT'];
        foreach ($out_schedules as $key => $date) {
            $out_date[str_replace("-","",$date['date'])] = $date['date'];
        }
        $flightschedules['dep_schedule'] = $out_date;
        /** */

        /** RET  -> get dates for Returns */
        $ret_date = array();
        $ret_schedules = $response['content']['flightschedules']['RET'];
        foreach ($ret_schedules as $key => $date) {
            $ret_date[str_replace("-","",$date['date'])] = $date['date'];
        }
        $flightschedules['ret_schedule'] = $ret_date;
        /** */

        return $flightschedules;
    }

    public function searchRoute( $depcode, $retcode, $dep_date, $ret_date, $flight_type )
    {
        if($flight_type == "one_way"){
            $url_final = $this->url_base."/flightavailability?departuredate=".$dep_date."&departureairport=".$depcode."&destinationairport=".$retcode;
        }else{
            $url_final = $this->url_base."/flightavailability?departuredate=".$dep_date."&departureairport=".$depcode."&destinationairport=".$retcode."&returndepartureairport=".$retcode."&returndestinationairport=".$depcode."&returndate=".$ret_date;
        }

        $response = $this->conn->check($url_final);
        $flights = $response['content']['flights'];

        /** I left this var_dump for you infomation */
        var_dump($url_final);
        return $flights;
    }

    
}