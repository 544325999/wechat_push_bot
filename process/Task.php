<?php
namespace process;

use App\Services\WechatTmpl;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        // 每天的7点50执行
        new Crontab('1 7 * * *', function(){
            (new WechatTmpl())->index();
        });
    }
}
