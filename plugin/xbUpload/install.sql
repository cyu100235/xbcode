-- 删除表语句
DROP TABLE IF EXISTS `xb_upload_engine`;
-- 表结构：xb_upload_engine
CREATE TABLE `xb_upload_engine` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  `title` varchar(50) DEFAULT NULL COMMENT '引擎名称',
  `plugin` varchar(50) DEFAULT NULL COMMENT '插件标识',
  `name` varchar(50) DEFAULT NULL COMMENT '引擎标识',
  `desc` varchar(50) DEFAULT NULL COMMENT '引擎描述',
  `prompt` varchar(100) DEFAULT NULL COMMENT '引擎提示词',
  `sort` int(11) DEFAULT '0' COMMENT '分类排序',
  `is_system` enum('10','20') DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件引擎';
-- 删除表语句
DROP TABLE IF EXISTS `xb_upload`;
-- 表结构：xb_upload
CREATE TABLE `xb_upload` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '上传时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  `cid` int(11) DEFAULT '0' COMMENT '分类ID 0全部',
  `uid` int(11) DEFAULT '-1' COMMENT '用户ID 0后台，否则用户',
  `title` varchar(100) DEFAULT NULL COMMENT '附件名称',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名称',
  `md5` varchar(255) DEFAULT NULL COMMENT '文件指纹',
  `uri` varchar(255) DEFAULT NULL COMMENT '文件地址',
  `format` varchar(30) DEFAULT NULL COMMENT '文件格式',
  `size` int(11) DEFAULT NULL COMMENT '文件大小，单位：字节',
  `adapter` varchar(50) DEFAULT NULL COMMENT '储存选定器',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件记录';
-- 删除表语句
DROP TABLE IF EXISTS `xb_upload_engine`;
-- 表结构：xb_upload_engine
CREATE TABLE `xb_upload_engine` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  `title` varchar(50) DEFAULT NULL COMMENT '引擎名称',
  `plugin` varchar(50) DEFAULT NULL COMMENT '插件标识',
  `name` varchar(50) DEFAULT NULL COMMENT '引擎标识',
  `desc` varchar(50) DEFAULT NULL COMMENT '引擎描述',
  `prompt` varchar(100) DEFAULT NULL COMMENT '引擎提示词',
  `sort` int(11) DEFAULT '0' COMMENT '分类排序',
  `is_system` enum('10','20') DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件引擎';
-- 删除表语句
DROP TABLE IF EXISTS `xb_upload_cate`;
-- 表结构：xb_upload_cate
CREATE TABLE `xb_upload_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  `title` varchar(50) DEFAULT NULL COMMENT '分类名称',
  `dir_name` varchar(50) DEFAULT NULL COMMENT '分类目录',
  `sort` int(11) DEFAULT '0' COMMENT '分类排序',
  `is_system` enum('10','20') DEFAULT '10' COMMENT '是否系统：10否，20是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件分类';
-- 删除表语句
DROP TABLE IF EXISTS `xb_upload`;
-- 表结构：xb_upload
CREATE TABLE `xb_upload` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `create_at` datetime DEFAULT NULL COMMENT '上传时间',
  `update_at` datetime DEFAULT NULL COMMENT '更新时间',
  `saas_appid` int(11) DEFAULT NULL COMMENT '站点ID',
  `cid` int(11) DEFAULT '0' COMMENT '分类ID 0全部',
  `uid` int(11) DEFAULT '-1' COMMENT '用户ID 0后台，否则用户',
  `title` varchar(100) DEFAULT NULL COMMENT '附件名称',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名称',
  `md5` varchar(255) DEFAULT NULL COMMENT '文件指纹',
  `uri` varchar(255) DEFAULT NULL COMMENT '文件地址',
  `format` varchar(30) DEFAULT NULL COMMENT '文件格式',
  `size` int(11) DEFAULT NULL COMMENT '文件大小，单位：字节',
  `adapter` varchar(50) DEFAULT NULL COMMENT '储存选定器',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='附件记录';
