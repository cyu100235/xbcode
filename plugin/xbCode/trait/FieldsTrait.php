<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\trait;

/**
 * 安全字段处理
 * @author 楚羽幽 958416459@qq.com
 * @copyright 贵州积木云网络科技有限公司
 */
trait FieldsTrait
{
    /**
     * 输出安全字段
     * @param array $data 所需处理数据
     * @param array $fields 安全字段
     * @return array
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    protected function ouputFields(array $data, array $fields)
    {
        $list = [];
        foreach ($data as $key=>$value) {
            if (in_array($key, $fields)) {
                $list[$key] = $value;
            }
        }
        return $list;   
    }
}