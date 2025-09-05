<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;
use plugin\xbCode\api\PluginPreviewApi;
use plugin\xbDeveloper\utils\GitUtil;

/**
 * 插件创建接口
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsCreate
{
    /**
     * 目录备注
     * @var array
     */
    private static $dirRemarks = [
        'api'                   => "接口目录\n主要与其他插件对接，非网络请求接口",
        'app'                   => '应用目录',
        'app/queue'             => '队列任务目录',
        'app/admin/controller'  => '后台控制器目录',
        'app/admin/view'        => '后台视图目录',
        'app/admin/view/index'  => '后台首页视图目录',
        'app/home/controller'   => '默认控制器目录',
        'app/model'             => '模型目录',
        'config'                => '配置目录',
        'enum'                  => '枚举目录',
        'data'                  => '数据目录',
        'public'                => '公共目录',
        'setting'               => '设置目录',
        'setting/config'        => '普通设置目录',
        'setting/tabs'          => '选项卡设置目录',
    ];

    /**
     * 插件文件路径
     * @var array
     */
    private static $files = [
        'api/Install.php',
        'app/functions.php',
        'app/admin/controller/IndexController.php',
        'app/admin/view/index/workbench.vue',
        'app/home/controller/IndexController.php',
        'app/queue/DemoQueue.php',
        // 'config/apidoc.php',
        'config/app.php',
        'config/autoload.php',
        'config/container.php',
        'config/exception.php',
        'config/log.php',
        'config/middleware.php',
        'config/process.php',
        'config/redis.php',
        'config/route.php',
        'config/static.php',
        'config/translation.php',
        'config/view.php',
        'config/menu.php',
        'config/dict.php',
        'config/home.php',
        'setting/config/basis.php',
        'setting/tabs/panel.php',
        'plugins.json',
        'preview.svg',
        'install.sql',
    ];

    /**
     * 是否输出日志
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static $output = false;

    /**
     * 插件信息
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static $plugin = [];

    /**
     * 创建插件
     * @param array $data
     * @param bool $output
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function create(array $data, bool $output = false)
    {
        if (empty($data['title'])) {
            $data['title'] = '未命名插件';
        }
        if (empty($data['name'])) {
            throw new Exception('请填写插件标识');
        }
        if (!str_starts_with($data['name'], 'xb')) {
            // 首字母转大写
            $data['name'] = ucfirst($data['name']);
            // 添加前缀
            $data['name'] = "xb{$data['name']}";
        }
        if (empty($data['desc'])) {
            $data['desc'] = '插件描述，20-50字';
        }
        if (empty($data['author'])) {
            $data['author'] = '积木云';
        }
        if (empty($data['gradient'])) {
            $data['gradient'] = false;
        }
        // 数据验证
        if (strpos($data['name'], '/') !== false) {
            throw new Exception('插件标识名称错误，名称不能包含字符 /');
        }
        if (is_dir(base_path() . "/plugin/{$data['name']}")) {
            throw new Exception("{$data['name']} 已经创建啦~");
        }
        // 检查模板文件完整性
        static::validateTpl($data['name']);
        // 是否输出日志
        static::$output = $output;
        // 插件信息
        static::$plugin = $data;
        // 插件根路径
        $pluginRootPath = base_path() . '/plugin';
        // 插件路径ju
        $pluginPath = "{$pluginRootPath}/{$data['name']}";
        // 创建插件说明文件
        static::mkPluginRemarks($pluginPath);
        // 批量创建目录
        foreach (static::$dirRemarks as $dir => $content) {
            $path = "{$pluginPath}/{$dir}";
            static::mkdir($path, $content, $data['name']);
        }
        // 批量创建文件
        foreach (static::$files as $path) {
            // 创建文件
            static::createFile($path, $data['name']);
        }
        // 删除插件预览图
        $previewPath = base_path() . "/plugin/{$data['name']}/preview.svg";
        if (file_exists($previewPath)) {
            unlink($previewPath);
        }
        // 重新创建插件预览图
        PluginPreviewApi::make()->create($data);
    }

    /**
     * 验证模板文件完整性
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function validateTpl(string $name)
    {
        $files = static::$files;
        $basePath = base_path();
        $prefix = [
            '.jpg',
            '.png',
            '.svg',
            '.php',
            '.json',
            '.sql',
            '.vue',
        ];
        foreach ($files as $file) {
            $templateFile = str_replace($prefix, '.tpl', $file);
            $filePath = "{$basePath}/plugin/xbDeveloper/data/plugin/{$templateFile}";
            if(!file_exists($filePath)) {
                $shortTplPath = str_replace($basePath, '', $filePath);
                throw new Exception("{$shortTplPath} 模板文件不存在，请检查模板文件是否完整");
            }
        }
    }
    
    /**
     * 克隆插件
     * @param array $data 插件数据
     * @param bool $output 是否输出日志
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function clone(array $data, bool $output = false)
    {
        // 数据验证
        $data = static::validate($data);
        if (empty($data['url'])) {
            throw new Exception('请填写插件仓库地址');
        }
        // 检测仓库地址格式
        if (!preg_match('/^git@.*\.git$/', $data['url'])) {
            throw new Exception('请填写正确的SSH格式仓库地址');
        }
        $pluginPath = base_path() . "/plugin/{$data['name']}";
        // 扫描目录
        $dirs = glob("{$pluginPath}/.*");
        $dirs = array_filter($dirs, function ($dir) {
            $file = basename($dir);
            if (!in_array($file, ['.', '..'])) {
                return $dir;
            }
        });
        $dirs = array_values($dirs);
        // 获取插件目录文件
        $files = glob("{$pluginPath}/*");
        $files = array_merge($dirs, $files);
        if (!empty($files)) {
            throw new Exception('该插件仓库已存在');
        }
        // 克隆仓库
        GitUtil::clone($data['url'], $pluginPath);
        // 是否输出日志
        static::$output = $output;
        // 插件信息
        static::$plugin = $data;
        // 创建插件说明文件
        if (!is_dir($pluginPath)) {
            static::mkPluginRemarks($pluginPath);
        }
        // 批量创建目录
        foreach (static::$dirRemarks as $dir => $content) {
            $path = "{$pluginPath}/{$dir}";
            // 检测文件是否存在
            if (is_dir($path)) {
                continue;
            }
            static::mkdir($path, $content, $data['name']);
        }
        // 批量创建文件
        foreach (static::$files as $path) {
            // 创建文件
            static::createFile($path, $data['name']);
        }
        // 检测插件预览图是否存在
        $previewPath = "{$pluginPath}/preview.svg";
        if (file_exists($previewPath)) {
            return;
        }
        // 重新创建插件预览图
        \plugin\xbCode\api\PluginsApi::make()->createPreview($data);
    }
    
    /**
     * 数据验证
     * @param array $data
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function validate(array $data)
    {
        if (empty($data['title'])) {
            throw new Exception('请填写插件名称');
        }
        if (empty($data['name'])) {
            throw new Exception('请填写插件标识');
        }
        $name = preg_replace('/xb/', '', $data['name'], 1);
        if (strpos($name, '/') !== false) {
            throw new Exception('插件标识名称错误，名称不能包含字符 /');
        }
        // 检测插件标识是否字母+数字
        if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) {
            throw new Exception('插件标识必是字母+数字');
        }
        // 检测插件标识是否字母开头
        if (!preg_match('/^[a-zA-Z]/', $name)) {
            throw new Exception('插件标识必须字母开头');
        }
        if (empty($data['desc'])) {
            throw new Exception('请填写插件描述');
        }
        if (empty($data['author'])) {
            throw new Exception('请填写作者名称');
        }
        $titleCount = mb_strlen($data['title']);
        $nameCount = mb_strlen($name);
        $descCount = mb_strlen($data['desc']);
        $authorCount = mb_strlen($data['author']);
        if ($titleCount < 2 || $titleCount > 10) {
            throw new Exception('插件名称长度为2-10个字');
        }
        if ($nameCount <= 1 || $nameCount > 15) {
            throw new Exception('插件标识长度为2-15个字');
        }
        if ($descCount < 3 || $descCount > 30) {
            throw new Exception('插件描述长度为3-30个字');
        }
        if ($authorCount < 2 || $authorCount > 8) {
            throw new Exception('作者名称长度为2-8个字符');
        }
        // 标首字母转大写
        $name = ucfirst($name);
        // 重组插件标识
        $data['name'] = "xb{$name}";
        // 返回数据
        return $data;
    }

    
    /**
     * 创建插件说明文件
     * @param string $pluginPath
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function mkPluginRemarks(string $pluginPath)
    {
        $content = "plugin.json为插件信息文件\n\n";
        $content .= "插件目录说明\r\n";
        foreach (static::$dirRemarks as $dir => $value) {
            $value = str_replace("\n", "，", $value);
            $content .= " - {$dir} ------ {$value}\r\n";
        }
        static::outputLog("Create Dir {$pluginPath}");
        mkdir($pluginPath, 0775, true);
        file_put_contents("{$pluginPath}/remarks.txt", "Create by 小白基地\n\n{$content}");
    }

    /**
     * 创建目录
     * @param string $path
     * @param string $content
     * @param string $name
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function mkdir(string $path, string $content, string $name)
    {
        if (is_dir($path)) {
            return;
        }
        static::outputLog("Create Dir {$path}");
        mkdir($path, 0775, true);
        file_put_contents("{$path}/remarks.txt", "Create by 小白基地\n\n{$content}");
    }

    /**
     * 创建文件
     * @param string $path
     * @param string $name
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function createFile(string $path, string $name)
    {
        // 站点根路径
        $basePath = base_path();
        // 目标插件地址
        $pluginPath = "{$basePath}/plugin/{$name}";
        if (!is_dir($pluginPath)) {
            throw new Exception("插件 {$name} 目录不存在");
        }
        // 文件后缀
        $suffix = ['.php', '.html', '.md', '.sql', '.json','.vue', '.jpg', '.svg', '.png', '.js', '.css'];
        // 去除后缀
        $fileName = str_replace($suffix, '', $path);
        // 目标文件路径
        $targetPath = "{$basePath}/plugin/{$name}/{$path}";
        // 检测文件是否存在
        if (file_exists($targetPath)) {
            static::outputLog("目标 {$targetPath} 文件已存在，跳过创建");
            return;
        }
        // 模板路径
        $tplPath = "/plugin/xbDeveloper/data/plugin/{$fileName}.tpl";
        // 完整模板路径
        $tplFullPath = "{$basePath}{$tplPath}";
        if (!file_exists($tplFullPath)) {
            throw new Exception("模板文件 {$tplPath} 不存在");
        }
        // 读取模板内容
        $content = file_get_contents($tplFullPath);
        // 替换模板中的变量
        $str1 = [
            '{PLUGIN_TITLE}',
            '{PLUGIN_NAME}',
            '{PLUGIN_DESC}',
            '{PLUGIN_AUTHOR}',
        ];
        $str2 = [
            static::$plugin['title'],
            static::$plugin['name'],
            static::$plugin['desc'],
            static::$plugin['author'],
        ];
        $content = str_replace($str1, $str2, $content);
        // 输出日志
        static::outputLog("Create File $targetPath");
        // 写入文件
        file_put_contents($targetPath, $content);
    }

    /**
     * 输出日志
     * @param string $message
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function outputLog(string $message)
    {
        if (static::$output) {
            echo $message . "\r\n";
        }
    }
}