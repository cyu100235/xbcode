<?php

namespace app\common\model;

use app\common\Model;
use app\common\service\UploadService;

/**
 * 项目模型
 * @copyright 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-05-03
 */
class Projects extends Model
{
    /**
     * 设置图标
     * @param mixed $value
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function setLogoAttr($value)
    {
        if ($value) {
            $value = UploadService::path($value);
        }
        return $value;
    }

    /**
     * 获取图标
     * @param mixed $value
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function getLogoAttr($value)
    {
        if ($value) {
            $value = UploadService::url($value);
        }
        return $value;
    }
}