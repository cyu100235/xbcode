<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\model;

use plugin\xbCode\Model;
use plugin\xbCode\api\AdminApi;
use plugin\xbCode\api\AdminSession;

/**
 * 管理员角色模型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminRole extends Model
{
    // 设置JSON字段转换
    protected $json = ['rule', 'theme'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 获取权限规则
     * @param mixed $value
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getRuleAttr($value)
    {
        if ($value && is_string($value)) {
            $value = json_decode($value, true);
        }
        $value = empty($value) ? [] : $value;
        return $value;
    }

    /**
     * 转换权限数据格式
     * @param mixed $value
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setRuleAttr($value)
    {
        if ($value && is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return $value;
    }

    /**
     * 写入后清理该角色下管理员的信息缓存
     * 角色名称 / 权限变更后，所属管理员的用户信息缓存需立即失效
     * 角色写入频率很低，此处不做字段差异判断，避免 json 字段差异比对误判
     * @param mixed $model
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function onAfterWrite($model)
    {
        AdminApi::forgetRoleUserinfo($model->getKey());
        // 单点登录配置变更后清理缓存，中间件下次访问即重新读取
        AdminSession::forgetRole((int) $model->getKey());
    }
}
