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
