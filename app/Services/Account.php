<?php

namespace App\Services;

use app\controller\Index;

class Account
{
    protected $message;

    protected $res;

    public function __construct($mes)
    {
        $this->message = $mes;
    }

    public function text() :string
    {
        $content = $this->message['Content'] ?? '';
        $res = (new AccountService())->textHandler($content);
        return $res ?? $content;
    }

    public function voice()
    {
        // 语音识别消息
        if (isset($this->message['Recognition']) && $this->message['Recognition']) {
            $res = (new AccountService())->textHandler($this->message['Content']);
            return $res ?? $this->message['Content'];
        }
        return '收到语音消息';
    }


    public function __call($name, $arguments)
    {
        // 根据类型去实现不同格式消息的处理方法
        $message = [
            'text' => '收到文本消息',
            'event' => '收到事件消息',
            'image' => '收到图片消息',
            'voice' => '收到语音消息',
            'video' => '收到视频消息',
            'location' => '收到坐标消息',
            'link' => '收到链接消息',
            'file' => '收到文件消息',
        ];
        return $message[$name] ?? '收到其它消息';
    }

}
