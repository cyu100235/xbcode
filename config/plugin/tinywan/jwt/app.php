<?php

return [
    'enable' => true,
    'jwt' => [
        'algorithms' => 'HS256',
        'access_secret_key' => '2026gMMLkJlxQkTV@xbcode#b34a8cb18ee759d8e281f83d5397d6d4',
        'access_exp' => 7200,
        'refresh_secret_key' => '2026FPyHonyIrUGv@xbcode#981345460c16126c9c37b6016f4ead2b',
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