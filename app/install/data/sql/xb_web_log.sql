DROP TABLE IF EXISTS `xb_web_log`;
CREATE TABLE `xb_web_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `create_at` datetime NOT NULL,
  `saas_appid` int(11) NOT NULL COMMENT '站点ID',
  `admin_id` int(11) NOT NULL COMMENT '管理员ID',
  `admin_name` varchar(50) NOT NULL COMMENT '管理员账号',
  `menu_title` varchar(50) NOT NULL COMMENT '菜单名称',
  `path` varchar(50) NOT NULL COMMENT '菜单地址',
  `method` varchar(10) NOT NULL COMMENT '请求类型',
  `real_ip` varchar(50) NOT NULL COMMENT '请求IP',
  `city_name` varchar(50) NOT NULL COMMENT '城市名称',
  `query` text NOT NULL COMMENT '请求参数',
  `result` text NOT NULL COMMENT '结果参数',
  `type` enum('10','20') NOT NULL DEFAULT '10' COMMENT '日志类型：10操作日志，20登录日志',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='站点操作日志';