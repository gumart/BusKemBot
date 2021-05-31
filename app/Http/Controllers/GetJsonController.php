<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class GetJsonController extends Controller
{
    
    public function getJSON(Request $request)
    {
        $this->client = new Client([
            'base_uri' => 'https://bus.vse42.ru/api/kema/routedirections/'
        ]);

        $response = $this->client->request('GET', '',[
            'query' => [
                'route' => '10',
                'vehicleType' => 'tr'
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        dd($data['data']["r9894cc6a21f4e449a169672f22d26386"]);
    }

}
