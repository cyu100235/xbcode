<?php

namespace app\admin\controller;

use app\common\BaseController;
use app\common\service\CloudService;
use think\facade\Db;
use think\Request;
use xbai8\MysqlHelper;

class DeveloperController extends BaseController
{
    /**
     * 开发者应用列表
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        $data = CloudService::getAuthorAppList();
        return $this->successRes($data);
    }

    /**
     * 安装测试应用
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        // 获取数据
        $appName = $request->get('app_name', '');
        // 数据验证
        if (empty($appName)) {
            return $this->fail('应用标识不能为空');
        }
        // 获取本地版本号
        $info = CloudService::getAppLocalVersion($appName);
        $version = $info['version'];
        // 数据路径
        $installSql = root_path("base/{$appName}/data").'install.sql';
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
        // 应用初始类
        $class = "\\base\\{$appName}\\Package";
        // 执行更新前置
        if (method_exists($class, 'before_install')) {
            call_user_func([$class, 'before_install'], $this->request, $appName, $version);
        }
        // 导入数据库
        $prefix = 'xbtest_';
        $mysql->importSqlFile($installSql,$prefix,$config['prefix']);
        // 获取测试数据表
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '{$prefix}%';";
        $result = Db::query($sql);
        if (!empty($result)) {
            $TABLE_NAME = [];
            foreach ($result as $value) {
                $TABLE_NAME[] = $value['TABLE_NAME'];
            }
            // 组装表名称
            $TABLE_NAME = implode(',', $TABLE_NAME);
            // 删除测试数据表
            $result = $mysql->getConnect()->query("DROP TABLE {$TABLE_NAME}");
        }
        // 执行更新前置
        if (method_exists($class, 'after_install')) {
            call_user_func([$class, 'after_install'], $this->request, $appName, $version);
        }
        // 测试通过
        return $this->success('安装测试通过');
    }

    /**
     * 更新应用测试
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        // 获取数据
        $appName = $request->get('app_name', '');
        // 数据验证
        if (empty($appName)) {
            return $this->fail('应用标识不能为空');
        }
        // 获取本地版本号
        $info = CloudService::getAppLocalVersion($appName);
        $version = $info['version'];
        // 数据路径
        $versionPath = root_path("base/{$appName}/data/version/{$version}");
        // 检测版本目录是否存在
        if (is_dir($versionPath)) {
            // 扫描目录下所有SQL文件
            $files = glob("{$versionPath}*.sql");
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
            // 应用初始类
            $class = "\\base\\{$appName}\\Package";
            // 执行更新前置
            if (method_exists($class, 'before_update')) {
                call_user_func([$class, 'before_update'], $this->request, $appName, $version);
            }
            // 导入数据库
            $prefix = 'xbtest_';
            foreach ($files as $file) {
                // 导入数据库
                $mysql->importSqlFile($file,$prefix,$config['prefix']);
            }
            // 获取测试数据表
            $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '{$prefix}%';";
            $result = Db::query($sql);
            if (!empty($result)) {
                $TABLE_NAME = [];
                foreach ($result as $value) {
                    $TABLE_NAME[] = $value['TABLE_NAME'];
                }
                // 组装表名称
                $TABLE_NAME = implode(',', $TABLE_NAME);
                // 删除测试数据表
                $result = $mysql->getConnect()->query("DROP TABLE {$TABLE_NAME}");
            }
            // 执行更新前置
            if (method_exists($class, 'before_update')) {
                call_user_func([$class, 'before_update'], $this->request, $appName, $version);
            }
        }
        // 测试通过
        return $this->success('更新测试通过');
    }

    /**
     * 开发者模式
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getDeveloperMode(Request $request)
    {
        if ($request->isPost()) {
            $developerMode = $request->post('developerMode', '');
            CloudService::developerMode($developerMode);
            return $this->success('切换模式成功');
        }
        $data = [
            'developerMode' => CloudService::developerMode()
        ];
        return $this->successRes($data);
    }

    /**
     * 获取框架版本
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getFrameVersion(Request $request)
    {
        $data = CloudService::getFrameVersion();
        if (!isset($data['code']) || $data['code'] != 200){
            return $this->fail('获取框架版本失败');
        }
        return $this->successRes($data['data']);
    }

    /**
     * 发布代码
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function publish(Request $request)
    {
        $data = $request->post();
        return CloudService::publishAuthorApp($data);
    }
}
