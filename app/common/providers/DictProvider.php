<?php

namespace app\common\providers;

use app\model\Dict;
use think\facade\Cache;

/**
 * 字典提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictProvider
{
    /**
     * 实例
     * @var DictProvider
     */
    protected static $_instance;

    /**
     * 字典标识
     * @var 
     */
    protected $name;

    /**
     * 获取实例
     * @param string $name
     * @return DictProvider
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(string $name)
    {
        if (!static::$_instance) {
            static::$_instance = new DictProvider;
        }
        static::$_instance->name = $name;
        return static::$_instance;
    }

    /**
     * 获取全部字典数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getCacheDict()
    {
        $data = Cache::get('dict_all', []);
        if (empty($data)) {
            Dict::cacheDict();
            $data = Cache::get('dict_all', []);
        }
        return $data;
    }

    /**
     * 解析字典数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function parse()
    {
        $data = $this->getCacheDict();
        if (empty($data)) {
            return [];
        }
        $data = array_column($data, 'content', 'name');
        $data = array_map(function ($item) {
            $content = explode("\n", $item);
            $content = array_map(function ($item) {
                if (strpos($item, '=') !== false) {
                    // 如果有=号，则分割为value和label
                    $content = explode("=", $item);
                } else {
                    // 如果没有=号，则默认值为label
                    $content = [$item, $item];
                }
                return [
                    'value' => $content[0],
                    'label' => $content[1],
                ];
            }, $content);
            return $content;
        }, $data);
        return $data;
    }

    /**
     * 获取某个字典数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getDictData()
    {
        $data = $this->parse();
        if (empty($data)) {
            return [];
        }
        $data = $data[$this->name] ?? [];
        return $data;
    }


    /**
     * 字典数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function dict()
    {
        $data = $this->getDictData();
        // 转换为键值对
        $data = array_column($data, 'label', 'value');
        return $data;
    }

    /**
     * 获取选项数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function options()
    {
        $data = $this->getDictData();
        return $data;
    }

    /**
     * 获取样式数据
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function style()
    {
        $data = $this->getDictData();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = [
                'type' => $value['label']
            ];
        }
        return $list;
    }

    /**
     * 获取开关数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function switch(string $value)
    {
        $data = $this->getDictData();
        $data = array_map(function ($item) use ($value) {
            if ($item['value'] == $value) {
                return [
                    'value' => $item['value'],
                    'text' => $item['label'],
                ];
            }
        }, $data);
        $data = array_filter($data);
        $data = current($data);
        return $data;
    }

    /**
     * 获取label
     * @param string $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getLabel(string $value)
    {
        $data = $this->dict();
        return $data[$value] ?? null;
    }

    /**
     * 获取value
     * @param string $label
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getValue(string $label)
    {
        $data = $this->getDictData();
        // 转换为键值对
        $data = array_column($data, 'value', 'label');
        return $data[$label] ?? null;
    }
}
