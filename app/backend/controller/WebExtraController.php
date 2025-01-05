<?php
namespace app\backend\controller;

use app\model\WebAdmin;
use app\model\WebSite;
use support\Request;
use think\facade\Db;
use xbcode\providers\MysqlProvider;
use xbcode\providers\UploadProvider;
use xbcode\utils\TokenUtil;
use xbcode\XbController;

/**
 * 站点扩展控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebExtraController extends XbController
{
    /**
     * 打开链接
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function link(Request $request)
    {
        $id   = $request->get('id');
        $type = $request->get('type');
        if (empty($id) || empty($type)) {
            return $this->fail('参数错误');
        }
        $model = WebSite::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该数据不存在');
        }
        if (!method_exists($this, $type)) {
            return $this->fail('该操作不存在');
        }
        // 查询系统管理员
        $where = [
            'admin.saas_appid' => $model['id'],
            'admin.is_system' => '20',
        ];
        $fields = [
            'admin.*',
            'site.domain',
        ];
        $model = WebAdmin::alias('admin')
            ->join('web_site site', 'site.id=admin.saas_appid')
            ->where($where)
            ->field($fields)
            ->findOrEmpty();
        if ($model->isEmpty()) {
            return $this->fail('该站点没有系统管理员');
        }
        $result = $model->toArray();
        if (empty($result['domain'])) {
            return $this->fail('该站点没有绑定域名');
        }
        return call_user_func([$this, $type], $request, $result);
    }
    
    /**
     * 打开后台
     * @param \support\Request $request
     * @param array $result
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function admin(Request $request, array $result)
    {
        // 生成令牌
        $data = [
            'id' => $result['id'],
            'saas_appid' => $result['saas_appid'],
            'username' => $result['username'],
            'state' => $result['state'],
            'is_system' => $result['is_system'],
        ];
        $data = TokenUtil::create($data);
        if (empty($data['access_token'])) {
            return $this->fail('令牌生成失败');
        }
        $domain = "http://{$result['domain']}";
        $url    = "{$domain}/admin/#/?token={$data['access_token']}";
        return $this->successRes([
            'url' => $url,
        ]);
    }
    
    /**
     * 打开前台
     * @param \support\Request $request
     * @param array $result
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function home(Request $request, array $result)
    {
        $domain = "http://{$result['domain']}";
        return $this->successRes([
            'url' => $domain,
        ]);
    }
    
    /**
     * 清空数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function clear(Request $request)
    {
        $id = $request->get('id');
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        $model = WebSite::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该站点不存在');
        }
        $data = $this->getTables();
        if (empty($data)) {
            return $this->fail('没有配置可清空的数据表');
        }
        // 遍历清空数据
        foreach ($data as $name) {
            // 过滤站点管理员表
            if ($name == 'web_admin') {
                continue;
            }
            // 获取表字典
            $fields = MysqlProvider::getColumnName($name);
            if (empty($fields)) {
                continue;
            }
            // 检测是否存在APPID字段
            if (!in_array('saas_appid', $fields)) {
                continue;
            }
            // 删除数据
            Db::name($name)->where('saas_appid', $id)->delete();
        }
        return $this->success('数据清空成功');
    }
    
    /**
     * 下载数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function download(Request $request)
    {
        $key = $request->get('key');
        if (empty($key)) {
            return $this->fail('参数错误');
        }
        // 文件唯一名称
        $filename = "website_{$key}.sql";
        // 文件短路径
        $shortPath = "/runtime/export/{$filename}";
        // 保存路径
        $path = base_path() . $shortPath;
        if (!is_file($path) || !file_exists($path)) {
            return $this->fail('文件密钥错误或文件不存在');
        }
        $dateTime = date('Y年m月d日-H点i分');
        $filename = "站点数据导出_{$dateTime}.sql";
        return response()->download($path, $filename);
    }
    
    /**
     * 导出数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function export(Request $request)
    {
        $id = $request->get('id');
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        $model = WebSite::where('id', $id)->find();
        if (!$model) {
            return $this->fail('该站点不存在');
        }
        // 文件名称哈希
        $fileHash = md5($model['id']);
        // 文件唯一名称
        $filename = "website_{$fileHash}.sql";
        // 文件短路径
        $shortPath = "/runtime/export/{$filename}";
        // 保存路径
        $path = base_path() . $shortPath;
        // 检测目录不存在
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        // 生成下载链接
        $url = xbUrl('WebExtra/download', ['key' => $fileHash], true);
        // 表前缀
        $prefix    = config('thinkorm.connections.mysql.prefix');
        $oldPrefix = '__PREFIX__';
        // 打开输出文件
        $outputFile = fopen($path, 'w');
        // 写入当前站点数据
        $this->writeSql($outputFile, "{$oldPrefix}web_site", [$model->toArray()]);
        // 表名称数据
        $data = $this->getTables();
        // 遍历导出数据
        foreach ($data as $name) {
            // 完整表名称
            $tableName = "{$prefix}{$name}";
            $fields    = MysqlProvider::getColumnName($tableName);
            if (empty($fields)) {
                continue;
            }
            // 检测是否存在APPID字段
            if (!in_array('saas_appid', $fields)) {
                continue;
            }
            $result = Db::name($name)->where('saas_appid', $model['id'])->select()->toArray();
            if (empty($result)) {
                fwrite($outputFile, "/* {$tableName}表没有数据 */\n");
            } else {
                $this->writeSql($outputFile, "{$oldPrefix}{$name}", $result);
            }
        }
        // 返回成功
        return $this->successRes([
            'url' => $url,
        ]);
    }

    /**
     * 获取可导出表名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function getTables()
    {
        // 表前缀
        $prefix = config('thinkorm.connections.mysql.prefix');
        // 获取所有表名称
        $tables = MysqlProvider::getTableNames();
        $data   = [];
        foreach ($tables as $name) {
            // 获取表字段
            $fields = MysqlProvider::getColumnName($name);
            // 检测是否存在APPID字段
            if (!in_array('saas_appid', $fields)) {
                continue;
            }
            // 替换表前缀
            $name = str_replace($prefix, '', $name);
            // 添加到数据
            $data[] = $name;
        }
        return $data;
    }

    /**
     * 写入SQL
     * @param mixed $outputFile
     * @param string $name
     * @param array $result
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function writeSql(mixed $outputFile, string $name, array $result)
    {
        fwrite($outputFile, "-- 表数据：{$name}\n");
        foreach ($result as $value) {
            // 输出表数据
            $escapedValues = array_map(function ($value) {
                return addslashes($value);
            }, $value);
            // 转义数据
            $columns = implode("','", $escapedValues);
            fwrite($outputFile, "INSERT INTO `{$name}` VALUES ('{$columns}');\n");
        }
    }
    
    /**
     * 导入站点数据
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function import(Request $request)
    {
        // 上传文件至本地
        $model = UploadProvider::upload('file', 0, 0, 'local');
        // 获取文件路径
        $data = $model->toArray();
        // 文件路径
        $path = public_path() . $data['uri'];
        // 表前缀名称
        $prefix = config('thinkorm.connections.mysql.prefix');
        // 导入数据
        MysqlProvider::importSql($path, '__PREFIX__', $prefix);
        // 返回成功
        return $this->success('导入成功');
    }
}
