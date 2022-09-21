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
            // 过滤标点
            $keyword = preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99)+/",'',urlencode($this->message['Content']));
            $res = (new AccountService())->textHandler(urldecode($keyword));
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
