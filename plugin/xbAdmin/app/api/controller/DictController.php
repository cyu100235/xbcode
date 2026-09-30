<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\api\controller;

use support\Request;
use support\Response;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\app\model\Dict;
use plugin\xbAdmin\app\BaseController;
use plugin\xbAdmin\app\validate\DictValidate;
use plugin\xbAdmin\exception\business\ExceptionBusiness;

/**
 * 字典接口
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DictController extends BaseController
{
    /**
     * 字典列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $plugin = $request->get('plugin', '');
        $name = $request->get('name', '');
        $isSystem = $request->get('is_system', '');
        $query = Dict::order('sort asc,id asc');
        if ($keyword) {
            $query->where('title', 'like', "%{$keyword}%");
        }
        if ($plugin) {
            $query->where('plugin', $plugin);
        }
        if ($name) {
            $query->where('name', $name);
        }
        if (in_array($isSystem, array_column(YesEnum::toArray(), 'value'), true)) {
            $query->where('is_system', $isSystem);
        }
        $result = $query->paginate()->each(function ($item) {
            $item['values_text'] = array_map(function ($row) {
                return $row['label'] . ' (' . $row['value'] . ')';
            }, $item['values']);
        });
        return $this->successData($result);
    }

    /**
     * 添加字典
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $post = (array) $request->post();
        xbValidate(DictValidate::class, $post, 'add');
        $post = $this->prepareData($post);
        // 后台手工新增的字典项不属于枚举派生，一律标记为非系统字典
        $post['is_system'] = YesEnum::NO['value'];
        $model = new Dict;
        if (!$model->save($post)) {
            throw new ExceptionBusiness('添加字典失败');
        }
        return $this->success('添加成功', ['id' => (int) $model['id']]);
    }

    /**
     * 修改字典
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        $post = (array) $request->post();
        xbValidate(DictValidate::class, $post, 'edit');
        $rows = $this->parseValues($post['values'] ?? []);
        $old = (array) ($model['values'] ?? []);
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            // 获取键值对字典数据
            $current = array_column($rows, 'value', 'key');
            $values = [];
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $key = trim((string) ($row['key'] ?? ''));
                // 只允许调整库中已有常量的标签与样式，禁止新增或改名
                if ($key === '' || !array_key_exists($key, $current)) {
                    continue;
                }
                $values[] = [
                    'key' => $key,
                    'label' => (string) ($row['label'] ?? ''),
                    'value' => $current[$key],
                    'style' => (string) ($row['style'] ?? ''),
                ];
            }
            $post['values'] = $this->syncStyle($values, $old);
            $post['plugin'] = (string) $model['plugin'];
            $post['name'] = (string) $model['name'];
        } else {
            $post['values'] = $this->syncStyle($rows, $old);
        }
        $post = $this->prepareData($post);
        unset($post['is_system']);
        if (!$model->save($post)) {
            throw new ExceptionBusiness('修改失败');
        }
        return $this->success('修改成功');
    }

    /**
     * 删除字典
     * @param Request $request
     * @return Response
     */
    public function del(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        if ((string) $model['is_system'] === YesEnum::YES['value']) {
            throw new ExceptionBusiness('系统字典，禁止删除');
        }
        if (!$model->delete()) {
            throw new ExceptionBusiness('删除失败');
        }
        return $this->success('删除成功');
    }

    /**
     * 查询数据
     * @param Request $request
     * @return Response
     */
    public function find(Request $request)
    {
        $model = $this->findModel((int) $request->get('id'));
        return $this->successRes($model->toArray());
    }

    /**
     * 插件标识选项
     * @return Response
     */
    public function plugins()
    {
        return $this->successRes(Dict::plugins());
    }

    /**
     * 接收插件上报的字典数据
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function report(Request $request)
    {
        $post = (array) $request->post();
        $plugin = trim((string) ($post['plugin'] ?? ''));
        if ($plugin === '' || !preg_match('/^[\w\-]+$/', $plugin)) {
            throw new ExceptionBusiness('插件标识错误');
        }
        $groups = $post['groups'] ?? [];
        if (!is_array($groups) || !$groups) {
            throw new ExceptionBusiness('字典分组不能为空');
        }
        $count = Dict::register($plugin, $groups);
        return $this->success('上报成功', ['count' => $count]);
    }

    /**
     * 读取枚举字典数据
     *
     * 部署在其他服务器上的插件通过该接口按「插件标识 + 枚举标识」读取字典，
     * 未收录该字典时返回空集合，由插件回退到本地枚举类常量。
     * @param Request $request
     * @throws ExceptionBusiness
     * @return Response
     */
    public function detail(Request $request)
    {
        $plugin = trim((string) ($request->post('plugin') ?? $request->get('plugin', '')));
        if ($plugin === '' || !preg_match('/^[\w\-]+$/', $plugin)) {
            throw new ExceptionBusiness('插件标识错误');
        }
        $name = trim((string) ($request->post('name') ?? $request->get('name', '')));
        if ($name === '' || !preg_match('/^[\w\-]+$/', $name)) {
            throw new ExceptionBusiness('枚举标识错误');
        }
        $model = Dict::where('plugin', $plugin)->where('name', $name)->order('id asc')->find();
        if (!$model) {
            return $this->success('', ['plugin' => $plugin, 'name' => $name, 'title' => '', 'values' => []]);
        }
        return $this->success('', [
            'plugin' => (string) $model['plugin'],
            'name' => (string) $model['name'],
            'title' => (string) $model['title'],
            'values' => (array) $model['values'],
        ]);
    }

    /**
     * 归一化表单提交的 values，兼容 JSON 字符串与数组
     * @param mixed $values
     * @return array 常量列表
     */
    protected function parseValues(mixed $values): array
    {
        if (is_string($values)) {
            $values = json_decode($values, true);
        }
        return is_array($values) ? $values : [];
    }

    /**
     * 把显示文字同步进样式
     *
     * 列表单元格渲染的是样式里的 HTML 片段（文字写在 HTML 标签中间），
     * 只改「显示文字」不改样式时，单元格仍显示旧文字。
     * 因此当样式未被手工修改、且样式内文字与显示文字不一致时，用显示文字替换样式内的文字；
     * 样式被手工改过则原样保留，不做干扰。
     * @param array $rows 提交的常量列表
     * @param array $old 库中已有的常量列表
     * @return array
     */
    protected function syncStyle(array $rows, array $old): array
    {
        $olds = [];
        foreach ($old as $row) {
            $key = (string) ($row['key'] ?? '');
            if ($key !== '') {
                $olds[$key] = $row;
            }
        }
        foreach ($rows as &$row) {
            if (!is_array($row)) {
                continue;
            }
            $key = (string) ($row['key'] ?? '');
            if ($key === '' || !isset($olds[$key])) {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            $style = (string) ($row['style'] ?? '');
            // 样式被手工改动过，或没有可同步的文字，都不处理
            if ($label === '' || $style === '' || $style !== (string) ($olds[$key]['style'] ?? '')) {
                continue;
            }
            if (trim(strip_tags($style)) === $label) {
                continue;
            }
            if (!str_contains($style, '<')) {
                // 纯文本样式，直接替换
                $row['style'] = $label;
                continue;
            }
            // 替换首个标签内的文字，保留标签与属性
            $row['style'] = preg_replace_callback('~>([^<>]*)(?=</)~', function () use ($label) {
                return '>' . $label;
            }, $style, 1) ?? $style;
        }
        unset($row);
        return $rows;
    }

    /**
     * 整理提交的字典数据，把常量列表组装进 values JSON 数组
     * @param array $post
     * @return array
     */
    protected function prepareData(array $post): array
    {
        foreach (['plugin', 'name', 'title'] as $field) {
            $post[$field] = trim((string) ($post[$field] ?? ''));
        }
        $post['sort'] = (int) ($post['sort'] ?? 0);
        return $post;
    }

    /**
     * 按ID查找字典
     * @param int $id
     * @throws ExceptionBusiness
     * @return Dict
     */
    protected function findModel(int $id): Dict
    {
        if ($id <= 0) {
            throw new ExceptionBusiness('数据不存在');
        }
        $model = Dict::where('id', $id)->find();
        if (!$model) {
            throw new ExceptionBusiness('该数据不存在');
        }
        return $model;
    }
}