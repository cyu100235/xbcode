<?php
namespace xbcode\providers\update;

use support\Log;
use xbcode\utils\MysqlUtil;

/**
 * 更新数据提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class UpdateDataProvider
{
    /**
     * 更新版本名称
     * @var string
     */
    private $versionName;

    /**
     * 更新版本编号
     * @var string
     */
    private $version;

    /**
     * 本地版本名称
     * @var string
     */
    private $localVersionName;

    /**
     * 本地版本编号
     * @var int
     */
    private $localVersion;

    /**
     * 构造函数
     * @param string $version 更新版本号
     * @param string $localVersion 本地版本号
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct(string $versionName, int $version, string $localVersionName, int $localVersion)
    {
        $this->versionName      = $versionName;
        $this->version          = $version;
        $this->localVersionName = $localVersionName;
        $this->localVersion     = $localVersion;
    }

    /**
     * 前置更新
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function beforeUpdate()
    {
        // sql目录
        $sqlDir = base_path() . '/update';
        if (!is_dir($sqlDir)) {
            return [];
        }
        // 获取sql文件
        $sqlFiles = glob("{$sqlDir}/*.sql");
        $data     = [];
        foreach ($sqlFiles as $file) {
            $sqlContent = file_get_contents($file);
            if (empty($sqlContent) && file_exists($file)) {
                unlink("{$sqlDir}/{$file}");
                continue;
            }
            $data[] = [
                'file' => $file,
                'sql' => $sqlContent,
            ];
        }
        return $data;
    }

    /**
     * 后置更新
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(array $data)
    {
        $prefix  = config('thinkorm.connections.mysql.prefix');
        $prefixs = ['`xb_', '`php_', '`__PREFIX__'];
        try {
            foreach ($data as $value) {
                if (empty($value['file'])) {
                    continue;
                }
                if (empty($value['sql'])) {
                    continue;
                }
                try {
                    // 替换前缀
                    $sql = str_replace($prefixs, "`{$prefix}", $value['sql']);
                    Log::info("更新系统SQL：{$sql}");
                    // 执行SQL
                    MysqlUtil::execute($sql);
                } catch (\Throwable $e) {
                    Log::error("系统更新SQL错误，继续执行：{$e->getMessage()}，Line：{$e->getLine()}，File：{$e->getFile()}");
                }
                // 执行后，无论成功失败，删除文件
                if (file_exists($value['file'])) {
                    unlink($value['file']);
                }
            }
        } catch (\Throwable $e) {
            Log::error("更新SQL错误，终止：{$e->getMessage()}，Line：{$e->getLine()}，File：{$e->getFile()}");
        }
    }
}