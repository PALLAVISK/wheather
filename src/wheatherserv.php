<?php
namespace Drupal\wheather;

class wheatherserv{
    public function test($city){
        $settings = \Drupal::config('simple.settings');
        $app=$settings->get('simple.id');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET','https://samples.openweathermap.org/data/2.5/weather?q='.$city.'&appid='.$app);
        $data = $response->getBody();
        return $data;
    }
}


