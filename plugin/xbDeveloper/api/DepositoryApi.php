<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;
use plugin\xbDeveloper\utils\GitUtil;

/**
 * 代码仓库接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DepositoryApi
{
    /**
     * 插件标识
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected string $name = '';

    /**
     * 构造函数
     * @param string $name
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function __construct(string $name)
    {
        if (empty($name)) {
            throw new Exception('名称不能为空');
        }
        $this->name = $name;
    }

    /**
     * 获取实例
     * @return DepositoryApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function make(string $name)
    {
        $class = new static($name);
        return $class;
    }

    /**
     * 推送代码仓库
     * @return void
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function push(string $commit)
    {
        // 获取插件目录
        $pluginDir = base_path("plugin/{$this->name}");
        // 检测插件目录是否存在
        if (!is_dir($pluginDir)) {
            throw new Exception("插件目录不存在：{$pluginDir}");
        }
        // 判断是否为 git 仓库或子模块
        if (!GitUtil::hasGitRepository($pluginDir)) {
            throw new Exception("插件 {$this->name} 不是 git 仓库也非 git 子模块，无法推送");
        }
        GitUtil::push($pluginDir, $commit);
    }

    /**
     * 检测插件是否为 git 仓库
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function isGitRepo(): bool
    {
        $pluginDir = base_path("plugin/{$this->name}");
        return is_dir("{$pluginDir}/.git") || is_file("{$pluginDir}/.git");
    }

    /**
     * 检测插件是否为 git 子模块
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function isSubmodule(): bool
    {
        $submodulePath = base_path(".git/modules/plugin/{$this->name}");
        return is_dir($submodulePath);
    }
}