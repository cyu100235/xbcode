<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Renders\crud\ColumnUtil;
use plugin\xbCode\builder\Renders\crud\column\TplColumn;
use plugin\xbCode\builder\Renders\crud\column\TagColumn;
use plugin\xbCode\builder\Renders\crud\column\MapColumn;
use plugin\xbCode\builder\Renders\crud\column\CopyColumn;
use plugin\xbCode\builder\Renders\crud\column\HtmlColumn;
use plugin\xbCode\builder\Renders\crud\column\JsonColumn;
use plugin\xbCode\builder\Renders\crud\column\DateColumn;
use plugin\xbCode\builder\Renders\crud\column\IconColumn;
use plugin\xbCode\builder\Renders\crud\column\CardColumn;
use plugin\xbCode\builder\Renders\crud\column\ColorColumn;
use plugin\xbCode\builder\Renders\crud\column\ImageColumn;
use plugin\xbCode\builder\Renders\crud\column\InputColumn;
use plugin\xbCode\builder\Renders\crud\column\AudioColumn;
use plugin\xbCode\builder\Renders\crud\column\VideoColumn;
use plugin\xbCode\builder\Renders\crud\column\NumberColumn;
use plugin\xbCode\builder\Renders\crud\column\StatusColumn;
use plugin\xbCode\builder\Renders\crud\column\SwitchColumn;
use plugin\xbCode\builder\Renders\crud\column\AvatarColumn;
use plugin\xbCode\builder\Renders\crud\column\ImagesColumn;
use plugin\xbCode\builder\Renders\crud\column\SelectColumn;
use plugin\xbCode\builder\Renders\crud\column\DateTimeColumn;
use plugin\xbCode\builder\Renders\crud\column\ProgressColumn;
use plugin\xbCode\builder\Renders\crud\column\VueComponentColumn;
use plugin\xbCode\builder\Renders\crud\column\ActionButtonColumn;

/**
 * 增删改查表格布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait CrudLayout
{
    use TplColumn;
    use TagColumn;
    use MapColumn;
    use CopyColumn;
    use HtmlColumn;
    use ColumnUtil;
    use DateColumn;
    use IconColumn;
    use CardColumn;
    use JsonColumn;
    use ColorColumn;
    use AudioColumn;
    use VideoColumn;
    use InputColumn;
    use ImageColumn;
    use NumberColumn;
    use StatusColumn;
    use SwitchColumn;
    use SwitchColumn;
    use AvatarColumn;
    use AvatarColumn;
    use ImagesColumn;
    use SelectColumn;
    use DateTimeColumn;
    use ProgressColumn;
    use VueComponentColumn;
    use ActionButtonColumn;
    use ActionButtonLayout;
}
