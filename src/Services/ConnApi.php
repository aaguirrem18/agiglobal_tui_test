<?php

namespace App\Service;
use Symfony\Component\HttpClient\HttpClient;

class ConnApi
{
    public function check(string $url): array
    {
        
        /** Tested with Postman */ 
        $client = HttpClient::create();
        try {
            $response = $client->request('GET', $url, [
                'auth_basic' => [
                    'php-applicant', 
                    'Z7VpVEQMsXk2LCBc',
                ],
            ]);

            ///all fields can be added by query parameter, for this test I preferred to pass the URL as a string
            // to make code more clean and fast coding
            $content = $response->getContent();
            $content = json_decode($content ,true);
            $status = array('error' => false,'content' => $content);
        } catch (\Throwable $th) {
            /** Return in case of an error JSON string error=true */
            $status = array('error' => true,'content' => 'Not Found');
        }


        return $status;

    }

    
}