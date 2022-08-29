<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;

class WechatTmpl
{
    public function index()
    {
        $config = config('wechat');

        // 获取多条配置 循环执行
        foreach ($config as $v) {
            $this->exec($v);
        }
    }

    protected function exec($wechatConfig)
    {
        $token = $this->getAccessToken($wechatConfig['app_id'], $wechatConfig['secret']);



        $weatherData = $this->getWeather($wechatConfig['weather_key'], $wechatConfig['region']);
        $noteData = $this->getCiBa();
        $birthdayData = $this->getBirthday($wechatConfig['birthday1']);
        // 组装请求数据
        $postData = $this->makePostData($wechatConfig['template_id'],
            $wechatConfig['region'],
            $weatherData,
            $noteData,
            $wechatConfig['love_date'],
            $birthdayData);

        // 给不同用户发送消息
        foreach ($wechatConfig['user'] as $v) {
            $postData['touser'] = $v;
            $this->sendMessage($token, $postData);
        }
    }

    protected function getWechatClient()
    {
        return new Client([
            'base_uri' => 'https://api.weixin.qq.com/cgi-bin/',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
            ]
        ]);
    }

    protected function sendMessage($token, $data)
    {
        $client = $this->getWechatClient();
        $response = $client->post('message/template/send?access_token='.$token, [
            'json' => $data
        ]);

        $body = (string)$response->getBody();
        return json_decode($body, true);
    }

    protected function makePostData($templateId, $region, $weatherData, $noteData, $loveDay, $birthday)
    {
        return [
            'touser' => '',
            'template_id' => $templateId,
            'url' => 'http://weixin.qq.com/download',
            'topcolor' => '#FF0000',
            'data' => [
                'date' => [
                    'value' => $this->getDay(),
                    'color' => $this->getColor(),
                ],
                'region' => [
                    'value' => $region,
                    'color' => $this->getColor(),
                ],
                "weather" => [
                    'value' => $weatherData['weather'],
                    'color' => $this->getColor(),
                ],
                "temp" => [
                    'value' => $weatherData['temp'],
                    'color' => $this->getColor(),
                ],
                "wind_dir" => [
                    'value' => $weatherData['wind_dir'],
                    'color' => $this->getColor(),
                ],
                "note_en" => [
                    'value' => $noteData['en'],
                    'color' => $this->getColor(),
                ],
                "note_ch" => [
                    'value' => $noteData['ch'],
                    'color' => $this->getColor(),
                ],
                "love_day" => [
                    'value' => $this->getDiffDay($loveDay),
                    'color' => $this->getColor(),
                ],
                "birthday1" => [
                    'value' => $birthday,
                    'color' => $this->getColor(),
                ],
            ]
        ];
    }

    protected function getDay()
    {
        $carbon = new Carbon();
        $week = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
        return $carbon->format('Y-m-d'). ' '. $week[$carbon->dayOfWeek] ;
    }

    protected function getBirthday($birthday)
    {
        $year = (new Carbon())->format('Y');
        $monthDay = (new Carbon($birthday['birthday']));
        $name = $birthday['name'];
        $now = $year . $monthDay->format('-m-d');

        if ($monthDay->isBirthday()) {
            return "今天".$name."生日哦，祝".$name."生日快乐！";
        }

        if (($res = $this->getDiffDay($now, false)) < 0) {
            // 说明今年的过了 获取明年生日
            $nextYear = (new Carbon())->addYear()->format('Y');
            $nextBirthday = $nextYear . $monthDay->format('-m-d');
            $res = $this->getDiffDay($nextBirthday);
        }
        return "距离".$name."的生日还有".$res."天";
    }

    protected function getColor()
    {
        $colors = array();

        for($i = 0;$i<6;$i++){
            $colors[] = dechex(rand(0,15));
        }

        return '#'.implode('',$colors);
    }

    protected function getAccessToken($appId, $secret)
    {
        $client = $this->getWechatClient();
        $response = $client->post('token?grant_type=client_credential&appid='.$appId.'&secret='.$secret);
        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        return $data['access_token'];
    }

    protected function getWeather($key, $region)
    {
        $client = new Client([
            'base_uri' => 'https://geoapi.qweather.com/v2/',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
            ]
        ]);
        $response = $client->get('city/lookup?location='.$region.'&key='.$key);
        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        if ($data['code'] == 404) {

        } elseif ($data['code'] == 401) {

        } else {
            $locationId = $data['location'][0]['id'];
            $client = new Client([
                'base_uri' => 'https://devapi.qweather.com/v7/',
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
                ]
            ]);
            $response = $client->get('weather/now?location='.$locationId.'&key='.$key);
            $body = (string)$response->getBody();
            $data = json_decode($body, true);
            return [
                'weather' => $data['now']['text'],
                'temp' => $data['now']['temp'] . "C",
                'wind_dir' => $data['now']['windDir'],
            ];
        }
    }

    protected function getCiBa()
    {
        $client = new Client([
            'base_uri' => 'http://open.iciba.com/',
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36'
            ]
        ]);
        $response = $client->get('dsapi');
        $body = (string)$response->getBody();
        $data = json_decode($body, true);
        return ['ch' => $data['note'], 'en' =>  $data['content']];
    }

    protected function getDiffDay($date, $bol = true)
    {
        return (new Carbon())->diffInDays($date, $bol);
    }

}
