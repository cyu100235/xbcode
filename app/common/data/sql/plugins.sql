DROP TABLE IF EXISTS `xb_plugins`;
CREATE TABLE `xb_plugins`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '插件名称',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '插件标识',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '插件介绍',
  `author_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '作者名称',
  `version_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '版本名称',
  `version` int(11) NULL DEFAULT 0 COMMENT '版本编号',
  `down` int(11) NULL DEFAULT 0 COMMENT '安装次数',
  `logo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '插件图标',
  `use_plugins` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '使用到的插件（JSON）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-插件安装' ROW_FORMAT = Dynamic;
