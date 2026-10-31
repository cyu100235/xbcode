<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Tpl;
use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Table\TableColumn;
use plugin\xbCode\builder\Components\Action\DialogAction;

/**
 * 内联行为列组件
 *
 * 在一个单元格内并排展示若干「值 / 操作入口」，例如「余额（跳转明细）+ 调整（弹窗）」。
 * 单元格统一渲染为 wrapper，内部按注册顺序横排，可自定义布局样式。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InlineColumn
{
    /**
     * 添加内联行为列
     * @param string $name 列键名
     * @param string $label 列标题
     * @param array $items 单元格内容，按顺序横向排列，每项为数组：
     * - `type` 内容类型：`link` 单页跳转、`dialog` 弹窗、`ajax` 请求、`url` 新页跳转、`tpl` 模板、`component` 组件实例
     * - `field` 取值字段名（amis 变量名），`text` 未设置时展示该字段值
     * - `text` 展示文本，支持 amis 表达式，默认 `${field}`
     * - `prefix` / `suffix` 展示文本前后缀
     * - `placeholder` 字段为空时的展示文本
     * - `url` 跳转 / 弹窗 / 请求地址
     * - `back` 单页跳转时是否追加返回地址，默认 false
     * - `title` 弹窗标题
     * - `size` 弹窗尺寸，默认 md
     * - `confirm` 请求前确认文案，设置后点击需二次确认
     * - `blank` 新页跳转是否新开标签页
     * - `class` 文本类名，默认按类型取（跳转/请求为 text-primary，弹窗为 text-info）
     * - `style` 文本样式，数组或 CSS 字符串
     * - `tpl` 类型为 `tpl` / `component` 时的内容
     * - `component` 直接指定渲染器实例
     * @param callable|array $option 列配置
     * @param array $wrapper 单元格容器配置
     * - `className` 容器类名
     * - `style` 容器样式，数组或 CSS 字符串
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnInline(string $name, string $label, array $items, callable|array $option = [], array $wrapper = [])
    {
        /** @var TableColumn $component */
        $component = $this->useCustomColumn(TableColumn::class, $name, $label, $option);
        $component->textOverflow('default');
        $body = [];
        foreach ($items as $item) {
            $built = $this->buildInlineItem((array) $item);
            if ($built !== null) {
                $body[] = $built;
            }
        }
        $component->type('wrapper')->body($body);
        $className = trim((string) ($wrapper['className'] ?? ''));
        if ($className !== '') {
            $component->className($className);
        }
        // 容器样式沿用 amis 的 style 配置，数组与 CSS 字符串均可
        if (isset($wrapper['style']) && $wrapper['style'] !== '') {
            $component->style($wrapper['style']);
        }
        return $component;
    }

    /**
     * 构建单元格内的单个内容
     * @param array $item 内容配置
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildInlineItem(array $item)
    {
        $type = (string) ($item['type'] ?? 'link');
        if ($type === 'component') {
            return $item['component'] ?? null;
        }
        if ($type === 'tpl') {
            return Tpl::make()->tpl((string) ($item['tpl'] ?? ''));
        }
        $body = [$this->inlineTextTpl($item, $type)];
        switch ($type) {
            case 'dialog':
                $action = new DialogAction();
                // 未指定标题/尺寸时沿用弹窗组件的默认值
                $dialogOption = array_filter([
                    'title' => (string) ($item['title'] ?? ''),
                    'size' => (string) ($item['size'] ?? ''),
                ]);
                // 按钮文本优先取 label，未设置时用展示文本
                $label = (string) ($item['label'] ?? $item['text'] ?? '');
                $action->dialogModel('GET:' . (string) ($item['url'] ?? ''), $label, $dialogOption);
                // 覆盖 dialogModel 内的主按钮样式，保持与列表内其它入口一致的链接样式
                $action->level('link')->actionType('dialog');
                $action->body($body);
                return $action;
            case 'ajax':
                $action = new AjaxAction();
                $action->level('link');
                $action->api((string) ($item['url'] ?? ''));
                if (!empty($item['confirm'])) {
                    $action->confirmTitle((string) ($item['title'] ?? '温馨提示'));
                    $action->confirmText((string) $item['confirm']);
                }
                $action->body($body);
                return $action;
            case 'url':
                $action = new UrlAction();
                $action->level('link');
                $action->url((string) ($item['url'] ?? ''));
                if (!empty($item['blank'])) {
                    $action->blank(true);
                }
                $action->body($body);
                return $action;
            case 'link':
            default:
                $action = new LinkAction($this->getUrl());
                $action->level('link');
                $action->body($body);
                $action->link((string) ($item['url'] ?? '${' . (string) ($item['field'] ?? '') . '}'));
                // 单页跳转按需追加返回当前列表的地址
                if (!empty($item['back'])) {
                    $action->isBack();
                }
                return $action;
        }
    }

    /**
     * 构建单元格内的文本模板
     * @param array $item 内容配置
     * @param string $type 内容类型
     * @return Tpl
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function inlineTextTpl(array $item, string $type): Tpl
    {
        $field = trim((string) ($item['field'] ?? ''));
        $text = (string) ($item['text'] ?? '');
        if ($text === '') {
            $text = $field === '' ? '' : '${' . $field . '}';
            // 配置了占位文本时，字段为空展示占位文本
            if ($field !== '' && isset($item['placeholder'])) {
                $text = '${' . $field . ' ? ' . $field . ' : \'' . $this->escapeInlineText((string) $item['placeholder']) . '\'}';
            }
        }
        $text = $this->inlineTextHtml((string) ($item['prefix'] ?? ''))
            . $text
            . $this->inlineTextHtml((string) ($item['suffix'] ?? ''));
        $className = trim((string) ($item['class'] ?? ($type === 'dialog' ? 'text-info' : 'text-primary')));
        $attrs = ($className === '' ? '' : ' class="' . $className . ' cursor-pointer"')
            . $this->inlineStyleAttr($item['style'] ?? ['font-size' => '12px']);
        return Tpl::make()->tpl('<span' . $attrs . '>' . $text . '</span>');
    }

    /**
     * 构建 style 属性
     * @param mixed $style 样式数组或 CSS 字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function inlineStyleAttr(mixed $style): string
    {
        $css = $this->inlineCss($style);
        return $css === '' ? '' : ' style="' . htmlspecialchars($css, ENT_QUOTES) . '"';
    }

    /**
     * 样式转 CSS 字符串
     * @param mixed $style 样式数组或 CSS 字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function inlineCss(mixed $style): string
    {
        if (is_string($style)) {
            $style = trim($style);
            return $style === '' ? '' : rtrim($style, ';') . ';';
        }
        if (!is_array($style)) {
            return '';
        }
        $css = '';
        foreach ($style as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $css .= $key . ': ' . $value . ';';
        }
        return $css;
    }

    /**
     * 转义展示文本
     * @param string $text 原始文本
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function inlineTextHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * 转义 amis 表达式里的字符串字面量
     * @param string $text 原始文本
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function escapeInlineText(string $text): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\\'"], $text);
    }
}
