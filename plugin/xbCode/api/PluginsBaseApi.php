<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\utils\DirUtil;
use plugin\xbCode\utils\ZipUtil;
use plugin\xbUpload\api\UploadApi;
use plugin\xbCode\app\validate\PluginValidate;

/**
 * 插件基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class PluginsBaseApi
{
    /**
     * 插件标识
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $name;

    /**
     * 插件版本
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $version;

    /**
     * 当前执行步骤
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $step;

    /**
     * 操作执行步骤
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $steps = [];

    /**
     * 插件目录地址
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $pluginPath;

    /**
     * 插件包目录
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $pakcageDir;

    /**
     * 插件包文件路径
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $packageFile;

    /**
     * 插件包路径
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $packagePath;


    /**
     * 获取安装步骤流程
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function steps()
    {
        if(empty($this->steps)) {
            throw new Exception('未定义步骤流程');
        }
        return $this->steps;
    }

    /**
     * 构造函数
     * @param string $name
     * @param string $version
     * @param string $method
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function start(string $name, string $version, string $step)
    {
        $this->setPluginInfo($name, $version);
        if(empty($this->steps)){
            throw new Exception('未定义安装步骤流程');
        }
        $steps = array_column($this->steps, 'name');
        // 检测步骤是否存在
        if(!in_array($step, $steps)) {
            throw new Exception("安装步骤{$step}不存在");
        }
        if(!method_exists($this, $step)) {
            throw new Exception("安装步骤{$step}方法不存在");
        }
        // 设置当前执行步骤
        $this->step = $step;
        // 执行步骤
        return call_user_func([$this, $step]);
    }

    /**
     * 初始化插件信息
     * @param string $name
     * @param string $version
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function setPluginInfo(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
        $this->pluginPath = base_path() . "/plugin/{$name}";
        $this->packageFile = "{$name}-{$version}.zip";
        $this->pakcageDir = runtime_path() . "/plugin";
        $this->packagePath = "{$this->pakcageDir}/{$this->packageFile}";
    }
    
    /**
     * 上传插件包
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function upload()
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('未安装ZipArchive扩展');
        }
        // 获取上传文件
        $name = 'file';
        $file = request()->file($name);
        if (empty($file)) {
            throw new Exception('上传插件包文件不存在');
        }
        // 获取文件路径
        $path = $file->getRealPath();
        // 打开压缩包
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new Exception('插件包打开失败');
        }
        // 检测版本文件是否完整
        if (!$zip->getFromName('plugins.json')) {
            throw new Exception('插件包不完整，缺少信息文件');
        }
        if (!$zip->getFromName('preview.svg')) {
            throw new Exception('插件包不完整，缺少预览图');
        }
        // 读取插件信息
        $plugin = json_decode($zip->getFromName('plugins.json'), true);
        // 插件参数验证
        xbValidate(PluginValidate::class, $plugin);
        // 检测插件是否已经安装
        if (PluginsApi::exists($plugin['name'])) {
            throw new Exception('该插件已存在');
        }
        // 初始化插件信息
        $this->setPluginInfo($plugin['name'], $plugin['version']);
        // 关闭压缩包资源
        $zip->close();
        // 上传插件包
        $data = UploadApi::uploadLocal($this->pakcageDir, $name, $this->packageFile);
        if (empty($data['uri'])) {
            throw new Exception('上传失败，请稍后再试');
        }
        return $this->nextResult("{$plugin['title']}，导入成功，标识：{$plugin['name']}，作者：{$plugin['author']}",[
            'plugin' => $plugin,
        ]);
    }
    
    /**
     * 解压插件包
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function unzip()
    {
        // 检测插件包是否存在
        if (!file_exists($this->packagePath)) {
            throw new Exception('插件包不存在');
        }
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
            throw new Exception("解压插件包失败:{$th->getMessage()}");
        }
        // 解压完成，删除插件包
        if (file_exists($this->packagePath)) {
            unlink($this->packagePath);
        }
        return $this->nextResult('插件包解压成功...');
    }
    
    /**
     * 执行脚本代码
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function script()
    {
        // 获取类命名空间
        $class = get_called_class();
        $method = debug_backtrace()[1]['function'] ?? '';
        if (empty($method)) {
            throw new Exception('获取调用栈方法名错误');
        }
        // 安装类路径
        $classPath = $this->pluginPath . '/api/Install.php';
        if (!file_exists($classPath)) {
            throw new Exception('插件脚本文件不存在');
        }
        // 重新引入更新类，确保是最新更新类
        require_once $classPath;
        $class = "\\plugin\\{$this->name}\\api\\Install";
        if (class_exists($class)) {
            // 执行前置方法
            $context = null;
            if (method_exists($class, "{$method}Before")) {
                $context = call_user_func([$class, "{$method}Before"], $this->version);
            }
            // 执行方法
            if (method_exists($class, $method)) {
                $context = call_user_func([$class, $method], $this->version, $context);
            }
            // 执行后置方法
            if (method_exists($class, "{$method}After")) {
                call_user_func([$class, "{$method}After"], $this->version, $context);
            }
        }
    }
    
    /**
     * 返回下一步结果
     * @param string $text 下一步提示文本
     * @param array $data 附加数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function nextResult(string $text, array $data = [])
    {
        return [
            'text' => $text,
            ...$data,
        ];
    }
}