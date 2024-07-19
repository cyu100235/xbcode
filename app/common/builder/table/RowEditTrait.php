<?php

namespace app\common\builder\table;

use hg\apidoc\annotation as Apidoc;
use support\Request;

/**
 * 编辑列
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait RowEditTrait
{

    /**
     * 设置开关值
     * @Apidoc\Method ("PUT")
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function rowEdit(Request $request)
    {
        // 主键名称
        $keyField = $request->post('keyField');
        // 主键值
        $id = $request->post($keyField);
        // 字段名
        $field = $request->post('field');
        // 数据值
        $value = $request->post('value');
        $where = [
            $keyField => $id
        ];
        $model = $this->model;
        $model = $model->where($where)->find();
        $model->$field = $value;
        if (!$model->save()) {
            return self::fail('修改失败');
        }
        return self::success('修改成功');
    }
}