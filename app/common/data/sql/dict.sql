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
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-字典数据' ROW_FORMAT = Dynamic;

INSERT INTO `xb_dict` VALUES (1, '2024-05-29 14:37:04', '2024-05-29 14:44:04', '封禁状态-文字', 'banText', '10=封禁\n20=正常', 0);
INSERT INTO `xb_dict` VALUES (2, '2024-05-29 14:37:34', '2024-05-29 15:38:35', '封禁状态-样式', 'banStyle', '10=error\n20=success', 0);
INSERT INTO `xb_dict` VALUES (3, '2024-05-29 14:40:12', '2024-05-29 14:43:51', '组件类型-文字', 'componentText', 'none/index=不使用组件\nform/index=表单组件\ntable/index=表格组件\nremote/index=远程组件', 0);
INSERT INTO `xb_dict` VALUES (4, '2024-05-29 14:43:38', '2024-05-29 14:43:38', '组件类型-样式', 'componentStyle', 'none/index=\nform/index=warning\ntable/index=success\nremote/index=info', 0);
INSERT INTO `xb_dict` VALUES (5, '2024-05-29 14:53:27', '2024-05-29 14:53:38', '请求类型-文字', 'methodsText', 'GET\nPOST\nPUT\nDELETE', 0);
INSERT INTO `xb_dict` VALUES (6, '2024-05-29 14:54:43', '2024-05-29 14:54:43', '请求类型-样式', 'methodsStyle', 'GET=info\nPOST=success\nPUT=warning\nDELETE=danger', 0);
INSERT INTO `xb_dict` VALUES (7, '2024-05-29 15:02:22', '2024-05-29 15:02:22', '显示状态-文字', 'showText', '10=隐藏\n20=显示', 0);
INSERT INTO `xb_dict` VALUES (8, '2024-05-29 15:02:54', '2024-05-29 15:02:54', '显示状态-样式', 'showStyle', '10=danger\n20=success', 0);
INSERT INTO `xb_dict` VALUES (9, '2024-05-29 15:03:34', '2024-05-29 15:03:34', '禁用状态-文字', 'stateText', '10=禁用\n20=正常', 0);
INSERT INTO `xb_dict` VALUES (10, '2024-05-29 15:03:58', '2024-05-29 15:03:58', '禁用状态-样式', 'stateStyle', '10=danger\n20=success', 0);
INSERT INTO `xb_dict` VALUES (11, '2024-05-29 15:07:01', '2024-05-29 15:07:01', '附件类型-文字', 'uploadFileText', 'image=图片类型\nvideo=视频类型\ndoc=文档类型\naudio音频类型\nfont=字体类型\nzip=压缩包类', 0);
INSERT INTO `xb_dict` VALUES (12, '2024-05-29 15:08:38', '2024-05-29 15:08:47', '附件类型-样式', 'uploadFileStyle', 'image=\nvideo=success\ndoc=info\naudio=\nfont=info\nzip=warning', 0);
INSERT INTO `xb_dict` VALUES (13, '2024-05-29 15:09:14', '2024-05-29 15:11:39', '附加类型-格式', 'uploadFileFormat', 'image=jpg,jpeg,png,gif\nvideo=mp4,avi,rmvb,mkv,flv\ndoc=doc,docx,xls,xlsx,ppt,pptx,pdf,txt\naudio=mp3,wav,flac,ape,alac\nfont=ttf,otf,woff,woff2\nzip=zip,rar,7z,tar,gz,bz2', 0);
INSERT INTO `xb_dict` VALUES (14, '2024-05-29 15:12:19', '2024-05-29 15:12:19', '是否状态-文字', 'yesNoText', '10=否\n20=是', 0);
INSERT INTO `xb_dict` VALUES (15, '2024-05-29 15:12:46', '2024-05-29 15:39:00', '是否状态-样式', 'yesNoStyle', '10=danger\n20=success', 0);
