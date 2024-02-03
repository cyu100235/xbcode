/*
 Navicat Premium Data Transfer

 Source Server         : 腾讯云-重庆-竞价服务器
 Source Server Type    : MySQL
 Source Server Version : 50744
 Source Host           : localhost:3306
 Source Schema         : xiaobai_host

 Target Server Type    : MySQL
 Target Server Version : 50744
 File Encoding         : 65001

 Date: 03/02/2024 09:00:53
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for xb_admin
-- ----------------------------
DROP TABLE IF EXISTS `xb_admin`;
CREATE TABLE `xb_admin`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `role_id` int(11) NULL DEFAULT NULL COMMENT '所属角色',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '所属项目',
  `pid` int(11) NULL DEFAULT 0 COMMENT '上级管理员ID',
  `username` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录账户',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户密码',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '用户状态：10禁用，20启用',
  `nickname` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '用户昵称',
  `login_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '最后登录IP',
  `login_time` datetime NULL DEFAULT NULL COMMENT '最后登录时间',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '用户头像',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 34 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-管理员' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_admin
-- ----------------------------
INSERT INTO `xb_admin` VALUES (1, '2023-12-02 12:26:43', '2024-02-03 08:50:11', 1, NULL, 0, 'admin', '$2y$10$pXM73SY4sQCKSlTsiqTjZ.0eC89iRjkmsf/y4us5NrJZJuFtWHLtS', '20', '楚羽幽', '117.188.192.134', '2024-02-03 08:50:11', 'uploads/system/20240128/068fb65f800d723a8ac2c1690a158807.png', '20');
INSERT INTO `xb_admin` VALUES (2, '2023-12-20 15:18:26', '2023-12-20 15:18:26', 1, 2, 0, 'dsadas', '$2y$10$3pNYeVhqaa8RncmWr1eLA.cuqWAnmk8Um1sIKuyJRuuE2d.10Zpz6', '20', '项目超管', '', NULL, '', '10');
INSERT INTO `xb_admin` VALUES (3, '2023-12-20 15:21:45', '2023-12-20 15:21:45', 2, 2, 0, 'asdsadas', '$2y$10$VlllSvSqNBEjtv50aV.3rOw6UcuTc7VAS4F53w2hMXuY4QB4SNNIK', '20', '项目超管', '', NULL, '', '10');
INSERT INTO `xb_admin` VALUES (4, '2023-12-20 15:22:39', '2023-12-20 15:22:39', 3, 2, 0, 'dsadsa', '$2y$10$qPhPLe9XLZK.QB3wL5MRGuN3QkxS/5vgTki3Yc.xgkI4pT.Ou0zY6', '20', '项目超管', '', NULL, '', '10');
INSERT INTO `xb_admin` VALUES (5, '2023-12-20 15:25:12', '2023-12-20 15:25:12', 4, 2, 0, 'dsadsad', '$2y$10$3hi.Cb8w7TlaTBnRjSqGw.WpcRZ2BcVK82TkH7tiLXmTuuT10wi1q', '20', '项目超管', '', NULL, '', '10');
INSERT INTO `xb_admin` VALUES (6, '2023-12-20 15:25:54', '2023-12-20 15:25:54', 1, 2, 0, 'ddd', '$2y$10$hkOZVrQWzY5OkWWiE0INkO3Rt8GuI/oMqDuXPfh.u4TdLKvBHvEji', '20', '项目超管', '', NULL, '', '10');
INSERT INTO `xb_admin` VALUES (9, '2024-01-27 02:58:28', '2024-01-27 07:09:27', 5, 4, 0, 'admin', '$2y$10$mkQWuF4rWaBirgCICFx8z.geoLTIZfCN3gRj8uofgcK9I/ZgaxPGG', '20', '超级管理员', '117.188.14.169', '2024-01-27 07:09:27', '', '20');
INSERT INTO `xb_admin` VALUES (10, '2024-01-27 10:12:26', '2024-01-28 03:36:42', 6, 5, 0, 'admin', '', '20', '测试管理员', '', NULL, '', '20');
INSERT INTO `xb_admin` VALUES (33, '2024-02-01 06:53:32', '2024-02-01 06:53:32', 30, 28, 0, 'admin', '$2y$10$kGwctFPsjhbOVJnBu5Xx2u0gZfpGQzhlW3fqK4DuznZvAxjYAp.J6', '20', '开发专用管理员', '', NULL, '', '20');

-- ----------------------------
-- Table structure for xb_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `xb_admin_log`;
CREATE TABLE `xb_admin_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL,
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '管理员',
  `role_id` int(11) NULL DEFAULT NULL COMMENT '管理员角色',
  `action_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '本次操作菜单名称',
  `action_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '登录IP',
  `action_type` enum('0','1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作类型：0登录，1新增，2修改，3删除',
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '操作路由',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '操作日志JSON格式',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-操作日志' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for xb_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `xb_admin_role`;
CREATE TABLE `xb_admin_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '所属项目',
  `pid` int(11) NULL DEFAULT 0 COMMENT '上级管理员，0顶级',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '部门名称',
  `rule` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '部门权限',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，1是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-角色记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_admin_role
-- ----------------------------
INSERT INTO `xb_admin_role` VALUES (1, NULL, NULL, NULL, 1, 'dddd', '[\"admin\\/systemIndex\",\"Index\\/index\",\"User\\/login\",\"Index\\/site\",\"User\\/index\",\"User\\/exit\",\"Index\\/clear\",\"Index\\/lock\",\"Apps\\/group\",\"Apps\\/index\",\"systemRules\\/tabs\",\"Admin\\/index\",\"Admin\\/add\",\"Admin\\/edit\",\"Admin\\/del\",\"AdminRole\\/index\",\"AdminRole\\/add\",\"AdminRole\\/edit\",\"AdminRole\\/del\",\"AdminRole\\/auth\",\"Menus\\/index\",\"Menus\\/add\",\"Menus\\/edit\",\"Menus\\/del\",\"systemConfig\\/tabs\",\"Settings\\/config\",\"Settings\\/config\",\"systemUpdate\\/group\",\"SystemUpdate\\/index\",\"SystemUpdate\\/logs\",\"SystemUpdate\\/auth\"]', '20');
INSERT INTO `xb_admin_role` VALUES (2, '2023-12-18 00:26:34', '2023-12-18 00:55:59', NULL, 1, '测试', '[\"admin\\/systemIndex\",\"Index\\/index\",\"User\\/login\",\"Index\\/site\",\"User\\/index\",\"User\\/exit\",\"Index\\/clear\",\"Index\\/lock\",\"Apps\\/group\",\"Apps\\/index\"]', '10');
INSERT INTO `xb_admin_role` VALUES (5, '2024-01-27 02:58:28', '2024-01-27 02:58:28', 4, 0, '聊天-超级管理员', NULL, '20');
INSERT INTO `xb_admin_role` VALUES (6, '2024-01-27 10:12:25', '2024-01-27 10:12:25', 5, 0, '测试-超级管理员', NULL, '20');
INSERT INTO `xb_admin_role` VALUES (7, '2024-01-28 00:09:30', '2024-01-28 00:09:30', 5, 10, '测试', '[]', '10');
INSERT INTO `xb_admin_role` VALUES (30, '2024-02-01 06:53:32', '2024-02-01 06:53:32', 28, 0, '超级管理员', NULL, '20');

-- ----------------------------
-- Table structure for xb_demo_article
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_article`;
CREATE TABLE `xb_demo_article`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-文章记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_article
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_article_cate`;
CREATE TABLE `xb_demo_article_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-文章分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_article_cate
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_goods
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods`;
CREATE TABLE `xb_demo_goods`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-商品记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_goods_cate
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods_cate`;
CREATE TABLE `xb_demo_goods_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `pid` int(11) NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-商品分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_cate
-- ----------------------------
INSERT INTO `xb_demo_goods_cate` VALUES (1, '2024-02-01 22:33:24', '2024-02-01 22:40:48', 0, 'dsad', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 1);
INSERT INTO `xb_demo_goods_cate` VALUES (2, '2024-02-01 22:34:56', '2024-02-01 22:40:52', 0, 'dad', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 1);
INSERT INTO `xb_demo_goods_cate` VALUES (3, '2024-02-01 22:41:07', '2024-02-01 22:41:07', 1, '测试', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100);

-- ----------------------------
-- Table structure for xb_demo_order
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_order`;
CREATE TABLE `xb_demo_order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-订单记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_order
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_order_service
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_order_service`;
CREATE TABLE `xb_demo_order_service`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-订单售后' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_order_service
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_user
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_user`;
CREATE TABLE `xb_demo_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-用户记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_user
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_user_bill
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_user_bill`;
CREATE TABLE `xb_demo_user_bill`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-用户账单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_user_bill
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_user_grade
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_user_grade`;
CREATE TABLE `xb_demo_user_grade`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-会员等级' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_user_grade
-- ----------------------------

-- ----------------------------
-- Table structure for xb_projects
-- ----------------------------
DROP TABLE IF EXISTS `xb_projects`;
CREATE TABLE `xb_projects`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '项目名称',
  `app_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用标识',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '项目标识',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '项目图标',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-项目记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_projects
-- ----------------------------
INSERT INTO `xb_projects` VALUES (28, '2024-02-01 06:53:32', '2024-02-01 06:53:32', '开发专用', 'xbaseDemo', 'admin888', 'uploads/system/20240128/068fb65f800d723a8ac2c1690a158807.png');

-- ----------------------------
-- Table structure for xb_settings
-- ----------------------------
DROP TABLE IF EXISTS `xb_settings`;
CREATE TABLE `xb_settings`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `saas_appid` int(10) NULL DEFAULT NULL COMMENT '应用ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置名称（点号为多重数组）',
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组名称（按文件名称）',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据值',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-配置记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_settings
-- ----------------------------
INSERT INTO `xb_settings` VALUES (1, '2024-01-29 08:37:46', '2024-01-29 08:37:46', NULL, 'web_name', 'system', 'dsadas');
INSERT INTO `xb_settings` VALUES (2, '2024-01-29 08:37:46', '2024-01-29 08:37:46', NULL, 'web_url', 'system', 'http://111.230.55.84:39602/');
INSERT INTO `xb_settings` VALUES (3, '2024-01-29 08:37:46', '2024-01-29 08:38:50', NULL, 'web_logo', 'system', 'http://111.230.55.84:39602/uploads/system/20240128/068fb65f800d723a8ac2c1690a158807.png');
INSERT INTO `xb_settings` VALUES (4, '2024-01-29 08:37:51', '2024-01-29 08:37:51', NULL, 'selected_active', 'upload', 'local');
INSERT INTO `xb_settings` VALUES (5, '2024-01-29 08:37:51', '2024-01-29 08:37:51', NULL, 'local.type', 'upload', 'local');
INSERT INTO `xb_settings` VALUES (6, '2024-01-29 08:37:51', '2024-01-29 08:37:51', NULL, 'local.root', 'upload', 'uploads');
INSERT INTO `xb_settings` VALUES (7, '2024-02-01 22:23:00', '2024-02-01 22:23:00', 28, 'selected_active', 'upload', 'local');
INSERT INTO `xb_settings` VALUES (8, '2024-02-01 22:23:00', '2024-02-01 22:23:00', 28, 'local.type', 'upload', 'local');
INSERT INTO `xb_settings` VALUES (9, '2024-02-01 22:23:00', '2024-02-01 22:23:00', 28, 'local.root', 'upload', 'uploads');
INSERT INTO `xb_settings` VALUES (10, '2024-02-01 22:23:31', '2024-02-01 22:23:31', 28, 'web_name', 'system', '');
INSERT INTO `xb_settings` VALUES (11, '2024-02-01 22:23:31', '2024-02-01 22:23:31', 28, 'web_url', 'system', 'http://94.191.20.230:39600/');
INSERT INTO `xb_settings` VALUES (12, '2024-02-01 22:23:31', '2024-02-01 22:23:31', 28, 'web_logo', 'system', '');

-- ----------------------------
-- Table structure for xb_upload
-- ----------------------------
DROP TABLE IF EXISTS `xb_upload`;
CREATE TABLE `xb_upload`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '上传时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '应用ID',
  `uid` int(11) NULL DEFAULT NULL COMMENT '用户ID',
  `cid` int(11) NULL DEFAULT 0 COMMENT '所属分类',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '附件名称',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件名称',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件地址',
  `format` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件格式',
  `size` int(11) NULL DEFAULT NULL COMMENT '文件大小，单位：字节',
  `adapter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '选定器：aliyun阿里云，qcloud腾讯云，qiniu七牛云',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-附件记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_upload
-- ----------------------------
INSERT INTO `xb_upload` VALUES (1, '2024-01-28 03:28:25', '2024-01-28 03:28:25', NULL, NULL, 1, '1.png', '1.png', 'uploads/system/20240128/068fb65f800d723a8ac2c1690a158807.png', 'png', 5592, 'local');
INSERT INTO `xb_upload` VALUES (2, '2024-02-01 22:23:42', '2024-02-01 22:23:42', 28, NULL, 25, '1.png', '1.png', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 'png', 5592, 'local');
INSERT INTO `xb_upload` VALUES (3, '2024-02-01 22:58:36', '2024-02-01 22:58:36', 28, NULL, 25, '1.jpg', '1.jpg', 'uploads/default/20240201/b9c3fe5690f8330d80abae98c9db476e.jpg', 'jpg', 99954, 'local');

-- ----------------------------
-- Table structure for xb_upload_cate
-- ----------------------------
DROP TABLE IF EXISTS `xb_upload_cate`;
CREATE TABLE `xb_upload_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '应用ID',
  `uid` int(11) NULL DEFAULT NULL COMMENT '用户ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  `dir_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类目录',
  `sort` int(11) NULL DEFAULT 0 COMMENT '分类排序',
  `is_system` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-附件分类' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_upload_cate
-- ----------------------------
INSERT INTO `xb_upload_cate` VALUES (1, '2023-12-18 10:58:47', '2023-12-18 10:58:49', NULL, NULL, '系统分类', 'system', 0, '20');
INSERT INTO `xb_upload_cate` VALUES (7, '2024-01-31 09:49:40', '2024-01-31 09:49:40', NULL, NULL, '1', 'dd', 0, '10');
INSERT INTO `xb_upload_cate` VALUES (25, '2024-02-01 06:53:32', '2024-02-01 06:53:32', 28, NULL, '默认分类', 'default', 0, '20');

SET FOREIGN_KEY_CHECKS = 1;
