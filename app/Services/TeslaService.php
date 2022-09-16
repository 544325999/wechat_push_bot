<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TeslaService
{
    protected $client;
    public function __construct()
    {
        $this->client = (new TeslaClient())->getClient();
    }

    public function getList()
    {
        try {
            $res = $this->client->get('api/1/vehicles');
            $body = (string)$res->getBody();
            return json_decode($body, true);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getInfo($id)
    {
        try {
            $res = $this->client->get('api/1/vehicles/' . $id . '/vehicle_data');
            $body = (string)$res->getBody();
            return json_decode($body, true);
        } catch (ClientException $exception) {
            if ($exception->getCode() != 200) {
                return '车辆状态异常或处于休眠状态';
            }
            return $exception->getMessage();
        }
    }


    public function parameters($lat, $lng)
    {
        $client = new Client(['base_uri' => 'https://restapi.amap.com/']);
        $response = $client->get('/v3/geocode/regeo?parameters', [
            'query' => [
                'key' => '550bd839f6fa45046b922f24dd6b7a5c',
                'location' => $lat.','.$lng,
            ]
        ]);
        $body = (string)$response->getBody();
        return json_decode($body, true);

    }
}
