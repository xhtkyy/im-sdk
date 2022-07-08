<?php

return [
    'default' => env('IM_DRIVER', 'kyy_im'),
    'drivers' => [
        'kyy_im' => [
            'jwt_secret' => env('IM_JWT_SECRET', ''),
            'jwt_ttl'    => env('IM_JWT_TTL', 7200),
            'base_uri'   => env('IM_KYYIM_BASE_URI', ''),
            'domain_id'  => env('IM_DOMAIN_ID', '0'), //默认 0: 快优易
            'org_id'     => env('IM_ORG_ID', '0'), //默认 0: 快优易
        ],
    ],
];
