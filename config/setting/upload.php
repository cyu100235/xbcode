<?php
use app\common\providers\DictProvider;
use FormBuilder\Factory\Elm;

return [
    [
        'field' => 'active',
        'title' => '当前使用上传方式',
        'value' => 'public',
        'component' => 'radio',
        'extra' => [
            'options' => [
                [
                    'label' => '本地附件(不推荐)',
                    'value' => 'public',
                ],
                [
                    'label' => '阿里云储存',
                    'value' => 'aliyun',
                ],
                [
                    'label' => '腾讯云储存',
                    'value' => 'qcloud',
                ],
                [
                    'label' => '七牛云储存',
                    'value' => 'qiniu',
                ],
            ],
            'control' => [
                // 本地储存
                [
                    'value' => 'public',
                    'where' => '==',
                    'rule' => [
                        Elm::input('public.url', '站点域名')
                            ->col(12)
                            ->appendRule('suffix', [
                                'type' => 'prompt-tip',
                                'props' => [
                                    'text' => '示例地址：https://www.xiaobai.host，无斜杠结尾',
                                ],
                            ])
                            ->build(),
                        Elm::input('public.root', '储存路径', '/uploads')
                            ->col(12)
                            ->appendRule('suffix', [
                                'type' => 'prompt-tip',
                                'props' => [
                                    'text' => '例如：uploads，储存路径：/public/uploads/20230101/xxx.jpg',
                                ],
                            ])->build(),
                    ],
                ],
                // 阿里云
                [
                    'value' => 'aliyun',
                    'where' => '==',
                    'rule' => [
                        Elm::hidden('aliyun.type', 'aliyun')->build(),
                        Elm::password('aliyun.access_id', 'access_id', '')->props([
                            'showPassword' => true
                        ])->appendRule('suffix', [
                                    'type' => 'prompt-tip',
                                    'props' => [
                                        'text' => '请填写阿里云 AccessID',
                                    ],
                                ])->col(12)->build(),
                        Elm::password('aliyun.access_secret', 'access_secret', '')->props([
                            'showPassword' => true
                        ])
                            ->appendRule('suffix', [
                                'type' => 'prompt-tip',
                                'props' => [
                                    'text' => '请填写阿里云 AccessSecret',
                                ],
                            ])->col(12)->build(),
                        Elm::input('aliyun.bucket', 'Bucket', '')->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => '请填写阿里云 OSS 储存空间Bucket名称',
                            ]
                        ])->col(12)->build(),
                        Elm::input('aliyun.endpoint', 'Bucket域名', '')->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => '请填写不用带协议的域名，例如：oss-cn-hangzhou.aliyuncs.com',
                            ],
                        ])->col(12)->build(),
                        // Elm::radio('aliyun_private_type', '是否私有空间','10')
                        // ->options(YesNoEum::options())
                        // ->col(12)
                        // ->build(),
                    ],
                ],
                // 腾讯云
                [
                    'value' => 'qcloud',
                    'where' => '==',
                    'rule' => [
                        Elm::hidden('qcloud.type', 'qcloud')->build(),
                        Elm::input('qcloud.region', '所属地域：Region')->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => '请填写地域简称，例如：ap-beijing、ap-hongkong、eu-frankfurt',
                            ],
                        ])->col(12)->build(),
                        Elm::input('qcloud.domain', '空间域名：Domain')->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => '请填写不用带协议的域名，例如：static.cloud.com',
                            ],
                        ])->col(12)->build(),
                        Elm::input('qcloud.app_id', 'APPID')->col(12)->build(),
                        Elm::input('qcloud.bucket', '存储空间名称：Bucket')->col(12)->build(),
                        Elm::password('qcloud.secret_id', 'SECRET_ID')
                            ->props([
                                'showPassword' => true
                            ])->col(12)->build(),
                        Elm::password('qcloud.secret_key', 'SECRET_KEY')
                            ->props([
                                'showPassword' => true
                            ])->col(12)->build(),
                        Elm::radio('qcloud.private_type', '是否私有空间', '10')
                            ->options(DictProvider::get('yesNoText')->options())
                            ->col(24)
                            ->build(),
                    ],
                ],
                // 七牛云
                [
                    'value' => 'qiniu',
                    'where' => '==',
                    'rule' => [
                        Elm::hidden('qiniu.type', 'qiniu')->build(),
                        Elm::password('qiniu.access_key', 'access_key')
                            ->props([
                                'showPassword' => true
                            ])->col(12)->build(),
                        Elm::password('qiniu.secret_key', 'secret_key')
                            ->props([
                                'showPassword' => true
                            ])
                            ->col(12)->build(),
                        Elm::input('qiniu.bucket', '存储空间名称：Bucket')->col(12)->build(),
                        Elm::input('qiniu.domain', '空间域名：Domain')->appendRule('suffix', [
                            'type' => 'prompt-tip',
                            'props' => [
                                'text' => "请填写不用带协议的域名，例如：static.xadmin.com\n如您的站点是HTTPS，七牛云必须开启HTTPS才能使用",
                            ],
                        ])->col(12)->build(),
                        Elm::radio('qiniu.private_type', '是否私有空间', '10')
                            ->options(DictProvider::get('yesNoText')->options())
                            ->col(24)
                            ->build(),
                    ],
                ],
            ],
        ],
    ]
];