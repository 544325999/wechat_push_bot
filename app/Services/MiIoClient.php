<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MiIoClient
{
    protected static $sid;
    protected static $user;
    protected static $pwd;
    public function __construct($sid, $user, $pwd)
    {
        self::$sid = $sid;
        self::$user = $user;
        self::$pwd = $pwd;
    }

    public static function send($uri, $data)
    {
        $conf = (new MiClient())->login(self::$sid, self::$user, self::$pwd);
        $data = json_encode($data);
        return self::handleResponse(self::post($conf, $uri, $data));
    }

    protected static function post($conf, $uri, $data)
    {
        $nonce = (new MiClient())->getDeviceId(16);
        $signedNonce = self::generateSignedNonce($conf['securityToken'], $nonce);

        $sign = self::generateSignature($uri, $signedNonce, $nonce, $data);
        $client = new Client([
            'base_uri' => 'https://api.io.mi.com',
            'cookies' => true,
            'headers' => [
                'User-Agent' => 'APP/com.xiaomi.mihome APPV/6.0.103 iosPassportSDK/3.9.0 iOS/14.3 miHSTS',
                'x-xiaomi-protocal-flag-cli' => 'PROTOCAL-HTTP2',
                'Cookie' => 'PassportDeviceId=' . $conf['deviceId'] . ';userId=' . $conf['userId'].';serviceToken=' . $conf['serviceToken'],
            ]
        ]);
        try {
            $response = $client->post('app'.$uri, [
                'form_params' => [
                    '_nonce' => $nonce,
                    'data' => $data,
                    'signature' => $sign
                ]
            ]);
            $body = (string)$response->getBody();
            return json_decode($body, true);
        } catch (GuzzleException $exception) {
            return $exception->getMessage();
        }
    }

    protected static function generateSignedNonce($token, $nonce)
    {
        $token = base64_decode($token);
        $nonce = base64_decode($nonce);
        $hash = hash('sha256', $token.$nonce, true);
        return base64_encode($hash);
    }

    protected static function generateSignature($uri, $signedNonce, $nonce, $data)
    {
        $sign = $uri."&".$signedNonce."&".$nonce."&data=".$data;
        $hash = hash_hmac('sha256', $sign, base64_decode($signedNonce), true);
        return base64_encode($hash);
    }

    protected static function handleResponse($data)
    {
        if (isset($data['code']) && $data['code'] == 0) {
            return $data['result'];
        }
        return $data['message'] ?? $data;
    }

}


