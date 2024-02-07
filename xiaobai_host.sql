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

 Date: 07/02/2024 15:22:50
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
INSERT INTO `xb_admin` VALUES (33, '2024-02-01 06:53:32', '2024-02-06 07:18:43', 30, 28, 0, 'admin', '$2y$10$kGwctFPsjhbOVJnBu5Xx2u0gZfpGQzhlW3fqK4DuznZvAxjYAp.J6', '20', '开发专用管理员', '117.188.206.160', '2024-02-06 07:18:43', '', '20');

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
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `cate_id` int(11) NULL DEFAULT NULL COMMENT '分类ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文章标题',
  `view` int(11) NULL DEFAULT 0 COMMENT '浏览量',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文章封面',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图文内容',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-文章记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_article
-- ----------------------------
INSERT INTO `xb_demo_article` VALUES (1, '2024-02-04 05:49:06', '2024-02-04 05:52:55', NULL, 1, 'dsa', 0, 'uploads/default/20240201/b9c3fe5690f8330d80abae98c9db476e.jpg', '<p>dsad</p>');
INSERT INTO `xb_demo_article` VALUES (2, '2024-02-04 05:49:55', '2024-02-04 05:49:55', NULL, 1, 'dsad', 0, 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', '<p>dsada</p>');

-- ----------------------------
-- Table structure for xb_demo_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_article_cate`;
CREATE TABLE `xb_demo_article_cate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类图标',
  `sort` int(11) NULL DEFAULT 0 COMMENT '分类排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-文章分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_article_cate
-- ----------------------------
INSERT INTO `xb_demo_article_cate` VALUES (1, '2024-02-04 04:08:22', '2024-02-04 04:09:48', NULL, '测试', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100);
INSERT INTO `xb_demo_article_cate` VALUES (2, '2024-02-04 07:00:43', '2024-02-04 07:00:43', 28, '测试', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100);

-- ----------------------------
-- Table structure for xb_demo_goods
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods`;
CREATE TABLE `xb_demo_goods`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `cate_id` int(11) NULL DEFAULT NULL COMMENT '分类ID',
  `sort` int(11) NULL DEFAULT 0 COMMENT '商品排序',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '卖点描述',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品封面',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图文内容',
  `delivery_id` int(11) NULL DEFAULT NULL COMMENT '运费模板ID',
  `spec_type` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '规格类型：10单规格，20多规格',
  `sales_volume` int(11) NULL DEFAULT NULL COMMENT '初始销量',
  `stock_type` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '10下单减库存，20付款减库存',
  `integral_switch` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '积分赠送：10关闭，20开启',
  `integral_use_switch` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '积分抵扣：10关闭，20开启',
  `integral_use_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '满足金额抵扣',
  `integral_use_max_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '最高抵扣百分比（按商品金额）',
  `grade_discount_switch` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户等级折扣：10关闭，20开启',
  `dealer_switch` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '单独分销：10关闭，20开启',
  `dealer_money_type` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '佣金类型：10百分比，20固定金额',
  `dealer_first_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '分销佣金（一级）',
  `dealer_second_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '分销佣金（二级）',
  `dealer_third_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '分销佣金（三级）',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '销售状态：10已下架，20销售中',
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
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `pid` int(11) NULL DEFAULT 0 COMMENT '父级ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分类图标',
  `sort` int(11) NULL DEFAULT 0 COMMENT '分类排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-商品分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_cate
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_goods_preview
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods_preview`;
CREATE TABLE `xb_demo_goods_preview`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `goods_id` int(11) NULL DEFAULT NULL COMMENT '商品ID',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片路径',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-商品预览图' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_preview
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_goods_sku
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods_sku`;
CREATE TABLE `xb_demo_goods_sku`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `goods_id` int(11) NULL DEFAULT NULL COMMENT '商品ID',
  `spec_id` int(11) NULL DEFAULT NULL COMMENT '规格ID',
  `spec_value_id` int(11) NULL DEFAULT NULL COMMENT '属性ID',
  `goods_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品编码',
  `original_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '市场价格',
  `sale_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '销售价格',
  `stock_num` int(11) NULL DEFAULT NULL COMMENT '当前库存',
  `goods_weight` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品重量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-商品库存' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_sku
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_goods_spec
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods_spec`;
CREATE TABLE `xb_demo_goods_spec`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL,
  `type_id` int(11) NULL DEFAULT NULL COMMENT '商品类型ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '规格标签名',
  `sort` int(11) NULL DEFAULT NULL COMMENT '规格排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-规格名称' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_spec
-- ----------------------------
INSERT INTO `xb_demo_goods_spec` VALUES (1, '2024-02-06 14:44:58.662419', 28, 1, '内存', 100);
INSERT INTO `xb_demo_goods_spec` VALUES (2, '2024-02-06 14:45:05.725240', 28, 1, '颜色', 100);
INSERT INTO `xb_demo_goods_spec` VALUES (3, '2024-02-06 15:04:33.258698', 28, 2, '颜色', 100);
INSERT INTO `xb_demo_goods_spec` VALUES (4, '2024-02-06 15:04:41.288770', 28, 2, '尺寸', 100);

-- ----------------------------
-- Table structure for xb_demo_goods_spec_value
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods_spec_value`;
CREATE TABLE `xb_demo_goods_spec_value`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `spec_id` int(11) NULL DEFAULT NULL COMMENT '规格名ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '属性名称',
  `sort` int(11) NULL DEFAULT NULL COMMENT '属性排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-规格数据' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_spec_value
-- ----------------------------
INSERT INTO `xb_demo_goods_spec_value` VALUES (1, '2024-02-06 14:45:19', 28, 1, '4G', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (2, '2024-02-06 14:45:51', 28, 1, '8G', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (3, '2024-02-06 14:45:56', 28, 1, '16G', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (4, '2024-02-06 14:46:02', 28, 1, '24G', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (5, '2024-02-06 14:46:12', 28, 2, '天蓝色', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (6, '2024-02-06 14:46:22', 28, 2, '海墨色', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (7, '2024-02-06 14:46:33', 28, 2, '天紫星', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (8, '2024-02-06 15:04:55', 28, 3, '红色', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (9, '2024-02-06 15:04:59', 28, 3, '紫色', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (10, '2024-02-06 15:05:04', 28, 3, '蓝色', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (11, '2024-02-06 15:05:21', 28, 3, '墨蓝色', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (12, '2024-02-06 15:05:28', 28, 4, 'S', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (13, '2024-02-06 15:05:32', 28, 4, 'M', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (14, '2024-02-06 15:05:36', 28, 4, 'L', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (15, '2024-02-06 15:05:40', 28, 4, 'XL', 100);
INSERT INTO `xb_demo_goods_spec_value` VALUES (16, '2024-02-06 15:05:44', 28, 4, 'XXL', 100);

-- ----------------------------
-- Table structure for xb_demo_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_goods_type`;
CREATE TABLE `xb_demo_goods_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类型名称',
  `sort` int(11) NULL DEFAULT NULL COMMENT '类型排序',
  `is_default` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '10非默认类型，20默认类型',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-商品类型' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_goods_type
-- ----------------------------
INSERT INTO `xb_demo_goods_type` VALUES (1, '2024-02-06 13:12:56', 28, '手机', 100, NULL);
INSERT INTO `xb_demo_goods_type` VALUES (2, '2024-02-06 14:44:31', 28, '衣服', 100, NULL);

-- ----------------------------
-- Table structure for xb_demo_order
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_order`;
CREATE TABLE `xb_demo_order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `order_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `goods_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品ID',
  `goods_unit_price` int(10) NULL DEFAULT 0 COMMENT '商品单价',
  `reality_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '实付金额',
  `total` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '订单总额',
  `num` int(11) NULL DEFAULT NULL COMMENT '购买数量',
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
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-订单售后' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_order_service
-- ----------------------------

-- ----------------------------
-- Table structure for xb_demo_single_page
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_single_page`;
CREATE TABLE `xb_demo_single_page`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '单页标题',
  `menu_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '单页标识',
  `view` int(11) NULL DEFAULT 0 COMMENT '浏览量',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '图文内容',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-单页内容' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_single_page
-- ----------------------------
INSERT INTO `xb_demo_single_page` VALUES (1, '2024-02-04 03:13:48', '2024-02-04 03:58:02', NULL, 'dsda', '测试555', 'dsa888', 0, '<p>dsadsa</p>');

-- ----------------------------
-- Table structure for xb_demo_user
-- ----------------------------
DROP TABLE IF EXISTS `xb_demo_user`;
CREATE TABLE `xb_demo_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '登录账号',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '登录密码',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '0' COMMENT '用户昵称',
  `status` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '10冻结，20正常',
  `balance` decimal(10, 0) NULL DEFAULT 0 COMMENT '用户余额',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户头像',
  `grade_id` int(11) NULL DEFAULT NULL COMMENT '用户等级ID',
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
  `saas_appid` int(11) NULL DEFAULT NULL COMMENT '项目ID',
  `uid` int(11) NULL DEFAULT NULL COMMENT '用户ID',
  `bill_type` enum('10','20','30') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '10' COMMENT '账单状态：10消费，20充值，30无变动',
  `money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '当前变动',
  `new_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '变动后',
  `old_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '变动前',
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '变动理由',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户-账单记录' ROW_FORMAT = DYNAMIC;

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
  `level` int(11) NULL DEFAULT NULL COMMENT '等级权重',
  `use_money` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '升级条件（消费满足金额）',
  `discount` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '等级权益（折扣）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '演示-会员等级' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xb_demo_user_grade
-- ----------------------------
INSERT INTO `xb_demo_user_grade` VALUES (1, '2024-02-04 06:38:32', '2024-02-04 06:43:52', '5555', 1, 1.00, 5.00);
INSERT INTO `xb_demo_user_grade` VALUES (2, '2024-02-04 06:39:12', '2024-02-04 06:44:02', '3333', 2, 55.00, 55.00);

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
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-配置记录' ROW_FORMAT = DYNAMIC;

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
INSERT INTO `xb_settings` VALUES (13, '2024-02-03 20:21:14', '2024-02-03 20:21:14', 28, 'integral_title', 'integral', '');
INSERT INTO `xb_settings` VALUES (14, '2024-02-03 20:21:14', '2024-02-03 20:21:14', 28, 'integral_desc', 'integral', '');
INSERT INTO `xb_settings` VALUES (15, '2024-02-03 20:21:14', '2024-02-03 20:21:14', 28, 'integral_give', 'integral', '10');
INSERT INTO `xb_settings` VALUES (16, '2024-02-03 20:21:14', '2024-02-03 20:21:14', 28, 'integral_use_switch', 'integral', '10');
INSERT INTO `xb_settings` VALUES (17, '2024-02-03 20:21:14', '2024-02-03 20:21:14', 28, 'integral_discount_money', 'integral', '');

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
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-附件记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of xb_upload
-- ----------------------------
INSERT INTO `xb_upload` VALUES (2, '2024-02-01 22:23:42', '2024-02-03 20:15:49', 28, NULL, 25, '1.png', '1.png', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 'png', 5592, 'local');
INSERT INTO `xb_upload` VALUES (3, '2024-02-01 22:58:36', '2024-02-01 22:58:36', 28, NULL, 25, '1.jpg', '1.jpg', 'uploads/default/20240201/b9c3fe5690f8330d80abae98c9db476e.jpg', 'jpg', 99954, 'local');
INSERT INTO `xb_upload` VALUES (5, '2024-02-03 20:16:51', '2024-02-03 20:16:51', NULL, NULL, 1, '2.jpg', '2.jpg', 'uploads/system/20240203/07deb97fb7d27a1c434f077f743bd5ad.jpg', 'jpg', 109121, 'local');
INSERT INTO `xb_upload` VALUES (6, '2024-02-03 20:16:58', '2024-02-03 20:16:58', NULL, NULL, 1, 'aaca0f5eb4d2d98a6ce6dffa99f8254b.png', 'aaca0f5eb4d2d98a6ce6dffa99f8254b.png', 'uploads/system/20240203/73d091817111a46c914f972554154a70.png', 'png', 2870, 'local');
INSERT INTO `xb_upload` VALUES (7, '2024-02-06 07:24:38', '2024-02-06 07:24:38', 28, NULL, 25, '2.png', '2.png', 'uploads/default/20240206/63dcef2400573897b435aae459a81be7.png', 'png', 4898, 'local');
INSERT INTO `xb_upload` VALUES (8, '2024-02-06 07:24:44', '2024-02-06 07:24:44', 28, NULL, 25, 'aaca0f5eb4d2d98a6ce6dffa99f8254b.png', 'aaca0f5eb4d2d98a6ce6dffa99f8254b.png', 'uploads/default/20240206/a0f53bb3bd444fcf5ab8e4f303992aae.png', 'png', 2870, 'local');
INSERT INTO `xb_upload` VALUES (9, '2024-02-06 07:24:52', '2024-02-06 07:24:52', 28, NULL, 25, 'logo-320.png', 'logo-320.png', 'uploads/default/20240206/100fec40ef3e3f7c319f2ab04e100865.png', 'png', 15424, 'local');
INSERT INTO `xb_upload` VALUES (10, '2024-02-06 07:25:04', '2024-02-06 07:25:04', 28, NULL, 25, 'top1.jpg', 'top1.jpg', 'uploads/default/20240206/efc77c0b35c505800cb44b29d35bd03d.jpg', 'jpg', 5740, 'local');
INSERT INTO `xb_upload` VALUES (11, '2024-02-06 07:25:17', '2024-02-06 07:25:17', 28, NULL, 25, '微信图片_20181022180915.png', '微信图片_20181022180915.png', 'uploads/default/20240206/1a3cda522f32b7ebb8942fbd705d154d.png', 'png', 83588, 'local');
INSERT INTO `xb_upload` VALUES (12, '2024-02-06 07:25:40', '2024-02-06 07:25:40', 28, NULL, 25, 'QQ图片20210727095143.png', 'QQ图片20210727095143.png', 'uploads/default/20240206/6373a916a9f4dd736b56e3d317224861.png', 'png', 185023, 'local');
INSERT INTO `xb_upload` VALUES (13, '2024-02-06 07:25:46', '2024-02-06 07:25:46', 28, NULL, 25, 'HC-icon.png', 'HC-icon.png', 'uploads/default/20240206/e0ee287c831b2de8aaf5e7ca1c6e45e5.png', 'png', 10572, 'local');
INSERT INTO `xb_upload` VALUES (14, '2024-02-06 07:26:00', '2024-02-06 07:26:00', 28, NULL, 25, 'resource.png', 'resource.png', 'uploads/default/20240206/b9ebb4dbcf36e0eb185a581629e684e2.png', 'png', 2870, 'local');
INSERT INTO `xb_upload` VALUES (15, '2024-02-06 07:26:05', '2024-02-06 07:26:05', 28, NULL, 25, 'icon.png', 'icon.png', 'uploads/default/20240206/0ce17ad103f44f77675ad428da45573a.png', 'png', 52295, 'local');

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
INSERT INTO `xb_upload_cate` VALUES (25, '2024-02-01 06:53:32', '2024-02-01 06:53:32', 28, NULL, '默认分类', 'default', 0, '20');

SET FOREIGN_KEY_CHECKS = 1;
