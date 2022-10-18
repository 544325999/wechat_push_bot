<?php

namespace App\Services;

use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;

class SphinxService
{
    public function __construct()
    {
        Jieba::init();
        Finalseg::init();
        Jieba::loadUserDict(runtime_path().'/dict/dict.txt');
    }

    public function run($msg) :array
    {
        $client = new SphinxApi();
        $q = $msg; //模拟关键字
//$mode = SPH_MATCH_ALL;
        $host = "127.0.0.1";// sphinx的服务地址  此处用的是本地服务 切记 不是数据库地址！！！
        $port = 9900;// sphinx监听端口号
        $index = "test1";   // 此处为配置文件中配置的索引项
        $client->SetServer ( $host, $port );
        $client->SetConnectTimeout(10);
        $client->SetArrayResult(true);
        $client->SetLimits(1,1000);//要获取所有数据是这里第三个参数控制，默认是1000,太大会影响效率
//$cl->SetMatchMode(SPH_MATCH_ALL);//这个关闭它，不然会提示警告
        $res = $client->Query( $q, $index );
        return $res;
        return $this->filterResult(Jieba::cutForSearch($msg));
    }

    /**
     * 过滤停用词
     * @param $data
     * @return array
     */
    protected function filterResult($data) :array
    {
        $contents = file_get_contents(runtime_path().'/dict/stop_words.txt');
        $explode = explode(PHP_EOL, trim($contents));
        return array_filter($data, function ($v) use ($explode) {
            if (in_array($v, $explode)) {
                return false;
            }
            return true;
        });
    }
}
