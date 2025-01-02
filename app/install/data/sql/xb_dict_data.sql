DROP TABLE IF EXISTS `xb_dict_data`;
CREATE TABLE `xb_dict_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_at` datetime NULL DEFAULT NULL,
  `update_at` datetime NULL DEFAULT NULL,
  `dict_id` int(11) NOT NULL COMMENT '字典ID',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '数据名称',
  `value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '数据参数',
  `sort` int(11) NULL DEFAULT 0 COMMENT '数据排序',
  `state` enum('10','20') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20' COMMENT '数据状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '字典数据' ROW_FORMAT = DYNAMIC;
INSERT INTO `xb_dict_data` VALUES (25, '2024-12-02 04:52:16', '2024-12-02 04:52:54', 24, '阿里云', 'aliyun', 0, '20');
INSERT INTO `xb_dict_data` VALUES (26, '2024-12-02 04:52:43', '2024-12-02 04:54:36', 24, '腾讯云', 'qcloud', 0, '20');
INSERT INTO `xb_dict_data` VALUES (27, '2024-12-02 06:13:00', '2024-12-02 06:13:00', 9, '已禁用', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (28, '2024-12-02 06:13:19', '2024-12-02 06:13:19', 9, '已启用', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (29, '2024-12-02 06:14:11', '2024-12-02 06:14:11', 16, '目录', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (30, '2024-12-02 06:14:19', '2024-12-02 06:14:19', 16, '菜单', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (31, '2024-12-02 06:14:26', '2024-12-02 06:14:26', 16, '按钮', '30', 0, '20');
INSERT INTO `xb_dict_data` VALUES (32, '2024-12-02 06:18:46', '2025-01-03 04:03:41', 3, '不使用组件', 'none/index', 2, '20');
INSERT INTO `xb_dict_data` VALUES (33, '2024-12-02 06:19:00', '2025-01-03 04:04:07', 3, '表单组件', 'form/index', 4, '20');
INSERT INTO `xb_dict_data` VALUES (34, '2024-12-02 06:19:22', '2025-01-03 04:03:59', 3, '表格组件', 'table/index', 3, '20');
INSERT INTO `xb_dict_data` VALUES (35, '2024-12-02 06:19:37', '2025-01-03 04:04:14', 3, '侧边表格', 'table/sidebar', 5, '20');
INSERT INTO `xb_dict_data` VALUES (36, '2024-12-02 06:19:56', '2025-01-03 04:04:20', 3, '远程组件', 'remote/index', 6, '20');
INSERT INTO `xb_dict_data` VALUES (37, '2024-12-02 17:00:47', '2024-12-02 17:04:33', 17, 'warning', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (38, '2024-12-02 17:00:56', '2024-12-02 17:04:44', 17, 'success', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (39, '2024-12-02 17:01:06', '2024-12-02 17:04:49', 17, 'danger', '30', 0, '20');
INSERT INTO `xb_dict_data` VALUES (40, '2024-12-02 17:09:10', '2024-12-02 17:09:10', 5, 'GET', 'GET', 0, '20');
INSERT INTO `xb_dict_data` VALUES (41, '2024-12-02 17:09:16', '2024-12-02 17:09:16', 5, 'POST', 'POST', 0, '20');
INSERT INTO `xb_dict_data` VALUES (42, '2024-12-02 17:09:23', '2024-12-02 17:09:23', 5, 'PUT', 'PUT', 0, '20');
INSERT INTO `xb_dict_data` VALUES (43, '2024-12-02 17:09:28', '2024-12-02 17:09:28', 5, 'DELETE', 'DELETE', 0, '20');
INSERT INTO `xb_dict_data` VALUES (44, '2024-12-02 17:10:36', '2024-12-02 17:10:36', 7, '隐藏', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (45, '2024-12-02 17:10:41', '2024-12-02 17:10:41', 7, '显示', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (46, '2024-12-02 17:11:01', '2024-12-02 17:11:01', 8, '10', 'danger', 0, '20');
INSERT INTO `xb_dict_data` VALUES (47, '2024-12-02 17:11:11', '2024-12-02 17:11:11', 8, '20', 'success', 0, '20');
INSERT INTO `xb_dict_data` VALUES (48, '2024-12-02 17:13:51', '2024-12-02 17:14:41', 4, 'info', 'none/index', 0, '20');
INSERT INTO `xb_dict_data` VALUES (49, '2024-12-02 17:16:17', '2024-12-02 17:16:17', 4, 'warning', 'form/index', 0, '20');
INSERT INTO `xb_dict_data` VALUES (50, '2024-12-02 17:16:43', '2024-12-02 17:16:43', 4, 'success', 'table/index', 0, '20');
INSERT INTO `xb_dict_data` VALUES (51, '2024-12-02 17:16:54', '2024-12-02 17:16:54', 4, 'success', 'table/sidebar', 0, '20');
INSERT INTO `xb_dict_data` VALUES (52, '2024-12-02 17:18:46', '2024-12-02 17:18:46', 4, 'primary', 'remote/index', 0, '20');
INSERT INTO `xb_dict_data` VALUES (53, '2024-12-02 19:08:36', '2024-12-02 19:08:36', 1, '已封禁', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (54, '2024-12-02 19:08:54', '2024-12-02 19:09:00', 1, '未封禁', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (55, '2024-12-02 19:09:16', '2024-12-02 19:09:16', 2, 'danger', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (56, '2024-12-02 19:09:25', '2024-12-02 19:09:25', 2, 'success', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (57, '2024-12-02 19:09:46', '2024-12-02 19:09:46', 6, 'primary', 'GET', 0, '20');
INSERT INTO `xb_dict_data` VALUES (58, '2024-12-02 19:09:57', '2024-12-02 19:09:57', 6, 'warning', 'POST', 0, '20');
INSERT INTO `xb_dict_data` VALUES (59, '2024-12-02 19:10:34', '2024-12-02 19:10:34', 6, 'warning', 'PUT', 0, '20');
INSERT INTO `xb_dict_data` VALUES (60, '2024-12-02 19:10:49', '2024-12-02 19:10:49', 6, 'danger', 'DELETE', 0, '20');
INSERT INTO `xb_dict_data` VALUES (61, '2024-12-02 19:12:21', '2024-12-02 19:12:21', 10, 'danger', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (62, '2024-12-02 19:12:28', '2024-12-02 19:12:28', 10, 'success', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (63, '2024-12-02 19:17:20', '2024-12-02 19:17:20', 11, '图片', 'image', 0, '20');
INSERT INTO `xb_dict_data` VALUES (64, '2024-12-02 19:17:30', '2024-12-02 19:17:30', 11, '视频', 'video', 0, '20');
INSERT INTO `xb_dict_data` VALUES (65, '2024-12-02 19:19:27', '2024-12-02 19:19:27', 11, '文档', 'doc', 0, '20');
INSERT INTO `xb_dict_data` VALUES (66, '2024-12-02 19:19:46', '2024-12-02 19:19:46', 11, '音频', 'audio', 0, '20');
INSERT INTO `xb_dict_data` VALUES (67, '2024-12-02 19:20:05', '2024-12-02 19:20:05', 11, '字体', 'font', 0, '20');
INSERT INTO `xb_dict_data` VALUES (68, '2024-12-02 19:20:15', '2024-12-02 19:20:15', 11, '压缩包', 'zip', 0, '20');
INSERT INTO `xb_dict_data` VALUES (69, '2024-12-02 19:20:21', '2024-12-02 19:20:21', 11, '其他', 'other', 0, '20');
INSERT INTO `xb_dict_data` VALUES (70, '2024-12-02 19:23:30', '2024-12-02 19:23:30', 12, 'primary', 'image', 0, '20');
INSERT INTO `xb_dict_data` VALUES (71, '2024-12-02 19:23:40', '2024-12-02 19:23:40', 12, 'success', 'video', 0, '20');
INSERT INTO `xb_dict_data` VALUES (72, '2024-12-02 19:23:53', '2024-12-02 19:23:53', 12, 'info', 'doc', 0, '20');
INSERT INTO `xb_dict_data` VALUES (73, '2024-12-02 19:24:07', '2024-12-02 19:24:07', 12, 'primary', 'audio', 0, '20');
INSERT INTO `xb_dict_data` VALUES (74, '2024-12-02 19:24:20', '2024-12-02 19:24:20', 12, 'info', 'font', 0, '20');
INSERT INTO `xb_dict_data` VALUES (75, '2024-12-02 19:24:30', '2024-12-02 19:24:30', 12, 'warning', 'zip', 0, '20');
INSERT INTO `xb_dict_data` VALUES (76, '2024-12-02 19:25:38', '2024-12-02 19:25:38', 13, 'image', 'jpg,jpeg,png,gif', 0, '20');
INSERT INTO `xb_dict_data` VALUES (77, '2024-12-02 19:25:51', '2024-12-02 19:25:51', 13, 'video', 'mp4,avi,rmvb,mkv,flv', 0, '20');
INSERT INTO `xb_dict_data` VALUES (78, '2024-12-02 19:26:47', '2024-12-02 19:26:47', 13, 'doc', 'doc,docx,xls,xlsx,ppt,pptx,pdf,txt,pem', 0, '20');
INSERT INTO `xb_dict_data` VALUES (79, '2024-12-02 19:27:02', '2024-12-02 19:27:02', 13, 'audio', 'mp3,wav,flac,ape,alac', 0, '20');
INSERT INTO `xb_dict_data` VALUES (80, '2024-12-02 19:28:30', '2024-12-02 19:28:30', 13, 'font', 'ttf,otf,woff,woff2', 0, '20');
INSERT INTO `xb_dict_data` VALUES (81, '2024-12-02 19:28:42', '2024-12-02 19:28:42', 13, 'zip', 'zip,rar,7z,tar,gz,bz2', 0, '20');
INSERT INTO `xb_dict_data` VALUES (82, '2024-12-02 19:28:48', '2024-12-02 19:28:48', 13, 'other', '*', 0, '20');
INSERT INTO `xb_dict_data` VALUES (83, '2024-12-02 19:29:08', '2024-12-02 19:29:08', 14, '否', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (84, '2024-12-02 19:29:12', '2024-12-02 19:29:12', 14, '是', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (85, '2024-12-02 19:47:48', '2024-12-02 19:47:48', 18, 'http', 'http', 0, '20');
INSERT INTO `xb_dict_data` VALUES (86, '2024-12-02 19:47:53', '2024-12-02 19:47:53', 18, 'https', 'https', 0, '20');
INSERT INTO `xb_dict_data` VALUES (87, '2024-12-02 19:48:14', '2024-12-02 19:48:14', 19, '电脑端', 'pc', 0, '20');
INSERT INTO `xb_dict_data` VALUES (88, '2024-12-02 19:48:52', '2024-12-02 19:48:52', 19, '移动端', 'mobile', 0, '20');
INSERT INTO `xb_dict_data` VALUES (89, '2024-12-02 19:49:03', '2024-12-02 19:49:03', 19, '微信小程序', 'weapp', 0, '20');
INSERT INTO `xb_dict_data` VALUES (90, '2024-12-02 19:49:16', '2024-12-02 19:49:16', 19, '微信公众号', 'wechat', 0, '20');
INSERT INTO `xb_dict_data` VALUES (91, '2024-12-02 19:49:30', '2024-12-02 19:49:30', 19, 'APP', 'app', 0, '20');
INSERT INTO `xb_dict_data` VALUES (92, '2024-12-02 19:50:28', '2024-12-02 19:50:28', 20, '减少', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (93, '2024-12-02 19:50:33', '2024-12-02 19:50:33', 20, '增加', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (94, '2024-12-02 19:51:06', '2024-12-02 19:51:06', 15, 'danger', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (95, '2024-12-02 19:51:13', '2024-12-02 19:51:13', 15, 'success', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (96, '2024-12-02 19:51:40', '2024-12-02 19:51:40', 21, '待支付', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (97, '2024-12-02 19:51:45', '2024-12-02 19:51:45', 21, '已支付', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (98, '2024-12-02 19:52:16', '2024-12-02 19:54:14', 22, '下架', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (99, '2024-12-02 19:52:24', '2024-12-02 19:54:03', 22, '上架', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (100, '2024-12-02 19:53:34', '2024-12-02 19:53:34', 23, '关闭', '10', 0, '20');
INSERT INTO `xb_dict_data` VALUES (101, '2024-12-02 19:53:40', '2024-12-02 19:53:40', 23, '开启', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (102, '2024-12-06 17:55:49', '2024-12-13 01:36:59', 25, '未使用', '10', 1, '20');
INSERT INTO `xb_dict_data` VALUES (103, '2024-12-06 17:55:54', '2024-12-13 01:37:01', 25, '使用中', '20', 0, '20');
INSERT INTO `xb_dict_data` VALUES (104, '2025-01-03 03:53:53', '2025-01-03 04:06:30', 3, '工作台', 'workbench/index', 1, '20');