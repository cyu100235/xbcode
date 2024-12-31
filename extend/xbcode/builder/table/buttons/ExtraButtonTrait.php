<?php
namespace xbcode\builder\table\buttons;
use xbcode\builder\ListBuilder;

/**
 * 表格顶部扩展按钮
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ExtraButtonTrait
{
    /**
     * 表格顶部扩展按钮
     * @var array
     * @author 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    private $extraButtonList = [];
    
    /**
     * 添加底部按钮
     * @param string $field
     * @param string $title
     * @param array $pageData
     * @param array $message
     * @param array $button
     * @return ListBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    public function addExtraButton(string $field, string $title, array $pageData = [], array $message = [], array $button = []): ListBuilder
    {
        $btnData = $this->checkUsedAttrs(
            $field,
            $title,
            $pageData,
            $message,
            $button
        );
        // 设置按钮
        $this->extraButtonList[] = $btnData;
        return $this;
    }
}