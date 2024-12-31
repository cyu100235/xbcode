<?php

return [
    [
        'field' => 'xbValidate',
        'title' => '',
        'value' => \app\validate\UploadConfigValidate::class,
        'type' => 'hidden',
        'extra' => [],
    ],
    [
        'field' => 'bucket',
        'title' => '空间名称',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写空间名称',
                ],
            ],
        ],
    ],
    [
        'field' => 'access_key',
        'title' => 'ACCESS_KEY',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写ACCESS_KEY',
                ],
            ],
        ],
    ],
    [
        'field' => 'secret_key',
        'title' => 'SECRET_KEY',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写SECRET_KEY',
                ],
            ],
        ],
    ],
    [
        'field' => 'domain',
        'title' => '空间域名',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'prompt' => '请补全http://或https://，例如https://static.cloud.com',
            'validate' => [
                [
                    'type' => 'string',
                    'required' => true,
                    'message' => '请填写空间域名',
                ],
            ],
        ],
    ],
];
