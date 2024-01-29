<?php
namespace app\common\utils\apps;

use app\common\service\CloudService;
use app\common\utils\JsonUtil;
use app\common\utils\zip\ZipUtil;
use Exception;
use think\Request;
use zjkal\MysqlHelper;

/**
 * 应用更新服务
 * 步骤如下：
 * 1、下载更新包
 * 2、备份应用代码
 * 3、备份数据库
 * 4、删除应用旧代码
 * 5、解压更新包
 * 6、执行执行数据安装
 * 7、安装成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UpdateUtil
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
     * 安装SQL文件路径
     * @var string|null
     */
    protected $installSqlPath = null;

    /**
     * 备份代码包文件
     * @var string|null
     */
    protected $backupFile = null;

    /**
     * 备份数据库文件
     * @var string|null
     */
    protected $backupSql = null;

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
        $this->package = root_path("runtime/apps") . "{$appName}-{$version}-update.zip";
        // 检测临时应用包，不存在则创建
        $packageDir = dirname($this->package);
        if (!is_dir($packageDir)) {
            mkdir($packageDir, 0755, true);
        }
        // 备份总目录
        $backupDir = root_path("backup");
        // 检测备份总目录是否有权限
        if (!is_dir($backupDir) || !is_writable($backupDir)){
            throw new Exception("备份总目录不存在或无权限");
        }
        // 备份应用包文件
        $this->backupFile = "{$backupDir}{$appName}-{$version}.zip";
        // 备份数据库文件
        $this->backupSql = "{$backupDir}{$appName}-{$version}.sql";
        // 应用目录
        $this->baseDirPath = root_path("base/{$appName}");
        // 检测应用目录是否存在
        if (!is_dir($this->baseDirPath)) {
            throw new Exception("【{$appName}】应用目录不存在");
        }
        // 安装SQL文件路径
        $this->installSqlPath = "{$this->baseDirPath}data/install.sql";
        // 设置应用标识
        $this->appName = $appName;
        // 设置版本号
        $this->version = $version;
    }
    
    /**
     * 下载更新包
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function download()
    {
        // 下载应用包
        // CloudService::downloadApp($this->appName, $this->version, $this->package);
        // 返回结果
        return $this->successRes([
            'next'  => 'backCode'
        ]);
    }

    /**
     * 备份应用代码
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function backCode()
    {
        // 开始备份代码包
        // ZipUtil::build($this->backupFile, $this->baseDirPath);
        // 返回结果
        return $this->successRes([
            'next'  => 'backSql'
        ]);
    }

    /**
     * 备份数据库
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function backSql()
    {
        if (is_file($this->installSqlPath) && file_exists($this->installSqlPath)) {
            // 读取安装SQL文件
            $sql = file_get_contents($this->installSqlPath);
            // 截取表名称
            $pattern = '/CREATE TABLE `(.*)`/';
            // 匹配表名称
            preg_match_all($pattern, $sql, $matches);
            // 获取表名称
            $tables = $matches[1] ?? [];
            if (!empty($tables)) {
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
                // 替换表前缀
                $prefixs = ['xb_','php_'];
                foreach ($tables as $key => $name) {
                    $tables[$key] = str_replace($prefixs, $config['prefix'], $name);
                }
                // 开始备份数据库（包含数据）
                $mysql->exportSqlFile($this->backupSql, true, $tables);
            }
        }
        // 返回结果
        return $this->successRes([
            'next'  => 'deleteCode'
        ]);
    }

    /**
     * 删除应用旧代码
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function deleteCode()
    {
        sleep(3);
        return $this->successRes([
            'next'  => 'unzip'
        ]);
    }

    /**
     * 解压更新包
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
        sleep(3);
        return $this->successRes([
            'next'  => 'updateData'
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
        sleep(3);
        return $this->successRes([
            'next'  => 'success'
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
        sleep(3);
        return $this->successRes([
            'next'  => ''
        ]);
    }
}