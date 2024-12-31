<?php
namespace xbcode\builder\table\buttons;
use xbcode\builder\ListBuilder;

/**
 * 表格右侧按钮
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait RightButtonTrait
{
    /**
     * 表格列按钮
     * @var array
     * @author 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    private $rightButtonList = [];
    
    /**
     * 添加右侧列按钮
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-02-28
     * @param  string      $field
     * @param  string      $title
     * @param  array       $pageData
     * @param  array       $message
     * @param  array       $button
     * @return ListBuilder
     */
    public function addRightButton(string $field, string $title, array $pageData = [], array $message = [], array $button = []): ListBuilder
    {
        if (!isset($button['link'])) {
            $button['link'] = true;
        }
        $btnData = $this->checkUsedAttrs(
            $field,
            $title,
            $pageData,
            $message,
            $button
        );
        // 别名参数处理，仅右侧按钮
        foreach ($btnData['pageData']['aliasParams'] as $key => $value) {
            if (is_numeric($key)) {
                $btnData['pageData']['aliasParams'][$value] = $value;
                unset($btnData['pageData']['aliasParams'][$key]);
            }
        }
        // 设置按钮
        $this->rightButtonList[] = $btnData;
        return $this;
    }
}