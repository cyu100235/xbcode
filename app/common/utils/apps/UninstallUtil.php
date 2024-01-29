<?php
namespace app\common\utils\apps;

use app\common\utils\JsonUtil;
use think\Request;

/**
 * 应用卸载服务
 * 步骤如下：
 * 1、卸载代码
 * 2、卸载数据
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
        // 设置应用标识
        $this->appName = $appName;
        // 设置版本号
        $this->version = $version;
    }

    /**
     * 删除本地代码
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function deleteCode(string $appName, int $version)
    {
        sleep(3);
        return $this->successRes([
            'next'  => 'deleteSql'
        ]);
    }

    /**
     * 删除本地数据库
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function deleteSql(string $appName, int $version)
    {
        sleep(3);
        return $this->successRes([
            'next'  => 'uninstallData'
        ]);
    }

    /**
     * 卸载云端数据
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallData(string $appName, int $version)
    {
        sleep(3);
        return $this->successRes([
            'next'  => 'success'
        ]);
    }

    /**
     * 卸载完成
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function success(string $appName, int $version)
    {
        sleep(3);
        return $this->successRes([
            'next'  => ''
        ]);
    }
}