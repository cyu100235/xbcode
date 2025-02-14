DROP TABLE IF EXISTS `xb_admin_role`;
CREATE TABLE `xb_admin_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '所属管理员ID，0顶级',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色名称',
  `rule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '角色权限',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '角色排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色记录' ROW_FORMAT = DYNAMIC;
DROP TABLE IF EXISTS `xb_admin`;
CREATE TABLE `xb_admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `role_id` int(11) NULL DEFAULT NULL COMMENT '所属部门',
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '上级管理员ID',
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录账户',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户密码',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '用户头像',
  `nickname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '用户昵称',
  `state` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20' COMMENT '用户状态：10禁用，20启用',
  `login_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '最后登录IP',
  `login_time` datetime NULL DEFAULT NULL COMMENT '最后登录时间',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统管理员' ROW_FORMAT = DYNAMIC;
DROP TABLE IF EXISTS `xb_admin_rule`;
CREATE TABLE `xb_admin_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `type` enum('10','20','30') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '菜单类型',
  `plugin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单地址',
  `pid` int(11) NOT NULL COMMENT '父级ID',
  `component` enum('none/index','table/index','table/sidebar','form/index','remote/index','workbench/index') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none/index' COMMENT '组件类型',
  `is_show` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否显示：10否，20是',
  `is_default` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否默认：10否，20是',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  `is_web` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20' COMMENT '站点菜单：10否，20是',
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '请求方式',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `params` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单参数',
  `sort` int(11) NOT NULL DEFAULT 100 COMMENT '菜单排序（值越大，越靠后）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 421 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台菜单' ROW_FORMAT = DYNAMIC;
INSERT INTO `xb_admin_rule` VALUES (2, '2024-03-27 15:57:58', '2024-11-30 12:29:09', '权限管理', '10', 'xbCode', 'Auth', 0, 'none/index', '20', '10', '10', '20', 'GET', 'TeamOutlined', '', 8888);
INSERT INTO `xb_admin_rule` VALUES (5, '2024-03-27 16:00:15', '2025-02-14 09:38:21', '工作台', '20', 'xbCode', 'workbench', 0, 'workbench/index', '20', '20', '20', '20', 'GET', 'DashboardOutlined', 'app/xbCode/admin/Index/workbench', 0);
INSERT INTO `xb_admin_rule` VALUES (6, '2024-03-27 16:00:53', '2024-10-20 00:31:46', '系统登录', '30', 'xbCode', 'admin/Login/login', 5, 'none/index', '10', '20', '20', '20', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (7, '2024-03-27 16:01:30', '2024-10-20 00:31:58', '获取应用信息', '30', 'xbCode', 'admin/Index/site', 5, 'none/index', '10', '20', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (8, '2024-03-27 16:02:06', '2024-10-20 00:32:10', '获取管理员信息', '30', 'xbCode', 'admin/Login/user', 5, 'none/index', '10', '20', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (9, '2024-03-27 16:03:11', '2024-10-20 00:32:18', '退出登录', '30', 'xbCode', 'admin/Login/exit', 5, 'none/index', '10', '20', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (16, '2024-03-27 16:18:18', '2024-11-25 19:50:32', '账号管理', '20', 'xbCode', 'admin/Admin/index', 2, 'table/index', '20', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (17, '2024-03-27 16:18:18', '2024-03-27 16:18:18', '账号管理-表格', '30', 'xbCode', 'admin/Admin/indexTable', 16, 'none/index', '10', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (18, '2024-03-27 16:18:18', '2024-05-05 16:37:03', '账号管理-添加', '30', 'xbCode', 'admin/Admin/add', 16, 'form/index', '10', '10', '10', '20', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (19, '2024-03-27 16:18:18', '2024-05-05 16:37:09', '账号管理-修改', '30', 'xbCode', 'admin/Admin/edit', 16, 'form/index', '10', '10', '10', '20', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (20, '2024-03-27 16:18:18', '2024-05-05 16:37:16', '账号管理-删除', '30', 'xbCode', 'admin/Admin/del', 16, 'none/index', '10', '10', '10', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (21, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '角色管理', '20', 'xbCode', 'admin/AdminRole/index', 2, 'table/index', '20', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (22, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '角色管理-表格', '30', 'xbCode', 'admin/AdminRole/indexTable', 21, 'none/index', '10', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (23, '2024-03-27 16:19:10', '2024-05-05 16:37:29', '角色管理-添加', '30', 'xbCode', 'admin/AdminRole/add', 21, 'form/index', '10', '10', '10', '20', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (24, '2024-03-27 16:19:10', '2024-05-05 16:37:35', '角色管理-修改', '30', 'xbCode', 'admin/AdminRole/edit', 21, 'form/index', '10', '10', '10', '20', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (25, '2024-03-27 16:19:10', '2024-05-05 16:37:43', '角色管理-删除', '30', 'xbCode', 'admin/AdminRole/del', 21, 'none/index', '10', '10', '10', '20', 'GET,DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (26, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理', '20', 'xbCode', 'admin/AdminRule/index', 2, 'table/index', '20', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (27, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-表格', '30', 'xbCode', 'admin/AdminRule/indexTable', 26, 'none/index', '10', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (28, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-添加', '30', 'xbCode', 'admin/AdminRule/add', 26, 'form/index', '10', '10', '10', '20', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (29, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-修改', '30', 'xbCode', 'admin/AdminRule/edit', 26, 'form/index', '10', '10', '10', '20', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (30, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-删除', '30', 'xbCode', 'admin/AdminRule/del', 26, 'none/index', '10', '10', '10', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (31, '2024-03-27 16:20:43', '2024-05-05 16:37:52', '角色管理-授权', '30', 'xbCode', 'admin/AdminRole/auth', 21, 'form/index', '10', '10', '10', '20', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (32, '2024-03-27 16:21:44', '2024-11-21 15:10:16', '账号管理-资料', '30', 'xbCode', 'admin/Admin/profile', 16, 'form/index', '10', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (160, '2024-05-05 17:15:22', '2024-10-20 00:43:39', '菜单管理-编辑列', '30', 'xbCode', 'admin/AdminRule/rowEdit', 26, 'none/index', '10', '10', '10', '20', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (285, '2024-08-01 17:04:45', '2024-10-20 00:43:50', '菜单管理-生成', '30', 'xbCode', 'admin/AdminRule/resources', 26, 'form/index', '10', '10', '10', '20', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (356, '2024-11-20 17:52:11', '2024-11-20 17:52:21', '账号管理-修改列', '10', 'xbCode', 'admin/Admin/rowEdit', 16, 'none/index', '10', '10', '10', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (420, '2025-01-01 02:41:56', '2025-01-01 02:41:56', '获取菜单', '30', 'xbCode', 'admin/Login/menus', 5, 'none/index', '10', '20', '20', '20', 'GET', '', '', 100);