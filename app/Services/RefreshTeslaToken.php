<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\Log;

class RefreshTeslaToken
{
    public function handle()
    {
        $config = (new TeslaClient())->getCache();
        if (empty($config)) {
            return true;
        }
        return $this->refreshToken($config['refresh_token'], $config['access_token']);
    }

    protected function refreshToken($refreshToken, $accessToken)
    {
        // 刷新缓存
        $data = [
            'refresh_token' => $refreshToken,
            'client_id' => 'ownerapi',
            'scope' => 'openid email offline_access',
            'grant_type' => 'refresh_token',
        ];
        $client =new Client([
            'base_uri' => 'https://auth.tesla.cn',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5 Build/MOB31E; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/44.0.2403.117 Mobile Safari/537.36',
                'x-tesla-user-agent' => 'TeslaApp/3.10.8-421/adff2e065/android/6.0.1',
                'Authorization' => 'Bearer '. $accessToken,
            ],
        ]);
        $time = (new Carbon())->format('Y-m-d H:i:s');
        try {
            $res = $client->post('oauth2/v3/token', [
                'json' => $data
            ]);
            $body = (string)$res->getBody();
            Log::error('[Task.refresh_tesla_token]: ' . $time . ':'. $body);
            file_put_contents((new TeslaClient())->getCacheFile(), $body);
            return json_decode($body, true);
        } catch (GuzzleException $exception) {
            Log::error('[ERROR][Task.refresh_tesla_token]: '.$time. ':'. $exception->getMessage());
            return $exception->getMessage();
        }
    }

}
