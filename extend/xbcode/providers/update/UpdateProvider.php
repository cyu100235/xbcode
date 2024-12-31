<?php
namespace xbcode\providers\update;

use xbcode\utils\DirUtil;
use xbcode\utils\ZipUtil;
use xbcode\trait\JsonTrait;
use xbcode\utils\FrameUtil;
use xbcode\utils\MysqlUtil;
use xbcode\service\xbcode\ProjectService;

/**
 * 更新服务提供者
 * 1.下载更新包
 * 2.备份代码
 * 3.备份数据库
 * 4.解压更新包
 * 5.更新数据库
 * 6.重启服务
 * 7.更新完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UpdateProvider
{
    // 引入JsonTrait
    use JsonTrait;

    /**
     * 更新版本名称
     * @var string
     */
    protected $versionName;

    /**
     * 更新版本号
     * @var int
     */
    protected $version;

    /**
     * 本地版本名称
     * @var string
     */
    protected $localVersionName;

    /**
     * 本地版本号
     * @var int
     */
    protected $localVersion;

    /**
     * 更新包路径
     * @var string
     */
    protected $updatePath;

    /**
     * 备份代码路径
     * @var string
     */
    protected $backupPath;

    /**
     * 站点根路径
     * @var string
     */
    protected $targetPath;

    /**
     * 忽略备份文件
     * @var array
     */
    protected $backupIgnoreFile = [
        '.git',
        'backup',
        'public/uploads',
        'runtime',
    ];

    /**
     * 构造函数
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct()
    {
        // 更新包路径
        $this->updatePath = base_path() . '/runtime/update';
        // 备份代码路径
        $this->backupPath = base_path() . '/backup';
        // 站点根路径
        $this->targetPath = base_path();
        // 本地版本名
        $this->localVersionName = config('projects.version_name');
        // 本地版本号
        $this->localVersion = config('projects.version');
        // 开发环境目标路径
        if (xbEnv('APP_DEBUG', false)) {
            $this->targetPath = base_path() . '/runtime/web';
        }
    }
    
    /**
     * 开始更新
     * @param string $versionName 更新版本名
     * @param int $version 更新版本号
     * @param string $step 更新步骤
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function start(string $versionName, int $version, string $step)
    {
        if (!method_exists($this, $step)) {
            return $this->fail('更新步骤错误');
        }
        // 更新版本名
        $this->versionName = $versionName;
        // 更新版本号
        $this->version = $version;
        // 执行更新步骤
        return call_user_func([$this, $step]);
    }

    /**
     * 下载更新包
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function download()
    {
        // 检测更新包目录是否存在
        if (!is_dir($this->updatePath)) {
            mkdir($this->updatePath, 0777, true);
        }
        // 储存路径
        $file = $this->updatePath . DIRECTORY_SEPARATOR . $this->version . '.zip';
        // 检测文件是否存在
        if (file_exists($file)) {
            unlink($file);
        }
        // 下载更新包
        $content = ProjectService::download($this->version);
        // 写入文件
        file_put_contents($file, $content);
        return $this->successRes([
            'next' => 'backupCode'
        ]);
    }

    /**
     * 备份代码
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function backupCode()
    {
        // 检测备份目录是否存在
        $versionBackupDir = $this->backupPath . DIRECTORY_SEPARATOR . $this->localVersionName;
        if (!is_dir($versionBackupDir)) {
            mkdir($versionBackupDir, 0777, true);
        }
        // 非开发环境，检测目标路径是否存在
        if (!xbEnv('APP_DEBUG', false) && !is_dir($this->targetPath)) {
            return $this->fail('备份代码目录不存在');
        }
        // 开发环境，创建目标路径
        if (xbEnv('APP_DEBUG', false) && !is_dir($this->targetPath)) {
            mkdir($this->targetPath, 0777, true);
            file_put_contents($this->targetPath . '/remarks.txt', '开发环境，不要删除');
        }
        // 备份文件名
        $date = date('Y年m月d日H点');
        $filename = "{$this->localVersion}-{$date}.zip";
        // 备份至压缩包路径
        $backupCodePath = $versionBackupDir . DIRECTORY_SEPARATOR . $filename;
        // 备份代码
        ZipUtil::build($backupCodePath, $this->targetPath, $this->backupIgnoreFile);
        return $this->successRes([
            'next' => 'backupSql'
        ]);
    }

    /**
     * 备份数据库
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function backupSql()
    {
        // 检测备份目录是否存在
        $versionBackupDir = $this->backupPath . DIRECTORY_SEPARATOR . $this->localVersionName;
        if (!is_dir($versionBackupDir)) {
            mkdir($versionBackupDir, 0755, true);
        }
        // 备份文件名
        $date = date('Y年m月d日H点');
        $filename = "{$this->localVersionName}-{$date}.sql";
        // 备份至压缩包路径
        $backupSqlPath = $versionBackupDir . DIRECTORY_SEPARATOR . $filename;
        // 备份数据库
        MysqlUtil::exportSql($backupSqlPath);
        return $this->successRes([
            'next' => 'unzip'
        ]);
    }

    /**
     * 解压更新包
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function unzip()
    {
        // 储存路径
        $zipPath = $this->updatePath . DIRECTORY_SEPARATOR . $this->versionName . '.zip';
        // 解压更新包
        ZipUtil::unzip($zipPath, $this->targetPath);
        // 解压完成，删除更新包
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
        // 返回数据
        return $this->successRes([
            'next' => 'updateData'
        ]);
    }

    /**
     * 更新数据库
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function updateData()
    {
        // 更新类路径
        $updateDataPath = base_path() . '/extend/xbcode/providers/update/UpdateDataProvider.php';
        if (!file_exists($updateDataPath)) {
            throw new \Exception('数据更新脚本不存在');
        }
        // 重新引入更新类，确保是最新更新类
        require_once $updateDataPath;
        $class = "\\xbcode\\providers\\update\\UpdateDataProvider";
        if (class_exists($class)) {
            $updateServiceCls = new $class(
                $this->versionName,
                $this->version,
                $this->localVersionName,
                $this->localVersion
            );
            // 执行更新前置
            $context = [];
            if (method_exists($class, 'beforeUpdate')) {
                $context = call_user_func([$updateServiceCls, 'beforeUpdate']);
            }
            // 执行update更新
            if (method_exists($class, 'update')) {
                call_user_func([$updateServiceCls, 'update'],$context);
            }
        }
        // 删除更新SQL目录
        $updateSqlPath = base_path() . '/update';
        if (is_dir($updateSqlPath)) {
            DirUtil::delDir($updateSqlPath);
        }
        return $this->successRes([
            'next' => 'restart'
        ]);
    }

    /**
     * 重启服务
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function restart()
    {
        // 停止服务
        FrameUtil::stop();
        return $this->successRes([
            'next' => 'success'
        ]);
    }

    /**
     * 更新完成
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function success()
    {
        return $this->successFul('更新成功，即将跳转...',[
            'next' => ''
        ]);
    }
}