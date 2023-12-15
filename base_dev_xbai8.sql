/*
 Navicat Premium Data Transfer

 Source Server         : 楚羽幽个人-备案-开发服务器
 Source Server Type    : MySQL
 Source Server Version : 50740
 Source Host           : localhost:3306
 Source Schema         : base_dev_xbai8

 Target Server Type    : MySQL
 Target Server Version : 50740
 File Encoding         : 65001

 Date: 15/12/2023 13:47:57
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
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-管理员' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_admin
-- ----------------------------
INSERT INTO `xb_admin` VALUES (1, '2023-12-02 12:26:43', '2023-12-14 23:30:00', 1, NULL, 0, 'admin', '$2y$10$pXM73SY4sQCKSlTsiqTjZ.0eC89iRjkmsf/y4us5NrJZJuFtWHLtS', '20', '楚羽幽', '117.188.12.180', '2023-12-14 23:30:00', '', '20');

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-角色管理' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_admin_role
-- ----------------------------

-- ----------------------------
-- Table structure for xb_app_ads
-- ----------------------------
DROP TABLE IF EXISTS `xb_app_ads`;
CREATE TABLE `xb_app_ads`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '位置标识',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '位置名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '广告标题',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '状态：10禁用，20启用',
  `image_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图片链接',
  `link_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '跳转链接',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用-图片广告' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_app_ads
-- ----------------------------

-- ----------------------------
-- Table structure for xb_app_article
-- ----------------------------
DROP TABLE IF EXISTS `xb_app_article`;
CREATE TABLE `xb_app_article`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `cid` int(11) NULL DEFAULT NULL COMMENT '分类ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '简短描述',
  `thumb` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文章封面',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '状态：10禁用，20启用',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `view` int(11) NULL DEFAULT 0 COMMENT '热度',
  `virtually_view` int(11) NULL DEFAULT 0 COMMENT '虚拟热度',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用-文章内容' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_app_article
-- ----------------------------

-- ----------------------------
-- Table structure for xb_app_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `xb_app_article_cate`;
CREATE TABLE `xb_app_article_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类标题',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '状态：10禁用，20启用',
  `sort` int(11) NULL DEFAULT NULL COMMENT '分类排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用-文章分类' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_app_article_cate
-- ----------------------------

-- ----------------------------
-- Table structure for xb_app_tags
-- ----------------------------
DROP TABLE IF EXISTS `xb_app_tags`;
CREATE TABLE `xb_app_tags`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签名称',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `menu_title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单标题',
  `sort` int(11) NULL DEFAULT 100 COMMENT '排序',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '状态：10禁用，20启用',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '应用-标签单页' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_app_tags
-- ----------------------------

-- ----------------------------
-- Table structure for xb_platforms
-- ----------------------------
DROP TABLE IF EXISTS `xb_platforms`;
CREATE TABLE `xb_platforms`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `platform_type` enum('miniwechat','wechat','douyin','h5','app','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-平台' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_platforms
-- ----------------------------

-- ----------------------------
-- Table structure for xb_projects
-- ----------------------------
DROP TABLE IF EXISTS `xb_projects`;
CREATE TABLE `xb_projects`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '管理员ID',
  `platform_id` int(11) NULL DEFAULT NULL COMMENT '所属平台',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '项目名称',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用标识',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '项目LOGO',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '项目状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-项目' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_projects
-- ----------------------------

-- ----------------------------
-- Table structure for xb_settings
-- ----------------------------
DROP TABLE IF EXISTS `xb_settings`;
CREATE TABLE `xb_settings`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组名称',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据值',
  `saas_appid` int(10) NULL DEFAULT NULL COMMENT '应用ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-配置项' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_settings
-- ----------------------------

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
  `size` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件大小',
  `adapter` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '选定器：aliyun阿里云，qcloud腾讯云，qiniu七牛云',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-附件管理器' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_upload
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-附件分类' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_upload_cate
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
