<?php
namespace app\admin\event;

use Webman\Http\UploadFile;
use app\model\Plugins;
use Exception;

/**
 * 插件导入事件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginImportEvent
{
    /**
     * 导入插件
     * @param \Webman\Http\UploadFile|null $file
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function import(UploadFile|null $file)
    {
        if (!$file) {
            throw new Exception('请上传文件');
        }
        // 转小写
        $extension = $file->getUploadExtension();
        $extension = strtolower($extension);
        if ($extension !== 'zip') {
            throw new Exception('请上传正确的插件文件格式');
        }
        // 打开压缩包
        $zip = new \ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new Exception('插件文件打开失败');
        }
        if (!$zip->getFromName('Install.php')) {
            throw new Exception('插件缺少安装类文件');
        }
        if (!$json = $zip->getFromName('info.json')) {
            throw new Exception('插件缺少信息文件');
        }
        $info = json_decode($json, true);
        if (!isset($info['title'])) {
            throw new Exception('插件信息缺少名称');
        }
        if (!isset($info['name'])) {
            throw new Exception('插件信息缺少标识');
        }
        if (!isset($info['version'])) {
            throw new Exception('插件信息缺少版本');
        }
        if (!isset($info['depend'])) {
            throw new Exception('插件信息缺少依赖');
        }
        if (empty($info['depend']) || !is_array($info['depend'])) {
            $info['depend'] = [];
        }
        if (!isset($info['author']) || empty($info['author'])) {
            throw new Exception('插件信息缺少作者');
        }
        if (!isset($info['logo']) || empty($info['logo'])) {
            $info['logo'] = '';
        }
        // 创建插件目录
        $pluginDir = base_path("plugin/{$info['name']}");
        if (!is_dir($pluginDir)) {
            mkdir($pluginDir, 0755, true);
        }
        // 解压插件
        $zip->extractTo($pluginDir);
        // 关闭资源
        $zip->close();
        // 新增记录
        $model = Plugins::where('name', $info['name'])->find();
        if (!$model) {
            $model = new Plugins;
        }
        $data = [
            'title' => $info['title'],
            'name' => $info['name'],
            'desc' => $info['desc'],
            'version' => $info['version'],
            'author' => $info['author'],
            'logo' => $info['logo'],
        ];
        if (!$model->save($data)) {
            throw new Exception('插件信息保存失败');
        }
    }
}
