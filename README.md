<center>
基于 <b>渐进式动态页面构建渲染研发框架</b> 快速创建属于你自己的系统
</center>

### 🌈 框架介绍

xbCode 基于渐进式动态页面构建渲染框架，支持在线安装插件、卸载的集成化插件框架，提供丰富的扩展功能。为开发者赋能。

### ☘ 交流群

为了让 xbCode 框架能快速成长，欢迎加入开发者交流群，群内除技术相关话题以外禁止任何广告行为，在你加入群聊之前请先考虑清楚

QQ 群：592808248

### 🌈开源地址

Gitee开源地址：https://gitee.com/xbcode_net/xbcode

github开源地址：https://github.com/cyu100235/xbcode

```
下载源代码时，切记必须使用以下命令，否则克隆出来的插件代码会是空的
git clone --recurse-submodules https://gitee.com/xbcode_net/xbcode.git
```

### ☘ 功能特性

- [√] 基于 Webman 应用插件化系统
- [√] 插件在线安装和卸载
- [√] 自定义远程组件（定制化页面）
- [√] 基于 JSON 动态构建（动态表格、动态表单、定制化页面 Vue 远程组件）
- [√] 低耦合的插件化系统
- [√] 全局附件库
- [√] 小程序、网页、APP 等
- [√] 表格 Excel 导出
- [√] 自定义任务队列
- [√] 部门权限管理（支持数据权限）
- [×] 低代码可视化UI界面
- [×] 一键 CURD 代码
- [×] 更多功能正在加紧中...

### 🍚 系统亮点

```
· 基于动态渲染化构建技术，因此并无前端工程代码，用户端可选择任何前端技术。
· 如无特殊页面，无需编写一行前端代码，即可完成插件开发，助你开发效率提升10倍不止。
```

### 🍎 运行环境

```
Nignx（必须）
PHP 8.1（必须）
MySQL >= 5.7或8.0
Redis
```

> ***温馨提示：***虚拟空间不支持，推荐使用 bt 宝塔面板，服务器推荐阿里云 ecs 或腾讯云 cvm 云服务器

### 🔨 宝塔安装（推荐使用）

```
1、获取源代码

方式二：
使用命令下载：git clone --recurse-submodules https://gitee.com/xbcode_net/xbcode.git

方式一：
打开《积木云官网》下载源代码

2、宝塔-网站-PHP项目-添加站点-选择异步项目-填写站点信息-确定
填写域名，示例：demo.xbcode.net（您的域名）
设置PHP版本：8.1
设置运行用户：root
填写启动命令：php webman start

3、选择站点目录所有文件-右键-选择批量修改权限-所有者www-设置为755

4、点击选择刚才创建的站点-服务管理-运行目录-选择public

5、点击选择刚才创建的站点-配置文件-伪静态配置文件-填写（路径设置为您自己的站点目录）
include /www/wwwroot/demo.xbcode.net/nginx.conf;

6、访问刚才填写的域名进行安装，示例：demo.xbcode.net
```

### 官方导航

[积木云官网](http://xbcode.net/) |
[在线体验](http://demo.xbcode.net/admin/) |
[开发文档](http://doc.xbcode.net/) |
[插件市场](http://xbcode.net/plugins)

### ⚡ 系统演示

![登录界面](/app/xbCode/preview/1.png)

![工作台界面](/app/xbCode/preview/2.png)

![插件界面](/app/xbCode/preview/3.png)

![管理员账号](/app/xbCode/preview/4.png)

![系统设置](/app/xbCode/preview/5.png)

<b style="color:red;">当前域名正在备案中，请稍后访问。</b>
系统演示： http://demo.xbcode.net/admin 账号：admin 密码：123456

---

### 📸 特别鸣谢

排名不分先后，感谢这些软件的开发者：webman、vue、mysql、redis、uniapp、amis、echarts、swiper、element-ui 等，如有遗漏请联系我！

---

### 🎬 开发团队

产品：楚羽幽

文档：楚羽幽

---

### 📺 使用须知

1、允许用于个人学习、毕业设计、教学案例、公益事业、商业使用；

2、如果商用必须保留版权信息，请自觉遵守，如需去除版权需购买商业授权；

3、禁止将本项目的代码和资源进行任何形式的出售，产生的一切任何后果责任由侵权者自负。

---

### 💾 版权信息

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有 Copyright © 2018-2025 by 积木云 (http://www.xbcode.net)

All rights reserved。

xbcode 商标和著作权所有者为贵州积木云网络科技有限公司。

感谢每一位贡献代码的朋友。

如果对您有帮助，您可以点右上角 💘Star💘 支持
