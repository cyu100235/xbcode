<?php
namespace xbcode\providers\dict;

use Exception;
use app\model\DictTag;
use app\model\DictData;

/**
 * 字典数据操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictDataProvider
{
    /**
     * 添加字典数据
     * @param string $name 字典标识
     * @param string $title 数据名
     * @param string $value 数据值
     * @param bool $useState
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addTagData(string $name, string $title, string $value,bool $useState = false)
    {
        // 检测字典是否存在
        $tag = DictTag::where('name', $name)->find();
        if (!$tag) {
            throw new Exception('字典标签不存在');
        }
        $data = [
            'dict_id' => $tag['id'],
            'name' => $name,
            'label' => $title,
            'value' => $value,
            'state' => $useState ? '20' : '10',
        ];
        $model = new DictData;
        $model->save($data);
        return $model;
    }
    
    /**
     * 删除字典数据
     * @param string $name 字典标识
     * @param string $label 数据名
     * @param string $value 数据值
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function delTagData(string $name, string $label = '', string $value = '')
    {
        $tag = DictTag::where('name', $name)->find();
        if (!$tag) {
            throw new Exception('字典标签不存在');
        }
        $where = [
            'dict_id' => $tag['id'],
        ];
        if ($label) {
            $where['label'] = $label;
        }
        if ($value) {
            $where['value'] = $value;
        }
        $model = DictData::where($where)->find();
        if (!$model) {
            return true;
        }
        return $model->delete();
    }
}