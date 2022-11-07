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

    public function run($msg, $offset = 1, $limit = 5)
    {
        $client = new SphinxApi();
        $client->SphinxClient();
        $q = $this->jieba($msg);
        $host = "127.0.0.1";
        $port = 9900;
        $index = "test1";
        $client->SetServer ( $host, $port );
        $client->SetConnectTimeout(10);
        $client->SetArrayResult(true);
        $client->SetLimits($offset,$limit);
        return $this->handleResponses($client->Query( $q, $index ));
    }

    public function test($msg, $offset = 1, $limit = 5)
    {
        $client = new SphinxApi();
        $client->SphinxClient();
        $q = $this->jieba($msg);
        $host = "127.0.0.1";
        $port = 9900;
        $index = "test1";
        $client->SetServer ( $host, $port );
        $client->SetConnectTimeout(10);
        $client->SetArrayResult(true);
        $client->SetLimits($offset,$limit);
        return $client->Query( $q, $index );
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

    public static function cleanData()
    {
        return Db::table('zbp_post')
            ->where('log_Status', 1)
            ->limit(100)
            ->orderBy('id')
            ->update(['log_Status' => 0]);
    }

}
