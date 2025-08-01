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
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Custom\Alert;

/**
 * 提示词布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait PromptLayout
{
    /**
     * 提示词组件列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $prompt = [];

    /**
     * 添加头部提示词组件
     * @param string $content
     * @return Alert
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderPrompt(string $content)
    {
        $component = new Alert;
        $component->type('primary');
        $component->closable(false);
        $component->content($content);
        $this->prompt[] = [
            'type' => 'xbPrompt',
            'props' => $component,
        ];
        return $component;
    }

    /**
     * 获取提示词组件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getPrompt()
    {
        return $this->prompt;
    }
}
