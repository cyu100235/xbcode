<?php

namespace app\common\model;

use app\common\Model;

class Settings extends Model
{
    // 设置JSON类型字段
	protected $json = ['value'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 处理储存数据
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function setValueAttr($value)
    {
        return $value;
    }
}
