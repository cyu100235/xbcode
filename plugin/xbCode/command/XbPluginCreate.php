<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command;

use Exception;
use plugin\xbCode\api\PluginPreview;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * 插件创建
 * 基于 xbCode/data/plugin-skeleton 模板目录生成插件骨架：
 *   - 批量 mkdir 创建目录（带每个目录 remarks.txt 说明）
 *   - 批量复制 .tpl 模板文件，替换 {PLUGIN_*} 变量后写入目标路径
 *   - 生成 preview.svg（内置 PluginPreview）和根 remarks.txt
 *
 * 用法示例：
 *   # 交互式创建
 *   php webman xb:plugin:create
 *
 *   # 通过参数快速创建
 *   php webman xb:plugin:create --title=示例插件 --name=xbDemo --author=积木云 --desc=示例描述 --server-address=127.0.0.1 --server-port=39000
 *
 * 参数说明：
 *   --title          插件标题（2-20 字）
 *   --name           插件标识（字母+数字，字母开头，2-20 字）
 *   --author         开发者名称（2-10 字）
 *   --desc           一句话描述（3-35 字）
 *   --server-address 服务地址（IP / 域名 / 完整 URL，默认 127.0.0.1）
 *   --server-port    服务端口（1-65535，缺省时取地址内显式端口，无则 39000）
 *
 * 服务地址与服务端口在描述之后采集，合并成完整地址写入插件 config/micro.php。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
#[AsCommand('xb:plugin:create', '快速创建xbCode插件基础开发骨架')]
class XbPluginCreate extends Command
{
    /**
     * 模板根目录（相对于插件根）
     */
    protected const SKELETON_DIR = '/plugin/xbCode/data/plugin-skeleton';

    /**
     * 服务地址默认值（交互式回车即采用）
     */
    protected const DEFAULT_SERVER_ADDRESS = '127.0.0.1';

    /**
     * 服务端口默认值（地址内未显式带端口时使用）
     */
    protected const DEFAULT_SERVER_PORT = 39000;

    /**
     * 文件名后缀到模板后缀的映射：createFile 时把后缀去掉，再拼接 .tpl 定位模板。
     * 对齐 xbDeveloper 原始后缀列表。
     * 注意：.blade.php 必须排在 .php 之前，否则 str_replace 会先把 .blade.php 的 .php 部分替换掉。
     * @var array<int, string>
     */
    protected static array $suffixMap = [
        '.blade.php', '.jpg', '.png', '.svg', '.php', '.json', '.sql', '.vue', '.html', '.md', '.js', '.css',
    ];

    /**
     * 插件目标文件列表（相对路径）。
     * 由 xbDeveloper 原始模板演进：删除 admin/home/queue，对齐官方骨架新增 controller/view，
     * plugins.json 改为 plugin.json（项目规范）。
     * @var array<int, string>
     */
    protected static array $files = [
        'api/Install.php',
        'app/functions.php',
        'app/controller/BaseController.php',
        'app/controller/IndexController.php',
        'app/view/index/index.blade.php',
        'config/app.php',
        'config/autoload.php',
        'config/container.php',
        'config/exception.php',
        'config/log.php',
        'config/middleware.php',
        'config/process.php',
        'config/route.php',
        'config/static.php',
        'config/translation.php',
        'config/view.php',
        'config/menu.php',
        'config/micro.php',
        'setting/basis.php',
        'plugin.json',
        'install.sql',
        'README.md',
    ];

    /**
     * 目录说明字典
     * 覆盖本自定义模板骨架实际生成的全部目录。
     * 键为相对于插件根的路径，值为目录用途说明。
     * @var array<string, string>
     */
    protected static array $dirRemarks = [
        'api'                     => '核心 API 与基础工具类目录，主要与其他插件对接，非网络请求接口',
        'app'                     => '应用主目录，承载控制器、模型、视图、命令等业务分层',
        'app/controller'          => '控制器目录',
        'app/model'               => '模型目录（继承 xbCode Model，支持 SAAS 隔离）',
        'app/middleware'          => '中间件目录',
        'app/view'                => '视图根目录',
        'app/view/index'          => '默认首页视图目录',
        'config'                  => '配置目录（app.php 必须含 enable=true）',
        'public'                  => '公共静态资源目录',
        'setting'                 => '插件配置模板',
    ];

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure(): void
    {
        $this
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, '插件标题（2-20 字）')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, '插件标识（字母+数字，字母开头，2-20 字）')
            ->addOption('author', null, InputOption::VALUE_OPTIONAL, '开发者名称（2-10 字）')
            ->addOption('desc', null, InputOption::VALUE_OPTIONAL, '一句话描述（3-35 字）')
            ->addOption('server-address', null, InputOption::VALUE_OPTIONAL, '服务地址（IP/域名/完整URL，默认 127.0.0.1）')
            ->addOption('server-port', null, InputOption::VALUE_OPTIONAL, '服务端口（1-65535，默认 39000）');
    }

    /**
     * 执行命令
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $title  = $input->getOption('title');
        $name   = $input->getOption('name');
        $author = $input->getOption('author');
        $desc   = $input->getOption('desc');
        $serverAddress = $input->getOption('server-address');
        $serverPort    = $input->getOption('server-port');
        $quick  = true;

        // 未通过参数传入时交互式询问
        if ($title === null) {
            $title = $helper->ask($input, $output, new Question('插件标题 (2-20字)：'));
            $quick = false;
        }
        if ($name === null) {
            $name = $helper->ask($input, $output, new Question('插件标识 (字母+数字，字母开头)：'));
            $quick = false;
        }
        if ($author === null) {
            $author = $helper->ask($input, $output, new Question('开发者名称 (2-10字)：'));
            $quick = false;
        }
        if ($desc === null) {
            $desc = $helper->ask($input, $output, new Question('一句话描述 (3-35字)：'));
            $quick = false;
        }
        if ($serverAddress === null) {
            $default = self::DEFAULT_SERVER_ADDRESS;
            $serverAddress = $helper->ask($input, $output, new Question("服务地址 (IP/域名/URL) [{$default}]：", $default));
            $quick = false;
        }
        if ($serverPort === null) {
            // 默认端口优先取地址里显式写的端口，没有则用框架默认端口
            $default = (string) ($this->parseServerPort((string) $serverAddress) ?? self::DEFAULT_SERVER_PORT);
            $serverPort = $helper->ask($input, $output, new Question("服务端口 (1-65535) [{$default}]：", $default));
            $quick = false;
        }

        // 数据验证
        try {
            $this->validate(compact('title', 'name', 'author', 'desc', 'serverAddress', 'serverPort'));
            $serverUrl = $this->buildServerAddress((string) $serverAddress, (string) $serverPort);
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return self::FAILURE;
        }

        // 交互模式确认
        if (!$quick) {
            $output->writeln('');
            $output->writeln('<info>----------插件信息----------</info>');
            $output->writeln("<info>插件标题：{$title}</info>");
            $output->writeln("<info>插件标识：{$name}</info>");
            $output->writeln("<info>开发者名称：{$author}</info>");
            $output->writeln("<info>一句话描述：{$desc}</info>");
            $output->writeln("<info>服务地址：{$serverUrl}</info>");
            $output->writeln('<info>----------插件信息----------</info>');
            $output->writeln('');
            $confirm = new ConfirmationQuestion('直接按【Enter】确认创建，输入 n 后确认取消：', true);
            if (!$helper->ask($input, $output, $confirm)) {
                return self::SUCCESS;
            }
            $output->writeln('');
        }

        // 生成骨架并定制业务文件
        $output->writeln("<info>正在创建 {$name} 插件...</info>");
        try {
            // 校验模板完整性
            $this->validateTemplates();
            // 基于自定义模板生成骨架（目录+文件+变量替换，含 config/micro.php 服务地址）
            $this->copySkeleton($name, $title, $author, $desc, $serverUrl, $output);
            // 预览图 + 根目录 remarks.txt
            $this->customize($name, $title, $author, $desc, $output);
        } catch (\Throwable $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return self::FAILURE;
        }

        $output->writeln("<info>{$name} 插件创建成功...</info>");
        return self::SUCCESS;
    }

    /**
     * 校验模板文件完整性
     * 对每个目标文件，按 $files 声明查找对应 .tpl 是否存在，缺失则抛异常。
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function validateTemplates(): void
    {
        $basePath = base_path() . self::SKELETON_DIR;
        foreach (self::$files as $file) {
            $tplName = str_replace(self::$suffixMap, '.tpl', $file);
            $tplFullPath = "{$basePath}/{$tplName}";
            if (!is_file($tplFullPath)) {
                throw new Exception("模板文件缺失：" . self::SKELETON_DIR . "/{$tplName}");
            }
        }
    }

    /**
     * 基于自定义模板生成骨架
     * @param string $name   插件标识
     * @param string $title  插件标题
     * @param string $author 开发者
     * @param string $desc   描述
     * @param string $serverUrl 合并后的完整服务地址（写入 config/micro.php）
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function copySkeleton(string $name, string $title, string $author, string $desc, string $serverUrl, OutputInterface $output): void
    {
        $pluginPath = base_path() . "/plugin/{$name}";
        $output->writeln('<info>--- 生成自定义骨架 ---</info>');
        // 批量创建目录，并写入每个目录内 remarks.txt
        foreach (self::$dirRemarks as $dir => $remark) {
            $this->makeDir("{$pluginPath}/{$dir}", $remark, $output);
        }
        // 批量创建文件（模板 .tpl → 替换变量 → 写入目标路径）
        $vars = [
            '{PLUGIN_TITLE}'  => $title,
            '{PLUGIN_NAME}'   => $name,
            '{PLUGIN_DESC}'   => $desc,
            '{PLUGIN_AUTHOR}' => $author,
            '{PLUGIN_SERVER_ADDRESS}' => $serverUrl,
        ];
        foreach (self::$files as $file) {
            $this->createFile($file, $name, $vars, $output);
        }
    }

    /**
     * 创建目录并写入目录内 remarks.txt（说明文件）
     * 目录已存在时跳过写入说明，不抛异常。
     * @param string            $dirPath 目标目录绝对路径
     * @param string            $remark  目录用途说明
     * @param OutputInterface   $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function makeDir(string $dirPath, string $remark, OutputInterface $output): void
    {
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
            $output->writeln("<info>  创建目录：{$dirPath}</info>");
        }
        $remarkFile = "{$dirPath}/remarks.txt";
        if (is_file($remarkFile)) {
            return;
        }
        file_put_contents($remarkFile, $remark . "\r\n");
    }

    /**
     * 从模板 .tpl 生成目标文件并替换变量
     * 目标文件已存在时跳过。
     * @param string            $file   相对插件根的目标路径，如 "config/app.php"
     * @param string            $name   插件标识（仅用于路径拼接）
     * @param array<string,string> $vars 变量映射：{占位符} => 真实值
     * @param OutputInterface   $output
     * @throws Exception 模板文件不存在时
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createFile(string $file, string $name, array $vars, OutputInterface $output): void
    {
        $targetPath = base_path() . "/plugin/{$name}/{$file}";
        if (is_file($targetPath)) {
            $output->writeln("<comment>  跳过（已存在）：{$file}</comment>");
            return;
        }
        $tplName    = str_replace(self::$suffixMap, '.tpl', $file);
        $tplFullPath = base_path() . self::SKELETON_DIR . "/{$tplName}";
        if (!is_file($tplFullPath)) {
            throw new Exception("模板文件不存在：" . self::SKELETON_DIR . "/{$tplName}");
        }
        $content = (string)file_get_contents($tplFullPath);
        $content = str_replace(array_keys($vars), array_values($vars), $content);
        file_put_contents($targetPath, $content);
        $output->writeln("<info>  创建文件：{$file}</info>");
    }

    /**
     * 数据验证
     * @param array $data 插件数据（title/name/author/desc/serverAddress/serverPort）
     * @return void
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function validate(array $data): void
    {
        if (empty($data['title'])) {
            throw new Exception('请填写插件标题');
        }
        if (empty($data['name'])) {
            throw new Exception('请填写插件标识');
        }
        if (strpos($data['name'], '/') !== false) {
            throw new Exception('插件标识名称错误，名称不能包含字符 /');
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $data['name'])) {
            throw new Exception('插件标识只能是字母+数字，不能包含特殊字符');
        }
        if (!preg_match('/^[a-zA-Z]/', $data['name'])) {
            throw new Exception('插件标识必须字母开头');
        }
        if (empty($data['author'])) {
            throw new Exception('请填写开发者名称');
        }
        if (empty($data['desc'])) {
            throw new Exception('请填写插件描述');
        }
        $titleCount  = mb_strlen($data['title']);
        $nameCount   = mb_strlen($data['name']);
        $descCount   = mb_strlen($data['desc']);
        $authorCount = mb_strlen($data['author']);
        if ($titleCount < 2 || $titleCount > 20) {
            throw new Exception('插件标题长度为2-20个字');
        }
        if ($nameCount <= 1 || $nameCount > 20) {
            throw new Exception('插件标识长度为2-20个字');
        }
        if ($descCount < 3 || $descCount > 35) {
            throw new Exception('插件描述长度为3-35个字');
        }
        if ($authorCount < 2 || $authorCount > 10) {
            throw new Exception('开发者名称长度为2-10个字符');
        }
        if (trim((string) $data['serverAddress']) === '') {
            throw new Exception('请填写服务地址');
        }
        if (!preg_match('#^(https?://)?[A-Za-z0-9\-.]+(:\d+)?(/\S*)?$#', $data['serverAddress'])) {
            throw new Exception('服务地址格式错误，示例：127.0.0.1 或 http://xbcode.net:39000');
        }
        $serverPort = trim((string) $data['serverPort']);
        if (!ctype_digit($serverPort) || (int) $serverPort < 1 || (int) $serverPort > 65535) {
            throw new Exception('服务端口必须是 1-65535 之间的数字');
        }
        if (is_dir(base_path() . "/plugin/{$data['name']}")) {
            throw new Exception("{$data['name']} 插件已存在");
        }
    }

    /**
     * 取服务地址里的 host[:port] 部分（去掉协议前缀与路径）
     * @param string $address 用户输入的服务地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function serverHost(string $address): string
    {
        $withoutScheme = (string) preg_replace('#^[a-z]+://#i', '', trim($address));
        return (string) preg_replace('#[/?].*$#', '', $withoutScheme);
    }

    /**
     * 解析服务地址里显式填写的端口
     * @param string $address 用户输入的服务地址（IP/域名/完整URL）
     * @return int|null 地址未带端口时返回 null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function parseServerPort(string $address): ?int
    {
        if (!preg_match('#:(\d+)$#', $this->serverHost($address), $matches)) {
            return null;
        }
        return (int) $matches[1];
    }

    /**
     * 合并服务地址与服务端口，得到写入 config/micro.php 的完整地址
     * 规则：保留显式填写的协议（http/https，缺省 http），去掉路径与地址内的端口，
     *       端口统一以单独采集到的服务端口为准。
     * @param string $address 服务地址
     * @param string $port    服务端口
     * @return string 形如 http://127.0.0.1:39000
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildServerAddress(string $address, string $port): string
    {
        $scheme = 'http';
        if (preg_match('#^(https?)://#i', trim($address), $matches) === 1) {
            $scheme = strtolower($matches[1]);
        }
        $host = (string) preg_replace('#:\d+$#', '', $this->serverHost($address));
        return "{$scheme}://{$host}:{$port}";
    }

    /**
     * 在自定义骨架基础上补充生成：预览图 + 根目录 remarks.txt
     * （plugin.json、config/app.php、README.md 等已在 copySkeleton 阶段由模板生成）
     * @param string $name 插件标识
     * @param string $title 插件标题
     * @param string $author 开发者
     * @param string $desc 描述
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function customize(string $name, string $title, string $author, string $desc, OutputInterface $output): void
    {
        $pluginPath = base_path() . "/plugin/{$name}";
        $output->writeln('<info>--- 补充定制 ---</info>');
        $this->createPreview($name, $title, $desc, $output);
        $this->createRemarks($pluginPath, $output);
    }

    /**
     * 生成预览图 preview.svg
     * 使用 xbCode 内置的 PluginPreview 合成图标与模板。
     * @param string $name 插件标识
     * @param string $title 标题
     * @param string $desc 描述
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createPreview(string $name, string $title, string $desc, OutputInterface $output): void
    {
        try {
            PluginPreview::make()->create(['name' => $name, 'title' => $title, 'desc' => $desc]);
            $output->writeln('<info>  生成预览图 preview.svg</info>');
        } catch (\Throwable $e) {
            $output->writeln("<comment>  预览图生成失败：{$e->getMessage()}</comment>");
        }
    }

    /**
     * 生成插件目录说明文件 remarks.txt
     * 汇总输出 plugin.json 说明及全部目录用途说明，格式与项目中 xbCrontab 保持一致。
     * @param string $pluginPath 插件根路径
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createRemarks(string $pluginPath, OutputInterface $output): void
    {
        $file = "{$pluginPath}/remarks.txt";
        if (file_exists($file)) {
            $output->writeln('<comment>  remarks.txt 已存在，跳过</comment>');
            return;
        }
        $content  = "Create by 积木云\r\n\r\n";
        $content .= "plugin.json为插件信息文件\r\n\r\n";
        $content .= "插件目录说明\r\n";
        foreach (self::$dirRemarks as $dir => $remark) {
            $remark = str_replace(["\r\n", "\n"], "，", $remark);
            $content .= " - {$dir} ------ {$remark}\r\n";
        }
        file_put_contents($file, $content);
        $output->writeln('<info>  创建目录说明 remarks.txt</info>');
    }
}
