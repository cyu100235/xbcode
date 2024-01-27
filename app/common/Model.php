<?php

namespace app\common;

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
    
    // 定义全局查询范围
    protected $globalScope = ['appid'];

    /**
     * 基类查询
     * @param mixed $query
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function scopeAppid($query)
    {
        $tableFields = $query->getTableFields();
        // 判断数据表内是否数据SAAS
        if (in_array('saas_appid', $tableFields) && request()->saas_appid) {
            $saas_appid     = request()->saas_appid ?? null;
            $tableName      = $query->getTable();
            $query->where("{$tableName}.saas_appid", $saas_appid);
        }
    }

    /**
     * 写入前置事件
     * @param mixed $model
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function onBeforeInsert(BaseModel $query)
    {
        $tableFields = $query->getTableFields();
        // 判断数据表内是否数据SAAS
        if (in_array('saas_appid', $tableFields) && request()->saas_appid) {
            $saas_appid = request()->saas_appid ?? null;
            $query->saas_appid = $saas_appid;
        }
    }
}