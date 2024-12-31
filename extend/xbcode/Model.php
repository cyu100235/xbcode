<?php
namespace xbcode;

use support\Cache;
use xbcode\utils\MysqlUtil;
use xbcode\model\BaseModel;

/**
 * 通用模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Model extends BaseModel
{
    // 定义全局的查询范围
    protected $globalScope = ['appid'];

    /**
     * 全局查询范围
     * @param mixed $query
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function scopeAppid($query)
    {
        self::setAppid($query, true);
    }

    /**
     * 写入前事件
     * 新增和更新都会触发
     * @param mixed $model
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function onBeforeWrite($model)
    {
        self::setAppid($model);
    }

    /**
     * 删除前事件
     * @param mixed $model
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function onBeforeDelete($model)
    {
        self::setAppid($model);
    }

    /**
     * 设置APPID
     * @param mixed $model 模型
     * @param bool $isQuery 是否是查询
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function setAppid($model, bool $isQuery = false)
    {
        // 获取站点ID
        $saasAppid = request()->saas_appid ?? null;
        if (!$saasAppid) {
            return;
        }
        // 获取当前表名
        $name   = $model->getTable();
        // 获取表字段
        $fields = Cache::get("xb_{$name}_fields",[]);
        // 检测表字段未缓存
        if (!$fields) {
            // 获取表字段
            $fields = MysqlUtil::getColumnName($name);
            // 缓存表字段(有效期10分钟)
            Cache::set("xb_{$name}_fields", $fields, 600);
        }
        // 检测当前表是否SAAS表
        if (!in_array('saas_appid', $fields)) {
            return;
        }
        if ($isQuery) {
            // 查询条件
            $model->where("{$name}.saas_appid", $saasAppid);
        } else {
            // 写入数据
            $model->saas_appid = $saasAppid;
        }
    }
}