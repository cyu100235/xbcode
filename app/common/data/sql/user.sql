DROP TABLE IF EXISTS `xb_user`;
CREATE TABLE `xb_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  `username` varchar(30) DEFAULT NULL COMMENT '用户账号',
  `password` varchar(255) DEFAULT NULL COMMENT '登录密码',
  `nickname` varchar(30) DEFAULT NULL COMMENT '用户昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `state` enum('10','20') NOT NULL DEFAULT '10' COMMENT '用户状态：10禁用，20启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统-用户记录';