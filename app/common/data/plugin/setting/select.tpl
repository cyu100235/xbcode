<?php

// 选中配置表单
return [
    // [
    //     'field' => 'active',
    //     'title' => '当前使用上传方式',
    //     'value' => 'public',
    //     'component' => 'radio',
    //     'extra' => [
    //         // select选项
    //         'options' => [
    //             [
    //                 'label' => '本地附件(不推荐)',
    //                 'value' => 'public',
    //             ],
    //             [
    //                 'label' => '腾讯云储存',
    //                 'value' => 'qcloud',
    //             ],
    //         ],
    //         // 表单联动控制规则
    //         'control' => [
    //             // 本地储存
    //             [
    //                 'value' => 'public',
    //                 'condition' => '==',
    //                 'rule' => [
    //                     [
    //                         'field' => 'public.url',
    //                         'title' => '站点域名',
    //                         'value' => '',
    //                         'component' => 'input',
    //                         'extra' => [
    //                             'suffix' => [
    //                                 'type' => 'prompt-tip',
    //                                 'props' => [
    //                                     'text' => '示例地址：https://www.xiaobai.host，无斜杠结尾',
    //                                 ],
    //                             ],
    //                         ],
    //                     ],
    //                     [
    //                         'field' => 'public.root',
    //                         'title' => '储存路径',
    //                         'value' => '/uploads',
    //                         'component' => 'input',
    //                         'extra' => [
    //                             'suffix' => [
    //                                 'type' => 'prompt-tip',
    //                                 'props' => [
    //                                     'text' => '例如：uploads，储存路径：/public/uploads/20230101/xxx.jpg',
    //                                 ],
    //                             ],
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //             // 腾讯云
    //             [
    //                 'value' => 'qcloud',
    //                 'condition' => '==',
    //                 'rule' => [
    //                     [
    //                         'field' => 'qcloud.type',
    //                         'title' => '上传方式',
    //                         'value' => 'qcloud',
    //                         'component' => 'hidden',
    //                     ],
    //                     [
    //                         'field' => 'qcloud.region',
    //                         'title' => '所属地域：Region',
    //                         'value' => '',
    //                         'component' => 'input',
    //                         'extra' => [
    //                             'suffix' => [
    //                                 'type' => 'prompt-tip',
    //                                 'props' => [
    //                                     'text' => '请填写地域简称，例如：ap-beijing、ap-hongkong、eu-frankfurt',
    //                                 ],
    //                             ],
    //                         ],
    //                     ],
    //                     [
    //                         'field' => 'qcloud.domain',
    //                         'title' => '空间域名：Domain',
    //                         'value' => '',
    //                         'component' => 'input',
    //                         'extra' => [
    //                             'suffix' => [
    //                                 'type' => 'prompt-tip',
    //                                 'props' => [
    //                                     'text' => '请填写不用带协议的域名，例如：static.cloud.com',
    //                                 ],
    //                             ],
    //                         ],
    //                     ],
    //                     [
    //                         'field' => 'qcloud.app_id',
    //                         'title' => 'APPID',
    //                         'value' => '',
    //                         'component' => 'input',
    //                     ],
    //                     [
    //                         'field' => 'qcloud.bucket',
    //                         'title' => '存储空间名称：Bucket',
    //                         'value' => '',
    //                         'component' => 'input',
    //                     ],
    //                     [
    //                         'field' => 'qcloud.secret_id',
    //                         'title' => 'SECRET_ID',
    //                         'value' => '',
    //                         'component' => 'password',
    //                         'extra' => [
    //                             'showPassword' => true,
    //                         ],
    //                     ],
    //                     [
    //                         'field' => 'qcloud.secret_key',
    //                         'title' => 'SECRET_KEY',
    //                         'value' => '',
    //                         'component' => 'password',
    //                         'extra' => [
    //                             'showPassword' => true,
    //                         ],
    //                     ],
    //                     [
    //                         'field' => 'qcloud.private_type',
    //                         'title' => '是否私有空间',
    //                         'value' => '10',
    //                         'component' => 'radio',
    //                         'extra' => [
    //                             'options' => [
    //                                 [
    //                                     'label' => '是',
    //                                     'value' => '10',
    //                                 ],
    //                                 [
    //                                     'label' => '否',
    //                                     'value' => '20',
    //                                 ],
    //                             ],
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //         ],
    //     ],
    // ]
];