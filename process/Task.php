<?php
namespace process;

use App\Services\RefreshTeslaToken;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        // 每7小时刷新token
//        new Crontab('0 */7 * * *', function(){

//        });
    }
}
