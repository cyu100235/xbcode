<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class XbPluginCreateCommand extends Command
{
    protected static $defaultName = 'xb-plugin:create';
    protected static $defaultDescription = 'Xb Plugin Create';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("Create Xb Plugin $name");

        if (strpos($name, '/') !== false) {
            $output->writeln('<error>Bad name, name must not contain character \'/\'</error>');
            return self::FAILURE;
        }

        // Create dir config/plugin/$name
        if (is_dir($plugin_config_path = base_path() . "/plugin/$name")) {
            $output->writeln("<error>Dir $plugin_config_path already exists</error>");
            return self::FAILURE;
        }

        $this->createAll($name);

        return self::SUCCESS;
    }

    /**
     * @param $name
     * @return void
     */
    protected function createAll($name)
    {
        $base_path = base_path();
        $this->mkdir("$base_path/plugin/$name/app/controller", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/event", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/model", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/view/index", 0777, true);
        $this->mkdir("$base_path/plugin/$name/config", 0777, true);
        $this->mkdir("$base_path/plugin/$name/data/sql", 0777, true);
        $this->mkdir("$base_path/plugin/$name/public", 0777, true);
        $this->mkdir("$base_path/plugin/$name/api", 0777, true);
        $this->mkdir("$base_path/plugin/$name/setting", 0777, true);
        $this->mkdir("$base_path/plugin/$name/docs", 0777, true);
        $this->createFunctionsFile("$base_path/plugin/$name/app/functions.php");
        $this->createControllerFile("$base_path/plugin/$name/app/controller/IndexController.php", $name);
        $this->createViewFile("$base_path/plugin/$name/app/view/index/index.html");
        $this->createConfigFiles("$base_path/plugin/$name/config", $name);
        $this->createSettingFiles("$base_path/plugin/$name/setting", $name);
        $this->createApiFiles("$base_path/plugin/$name", $name);
        $this->createInfoFiles("$base_path/plugin/$name", $name);
        $this->createDocsFiles("$base_path/plugin/$name/docs", $name);
        $this->createInstallSqlFile("$base_path/plugin/$name/data/sql/install.sql");
        $this->createInstallSqlFile("$base_path/plugin/$name/data/sql/update.sql");
        $this->createInstallSqlFile("$base_path/plugin/$name/data/sql/uninstall.sql");
    }

    /**
     * @param $path
     * @return void
     */
    protected function mkdir($path, $mode = 0777, $recursive = false)
    {
        if (is_dir($path)) {
            return;
        }
        echo "Create $path\r\n";
        mkdir($path, $mode, $recursive);
        file_put_contents("$path/remarks.txt", "Create by 小白基地\n");
    }

    /**
     * 创建文档文件
     * @param string $path
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function createDocsFiles(string $path, string $name)
    {
        // 接口文档
        $content = <<<EOF
# 事件文档

EOF;
        file_put_contents("$path/events.md", $content);

        // 事件文档
        $content = <<<EOF
# 接口文档

EOF;
        file_put_contents("$path/apis.md", $content);

        // 测试文档
        $content = <<<EOF
        # 测试文档
EOF;
        file_put_contents("$path/test.md", $content);
        $content = <<<EOF
<?php

return [
    'apps' => [
        [
            // （必须）标题
            'title' => '官网端',
            // （必须）控制器目录地址
            'path' => 'plugin\\{$name}\\app\\controller',
            // （必须）唯一的key
            'key' => 'home',
        ],
    ],
    'docs' => [
        [
            'title' => '测试文档',
            'path' => 'plugin/{$name}/docs/test',
        ],
    ],
];
EOF;
        $pluginPath = dirname($path);
        file_put_contents("$pluginPath/config/apidoc.php", $content);
    }

    /**
     * 创建插件配置文件
     * @param mixed $path
     * @param mixed $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function createSettingFiles($path, $name)
    {
        $content = <<<EOF
<?php

return [
    [
        // 支持以.多层级配置
        'field' => 'plugin_name',
        'title' => '网站名称',
        'value' => '',
        'component' => 'input',
        'extra' => [
            'col' => 12,
            'prompt' => '应用名称，显示在浏览器标签页',
        ],
    ],
];
EOF;
        file_put_contents("$path/basis.php", $content);
        $content = <<<EOF
<?php
return [
    [
        'title' => '基础配置',
        'name' => 'basis',
    ],
];
EOF;
        file_put_contents("$path/config.php", $content);
    }

    /**
     * @param $path
     * @param $name
     * @return void
     */
    protected function createControllerFile($path, $name)
    {
        $content = <<<EOF
<?php
namespace plugin\\$name\\app\\controller;

use app\\common\\XbController;
use support\\Request;

class IndexController extends XbController
{
    public function index(Request \$request)
    {
        return view('index/index', ['name' => '$name']);
    }
}
EOF;
        file_put_contents($path, $content);
    }

    /**
     * @param $path
     * @return void
     */
    protected function createViewFile($path)
    {
        $content = <<<EOF
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico"/>
    <title>小白基地插件</title>

</head>
<body>
hello <?=htmlspecialchars(\$name)?>
</body>
</html>


EOF;
        file_put_contents($path, $content);

    }


    /**
     * @param $file
     * @return void
     */
    protected function createFunctionsFile($file)
    {
        $content = <<<EOF
<?php
/**
 * 插件自定义函数库
 */

EOF;
        file_put_contents($file, $content);
    }

    /**
     * 插件插件信息文件
     * @param mixed $basePath
     * @param mixed $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function createInfoFiles($basePath, $name)
    {
        $data = [
            'title' => '基础插件系统',
            'name' => $name,
            'version' => '1.0.0',
            'desc' => '',
            'author' => '楚羽幽',
            'logo' => '',
            'depend' => [],
        ];
        $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents("$basePath/info.json", $content);
    }

    /**
     * @param $base
     * @param $name
     * @return void
     */
    protected function createApiFiles($basePath, $name)
    {
        $content = <<<EOF
<?php
namespace plugin\\$name;

use app\common\providers\MenuProvider;

/**
 * 插件安装卸载类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install
{
    /**
     * 安装前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installBefore()
    {
    }
    
    /**
     * 安装
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install()
    {
        // 创建菜单
        \$this->createMenus();
        // 创建字典
        \$this->createDicts();
        
        // 导入安装SQL
        // \$sql = __DIR__ . '/data/install.sql';
        // MysqlProvider::importSql(\$sql);
    }

    
    /**
     * 创建字典
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function createDicts()
    {
        // 获取字典数据
        \$data = config('plugin.{$name}.dict', []);
        if (empty(\$dicts)) {
            return true;
        }
        // 批量创建字典
        DictProvider::addDicts(\$data);
        return true;
    }

    /**
     * 创建菜单
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function createMenus()
    {
        // 获取菜单数据
        \$menus = config('plugin.finance.menus', []);
        if (empty(\$menus)) {
            return true;
        }
        // 批量创建菜单
        MenuProvider::createMenus(\$menus);
        return true;
    }

    /**
     * 安装后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installAfter()
    {
    }

    /**
     * 更新前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateBefore()
    {
        // 导入更新SQL
        // \$sql = __DIR__ . '/data/update.sql';
        // MysqlProvider::importSql(\$sql);
    }

    /**
     * 更新
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update()
    {
    }

    /**
     * 更新后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateAfter()
    {
    }

    /**
     * 卸载前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallBefore()
    {
    }

    /**
     * 卸载
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall()
    {
        // 卸载菜单数据
        \$this->delMenu();
        
        // 批量删除字典
        \$dicts = config('plugin.{$name}.dict', []);
        DictProvider::delDicts(\$dicts);

        // 导入卸载SQL
        // \$sql = __DIR__ . '/data/uninstall.sql';
        // MysqlProvider::importSql(\$sql);
    }

    /**
     * 卸载菜单数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function delMenu()
    {
        // 获取菜单数据
        \$menus = config('plugin.goods.menus', []);
        if (\$menus) {
            // 执行倒序
            \$menus = array_reverse(\$menus);
            // 获取菜单KEY
            \$keys = array_column(\$menus, 'path');
            // 删除菜单
            MenuProvider::delMenus(\$keys);
        }
    }

    /**
     * 卸载后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallAfter()
    {
    }
}
EOF;

        file_put_contents("$basePath/Install.php", $content);

    }

    /**
     * @return void
     */
    protected function createInstallSqlFile($file)
    {
        file_put_contents($file, '');
    }

    /**
     * @param $base
     * @param $name
     * @return void
     */
    protected function createConfigFiles($base, $name)
    {
        // app.php
        $content = <<<EOF
<?php

use support\\Request;

return [
    'debug' => true,
    'controller_suffix' => 'Controller',
    'controller_reuse' => false,
];

EOF;
        file_put_contents("$base/app.php", $content);

        // autoload.php
        $content = <<<EOF
<?php
return [
    'files' => [
        base_path() . '/plugin/$name/app/functions.php',
    ]
];
EOF;
        file_put_contents("$base/autoload.php", $content);

        // container.php
        $content = <<<EOF
<?php
return new Webman\\Container;

EOF;
        file_put_contents("$base/container.php", $content);

        // exception.php
        $content = <<<EOF
<?php

return [
    '' => \\app\common\\exception\\Handler::class,
];

EOF;
        file_put_contents("$base/exception.php", $content);

        // log.php
        $content = <<<EOF
<?php

return [
    'default' => [
        'handlers' => [
            [
                'class' => Monolog\\Handler\\RotatingFileHandler::class,
                'constructor' => [
                    runtime_path() . '/logs/$name.log',
                    7,
                    Monolog\\Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Monolog\\Formatter\\LineFormatter::class,
                    'constructor' => [null, 'Y-m-d H:i:s', true],
                ],
            ]
        ],
    ],
];

EOF;
        file_put_contents("$base/log.php", $content);

        // middleware.php
        $content = <<<EOF
<?php
use app\common\providers\MiddlewareProvider;

return MiddlewareProvider::init('{$name}');

EOF;
        file_put_contents("$base/middleware.php", $content);

        // process.php
        $content = <<<EOF
<?php
return [];

EOF;
        file_put_contents("$base/process.php", $content);

        // redis.php
        $content = <<<EOF
<?php
return [
    'default' => [
        'host' => xbEnv('REDIS.HOST', '127.0.0.1'),
        'password' => xbEnv('REDIS.PASSWD', null),
        'port' => xbEnv('REDIS.PORT', 6379),
        'database' => xbEnv('REDIS.DATABASE', 0),
    ],
];       

EOF;
        file_put_contents("$base/redis.php", $content);

        // route.php
        $content = <<<EOF
<?php
use Webman\Route;
use app\common\providers\RouteProvider;

RouteProvider::regPluginRoute('{$name}');

EOF;
        file_put_contents("$base/route.php", $content);

        // static.php
        $content = <<<EOF
<?php

return [
    'enable' => true,
    'middleware' => [],    // Static file Middleware
];

EOF;
        file_put_contents("$base/static.php", $content);

        // translation.php
        $content = <<<EOF
<?php

return [
    // Default language
    'locale' => 'zh_CN',
    // Fallback language
    'fallback_locale' => ['zh_CN', 'en'],
    // Folder where language files are stored
    'path' => base_path() . "/plugin/$name/resource/translations",
];

EOF;
        file_put_contents("$base/translation.php", $content);

        // view.php
        $content = <<<EOF
<?php

use support\\view\\Raw;
use support\\view\\Twig;
use support\\view\\Blade;
use support\\view\\ThinkPHP;

return [
    'handler' => Raw::class
];

EOF;
        file_put_contents("$base/view.php", $content);

        // thinkorm.php
        $content = <<<EOF
<?php

return [];

EOF;
        file_put_contents("$base/thinkorm.php", $content);

    }

}
