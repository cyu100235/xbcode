<?php

return [
    [
        'field' => 'bt_api_state',
        'title' => '宝塔服务',
        'value' => 10,
        'type' => 'radio',
        'extra' => [
            'prompt' => '是否开启宝塔面板API服务',
            'options' => [
                [
                    'value' => 10,
                    'label' => '关闭',
                ],
                [
                    'value' => 20,
                    'label' => '开启',
                ],
            ],
        ],
    ],
    [
        'field' => 'bt_api_domain',
        'title' => '接口地址',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
            'prompt' => '宝塔面板地址，例如：http://111.555.888.999:8888/',
        ],
    ],
    [
        'field' => 'bt_api_key',
        'title' => '接口密钥',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
            'prompt' => '宝塔面板API密钥',
        ],
    ],
    [
        'field' => 'description',
        'title' => '',
        'value' => '',
        'type' => 'xbAlert',
        'extra' => [
            'props' => [
                'title' => '温馨提示',
                'type' => 'success',
                'closable' => false,
                'content' => <<<STR
                <div>开启API后，必需在IP白名单列表中的IP才能访问面板API接口</div>
                <div>请不要在生产环境开启，这可能增加服务器安全风险；</div>
                <div>请不要使用第三方应用调用宝塔面板API，以防止潜在的安全威胁。</div>
                <div>
                API接口文档： 
                <a href="https://www.bt.cn/data/api-doc.pdf" target="_blank">
                https://www.bt.cn/data/api-doc.pdf
                </a>
                </div>
                <div>
                API使用文档： 
                <a href="https://www.bt.cn/bbs/thread-20376-1-1.html" target="_blank">
                https://www.bt.cn/bbs/thread-20376-1-1.html
                </a>
                </div>
                STR,
            ]
        ],
    ],
];
