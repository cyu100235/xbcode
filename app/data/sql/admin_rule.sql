DROP TABLE IF EXISTS `xb_admin_rule`;
CREATE TABLE `xb_admin_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单地址',
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单类@方法名',
  `pid` int(11) NULL DEFAULT NULL COMMENT '父级ID',
  `component` enum('none/index','table/index','form/index','remote/index') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'none/index' COMMENT '组件类型',
  `is_show` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否显示：10否，20是',
  `is_default` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否默认：10否，20是',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  `methods` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求方式',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单图标',
  `params` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单参数',
  `sort` int(11) NULL DEFAULT 100 COMMENT '菜单排序（值越大，越靠后）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 166 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-角色菜单' ROW_FORMAT = DYNAMIC;

INSERT INTO `xb_admin_rule` VALUES (1, '2024-03-27 15:57:10', '2024-05-05 16:27:01', '首页', 'Index/group', '', 0, 'none/index', '20', '20', '20', 'GET', 'HomeFilled', '', 1);
INSERT INTO `xb_admin_rule` VALUES (2, '2024-03-27 15:57:58', '2024-04-30 16:43:12', '权限', 'Auth/group', NULL, 0, 'none/index', '20', '10', '10', 'GET', 'Connection', NULL, 7777);
INSERT INTO `xb_admin_rule` VALUES (3, '2024-03-27 15:58:24', '2024-03-27 16:28:00', '系统', 'System/group', NULL, 0, 'none/index', '20', '10', '10', 'GET', 'Setting', NULL, 9999);
INSERT INTO `xb_admin_rule` VALUES (5, '2024-03-27 16:00:15', '2024-05-05 16:27:19', '工作台', 'Index/index', '', 1, 'remote/index', '20', '20', '20', 'GET', '', 'vue/admin/welcome', 0);
INSERT INTO `xb_admin_rule` VALUES (6, '2024-03-27 16:00:53', '2024-03-27 17:19:43', '系统登录', 'admin/Login/login', '\\app\\admin\\controller\\LoginController@login', 1, 'none/index', '10', '20', '20', 'POST', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (7, '2024-03-27 16:01:30', '2024-05-05 16:24:13', '获取应用信息', 'admin/Index/site', '\\app\\admin\\controller\\IndexController@site', 1, 'none/index', '10', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (8, '2024-03-27 16:02:06', '2024-03-27 17:20:43', '获取管理员信息', 'admin/Login/user', '\\app\\admin\\controller\\LoginController@user', 1, 'none/index', '10', '20', '20', 'GET', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (9, '2024-03-27 16:03:11', '2024-05-05 16:35:07', '退出登录', 'admin/Login/exit', '\\app\\admin\\controller\\LoginController@exit', 1, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (10, '2024-03-27 16:03:38', '2024-05-05 16:35:29', '清除缓存', 'admin/Index/clear', '\\app\\admin\\controller\\IndexController@clear', 1, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (11, '2024-03-27 16:04:09', '2024-05-05 16:35:34', '锁定屏幕', 'admin/Index/lock', '\\app\\admin\\controller\\IndexController@lock', 1, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (12, '2024-03-27 16:04:25', '2024-05-05 16:35:41', '解除锁定', 'admin/Index/unlock', '\\app\\admin\\controller\\IndexController@unlock', 1, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (16, '2024-03-27 16:18:18', '2024-03-27 16:26:10', '账号管理', 'admin/Admin/index', '\\app\\admin\\controller\\AdminController@index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (17, '2024-03-27 16:18:18', '2024-03-27 16:18:18', '账号管理-表格', 'admin/Admin/indexTable', '\\app\\admin\\controller\\AdminController@indexTable', 16, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (18, '2024-03-27 16:18:18', '2024-05-05 16:37:03', '账号管理-添加', 'admin/Admin/add', '\\app\\admin\\controller\\AdminController@add', 16, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (19, '2024-03-27 16:18:18', '2024-05-05 16:37:09', '账号管理-修改', 'admin/Admin/edit', '\\app\\admin\\controller\\AdminController@edit', 16, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (20, '2024-03-27 16:18:18', '2024-05-05 16:37:16', '账号管理-删除', 'admin/Admin/del', '\\app\\admin\\controller\\AdminController@del', 16, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (21, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '角色管理', 'admin/AdminRole/index', '\\app\\admin\\controller\\AdminRoleController@index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (22, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '角色管理-表格', 'admin/AdminRole/indexTable', '\\app\\admin\\controller\\AdminRoleController@indexTable', 21, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (23, '2024-03-27 16:19:10', '2024-05-05 16:37:29', '角色管理-添加', 'admin/AdminRole/add', '\\app\\admin\\controller\\AdminRoleController@add', 21, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (24, '2024-03-27 16:19:10', '2024-05-05 16:37:35', '角色管理-修改', 'admin/AdminRole/edit', '\\app\\admin\\controller\\AdminRoleController@edit', 21, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (25, '2024-03-27 16:19:10', '2024-05-05 16:37:43', '角色管理-删除', 'admin/AdminRole/del', '\\app\\admin\\controller\\AdminRoleController@del', 21, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (26, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理', 'admin/Menus/index', '\\app\\admin\\controller\\MenusController@index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (27, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-表格', 'admin/Menus/indexTable', '\\app\\admin\\controller\\MenusController@indexTable', 26, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (28, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-添加', 'admin/Menus/add', '\\app\\admin\\controller\\MenusController@add', 26, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (29, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-修改', 'admin/Menus/edit', '\\app\\admin\\controller\\MenusController@edit', 26, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (30, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-删除', 'admin/Menus/del', '\\app\\admin\\controller\\MenusController@del', 26, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (31, '2024-03-27 16:20:43', '2024-05-05 16:37:52', '角色管理-授权', 'admin/AdminRole/auth', '\\app\\admin\\controller\\AdminRoleController@auth', 21, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (32, '2024-03-27 16:21:44', '2024-04-19 11:49:44', '账号管理-资料', 'admin/Admin/info', '\\app\\admin\\controller\\AdminController@info', 16, 'form/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (33, '2024-03-27 16:22:57', '2024-05-05 16:38:30', '系统设置', 'admin/Settings/config', '\\app\\admin\\controller\\SettingsController@config', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', 'group=system', 1);
INSERT INTO `xb_admin_rule` VALUES (34, '2024-03-27 16:23:32', '2024-05-05 16:38:34', '上传设置', 'admin/Settings/selected', '\\app\\admin\\controller\\SettingsController@selected', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', 'group=upload', 2);
INSERT INTO `xb_admin_rule` VALUES (35, '2024-03-27 16:44:02', '2024-05-04 01:34:27', '插件', 'admin/Plugins/group', NULL, 0, 'none/index', '20', '10', '10', 'GET', 'ElemeFilled', NULL, 2);
INSERT INTO `xb_admin_rule` VALUES (46, '2024-03-27 16:47:45', '2024-04-11 20:47:57', '插件管理', 'admin/Plugins/index', '\\app\\admin\\controller\\PluginsController@index', 35, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (47, '2024-03-27 16:47:45', '2024-03-27 16:47:45', '插件管理-表格', 'admin/Plugins/indexTable', '\\app\\admin\\controller\\PluginsController@indexTable', 46, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (48, '2024-03-27 16:47:45', '2024-05-05 16:36:24', '插件管理-演示', 'admin/Plugins/demo', '\\app\\admin\\controller\\PluginsController@demo', 46, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (49, '2024-03-27 16:47:45', '2024-05-05 16:36:30', '插件管理-安装', 'admin/Plugins/install', '\\app\\admin\\controller\\PluginsController@install', 46, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (50, '2024-03-27 16:47:45', '2024-05-05 16:36:36', '插件管理-卸载', 'admin/Plugins/uninstall', '\\app\\admin\\controller\\PluginsController@uninstall', 46, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (155, '2024-05-05 17:12:46', '2024-05-05 17:15:37', '附件管理', 'admin/Uploadify/index', '\\app\\admin\\controller\\UploadifyController@index', 3, 'table/index', '10', '10', '10', 'GET', '', '', 999);
INSERT INTO `xb_admin_rule` VALUES (156, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-表格', 'admin/Uploadify/indexTable', '\\app\\admin\\controller\\UploadifyController@indexTable', 155, 'none/index', '10', '10', '10', 'GET', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (157, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-添加', 'admin/Uploadify/add', '\\app\\admin\\controller\\UploadifyController@add', 155, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (158, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-修改', 'admin/Uploadify/edit', '\\app\\admin\\controller\\UploadifyController@edit', 155, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (159, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-删除', 'admin/Uploadify/del', '\\app\\admin\\controller\\UploadifyController@del', 155, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (160, '2024-05-05 17:15:22', '2024-05-05 17:15:22', '菜单管理-编辑列', 'admin/Menus/rowEdit', '\\app\\admin\\controller\\MenusController@rowEdit', 26, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (161, '2024-05-05 17:23:45', '2024-05-05 17:23:59', '附件接口', 'admin/Upload/index', '\\app\\admin\\controller\\UploadController@index', 155, 'table/index', '10', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (163, '2024-05-05 17:23:45', '2024-05-05 17:27:28', '附件接口-上传', 'admin/Upload/upload', '\\app\\admin\\controller\\UploadController@upload', 161, 'none/index', '10', '20', '20', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (164, '2024-05-05 17:23:45', '2024-05-05 17:27:48', '附件接口-修改', 'admin/Upload/edit', '\\app\\admin\\controller\\UploadController@edit', 161, 'none/index', '10', '20', '20', 'PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (165, '2024-05-05 17:23:45', '2024-05-05 17:23:45', '附件接口-删除', 'admin/Upload/del', '\\app\\admin\\controller\\UploadController@del', 161, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
