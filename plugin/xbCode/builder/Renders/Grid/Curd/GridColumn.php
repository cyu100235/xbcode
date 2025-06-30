<?php

namespace plugin\xbCode\builder\Renders\Grid\Curd;

use plugin\xbCode\builder\Components\Card;
use plugin\xbCode\builder\Components\Icon;
use plugin\xbCode\builder\Components\Image;
use plugin\xbCode\builder\Renders\Grid;
use plugin\xbCode\builder\Renders\Grid\Column\Column;

/**
 * 表格列组件
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait GridColumn
{
    /**
     * 表格实例
     * @var Grid
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    protected Grid $grid;

    /**
     * 表格列配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $columns = [];

    /**
     * 添加普通列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumn(string $name, string $label, callable|array $option = null): Column
    {
        $component = new Column($name, $label);
        $this->setComponent($component, $option);
        $this->columns[] = $component;
        return $component;
    }

    /**
     * 添加文本列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnInput(string $name, string $label, callable|array $option = null): Column
    {
        $component = $this->addColumn($name, $label, $option);
        $component->quickEdit([
            'type' => 'input-text',
            'saveImmediately' => true,
        ]);
        return $component;
    }

    /**
     * 添加数字列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnNumber(string $name, string $label, callable|array $option = null): Column
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('input-number');
        return $component;
    }

    /**
     * 添加日期列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDate(string $name, string $label, callable|array $option = null): Column
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('date');
        return $component;
    }

    /**
     * 添加日期时间列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Grid\Column\Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDateTime(string $name, string $label, callable|array $option = null): Column
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('datetime');
        return $component;
    }

    /**
     * 添加状态列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnStatus(string $name, string $label, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('status');
        return $component;
    }

    /**
     * 添加映射列
     * @param string $name
     * @param string $label
     * @param array $mapping
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnMap(string $name, string $label, array $mapping, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->mapping($mapping);
        return $component;
    }
    
    /**
     *  添加图标列
     * @param string $name 字段名称
     * @param string $label 标签名称
     * @param array $config 图标设置
     * @param callable|array $option
     * @return Grid\Column\Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnIcon(string $name, string $label, array $config = [],callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->icon(function(Icon $icon)use($config){
            $icon->setVariables($config);
        });
        return $component;
    }

    /**
     * 添加进度列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnProgress(string $name, string $label, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('progress');
        return $component;
    }
    
    /**
     * 添加开关列
     * @param string $name
     * @param string $label
     * @param array $switch
     * @param callable|array $option
     * @return Grid\Column\Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnSwitch(string $name, string $label, array $switch = [], callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->actionType('ajax');
        $component->quickEdit([
            'type' => 'switch',
            'mode' => 'inline',
            'saveImmediately' => true,
            ...$switch,
        ]);
        return $component;
    }

    /**
     * 添加卡片列
     * @param string $name
     * @param string $label
     * @param array $fields
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnCard(string $name, string $label, array $fields = [], callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('card');
        // 设置卡片属性
        $title = $fields['title'] ?? 'title';
        $subTitle = $fields['subtitle'] ?? 'subtitle';
        $image = $fields['image'] ?? 'image';
        $component->header([
            'title' => "<%= this.{$title} %>",
            'subTitle' => "<%= this.{$subTitle} %>",
            'avatar' => "<%= this.{$image} %>",
            'avatarClassName' => 'pull-left thumb-md avatar b-3x m-r',
        ]);
        return $component;
    }

    /**
     * 添加JSON列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnJson(string $name, string $label, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('json');
        return $component;
    }

    /**
     * 添加图片列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnImage(string $name, string $label, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->image(30,30);
        return $component;
    }

    /**
     * 添加图片组列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Grid\Column\Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnImages(string $name, string $label, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('images');
        return $component;
    }

    /**
     * 添加音频列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnAudio(string $name, string $label, callable|array $option = null): Column
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('audio');
        return $component;
    }

    /**
     * 添加视频列
     * @param string $name
     * @param string $label
     * @param callable|array $option
     * @return Column
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnVideo(string $name, string $label, callable|array $option = null)
    {
        $component = $this->addColumn($name, $label, $option);
        $component->type('video');
        return $component;
    }

    /**
     * 获取表格列配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getColumns(): array
    {
        return $this->columns;
    }
}
