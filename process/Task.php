<?php
namespace process;

use App\Services\FanyunService;
use App\Services\RefreshTeslaToken;
use App\Services\WechatTmpl;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        // 每天的7点01执行 推送模版消息
        new Crontab('1 7 * * *', function(){
            (new WechatTmpl())->index();
        });

        // 每7小时刷新token
        new Crontab('0 */7 * * *', function(){
            (new RefreshTeslaToken())->handle();
        });

        // 凡云自动签到
        new Crontab('2 7 * * *', function(){
            (new FanyunService())->login();
        });

    }
}
