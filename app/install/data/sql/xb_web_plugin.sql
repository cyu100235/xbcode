DROP TABLE IF EXISTS `xb_web_plugin`;
CREATE TABLE `xb_web_plugin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `site_id` int(11) NOT NULL COMMENT '站点ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '插件标识',
  `expire_time` datetime NULL DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '站点插件' ROW_FORMAT = Dynamic;