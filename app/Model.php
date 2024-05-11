<?php

namespace app;

use think\Model as BaseModel;

/**
 * 基类模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Model extends BaseModel
{
    // 开启自动时间戳
    protected $autoWriteTimestamp = 'datetime';
    // 定义时间戳字段名
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
}