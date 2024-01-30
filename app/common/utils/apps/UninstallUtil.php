<?php
namespace app\common\utils\apps;

use app\common\service\CloudService;
use app\common\utils\DirUtil;
use app\common\utils\JsonUtil;
use think\Request;

/**
 * 应用卸载服务
 * 步骤如下：
 * 1、卸载数据
 * 2、卸载代码
 * 3、云卸载数据
 * 4、卸载完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UninstallUtil
{
    // 引入JsonUtil
    use JsonUtil;

    /**
     * 请求对象
     * @var Request|null
     */
    protected $request = null;

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
        // 创建应用目录
        $this->baseDirPath = root_path("base/{$appName}");
        // 安装SQL文件路径
        $this->installSqlPath = "{$this->baseDirPath}data/install.sql";
        // 设置应用标识
        $this->appName = $appName;
        // 设置版本号
        $this->version = $version;
    }

    /**
     * 删除本地数据库
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function deleteSql()
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
                $db = new \PDO("mysql:host={$config['hostname']}:{$config['hostport']};dbname={$config['database']}", $config['username'], $config['password']);
                // 替换表前缀
                $prefixs = ['xb_', 'php_'];
                foreach ($tables as $name) {
                    $tableName = str_replace($prefixs, $config['prefix'], $name);
                    $sql = "DROP TABLE IF EXISTS `{$tableName}`;";
                    $result = $db->query($sql);
                    if ($result === false) {
                        throw new \Exception('卸载数据失败');
                    }
                }
            }
        }
        return $this->successRes([
            'next'  => 'uninstallData'
        ]);
    }

    /**
     * 删除本地代码
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function deleteCode()
    {
        // 删除应用目录
        DirUtil::delDir($this->baseDirPath);
        // 数据返回
        return $this->successRes([
            'next'  => 'deleteSql'
        ]);
    }

    /**
     * 卸载云端数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallData()
    {
        // 处理云端数据
        CloudService::unInstallApp($this->appName, $this->version);
        // 数据返回
        return $this->successRes([
            'next'  => 'success'
        ]);
    }

    /**
     * 卸载完成
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function success()
    {
        return $this->successFul('卸载完成',[
            'next'  => ''
        ]);
    }
}