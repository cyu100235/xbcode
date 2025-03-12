<?php
namespace plugin\xbCode\app\validate;

use plugin\xbCode\app\model\UploadCate;
use taoser\Validate;

class UploadCateValidate extends Validate
{
    protected $rule =   [
        'title'             => 'require|verifyTitle',
        'dir_name'          => 'require|alpha|verifyDirName',
        'sort'              => 'require|number',
    ];

    protected $message  =   [
        'title.require'     => '请输入分类名称',
        'dir_name.require'  => '请输入目录名称',
        'dir_name.alpha'    => '目录名称只能是字母',
        'sort.require'      => '请输入分类排序',
        'sort.number'       => '分类排序只能是数字',
    ];

    /**
     * 添加场景验证
     * @return UploadCateValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'title',
                'dir_name',
                'sort',
            ]);
    }

    /**
     * 编辑场景验证
     * @return UploadCateValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'title',
                'dir_name',
                'sort',
            ])
            ->remove('title', 'verifyTitle')
            ->remove('dir_name', 'verifyDirName');
    }

    /**
     * 验证分类名称
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyTitle($value)
    {
        $where = [
            'title' => $value
        ];
        if (UploadCate::where($where)->count()) {
            return '该分类名称已存在';
        }
        return true;
    }

    /**
     * 验证目录名称
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyDirName($value)
    {
        $where = [
            'dir_name' => $value
        ];
        if (UploadCate::where($where)->count()) {
            return '该目录名称已存在';
        }
        return true;
    }
}
