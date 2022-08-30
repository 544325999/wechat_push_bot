<?php

namespace app\controller;

use App\Services\Account;
use App\Services\MiIoService;
use App\Services\WechatTmpl;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Support\XML;
use support\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Workerman\Http\Client;

class Index
{
    public function index(Request $request)
    {
        $config = [
            'app_id' => 'wxa7f1ef4c49583990',
            'secret' => '199e5f5586d8b934e8204c6b5fd33333',
            'token' => '172cdc8b87d5e765e7777a7f0f7de04c',
            'response_type' => 'array',
        ];
        $app = Factory::officialAccount($config);
        $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
        $symfony_request->headers = new HeaderBag($request->header());
        $app->rebind('request', $symfony_request);

        $message = XML::parse($symfony_request->getContent());

        $service = new Account($message);
        if (isset($message['MsgType'])) {
            $app->server->push([$service, $message['MsgType']]);
        }

        $response = $app->server->serve();
        return $response->getContent();
    }

    public function view(Request $request)
    {
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        $postData = [
            'qs' => '%3F_json%3Dtrue%26sid%3Dxiaomiio',
            'sid' => 'xiaomiio',
            '_sign' => '7RoEnWWgpjzsTGA0t/VgVXgydtc=',
            'callback' => 'https://sts.api.io.mi.com/sts',
            'user' => '18519230486',
            'hash' => strtoupper(md5('ss544325999')),
            '_json' => true,
        ];
        $client = new Client();
        $client->post('https://account.xiaomi.com/pass/serviceLoginAuth2', $postData, function ($response) {
            echo $response->getBody();
        }, function ($exception) {
            echo $exception;
        });
        return json(['code' => 0, 'msg' => 'ok']);
    }

    /**
     * 开启空调
     * @return string
     */
    public function turnOnAirConditioner()
    {
        $config = config('mio');
        $params = [
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 1, 'value' => true],
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 2, 'value' => 1],
        ];
        try {
            $service = new MiIoService();
            $service::setMioSpec($params);
            return '开启成功';
        } catch (\Exception $exception) {
            return '开启失败';
        }
    }

    /**
     * 关闭空调
     * @return string
     */
    public function turnOffAirConditioner()
    {
        $config = config('mio');

        $params = [
            ['did' => $config['air_conditioner_did'], 'siid' => 2, 'piid' => 1, 'value' => false],
        ];
        try {
            $service = new MiIoService();
            $service::setMioSpec($params);
            return '已关闭';
        } catch (\Exception $exception) {
            return '关闭失败';
        }
    }



}
