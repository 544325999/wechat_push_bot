<?php

namespace App\Services;

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
}
