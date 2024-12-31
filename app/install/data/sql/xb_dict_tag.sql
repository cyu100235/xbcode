DROP TABLE IF EXISTS `xb_dict_tag`;
CREATE TABLE `xb_dict_tag`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '名称',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标识',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  `state` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '字典标签' ROW_FORMAT = DYNAMIC;
INSERT INTO `xb_dict_tag` VALUES (1, '2024-05-29 14:37:04', '2024-12-02 06:23:31', '封禁状态文字', 'banText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (2, '2024-05-29 14:37:34', '2024-12-02 06:23:26', '封禁状态样式', 'banStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (3, '2024-05-29 14:40:12', '2024-12-02 06:23:17', '组件类型文字', 'componentText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (4, '2024-05-29 14:43:38', '2024-12-02 06:23:23', '组件类型样式', 'componentStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (5, '2024-05-29 14:53:27', '2024-12-02 17:08:52', '请求类型文字', 'methodsText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (6, '2024-05-29 14:54:43', '2024-12-02 17:09:01', '请求类型样式', 'methodsStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (7, '2024-05-29 15:02:22', '2024-12-02 17:10:11', '显示状态文字', 'showText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (8, '2024-05-29 15:02:54', '2024-12-02 17:10:16', '显示状态样式', 'showStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (9, '2024-05-29 15:03:34', '2024-12-02 19:12:06', '禁用状态文字', 'stateText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (10, '2024-05-29 15:03:58', '2024-12-02 19:12:11', '禁用状态样式', 'stateStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (11, '2024-05-29 15:07:01', '2024-12-02 19:16:44', '附件类型文字', 'uploadFileText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (12, '2024-05-29 15:08:38', '2024-12-02 19:16:49', '附件类型样式', 'uploadFileStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (13, '2024-05-29 15:09:14', '2024-12-02 19:16:53', '附加类型格式', 'uploadFileFormat', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (14, '2024-05-29 15:12:19', '2024-06-01 14:50:25', '是否状态-文字', 'yesNoText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (15, '2024-05-29 15:12:46', '2024-06-01 14:50:00', '是否状态-样式', 'yesNoStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (16, '2024-08-06 15:31:39', '2024-12-02 06:17:43', '菜单类型文字', 'menuTypeText', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (17, '2024-08-06 17:42:27', '2024-12-02 06:18:29', '菜单类型样式', 'menuTypeStyle', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (18, '2024-11-08 00:23:08', '2024-11-08 00:23:08', '协议类型', 'protocolType', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (19, '2024-11-14 16:41:47', '2024-11-14 16:41:47', '平台渠道', 'platformChangels', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (20, '2024-11-15 11:29:33', '2024-12-02 19:50:17', '增减类型', 'dataActionType', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (21, '2024-11-15 12:37:55', '2024-11-15 12:37:55', '是否支付', 'payState', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (22, '2024-11-15 17:31:04', '2024-12-06 17:54:03', '上下架状态', 'upState', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (23, '2024-11-18 02:11:31', '2024-11-18 02:11:31', '开关状态', 'switchState', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (24, '2024-11-18 12:24:38', '2024-11-18 13:38:45', '短信通知类型', 'smsNoticeType', 0, '20');
INSERT INTO `xb_dict_tag` VALUES (25, '2024-12-06 17:55:30', '2024-12-06 17:55:30', '使用状态', 'useState', 0, '20');