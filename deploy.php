<?php
namespace Deployer;

require 'recipe/common.php';

/*
|--------------------------------------------------------------------------
| 部署环境说明（在项目根目录执行）
|--------------------------------------------------------------------------
|
|   test —— 测试环境
|   prod —— 生产环境
|
| 常用命令（末尾的 <环境> 换成 test 或 prod）：
|
| 0) 先确认配置和连通性
|    php vendor/bin/dep config <环境>
|    php vendor/bin/dep ssh <环境>
|
| 1) 查看可用命令/任务树
|    php vendor/bin/dep list
|    php vendor/bin/dep tree deploy
|
| 2) 首次部署
|    php vendor/bin/dep deploy:setup <环境>
|    php vendor/bin/dep deploy <环境>
|    php vendor/bin/dep webman:start <环境>
|
| 3) 常规发布
|    php vendor/bin/dep deploy test
|    php vendor/bin/dep deploy prod
|
| 4) 发布前检查（可选）
|    php vendor/bin/dep deploy:info <环境>
|    php vendor/bin/dep deploy:check_remote <环境>
|
| 5) 查看发布记录
|    php vendor/bin/dep releases <环境>
|
| 6) 回滚到上一版本
|    php vendor/bin/dep rollback <环境>
|
| 7) 回滚到指定版本（先看 releases，再指定 revision）
|    php vendor/bin/dep releases <环境>
|    php vendor/bin/dep rollback <环境> --revision=12
|
| 8) 仅预览执行计划（不真正执行）
|    php vendor/bin/dep deploy <环境> --plan
|    php vendor/bin/dep rollback <环境> --revision=12 --plan
|
| 9) 异常中断后手动解锁
|    php vendor/bin/dep deploy:unlock <环境>
|
| 10) Webman 运维命令
|    php vendor/bin/dep webman:status <环境>
|    php vendor/bin/dep webman:restart <环境>
|
| 11) 远程执行临时命令（排障）
|    php vendor/bin/dep run <环境> 'cd {{current_path}} && php -v'
|
| 12) 按需指定分支/标签发布
|    php vendor/bin/dep deploy <环境> --branch=main
|    php vendor/bin/dep deploy <环境> --tag=v1.2.3
|
| 13) 控制并行度/记录日志（多主机时更常用）
|    php vendor/bin/dep deploy <环境> --limit=1
|    php vendor/bin/dep deploy <环境> --log=deploy.log
|
| 14) 显示更详细输出（排障常用）
|    php vendor/bin/dep deploy <环境> -vvv
|
*/

// TODO 以下为默认配置，请根据实际情况进行修改，设置TODO都需要修改

// TODO 项目名（请修改成你的项目名称）
set('application', 'xbcode');

// TODO Git仓库（请改成你的真实仓库地址）
set('repository', 'git@gitee.com:xbcode_net/xbcode.git');

// 默认分支
set('branch', 'main');

// 每次部署后保留最近发布版本数
set('keep_releases', 5);

// 启用 SSH 复用，提升部署速度
set('ssh_multiplexing', false);

// 避免部分环境下符号链接相对路径问题
set('use_relative_symlink', false);

// 保留共享文件（发布时不会被覆盖）
set('shared_files', [
    // 环境变量
    '.env',
    // Nginx 配置
    'nginx.conf',
]);

// 保留共享目录
set('shared_dirs', [
    // 缓存目录
    'runtime',
    // 附件目录
    'public/attachment'
]);

// 可写目录
set('writable_dirs', ['runtime']);
set('writable_mode', 'chmod');
set('writable_chmod_mode', '775');

// PHP 可执行文件（请修改成你的服务器上 PHP 可执行文件路径）
set('bin/php', '/www/server/php/82/bin/php');

// Composer 参数（生产环境）
set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');

// 远端执行 git 时使用的 Gitee 公钥（注意：这是服务器上的公钥路径）
set('remote_git_identity_file', '~/.ssh/id_rsa');
set('remote_git_ssh_command', 'ssh -i {{remote_git_identity_file}} -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new');

// 测试环境（请按实际服务器修改）
host('test')
    // TODO 服务器地址
    ->setHostname('127.0.0.1')
    // TODO 远程用户名
    ->setRemoteUser('root')
    // TODO 远程端口
    ->setPort(22)
    // 指定连接服务器使用的本地私钥，避免无交互部署时走密码认证失败
    ->setIdentityFile('~/.ssh/id_rsa')
    // 开启 Agent 转发，远端拉取私有仓库时复用本机密钥
    ->setForwardAgent(true)
    // 首次连接自动接受主机指纹，避免阻塞
    ->setSshArguments([
        '-o StrictHostKeyChecking=accept-new',
    ])
    ->set('branch', 'main')
    ->set('http_user', 'www')
    // TODO 部署目录（请修改成测试环境服务器上根目录）
    ->set('deploy_path', '/www/wwwroot/test.example.com');

// 生产环境（请按实际服务器修改）
host('prod')
    // TODO 服务器地址
    ->setHostname('127.0.0.1')
    // TODO 远程用户名
    ->setRemoteUser('root')
    // TODO 远程端口
    ->setPort(22)
    // 指定连接服务器使用的本地私钥，避免无交互部署时走密码认证失败
    ->setIdentityFile('~/.ssh/id_rsa')
    // 开启 Agent 转发，远端拉取私有仓库时复用本机密钥
    ->setForwardAgent(true)
    // 首次连接自动接受主机指纹，避免阻塞
    ->setSshArguments([
        '-o StrictHostKeyChecking=accept-new',
    ])
    ->set('branch', 'main')
    ->set('http_user', 'www')
    // TODO 部署目录（请修改成生产环境服务器上根目录）
    ->set('deploy_path', '/www/wwwroot/prod.example.com');

// 覆盖默认 deploy:release，规避当前环境下 release 阶段异常退出（exit 139）问题
task('deploy:release', function () {
    cd('{{deploy_path}}');

    // 清理残留 release 软链
    if (test('[ -h release ]')) {
        run('rm release');
    }

    $latest = run('cat .dep/latest_release || echo 0');
    $releaseName = strval(intval($latest) + 1);
    $releasePath = "releases/$releaseName";

    if (test("[ -d $releasePath ]")) {
        throw new \Deployer\Exception\Exception("Release name \"$releaseName\" already exists.");
    }

    run("echo $releaseName > .dep/latest_release");
    run("mkdir -p $releasePath");
    run("{{bin/symlink}} $releasePath {{deploy_path}}/release");
});

// 覆盖默认 deploy:update_code，规避当前环境下内置任务异常退出（exit 139）问题
task('deploy:update_code', function () {
    // 直接克隆到 release_path，确保后续子模块任务可用（需要 .git 目录）
    run('GIT_SSH_COMMAND="{{remote_git_ssh_command}}" git clone --recursive -b {{branch}} {{repository}} {{deploy_path}}/release 2>&1');
    run("cd {{deploy_path}}/release && git rev-parse HEAD > REVISION");
});

// 初始化并更新 Git 子模块（首次部署及后续更新都可复用）
task('deploy:submodule', function () {
    run('cd {{release_path}} && git submodule sync --recursive');
    run('cd {{release_path}} && GIT_SSH_COMMAND="{{remote_git_ssh_command}}" git submodule update --init --recursive');
});

// 首次部署后可手动执行：dep webman:start <环境>
task('webman:start', function () {
    // 先停止 webman
    run('cd {{current_path}} && {{bin/php}} webman stop');
    // 再启动 webman
    run('cd {{current_path}} && {{bin/php}} webman start -d');
});

// 常规发布结束后重启 webman
task('webman:restart', function () {
    // 先停止 webman
    run('cd {{current_path}} && {{bin/php}} webman stop');
    // 再启动 webman
    run('cd {{current_path}} && {{bin/php}} webman start -d');
});

// 查看进程状态（排障时很有用）
task('webman:status', function () {
    run('cd {{current_path}} && {{bin/php}} webman status');
});

// 首次部署时用插件内置的 nginx.conf 初始化根目录伪静态（仅首次，后续不覆盖）
// 说明：nginx.conf 属于 shared_files，会被软链到 shared/nginx.conf 并共享给各版本，
// 根目录的 nginx.conf 即指向该文件。首次写入后不再覆盖，
// 避免覆盖运行时自动调整过的端口等配置。
task('nginx:init', function () {
    $dst = '{{current_path}}/nginx.conf';
    // 已存在且非空说明不是首次部署，保持现状
    if (test("[ -s $dst ]")) {
        return;
    }
    $src = '{{current_path}}/plugin/xbCode/nginx.conf';
    if (!test("[ -f $src ]")) {
        // 注意：中文逗号属于合法的变量名字符，此处必须用 {} 明确变量边界
        warning("未找到 {$src}，跳过 nginx.conf 初始化");
        return;
    }
    run("cp -f $src $dst");
});

// 部署后同步数据库表结构（以插件 install.sql 为准）
task('db:table-sync', function () {
    $env = '{{current_path}}/.env';
    // 首次部署时 shared/.env 可能仅被 touch 为空文件，此时数据库配置为空，
    // 直接同步会报 "Access denied for user ''"，因此先检测 .env 已存在且内容非空。
    if (!test("[ -f $env ]") || !test("[ -s $env ]")) {
        warning("跳过表结构同步，需要执行程序安装后配置数据库连接");
        return;
    }
    run('cd {{current_path}} && {{bin/php}} webman xb-table:sync');
});

// 执行部署后置业务类
task('deploy:hook', function () {
    run('cd {{current_path}} && {{bin/php}} plugin/xbCode/hooks/deploy-hook.php');
});

// 失败自动解锁
after('deploy:failed', 'deploy:unlock');

// 代码拉取后更新子模块
after('deploy:update_code', 'deploy:submodule');

// 部署成功后同步nginx.conf配置文件
after('deploy:success', 'nginx:init');
// 部署成功后同步表结构
after('deploy:success', 'db:table-sync');
// 部署成功后执行部署后置业务
after('deploy:success', 'deploy:hook');
// 部署成功后重启
after('deploy:success', 'webman:restart');
