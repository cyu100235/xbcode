-- 删除表语句
DROP TABLE IF EXISTS `xb_config`;
-- 表结构：xb_config
CREATE TABLE `xb_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NOT NULL COMMENT '创建时间',
  `update_at` datetime NOT NULL COMMENT '更新时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  `name` varchar(50) NOT NULL COMMENT '配置名称',
  `value` text COMMENT '配置数据',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=337 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='配置参数';
