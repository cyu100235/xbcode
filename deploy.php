<?php
namespace Deployer;

require 'recipe/common.php';

/*
|--------------------------------------------------------------------------
| 日常部署命令速查（在项目根目录执行）
|--------------------------------------------------------------------------
|
| 0) 先确认配置和连通性
|    vendor/bin/dep config prod
|    vendor/bin/dep ssh prod
|
| 1) 查看可用命令/任务树
|    vendor/bin/dep list
|    vendor/bin/dep tree deploy
|
| 2) 首次部署（目标主机：prod）
|    vendor/bin/dep deploy:setup prod
|    vendor/bin/dep deploy prod
|    vendor/bin/dep webman:start prod
|
| 3) 常规发布
|    vendor/bin/dep deploy prod
|
| 4) 发布前检查（可选）
|    vendor/bin/dep deploy:info prod
|    vendor/bin/dep deploy:check_remote prod
|
| 5) 查看发布记录
|    vendor/bin/dep releases prod
|
| 6) 回滚到上一版本
|    vendor/bin/dep rollback prod
|
| 7) 回滚到指定版本（先看 releases，再指定 revision）
|    vendor/bin/dep releases prod
|    vendor/bin/dep rollback prod --revision=12
|
| 8) 仅预览执行计划（不真正执行）
|    vendor/bin/dep deploy prod --plan
|    vendor/bin/dep rollback prod --revision=12 --plan
|
| 9) 异常中断后手动解锁
|    vendor/bin/dep deploy:unlock prod
|
| 10) Webman 运维命令
|    vendor/bin/dep webman:status prod
|    vendor/bin/dep webman:restart prod
|
| 11) 远程执行临时命令（排障）
|    vendor/bin/dep run prod 'cd {{current_path}} && php -v'
|
| 12) 按需指定分支/标签发布
|    vendor/bin/dep deploy prod --branch=main
|    vendor/bin/dep deploy prod --tag=v1.2.3
|
| 13) 控制并行度/记录日志（多主机时更常用）
|    vendor/bin/dep deploy prod --limit=1
|    vendor/bin/dep deploy prod --log=deploy.log
|
| 14) 显示更详细输出（排障常用）
|    vendor/bin/dep deploy prod -vvv
|
*/

// 项目名
set('application', 'xbcode');

// Git 仓库（请改成你的真实仓库地址）
set('repository', 'https://gitee.com/xbcode_net/xbcode.git');

// 默认分支
set('branch', 'main');

// 部署目录（服务器上的根目录）
set('deploy_path', '/www/wwwroot/demo.xbcode.net');

// 每次部署后保留最近发布版本数
set('keep_releases', 5);

// 启用 SSH 复用，提升部署速度
set('ssh_multiplexing', true);

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

// PHP 可执行文件
set('bin/php', '/www/server/php/81/bin/php');

// Composer 参数（生产环境）
set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');

// 主机配置（请按实际服务器修改）
host('prod')
    ->setHostname('111.230.55.84')
    ->setRemoteUser('root')
    ->setPort(22)
    // 指定私钥，避免无交互部署时走密码认证失败
    ->setIdentityFile('~/.ssh/id_rsa')
    // 开启 Agent 转发，远端拉取私有仓库时复用本机密钥
    ->setForwardAgent(true)
    // 首次连接自动接受主机指纹，避免阻塞
    ->setSshArguments([
        '-o StrictHostKeyChecking=accept-new',
    ])
    ->set('branch', 'main')
    ->set('http_user', 'www');

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
    run('git clone --recursive -b {{branch}} {{repository}} {{deploy_path}}/release 2>&1');
    run("cd {{deploy_path}}/release && git rev-parse HEAD > REVISION");
});

// 初始化并更新 Git 子模块（首次部署及后续更新都可复用）
task('deploy:submodule', function () {
    run('cd {{release_path}} && git submodule sync --recursive');
    run('cd {{release_path}} && git submodule update --init --recursive');
});

// 首次部署后可手动执行：dep webman:start prod
task('webman:start', function () {
    run('cd {{current_path}} && {{bin/php}} webman start -d');
});

// 常规发布结束后重启 webman
task('webman:restart', function () {
    run('cd {{current_path}} && {{bin/php}} webman restart -d || {{bin/php}} webman start -d');
});

// 查看进程状态（排障时很有用）
task('webman:status', function () {
    run('cd {{current_path}} && {{bin/php}} webman status');
});

// 失败自动解锁
after('deploy:failed', 'deploy:unlock');

// 代码拉取后更新子模块
after('deploy:update_code', 'deploy:submodule');

// 部署成功后重启
after('deploy:success', 'webman:restart');