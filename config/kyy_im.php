<?php

return [
    'default' => env('IM_DRIVER', 'kyy_im'),
    'drivers' => [
        'kyy_im' => [
            'jwt_secret'    => env('IM_JWT_SECRET', ''),
            'jwt_ttl'       => env('IM_JWT_TTL', 7200),
            'base_uri'      => env('IM_KYYIM_BASE_URI', ''),
            'domain_id'     => env('IM_DOMAIN_ID', '0'), //默认 0: 快优易
            'org_id'        => env('IM_ORG_ID', '0'), //默认 0: 快优易
            'proxy_address' => env('CURLOPT_PROXY'),
            'proxy_port'    => env('CURLOPT_PROXYPORT'),
            'proxy_user'    => env('CURLOPT_PROXYUSER'),
            'proxy_pwd'     => env('CURLOPT_PROXYPWD'),
        ],
    ],
    'class'   => [
        //需要的对象映射
        \KyyIM\Template\Models\Member::class          => \App\Models\Member::class,
        \KyyIM\Template\Models\Kefu::class            => \App\Models\Kefu\Kefu::class,
        \KyyIM\Template\Models\Institution::class     => \App\Models\Institution::class,
        \KyyIM\Template\Models\Project::class         => \App\Models\Rebuild\Project::class,
        \KyyIM\Template\Models\TemplateMessage::class => \App\Models\TemplateMessage::class,
        //消息类型枚举 兼容旧版定义
        \KyyIM\Constants\MessageConstant::class       => \App\Common\Constant\MessageConstant::class,
    ]
];
