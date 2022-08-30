<?php

namespace App\Services;

use app\controller\Index;
use EasyWeChat\Kernel\Messages\Text;

class Account
{
    protected $message;

    public function __construct($mes)
    {
        $this->message = $mes;
    }

    public function text()
    {
        $content = $this->message['Content'] ?? '';
        if ($content == '打开空调') {
            return (new Index())->turnOnAirConditioner();
        } else if ($content == '关闭空调') {
            return (new Index())->turnOffAirConditioner();
        }
        return new Text($content);
    }


    public function __call($name, $arguments)
    {
        // 根据类型去实现不同格式消息的处理方法
        $message = [
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
