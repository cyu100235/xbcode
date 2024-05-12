<?php
namespace app\service\cloud;

use app\utils\JsonUtil;
use Exception;
use support\Request;

/**
 * 插件安装云服务
 * 步骤如下：
 * 1、下载更新包
 * 2、解压更新包
 * 3、执行数据安装
 * 4、安装成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PluginsInstallCloud
{
    use JsonUtil;

    /**
     * 请求对象
     * @var Request
     */
    private static $request;

    /**
     * 安装插件
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(Request $request)
    {
        // 获取执行步骤
        $step = $request->post('step','download');
        // 获取插件名称
        $name = $request->post("name");
        // 安装版本
        $version = $request->post("version");
        // 参数验证
        if (empty($name) || empty($version)){
            throw new Exception("参数错误");
        }
        // 设置请求对象
        self::$request = $request;
        // 临时插件包路径
        $package = base_path("runtime/plugin/") . "{$name}-{$version}-install.zip";
        // 检测临时应用包目录，不存在则创建
        $packageDirPath = dirname($package);
        if (!is_dir($packageDirPath)) {
            mkdir($packageDirPath, 0755, true);
        }
        if (!method_exists(self::class, $step)) {
            throw new Exception("安装步骤错误");
        }
        // 执行转发
        return call_user_func([self::class, $step],$request);
    }

    /**
     * 下载插件
     * @param \support\Request $request
     * @throws \Exception
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function download(Request $request)
    {
        // 获取插件名称
        $name = $request->post("name");
        // 安装版本
        $version = $request->post("version");
        // 请求云端下载
        $data = [
            'name' => $name,
            'version' => $version
        ];
        $result = HttpCloud::get('plugins/download', $data);
        // 获取错误
        $data = $result->array();
        if (isset($data['code']) || isset($data['msg'])) {
            throw new Exception($data['msg'], $data['code']);
        }
        $content = $result->body();
        // 临时文件路径
        $package = base_path("runtime/plugin/") . "{$name}-{$version}-install.zip";
        // 写入文件
        file_put_contents($package, $content);
        // 返回结果
        return self::successFul('下载插件成功', [
            'url'   => xbUrl('Plugins/install'),
            'query' => [
                'step' => 'unzip',
            ],
        ]);
    }
    public function unzip($package)
    {
        // 获取插件名称
        $name = self::$request->post("name");
        // 安装版本
        $version = self::$request->post("version");
        // 插件目录
        $pluginDir = base_path("plugins/{$name}");
        // 解压插件包
        $zip = new \ZipArchive();
        if ($zip->open($package) === true) {
            $zip->extractTo($pluginDir);
            $zip->close();
        } else {
            throw new Exception("解压失败");
        }
        // 删除插件包
        unlink($package);
        // 返回结果
        return self::installSql();
    }
    public function database()
    {
        // 获取插件名称
        $name = self::$request->post("name");
        // 安装版本
        $version = self::$request->post("version");
        // 插件目录
        $pluginDir = base_path("plugins/{$name}");
        // 数据安装
        $sqlFile = $pluginDir . "/install.sql";
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $sql = str_replace("\r", "\n", $sql);
            $sql = explode(";\n", $sql);
            foreach ($sql as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    \think\facade\Db::execute($value);
                }
            }
        }
        // 返回结果
        return self::installSuccess();
    }
    public function success1()
    {
        // 获取插件名称
        $name = self::$request->post("name");
        // 安装版本
        $version = self::$request->post("version");
        // 插件目录
        $pluginDir = base_path("plugins/{$name}");
        // 安装成功
        $data = [
            'name' => $name,
            'version' => $version,
            'title' => $name,
            'description' => '',
            'status' => 1,
            'config' => '',
            'author' => '',
            'author_url' => '',
            'install_time' => time(),
            'update_time' => time(),
        ];
        // 插入数据
        \app\model\Plugins::create($data);
        // 返回结果
        return "安装成功";
    }
}