<?php

namespace app\controller;

use App\Services\Account;
use App\Services\SphinxService;
use App\Services\MiIoService;
use App\Services\TeslaService;
use App\Services\WechatTmpl;
use support\Request;


class Index
{
    public function index(Request $request)
    {
        $res = (new SphinxService())->run($request->get('msg'));
        return json(['code' => 0, 'data' => $res]);
    }


    public function view(Request $request)
    {
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

    public function test()
    {
//        return json(['code' => 0, 'data' => $res]);
    }

}
