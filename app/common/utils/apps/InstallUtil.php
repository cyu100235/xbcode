<?php
namespace app\common\utils\apps;

use xbCloud\CloudService;
use app\common\utils\JsonUtil;
use app\common\utils\zip\ZipUtil;
use think\Request;
use xbai8\MysqlHelper;

/**
 * 应用安装服务
 * 步骤如下：
 * 1、下载更新包
 * 2、解压更新包
 * 3、执行数据安装
 * 4、安装成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class InstallUtil
{
    // 引入JsonUtil
    use JsonUtil;

    /**
     * 请求对象
     * @var Request|null
     */
    protected $request = null;

    /**
     * 临时应用包路径
     * @var string|null
     */
    protected $package = null;

    /**
     * 应用目录
     * @var string|null
     */
    protected $baseDirPath = null;

    /**
     * 应用标识
     * @var string|null
     */
    protected $appName = null;

    /**
     * 版本号
     * @var int|null
     */
    protected $version = null;

    /**
     * 构造方法
     * @param \think\Request $request
     * @param string $appName
     * @param int $version
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct(Request $request,string $appName, int $version)
    {
        // 设置请求对象
        $this->request = $request;
        // 临时应用包路径
        $this->package = root_path("runtime/apps") . "{$appName}-{$version}-install.zip";
        // 检测临时应用包目录，不存在则创建
        $packageDirPath = dirname($this->package);
        if (!is_dir($packageDirPath)) {
            mkdir($packageDirPath, 0755, true);
        }
        // 应用目录
        $this->baseDirPath = root_path("base/{$appName}");
        // 设置应用标识
        $this->appName = $appName;
        // 设置版本号
        $this->version = $version;
    }
    
    /**
     * 下载应用包
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function download()
    {
        // 下载应用包
        CloudService::downloadApp($this->appName, $this->version, $this->package);
        // 返回结果
        return JsonUtil::successRes([
            'next' => 'unzip',
        ]);
    }
    
    /**
     * 解压应用包
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function unzip()
    {
        // 检测应用目录，不存在则创建
        if (!is_dir($this->baseDirPath)) {
            mkdir($this->baseDirPath, 0755, true);
        }
        // 解压应用包
        ZipUtil::unzip($this->package, $this->baseDirPath);
        // 返回结果
        return JsonUtil::successRes([
            'next' => 'updateData',
        ]);
    }
    
    /**
     * 数据安装
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateData()
    {
        // 应用初始类
        $class = "\\base\\{$this->appName}\\Package";
        // 执行安装前置
        if (method_exists($class, 'before_install')) {
            call_user_func([$class, 'before_install'], $this->request, $this->appName, $this->version);
        }
        // 执行安装数据
        $sqlFile = "{$this->baseDirPath}/data/install.sql";
        if (is_file($sqlFile) && file_exists($sqlFile)) {
            // 连接数据库
            $config = config('database.connections.mysql');
            $mysql  = new MysqlHelper(
                $config['username'],
                $config['password'],
                $config['database'],
                $config['hostname'],
                $config['hostport'],
                $config['prefix'],
                $config['charset']
            );
            try {
                // 导入SQL文件
                $mysql->importSqlFile($sqlFile, $config['prefix']);
            } catch (\Throwable $e) {
                // 表已存在，忽略
                $tableExists = strpos($e->getMessage(), 'already exists') !== false;
                // 字段已存在，忽略
                $fieldExists = strpos($e->getMessage(), 'Duplicate column name') !== false;
                if (!$tableExists && !$fieldExists){
                    // 其他报错，并抛出异常
                    throw $e;
                }
            }
        }
        // 执行更新后置
        if (method_exists($class, 'after_update')) {
            call_user_func([$class, 'after_update'], $this->request, $this->appName, $this->version);
        }
        // 返回结果
        return JsonUtil::successRes([
            'next' => 'success',
        ]);
    }
    
    /**
     * 安装成功
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function success()
    {
        // 安装完成
        CloudService::installApp($this->appName, $this->version);
        // 安装完成，删除临时应用包
        file_exists($this->package) && unlink($this->package);
        // 返回结果
        return $this->successFul('安装完成',[
            'next' => ''
        ]);
    }
}