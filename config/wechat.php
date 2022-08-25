<?php

return [
    // default 这个名字随便起 不重复就行 可随意粘贴
    // 自行替换 => 后面 '' 单引号配置信息
    'default' => [
        // 公众号appid
        'app_id' => 'appid',
        // 公众号secret
        'secret' => 'secretsecret',
        // 公众号 模版id
        'template_id' => 'template_id',
        // 用户openid 可随意添加注意格式
        'user' => [
            "openid",
            "openid",
        ],
        // 和风天气key
        'weather_key' => 'key',
        // 所在地区 用来获取天气
        'region' => '榆次区',
        // 在一起时间
        'love_date' => '2021-05-20',
        // 公历生日加昵称 用以计算
        'birthday1' => [
            'name' => '猪猪',
            'birthday' => '1998-04-16'
        ],
        // 暂未使用
        'birthday2' => [
            'name' => '臭宝儿',
            'birthday' => '1998-03-20'
        ],
    ],

    // my
    'my' => [
        'app_id' => '',
        'secret' => '',
        'template_id' => '',
        'user' => [
            "-ITTfJ-nmE",
            "--QZm4QbRVe4"
        ],
        'weather_key' => '',
        'region' => '普陀区',
        'love_date' => '2022-11-18',
        'birthday1' => [
            'name' => '猪',
            'birthday' => '1996-17-21'
        ],
        'birthday2' => [
            'name' => '猪',
            'birthday' => '1996-17-21'
        ],
    ]
];
