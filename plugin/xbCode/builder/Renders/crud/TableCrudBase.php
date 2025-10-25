<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud;

/**
 * 表格基础参数
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TableCrudBase
{
    /**
     * 分页字段名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $pageField = 'page';

    /**
     * 每页显示条数字段名
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $limitField = 'limit';

    /**
     * 每页显示条数
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected int $limit = 30;
    
    /**
     * 设置页码字段名
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setPageField(string $field)
    {
        $this->useCRUD()->pageField($field);
        $this->pageField = $field;
        return $this;
    }

    /**
     * 获取页码字段名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getPageField()
    {
        return $this->pageField;
    }

    /**
     * 设置每页显示条数字段名
     * @param string $field
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setLimitField(string $field)
    {
        $this->useCRUD()->perPageField($field);
        $this->limitField = $field;
        return $this;
    }

    /**
     * 获取每页显示条数字段名
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getLimitField()
    {
        return $this->pageField;
    }
    
    /**
     * 设置每页显示条数
     * @param int $limit
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setLimit(int $limit)
    {
        $this->useCRUD()->perPage($limit);
        $this->limit = $limit;
        return $this;
    }

    /**
     * 获取每页显示条数
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
