<?php

namespace App\Services;

use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\Jieba;
use support\Db;

class SphinxService
{
    public function __construct()
    {
        Jieba::init();
        Finalseg::init();
        Jieba::loadUserDict(runtime_path().'/dict/dict.txt');
    }

    public function run($msg)
    {
        $client = new SphinxApi();
        $client->SphinxClient();
        $q = $this->jieba($msg);
        $host = "127.0.0.1";// sphinx的服务地址  此处用的是本地服务 切记 不是数据库地址！！！
        $port = 9900;// sphinx监听端口号
        $index = "test1";   // 此处为配置文件中配置的索引项
        $client->SetServer ( $host, $port );
        $client->SetConnectTimeout(10);
        $client->SetArrayResult(true);
        $client->SetLimits(1,5);//要获取所有数据是这里第三个参数控制，默认是1000,太大会影响效率
//        $client->SetMatchMode(SPH_MATCH_ANY);//这个关闭它，不然会提示警告
        return $this->handleResponses($client->Query( $q, $index ));
    }

    protected function handleResponses($data)
    {
        if (!$data) {
            return false;
        }
        if (count($data['matches']) <= 0) {
            return $data;
        }
        return Db::table('zbp_post')->select(['log_ID','log_Title','log_Intro','log_Content'])
            ->whereIn('log_ID', array_column($data['matches'], 'id'))->get();
    }

    protected function jieba($msg)
    {
        $results = $this->filterResult(Jieba::cutForSearch($msg));
        foreach ($results as $res) {
            $words[] = '(' . $res . ')';
        }
//        $words[] = '(' . $msg . ')';
        return join('|', $words);
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
