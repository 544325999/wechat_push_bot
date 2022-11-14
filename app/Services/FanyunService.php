<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class FanyunService
{
    public function login()
    {
        $arr = [
            'email' => getenv('login_email'),
            'passwd' => getenv('login_pwd'),
            'code' => '',
        ];
        $client = $this->getClient('https://clashk.netlify.app/auth/login');

        try {
            $res = $client->request('POST', 'auth/login',[
                'form_params' => $arr
            ]);
            $head_arr = $res->getHeader('Set-Cookie');
            $cookie_arr = [];
            foreach ($head_arr as $val){
                $new_sub = explode('=',substr($val,0,strpos($val,';')));
                $cookie_arr[$new_sub[0]] = $new_sub[1];
            }

            $data = json_decode((string)$res->getBody(), true);
            if ($data['msg'] == '登录成功') {
                $cookieJar = CookieJar::fromArray($cookie_arr, 'clashk.netlify.app');
                $client = $this->getClient('https://app.clash.cat/user');
                $response = $client->post('user/checkin', [
                    'cookies' => $cookieJar,
                ]);
                return json_decode((string) $response->getBody(), true);
            }

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    protected function getClient($referer)
    {
        return new Client([
            'base_uri' => 'https://clashk.netlify.app',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
                'Referer' => $referer,
                'x-requested-with' => 'XMLHttpRequest',
                'Origin' => 'https://clashk.netlify.app',
                'authority' => 'clashk.netlify.app',
                'Cookie' => '_ga=GA1.3.1814138946.1668046124',
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'sec-fetch-site' => 'same-origin',
            ]
        ]);
    }
}
