<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

/**
 * 地区联动数据接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class RegionApi
{
    /**
     * 行政区数据来源
     * https://github.com/modood/Administrative-divisions-of-China (WTFPL)
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected const SOURCE_URL = 'https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/pca-code.json';

    /**
     * 地区联级选项缓存
     * @var array|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $options = null;

    /**
     * 地区联级选项文件路径
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function file(): string
    {
        return runtime_path() . '/temp/city.json';
    }

    /**
     * 下载并生成地区联级选项文件（已存在时跳过）
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function download(): bool
    {
        $cityFile = static::file();
        if (is_file($cityFile) && filesize($cityFile) > 0) {
            return true;
        }
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'Mozilla/5.0',
            ],
        ]);
        $content = @file_get_contents(static::SOURCE_URL, false, $context);
        if (empty($content)) {
            return false;
        }
        $data = json_decode($content, true);
        if (empty($data)) {
            return false;
        }
        $options = static::format($data);
        if (empty($options)) {
            return false;
        }
        static::$options = $options;
        $dir = dirname($cityFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return (bool) @file_put_contents($cityFile, json_encode($options, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 获取地区联级选项（label/value/children）
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function options(): array
    {
        if (static::$options !== null) {
            return static::$options;
        }
        $cityFile = static::file();
        if (!is_file($cityFile)) {
            return static::$options = [];
        }
        $options = json_decode((string) file_get_contents($cityFile), true);
        return static::$options = is_array($options) ? $options : [];
    }

    /**
     * 按行政区编码解析省、市、区县名称
     * @param string $code
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function names(string $code): array
    {
        $names = ['province' => '', 'city' => '', 'district' => ''];
        $path = static::findPath(static::options(), trim($code), []);
        if (empty($path)) {
            return $names;
        }
        $path = array_pad($path, 3, '');
        return [
            'province' => (string) $path[0],
            'city' => (string) $path[1],
            'district' => (string) $path[2],
        ];
    }

    /**
     * 按省、市、区县名称反查行政区编码
     * @param string $province
     * @param string $city
     * @param string $district
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function code(string $province, string $city = '', string $district = ''): string
    {
        return static::findCode(static::options(), [$province, $city, $district], 0);
    }

    /**
     * 转换为级联选择器选项结构（label/value/children）
     * @param array $nodes
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function format(array $nodes): array
    {
        $options = [];
        foreach ($nodes as $node) {
            if (empty($node['name'])) {
                continue;
            }
            $item = [
                'label' => $node['name'],
                'value' => (string) ($node['code'] ?? $node['name']),
            ];
            if (!empty($node['children'])) {
                $item['children'] = static::format($node['children']);
            }
            $options[] = $item;
        }
        return $options;
    }

    /**
     * 递归查找行政区编码对应的名称路径
     * @param array $options
     * @param string $value
     * @param array $path
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function findPath(array $options, string $value, array $path): array
    {
        if ($value === '') {
            return [];
        }
        foreach ($options as $option) {
            $current = array_merge($path, [(string) ($option['label'] ?? '')]);
            if ((string) ($option['value'] ?? '') === $value) {
                return $current;
            }
            if (!empty($option['children'])) {
                $found = static::findPath($option['children'], $value, $current);
                if (!empty($found)) {
                    return $found;
                }
            }
        }
        return [];
    }

    /**
     * 递归按名称匹配行政区编码
     * @param array $options
     * @param array $names 依次为省、市、区县名称
     * @param int $level
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function findCode(array $options, array $names, int $level): string
    {
        $target = trim((string) ($names[$level] ?? ''));
        if ($target === '') {
            return '';
        }
        foreach ($options as $option) {
            if ((string) ($option['label'] ?? '') !== $target) {
                continue;
            }
            $value = (string) ($option['value'] ?? '');
            $children = (array) ($option['children'] ?? []);
            $next = trim((string) ($names[$level + 1] ?? ''));
            if ($level >= 2 || empty($children) || $next === '') {
                return $value;
            }
            $code = static::findCode($children, $names, $level + 1);
            if ($code !== '') {
                return $code;
            }
        }
        return '';
    }
}
