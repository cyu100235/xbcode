<?php

# 支付配置
return [
    [
        'title'         => '易支付',
        'field'          => 'epay',
        'children'      => [
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'field' => 'epay.appid',
                # 配置项名称
                'title' => '商户号',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'field' => 'epay.appkey',
                # 配置项名称
                'title' => '支付密钥',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'field' => 'epay.mode',
                # 配置项名称
                'title' => '支付模式',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'radio',
                # 数据默认值
                'value' => '',
                'extra'         => [
                    // alipay:支付宝,qqpay:QQ钱包,wxpay:微信支付
                    'options' => [
                        ['value' => 'alipay', 'label' => '支付宝'],
                        ['value' => 'qqpay', 'label' => 'QQ钱包'],
                        ['value' => 'wxpay', 'label' => '微信支付'],
                    ],
                ]
            ],
            [
                'field' => 'epay.url',
                'title' => '易支付接口地址',
                'component' => 'input',
                'value' => '',
                'extra'         => [
                    'prompt' => [
                        'text' => '以“/”结尾，如：http://www.baidu.com/'
                    ],
                ]
            ]
        ],
    ],
    [
        'title'         => '公众号支付',
        'field'          => 'wxpay',
        # 仅虚线模式有效
        // 'divider'       => [
        //     'borderStyle'       => 'dashed',
        // ],
        'col'           => 12,
        'children'      => [
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'field' => 'wxpay.mch_id',
                # 配置项名称
                'title' => '微信商户号',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
            [
                # 配置项标识(可通过getHpconfig进行读取)
                'field' => 'wxpay.mch_key',
                # 配置项名称
                'title' => '支付密钥',
                # 表单组件(参考common/enum/FormType.php)
                'component' => 'input',
                # 数据默认值
                'value' => '',
            ],
        ],
    ],
];