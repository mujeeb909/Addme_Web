/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : addmee

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2021-11-22 14:15:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for activities
-- ----------------------------
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `details` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of activities
-- ----------------------------

-- ----------------------------
-- Table structure for business_infos
-- ----------------------------
DROP TABLE IF EXISTS `business_infos`;
CREATE TABLE `business_infos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `bio` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of business_infos
-- ----------------------------
INSERT INTO `business_infos` VALUES ('1', '6', 'Lorem Ipsum', null, '1', '6', '6', '2021-04-02 08:23:59', '2021-04-02 08:24:50');

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS `configs`;
CREATE TABLE `configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of configs
-- ----------------------------

-- ----------------------------
-- Table structure for contact_cards
-- ----------------------------
DROP TABLE IF EXISTS `contact_cards`;
CREATE TABLE `contact_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `customer_profile_ids` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_business` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of contact_cards
-- ----------------------------
INSERT INTO `contact_cards` VALUES ('2', '8', '263,264,242', '0', '8', '8', '2021-04-30 18:18:42', '2021-06-03 08:00:35');
INSERT INTO `contact_cards` VALUES ('17', '106', '464,463,461,462', '0', '106', null, '2021-06-25 01:58:02', '2021-06-25 01:58:02');
INSERT INTO `contact_cards` VALUES ('18', '107', '474,473', '0', '107', null, '2021-06-25 11:14:18', '2021-06-25 11:14:18');
INSERT INTO `contact_cards` VALUES ('19', '116', '509,508,507,510,511', '0', '116', null, '2021-06-29 17:30:56', '2021-06-29 17:30:56');
INSERT INTO `contact_cards` VALUES ('20', '115', '499,504,501,503,502,500', '0', '115', null, '2021-06-29 18:49:22', '2021-06-29 18:49:22');
INSERT INTO `contact_cards` VALUES ('21', '120', '538,537,536,535,534,533,532', '0', '120', null, '2021-06-30 16:04:53', '2021-06-30 16:04:53');
INSERT INTO `contact_cards` VALUES ('22', '123', '0', '0', '123', '123', '2021-06-30 21:41:07', '2021-07-06 20:26:20');
INSERT INTO `contact_cards` VALUES ('23', '126', '564', '0', '126', null, '2021-07-01 04:10:49', '2021-07-01 04:10:49');
INSERT INTO `contact_cards` VALUES ('24', '119', '525,527,526,529', '0', '119', null, '2021-07-02 02:38:50', '2021-07-02 02:38:50');
INSERT INTO `contact_cards` VALUES ('25', '102', '634,637,636,635', '0', '102', null, '2021-07-05 13:56:59', '2021-07-05 13:56:59');
INSERT INTO `contact_cards` VALUES ('26', '144', '667,666,665,664,662,663', '0', '144', '144', '2021-07-06 13:07:38', '2021-07-06 13:07:42');
INSERT INTO `contact_cards` VALUES ('27', '110', '484,485,482,486,483,616', '0', '110', null, '2021-07-06 14:21:31', '2021-07-06 14:21:31');

-- ----------------------------
-- Table structure for customer_profiles
-- ----------------------------
DROP TABLE IF EXISTS `customer_profiles`;
CREATE TABLE `customer_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profile_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_business` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `is_direct` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sequence` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=689 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of customer_profiles
-- ----------------------------
INSERT INTO `customer_profiles` VALUES ('15', '8', 'contact-card', '8', null, null, '0', '1', '0', null, '13', '8', '8', '2021-04-20 13:52:44', '2021-07-06 22:19:05');
INSERT INTO `customer_profiles` VALUES ('16', '8', 'contact-card', '8', null, null, '1', '1', '0', null, '11', '8', '8', '2021-04-20 13:52:44', '2021-05-17 00:25:23');
INSERT INTO `customer_profiles` VALUES ('176', 'wahab', 'instagram', '8', null, null, '0', '1', '0', null, '1', '8', '8', '2021-05-03 19:14:26', '2021-07-06 22:19:12');
INSERT INTO `customer_profiles` VALUES ('195', 'https://logiqon.co', 'www', '8', null, null, '0', '1', '0', null, '0', '8', '8', '2021-05-12 19:10:16', '2021-07-06 22:19:12');
INSERT INTO `customer_profiles` VALUES ('202', '+923322840825', 'text', '8', null, null, '0', '1', '0', null, '11', '8', '8', '2021-05-16 14:05:56', '2021-07-06 22:19:05');
INSERT INTO `customer_profiles` VALUES ('204', 'https://www.facebook.com/beeinghamza', 'facebook', '8', null, null, '0', '1', '0', null, '2', '8', '8', '2021-05-16 14:06:42', '2021-07-06 22:19:12');
INSERT INTO `customer_profiles` VALUES ('206', 'https://youtube.com/c/HamzaMehmood', 'youtube', '8', null, null, '0', '1', '0', null, '10', '8', '8', '2021-05-16 16:33:39', '2021-07-06 22:19:05');
INSERT INTO `customer_profiles` VALUES ('207', 'tubbolive', 'twitch', '8', null, null, '0', '1', '0', null, '3', '8', '8', '2021-05-16 16:35:18', '2021-07-06 22:19:10');
INSERT INTO `customer_profiles` VALUES ('208', 'https://www.linkedin.com/company/logiqon-solutions', 'linkedin', '8', null, null, '0', '1', '0', null, '7', '8', '8', '2021-05-16 16:35:56', '2021-07-06 22:18:57');
INSERT INTO `customer_profiles` VALUES ('209', 'iamhamza', 'twitter', '8', null, null, '0', '1', '0', null, '8', '8', '8', '2021-05-16 16:36:10', '2021-07-06 22:19:05');
INSERT INTO `customer_profiles` VALUES ('216', 'i.amhamza', 'snapchat', '8', null, null, '0', '1', '0', null, '9', '8', '8', '2021-05-16 18:02:04', '2021-07-06 22:19:05');
INSERT INTO `customer_profiles` VALUES ('219', 'house 875, street 41, block C, PWD, islamabad', 'address', '8', null, null, '0', '1', '0', null, '12', '8', '8', '2021-05-16 21:49:40', '2021-07-06 22:19:05');
INSERT INTO `customer_profiles` VALUES ('220', 'diako099', 'telegram', '8', null, null, '0', '1', '0', null, '5', '8', '8', '2021-05-16 21:54:06', '2021-07-06 22:18:54');
INSERT INTO `customer_profiles` VALUES ('241', 'hamza-mehmood-2', 'soundcloud', '8', null, null, '0', '1', '0', null, '6', '8', '8', '2021-05-19 20:19:37', '2021-07-06 22:18:54');
INSERT INTO `customer_profiles` VALUES ('242', 'https://open.spotify.com/user/31seux2xic2blyqalqrlju66g6sm?si=KfBmnO3KSJGvEKVnOIEtug', 'spotify', '8', null, null, '0', '1', '0', null, '4', '8', '8', '2021-05-19 20:28:50', '2021-07-06 22:19:10');
INSERT INTO `customer_profiles` VALUES ('451', '102', 'contact-card', '102', null, null, '0', '1', '0', null, '4', '102', '102', '2021-06-24 10:18:54', '2021-07-06 22:16:45');
INSERT INTO `customer_profiles` VALUES ('452', '102', 'contact-card', '102', null, null, '1', '1', '0', null, '0', '102', null, '2021-06-24 10:18:54', '2021-06-24 10:18:54');
INSERT INTO `customer_profiles` VALUES ('453', '103', 'contact-card', '103', null, null, '0', '1', '0', null, '0', '103', null, '2021-06-24 10:20:15', '2021-06-24 10:20:15');
INSERT INTO `customer_profiles` VALUES ('454', '103', 'contact-card', '103', null, null, '1', '1', '0', null, '0', '103', null, '2021-06-24 10:20:15', '2021-06-24 10:20:15');
INSERT INTO `customer_profiles` VALUES ('455', '104', 'contact-card', '104', null, null, '0', '1', '0', null, '0', '104', null, '2021-06-24 17:51:38', '2021-06-24 17:51:38');
INSERT INTO `customer_profiles` VALUES ('456', '104', 'contact-card', '104', null, null, '1', '1', '0', null, '0', '104', null, '2021-06-24 17:51:38', '2021-06-24 17:51:38');
INSERT INTO `customer_profiles` VALUES ('457', '105', 'contact-card', '105', null, null, '0', '1', '0', null, '0', '105', null, '2021-06-24 22:38:31', '2021-06-24 22:38:31');
INSERT INTO `customer_profiles` VALUES ('458', '105', 'contact-card', '105', null, null, '1', '1', '0', null, '0', '105', null, '2021-06-24 22:38:32', '2021-06-24 22:38:32');
INSERT INTO `customer_profiles` VALUES ('459', '106', 'contact-card', '106', null, null, '0', '1', '0', null, '0', '106', '106', '2021-06-25 01:35:35', '2021-07-02 15:20:27');
INSERT INTO `customer_profiles` VALUES ('460', '106', 'contact-card', '106', null, null, '1', '1', '0', null, '0', '106', null, '2021-06-25 01:35:35', '2021-06-25 01:35:35');
INSERT INTO `customer_profiles` VALUES ('461', 'https://www.facebook.com/Sandy.silentdevil', 'facebook', '106', null, null, '0', '1', '0', null, '4', '106', '106', '2021-06-25 01:56:34', '2021-07-02 15:20:27');
INSERT INTO `customer_profiles` VALUES ('462', 'its.saadahmed', 'instagram', '106', null, null, '0', '1', '0', null, '5', '106', '106', '2021-06-25 01:56:48', '2021-07-02 15:20:27');
INSERT INTO `customer_profiles` VALUES ('463', '+923008448463', 'whatsapp', '106', null, null, '0', '1', '0', null, '1', '106', '106', '2021-06-25 01:57:32', '2021-07-02 15:20:27');
INSERT INTO `customer_profiles` VALUES ('464', '+923008448463', 'call', '106', null, null, '0', '1', '0', null, '3', '106', '106', '2021-06-25 01:57:46', '2021-07-02 15:20:27');
INSERT INTO `customer_profiles` VALUES ('465', 'exigency.biz', 'www', '106', null, null, '0', '1', '0', null, '2', '106', '106', '2021-06-25 01:58:49', '2021-07-02 15:20:27');
INSERT INTO `customer_profiles` VALUES ('466', '107', 'contact-card', '107', null, null, '0', '1', '0', null, '1', '107', '107', '2021-06-25 10:35:23', '2021-07-06 23:13:33');
INSERT INTO `customer_profiles` VALUES ('467', '107', 'contact-card', '107', null, null, '1', '1', '0', null, '0', '107', null, '2021-06-25 10:35:23', '2021-06-25 10:35:23');
INSERT INTO `customer_profiles` VALUES ('468', '+493485326986', 'call', '107', null, null, '0', '1', '0', null, '4', '107', '107', '2021-06-25 10:37:30', '2021-07-06 22:36:20');
INSERT INTO `customer_profiles` VALUES ('469', '108', 'contact-card', '108', null, null, '0', '1', '0', null, '0', '108', null, '2021-06-25 10:38:55', '2021-06-25 10:38:55');
INSERT INTO `customer_profiles` VALUES ('470', '108', 'contact-card', '108', null, null, '1', '1', '0', null, '0', '108', null, '2021-06-25 10:38:55', '2021-06-25 10:38:55');
INSERT INTO `customer_profiles` VALUES ('471', 'wahab202', 'snapchat', '107', null, null, '0', '1', '0', null, '5', '107', '107', '2021-06-25 11:09:42', '2021-07-06 22:36:20');
INSERT INTO `customer_profiles` VALUES ('472', '+494865432134', 'text', '107', null, null, '0', '1', '0', null, '2', '107', '107', '2021-06-25 11:09:49', '2021-07-06 22:36:20');
INSERT INTO `customer_profiles` VALUES ('473', 'abdulwahabaziz20@gmail.com', 'email', '107', null, null, '0', '1', '0', null, '3', '107', '107', '2021-06-25 11:09:59', '2021-07-06 22:36:20');
INSERT INTO `customer_profiles` VALUES ('474', '+493485691564', 'whatsapp', '107', null, null, '0', '1', '0', null, '0', '107', '107', '2021-06-25 11:14:10', '2021-07-06 23:13:33');
INSERT INTO `customer_profiles` VALUES ('475', '109', 'contact-card', '109', null, null, '0', '1', '0', null, '0', '109', null, '2021-06-25 16:21:48', '2021-06-25 16:21:48');
INSERT INTO `customer_profiles` VALUES ('476', '109', 'contact-card', '109', null, null, '1', '1', '0', null, '0', '109', null, '2021-06-25 16:21:48', '2021-06-25 16:21:48');
INSERT INTO `customer_profiles` VALUES ('477', '+971553613144', 'whatsapp', '109', null, null, '0', '1', '0', null, '0', '109', '109', '2021-06-25 16:23:13', '2021-06-25 16:25:20');
INSERT INTO `customer_profiles` VALUES ('478', 'rizhaider32', 'instagram', '109', null, null, '0', '1', '0', null, '1', '109', '109', '2021-06-25 16:25:00', '2021-06-25 16:25:20');
INSERT INTO `customer_profiles` VALUES ('479', '+923334111772', 'call', '109', null, null, '0', '1', '0', null, '0', '109', null, '2021-06-25 16:25:41', '2021-06-25 16:25:41');
INSERT INTO `customer_profiles` VALUES ('480', '110', 'contact-card', '110', null, null, '0', '1', '0', null, '4', '110', '110', '2021-06-25 17:43:12', '2021-07-06 19:25:11');
INSERT INTO `customer_profiles` VALUES ('481', '110', 'contact-card', '110', null, null, '1', '1', '0', null, '0', '110', null, '2021-06-25 17:43:12', '2021-06-25 17:43:12');
INSERT INTO `customer_profiles` VALUES ('482', 'eazyworld_', 'instagram', '110', null, null, '0', '1', '0', null, '2', '110', '110', '2021-06-25 17:43:59', '2021-07-06 19:33:09');
INSERT INTO `customer_profiles` VALUES ('483', 'yilmaze', 'snapchat', '110', null, null, '0', '1', '0', null, '5', '110', '110', '2021-06-25 17:44:06', '2021-07-06 15:37:42');
INSERT INTO `customer_profiles` VALUES ('484', '+4915739390394', 'whatsapp', '110', null, null, '0', '1', '0', null, '1', '110', '110', '2021-06-25 17:44:15', '2021-07-06 19:54:36');
INSERT INTO `customer_profiles` VALUES ('485', '+4915739390394', 'call', '110', null, null, '0', '1', '0', null, '3', '110', '110', '2021-06-25 17:44:26', '2021-07-06 19:25:11');
INSERT INTO `customer_profiles` VALUES ('486', 'www.addmee.de', 'www', '110', null, null, '0', '1', '0', null, '0', '110', '110', '2021-06-25 17:44:41', '2021-07-06 19:54:36');
INSERT INTO `customer_profiles` VALUES ('487', '111', 'contact-card', '111', null, null, '0', '1', '0', null, '0', '111', null, '2021-06-28 21:23:48', '2021-06-28 21:23:48');
INSERT INTO `customer_profiles` VALUES ('488', '111', 'contact-card', '111', null, null, '1', '1', '0', null, '0', '111', null, '2021-06-28 21:23:48', '2021-06-28 21:23:48');
INSERT INTO `customer_profiles` VALUES ('489', '112', 'contact-card', '112', null, null, '0', '1', '0', null, '0', '112', null, '2021-06-28 22:05:36', '2021-06-28 22:05:36');
INSERT INTO `customer_profiles` VALUES ('490', '112', 'contact-card', '112', null, null, '1', '1', '0', null, '0', '112', null, '2021-06-28 22:05:36', '2021-06-28 22:05:36');
INSERT INTO `customer_profiles` VALUES ('491', 'K2an01', 'instagram', '112', null, null, '0', '1', '0', null, '0', '112', '112', '2021-06-28 22:10:28', '2021-06-29 21:48:45');
INSERT INTO `customer_profiles` VALUES ('492', 'Kaan65', 'snapchat', '112', null, null, '0', '1', '0', null, '1', '112', '112', '2021-06-28 22:10:35', '2021-06-29 21:48:45');
INSERT INTO `customer_profiles` VALUES ('493', '113', 'contact-card', '113', null, null, '0', '1', '0', null, '0', '113', null, '2021-06-28 22:36:48', '2021-06-28 22:36:48');
INSERT INTO `customer_profiles` VALUES ('494', '113', 'contact-card', '113', null, null, '1', '1', '0', null, '0', '113', null, '2021-06-28 22:36:48', '2021-06-28 22:36:48');
INSERT INTO `customer_profiles` VALUES ('495', '114', 'contact-card', '114', null, null, '0', '1', '0', null, '0', '114', null, '2021-06-28 22:39:29', '2021-06-28 22:39:29');
INSERT INTO `customer_profiles` VALUES ('496', '114', 'contact-card', '114', null, null, '1', '1', '0', null, '0', '114', null, '2021-06-28 22:39:29', '2021-06-28 22:39:29');
INSERT INTO `customer_profiles` VALUES ('497', '115', 'contact-card', '115', null, null, '0', '1', '0', null, '0', '115', null, '2021-06-28 22:56:32', '2021-06-28 22:56:32');
INSERT INTO `customer_profiles` VALUES ('498', '115', 'contact-card', '115', null, null, '1', '1', '0', null, '0', '115', null, '2021-06-28 22:56:32', '2021-06-28 22:56:32');
INSERT INTO `customer_profiles` VALUES ('499', '_et97_', 'instagram', '115', null, null, '0', '1', '0', null, '0', '115', '115', '2021-06-28 22:57:43', '2021-06-28 23:04:27');
INSERT INTO `customer_profiles` VALUES ('500', 'tabe18', 'snapchat', '115', null, null, '0', '1', '0', null, '5', '115', '115', '2021-06-28 22:57:49', '2021-06-28 23:04:27');
INSERT INTO `customer_profiles` VALUES ('501', 'levi_d_ackerman', 'tiktok', '115', null, null, '0', '1', '0', null, '2', '115', '115', '2021-06-28 22:58:26', '2021-06-28 23:04:27');
INSERT INTO `customer_profiles` VALUES ('502', '+4917620379284', 'call', '115', null, null, '0', '1', '0', null, '4', '115', '115', '2021-06-28 22:58:57', '2021-06-28 23:04:27');
INSERT INTO `customer_profiles` VALUES ('503', 'tabesanelankovan1997@gmail.com', 'email', '115', null, null, '0', '1', '0', null, '3', '115', '115', '2021-06-28 22:59:20', '2021-06-28 23:04:27');
INSERT INTO `customer_profiles` VALUES ('504', 'https://m.facebook.com/tabe.elankovan', 'facebook', '115', null, null, '0', '1', '0', null, '1', '115', '115', '2021-06-28 23:00:21', '2021-06-28 23:04:27');
INSERT INTO `customer_profiles` VALUES ('505', '116', 'contact-card', '116', null, null, '0', '1', '0', null, '4', '116', '116', '2021-06-29 17:20:11', '2021-06-29 19:14:11');
INSERT INTO `customer_profiles` VALUES ('506', '116', 'contact-card', '116', null, null, '1', '1', '0', null, '0', '116', null, '2021-06-29 17:20:11', '2021-06-29 17:20:11');
INSERT INTO `customer_profiles` VALUES ('507', 'raminramez_', 'instagram', '116', null, null, '0', '1', '0', null, '0', '116', '116', '2021-06-29 17:22:58', '2021-06-29 19:14:00');
INSERT INTO `customer_profiles` VALUES ('508', '+491776121248', 'call', '116', null, null, '0', '1', '0', null, '1', '116', '116', '2021-06-29 17:25:06', '2021-06-29 19:14:16');
INSERT INTO `customer_profiles` VALUES ('509', '+491776121248', 'whatsapp', '116', null, null, '0', '1', '0', null, '7', '116', '116', '2021-06-29 17:25:15', '2021-06-29 19:14:11');
INSERT INTO `customer_profiles` VALUES ('510', 'raminramezofficial@gmail.com', 'email', '116', null, null, '0', '1', '0', null, '5', '116', '116', '2021-06-29 17:25:42', '2021-06-29 19:14:11');
INSERT INTO `customer_profiles` VALUES ('511', 'https://www.facebook.com/ramin.ramez.50', 'facebook', '116', null, null, '0', '1', '0', null, '2', '116', '116', '2021-06-29 17:27:17', '2021-06-29 19:14:16');
INSERT INTO `customer_profiles` VALUES ('512', 'https://www.paypal.me/raminramez', 'paypal', '116', null, null, '0', '1', '0', null, '8', '116', '116', '2021-06-29 17:30:38', '2021-06-29 19:14:11');
INSERT INTO `customer_profiles` VALUES ('513', '117', 'contact-card', '117', null, null, '0', '1', '0', null, '0', '117', null, '2021-06-29 18:09:08', '2021-06-29 18:09:08');
INSERT INTO `customer_profiles` VALUES ('514', '117', 'contact-card', '117', null, null, '1', '1', '0', null, '0', '117', null, '2021-06-29 18:09:08', '2021-06-29 18:09:08');
INSERT INTO `customer_profiles` VALUES ('515', 'yaya_toure1', 'instagram', '117', null, null, '0', '1', '0', null, '0', '117', null, '2021-06-29 18:10:48', '2021-06-29 18:10:48');
INSERT INTO `customer_profiles` VALUES ('516', 'https://open.spotify.com/playlist/6sRPnlODr0G4JRyqeZzyGI?si=cZlbT7kySTqpHe_ndhg7VQ&utm_source=copy-link&dl_branch=1', 'spotify', '116', null, null, '0', '1', '0', null, '6', '116', '116', '2021-06-29 19:10:41', '2021-06-29 19:14:11');
INSERT INTO `customer_profiles` VALUES ('517', 'www.labottegarheine.com', 'www', '116', null, null, '0', '1', '0', null, '3', '116', '116', '2021-06-29 19:11:12', '2021-06-29 19:14:16');
INSERT INTO `customer_profiles` VALUES ('518', '118', 'contact-card', '118', null, null, '0', '1', '0', null, '0', '118', null, '2021-06-29 19:50:14', '2021-06-29 19:50:14');
INSERT INTO `customer_profiles` VALUES ('519', '118', 'contact-card', '118', null, null, '1', '1', '0', null, '0', '118', null, '2021-06-29 19:50:14', '2021-06-29 19:50:14');
INSERT INTO `customer_profiles` VALUES ('520', 'Jeremyjamal_', 'instagram', '118', null, null, '0', '1', '0', null, '0', '118', null, '2021-06-29 19:51:35', '2021-06-29 19:51:35');
INSERT INTO `customer_profiles` VALUES ('521', '+491623654091', 'call', '118', null, null, '0', '1', '0', null, '0', '118', null, '2021-06-29 19:51:45', '2021-06-29 19:51:45');
INSERT INTO `customer_profiles` VALUES ('522', '+491623654091', 'whatsapp', '118', null, null, '0', '1', '0', null, '0', '118', null, '2021-06-29 19:51:53', '2021-06-29 19:51:53');
INSERT INTO `customer_profiles` VALUES ('523', '119', 'contact-card', '119', null, null, '0', '1', '0', null, '5', '119', '119', '2021-06-30 14:46:45', '2021-07-02 02:41:44');
INSERT INTO `customer_profiles` VALUES ('524', '119', 'contact-card', '119', null, null, '1', '1', '0', null, '0', '119', null, '2021-06-30 14:46:45', '2021-06-30 14:46:45');
INSERT INTO `customer_profiles` VALUES ('525', 'addmee.de', 'instagram', '119', null, null, '0', '1', '0', null, '2', '119', '119', '2021-06-30 14:49:35', '2021-07-06 19:20:23');
INSERT INTO `customer_profiles` VALUES ('526', 'https://www.facebook.com/addmee.de/', 'facebook', '119', null, null, '0', '1', '0', null, '3', '119', '119', '2021-06-30 14:51:34', '2021-07-06 19:20:23');
INSERT INTO `customer_profiles` VALUES ('527', 'addmee.de', 'tiktok', '119', null, null, '0', '1', '0', null, '0', '119', '119', '2021-06-30 14:51:42', '2021-07-06 19:57:39');
INSERT INTO `customer_profiles` VALUES ('528', 'info@addmee.de', 'email', '119', null, null, '0', '1', '0', null, '4', '119', '119', '2021-06-30 14:51:56', '2021-07-02 02:42:35');
INSERT INTO `customer_profiles` VALUES ('529', 'www.addmee.de', 'www', '119', null, null, '0', '1', '0', null, '1', '119', '119', '2021-06-30 14:52:12', '2021-07-06 19:57:39');
INSERT INTO `customer_profiles` VALUES ('530', '120', 'contact-card', '120', null, null, '0', '1', '0', null, '12', '120', '120', '2021-06-30 16:00:31', '2021-07-04 19:54:33');
INSERT INTO `customer_profiles` VALUES ('531', '120', 'contact-card', '120', null, null, '1', '1', '0', null, '0', '120', null, '2021-06-30 16:00:31', '2021-06-30 16:00:31');
INSERT INTO `customer_profiles` VALUES ('532', 'erdem24_7', 'instagram', '120', null, null, '0', '1', '0', null, '0', '120', '120', '2021-06-30 16:01:35', '2021-07-03 19:52:45');
INSERT INTO `customer_profiles` VALUES ('533', 'https://youtube.com/channel/UCu2faMW9PLJFdiLoTgCFtNw', 'youtube', '120', null, null, '0', '1', '0', null, '5', '120', '120', '2021-06-30 16:02:25', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('534', 'erdem24_7', 'tiktok', '120', null, null, '0', '1', '0', null, '2', '120', '120', '2021-06-30 16:02:41', '2021-07-04 19:54:39');
INSERT INTO `customer_profiles` VALUES ('535', 'https://open.spotify.com/playlist/0ASJSpzRW9eTzkzRZ2SfWi?si=sWKFWRHQR16BER5aSfG1Cg&dl_branch=1', 'spotify', '120', null, null, '0', '1', '0', null, '4', '120', '120', '2021-06-30 16:03:14', '2021-07-04 19:54:47');
INSERT INTO `customer_profiles` VALUES ('536', 'moafaka', 'telegram', '120', null, null, '0', '1', '0', null, '11', '120', '120', '2021-06-30 16:03:27', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('537', 'https://www.facebook.com/erdem.nazli', 'facebook', '120', null, null, '0', '1', '0', null, '3', '120', '120', '2021-06-30 16:04:11', '2021-07-04 19:54:44');
INSERT INTO `customer_profiles` VALUES ('538', '+4923368191600', 'call', '120', null, null, '0', '1', '0', null, '10', '120', '120', '2021-06-30 16:04:24', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('539', '121', 'contact-card', '121', null, null, '0', '1', '0', null, '0', '121', null, '2021-06-30 17:40:49', '2021-06-30 17:40:49');
INSERT INTO `customer_profiles` VALUES ('540', '121', 'contact-card', '121', null, null, '1', '1', '0', null, '0', '121', null, '2021-06-30 17:40:49', '2021-06-30 17:40:49');
INSERT INTO `customer_profiles` VALUES ('541', 'maik.lein', 'instagram', '121', null, null, '0', '1', '0', null, '0', '121', null, '2021-06-30 17:46:20', '2021-06-30 17:46:20');
INSERT INTO `customer_profiles` VALUES ('542', 'maikl_123', 'snapchat', '121', null, null, '0', '1', '0', null, '0', '121', null, '2021-06-30 17:46:35', '2021-06-30 17:46:35');
INSERT INTO `customer_profiles` VALUES ('543', 'hokagemaikel', 'tiktok', '121', null, null, '0', '1', '0', null, '0', '121', null, '2021-06-30 17:46:43', '2021-06-30 17:46:43');
INSERT INTO `customer_profiles` VALUES ('544', 'leinmaik', 'twitter', '121', null, null, '0', '1', '0', null, '0', '121', null, '2021-06-30 17:47:05', '2021-06-30 17:47:05');
INSERT INTO `customer_profiles` VALUES ('545', 'hokagemaikel', 'twitch', '121', null, null, '0', '1', '0', null, '0', '121', null, '2021-06-30 17:47:15', '2021-06-30 17:47:15');
INSERT INTO `customer_profiles` VALUES ('546', 'www.friseurzubehoer24.de', 'www', '120', null, null, '0', '1', '0', null, '9', '120', '120', '2021-06-30 17:49:49', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('547', '122', 'contact-card', '122', null, null, '0', '1', '0', null, '0', '122', null, '2021-06-30 18:54:14', '2021-06-30 18:54:14');
INSERT INTO `customer_profiles` VALUES ('548', '122', 'contact-card', '122', null, null, '1', '1', '0', null, '0', '122', null, '2021-06-30 18:54:14', '2021-06-30 18:54:14');
INSERT INTO `customer_profiles` VALUES ('549', 'mertyilmaz3', 'snapchat', '122', null, null, '0', '1', '0', null, '0', '122', '122', '2021-06-30 18:55:17', '2021-06-30 18:57:25');
INSERT INTO `customer_profiles` VALUES ('550', 'mert_y23', 'instagram', '122', null, null, '0', '1', '0', null, '0', '122', '122', '2021-06-30 18:56:02', '2021-06-30 18:56:36');
INSERT INTO `customer_profiles` VALUES ('551', '123', 'contact-card', '123', null, null, '0', '1', '0', null, '0', '123', null, '2021-06-30 20:39:25', '2021-06-30 20:39:25');
INSERT INTO `customer_profiles` VALUES ('552', '123', 'contact-card', '123', null, null, '1', '1', '0', null, '0', '123', null, '2021-06-30 20:39:25', '2021-06-30 20:39:25');
INSERT INTO `customer_profiles` VALUES ('553', '124', 'contact-card', '124', null, null, '0', '1', '0', null, '0', '124', null, '2021-06-30 20:45:23', '2021-06-30 20:45:23');
INSERT INTO `customer_profiles` VALUES ('554', '124', 'contact-card', '124', null, null, '1', '1', '0', null, '0', '124', null, '2021-06-30 20:45:23', '2021-06-30 20:45:23');
INSERT INTO `customer_profiles` VALUES ('555', '+4917623776012', 'call', '124', null, null, '0', '1', '0', null, '0', '124', null, '2021-06-30 20:53:07', '2021-06-30 20:53:07');
INSERT INTO `customer_profiles` VALUES ('557', '125', 'contact-card', '125', null, null, '0', '1', '0', null, '0', '125', null, '2021-06-30 21:49:52', '2021-06-30 21:49:52');
INSERT INTO `customer_profiles` VALUES ('558', '125', 'contact-card', '125', null, null, '1', '1', '0', null, '0', '125', null, '2021-06-30 21:49:52', '2021-06-30 21:49:52');
INSERT INTO `customer_profiles` VALUES ('560', 'abdullah_de94', 'instagram', '123', null, null, '0', '1', '0', null, '0', '123', '123', '2021-06-30 21:55:50', '2021-06-30 23:45:25');
INSERT INTO `customer_profiles` VALUES ('562', '126', 'contact-card', '126', null, null, '0', '1', '0', null, '0', '126', null, '2021-06-30 23:26:51', '2021-06-30 23:26:51');
INSERT INTO `customer_profiles` VALUES ('563', '126', 'contact-card', '126', null, null, '1', '1', '0', null, '0', '126', null, '2021-06-30 23:26:51', '2021-06-30 23:26:51');
INSERT INTO `customer_profiles` VALUES ('564', 'kaan35clk', 'instagram', '126', null, null, '0', '1', '0', null, '0', '126', null, '2021-06-30 23:30:34', '2021-06-30 23:30:34');
INSERT INTO `customer_profiles` VALUES ('565', '+491633845214', 'whatsapp', '126', null, null, '0', '1', '0', null, '0', '126', null, '2021-06-30 23:31:25', '2021-06-30 23:31:25');
INSERT INTO `customer_profiles` VALUES ('566', '127', 'contact-card', '127', null, null, '0', '1', '0', null, '0', '127', null, '2021-07-01 00:16:46', '2021-07-01 00:16:46');
INSERT INTO `customer_profiles` VALUES ('567', '127', 'contact-card', '127', null, null, '1', '1', '0', null, '0', '127', null, '2021-07-01 00:16:46', '2021-07-01 00:16:46');
INSERT INTO `customer_profiles` VALUES ('568', 'abc', 'snapchat', '127', null, null, '0', '1', '0', null, '0', '127', null, '2021-07-01 00:17:22', '2021-07-01 00:17:22');
INSERT INTO `customer_profiles` VALUES ('569', '128', 'contact-card', '128', null, null, '0', '1', '0', null, '0', '128', null, '2021-07-01 16:26:16', '2021-07-01 16:26:16');
INSERT INTO `customer_profiles` VALUES ('570', '128', 'contact-card', '128', null, null, '1', '1', '0', null, '0', '128', null, '2021-07-01 16:26:16', '2021-07-01 16:26:16');
INSERT INTO `customer_profiles` VALUES ('571', '_malik_48', 'instagram', '128', null, null, '0', '1', '0', null, '0', '128', null, '2021-07-01 16:27:23', '2021-07-01 16:27:23');
INSERT INTO `customer_profiles` VALUES ('572', 'xmalik159', 'snapchat', '128', null, null, '0', '1', '0', null, '0', '128', null, '2021-07-01 16:27:27', '2021-07-01 16:27:27');
INSERT INTO `customer_profiles` VALUES ('573', 'i.amhamza', 'instagram', '113', null, null, '0', '1', '0', null, '0', '113', null, '2021-07-01 20:00:38', '2021-07-01 20:00:38');
INSERT INTO `customer_profiles` VALUES ('574', 'i.amhamza91', 'snapchat', '113', null, null, '0', '1', '0', null, '0', '113', null, '2021-07-01 20:00:49', '2021-07-01 20:00:49');
INSERT INTO `customer_profiles` VALUES ('575', 'http://linkedin.com/in/hamzamehmood91', 'linkedin', '113', null, null, '0', '1', '0', null, '0', '113', null, '2021-07-01 20:01:54', '2021-07-01 20:01:54');
INSERT INTO `customer_profiles` VALUES ('576', 'www.logiqon.co', 'www', '113', null, null, '0', '1', '0', null, '0', '113', null, '2021-07-01 20:02:34', '2021-07-01 20:02:34');
INSERT INTO `customer_profiles` VALUES ('577', '129', 'contact-card', '129', null, null, '0', '1', '0', null, '0', '129', null, '2021-07-01 22:40:18', '2021-07-01 22:40:18');
INSERT INTO `customer_profiles` VALUES ('578', '129', 'contact-card', '129', null, null, '1', '1', '0', null, '0', '129', null, '2021-07-01 22:40:18', '2021-07-01 22:40:18');
INSERT INTO `customer_profiles` VALUES ('579', 'alibadran832', 'snapchat', '129', null, null, '0', '1', '0', null, '0', '129', '129', '2021-07-01 22:41:10', '2021-07-01 22:41:35');
INSERT INTO `customer_profiles` VALUES ('580', 'ali__157', 'instagram', '129', null, null, '0', '1', '0', null, '0', '129', '129', '2021-07-01 22:41:18', '2021-07-01 22:41:25');
INSERT INTO `customer_profiles` VALUES ('581', '130', 'contact-card', '130', null, null, '0', '1', '0', null, '0', '130', null, '2021-07-03 19:46:07', '2021-07-03 19:46:07');
INSERT INTO `customer_profiles` VALUES ('582', '130', 'contact-card', '130', null, null, '1', '1', '0', null, '0', '130', null, '2021-07-03 19:46:07', '2021-07-03 19:46:07');
INSERT INTO `customer_profiles` VALUES ('583', 'miyou6487', 'instagram', '130', null, null, '0', '1', '0', null, '0', '130', '130', '2021-07-03 19:47:48', '2021-07-03 19:52:37');
INSERT INTO `customer_profiles` VALUES ('584', 'mimoyou', 'telegram', '130', null, null, '0', '1', '0', null, '1', '130', '130', '2021-07-03 19:48:38', '2021-07-03 19:52:37');
INSERT INTO `customer_profiles` VALUES ('585', '131', 'contact-card', '131', null, null, '0', '1', '0', null, '0', '131', null, '2021-07-03 20:13:06', '2021-07-03 20:13:06');
INSERT INTO `customer_profiles` VALUES ('586', '131', 'contact-card', '131', null, null, '1', '1', '0', null, '0', '131', null, '2021-07-03 20:13:06', '2021-07-03 20:13:06');
INSERT INTO `customer_profiles` VALUES ('587', 'maja_al1', 'instagram', '131', null, null, '0', '1', '0', null, '0', '131', null, '2021-07-03 20:15:29', '2021-07-03 20:15:29');
INSERT INTO `customer_profiles` VALUES ('588', 'https://www.facebook.com/maja.alssayrafi', 'facebook', '131', null, null, '0', '1', '0', null, '0', '131', null, '2021-07-03 20:16:31', '2021-07-03 20:16:31');
INSERT INTO `customer_profiles` VALUES ('589', 'maja_al', 'telegram', '131', null, null, '0', '1', '0', null, '0', '131', null, '2021-07-03 20:17:33', '2021-07-03 20:17:33');
INSERT INTO `customer_profiles` VALUES ('590', 'maja-lisa@hotmail.de', 'email', '131', null, null, '0', '1', '0', null, '0', '131', '131', '2021-07-03 20:17:49', '2021-07-04 17:43:11');
INSERT INTO `customer_profiles` VALUES ('591', '132', 'contact-card', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:43:23', '2021-07-03 20:43:23');
INSERT INTO `customer_profiles` VALUES ('592', '132', 'contact-card', '132', null, null, '1', '1', '0', null, '0', '132', null, '2021-07-03 20:43:23', '2021-07-03 20:43:23');
INSERT INTO `customer_profiles` VALUES ('593', 'cccompetition', 'instagram', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:44:17', '2021-07-03 20:44:17');
INSERT INTO `customer_profiles` VALUES ('594', '+491738726422', 'whatsapp', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:44:37', '2021-07-03 20:44:37');
INSERT INTO `customer_profiles` VALUES ('595', '+491738726422', 'call', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:44:51', '2021-07-03 20:44:51');
INSERT INTO `customer_profiles` VALUES ('596', '+491738726422', 'text', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:45:00', '2021-07-03 20:45:00');
INSERT INTO `customer_profiles` VALUES ('597', 'cccompetition@gmx.de', 'email', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:45:12', '2021-07-03 20:45:12');
INSERT INTO `customer_profiles` VALUES ('598', '133', 'contact-card', '133', null, null, '0', '1', '0', null, '0', '133', null, '2021-07-03 20:45:32', '2021-07-03 20:45:32');
INSERT INTO `customer_profiles` VALUES ('599', '133', 'contact-card', '133', null, null, '1', '1', '0', null, '0', '133', null, '2021-07-03 20:45:32', '2021-07-03 20:45:32');
INSERT INTO `customer_profiles` VALUES ('600', 'www.cccompetition.de', 'www', '132', null, null, '0', '1', '0', null, '0', '132', null, '2021-07-03 20:45:33', '2021-07-03 20:45:33');
INSERT INTO `customer_profiles` VALUES ('601', 'rose.smith.babes', 'instagram', '133', null, null, '0', '1', '0', null, '0', '133', null, '2021-07-03 20:47:20', '2021-07-03 20:47:20');
INSERT INTO `customer_profiles` VALUES ('602', '134', 'contact-card', '134', null, null, '0', '1', '0', null, '0', '134', null, '2021-07-03 21:07:47', '2021-07-03 21:07:47');
INSERT INTO `customer_profiles` VALUES ('603', '134', 'contact-card', '134', null, null, '1', '1', '0', null, '0', '134', null, '2021-07-03 21:07:47', '2021-07-03 21:07:47');
INSERT INTO `customer_profiles` VALUES ('604', 'krn_x8', 'instagram', '134', null, null, '0', '1', '0', null, '0', '134', null, '2021-07-03 21:08:54', '2021-07-03 21:08:54');
INSERT INTO `customer_profiles` VALUES ('605', '135', 'contact-card', '135', null, null, '0', '1', '0', null, '0', '135', null, '2021-07-03 22:23:53', '2021-07-03 22:23:53');
INSERT INTO `customer_profiles` VALUES ('606', '135', 'contact-card', '135', null, null, '1', '1', '0', null, '0', '135', null, '2021-07-03 22:23:53', '2021-07-03 22:23:53');
INSERT INTO `customer_profiles` VALUES ('607', 'corcia_86', 'instagram', '135', null, null, '0', '1', '0', null, '0', '135', '135', '2021-07-03 22:27:15', '2021-07-03 22:27:19');
INSERT INTO `customer_profiles` VALUES ('608', '+4917647721064', 'whatsapp', '135', null, null, '0', '1', '0', null, '0', '135', null, '2021-07-03 22:28:14', '2021-07-03 22:28:14');
INSERT INTO `customer_profiles` VALUES ('609', '+4917647721064', 'call', '135', null, null, '0', '1', '0', null, '0', '135', null, '2021-07-03 22:28:53', '2021-07-03 22:28:53');
INSERT INTO `customer_profiles` VALUES ('610', '136', 'contact-card', '136', null, null, '0', '1', '0', null, '0', '136', null, '2021-07-04 18:31:32', '2021-07-04 18:31:32');
INSERT INTO `customer_profiles` VALUES ('611', '136', 'contact-card', '136', null, null, '1', '1', '0', null, '0', '136', null, '2021-07-04 18:31:32', '2021-07-04 18:31:32');
INSERT INTO `customer_profiles` VALUES ('612', 'laura.pollok', 'instagram', '136', null, null, '0', '1', '0', null, '0', '136', null, '2021-07-04 18:32:14', '2021-07-04 18:32:14');
INSERT INTO `customer_profiles` VALUES ('613', '137', 'contact-card', '137', null, null, '0', '1', '0', null, '0', '137', null, '2021-07-04 18:55:47', '2021-07-04 18:55:47');
INSERT INTO `customer_profiles` VALUES ('614', '137', 'contact-card', '137', null, null, '1', '1', '0', null, '0', '137', null, '2021-07-04 18:55:47', '2021-07-04 18:55:47');
INSERT INTO `customer_profiles` VALUES ('615', 'umbrella.eheh', 'instagram', '137', null, null, '0', '1', '0', null, '0', '137', null, '2021-07-04 18:56:29', '2021-07-04 18:56:29');
INSERT INTO `customer_profiles` VALUES ('616', 'https://www.paypal.me/yilmazezi', 'paypal', '110', null, null, '0', '1', '0', null, '6', '110', '110', '2021-07-04 19:04:22', '2021-07-06 15:37:42');
INSERT INTO `customer_profiles` VALUES ('617', '138', 'contact-card', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:29:46', '2021-07-04 19:29:46');
INSERT INTO `customer_profiles` VALUES ('618', '138', 'contact-card', '138', null, null, '1', '1', '0', null, '0', '138', null, '2021-07-04 19:29:46', '2021-07-04 19:29:46');
INSERT INTO `customer_profiles` VALUES ('619', 'Woodstyle360', 'instagram', '138', null, null, '0', '1', '0', null, '0', '138', '138', '2021-07-04 19:31:49', '2021-07-04 19:32:02');
INSERT INTO `customer_profiles` VALUES ('620', 'Woodstyle360', 'facebook', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:32:13', '2021-07-04 19:32:13');
INSERT INTO `customer_profiles` VALUES ('621', 'Woodstyle360', 'tiktok', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:32:30', '2021-07-04 19:32:30');
INSERT INTO `customer_profiles` VALUES ('622', 'https://youtube.com/channel/UCdldhPO6AWWI9w2k6YMxVig', 'youtube', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:33:25', '2021-07-04 19:33:25');
INSERT INTO `customer_profiles` VALUES ('623', '+49017634517629', 'whatsapp', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:33:39', '2021-07-04 19:33:39');
INSERT INTO `customer_profiles` VALUES ('624', '+49231 58693108', 'call', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:34:11', '2021-07-04 19:34:11');
INSERT INTO `customer_profiles` VALUES ('625', 'info@woodstyle360.de', 'email', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:34:27', '2021-07-04 19:34:27');
INSERT INTO `customer_profiles` VALUES ('626', 'Küferstr. 10, 45731 Waltrop', 'address', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:34:58', '2021-07-04 19:34:58');
INSERT INTO `customer_profiles` VALUES ('627', 'www.woodstyle360.de', 'www', '138', null, null, '0', '1', '0', null, '0', '138', null, '2021-07-04 19:35:34', '2021-07-04 19:35:34');
INSERT INTO `customer_profiles` VALUES ('628', '139', 'contact-card', '139', null, null, '0', '1', '0', null, '0', '139', null, '2021-07-04 19:43:24', '2021-07-04 19:43:24');
INSERT INTO `customer_profiles` VALUES ('629', '139', 'contact-card', '139', null, null, '1', '1', '0', null, '0', '139', null, '2021-07-04 19:43:24', '2021-07-04 19:43:24');
INSERT INTO `customer_profiles` VALUES ('630', '+4916090321008', 'whatsapp', '120', null, null, '0', '1', '0', null, '1', '120', '120', '2021-07-04 19:53:20', '2021-07-04 19:54:36');
INSERT INTO `customer_profiles` VALUES ('631', '+4916090321008', 'text', '120', null, null, '0', '1', '0', null, '8', '120', '120', '2021-07-04 19:53:28', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('632', 'en@friseurzubehoer24.de', 'email', '120', null, null, '0', '1', '0', null, '7', '120', '120', '2021-07-04 19:53:48', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('633', 'eisenwerkstrasse 63', 'address', '120', null, null, '0', '1', '0', null, '6', '120', '120', '2021-07-04 19:54:02', '2021-07-04 19:54:50');
INSERT INTO `customer_profiles` VALUES ('634', '+493458838282', 'call', '102', null, null, '0', '1', '0', null, '3', '102', '102', '2021-07-05 13:44:54', '2021-07-06 22:26:34');
INSERT INTO `customer_profiles` VALUES ('635', '+49463828294', 'whatsapp', '102', null, null, '0', '1', '0', null, '0', '102', '102', '2021-07-05 13:44:58', '2021-07-06 22:26:34');
INSERT INTO `customer_profiles` VALUES ('636', '+49462829422', 'text', '102', null, null, '0', '1', '0', null, '1', '102', '102', '2021-07-05 13:45:02', '2021-07-06 22:26:34');
INSERT INTO `customer_profiles` VALUES ('637', 'abdulwahab@gmail.com', 'email', '102', null, null, '0', '1', '0', null, '2', '102', '102', '2021-07-05 13:45:13', '2021-07-06 22:26:34');
INSERT INTO `customer_profiles` VALUES ('638', '140', 'contact-card', '140', null, null, '0', '1', '0', null, '0', '140', null, '2021-07-05 15:20:39', '2021-07-05 15:20:39');
INSERT INTO `customer_profiles` VALUES ('639', '140', 'contact-card', '140', null, null, '1', '1', '0', null, '0', '140', null, '2021-07-05 15:20:39', '2021-07-05 15:20:39');
INSERT INTO `customer_profiles` VALUES ('640', 'r.raufie', 'instagram', '140', null, null, '0', '1', '0', null, '0', '140', null, '2021-07-05 15:21:28', '2021-07-05 15:21:28');
INSERT INTO `customer_profiles` VALUES ('641', '141', 'contact-card', '141', null, null, '0', '1', '0', null, '0', '141', null, '2021-07-05 15:36:16', '2021-07-05 15:36:16');
INSERT INTO `customer_profiles` VALUES ('642', '141', 'contact-card', '141', null, null, '1', '1', '0', null, '0', '141', null, '2021-07-05 15:36:16', '2021-07-05 15:36:16');
INSERT INTO `customer_profiles` VALUES ('644', 'sivasampoognanasegaran', 'tiktok', '141', null, null, '0', '1', '0', null, '2', '141', '141', '2021-07-05 15:38:05', '2021-07-05 15:43:21');
INSERT INTO `customer_profiles` VALUES ('645', '+4917623776012', 'whatsapp', '141', null, null, '0', '1', '0', null, '4', '141', '141', '2021-07-05 15:38:29', '2021-07-05 15:43:21');
INSERT INTO `customer_profiles` VALUES ('646', 'Markgnanam', 'facebook', '141', null, null, '0', '1', '0', null, '1', '141', '141', '2021-07-05 15:38:47', '2021-07-05 15:43:19');
INSERT INTO `customer_profiles` VALUES ('647', '+4917623776012', 'call', '141', null, null, '0', '1', '0', null, '3', '141', '141', '2021-07-05 15:39:28', '2021-07-05 15:43:21');
INSERT INTO `customer_profiles` VALUES ('648', 'markgnanam', 'instagram', '141', null, null, '0', '1', '0', null, '0', '141', '141', '2021-07-05 21:19:46', '2021-07-05 21:22:52');
INSERT INTO `customer_profiles` VALUES ('649', 'markgnanam', 'youtube', '141', null, null, '0', '1', '0', null, '0', '141', null, '2021-07-05 21:20:04', '2021-07-05 21:20:04');
INSERT INTO `customer_profiles` VALUES ('650', 'markgnanam', 'snapchat', '141', null, null, '0', '1', '0', null, '0', '141', null, '2021-07-05 21:20:20', '2021-07-05 21:20:20');
INSERT INTO `customer_profiles` VALUES ('652', '142', 'contact-card', '142', null, null, '0', '1', '0', null, '0', '142', null, '2021-07-06 01:28:36', '2021-07-06 01:28:36');
INSERT INTO `customer_profiles` VALUES ('653', '142', 'contact-card', '142', null, null, '1', '1', '0', null, '0', '142', null, '2021-07-06 01:28:36', '2021-07-06 01:28:36');
INSERT INTO `customer_profiles` VALUES ('654', 'mus7iii', 'instagram', '142', null, null, '0', '1', '0', null, '0', '142', null, '2021-07-06 01:29:08', '2021-07-06 01:29:08');
INSERT INTO `customer_profiles` VALUES ('655', 'mustafa_hk', 'snapchat', '142', null, null, '0', '1', '0', null, '0', '142', null, '2021-07-06 01:29:35', '2021-07-06 01:29:35');
INSERT INTO `customer_profiles` VALUES ('656', '+4915114369336', 'whatsapp', '142', null, null, '0', '1', '0', null, '0', '142', null, '2021-07-06 01:29:51', '2021-07-06 01:29:51');
INSERT INTO `customer_profiles` VALUES ('657', '143', 'contact-card', '143', null, null, '0', '1', '0', null, '0', '143', null, '2021-07-06 01:35:03', '2021-07-06 01:35:03');
INSERT INTO `customer_profiles` VALUES ('658', '143', 'contact-card', '143', null, null, '1', '1', '0', null, '0', '143', null, '2021-07-06 01:35:03', '2021-07-06 01:35:03');
INSERT INTO `customer_profiles` VALUES ('659', '144', 'contact-card', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 12:57:11', '2021-07-06 12:57:11');
INSERT INTO `customer_profiles` VALUES ('660', '144', 'contact-card', '144', null, null, '1', '1', '0', null, '0', '144', null, '2021-07-06 12:57:11', '2021-07-06 12:57:11');
INSERT INTO `customer_profiles` VALUES ('662', '+49017698557980', 'whatsapp', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 12:59:54', '2021-07-06 12:59:54');
INSERT INTO `customer_profiles` VALUES ('663', '+49017698557980', 'call', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 13:00:05', '2021-07-06 13:00:05');
INSERT INTO `customer_profiles` VALUES ('664', '+49017698557980', 'text', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 13:00:14', '2021-07-06 13:00:14');
INSERT INTO `customer_profiles` VALUES ('665', '@biadeluca', 'telegram', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 13:01:06', '2021-07-06 13:01:06');
INSERT INTO `customer_profiles` VALUES ('666', 'bianca.cagatay.deluca@gmail.com', 'email', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 13:01:58', '2021-07-06 13:01:58');
INSERT INTO `customer_profiles` VALUES ('667', '@bia.cagatay.deluca', 'instagram', '144', null, null, '0', '1', '0', null, '0', '144', null, '2021-07-06 13:07:06', '2021-07-06 13:07:06');
INSERT INTO `customer_profiles` VALUES ('668', '145', 'contact-card', '145', null, null, '0', '1', '0', null, '0', '145', null, '2021-07-06 15:36:21', '2021-07-06 15:36:21');
INSERT INTO `customer_profiles` VALUES ('669', '145', 'contact-card', '145', null, null, '1', '1', '0', null, '0', '145', null, '2021-07-06 15:36:21', '2021-07-06 15:36:21');
INSERT INTO `customer_profiles` VALUES ('670', '_1907_emir', 'instagram', '145', null, null, '0', '1', '0', null, '0', '145', null, '2021-07-06 15:36:42', '2021-07-06 15:36:42');
INSERT INTO `customer_profiles` VALUES ('671', '146', 'contact-card', '146', null, null, '0', '1', '0', null, '0', '146', null, '2021-07-06 15:40:45', '2021-07-06 15:40:45');
INSERT INTO `customer_profiles` VALUES ('672', '146', 'contact-card', '146', null, null, '1', '1', '0', null, '0', '146', null, '2021-07-06 15:40:45', '2021-07-06 15:40:45');
INSERT INTO `customer_profiles` VALUES ('673', 'enesoe1', 'instagram', '146', null, null, '0', '1', '0', null, '0', '146', null, '2021-07-06 15:41:08', '2021-07-06 15:41:08');
INSERT INTO `customer_profiles` VALUES ('674', 'enesi04', 'snapchat', '146', null, null, '0', '1', '0', null, '0', '146', null, '2021-07-06 15:41:24', '2021-07-06 15:41:24');
INSERT INTO `customer_profiles` VALUES ('675', '147', 'contact-card', '147', null, null, '0', '1', '0', null, '0', '147', null, '2021-07-06 15:50:22', '2021-07-06 15:50:22');
INSERT INTO `customer_profiles` VALUES ('676', '147', 'contact-card', '147', null, null, '1', '1', '0', null, '0', '147', null, '2021-07-06 15:50:22', '2021-07-06 15:50:22');
INSERT INTO `customer_profiles` VALUES ('677', '148', 'contact-card', '148', null, null, '0', '1', '0', null, '0', '148', null, '2021-07-06 15:50:38', '2021-07-06 15:50:38');
INSERT INTO `customer_profiles` VALUES ('678', '148', 'contact-card', '148', null, null, '1', '1', '0', null, '0', '148', null, '2021-07-06 15:50:38', '2021-07-06 15:50:38');
INSERT INTO `customer_profiles` VALUES ('679', 'salim_salim11', 'instagram', '147', null, null, '0', '1', '0', null, '0', '147', '147', '2021-07-06 15:51:39', '2021-07-06 21:44:48');
INSERT INTO `customer_profiles` VALUES ('681', 'https://www.facebook.com/profile.php?id=100000349009137', 'facebook', '148', null, null, '0', '1', '0', null, '0', '148', '148', '2021-07-06 15:55:13', '2021-07-06 15:55:20');
INSERT INTO `customer_profiles` VALUES ('682', 'fatihca44', 'instagram', '148', null, null, '0', '1', '0', null, '0', '148', null, '2021-07-06 16:00:36', '2021-07-06 16:00:36');
INSERT INTO `customer_profiles` VALUES ('683', '149', 'contact-card', '149', null, null, '0', '1', '0', null, '0', '149', null, '2021-07-06 19:50:27', '2021-07-06 19:50:27');
INSERT INTO `customer_profiles` VALUES ('684', '149', 'contact-card', '149', null, null, '1', '1', '0', null, '0', '149', null, '2021-07-06 19:50:27', '2021-07-06 19:50:27');
INSERT INTO `customer_profiles` VALUES ('685', 'eazyworld', 'instagram', '149', null, null, '0', '1', '0', null, '0', '149', null, '2021-07-06 19:52:01', '2021-07-06 19:52:01');
INSERT INTO `customer_profiles` VALUES ('686', '+4915757836211', 'whatsapp', '147', null, null, '0', '1', '0', null, '0', '147', null, '2021-07-06 21:44:33', '2021-07-06 21:44:33');
INSERT INTO `customer_profiles` VALUES ('687', 'https://www.livescore.com/en/football/euro-2020/semi-finals/italy-vs-spain/80024/', 'www', '148', null, null, '0', '1', '0', null, '0', '148', null, '2021-07-06 23:30:37', '2021-07-06 23:30:37');
INSERT INTO `customer_profiles` VALUES ('688', 'fatihc1453@gmail.com', 'email', '148', null, null, '0', '1', '0', null, '0', '148', null, '2021-07-06 23:33:19', '2021-07-06 23:33:19');

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for login_histories
-- ----------------------------
DROP TABLE IF EXISTS `login_histories`;
CREATE TABLE `login_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of login_histories
-- ----------------------------

-- ----------------------------
-- Table structure for menus
-- ----------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `css_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `has_sub_menus` int(11) DEFAULT NULL,
  `is_sub_menu` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of menus
-- ----------------------------
INSERT INTO `menus` VALUES ('1', 'Customers', 'customers', '0', 'fa fa-users', '1', '1', '0', '0', '1', '1', '2021-03-06 15:29:23', '2020-03-31 19:56:25');
INSERT INTO `menus` VALUES ('2', 'Update Customer', 'update_customer', '1', 'fa fa-circle-o', '2', '1', '0', '0', '1', '1', '2021-03-06 15:29:23', '2020-03-31 19:56:25');
INSERT INTO `menus` VALUES ('3', 'Transactions', 'transactions', '0', 'fa fa-user', '3', '0', '0', '0', '1', '1', '2021-03-06 15:29:23', '2021-03-06 15:29:23');
INSERT INTO `menus` VALUES ('4', 'Profiles', 'profiles', '0', 'fa fa-circle-o', '4', '1', '0', '0', '1', '1', '2021-03-06 15:29:23', '2021-03-06 15:29:23');
INSERT INTO `menus` VALUES ('5', 'Add Profile', 'add_profile', '4', 'fa fa-circle-o', '5', '1', '0', '0', '1', '1', '2021-03-06 15:29:23', '2021-03-06 15:29:23');
INSERT INTO `menus` VALUES ('6', 'Update Profile', 'update_profile', '4', 'fa fa-circle-o', '6', '1', '0', '0', '1', '1', '2021-03-06 15:29:23', '2021-03-06 15:29:23');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2014_10_12_000000_create_users_table', '1');
INSERT INTO `migrations` VALUES ('2', '2014_10_12_100000_create_password_resets_table', '1');
INSERT INTO `migrations` VALUES ('3', '2016_06_01_000001_create_oauth_auth_codes_table', '1');
INSERT INTO `migrations` VALUES ('4', '2016_06_01_000002_create_oauth_access_tokens_table', '1');
INSERT INTO `migrations` VALUES ('5', '2016_06_01_000003_create_oauth_refresh_tokens_table', '1');
INSERT INTO `migrations` VALUES ('6', '2016_06_01_000004_create_oauth_clients_table', '1');
INSERT INTO `migrations` VALUES ('7', '2016_06_01_000005_create_oauth_personal_access_clients_table', '1');
INSERT INTO `migrations` VALUES ('8', '2019_08_19_000000_create_failed_jobs_table', '1');
INSERT INTO `migrations` VALUES ('9', '2021_03_01_052014_create_activities_table', '1');
INSERT INTO `migrations` VALUES ('10', '2021_03_01_065414_create_configs_table', '2');
INSERT INTO `migrations` VALUES ('11', '2021_03_05_102619_create_user_groups_table', '2');
INSERT INTO `migrations` VALUES ('12', '2021_03_05_110102_create_menus_table', '3');
INSERT INTO `migrations` VALUES ('13', '2021_03_05_123458_create_login_histories_table', '4');
INSERT INTO `migrations` VALUES ('14', '2021_03_25_071735_create_profile_types_table', '5');
INSERT INTO `migrations` VALUES ('15', '2021_03_25_071809_create_profiles_table', '5');
INSERT INTO `migrations` VALUES ('16', '2021_03_25_072221_create_customer_profiles_table', '5');
INSERT INTO `migrations` VALUES ('17', '2021_04_02_051625_create_business_infos_table', '6');
INSERT INTO `migrations` VALUES ('18', '2021_04_03_102709_create_contact_cards_table', '7');

-- ----------------------------
-- Table structure for oauth_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of oauth_access_tokens
-- ----------------------------
INSERT INTO `oauth_access_tokens` VALUES ('008807112122d1448bea8a3aa6deafc4bf397addb03157cf5011ecfeeb395cf3601b65af9309b0e5', '67', '3', 'authToken', '[]', '1', '2021-05-08 00:45:09', '2021-05-08 00:45:09', '2022-05-07 21:45:09');
INSERT INTO `oauth_access_tokens` VALUES ('011afd20a690e7199b202b6a0f93ed3eea2372bb5e761a1284177ee80a2fe7a67903e0c80f3513fd', '120', '3', 'authToken', '[]', '0', '2021-06-30 16:00:31', '2021-06-30 16:00:31', '2022-06-30 13:00:31');
INSERT INTO `oauth_access_tokens` VALUES ('015d6464256473c06d95afd0e2128d801f48bafac44cec47c060121b4c515c1463abd47b8094e6e1', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:54:26', '2021-04-19 01:54:26', '2022-04-18 22:54:26');
INSERT INTO `oauth_access_tokens` VALUES ('01cd21a4ff45d1a498bb11a18751d6e5b332279c0b56b003d59f2e06e5bf14bca911633e56ef403b', '9', '3', 'authToken', '[]', '1', '2021-04-16 18:09:03', '2021-04-16 18:09:03', '2022-04-16 15:09:03');
INSERT INTO `oauth_access_tokens` VALUES ('01cf30df3b0f063742d80a7991fbe5aa88bb4fca1c70551e57880ceaba720d03e7765e10180c9b05', '83', '3', 'authToken', '[]', '1', '2021-06-23 18:29:32', '2021-06-23 18:29:32', '2022-06-23 15:29:32');
INSERT INTO `oauth_access_tokens` VALUES ('0270b303d1f361ac976186d63ceeff6dcbc95ebd296051a644e54e948ac1ebd0205dd1aadac4362e', '8', '3', 'authToken', '[]', '0', '2021-05-05 16:08:36', '2021-05-05 16:08:36', '2022-05-05 13:08:36');
INSERT INTO `oauth_access_tokens` VALUES ('02e76315442971e1f990d82450af247e95c109d983eaea38f87005f8fd5a98b948008e0736b9c616', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:02:38', '2021-04-21 23:02:38', '2022-04-21 20:02:38');
INSERT INTO `oauth_access_tokens` VALUES ('0373095852a1c1e26887c1e747e99f710dea358d6236a1ad8059ea80274adfaa9652c0cfa93eab18', '67', '3', 'authToken', '[]', '0', '2021-05-26 01:04:49', '2021-05-26 01:04:49', '2022-05-25 22:04:49');
INSERT INTO `oauth_access_tokens` VALUES ('037822127d8bdf79dc4d6414a66c423b37c15638ce0d7c07bb7fd71fd0ec3b2c1728b7530b279124', '8', '3', 'authToken', '[]', '0', '2021-04-20 22:49:08', '2021-04-20 22:49:08', '2022-04-20 19:49:08');
INSERT INTO `oauth_access_tokens` VALUES ('038e4ff362c38c52c9c43d4f34c6f2738e124a3af178e3058d59e2e5baf9dd08cbd753be94a20439', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:07:03', '2021-05-04 01:07:03', '2022-05-03 22:07:03');
INSERT INTO `oauth_access_tokens` VALUES ('039d8d8964c885362ac8bc51714264610ced7c0c9e3b0ba0acce403c344647409767a8072233b7a4', '8', '3', 'authToken', '[]', '0', '2021-06-22 21:44:48', '2021-06-22 21:44:48', '2022-06-22 18:44:48');
INSERT INTO `oauth_access_tokens` VALUES ('03b3ff1bce8dc18c49be7ceeba729338fafd2cc009664e6cc4cdc45b0349188327ae29173a9e9e67', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:57:15', '2021-04-21 23:57:15', '2022-04-21 20:57:15');
INSERT INTO `oauth_access_tokens` VALUES ('03c910e9f597aa6cd86abfbebc15ccf2bc8256df16be34fe308e3718ff45d612d13a3c13f37531ea', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:07:01', '2021-05-03 00:07:01', '2022-05-02 21:07:01');
INSERT INTO `oauth_access_tokens` VALUES ('03ed168d1e732f068a3544ebfce6eb4eab8a9a1ea5b23bc1ba81d0ae33939fbd27c2951cff227a31', '75', '3', 'authToken', '[]', '0', '2021-05-25 11:37:54', '2021-05-25 11:37:54', '2022-05-25 08:37:54');
INSERT INTO `oauth_access_tokens` VALUES ('03fd798cd2b02f914fa94d9169140c900fd8b72021fd8e76ea90eed4b30e93f2b6ccef2917613cf9', '89', '3', 'authToken', '[]', '1', '2021-06-21 18:40:51', '2021-06-21 18:40:51', '2022-06-21 15:40:51');
INSERT INTO `oauth_access_tokens` VALUES ('040e71a947094d63e5f74a318643817b371a96d69190b9dec16b27628a75b8b94cf879ef64e2b6f9', '130', '3', 'authToken', '[]', '0', '2021-07-03 19:46:07', '2021-07-03 19:46:07', '2022-07-03 16:46:07');
INSERT INTO `oauth_access_tokens` VALUES ('04f5934652a8390f6d75f5017b4f00931106b95d1935af6d1c8c74e08392ed2073ecffd278c3dddd', '17', '3', 'authToken', '[]', '0', '2021-04-16 14:32:37', '2021-04-16 14:32:37', '2022-04-16 11:32:37');
INSERT INTO `oauth_access_tokens` VALUES ('050416cb6c3a2ec582396cab53523aa60e2b1781d794ecb0380f156e7652dc7248218b8c1887a401', '8', '3', 'authToken', '[]', '0', '2021-04-30 14:30:43', '2021-04-30 14:30:43', '2022-04-30 11:30:43');
INSERT INTO `oauth_access_tokens` VALUES ('0527dbfb7578da61d4bfccc5fb89c2feff032778788a9e0bcea106522576203220f89c11964f0294', '67', '3', 'authToken', '[]', '0', '2021-05-24 20:35:37', '2021-05-24 20:35:37', '2022-05-24 17:35:37');
INSERT INTO `oauth_access_tokens` VALUES ('0610d957701cfd029fd29196889caae743a0c3348b47ff692bc266dee2860feb22160ce409feda65', '96', '3', 'authToken', '[]', '1', '2021-06-23 20:28:53', '2021-06-23 20:28:53', '2022-06-23 17:28:53');
INSERT INTO `oauth_access_tokens` VALUES ('06522c4592e21c85e3d5568b42b240b62aaf2b7d02693ba9eae3c1ba0726a32b314f582011212745', '83', '3', 'authToken', '[]', '1', '2021-06-20 20:17:12', '2021-06-20 20:17:12', '2022-06-20 17:17:12');
INSERT INTO `oauth_access_tokens` VALUES ('06582c0ea74b27f40ec69becfb2014d8036dab58951ea4cf70296c81f0eca48f2ccf00a7078ecd1d', '75', '3', 'authToken', '[]', '0', '2021-06-18 23:34:13', '2021-06-18 23:34:13', '2022-06-18 20:34:13');
INSERT INTO `oauth_access_tokens` VALUES ('071cde1e723caf21d4ea58aad5a7210e07324f8bcbc66ac46ccefcf0e1b0909a3d15249a78544044', '9', '3', 'authToken', '[]', '1', '2021-04-16 13:38:21', '2021-04-16 13:38:21', '2022-04-16 10:38:21');
INSERT INTO `oauth_access_tokens` VALUES ('072c2d58003aa8520a7d191fc6001dfa8d35f19cb8b02911b8036a17d525926c6e257bd84a0114c0', '144', '3', 'authToken', '[]', '0', '2021-07-06 12:57:11', '2021-07-06 12:57:11', '2022-07-06 09:57:11');
INSERT INTO `oauth_access_tokens` VALUES ('07349bd7ce371006e8aeb6bf07d21343a484c3bd8d61f5b0e72c7b80e8f13a74c0a2c403fc944440', '96', '3', 'authToken', '[]', '1', '2021-06-23 20:22:42', '2021-06-23 20:22:42', '2022-06-23 17:22:42');
INSERT INTO `oauth_access_tokens` VALUES ('0764e25aee1b3dfd2d28bc0618ed0dfe5be34e833c770f7fe8c1440f9a0f021cbbb0f8637f20b658', '8', '3', 'authToken', '[]', '1', '2021-05-19 13:09:26', '2021-05-19 13:09:26', '2022-05-19 10:09:26');
INSERT INTO `oauth_access_tokens` VALUES ('077fcf003850408670751c912d8288132773d5d6363e89be6d138c1d51a5e1dd2398065845e5e75b', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:52:59', '2021-04-21 23:52:59', '2022-04-21 20:52:59');
INSERT INTO `oauth_access_tokens` VALUES ('07c2f8c7edd871a713e129ac312b1d31cc0c9db3afdae7499d6f711b4966649583858143b0c28ddb', '8', '3', 'authToken', '[]', '1', '2021-04-22 00:46:13', '2021-04-22 00:46:13', '2022-04-21 21:46:13');
INSERT INTO `oauth_access_tokens` VALUES ('07cfc144ee30815450ad07d1f7910fcaf9d1153e14c68e408ece0d569b20eefbe738c4e411989208', '53', '3', 'authToken', '[]', '1', '2021-04-22 01:04:45', '2021-04-22 01:04:45', '2022-04-21 22:04:45');
INSERT INTO `oauth_access_tokens` VALUES ('08ead269b61b29b1021c093f83838997aded99651dfe265e93a25f3d9840b12058891bd5d60ce56e', '8', '3', 'authToken', '[]', '1', '2021-05-01 15:49:23', '2021-05-01 15:49:23', '2022-05-01 12:49:23');
INSERT INTO `oauth_access_tokens` VALUES ('09dfbfaf7d6c268ecfbab7432f8119829d5bbd967be995da62f21fa0a23c3392ef358cf02572a96a', '66', '3', 'authToken', '[]', '1', '2021-06-02 23:03:23', '2021-06-02 23:03:23', '2022-06-02 20:03:23');
INSERT INTO `oauth_access_tokens` VALUES ('09f8b84646b0ef649b277a43c6e154a7d88ed921882ea9286fe5906baa9f3383769dd0937183792a', '8', '3', 'authToken', '[]', '0', '2021-05-25 21:13:12', '2021-05-25 21:13:12', '2022-05-25 18:13:12');
INSERT INTO `oauth_access_tokens` VALUES ('0aed3e3f7c1096d1e27ed00cae2ea7c7a4d43c8bb4d3c145241b2de6334dba2b4e0f7af235bf4bd1', '6', '3', 'authToken', '[]', '0', '2021-04-14 04:54:33', '2021-04-14 04:54:33', '2022-04-14 01:54:33');
INSERT INTO `oauth_access_tokens` VALUES ('0b0974f078cd297aeb90387bd7d7d0a9fea5d22a4bc78fe097112c8fa3aa822f7996bcecab7df7ca', '67', '3', 'authToken', '[]', '0', '2021-05-08 00:42:56', '2021-05-08 00:42:56', '2022-05-07 21:42:56');
INSERT INTO `oauth_access_tokens` VALUES ('0b5719ec6afab68be0089609b26005e8e7dd477d8ff65a1175cde6c96201fd505aab4a00cde31fed', '75', '3', 'authToken', '[]', '0', '2021-06-19 00:20:54', '2021-06-19 00:20:54', '2022-06-18 21:20:54');
INSERT INTO `oauth_access_tokens` VALUES ('0b670cb83273461ce9f99e287a44f0ec5dfccdb48c836d4d98d70978e3967ed5c428d020b0418ab1', '8', '3', 'authToken', '[]', '0', '2021-04-21 13:27:57', '2021-04-21 13:27:57', '2022-04-21 10:27:57');
INSERT INTO `oauth_access_tokens` VALUES ('0b8e205c78c93f04ddf09e6ecac837216dcd56e8d114d7d7630ff860d5718df909a54104213efce1', '110', '3', 'authToken', '[]', '0', '2021-07-06 19:22:17', '2021-07-06 19:22:17', '2022-07-06 16:22:17');
INSERT INTO `oauth_access_tokens` VALUES ('0ba9a5eb9b0f99f67cc74b50cdfa971b1607f0b7bbc14be6daafaa7ee556f3145bc35e39e39ff5e1', '11', '3', 'authToken', '[]', '0', '2021-04-14 20:02:53', '2021-04-14 20:02:53', '2022-04-14 17:02:53');
INSERT INTO `oauth_access_tokens` VALUES ('0bcc67f4150ea42376229883fc77d891820808cd1f2c9a9a6e2effc4fe0def554965c90a23cb6972', '85', '3', 'authToken', '[]', '1', '2021-06-21 18:30:53', '2021-06-21 18:30:53', '2022-06-21 15:30:53');
INSERT INTO `oauth_access_tokens` VALUES ('0bd737e527787e45dbbb7bfa786832fc26f59669501ae1f75bb8c8fa7f3e2aba63178d77b4caea4a', '8', '3', 'authToken', '[]', '0', '2021-06-09 22:40:58', '2021-06-09 22:40:58', '2022-06-09 19:40:58');
INSERT INTO `oauth_access_tokens` VALUES ('0bdac971b8367814ed487b9cf89fab80683297f4307a31ca0b63f2d12f53e48218c760451caf242f', '45', '3', 'authToken', '[]', '0', '2021-05-25 13:51:52', '2021-05-25 13:51:52', '2022-05-25 10:51:52');
INSERT INTO `oauth_access_tokens` VALUES ('0cd7e4be8c5102e553a7df2cc044266580ed8ba42f910f0fd9eb6adcac88380e894fa9709fe6a1d7', '135', '3', 'authToken', '[]', '0', '2021-07-03 22:23:53', '2021-07-03 22:23:53', '2022-07-03 19:23:53');
INSERT INTO `oauth_access_tokens` VALUES ('0cf7af9d3617d1a1571d0bdb9d1f4854cc5da49f7f4a26bbff0cb9897616227d07df1a586a25007f', '8', '3', 'authToken', '[]', '0', '2021-05-23 20:25:23', '2021-05-23 20:25:23', '2022-05-23 17:25:23');
INSERT INTO `oauth_access_tokens` VALUES ('0d6f78281c316e922a56fb1c00c637fff1add9318769acb2ae5a6a48a28c97e4cf8a33fe4721bdfb', '6', '3', 'authToken', '[]', '1', '2021-05-19 19:56:46', '2021-05-19 19:56:46', '2022-05-19 16:56:46');
INSERT INTO `oauth_access_tokens` VALUES ('0d91ba030ece6489c81a3c034fe1b39d510a97b1bc6513202edeb2e3cee6e16c11b6b4b3a6001125', '21', '3', 'authToken', '[]', '0', '2021-04-18 12:57:47', '2021-04-18 12:57:47', '2022-04-18 09:57:47');
INSERT INTO `oauth_access_tokens` VALUES ('0df73f4dba67a9e00d8a43db3c758f775e5c9176f347f71f8ddeb4cff6f3f2205f81baee793be5ff', '8', '3', 'authToken', '[]', '1', '2021-04-18 19:22:34', '2021-04-18 19:22:34', '2022-04-18 16:22:34');
INSERT INTO `oauth_access_tokens` VALUES ('0e1aa942760bbd2fffa1196f82937e627b87079713f6f1abe7dec58418f25800c4c63f5e2d8a84ac', '8', '3', 'authToken', '[]', '0', '2021-06-18 23:59:58', '2021-06-18 23:59:58', '2022-06-18 20:59:58');
INSERT INTO `oauth_access_tokens` VALUES ('0e3c7b5a1d8c3a5f2bec4e7a1fe11f5308a3e02ab9d7cd56e8d2fba27b5bdb9f50acc07463fb8240', '8', '3', 'authToken', '[]', '0', '2021-04-15 10:45:41', '2021-04-15 10:45:41', '2022-04-15 07:45:41');
INSERT INTO `oauth_access_tokens` VALUES ('0ea03cdee092182f9facd6c26291036c8e2a589590e33db2ba50a7d74d149b61d9d8d364ad0418c0', '73', '3', 'authToken', '[]', '1', '2021-06-01 14:06:42', '2021-06-01 14:06:42', '2022-06-01 11:06:42');
INSERT INTO `oauth_access_tokens` VALUES ('0fa3b978b8705d47bff874f0a1e08a949059c6f4681e643aa1eff17c0e0371bc15a68c09db42f318', '49', '3', 'authToken', '[]', '1', '2021-04-22 00:59:26', '2021-04-22 00:59:26', '2022-04-21 21:59:26');
INSERT INTO `oauth_access_tokens` VALUES ('0fc47b99231e55b7b5cc7db4f8d8c9e9bcc217362968df54eace26ad8ed2fcaa3877ea81aeb32321', '6', '3', 'authToken', '[]', '1', '2021-03-25 15:28:21', '2021-03-25 15:28:21', '2022-03-25 11:28:21');
INSERT INTO `oauth_access_tokens` VALUES ('100a31c7db810c5e3b100256bb8a9618b6ad4a508dc10f9386d7381ea4f1f9a5a7025250563578fd', '9', '3', 'authToken', '[]', '1', '2021-05-06 01:50:48', '2021-05-06 01:50:48', '2022-05-05 22:50:48');
INSERT INTO `oauth_access_tokens` VALUES ('10868cd3380488aeb0292dd55098f6008cb11750b34c4447e3dcd033429cbab7e63eb1329e2ddfa7', '66', '3', 'authToken', '[]', '1', '2021-06-23 18:02:36', '2021-06-23 18:02:36', '2022-06-23 15:02:36');
INSERT INTO `oauth_access_tokens` VALUES ('109ef3bdb46691e4e8c2fbc0a1f2172703fcb364ca4928b8df15e9b0c5f95a140f1382177da0dada', '47', '3', 'authToken', '[]', '0', '2021-04-21 12:59:10', '2021-04-21 12:59:10', '2022-04-21 09:59:10');
INSERT INTO `oauth_access_tokens` VALUES ('10a038f58a80df6e0059d72856c3477fbc8b804a7df33435a176614b33365f8c61729ec7b31324d2', '19', '3', 'authToken', '[]', '0', '2021-04-15 22:04:54', '2021-04-15 22:04:54', '2022-04-15 19:04:54');
INSERT INTO `oauth_access_tokens` VALUES ('10deeb04b727051b0cc5674fdcd6c2e2fd0c233ccaa92dbe1509383a2bd809a5f391ce5120fda2ab', '17', '3', 'authToken', '[]', '0', '2021-04-16 14:29:59', '2021-04-16 14:29:59', '2022-04-16 11:29:59');
INSERT INTO `oauth_access_tokens` VALUES ('1135a6aeeeea0c3be0179af3c6a79deee034982e333a97ff5bdb01d1e969d0f75b7684646f5f62af', '6', '3', 'authToken', '[]', '1', '2021-06-03 07:36:23', '2021-06-03 07:36:23', '2022-06-03 04:36:23');
INSERT INTO `oauth_access_tokens` VALUES ('1191fd13b6fdf3970d1f0daa39900b05906f7b2ac75f5b9c8bb71a0c2ec9876aa52173c3a5dd29ee', '8', '3', 'authToken', '[]', '1', '2021-04-29 15:01:41', '2021-04-29 15:01:41', '2022-04-29 12:01:41');
INSERT INTO `oauth_access_tokens` VALUES ('12170426fdbab34a07dcd9d51f8ddafae15bcdbc00da68aec28097ae765ef6625b56d2dfd0992bb7', '70', '3', 'authToken', '[]', '0', '2021-06-20 18:28:22', '2021-06-20 18:28:22', '2022-06-20 15:28:22');
INSERT INTO `oauth_access_tokens` VALUES ('1232df8caf4436544f8712569441292096e99e31fc4b5fb2b415bd58ebf00d9fb90ebf1a807b17bc', '119', '3', 'authToken', '[]', '1', '2021-07-06 19:21:59', '2021-07-06 19:21:59', '2022-07-06 16:21:59');
INSERT INTO `oauth_access_tokens` VALUES ('12495727e1f9eb92a66a9e65bd07fe0cef93db41d4463c147da6c223ee56d8457dd27482ebe766ef', '8', '3', 'authToken', '[]', '1', '2021-06-24 15:33:42', '2021-06-24 15:33:42', '2022-06-24 12:33:42');
INSERT INTO `oauth_access_tokens` VALUES ('12f41648c7e157e6296360bed7d7d2ae89a95f5a0f71aebcf34a3abd7af432802f975c3016699db5', '21', '3', 'authToken', '[]', '0', '2021-04-16 14:20:27', '2021-04-16 14:20:27', '2022-04-16 11:20:27');
INSERT INTO `oauth_access_tokens` VALUES ('1324a68109535af99e135dac35eb9817557c83e03ed5781affc01248584b1f08ade5f547432b2ee2', '6', '3', 'authToken', '[]', '0', '2021-04-14 10:59:27', '2021-04-14 10:59:27', '2022-04-14 07:59:27');
INSERT INTO `oauth_access_tokens` VALUES ('1347d5336c59a73628ba97a418e770576008622278c057ef88d4c44e59d38f1e9ff5125b4007f8f9', '82', '3', 'authToken', '[]', '1', '2021-06-02 23:05:40', '2021-06-02 23:05:40', '2022-06-02 20:05:40');
INSERT INTO `oauth_access_tokens` VALUES ('1387752872649d756aadbb99e6f76c3fd9d032de496b110ae96c5ef8ff2dda3eafa10c343f8c7d49', '54', '3', 'authToken', '[]', '1', '2021-04-22 01:05:40', '2021-04-22 01:05:40', '2022-04-21 22:05:40');
INSERT INTO `oauth_access_tokens` VALUES ('13d6b908882e5641d0a9b898e63ae382cfd8b9423adf9231d7d4e600441e90d0541be5c3211f7749', '102', '3', 'authToken', '[]', '1', '2021-06-24 10:18:54', '2021-06-24 10:18:54', '2022-06-24 07:18:54');
INSERT INTO `oauth_access_tokens` VALUES ('14714128ee4073cb7b65af24d76606989151d8a6b014e20473b5a101ee8f5ec2e5166fdabe3f26ef', '9', '3', 'authToken', '[]', '0', '2021-04-30 13:59:28', '2021-04-30 13:59:28', '2022-04-30 10:59:28');
INSERT INTO `oauth_access_tokens` VALUES ('14e5ee37dc722327cf32ce723f168d90327dd1ef7645dddefa022046a607159d068d6a700b5a78d3', '67', '3', 'authToken', '[]', '0', '2021-05-24 19:23:24', '2021-05-24 19:23:24', '2022-05-24 16:23:24');
INSERT INTO `oauth_access_tokens` VALUES ('15464798cc357e93538b1699c6faa7afe969a7cef3e4149ffd51c178f2d1651cda9f08711eb01185', '80', '3', 'authToken', '[]', '1', '2021-06-02 19:43:26', '2021-06-02 19:43:26', '2022-06-02 16:43:26');
INSERT INTO `oauth_access_tokens` VALUES ('15af59ab6ebcb16beec2dbb6d468aab0407bb68cf88042e27a70aeec0ab04fc8b56def36f9a06ed8', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:36:45', '2021-05-04 18:36:45', '2022-05-04 15:36:45');
INSERT INTO `oauth_access_tokens` VALUES ('1602284815c09ae5aa07ca47aa0496385ff504115bb5d4ec2750b3a517268e5ac1b8e989ae29a339', '93', '3', 'authToken', '[]', '1', '2021-06-23 18:05:49', '2021-06-23 18:05:49', '2022-06-23 15:05:49');
INSERT INTO `oauth_access_tokens` VALUES ('176424563f916d1f639db818e11848c7c1c849380e4690ebc66c3a78178013f5de1e0beef37cd35c', '83', '3', 'authToken', '[]', '0', '2021-06-16 00:23:11', '2021-06-16 00:23:11', '2022-06-15 21:23:11');
INSERT INTO `oauth_access_tokens` VALUES ('176a9fb199502d363129c8c9ac591464fb7c2de4c4b46a25fd3e874c3d35c4ff82169de38242f307', '6', '3', 'authToken', '[]', '1', '2021-04-23 00:38:29', '2021-04-23 00:38:29', '2022-04-22 21:38:29');
INSERT INTO `oauth_access_tokens` VALUES ('176d942362719f96e0c41780e9aed9d520646b09fc334977736fd3e71bcb5c1b0a922d9f6f4eabdf', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:34:37', '2021-05-04 18:34:37', '2022-05-04 15:34:37');
INSERT INTO `oauth_access_tokens` VALUES ('17854bb34d2ef285ffb569f7b394d0ff031a0f9dfd37cc70555b89c40ba70e9656fb1ae3f3c330f0', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:17:33', '2021-05-03 00:17:33', '2022-05-02 21:17:33');
INSERT INTO `oauth_access_tokens` VALUES ('183cf64dc7181c92191c84f546e452736c0b2a1d03b644093ffe007dfbbb6915f70b79589ef5ce62', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:26:51', '2021-06-23 20:26:51', '2022-06-23 17:26:51');
INSERT INTO `oauth_access_tokens` VALUES ('186f85d1644e0caef1b462f45867fbf2dd9a9ebd587fb257dc514754b6de6abeec30efa97476c80f', '6', '3', 'authToken', '[]', '0', '2021-05-24 06:57:34', '2021-05-24 06:57:34', '2022-05-24 03:57:34');
INSERT INTO `oauth_access_tokens` VALUES ('187d7a69d52deeaab541f877ba12a22f2e175086f3bcccf9e2bdb0d8586635bc1e80ddcb74813169', '6', '3', 'authToken', '[]', '0', '2021-04-22 17:02:52', '2021-04-22 17:02:52', '2022-04-22 14:02:52');
INSERT INTO `oauth_access_tokens` VALUES ('1981afebca2232020709d3ef66ceff219a326083d130655192d2204a7076abfaf8aa23979c4bd7d6', '8', '3', 'authToken', '[]', '0', '2021-05-01 15:32:06', '2021-05-01 15:32:06', '2022-05-01 12:32:06');
INSERT INTO `oauth_access_tokens` VALUES ('19f55224c017213e81d14fb380f608f02136a80e4e179e689a2bbba2c7e4d3307d4eb27f469061fc', '6', '3', 'authToken', '[]', '1', '2021-04-21 22:59:31', '2021-04-21 22:59:31', '2022-04-21 19:59:31');
INSERT INTO `oauth_access_tokens` VALUES ('1a7b761b32f8f1c79d36ed9bbf0f6c577be1957a6d21aaaa9cd1864a69ec819470a122ec74bfc496', '9', '3', 'authToken', '[]', '1', '2021-05-07 10:22:45', '2021-05-07 10:22:45', '2022-05-07 07:22:45');
INSERT INTO `oauth_access_tokens` VALUES ('1ad567502e454f16893bd0159053dae7f1362bcb7ab204e40bb799c68549b1af2438fd580582ea57', '50', '3', 'authToken', '[]', '1', '2021-04-22 01:00:54', '2021-04-22 01:00:54', '2022-04-21 22:00:54');
INSERT INTO `oauth_access_tokens` VALUES ('1b13170f5b715b20690c9211379b138046151853248a4e4b437837b053373064f23da463bb0218fc', '70', '3', 'authToken', '[]', '1', '2021-05-22 02:33:58', '2021-05-22 02:33:58', '2022-05-21 23:33:58');
INSERT INTO `oauth_access_tokens` VALUES ('1b2ec8def1ec3caebb59585f95bc4d827499ab3f2403efafbb41e4007aa9b07deefa3eef073767b1', '6', '3', 'authToken', '[]', '0', '2021-03-25 14:27:12', '2021-03-25 14:27:12', '2022-03-25 10:27:12');
INSERT INTO `oauth_access_tokens` VALUES ('1b8cf1f4a1eb7443998a7aca0e90d90226a9fd88ce212fd3ed8e617bcfd433b5f69fa7a62a42d9cc', '108', '3', 'authToken', '[]', '1', '2021-06-25 10:39:31', '2021-06-25 10:39:31', '2022-06-25 07:39:31');
INSERT INTO `oauth_access_tokens` VALUES ('1c01e313b1d0cf11feecf0007742564294063cb6c9cbe80fa34fa7eec9c0eb739dfb8e0541acbb3e', '8', '3', 'authToken', '[]', '1', '2021-05-12 14:01:14', '2021-05-12 14:01:14', '2022-05-12 11:01:14');
INSERT INTO `oauth_access_tokens` VALUES ('1c447c63fb88094bad054b42fba270d0535f4d8c0b89a7183291d7c6e83401d38e9483e78d406f9d', '8', '3', 'authToken', '[]', '1', '2021-05-05 19:37:41', '2021-05-05 19:37:41', '2022-05-05 16:37:41');
INSERT INTO `oauth_access_tokens` VALUES ('1c6f101fd138f911e418e238a9f2d161575976ef73766e8f5a9c44d48be5e84054742c17a38729c1', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:50:21', '2021-04-19 01:50:21', '2022-04-18 22:50:21');
INSERT INTO `oauth_access_tokens` VALUES ('1cfd145f3584552815427c5b45a3a2463576c7264e9232a54f0e3abc45e026c56c34583e6a4d681f', '83', '3', 'authToken', '[]', '0', '2021-06-23 20:28:39', '2021-06-23 20:28:39', '2022-06-23 17:28:39');
INSERT INTO `oauth_access_tokens` VALUES ('1d1419b22f182a0d449592d6702c624ee750ef988a57256859dd25a7f825b546da52eebbb3c3513a', '6', '3', 'authToken', '[]', '0', '2021-04-21 13:11:16', '2021-04-21 13:11:16', '2022-04-21 10:11:16');
INSERT INTO `oauth_access_tokens` VALUES ('1d932fb4b1e56f7a41c54a0ae6fdeacdbe3413aea11e0b100c46035e6c3acfc0e3c3744d3bc0d366', '9', '3', 'authToken', '[]', '1', '2021-04-17 14:40:17', '2021-04-17 14:40:17', '2022-04-17 11:40:17');
INSERT INTO `oauth_access_tokens` VALUES ('1db1129fec1feec9f1ca9f605948d37c5def8add5cbf064a9b5b738018c19357c4cfb68ffb28b616', '107', '3', 'authToken', '[]', '0', '2021-06-25 12:32:16', '2021-06-25 12:32:16', '2022-06-25 09:32:16');
INSERT INTO `oauth_access_tokens` VALUES ('1e1793877eb3219939e82744c395ce740c739e54a0b12b5e4c4d0b5d1d883b0d9a0dc695485b6525', '9', '3', 'authToken', '[]', '1', '2021-05-19 08:15:53', '2021-05-19 08:15:53', '2022-05-19 05:15:53');
INSERT INTO `oauth_access_tokens` VALUES ('1eb487f995a0e5db5e7013fbe5171ec7a0ac5cb0284ca8c61f4b0ac94a25b00a2be36c0c9307ca3e', '6', '3', 'authToken', '[]', '0', '2021-04-21 13:11:38', '2021-04-21 13:11:38', '2022-04-21 10:11:38');
INSERT INTO `oauth_access_tokens` VALUES ('1f75e1d8b79660b371d627d34a98010866bdff2fa7c5e82572f5dd0f2f98410e584865ba27ed2c2d', '8', '3', 'authToken', '[]', '0', '2021-04-12 15:43:49', '2021-04-12 15:43:49', '2022-04-12 12:43:49');
INSERT INTO `oauth_access_tokens` VALUES ('2054a4209d1e5b26b9c0ad877ed06d58391a8cf735606f08429ad24efb68d5870b092eb3642b2460', '7', '3', 'authToken', '[]', '0', '2021-05-03 00:13:57', '2021-05-03 00:13:57', '2022-05-02 21:13:57');
INSERT INTO `oauth_access_tokens` VALUES ('2085a33fcd1af5b3af32e33b0897beeaf0890cb6ef90425e1ea4205e1af357fa93ddaa8463da7d64', '8', '3', 'authToken', '[]', '0', '2021-05-26 19:14:57', '2021-05-26 19:14:57', '2022-05-26 16:14:57');
INSERT INTO `oauth_access_tokens` VALUES ('20c0b679b2f3753b9aa43241c174cce12ed4b74375dc074534cc00f9248e4698ecec5ebf69384398', '96', '3', 'authToken', '[]', '1', '2021-06-23 20:28:29', '2021-06-23 20:28:29', '2022-06-23 17:28:29');
INSERT INTO `oauth_access_tokens` VALUES ('20f5df7a8ab8e31b490b21e19b95b71e0c5e70fd60de2360731a37122c86f727ec6c7ef78663fc48', '8', '3', 'authToken', '[]', '1', '2021-04-21 00:56:33', '2021-04-21 00:56:33', '2022-04-20 21:56:33');
INSERT INTO `oauth_access_tokens` VALUES ('21e0f2f485474263acd52b2c978d81958f6bedb1ace7ce2f4566aeccaaca53dd4092576661282d66', '8', '3', 'authToken', '[]', '0', '2021-05-23 13:44:51', '2021-05-23 13:44:51', '2022-05-23 10:44:51');
INSERT INTO `oauth_access_tokens` VALUES ('2208a65dae57a248081a57ed483c7cf17acbf838c34c4cc7ab62f94a0d8cca086725891d74ac5558', '8', '3', 'authToken', '[]', '1', '2021-04-21 13:30:00', '2021-04-21 13:30:00', '2022-04-21 10:30:00');
INSERT INTO `oauth_access_tokens` VALUES ('220d6ee266155728722b43acd36130c6750bba08f1e827ce012d2d4c4deb1f348960e78f1891be86', '75', '3', 'authToken', '[]', '0', '2021-05-25 11:23:51', '2021-05-25 11:23:51', '2022-05-25 08:23:51');
INSERT INTO `oauth_access_tokens` VALUES ('2230e742973e160419f977bcc558013914e57215e9327130c459178925d1dcf154106a6bf810e890', '67', '3', 'authToken', '[]', '0', '2021-05-25 02:31:02', '2021-05-25 02:31:02', '2022-05-24 23:31:02');
INSERT INTO `oauth_access_tokens` VALUES ('2240a164f531408aa621e9dd99bd9ec2ae6166a514a047344717c57dade478d071fb2293ec55bc7d', '95', '3', 'authToken', '[]', '0', '2021-06-23 00:24:31', '2021-06-23 00:24:31', '2022-06-22 21:24:31');
INSERT INTO `oauth_access_tokens` VALUES ('22769c16e6edb1d8cc428d965b1b061a084ccc3e2836fe2aa86c652fc3d6cc1cde0f4a09329a240e', '45', '3', 'authToken', '[]', '0', '2021-04-28 02:39:01', '2021-04-28 02:39:01', '2022-04-27 23:39:01');
INSERT INTO `oauth_access_tokens` VALUES ('22b9383a7c7722b4d77dc99325cb1e02245599eb18244c3a2c198b67e3487e7ed7c5afa3ec3fd3cb', '67', '3', 'authToken', '[]', '0', '2021-05-26 17:49:23', '2021-05-26 17:49:23', '2022-05-26 14:49:23');
INSERT INTO `oauth_access_tokens` VALUES ('22c2aae6639a940b0f704d351c2b6fdd930d9afa24cbb5dce074caf0ba8a5c697e5604fd1c083a7a', '6', '3', 'authToken', '[]', '0', '2021-05-16 22:04:07', '2021-05-16 22:04:07', '2022-05-16 19:04:07');
INSERT INTO `oauth_access_tokens` VALUES ('22dc0b87a54b9065b485c90ae6cd1aa9dca5f1db0902a6ddf1bbe2c10d9a2599d1877f56c42b122f', '133', '3', 'authToken', '[]', '0', '2021-07-03 20:45:32', '2021-07-03 20:45:32', '2022-07-03 17:45:32');
INSERT INTO `oauth_access_tokens` VALUES ('2307828d65ef7b57d9e996211529d0ebb5ea0eb1e7ece67eec1b3eb3e7c7d8d9672b60c66220df52', '9', '3', 'authToken', '[]', '1', '2021-04-17 14:37:44', '2021-04-17 14:37:44', '2022-04-17 11:37:44');
INSERT INTO `oauth_access_tokens` VALUES ('23a3508b9dfed314444dbb61137260fa1edfa601d0205e2f0600f55927746396e1c0c62f7057473f', '8', '3', 'authToken', '[]', '0', '2021-06-09 22:11:45', '2021-06-09 22:11:45', '2022-06-09 19:11:45');
INSERT INTO `oauth_access_tokens` VALUES ('24559bd5aed2d8506e7acbd46a9adfabfcffac73140edb32761bc45bcf94fe9e08dca13c0efcb752', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:36:55', '2021-04-21 23:36:55', '2022-04-21 20:36:55');
INSERT INTO `oauth_access_tokens` VALUES ('245bab3faa17b0c05f92322d7e170d473974627391bfaed3da68a8be94fcebae054cc927754dccbb', '8', '3', 'authToken', '[]', '1', '2021-05-01 15:43:27', '2021-05-01 15:43:27', '2022-05-01 12:43:27');
INSERT INTO `oauth_access_tokens` VALUES ('246732e17451da4c83a6f66efb7a4e3862b004a3e246a8d565388d7d7d6f7966348a0c7d365f0bef', '67', '3', 'authToken', '[]', '0', '2021-05-23 21:33:37', '2021-05-23 21:33:37', '2022-05-23 18:33:37');
INSERT INTO `oauth_access_tokens` VALUES ('2496c882000fe14508dedf6934b855a28ff0043be3ede9ff56c4b41ea6f435f0de5aee7458f5b6a9', '25', '3', 'authToken', '[]', '1', '2021-04-17 14:31:37', '2021-04-17 14:31:37', '2022-04-17 11:31:37');
INSERT INTO `oauth_access_tokens` VALUES ('254cf7cb95ba73af092762aae0b2d24b4857248fa95fcff0ba30950b0df13913941b8d2cbb01c2b3', '8', '3', 'authToken', '[]', '0', '2021-04-18 18:16:05', '2021-04-18 18:16:05', '2022-04-18 15:16:05');
INSERT INTO `oauth_access_tokens` VALUES ('259a53fff5e1c607a65e1bdb2ed5267ddf8156070321a948d4e3530c3998dfeaf574a16818cea245', '123', '3', 'authToken', '[]', '0', '2021-06-30 21:30:04', '2021-06-30 21:30:04', '2022-06-30 18:30:04');
INSERT INTO `oauth_access_tokens` VALUES ('25d7743b2c9dfd9638ad97a77feb8e065b819ae3ac7d5f6fa6bc218ba0683858d1d47880ea07a34b', '96', '3', 'authToken', '[]', '1', '2021-06-23 20:27:10', '2021-06-23 20:27:10', '2022-06-23 17:27:10');
INSERT INTO `oauth_access_tokens` VALUES ('26a060e257aaf06946e1d2fe7e7156e51e8d54d4d801f35b98a2995feb3eced15522599861bc4001', '64', '3', 'authToken', '[]', '1', '2021-06-23 22:43:02', '2021-06-23 22:43:02', '2022-06-23 19:43:02');
INSERT INTO `oauth_access_tokens` VALUES ('26f1369fd99f3bb604e98690777472e66c2c67ee70f2fa1b09bd96ec2b8c2986a27c6016433834ab', '73', '3', 'authToken', '[]', '1', '2021-06-24 02:58:23', '2021-06-24 02:58:23', '2022-06-23 23:58:23');
INSERT INTO `oauth_access_tokens` VALUES ('275c2a18ff2e8301bc54c01cd065e061d773381b7f87deccce0126fd895299116cc987ce4172fb89', '28', '3', 'authToken', '[]', '0', '2021-04-18 02:02:41', '2021-04-18 02:02:41', '2022-04-17 23:02:41');
INSERT INTO `oauth_access_tokens` VALUES ('278614b734bf2db85bf93821aba6535fb5227d68adc1ed45a870c43c81e69c9f1182de1469f43851', '6', '3', 'authToken', '[]', '0', '2021-04-22 00:19:23', '2021-04-22 00:19:23', '2022-04-21 21:19:23');
INSERT INTO `oauth_access_tokens` VALUES ('27a05b239278eb015bdba0dae571230df2e938627986a9ba4699f6171b9ec1f8a2a193cbdc3a2b5f', '36', '3', 'authToken', '[]', '1', '2021-04-18 12:56:34', '2021-04-18 12:56:34', '2022-04-18 09:56:34');
INSERT INTO `oauth_access_tokens` VALUES ('27c6e63072e55d56ba30920b9722867c0506cf2d404e86ddb0fc636f94e58a4c0425b09e27a3e66b', '114', '3', 'authToken', '[]', '0', '2021-06-28 22:39:29', '2021-06-28 22:39:29', '2022-06-28 19:39:29');
INSERT INTO `oauth_access_tokens` VALUES ('27d74d8706142c60752df87659cb09fd5d438ba80bf8f20ec36cf442ea03d9ce419698514cbda94a', '67', '3', 'authToken', '[]', '0', '2021-05-24 17:25:06', '2021-05-24 17:25:06', '2022-05-24 14:25:06');
INSERT INTO `oauth_access_tokens` VALUES ('27e0c66c058e83635c9b64b0e4c37efed57b28ddd7221deeb741a471116ec8c66f456ab853250681', '105', '3', 'authToken', '[]', '0', '2021-06-24 22:38:32', '2021-06-24 22:38:32', '2022-06-24 19:38:32');
INSERT INTO `oauth_access_tokens` VALUES ('281a16072ee22f7bd11a254ae86601ded4235d445438f19ae315811449c422925adbed4cbcd40602', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:50:33', '2021-04-19 01:50:33', '2022-04-18 22:50:33');
INSERT INTO `oauth_access_tokens` VALUES ('28952ae214bb21ecf4925676f95acc1775cf5e7a37c59d91adc6fe51e638baa7b8e55656822fb5c4', '6', '3', 'authToken', '[]', '1', '2021-04-22 20:16:51', '2021-04-22 20:16:51', '2022-04-22 17:16:51');
INSERT INTO `oauth_access_tokens` VALUES ('28f43ae6045d3c630b8d9abb18b3cfe10df2c55e5c9b98e21554613dcd589398f778c3bfcbd10fcf', '110', '3', 'authToken', '[]', '1', '2021-07-04 23:50:08', '2021-07-04 23:50:08', '2022-07-04 20:50:08');
INSERT INTO `oauth_access_tokens` VALUES ('29dbda11e6e6b89eb7466544728d9d8ef8b2c77a7d32c8b3ef9d12e9c75c4d8655aeb2b2801ab36d', '78', '3', 'authToken', '[]', '0', '2021-05-26 00:52:31', '2021-05-26 00:52:31', '2022-05-25 21:52:31');
INSERT INTO `oauth_access_tokens` VALUES ('2a2b8953a71df5bdc4f23633cc7a7cd5b63b1f87b71112e95da277e46274ae81376201aa9760176d', '8', '3', 'authToken', '[]', '0', '2021-05-10 15:32:18', '2021-05-10 15:32:18', '2022-05-10 12:32:18');
INSERT INTO `oauth_access_tokens` VALUES ('2a55bf3be69c3f1df9384e5f5492f02b399184ce5ef4ed6c616fe65256ae167e0d63e3e1d613bbf3', '8', '3', 'authToken', '[]', '1', '2021-05-18 14:28:38', '2021-05-18 14:28:38', '2022-05-18 11:28:38');
INSERT INTO `oauth_access_tokens` VALUES ('2a9b42ea529557b15698f6b8aa5aec377e328c765920c1832fefc67fd51b86ae694e4f170e36cc4d', '8', '3', 'authToken', '[]', '1', '2021-05-20 23:45:01', '2021-05-20 23:45:01', '2022-05-20 20:45:01');
INSERT INTO `oauth_access_tokens` VALUES ('2ab07fe68c2bca24e5a352743c0c27ddba2b91de2e59ac4ac04e72f52086c0463f2dde419b34fa7d', '110', '3', 'authToken', '[]', '0', '2021-06-25 17:43:12', '2021-06-25 17:43:12', '2022-06-25 14:43:12');
INSERT INTO `oauth_access_tokens` VALUES ('2b0c36a44015f64d9ea2949b5b4881e5362258749005dbdf5308c70081650080cdcb171db566bbb1', '60', '3', 'authToken', '[]', '0', '2021-05-02 13:59:45', '2021-05-02 13:59:45', '2022-05-02 10:59:45');
INSERT INTO `oauth_access_tokens` VALUES ('2b5b0fdda86babe13afe286ff763ca2854d5bb22515582dcd225419a347bc27d2a670ffbe164811b', '8', '3', 'authToken', '[]', '0', '2021-04-12 15:26:33', '2021-04-12 15:26:33', '2022-04-12 12:26:33');
INSERT INTO `oauth_access_tokens` VALUES ('2b5b2eb0597deed0721f652c1b6c03fcfb81bc764ced0d5b9a651774faf38f5a033fec933913602f', '8', '3', 'authToken', '[]', '0', '2021-06-09 22:52:38', '2021-06-09 22:52:38', '2022-06-09 19:52:38');
INSERT INTO `oauth_access_tokens` VALUES ('2c204ae41623a7a859b8329b43a16cfaf8b1c1c38a6e6faeb2ccb26d65039a05343f5b9a3acdb729', '20', '3', 'authToken', '[]', '0', '2021-04-15 22:08:06', '2021-04-15 22:08:06', '2022-04-15 19:08:06');
INSERT INTO `oauth_access_tokens` VALUES ('2c682e0976eb030f31d0e4a3ead6d0d5dabae2f534317f9a10bd5320f9fa99758bfc93b0bd8d9dec', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:28:38', '2021-04-21 23:28:38', '2022-04-21 20:28:38');
INSERT INTO `oauth_access_tokens` VALUES ('2d0f39f5b663f6a54c7cb18960ee3218d558c1984b0eddefd6e3c57cc7877af3bfd4bdf1cbcbe862', '6', '3', 'authToken', '[]', '0', '2021-05-21 10:25:50', '2021-05-21 10:25:50', '2022-05-21 07:25:50');
INSERT INTO `oauth_access_tokens` VALUES ('2d26e52c2772e605c8b4d6e105cfdb91e95671f70e21babe751f4f1b33beb9aa7c74475740acc9c1', '8', '3', 'authToken', '[]', '1', '2021-04-22 00:45:42', '2021-04-22 00:45:42', '2022-04-21 21:45:42');
INSERT INTO `oauth_access_tokens` VALUES ('2d723ef35db76ef09cccd9db5e75ef7fbfd149e3edc2bce30544d177e2858aa222f90b4a6f1a55aa', '6', '3', 'authToken', '[]', '1', '2021-04-25 21:20:21', '2021-04-25 21:20:21', '2022-04-25 18:20:21');
INSERT INTO `oauth_access_tokens` VALUES ('2e48fcdb2473d980cbbc8a9bc9357ebbc2405ab03b59e8b93674dea28543de9a8081bcb93ad8ed5b', '73', '3', 'authToken', '[]', '0', '2021-06-24 02:47:48', '2021-06-24 02:47:48', '2022-06-23 23:47:48');
INSERT INTO `oauth_access_tokens` VALUES ('2e9b362814c07ba416c523bd7025343107e6ea95a9d942bf131ab06a12026507bc163660c00dd5e3', '9', '3', 'authToken', '[]', '1', '2021-04-16 17:59:03', '2021-04-16 17:59:03', '2022-04-16 14:59:03');
INSERT INTO `oauth_access_tokens` VALUES ('2ecc9f45a4ecaa7a5487798258c96396c29084a8f2ca6f784e096032553a728173913667f0c95219', '93', '3', 'authToken', '[]', '1', '2021-06-23 18:08:06', '2021-06-23 18:08:06', '2022-06-23 15:08:06');
INSERT INTO `oauth_access_tokens` VALUES ('2f6227ee01ac9d36172aac309cef0f18b73f4cb51ed4f226263bf7d97c0ca81901cae04b7c6a7de4', '83', '3', 'authToken', '[]', '0', '2021-06-23 20:28:39', '2021-06-23 20:28:39', '2022-06-23 17:28:39');
INSERT INTO `oauth_access_tokens` VALUES ('2fe4dcad93d241b7e865c373ccba34a8c479ee9fe0d9bf81cdffc393eca7b6b6628203f7526b6075', '8', '3', 'authToken', '[]', '0', '2021-05-05 01:47:49', '2021-05-05 01:47:49', '2022-05-04 22:47:49');
INSERT INTO `oauth_access_tokens` VALUES ('300be6c45dcbb942053c3dbf14e2d1e781521f9395ee820cce1d16b2c46d7cff8f0f17ec0aa6ded4', '83', '3', 'authToken', '[]', '0', '2021-06-06 16:31:20', '2021-06-06 16:31:20', '2022-06-06 13:31:20');
INSERT INTO `oauth_access_tokens` VALUES ('30d90f4e03b21eb1263169dd2298370209bb63c7627f13367603e9ef5acf9e103797fc1d59ff19b5', '134', '3', 'authToken', '[]', '0', '2021-07-03 21:07:47', '2021-07-03 21:07:47', '2022-07-03 18:07:47');
INSERT INTO `oauth_access_tokens` VALUES ('319c1b56600c06c9266725c9f23d7d65a2d2d515512f63b0c832f6d6053c20ff7dfaf6ac9a51a6b8', '51', '3', 'authToken', '[]', '1', '2021-04-22 01:03:05', '2021-04-22 01:03:05', '2022-04-21 22:03:05');
INSERT INTO `oauth_access_tokens` VALUES ('31ff3507a62e875e205c09fb87d3d4e5c3cdccefe4735e486d38655eee6423bec34734e36c850247', '9', '3', 'authToken', '[]', '1', '2021-04-16 18:13:43', '2021-04-16 18:13:43', '2022-04-16 15:13:43');
INSERT INTO `oauth_access_tokens` VALUES ('320eaccfc62e1aba6fad8e2d64366e8a44004884b16b3976096ea555019fb509c36e3a31b8292d9f', '8', '3', 'authToken', '[]', '0', '2021-05-12 13:59:32', '2021-05-12 13:59:32', '2022-05-12 10:59:32');
INSERT INTO `oauth_access_tokens` VALUES ('335c0e25b8000e781091333882a526bd984363b1b9b72a10b4b591df605fa9d88bd8d96014927b30', '6', '3', 'authToken', '[]', '0', '2021-04-28 14:46:33', '2021-04-28 14:46:33', '2022-04-28 11:46:33');
INSERT INTO `oauth_access_tokens` VALUES ('33a34553b43f0483f7febed2f3fc7702593303a4bfad537eb10893af687afbf225b9e9a84f5375c2', '6', '3', 'authToken', '[]', '0', '2021-04-14 14:37:07', '2021-04-14 14:37:07', '2022-04-14 11:37:07');
INSERT INTO `oauth_access_tokens` VALUES ('33f2c6f679473a6f2b0051c1fd026ff7f1e59f193e2c27cfd1d068658d81e65f8fe88a0efa9f16f2', '9', '3', 'authToken', '[]', '0', '2021-04-13 06:57:35', '2021-04-13 06:57:35', '2022-04-13 03:57:35');
INSERT INTO `oauth_access_tokens` VALUES ('346d0def17438582de6c2b129b88e46c231d6621d03f54b46cbdc5f82640a1f8258c8a4594cf7686', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:39:17', '2021-06-23 20:39:17', '2022-06-23 17:39:17');
INSERT INTO `oauth_access_tokens` VALUES ('349fd6c86f8bf332b9afb9d5921c8d4c68b4b9f1d3751b1539e65950d699340e7c1c0b2b38dcf629', '113', '3', 'authToken', '[]', '0', '2021-06-28 22:36:48', '2021-06-28 22:36:48', '2022-06-28 19:36:48');
INSERT INTO `oauth_access_tokens` VALUES ('351b8f489403ad44fc1e992c7ffa6b9eaaea43bd9e9babf477e843694af4d2a6cbad35fba36845e4', '43', '3', 'authToken', '[]', '0', '2021-04-18 20:42:46', '2021-04-18 20:42:46', '2022-04-18 17:42:46');
INSERT INTO `oauth_access_tokens` VALUES ('35659da9cbad594bd5fad10299002ac79f22189c05849cdeed92238eb957c5cb6b8bc67b4dfeae1c', '42', '3', 'authToken', '[]', '0', '2021-04-18 20:39:28', '2021-04-18 20:39:28', '2022-04-18 17:39:28');
INSERT INTO `oauth_access_tokens` VALUES ('35daa123971b829adbdd1336e9a9f9cd2043285b6164f6055ebe3ccd358618b3c6b2d774072d5564', '8', '3', 'authToken', '[]', '0', '2021-05-17 21:26:12', '2021-05-17 21:26:12', '2022-05-17 18:26:12');
INSERT INTO `oauth_access_tokens` VALUES ('362354702fb0257ec50ce6cac7f2a10dd7cbb19b23cd5e93971f671cd9f40518004d2cba2e75269e', '9', '3', 'authToken', '[]', '1', '2021-05-06 01:12:22', '2021-05-06 01:12:22', '2022-05-05 22:12:22');
INSERT INTO `oauth_access_tokens` VALUES ('3660072fb8374d0d48bbe4c661154dd99878887539e7da754f0aaf2c94decfccd95808ee989ddd3f', '8', '3', 'authToken', '[]', '0', '2021-05-27 00:24:41', '2021-05-27 00:24:41', '2022-05-26 21:24:41');
INSERT INTO `oauth_access_tokens` VALUES ('3683661ee83346f778342df550520a880286615455d239e051394d5b906698dadf7b0b5e073448ea', '101', '3', 'authToken', '[]', '1', '2021-06-24 02:04:34', '2021-06-24 02:04:34', '2022-06-23 23:04:34');
INSERT INTO `oauth_access_tokens` VALUES ('36965a7d39d0dd929b8a246cee4b84b758bd2a6ff3c7b13382eb62bc7454e5ad43bf6ab740884f4c', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:09:28', '2021-04-22 00:09:28', '2022-04-21 21:09:28');
INSERT INTO `oauth_access_tokens` VALUES ('36c814252007b8229ed9447682bde1607bde64302f9720344f630800119b8093d4abcf42a04fe422', '6', '3', 'authToken', '[]', '0', '2021-04-21 13:11:05', '2021-04-21 13:11:05', '2022-04-21 10:11:05');
INSERT INTO `oauth_access_tokens` VALUES ('36cc1f00953716e9e482e14548b4c142369828156255a88fbcf76e760c62a4706be09f97db565dbf', '75', '3', 'authToken', '[]', '0', '2021-05-25 11:34:43', '2021-05-25 11:34:43', '2022-05-25 08:34:43');
INSERT INTO `oauth_access_tokens` VALUES ('36df34fce254f8b88c35d64beef05bfffcd55d97f757829794d47508ec685dae378bde3ae9086fd6', '12', '3', 'authToken', '[]', '0', '2021-04-14 20:11:54', '2021-04-14 20:11:54', '2022-04-14 17:11:54');
INSERT INTO `oauth_access_tokens` VALUES ('3731c0f5262c8c16c020fc1570a78056b99f5a35dd4fe939d01e364b5bd4137e62b523096fead5e1', '8', '3', 'authToken', '[]', '0', '2021-05-04 22:54:57', '2021-05-04 22:54:57', '2022-05-04 19:54:57');
INSERT INTO `oauth_access_tokens` VALUES ('37c51d2fe14c4c7a7713b31e0cde161d78bdf429d11d364acedb8c19dc263537812e2800f38a396e', '107', '3', 'authToken', '[]', '1', '2021-06-25 11:07:34', '2021-06-25 11:07:34', '2022-06-25 08:07:34');
INSERT INTO `oauth_access_tokens` VALUES ('3809c8f7664c35b9dc60ecba7dca31977aaac212c0dc4da592a4ffb9f7da864a2ff052b5f386841c', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:06:23', '2021-04-22 00:06:23', '2022-04-21 21:06:23');
INSERT INTO `oauth_access_tokens` VALUES ('391685ee7959a35e4aefdaba5a2da99e76fa8ca94865da04de6c9d1c590ff9b11b215bad6d4e309b', '73', '3', 'authToken', '[]', '1', '2021-06-01 15:24:37', '2021-06-01 15:24:37', '2022-06-01 12:24:37');
INSERT INTO `oauth_access_tokens` VALUES ('39b58c16fff583816aa724be506f8756fb6f8de344e88f478b61e23f2e86877e7aeb4928c9cd9196', '45', '3', 'authToken', '[]', '0', '2021-04-19 01:02:34', '2021-04-19 01:02:34', '2022-04-18 22:02:34');
INSERT INTO `oauth_access_tokens` VALUES ('39d4e5adb7b4c04c3e30002e2c38b499da5a93cadb93f6c42fa7367ab891e079da8cb3ccb321f6ce', '8', '3', 'authToken', '[]', '0', '2021-05-02 13:58:59', '2021-05-02 13:58:59', '2022-05-02 10:58:59');
INSERT INTO `oauth_access_tokens` VALUES ('3a0de7f59cf5761ae0c9386f51228728c10b098cb5163eb9e4a54d61228146eeb02135b70cf349d9', '66', '3', 'authToken', '[]', '1', '2021-06-24 02:03:15', '2021-06-24 02:03:15', '2022-06-23 23:03:15');
INSERT INTO `oauth_access_tokens` VALUES ('3a1d9b56322b1d7983cca57a5df4bc04dcf9330c9b9a387f9ecffb2eca7d6dd2fd2776ba13db529c', '8', '3', 'authToken', '[]', '0', '2021-04-15 10:36:36', '2021-04-15 10:36:36', '2022-04-15 07:36:36');
INSERT INTO `oauth_access_tokens` VALUES ('3a308abcff5c3e3ec3b813ca07e1491b0d7e29ea6fa474768099d4ff36997f2c7e5e8be0481de0b3', '8', '3', 'authToken', '[]', '0', '2021-05-04 18:32:54', '2021-05-04 18:32:54', '2022-05-04 15:32:54');
INSERT INTO `oauth_access_tokens` VALUES ('3b43e8cdbc7eb3723ae7892f7c51d404667263e3afaf741ae686b20da2092d408bb4c456791cd57a', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:04:30', '2021-04-22 00:04:30', '2022-04-21 21:04:30');
INSERT INTO `oauth_access_tokens` VALUES ('3b95adff48d1b6a6c25fd74404778034a79f6cbf2d1385fc15d610e498920f33ac0699c4b3b7708a', '8', '3', 'authToken', '[]', '0', '2021-05-03 19:00:09', '2021-05-03 19:00:09', '2022-05-03 16:00:09');
INSERT INTO `oauth_access_tokens` VALUES ('3bb5b41eb76e8ef512dfc49657795cc152e7945801c950b9bb793f997e5a800544a0e6ba3e6671e1', '9', '3', 'authToken', '[]', '1', '2021-05-19 08:17:53', '2021-05-19 08:17:53', '2022-05-19 05:17:53');
INSERT INTO `oauth_access_tokens` VALUES ('3bc275bde1ead98e4258bd96cc3473a633fcf7b5f2c6e432e3dfb25b2b12969eb291de8907b7a42c', '106', '3', 'authToken', '[]', '0', '2021-06-25 17:06:29', '2021-06-25 17:06:29', '2022-06-25 14:06:29');
INSERT INTO `oauth_access_tokens` VALUES ('3cf4a00b126d843c00c5c306d002dccbc042e07809202348ce44ebd5936fb8026357f92a4e114952', '8', '3', 'authToken', '[]', '1', '2021-05-21 10:27:35', '2021-05-21 10:27:35', '2022-05-21 07:27:35');
INSERT INTO `oauth_access_tokens` VALUES ('3d1f9b26681ddeb99d1fbe92a7c0cae980e85e50b7d97eab7979604bf9c1e60b4037e9f326ef4ca6', '6', '3', 'authToken', '[]', '0', '2021-04-14 19:48:31', '2021-04-14 19:48:31', '2022-04-14 16:48:31');
INSERT INTO `oauth_access_tokens` VALUES ('3e23b0315e5a5bc235bac5290553432b175ac5d0d5520e83c02c56f03b6359e79424d8cd80196a63', '67', '3', 'authToken', '[]', '0', '2021-05-26 21:56:04', '2021-05-26 21:56:04', '2022-05-26 18:56:04');
INSERT INTO `oauth_access_tokens` VALUES ('3e70e6a972db5e9104b346d74bdbb30152c04a5fea3352d614ef3cc29e2f105ecd6e6ed55602de8a', '9', '3', 'authToken', '[]', '1', '2021-05-21 19:10:49', '2021-05-21 19:10:49', '2022-05-21 16:10:49');
INSERT INTO `oauth_access_tokens` VALUES ('3e8f454b675e294712f0ff85a9e960d327b3044ef86c5c632e1396de3bc71492a60c24978ed53416', '9', '3', 'authToken', '[]', '0', '2021-04-29 16:36:00', '2021-04-29 16:36:00', '2022-04-29 13:36:00');
INSERT INTO `oauth_access_tokens` VALUES ('3f1e6fdc5224d2a9a9878d087e9f50859f50ee59b13d59c01eb02b6d7b0aecb64aab49dc09e9cda3', '8', '3', 'authToken', '[]', '0', '2021-06-01 14:07:42', '2021-06-01 14:07:42', '2022-06-01 11:07:42');
INSERT INTO `oauth_access_tokens` VALUES ('3f8ea590623cd6a28217f2df036f70d8a33e5a41f3b786f9e493ead10beca636df749ad78fce563f', '14', '3', 'authToken', '[]', '0', '2021-04-14 21:18:50', '2021-04-14 21:18:50', '2022-04-14 18:18:50');
INSERT INTO `oauth_access_tokens` VALUES ('3fb50cb4824023c67dc6bbf30307e76f75814b07faf0dacd5f60df23665a9f08f454e16a27114600', '6', '3', 'authToken', '[]', '0', '2021-04-22 02:02:04', '2021-04-22 02:02:04', '2022-04-21 23:02:04');
INSERT INTO `oauth_access_tokens` VALUES ('40b6cc49a2ad2589154647e9f84e9a4ac4d57902238e57452a32ba5ef41d27aeb1c31419a16b3d84', '6', '3', 'authToken', '[]', '1', '2021-05-21 10:27:04', '2021-05-21 10:27:04', '2022-05-21 07:27:04');
INSERT INTO `oauth_access_tokens` VALUES ('40d5294140525ad8d989850816303c6b0b70a00c6795f63dfd184fe83511245ce1a900c2cdbf4f6d', '9', '3', 'authToken', '[]', '1', '2021-04-16 18:12:07', '2021-04-16 18:12:07', '2022-04-16 15:12:07');
INSERT INTO `oauth_access_tokens` VALUES ('4110b2c02bbaa94a5acbc8d77a9aea057c2eceaa4fa1cd3a785b03919f40297e8c918760c7c3eb8d', '8', '3', 'authToken', '[]', '0', '2021-06-18 22:46:53', '2021-06-18 22:46:53', '2022-06-18 19:46:53');
INSERT INTO `oauth_access_tokens` VALUES ('4118fdb3ec068ec223f861396d80dbfcbacba762ed7b35ce34fb87c67aea869d0d30478a5b1c3eb9', '110', '3', 'authToken', '[]', '1', '2021-07-06 19:17:01', '2021-07-06 19:17:01', '2022-07-06 16:17:01');
INSERT INTO `oauth_access_tokens` VALUES ('412d24e61356d1eaaffbf2331d5ae8d1260e733b081ec830e72a6d2187f2eec3cc37595e5ad63f8a', '8', '3', 'authToken', '[]', '0', '2021-05-27 12:29:49', '2021-05-27 12:29:49', '2022-05-27 09:29:49');
INSERT INTO `oauth_access_tokens` VALUES ('42766a9f9efd81dc618ec27ee0410278e58b7cd8aa576d6c2ee978b247cec2733306902e698e0336', '75', '3', 'authToken', '[]', '0', '2021-05-25 11:56:00', '2021-05-25 11:56:00', '2022-05-25 08:56:00');
INSERT INTO `oauth_access_tokens` VALUES ('42f0d6916944d1f54bb754c2fc8b81556f353eff6829907f560713affc3a07f3387f90d1565f6992', '8', '3', 'authToken', '[]', '0', '2021-06-16 00:00:31', '2021-06-16 00:00:31', '2022-06-15 21:00:31');
INSERT INTO `oauth_access_tokens` VALUES ('4326b22413dc2aa044628f76b610f1721f8494875628c5c62cb8b7824271d5c6ca60a77928fe6083', '8', '3', 'authToken', '[]', '0', '2021-05-18 18:06:28', '2021-05-18 18:06:28', '2022-05-18 15:06:28');
INSERT INTO `oauth_access_tokens` VALUES ('432ffc4a2ec278512307a5e88e3a92104f51524bc78fb4fdf9de83f71a740ba5c8c9daec0ab908f7', '8', '3', 'authToken', '[]', '0', '2021-04-21 00:33:05', '2021-04-21 00:33:05', '2022-04-20 21:33:05');
INSERT INTO `oauth_access_tokens` VALUES ('4396c29746b887e844008a03ab6110fc222cae10eeb41f2483a37a2426c83079d7786c0ad64d9f07', '102', '3', 'authToken', '[]', '1', '2021-06-24 14:10:47', '2021-06-24 14:10:47', '2022-06-24 11:10:47');
INSERT INTO `oauth_access_tokens` VALUES ('43c1dc92cd4ce2c8249eb8572e74fa56dcdebe20ba08e8d0a77cb6963904a723c99c6fe092ae7136', '6', '3', 'authToken', '[]', '1', '2021-05-19 18:39:55', '2021-05-19 18:39:55', '2022-05-19 15:39:55');
INSERT INTO `oauth_access_tokens` VALUES ('4410e7192bf7c2dee1a467af5faae6228434cf07376b5dfbab4611278bba3d2d8464484beb99042a', '6', '3', 'authToken', '[]', '1', '2021-04-21 22:58:14', '2021-04-21 22:58:14', '2022-04-21 19:58:14');
INSERT INTO `oauth_access_tokens` VALUES ('44216c033594dbad9432290662bced7b8a7b9b5dbd82da4fce58cff6220261d58b43495402af64fe', '6', '3', 'authToken', '[]', '0', '2021-04-21 00:48:59', '2021-04-21 00:48:59', '2022-04-20 21:48:59');
INSERT INTO `oauth_access_tokens` VALUES ('442ca9326b6d0c3e06b52810fa87030c0338d63f75bec3eca2da30a27a2cc9600da207009f7158f6', '104', '3', 'authToken', '[]', '1', '2021-06-25 10:29:35', '2021-06-25 10:29:35', '2022-06-25 07:29:35');
INSERT INTO `oauth_access_tokens` VALUES ('449eb802ce0d5eb45c51712ba688ef40028f5570bc781d8186a4b404b0a4ded9e6bb7e95773095a3', '70', '3', 'authToken', '[]', '0', '2021-06-23 19:12:51', '2021-06-23 19:12:51', '2022-06-23 16:12:51');
INSERT INTO `oauth_access_tokens` VALUES ('44e8395c263047ed8b7c189e74ffc39109309ebd192b672049bf15b56c321c4c20f736649d70aeed', '8', '3', 'authToken', '[]', '0', '2021-05-01 11:00:35', '2021-05-01 11:00:35', '2022-05-01 08:00:35');
INSERT INTO `oauth_access_tokens` VALUES ('44f7c34bf10fd611316d170cad683292259049ceaf2c240e91fb3d32a6adb3fb69a88349e42f9e82', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:33:09', '2021-05-04 18:33:09', '2022-05-04 15:33:09');
INSERT INTO `oauth_access_tokens` VALUES ('45023086c5aa98d9c9035fb284705f9e497741e3e166078bd6e3c4c48b8cea6c9c71d25d5af77687', '83', '3', 'authToken', '[]', '0', '2021-06-24 02:48:28', '2021-06-24 02:48:28', '2022-06-23 23:48:28');
INSERT INTO `oauth_access_tokens` VALUES ('4526f69493b17129da3bd9472b3911dd550eb1c7d8dcca303619105ecf191ece51a50c9d38d60118', '17', '3', 'authToken', '[]', '0', '2021-04-15 16:42:31', '2021-04-15 16:42:31', '2022-04-15 13:42:31');
INSERT INTO `oauth_access_tokens` VALUES ('457f3d8fec26f6e90425a261c98310d25bee998413024eedbdb0f2e782ffff4ab5f7f400d0d8cea6', '113', '3', 'authToken', '[]', '1', '2021-07-05 14:30:51', '2021-07-05 14:30:51', '2022-07-05 11:30:51');
INSERT INTO `oauth_access_tokens` VALUES ('45b74d76f9c768b11087d3296616995eaa260285d841177cc39b335337dd264b7dcac27e3598f61e', '98', '3', 'authToken', '[]', '0', '2021-06-23 18:48:54', '2021-06-23 18:48:54', '2022-06-23 15:48:54');
INSERT INTO `oauth_access_tokens` VALUES ('45e396dde138a54f9a3d93ada342d8996a4b12f696407f7739440119fad5cc2ab0c3bd38f58dabf6', '8', '3', 'authToken', '[]', '0', '2021-05-04 22:23:51', '2021-05-04 22:23:51', '2022-05-04 19:23:51');
INSERT INTO `oauth_access_tokens` VALUES ('461cedf50f6bf17e222cb1dec9ea5ede2f3b5206fc34819267c63f2b26363c3822a478713bc8d6f2', '102', '3', 'authToken', '[]', '1', '2021-06-24 13:48:31', '2021-06-24 13:48:31', '2022-06-24 10:48:31');
INSERT INTO `oauth_access_tokens` VALUES ('467277adc58b5e2cefebcdef05c123c8066eb1fd80b1716dfa761ff574442f6331b770590ee69043', '109', '3', 'authToken', '[]', '0', '2021-06-29 21:02:21', '2021-06-29 21:02:21', '2022-06-29 18:02:21');
INSERT INTO `oauth_access_tokens` VALUES ('4691b76e0593c3168600e72b75a968630bd2223413c8ea0587053d6ecbdc92f7841fb780fcc92530', '8', '3', 'authToken', '[]', '1', '2021-05-01 12:03:36', '2021-05-01 12:03:36', '2022-05-01 09:03:36');
INSERT INTO `oauth_access_tokens` VALUES ('46cb1ebc6099e4d9128e9027499776bb08e9543a37d2beb5c9c3e0f17e60982e7e10ee6a98c99c1e', '45', '3', 'authToken', '[]', '0', '2021-05-03 00:29:22', '2021-05-03 00:29:22', '2022-05-02 21:29:22');
INSERT INTO `oauth_access_tokens` VALUES ('46e84eb215432b60c9c222c0084da2fa6e2e8e1a077b5d4ed144f754fca6e509dc9da69c4f23adee', '127', '3', 'authToken', '[]', '0', '2021-07-01 00:16:46', '2021-07-01 00:16:46', '2022-06-30 21:16:46');
INSERT INTO `oauth_access_tokens` VALUES ('47c7094b53f3a9ef746fd758b11fd00e6cf70e7f864fb0f2d74f6255de564dabfa8fd3a2cd1d116c', '118', '3', 'authToken', '[]', '0', '2021-06-29 19:50:14', '2021-06-29 19:50:14', '2022-06-29 16:50:14');
INSERT INTO `oauth_access_tokens` VALUES ('48d248cc00587a629171c0f9639caa8043a567c6a7886fcb5152423387f73841c1f8970ebf93f7d5', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:01:43', '2021-05-04 01:01:43', '2022-05-03 22:01:43');
INSERT INTO `oauth_access_tokens` VALUES ('49c7895f3488fd39d74d9a78e4197c6ae376e966a610a99c9266c6bc22cee1eff6c8cc7dcb8f5d5a', '149', '3', 'authToken', '[]', '0', '2021-07-06 19:50:27', '2021-07-06 19:50:27', '2022-07-06 16:50:27');
INSERT INTO `oauth_access_tokens` VALUES ('4a5d9da51e92caa81444842bddc63b31a792e88100f4fa060ee6b7a762e3773d4b9b94564ffddbf4', '66', '3', 'authToken', '[]', '0', '2021-06-24 02:05:22', '2021-06-24 02:05:22', '2022-06-23 23:05:22');
INSERT INTO `oauth_access_tokens` VALUES ('4a98e64de5c64ac5cd8cfe233decbfefe68eb11c27f5f7c1d36fddb2f3a987fe4c200fce9d15d497', '8', '3', 'authToken', '[]', '1', '2021-05-03 00:29:53', '2021-05-03 00:29:53', '2022-05-02 21:29:53');
INSERT INTO `oauth_access_tokens` VALUES ('4b34ee3e206a9c97bcd5dbdf6c4cb31b22d186b050d1f2657c46a6410642b02493ea4cfd2ed98afc', '122', '3', 'authToken', '[]', '0', '2021-06-30 18:54:14', '2021-06-30 18:54:14', '2022-06-30 15:54:14');
INSERT INTO `oauth_access_tokens` VALUES ('4ba84fbaeaa56c418fa7f97cd8c0fac8aba81ef3067242c90309dc5d4e854cfca32a2518eab3acbc', '67', '3', 'authToken', '[]', '0', '2021-05-08 00:38:28', '2021-05-08 00:38:28', '2022-05-07 21:38:28');
INSERT INTO `oauth_access_tokens` VALUES ('4befd7098f78372d076798ecff6480950dabdb5b38f977df0596bb07d7d8cfe4726448649c9c08b3', '7', '3', 'authToken', '[]', '0', '2021-04-16 13:09:10', '2021-04-16 13:09:10', '2022-04-16 10:09:10');
INSERT INTO `oauth_access_tokens` VALUES ('4c3b5790e349de87f3d82d74106204b7bf2c8ee386c7bd482b6aadc81653fe6428372984be9cb5c9', '83', '3', 'authToken', '[]', '1', '2021-06-23 18:27:03', '2021-06-23 18:27:03', '2022-06-23 15:27:03');
INSERT INTO `oauth_access_tokens` VALUES ('4cca4e3e17c1cf142318091b5e44119eee83df0dba0c15a9e728742ee0474036761a36a9e1edd03e', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:54:27', '2021-04-19 01:54:27', '2022-04-18 22:54:27');
INSERT INTO `oauth_access_tokens` VALUES ('4cfb4ee7dfb91ceed22c525e9940c4abd7188a5b61af0d49503fe40621d0ce3d3ff59ff97ae3a659', '83', '3', 'authToken', '[]', '1', '2021-06-12 17:10:12', '2021-06-12 17:10:12', '2022-06-12 14:10:12');
INSERT INTO `oauth_access_tokens` VALUES ('4dbaf8825ea1fa1e37c36ce2f258ca35b30e9fd2b20a318f045338cbc9c6b24abe7ca8e99b4b1a65', '116', '3', 'authToken', '[]', '0', '2021-06-29 19:09:08', '2021-06-29 19:09:08', '2022-06-29 16:09:08');
INSERT INTO `oauth_access_tokens` VALUES ('4e2e41df7b5d27ce2b88576bf6e700335b0fd525b19f570469f2bbe0736aed12cf2cc94c0611ff5a', '107', '3', 'authToken', '[]', '0', '2021-07-05 13:50:29', '2021-07-05 13:50:29', '2022-07-05 10:50:29');
INSERT INTO `oauth_access_tokens` VALUES ('4e6c827a4caf675cd9b2600cd34c0a203ea5387196b6d7500f8c6c283877d08052037277f6aca0b2', '34', '3', 'authToken', '[]', '1', '2021-04-18 12:51:49', '2021-04-18 12:51:49', '2022-04-18 09:51:49');
INSERT INTO `oauth_access_tokens` VALUES ('4ee609e1eb4122bfc7af0d5907311fd2d6eec19407ef4d206f4c144ed6047a9d5efc0e888a819b77', '89', '3', 'authToken', '[]', '1', '2021-06-21 18:31:59', '2021-06-21 18:31:59', '2022-06-21 15:31:59');
INSERT INTO `oauth_access_tokens` VALUES ('4f28186f59790cedf3c95a5564ea21fdb826eb9ff5be57f2d2db49d8790758374a1467c640692b51', '45', '3', 'authToken', '[]', '0', '2021-04-28 19:14:43', '2021-04-28 19:14:43', '2022-04-28 16:14:43');
INSERT INTO `oauth_access_tokens` VALUES ('4fa2b04532afa90d73b5f51547f8541d13bc9361a2f1b641275116e1466bfe474fdf9276857b4072', '8', '3', 'authToken', '[]', '1', '2021-04-27 23:06:31', '2021-04-27 23:06:31', '2022-04-27 20:06:31');
INSERT INTO `oauth_access_tokens` VALUES ('4fc45ee03f17b468d2154469c462c04bb3f3da4e74fbb7441a4b64ba90b4c76faa017e1ef69a8a17', '6', '3', 'authToken', '[]', '0', '2021-03-25 13:59:45', '2021-03-25 13:59:45', '2022-03-25 09:59:45');
INSERT INTO `oauth_access_tokens` VALUES ('4fd14650ec29b3dc892d882de3b31044f21b0cef5d62c35b61ca2114e5e06fd452ccf2bc1d7b1cfc', '75', '3', 'authToken', '[]', '0', '2021-06-18 23:52:39', '2021-06-18 23:52:39', '2022-06-18 20:52:39');
INSERT INTO `oauth_access_tokens` VALUES ('502742f2d516ecbbc667b6f812884d13919d4941a5a8134edb00c0ce8e17250eac2c7c63e3149cb0', '7', '3', 'authToken', '[]', '0', '2021-04-15 20:43:35', '2021-04-15 20:43:35', '2022-04-15 17:43:35');
INSERT INTO `oauth_access_tokens` VALUES ('50fa3923e9761a098413a89d0808fd693c38e9b3c8219aa75f2bd8bfb2967180a7859b8b0de25170', '69', '3', 'authToken', '[]', '1', '2021-06-02 23:03:39', '2021-06-02 23:03:39', '2022-06-02 20:03:39');
INSERT INTO `oauth_access_tokens` VALUES ('5105ef640d322ab5cf92b0a4d7b3ff9cd1b4463e7043fc172cb8af730660c5411478df6fa385176a', '6', '3', 'authToken', '[]', '0', '2021-04-02 08:24:55', '2021-04-02 08:24:55', '2022-04-02 05:24:55');
INSERT INTO `oauth_access_tokens` VALUES ('5131cf958031468a7003cf13d93d49168de4c39d8a01cf37462793eee2981bf78f29c19ae1e07fee', '6', '3', 'authToken', '[]', '1', '2021-04-21 22:54:36', '2021-04-21 22:54:36', '2022-04-21 19:54:36');
INSERT INTO `oauth_access_tokens` VALUES ('5138c5c10913b1cc31c966c66305c825875dd325fc116e0c244171062f24b20cf4e511249f2f64fb', '8', '3', 'authToken', '[]', '1', '2021-04-21 00:42:12', '2021-04-21 00:42:12', '2022-04-20 21:42:12');
INSERT INTO `oauth_access_tokens` VALUES ('524263060aaeb2a5b6175e1ba9c0569b7d25a586812a5e26590e3c33d94228e1647bb65791f075d1', '97', '3', 'authToken', '[]', '1', '2021-06-23 18:44:27', '2021-06-23 18:44:27', '2022-06-23 15:44:27');
INSERT INTO `oauth_access_tokens` VALUES ('52486a6c54b0e3cc3c5ce968a5c8230d1277e0a3d1198eb356ee668d9d046e2d804e135d79a3b83f', '66', '3', 'authToken', '[]', '1', '2021-06-24 01:59:37', '2021-06-24 01:59:37', '2022-06-23 22:59:37');
INSERT INTO `oauth_access_tokens` VALUES ('52d931b2ff5c1005e3bc5e73b690e760b771d9af3c2c5c3e0720f9d902a5dc8107295417bfc2dd01', '8', '3', 'authToken', '[]', '1', '2021-05-23 13:25:00', '2021-05-23 13:25:00', '2022-05-23 10:25:00');
INSERT INTO `oauth_access_tokens` VALUES ('52f0342d3cc521a86997df5f080957b279632569f9e71be64e55db102c5378ec2bfb0c584bacba5b', '8', '3', 'authToken', '[]', '1', '2021-05-01 13:51:49', '2021-05-01 13:51:49', '2022-05-01 10:51:49');
INSERT INTO `oauth_access_tokens` VALUES ('53076c61690d5222e17436dfbd95267eb53d6295177ab11c5cbd60a8410f233653dc6936ee1078fa', '6', '3', 'authToken', '[]', '0', '2021-05-09 23:03:30', '2021-05-09 23:03:30', '2022-05-09 20:03:30');
INSERT INTO `oauth_access_tokens` VALUES ('537adcc9e06dffde900218e70db6de33b63addf87129ec645e18755c67c8913ce720c8aa1516cd87', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:34:34', '2021-05-04 18:34:34', '2022-05-04 15:34:34');
INSERT INTO `oauth_access_tokens` VALUES ('544b874808146abe4a94f728cd0e3ad235edf21fbf4c798b299f12109ccb7244426c36c8d408a516', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:11:23', '2021-05-04 01:11:23', '2022-05-03 22:11:23');
INSERT INTO `oauth_access_tokens` VALUES ('545f5bb9c4289ba97db00283563cbb8e7c53be8aff576ad3f2d9ca96e9a2483d47ad0cc8c7ca4596', '37', '3', 'authToken', '[]', '0', '2021-04-18 20:19:57', '2021-04-18 20:19:57', '2022-04-18 17:19:57');
INSERT INTO `oauth_access_tokens` VALUES ('5478dcf05baf44530c80f8f495c559f87973e74d89a3d8b48329f777cdb0e749372094a47ab9c35e', '66', '3', 'authToken', '[]', '1', '2021-06-23 18:08:16', '2021-06-23 18:08:16', '2022-06-23 15:08:16');
INSERT INTO `oauth_access_tokens` VALUES ('548c1ccdf8a0bdde80f17173ac0301215b9bf881c600c750e0b370315952b4c1fde64a12f8042b1d', '8', '3', 'authToken', '[]', '0', '2021-04-12 14:57:19', '2021-04-12 14:57:19', '2022-04-12 11:57:19');
INSERT INTO `oauth_access_tokens` VALUES ('54db3025bc6c541c01635385453721a32f41afd8184df50d4ef911ffc46c4cbc8daf830a917c9032', '102', '3', 'authToken', '[]', '1', '2021-06-24 14:10:01', '2021-06-24 14:10:01', '2022-06-24 11:10:01');
INSERT INTO `oauth_access_tokens` VALUES ('5540ace2147b79f4cce4ea13ce09e2384b1806921a872ba9871681b8b3695309dc36e95e8d69becc', '124', '3', 'authToken', '[]', '0', '2021-06-30 20:45:23', '2021-06-30 20:45:23', '2022-06-30 17:45:23');
INSERT INTO `oauth_access_tokens` VALUES ('55802b312c3a6f88f2d137f9bfabe1ec4efe4b3af997ab7d96d5f48c790643e063cad0c6994fda25', '66', '3', 'authToken', '[]', '1', '2021-06-21 21:45:06', '2021-06-21 21:45:06', '2022-06-21 18:45:06');
INSERT INTO `oauth_access_tokens` VALUES ('55a15aaccca0bb0929417842a847176eb23f39e601e0e45598b09459cbd4be9b52f9ff21d6908437', '73', '3', 'authToken', '[]', '1', '2021-06-01 14:03:20', '2021-06-01 14:03:20', '2022-06-01 11:03:20');
INSERT INTO `oauth_access_tokens` VALUES ('55bf6ee9fd17e7e8982f2da980776733ba965cfd8887d9e34a8f64b1377539641881ed0936a2942c', '8', '3', 'authToken', '[]', '0', '2021-05-03 20:11:38', '2021-05-03 20:11:38', '2022-05-03 17:11:38');
INSERT INTO `oauth_access_tokens` VALUES ('55d6878a84c50faa068f6a93a7d48b8e189d46e5a97fa772ad894a27403f1abab3e2a1daa24da4ba', '66', '3', 'authToken', '[]', '1', '2021-06-11 01:50:28', '2021-06-11 01:50:28', '2022-06-10 22:50:28');
INSERT INTO `oauth_access_tokens` VALUES ('571133e3b6d2b54d4fd4d2bc65e4caf0f8ecfc74b960b2cf19942a4b489ad70abb1ef0c3c55a563f', '106', '3', 'authToken', '[]', '0', '2021-07-02 13:12:25', '2021-07-02 13:12:25', '2022-07-02 10:12:25');
INSERT INTO `oauth_access_tokens` VALUES ('572ad3aefa14df684aa894fa74fc9a5cd9ec749cf22c43546ebc219128a537bb1d670cbe389779e4', '83', '3', 'authToken', '[]', '1', '2021-06-12 17:19:44', '2021-06-12 17:19:44', '2022-06-12 14:19:44');
INSERT INTO `oauth_access_tokens` VALUES ('573f40f30ba8b6b6043a57d4e2e3f18c1bd974a1be0151e5e772b284358231022ad825b8a9c60532', '128', '3', 'authToken', '[]', '0', '2021-07-01 16:26:16', '2021-07-01 16:26:16', '2022-07-01 13:26:16');
INSERT INTO `oauth_access_tokens` VALUES ('5758381a34e7c8a016520bff083c4b554e3afd646e7a2b2d2fae5e8ebce7b51e0984b6cc6c2a8e33', '12', '3', 'authToken', '[]', '1', '2021-05-01 19:17:48', '2021-05-01 19:17:48', '2022-05-01 16:17:48');
INSERT INTO `oauth_access_tokens` VALUES ('576278ffb889053633cfbb0290413bd86e26c680065fc57b0fa1b0a2a8af95289c34171a9b29849a', '8', '3', 'authToken', '[]', '0', '2021-04-12 17:39:20', '2021-04-12 17:39:20', '2022-04-12 14:39:20');
INSERT INTO `oauth_access_tokens` VALUES ('57d4c9d13bbfd387e621622946e7ceba0ca4a87c515b413f933f4bb7d43eaf8d80b5261cfdd3d3ea', '23', '3', 'authToken', '[]', '0', '2021-04-17 13:32:57', '2021-04-17 13:32:57', '2022-04-17 10:32:57');
INSERT INTO `oauth_access_tokens` VALUES ('58441b4a4e5d011e3163680b02c6b8337aba24e5de63bfc3213cc2088e23367b5778c36912c8f5ba', '67', '3', 'authToken', '[]', '0', '2021-05-25 17:32:54', '2021-05-25 17:32:54', '2022-05-25 14:32:54');
INSERT INTO `oauth_access_tokens` VALUES ('58c0f82ed3eae6b080e8897118142f5df7776cc8a686c6e00fe0ab27193079c78811647bac9e3e6d', '8', '3', 'authToken', '[]', '0', '2021-05-20 12:21:49', '2021-05-20 12:21:49', '2022-05-20 09:21:49');
INSERT INTO `oauth_access_tokens` VALUES ('59cdcd0c16116a4e2c152479c12d927a3d6f621b3dafed6fe08bb63e48cee4c986d06f6d46ec25b6', '129', '3', 'authToken', '[]', '0', '2021-07-01 22:40:18', '2021-07-01 22:40:18', '2022-07-01 19:40:18');
INSERT INTO `oauth_access_tokens` VALUES ('5ac359218fb47fdf297cc4448d22f3ba65374b6aed0ed5053e6e3659c56285c338639390dab8066e', '86', '3', 'authToken', '[]', '1', '2021-06-13 20:06:45', '2021-06-13 20:06:45', '2022-06-13 17:06:45');
INSERT INTO `oauth_access_tokens` VALUES ('5b754a333e2ead742d614af4f88776431bde0dbc29e88f004d570716660f7944d3b207a42784825f', '142', '3', 'authToken', '[]', '0', '2021-07-06 01:28:36', '2021-07-06 01:28:36', '2022-07-05 22:28:36');
INSERT INTO `oauth_access_tokens` VALUES ('5b7dd854085da7a284c877c126c335c9900d947225fbbd5058197fc8ae267f4808c0b56115554abb', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:22:07', '2021-04-21 23:22:07', '2022-04-21 20:22:07');
INSERT INTO `oauth_access_tokens` VALUES ('5ba6556f907857799a0a23303049db747c24158eebda173ee2fb9d37ae0274a07e29065b4caf91e7', '6', '3', 'authToken', '[]', '1', '2021-04-22 19:25:14', '2021-04-22 19:25:14', '2022-04-22 16:25:14');
INSERT INTO `oauth_access_tokens` VALUES ('5bc22a573793a1faf6c17e68a2a076402d4ce2e13387721f34b18ef4767010c7806af4d2b284c7f8', '9', '3', 'authToken', '[]', '0', '2021-05-05 01:24:01', '2021-05-05 01:24:01', '2022-05-04 22:24:01');
INSERT INTO `oauth_access_tokens` VALUES ('5bca3d7f17e5db75ef376638bd45f853a2f8607a2502bc5f5ca29c1d6fade19c7597fb7d52a8d515', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:34:13', '2021-05-04 18:34:13', '2022-05-04 15:34:13');
INSERT INTO `oauth_access_tokens` VALUES ('5bfda1d488d6ecb6e267a2791f7443008d983ccfe8d65856685340ec5c611c97482def13a376e490', '67', '3', 'authToken', '[]', '1', '2021-05-18 14:24:53', '2021-05-18 14:24:53', '2022-05-18 11:24:53');
INSERT INTO `oauth_access_tokens` VALUES ('5c2b0b4ec35aeb43c1a1c5c7c56cc76e7b91aeeea7ae3d2bfb4f75ecf14f13a2cc7fd78137ca59d6', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:15:48', '2021-04-22 00:15:48', '2022-04-21 21:15:48');
INSERT INTO `oauth_access_tokens` VALUES ('5c3ca31eec6014cd7dc7e553a935dd9251e7157068214d1c587275269ffd22970359d9828ed45b53', '6', '3', 'authToken', '[]', '1', '2021-04-23 22:05:53', '2021-04-23 22:05:53', '2022-04-23 19:05:53');
INSERT INTO `oauth_access_tokens` VALUES ('5ca166614b85298da540dab6f6f72b398421f04c521be981017906541ad1e9c2a3da0f5d4d4f5bdf', '94', '3', 'authToken', '[]', '0', '2021-06-23 00:33:32', '2021-06-23 00:33:32', '2022-06-22 21:33:32');
INSERT INTO `oauth_access_tokens` VALUES ('5cbe9ed937cad566d17647f7736c5dbc776af51ea6fbd589bf7a2ef8c0adcede9df3f3ff1fd225ed', '10', '3', 'authToken', '[]', '0', '2021-04-13 08:18:54', '2021-04-13 08:18:54', '2022-04-13 05:18:54');
INSERT INTO `oauth_access_tokens` VALUES ('5ce992e56f948faf1abfc99b102fc4f6a8d5e9b239e38c3f6d637cd089b72cba2521a92d5425202b', '8', '3', 'authToken', '[]', '1', '2021-06-26 08:29:23', '2021-06-26 08:29:23', '2022-06-26 05:29:23');
INSERT INTO `oauth_access_tokens` VALUES ('5d62ecbb5e476b4b5174d78ea8a48bc50580dc5fab786eac95fd63c18ec3a31ad58faf6064e51c6a', '107', '3', 'authToken', '[]', '1', '2021-06-25 10:36:10', '2021-06-25 10:36:10', '2022-06-25 07:36:10');
INSERT INTO `oauth_access_tokens` VALUES ('5de5aa19fc4775720059d026da2b48ed71b1a11e9e94a312d67bfd3da487cb6210c54e2a6c8b9573', '66', '3', 'authToken', '[]', '1', '2021-06-23 18:06:23', '2021-06-23 18:06:23', '2022-06-23 15:06:23');
INSERT INTO `oauth_access_tokens` VALUES ('5e24e95cb17628a776f510acc433e99c1e105d987c49046f819fe21822f5d26c906c0514578caf20', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:22:47', '2021-05-03 00:22:47', '2022-05-02 21:22:47');
INSERT INTO `oauth_access_tokens` VALUES ('5eb4ddcf0fb7567a9a2f96ca71cf9495520c0ac47b2877ed73d24b0282eb71ee4df10c049b22f0f0', '8', '3', 'authToken', '[]', '0', '2021-05-25 10:49:36', '2021-05-25 10:49:36', '2022-05-25 07:49:36');
INSERT INTO `oauth_access_tokens` VALUES ('5f3d9b4de626984775bb6b6370b5b2161f9eb5850f24ee5379e1fb0af715124e5b29b3836aecdbb1', '73', '3', 'authToken', '[]', '0', '2021-06-01 13:41:49', '2021-06-01 13:41:49', '2022-06-01 10:41:49');
INSERT INTO `oauth_access_tokens` VALUES ('5f760faa0c953bddc77d9fab2fb576b4dbe99a247473bcbc46c76582d65d11bd9d8289c81ae6d7f1', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:32:37', '2021-05-04 18:32:37', '2022-05-04 15:32:37');
INSERT INTO `oauth_access_tokens` VALUES ('5fbcb18492e847ccaae96cb79dc4afbb7610fa54720bdca28189c0bcf2acaa525ac08eda09a0a26a', '73', '3', 'authToken', '[]', '0', '2021-05-26 23:34:14', '2021-05-26 23:34:14', '2022-05-26 20:34:14');
INSERT INTO `oauth_access_tokens` VALUES ('60df70f4523bb6015d0426b64cb4bfbaecd47517f0ddadddff1c08b49d129d02902700e766e507d7', '28', '3', 'authToken', '[]', '0', '2021-04-18 02:04:01', '2021-04-18 02:04:01', '2022-04-17 23:04:01');
INSERT INTO `oauth_access_tokens` VALUES ('61272e74cc673415f21c460b3957cc5283a2e4de4a74b38aacf0f53d1ed954c3f70b33d74257cf41', '68', '3', 'authToken', '[]', '1', '2021-05-16 17:54:40', '2021-05-16 17:54:40', '2022-05-16 14:54:40');
INSERT INTO `oauth_access_tokens` VALUES ('612c53054dd6635905fd53acb827e2ec0222affbe579fcc572305858be9926fbe55393a31769b60a', '6', '3', 'authToken', '[]', '0', '2021-04-14 21:01:46', '2021-04-14 21:01:46', '2022-04-14 18:01:46');
INSERT INTO `oauth_access_tokens` VALUES ('61a541f5ced00e91285c511f2a33d6942ef5839bb95d3b7f49c279cfe7a99e22a3f68e60560479fe', '111', '3', 'authToken', '[]', '0', '2021-06-28 21:23:48', '2021-06-28 21:23:48', '2022-06-28 18:23:48');
INSERT INTO `oauth_access_tokens` VALUES ('61d129323ab729e3d15117376e55e2b2ffe2a58044f19a6b3148ce3bdbe09378ac9d3c38ea909ac4', '67', '3', 'authToken', '[]', '0', '2021-05-23 23:51:44', '2021-05-23 23:51:44', '2022-05-23 20:51:44');
INSERT INTO `oauth_access_tokens` VALUES ('61fb41cb43a3883264792379bce64f0fe9a5b0e0a63955b04e8caa568afb8a1a53eb4e0f074edcba', '8', '3', 'authToken', '[]', '0', '2021-06-22 21:13:15', '2021-06-22 21:13:15', '2022-06-22 18:13:15');
INSERT INTO `oauth_access_tokens` VALUES ('62429c0ce8a9a97819f9c135d1cf5ae54194d9e13146606dc7520718e6e804d0cd17e5234d4d9e2e', '8', '3', 'authToken', '[]', '0', '2021-04-12 16:44:49', '2021-04-12 16:44:49', '2022-04-12 13:44:49');
INSERT INTO `oauth_access_tokens` VALUES ('628b336cc9f218cf453f9ee5c21cbaad55b658ac6aa50c9eb46884ca4d4271ed6a49a3e596fdf8b1', '66', '3', 'authToken', '[]', '1', '2021-06-23 19:15:12', '2021-06-23 19:15:12', '2022-06-23 16:15:12');
INSERT INTO `oauth_access_tokens` VALUES ('628d492419f976e599ce8b2042824b2bcb6153f9f2150aaa52c9cae671ecaea793aefe94797aa5be', '58', '3', 'authToken', '[]', '1', '2021-05-01 18:17:16', '2021-05-01 18:17:16', '2022-05-01 15:17:16');
INSERT INTO `oauth_access_tokens` VALUES ('628fe956d2b1189020533b59e7b0472a53414f993c57b623fa6c0b38bd422fe937a7e859b3263245', '21', '3', 'authToken', '[]', '1', '2021-04-16 14:18:59', '2021-04-16 14:18:59', '2022-04-16 11:18:59');
INSERT INTO `oauth_access_tokens` VALUES ('62a6d56914a5ff1b7cf55856f4b937cdfddca57c08be270989185c5d57a3993e23ba34999490936e', '64', '3', 'authToken', '[]', '1', '2021-05-03 00:27:42', '2021-05-03 00:27:42', '2022-05-02 21:27:42');
INSERT INTO `oauth_access_tokens` VALUES ('62fa7601eb0d2633f7bd066886036e8285fc89dc4dedec7fa61f9ced87a19f054ed167c3abaeab86', '7', '3', 'authToken', '[]', '0', '2021-05-01 23:00:32', '2021-05-01 23:00:32', '2022-05-01 20:00:32');
INSERT INTO `oauth_access_tokens` VALUES ('63285ad630637f57a49864d9916e37add960dd93386ae5c41b032e34f427fdf99a4dce363d84ee51', '8', '3', 'authToken', '[]', '0', '2021-04-12 15:45:19', '2021-04-12 15:45:19', '2022-04-12 12:45:19');
INSERT INTO `oauth_access_tokens` VALUES ('63cb926bdb8503c87393e3afc43b77c09742ddfeb2a30b8f21b8754fa5f4b975209af3354392e0cd', '62', '3', 'authToken', '[]', '1', '2021-05-03 00:18:14', '2021-05-03 00:18:14', '2022-05-02 21:18:14');
INSERT INTO `oauth_access_tokens` VALUES ('63ddc52552c5b691deb6fd5a9334b9e394c68fac97096940f82e114bfb0c5d41c27ac99c64e14714', '24', '3', 'authToken', '[]', '1', '2021-04-17 14:29:50', '2021-04-17 14:29:50', '2022-04-17 11:29:50');
INSERT INTO `oauth_access_tokens` VALUES ('6494de24a0fa21f59d13e7bd76885b7506e6e0cd220d4bcd085ee28ab40a43663b1231b93b735f7e', '45', '3', 'authToken', '[]', '0', '2021-05-03 00:51:18', '2021-05-03 00:51:18', '2022-05-02 21:51:18');
INSERT INTO `oauth_access_tokens` VALUES ('6538a7f2599273247e7018ba3de5546c00d379993f15ea7981de7785a3c9a14231bced34f97a5c48', '8', '3', 'authToken', '[]', '0', '2021-05-17 21:19:39', '2021-05-17 21:19:39', '2022-05-17 18:19:39');
INSERT INTO `oauth_access_tokens` VALUES ('65668b90a479d23670ba3f38fef44ab9411b465be2a577af65b340734675cd20368527b5a55e6efa', '93', '3', 'authToken', '[]', '1', '2021-06-23 18:02:20', '2021-06-23 18:02:20', '2022-06-23 15:02:20');
INSERT INTO `oauth_access_tokens` VALUES ('65ae6cded7aac84773ae063f7fa6b9729536ed934f71ece903e0259698b34cb6823f68589f929094', '6', '3', 'authToken', '[]', '0', '2021-04-14 15:27:23', '2021-04-14 15:27:23', '2022-04-14 12:27:23');
INSERT INTO `oauth_access_tokens` VALUES ('65d16dc7f2c1d0fe4af2e3a8502c2d8a498d4ce13fc96ca65ec3a5982f286ce57966fcadc0099333', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:50:53', '2021-05-04 01:50:53', '2022-05-03 22:50:53');
INSERT INTO `oauth_access_tokens` VALUES ('6609999f3e6b214617069ccb4e3eb411e5180b802da7c0eca564f575ee60369e579fc354a3b84fd8', '100', '3', 'authToken', '[]', '1', '2021-06-24 02:02:50', '2021-06-24 02:02:50', '2022-06-23 23:02:50');
INSERT INTO `oauth_access_tokens` VALUES ('6649f5cc9900bbd9d4e2637de65e5ef0d11d9710bc0bb9f24ead263af33a01204dd8ce0494039deb', '7', '3', 'authToken', '[]', '0', '2021-04-16 13:28:44', '2021-04-16 13:28:44', '2022-04-16 10:28:44');
INSERT INTO `oauth_access_tokens` VALUES ('67b4a555720e3dbd3ba307135fe51006f79dd49ec398e02942cb4d2f59cf2e065bc9c3f3c081875e', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:44:30', '2021-04-21 23:44:30', '2022-04-21 20:44:30');
INSERT INTO `oauth_access_tokens` VALUES ('68ec6fffce6cad17f09b418ea9aff63158b9ad0e486289326fa6c5a697093deada2cd31ca66ae7f8', '17', '3', 'authToken', '[]', '0', '2021-04-16 14:51:22', '2021-04-16 14:51:22', '2022-04-16 11:51:22');
INSERT INTO `oauth_access_tokens` VALUES ('6922d746ab87e2d7dd1258c7a0f4433f97c480290a0649446052db8c00f16e724f957c55129c588a', '75', '3', 'authToken', '[]', '0', '2021-05-25 11:02:36', '2021-05-25 11:02:36', '2022-05-25 08:02:36');
INSERT INTO `oauth_access_tokens` VALUES ('695ccc424020b0f4b878deca9cefecf544f9f2624015db202e2c887c22fd98d87da6a0bce1e73afe', '8', '3', 'authToken', '[]', '0', '2021-04-12 16:48:27', '2021-04-12 16:48:27', '2022-04-12 13:48:27');
INSERT INTO `oauth_access_tokens` VALUES ('695de0c321547f1d2b40193dcc09df906de568568ab7819e920ebcffe2ad09124944c406a606f37d', '21', '3', 'authToken', '[]', '1', '2021-04-16 17:47:08', '2021-04-16 17:47:08', '2022-04-16 14:47:08');
INSERT INTO `oauth_access_tokens` VALUES ('699f96acad7e8a30e054f6bc6d343195644fdc934e2bbb48028a540587cd18aa2aadfc8986adc191', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:39:10', '2021-06-23 20:39:10', '2022-06-23 17:39:10');
INSERT INTO `oauth_access_tokens` VALUES ('69f704b0d3d66d3b57d2f54d00b397707b11556cced1edfccef7cee1dbe842d9d199b0c01cd90959', '29', '3', 'authToken', '[]', '0', '2021-04-18 02:30:11', '2021-04-18 02:30:11', '2022-04-17 23:30:11');
INSERT INTO `oauth_access_tokens` VALUES ('6abd3f7efbfd3ef2c3db863ce55da5fe457a57e9b3d70720186152519ea7dbe62c6d6e90fc2f8133', '66', '3', 'authToken', '[]', '1', '2021-06-21 18:35:25', '2021-06-21 18:35:25', '2022-06-21 15:35:25');
INSERT INTO `oauth_access_tokens` VALUES ('6b16dfbe1ab4de87aa382d2a45a964dfda9f73c1adc76c502df4f439d9c31f72871ac581d633a5f7', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:54:46', '2021-04-21 23:54:46', '2022-04-21 20:54:46');
INSERT INTO `oauth_access_tokens` VALUES ('6bc16c1a597650d6ce3bdc7527cb1d5a39129c3f59e6b6a1fbafd2af6e88432d4df74242d648d091', '27', '3', 'authToken', '[]', '1', '2021-04-17 14:43:37', '2021-04-17 14:43:37', '2022-04-17 11:43:37');
INSERT INTO `oauth_access_tokens` VALUES ('6bd144a8d61066658abe27bb859d7fae546d9af3fc5f2ee445d4498b756ef193c0838f1095b5a942', '96', '3', 'authToken', '[]', '1', '2021-06-23 18:26:33', '2021-06-23 18:26:33', '2022-06-23 15:26:33');
INSERT INTO `oauth_access_tokens` VALUES ('6c0099061354ac06bf01777765fffbe1a7968ba0e4e93b306fd068047d76c89e4112d7f471f936f2', '136', '3', 'authToken', '[]', '0', '2021-07-04 18:31:32', '2021-07-04 18:31:32', '2022-07-04 15:31:32');
INSERT INTO `oauth_access_tokens` VALUES ('6c299f459b995380166543e6bae5fad9056f1841dbde8433f147e5b0962cd1cdd48c52a6aacdbd2a', '81', '3', 'authToken', '[]', '1', '2021-06-02 20:02:09', '2021-06-02 20:02:09', '2022-06-02 17:02:09');
INSERT INTO `oauth_access_tokens` VALUES ('6c2fa8d726ec4d3cb238770ae80d25a7e65497d31adc24e7be29e2f6f794dafac0d9195f47362bd6', '138', '3', 'authToken', '[]', '0', '2021-07-04 19:29:46', '2021-07-04 19:29:46', '2022-07-04 16:29:46');
INSERT INTO `oauth_access_tokens` VALUES ('6cd0aa21e2a14e5724193126e6eb4dcc395ee8f83d012640a021819914990e4ce86cb320d2551a13', '110', '3', 'authToken', '[]', '1', '2021-06-29 21:54:29', '2021-06-29 21:54:29', '2022-06-29 18:54:29');
INSERT INTO `oauth_access_tokens` VALUES ('6ce7cf055eb54c308eeabeac1adfdaa1c1f334b61a54b7fef6510ce4d996d5c1409c60606bbac360', '8', '3', 'authToken', '[]', '1', '2021-05-12 21:08:12', '2021-05-12 21:08:12', '2022-05-12 18:08:12');
INSERT INTO `oauth_access_tokens` VALUES ('6d3b9efc0446c7d51028d716e7c00cf5ed5f0b2e674e0592ce6a2d7a2a0f5ddb90d01f2fbebaddd3', '119', '3', 'authToken', '[]', '1', '2021-07-06 19:16:37', '2021-07-06 19:16:37', '2022-07-06 16:16:37');
INSERT INTO `oauth_access_tokens` VALUES ('6d5a2eb09181666354e0ed535d60906226576d336a9d2ee31aa0effaf1a8cf76f1ac91dbc3edc940', '67', '3', 'authToken', '[]', '0', '2021-05-24 17:22:58', '2021-05-24 17:22:58', '2022-05-24 14:22:58');
INSERT INTO `oauth_access_tokens` VALUES ('6e6109fb6238f581e68962e69d361d0ebe76b0b39d32f05194033cdd72862649d4c7e9d9b14cbc79', '28', '3', 'authToken', '[]', '0', '2021-04-18 01:52:06', '2021-04-18 01:52:06', '2022-04-17 22:52:06');
INSERT INTO `oauth_access_tokens` VALUES ('6e766d60434e9f8036349ae203d2c324485358145575145e2d4e1d2d96495d67c429907ccdcb9958', '58', '3', 'authToken', '[]', '1', '2021-05-01 18:29:00', '2021-05-01 18:29:00', '2022-05-01 15:29:00');
INSERT INTO `oauth_access_tokens` VALUES ('6e9c61037a0cf6a4f51391e0354e046546ab2799d248ae32b8a9c9e544cf32471f9aa45ba7ecebda', '9', '3', 'authToken', '[]', '1', '2021-05-06 12:13:09', '2021-05-06 12:13:09', '2022-05-06 09:13:09');
INSERT INTO `oauth_access_tokens` VALUES ('6e9d4d0d458d0124164bdc12b1d20e794c16f78df030e9178c72c0ad0bab3eeb5be9e78fe6b8c22c', '62', '3', 'authToken', '[]', '1', '2021-05-02 19:05:39', '2021-05-02 19:05:39', '2022-05-02 16:05:39');
INSERT INTO `oauth_access_tokens` VALUES ('6ebceb4438f058e18b26bbd754850c24c964771eb5870fe0f9f50c7024a8f95b7a7a05aa551819cc', '8', '3', 'authToken', '[]', '0', '2021-05-23 23:47:36', '2021-05-23 23:47:36', '2022-05-23 20:47:36');
INSERT INTO `oauth_access_tokens` VALUES ('6ec26baa1fcb60ae8bfb96f8094dc7bf952b30c5bd48df606c4db98a01c053eefedb3ff691186cfb', '9', '3', 'authToken', '[]', '1', '2021-04-16 13:50:09', '2021-04-16 13:50:09', '2022-04-16 10:50:09');
INSERT INTO `oauth_access_tokens` VALUES ('6edbdbddde3e76860573aa47135dc6d5d6ad768daac0844bd50d89b2c21e85e933c07a070a8abce1', '45', '3', 'authToken', '[]', '0', '2021-04-28 02:38:46', '2021-04-28 02:38:46', '2022-04-27 23:38:46');
INSERT INTO `oauth_access_tokens` VALUES ('6f754e488240743b2022249ad7da194e3bb5782a198a152c8b465ea474cd1082335bf472dc702a89', '6', '3', 'authToken', '[]', '0', '2021-03-25 16:26:16', '2021-03-25 16:26:16', '2022-03-25 12:26:16');
INSERT INTO `oauth_access_tokens` VALUES ('6fc8cf2031f678306fb1353ab2861590624537189f3088f76df0d7440cece4bb2b2af4c171c18944', '140', '3', 'authToken', '[]', '0', '2021-07-05 15:20:39', '2021-07-05 15:20:39', '2022-07-05 12:20:39');
INSERT INTO `oauth_access_tokens` VALUES ('701ec1558d490c201f459541424fd1c93d96844ab39e1dc8f1e667724333dd6f5613dd2b5d972a87', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:46:35', '2021-04-21 23:46:35', '2022-04-21 20:46:35');
INSERT INTO `oauth_access_tokens` VALUES ('703066172c527f4c347b8e6ae13028b204922791adaef7f9c65038b67c91bbf1df3c53edb8853b60', '67', '3', 'authToken', '[]', '0', '2021-06-10 22:43:38', '2021-06-10 22:43:38', '2022-06-10 19:43:38');
INSERT INTO `oauth_access_tokens` VALUES ('70592fc4c103fb65305962cc0525906b2af6d0b46e8aac514acec8d2739c09a901a8173ff2014aaa', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:50:27', '2021-04-19 01:50:27', '2022-04-18 22:50:27');
INSERT INTO `oauth_access_tokens` VALUES ('7124475a832240a90d9e26c4a8c9fb04849e76e984d107e2d8130524e84f182cbb85147e9bbeaa6e', '12', '3', 'authToken', '[]', '1', '2021-06-02 12:17:01', '2021-06-02 12:17:01', '2022-06-02 09:17:01');
INSERT INTO `oauth_access_tokens` VALUES ('718c0c5af2f04749d76ade78b55fd90fb247b0aa0b759a8e1b8bbfe97d0700236c341e8e0f3c9704', '66', '3', 'authToken', '[]', '1', '2021-06-23 18:45:13', '2021-06-23 18:45:13', '2022-06-23 15:45:13');
INSERT INTO `oauth_access_tokens` VALUES ('72ac8dea64a98a398b94002e645405aafb65462cab0c1ee562a767263d17bc2a63c61585d1705e97', '94', '3', 'authToken', '[]', '0', '2021-06-23 00:36:53', '2021-06-23 00:36:53', '2022-06-22 21:36:53');
INSERT INTO `oauth_access_tokens` VALUES ('730821dbb4e0a88ba211f49c08b3fafb015078f7e2c35d4a5603958462083dfbb81d12f6bb3dfd02', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:12:35', '2021-04-22 00:12:35', '2022-04-21 21:12:35');
INSERT INTO `oauth_access_tokens` VALUES ('731f522693bded8501adced62df4124005937a0e4809ed872bae5e8b21c4343866cc1281a6f980e2', '96', '3', 'authToken', '[]', '1', '2021-06-23 18:29:48', '2021-06-23 18:29:48', '2022-06-23 15:29:48');
INSERT INTO `oauth_access_tokens` VALUES ('74336025d44263ed5fc652a914de5fd7dc741aaf52b5b4462a6c2e17830b1ad6511b21aa5b8750f1', '8', '3', 'authToken', '[]', '1', '2021-05-12 13:30:41', '2021-05-12 13:30:41', '2022-05-12 10:30:41');
INSERT INTO `oauth_access_tokens` VALUES ('74a9accc2e352589168755f53f8f97f3e5cd527cc0347102e2e6c358e5e5d765c89aa30c29bf96fc', '8', '3', 'authToken', '[]', '1', '2021-04-29 16:06:27', '2021-04-29 16:06:27', '2022-04-29 13:06:27');
INSERT INTO `oauth_access_tokens` VALUES ('75434607e63c5d9928a6194ff4562db9d4e53b7ef0ea9f7552c23e7f945239113a77fe19f7f811f8', '67', '3', 'authToken', '[]', '0', '2021-05-08 00:38:52', '2021-05-08 00:38:52', '2022-05-07 21:38:52');
INSERT INTO `oauth_access_tokens` VALUES ('75f8aa58821affcd4854e64ee19c7d08d0e1a156109a18e36c4f8eb71fe0302de9e7c0d702f29e23', '66', '3', 'authToken', '[]', '1', '2021-06-24 02:02:06', '2021-06-24 02:02:06', '2022-06-23 23:02:06');
INSERT INTO `oauth_access_tokens` VALUES ('769b0a3107c0ac6c8f0854ba0454a3f6796fc882226a80f95b7e1a3cb7697161d9febdd7e7ae41f5', '83', '3', 'authToken', '[]', '0', '2021-06-12 14:44:17', '2021-06-12 14:44:17', '2022-06-12 11:44:17');
INSERT INTO `oauth_access_tokens` VALUES ('76d96fdac378053221039ce01c72b996d6a14e2df11ad5206e50b2f4ca7d499db1ef019138576fa0', '8', '3', 'authToken', '[]', '0', '2021-04-21 00:55:35', '2021-04-21 00:55:35', '2022-04-20 21:55:35');
INSERT INTO `oauth_access_tokens` VALUES ('77449ed653fd0cc9051a6a5ceb496f25356e54b172bf498e83d5b1ab9f5ae14f151d1c63d177ee30', '28', '3', 'authToken', '[]', '0', '2021-06-24 02:36:58', '2021-06-24 02:36:58', '2022-06-23 23:36:58');
INSERT INTO `oauth_access_tokens` VALUES ('7781d1dff213831087220dcf2eec9b37e3af02a333b8c0853bac703cb86feb2ecee3e0bedc74c371', '28', '3', 'authToken', '[]', '0', '2021-06-24 02:15:02', '2021-06-24 02:15:02', '2022-06-23 23:15:02');
INSERT INTO `oauth_access_tokens` VALUES ('778fcce41c71aa5fd8ae6b7011b75c9f54374c7b825e1e9ab9cde6a95e9e37b7fab42b1b9a19f320', '6', '3', 'authToken', '[]', '0', '2021-04-28 13:49:29', '2021-04-28 13:49:29', '2022-04-28 10:49:29');
INSERT INTO `oauth_access_tokens` VALUES ('7790af0063041e2114881571197dabf65a263c8e7e9c4e5242b26bd46bee29955c727b779b0291a2', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:55:43', '2021-04-19 01:55:43', '2022-04-18 22:55:43');
INSERT INTO `oauth_access_tokens` VALUES ('779a9d327df332b53ce2586b7f7b829c2a1c5a06f43e9ca004076bb0629823d093f21e0651616a97', '8', '3', 'authToken', '[]', '1', '2021-06-02 13:24:10', '2021-06-02 13:24:10', '2022-06-02 10:24:10');
INSERT INTO `oauth_access_tokens` VALUES ('77b04fbad0541508de1b08c6020dd030057013fa8fd3f82f9058b5c3ee635ce83384487f3121da78', '96', '3', 'authToken', '[]', '1', '2021-06-23 18:27:17', '2021-06-23 18:27:17', '2022-06-23 15:27:17');
INSERT INTO `oauth_access_tokens` VALUES ('77e452540019f5407555876fd03d598a840b677e01f97ec576fa01375d7d8594f649806a628d7a5e', '8', '3', 'authToken', '[]', '0', '2021-06-15 19:44:01', '2021-06-15 19:44:01', '2022-06-15 16:44:01');
INSERT INTO `oauth_access_tokens` VALUES ('785150789962ec223fa0c20e283f06e8f6aefe962c183bcf82b36c7b851433a9c124b21d3cc1e794', '99', '3', 'authToken', '[]', '1', '2021-06-24 01:58:58', '2021-06-24 01:58:58', '2022-06-23 22:58:58');
INSERT INTO `oauth_access_tokens` VALUES ('7858e92d5fe8012ec3d64a1537c7b86b8e816cd9432be359ba47df5bd506a0f477dc5ae181a692a7', '103', '3', 'authToken', '[]', '0', '2021-08-26 10:50:36', '2021-08-26 10:50:36', '2022-08-26 10:50:36');
INSERT INTO `oauth_access_tokens` VALUES ('78b3deebfbdf9be10f4a87130d206dec6588bad452a246bb9aa5a849a2d7dc446ec4fed4fbb31533', '78', '3', 'authToken', '[]', '0', '2021-05-26 00:52:31', '2021-05-26 00:52:31', '2022-05-25 21:52:31');
INSERT INTO `oauth_access_tokens` VALUES ('78df4e00ca7cc1b78557093cb7f95b8363bdd0ef8e6bce2ecda6bf3373f269535ba13952309162ae', '61', '3', 'authToken', '[]', '0', '2021-05-02 17:19:39', '2021-05-02 17:19:39', '2022-05-02 14:19:39');
INSERT INTO `oauth_access_tokens` VALUES ('7902a1d0eb49ea74db9fce95836febcd5d3cd1489c1aeef3c393615d63488be93982c1a6807caea7', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:34:43', '2021-05-04 18:34:43', '2022-05-04 15:34:43');
INSERT INTO `oauth_access_tokens` VALUES ('79209c83ac2c0684e6ab432c62d3d13e0b98eaf4fc4c73bdf835e4dc4ef96856562ce25367a2e3f7', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:05:00', '2021-05-04 18:05:00', '2022-05-04 15:05:00');
INSERT INTO `oauth_access_tokens` VALUES ('79247ddfb6e47735fd24a730ec8fe4c070e4d4a83902f4986b252f630551f2de3beb9dac79182fa2', '45', '3', 'authToken', '[]', '0', '2021-04-29 23:58:34', '2021-04-29 23:58:34', '2022-04-29 20:58:34');
INSERT INTO `oauth_access_tokens` VALUES ('7992400e465cf29cb324b8cd762c47ec7e342b7c141accafbad1ff7153f883efaf707484964978dc', '107', '3', 'authToken', '[]', '0', '2021-07-05 11:17:40', '2021-07-05 11:17:40', '2022-07-05 08:17:40');
INSERT INTO `oauth_access_tokens` VALUES ('79df0bcc2a45fc05875439dbf6f2e442759b365ef455c0c9f6e7f7229d1a55b1ee1fab71439c573a', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:28:08', '2021-06-23 20:28:08', '2022-06-23 17:28:08');
INSERT INTO `oauth_access_tokens` VALUES ('7a0f2ec2214ab7a26cfef0d188be4cdabe5863f01921a148c47d16ba20e2b650a36ee01e891d5b03', '73', '3', 'authToken', '[]', '1', '2021-06-01 13:22:11', '2021-06-01 13:22:11', '2022-06-01 10:22:11');
INSERT INTO `oauth_access_tokens` VALUES ('7a34ae6cf973a4e1610c808ddae973ace28f37b2446f5d488d09784cb17b41c1b07ca3b444860419', '67', '3', 'authToken', '[]', '0', '2021-05-18 14:28:18', '2021-05-18 14:28:18', '2022-05-18 11:28:18');
INSERT INTO `oauth_access_tokens` VALUES ('7a5357603c88db682cdcff667a98130138dfc8f7288da0d8518d6b5a5fcf556c189ce329011a1fdc', '6', '3', 'authToken', '[]', '0', '2021-05-03 00:14:16', '2021-05-03 00:14:16', '2022-05-02 21:14:16');
INSERT INTO `oauth_access_tokens` VALUES ('7a97895b6051b373748562377228b29644026e68427644813e88a209da0a82f52851118929b3af0c', '60', '3', 'authToken', '[]', '0', '2021-05-02 13:59:20', '2021-05-02 13:59:20', '2022-05-02 10:59:20');
INSERT INTO `oauth_access_tokens` VALUES ('7aa0718fa5cf59c86094f0d57f13eac36479d32acc4f848cced0192877d201c8bb5d0fcf40949b7e', '9', '3', 'authToken', '[]', '1', '2021-04-16 17:58:20', '2021-04-16 17:58:20', '2022-04-16 14:58:20');
INSERT INTO `oauth_access_tokens` VALUES ('7ac71ffd0b10e1a31c7c019dcff7fb9521848c1a0a34df6bdbc34cd563e50db7efcda24e37d2ae9f', '45', '3', 'authToken', '[]', '0', '2021-04-30 02:11:52', '2021-04-30 02:11:52', '2022-04-29 23:11:52');
INSERT INTO `oauth_access_tokens` VALUES ('7b0b2ec6a42f90f4f4b58f0dababc64e6aa4fff1b71a86026c12db19bc02a22c0ac7074db87e38e1', '106', '3', 'authToken', '[]', '0', '2021-06-29 19:50:57', '2021-06-29 19:50:57', '2022-06-29 16:50:57');
INSERT INTO `oauth_access_tokens` VALUES ('7b55bc65b90305ffa7ee4f9ddd8d2fc878e0aaf2c06d7fec1bc1e5870766396f8653228adb82c984', '95', '3', 'authToken', '[]', '0', '2021-06-23 00:23:32', '2021-06-23 00:23:32', '2022-06-22 21:23:32');
INSERT INTO `oauth_access_tokens` VALUES ('7c05e16dc511a145c0d7fc04ff3edb517ac2889b94f29e4291ee3fb832d4ab27e0d281e4a7b70266', '73', '3', 'authToken', '[]', '1', '2021-05-30 14:20:25', '2021-05-30 14:20:25', '2022-05-30 11:20:25');
INSERT INTO `oauth_access_tokens` VALUES ('7c0ce1204668f7896ae2f0b5d9e099f8e2af770d24d94fb4e210c64b90dd02ee841c636855f7fa2f', '8', '3', 'authToken', '[]', '0', '2021-05-16 23:32:30', '2021-05-16 23:32:30', '2022-05-16 20:32:30');
INSERT INTO `oauth_access_tokens` VALUES ('7c5fe72a4f0a94fc547865d6ffc8252fdc3d37f5aee999dc89e540a9ec6e3935f8be0285625aa6ed', '8', '3', 'authToken', '[]', '0', '2021-07-06 23:15:18', '2021-07-06 23:15:18', '2022-07-06 20:15:18');
INSERT INTO `oauth_access_tokens` VALUES ('7c605ed824a033546002582bf8f22eaf13cb10b8014fd395da6300081bf0c6021bceadf4babcbbc3', '44', '3', 'authToken', '[]', '0', '2021-04-19 01:24:55', '2021-04-19 01:24:55', '2022-04-18 22:24:55');
INSERT INTO `oauth_access_tokens` VALUES ('7c70698149af861936a9d103990c76caf7dd582fd188ef7ef96cebd1cfccf401f689fd934bf564d3', '143', '3', 'authToken', '[]', '0', '2021-07-06 01:35:03', '2021-07-06 01:35:03', '2022-07-05 22:35:03');
INSERT INTO `oauth_access_tokens` VALUES ('7c73e2eb8b4e26b8f50b56dbb30b7a1aa2246367e5729fdbf2458be67b95f3758c1ee262817e3645', '67', '3', 'authToken', '[]', '0', '2021-05-09 02:21:50', '2021-05-09 02:21:50', '2022-05-08 23:21:50');
INSERT INTO `oauth_access_tokens` VALUES ('7d149fd98b34d04f1e83237e6d6a18a5ca6fec1685235d32af64615f14b933191e7079d7704714b1', '6', '3', 'authToken', '[]', '0', '2021-04-22 19:41:23', '2021-04-22 19:41:23', '2022-04-22 16:41:23');
INSERT INTO `oauth_access_tokens` VALUES ('7d2198c81cb4b822cbe364766bf23f04725e8d7e641f05c725e983c7bb822dcd6a87192135ab5f61', '8', '3', 'authToken', '[]', '0', '2021-07-04 20:41:05', '2021-07-04 20:41:05', '2022-07-04 17:41:05');
INSERT INTO `oauth_access_tokens` VALUES ('7d6c434e23182373a065a9a1daab70137ccf7d57847e5549b70bc15f48d5a10ea92fa1cde28fd67b', '39', '3', 'authToken', '[]', '0', '2021-04-18 20:28:58', '2021-04-18 20:28:58', '2022-04-18 17:28:58');
INSERT INTO `oauth_access_tokens` VALUES ('7d96af0e01d895fe452ee7d394b96a8276cdf4b221b39cfd65dc951a8031c66610d3af6bf789847d', '9', '3', 'authToken', '[]', '1', '2021-05-17 17:28:12', '2021-05-17 17:28:12', '2022-05-17 14:28:12');
INSERT INTO `oauth_access_tokens` VALUES ('7e148e9e577699252ed5bcd9b6fc96cca2f5ae11e69cda7676c47a7a0bc75cee040619820c3b0e39', '105', '3', 'authToken', '[]', '0', '2021-06-24 22:38:38', '2021-06-24 22:38:38', '2022-06-24 19:38:38');
INSERT INTO `oauth_access_tokens` VALUES ('7ff2fd3751862820983b5ab59884f616759aa5a408fb0fa5ab739bdb13f4c0a72fa785d8fdb41de5', '8', '3', 'authToken', '[]', '0', '2021-04-11 07:12:07', '2021-04-11 07:12:07', '2022-04-11 04:12:07');
INSERT INTO `oauth_access_tokens` VALUES ('80bb34d6e1690f433cb601d34e31fc130abad37b31b492b115eadea23d77fa3f1656b059f3b81d8d', '67', '3', 'authToken', '[]', '0', '2021-06-24 02:23:44', '2021-06-24 02:23:44', '2022-06-23 23:23:44');
INSERT INTO `oauth_access_tokens` VALUES ('81f5b1ab489faa7bf5b42aa271e00403d6e0abef8e97971739d3a35383aaf47b9fe614dad1fadb2d', '110', '3', 'authToken', '[]', '0', '2021-06-28 21:35:57', '2021-06-28 21:35:57', '2022-06-28 18:35:57');
INSERT INTO `oauth_access_tokens` VALUES ('8224427d0ba3dc9e23bbdda4683c5d392fd7f049e8809109ba67b58a9e693f477c891a5972514164', '90', '3', 'authToken', '[]', '1', '2021-06-21 19:12:19', '2021-06-21 19:12:19', '2022-06-21 16:12:19');
INSERT INTO `oauth_access_tokens` VALUES ('823214d3070f33f073e03afdc850a5043541b058e23737d6df995e2b7ccdce028adc09c55ee8caec', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:59:48', '2021-04-21 23:59:48', '2022-04-21 20:59:48');
INSERT INTO `oauth_access_tokens` VALUES ('823305942c9c4d94088ec4976cf8040f754a7c71e28a75918fe88fc1fc125394d9f80c5aa8632f87', '52', '3', 'authToken', '[]', '1', '2021-04-22 01:03:56', '2021-04-22 01:03:56', '2022-04-21 22:03:56');
INSERT INTO `oauth_access_tokens` VALUES ('826daf60fb9bbda3d3bb44bab72d4f5d7386f35c7236ed3d13b2008d8e5a7bb2b6bd9cebafe582cf', '13', '3', 'authToken', '[]', '0', '2021-04-14 21:16:19', '2021-04-14 21:16:19', '2022-04-14 18:16:19');
INSERT INTO `oauth_access_tokens` VALUES ('82b887b5ad62b40429ddecfcb73ac490bdb4a608861620223fe1317e851a6c607bb98fc54f921989', '91', '3', 'authToken', '[]', '1', '2021-06-21 20:55:54', '2021-06-21 20:55:54', '2022-06-21 17:55:54');
INSERT INTO `oauth_access_tokens` VALUES ('82c23dd4f80c804717bf6e3466d762aa8d2a47df9cd2d7c015cc77a746f6d31defbc7a6dc5af0ede', '8', '3', 'authToken', '[]', '0', '2021-04-12 16:48:08', '2021-04-12 16:48:08', '2022-04-12 13:48:08');
INSERT INTO `oauth_access_tokens` VALUES ('83c0daf7c7c28ca020cfdc3f4850001a00dfdeaa13cea6133a0a4cc9327b523ff2c6bdbba53e86ae', '141', '3', 'authToken', '[]', '0', '2021-07-05 15:36:16', '2021-07-05 15:36:16', '2022-07-05 12:36:16');
INSERT INTO `oauth_access_tokens` VALUES ('84461a4ba4f26623164b6b2586f1556b5334c11a22a13dc60db3af52c555c2467d86cdc15f783dfa', '66', '3', 'authToken', '[]', '1', '2021-06-02 23:02:10', '2021-06-02 23:02:10', '2022-06-02 20:02:10');
INSERT INTO `oauth_access_tokens` VALUES ('8507f8702cc66747eae341798c7ffbea51cc6fbc53ad3467746c1931180db15a7a78818fda35acc7', '22', '3', 'authToken', '[]', '0', '2021-04-16 14:33:43', '2021-04-16 14:33:43', '2022-04-16 11:33:43');
INSERT INTO `oauth_access_tokens` VALUES ('852e30eab363fc0bb5aaa6938953f7beefe2d55538247cce3ca8108a30946f97cf33255bff1facf7', '16', '3', 'authToken', '[]', '0', '2021-04-14 22:05:17', '2021-04-14 22:05:17', '2022-04-14 19:05:17');
INSERT INTO `oauth_access_tokens` VALUES ('8580cdf62c60e248e5f2b409083693902eca797acfa27b4e6e28283b1fbef00e8447361adb56f5a2', '107', '3', 'authToken', '[]', '1', '2021-06-25 10:36:36', '2021-06-25 10:36:36', '2022-06-25 07:36:36');
INSERT INTO `oauth_access_tokens` VALUES ('860b3829f61f5a1fa58eceed3d214785f3b5793db2cd1b0f7383c9a86cc7fa9a270551a7d96be389', '67', '3', 'authToken', '[]', '1', '2021-05-08 16:24:02', '2021-05-08 16:24:02', '2022-05-08 13:24:02');
INSERT INTO `oauth_access_tokens` VALUES ('86e8f12737a22cbfb762ae4707fd5fd2e3612547ce9999f2e4eef6f77e0760dc8f43da92d6f067df', '66', '3', 'authToken', '[]', '1', '2021-05-06 03:42:23', '2021-05-06 03:42:23', '2022-05-06 00:42:23');
INSERT INTO `oauth_access_tokens` VALUES ('86f2341d303079b7a2ff61a16afcaefca0b2c3ead6c4d7bd996796e5d942cd575821aa0f86a5629c', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:28:39', '2021-06-23 20:28:39', '2022-06-23 17:28:39');
INSERT INTO `oauth_access_tokens` VALUES ('86f5cb6aadb007f41195bd8fe2c3e83ffc17be7055bae5d37f398be8a0a232710175eb1963b77288', '8', '3', 'authToken', '[]', '0', '2021-04-15 10:36:18', '2021-04-15 10:36:18', '2022-04-15 07:36:18');
INSERT INTO `oauth_access_tokens` VALUES ('873fba45526d69d287daaee34fdecceaea68b687ec5ec696cff27913f4ea5057a026da82a1cc2058', '97', '3', 'authToken', '[]', '1', '2021-06-23 19:14:48', '2021-06-23 19:14:48', '2022-06-23 16:14:48');
INSERT INTO `oauth_access_tokens` VALUES ('875965441367add59c1894a01bf5cbbd33b1f90e39e9e40e0a5355f37bf366991ea5c08dd6f620d1', '139', '3', 'authToken', '[]', '0', '2021-07-04 19:43:24', '2021-07-04 19:43:24', '2022-07-04 16:43:24');
INSERT INTO `oauth_access_tokens` VALUES ('8789bdb788fd9b963369e00c4be22622a4dea2be4ec5e0028667dcc7d515a7f05a082ce5e2af1c1e', '41', '3', 'authToken', '[]', '0', '2021-04-18 20:37:00', '2021-04-18 20:37:00', '2022-04-18 17:37:00');
INSERT INTO `oauth_access_tokens` VALUES ('87b79a7be8bf5d960b2e49a75d5276de76d9a3c8e09f97223260702b17470bed5e46be03b3e976ae', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:08:29', '2021-05-04 01:08:29', '2022-05-03 22:08:29');
INSERT INTO `oauth_access_tokens` VALUES ('882fb0d2fd6783f1c6fcec6a2fa7fd84afc39900aec38c01885e81d380497e6d932731f84d476b81', '9', '3', 'authToken', '[]', '0', '2021-04-16 14:02:24', '2021-04-16 14:02:24', '2022-04-16 11:02:24');
INSERT INTO `oauth_access_tokens` VALUES ('8873e2a16e5aa241afa95cafdd316220b25a916b28ff006f07d847c97d2301250279396b1328166e', '102', '3', 'authToken', '[]', '1', '2021-06-24 15:36:41', '2021-06-24 15:36:41', '2022-06-24 12:36:41');
INSERT INTO `oauth_access_tokens` VALUES ('88e28a8e30decd0761423091e7106b0558471487ef03071fed3d5ae7b977f368253e2717cf86292a', '8', '3', 'authToken', '[]', '1', '2021-04-22 00:43:18', '2021-04-22 00:43:18', '2022-04-21 21:43:18');
INSERT INTO `oauth_access_tokens` VALUES ('88e80fab95fa0c857020fb1dad8a4ce6c5fa8974d079bb5272803a5472ae66a7125706fc18d8f472', '6', '3', 'authToken', '[]', '1', '2021-04-25 21:16:43', '2021-04-25 21:16:43', '2022-04-25 18:16:43');
INSERT INTO `oauth_access_tokens` VALUES ('89a8b21c7e1fd0057732a759ce2bc487ca6b16a09cc76439ae46c646fb14a0fd8fe2c852298ba334', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:50:31', '2021-04-19 01:50:31', '2022-04-18 22:50:31');
INSERT INTO `oauth_access_tokens` VALUES ('89d53b983d58fd60c212d742fff9321d672162b50306c3822b703306a31abfcf7055b64c5cb44b98', '55', '3', 'authToken', '[]', '1', '2021-04-25 10:25:29', '2021-04-25 10:25:29', '2022-04-25 07:25:29');
INSERT INTO `oauth_access_tokens` VALUES ('89e8c1b87e6b68eb568c0eefa6e8532d836d75c95ff4373eb5d86b7b7784c69b240f19cb7df3ae53', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:15:14', '2021-04-21 23:15:14', '2022-04-21 20:15:14');
INSERT INTO `oauth_access_tokens` VALUES ('89f56dddab7cfaabf60a887a9d0be95c3aa81b046a38d8f709b763c4a3d1112bda17d3553a6a068b', '59', '3', 'authToken', '[]', '1', '2021-05-01 20:05:07', '2021-05-01 20:05:07', '2022-05-01 17:05:07');
INSERT INTO `oauth_access_tokens` VALUES ('89f74a516f9c1db19863a2c202febd74abc698b56e674a2501e7d028377d3d783ac88227eb65e6ca', '70', '3', 'authToken', '[]', '1', '2021-05-18 18:13:57', '2021-05-18 18:13:57', '2022-05-18 15:13:57');
INSERT INTO `oauth_access_tokens` VALUES ('8a227f67df12213ac49de1fd310a821d04210dda063d17abab27034959a9c10085d5e158a14d936a', '6', '3', 'authToken', '[]', '1', '2021-04-25 10:20:46', '2021-04-25 10:20:46', '2022-04-25 07:20:46');
INSERT INTO `oauth_access_tokens` VALUES ('8ab8b8afb6cbd4cafcbce148b8ff0040046fe2ece48b9861806b407c36db3a1b86d9eaa6610a9daf', '8', '3', 'authToken', '[]', '0', '2021-04-15 10:45:23', '2021-04-15 10:45:23', '2022-04-15 07:45:23');
INSERT INTO `oauth_access_tokens` VALUES ('8ac5e3da229d24cf31d4059ae40cb08dd9c4ddc079685ace34470de781532a479079d62dd4e41111', '8', '3', 'authToken', '[]', '1', '2021-05-12 13:23:11', '2021-05-12 13:23:11', '2022-05-12 10:23:11');
INSERT INTO `oauth_access_tokens` VALUES ('8b30f3d7c9f7f72c8c934525c6b1174f3b7415a16c23cb6f30b988012faae107610f0d875e1f32d7', '77', '3', 'authToken', '[]', '0', '2021-05-25 20:48:01', '2021-05-25 20:48:01', '2022-05-25 17:48:01');
INSERT INTO `oauth_access_tokens` VALUES ('8b345a14a68d9a9b83a9764b5139a98ba42a351d7831f73b04f059897fa120b9411242d4393461cc', '44', '3', 'authToken', '[]', '0', '2021-04-18 21:01:06', '2021-04-18 21:01:06', '2022-04-18 18:01:06');
INSERT INTO `oauth_access_tokens` VALUES ('8bf8990e1d65a45ec094a97e4ca5b9afc2a4c28986b5975ee11bfeadaeff9433ff157801f9331848', '87', '3', 'authToken', '[]', '0', '2021-06-15 15:24:01', '2021-06-15 15:24:01', '2022-06-15 12:24:01');
INSERT INTO `oauth_access_tokens` VALUES ('8ce269e509836368730f597debfb17efef6153986aa730464bc50d19355222601785759e47043d54', '28', '3', 'authToken', '[]', '0', '2021-04-18 02:02:37', '2021-04-18 02:02:37', '2022-04-17 23:02:37');
INSERT INTO `oauth_access_tokens` VALUES ('8e6b245a16ea9d0879101a8257375138e58fee4ef1299c64b8398f967e79ec8e72d3cde7b0e07979', '9', '3', 'authToken', '[]', '0', '2021-05-21 23:05:30', '2021-05-21 23:05:30', '2022-05-21 20:05:30');
INSERT INTO `oauth_access_tokens` VALUES ('8fcfdbba0eca96744d3d00c992699d2035f95999ea6694d98b1e5f3fe727245c2fd73b3e234ac5b9', '137', '3', 'authToken', '[]', '0', '2021-07-04 18:55:48', '2021-07-04 18:55:48', '2022-07-04 15:55:48');
INSERT INTO `oauth_access_tokens` VALUES ('8fe5c08d9b81faddccce7e5c9ab20f7d6b2ab1d5bfc298a656c4546ee858bd25b8bc2c25dfc01e2d', '83', '3', 'authToken', '[]', '0', '2021-06-12 14:34:37', '2021-06-12 14:34:37', '2022-06-12 11:34:37');
INSERT INTO `oauth_access_tokens` VALUES ('9119838be1c5d2fa2204d41472f5d21997397fc6a64be1d782c2cab163d4608e3fdcaa23081e6ee4', '70', '3', 'authToken', '[]', '1', '2021-05-23 16:56:24', '2021-05-23 16:56:24', '2022-05-23 13:56:24');
INSERT INTO `oauth_access_tokens` VALUES ('918760c6fc523cd83ec672cc75803c56acfeae00dd3f22ff854ed7e143b3dd58f73185f02ccbae5a', '8', '3', 'authToken', '[]', '0', '2021-06-25 00:44:44', '2021-06-25 00:44:44', '2022-06-24 21:44:44');
INSERT INTO `oauth_access_tokens` VALUES ('918901ce4666d68c00854815f7402d1363472b740974fab75a22a74d01841904f17c749373870512', '9', '3', 'authToken', '[]', '0', '2021-05-21 10:27:54', '2021-05-21 10:27:54', '2022-05-21 07:27:54');
INSERT INTO `oauth_access_tokens` VALUES ('91b0a4c96b109bd708ee283603e606d4caf728966fbef40edf40006add96d32b1242f6a7f4ca8db7', '105', '3', 'authToken', '[]', '0', '2021-06-24 22:38:33', '2021-06-24 22:38:33', '2022-06-24 19:38:33');
INSERT INTO `oauth_access_tokens` VALUES ('91b9ab5604cba032d9026fbf0e31d5dad936be56d878822b89f30ab708f7f4fbd43288da16c7dfe6', '72', '3', 'authToken', '[]', '1', '2021-05-20 21:49:58', '2021-05-20 21:49:58', '2022-05-20 18:49:58');
INSERT INTO `oauth_access_tokens` VALUES ('920508b1c36e0cc50dcac900f3d8806c36138fc5c552b878623de5cd1a125ead8c0200bc02479437', '45', '3', 'authToken', '[]', '0', '2021-04-28 02:38:43', '2021-04-28 02:38:43', '2022-04-27 23:38:43');
INSERT INTO `oauth_access_tokens` VALUES ('9218c5927e414578086907707c0a2681d0af6e7a5788b658d1dd749b1d83d4703f29906f25f3b5ee', '8', '3', 'authToken', '[]', '0', '2021-05-05 17:23:27', '2021-05-05 17:23:27', '2022-05-05 14:23:27');
INSERT INTO `oauth_access_tokens` VALUES ('921c65e6332512fba90e47d2acf872ec7cb1c82072fe598cd7baff360a0d36926765742d87962901', '147', '3', 'authToken', '[]', '0', '2021-07-06 15:50:22', '2021-07-06 15:50:22', '2022-07-06 12:50:22');
INSERT INTO `oauth_access_tokens` VALUES ('9223d609c9814a49fe17a6f23f939de4d779193689dbe1ccd5f2ea8fdb09393170b06cefcabb9530', '28', '3', 'authToken', '[]', '0', '2021-06-24 02:23:02', '2021-06-24 02:23:02', '2022-06-23 23:23:02');
INSERT INTO `oauth_access_tokens` VALUES ('92926c8424d1d0d356dec20ee958b20068c36490f8a5ee85d976f999a476e65f9ce9bc4145c7e4b5', '8', '3', 'authToken', '[]', '1', '2021-06-02 12:17:25', '2021-06-02 12:17:25', '2022-06-02 09:17:25');
INSERT INTO `oauth_access_tokens` VALUES ('92d702cfe9d0478ed3b051d9adac252b10bf41a9ac323d785e4bade53c137a19459a0ad02cb8e554', '8', '3', 'authToken', '[]', '1', '2021-05-06 19:53:28', '2021-05-06 19:53:28', '2022-05-06 16:53:28');
INSERT INTO `oauth_access_tokens` VALUES ('937b32ccb949a0aa58406e01234c2ca63f757d0bdb378f5a5a32fe4288f2f0df019a53158c3f221a', '121', '3', 'authToken', '[]', '0', '2021-06-30 17:40:49', '2021-06-30 17:40:49', '2022-06-30 14:40:49');
INSERT INTO `oauth_access_tokens` VALUES ('93b4d2c2d1620ffa8f64c3d35f08abdf81e538c4863ac99831c10e91af62aa34ef58061e5d999e59', '119', '3', 'authToken', '[]', '0', '2021-07-05 21:24:28', '2021-07-05 21:24:28', '2022-07-05 18:24:28');
INSERT INTO `oauth_access_tokens` VALUES ('93fb8d9dab1559f1f28af1cb649a61483d1bf9c2545c7c23863cb694f21896f8e72dce68b9de4acb', '8', '3', 'authToken', '[]', '1', '2021-06-02 15:24:25', '2021-06-02 15:24:25', '2022-06-02 12:24:25');
INSERT INTO `oauth_access_tokens` VALUES ('9433662ee0ec2d64f2038118da630ee089f4a72195908e0875c76914962486ed8b9cc6d5f9db201e', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:13:52', '2021-05-04 01:13:52', '2022-05-03 22:13:52');
INSERT INTO `oauth_access_tokens` VALUES ('9499b11f682698aeda8fc4375211bbbc61492a772ba16340c34208d2a94fd19136cfd2715d49c2e8', '67', '3', 'authToken', '[]', '0', '2021-06-24 02:15:25', '2021-06-24 02:15:25', '2022-06-23 23:15:25');
INSERT INTO `oauth_access_tokens` VALUES ('94b7a5321b66b95d679981520d85afe0b65bab61d84241af96ec99734f6b3214526602332d2778f6', '8', '3', 'authToken', '[]', '0', '2021-06-26 08:37:22', '2021-06-26 08:37:22', '2022-06-26 05:37:22');
INSERT INTO `oauth_access_tokens` VALUES ('96f019e0231846a334077ece7e5a365fb4202352ab33502a453f2951bd3fb8aa99fa1a078b783b11', '8', '3', 'authToken', '[]', '0', '2021-05-17 21:32:01', '2021-05-17 21:32:01', '2022-05-17 18:32:01');
INSERT INTO `oauth_access_tokens` VALUES ('9763b5811b338aa2ba4747e8d3069679ad6b2bf404475b0a74d14df8f2a7641dbf50c6be85f1f166', '8', '3', 'authToken', '[]', '1', '2021-05-26 10:28:09', '2021-05-26 10:28:09', '2022-05-26 07:28:09');
INSERT INTO `oauth_access_tokens` VALUES ('9777bc0abe12f8602dab5776ba589922798ef424740bd2fbda14c8d6a5031fdb5b9ced9a1b02e3f9', '8', '3', 'authToken', '[]', '0', '2021-04-12 12:39:27', '2021-04-12 12:39:27', '2022-04-12 09:39:27');
INSERT INTO `oauth_access_tokens` VALUES ('9816e7e0ec70f8551669f5f14b1208f3f10dfd4f81dcfa9977ede4b1ca804a39a00dd1b2704d162d', '9', '3', 'authToken', '[]', '1', '2021-05-24 12:59:32', '2021-05-24 12:59:32', '2022-05-24 09:59:32');
INSERT INTO `oauth_access_tokens` VALUES ('98858edb25e241a47f6bb8153bf18837af878ff0a1c9b45e888752902be5627496b8909448e005d4', '8', '3', 'authToken', '[]', '0', '2021-04-29 14:58:31', '2021-04-29 14:58:31', '2022-04-29 11:58:31');
INSERT INTO `oauth_access_tokens` VALUES ('996c27e6babd8084d98fc70f2e717de83299fd4ae827182def652ddfdfd64b44454e0ab41170ba3e', '8', '3', 'authToken', '[]', '1', '2021-04-18 12:31:40', '2021-04-18 12:31:40', '2022-04-18 09:31:40');
INSERT INTO `oauth_access_tokens` VALUES ('99cc9d2657e9499e61cf245ce9e880951bdd289e5d5f925159518268f4e1fc7670660ba88b589472', '66', '3', 'authToken', '[]', '1', '2021-06-24 02:02:37', '2021-06-24 02:02:37', '2022-06-23 23:02:37');
INSERT INTO `oauth_access_tokens` VALUES ('9a339dded8a6e5745174f882cc972fec417e91b560703aac3a5841e305ee5340482909c3daaf49ca', '6', '3', 'authToken', '[]', '1', '2021-04-21 22:56:41', '2021-04-21 22:56:41', '2022-04-21 19:56:41');
INSERT INTO `oauth_access_tokens` VALUES ('9a3b88aae5a141a0ab4c4edce1df184ec6f98923df66287f232292bc248ea2a9ab75c999b71885ad', '6', '3', 'authToken', '[]', '0', '2021-04-02 08:09:43', '2021-04-02 08:09:43', '2022-04-02 05:09:43');
INSERT INTO `oauth_access_tokens` VALUES ('9a4183f3d02cbb4c51b72e4f85d5add1d895fdeb7a874e87cb6d5fbdfa592572500dae2de6aad0a6', '83', '3', 'authToken', '[]', '1', '2021-06-12 17:18:38', '2021-06-12 17:18:38', '2022-06-12 14:18:38');
INSERT INTO `oauth_access_tokens` VALUES ('9b30f401d5f15cdf583b235809f7b67b3cc505a5a8cc072cefe5cd6dfdd821822a40196196c6e3b3', '83', '3', 'authToken', '[]', '1', '2021-06-23 22:42:40', '2021-06-23 22:42:40', '2022-06-23 19:42:40');
INSERT INTO `oauth_access_tokens` VALUES ('9b5d1eccacdaa0c83e5645312e61c862bdbaa9ab31eda22b764891588129e0a46c103f53e9029868', '8', '3', 'authToken', '[]', '0', '2021-04-29 01:33:45', '2021-04-29 01:33:45', '2022-04-28 22:33:45');
INSERT INTO `oauth_access_tokens` VALUES ('9bc1cfae2a8c988469c928626211fbaa32f78e9ef6b4f6e8eebc01f89f5cd3deea04c5704b3872a7', '8', '3', 'authToken', '[]', '1', '2021-05-20 22:29:52', '2021-05-20 22:29:52', '2022-05-20 19:29:52');
INSERT INTO `oauth_access_tokens` VALUES ('9bc694cd5f11f78f0ea40370a238f36ba5f64bb127733333eca190ceb559a095c8d331c576598dc2', '83', '3', 'authToken', '[]', '1', '2021-06-23 23:00:38', '2021-06-23 23:00:38', '2022-06-23 20:00:38');
INSERT INTO `oauth_access_tokens` VALUES ('9c75555f8dc5a4ca991c393fb79cc1d0ea0c667d0c03f393eccb99b59c2e7913ebb08ce84e85f4f9', '8', '3', 'authToken', '[]', '0', '2021-05-04 00:58:18', '2021-05-04 00:58:18', '2022-05-03 21:58:18');
INSERT INTO `oauth_access_tokens` VALUES ('9c9df3e40f24adaec32b7dc72145df65e73c27e1f8048567f674e0318d64e029850576b62322e0a1', '8', '3', 'authToken', '[]', '1', '2021-05-03 00:34:39', '2021-05-03 00:34:39', '2022-05-02 21:34:39');
INSERT INTO `oauth_access_tokens` VALUES ('9cae997b943eab767a15f724b88eb78fdfaa0520f19195f3fd86112a3175ceecbf023c224d8e36cb', '8', '3', 'authToken', '[]', '1', '2021-05-19 08:13:23', '2021-05-19 08:13:23', '2022-05-19 05:13:23');
INSERT INTO `oauth_access_tokens` VALUES ('9d01d638b40fc40079cafe43fa79dab8e70395049ccb82f45a14ad67939d4c2e71dc680d7def7153', '6', '3', 'authToken', '[]', '0', '2021-05-04 18:32:40', '2021-05-04 18:32:40', '2022-05-04 15:32:40');
INSERT INTO `oauth_access_tokens` VALUES ('9d0e53dabbb72ebe7d6b73a23af6479027183f355948e6448b98268af4bf07c9c9fe9a36e25a0903', '75', '3', 'authToken', '[]', '0', '2021-06-18 23:33:45', '2021-06-18 23:33:45', '2022-06-18 20:33:45');
INSERT INTO `oauth_access_tokens` VALUES ('9d457dc3d93254e5de39c7d1530026edfedf924a02f57560c6201c6891dbb794c1a9a5c9d08d249f', '8', '3', 'authToken', '[]', '0', '2021-04-18 19:27:42', '2021-04-18 19:27:42', '2022-04-18 16:27:42');
INSERT INTO `oauth_access_tokens` VALUES ('9d72ed33d923df2b16cf12acb6b1b3ecbd228089b77ed4c110820c903f7a9e59c1b8729d2b8f86c3', '8', '3', 'authToken', '[]', '1', '2021-05-17 18:13:26', '2021-05-17 18:13:26', '2022-05-17 15:13:26');
INSERT INTO `oauth_access_tokens` VALUES ('9dc791695ce6ce940e3f1e014a1521b08431e543b3ab23eed74afd7b47b111618141a3781bc6e14b', '8', '3', 'authToken', '[]', '1', '2021-05-06 16:44:22', '2021-05-06 16:44:22', '2022-05-06 13:44:22');
INSERT INTO `oauth_access_tokens` VALUES ('9ee309292f2e2b241da17bb43ead3bf783f30dae767dd7e57069f5a920a8a09e4216f644b7ff7152', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:09:11', '2021-04-22 00:09:11', '2022-04-21 21:09:11');
INSERT INTO `oauth_access_tokens` VALUES ('9f2e5508190fd702a6fe4d2a789c4da8e100144ce2a4f2ec902a6f8a6b99f14b5fed5f42f5021879', '8', '3', 'authToken', '[]', '0', '2021-04-15 18:26:04', '2021-04-15 18:26:04', '2022-04-15 15:26:04');
INSERT INTO `oauth_access_tokens` VALUES ('9f2f101eb93669e70a0f4f765d0f430f4f63c86b248d6b5478d11263dd02a2125e58a32c383749a0', '66', '3', 'authToken', '[]', '1', '2021-06-21 19:12:47', '2021-06-21 19:12:47', '2022-06-21 16:12:47');
INSERT INTO `oauth_access_tokens` VALUES ('9f529c18c3d290563ae7dedf653f587322d0f8a8fbaede2fe86a1c51cae1073acfb056856ce561c9', '8', '3', 'authToken', '[]', '1', '2021-04-22 00:00:17', '2021-04-22 00:00:17', '2022-04-21 21:00:17');
INSERT INTO `oauth_access_tokens` VALUES ('9f65136335afe8119e49aaa5dbbc93122c409173edd1d41c8a2d174192dbccfc643a72040feb148a', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:31:08', '2021-04-21 23:31:08', '2022-04-21 20:31:08');
INSERT INTO `oauth_access_tokens` VALUES ('9fd59b32351ee57c3f98451127101af5bb870275c9b9ef7214fccd435af9c1d3a7b2caa1426304ae', '90', '3', 'authToken', '[]', '1', '2021-06-21 19:01:50', '2021-06-21 19:01:50', '2022-06-21 16:01:50');
INSERT INTO `oauth_access_tokens` VALUES ('9ff38b899776538637607da0024d6167472494bf7758a6b2e78ad792c4679ff95916a9e180f66787', '9', '3', 'authToken', '[]', '1', '2021-04-16 18:09:59', '2021-04-16 18:09:59', '2022-04-16 15:09:59');
INSERT INTO `oauth_access_tokens` VALUES ('a0176f4f39a1dd8149f305916d55fcfa54979a5828882304692f8bc16fbbe45f5b948b72634f2b28', '83', '3', 'authToken', '[]', '0', '2021-06-23 23:47:57', '2021-06-23 23:47:57', '2022-06-23 20:47:57');
INSERT INTO `oauth_access_tokens` VALUES ('a03a389897eb4aec6d96e472f26b01ece847db8640dd98e2793544784f7d9040226fe0575b2bf3af', '8', '3', 'authToken', '[]', '0', '2021-05-26 18:24:11', '2021-05-26 18:24:11', '2022-05-26 15:24:11');
INSERT INTO `oauth_access_tokens` VALUES ('a05c08f1c82ca72f8573bddfc4105d9cf6d9a35cf543a778759efd5e582bde9b044e78f1506da9a4', '8', '3', 'authToken', '[]', '1', '2021-05-21 00:34:12', '2021-05-21 00:34:12', '2022-05-20 21:34:12');
INSERT INTO `oauth_access_tokens` VALUES ('a0c687b5dea75510913d7f6d7ff32613f92be23782bb5af0b798770016d9a732d7cdc1eabd2e7c93', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:15:33', '2021-04-22 00:15:33', '2022-04-21 21:15:33');
INSERT INTO `oauth_access_tokens` VALUES ('a104b222f5160e399bb2bd62aa556d39c04861dd6322f5cf6c520482e9fbfbf4928b61735b3bf11a', '125', '3', 'authToken', '[]', '1', '2021-06-30 21:49:52', '2021-06-30 21:49:52', '2022-06-30 18:49:52');
INSERT INTO `oauth_access_tokens` VALUES ('a11df7c86d3e83b1322675e3c6cc59dc6158e43119c0bce21822d75e376068852f453556e6f994a8', '73', '3', 'authToken', '[]', '0', '2021-05-27 22:25:55', '2021-05-27 22:25:55', '2022-05-27 19:25:55');
INSERT INTO `oauth_access_tokens` VALUES ('a17952097b2cb5080d2a940c89d4ea34bfe5331cb702dc2479161d1cdafd48c3d507f58c1ce9c3d8', '8', '3', 'authToken', '[]', '1', '2021-06-03 07:19:42', '2021-06-03 07:19:42', '2022-06-03 04:19:42');
INSERT INTO `oauth_access_tokens` VALUES ('a1c761a6e592e37b5f55c5de775bc490ebbb76eb466e9d8d7d66957a8d7db5f5881807aa7f81f0f0', '8', '3', 'authToken', '[]', '0', '2021-05-22 18:00:49', '2021-05-22 18:00:49', '2022-05-22 15:00:49');
INSERT INTO `oauth_access_tokens` VALUES ('a1e1716d1c3d2c0f5b20dd781235ec6026120c25dc57f2784935269f23d9360c4287ad1121586566', '8', '3', 'authToken', '[]', '0', '2021-04-12 17:34:24', '2021-04-12 17:34:24', '2022-04-12 14:34:24');
INSERT INTO `oauth_access_tokens` VALUES ('a1f9433419cd7255746d51aa2c9908a713f3c25489a3e33493e7172cadeb4d79ccb1b59763c36603', '28', '3', 'authToken', '[]', '0', '2021-05-25 17:36:35', '2021-05-25 17:36:35', '2022-05-25 14:36:35');
INSERT INTO `oauth_access_tokens` VALUES ('a228b191c6f15666bae7deb28fda144b2dde9f10353c710237b2815414b29c0a8aa4aa9f9844d46b', '60', '3', 'authToken', '[]', '0', '2021-05-02 14:00:09', '2021-05-02 14:00:09', '2022-05-02 11:00:09');
INSERT INTO `oauth_access_tokens` VALUES ('a2c77e4178ab80473864d7a613dde9a7cc954d6067955275a99c9e97d473bf453485dd973ae13e4e', '103', '3', 'authToken', '[]', '1', '2021-06-24 10:20:15', '2021-06-24 10:20:15', '2022-06-24 07:20:15');
INSERT INTO `oauth_access_tokens` VALUES ('a2ea9e545faf9a23bbba9502e48d4958dc4564b0e4dc2b26eed0fc1226e23796d334e1330794b107', '7', '3', 'authToken', '[]', '0', '2021-04-16 13:31:29', '2021-04-16 13:31:29', '2022-04-16 10:31:29');
INSERT INTO `oauth_access_tokens` VALUES ('a309fb9b7d8c7dba4aa7d8bb5e17ad64d3536f6b321a34d66a5803307f868e469a4a009706ff7bb7', '8', '3', 'authToken', '[]', '0', '2021-05-06 19:48:45', '2021-05-06 19:48:45', '2022-05-06 16:48:45');
INSERT INTO `oauth_access_tokens` VALUES ('a323caab1a223cb5dc751f816359ba3cb936f39aab63db9c8797ebd0cb0776dbce3d845ca6b60484', '94', '3', 'authToken', '[]', '0', '2021-06-23 00:44:16', '2021-06-23 00:44:16', '2022-06-22 21:44:16');
INSERT INTO `oauth_access_tokens` VALUES ('a370010860b028378f113828c268e8adbebe5be0561c3b713ab9e78c03bb53a219e6152c3b0c8a93', '66', '3', 'authToken', '[]', '1', '2021-06-23 18:00:51', '2021-06-23 18:00:51', '2022-06-23 15:00:51');
INSERT INTO `oauth_access_tokens` VALUES ('a38e9648f3e57b0e978a5411afbc0f9212b0ff6573ff8d54023c3b05b9a98b63e38e2c1487d8cfbe', '8', '3', 'authToken', '[]', '1', '2021-05-22 20:31:00', '2021-05-22 20:31:00', '2022-05-22 17:31:00');
INSERT INTO `oauth_access_tokens` VALUES ('a42bc5b2c6251297aef941368cf71816f04759bec1242eaab91f3ca08e5cc4580e1ce532cafd4509', '8', '3', 'authToken', '[]', '0', '2021-04-12 15:27:56', '2021-04-12 15:27:56', '2022-04-12 12:27:56');
INSERT INTO `oauth_access_tokens` VALUES ('a45f2e327dac73ac035d7ff20c1f4b38ee70990fb5491724fe912c4b326473d920e1acd0c755ac93', '8', '3', 'authToken', '[]', '1', '2021-05-01 13:47:19', '2021-05-01 13:47:19', '2022-05-01 10:47:19');
INSERT INTO `oauth_access_tokens` VALUES ('a4e303028d0c352c22d2de94a9b7157fd1ccbafda1699709c1fe9322aa605f847d99a7477f76bfe1', '8', '3', 'authToken', '[]', '0', '2021-07-04 16:04:46', '2021-07-04 16:04:46', '2022-07-04 13:04:46');
INSERT INTO `oauth_access_tokens` VALUES ('a522c5544f3645664d6cf7d0f83a0de97dac76c9075c488f7148846559f65b2b8cfb55ceb4d9120e', '8', '3', 'authToken', '[]', '1', '2021-06-24 16:07:25', '2021-06-24 16:07:25', '2022-06-24 13:07:25');
INSERT INTO `oauth_access_tokens` VALUES ('a56c5c7ea60f943c0c4ad874d2e243b7f94d04c320eeb228aae8009203cd3d8a5498a6ef54ce7a87', '8', '3', 'authToken', '[]', '0', '2021-04-30 20:30:28', '2021-04-30 20:30:28', '2022-04-30 17:30:28');
INSERT INTO `oauth_access_tokens` VALUES ('a5890c37fc6367a7f815cb8b06c76feeccb6a49723494f2d7533cdd49245595b3baad3cd1d725a5f', '70', '3', 'authToken', '[]', '0', '2021-06-15 14:39:41', '2021-06-15 14:39:41', '2022-06-15 11:39:41');
INSERT INTO `oauth_access_tokens` VALUES ('a58d5579358a1e21b4849bbb87a55f92aaa70ea9376d686ce080c9afb849a781a3a13afecf73048a', '6', '3', 'authToken', '[]', '0', '2021-04-22 00:01:53', '2021-04-22 00:01:53', '2022-04-21 21:01:53');
INSERT INTO `oauth_access_tokens` VALUES ('a5adef5d9381b02c7cd3510e62c89862b81337f8cc11c1509ac48099b56b5fb5a9ac38272d9f58f0', '45', '3', 'authToken', '[]', '0', '2021-04-30 02:26:59', '2021-04-30 02:26:59', '2022-04-29 23:26:59');
INSERT INTO `oauth_access_tokens` VALUES ('a66363f30492dab5a9875da3d4ba7db04591fa50f893dc6dce0e512987aca4ad5df536f588a134d1', '83', '3', 'authToken', '[]', '1', '2021-06-23 18:29:59', '2021-06-23 18:29:59', '2022-06-23 15:29:59');
INSERT INTO `oauth_access_tokens` VALUES ('a725109a0503f80803e81ada92fca60f008e2a8041c9d8b099a4f8ffdef194e8b0f9d8150a6663c5', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:07:54', '2021-05-03 00:07:54', '2022-05-02 21:07:54');
INSERT INTO `oauth_access_tokens` VALUES ('a75dd3baeb08d42f715b7040eac2af51fb6819c8bcb1a519e9c47400b1951ea163ae4689793ae68b', '6', '3', 'authToken', '[]', '1', '2021-05-01 11:43:01', '2021-05-01 11:43:01', '2022-05-01 08:43:01');
INSERT INTO `oauth_access_tokens` VALUES ('a79e270e480e4f61f8777964dd6ceb7d1ed0fd5fa2be8c4f71a316af0119f306d27d678a4247e34a', '8', '3', 'authToken', '[]', '0', '2021-06-09 21:59:09', '2021-06-09 21:59:09', '2022-06-09 18:59:09');
INSERT INTO `oauth_access_tokens` VALUES ('a7c165ab1ab8faad6ca308fd99691e6f6c526686a945fbe1fbb83017aa2e482e11fd570add245732', '70', '3', 'authToken', '[]', '0', '2021-06-16 23:01:35', '2021-06-16 23:01:35', '2022-06-16 20:01:35');
INSERT INTO `oauth_access_tokens` VALUES ('a86012c19cc5deb1b92675f62a0c8259f0fe0785fe177f16b7dda4e2d82da6efbec8f2cbb6d47e5b', '88', '3', 'authToken', '[]', '1', '2021-06-16 15:50:35', '2021-06-16 15:50:35', '2022-06-16 12:50:35');
INSERT INTO `oauth_access_tokens` VALUES ('a96afeb3b1f92e508905625ede381ca284ce77505cc975d49e0effb4ef95cacfb27faaa6ef48d9f8', '66', '3', 'authToken', '[]', '1', '2021-06-24 01:58:30', '2021-06-24 01:58:30', '2022-06-23 22:58:30');
INSERT INTO `oauth_access_tokens` VALUES ('a96e9977c99f9cf617d4934a696a6a7105a4f6f4b7605942d78b7baf943dd7328b010f86fe277fe6', '9', '3', 'authToken', '[]', '0', '2021-05-26 14:04:38', '2021-05-26 14:04:38', '2022-05-26 11:04:38');
INSERT INTO `oauth_access_tokens` VALUES ('aa21886f6cefe5e2d8d8723bbaf8088c699da358b40b2a05779905726b036ac65b45572911fd8059', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:12:50', '2021-04-21 23:12:50', '2022-04-21 20:12:50');
INSERT INTO `oauth_access_tokens` VALUES ('aa3684f054c44898640257c966b9832d563c09899fb9600d25730f1bbb0d87280f7f2e09d21bed32', '6', '3', 'authToken', '[]', '0', '2021-04-21 12:23:36', '2021-04-21 12:23:36', '2022-04-21 09:23:36');
INSERT INTO `oauth_access_tokens` VALUES ('aba5a6fbd1cc3f33b5b5013d99af60d8763c50072c44010c876069ffe0f05906f5253af867bc92fe', '93', '3', 'authToken', '[]', '1', '2021-06-22 13:40:59', '2021-06-22 13:40:59', '2022-06-22 10:40:59');
INSERT INTO `oauth_access_tokens` VALUES ('abebbd19f1667d68f6b02aabb9c31ab3f133922be7cf9ab1c9ab42d34c66b13ac6394dfb73ea451c', '8', '3', 'authToken', '[]', '1', '2021-05-18 14:22:21', '2021-05-18 14:22:21', '2022-05-18 11:22:21');
INSERT INTO `oauth_access_tokens` VALUES ('ac9ad076a98e6a317acae317bbc525d504fcca243f82be9d7dc3d1cf3f481a42788c97b450fa228e', '78', '3', 'authToken', '[]', '0', '2021-06-24 02:14:23', '2021-06-24 02:14:23', '2022-06-23 23:14:23');
INSERT INTO `oauth_access_tokens` VALUES ('acde746cc6905771d81564402792fc54b52e1e12dc7f52f892eecb1da5f9965424f856d6fb3ee5cc', '56', '3', 'authToken', '[]', '0', '2021-04-28 21:13:25', '2021-04-28 21:13:25', '2022-04-28 18:13:25');
INSERT INTO `oauth_access_tokens` VALUES ('ad7ce81cf96c74a5ce828d2871bf367bcd68cc833aaacfd918118cb4c99249efd5a538d304a745a0', '106', '3', 'authToken', '[]', '1', '2021-06-27 02:09:40', '2021-06-27 02:09:40', '2022-06-26 23:09:40');
INSERT INTO `oauth_access_tokens` VALUES ('adae13e8748ad46b8215203e26088c18d90a3bbb0ee2c2f766a2c440297e7141bb12237304e3ca7c', '9', '3', 'authToken', '[]', '0', '2021-05-20 20:01:42', '2021-05-20 20:01:42', '2022-05-20 17:01:42');
INSERT INTO `oauth_access_tokens` VALUES ('ae7d894dbe336396b1f01c0ddf76b28095badefc684e77ced96e1c1db9e65b639d2ae8d709f74e87', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:21:11', '2021-05-03 00:21:11', '2022-05-02 21:21:11');
INSERT INTO `oauth_access_tokens` VALUES ('ae8757a63b5048172ea7c1a6f8cb2da3bef92b18d4c8f658f21a011edc9cb8a4d503aa5256c49771', '115', '3', 'authToken', '[]', '0', '2021-06-28 22:56:32', '2021-06-28 22:56:32', '2022-06-28 19:56:32');
INSERT INTO `oauth_access_tokens` VALUES ('ae99b4175a207809953b69d6d3c19d639e5b1bd87043499b37c857a6396b8459549a76ab5178e177', '40', '3', 'authToken', '[]', '0', '2021-04-18 20:31:17', '2021-04-18 20:31:17', '2022-04-18 17:31:17');
INSERT INTO `oauth_access_tokens` VALUES ('aea721664a2e5d0fb646826f817f593e5d4420d60ad39a056b61f461927e74fa77b3d84e88fb8260', '76', '3', 'authToken', '[]', '1', '2021-05-25 11:15:30', '2021-05-25 11:15:30', '2022-05-25 08:15:30');
INSERT INTO `oauth_access_tokens` VALUES ('aeb7554f4ba6cd1f68eeed068a3b56c757d333872dcc5a29f53d7c689f44d3f7a43b64e439e86816', '38', '3', 'authToken', '[]', '0', '2021-04-18 20:25:31', '2021-04-18 20:25:31', '2022-04-18 17:25:31');
INSERT INTO `oauth_access_tokens` VALUES ('af1f24fd42b1d136b355dd76771b438d955a3d551e389f270ffe90b08e2b12d2c56a3919c540619b', '45', '3', 'authToken', '[]', '0', '2021-04-28 19:18:16', '2021-04-28 19:18:16', '2022-04-28 16:18:16');
INSERT INTO `oauth_access_tokens` VALUES ('af54177d471a91c02ec955b8519ac7ca6e26fbe2e766040e740d62346394d90381252595a17e5506', '8', '3', 'authToken', '[]', '0', '2021-05-04 19:50:28', '2021-05-04 19:50:28', '2022-05-04 16:50:28');
INSERT INTO `oauth_access_tokens` VALUES ('af6619d962aa9905b78b7516bd511e47c7307858b67afed5ac0e107ddcda754bd1a1ac2b65871c3b', '8', '3', 'authToken', '[]', '0', '2021-05-04 19:55:39', '2021-05-04 19:55:39', '2022-05-04 16:55:39');
INSERT INTO `oauth_access_tokens` VALUES ('b02db100cdb3f1479870a5d3f44543bab3547fce07f06cfdedbfbdc21813502e9ccbec3fdfb1bdb2', '6', '3', 'authToken', '[]', '0', '2021-04-07 09:48:24', '2021-04-07 09:48:24', '2022-04-07 06:48:24');
INSERT INTO `oauth_access_tokens` VALUES ('b0cc0b866ed778c916e468e6172cb579c659051fab3ce6c75789a1c518fde0e432f4999df65945a0', '6', '3', 'authToken', '[]', '0', '2021-04-14 14:38:03', '2021-04-14 14:38:03', '2022-04-14 11:38:03');
INSERT INTO `oauth_access_tokens` VALUES ('b140966c0d1ce7fa03b178e7a32840fede50077b0931659308bbda63e8b400d30a919970c1397e99', '8', '3', 'authToken', '[]', '0', '2021-04-30 20:38:27', '2021-04-30 20:38:27', '2022-04-30 17:38:27');
INSERT INTO `oauth_access_tokens` VALUES ('b2a448d0d0b2200890cd7dab9026f3d22594d4f68aca8545504f21af62b3bce6bfac64bf2f95f6f3', '6', '3', 'authToken', '[]', '1', '2021-04-22 19:42:28', '2021-04-22 19:42:28', '2022-04-22 16:42:28');
INSERT INTO `oauth_access_tokens` VALUES ('b38d5b8a61e6056572c1e7eae139c72c969230c7612057fa96eac100a1c5a1b73e481cce5d229d4a', '9', '3', 'authToken', '[]', '0', '2021-06-15 22:35:33', '2021-06-15 22:35:33', '2022-06-15 19:35:33');
INSERT INTO `oauth_access_tokens` VALUES ('b3d2299ad3dd1a13be3a3b44878fdac3e11bbfd067007f793ab5cd93d6e13a6b0270213b8166d87c', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:18:36', '2021-05-03 00:18:36', '2022-05-02 21:18:36');
INSERT INTO `oauth_access_tokens` VALUES ('b3f13d230c4910bdce7368b055c5da40a5d3d29c7026a04e45178cec28022c5edc48b32b0554f7e6', '11', '3', 'authToken', '[]', '1', '2021-04-30 20:08:07', '2021-04-30 20:08:07', '2022-04-30 17:08:07');
INSERT INTO `oauth_access_tokens` VALUES ('b42cb1d7868ed264d14e5be50d00e1a6bcafb0a66eeb967339994c5f9a9498c87850d8aff9191e18', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:36:52', '2021-06-23 20:36:52', '2022-06-23 17:36:52');
INSERT INTO `oauth_access_tokens` VALUES ('b4324195c04bfc6089c62e26eceee7a06d6a7602a1d5d604d46784e4e8a0f2374d63b24e8b4d3240', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:51:40', '2021-04-21 23:51:40', '2022-04-21 20:51:40');
INSERT INTO `oauth_access_tokens` VALUES ('b482b1673946271c53f5beb35d8b0a4029ee3fb04a0bef7128fc3bc28b77a20a3964bfb01699f95e', '62', '3', 'authToken', '[]', '1', '2021-05-06 01:55:35', '2021-05-06 01:55:35', '2022-05-05 22:55:35');
INSERT INTO `oauth_access_tokens` VALUES ('b55e5281ed8d6f28c7a17075488e36050499abf3144a09b8d396923267df33ea20384c44344fdc2b', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:54:24', '2021-04-19 01:54:24', '2022-04-18 22:54:24');
INSERT INTO `oauth_access_tokens` VALUES ('b5c2cc9dc926c094c69f89f712889028c6450abb8003286417b3595fa99a8ab16f4b74acab1211ea', '9', '3', 'authToken', '[]', '0', '2021-05-25 12:11:19', '2021-05-25 12:11:19', '2022-05-25 09:11:19');
INSERT INTO `oauth_access_tokens` VALUES ('b5fafa681f16e7fa8a01c9a3b9080089cb6bb382b128849acacdd5ce3745a9fddfc5d95d332f7dba', '73', '3', 'authToken', '[]', '1', '2021-05-22 01:23:11', '2021-05-22 01:23:11', '2022-05-21 22:23:11');
INSERT INTO `oauth_access_tokens` VALUES ('b5ff0de8b81365f2e76f2bf39b2ad4b7fafcd112b206426089d030769e857f38321ea0b67d9e0df5', '70', '3', 'authToken', '[]', '0', '2021-06-22 23:00:27', '2021-06-22 23:00:27', '2022-06-22 20:00:27');
INSERT INTO `oauth_access_tokens` VALUES ('b635b02bced76bd3a36f4af0503e531e4963de4313fee554ef573cc3dfd27da8ff50af3efa5c1dea', '18', '3', 'authToken', '[]', '0', '2021-04-15 20:32:35', '2021-04-15 20:32:35', '2022-04-15 17:32:35');
INSERT INTO `oauth_access_tokens` VALUES ('b66f2aed8f38e8f7e6e771545f1a7a4f5f0b589e58dd2e852fa3f16a4bea24f3339bd4ebb228708b', '9', '3', 'authToken', '[]', '1', '2021-05-06 13:11:27', '2021-05-06 13:11:27', '2022-05-06 10:11:27');
INSERT INTO `oauth_access_tokens` VALUES ('b66f51f115cfb286b4998c62ce1ab4575488f4248719368d339265bae638d29576c525627e723d6d', '64', '3', 'authToken', '[]', '1', '2021-05-03 00:26:58', '2021-05-03 00:26:58', '2022-05-02 21:26:58');
INSERT INTO `oauth_access_tokens` VALUES ('b66fc0e6447984990d009b573f612194288f8988a37c20f073c67afc6fe9627c5e35c2b260d73340', '83', '3', 'authToken', '[]', '0', '2021-06-12 17:13:57', '2021-06-12 17:13:57', '2022-06-12 14:13:57');
INSERT INTO `oauth_access_tokens` VALUES ('b6714b6980ff74cb5cc9c86531ad3a2e11fa44b416ba25e4dc71101bb78a4bae108ba6fa6359a029', '73', '3', 'authToken', '[]', '1', '2021-05-20 23:48:11', '2021-05-20 23:48:11', '2022-05-20 20:48:11');
INSERT INTO `oauth_access_tokens` VALUES ('b75a052fa8a0e502e3a1441e7add3818a7c78db18c72b29cfb8ef397fe4cb3eca51fb0fd4f438b3d', '8', '3', 'authToken', '[]', '0', '2021-04-12 17:29:25', '2021-04-12 17:29:25', '2022-04-12 14:29:25');
INSERT INTO `oauth_access_tokens` VALUES ('b79104c19fa0c18febddfaaf5d70e4f664dee368e518ffbc58bfa894cf05cbd3862f8fabdb9c80d9', '8', '3', 'authToken', '[]', '0', '2021-05-27 13:51:09', '2021-05-27 13:51:09', '2022-05-27 10:51:09');
INSERT INTO `oauth_access_tokens` VALUES ('b8807e4a19c404f25ad06c20255f956d105964e7112d0e71aedd111d7730684bef97231acd1d496a', '6', '3', 'authToken', '[]', '1', '2021-05-21 13:04:03', '2021-05-21 13:04:03', '2022-05-21 10:04:03');
INSERT INTO `oauth_access_tokens` VALUES ('b88d663fb31410485f7b1c31d2d3652a6d1eb74ac37a36dd38360f652c81754689b2babae2b69abd', '102', '3', 'authToken', '[]', '1', '2021-07-05 09:40:33', '2021-07-05 09:40:33', '2022-07-05 06:40:33');
INSERT INTO `oauth_access_tokens` VALUES ('b8d6fcd677e225773839ee14901ea7aaaf5d7a09a1f7fb7fc53a56eb4faada782233b02fa7e787b0', '9', '3', 'authToken', '[]', '1', '2021-04-16 17:56:30', '2021-04-16 17:56:30', '2022-04-16 14:56:30');
INSERT INTO `oauth_access_tokens` VALUES ('b92a686c9ae09abe7de56533b01458554026ce481e02f22847d2273b4f478083ae04435e07e81354', '107', '3', 'authToken', '[]', '0', '2021-07-06 22:34:24', '2021-07-06 22:34:24', '2022-07-06 19:34:24');
INSERT INTO `oauth_access_tokens` VALUES ('b935674273a5851881d846d69cd328ec3903f69db1f6dd9c8fdd2da3a7ff1eab6e9f02747e7a284a', '9', '3', 'authToken', '[]', '1', '2021-05-26 14:09:29', '2021-05-26 14:09:29', '2022-05-26 11:09:29');
INSERT INTO `oauth_access_tokens` VALUES ('b9a1872d2aca3b6ff711afc96d6c32739d733bfc1581d57a3cbc517eac9f5c4b9056db1a56b9305a', '67', '3', 'authToken', '[]', '0', '2021-05-27 18:15:40', '2021-05-27 18:15:40', '2022-05-27 15:15:40');
INSERT INTO `oauth_access_tokens` VALUES ('ba580d027dd43d4ffec8607bed2da7828fd89b11edc5155e2935b034b7d6ffae71069ca299446827', '15', '3', 'authToken', '[]', '0', '2021-04-14 22:00:06', '2021-04-14 22:00:06', '2022-04-14 19:00:06');
INSERT INTO `oauth_access_tokens` VALUES ('ba846e23e0618d747bea45430cbdacd59dbdbcaea25dccc81bdf9bb7c3cb2a615eceb1680ad92991', '46', '3', 'authToken', '[]', '0', '2021-04-19 01:16:16', '2021-04-19 01:16:16', '2022-04-18 22:16:16');
INSERT INTO `oauth_access_tokens` VALUES ('bacdd111c4b7b6278c3da2a1d4e88dc4e7264cfa75a4606d8644807c49f3345a49c0e3cf3df1542b', '8', '3', 'authToken', '[]', '0', '2021-06-25 18:36:33', '2021-06-25 18:36:33', '2022-06-25 15:36:33');
INSERT INTO `oauth_access_tokens` VALUES ('bb12736186c0ed9a9426904d36c47380d1d2b8435e4b9f663a872ed406a1d24aea4534d3f84a7dd8', '6', '3', 'authToken', '[]', '1', '2021-04-23 15:43:33', '2021-04-23 15:43:33', '2022-04-23 12:43:33');
INSERT INTO `oauth_access_tokens` VALUES ('bb36a4711fd79e19ba21e8f4ee83f887f2068ac35b7677ea1333e2cd77981f9b6b4221a78b25c1cb', '8', '3', 'authToken', '[]', '0', '2021-04-11 07:11:15', '2021-04-11 07:11:15', '2022-04-11 04:11:15');
INSERT INTO `oauth_access_tokens` VALUES ('bb5a9eb4381ba901a764ed81330e3fa63e4692ea9665e594c10cc9f2bebec76d679793abe559dc36', '9', '3', 'authToken', '[]', '1', '2021-05-21 18:51:20', '2021-05-21 18:51:20', '2022-05-21 15:51:20');
INSERT INTO `oauth_access_tokens` VALUES ('bbfb48cef5f44c74642fa6bfe70ada9a87565790eb9dd15bad8c730aba0fe4a38d4b29493137f08a', '45', '3', 'authToken', '[]', '0', '2021-05-04 19:10:12', '2021-05-04 19:10:12', '2022-05-04 16:10:12');
INSERT INTO `oauth_access_tokens` VALUES ('bc21805f8f0448af78401bd1bd2220850dac598af74f3c42f9a358f78b610c6f304ef184be30b05a', '28', '3', 'authToken', '[]', '0', '2021-04-18 02:02:47', '2021-04-18 02:02:47', '2022-04-17 23:02:47');
INSERT INTO `oauth_access_tokens` VALUES ('bc28a27b13ac2e943369bba82eea5456cffb45a7ff0989b37f40e5dd1532bac91942a88b51835ad6', '88', '3', 'authToken', '[]', '1', '2021-06-17 14:47:03', '2021-06-17 14:47:03', '2022-06-17 11:47:03');
INSERT INTO `oauth_access_tokens` VALUES ('bc54d629111a2896aeda5eb207edc3dc49096765a8ad4bb00ae0e14b4f7cd046ce5692582d6f209f', '8', '3', 'authToken', '[]', '1', '2021-06-01 13:04:07', '2021-06-01 13:04:07', '2022-06-01 10:04:07');
INSERT INTO `oauth_access_tokens` VALUES ('bccb03e43d88e47d03b21996f2b28920a6c0f5cd429fc0f96e8ac8e84ae6f77634158b27878afdeb', '7', '3', 'authToken', '[]', '0', '2021-04-14 08:00:17', '2021-04-14 08:00:17', '2022-04-14 05:00:17');
INSERT INTO `oauth_access_tokens` VALUES ('bd0ad014f5be07116068c666257b2ec3f2fbc22249f1ae5e699743adf57788861dc64983bee25ea6', '119', '3', 'authToken', '[]', '0', '2021-06-30 14:46:45', '2021-06-30 14:46:45', '2022-06-30 11:46:45');
INSERT INTO `oauth_access_tokens` VALUES ('bd3f18ac4d826eef7b4be95c9a1f99cb91a5fd1b6a752888ee1095c542dd4a502ddb1f06996abb97', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:35:41', '2021-04-21 23:35:41', '2022-04-21 20:35:41');
INSERT INTO `oauth_access_tokens` VALUES ('bdbfff7af517748f48d72bc3fd4cbc0e36914dded23abb53745a71fef07dca3463cb8816ef194c4b', '8', '3', 'authToken', '[]', '0', '2021-05-17 18:13:41', '2021-05-17 18:13:41', '2022-05-17 15:13:41');
INSERT INTO `oauth_access_tokens` VALUES ('be61f108dd77fde6808fbb9ddd7e5039366c2798a1925b628ea295003a013bfe2199388f9ff6e3cb', '8', '3', 'authToken', '[]', '0', '2021-06-28 17:26:15', '2021-06-28 17:26:15', '2022-06-28 14:26:15');
INSERT INTO `oauth_access_tokens` VALUES ('be74ea040ddc6d55fc82e3400dc9ff7940748cbfacf97098c7651636f495a3f6df6872fb8012f1b3', '8', '3', 'authToken', '[]', '0', '2021-05-06 12:23:44', '2021-05-06 12:23:44', '2022-05-06 09:23:44');
INSERT INTO `oauth_access_tokens` VALUES ('bee59cea0b323fcaec7fb08a80c5652f5262548cb5cb7281113b07a411649fec9272dcb8b9729a44', '106', '3', 'authToken', '[]', '0', '2021-06-25 01:35:35', '2021-06-25 01:35:35', '2022-06-24 22:35:35');
INSERT INTO `oauth_access_tokens` VALUES ('bef186f21c14689d9be67f96d5ab494a1e8db36e683bb28c78e32958e471df7f73ec74db8b8d2a3b', '84', '3', 'authToken', '[]', '1', '2021-06-13 16:53:16', '2021-06-13 16:53:16', '2022-06-13 13:53:16');
INSERT INTO `oauth_access_tokens` VALUES ('bf2bac150af7ff5cf8e2058fe7627581045896716000498f1206f8ad63227081321331f0ec452f9c', '30', '3', 'authToken', '[]', '0', '2021-04-18 11:14:46', '2021-04-18 11:14:46', '2022-04-18 08:14:46');
INSERT INTO `oauth_access_tokens` VALUES ('bf5c51731eb66a48a2c1f92201304a2d4fc8e451ec2ff10dcd3cec55de72e3a8a459eca98de101ac', '107', '3', 'authToken', '[]', '1', '2021-06-25 11:13:26', '2021-06-25 11:13:26', '2022-06-25 08:13:26');
INSERT INTO `oauth_access_tokens` VALUES ('bfa367a045d54b26df41133855ad5ca0ce910fe7c16e81a93c29ff4a5610801edb60b8d53b80d4dd', '31', '3', 'authToken', '[]', '1', '2021-04-18 12:39:50', '2021-04-18 12:39:50', '2022-04-18 09:39:50');
INSERT INTO `oauth_access_tokens` VALUES ('bfcb1ec8ce7710f3c4c4fc2495fa57761480c0b5a24af50049eb6b9ca490ee4d54dd37a45cc6ac66', '73', '3', 'authToken', '[]', '0', '2021-05-30 00:47:56', '2021-05-30 00:47:56', '2022-05-29 21:47:56');
INSERT INTO `oauth_access_tokens` VALUES ('bfcc602cec83bd1be6c289c5c7835543cf2f62c82a1ab07bd9112995138be6009670e3b3ce8337e0', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:58:42', '2021-04-21 23:58:42', '2022-04-21 20:58:42');
INSERT INTO `oauth_access_tokens` VALUES ('c044bb55319ed50a634bef4684958c469e638bec957b0d794e3f22d895ed78519fdcf7eafbf47e84', '83', '3', 'authToken', '[]', '0', '2021-06-03 21:21:31', '2021-06-03 21:21:31', '2022-06-03 18:21:31');
INSERT INTO `oauth_access_tokens` VALUES ('c0f737a93568ad1d52120f1b566d441ba8293b09e6f27ea85c50760c9f329efa4b1abe5607f02342', '75', '3', 'authToken', '[]', '0', '2021-06-22 21:42:41', '2021-06-22 21:42:41', '2022-06-22 18:42:41');
INSERT INTO `oauth_access_tokens` VALUES ('c1b3800a007887d95735b5e7d6c1304798f8d2776d837419bbd1f78fb30a81dfcfceed11266dc9f8', '9', '3', 'authToken', '[]', '0', '2021-05-26 13:30:42', '2021-05-26 13:30:42', '2022-05-26 10:30:42');
INSERT INTO `oauth_access_tokens` VALUES ('c26455b7c2343ac940613c656b9aded351d08071c4f6a98307767b081620d5e9afcd7f8383866c18', '8', '3', 'authToken', '[]', '1', '2021-05-05 20:53:58', '2021-05-05 20:53:58', '2022-05-05 17:53:58');
INSERT INTO `oauth_access_tokens` VALUES ('c2dfbb209652d1f4cb0a480a5f5b4c480eb4244d4d731cd5ab79db07d8db0e59ed625a35c8a7ff1d', '92', '3', 'authToken', '[]', '1', '2021-06-21 21:28:33', '2021-06-21 21:28:33', '2022-06-21 18:28:33');
INSERT INTO `oauth_access_tokens` VALUES ('c34dc1b5939318d70bb2b093be609bfa59c813bcaa46a6a9a6e04b1cab96f04a9b1c912eee406bbb', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:40:42', '2021-04-21 23:40:42', '2022-04-21 20:40:42');
INSERT INTO `oauth_access_tokens` VALUES ('c398f0714797121d88bdb23c9053c14643089c524d83ff7d0915a0eb98495a6de25815f70b3b4fca', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:14:49', '2021-05-04 01:14:49', '2022-05-03 22:14:49');
INSERT INTO `oauth_access_tokens` VALUES ('c43d6f53e204c91e4c2a1b67c857c09c4f7014a2855e4c8011cfe42f48e2da2c47c4a98c89137ecd', '83', '3', 'authToken', '[]', '1', '2021-06-04 11:52:58', '2021-06-04 11:52:58', '2022-06-04 08:52:58');
INSERT INTO `oauth_access_tokens` VALUES ('c506c72fa351f4ffbb516c299b75dccf37ca4cb13c2094950f972fa2b90c9fce83d16644b1f8ffd8', '9', '3', 'authToken', '[]', '1', '2021-05-16 20:45:52', '2021-05-16 20:45:52', '2022-05-16 17:45:52');
INSERT INTO `oauth_access_tokens` VALUES ('c63120b1a152ed08c3322e37fa5134732a4f0cb0b4ea83f938c1c2175c79762acd0765f0d24cbec2', '6', '3', 'authToken', '[]', '1', '2021-04-22 00:04:04', '2021-04-22 00:04:04', '2022-04-21 21:04:04');
INSERT INTO `oauth_access_tokens` VALUES ('c63d587c6e981159b065d5b48ed54a162db7624ea83801dbac2f45e2eaa001d94e0197b8604e0c6d', '6', '3', 'authToken', '[]', '1', '2021-03-25 14:14:42', '2021-03-25 14:14:42', '2022-03-25 10:14:42');
INSERT INTO `oauth_access_tokens` VALUES ('c663e7f7c43771ab3e089c045249d6d9afcfaf4c79f250ef263e5a9c2840fb7e591e52f0fbf59386', '67', '3', 'authToken', '[]', '0', '2021-05-25 17:44:03', '2021-05-25 17:44:03', '2022-05-25 14:44:03');
INSERT INTO `oauth_access_tokens` VALUES ('c676beec9e795b7e6832185f9fdcacb1403ae832f53b39b9cc93fff7ba690a2f508dbd5910de2139', '8', '3', 'authToken', '[]', '0', '2021-04-12 16:46:58', '2021-04-12 16:46:58', '2022-04-12 13:46:58');
INSERT INTO `oauth_access_tokens` VALUES ('c698b01c6d35131cc11dbd172d793f1bdae5a8081eec2d74239b27cf472d8d6495d0adcf39849909', '107', '3', 'authToken', '[]', '0', '2021-06-25 10:39:46', '2021-06-25 10:39:46', '2022-06-25 07:39:46');
INSERT INTO `oauth_access_tokens` VALUES ('c6f6f98c001bcb2072108e253d4998417e666230e87419dea2a2bff17d1ed08b561eec4f2a295ea3', '61', '3', 'authToken', '[]', '0', '2021-05-02 14:00:27', '2021-05-02 14:00:27', '2022-05-02 11:00:27');
INSERT INTO `oauth_access_tokens` VALUES ('c6fea19a12c6b6e5b3f2b1769e5eec248e272ca1a927fb637ad673a5b61ba8b362ae088d987571c0', '8', '3', 'authToken', '[]', '0', '2021-05-04 01:12:20', '2021-05-04 01:12:20', '2022-05-03 22:12:20');
INSERT INTO `oauth_access_tokens` VALUES ('c7573e9ea3dc7915c879ba239cf3327db6caaec71b7c60d5a6de68a5c4cdd165810ae21bf182900c', '6', '3', 'authToken', '[]', '0', '2021-05-19 20:16:00', '2021-05-19 20:16:00', '2022-05-19 17:16:00');
INSERT INTO `oauth_access_tokens` VALUES ('c7816f3abd08b24ada853d68cf8a9b0a74976ed1f9c8617599aed36569135356594b58f81b431618', '66', '3', 'authToken', '[]', '1', '2021-06-21 19:11:32', '2021-06-21 19:11:32', '2022-06-21 16:11:32');
INSERT INTO `oauth_access_tokens` VALUES ('c7a061ffed186d598db47bd35fd5eb0975e776c939f505dd9c72ceef06ee144f5835fac7c9975045', '9', '3', 'authToken', '[]', '0', '2021-05-26 13:35:54', '2021-05-26 13:35:54', '2022-05-26 10:35:54');
INSERT INTO `oauth_access_tokens` VALUES ('c890b8fecacff6007165e13dcc8bb1a04b6b255919da5d1614468cbb578952ec86b91aa2998415a4', '107', '3', 'authToken', '[]', '1', '2021-06-25 10:37:53', '2021-06-25 10:37:53', '2022-06-25 07:37:53');
INSERT INTO `oauth_access_tokens` VALUES ('c8a9374d4c6e912991b7a00dcd1d0ca49272bce65f17523516f1abffb233a48f1edf093a9e1067c4', '8', '3', 'authToken', '[]', '1', '2021-04-18 18:09:29', '2021-04-18 18:09:29', '2022-04-18 15:09:29');
INSERT INTO `oauth_access_tokens` VALUES ('c931662f00cd8b254acf093440f66660be28694d50ec25a9bc48d64f3b69fed4bf79ae26252cc26c', '66', '3', 'authToken', '[]', '1', '2021-06-21 19:34:09', '2021-06-21 19:34:09', '2022-06-21 16:34:09');
INSERT INTO `oauth_access_tokens` VALUES ('c97cf1eeb9445799a65324dc94143612f9b164e7bce191ab81325b86e3046d4f9b59084d7b4d3a4b', '8', '3', 'authToken', '[]', '0', '2021-05-05 01:50:40', '2021-05-05 01:50:40', '2022-05-04 22:50:40');
INSERT INTO `oauth_access_tokens` VALUES ('c9b0edb6b31dbaa97f2a2bbfa3b4fb800a02555b9bab6ca4cf0be61649fc0a8904e2beb0a05f98df', '8', '3', 'authToken', '[]', '0', '2021-06-12 15:48:15', '2021-06-12 15:48:15', '2022-06-12 12:48:15');
INSERT INTO `oauth_access_tokens` VALUES ('ca2c6cc9792ee31f1f8782e415adeaa80695cc5fd2f4090675ebeac4df5581f0773a6bdaa3fdfa20', '88', '3', 'authToken', '[]', '1', '2021-06-21 18:28:58', '2021-06-21 18:28:58', '2022-06-21 15:28:58');
INSERT INTO `oauth_access_tokens` VALUES ('ca34e31738bf0a0b9cc2233c4d709d2c895fe3d033fa783583f0ca66d112d0f9e99e27b69e4a4f73', '8', '3', 'authToken', '[]', '0', '2021-05-05 23:05:56', '2021-05-05 23:05:56', '2022-05-05 20:05:56');
INSERT INTO `oauth_access_tokens` VALUES ('ca5f2247d97a89eb2935861e0c9a7e695e7d84268dcf1162f1452c5a077d56fb3e8fbef89bd22667', '70', '3', 'authToken', '[]', '0', '2021-06-06 03:32:42', '2021-06-06 03:32:42', '2022-06-06 00:32:42');
INSERT INTO `oauth_access_tokens` VALUES ('cab0649ec01159e4d66074aaf6ac8dd99ca5c2c8355fc6caf38d48474311bb0601b7bcd64d045c48', '8', '3', 'authToken', '[]', '0', '2021-04-12 16:47:18', '2021-04-12 16:47:18', '2022-04-12 13:47:18');
INSERT INTO `oauth_access_tokens` VALUES ('cabd6fe9392e8d818a4c93b3ae2f9946e4d9ebbdee140ded52f330bddd1eadb21370fc442159aed7', '7', '3', 'authToken', '[]', '0', '2021-03-30 14:26:09', '2021-03-30 14:26:09', '2022-03-30 11:26:09');
INSERT INTO `oauth_access_tokens` VALUES ('cb31618b9cc85f1f61c38c40830c77cc40c37af37920d2f3d8b1ae5c8959645161fa2dc7837875f4', '8', '3', 'authToken', '[]', '0', '2021-04-18 12:59:46', '2021-04-18 12:59:46', '2022-04-18 09:59:46');
INSERT INTO `oauth_access_tokens` VALUES ('cb5615a9d6bc99dd7f297fcf4ee069f52eba62f0d21eefd398d171047999d82e677d2cf009f1b44b', '8', '3', 'authToken', '[]', '0', '2021-05-25 11:15:25', '2021-05-25 11:15:25', '2022-05-25 08:15:25');
INSERT INTO `oauth_access_tokens` VALUES ('cbd56efbba3c63d617cf38b13cb9a26d00c011b1c96c8cc862e6c25e96a0821ae96a35a63c6a1f60', '6', '3', 'authToken', '[]', '1', '2021-04-21 13:33:13', '2021-04-21 13:33:13', '2022-04-21 10:33:13');
INSERT INTO `oauth_access_tokens` VALUES ('cc671241c8e98ce3c161be63acb935679e94414141cdd2df0d6e59e03d63cc33d59b14a64858c5f5', '8', '3', 'authToken', '[]', '0', '2021-05-20 13:10:21', '2021-05-20 13:10:21', '2022-05-20 10:10:21');
INSERT INTO `oauth_access_tokens` VALUES ('ccb447de37b2c0f35ee6c5932daffe5eabea9ceb4d7586157944e67cd95d2d600e591585d33d11d3', '65', '3', 'authToken', '[]', '1', '2021-05-06 01:56:59', '2021-05-06 01:56:59', '2022-05-05 22:56:59');
INSERT INTO `oauth_access_tokens` VALUES ('cef6f850e2691d629e3e1ec574cabfad61a84a21f9ceee60bfbaafa92968b4ce0893c44fc73f8380', '108', '3', 'authToken', '[]', '1', '2021-06-25 10:38:55', '2021-06-25 10:38:55', '2022-06-25 07:38:55');
INSERT INTO `oauth_access_tokens` VALUES ('cf72c91637665d17f1f9ab6b5a5a345f43855fb3d43a6728dbaa9e24b6fde9285751eb8a1b09ce4e', '28', '3', 'authToken', '[]', '0', '2021-06-02 21:14:24', '2021-06-02 21:14:24', '2022-06-02 18:14:24');
INSERT INTO `oauth_access_tokens` VALUES ('cf9f01ea202d75cbfd326440197cf7069e1dc68849caeafcc09bd6e0d621d11a4440eb73889859af', '83', '3', 'authToken', '[]', '1', '2021-06-19 13:35:44', '2021-06-19 13:35:44', '2022-06-19 10:35:44');
INSERT INTO `oauth_access_tokens` VALUES ('d0323abc06605c8ad1199fd05dbac6b1b197ad9bd37bb277255d8c21632683d1be5f774a1e10e82d', '104', '3', 'authToken', '[]', '1', '2021-06-24 17:51:38', '2021-06-24 17:51:38', '2022-06-24 14:51:38');
INSERT INTO `oauth_access_tokens` VALUES ('d0848e07444209a3dfde8a2d458ee80b560e8080a588eca2355d8eb17064e94963afdf23fbbc41a9', '6', '3', 'authToken', '[]', '0', '2021-04-05 08:03:58', '2021-04-05 08:03:58', '2022-04-05 05:03:58');
INSERT INTO `oauth_access_tokens` VALUES ('d08bcf6e2a09f1b8bde940ff1c4a0033de1cf5b05bfdb7e30de2b01b398ea6a2ceda3d6226243578', '66', '3', 'authToken', '[]', '1', '2021-05-06 15:45:16', '2021-05-06 15:45:16', '2022-05-06 12:45:16');
INSERT INTO `oauth_access_tokens` VALUES ('d1232c0ebaabf07a423edbaf424c7e852b9f8cbcd4d5f4fe3accc51b66503ab1f3eb436be76b8f84', '69', '3', 'authToken', '[]', '1', '2021-05-17 03:29:05', '2021-05-17 03:29:05', '2022-05-17 00:29:05');
INSERT INTO `oauth_access_tokens` VALUES ('d1732202b323d540516c7c276ebfbbc86a227661d85f0db715b70b3349a3e315ee90ef2e8bea310f', '8', '3', 'authToken', '[]', '0', '2021-06-09 22:33:46', '2021-06-09 22:33:46', '2022-06-09 19:33:46');
INSERT INTO `oauth_access_tokens` VALUES ('d1d15aca5e26538e16e31d029d5238e22860b60a24c1624f074472d6712571e76310d5df7b13bbc0', '146', '3', 'authToken', '[]', '0', '2021-07-06 15:40:45', '2021-07-06 15:40:45', '2022-07-06 12:40:45');
INSERT INTO `oauth_access_tokens` VALUES ('d2dd9bf5b66aa84ab1d0b29aab3909145897176b63fe60b8765b34da7d6241c22120ffc5aa6499fb', '110', '3', 'authToken', '[]', '0', '2021-06-28 21:33:44', '2021-06-28 21:33:44', '2022-06-28 18:33:44');
INSERT INTO `oauth_access_tokens` VALUES ('d37a9a5fc8399c3f684567e9df239f5c5145c2e738a2776f3bee1e06db78a038c07c991654e64707', '99', '3', 'authToken', '[]', '1', '2021-06-24 01:57:22', '2021-06-24 01:57:22', '2022-06-23 22:57:22');
INSERT INTO `oauth_access_tokens` VALUES ('d3c644a98c6e5e361441e56ad5dcb25e88fc3dd27b667c3734a00eff08edde3113c212c9f5549998', '9', '3', 'authToken', '[]', '0', '2021-05-05 23:23:02', '2021-05-05 23:23:02', '2022-05-05 20:23:02');
INSERT INTO `oauth_access_tokens` VALUES ('d3e700f7e14e4232765d0a5fd8d5426357144a3c43e7bc8eb470fd4f679e7866d0f7e252b2d414a9', '83', '3', 'authToken', '[]', '1', '2021-06-03 03:02:23', '2021-06-03 03:02:23', '2022-06-03 00:02:23');
INSERT INTO `oauth_access_tokens` VALUES ('d3efcf9bf75969a8f9b3f52235f9fca7d07b5ca64f11294a25edd0c98e18a8c385212b60427eb828', '78', '3', 'authToken', '[]', '0', '2021-06-24 02:36:08', '2021-06-24 02:36:08', '2022-06-23 23:36:08');
INSERT INTO `oauth_access_tokens` VALUES ('d4213fe0e5b1e75ca81e502ea808ed9bc3788d594abf395d1eef28413cad2fd5b4eaa8032e9992b9', '45', '3', 'authToken', '[]', '0', '2021-04-28 02:42:13', '2021-04-28 02:42:13', '2022-04-27 23:42:13');
INSERT INTO `oauth_access_tokens` VALUES ('d428c501d20ea9f8fdec940703ba4b9bdb1a080c799adfa8a1fb8d8d96350c0fb5546e2e773bdedc', '33', '3', 'authToken', '[]', '1', '2021-04-18 12:49:15', '2021-04-18 12:49:15', '2022-04-18 09:49:15');
INSERT INTO `oauth_access_tokens` VALUES ('d4327c0be4b54400e4953ecc64cc6429ba7a6050eb229fb06d327d150317966b492155f4d8d53926', '75', '3', 'authToken', '[]', '0', '2021-05-25 11:55:43', '2021-05-25 11:55:43', '2022-05-25 08:55:43');
INSERT INTO `oauth_access_tokens` VALUES ('d45e618a92070271af1879823494c9fb0dd47d1a988ad305e42d4f8a625233865855ed080af93084', '94', '3', 'authToken', '[]', '0', '2021-06-23 00:21:59', '2021-06-23 00:21:59', '2022-06-22 21:21:59');
INSERT INTO `oauth_access_tokens` VALUES ('d46d4f1be5d310c4ecc7dcb2743adcf4bb203e9a169b96254ef8b7d92b22c9310098184bb8e7aa51', '83', '3', 'authToken', '[]', '1', '2021-06-23 21:01:34', '2021-06-23 21:01:34', '2022-06-23 18:01:34');
INSERT INTO `oauth_access_tokens` VALUES ('d4872a1a3df744d62218d88a2be9a28c55fe65517bf508f783577a115b25bbb8b8401c1a86012958', '148', '3', 'authToken', '[]', '0', '2021-07-06 15:50:38', '2021-07-06 15:50:38', '2022-07-06 12:50:38');
INSERT INTO `oauth_access_tokens` VALUES ('d4b349c5fec71f4c5942596eb4a9e9904e63f1e0ffdeba500531d400e48cff7ec91652a85f10f67f', '61', '3', 'authToken', '[]', '0', '2021-05-02 17:19:49', '2021-05-02 17:19:49', '2022-05-02 14:19:49');
INSERT INTO `oauth_access_tokens` VALUES ('d4b9587f53b183cbcad90cf3c0cad79dccb6d595a67730d620af26c5ab296e6f092e58d497e8337d', '67', '3', 'authToken', '[]', '0', '2021-05-26 00:53:14', '2021-05-26 00:53:14', '2022-05-25 21:53:14');
INSERT INTO `oauth_access_tokens` VALUES ('d537eb0a35a0ac476ab9c5dea2f610cd44e4a62ec744da6aaff002bfa09d630edfd6b1ee6a57079d', '26', '3', 'authToken', '[]', '1', '2021-04-17 14:34:44', '2021-04-17 14:34:44', '2022-04-17 11:34:44');
INSERT INTO `oauth_access_tokens` VALUES ('d58b235b8c3d24f3cfb08252de660591efb6baf78301d91497dcc888a9f3fa73fa213a48f4c246fd', '8', '3', 'authToken', '[]', '0', '2021-04-15 18:37:09', '2021-04-15 18:37:09', '2022-04-15 15:37:09');
INSERT INTO `oauth_access_tokens` VALUES ('d5e1cc44bdbfa09fd25442e85fe0e3ac93fcc09e7cdf3d67ecd6e2967b5d7a72973b99fc631d01be', '78', '3', 'authToken', '[]', '0', '2021-05-26 17:47:59', '2021-05-26 17:47:59', '2022-05-26 14:47:59');
INSERT INTO `oauth_access_tokens` VALUES ('d620a3f368253c8e099e7d1199290d2140e62d2181939980691f8384000e6c773446403cd7dc6cb0', '8', '3', 'authToken', '[]', '1', '2021-05-19 22:27:35', '2021-05-19 22:27:35', '2022-05-19 19:27:35');
INSERT INTO `oauth_access_tokens` VALUES ('d6de3a038e7abd5861cc40938b3e2221ed249c8c6989a9679ccae0b811fe9cf6294b4ecae7ed48a6', '9', '3', 'authToken', '[]', '0', '2021-04-16 13:46:00', '2021-04-16 13:46:00', '2022-04-16 10:46:00');
INSERT INTO `oauth_access_tokens` VALUES ('d7017ade83d2b474c7368b1e6f8b842482f4dfc0e67e0fa9e7eed5bdde8c6709e57b12e8490cbe00', '62', '3', 'authToken', '[]', '1', '2021-05-03 00:29:00', '2021-05-03 00:29:00', '2022-05-02 21:29:00');
INSERT INTO `oauth_access_tokens` VALUES ('d7399c3d2b3077c992958b626253f91326d36793f7883062b0ee8a955573dfe1c3db46ccb4788842', '145', '3', 'authToken', '[]', '0', '2021-07-06 15:36:21', '2021-07-06 15:36:21', '2022-07-06 12:36:21');
INSERT INTO `oauth_access_tokens` VALUES ('d79d8885352612d09afb287553500115a265ee7933dfcd626dfe104a699698c9684732960ceb80da', '83', '3', 'authToken', '[]', '0', '2021-06-13 02:23:07', '2021-06-13 02:23:07', '2022-06-12 23:23:07');
INSERT INTO `oauth_access_tokens` VALUES ('d94a6e2a0083ff8491e7eba9ed38a0b6ac4e2ad648985d40a96aae738dedd760c3a582b5473a3fde', '73', '3', 'authToken', '[]', '1', '2021-06-01 13:33:25', '2021-06-01 13:33:25', '2022-06-01 10:33:25');
INSERT INTO `oauth_access_tokens` VALUES ('d9560d866c4e73298d1573b9d8fc07809d8aed7fe373b89fa3f7fcb04c089241edf21da91119f9b1', '78', '3', 'authToken', '[]', '0', '2021-06-24 02:37:45', '2021-06-24 02:37:45', '2022-06-23 23:37:45');
INSERT INTO `oauth_access_tokens` VALUES ('d958aba5f32c04cbf5278dec0c4c43c9ad9650af3a4a1810c3b61c82f96b7b2952f833d22d52ecef', '110', '3', 'authToken', '[]', '1', '2021-06-28 22:04:55', '2021-06-28 22:04:55', '2022-06-28 19:04:55');
INSERT INTO `oauth_access_tokens` VALUES ('d9b1fa105704195a6be70cab1e8205d37fe90a72be306d0c5f44c3377f9874c6bf5276edd91957c2', '48', '3', 'authToken', '[]', '1', '2021-04-22 00:55:33', '2021-04-22 00:55:33', '2022-04-21 21:55:33');
INSERT INTO `oauth_access_tokens` VALUES ('da2f1859b929932d4b8c412488447bbd2c3ac1b80de086fd67dd5963d28b883bd18918c9dc8ae232', '8', '3', 'authToken', '[]', '0', '2021-06-19 00:22:03', '2021-06-19 00:22:03', '2022-06-18 21:22:03');
INSERT INTO `oauth_access_tokens` VALUES ('db12aec3fce3943f24ba3aef5187c67daaca091854a52e3d0376701a34ef31a9548933471e26df3e', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:05:20', '2021-04-21 23:05:20', '2022-04-21 20:05:20');
INSERT INTO `oauth_access_tokens` VALUES ('db2d24456921045d4e467e8539607d244337098c749b37e2d39540ca3f96d681b2054ad83ea7b891', '6', '3', 'authToken', '[]', '1', '2021-04-22 18:44:46', '2021-04-22 18:44:46', '2022-04-22 15:44:46');
INSERT INTO `oauth_access_tokens` VALUES ('db37b1a4e7bace80f1526e18efd7951a15305aa8b8bdbd4db1ff22c9b44f56828c04e3737afa435e', '83', '3', 'authToken', '[]', '1', '2021-06-24 02:56:16', '2021-06-24 02:56:16', '2022-06-23 23:56:16');
INSERT INTO `oauth_access_tokens` VALUES ('db532f5b1f71cadd627ccf5cb5aa3a84de56123529f1f5ab44da12a3eeb45f6c49a233c7befe5732', '8', '3', 'authToken', '[]', '0', '2021-05-04 21:59:48', '2021-05-04 21:59:48', '2022-05-04 18:59:48');
INSERT INTO `oauth_access_tokens` VALUES ('dc0a85d20798fee6b46372eb13ed14eeff37ade678390eaa7a2115e4b2e56f84150e62f56d046ca2', '8', '3', 'authToken', '[]', '0', '2021-05-05 01:21:12', '2021-05-05 01:21:12', '2022-05-04 22:21:12');
INSERT INTO `oauth_access_tokens` VALUES ('dc7e36c2abc03701f0814704793a269c7d118d5e5d855b5dd9d2091f16db49a9d42195ce1392dcc1', '67', '3', 'authToken', '[]', '0', '2021-05-25 17:58:30', '2021-05-25 17:58:30', '2022-05-25 14:58:30');
INSERT INTO `oauth_access_tokens` VALUES ('dcd05fe2ded71431b84cd9d2f1fa1a4ae35958ceac2b9763aaec9512cd2cacc4396ed77d77bab645', '102', '3', 'authToken', '[]', '1', '2021-06-24 16:07:50', '2021-06-24 16:07:50', '2022-06-24 13:07:50');
INSERT INTO `oauth_access_tokens` VALUES ('dda4be0ad7682d14545b3759309e8e3c2fdafedd1cd3800a9bcdb66cd09fedd8b0a3ea1be20fa098', '102', '3', 'authToken', '[]', '0', '2021-07-05 09:49:19', '2021-07-05 09:49:19', '2022-07-05 06:49:19');
INSERT INTO `oauth_access_tokens` VALUES ('ddb5d1d8990f8f75e71d995d8f0c8caae035414a1eedaf6a812d2180df25c95cb8d0f9faf2db4457', '103', '3', 'authToken', '[]', '1', '2021-06-25 10:38:38', '2021-06-25 10:38:38', '2022-06-25 07:38:38');
INSERT INTO `oauth_access_tokens` VALUES ('ddbc61b03e4db03991538c2a63eb28e20d0fc4e82e2c5a8e3d8cc0f94f337ce8baae43938e1b9015', '6', '3', 'authToken', '[]', '0', '2021-04-14 14:40:06', '2021-04-14 14:40:06', '2022-04-14 11:40:06');
INSERT INTO `oauth_access_tokens` VALUES ('ddf601fcf62b7960889b71d0d62674c37e8de2a70648910eb2222fa646227a292f3eb2a8501ea458', '61', '3', 'authToken', '[]', '0', '2021-05-02 15:13:23', '2021-05-02 15:13:23', '2022-05-02 12:13:23');
INSERT INTO `oauth_access_tokens` VALUES ('def8fdbf7f2fadfef30094e5e0643752bbc1a847a074daa89989ee0502ec9a8a3542c63017189bac', '15', '3', 'authToken', '[]', '0', '2021-05-24 19:04:08', '2021-05-24 19:04:08', '2022-05-24 16:04:08');
INSERT INTO `oauth_access_tokens` VALUES ('df2d4f726e3980e68e8c3bf59861d82e8d7fe0e6b2542fcf313c3c04d6f6ecbe7923a4ac88d37bb5', '8', '3', 'authToken', '[]', '1', '2021-05-06 20:35:30', '2021-05-06 20:35:30', '2022-05-06 17:35:30');
INSERT INTO `oauth_access_tokens` VALUES ('df3c19800f8334efc1555df5ad348922b4074ea4407542c2781fbcc04f232a69716952d632128ece', '6', '3', 'authToken', '[]', '0', '2021-04-21 13:11:00', '2021-04-21 13:11:00', '2022-04-21 10:11:00');
INSERT INTO `oauth_access_tokens` VALUES ('dfe55538e2027c726720347066980b4ee2dbbdf5e50608b8ce7e758ceab5d3d9b4d2fb92e23ad852', '9', '3', 'authToken', '[]', '0', '2021-04-14 06:03:48', '2021-04-14 06:03:48', '2022-04-14 03:03:48');
INSERT INTO `oauth_access_tokens` VALUES ('e030d4f4978985127f386559a95894f8e9697d0b8fec492d9a8b39acb2cfb55eca7d799d0d6d14fc', '107', '3', 'authToken', '[]', '1', '2021-06-25 10:35:23', '2021-06-25 10:35:23', '2022-06-25 07:35:23');
INSERT INTO `oauth_access_tokens` VALUES ('e063f03299dcba98ff8f74c28e1e68baa490ac8137cf6db410e1d18687c2261d73d25ae8a46b8813', '27', '3', 'authToken', '[]', '0', '2021-04-17 14:45:21', '2021-04-17 14:45:21', '2022-04-17 11:45:21');
INSERT INTO `oauth_access_tokens` VALUES ('e09815b27fb908835b12c27fe668c5be3e59d594be95837e9edfb7a48f4458af5553fa538d20dc3d', '6', '3', 'authToken', '[]', '0', '2021-04-14 15:46:45', '2021-04-14 15:46:45', '2022-04-14 12:46:45');
INSERT INTO `oauth_access_tokens` VALUES ('e10224449dc4849bd8cc5b0494fecae6046cb235a7b41cbe2d21f2f624e53a84e0f9df2286104892', '8', '3', 'authToken', '[]', '0', '2021-05-20 12:10:00', '2021-05-20 12:10:00', '2022-05-20 09:10:00');
INSERT INTO `oauth_access_tokens` VALUES ('e1ed74c5f7dd4f437c15681cdaac9f7b21251fe55e8d73d7377380fecf60500bf1f27fe693bb91a5', '8', '3', 'authToken', '[]', '0', '2021-05-05 18:56:12', '2021-05-05 18:56:12', '2022-05-05 15:56:12');
INSERT INTO `oauth_access_tokens` VALUES ('e244f2620914281fad4f6b91b3ed9607c74f037063422eb8cb0ce408d219a66939e27fad02e95c86', '28', '3', 'authToken', '[]', '0', '2021-05-26 17:48:41', '2021-05-26 17:48:41', '2022-05-26 14:48:41');
INSERT INTO `oauth_access_tokens` VALUES ('e28a6dd899be1d8d4ce1c4cd22912bb20e1e0953e026faf74b5d37b4439527b1d7d8af09140d97f2', '32', '3', 'authToken', '[]', '1', '2021-04-18 12:45:20', '2021-04-18 12:45:20', '2022-04-18 09:45:20');
INSERT INTO `oauth_access_tokens` VALUES ('e28aa911f00163c3bd01f696e525d021723ee801b8cf82e99fb5ff4775c1d60a6566daf258c864d1', '6', '3', 'authToken', '[]', '1', '2021-04-25 10:22:25', '2021-04-25 10:22:25', '2022-04-25 07:22:25');
INSERT INTO `oauth_access_tokens` VALUES ('e2f0f0c0193c681629687e591a6c8d5b12773f6541ae51005ff5a3234416fb3bda4db9fc89ef5567', '65', '3', 'authToken', '[]', '1', '2021-05-06 01:57:30', '2021-05-06 01:57:30', '2022-05-05 22:57:30');
INSERT INTO `oauth_access_tokens` VALUES ('e3626bd5e56c9ec675912c2dcbec118ae7580c36833d8a4ccb999b984c0b40e3ae1fe34482aeacff', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:54:43', '2021-04-19 01:54:43', '2022-04-18 22:54:43');
INSERT INTO `oauth_access_tokens` VALUES ('e397ee6cfe0e8e496dd1665ea68a70c4f0995028d7d159249a07b77f6f61f6b62c91d9915005fd10', '8', '3', 'authToken', '[]', '1', '2021-06-25 11:18:48', '2021-06-25 11:18:48', '2022-06-25 08:18:48');
INSERT INTO `oauth_access_tokens` VALUES ('e3e87022ae174689ecc02c5d557ae8d5cd5f42d8d7c1df7b8dbc23bc6d6aea09f72660217817c16b', '8', '3', 'authToken', '[]', '0', '2021-06-27 08:35:59', '2021-06-27 08:35:59', '2022-06-27 05:35:59');
INSERT INTO `oauth_access_tokens` VALUES ('e42745426bc99106647f0efaeb3c56197288ff970ed5adb5962bccac09e903eca27478780934a324', '76', '3', 'authToken', '[]', '1', '2021-05-25 11:16:09', '2021-05-25 11:16:09', '2022-05-25 08:16:09');
INSERT INTO `oauth_access_tokens` VALUES ('e479353210b815ab579004ac32d21e7be1e3aa6e8155ad1f3865268cebcc541ea8bf03ac2ba08451', '67', '3', 'authToken', '[]', '0', '2021-05-24 19:28:03', '2021-05-24 19:28:03', '2022-05-24 16:28:03');
INSERT INTO `oauth_access_tokens` VALUES ('e49249718b6f096b7ee72eb51f936cfd512c315cf4f16dd1288c02ba1fadbd46c24a58adb9dc4f9b', '45', '3', 'authToken', '[]', '0', '2021-05-25 13:55:10', '2021-05-25 13:55:10', '2022-05-25 10:55:10');
INSERT INTO `oauth_access_tokens` VALUES ('e4b08937c6c81b2d61761ec0d71f9ef36dc4497003a6d488a1ecb66c94da0c9f2d920d7227a4f007', '117', '3', 'authToken', '[]', '0', '2021-06-29 18:09:08', '2021-06-29 18:09:08', '2022-06-29 15:09:08');
INSERT INTO `oauth_access_tokens` VALUES ('e4e10161baf616b8ea611b65cd270b1bb915788434cbed46dc15cd9c2e8b7e56003658abbdd9f4a5', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:22:20', '2021-06-23 20:22:20', '2022-06-23 17:22:20');
INSERT INTO `oauth_access_tokens` VALUES ('e4f34b8328cc40e654726c6b4b9457610ce36b8463ab009ede25e837a707a29ace5a3187d5f6bc3c', '36', '3', 'authToken', '[]', '1', '2021-04-18 12:57:12', '2021-04-18 12:57:12', '2022-04-18 09:57:12');
INSERT INTO `oauth_access_tokens` VALUES ('e5b54d52b4254a114cf7197a97fa3d57d21533001397aa8cd6c4361413095dc47530077c66a89398', '102', '3', 'authToken', '[]', '1', '2021-06-24 13:45:48', '2021-06-24 13:45:48', '2022-06-24 10:45:48');
INSERT INTO `oauth_access_tokens` VALUES ('e60015436853587a2784696acb1ebf621493848c6664cfca0bca52e711b72506645e392f4c8443df', '73', '3', 'authToken', '[]', '1', '2021-05-26 14:02:04', '2021-05-26 14:02:04', '2022-05-26 11:02:04');
INSERT INTO `oauth_access_tokens` VALUES ('e70e3f958e358a452d17ec57abb37342b1704eea278355a6eaaf7d202b086655d29cec1fdfccb45f', '66', '3', 'authToken', '[]', '1', '2021-06-21 18:42:41', '2021-06-21 18:42:41', '2022-06-21 15:42:41');
INSERT INTO `oauth_access_tokens` VALUES ('e730dd964c33f20516c1a5659782f431a0d3338bb8eaec25453d353fa8376b474c79b29e262ffe12', '63', '3', 'authToken', '[]', '1', '2021-05-03 00:20:05', '2021-05-03 00:20:05', '2022-05-02 21:20:05');
INSERT INTO `oauth_access_tokens` VALUES ('e74f2ea5669e914efebf9d9bea96e8b08d79e94e7303e481a84e92cd8c7668f26e12b3c0ca67411c', '67', '3', 'authToken', '[]', '1', '2021-05-06 20:42:48', '2021-05-06 20:42:48', '2022-05-06 17:42:48');
INSERT INTO `oauth_access_tokens` VALUES ('e7dd5f1fbccdf2ba15c4c0f4625b58f0888362aa8226bfdb96ea0ef6ad90b9f5ca57fc62ba983225', '74', '3', 'authToken', '[]', '0', '2021-05-21 09:14:44', '2021-05-21 09:14:44', '2022-05-21 06:14:44');
INSERT INTO `oauth_access_tokens` VALUES ('e7e840c8452052cf1d92d6e220409cb3f2270bf912d6b7904d21e28ce928be546939d6546bb5959c', '106', '3', 'authToken', '[]', '0', '2021-06-25 19:23:43', '2021-06-25 19:23:43', '2022-06-25 16:23:43');
INSERT INTO `oauth_access_tokens` VALUES ('e8680e363972c7809e3208af50ac7553a0135b90f2d7c088bbaa721167d0366261ae7dee42a62093', '6', '3', 'authToken', '[]', '1', '2021-04-23 22:03:47', '2021-04-23 22:03:47', '2022-04-23 19:03:47');
INSERT INTO `oauth_access_tokens` VALUES ('e8829bee82f4850ad644a90f2713a66023b4c422f1966dc86adea7398b0534fae5188d341a30fa6a', '89', '3', 'authToken', '[]', '1', '2021-06-21 19:29:50', '2021-06-21 19:29:50', '2022-06-21 16:29:50');
INSERT INTO `oauth_access_tokens` VALUES ('e8a2c44c2eea6f51f74fc96f145bffddb8cfaf7bdb2d72ca9ae7ba144a329b27a10dd8aed283d7a7', '109', '3', 'authToken', '[]', '0', '2021-06-25 16:21:48', '2021-06-25 16:21:48', '2022-06-25 13:21:48');
INSERT INTO `oauth_access_tokens` VALUES ('e917f64c97cbb08e6f9a37d6f38d6f4829d63500ac14fd4d9102edff929c56f072b92a7cbc502fa8', '9', '3', 'authToken', '[]', '1', '2021-04-16 18:00:25', '2021-04-16 18:00:25', '2022-04-16 15:00:25');
INSERT INTO `oauth_access_tokens` VALUES ('e9c0b18da0925b60a55480a6dbb2bd1648bd00d88a9edb8b2f87440d1e2d44735d99d4c33ad0b1b5', '116', '3', 'authToken', '[]', '1', '2021-06-29 17:20:11', '2021-06-29 17:20:11', '2022-06-29 14:20:11');
INSERT INTO `oauth_access_tokens` VALUES ('e9c0ec5ffa708836e5a4b336a4ff1a16b8d627d28464ff75b8e994a5070f4b7b601b480f8b682fa2', '100', '3', 'authToken', '[]', '1', '2021-06-24 02:02:16', '2021-06-24 02:02:16', '2022-06-23 23:02:16');
INSERT INTO `oauth_access_tokens` VALUES ('e9d67fd81bee9fb2f32305b90cbefcd8fdf9c0ce752ddfb5d2528faec04f1b8fde36a5c8def90193', '132', '3', 'authToken', '[]', '0', '2021-07-03 20:43:23', '2021-07-03 20:43:23', '2022-07-03 17:43:23');
INSERT INTO `oauth_access_tokens` VALUES ('ea096a6b2ee758ca099ec851480b80195c317428144d20827c69a1a16b7e1d7637dca180cd8bca37', '73', '3', 'authToken', '[]', '1', '2021-06-03 02:54:58', '2021-06-03 02:54:58', '2022-06-02 23:54:58');
INSERT INTO `oauth_access_tokens` VALUES ('eae4f2736cca85962004e54c342a61eb8b0b1add745ccac665da457b2c13dcfb41ffac9237a11a23', '83', '3', 'authToken', '[]', '1', '2021-06-23 20:34:13', '2021-06-23 20:34:13', '2022-06-23 17:34:13');
INSERT INTO `oauth_access_tokens` VALUES ('eaf7e31875600435fc6ed544a9aedb585ef78c8db460b965cdb25d05e356d50afde03d33fc01e41d', '8', '3', 'authToken', '[]', '1', '2021-04-30 20:07:35', '2021-04-30 20:07:35', '2022-04-30 17:07:35');
INSERT INTO `oauth_access_tokens` VALUES ('eb409bad59b637d395b8d66d7fd74608d57e3cfe42585bbbf37c0ce4cb10a9276250d0f9e0b2f7fe', '9', '3', 'authToken', '[]', '1', '2021-05-25 10:54:52', '2021-05-25 10:54:52', '2022-05-25 07:54:52');
INSERT INTO `oauth_access_tokens` VALUES ('ebe6f57a7ef824d0fae7f9a1156f6e032b9fe2122c50e3e8c0ae1ceaaa9be2c93e198b726ac3f560', '93', '3', 'authToken', '[]', '1', '2021-06-22 18:21:15', '2021-06-22 18:21:15', '2022-06-22 15:21:15');
INSERT INTO `oauth_access_tokens` VALUES ('ec764f2192184b2cc03e8a0d5f7e98e8ef5c79942b909d66c66c3b5770c933e0b4b981a6898ccb20', '123', '3', 'authToken', '[]', '1', '2021-06-30 20:39:25', '2021-06-30 20:39:25', '2022-06-30 17:39:25');
INSERT INTO `oauth_access_tokens` VALUES ('ec7dfdeb22f81395ecf4422a6dcc6a3dd5fa078476571d3c8436af686d26aa94bb286cd567f10147', '6', '3', 'authToken', '[]', '1', '2021-04-21 23:09:26', '2021-04-21 23:09:26', '2022-04-21 20:09:26');
INSERT INTO `oauth_access_tokens` VALUES ('ecaa486781180d60f946388f8262a8d6df9675087c34ec4640fa112df960e8785bf920aa2b52c42c', '85', '3', 'authToken', '[]', '1', '2021-06-13 17:49:16', '2021-06-13 17:49:16', '2022-06-13 14:49:16');
INSERT INTO `oauth_access_tokens` VALUES ('ed2839ea2cd8f5a6b861cf299ca1ee99e82c915095379d702544a7e919f6af0a859c30e69f173e13', '79', '3', 'authToken', '[]', '1', '2021-06-02 19:41:04', '2021-06-02 19:41:04', '2022-06-02 16:41:04');
INSERT INTO `oauth_access_tokens` VALUES ('ed44cd1e8781c109f76b353d444bf9b23296dbdbf4dd1f54fdc424221f90e66faa71b6929b8240b8', '9', '3', 'authToken', '[]', '1', '2021-04-16 12:10:27', '2021-04-16 12:10:27', '2022-04-16 09:10:27');
INSERT INTO `oauth_access_tokens` VALUES ('ed5a35e5f2022e451baf6590293f326ab51d6e44620b859d7839ff72a518718f7874efcb7940d9ae', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:50:37', '2021-04-19 01:50:37', '2022-04-18 22:50:37');
INSERT INTO `oauth_access_tokens` VALUES ('ed8c75ca44f82f0997793cbaeb9df60c4cddd3881d72810a343941368946b0a1fe955f549c7d288a', '8', '3', 'authToken', '[]', '0', '2021-05-05 01:27:45', '2021-05-05 01:27:45', '2022-05-04 22:27:45');
INSERT INTO `oauth_access_tokens` VALUES ('ede3cc6261d01950b148872c19bfa9f3f8b192a692353f20c5915442b4313a936bf3985a60caa57e', '96', '3', 'authToken', '[]', '1', '2021-06-23 21:01:58', '2021-06-23 21:01:58', '2022-06-23 18:01:58');
INSERT INTO `oauth_access_tokens` VALUES ('edfc43e8ed28d9a89b9f117ee943eb1826a8af0e5a2e7fedd33c17b90eb0c326160ba88a9c72073d', '6', '3', 'authToken', '[]', '1', '2021-04-21 13:34:43', '2021-04-21 13:34:43', '2022-04-21 10:34:43');
INSERT INTO `oauth_access_tokens` VALUES ('ee4eb7079d7561a8dfed97f6b06c4c4a89715d2f019086a2c338e34c1781763cbb6f8f98cea9de5a', '45', '3', 'authToken', '[]', '0', '2021-05-03 00:55:10', '2021-05-03 00:55:10', '2022-05-02 21:55:10');
INSERT INTO `oauth_access_tokens` VALUES ('ef7143bdb393ee39ba0d88a4b712cc4de99c2f42c77595430c85a4f1ab446d6d0638ec061fc24e45', '6', '3', 'authToken', '[]', '0', '2021-06-03 07:43:40', '2021-06-03 07:43:40', '2022-06-03 04:43:40');
INSERT INTO `oauth_access_tokens` VALUES ('efc5f691ee9c696f88e60f4c970bfe2f956aee30e9be2856f8d5f7105ab34fb3eeb90f6712b3305b', '131', '3', 'authToken', '[]', '0', '2021-07-03 20:13:06', '2021-07-03 20:13:06', '2022-07-03 17:13:06');
INSERT INTO `oauth_access_tokens` VALUES ('f008e0074ac2f1bfcbc53f189d9a401858999b31fa970e7591f706d056c1ffbbd8c55b9e3bdcb279', '112', '3', 'authToken', '[]', '0', '2021-06-28 22:05:36', '2021-06-28 22:05:36', '2022-06-28 19:05:36');
INSERT INTO `oauth_access_tokens` VALUES ('f02f98bf468fd061a5d10cf849f3ad3491172de2b86cc4c3cd8e03e1521e03ea9e27cee6b1c94f13', '6', '3', 'authToken', '[]', '0', '2021-03-25 14:27:00', '2021-03-25 14:27:00', '2022-03-25 10:27:00');
INSERT INTO `oauth_access_tokens` VALUES ('f14ed6fcfa41c0ec5a7957100f040c2502006bad1702ecfab7f764cbabe88bc274470d595ff7cdce', '27', '3', 'authToken', '[]', '1', '2021-04-17 14:43:01', '2021-04-17 14:43:01', '2022-04-17 11:43:01');
INSERT INTO `oauth_access_tokens` VALUES ('f192130187d08b3bd5063e90bea99cf78e630579191d214ba017c4753128f91b179324ed60829d94', '8', '3', 'authToken', '[]', '0', '2021-05-06 20:23:06', '2021-05-06 20:23:06', '2022-05-06 17:23:06');
INSERT INTO `oauth_access_tokens` VALUES ('f1bf72f561bb48568e55def3af642685ac9caf475209240dffa55ad18ae7718a56115fa1fc97f2f8', '27', '3', 'authToken', '[]', '1', '2021-04-17 14:45:36', '2021-04-17 14:45:36', '2022-04-17 11:45:36');
INSERT INTO `oauth_access_tokens` VALUES ('f1e5108c1947877b343ecca624de789d0b1ef5044c5b358c3243a639c0832094f1aef9dd8c69b5c6', '71', '3', 'authToken', '[]', '1', '2021-05-20 19:09:56', '2021-05-20 19:09:56', '2022-05-20 16:09:56');
INSERT INTO `oauth_access_tokens` VALUES ('f2375d54ebedb52412c6a6d90ab4eebe59b3684ab9648a807120d6e6d4d8c6608dba760628bb6e86', '45', '3', 'authToken', '[]', '0', '2021-05-03 18:55:38', '2021-05-03 18:55:38', '2022-05-03 15:55:38');
INSERT INTO `oauth_access_tokens` VALUES ('f27cec7a7528843f2c1f41f2c4dbed83a94695ebb7ac94d5db555e296ae9b50eed29ead829b783f5', '28', '3', 'authToken', '[]', '0', '2021-04-18 02:03:47', '2021-04-18 02:03:47', '2022-04-17 23:03:47');
INSERT INTO `oauth_access_tokens` VALUES ('f2a3bdcc614cea234b102b14561519fda1d1edbded7919b115fd0e7ca2cd06635952e1453a5fd79e', '100', '3', 'authToken', '[]', '1', '2021-06-24 02:01:26', '2021-06-24 02:01:26', '2022-06-23 23:01:26');
INSERT INTO `oauth_access_tokens` VALUES ('f3bb96ee43f09e8f963417173378291708b2e35aab3e998d15c32170bf751bae0e884ff4780eeea1', '8', '3', 'authToken', '[]', '1', '2021-05-01 12:04:13', '2021-05-01 12:04:13', '2022-05-01 09:04:13');
INSERT INTO `oauth_access_tokens` VALUES ('f4173700be2e46ea00784c06fcb56473b270c19d3d4fa1723e384bded52a9fb14d527be7a63004db', '126', '3', 'authToken', '[]', '0', '2021-06-30 23:26:51', '2021-06-30 23:26:51', '2022-06-30 20:26:51');
INSERT INTO `oauth_access_tokens` VALUES ('f470c67b06d9940edf68ad21ec0f4a8baf5b0a76110b22fee9de4242bb129de737250d6535e467ec', '8', '3', 'authToken', '[]', '1', '2021-05-06 18:46:51', '2021-05-06 18:46:51', '2022-05-06 15:46:51');
INSERT INTO `oauth_access_tokens` VALUES ('f58f622971e6f828aabe60e65f02bad93d24a849171f7a193e3f7583ca7a2f96bb90db70031bc54d', '8', '3', 'authToken', '[]', '0', '2021-04-21 00:14:40', '2021-04-21 00:14:40', '2022-04-20 21:14:40');
INSERT INTO `oauth_access_tokens` VALUES ('f5a002d707149543a0ff16d5524c8564c4cf03ae44ce238528fa77ad777d41fc7ca70d99db0638af', '67', '3', 'authToken', '[]', '0', '2021-06-22 19:28:20', '2021-06-22 19:28:20', '2022-06-22 16:28:20');
INSERT INTO `oauth_access_tokens` VALUES ('f5be7d40fa9435223570e114301a3963ff26b78e8f4b2fe7fb17ec213f0433f933de1adceffa5953', '9', '3', 'authToken', '[]', '1', '2021-05-16 20:45:21', '2021-05-16 20:45:21', '2022-05-16 17:45:21');
INSERT INTO `oauth_access_tokens` VALUES ('f5d8a969b74c2fe984a30d124afbf79fe2bde9273d5bec39187c10d923fa84be77b71d7e01922871', '6', '3', 'authToken', '[]', '0', '2021-04-02 08:09:02', '2021-04-02 08:09:02', '2022-04-02 05:09:02');
INSERT INTO `oauth_access_tokens` VALUES ('f68e5f6726e5cab3847e9f90e4f559a716dc18cf48286333da5663655a1ea8a489a274932ca3c09c', '6', '3', 'authToken', '[]', '0', '2021-04-14 15:44:34', '2021-04-14 15:44:34', '2022-04-14 12:44:34');
INSERT INTO `oauth_access_tokens` VALUES ('f6acd25b6c7f8e05f8a0c38195da156c5d072194e41b6802b26f3c8a3398a15d8c0cf94941943fb4', '78', '3', 'authToken', '[]', '0', '2021-06-24 02:23:17', '2021-06-24 02:23:17', '2022-06-23 23:23:17');
INSERT INTO `oauth_access_tokens` VALUES ('f6b617a1d6554cedd3ce48603306869d53be911a0cc17d21254e4c29f4bffd16f6571a0c41313805', '66', '3', 'authToken', '[]', '1', '2021-06-21 16:38:26', '2021-06-21 16:38:26', '2022-06-21 13:38:26');
INSERT INTO `oauth_access_tokens` VALUES ('f7304de8d4b110f2dc0d62c9da7570818437da69ffd2e9b5e13d6e95f667314edecc6dec51be00f7', '9', '3', 'authToken', '[]', '1', '2021-05-21 10:53:37', '2021-05-21 10:53:37', '2022-05-21 07:53:37');
INSERT INTO `oauth_access_tokens` VALUES ('f7743fb3d9c0f5e6ded0b0eeaf93a2b9715f250bc852c73d92e5ed918f72d90a829f7d48b715fa89', '62', '3', 'authToken', '[]', '1', '2021-05-02 19:06:48', '2021-05-02 19:06:48', '2022-05-02 16:06:48');
INSERT INTO `oauth_access_tokens` VALUES ('f81fa0308a5b7dc01caba0fa63ae0ba7722442b9a18faa0a99476bc3434a0349b099ca52f82dd879', '7', '3', 'authToken', '[]', '0', '2021-04-16 13:34:00', '2021-04-16 13:34:00', '2022-04-16 10:34:00');
INSERT INTO `oauth_access_tokens` VALUES ('f8b0c4473852840949403a46a927558cb5ab59cfe33422fb17dcd84e8d849f5028ba7fb5bd56be94', '73', '3', 'authToken', '[]', '0', '2021-06-01 15:15:54', '2021-06-01 15:15:54', '2022-06-01 12:15:54');
INSERT INTO `oauth_access_tokens` VALUES ('f92964c0240574dd98ff9080d6f396f9aa44b5918417060cce0ed9f340e898ffe6bec737a7fc9815', '73', '3', 'authToken', '[]', '0', '2021-06-24 02:45:43', '2021-06-24 02:45:43', '2022-06-23 23:45:43');
INSERT INTO `oauth_access_tokens` VALUES ('f98f73362f2ec6931db4c1b6e8814d0f1ff8207ed03af25cdd511c03fb3c088995d027414fd64fe8', '8', '3', 'authToken', '[]', '1', '2021-05-26 14:07:36', '2021-05-26 14:07:36', '2022-05-26 11:07:36');
INSERT INTO `oauth_access_tokens` VALUES ('fa3a27194837bda2d80bf0888bbfa68a497eef7d84c84f53715293a9948d7f0a87e6c986e07b8583', '8', '3', 'authToken', '[]', '1', '2021-05-22 14:15:01', '2021-05-22 14:15:01', '2022-05-22 11:15:01');
INSERT INTO `oauth_access_tokens` VALUES ('fa4dcfd1b947cbaa23d969dbfece5333da291031338f095666dd4e55aa4a741a78b3c2caa2f769b1', '8', '3', 'authToken', '[]', '1', '2021-07-05 14:28:44', '2021-07-05 14:28:44', '2022-07-05 11:28:44');
INSERT INTO `oauth_access_tokens` VALUES ('fa551d0d1d9b861991f40e9989807359567509be95f762f391401dbd3d19eab25719261c65acf0f0', '8', '3', 'authToken', '[]', '0', '2021-04-12 12:40:02', '2021-04-12 12:40:02', '2022-04-12 09:40:02');
INSERT INTO `oauth_access_tokens` VALUES ('faff0d652f276589d858cf5df006b6f5e23d599806ba81e9fb52bfbf6e24e6eff2cd44bcb7f0ee27', '28', '3', 'authToken', '[]', '0', '2021-04-19 01:54:29', '2021-04-19 01:54:29', '2022-04-18 22:54:29');
INSERT INTO `oauth_access_tokens` VALUES ('fb10e3b350bbe47e7c6ec0461046219a8d2a5ac2bbf3b10e87748aa4746f236c81f704830441823a', '45', '3', 'authToken', '[]', '0', '2021-05-03 00:54:39', '2021-05-03 00:54:39', '2022-05-02 21:54:39');
INSERT INTO `oauth_access_tokens` VALUES ('fb61abfc151f1fccf62f984a26b0371fe263e107840442566358afc72ad26d20ba7514919bf4bc16', '6', '3', 'authToken', '[]', '1', '2021-04-30 20:06:47', '2021-04-30 20:06:47', '2022-04-30 17:06:47');
INSERT INTO `oauth_access_tokens` VALUES ('fc666595c215dd41901883dbd19cd702ecf777a6b64952de673dfec16283d054fa912e7d1bb8bde7', '28', '3', 'authToken', '[]', '0', '2021-04-18 02:03:38', '2021-04-18 02:03:38', '2022-04-17 23:03:38');
INSERT INTO `oauth_access_tokens` VALUES ('fce46d79d1a636118ea70bf7243f01620c857b2cbcba3457fec729efb6b23387a80535aa8fbb5e2b', '35', '3', 'authToken', '[]', '1', '2021-04-18 12:53:45', '2021-04-18 12:53:45', '2022-04-18 09:53:45');
INSERT INTO `oauth_access_tokens` VALUES ('fd12271ffc533ee68dd016f4a226b9e5e04aec245a2fa2abd4090ab057cd48feacf9dedb5768f69d', '45', '3', 'authToken', '[]', '0', '2021-05-08 19:00:25', '2021-05-08 19:00:25', '2022-05-08 16:00:25');
INSERT INTO `oauth_access_tokens` VALUES ('fd6dc76597dd0c2d92e2cd3da99f68f5b8a7c6e90e491de43665ab267ce5923330520162951da5df', '8', '3', 'authToken', '[]', '1', '2021-06-24 14:10:16', '2021-06-24 14:10:16', '2022-06-24 11:10:16');
INSERT INTO `oauth_access_tokens` VALUES ('fd940e16680d1e2a154e4e66bbaefb0c842090a19f73bcb389049662d98cb4a85702c7de80c10597', '8', '3', 'authToken', '[]', '0', '2021-04-21 13:11:50', '2021-04-21 13:11:50', '2022-04-21 10:11:50');
INSERT INTO `oauth_access_tokens` VALUES ('fddf955296af4e31c0053c35f35d1afbc1ec489c4d445475dfee904b77cdb2585560806e15773555', '83', '3', 'authToken', '[]', '0', '2021-06-23 20:28:52', '2021-06-23 20:28:52', '2022-06-23 17:28:52');
INSERT INTO `oauth_access_tokens` VALUES ('fdef37007b4965a21f77dfd8487cbee2ded36c4965037c9728ca5c2f9b711c5435ed58885943dba4', '57', '3', 'authToken', '[]', '0', '2021-04-28 22:35:14', '2021-04-28 22:35:14', '2022-04-28 19:35:14');
INSERT INTO `oauth_access_tokens` VALUES ('fe0ca71fff3cf88c590542a309e6ed3d73829f700379b83b7f60f74f6921afe513985321058eddaf', '8', '3', 'authToken', '[]', '0', '2021-04-21 11:59:06', '2021-04-21 11:59:06', '2022-04-21 08:59:06');
INSERT INTO `oauth_access_tokens` VALUES ('fe69f988685605509d51aa9e128de4ee7e0e616c347d301d0c573f1847da2b7cb570236f8511b406', '6', '3', 'authToken', '[]', '1', '2021-04-28 17:44:43', '2021-04-28 17:44:43', '2022-04-28 14:44:43');
INSERT INTO `oauth_access_tokens` VALUES ('fff7aaae45ec9a2da19f01f97a1e0aa9c884e7cfe2945d955ce7712b1734b0c368371b0c103b695a', '8', '3', 'authToken', '[]', '0', '2021-05-04 20:01:03', '2021-05-04 20:01:03', '2022-05-04 17:01:03');

-- ----------------------------
-- Table structure for oauth_auth_codes
-- ----------------------------
DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of oauth_auth_codes
-- ----------------------------

-- ----------------------------
-- Table structure for oauth_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of oauth_clients
-- ----------------------------
INSERT INTO `oauth_clients` VALUES ('1', null, 'Popl Personal Access Client', '6w3owx0R2CvPUBoOHMlmbDZjPKPlPGDCqEAq81Ot', null, 'http://localhost', '1', '0', '0', '2021-03-25 13:59:06', '2021-03-25 13:59:06');
INSERT INTO `oauth_clients` VALUES ('2', null, 'Popl Password Grant Client', '0aMff4Uy1eF6JgfcYnfwHa0b7piWjhCUIQuCZOJJ', 'users', 'http://localhost', '0', '1', '0', '2021-03-25 13:59:06', '2021-03-25 13:59:06');
INSERT INTO `oauth_clients` VALUES ('3', null, 'Popl Personal Access Client', 'oklgYVvGaSmv1B4PlXxr47Uu9xYTDjotePmkCfoo', null, 'http://localhost', '1', '0', '0', '2021-03-25 13:59:32', '2021-03-25 13:59:32');
INSERT INTO `oauth_clients` VALUES ('4', null, 'Popl Password Grant Client', '3YjRdEC2QqqAeNmEp7BiHtwKorzB956kUXnl6MFK', 'users', 'http://localhost', '0', '1', '0', '2021-03-25 13:59:32', '2021-03-25 13:59:32');

-- ----------------------------
-- Table structure for oauth_personal_access_clients
-- ----------------------------
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of oauth_personal_access_clients
-- ----------------------------
INSERT INTO `oauth_personal_access_clients` VALUES ('1', '1', '2021-03-25 13:59:06', '2021-03-25 13:59:06');
INSERT INTO `oauth_personal_access_clients` VALUES ('2', '3', '2021-03-25 13:59:32', '2021-03-25 13:59:32');

-- ----------------------------
-- Table structure for oauth_refresh_tokens
-- ----------------------------
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of oauth_refresh_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for profiles
-- ----------------------------
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_type_id` int(11) DEFAULT NULL,
  `is_pro` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_code` (`profile_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of profiles
-- ----------------------------
INSERT INTO `profiles` VALUES ('1', 'Facebook', 'Facebook', 'url', 'facebook', null, '1', '0', '1', '20210330071416__facebookicon.png', '1', '1', '2021-03-25 16:31:32', '2021-05-19 19:22:26');
INSERT INTO `profiles` VALUES ('2', 'Instagram', 'Instagram', 'username', 'instagram', 'https://instagram.com/', '1', '0', '1', '20210330071410__insta.png', '1', '1', '2021-03-25 16:31:32', '2021-06-23 18:22:57');
INSERT INTO `profiles` VALUES ('3', 'Whatsapp', 'Whatsapp', 'number', 'whatsapp', 'https://api.whatsapp.com/send?phone=', '4', '0', '1', '20210330071400__whatsappPro.png', '1', '1', '2021-03-25 16:31:32', '2021-03-30 10:14:00');
INSERT INTO `profiles` VALUES ('4', 'Call', 'Anruf', 'number', 'call', 'tel:', '4', '0', '1', '20210519053154__ic_phone.png', '1', '1', '2021-03-25 12:07:30', '2021-07-05 07:46:02');
INSERT INTO `profiles` VALUES ('5', 'Text', 'Nachricht', 'number', 'text', 'sms:', '4', '0', '1', '20210330071345__imessage.png', '1', '1', '2021-03-25 12:07:30', '2021-07-05 07:45:52');
INSERT INTO `profiles` VALUES ('6', 'Email', 'Email', 'other', 'email', 'mailto:', '4', '0', '1', '20210519054507__Email.png', '1', '1', '2021-03-25 12:07:30', '2021-05-19 20:45:07');
INSERT INTO `profiles` VALUES ('9', 'www', 'Browser', 'url', 'www', null, '5', '0', '1', '20210330071211__safariPro.png', '1', '1', '2021-03-29 14:41:48', '2021-07-05 07:45:12');
INSERT INTO `profiles` VALUES ('10', 'Address', 'Adresse', 'other', 'address', 'https://www.google.com/maps/search/?api=1&query=', '4', '0', '1', '20210330072214__googlemaps.png', '1', '1', '2021-03-30 10:22:14', '2021-07-05 07:45:39');
INSERT INTO `profiles` VALUES ('11', 'Custom', 'Custom', 'url', 'custom', null, '5', '1', '0', '20210409101549__Probutton.png', '1', '1', '2021-04-09 13:15:49', '2021-05-16 21:32:31');
INSERT INTO `profiles` VALUES ('12', 'File', 'File', 'url', 'file', null, '5', '1', '0', '20210409101612__fileWhite.png', '1', '1', '2021-04-09 13:16:12', '2021-05-16 21:32:20');
INSERT INTO `profiles` VALUES ('13', 'Contact Card', 'Kontaktkarte', 'url', 'contact-card', null, '4', '0', '1', '20210516125851__ic_phone_contact.png', '1', '1', '2021-04-20 10:56:48', '2021-07-05 07:45:23');
INSERT INTO `profiles` VALUES ('14', 'Snapchat', 'Snapchat', 'username', 'snapchat', 'https://www.snapchat.com/add/', '1', '0', '1', '20210516112240__Snapchat.png', '1', '1', '2021-05-16 14:22:40', '2021-05-19 19:21:04');
INSERT INTO `profiles` VALUES ('15', 'Twitter', 'Twitter', 'username', 'twitter', 'https://twitter.com/', '1', '0', '1', '20210516112350__Twitter.png', '1', '1', '2021-05-16 14:23:50', '2021-05-19 20:30:17');
INSERT INTO `profiles` VALUES ('16', 'TikTok', 'TikTok', 'username', 'tiktok', 'https://www.tiktok.com/@', '1', '0', '1', '20210516112443__TikTok.png', '1', '1', '2021-05-16 14:24:43', '2021-05-19 19:19:39');
INSERT INTO `profiles` VALUES ('17', 'LinkedIn', 'LinkedIn', 'url', 'linkedin', null, '1', '0', '1', '20210516112545__LinkedIn.png', '1', null, '2021-05-16 14:25:45', '2021-05-16 14:25:45');
INSERT INTO `profiles` VALUES ('18', 'YouTube', 'YouTube', 'url', 'youtube', null, '1', '0', '1', '20210519053734__ic_youtube_new.png', '1', '1', '2021-05-16 14:26:52', '2021-05-19 20:37:34');
INSERT INTO `profiles` VALUES ('19', 'Spotify', 'Spotify', 'url', 'spotify', null, '2', '0', '1', '20210516113536__Spotify.png', '1', null, '2021-05-16 14:35:36', '2021-05-16 14:35:36');
INSERT INTO `profiles` VALUES ('20', 'Apple Music', 'Apple Music', 'url', 'apple-music', null, '2', '0', '1', '20210516064637__ic_itunes.png', '1', '1', '2021-05-16 14:36:22', '2021-05-16 21:46:37');
INSERT INTO `profiles` VALUES ('21', 'SoundCloud', 'SoundCloud', 'username', 'soundcloud', 'http://soundcloud.com/', '2', '0', '1', '20210516113707__SoundCloud.png', '1', '1', '2021-05-16 14:37:07', '2021-05-19 20:18:33');
INSERT INTO `profiles` VALUES ('22', 'PayPal', 'PayPal', 'url', 'paypal', null, '3', '0', '1', '20210516064247__ic_paypal.png', '1', '1', '2021-05-16 14:37:58', '2021-05-16 21:42:47');
INSERT INTO `profiles` VALUES ('23', 'Klarna', 'Klarna', 'url', 'klarna', null, '3', '0', '1', '20210516114101__Klarna-Logo-1.png', '1', null, '2021-05-16 14:41:01', '2021-05-16 14:41:01');
INSERT INTO `profiles` VALUES ('24', 'Cash App', 'Cash App', 'url', 'cash-app', null, '3', '0', '1', '20210516114149__CashApp.png', '1', null, '2021-05-16 14:41:49', '2021-05-16 14:41:49');
INSERT INTO `profiles` VALUES ('25', 'Twitch', 'Twitch', 'username', 'twitch', 'https://www.twitch.tv/', '1', '0', '1', '20210516114420__Twitch.png', '1', '1', '2021-05-16 14:44:20', '2021-05-19 20:17:33');
INSERT INTO `profiles` VALUES ('26', 'Pinterest', 'Pinterest', 'username', 'pinterest', 'https://www.pinterest.com/', '1', '0', '1', '20210519054245__ic_pintrest_new.png', '1', '1', '2021-05-16 15:56:58', '2021-05-19 20:42:45');
INSERT INTO `profiles` VALUES ('27', 'Telegram', 'Telegram', 'username', 'telegram', 'https://telegram.me/', '4', '0', '1', '20210519054028__ic_telegram_new.png', '1', '1', '2021-05-16 21:31:42', '2021-05-19 20:40:28');
INSERT INTO `profiles` VALUES ('28', 'Linktree', 'Linktree', 'url', 'linktree', null, '5', '0', '1', '20210516063630__Linktree.png', '1', null, '2021-05-16 21:36:30', '2021-05-16 21:36:30');

-- ----------------------------
-- Table structure for profile_types
-- ----------------------------
DROP TABLE IF EXISTS `profile_types`;
CREATE TABLE `profile_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of profile_types
-- ----------------------------
INSERT INTO `profile_types` VALUES ('1', 'Social Media', '1', '1', '1', '2021-03-25 16:28:05', '2021-03-25 16:28:05');
INSERT INTO `profile_types` VALUES ('2', 'Music', '1', '1', '1', '2021-03-25 16:28:05', '2021-03-25 16:28:05');
INSERT INTO `profile_types` VALUES ('3', 'Payment', '1', '1', '1', '2021-03-25 16:28:05', '2021-03-25 16:28:05');
INSERT INTO `profile_types` VALUES ('4', 'Contact', '1', '1', '1', '2021-03-25 16:28:05', '2021-03-25 16:28:05');
INSERT INTO `profile_types` VALUES ('5', 'Other', '1', '1', '1', '2021-03-25 16:31:32', '2021-03-25 16:31:32');

-- ----------------------------
-- Table structure for taps_views
-- ----------------------------
DROP TABLE IF EXISTS `taps_views`;
CREATE TABLE `taps_views` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT 0,
  `is_tap_view` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 't',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'main-profile' COMMENT '''main-profile'', ''profile''',
  `type_id` int(11) DEFAULT 0,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `lat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `lng` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of taps_views
-- ----------------------------
INSERT INTO `taps_views` VALUES ('1', '8', 't', 'main-profile', '8', '', '33.4585511', '73.0145741', '0', null, '2021-08-26 10:16:58', '2021-08-26 10:16:58');
INSERT INTO `taps_views` VALUES ('2', '8', 'v', 'main-profile', '8', '', '33.6192039', '73.1703513', '0', null, '2021-08-26 10:40:05', '2021-08-26 10:40:05');
INSERT INTO `taps_views` VALUES ('3', '8', 't', 'main-profile', '8', '', '33.6192039', '73.1703513', '0', null, '2021-08-25 10:40:11', '2021-08-26 10:40:11');
INSERT INTO `taps_views` VALUES ('4', '8', 'v', 'main-profile', '8', '', '33.6192039', '73.1703513', '0', null, '2021-08-26 10:40:26', '2021-08-26 10:40:26');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` int(11) NOT NULL DEFAULT 3,
  `user_group_id` int(11) DEFAULT 2,
  `logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pro` int(11) NOT NULL DEFAULT 0,
  `is_public` int(11) NOT NULL DEFAULT 1,
  `profile_view` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'personal',
  `status` int(11) NOT NULL DEFAULT 1,
  `provider` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `vcode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vcode_expiry` timestamp NULL DEFAULT NULL,
  `subscription_date` timestamp NULL DEFAULT NULL,
  `subscription_expires_on` timestamp NULL DEFAULT NULL,
  `open_direct` int(1) DEFAULT 0,
  `fcm_token` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'Bilal Khan', 'addmee@mailinator.com', 'addmee.admin', '$2y$10$x7Xj.hcfysb4forPn3jGSebVeq9V5DFtoVQEOnP20VPd2evCBPV9u', null, '1', '1', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, '2021-03-01 15:51:42', null, '2021-06-16 16:39:43', '2021-06-16 16:39:43', '2021-06-16 16:39:43', '0', '', '1', '1', 'uNpl9LciUlz50To1NXsBFMp1yUB5vOjUDrddlRChaA5rrVP9UkPqnZELQVeV', '2021-03-01 15:51:18', '2021-03-11 10:59:06');
INSERT INTO `users` VALUES ('8', 'Wahab', 'wahab@yopmail.com', 'wahab111', '$2y$10$LPNoguFEpRckqQQtmkQbnOflZYPCXtjkvhxIsZZ2bA.YUzYlDv4GS', '1991-06-08', '1', '2', null, 'This is a test profile', '20210609072429__IMG_20210610_002410504.jpg', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '420491', '2021-07-07 01:15:17', '2021-07-07 01:15:17', '2021-07-07 01:15:17', '0', 'fcm_token', null, '8', null, '2021-04-11 07:11:15', '2021-07-06 23:15:17');
INSERT INTO `users` VALUES ('102', 'Wahab', 'wahab+1@yopmail.com', 'wahab', '$2y$10$1zvN8ZiEncmAjHAJBRobCeEHif.VLhGgjUHBX76zDD9b5SCktwgQK', null, '3', '2', null, 'Hello everyone I am a good developer and I like making apps for iOS', '20210705072914__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '5691', '2021-07-07 00:11:04', '2021-07-07 00:11:04', '2021-07-07 00:11:04', '0', null, null, '102', null, '2021-06-24 10:18:54', '2021-07-06 22:11:04');
INSERT INTO `users` VALUES ('103', 'wahab facebook', 'abdulwahabaziz20@yahoo.com', 'wahabfb', '$2y$10$x7Xj.hcfysb4forPn3jGSebVeq9V5DFtoVQEOnP20VPd2evCBPV9u', null, '3', '2', null, 'Hello\nI am', null, '0', '1', 'personal', '1', 'facebook', '', null, null, null, null, '2021-08-26 15:50:36', '6508', '2021-06-25 12:38:38', '2021-06-25 12:38:38', '2021-06-25 12:38:38', '0', 'fcm_token', null, null, null, '2021-06-24 10:20:15', '2021-08-26 10:50:36');
INSERT INTO `users` VALUES ('104', 'wahab apple id', 'abdulwahabaziz20@gmail.com', 'wahabAppleId', '$2y$10$yPaExbXvCeT94swKrqdAyeYoWeAj7lKw0X23gomSFRLOXUSYN8mVO', null, '3', '2', null, null, null, '0', '1', 'personal', '1', 'apple', '', null, null, null, null, null, '8254', '2021-06-25 12:29:35', '2021-06-25 12:29:35', '2021-06-25 12:29:35', '0', null, null, null, null, '2021-06-24 17:51:38', '2021-06-25 10:29:35');
INSERT INTO `users` VALUES ('105', null, 'mbilal@mailinator.com', null, '$2y$10$ouQ.0edSPtpve8VL1aguXeqounkA.UOhdvofpVuJsMy2rSuYjwmMW', null, '3', '2', null, null, null, '0', '1', 'personal', '1', 'facebook', '123456', null, null, null, null, null, '6964', '2021-06-25 22:38:31', null, null, '0', 'fcm_token', null, null, null, '2021-06-24 22:38:31', '2021-06-24 22:38:31');
INSERT INTO `users` VALUES ('106', 'saadahmed', 'Saad.ahmed87@hotmail.com', 'saaaaadaad', '$2y$10$ERx.N75YHAij9WYnFlH0IeLeHZoscHmU5Bgabx0ee0S2/de6ZZdki', '1987-05-05', '1', '2', null, 'exigency.biz\nbyb\nAlhamdulillah\n...', '20210704041033__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '5262', '2021-07-04 21:10:33', '2021-07-04 21:10:33', '2021-07-04 21:10:33', '0', null, null, '106', null, '2021-06-25 01:35:35', '2021-07-04 19:10:33');
INSERT INTO `users` VALUES ('107', 'Abdul Wahab Aziz', 'no-email@test.com', 'wahab123', '$2y$10$Dkb0uciD4RuAQPlcDPQ/JeMzI6Uk9exqs8vKvT/SkuMkpxUcCfl9G', '1997-02-10', '1', '2', null, 'Hello everyone i am an iOS developer and i like making apps that helps humankind.', '20210705074000__file.png', '0', '1', 'personal', '1', 'apple', '000447.febab6557a5e47de8b94cd800905e3b1.1439', null, null, null, null, null, '3469', '2021-07-07 01:13:38', '2021-07-07 01:13:38', '2021-07-07 01:13:38', '0', null, null, '107', null, '2021-06-25 10:35:23', '2021-07-06 23:13:38');
INSERT INTO `users` VALUES ('108', 'wahab gmail', 'abdulwahabaziz23@gmail.com', 'wahabGmail', '$2y$10$GTZ0v1a45S3A9GlBh9sbV.Ng1Pqf0T6grQJOthLtPtOJXAIeufWUW', '0000-00-00', '1', '2', null, 'Hello everyone i am Abdul Wahab', null, '0', '1', 'personal', '1', 'google', 'ya29.a0AfH6SMAQlJMjN6bJ_p_DgushZKUtCXL7-FRskaM_NloNejepjTwFAvdMQ8KQIOxMMStsJc_TbugBatDH-nO9oYlaeilOcS8h1gLknP3hj3PYjbpP2PenNt0IosbEr59iPmp7y5EeMcXW7eqmHfeHNEHvUU4a', null, null, null, null, null, '2289', '2021-06-25 12:39:09', '2021-06-25 12:39:09', '2021-06-25 12:39:09', '0', null, null, null, null, '2021-06-25 10:38:55', '2021-06-25 10:39:09');
INSERT INTO `users` VALUES ('109', 'riz', 'Haider_riz@yahoo.com', 'riz', '$2y$10$or1N07WoSE6knDTiqINRnOVK9IOChtagEF3wldJaR9Hm8LWbfQv6a', '1985-12-05', '1', '2', null, 'Alhumdulillah...', '20210625012224__IMG_20210625_182220518.jpg', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '8763', '2021-06-25 18:25:20', '2021-06-25 18:25:20', '2021-06-25 18:25:20', '1', 'fcm_token', null, '109', null, '2021-06-25 16:21:48', '2021-06-25 16:25:20');
INSERT INTO `users` VALUES ('110', 'Yilmaz', 'yilmaz.ez@icloud.com', 'Yilmaz', '$2y$10$THlbAOWc0RgjZdVmMPqHFO0S/t8K6WoPMZqMZ6H8PjH1smx9AF.qe', null, '3', '2', null, 'AddMee CEO', '20210705124856__file.png', '0', '1', 'personal', '1', 'apple', '001018.e0edae12b2c74adf93020992d2d7b4ab.1443', null, null, null, null, null, '5551', '2021-07-07 02:00:34', '2021-07-07 02:00:34', '2021-07-07 02:00:34', '0', null, null, '110', null, '2021-06-25 17:43:12', '2021-07-07 00:00:34');
INSERT INTO `users` VALUES ('111', 'afaq123', 'afaqwaseem3@gmail.com', 'afaq123', '$2y$10$zlslY8MimtMfOSv0rl5B5uht4xV5EwHxvKbnaufqwANQQgIurCzA2', null, '3', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '4337', '2021-06-28 23:24:17', '2021-06-28 23:24:17', '2021-06-28 23:24:17', '0', 'fcm_token', null, null, null, '2021-06-28 21:23:48', '2021-06-28 21:24:17');
INSERT INTO `users` VALUES ('112', 'K2aan01', 'kaan.mavruk@hotmail.com', 'K2aaaaaan01', '$2y$10$IstobZCAro8CFR1ABjiq/./P78MzkhJA4v7Fal8V5m7VWaL4b6HVS', '1997-10-06', '1', '2', null, 'Hallo i bims Kaan', '20210628070610__file.png', '0', '1', 'personal', '1', 'facebook', 'EAAK05yRlTeUBALepHxzPIYjNRADZAWZBjhiN0OOwTOEwifPA6jCjQaqtKQdPOsbYiYhOp7KZAxZCZCSUFZBJDnefjHuANEOgx7qZAZAiEvWZBvpRJPuYE2xyV9KCb9EOxOMFIZBK28g9e8yQ5TWdZAApun7vgTEkUNh4rw1Vgi8uBuMb5MxvJKHmGihR6RFdSd7phUJDiTiWtUGNWNLfjQ54X1NXoqRwlZCE3RxfSksRVDne2qhxr6hgI2fx', null, null, null, null, null, '1196', '2021-06-29 23:49:22', '2021-06-29 23:49:22', '2021-06-29 23:49:22', '0', null, null, '112', null, '2021-06-28 22:05:36', '2021-06-29 21:49:22');
INSERT INTO `users` VALUES ('113', 'Hamza Mehmood', 'social4it@gmail.com', 'hamzamehmood', '$2y$10$NFJoiWv3P6nMSXQgLSpIu.E2O/pH63n35DNT3QnKSL6ADEjRFJtRK', '1992-06-08', '1', '2', null, 'This is Hamza Mehmood, CEO & Founder at Logiqon Solutions', '20210705113915__file.png', '0', '1', 'personal', '1', 'google', 'ya29.a0ARrdaM8TFMAeegaypULpCUI6T1oc94Apu3QjAUXLad5GxQkcbF61ewNm-XfClJIdWask5QpjzQt89qVMd_DKcWeRlvtSVQluLAs4hX9Z1qSiFHmFovtXpxmkFkxhbr6QSQSIHK1ZJ8Olhp9tFlf5Ic78Oeb4', null, null, null, null, null, '7552', '2021-07-05 16:39:15', '2021-07-05 16:39:15', '2021-07-05 16:39:15', '0', null, null, '113', null, '2021-06-28 22:36:48', '2021-07-05 14:39:15');
INSERT INTO `users` VALUES ('114', 'RIDVAN', 'ridvanoek@hotmail.com', 'ridxonair', '$2y$10$lJsG2RM5bTtZX8sHBOHg9OrWlhm.FTfT.NBsi4O1yIqoYks8WqRc.', '1998-01-09', '1', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '6174', '2021-06-29 00:40:30', '2021-06-29 00:40:30', '2021-06-29 00:40:30', '0', 'fcm_token', null, null, null, '2021-06-28 22:39:29', '2021-06-28 22:40:30');
INSERT INTO `users` VALUES ('115', 'Tabe', 'tabesanelankovan1997@gmail.com', 'Tabe', '$2y$10$Xo8QchOguMOp39gjapIaCOCSmsvJizPiKxL6kAMpAi/1/H52Ta91i', '1997-05-18', '1', '2', null, null, '20210628080716__file.png', '0', '1', 'personal', '1', 'google', 'ya29.a0ARrdaM87Hk-IvNx6eUNIqz6Puzgvc45E6NmxaaIT9fN53GaGSi7iR0o1hRf7Anf4vR17na-hRtKm8bptT5WfhTCMalPh3R8WxwMkTj4n6NucsmIxfje06NY7jPFng_yRbHhB8BIGfrEwZ2DvDUdihfdE1yYu', null, null, null, null, null, '4187', '2021-06-29 20:50:17', '2021-06-29 20:50:17', '2021-06-29 20:50:17', '0', null, null, '115', null, '2021-06-28 22:56:32', '2021-06-29 18:50:17');
INSERT INTO `users` VALUES ('116', 'Ramin Ramez', 'raminramezofficial@gmail.com', 'Ramin', '$2y$10$LnA3zq92nKkU.SCmKBAwSuxELF6Tr6Ga5.uVV9xn6SSWNl/deOSoe', '1983-11-27', '0', '2', null, 'urban italian fine food & wine\nlifestyle fashion events \ncurrent Projekts\nsoul2soul.com', '20210629022234__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '4177', '2021-07-02 17:45:16', '2021-07-02 17:45:16', '2021-07-02 17:45:16', '0', 'fcm_token', null, '116', null, '2021-06-29 17:20:11', '2021-07-02 15:45:16');
INSERT INTO `users` VALUES ('117', 'YAYA', '6wpmgckdzw@privaterelay.appleid.com', 'yaya', '$2y$10$nT.kmKrNFkD8jkIn.i/Hyu6kZ.IYfqLgF5xD7BKIcEAojH3IoetuO', null, '1', '2', null, null, null, '0', '1', 'personal', '1', 'apple', '001053.2749921430a1463799401770f33fb0df.1509', null, null, null, null, null, '9371', '2021-06-29 20:10:53', '2021-06-29 20:10:53', '2021-06-29 20:10:53', '0', null, null, null, null, '2021-06-29 18:09:08', '2021-06-29 18:10:53');
INSERT INTO `users` VALUES ('118', 'Jeremy Jamal', '4tdb75jggq@privaterelay.appleid.com', 'Jeremyjamal', '$2y$10$jLDFR9p5vg42.SKNaUPar.eIQZk2IBnCPqZ7qioHFmo0f4bBtBgZu', null, '3', '2', null, null, '20210629045254__file.png', '0', '1', 'personal', '1', 'apple', '001219.b6cf6cd8a81e4d5fb57b68db82548679.1650', null, null, null, null, null, '6034', '2021-06-29 21:53:00', '2021-06-29 21:53:00', '2021-06-29 21:53:00', '0', null, null, '118', null, '2021-06-29 19:50:14', '2021-06-29 19:53:00');
INSERT INTO `users` VALUES ('119', 'AddMee', 'yilmazezikoglu@googlemail.com', 'addmee', '$2y$10$TWn0ufDAr/.C2KoQ3M8MOu1QS88/t.5bLk4twz3DyTb9gSat.JXga', null, '3', '2', null, 'DEUTSCHLANDS NR.1 DIGITALE VISITENKARTE', '20210630114809__file.png', '0', '1', 'personal', '1', 'google', 'ya29.a0ARrdaM-RGz3Zr1EGLFpER6c5aykMTJ1Npx0lkEuNHWZNVydENuzqfPFDXDMAnvExn4ffr5JmyQhsgy8h7Uv7P8Q4XBvxR2oP2DPThc8i0nSMgIc8MfbnCj_LlERqaXdVG9mJ3Z4luti8iYgNnmVGFixK6Z1p', null, null, null, null, null, '2984', '2021-07-06 21:58:12', '2021-07-06 21:58:12', '2021-07-06 21:58:12', '0', null, null, '119', null, '2021-06-30 14:46:45', '2021-07-06 19:58:12');
INSERT INTO `users` VALUES ('120', 'Erdem Nazli', 'erdem.nazli85@googlemail.com', 'Moafaka', '$2y$10$lvqmoq70IOzTgwuEvoHfFe2ocZ.WZenUwfWTqqasY1YN5mLuiDqf.', '2020-11-29', '1', '2', null, 'Life is short for later :)', '20210630010552__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '8249', '2021-07-05 14:39:09', '2021-07-05 14:39:09', '2021-07-05 14:39:09', '0', 'fcm_token', null, '120', null, '2021-06-30 16:00:31', '2021-07-05 12:39:09');
INSERT INTO `users` VALUES ('121', null, 'maiklein123@gmx.de', 'HokageMaikel', '$2y$10$8DhzZKpS4kWCCvi4Bzv2uux6TCHi2AsiAqOGj4GVaq9FJbiVe57Dq', null, '3', '2', null, null, '20210630031604__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '5870', '2021-06-30 20:16:16', '2021-06-30 20:16:16', '2021-06-30 20:16:16', '0', 'fcm_token', null, '121', null, '2021-06-30 17:40:49', '2021-06-30 18:16:16');
INSERT INTO `users` VALUES ('122', 'mert', 'mert3.yilmaz@gmx.de', 'mert', '$2y$10$nyycM27yHSItZK79qUXvXe/i0VWDUi3A8GWD.p3JIRWIKpFsHAE/C', null, '0', '2', null, 'moin', null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '1634', '2021-06-30 20:56:03', '2021-06-30 20:56:03', '2021-06-30 20:56:03', '0', 'fcm_token', null, null, null, '2021-06-30 18:54:14', '2021-06-30 18:56:03');
INSERT INTO `users` VALUES ('123', 'Abdullah Demirdas', 'yvcxfb95qx@privaterelay.appleid.com', 'Abdullah', '$2y$10$sw2LxRw4o.AzPWkui2Toueuy/1LbnZ0yjLVOi.typ6aqYjIw/P6cS', '1994-02-10', '1', '2', null, 'Entrepreneur', '20210630065730__file.png', '0', '1', 'personal', '1', 'apple', '000325.5eed61786d1d45aa82b3b5cf2e4a7225.1739', null, null, null, null, null, '4737', '2021-07-06 22:25:25', '2021-07-06 22:25:25', '2021-07-06 22:25:25', '0', null, null, '123', null, '2021-06-30 20:39:25', '2021-07-06 20:25:25');
INSERT INTO `users` VALUES ('124', null, 'sivasampoo01@gmail.com', 'Mark1971', '$2y$10$mfGZnksfjMmz8BlDitOn4OfnxyakUJ1VzEIlc8d9xP78.emT1FFey', '1971-02-20', '1', '2', null, 'Sivasampoo Gnanasegaran', null, '0', '2', 'personal', '1', 'google', 'ya29.a0ARrdaM97soJBKRLpj4ycIeVbRtf9ScLXTDLBm30kNrY-bG9jQ4fvIuP74jyLPHMBo-y9ofxoFQlJrH_cdoqmnexnE8StnEPPikHjMHK6hh0zA32KlaerajMHm2hwQ5l1xOUXK7TFde2KrFT_ysKpfnhHQjnC', null, null, null, null, null, '5095', '2021-06-30 22:56:38', '2021-06-30 22:56:38', '2021-06-30 22:56:38', '0', null, null, null, null, '2021-06-30 20:45:23', '2021-06-30 20:56:38');
INSERT INTO `users` VALUES ('125', null, 'ismetmavruk@gmail.com', 'ismet', '$2y$10$IWxaf1.H3BuLzJOQS4PutO4Uda4jchPUX.UCM7qrc/cKO5SuCcB8O', null, '3', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '5441', '2021-06-30 23:52:16', '2021-06-30 23:52:16', '2021-06-30 23:52:16', '1', 'fcm_token', null, '125', null, '2021-06-30 21:49:52', '2021-06-30 21:52:16');
INSERT INTO `users` VALUES ('126', 'kaan', 'kaan-colak@hotmail.de', 'Kaan', '$2y$10$mS/Ksj5CyOqLTsBzp3jYvubLp4Y0DJom.A6FJflFZ6iS7RHsfIPNG', '0000-00-00', '1', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '7581', '2021-07-01 06:11:54', '2021-07-01 06:11:54', '2021-07-01 06:11:54', '0', 'fcm_token', null, null, null, '2021-06-30 23:26:51', '2021-07-01 04:11:54');
INSERT INTO `users` VALUES ('127', 'peter', 'infantembaby@gmail.com', 'Peter', '$2y$10$J2/xx4oO2OOh30TQqjH73.9dq41Orf/5w.PdTAY5wz1cBra2iH2uy', null, '3', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '2368', '2021-07-01 02:18:14', '2021-07-01 02:18:14', '2021-07-01 02:18:14', '0', 'fcm_token', null, '127', null, '2021-07-01 00:16:46', '2021-07-01 00:18:14');
INSERT INTO `users` VALUES ('128', 'Malik', 'malikwunsch20@gmail.com', 'Malik', '$2y$10$w6ABHDc6wjsAegHQfk4n6O/wxh5h9FRluYJryjiwf2nfWlhph2Y5a', '2001-12-10', '1', '2', null, null, '20210701012710__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '9781', '2021-07-01 18:27:45', '2021-07-01 18:27:45', '2021-07-01 18:27:45', '0', 'fcm_token', null, '128', null, '2021-07-01 16:26:16', '2021-07-01 16:27:45');
INSERT INTO `users` VALUES ('129', null, 'alibadran771@yahoo.de', 'Ali', '$2y$10$FXYLkpVJfP2blE2ZrdA82eplsdMEMPOjSLVSwrTgkgQlPa2I3.gm.', null, '3', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '8102', '2021-07-02 00:40:27', '2021-07-02 00:40:27', '2021-07-02 00:40:27', '0', 'fcm_token', null, null, null, '2021-07-01 22:40:18', '2021-07-01 22:40:27');
INSERT INTO `users` VALUES ('130', null, 'starmiray@hotmail.com', 'MiYou', '$2y$10$8Y/LqX3Wht73yiIzidn5Me62bWgEozuvrMwHzqg4R5C7rutHfpOaK', null, '3', '2', null, 'MiYou Mireille Youssef ??', '20210703044727__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '4786', '2021-07-03 21:53:32', '2021-07-03 21:53:32', '2021-07-03 21:53:32', '0', 'fcm_token', null, '130', null, '2021-07-03 19:46:07', '2021-07-03 19:53:32');
INSERT INTO `users` VALUES ('131', 'Maja', 'maja-lisa@hotmail.de', 'Maja', '$2y$10$T.6ZYQ6vjAG64VcZBm/f5OtPGRFAAvePl0wB7WR85Nt.1KPhamL36', '1998-04-09', '2', '2', null, '• 23 \n• Based in Nbg', '20210703051448__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '7803', '2021-07-04 19:44:18', '2021-07-04 19:44:18', '2021-07-04 19:44:18', '1', 'fcm_token', null, '131', null, '2021-07-03 20:13:06', '2021-07-04 17:44:18');
INSERT INTO `users` VALUES ('132', null, 'cccompetition@gmx.de', 'cccompetition', '$2y$10$eqppzm2bI7RlXo5jrQkY0uyfYzPDMT6A//H25GZIYs74nocUExf3C', '1992-03-29', '3', '2', null, 'Hobbyfotograf / Portrait-Fotograf', '20210703055123__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '1838', '2021-07-03 23:23:34', '2021-07-03 23:23:34', '2021-07-03 23:23:34', '0', 'fcm_token', null, '132', null, '2021-07-03 20:43:23', '2021-07-03 21:23:34');
INSERT INTO `users` VALUES ('133', 'Rose', 'rose.smith.babes@gmail.com', 'Rose', '$2y$10$QXRu.RXCYSCsBvgA629RROUQVzv4jH08mYlVaV0sG/FuZ.JszV622', '2003-04-13', '2', '2', null, 'Hey I’m Rose', '20210703054624__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '5059', '2021-07-03 22:46:44', '2021-07-03 22:46:44', '2021-07-03 22:46:44', '0', 'fcm_token', null, '133', null, '2021-07-03 20:45:32', '2021-07-03 20:46:44');
INSERT INTO `users` VALUES ('134', 'Karina', 'sc2kncpmbq@privaterelay.appleid.com', 'Karina', '$2y$10$RTwT7bX2CJRwppvbo3Tu.eHL7ehFmnJ2lkZWG6mvxtbXSdzJwUDDC', null, '3', '2', null, null, null, '0', '2', 'personal', '1', 'apple', '001930.025103df7b7d4fe3bde67f5c62e26ce7.1807', null, null, null, null, null, '8177', '2021-07-04 03:52:44', '2021-07-04 03:52:44', '2021-07-04 03:52:44', '0', null, null, null, null, '2021-07-03 21:07:47', '2021-07-04 01:52:44');
INSERT INTO `users` VALUES ('135', null, 'corciaeren@web.de', null, '$2y$10$8XPhLRJ/Gy7I7FUebX.GeuSbi4NaeheLhPhK1JiUgFkhNYUfXh1QW', null, '2', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '2250', '2021-07-04 00:29:11', '2021-07-04 00:29:11', '2021-07-04 00:29:11', '0', 'fcm_token', null, null, null, '2021-07-03 22:23:53', '2021-07-03 22:29:11');
INSERT INTO `users` VALUES ('136', 'Laura Pollok', 'zfd9nd5whw@privaterelay.appleid.com', 'laurapollok', '$2y$10$KleFFj7l/P1vQ0GLNLPy7eUCQcqSPjYtIh/B4KmZ93Pb1hjJrrVIy', null, '3', '2', null, 'Steckbrief', '20210704035644__file.png', '0', '1', 'personal', '1', 'apple', '001044.3e1b73f84eea4bbea9e56f7213c9c9db.1531', null, null, null, null, null, '8474', '2021-07-04 20:59:59', '2021-07-04 20:59:59', '2021-07-04 20:59:59', '0', null, null, '136', null, '2021-07-04 18:31:32', '2021-07-04 18:59:59');
INSERT INTO `users` VALUES ('137', 'ella', 'ella.t95@web.de', 'umbrella', '$2y$10$hfns6fKWjiWhn8gz40jkxOnjVAuTN3zbQaghqEBgIReYTD5d1gq8.', null, '3', '2', null, null, '20210704035928__file.png', '0', '1', 'personal', '1', 'apple', '000663.72a9ec24e1c74a21875c5875ac4c02bb.1555', null, null, null, null, null, '6349', '2021-07-04 20:59:47', '2021-07-04 20:59:47', '2021-07-04 20:59:47', '0', null, null, '137', null, '2021-07-04 18:55:47', '2021-07-04 18:59:47');
INSERT INTO `users` VALUES ('138', null, 'info@woodstyle360.de', 'Woodstyle360', '$2y$10$AbHB90WJrAkbhcGrt9xxyOAmMqtN4l4ObVd5NZ2NOFMelKzaS3voW', null, '3', '2', null, null, '20210704043125__file.png', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '8358', '2021-07-04 21:31:25', '2021-07-04 21:31:25', '2021-07-04 21:31:25', '0', 'fcm_token', null, '138', null, '2021-07-04 19:29:46', '2021-07-04 19:31:25');
INSERT INTO `users` VALUES ('139', null, 'systems.hamzamehmood@gmail.com', null, '$2y$10$dwfJfrxseGLwACWBCN0Nhe4s0vCtyKasbJB76W.rpi.oTct3bOjk6', null, '3', '2', null, null, null, '0', '1', 'personal', '1', 'google', 'firebase', null, null, null, null, null, '3811', '2021-07-05 19:43:24', null, null, '0', 'fcm_token', null, null, null, '2021-07-04 19:43:24', '2021-07-04 19:43:24');
INSERT INTO `users` VALUES ('140', 'Robina', 'bvkyvp9yjz@privaterelay.appleid.com', 'Robina', '$2y$10$XOkmhXP8spTdvjHp20ZYkuzdhNxs2wVRGdpOMeOm10Vz7befg0YTq', null, '3', '2', null, null, null, '0', '2', 'personal', '1', 'apple', '000344.2c7bb8ddc31c4a90b092f8a80c88dcc2.1220', null, null, null, null, null, '4286', '2021-07-05 17:25:10', '2021-07-05 17:25:10', '2021-07-05 17:25:10', '1', null, null, '140', null, '2021-07-05 15:20:39', '2021-07-05 15:25:10');
INSERT INTO `users` VALUES ('141', 'MarkGnanam', 'wtc45jknf5@privaterelay.appleid.com', 'Mark', '$2y$10$c0bk4LGp9zeOrFf31ejvfe8dlTqFQZfoDcjEcl9yyRv2fjv8qTw9y', null, '3', '2', null, null, '20210705124103__file.png', '0', '1', 'personal', '1', 'apple', '000055.ab59bfd86352498aacc5481c667e3521.1236', null, null, null, null, null, '2656', '2021-07-05 17:41:03', '2021-07-05 17:41:03', '2021-07-05 17:41:03', '0', null, null, '141', null, '2021-07-05 15:36:16', '2021-07-05 15:41:03');
INSERT INTO `users` VALUES ('142', 'Mustafa', 'Mustafa.hk@hotmail.de', 'Mustafa', '$2y$10$WjyixGacGiLj4rgr.SYnYu5YRQBKkvVTR0I0XOhHotBiJviiELVa.', null, '3', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '7862', '2021-07-06 03:33:17', '2021-07-06 03:33:17', '2021-07-06 03:33:17', '0', 'fcm_token', null, null, null, '2021-07-06 01:28:36', '2021-07-06 01:33:17');
INSERT INTO `users` VALUES ('143', null, 'oencue.burak@gmail.com', 'Burak', '$2y$10$CbsZy9MXG7jS/e1WN5o70eQIA2eCUW1lyS01tDXn8b.Y5Mu9Xr7jC', null, '3', '2', null, null, null, '0', '1', 'personal', '1', 'google', 'ya29.a0ARrdaM8Z96V-sGcHDS4OdaFwG5WPimz3reqRPFwT-P1VQmX0By-gT9H4ceD-b2ZXVjQxG9GnhowFt9C0mbbU1DSItySeSUgcF_5nUHhdhw5p1oJytHDmehkdE0GZ6t5j3oSAdVoRCoD3SF0fIaHd45rA6tga', null, null, null, null, null, '9101', '2021-07-06 03:35:26', '2021-07-06 03:35:26', '2021-07-06 03:35:26', '0', null, null, null, null, '2021-07-06 01:35:03', '2021-07-06 01:35:26');
INSERT INTO `users` VALUES ('144', 'Bia!', 'bianca.cagatay.deluca@gmail.com', 'Bia', '$2y$10$iCcmq8O.D7LTaBy3JNKwa.Q1BTNSBDpaVDl.fBJ4sPehz1uHA/rxW', '1976-10-11', '2', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '9181', '2021-07-06 15:07:58', '2021-07-06 15:07:58', '2021-07-06 15:07:58', '0', 'fcm_token', null, null, null, '2021-07-06 12:57:11', '2021-07-06 13:07:58');
INSERT INTO `users` VALUES ('145', 'Emir', 'emir.oek@gmail.com', 'emir1907', '$2y$10$rlCeZ01n/qgoooSAkPqWiuk/bPgp6JxWBsxDpZK6ZeDTI0z1y7WYO', null, '3', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '9049', '2021-07-06 17:37:15', '2021-07-06 17:37:15', '2021-07-06 17:37:15', '0', 'fcm_token', null, null, null, '2021-07-06 15:36:21', '2021-07-06 15:37:15');
INSERT INTO `users` VALUES ('146', 'enes?', 'enesoek@gmail.com', 'enes', '$2y$10$3f10WB7va0sHJ0jSd.t3EeUCOe5AW4d3xqNiH0WNAW.PlVqdbaRr6', null, '0', '2', null, 'AddMee⚡', '20210706124441__IMG_20210706_154435343.jpg', '0', '1', 'personal', '1', '', '', null, null, null, null, null, '7726', '2021-07-06 19:20:45', '2021-07-06 19:20:45', '2021-07-06 19:20:45', '0', 'fcm_token', null, '146', null, '2021-07-06 15:40:45', '2021-07-06 17:20:45');
INSERT INTO `users` VALUES ('147', 'Salim', 's.goekce91@icloud.com', 'Salim', '$2y$10$XxOuJSMH7yATarvCFJyhoeUWo.3LNDIwhrjKm8uL/BxowcGtn6jeC', null, '3', '2', null, null, '20210706125058__file.png', '0', '1', 'personal', '1', 'apple', '001071.de0eb2d9af334242985953c8d75460d5.1250', null, null, null, null, null, '8175', '2021-07-06 23:45:35', '2021-07-06 23:45:35', '2021-07-06 23:45:35', '0', null, null, '147', null, '2021-07-06 15:50:22', '2021-07-06 21:45:35');
INSERT INTO `users` VALUES ('148', 'Fatih Cakmak', 'fatihc1453@gmail.com', 'Fatih44', '$2y$10$xIGX4OggPs2Sya3XhJPedO8icbNarGE6IKLfBwxjB6WghPPU.JTem', '1995-11-15', '1', '2', null, null, '20210706010333__file.png', '0', '1', 'personal', '1', 'apple', '000937.958279c982d548c7b88b14687f72adc3.1250', null, null, null, null, null, '6327', '2021-07-06 18:03:33', '2021-07-06 18:03:33', '2021-07-06 18:03:33', '0', null, null, '148', null, '2021-07-06 15:50:38', '2021-07-06 16:03:33');
INSERT INTO `users` VALUES ('149', 'eazy', 'yilmaz.ez@gmx.de', 'eazy', '$2y$10$jIKWA4Y9V6OB5BEYqmO6CevLW7i71PnVBePrgMmaBL9qBDQ7I9xWC', null, '0', '2', null, null, null, '0', '1', 'personal', '1', '', '', null, null, null, null, null, '9949', '2021-07-06 21:52:05', '2021-07-06 21:52:05', '2021-07-06 21:52:05', '0', 'fcm_token', null, null, null, '2021-07-06 19:50:27', '2021-07-06 19:52:05');

-- ----------------------------
-- Table structure for user_groups
-- ----------------------------
DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permissions` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_groups
-- ----------------------------
INSERT INTO `user_groups` VALUES ('1', 'Admin', '0', '1', '1', '2021-03-05 15:24:59', '2021-03-05 15:24:59');
INSERT INTO `user_groups` VALUES ('2', 'User', '0', '1', '1', '2021-03-05 15:24:59', '2021-03-05 15:24:59');

-- ----------------------------
-- Table structure for user_notes
-- ----------------------------
DROP TABLE IF EXISTS `user_notes`;
CREATE TABLE `user_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_notes
-- ----------------------------
INSERT INTO `user_notes` VALUES ('3', '8', 'Hamza Mehmood', 'max4it@live.com', '03322840825', 'Hello friend!!!', '8', null, '2021-05-18 18:07:47', '2021-05-18 18:07:47');
INSERT INTO `user_notes` VALUES ('9', '8', 'Abdul Wahab', 'wahab@yopmail.com', '1234', 'Hello i want to connect', '8', null, '2021-05-22 15:07:09', '2021-05-22 15:07:09');
INSERT INTO `user_notes` VALUES ('14', '115', 'Tabe', 'tabesanelankovan1997@gmail.com', '1234', 'Du sibbi', '115', null, '2021-06-28 23:06:53', '2021-06-28 23:06:53');
INSERT INTO `user_notes` VALUES ('15', '120', 'Erdem Nazli', 'erdem.nazli85@googlemail.com', '1234', 'Hinterlasse eine Notiz', '120', null, '2021-06-30 20:54:32', '2021-06-30 20:54:32');
