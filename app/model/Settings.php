<?php

namespace app\model;

use app\common\Model;
use app\common\providers\UploadProvider;

class Settings extends Model
{

    /**
     * 设置数据值
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function setValueAttr($value)
    {
        if ($value && is_array($value)) {
            $value = UploadProvider::path($value);
        }
        return $value;
    }

    /**
     * 获取数据值
     * @param mixed $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getValueAttr($value)
    {
        if ($value && strpos($value, 'uploads') !== false) {
            $value = UploadProvider::url($value);
        }
        return $value;
    }
}
