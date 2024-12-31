<?php
namespace xbcode\builder\table\buttons;
use xbcode\builder\ListBuilder;

/**
 * 表格顶部按钮
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait TopButtonTrait
{
    /**
     * 表格顶部按钮
     * @var array
     * @author 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    private $topButtonList = [];
    
    /**
     * 添加表格头部按钮
     * @param string $field
     * @param string $title
     * @param array $pageData
     * @param array $message
     * @param array $button
     * @return \xbcode\builder\ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addTopButton(string $field, string $title, array $pageData = [], array $message = [], array $button = []): ListBuilder
    {
        $btnData = $this->checkUsedAttrs(
            $field,
            $title,
            $pageData,
            $message,
            $button
        );
        // 设置按钮
        $this->topButtonList[] = $btnData;
        return $this;
    }
}