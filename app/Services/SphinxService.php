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

    public function run($msg)
    {
        return Jieba::cutForSearch($msg);
    }



}
