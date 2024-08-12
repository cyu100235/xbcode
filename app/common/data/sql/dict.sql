DROP TABLE IF EXISTS `xb_dict`;
CREATE TABLE `xb_dict`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '名称',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标识',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-字典数据' ROW_FORMAT = DYNAMIC;

INSERT INTO `xb_dict` VALUES (1, '2024-05-29 14:37:04', '2024-05-29 14:44:04', '封禁状态-文字', 'banText', '10=封禁\n|20=正常', 0);
INSERT INTO `xb_dict` VALUES (2, '2024-05-29 14:37:34', '2024-06-01 14:52:40', '封禁状态-样式', 'banStyle', '10=error|20=success', 0);
INSERT INTO `xb_dict` VALUES (3, '2024-05-29 14:40:12', '2024-06-01 14:52:57', '组件类型-文字', 'componentText', 'none/index=不使用组件|form/index=表单组件|table/index=表格组件|remote/index=远程组件', 0);
INSERT INTO `xb_dict` VALUES (4, '2024-05-29 14:43:38', '2024-06-01 14:52:54', '组件类型-样式', 'componentStyle', 'none/index=|form/index=warning|table/index=success|remote/index=info', 0);
INSERT INTO `xb_dict` VALUES (5, '2024-05-29 14:53:27', '2024-06-01 14:50:49', '请求类型-文字', 'methodsText', 'GET|POST|PUT|DELETE', 0);
INSERT INTO `xb_dict` VALUES (6, '2024-05-29 14:54:43', '2024-06-01 14:50:47', '请求类型-样式', 'methodsStyle', 'GET=info|POST=success|PUT=warning|DELETE=danger', 0);
INSERT INTO `xb_dict` VALUES (7, '2024-05-29 15:02:22', '2024-06-01 14:50:44', '显示状态-文字', 'showText', '10=隐藏|20=显示', 0);
INSERT INTO `xb_dict` VALUES (8, '2024-05-29 15:02:54', '2024-06-01 14:50:42', '显示状态-样式', 'showStyle', '10=danger|20=success', 0);
INSERT INTO `xb_dict` VALUES (9, '2024-05-29 15:03:34', '2024-06-01 14:50:40', '禁用状态-文字', 'stateText', '10=禁用|20=正常', 0);
INSERT INTO `xb_dict` VALUES (10, '2024-05-29 15:03:58', '2024-06-01 14:50:38', '禁用状态-样式', 'stateStyle', '10=danger|20=success', 0);
INSERT INTO `xb_dict` VALUES (11, '2024-05-29 15:07:01', '2024-06-01 14:50:35', '附件类型-文字', 'uploadFileText', 'image=图片类型|video=视频类型|doc=文档类型|audio音频类型|font=字体类型|zip=压缩包类', 0);
INSERT INTO `xb_dict` VALUES (12, '2024-05-29 15:08:38', '2024-06-01 14:50:33', '附件类型-样式', 'uploadFileStyle', 'image=|video=success|doc=info|audio=|font=info|zip=warning', 0);
INSERT INTO `xb_dict` VALUES (13, '2024-05-29 15:09:14', '2024-06-01 14:50:31', '附加类型-格式', 'uploadFileFormat', 'image=jpg,jpeg,png,gif|video=mp4,avi,rmvb,mkv,flv|doc=doc,docx,xls,xlsx,ppt,pptx,pdf,txt|audio=mp3,wav,flac,ape,alac|font=ttf,otf,woff,woff2|zip=zip,rar,7z,tar,gz,bz2', 0);
INSERT INTO `xb_dict` VALUES (14, '2024-05-29 15:12:19', '2024-06-01 14:50:25', '是否状态-文字', 'yesNoText', '10=否|20=是', 0);
INSERT INTO `xb_dict` VALUES (15, '2024-05-29 15:12:46', '2024-06-01 14:50:00', '是否状态-样式', 'yesNoStyle', '10=danger|20=success', 0);
INSERT INTO `xb_dict` VALUES (16, '2024-08-06 15:31:39', '2024-08-06 15:31:39', '菜单类型', 'menuTypeText', '10=目录|20=菜单|30=按钮', 0);
INSERT INTO `xb_dict` VALUES (17, '2024-08-06 17:42:27', '2024-08-06 17:49:33', '菜单类型-样式', 'menuTypeStyle', '10=|20=success|30=warning', 0);