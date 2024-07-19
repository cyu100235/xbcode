<?php
namespace app\common\base;

use think\db\Query;
use think\Paginator;

/**
 * 基类查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class BaseQuery extends Query
{
    /**
     * 重写分页方法
     * @param int|array $listRows
     * @param int|bool $simple
     * @return \think\Paginator
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function paginate(int|array $listRows = null, int|bool $simple = false): Paginator
    {
        if (is_null($listRows) || empty($listRows)) {
            $listRows = input('limit', 20);
        }
        return parent::paginate($listRows, $simple);
    }
}