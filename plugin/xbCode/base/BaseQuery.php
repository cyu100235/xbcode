<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\base;

use think\db\Query;
use think\Paginator;

/**
 * 重写查询
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BaseQuery extends Query
{
    /**
     * 重写分页方法
     * @param int|array $listRows 每页数量
     * @param int|bool $simple 是否简洁模式
     * @return Paginator
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function paginate(int|array|null $listRows = null, int|bool $simple = false): Paginator
    {
        if (is_null($listRows) || empty($listRows)) {
            $listRows = (int) request()->get('limit', 30);
        }
        return parent::paginate($listRows, $simple);
    }
}
