<?php
namespace xbcode\providers\dict;

use app\model\DictData;
use app\model\DictTag;

/**
 * 字典标签操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictTagProvider
{
    /**
     * 添加字典
     * @param string $name
     * @param string $title
     * @param bool $useState
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addTag(string $name, string $title, bool $useState = false)
    {
        // 检测字典是否已添加
        $model = DictTag::where('name', $name)->find();
        if ($model) {
            return $model;
        }
        $data = [
            'name' => $name,
            'title' => $title,
            'state' => $useState ? '20' : '10',
        ];
        $model = new DictTag;
        $model->save($data);
        return $model;
    }

    /**
     * 修改字典
     * @param string $name 字典标识
     * @param string $title 字典名称
     * @param bool $useState 是否启用
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function editTag(string $name, string $title, bool $useState = false)
    {
        $model = DictTag::where('name', $name)->find();
        if (!$model) {
            return false;
        }
        $data = [
            'title' => $title,
            'state' => $useState ? '20' : '10',
        ];
        $model->save($data);
        return $model;
    }
    
    /**
     * 删除字典
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function delTag(string $name)
    {
        $model = DictTag::where('name', $name)->find();
        if (!$model) {
            return true;
        }
        return $model->delete();
    }

    /**
     * 获取字典
     * @param string $name 字典标识
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getTag(string $name)
    {
        return DictTag::where('name', $name)->find();
    }

    /**
     * 获取字典数据
     * @param string $name 字典标识
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getTagData(string $name)
    {
        $model = DictTag::where('name', $name)->find();
        if (!$model) {
            return [];
        }
        $data = DictData::where('dict_id', $model->id)->select()->toArray();
        return $data;
    }
}