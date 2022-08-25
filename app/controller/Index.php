<?php

namespace app\controller;

use App\Services\WechatTmpl;
use support\Request;


class Index
{
    protected $service;

    public function __construct()
    {
        $this->service = new WechatTmpl();
    }

    public function index()
    {
        $this->service->index();
        return json(['code' => 0, 'msg' => 'ok']);

    }

    public function view(Request $request)
    {
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
