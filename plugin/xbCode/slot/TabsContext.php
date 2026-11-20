<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\slot;

use Throwable;

/**
 * 选项卡配置插槽上下文
 *
 * 用于宿主配置页（如「用户设置」）向其他插件开放选项卡扩展能力：
 * 监听器通过 add() 追加选项卡，宿主在所有监听器执行完毕后读取 all() 渲染。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TabsContext
{
    /**
     * 选项卡列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $tabs;

    /**
     * 构造函数
     * @param array $tabs 宿主已有的选项卡列表
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct(array $tabs = [])
    {
        $this->tabs = $tabs;
    }

    /**
     * 追加单个选项卡
     *
     * 选项卡名称即配置项分组名（保存时按 `_tab` 分组落库），
     * 与宿主已有选项卡重名时忽略，避免覆盖宿主自身配置。
     * @param array $tab 选项卡数据，需包含 name/title/body
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(array $tab): static
    {
        if (empty($tab['name']) || empty($tab['title']) || empty($tab['body'])) {
            return $this;
        }
        if ($this->has((string) $tab['name'])) {
            return $this;
        }
        $this->tabs[] = $tab;
        return $this;
    }

    /**
     * 批量追加选项卡
     * @param array $tabs 选项卡列表
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addMany(array $tabs): static
    {
        foreach ($tabs as $tab) {
            if (is_array($tab)) {
                $this->add($tab);
            }
        }
        return $this;
    }

    /**
     * 是否存在指定名称的选项卡
     * @param string $name 选项卡名称
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function has(string $name): bool
    {
        foreach ($this->tabs as $tab) {
            if ((string) ($tab['name'] ?? '') === $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取全部选项卡
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function all(): array
    {
        return $this->tabs;
    }

    /**
     * 获取选项卡数量
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function count(): int
    {
        return count($this->tabs);
    }
}
