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
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
  `plugin` varchar(100) NOT NULL DEFAULT '' COMMENT '所属插件标识',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '任务标识',
  `mode` varchar(10) NOT NULL DEFAULT 'php' COMMENT '执行方式：php PHP可调用，command 命令执行',
  `type` varchar(10) NOT NULL DEFAULT '10' COMMENT '任务类型，执行方式为command时有效：10 执行Shell命令，20 访问URL，30 执行PHP代码',
  `target` varchar(255) NOT NULL DEFAULT '' COMMENT '执行目标，执行方式为php时有效，PHP可调用，格式：类名::方法名',
  `params` text COMMENT '执行参数，执行方式为php时有效，JSON数组文本，按顺序作为方法参数',
  `command` varchar(255) NOT NULL DEFAULT '' COMMENT '任务命令，执行方式为command时有效：Shell命令、URL或PHP代码',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT '执行周期，固定间隔配置JSON：档位+各时间单位数值，周按7天、月按30天',
  `state` enum('10','20') NOT NULL DEFAULT '20' COMMENT '任务状态：10禁用，20启用',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `last_time` datetime DEFAULT NULL COMMENT '最后执行时间',
  `run_count` int NOT NULL DEFAULT '0' COMMENT '累计执行次数',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_state` (`state`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='定时任务';
