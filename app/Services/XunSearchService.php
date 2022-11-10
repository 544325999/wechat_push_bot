<?php

namespace App\Services;

class XunSearchService
{
    protected $xs;
    public function __construct()
    {
        $this->xs = new \XS('zblog');
    }

    public function run($msg, $offset = 1, $limit = 5)
    {
        $search = $this->xs->search;
        $search->setQuery($msg);
        $search->addWeight('log_Title', 'xunsearch');
        $search->setLimit($offset, $limit);
        return $search->search();
    }
}
