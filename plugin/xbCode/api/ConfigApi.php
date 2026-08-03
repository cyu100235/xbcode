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

use Exception;
use support\think\Cache;
use plugin\xbUpload\api\Files;
use plugin\xbCode\app\model\Config;

/**
 * 配置接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigApi
{
    /**
     * 缓存KEY
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $keyName = 'xbcode-config';

    /**
     * 分组名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $group = '';

    /**
     * 附件目录
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $attachment = 'attachment/';

    /**
     * 是否解析层级数据
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $level = false;

    /**
     * 构造函数
     * @param string $group 分组名称
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(string $group)
    {
        $this->group = $group;
    }

    /**
     * 获取实例
     * @param string $group 分组名称
     * @return ConfigApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(string $group)
    {
        $class = new static($group);
        return $class;
    }

    /**
     * 获取配置缓存
     * @param bool $force 是否强制刷新缓存
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getCache(bool $force = false)
    {
        $keyName = $this->keyName;
        if (!empty(request()->saas_appid)) {
            $keyName .= '-' . request()->saas_appid;
        }
        $data = Cache::get($keyName);
        if ($data && !$force) {
            return $data;
        }
        $data = Config::select()->toArray();
        Cache::set($keyName, $data);
        return $data;
    }

    /**
     * 查询分组
     * @param string $type
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function readGroupParse(string $type)
    {
        if (str_contains($this->group, $type)) {
            $temp = explode($type, $this->group);
            $this->group = end($temp);
        }
    }

    /**
     * 是否解析层级数据
     * @param bool $value
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function level(bool $value = false)
    {
        $this->level = $value;
        return $this;
    }

    /**
     * 获取配置
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function _config()
    {
        $group = $this->group;
        $data = $this->getCache();
        $list = [];
        foreach ($data as $value) {
            $list[$value['group']][$value['name']] = $value['value'];
        }
        $config = $list[$group] ?? [];
        return $config;
    }

    /**
     * 将 JSON 字符串转换为数组
     * @param mixed $value 待转换的值
     * @return mixed 转换后的值
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function convertJsonToArray(mixed $value): mixed
    {
        // 只处理字符串类型
        if (!is_string($value)) {
            return $value;
        }

        // 去除首尾空格
        $trimmed = trim($value);

        // 检测是否为 JSON 格式（以 { 或 [ 开头）
        if (empty($trimmed) || ($trimmed[0] !== '{' && $trimmed[0] !== '[')) {
            return $value;
        }

        // 尝试解析 JSON
        $decoded = json_decode($trimmed, true);

        // 检查 JSON 解析是否成功
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 解析失败或结果不是数组，返回原值
        return $value;
    }

    /**
     * 递归处理数组中的 JSON 字符串
     * @param mixed $data 待处理的数据
     * @return mixed 处理后的数据
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function processJsonInData(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->processJsonInData($value);
            }
            return $data;
        }
        return $this->convertJsonToArray($data);
    }

    /**
     * 获取配置
     * @param string $name
     * @param mixed $default
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function get(string $name = '', mixed $default = '')
    {
        // 检测分组名称
        if (empty($this->group)) {
            throw new Exception('获取配置项分组名称失败');
        }
        // 解析分组
        $this->readGroupParse('/');
        $this->readGroupParse('.');
        // 获取全部配置作为数据解析
        $data = $this->_config();
        if (empty($data)) {
            return $default;
        }
        // 是否读取多配置项
        if (str_contains($name, ',')) {
            $names = explode(',', $name);
            $res = [];
            foreach ($names as $value) {
                $res[$value] = $this->get($value, $default);
            }
            return $res;
        }

        // 统一替换文件URL
        $data = ConfigChecked::replaceFileUrl($data);

        // 获取当前分组下的所有配置项
        if ($name === '') {
            if ($this->level) {
                $result = ConfigChecked::getConfigValue($data);
                return $this->processJsonInData($result);
            }
            return $this->processJsonInData($data);
        }

        // 1. 优先进行精确匹配读取
        if (array_key_exists($name, $data)) {
            return $this->convertJsonToArray($data[$name]);
        }

        // 2. 层级数据读取支持
        if ($this->level || str_contains($name, '.')) {
            $tree = ConfigChecked::getConfigValue($this->processJsonInData($data));
            $parts = explode('.', $name);
            $current = $tree;
            foreach ($parts as $part) {
                if (is_array($current) && array_key_exists($part, $current)) {
                    $current = $current[$part];
                } else {
                    return $default;
                }
            }
            return $this->convertJsonToArray($current);
        }

        // 3. 开启前缀匹配支持 (如 'qcloud' 获取所有 'qcloud.xxx' 项)
        $prefix = $name . '.';
        $subset = [];
        foreach ($data as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $subset[$key] = $value;
            }
        }
        if (!empty($subset)) {
            return $this->processJsonInData($subset);
        }

        return $default;
    }

    /**
     * 保存配置
     * @param array $data
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function set(array $data)
    {
        // 1.是否有验证器
        $validate = $data['xbValidate'] ?? null;
        if ($validate) {
            // 2.解析层级做验证
            $valiData = ConfigChecked::getConfigValue($data);
            xbValidate($validate, $valiData);
            unset($data['xbValidate']);
        }
        // 3.获取分组名称
        $group = $this->group;
        if (str_contains($group, '/') || str_contains($group, '.')) {
            $group = ConfigChecked::getGroupName($group);
        }
        $this->group = $group;
        foreach ($data as $field => $value) {
            // 查询条件
            $where = [
                'group' => $this->group,
                'name' => $field,
            ];
            $model = Config::where($where)->find();
            if (!$model) {
                $model = new Config;
            }
            // 检测是字符串URL
            if (is_string($value) && $value && str_contains($value, $this->attachment)) {
                $value = Files::make()->path($value) ?: $value;
            }
            if (is_array($value) && !empty($value)) {
                $value = array_map(function ($item) {
                    if ($item === '' || $item === null) {
                        return $item;
                    }
                    try {
                        // 检测是否附件路径
                        if (str_contains($item, $this->attachment)) {
                            $item = Files::make()->path($item);
                        }
                    } catch (\Throwable $th) {
                    }
                    return $item;
                }, $value);
            }
            $value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value;
            $configData = [
                'group' => $this->group,
                'name' => $field,
                'value' => $value,
            ];
            if (!$model->save($configData)) {
                throw new Exception('配置保存失败');
            }
        }
        $this->getCache(true);
    }

    /**
     * 追加配置项
     * @param string $name 配置项名称
     * @param mixed $value 配置项值
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(string $name, mixed $value)
    {
        // 检测分组名称
        if (empty($this->group)) {
            throw new Exception('追加配置项分组名称失败');
        }
        
        // 解析分组
        $group = $this->group;
        if (str_contains($group, '/') || str_contains($group, '.')) {
            $group = ConfigChecked::getGroupName($group);
        }
        
        // 查询配置项是否已存在
        $where = [
            'group' => $group,
            'name' => $name,
        ];
        $exists = Config::where($where)->find();
        if ($exists) {
            throw new Exception("配置项 {$name} 已存在，请使用 set 方法更新");
        }
        
        // 追加新的配置项
        $this->set([$name => $value]);
        // 刷新缓存
        $this->getCache(true);
    }

    /**
     * 删除缓存
     * @param string $name 配置项名称
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(string $name = '')
    {
        $where = [
            'group' => $this->group,
            'name' => $name,
        ];
        if ($name) {
            $where['name'] = $name;
        }
        Config::where($where)->delete();
        $this->getCache(true);
    }
}
