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
  `component` enum('none/index','table/index','table/sidebar','form/index','remote/index') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none/index' COMMENT '组件类型',
  `is_show` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否显示：10否，20是',
  `is_default` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否默认：10否，20是',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  `method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '请求方式',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `params` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '菜单参数',
  `sort` int(11) NOT NULL DEFAULT 100 COMMENT '菜单排序（值越大，越靠后）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 423 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '后台菜单' ROW_FORMAT = DYNAMIC;
INSERT INTO `xb_admin_rule` VALUES (2, '2024-03-27 15:57:58', '2024-11-30 12:29:09', '权限管理', '10', '', 'Auth', 0, 'none/index', '20', '10', '10', 'GET', 'TeamOutlined', '', 8888);
INSERT INTO `xb_admin_rule` VALUES (3, '2024-03-27 15:58:24', '2025-01-01 04:59:11', '系统设置', '10', '', 'Setting', 0, 'none/index', '20', '10', '10', 'GET', 'Setting', '', 9999);
INSERT INTO `xb_admin_rule` VALUES (5, '2024-03-27 16:00:15', '2024-11-30 12:32:55', '工作台', '20', '', 'workbench', 0, 'remote/index', '20', '20', '20', 'GET', 'DashboardOutlined', 'backend/Index/workbench', 0);
INSERT INTO `xb_admin_rule` VALUES (6, '2024-03-27 16:00:53', '2024-10-20 00:31:46', '系统登录', '30', '', 'backend/Login/login', 5, 'none/index', '10', '20', '20', 'POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (7, '2024-03-27 16:01:30', '2024-10-20 00:31:58', '获取应用信息', '30', '', 'backend/Index/site', 5, 'none/index', '10', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (8, '2024-03-27 16:02:06', '2024-10-20 00:32:10', '获取管理员信息', '30', '', 'backend/Login/user', 5, 'none/index', '10', '20', '20', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (9, '2024-03-27 16:03:11', '2024-10-20 00:32:18', '退出登录', '30', '', 'backend/Login/exit', 5, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (10, '2024-03-27 16:03:38', '2024-10-20 00:32:27', '清除缓存', '30', '', 'backend/Index/clear', 5, 'remote/index', '10', '20', '20', 'DELETE', '', 'vue/admin/clear', 0);
INSERT INTO `xb_admin_rule` VALUES (11, '2024-03-27 16:04:09', '2024-10-20 00:32:35', '锁定屏幕', '30', '', 'backend/Index/lock', 5, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (12, '2024-03-27 16:04:25', '2024-10-20 00:32:43', '解除锁定', '30', '', 'backend/Index/unlock', 5, 'none/index', '10', '20', '20', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (16, '2024-03-27 16:18:18', '2024-11-25 19:50:32', '账号管理', '20', '', 'backend/Admin/index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (17, '2024-03-27 16:18:18', '2024-03-27 16:18:18', '账号管理-表格', '30', '', 'backend/Admin/indexTable', 16, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (18, '2024-03-27 16:18:18', '2024-05-05 16:37:03', '账号管理-添加', '30', '', 'backend/Admin/add', 16, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (19, '2024-03-27 16:18:18', '2024-05-05 16:37:09', '账号管理-修改', '30', '', 'backend/Admin/edit', 16, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (20, '2024-03-27 16:18:18', '2024-05-05 16:37:16', '账号管理-删除', '30', '', 'backend/Admin/del', 16, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (21, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '角色管理', '20', '', 'backend/AdminRole/index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (22, '2024-03-27 16:19:10', '2024-03-27 16:19:10', '角色管理-表格', '30', '', 'backend/AdminRole/indexTable', 21, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (23, '2024-03-27 16:19:10', '2024-05-05 16:37:29', '角色管理-添加', '30', '', 'backend/AdminRole/add', 21, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (24, '2024-03-27 16:19:10', '2024-05-05 16:37:35', '角色管理-修改', '30', '', 'backend/AdminRole/edit', 21, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (25, '2024-03-27 16:19:10', '2024-05-05 16:37:43', '角色管理-删除', '30', '', 'backend/AdminRole/del', 21, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (26, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理', '20', '', 'backend/AdminRule/index', 2, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (27, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-表格', '30', '', 'backend/AdminRule/indexTable', 26, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (28, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-添加', '30', '', 'backend/AdminRule/add', 26, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (29, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-修改', '30', '', 'backend/AdminRule/edit', 26, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (30, '2024-03-27 16:19:42', '2024-03-27 16:19:42', '菜单管理-删除', '30', '', 'backend/AdminRule/del', 26, 'none/index', '10', '10', '10', 'DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (31, '2024-03-27 16:20:43', '2024-05-05 16:37:52', '角色管理-授权', '30', '', 'backend/AdminRole/auth', 21, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (32, '2024-03-27 16:21:44', '2024-11-21 15:10:16', '账号管理-资料', '30', '', 'backend/Admin/profile', 16, 'form/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (34, '2024-03-27 16:23:32', '2024-12-07 21:57:39', '上传设置', '20', '', 'backend/UploadConf/index', 3, 'table/index', '20', '10', '10', 'GET,PUT', '', '', 3);
INSERT INTO `xb_admin_rule` VALUES (160, '2024-05-05 17:15:22', '2024-10-20 00:43:39', '菜单管理-编辑列', '30', '', 'backend/AdminRule/rowEdit', 26, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (276, '2024-07-13 16:04:53', '2024-11-30 12:33:50', '站点管理', '10', '', 'Tenant', 0, 'none/index', '20', '10', '10', 'GET', 'DesktopOutlined', '', 10);
INSERT INTO `xb_admin_rule` VALUES (277, '2024-07-13 16:05:48', '2024-11-29 21:29:34', '站点管理', '20', '', 'backend/WebSite/index', 276, 'table/index', '20', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (278, '2024-07-13 16:05:48', '2024-12-13 00:55:43', '站点管理-表格', '30', '', 'backend/WebSite/indexTable', 277, 'none/index', '10', '10', '10', 'GET', '', '', 1);
INSERT INTO `xb_admin_rule` VALUES (279, '2024-07-13 16:05:48', '2024-12-13 00:55:45', '站点管理-添加', '30', '', 'backend/WebSite/add', 277, 'form/index', '10', '10', '10', 'GET,POST', '', '', 2);
INSERT INTO `xb_admin_rule` VALUES (280, '2024-07-13 16:05:48', '2024-12-13 00:55:46', '站点管理-修改', '30', '', 'backend/WebSite/edit', 277, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 3);
INSERT INTO `xb_admin_rule` VALUES (281, '2024-07-13 16:05:48', '2024-12-13 00:55:48', '站点管理-删除', '30', '', 'backend/WebSite/del', 277, 'none/index', '10', '10', '10', 'DELETE', '', '', 4);
INSERT INTO `xb_admin_rule` VALUES (285, '2024-08-01 17:04:45', '2024-10-20 00:43:50', '菜单管理-生成', '30', '', 'backend/AdminRule/resources', 26, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (286, '2024-08-01 18:35:14', '2024-12-30 23:36:44', '站点管理-操作', '30', '', 'backend/WebSite/action', 277, 'none/index', '10', '10', '10', 'GET,POST,PUT,DELETE', '', '', 5);
INSERT INTO `xb_admin_rule` VALUES (328, '2024-11-15 15:47:32', '2024-12-13 00:56:13', '站点管理-跳转', '30', '', 'backend/WebExtra/link', 277, 'none/index', '10', '10', '10', 'GET', '', '', 10);
INSERT INTO `xb_admin_rule` VALUES (330, '2024-11-17 13:03:48', '2024-12-07 21:57:45', '保存上传设置', '30', '', 'backend/UploadConf/config', 34, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (356, '2024-11-20 17:52:11', '2024-11-20 17:52:21', '账号管理-修改列', '10', '', 'backend/Admin/rowEdit', 16, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (357, '2024-11-25 23:00:37', '2024-11-25 23:01:50', '站点管理员', '30', '', 'backend/WebAdmin/index', 277, 'table/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (358, '2024-11-25 23:52:11', '2024-11-25 23:52:11', '站点管理员-表格', '30', '', 'backend/WebAdmin/index', 357, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (359, '2024-11-25 23:53:02', '2024-11-25 23:53:02', '站点管理员-添加', '30', '', 'backend/WebAdmin/add', 357, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (360, '2024-11-25 23:53:41', '2024-11-25 23:53:41', '站点管理员-修改', '30', '', 'backend/WebAdmin/edit', 357, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (361, '2024-11-25 23:54:34', '2024-11-25 23:54:34', '站点管理员-删除', '30', '', 'backend/WebAdmin/del', 357, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (362, '2024-11-28 15:56:09', '2024-11-28 15:56:09', '站点菜单', '20', '', 'backend/WebMenus/index', 276, 'table/index', '20', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (363, '2024-11-26 17:07:01', '2024-11-26 17:07:01', '站点菜单-表格', '30', '', 'backend/WebMenus/indexTable', 362, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (364, '2024-11-26 17:07:40', '2024-11-26 17:09:37', '站点菜单-添加', '30', '', 'backend/WebMenus/add', 362, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (365, '2024-11-26 17:08:35', '2024-11-26 17:09:33', '站点菜单-修改', '30', '', 'backend/WebMenus/edit', 362, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (366, '2024-11-26 17:08:56', '2024-11-26 17:10:17', '站点菜单-删除', '30', '', 'backend/WebMenus/del', 362, 'none/index', '10', '10', '10', 'DELETE', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (367, '2024-11-26 17:10:12', '2024-11-26 17:10:12', '站点菜单-编辑列', '30', '', 'backend/WebMenus/rowEdit', 362, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (368, '2024-11-26 17:10:52', '2024-11-26 17:10:52', '站点菜单-生成', '30', '', 'backend/WebMenus/resources', 362, 'none/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (369, '2024-11-26 18:21:23', '2024-12-13 00:55:56', '站点管理-导入', '30', '', 'backend/WebExtra/import', 277, 'none/index', '10', '10', '10', 'GET', '', '', 7);
INSERT INTO `xb_admin_rule` VALUES (370, '2024-11-26 18:22:13', '2024-12-13 00:56:00', '站点管理-导出', '30', '', 'backend/WebExtra/export', 277, 'none/index', '10', '10', '10', 'GET', '', '', 8);
INSERT INTO `xb_admin_rule` VALUES (371, '2024-11-26 18:22:50', '2024-12-13 00:56:04', '站点管理-清空', '30', '', 'backend/WebExtra/clear', 277, 'none/index', '10', '10', '10', 'GET', '', '', 9);
INSERT INTO `xb_admin_rule` VALUES (372, '2024-11-27 18:04:26', '2024-11-28 15:59:49', '宝塔设置', '20', '', 'backend/Setting/config/backend/bt', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', '', 4);
INSERT INTO `xb_admin_rule` VALUES (374, '2024-11-28 12:24:10', '2024-11-28 15:59:52', '定时任务', '20', '', 'backend/Crontab/index', 3, 'table/index', '20', '10', '10', 'GET', '', '', 5);
INSERT INTO `xb_admin_rule` VALUES (375, '2024-11-28 12:25:43', '2024-11-28 12:25:43', '定时任务-添加', '30', '', 'backend/Crontab/add', 374, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (376, '2024-11-28 12:26:15', '2024-11-28 12:26:15', '定时任务-修改', '30', '', 'backend/Crontab/edit', 374, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (377, '2024-11-28 12:26:56', '2024-11-28 12:26:56', '定时任务-删除', '30', '', 'backend/Crontab/del', 374, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (378, '2024-11-28 12:27:22', '2024-11-28 12:27:22', '定时任务-表格', '30', '', 'backend/Crontab/indexTable', 374, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (379, '2024-11-28 12:27:56', '2024-11-28 12:27:56', '定时任务-操作', '30', '', 'backend/Crontab/action', 374, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (380, '2024-11-28 12:29:22', '2024-11-28 15:59:53', '字典管理', '20', '', 'backend/Dict/index', 3, 'table/index', '20', '10', '10', 'GET', '', '', 6);
INSERT INTO `xb_admin_rule` VALUES (381, '2024-11-28 12:29:53', '2024-11-28 12:29:53', '字典管理-添加', '30', '', 'backend/Dict/add', 380, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (382, '2024-11-28 12:30:18', '2024-11-28 12:30:38', '字典管理-修改', '30', '', 'backend/Dict/edit', 380, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (383, '2024-11-28 12:31:13', '2024-11-28 12:31:13', '字典管理-删除', '30', '', 'backend/Dict/del', 380, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (384, '2024-11-28 12:31:48', '2024-11-28 12:31:48', '字典管理-表格', '30', '', 'backend/Dict/indexTable', 380, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (385, '2024-11-28 12:34:57', '2024-12-13 01:33:18', '版权设置', '20', '', 'backend/Setting/config/backend/copyright', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', '', 2);
INSERT INTO `xb_admin_rule` VALUES (386, '2024-11-28 13:20:04', '2024-11-28 13:20:04', '公告管理', '20', '', 'backend/WebNotice/index', 276, 'table/index', '20', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (387, '2024-11-28 13:20:43', '2024-11-28 13:20:43', '公告管理-添加', '30', '', 'backend/WebNotice/add', 386, 'form/index', '10', '10', '10', 'GET,POST', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (388, '2024-11-28 13:21:09', '2024-11-28 13:21:09', '公告管理-修改', '30', '', 'backend/WebNotice/edit', 386, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (389, '2024-11-28 13:21:38', '2024-11-28 13:21:38', '公告管理-删除', '30', '', 'backend/WebNotice/del', 386, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (390, '2024-11-28 13:22:10', '2024-11-28 13:22:10', '公告管理-表格', '30', '', 'backend/WebNotice/index', 386, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (391, '2024-11-28 13:25:44', '2024-11-28 13:25:44', '公告管理-修改列', '30', '', 'backend/WebNotice/rowEdit', 386, 'none/index', '10', '10', '10', 'PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (393, '2024-11-28 16:00:22', '2024-11-28 16:00:25', '网站信息', '20', '', 'backend/Setting/config/backend/system', 3, 'form/index', '20', '10', '10', 'GET,PUT', '', '', 1);
INSERT INTO `xb_admin_rule` VALUES (394, '2024-12-13 00:59:33', '2024-12-13 01:36:44', '字典数据', '20', '', 'backend/DictData/index', 380, 'table/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (395, '2024-12-13 01:22:02', '2024-12-13 01:22:02', '字典数据-添加', '30', '', 'backend/DictData/add', 394, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (396, '2024-12-13 01:22:02', '2024-12-13 01:22:02', '字典数据-修改', '30', '', 'backend/DictData/edit', 394, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (397, '2024-12-13 01:22:02', '2024-12-13 01:22:02', '字典数据-删除', '30', '', 'backend/DictData/del', 394, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (398, '2024-12-13 01:22:02', '2024-12-13 01:22:02', '字典数据-修改列', '30', '', 'backend/DictData/rowEdit', 394, 'none/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (399, '2024-12-13 01:22:02', '2024-12-13 01:22:02', '字典数据-表格', '30', '', 'backend/DictData/indexTable', 394, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (400, '2024-12-13 03:01:23', '2024-12-13 03:01:43', '站点管理-下载', '30', '', 'backend/WebExtra/download', 277, 'none/index', '10', '10', '10', 'GET', '', '', 11);
INSERT INTO `xb_admin_rule` VALUES (401, '2024-12-13 04:08:12', '2024-12-13 04:47:00', '工作台', '20', 'xbcode', 'workbench', 0, 'remote/index', '20', '20', '20', 'GET', 'Monitor', 'admin/Index/workbench', 1);
INSERT INTO `xb_admin_rule` VALUES (402, '2024-12-13 04:48:30', '2024-12-13 04:48:36', '插件管理', '10', '', 'Plugins', 0, 'none/index', '20', '10', '10', 'GET', 'ElemeFilled', '', 5);
INSERT INTO `xb_admin_rule` VALUES (403, '2024-12-13 04:49:42', '2024-12-27 21:34:05', '插件列表', '20', '', 'backend/Plugins/index', 402, 'table/index', '20', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (404, '2024-12-26 06:20:50', '2024-12-26 06:20:50', '系统更新', '10', '', 'update', 3, 'none/index', '20', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (405, '2024-12-26 06:21:29', '2024-12-26 06:21:29', '在线更新', '20', '', 'backend/Server/update', 404, 'remote/index', '20', '10', '10', 'GET', '', 'backend/Server/update', 100);
INSERT INTO `xb_admin_rule` VALUES (406, '2024-12-26 06:22:27', '2024-12-26 06:22:27', '系统授权', '20', '', 'backend/Server/authorize', 404, 'remote/index', '20', '10', '10', 'GET', '', 'backend/Server/authorize', 100);
INSERT INTO `xb_admin_rule` VALUES (407, '2024-12-26 06:23:57', '2024-12-26 06:23:57', '登录授权', '30', '', 'backend/Server/login', 404, 'remote/index', '10', '10', '10', 'GET', '', 'backend/Server/login', 100);
INSERT INTO `xb_admin_rule` VALUES (408, '2024-12-28 00:21:26', '2024-12-29 22:43:22', '插件列表-购买', '30', '', 'backend/Plugins/buy', 403, 'remote/index', '10', '10', '10', 'GET,POST', '', 'backend/Plugins/buy', 100);
INSERT INTO `xb_admin_rule` VALUES (409, '2024-12-29 05:14:40', '2024-12-29 22:42:55', '插件列表-检测', '30', '', 'backend/Plugins/checked', 403, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (410, '2024-12-29 05:31:48', '2024-12-29 22:43:12', '插件列表-安装', '30', '', 'backend/Plugins/install', 403, 'remote/index', '10', '10', '10', 'GET,POST', '', 'backend/Plugins/install', 100);
INSERT INTO `xb_admin_rule` VALUES (411, '2024-12-29 05:32:16', '2024-12-29 22:43:51', '插件列表-更新', '30', '', 'backend/Plugins/update', 403, 'remote/index', '10', '10', '10', 'GET,PUT', '', 'backend/Plugins/install', 100);
INSERT INTO `xb_admin_rule` VALUES (412, '2024-12-29 05:32:43', '2024-12-29 22:44:07', '插件列表-卸载', '30', '', 'backend/Plugins/uninstall', 403, 'remote/index', '10', '10', '10', 'GET,DELETE', '', 'backend/Plugins/uninstall', 100);
INSERT INTO `xb_admin_rule` VALUES (413, '2024-12-29 22:47:26', '2024-12-29 22:47:26', '插件列表-详情', '30', '', 'backend/Plugins/detail', 403, 'none/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (414, '2024-12-30 03:16:19', '2024-12-30 03:16:19', '插件列表-状态', '30', '', 'backend/Plugins/state', 403, 'none/index', '10', '10', '10', 'PUT', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (415, '2024-12-31 02:25:37', '2024-12-31 02:25:37', '站点插件', '20', '', 'backend/WebPlugin/index', 277, 'table/index', '10', '10', '10', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (416, '2024-12-31 02:26:02', '2024-12-31 02:26:02', '站点插件-添加', '30', '', 'backend/WebPlugin/add', 415, 'form/index', '10', '10', '10', 'GET,POST', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (417, '2024-12-31 02:26:02', '2024-12-31 02:26:02', '站点插件-修改', '30', '', 'backend/WebPlugin/edit', 415, 'form/index', '10', '10', '10', 'GET,PUT', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (418, '2024-12-31 02:26:02', '2024-12-31 02:26:02', '站点插件-删除', '30', '', 'backend/WebPlugin/del', 415, 'none/index', '10', '10', '10', 'GET,DELETE', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (419, '2024-12-31 02:26:02', '2024-12-31 02:26:02', '站点插件-表格', '30', '', 'backend/WebPlugin/indexTable', 415, 'none/index', '10', '10', '10', 'GET', '', '', 0);
INSERT INTO `xb_admin_rule` VALUES (420, '2025-01-01 02:41:56', '2025-01-01 02:41:56', '获取菜单', '30', '', 'backend/Login/menus', 5, 'none/index', '10', '20', '20', 'GET', '', '', 100);
INSERT INTO `xb_admin_rule` VALUES (421, '2025-01-01 04:58:25', '2025-01-01 04:58:37', '系统日志', '20', '', 'backend/AdminLog/index', 3, 'table/index', '20', '10', '10', 'GET', '', '', 7);
INSERT INTO `xb_admin_rule` VALUES (422, '2025-01-01 04:58:47', '2025-01-01 04:58:47', '系统日志-表格', '30', '', 'backend/AdminLog/indexTable', 421, 'none/index', '10', '10', '10', 'GET', '', '', 0);