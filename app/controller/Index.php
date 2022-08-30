<?php

namespace app\controller;

use App\Services\Account;
use App\Services\MiIoService;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Support\XML;
use support\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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

    public function lulu(Request $request)
    {
        $config = [
            'app_id' => getenv('app_id'),
            'secret' => getenv('app_secret'),
            'token' => getenv('token'),
            'aes_key' => getenv('aes_key'),
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
