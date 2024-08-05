DROP TABLE IF EXISTS `xb_admin_rule`;
CREATE TABLE `xb_admin_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `plugin_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '插件标识',
  `module_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '模块名称',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单地址',
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
) ENGINE = InnoDB AUTO_INCREMENT = 287 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-角色菜单' ROW_FORMAT = DYNAMIC;

INSERT INTO `xb_admin_rule` VALUES (1, '2024-03-27 15:57:10', '2024-05-29 11:30:40', '首页', '', 'admin', 'home', 0, 'none/index', '20', '20', '20', 'GET', 'HomeFilled', '', 1);
INSERT INTO `xb_admin_rule` VALUES (2, '2024-03-27 15:57:58', '2024-07-22 16:50:10', '权限', '', 'admin', 'auth', 0, 'none/index', '20', '10', '10', 'GET', 'Connection', '', 8888);
INSERT INTO `xb_admin_rule` VALUES (3, '2024-03-27 15:58:24', '2024-05-29 11:31:02', '系统', '', 'admin', 'setting', 0, 'none/index', '20', '10', '10', 'GET', 'Setting', '', 9999);
INSERT INTO `xb_admin_rule` VALUES (5, '2024-03-27 16:00:15', '2024-08-06 00:41:04', '工作台', '', 'admin', 'Index/index', 1, 'vue/index', '20', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (6, '2024-03-27 16:00:53', '2024-03-27 17:19:43', '系统登录', '', 'admin', 'Login/login', 1, 'none/index', '10', '20', '20', 'POST', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (7, '2024-03-27 16:01:30', '2024-05-05 16:24:13', '获取应用信息', '', 'admin', 'Index/site', 1, 'none/index', '10', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (8, '2024-03-27 16:02:06', '2024-03-27 17:20:43', '获取管理员信息', '', 'admin', 'Login/user', 1, 'none/index', '10', '20', '20', 'GET', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (9, '2024-03-27 16:03:11', '2024-07-30 11:03:15', '退出登录', '', 'admin', 'Login/exit', 5, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (10, '2024-03-27 16:03:38', '2024-07-30 11:02:55', '清除缓存', '', 'admin', 'Index/clear', 5, 'remote/index', '10', '20', '20', 'DELETE', '', 'vue/admin/clear', 0);
INSERT INTO `xb_admin_rule` VALUES (11, '2024-03-27 16:04:09', '2024-07-30 11:03:25', '锁定屏幕', '', 'admin', 'Index/lock', 5, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (12, '2024-03-27 16:04:25', '2024-07-30 11:03:32', '解除锁定', '', 'admin', 'Index/unlock', 5, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (16, '2024-03-27 16:18:18', '2024-03-27 16:26:10', '账号管理', '', 'admin', 'Admin/index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (17, '2024-03-27 16:18:18', '2024-03-27 16:18:18', '账号管理-表格', '', 'admin', 'Admin/indexTable', 16, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (18, '2024-03-27 16:18:18', '2024-05-05 16:37:03', '账号管理-添加', '', 'admin', 'Admin/add', 16, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (19, '2024-03-27 16:18:18', '2024-05-05 16:37:09', '账号管理-修改', '', 'admin', 'Admin/edit', 16, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (20, '2024-03-27 16:18:18', '2024-05-05 16:37:16', '账号管理-删除', '', 'admin', 'Admin/del', 16, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (21, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '部门管理', '', 'admin', 'AdminRole/index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (22, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '部门管理-表格', '', 'admin', 'AdminRole/indexTable', 21, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (23, '2024-03-27 16:19:10', '2024-05-05 16:37:29', '部门管理-添加', '', 'admin', 'AdminRole/add', 21, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (24, '2024-03-27 16:19:10', '2024-05-05 16:37:35', '部门管理-修改', '', 'admin', 'AdminRole/edit', 21, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (25, '2024-03-27 16:19:10', '2024-05-05 16:37:43', '部门管理-删除', '', 'admin', 'AdminRole/del', 21, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (26, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理', '', 'admin', 'Menus/index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (27, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-表格', '', 'admin', 'Menus/indexTable', 26, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (28, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-添加', '', 'admin', 'Menus/add', 26, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (29, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-修改', '', 'admin', 'Menus/edit', 26, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (30, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-删除', '', 'admin', 'Menus/del', 26, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (31, '2024-03-27 16:20:43', '2024-05-05 16:37:52', '角色管理-授权', '', 'admin', 'AdminRole/auth', 21, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (32, '2024-03-27 16:21:44', '2024-04-19 11:49:44', '账号管理-资料', '', 'admin', 'Admin/info', 16, 'form/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (33, '2024-03-27 16:22:57', '2024-05-05 16:38:30', '系统设置', '', 'admin', 'Settings/config', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', 'group=system', 1);
INSERT INTO `xb_admin_rule` VALUES (34, '2024-03-27 16:23:32', '2024-05-05 16:38:34', '上传设置', '', 'admin', 'Settings/selected', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', 'group=upload', 2);
INSERT INTO `xb_admin_rule` VALUES (35, '2024-03-27 16:44:02', '2024-07-22 16:50:18', '插件', '', 'admin', 'plugin', 0, 'none/index', '20', '10', '10', 'GET', 'ElemeFilled', '', 7777);
INSERT INTO `xb_admin_rule` VALUES (46, '2024-03-27 16:47:45', '2024-08-01 17:20:39', '插件管理', '', 'admin', 'Plugins/index', 35, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (47, '2024-03-27 16:47:45', '2024-03-27 16:47:45', '插件管理-表格', '', 'admin', 'Plugins/indexTable', 46, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (48, '2024-03-27 16:47:45', '2024-05-05 16:36:24', '插件管理-演示', '', 'admin', 'PluginsAction/demo', 46, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (49, '2024-03-27 16:47:45', '2024-05-14 22:57:24', '插件管理-安装', '', 'admin', 'PluginsAction/install', 46, 'none/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (50, '2024-03-27 16:47:45', '2024-05-05 16:36:36', '插件管理-卸载', '', 'admin', 'PluginsAction/uninstall', 46, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (155, '2024-05-05 17:12:46', '2024-05-29 11:31:27', '附件管理', '', 'admin', 'Uploadify/index', 267, 'table/index', '10', '10', '10', 'GET', '', '', 999);
INSERT INTO `xb_admin_rule` VALUES (156, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-表格', '', 'admin', 'Uploadify/indexTable', 155, 'none/index', '10', '10', '10', 'GET', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (157, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-添加', '', 'admin', 'Uploadify/add', 155, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (158, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-修改', '', 'admin', 'Uploadify/edit', 155, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (159, '2024-05-05 17:12:46', '2024-05-05 17:12:46', '附件管理-删除', '', 'admin', 'Uploadify/del', 155, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (160, '2024-05-05 17:15:22', '2024-05-05 17:15:22', '菜单管理-编辑列', '', 'admin', 'Menus/rowEdit', 26, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (161, '2024-05-05 17:23:45', '2024-05-05 17:23:59', '附件接口', '', 'admin', 'Upload/index', 155, 'table/index', '10', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (163, '2024-05-05 17:23:45', '2024-05-05 17:27:28', '附件接口-上传', '', 'admin', 'Upload/upload', 161, 'none/index', '10', '20', '20', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (164, '2024-05-05 17:23:45', '2024-05-05 17:27:48', '附件接口-修改', '', 'admin', 'Upload/edit', 161, 'none/index', '10', '20', '20', 'PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (165, '2024-05-05 17:23:45', '2024-05-05 17:23:45', '附件接口-删除', '', 'admin', 'Upload/del', 161, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (166, '2024-05-12 02:26:18', '2024-05-12 02:26:29', '插件管理-更新', '', 'admin', 'PluginsAction/update', 46, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (167, '2024-05-12 06:35:53', '2024-05-14 22:57:11', '插件管理-购买', '', 'admin', 'PluginsAction/order', 46, 'none/index', '10', '10', '10', 'GET,POST', '', '/vue/admin/plugins/order', 0);
INSERT INTO `xb_admin_rule` VALUES (168, '2024-05-12 15:49:35', '2024-05-15 11:29:46', '插件管理-统订单', '', 'admin', 'PluginsAction/unifiedOrder', 46, 'none/index', '10', '10', '10', 'GET', '', '/vue/admin/plugins/detail', 0);
INSERT INTO `xb_admin_rule` VALUES (169, '2024-05-13 13:01:14', '2024-05-13 13:02:08', '云服务中心', '', 'admin', 'Cloud/view', 35, 'remote/index', '10', '10', '10', 'GET', '', 'vue/admin/cloud/index', 0);
INSERT INTO `xb_admin_rule` VALUES (170, '2024-05-13 13:01:39', '2024-05-13 13:03:27', '获取用户信息', '', 'admin', 'Cloud/index', 169, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (171, '2024-05-13 13:02:46', '2024-05-13 13:02:59', '用户登录', '', 'admin', 'Cloud/login', 169, 'none/index', '10', '10', '10', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (234, '2024-05-17 10:46:50', '2024-05-17 10:46:50', '插件管理-详情', '', 'admin', 'PluginsAction/detail', 46, 'remote/index', '10', '10', '10', 'GET', '', 'vue/admin/plugins/detail', 0);
INSERT INTO `xb_admin_rule` VALUES (266, '2024-05-20 14:45:13', '2024-05-20 14:45:13', '插件管理-配置', '', 'admin', 'PluginsAction/config', 46, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (267, '2024-05-29 11:29:19', '2024-05-29 11:29:39', '通用', '', 'admin', 'currency', 0, 'none/index', '20', '10', '10', 'GET', 'Coin', '', 6666);
INSERT INTO `xb_admin_rule` VALUES (268, '2024-05-29 11:32:43', '2024-05-29 11:32:43', '字典管理', '', 'admin', 'Dict/index', 267, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (269, '2024-05-29 11:32:43', '2024-05-29 11:32:43', '字典管理-表格', '', 'admin', 'Dict/indexTable', 268, 'none/index', '10', '10', '10', 'GET', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (270, '2024-05-29 11:32:43', '2024-05-29 11:32:43', '字典管理-添加', '', 'admin', 'Dict/add', 268, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (271, '2024-05-29 11:32:43', '2024-05-29 11:32:43', '字典管理-修改', '', 'admin', 'Dict/edit', 268, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (272, '2024-05-29 11:32:43', '2024-05-29 11:32:43', '字典管理-删除', '', 'admin', 'Dict/del', 268, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (273, '2024-05-29 15:26:15', '2024-05-29 15:38:08', '字典管理-编辑列', '', 'admin', 'Dict/rowEdit', 268, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (274, '2024-07-08 15:02:57', '2024-07-08 15:04:06', '插件管理-导入', '', 'admin', 'PluginsAction/import', 46, 'none/index', '10', '10', '10', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (275, '2024-07-08 15:03:48', '2024-07-08 15:04:18', '插件管理-导出', '', 'admin', 'PluginsAction/export', 46, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (276, '2024-07-13 16:04:53', '2024-07-13 16:04:58', '用户', '', 'admin', 'user', 0, 'none/index', '20', '10', '10', 'GET', 'UserFilled', '', 3);
INSERT INTO `xb_admin_rule` VALUES (277, '2024-07-13 16:05:48', '2024-07-13 16:05:48', '用户管理', '', 'admin', 'User/index', 276, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (278, '2024-07-13 16:05:48', '2024-07-13 16:05:48', '用户管理-表格', '', 'admin', 'User/indexTable', 277, 'none/index', '10', '10', '10', 'GET', '', NULL, 0);
INSERT INTO `xb_admin_rule` VALUES (279, '2024-07-13 16:05:48', '2024-07-13 16:05:48', '用户管理-添加', '', 'admin', 'User/add', 277, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (280, '2024-07-13 16:05:48', '2024-07-13 16:05:48', '用户管理-修改', '', 'admin', 'User/edit', 277, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (281, '2024-07-13 16:05:48', '2024-07-13 16:05:48', '用户管理-删除', '', 'admin', 'User/del', 277, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (282, '2024-07-30 11:06:40', '2024-07-30 11:06:40', '版本更新', '', 'admin', 'Updated/index', 5, 'remote/index', '10', '10', '10', 'GET', '', 'vue/admin/updated', 0);
INSERT INTO `xb_admin_rule` VALUES (283, '2024-07-30 11:31:11', '2024-07-30 11:31:11', '重启系统', '', 'admin', 'Index/restart', 5, 'none/index', '10', '10', '10', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (285, '2024-08-01 17:04:45', '2024-08-01 17:10:36', '菜单管理-生成', '', 'admin', 'Menus/resources', 26, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (286, '2024-08-01 18:35:14', '2024-08-01 18:35:12', '用户管理-修改列', '', 'admin', 'User/rowEdit', 277, 'none/index', '10', '10', '10', 'GET,POST,PUT,DELETE', '', '', 0);