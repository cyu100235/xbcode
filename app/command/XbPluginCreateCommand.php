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
        if (is_dir($plugin_config_path = base_path()."/plugin/$name")) {
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
        $this->mkdir("$base_path/plugin/$name/app/model", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/middleware", 0777, true);
        $this->mkdir("$base_path/plugin/$name/app/view/index", 0777, true);
        $this->mkdir("$base_path/plugin/$name/config", 0777, true);
        $this->mkdir("$base_path/plugin/$name/data/sql", 0777, true);
        $this->mkdir("$base_path/plugin/$name/public", 0777, true);
        $this->mkdir("$base_path/plugin/$name/api", 0777, true);
        $this->createFunctionsFile("$base_path/plugin/$name/app/functions.php");
        $this->createControllerFile("$base_path/plugin/$name/app/controller/IndexController.php", $name);
        $this->createViewFile("$base_path/plugin/$name/app/view/index/index.html");
        $this->createConfigFiles("$base_path/plugin/$name/config", $name);
        $this->createApiFiles("$base_path/plugin/$name", $name);
        $this->createInfoFiles("$base_path/plugin/$name", $name);
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

use support\\Request;

class IndexController
{
    public function index(Request \$request)
    {
        return view('index/index', ['name' => 'user']);
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
            'title'     => '基础插件系统',
            'name'      => $name,
            'version'   => '1.0.0',
            'depend'    => [],
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
        p('安装前');
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
        \$data = [];
        // MenuProvider::createMenu('',\$data);
        // 导入SQL
        \$sql = __DIR__ . '/data/install.sql';
        p('正在安装');
    }

    /**
     * 安装后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installAfter()
    {
        p('安装后');
    }

    /**
     * 更新前
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateBefore()
    {
        // 导入SQL
        \$sql = __DIR__ . '/data/update.sql';
        p('更新之前');
    }

    /**
     * 更新
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update()
    {
        p('正在更新');
    }

    /**
     * 更新后
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateAfter()
    {
        p('更新之后');
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
        // 删除菜单
        MenuProvider::delMenu('');
        // 导入SQL
        \$sql = __DIR__ . '/data/uninstall.sql';
        p('正在卸载');
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

    /**
     * 设置插件配置（可不提供）
     * @return array[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config()
    {
        return [
            [
                'field' => 'baseic',
                'title' => '基本配置',
                'children' => [
                    [
                        'field' => 'baseic.web_name',
                        'title' => '网站名称',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                            'prompt' => '应用名称，显示在浏览器标签页',
                        ],
                    ],
                    [
                        'field' => 'baseic.web_url',
                        'title' => '网站域名',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                            'prompt' => '网站链接，以斜杠结尾，如：https://xiaobai.host/',
                        ],
                    ],
                    [
                        'field' => 'baseic.web_title',
                        'title' => '网站标题',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                        ],
                    ],
                    [
                        'field' => 'baseic.web_keywords',
                        'title' => '网站关键字',
                        'value' => '',
                        'component' => 'input',
                        'extra' => [
                            'col' => 12,
                        ],
                    ],
                    [
                        'field' => 'baseic.web_description',
                        'title' => '网站描述',
                        'value' => '',
                        'component' => 'textarea',
                        'extra' => [
                            'rows' => 4,
                            'resize' => 'none',
                            'prompt' => '请勿手动换行，字数在100字以内',
                        ],
                    ],
                    [
                        'field' => 'baseic.web_logo',
                        'title' => '系统图标',
                        'value' => '',
                        'component' => 'uploadify',
                        'extra' => [],
                    ],
                ],
            ],
        ];
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


        // database.php
        $content = <<<EOF
<?php
return  [];

EOF;
        file_put_contents("$base/database.php", $content);

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

return [
    '' => [
        
    ]
];

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

use Webman\\Route;


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
