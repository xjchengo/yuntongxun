<?php

return [
    'uri_prefix' => function($uri){
        return $uri;
    },

    'captcha' => [
        'expire_seconds' => '120',
        'width' => '80',
        'height' => '35',
        'background_color' => [
            'red' => '255',
            'green' => '255',
            'blue' => '255'
        ],
        'length' => '4',
        'charset' => '1234567890',
        'distortion' => true,
        'max_bebind_lines' => 1,
        'max_front_lines' => 1,
        'attempt_limit' => 10
    ],

    'sms' => [
        'expire_seconds' => '120',
        'length' => '4',
        'charset' => '1234567890',
        'attempt_limit' => 5,
        'default_template_id' => 1,

    ],

    'ronglian' => [
        'account_sid' => '', //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        'account_token' => '', //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN

        /*
         * 应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
         * 在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
         */
        'app_id' => '',

        'server_ip' => 'sandboxapp.cloopen.com', //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        'server_port' => '8883',
        'soft_version' => '2013-12-26',
        'enable_log' => true
    ]
];
