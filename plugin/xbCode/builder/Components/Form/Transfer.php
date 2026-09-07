<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

/**
 * @method $this options(array $value) 设置选项组
 * @method $this source(string $value) 设置动态选项组
 * @method $this delimeter(string $value) 设置拼接符
 * @method $this joinValues(bool $value) 设置拼接值
 * @method $this extractValue(bool $value) 设置提取值
 * @method $this searchApi(string $value) 设置检索接口
 * @method $this resultListModeFollowSelect(bool $value) 设置结果面板跟随模式
 * @method $this statistics(bool $value) 设置是否显示统计数据
 * @method $this selectTitle(string $value) 设置左侧的标题文字
 * @method $this resultTitle(string $value) 设置右侧结果的标题文字
 * @method $this sortable(bool $value) 设置结果可以进行拖拽排序
 * @method $this selectMode(string $value) 设置选择模式
 * @method $this searchResultMode(string $value) 设置搜索结果的展示形式
 * @method $this searchable(bool $value) 设置左侧列表搜索功能
 * @method $this searchPlaceholder(string $value) 设置左侧列表搜索框提示
 * @method $this columns(array $value) 设置当展示形式为 table 时配置展示哪些列
 * @method $this leftOptions(array $value) 设置当展示形式为 associated 时用来配置左边的选项集
 * @method $this leftMode(string $value) 设置当展示形式为 associated 时用来配置左边的选择形式
 * @method $this rightMode(string $value) 设置当展示形式为 associated 时用来配置右边的选择形式
 * @method $this resultSearchable(bool $value) 设置结果列表的检索功能
 * @method $this resultSearchPlaceholder(string $value) 设置结果列表搜索框提示
 * @method $this menuTpl(string $value) 设置自定义选项展示
 * @method $this valueTpl(string $value) 设置自定义值的展示
 * @method $this itemHeight(int $value) 设置每个选项的高度
 * @method $this virtualThreshold(int $value) 设置在选项数量超过多少时开启虚拟渲染
 * @method $this pagination(array $value) 设置分页配置
 * @method $this selectTreeMode() 启用树形选择模式（selectMode=tree，并注入推荐缺省属性）
 */
class Transfer extends FormOptions
{
    public string $type = 'transfer';

    /**
     * 默认属性：与 amis Tree 内部默认值对齐，规避 UI Transfer 未透传属性的 BUG
     * - joinValues(false)    = 值为数组，不拼接成逗号串
     * - extractValue(false)  = 与 Tree 取不到该属性时的默认值一致，绕开前端未透传缺陷
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function defaultAttr()
    {
        $this->joinValues(false)->extractValue(false);
        return $this;
    }

    /**
     * 启用树形选择模式（selectMode=tree，并注入树形模式推荐的缺省属性）
     * - 强制设置 selectMode=tree
     * - 若用户未显式设置 resultListModeFollowSelect，则默认置为 true（结果面板展示跟随左侧树形层级/表格式）
     * - 用户先设置的同名属性（selectTitle/resultTitle 等）永远保留不被覆盖
     * @return $this
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function selectTreeMode()
    {
        $this->selectMode('tree');
        // 仅做缺省设置：用户先设置过的属性不覆盖
        if (!isset($this->resultListModeFollowSelect)) {
            $this->resultListModeFollowSelect(true);
        }
        return $this;
    }
}
