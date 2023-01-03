<?php

namespace app\controller;

use App\Services\EsSearchService;
use App\Services\SphinxService;
use App\Services\XunSearchService;
use support\Request;
use yzh52521\mailer\Mailer;


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

    public function xun(Request $request)
    {
        $data = $request->all();
        $res = (new XunSearchService())->run($data['msg'], $data['offset'], $data['limit']);
        return json(['code' => 0, 'data' => $res]);
    }
    public function scwc(Request $request)
    {
        $data = $request->all();
        $res = (new XunSearchService())->scws($data['msg']);
        return json(['code' => 0, 'data' => $res]);
    }

    public function es(Request $request)
    {
        $data = $request->all();
        $res = (new EsSearchService())->run($data['msg']);
        return json(['code' => 0, 'data' => $res]);
    }

    public function xx()
    {
        return Mailer::setFrom('10086@qq.com')
            ->setTo('544325999@qq.com')
            ->setSubject('异常邮件')
            ->setTextBody('sphinx')
            ->send();
    }
}
