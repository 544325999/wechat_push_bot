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
    }

    public function run($msg) :array
    {
        return $this->filterResult(Jieba::cutForSearch($msg));
    }

    /**
     * 过滤停用词
     * @param $data
     * @return array
     */
    protected function filterResult($data) :array
    {
        $contents = file_get_contents(runtime_path().'/dict/dict.txt');
        $explode = explode(PHP_EOL, trim($contents));
        return array_filter($data, function ($v) use ($explode) {
            if (in_array($v, $explode)) {
                return false;
            }
            return true;
        });
    }
}
