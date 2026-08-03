# 储存设置 (xbUpload)

#### 介绍
储存设置（xbUpload）是积木云 xbCode 框架下的 **核心存储管理插件**。它为系统内所有涉及文件上传的功能提供统一的基础设施支持，涵盖了从本地存储到云存储的多引擎切换、分片上传大文件处理、以及精细化的文件记录管理。

#### 插件信息
- 插件标识：xbUpload
- 插件名称：储存设置
- 功能描述：提供统一的文件上传、分片上传、存储引擎管理及文件元数据维护能力。
- 当前版本：1.0.0
- 作者：积木云
- 官网：`http://www.xbcode.net`
- 文档：`http://doc.xbcode.net`

#### 功能特性
1. **统一上传体系**
   - 提供标准化的文件上传接口，支持单文件与多文件并发上传。
   - 内置文件类型校验、大小限制等安全过滤机制。
   
2. **大文件分片上传**
   - 支持文件切片（Chunk）上传，有效应对网络波动导致的大文件上传失败问题。
   - 提供自动合并切片、断点续传支持逻辑。

3. **多引擎驱动存储**
   - 默认支持 **本地服务器存储（LocalServer）**。
   - 采用引擎驱动设计模式，可轻松扩展对接阿里云 OSS、腾讯云 COS、七牛云等第三方云存储。
   - 支持在管理后台一键切换当前激活的存储引擎。

4. **可视化文件管理**
   - 提供后台文件库管理功能，支持查看、搜索和删除已上传文件。
   - 实时记录文件元信息：原始文件名、保存路径、文件大小、MIME 类型、存储引擎、MD5 校验值等。

5. **技术实现**
   - 基于 xbCode 动态渲染器（Builder::crud / Builder::form）构建的管理后台。
   - 深度集成 Webman 文件上传系统与 ThinkPHP ORM (think-orm3)。
   - 使用 `plugin\xbUpload\api\UploadApi` 作为全局上传调用入口。

#### 软件架构
- 运行环境：PHP 8.1 + Webman + MySQL 5.7+
- 核心命名空间：`plugin\xbUpload`
- 主要目录结构：
  - `api/`：核心逻辑类（`UploadApi` 处理上传、`UploadChunk` 处理分片、`EngineApi` 引擎调度）。
  - `app/admin/controller/`：管理后台控制器（`UploadController` 文件管理、`EngineController` 引擎设置）。
  - `engine/`：存储驱动实现目录。
  - `app/model/`：数据模型（`Upload` 附件记录、`UploadEngine` 引擎配置）。
  - `enum/`：枚举定义（`UploadExtEnum` 允许后缀、`UseStateEnum` 启用状态）。

#### 安装教程
1. 在 xbCode 后台 **插件市场** 中搜索 “储存设置” 或 “xbUpload”，在线一键安装。
2. 安装完成后，根据提示执行 `install.sql` 初始化文件库及引擎相关数据表。
3. 在后台 **插件管理** 中启用 xbUpload 插件。
4. 前往 **设置 > 储存设置** 配置默认存储引擎及相关参数（如本地存储目录或云存储 Key）。

#### 使用说明
1. **上传文件**
   - 使用接口：`plugin\xbUpload\api\UploadApi::upload($name)`。
   - 示例：`UploadApi::upload('file')` 将按默认引擎上传文件并返回记录信息。

2. **获取文件 URL**
   - 使用 `plugin\xbUpload\api\Files::make()->url($uri)` 自动根据引擎转换文件的完整访问地址。

3. **扩展引擎**
   - 在 `engine/` 目录下继承 `plugin\xbUpload\service\Server` 实现新的存储驱动（如对接第三方云存储）。

#### 注意事项
- 使用本地存储时，请确保 `public/storage` 目录具备读写权限。
- 如需上传超大文件，请同步调整 `php.ini` 中的 `upload_max_filesize` 和 `post_max_size`。
- 建议定期清理文件库中的冗余或过期文件记录，以释放存储空间。
