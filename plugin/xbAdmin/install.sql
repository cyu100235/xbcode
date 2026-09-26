-- 删除表语句
DROP TABLE IF EXISTS `xb_admin_role`;
-- 表结构：`xb_admin_role`
CREATE TABLE `xb_admin_role` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `admin_id` int NOT NULL DEFAULT '0' COMMENT '所属管理员ID，0为顶级',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `rule` text COMMENT '角色权限，权限地址数组的JSON文本',
  `theme` text COMMENT '部门主题风格，JSON文本',
  `sort` int NOT NULL DEFAULT '100' COMMENT '角色排序',
  `is_system` enum('10','20') NOT NULL DEFAULT '10' COMMENT '是否系统角色：10否，20是',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_admin_id` (`admin_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='后台角色';

-- 删除表语句
DROP TABLE IF EXISTS `xb_admin`;
-- 表结构：`xb_admin`
CREATE TABLE `xb_admin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `admin_id` int NOT NULL DEFAULT '0' COMMENT '上级管理员ID，0为顶级',
  `role_id` int NOT NULL DEFAULT '0' COMMENT '所属角色ID',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '登录账号',
  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '登录密码',
  `nickname` varchar(20) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `state` enum('10','20') NOT NULL DEFAULT '20' COMMENT '账号状态：10禁用，20启用',
  `is_system` enum('10','20') NOT NULL DEFAULT '10' COMMENT '是否超级管理员：10否，20是',
  `login_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_username` (`username`) USING BTREE,
  KEY `idx_role_id` (`role_id`) USING BTREE,
  KEY `idx_admin_id` (`admin_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='人员管理';

-- 删除表语句
DROP TABLE IF EXISTS `xb_admin_rule`;
-- 表结构：`xb_admin_rule`
CREATE TABLE `xb_admin_rule` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `pid` int NOT NULL DEFAULT '0' COMMENT '父级ID，0为顶级',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `short_title` varchar(10) NOT NULL DEFAULT '' COMMENT '菜单短名称',
  `type` enum('10','20','30') NOT NULL DEFAULT '10' COMMENT '菜单类型：10目录，20菜单，30按钮',
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '权限地址',
  `method` varchar(20) NOT NULL DEFAULT 'GET' COMMENT '请求方式，多个以逗号分隔',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `params` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单参数，JSON文本',
  `is_show` enum('10','20') NOT NULL DEFAULT '10' COMMENT '是否显示：10否，20是',
  `is_default` enum('10','20') NOT NULL DEFAULT '10' COMMENT '是否默认权限：10否，20是',
  `is_system` enum('10','20') NOT NULL DEFAULT '10' COMMENT '是否系统菜单：10否，20是',
  `state` enum('10','20') NOT NULL DEFAULT '20' COMMENT '是否启用：10否，20是',
  `sort` int NOT NULL DEFAULT '100' COMMENT '菜单排序，值越大越靠后',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_pid` (`pid`) USING BTREE,
  KEY `idx_plugin_path` (`plugin`,`path`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='后台菜单';

-- 删除表语句
DROP TABLE IF EXISTS `xb_config`;
-- 表结构：`xb_config`
CREATE TABLE `xb_config` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识',
  `group` varchar(50) DEFAULT NULL COMMENT '数据分组',
  `name` varchar(50) DEFAULT NULL COMMENT '字段名称',
  `value` text COMMENT '字段数据',
  PRIMARY KEY (`id`),
  KEY `idx_plugin_group_name` (`plugin`,`group`,`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb3 COMMENT='系统配置';

-- 删除表语句
DROP TABLE IF EXISTS `xb_config_group`;
-- 表结构：`xb_config_group`
CREATE TABLE `xb_config_group` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识',
  `group` varchar(50) NOT NULL DEFAULT '' COMMENT '分组标识，即 setting 目录下的文件名',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '分组标题',
  `body` text COMMENT '分组字段模板，组件数组的JSON文本',
  `sort` int NOT NULL DEFAULT '0' COMMENT '分组排序',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_plugin_group` (`plugin`,`group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='插件配置分组';

-- 删除表语句
DROP TABLE IF EXISTS `xb_dict`;
-- 表结构：`xb_dict`
CREATE TABLE `xb_dict` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '枚举标题（取自类注释）',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '枚举标识（取自类名称）',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '10' COMMENT '系统字典：10否，20是',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序（值越大越靠后）',
  `values` text COMMENT '枚举数据（JSON）',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_plugin_group` (`plugin`) USING BTREE,
  KEY `idx_plugin_group_key` (`plugin`,`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='字典管理';

-- 删除表语句
DROP TABLE IF EXISTS `xb_crontab`;
-- 表结构：`xb_crontab`
CREATE TABLE `xb_crontab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `title` varchar(100) NOT NULL COMMENT '任务名称',
  `name` varchar(50) NOT NULL COMMENT '任务标识',
  `plugin` varchar(100) NOT NULL COMMENT '插件标识',
  `type` enum('10','20','30') NOT NULL DEFAULT '10' COMMENT '任务类型',
  `state` enum('10','20','30') NOT NULL DEFAULT '10' COMMENT '任务状态',
  `cron_expression` varchar(100) NOT NULL DEFAULT '' COMMENT 'Cron表达式',
  `cron_desc` varchar(100) NOT NULL DEFAULT '' COMMENT '周期描述',
  `command` varchar(255) DEFAULT NULL COMMENT '执行命令',
  `last_time` datetime DEFAULT NULL COMMENT '最后执行时间',
  `error` text COMMENT '错误原因',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='定时任务';

-- 删除表语句
DROP TABLE IF EXISTS `xb_crontab_log`;
-- 表结构：`xb_crontab_log`
CREATE TABLE `xb_crontab_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_at` datetime DEFAULT NULL,
  `crontab_id` int(11) DEFAULT NULL,
  `run_second_time` varchar(30) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='定时任务日志';
