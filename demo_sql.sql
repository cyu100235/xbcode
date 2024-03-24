-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-03-25 03:43:49
-- 服务器版本： 5.7.40-log
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `video_xb_cloud`
--

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_article`
--

CREATE TABLE `xb_demo_article` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `cate_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `title` varchar(50) DEFAULT NULL COMMENT '文章标题',
  `view` int(11) DEFAULT '0' COMMENT '浏览量',
  `logo` varchar(255) DEFAULT NULL COMMENT '文章封面',
  `content` text COMMENT '图文内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-文章记录' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_article`
--

INSERT INTO `xb_demo_article` (`id`, `create_at`, `update_at`, `saas_appid`, `cate_id`, `title`, `view`, `logo`, `content`) VALUES
(1, '2024-02-04 05:49:06', '2024-02-04 05:52:55', NULL, 1, 'dsa', 0, 'uploads/default/20240201/b9c3fe5690f8330d80abae98c9db476e.jpg', '<p>dsad</p>'),
(2, '2024-02-04 05:49:55', '2024-02-04 05:49:55', NULL, 1, 'dsad', 0, 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', '<p>dsada</p>');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_article_cate`
--

CREATE TABLE `xb_demo_article_cate` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `title` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '分类图标',
  `sort` int(11) DEFAULT '0' COMMENT '分类排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-文章分类' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_article_cate`
--

INSERT INTO `xb_demo_article_cate` (`id`, `create_at`, `update_at`, `saas_appid`, `title`, `logo`, `sort`) VALUES
(1, '2024-02-04 04:08:22', '2024-02-04 04:09:48', NULL, '测试', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100),
(2, '2024-02-04 07:00:43', '2024-02-04 07:00:43', 28, '测试', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100);

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_goods`
--

CREATE TABLE `xb_demo_goods` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `cate_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `sort` int(11) DEFAULT '0' COMMENT '商品排序',
  `title` varchar(50) DEFAULT NULL COMMENT '商品标题',
  `desc` varchar(255) DEFAULT NULL COMMENT '卖点描述',
  `logo` varchar(255) DEFAULT NULL COMMENT '商品封面',
  `content` text COMMENT '图文内容',
  `delivery_id` int(11) DEFAULT NULL COMMENT '运费模板ID',
  `spec_type` enum('10','20') DEFAULT NULL COMMENT '规格类型：10单规格，20多规格',
  `sales_volume` int(11) DEFAULT NULL COMMENT '初始销量',
  `stock_type` enum('10','20') DEFAULT NULL COMMENT '10下单减库存，20付款减库存',
  `integral_switch` enum('10','20') DEFAULT NULL COMMENT '积分赠送：10关闭，20开启',
  `integral_use_switch` enum('10','20') DEFAULT NULL COMMENT '积分抵扣：10关闭，20开启',
  `integral_use_money` decimal(10,2) DEFAULT '0.00' COMMENT '满足金额抵扣',
  `integral_use_max_money` decimal(10,2) DEFAULT '0.00' COMMENT '最高抵扣百分比（按商品金额）',
  `grade_discount_switch` enum('10','20') DEFAULT NULL COMMENT '用户等级折扣：10关闭，20开启',
  `dealer_switch` enum('10','20') DEFAULT NULL COMMENT '单独分销：10关闭，20开启',
  `dealer_money_type` enum('10','20') DEFAULT NULL COMMENT '佣金类型：10百分比，20固定金额',
  `dealer_first_money` decimal(10,2) DEFAULT '0.00' COMMENT '分销佣金（一级）',
  `dealer_second_money` decimal(10,2) DEFAULT '0.00' COMMENT '分销佣金（二级）',
  `dealer_third_money` decimal(10,2) DEFAULT '0.00' COMMENT '分销佣金（三级）',
  `status` enum('10','20') DEFAULT '10' COMMENT '销售状态：10已下架，20销售中'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-商品记录' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_goods`
--

INSERT INTO `xb_demo_goods` (`id`, `create_at`, `update_at`, `saas_appid`, `cate_id`, `sort`, `title`, `desc`, `logo`, `content`, `delivery_id`, `spec_type`, `sales_volume`, `stock_type`, `integral_switch`, `integral_use_switch`, `integral_use_money`, `integral_use_max_money`, `grade_discount_switch`, `dealer_switch`, `dealer_money_type`, `dealer_first_money`, `dealer_second_money`, `dealer_third_money`, `status`) VALUES
(7, '2024-02-09 21:58:42', '2024-02-09 21:58:42', 28, 2, 0, 'dsadsa', '', 'uploads/default/20240206/0ce17ad103f44f77675ad428da45573a.png', '<p><br></p>', NULL, '10', 0, '10', '10', '10', '0.00', '0.00', '10', '10', '10', '0.00', '0.00', '0.00', '20');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_goods_cate`
--

CREATE TABLE `xb_demo_goods_cate` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `pid` int(11) DEFAULT '0' COMMENT '父级ID',
  `title` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '分类图标',
  `sort` int(11) DEFAULT '0' COMMENT '分类排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-商品分类' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_goods_cate`
--

INSERT INTO `xb_demo_goods_cate` (`id`, `create_at`, `update_at`, `saas_appid`, `pid`, `title`, `logo`, `sort`) VALUES
(1, '2024-02-07 21:03:56', '2024-02-07 21:03:56', 28, 0, '手机', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100),
(2, '2024-02-07 21:04:07', '2024-02-07 21:04:07', 28, 1, '华为', 'uploads/default/20240201/960ef0e8559e2efba504888a354f2d80.png', 100),
(3, '2024-02-07 21:04:20', '2024-02-07 21:04:20', 28, 1, '金立', 'uploads/default/20240206/100fec40ef3e3f7c319f2ab04e100865.png', 100),
(4, '2024-02-07 21:04:35', '2024-02-07 21:04:35', 28, 1, 'OPPO', 'uploads/default/20240206/63dcef2400573897b435aae459a81be7.png', 100);

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_goods_preview`
--

CREATE TABLE `xb_demo_goods_preview` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品ID',
  `image_url` varchar(255) DEFAULT NULL COMMENT '图片路径'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-商品预览图' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_goods_preview`
--

INSERT INTO `xb_demo_goods_preview` (`id`, `create_at`, `saas_appid`, `goods_id`, `image_url`) VALUES
(7, '2024-02-09 21:58:42', 28, 7, 'uploads/default/20240206/b9ebb4dbcf36e0eb185a581629e684e2.png');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_goods_sku`
--

CREATE TABLE `xb_demo_goods_sku` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品ID',
  `spec_id` int(11) DEFAULT NULL COMMENT '规格名ID',
  `value_id` int(11) DEFAULT NULL COMMENT '规格值ID',
  `goods_no` varchar(255) DEFAULT NULL COMMENT '商品编码',
  `original_price` decimal(10,2) DEFAULT NULL COMMENT '市场价格',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT '销售价格',
  `stock_num` int(11) DEFAULT NULL COMMENT '当前库存',
  `goods_weight` decimal(10,2) DEFAULT NULL COMMENT '商品重量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-商品SKU' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_goods_sku`
--

INSERT INTO `xb_demo_goods_sku` (`id`, `create_at`, `saas_appid`, `goods_id`, `spec_id`, `value_id`, `goods_no`, `original_price`, `sale_price`, `stock_num`, `goods_weight`) VALUES
(1, '2024-02-09 21:58:42', 28, 7, 3, 2, 'dsadas', '20.00', '10.00', 30, '40.00'),
(2, '2024-02-09 21:58:42', 28, 7, 4, 3, 'dsad', '20.00', '10.00', 30, '40.00'),
(3, '2024-02-09 21:58:42', 28, 7, 5, 4, 'dsad', '20.00', '10.00', 30, '40.00'),
(4, '2024-02-09 21:58:42', 28, 7, 5, 5, 'dsadasd', '20.00', '10.00', 30, '40.00'),
(5, '2024-02-09 21:58:42', 28, 7, 5, 6, 'sada', '20.00', '10.00', 30, '40.00'),
(6, '2024-02-09 21:58:42', 28, 7, 5, 7, 'dsadsa', '20.00', '10.00', 30, '40.00'),
(7, '2024-02-09 21:58:42', 28, 7, 4, 8, 'dsad', '20.00', '10.00', 30, '40.00'),
(8, '2024-02-09 21:58:42', 28, 7, 4, 9, 'dsadsa', '20.00', '10.00', 30, '40.00'),
(9, '2024-02-09 21:58:42', 28, 7, 3, 10, 'dsadsa', '20.00', '10.00', 30, '40.00');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_goods_spec_label`
--

CREATE TABLE `xb_demo_goods_spec_label` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `name` varchar(255) DEFAULT NULL COMMENT '规格名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-规格名称' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_goods_spec_label`
--

INSERT INTO `xb_demo_goods_spec_label` (`id`, `create_at`, `saas_appid`, `name`) VALUES
(3, '2024-02-09 21:58:42', 28, '类型'),
(4, '2024-02-09 21:58:42', 28, '颜色'),
(5, '2024-02-09 21:58:42', 28, '尺寸');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_goods_spec_value`
--

CREATE TABLE `xb_demo_goods_spec_value` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `spec_id` int(11) DEFAULT NULL COMMENT '规格名ID',
  `name` varchar(255) DEFAULT NULL COMMENT '规格名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-规格数据' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_goods_spec_value`
--

INSERT INTO `xb_demo_goods_spec_value` (`id`, `create_at`, `saas_appid`, `spec_id`, `name`) VALUES
(2, '2024-02-09 21:58:42', 28, 3, '有线型'),
(3, '2024-02-09 21:58:42', 28, 4, '蓝色'),
(4, '2024-02-09 21:58:42', 28, 5, 'S'),
(5, '2024-02-09 21:58:42', 28, 5, 'M'),
(6, '2024-02-09 21:58:42', 28, 5, 'L'),
(7, '2024-02-09 21:58:42', 28, 5, 'XL'),
(8, '2024-02-09 21:58:42', 28, 4, '紫色'),
(9, '2024-02-09 21:58:42', 28, 4, '红色'),
(10, '2024-02-09 21:58:42', 28, 3, '无线型');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_order`
--

CREATE TABLE `xb_demo_order` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `goods_id` varchar(255) DEFAULT NULL COMMENT '商品ID',
  `goods_unit_price` int(10) DEFAULT '0' COMMENT '商品单价',
  `reality_price` decimal(10,2) DEFAULT NULL COMMENT '实付金额',
  `total` decimal(10,2) DEFAULT '0.00' COMMENT '订单总额',
  `num` int(11) DEFAULT NULL COMMENT '购买数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-订单记录' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_order_service`
--

CREATE TABLE `xb_demo_order_service` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `title` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-订单售后' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_shop`
--

CREATE TABLE `xb_demo_shop` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `title` varchar(100) DEFAULT NULL COMMENT '门店名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '门店图标',
  `desc` varchar(255) DEFAULT NULL COMMENT '门店简介',
  `contact` varchar(30) DEFAULT NULL COMMENT '联系人',
  `phone` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `business_hours` varchar(255) DEFAULT NULL COMMENT '营业时间',
  `province_id` int(11) DEFAULT NULL COMMENT '省级ID',
  `city_id` int(11) DEFAULT NULL COMMENT '市级ID',
  `region_id` int(11) DEFAULT NULL COMMENT '区域ID',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `longitude` varchar(255) DEFAULT NULL COMMENT '经度',
  `latitude` varchar(255) DEFAULT NULL COMMENT '纬度',
  `sort` int(11) DEFAULT '0' COMMENT '门店排序(数字越小越靠前)',
  `is_check` enum('10','20') DEFAULT '10' COMMENT '自提核销：10否，20是',
  `status` enum('10','20') DEFAULT '10' COMMENT '门店状态：10禁用，20正常'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-门店记录' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_shop`
--

INSERT INTO `xb_demo_shop` (`id`, `create_at`, `update_at`, `saas_appid`, `title`, `logo`, `desc`, `contact`, `phone`, `business_hours`, `province_id`, `city_id`, `region_id`, `address`, `longitude`, `latitude`, `sort`, `is_check`, `status`) VALUES
(2, '2024-02-10 23:22:07', '2024-02-10 23:36:30', 28, '测试门店', 'uploads/default/20240206/100fec40ef3e3f7c319f2ab04e100865.png', 'dsaa', 'dsa', 'dsadas', 'dsadsa', NULL, NULL, NULL, '', NULL, NULL, 100, '10', '20');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_shop_clerk`
--

CREATE TABLE `xb_demo_shop_clerk` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '主键',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `saas_appid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '项目ID',
  `shop_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属门店id',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `real_name` varchar(30) NOT NULL DEFAULT '' COMMENT '店员姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` enum('10','20') NOT NULL DEFAULT '10' COMMENT '状态：10禁用，20正常'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='演示-门店员工' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_shop_order`
--

CREATE TABLE `xb_demo_shop_order` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '主键ID',
  `create_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `saas_appid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '项目ID',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单ID',
  `shop_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '门店ID',
  `clerk_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '核销员ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='演示-门店核销' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_single_page`
--

CREATE TABLE `xb_demo_single_page` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `title` varchar(50) DEFAULT NULL COMMENT '单页标题',
  `menu_title` varchar(50) DEFAULT NULL COMMENT '菜单名称',
  `name` varchar(50) DEFAULT NULL COMMENT '单页标识',
  `view` int(11) DEFAULT '0' COMMENT '浏览量',
  `content` text COMMENT '图文内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-单页内容' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_single_page`
--

INSERT INTO `xb_demo_single_page` (`id`, `create_at`, `update_at`, `saas_appid`, `title`, `menu_title`, `name`, `view`, `content`) VALUES
(1, '2024-02-04 03:13:48', '2024-02-04 03:58:02', NULL, 'dsda', '测试555', 'dsa888', 0, '<p>dsadsa</p>');

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_user`
--

CREATE TABLE `xb_demo_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `username` varchar(50) DEFAULT NULL COMMENT '登录账号',
  `password` varchar(255) DEFAULT NULL COMMENT '登录密码',
  `nickname` varchar(50) DEFAULT '0' COMMENT '用户昵称',
  `status` enum('10','20') DEFAULT NULL COMMENT '10冻结，20正常',
  `balance` decimal(10,0) DEFAULT '0' COMMENT '用户余额',
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `grade_id` int(11) DEFAULT NULL COMMENT '用户等级ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-用户记录' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_user_bill`
--

CREATE TABLE `xb_demo_user_bill` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `saas_appid` int(11) DEFAULT NULL COMMENT '项目ID',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `bill_type` enum('10','20','30') DEFAULT '10' COMMENT '账单状态：10消费，20充值，30无变动',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '当前变动',
  `new_money` decimal(10,2) DEFAULT '0.00' COMMENT '变动后',
  `old_money` decimal(10,2) DEFAULT '0.00' COMMENT '变动前',
  `remarks` varchar(255) DEFAULT NULL COMMENT '变动理由'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户-账单记录' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `xb_demo_user_grade`
--

CREATE TABLE `xb_demo_user_grade` (
  `id` int(10) UNSIGNED NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `level` int(11) DEFAULT NULL COMMENT '等级权重',
  `use_money` decimal(10,2) DEFAULT '0.00' COMMENT '升级条件（消费满足金额）',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '等级权益（折扣）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='演示-会员等级' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `xb_demo_user_grade`
--

INSERT INTO `xb_demo_user_grade` (`id`, `create_at`, `update_at`, `title`, `level`, `use_money`, `discount`) VALUES
(1, '2024-02-04 06:38:32', '2024-02-04 06:43:52', '5555', 1, '1.00', '5.00'),
(2, '2024-02-04 06:39:12', '2024-02-04 06:44:02', '3333', 2, '55.00', '55.00');

--
-- 转储表的索引
--

--
-- 表的索引 `xb_demo_article`
--
ALTER TABLE `xb_demo_article`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_article_cate`
--
ALTER TABLE `xb_demo_article_cate`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_goods`
--
ALTER TABLE `xb_demo_goods`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_goods_cate`
--
ALTER TABLE `xb_demo_goods_cate`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_goods_preview`
--
ALTER TABLE `xb_demo_goods_preview`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_goods_sku`
--
ALTER TABLE `xb_demo_goods_sku`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_goods_spec_label`
--
ALTER TABLE `xb_demo_goods_spec_label`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_goods_spec_value`
--
ALTER TABLE `xb_demo_goods_spec_value`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_order`
--
ALTER TABLE `xb_demo_order`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_order_service`
--
ALTER TABLE `xb_demo_order_service`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_shop`
--
ALTER TABLE `xb_demo_shop`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_shop_clerk`
--
ALTER TABLE `xb_demo_shop_clerk`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_shop_order`
--
ALTER TABLE `xb_demo_shop_order`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_single_page`
--
ALTER TABLE `xb_demo_single_page`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_user`
--
ALTER TABLE `xb_demo_user`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_user_bill`
--
ALTER TABLE `xb_demo_user_bill`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `xb_demo_user_grade`
--
ALTER TABLE `xb_demo_user_grade`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `xb_demo_article`
--
ALTER TABLE `xb_demo_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `xb_demo_article_cate`
--
ALTER TABLE `xb_demo_article_cate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `xb_demo_goods`
--
ALTER TABLE `xb_demo_goods`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `xb_demo_goods_cate`
--
ALTER TABLE `xb_demo_goods_cate`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `xb_demo_goods_preview`
--
ALTER TABLE `xb_demo_goods_preview`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `xb_demo_goods_sku`
--
ALTER TABLE `xb_demo_goods_sku`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `xb_demo_goods_spec_label`
--
ALTER TABLE `xb_demo_goods_spec_label`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `xb_demo_goods_spec_value`
--
ALTER TABLE `xb_demo_goods_spec_value`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `xb_demo_order`
--
ALTER TABLE `xb_demo_order`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `xb_demo_order_service`
--
ALTER TABLE `xb_demo_order_service`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `xb_demo_shop`
--
ALTER TABLE `xb_demo_shop`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `xb_demo_shop_clerk`
--
ALTER TABLE `xb_demo_shop_clerk`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键';

--
-- 使用表AUTO_INCREMENT `xb_demo_shop_order`
--
ALTER TABLE `xb_demo_shop_order`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID';

--
-- 使用表AUTO_INCREMENT `xb_demo_single_page`
--
ALTER TABLE `xb_demo_single_page`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `xb_demo_user`
--
ALTER TABLE `xb_demo_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `xb_demo_user_bill`
--
ALTER TABLE `xb_demo_user_bill`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `xb_demo_user_grade`
--
ALTER TABLE `xb_demo_user_grade`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
