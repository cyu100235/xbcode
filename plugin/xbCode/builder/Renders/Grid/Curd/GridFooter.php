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
namespace plugin\xbCode\builder\Renders\Grid\Curd;

/**
 * 表格工具栏
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait GridFooter
{
    /**
     * 表格底部工具栏
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $footerToolbar = [];

    /**
     * 初始化表格底部工具栏
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function initFooterToolbar()
    {
        $this->footerToolbar = [
            'statistics',
            'pagination',
        ];
    }

    /**
     * 渲染表格底部工具栏
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderFooterToolbar(): array
    {
        return $this->footerToolbar;
    }
}
