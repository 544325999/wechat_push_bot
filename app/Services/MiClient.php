<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class MiClient
{
    public function login($sid, $user, $password)
    {
        $filePath = runtime_path(). '/users/' .$user.'.json';
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            if ($json != '') {
                $data = json_decode($json, true);
                $bol = (new Carbon())->lt($data['time']);
                if ($bol) {
                    return $data;
                }
            }
        }

        $client = $this->getClient();
        $response = $client->get('pass/serviceLogin?_json=true&sid='.$sid);
        $data = $this->handleResponse($response);
        $result = $this->loginAuth($data, $user, $password);
        if($result['code'] != 0 ) {
            // error
            return false;
        }

        $data = $this->loginIo($result);
        file_put_contents($filePath, json_encode($data));
        return $data;
    }

    protected function loginAuth($data, $user, $password)
    {
        $client = $this->getClient();
        $postData = [
            'qs' => $data['qs'],
            'sid' => $data['sid'],
            '_sign' => $data['_sign'],
            'callback' => $data['callback'],
            'user' => $user,
            'hash' => strtoupper(md5($password)),
            '_json' => true,
        ];
        $response = $client->post('pass/serviceLoginAuth2',[
            'form_params' => $postData
        ]);
        return $this->handleResponse($response);
    }

    protected function loginIo($data)
    {
        $location = $data['location'];
        $userId = $data['userId'];
        $securityToken = $data['ssecurity'];
        $cookieJar = new CookieJar();
        $client = new Client([
            'headers' => [
                'User-Agent' => 'APP/com.xiaomi.mihome APPV/6.0.103 iosPassportSDK/3.9.0 iOS/14.4 miHSTS'
            ],
            'cookies' => $cookieJar
        ]);
        $url = $location . '&clientSign=';
        $client->get($url);

        $cookJar = $cookieJar->getIterator();  //??????????????????GuzzleHttp\Cookie\SetCookie??????
        $cookies = [];
        foreach ($cookJar as $v){
            $ck = $v->toArray();                    //????????????,???????????????GuzzleHttp\Cookie\CookieJar
            $cookies[$ck['Name']] = $ck['Value'];  //??????????????????
        }

        return [
            'userId' => $userId,
            'securityToken' => $securityToken,
            'deviceId' => $this->getDeviceId(16),
            'serviceToken' => $cookies['serviceToken'],
            'time' => (new Carbon())->addDays('15')->format('Y-m-d H:i:s')
        ];
    }

    protected function handleResponse($response)
    {
        $body = (string)$response->getBody();
        // ??????????????????11???
        $json = substr($body, 11);
        return json_decode($json, true);
    }

    protected function getClient()
    {
        return new Client([
            'base_uri' => 'https://account.xiaomi.com/',
            'headers' => [
                'User-Agent' => 'APP/com.xiaomi.mihome APPV/6.0.103 iosPassportSDK/3.9.0 iOS/14.4 miHSTS'
            ]
        ]);
    }

    public function getDeviceId($length)
    {
        $str = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $id = '';
        for ($i = 1; $i <= $length; $i++) {
            $key = rand(0, 61);
            $id .= $str[$key];
        }
        return $id;
    }
}
