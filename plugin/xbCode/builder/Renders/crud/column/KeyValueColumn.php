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

use plugin\xbCode\builder\Components\Table\TableColumn;
use plugin\xbCode\builder\Components\Table\KeyValueLinkColumn;

/**
 * 键值列组件
 *
 * 把单元格内的多个字段组合成若干行「标签：值」，
 * 适用于「昵称/账号」「设备IP/脚本ID」「今日收益/总收益」这类同格多行展示。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait KeyValueColumn
{
    /**
     * 添加键值列
     * @param string $name 列键名
     * @param string $label 列标题
     * @param array $items 行配置，每项渲染为一行
     * - `label` 行标签
     * - `field` 取值字段名（amis 变量名）
     * - `placeholder` 字段为空时的占位文本，默认 `-`
     * - `color` 整行文字颜色
     * - `style` 整行样式，数组或 CSS 字符串
     * - `valueStyle` 值文本样式，数组或 CSS 字符串
     * - `valueClass` 值文本类名
     * - `tpl` 直接指定该行 HTML，设置后忽略上面几项
     * @param callable|array $option 列配置
     * @param array $tpl 容器配置
     * - `separator` 标签与值之间的分隔符，默认 `：`
     * - `className` 容器类名
     * - `style` 容器样式，数组或 CSS 字符串
     * @return KeyValueLinkColumn|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnKeyValue(string $name, string $label, array $items, callable|array $option = [], array $tpl = [])
    {
        /** @var KeyValueLinkColumn|TableColumn */
        $component = $this->useCustomColumn(new KeyValueLinkColumn($this->getUrl()), $name, $label, $option);
        $component->tpl($this->buildKeyValueTpl($items, $tpl));
        return $component;
    }

    /**
     * 构建键值列模板
     * @param array $items 行配置
     * @param array $tpl 容器配置
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildKeyValueTpl(array $items, array $tpl = []): string
    {
        $separator = (string) ($tpl['separator'] ?? '：');
        $className = trim((string) ($tpl['className'] ?? ''));
        $attrs = ($className === '' ? '' : ' class="' . htmlspecialchars($className, ENT_QUOTES) . '"')
            . $this->buildKeyValueStyleAttr($tpl['style'] ?? '');
        $lines = '';
        foreach ($items as $item) {
            $lines .= $this->buildKeyValueLine((array) $item, $separator);
        }
        return '<div' . $attrs . '>' . $lines . '</div>';
    }

    /**
     * 构建单行「标签：值」
     * @param array $item 行配置
     * @param string $separator 标签与值之间的分隔符
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildKeyValueLine(array $item, string $separator): string
    {
        $rowStyle = $item['style'] ?? '';
        if (!empty($item['color'])) {
            $rowStyle = $this->mergeKeyValueCss($rowStyle, ['color' => (string) $item['color']]);
        }
        $rowAttrs = $this->buildKeyValueStyleAttr($rowStyle);
        // 直接指定 HTML 时原样输出，便于单行做特殊排版
        if (!empty($item['tpl'])) {
            return '<div' . $rowAttrs . '>' . (string) $item['tpl'] . '</div>';
        }
        $field = trim((string) ($item['field'] ?? ''));
        $placeholder = (string) ($item['placeholder'] ?? '-');
        $text = '';
        if ((string) ($item['label'] ?? '') !== '') {
            $text = htmlspecialchars((string) $item['label'] . $separator, ENT_QUOTES);
        }
        // 字段为空时展示占位文本，避免出现空白行
        $value = $field === ''
            ? htmlspecialchars($placeholder, ENT_QUOTES)
            : '${' . $field . ' ? ' . $field . ' : \'' . $this->escapeKeyValueText($placeholder) . '\'}';
        $valueClass = trim((string) ($item['valueClass'] ?? ''));
        $valueAttrs = ($valueClass === '' ? '' : ' class="' . htmlspecialchars($valueClass, ENT_QUOTES) . '"')
            . $this->buildKeyValueStyleAttr($item['valueStyle'] ?? '');
        return '<div' . $rowAttrs . '>' . $text . '<span' . $valueAttrs . '>' . $value . '</span></div>';
    }

    /**
     * 构建 style 属性
     * @param mixed $style 样式数组或 CSS 字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildKeyValueStyleAttr(mixed $style): string
    {
        $css = $this->toKeyValueCss($style);
        return $css === '' ? '' : ' style="' . htmlspecialchars($css, ENT_QUOTES) . '"';
    }

    /**
     * 追加样式到已有样式
     * @param mixed $style 原有样式，数组或 CSS 字符串
     * @param array $extra 追加样式
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function mergeKeyValueCss(mixed $style, array $extra): string
    {
        return $this->toKeyValueCss($style) . $this->toKeyValueCss($extra);
    }

    /**
     * 样式转 CSS 字符串
     * @param mixed $style 样式数组或 CSS 字符串
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function toKeyValueCss(mixed $style): string
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
     * 转义 amis 表达式里的字符串字面量
     * @param string $text 原始文本
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function escapeKeyValueText(string $text): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\\'"], $text);
    }
}
