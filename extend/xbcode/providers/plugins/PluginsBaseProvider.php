<?php
namespace xbcode\providers\plugins;

use support\Response;
use app\model\Plugins;
use xbcode\utils\DirUtil;
use xbcode\utils\ZipUtil;
use xbcode\trait\JsonTrait;
use xbcode\service\xbcode\PluginService;

/**
 * 插件服务基类提供
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class PluginsBaseProvider
{
    // 引入JsonTrait
    use JsonTrait;

    /**
     * 插件标识
     * @var string
     */
    protected $pluginName;

    /**
     * 版本名称
     * @var string
     */
    protected $versionName;

    /**
     * 版本编号
     * @var int
     */
    protected $version;

    /**
     * 插件目录
     * @var string
     */
    protected $pluginPath;

    /**
     * 插件包文件路径
     * @var string
     */
    protected $packagePath;
    
    /**
     * 开始服务
     * @param string $step 执行步骤
     * @param string $name 插件标识
     * @param string $versionName 版本名称
     * @param int $version 版本编号
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(string $step, string $name, string $versionName, int $version)
    {
        if (!method_exists($this, $step)) {
            return $this->fail('更新步骤错误');
        }
        // 插件标识
        $this->pluginName = $name;
        // 版本名称
        $this->versionName = $versionName;
        // 版本编号
        $this->version = $version;
        // 插件目录
        $this->pluginPath = base_path()."/plugin/{$name}";
        // 插件包路径
        $this->packagePath = runtime_path()."/plugins/{$name}-{$versionName}.zip";
        // 执行更新步骤
        return call_user_func([$this, $step]);
    }
    
    /**
     * 下载插件包
     * @param string $next 下一步名称
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function download(string $next = 'unzip'): Response
    {
        // 检测插件包储存目录是否存在
        $pluginPath = dirname($this->packagePath);
        if (!is_dir($pluginPath)) {
            mkdir($pluginPath, 0777, true);
        }
        // 检测文件是否存在
        if (file_exists($this->packagePath)) {
            unlink($this->packagePath);
        }
        // 下载更新包
        $content = PluginService::download($this->pluginName, $this->versionName, $this->version);
        // 写入文件
        file_put_contents($this->packagePath, $content);
        // 返回数据
        return $this->successRes([
            'next' => $next
        ]);
    }
    
    /**
     * 解压插件包
     * @param string $next 下一步名称
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function unzip(string $next = 'database'): Response
    {
        // 检测插件目录是否存在
        if (!is_dir($this->pluginPath)) {
            mkdir($this->pluginPath, 0777, true);
        }
        try {
            // 解压更新包
            ZipUtil::unzip($this->packagePath, $this->pluginPath);
        } catch (\Throwable $th) {
            // 检测插件包是否存在
            if (DirUtil::isDirEmpty($this->pluginPath)) {
                DirUtil::delDir($this->pluginPath);
            }
            return $this->fail("解压插件包失败:{$th->getMessage()}");
        }
        // 解压完成，删除插件包
        if (file_exists($this->packagePath)) {
            unlink($this->packagePath);
        }
        // 返回数据
        return $this->successRes([
            'next' => $next
        ]);
    }
    
    /**
     * 获取插件本地版本
     * @param string $name
     * @param string $field
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function localVersion(string $name, string $field = 'version_name')
    {
        $version = Plugins::where('name', $name)->value($field);
        return $version ?: '';
    }

    /**
     * 安装数据库
     * @param string $next 下一步名称
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract protected function database(string $next = 'success'): Response;

    /**
     * 操作完成
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract protected function success(): Response;
}