<?php

namespace App\Services;

use GuzzleHttp\Client;

class TeslaClient
{
    protected static $cacheFile;

    public function __construct()
    {
        self::$cacheFile = runtime_path(). '/tesla/tesla.json';
    }

    public function getClient()
    {
        $token = $this->getCache('access_token');
        return new Client([
            'base_uri' => 'https://owner-api.teslamotors.com/',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5 Build/MOB31E; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/44.0.2403.117 Mobile Safari/537.36',
                'x-tesla-user-agent' => 'TeslaApp/3.10.8-421/adff2e065/android/6.0.1',
                'Authorization' => 'Bearer '. $token,
            ]
        ]);
    }

    public function getCache($name = '')
    {
        $cacheFile = $this->getCacheFile();

        if (file_exists($cacheFile)) {
            $json = file_get_contents($cacheFile);
            $data = json_decode($json, true);
            return $data[$name] ?? $data;
        }
        return [];
    }

    public function getCacheFile()
    {
        return self::$cacheFile;
    }

}
