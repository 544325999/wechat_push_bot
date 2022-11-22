<?php

namespace App\Services;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use support\Db;

class EsSearchService
{
    protected $es;
    public function __construct()
    {
        $this->es = ClientBuilder::create()->build();
    }

    public function run()
    {
        $params = [
            'index' => 'blog',
            'type' => 'my_type',
            'body' => [
                'query' => [
                    'match' => [
                        'title' => '螃蟹'
                    ]
                ]
            ],
            'analyzer' => 'ik_smart'
        ];

        try {
            return $this->es->search($params);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function index()
    {
        $data = Db::table('zbp_post')
            ->select(['log_ID','log_Title','log_Intro','log_Content'])
            ->where('log_Status', 0)
            ->get();

        foreach ($data as $v) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'blog',
                    '_type' => 'my_type',
                ]
            ];

            $params['body'][] = [
                'id' => $v->log_ID,
                'title' => $v->log_Title,
                'intro' => $v->log_Intro,
                'content' => $v->log_Content,
            ];
        }
        return $this->es->bulk($params);
    }

}
