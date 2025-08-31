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
namespace plugin\xbCode\api;

use Exception;
use plugin\xbUpload\api\Files;
use plugin\xbCode\app\model\Config;

/**
 * 配置接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigApi
{
    /**
     * 分组名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $group = '';

    /**
     * 是否解析层级数据
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $parseLevel = false;

    /**
     * 构造函数
     * @param string $group
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(string $group = '')
    {
        $this->group = $group;
    }

    /**
     * 创建示例
     * @param string $group
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
     * 
     * @param string $type
     * @throws \Exception
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
     * 解析配置层级数据
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function parseLevel()
    {
        $this->parse = true;
        return $this;
    }

    /**
     * 读取配置项
     * @param string $name
     * @param mixed $default
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function get(string $name = '', mixed $default = '')
    {
        // 是否读取多配置项
        if (str_contains($name, ',')) {
            $names = explode(',', $name);
            $data = [];
            foreach ($names as $value) {
                $data[$value] = $this->get($value, $default);
            }
            return $data;
        }
        // 解析斜杠与点号获取分组与配置项名称
        $this->readGroupParse('/');
        $this->readGroupParse('.');
        // 检测分组名称
        if (empty($this->group)) {
            throw new Exception('获取配置项分组名称失败');
        }
        $where = [
            'group' => $this->group,
        ];
        if ($name) {
            $where['name'] = $name;
        }
        $data = Config::where($where)->column('value', 'name');
        if (empty($data)) {
            return $default;
        }
        // 替换文件URL
        $data = ConfigChecked::replaceFileUrl($data);
        // 解析配置项层级
        if ($this->parseLevel) {
            $data = ConfigChecked::getConfigValue($data);
        }
        if (count($data) === 1 && isset($data[$name])) {
            return $data[$name];
        }
        // 返回全部配置
        return $data;
    }

    /**
     * 保存配置
     * @param array $data
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function set(array $data)
    {
        // 是否有验证器
        $validate = $data['xbValidate'] ?? null;
        if ($validate) {
            // 解析层级做验证
            $valiData = ConfigChecked::getConfigValue($data);
            xbValidate($validate, $valiData);
            unset($data['xbValidate']);
        }
        try {
            $this->group = ConfigChecked::getGroupName($this->group);
        } catch (\Throwable $th) {
        }
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
            if (is_string($value) && $value) {
                if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
                    $value = Files::path($value) ?: $value;
                }
            }
            if (is_array($value) && !empty($value)) {
                $value = array_map(function ($item) {
                    if (empty($item)) {
                        return $item;
                    }
                    try {
                        // 检测是否URL地址
                        $value = Files::path($item);
                    } catch (\Throwable $th) {
                        $value = json_encode($item, 256);
                    }
                    return $value;
                }, $value);
            }
            $configData = [
                'group' => $this->group,
                'name' => $field,
                'value' => $value,
            ];
            if (!$model->save($configData)) {
                throw new Exception('配置保存失败');
            }
        }
    }
}