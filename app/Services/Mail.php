<?php

namespace App\Services;

use yzh52521\mailer\Mailer;

class Mail
{
    public static function send()
    {
        return Mailer::setFrom('10086@qq.com')
            ->setTo('17600088849@163.com')
            ->setSubject('异常邮件')
            ->setTextBody('sphinx')
            ->send();
    }
}
