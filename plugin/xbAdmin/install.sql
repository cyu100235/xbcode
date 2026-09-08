-- xbAdmin 后台权限管理数据表
-- 表前缀 `xb_` 为模板前缀，导入时由 plugin/xbCode/api/Mysql.php 替换为 .env 中的 DB_PREFIX
-- 本文件不含 DROP TABLE 语句，重复执行不会清空已有数据

CREATE TABLE `xb_admin_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属管理员ID，0为顶级',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `rule` text COMMENT '角色权限，权限地址数组的JSON文本',
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '角色排序',
  `is_system` enum('10','20') NOT NULL DEFAULT '10' COMMENT '是否系统角色：10否，20是',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_admin_id` (`admin_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台角色';

CREATE TABLE `xb_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级管理员ID，0为顶级',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属角色ID',
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台管理员';

CREATE TABLE `xb_admin_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID，0为顶级',
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
  `sort` int(11) NOT NULL DEFAULT '100' COMMENT '菜单排序，值越大越靠后',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_pid` (`pid`) USING BTREE,
  KEY `idx_plugin_path` (`plugin`,`path`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台菜单权限';
