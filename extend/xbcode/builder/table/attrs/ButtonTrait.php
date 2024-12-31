<?php
namespace xbcode\builder\table\attrs;
use xbcode\builder\ListBuilder;

trait ButtonTrait
{
    /**
     * 开启右侧操作选项
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-02-28
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addActionOptions(string $title, array $extra = []): ListBuilder
    {
        $field = 'rightButtons';
        $extra = array_merge([
            'width' => 'auto',
            'fixed' => 'right',
            'slots' => [
                'default' => $field
            ],
            'params' => [
                // 是否显示更多按钮
                'group' => false,
                // 更多按钮文本
                'groupText' => '',
                // 更多按钮图标
                'buttonGroupIcon' => '',
                // 更多按钮图标类型：element / @vicons/antd（默认）
                'buttonGroupIconType' => '',
                // 按钮样式，参考element-plug
                'buttonStyle' => [
                    'link' => false,
                ],
            ],
        ], $extra);
        $this->addColumn($field, $title, $extra);
        return $this;
    }

    /**
     * 获取按钮通用参数
     * @param string $field
     * @param string $title
     * @param array $pageData
     * @param array $message
     * @param array $button
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-18
     */
    private function checkUsedAttrs(string $field, string $title, array $pageData = [], array $message = [], array $button = []): array
    {
        // 获取默认数据
        $btnData = $this->getBtnDefaultAttrs($field, $title);
        // 合并页面数据
        $btnData['pageData'] = array_merge($btnData['pageData'], $pageData);
        // 处理模态框参数
        $this->getModalAttrs($btnData);
        // 合并消息数据
        $btnData['message'] = array_merge($btnData['message'], $message);
        // 默认的按钮样式
        $btnData['button'] = array_merge($this->getBtnDefaultStyle(), $button);

        // 返回按钮
        return $btnData;
    }

    /**
     * 获取按钮默认参数
     * @param string $field
     * @param string $title
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-18
     */
    private function getBtnDefaultAttrs(string $field, string $title): array
    {
        $data = [
            'field' => $field,
            'title' => $title,
            'pageData' => [
                /**
                 * 按钮组件类型
                 * page：跳转页面
                 * link：跳转链接
                 * confirm：确认框
                 * table：模态框-表格
                 * modal：模态框-表单
                 * remote：模态框-远程组件
                 * info：模态框-详情数据
                 * drawerForm：抽屉-表单
                 * drawerInfo：抽屉-信息
                 * drawerTable：抽屉-表格
                 * drawerRemote：抽屉-远程组件
                 */
                'type' => 'page',
                // 是否支持返回
                'isBack' => true,
                // 请求API
                'api' => '',
                // 跳转路径
                'path' => '',
                // 请求类型
                'method' => 'GET',
                // 附带参数
                'queryParams' => [],
                // 右侧按钮别名参数(仅支持右侧按钮)
                'aliasParams' => [],
            ],
            // 按钮样式
            'button' => [],
            // type为[modal，table，remote，info]时有效
            'message' => [],
        ];
        return $data;
    }

    /**
     * 合并模态框参数
     * @param array $btnData
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-18
     */
    private function getModalAttrs(array &$btnData): array
    {
        // 设置模态框专有属性
        if (in_array($btnData['pageData']['type'], ['modal', 'table', 'remote', 'info'])) {
            $btnData['message']['customStyle']['width']  = '480px';
            $btnData['message']['customStyle']['height'] = '65vh';
            $btnData['message']['closeOnClickModal']     = false;
            $btnData['message']['showConfirmButton']     = true;
            $btnData['message']['confirmButtonText']     = '确定';
            $btnData['message']['cancelButtonText']      = '取消';
        }
        // 设置确认框专有属性
        if ($btnData['pageData']['type'] === 'confirm') {
        }
        return $btnData;
    }

    /**
     * 获得默认按钮样式
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-02-28
     * @param  string $text
     * @param  string $type
     * @param  string $size
     * @return array
     */
    private function getBtnDefaultStyle(string $type = 'default', string $size = ''): array
    {
        $data = [
            //类型 default / primary / success / warning / danger / info / text
            'type' => $type,
            //尺寸 medium / small / mini
            'size' => $size,
            //是否朴素按钮
            'plain' => false,
            //是否圆角按钮
            'round' => false,
            //是否圆形按钮
            'circle' => false,
            //是否加载中状态
            'loading' => false,
            //是否禁用状态
            'disabled' => false,
            //图标类名
            'icon' => '',
            // 按钮属性
            'iconAttrs' => [],
            //是否默认聚焦
            'autofocus' => false,
            //原生 type 属性
            'nativeType' => "button",
            // 是否显示按钮
            'show' => true,
            // 是否文字链接
            'link' => false,
            // 元素类型
            'tag' => 'button',
        ];
        return $data;
    }
}