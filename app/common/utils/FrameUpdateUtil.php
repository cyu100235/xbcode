<?php
namespace app\common\utils;

use app\common\exception\RollBackCodeException;
use app\common\service\CloudService;
use app\common\service\SystemService;
use app\common\utils\zip\ZipUtil;
use Exception;
use think\facade\Log;
use think\Request;
use zjkal\MysqlHelper;

/**
 * 框架更新服务
 * 更新步骤如下：
 * 1、下载更新包
 * 2、备份代码
 * 3、备份数据库
 * 4、解压更新包-删除代码-复制已解压文件至目标路径
 * 5、执行数据同步更新
 * 6、更新成功
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class FrameUpdateUtil
{
    /**
     * 当前请求管理器
     * @var Request
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $request = null;

    /**
     * 本地版本名称
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $clientVersionName = '';

    /**
     * 本地版本号
     * @var int
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $clientVersion = 0;

    /**
     * 临时ZIP文件路径
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $tempZipFilePath = null;

    /**
     * 备份源代码路径
     * @var string
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $backupCodePath = null;

    /**
     * 备份源数据库路径
     * @var string
     */
    protected $backupSqlPath = null;

    /**
     * 备份覆盖代码路径
     * @var 
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $backCoverPath = null;

    /**
     * 目标路径
     * @var string
     */
    protected $targetPath = null;


    /**
     * 打包时忽略文件或目录列表
     * 删除时忽略以下目录或文件
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $ignoreList = [
        '.git',
        'backup',
        'update',
        'public/upload',
        'public/uploads',
        'plugin',
        'runtime',
    ];

    /**
     * 备份需要覆盖的文件
     * @var array
     * @author 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    protected $backCoverList = [
        '.env',
    ];

    /**
     * 构造函数
     * @param \think\Request $Request
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct(Request $request)
    {
        // 设置请求实体
        $this->request = $request;
        // 本地版本信息
        $systemInfo = SystemService::info();
        // 客户端版本号
        $this->clientVersion = $systemInfo['version'];
        // 客户端版本名称
        $this->clientVersionName = $systemInfo['version_name'];

        // 下载框架更新包临时地址
        $this->tempZipFilePath = root_path('runtime/core') . "xbase-update.zip";
        // 检测核心目录不存在则创建
        if (!is_dir(dirname($this->tempZipFilePath))) {
            mkdir(dirname($this->tempZipFilePath), 0777, true);
        }
        // 解压至目标地址(根据环境变量设置)
        if (!env('APP_DEBUG', true)) {
            // 生产环境
            $rootPath         = substr(root_path(), 0, -1);
            $this->targetPath = $rootPath;
        } else {
            // 开发环境
            $this->targetPath = substr(root_path('runtime/web'), 0, -1);
            if (!is_dir($this->targetPath)) {
                mkdir($this->targetPath, 0777, true);
            }
        }
        // 备份当前版本代码地址
        $this->backupCodePath = root_path() . "backup/xbase-backup-{$this->clientVersion}.zip";
        // 备份当前版本数据库地址
        $this->backupSqlPath = root_path() . "backup/xbase-backup-{$this->clientVersion}.sql";
        // 备份覆盖代码地址
        $this->backCoverPath = root_path('runtime/core') . "xbase-backup-cover.zip";
    }

    /**
     * 下载更新包
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function download()
    {
        $version = (int) $this->request->post('version', '');
        if (empty($version)) {
            throw new Exception('下载版本号错误');
        }
        // 下载更新包
        // CloudService::downloadFrame($version, $this->tempZipFilePath);
        // 下载成功
        return JsonUtil::successRes([
            'next' => 'backCode'
        ]);
    }

    /**
     * 备份代码
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function backCode()
    {
        try {
            // 打包至目标压缩包
            if (!is_dir(dirname($this->backupCodePath))) {
                mkdir(dirname($this->backupCodePath), 0777, true);
            }
            // 目标目录为空，直接备份走下一步
            if (DirUtil::isDirEmpty($this->targetPath)) {
                return JsonUtil::successRes([
                    'next' => 'backSql'
                ]);
            }
            // 备份原始代码
            ZipUtil::build($this->backupCodePath, $this->targetPath, $this->ignoreList);
            // 备份覆盖代码
            ZipUtil::buildFiles($this->backCoverPath, $this->targetPath, $this->backCoverList);
        } catch (\Throwable $e) {
            Log::write(
                "备份代码失败：{$e->getMessage()}，Line：{$e->getFile()}，File：{$e->getFile()}",
                'xbase_update_error'
            );
            throw $e;
        }
        return JsonUtil::successRes([
            'next' => 'backSql'
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
        try {
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
            // 检测备份目录是否存在
            if (!is_dir(dirname($this->backupSqlPath))) {
                mkdir(dirname($this->backupSqlPath), 0755, true);
            }
            // 执行导出数据
            $mysql->exportSqlFile($this->backupSqlPath);
        } catch (\Throwable $e) {
            Log::write(
                "备份数据库失败：{$e->getMessage()}，Line：{$e->getFile()}，File：{$e->getFile()}",
                'xbase_update_error'
            );
            throw $e;
        }
        return JsonUtil::successRes([
            'next' => 'unzip'
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
        try {
            // 解压更新包
            ZipUtil::unzip($this->tempZipFilePath, $this->targetPath);
            // 解压覆盖文件
            ZipUtil::unzip($this->backCoverPath, $this->targetPath);
            // 解压成功，删除临时文件
            file_exists($this->tempZipFilePath) && unlink($this->tempZipFilePath);
        } catch (\Throwable $e) {
            // 日志记录
            Log::write(
                "更新解压出错：{$e->getMessage()}，line：{$e->getLine()}，file：{$e->getFile()}",
                "xbase_update_error"
            );
            // 报错异常，执行代码回滚
            throw new RollBackCodeException("解压出错：{$e->getMessage()}");
        }
        return JsonUtil::successRes([
            'next' => 'updateData'
        ]);
    }

    /**
     * 执行数据同步更新
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateData()
    {
        return JsonUtil::successRes([
            'next' => 'success'
        ]);
    }

    /**
     * 更新成功
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function success()
    {
        return JsonUtil::successFul('更新成功，即将跳转...', [
            'next' => ''
        ]);
    }
}
