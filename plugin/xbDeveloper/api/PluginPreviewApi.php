<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;

/**
 * 插件预览图接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginPreviewApi
{
    /**
     * 预览图模板根目录
     * @var string
     */
    protected string $previewDir;

    /**
     * 图标目录
     * @var string
     */
    protected string $iconDir;

    /**
     * 模板目录
     * @var string
     */
    protected string $templateDir;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->previewDir  = base_path() . '/plugin/xbDeveloper/data/preview';
        $this->iconDir     = $this->previewDir . '/icons';
        $this->templateDir = $this->previewDir . '/template';
    }

    /**
     * 实例化
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        return new static;
    }

    /**
     * 获取所有图标文件列表（文件名 => 路径）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getIcons(): array
    {
        return $this->scanSvgFiles($this->iconDir);
    }

    /**
     * 获取图标列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getIconList(): array
    {
        $data = $this->scanSvgFiles($this->iconDir);
        return array_map(function ($filename, $path) {
            $str = [
                base_path(),
                '/data/preview'
            ];
            $file = str_replace($str, '', $path);
            $url = str_replace('/plugin/', '/app/', $file);
            return [
                'label' => $filename,
                'value' => $filename,
                'image' => $url,
            ];
        }, array_keys($data), $data);
    }

    /**
     * 获取所有模板文件列表（文件名 => 路径）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getTemplates(): array
    {
        return $this->scanSvgFiles($this->templateDir);
    }

    /**
     * 获取图标背景模板列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getTemplateList(): array
    {
        $data = $this->scanSvgFiles($this->templateDir);
        return array_map(function ($filename, $path) {
            $str = [
                base_path(),
                '/data/preview'
            ];
            $file = str_replace($str, '', $path);
            $url = str_replace('/plugin/', '/app/', $file);
            return [
                'label' => $filename,
                'value' => $filename,
                'image' => $url,
            ];
        }, array_keys($data), $data);
    }
    

    /**
     * 创建插件预览图
     * @param array  $plugin       插件信息（需含 name 字段）
     * @param string $iconFile     图标文件名，如 "1.svg"，为空时随机选取
     * @param string $templateFile 模板文件名，如 "1.svg"，为空时随机选取
     * @param bool   $force        是否强制重新生成
     * @return string 生成的预览图路径
     * @copyright 贵州积木云网络有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create(array $plugin, string $iconFile = '', string $templateFile = '', bool $force = false): string
    {
        $targetPath = base_path() . "/plugin/{$plugin['name']}/preview.svg";
        if (file_exists($targetPath) && !$force) {
            return $targetPath;
        }

        // 解析图标与模板文件路径
        $iconPath = $this->resolveFile($this->iconDir, $iconFile, '图标');
        $tplPath  = $this->resolveFile($this->templateDir, $templateFile, '模板');

        // 合成 SVG
        $svgContent = $this->composite($tplPath, $iconPath);

        // 写入目标文件
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($targetPath, $svgContent);

        return $targetPath;
    }

    /**
     * 替换插件图标
     * @param string $name
     * @param string $iconPath
     * @param string $templatePath
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function replace(string $name, string $iconPath, string $templatePath = '')
    {
        if (empty($name)) {
            throw new Exception('插件标识参数错误');
        }
        if (empty($iconPath)) {
            throw new Exception('请上传插件图标');
        }
        $pluginPath = base_path() . "/plugin/{$name}";
        if (!is_dir($pluginPath)) {
            throw new Exception("插件目录不存在");
        }
        // 使用插件原本图标
        if (empty($templatePath)) {
            $templatePath = $pluginPath . '/preview.svg';
        }
        // 验证模板是否有图标预置点
        if (!preg_match('/<g id="icon"[^>]*>.*?<\/g>/s', file_get_contents($templatePath))) {
            throw new Exception("模板中不存在图标预置点");
        }
        // 最终生成位置
        $targetPath = $pluginPath . "/preview.svg";
        // 合成 SVG
        $svgContent = $this->composite($templatePath, $iconPath);
        // 写入目标文件
        file_put_contents($targetPath, $svgContent);
    }

    /**
     * 合成预览图 SVG
     *
     * 将图标 SVG 的内容提取并缩放后，嵌入模板的 {{icon}} 占位符位置。
     * 模板中的图标容器已做 translate(150,150)，坐标原点即中心，
     * 图标以 0,0 为中心居中渲染。
     *
     * @param string $tplPath  模板文件路径
     * @param string $iconPath 图标文件路径
     * @return string 合成后的 SVG 字符串
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function composite(string $tplPath, string $iconPath): string
    {
        $tplContent  = file_get_contents($tplPath);
        $iconContent = file_get_contents($iconPath);

        // 解析图标 viewBox 尺寸
        [$vw, $vh] = $this->parseViewBox($iconContent);

        // 目标尺寸：圆形容器半径 70，图标占 80%（即 112px）
        $targetSize = 112;

        // 缩放比例
        $scale = $targetSize / max($vw, $vh);

        // 居中偏移（以模板坐标原点 0,0 为中心）
        $offsetX = -($vw * $scale / 2);
        $offsetY = -($vh * $scale / 2);

        // 提取图标 <svg> 内部子元素
        $innerContent = $this->extractSvgInner($iconContent);

        // 构建图标片段：使用 g 标签包裹，应用缩放与偏移
        $iconFragment = sprintf(
            '<g transform="translate(%s, %s) scale(%s)">%s</g>',
            round($offsetX, 4),
            round($offsetY, 4),
            round($scale, 6),
            $innerContent
        );
        // 使用 DOMDocument 解析模板 SVG，找到 id="icon" 的 g 标签并替换内容
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;
        // 包装为合法 XML （避免实体解析问题）
        @$dom->loadXML($tplContent);
        $xpath = new \DOMXPath($dom);
        $iconNodes = $xpath->query('//*[@id="icon"]');
        if ($iconNodes && $iconNodes->length > 0) {
            $iconNode = $iconNodes->item(0);
            // 清空原有子节点
            while ($iconNode->firstChild) {
                $iconNode->removeChild($iconNode->firstChild);
            }
            // 将 $iconFragment 解析为 DOM 节点并插入
            $fragDom = new \DOMDocument();
            @$fragDom->loadXML('<root>' . $iconFragment . '</root>');
            foreach ($fragDom->documentElement->childNodes as $child) {
                $imported = $dom->importNode($child, true);
                $iconNode->appendChild($imported);
            }
        }
        $result = $dom->saveXML($dom->documentElement);
        // 移除 saveXML 可能添加的 XML 声明头
        $result = preg_replace('/^<\?xml[^?]*\?>\s*/', '', $result);
        return $result;
    }

    /**
     * 解析 SVG 的 viewBox 宽高
     * @param string $svgContent SVG 内容
     * @return array [width, height]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function parseViewBox(string $svgContent): array
    {
        // 尝试读取 viewBox="minX minY width height"
        if (preg_match('/viewBox=["\']([^"\']*)["\']/', $svgContent, $m)) {
            $parts = preg_split('/[\s,]+/', trim($m[1]));
            if (count($parts) >= 4) {
                return [(float)$parts[2], (float)$parts[3]];
            }
        }
        // 回退读取 width/height 属性
        $w = 1024;
        $h = 1024;
        if (preg_match('/<svg[^>]+width=["\']([\d.]+)["\']/', $svgContent, $mw)) {
            $w = (float)$mw[1];
        }
        if (preg_match('/<svg[^>]+height=["\']([\d.]+)["\']/', $svgContent, $mh)) {
            $h = (float)$mh[1];
        }
        return [$w, $h];
    }

    /**
     * 提取 SVG 内部子元素（去掉外层 svg 标签及 XML 声明）
     * @param string $svgContent 原始 SVG 内容
     * @return string 内部元素字符串
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function extractSvgInner(string $svgContent): string
    {
        // 去掉 XML 声明与 DOCTYPE
        $svgContent = preg_replace('/<\?xml[^?]*\?>/', '', $svgContent);
        $svgContent = preg_replace('/<!DOCTYPE[^>]*>/', '', $svgContent);

        // 提取 <svg ...> 与 </svg> 之间的内容
        if (preg_match('/<svg[^>]*>([\s\S]*?)<\/svg>/i', $svgContent, $m)) {
            return trim($m[1]);
        }

        // 若无法解析，返回原内容
        return trim($svgContent);
    }

    /**
     * 扫描目录下所有 SVG 文件
     * @param string $dir 目录路径
     * @return array [文件名 => 完整路径]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function scanSvgFiles(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }
        $files  = glob($dir . '/*.svg') ?: [];
        $result = [];
        foreach ($files as $path) {
            $result[basename($path)] = $path;
        }
        return $result;
    }

    /**
     * 解析文件路径（指定文件名时验证存在，否则顺序循环选取）
     * @param string $dir      目录路径
     * @param string $filename 文件名（为空时顺序循环）
     * @param string $label    类型说明（用于异常提示）
     * @return string 文件完整路径
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function resolveFile(string $dir, string $filename, string $label): string
    {
        if (!is_dir($dir)) {
            throw new Exception("{$label}目录不存在：{$dir}");
        }

        if ($filename !== '') {
            $path = $dir . '/' . $filename;
            if (!file_exists($path)) {
                throw new Exception("{$label}文件不存在：{$filename}");
            }
            return $path;
        }

        // 顺序循环选取
        $files = glob($dir . '/*.svg') ?: [];
        if (empty($files)) {
            throw new Exception("{$label}目录下不存在 SVG 文件");
        }
        sort($files);

        $indexFile = $dir . '/.seq_index';
        $index = 0;
        if (file_exists($indexFile)) {
            $index = (int)file_get_contents($indexFile);
        }
        $index = $index % count($files);
        file_put_contents($indexFile, (string)($index + 1));

        return $files[$index];
    }
}
