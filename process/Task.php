<?php
namespace process;

use App\Services\SphinxService;
use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        new Crontab('1 1 2,4,6,8,10 * * *', function(){
            SphinxService::cleanData();
        });
    }
}
