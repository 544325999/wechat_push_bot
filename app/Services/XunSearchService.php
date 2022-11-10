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
        $search->setFuzzy()->setQuery($msg);
        $search->addWeight('log_Title', 'xunsearch');
        $search->setLimit($offset, $limit);
        return $this->handleResponses($search->search());
    }

    protected function handleResponses($data)
    {
        $result = [];
        foreach ($data as $v) {
           $result[] = [
               'log_ID' => $v->log_ID,
               'log_Title' => $v->log_Title,
               'log_Intro' => $v->log_Intro,
               'log_Content' => $v->log_Content,
           ];
        }
       return $result;
    }
}
