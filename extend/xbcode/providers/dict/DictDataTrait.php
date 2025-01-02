<?php
namespace xbcode\providers\dict;

use support\Cache;
use app\model\DictTag;
use app\model\DictData;

/**
 * 字典数据获取使用
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait DictDataTrait
{
    /**
     * 处理并缓存字典数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function cacheDictData()
    {
        $data = DictTag::where('state', '20')->order('sort asc,id asc')->column('id,title,name', 'name');
        foreach ($data as &$value) {
            // 查询条件
            $where         = [
                'dict_id' => $value['id'],
                'state' => '20'
            ];
            $children      = DictData::where($where)->order('sort asc,id asc')->column('label,value');
            $value['dict'] = $children;
        }
        Cache::set('dict_data', $data, 3600);
    }
    
    /**
     * 获取缓存字典数据
     * @param bool $force 是否强制刷新缓存
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getCacheDict(bool $force = false)
    {
        $data = Cache::get('dict_data', []);
        if (empty($data) || $force) {
            // 缓存字典数据
            self::cacheDictData();
            // 获取缓存数据
            $data = Cache::get('dict_data', []);
        }
        return $data;
    }

    /**
     * 获取字典选项
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function dictOptions()
    {
        $data = $this->getCacheDict();
        if (empty($data)) {
            return [];
        }
        $data = array_map(function ($item) {
            return [
                'value' => $item['name'],
                'label' => $item['title'],
            ];
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
        $data = $this->getCacheDict();
        if (empty($data)) {
            return [];
        }
        $data = $data[$this->name]['dict'] ?? [];
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
     * 过滤数据
     * @param array $names
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function filter(array $names)
    {
        $options = $this->getDictData();
        $filter  = array_filter($options, function ($item) use ($names) {
            return in_array($item['value'], $names);
        });
        $filter  = array_values($filter);
        return $filter;
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
     * 开关数据
     * @param string $value 示例：10关闭，20开启
     * @return array
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