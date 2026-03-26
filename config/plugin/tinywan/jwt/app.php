<?php

return [
    'enable' => true,
    'jwt' => [
        'algorithms' => 'HS256',
        'access_secret_key' => '2026mstwKcKFdvSB@xbcode#3610fc0c15c63b81afb8799795c4a029',
        'access_exp' => 7200,
        'refresh_secret_key' => '2026WLskVjoToOpf@xbcode#a6d295986839930618323df4616322ca',
        'refresh_exp' => 604800,
        'refresh_disable' => false,
        'iss' => 'webman.tinywan.cn',
        'nbf' => 0,
        'leeway' => 60,
        'is_single_device' => false,
        'cache_token_ttl' => 604800,
        'cache_token_pre' => 'JWT:TOKEN:',
        'cache_refresh_token_pre' => 'JWT:REFRESH_TOKEN:',
        'user_model' => function ($uid) {
            return [];
        },
        'is_support_get_token' => false,
        'is_support_get_token_key' => 'authorization',
        'access_private_key' => '-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----',
        'access_public_key' => '-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----',
        'refresh_private_key' => '-----BEGIN RSA PRIVATE KEY-----
...
-----END RSA PRIVATE KEY-----',
        'refresh_public_key' => '-----BEGIN PUBLIC KEY-----
...
-----END PUBLIC KEY-----'
    ]
];