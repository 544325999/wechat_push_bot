<?php

namespace app\controller;

use App\Services\SphinxService;
use support\Request;


class Index
{
    public function index(Request $request)
    {
        $data = $request->all();
        $res = (new SphinxService())->run($data['msg'], $data['offset'], $data['limit']);
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

    public function test(Request $request)
    {
        $res = (new SphinxService())->test($request->get('msg'));
        return json(['code' => 0, 'data' => $res]);
    }

}
