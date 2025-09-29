/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: visersim
-- ------------------------------------------------------
-- Server version	10.11.13-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `click_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
INSERT INTO `admin_notifications` VALUES
(1,0,'A new contact message has been submitted',0,'/admin/ticket/view/1','2025-08-21 21:50:50','2025-08-21 21:50:50');
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_password_resets`
--

DROP TABLE IF EXISTS `admin_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_password_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_password_resets`
--

LOCK TABLES `admin_password_resets` WRITE;
/*!40000 ALTER TABLE `admin_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES
(1,'Super Admins','admin@rainbow-nights.com','admin',NULL,NULL,'$2y$10$E.NgdXb8iq2uyjaYSHnw/uyj.YRIxP4qsegod14vg338ILgdSTOXG',NULL,NULL,'2024-08-10 23:41:14');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bkash_token`
--

DROP TABLE IF EXISTS `bkash_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bkash_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sandbox_mode` tinyint(4) NOT NULL,
  `id_expiry` bigint(20) NOT NULL DEFAULT 0,
  `id_token` varchar(2048) NOT NULL,
  `refresh_expiry` bigint(20) NOT NULL DEFAULT 0,
  `refresh_token` varchar(2048) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bkash_token`
--

LOCK TABLES `bkash_token` WRITE;
/*!40000 ALTER TABLE `bkash_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `bkash_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '0',
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_plan`
--

DROP TABLE IF EXISTS `country_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_plan` (
  `plan_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `country_id` bigint(20) unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_plan`
--

LOCK TABLES `country_plan` WRITE;
/*!40000 ALTER TABLE `country_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `country_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cron_job_logs`
--

DROP TABLE IF EXISTS `cron_job_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cron_job_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cron_job_id` int(10) unsigned NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `duration` int(10) unsigned NOT NULL DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cron_job_logs`
--

LOCK TABLES `cron_job_logs` WRITE;
/*!40000 ALTER TABLE `cron_job_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cron_job_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cron_jobs`
--

DROP TABLE IF EXISTS `cron_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cron_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cron_schedule_id` int(11) NOT NULL DEFAULT 0,
  `next_run` datetime DEFAULT NULL,
  `last_run` datetime DEFAULT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cron_jobs`
--

LOCK TABLES `cron_jobs` WRITE;
/*!40000 ALTER TABLE `cron_jobs` DISABLE KEYS */;
INSERT INTO `cron_jobs` VALUES
(5,'Fetch Currency','fetch_currency','[\"\\\\App\\\\Http\\\\Controllers\\\\CronController\",\"fetchCurrency\"]','',3,'2025-07-04 10:10:14','2025-07-03 10:10:14',1,1,'2025-05-11 04:46:06','2025-07-03 04:10:14'),
(6,'Sync Plans from DataPlan.io','sync_plan','[\"\\\\App\\\\Http\\\\Controllers\\\\CronController\",\"syncDataPlan\"]','',3,'2025-07-04 10:10:31','2025-07-03 10:10:31',1,1,'2025-05-11 04:46:06','2025-07-03 04:10:31');
/*!40000 ALTER TABLE `cron_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cron_schedules`
--

DROP TABLE IF EXISTS `cron_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cron_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `interval` int(10) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cron_schedules`
--

LOCK TABLES `cron_schedules` WRITE;
/*!40000 ALTER TABLE `cron_schedules` DISABLE KEYS */;
INSERT INTO `cron_schedules` VALUES
(1,'Hourly',3600,1,'2024-03-13 23:34:09','2024-05-06 04:45:32'),
(3,'Daily',86400,1,'2024-05-06 04:46:39','2024-05-06 04:46:39');
/*!40000 ALTER TABLE `cron_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `api_currency` varchar(40) DEFAULT NULL,
  `conversion_rate` decimal(28,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposits`
--

DROP TABLE IF EXISTS `deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deposits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `order_id` int(10) unsigned NOT NULL DEFAULT 0,
  `method_code` int(10) unsigned NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text DEFAULT NULL,
  `btc_amount` varchar(255) DEFAULT NULL,
  `btc_wallet` varchar(255) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `payment_try` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `is_web` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'This will be 1 if the request is from NextJs application',
  `admin_feedback` varchar(255) DEFAULT NULL,
  `success_url` varchar(255) DEFAULT NULL,
  `failed_url` varchar(255) DEFAULT NULL,
  `last_cron` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposits`
--

LOCK TABLES `deposits` WRITE;
/*!40000 ALTER TABLE `deposits` DISABLE KEYS */;
/*!40000 ALTER TABLE `deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `device_tokens`
--

DROP TABLE IF EXISTS `device_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `is_app` tinyint(1) NOT NULL DEFAULT 0,
  `token` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device_tokens`
--

LOCK TABLES `device_tokens` WRITE;
/*!40000 ALTER TABLE `device_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `esims`
--

DROP TABLE IF EXISTS `esims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `esims` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `order_item_id` bigint(20) NOT NULL,
  `serial_number` int(11) NOT NULL DEFAULT 0,
  `phone_number` varchar(20) NOT NULL DEFAULT '0',
  `qr_code` varchar(255) NOT NULL DEFAULT '0',
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `esims`
--

LOCK TABLES `esims` WRITE;
/*!40000 ALTER TABLE `esims` DISABLE KEYS */;
/*!40000 ALTER TABLE `esims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extensions`
--

DROP TABLE IF EXISTS `extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `script` text DEFAULT NULL,
  `shortcode` text DEFAULT NULL COMMENT 'object',
  `support` text DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extensions`
--

LOCK TABLES `extensions` WRITE;
/*!40000 ALTER TABLE `extensions` DISABLE KEYS */;
INSERT INTO `extensions` VALUES
(1,'tawk-chat','Tawk.to','Key location is shown bellow','tawky_big.png','<script>\r\n                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n                        (function(){\r\n                        var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\n                        s1.async=true;\r\n                        s1.src=\"https://embed.tawk.to/{{app_key}}\";\r\n                        s1.charset=\"UTF-8\";\r\n                        s1.setAttribute(\"crossorigin\",\"*\");\r\n                        s0.parentNode.insertBefore(s1,s0);\r\n                        })();\r\n                    </script>','{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}','twak.png',0,'2019-10-18 11:16:05','2024-05-16 06:23:02'),
(2,'google-recaptcha2','Google Recaptcha 2','Key location is shown bellow','recaptcha3.png','\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\n<div class=\"g-recaptcha\" data-sitekey=\"{{site_key}}\" data-callback=\"verifyCaptcha\"></div>\n<div id=\"g-recaptcha-error\"></div>','{\"site_key\":{\"title\":\"Site Key\",\"value\":\"6LdPC88fAAAAADQlUf_DV6Hrvgm-pZuLJFSLDOWV\"},\"secret_key\":{\"title\":\"Secret Key\",\"value\":\"6LdPC88fAAAAAG5SVaRYDnV2NpCrptLg2XLYKRKB\"}}','recaptcha.png',0,'2019-10-18 11:16:05','2025-07-05 05:29:09'),
(3,'custom-captcha','Custom Captcha','Just put any random string','customcaptcha.png',NULL,'{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}','na',0,'2019-10-18 11:16:05','2025-07-05 05:29:07'),
(4,'google-analytics','Google Analytics','Key location is shown bellow','google_analytics.png','<script async src=\"https://www.googletagmanager.com/gtag/js?id={{measurement_id}}\"></script>\n                <script>\n                  window.dataLayer = window.dataLayer || [];\n                  function gtag(){dataLayer.push(arguments);}\n                  gtag(\"js\", new Date());\n                \n                  gtag(\"config\", \"{{measurement_id}}\");\n                </script>','{\"measurement_id\":{\"title\":\"Measurement ID\",\"value\":\"------\"}}','ganalytics.png',0,NULL,'2021-05-03 22:19:12'),
(5,'fb-comment','Facebook Comment ','Key location is shown bellow','Facebook.png','<div id=\"fb-root\"></div><script async defer crossorigin=\"anonymous\" src=\"https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v4.0&appId={{app_key}}&autoLogAppEvents=1\"></script>','{\"app_key\":{\"title\":\"App Key\",\"value\":\"----\"}}','fb_com.png',0,NULL,'2022-03-21 17:18:36');
/*!40000 ALTER TABLE `extensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `form_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frontends`
--

DROP TABLE IF EXISTS `frontends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `frontends` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data_keys` varchar(40) DEFAULT NULL,
  `data_values` longtext DEFAULT NULL,
  `seo_content` longtext DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frontends`
--

LOCK TABLES `frontends` WRITE;
/*!40000 ALTER TABLE `frontends` DISABLE KEYS */;
INSERT INTO `frontends` VALUES
(1,'seo.data','{\"seo_image\":\"1\",\"keywords\":[\"eSIM\",\"international eSIM\",\"global eSIM\",\"travel eSIM\",\"mobile data eSIM\",\"eSIM data plans\",\"buy eSIM online\",\"instant eSIM activation\",\"prepaid eSIM\"],\"meta_robots\":\"\",\"description\":\"Buy international eSIMs for travel with instant activation and global coverage. Choose affordable mobile data plans for over 190+ countries. No physical SIM needed\\u2014stay connected anywhere.\",\"social_title\":\"eSIM - International eSIM And Data Purchase Platform\",\"social_description\":\"Stay connected while traveling with eSIM \\u2013 the easiest way to get mobile data worldwide. Activate instantly, no physical SIM required. Explore affordable global data plans now.\",\"image\":\"68690d2f487081751715119.png\"}',NULL,NULL,'','2020-07-04 23:42:52','2025-07-05 05:32:00'),
(24,'about.content','{\"has_image\":\"1\",\"subheading\":\"Redefining Connectivity with eSIM Technology\",\"description\":\"We are revolutionizing mobile connectivity through cutting-edge eSIM technology, delivering seamless, flexible, and borderless communication solutions. As a leading eSIM service provider, our mission is to empower users with instant access to global networks\\u2014anytime, anywhere\\u2014without the limitations of traditional SIM cards.\",\"image\":\"67ff3f049b2d51744781060.png\"}',NULL,'basic','','2020-10-28 00:51:20','2025-07-07 00:13:55'),
(27,'contact_us.content','{\"email_title\":\"Email Address\",\"email_address\":\"support.esim@gmail.com\",\"secondary_email_address\":\"support.esim@hotmail.com\",\"address_title\":\"Office Address\",\"address\":\"28 Benin, Niger #5 San  Francisco United States\",\"phone_title\":\"Phone number\",\"phone_number\":\"+1555999777\",\"secondary_phone_number\":\"+1222444666\",\"form_title\":\"Get in Touch With Us\",\"form_subtitle\":\"Have questions or need support? We\'re here to help\\u2014reach out anytime! Our team is ready to assist you with anything you need.\",\"latitude\":\"48.8575475\",\"longitude\":\"2.3513765\"}',NULL,'basic','','2020-10-28 00:59:19','2025-07-07 01:37:10'),
(28,'counter.content','{\"heading\":\"Latest News\",\"subheading\":\"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Doloribus necessitatibus repudiandae porro reprehenderit, beatae perferendis repellat quo ipsa omnis, vitae!\"}',NULL,'basic',NULL,'2020-10-28 01:04:02','2024-03-13 23:54:07'),
(33,'feature.content','{\"heading\":\"asdf\",\"sub_heading\":\"asdf\"}',NULL,'basic',NULL,'2021-01-03 23:40:54','2021-01-03 23:40:55'),
(34,'feature.element','{\"title\":\"asdf\",\"description\":\"asdf\",\"feature_icon\":\"asdf\"}',NULL,'basic',NULL,'2021-01-03 23:41:02','2021-01-03 23:41:02'),
(36,'service.content','{\"subheading\":\"Our Services\",\"description\":\"We offer easy-to-use eSIM plans to keep you connected wherever you go.\"}',NULL,'basic','','2021-03-06 01:27:34','2025-07-07 00:55:18'),
(41,'cookie.data','{\"short_desc\":\"We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.\",\"description\":\"<h4>Introduction<\\/h4>\\r\\n\\r\\n<p>This Cookie Policy explains how to use cookies and similar technologies to recognize you when you visit our website.\\r\\n    It explains what these technologies are and why we use them, as well as your rights to control our use of them.<\\/p>\\r\\n<br>\\r\\n<h4>What are cookies?<\\/h4>\\r\\n\\r\\n<p>Cookies are small pieces of data stored on your computer or mobile device when you visit a website. Cookies are\\r\\n    widely used by website owners to make their websites work, or to work more efficiently, as well as to provide\\r\\n    reporting information.<\\/p>\\r\\n<br>\\r\\n<h4>Why do we use cookies?<\\/h4>\\r\\n\\r\\n<p>We use cookies for several reasons. Some cookies are required for technical reasons for our Website to operate, and\\r\\n    we refer to these as \\\"essential\\\" or \\\"strictly necessary\\\" cookies. Other cookies enable us to track and target the\\r\\n    interests of our users to enhance the experience on our Website. Third parties serve cookies through our Website for\\r\\n    advertising, analytics, and other purposes.<\\/p>\\r\\n<br>\\r\\n<h4>What types of cookies do we use?<\\/h4>\\r\\n\\r\\n<div>\\r\\n    <ul>\\r\\n        <li class=\\\"mb-1\\\">\\r\\n            <strong>Essential Website Cookies:<\\/strong>\\r\\n            These cookies are strictly necessary to provide you with services available through our Website and to use\\r\\n            some of its features.\\r\\n        <\\/li>\\r\\n        <li class=\\\"mb-1\\\">\\r\\n            <strong>Analytics and Performance Cookies:<\\/strong>\\r\\n            These cookies allow us to count visits and traffic sources to measure and improve our Website\'s performance.\\r\\n        <\\/li>\\r\\n        <li>\\r\\n            <strong>Advertising Cookies:<\\/strong>\\r\\n            These cookies make advertising messages more relevant to you and your interests. They perform functions like\\r\\n            preventing the same ad from continuously reappearing, ensuring that ads are properly displayed, and in some\\r\\n            cases selecting advertisements that are based on your interests.\\r\\n        <\\/li>\\r\\n    <\\/ul>\\r\\n<\\/div>\\r\\n<br>\\r\\n<h4>Data Collected by Cookies<\\/h4>\\r\\n<p>Cookies may collect various types of data, including but not limited to:<\\/p>\\r\\n<ul>\\r\\n    <li>IP addresses<\\/li>\\r\\n    <li>Browser and device information<\\/li>\\r\\n    <li>Referring website addresses<\\/li>\\r\\n    <li>Pages visited on our website<\\/li>\\r\\n    <li>Interactions with our website, such as clicks and mouse movements<\\/li>\\r\\n    <li>Time spent on our website<\\/li>\\r\\n<\\/ul>\\r\\n<br>\\r\\n<h4>How We Use Collected Data<\\/h4>\\r\\n\\r\\n<p>We may use data collected by cookies for the following purposes:<\\/p>\\r\\n<ul>\\r\\n    <li>To personalize your experience on our website<\\/li>\\r\\n    <li>To improve our website\'s functionality and performance<\\/li>\\r\\n    <li>To analyze trends and gather demographic information about our user base<\\/li>\\r\\n    <li>To deliver targeted advertising based on your interests<\\/li>\\r\\n    <li>To prevent fraudulent activity and enhance website security<\\/li>\\r\\n<\\/ul>\\r\\n<br>\\r\\n<h4>Third-party cookies<\\/h4>\\r\\n\\r\\n<p>In addition to our cookies, we may also use various third-party cookies to report usage statistics of our Website,\\r\\n    deliver advertisements on and through our Website, and so on.<\\/p>\\r\\n<br>\\r\\n<h4>How can we control cookies?<\\/h4>\\r\\n\\r\\n<p>You have the right to decide whether to accept or reject cookies. You can exercise your cookie preferences by\\r\\n    clicking on the \\\"Cookie Settings\\\" link in the footer of our website. You can also set or amend your web browser\\r\\n    controls to accept or refuse cookies. If you choose to reject cookies, you may still use our Website though your\\r\\n    access to some functionality and areas of our Website may be restricted.<\\/p>\\r\\n<br>\\r\\n<h4>Changes to our Cookie Policy<\\/h4>\\r\\n\\r\\n<p>We may update our Cookie Policy from time to time. We will notify you of any changes by posting the new Cookie Policy\\r\\n    on this page.<\\/p>\",\"status\":1}',NULL,NULL,NULL,'2020-07-04 23:42:52','2025-07-03 04:39:47'),
(42,'policy_pages.element','{\"title\":\"Privacy Policy\",\"details\":\"<h4>Introduction<\\/h4>\\r\\n        <p>\\r\\n            This Privacy Policy describes how we collects, uses, and discloses information, including personal information, in connection with your use of our website.\\r\\n        <\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Information We Collect<\\/h4>\\r\\n        <p>We collect two main types of information on the Website:<\\/p>\\r\\n        <ul>\\r\\n            <li><p><strong>Personal Information: <\\/strong>This includes data that can identify you as an individual, such as your name, email address, phone number, or mailing address. We only collect this information when you voluntarily provide it to us, like signing up for a newsletter, contacting us through a form, or making a purchase.<\\/p><\\/li>\\r\\n            <li><p><strong>Non-Personal Information: <\\/strong>This data cannot be used to identify you directly. It includes details like your browser type, device type, operating system, IP address, browsing activity, and usage statistics. We collect this information automatically through cookies and other tracking technologies.<\\/p><\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>How We Use Information<\\/h4>\\r\\n        <p>The information we collect allows us to:<\\/p>\\r\\n        <ul>\\r\\n            <li>Operate and maintain the Website effectively.<\\/li>\\r\\n            <li>Send you newsletters or marketing communications, but only with your consent.<\\/li>\\r\\n            <li>Respond to your inquiries and fulfill your requests.<\\/li>\\r\\n            <li>Improve the Website and your user experience.<\\/li>\\r\\n            <li>Personalize your experience on the Website based on your browsing habits.<\\/li>\\r\\n            <li>Analyze how the Website is used to improve our services.<\\/li>\\r\\n            <li>Comply with legal and regulatory requirements.<\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>Sharing of Information<\\/h4>\\r\\n        <p>We may share your information with trusted third-party service providers who assist us in operating the Website and delivering our services. These providers are obligated by contract to keep your information confidential and use it only for the specific purposes we disclose it for.<\\/p>\\r\\n        <p>We will never share your personal information with any third parties for marketing purposes without your explicit consent.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Data Retention<\\/h4>\\r\\n        <p>We retain your personal information only for as long as necessary to fulfill the purposes it was collected for. We may retain it for longer periods only if required or permitted by law.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Security Measures<\\/h4>\\r\\n        <p>We take reasonable precautions to protect your information from unauthorized access, disclosure, alteration, or destruction. However, complete security cannot be guaranteed for any website or internet transmission.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Changes to this Privacy Policy<\\/h4>\\r\\n        <p>We may update this Privacy Policy periodically. We will notify you of any changes by posting the revised policy on the Website. We recommend reviewing this policy regularly to stay informed of any updates.<\\/p>\\r\\n        <p><strong>Remember:<\\/strong>  This is a sample policy and may need adjustments to comply with specific laws and reflect your website\'s unique data practices. Consider consulting with a legal professional to ensure your policy is fully compliant.<\\/p>\"}',NULL,'basic','privacy-policy','2021-06-09 08:50:42','2024-04-24 05:43:19'),
(43,'policy_pages.element','{\"title\":\"Terms of Service\",\"details\":\"<h4>Introduction<\\/h4>\\r\\n        <p>\\r\\n            This Privacy Policy describes how we collect, use, and disclose information, including personal information, in connection with your use of our website.\\r\\n        <\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Information We Collect<\\/h4>\\r\\n        <p>We collect two main types of information on the Website:<\\/p>\\r\\n        <ul>\\r\\n            <li class=\\\"mb-2\\\"><p><strong>Personal Information: <\\/strong>This includes data that can identify you as an individual, such as your name, email address, phone number, or mailing address. We only collect this information when you voluntarily provide it to us, like signing up for a newsletter, contacting us through a form, or making a purchase.<\\/p><\\/li>\\r\\n            <li><p><strong>Non-Personal Information: <\\/strong>This data cannot be used to identify you directly. It includes details like your browser type, device type, operating system, IP address, browsing activity, and usage statistics. We collect this information automatically through cookies and other tracking technologies.<\\/p><\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>How We Use Information<\\/h4>\\r\\n        <p>The information we collect allows us to:<\\/p>\\r\\n        <ul>\\r\\n            <li>Operate and maintain the Website effectively.<\\/li>\\r\\n            <li>Send you newsletters or marketing communications, but only with your consent.<\\/li>\\r\\n            <li>Respond to your inquiries and fulfill your requests.<\\/li>\\r\\n            <li>Improve the Website and your user experience.<\\/li>\\r\\n            <li>Personalize your experience on the Website based on your browsing habits.<\\/li>\\r\\n            <li>Analyze how the Website is used to improve our services.<\\/li>\\r\\n            <li>Comply with legal and regulatory requirements.<\\/li>\\r\\n        <\\/ul>\\r\\n        <br \\/>\\r\\n        <h4>Sharing of Information<\\/h4>\\r\\n        <p>We may share your information with trusted third-party service providers who assist us in operating the Website and delivering our services. These providers are obligated by contract to keep your information confidential and use it only for the specific purposes we disclose it for.<\\/p>\\r\\n        <p>We will never share your personal information with any third parties for marketing purposes without your explicit consent.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Data Retention<\\/h4>\\r\\n        <p>We retain your personal information only for as long as necessary to fulfill the purposes it was collected for. We may retain it for longer periods only if required or permitted by law.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Security Measures<\\/h4>\\r\\n        <p>We take reasonable precautions to protect your information from unauthorized access, disclosure, alteration, or destruction. However, complete security cannot be guaranteed for any website or internet transmission.<\\/p>\\r\\n        <br \\/>\\r\\n        <h4>Changes to this Privacy Policy<\\/h4>\\r\\n        <p>We may update this Privacy Policy periodically. We will notify you of any changes by posting the revised policy on the Website. We recommend reviewing this policy regularly to stay informed of any updates.<\\/p>\\r\\n        <p><strong>Remember:<\\/strong>  This is a sample policy and may need adjustments to comply with specific laws and reflect your website\'s unique data practices. Consider consulting with a legal professional to ensure your policy is fully compliant.<\\/p>\"}',NULL,'basic','terms-of-service','2021-06-09 08:51:18','2025-07-07 02:40:34'),
(44,'maintenance.data','{\"description\":\"<div class=\\\"mb-5\\\" style=\\\"font-family: Nunito, sans-serif; margin-bottom: 3rem !important;\\\"><h3 class=\\\"mb-3\\\" style=\\\"text-align: center; font-weight: 600; line-height: 1.3; font-size: 24px; font-family: Exo, sans-serif;\\\"><font color=\\\"#ff0000\\\">THE SITE IS UNDER MAINTENANCE<\\/font><\\/h3><p class=\\\"font-18\\\" style=\\\"color: rgb(111, 111, 111); text-align: center; margin-right: 0px; margin-left: 0px; font-size: 18px !important;\\\">We\'re just tuning up a few things.We apologize for the inconvenience but Front is currently undergoing planned maintenance. Thanks for your patience.<\\/p><\\/div>\",\"image\":\"6603c203472ad1711522307.png\"}',NULL,NULL,NULL,'2020-07-04 23:42:52','2024-03-27 06:51:47'),
(55,'counter.content','{\"heading\":\"Latest Newsss\",\"subheading\":\"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Doloribus necessitatibus repudiandae porro reprehenderit, beatae perferendis repellat quo ipsa omnis, vitae!\"}',NULL,'basic','','2024-04-21 01:13:50','2024-04-21 01:13:50'),
(56,'counter.content','{\"heading\":\"Latest News\",\"subheading\":\"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Doloribus necessitatibus repudiandae porro reprehenderit, beatae perferendis repellat quo ipsa omnis, vitae!\"}',NULL,'basic','','2024-04-21 01:13:52','2024-04-21 01:13:52'),
(60,'kyc.content','{\"required\":\"Complete KYC to unlock the full potential of our platform! KYC helps us verify your identity and keep things secure. It is quick and easy just follow the on-screen instructions. Get started with KYC verification now!\",\"pending\":\"Your KYC verification is being reviewed. We might need some additional information. You will get an email update soon. In the meantime, explore our platform with limited features.\",\"reject\":\"We regret to inform you that the Know Your Customer (KYC) information provided has been reviewed and unfortunately, it has not met our verification standards.\"}',NULL,'basic','','2024-04-25 06:35:35','2025-07-03 03:26:09'),
(61,'kyc.content','{\"required\":\"Complete KYC to unlock the full potential of our platform! KYC helps us verify your identity and keep things secure. It is quick and easy just follow the on-screen instructions. Get started with KYC verification now!\",\"pending\":\"Your KYC verification is being reviewed. We might need some additional information. You will get an email update soon. In the meantime, explore our platform with limited features.\",\"reject\":\"We regret to inform you that the Know Your Customer (KYC) information provided has been reviewed and unfortunately, it has not met our verification standards.\"}',NULL,'basic','','2024-04-25 06:40:29','2024-04-25 06:40:29'),
(64,'banner.content','{\"has_image\":\"1\",\"heading\":\"Instant eSIM Activation Connect Anytime, Anywhere!\",\"subheading\":\"Stay connected anywhere with global coverage, instant eSIM setup, and the best-value data plans.\",\"placeholder\":\"Where is your destination?\",\"banner_bg\":\"6808b5998c90b1745401241.png\",\"banner_image\":\"67fe50cfd1fe51744720079.png\"}',NULL,'basic','','2024-05-01 00:06:45','2025-07-07 00:11:33'),
(66,'register_disable.content','{\"has_image\":\"1\",\"heading\":\"Registration Currently Disabled\",\"subheading\":\"Page you are looking for doesn\'t exit or an other error occurred or temporarily unavailable.\",\"button_name\":\"Go to Home\",\"button_url\":\"#\",\"image\":\"663a0f20ecd0b1715080992.png\"}',NULL,'basic','','2024-05-07 05:23:12','2025-04-19 03:58:06'),
(67,'client.content','{\"title\":\"Trusted by 100,000+ Business\"}',NULL,'basic','','2025-04-15 06:50:56','2025-04-15 06:50:56'),
(68,'client.element','{\"has_image\":\"1\",\"image\":\"67fe56554ecf11744721493.png\"}',NULL,'basic','','2025-04-15 06:51:33','2025-04-15 06:51:33'),
(69,'client.element','{\"has_image\":\"1\",\"image\":\"67fe565c7bb671744721500.png\"}',NULL,'basic','','2025-04-15 06:51:40','2025-04-15 06:51:40'),
(70,'client.element','{\"has_image\":\"1\",\"image\":\"67fe5663b97d41744721507.png\"}',NULL,'basic','','2025-04-15 06:51:47','2025-04-15 06:51:47'),
(71,'client.element','{\"has_image\":\"1\",\"image\":\"67fe566a7dba91744721514.png\"}',NULL,'basic','','2025-04-15 06:51:54','2025-04-15 06:51:54'),
(72,'client.element','{\"has_image\":\"1\",\"image\":\"67fe567498e021744721524.png\"}',NULL,'basic','','2025-04-15 06:52:04','2025-04-15 06:52:04'),
(73,'client.element','{\"has_image\":\"1\",\"image\":\"67fe56801bc611744721536.png\"}',NULL,'basic','','2025-04-15 06:52:16','2025-04-15 06:52:16'),
(74,'about.element','{\"has_image\":\"1\",\"number\":\"250\",\"suffix\":\"k\",\"title\":\"Total eSIM\",\"icon\":\"6856bc87843a71750514823.png\"}',NULL,'basic','','2025-04-15 23:26:50','2025-07-06 23:59:29'),
(75,'about.element','{\"has_image\":\"1\",\"number\":\"566\",\"suffix\":\"k\",\"title\":\"SIM User\",\"icon\":\"6856bc7dce7e11750514813.png\"}',NULL,'basic','','2025-04-15 23:27:11','2025-07-06 23:59:22'),
(76,'about.element','{\"has_image\":\"1\",\"number\":\"130\",\"suffix\":\"+\",\"title\":\"Destinations\",\"icon\":\"6856bc76b3b231750514806.png\"}',NULL,'basic','','2025-04-15 23:27:54','2025-07-06 23:58:52'),
(77,'work_process.content','{\"subheading\":\"How It Works\",\"description\":\"If you want to purchase an eSIM plan, follow these steps to activate it.\"}',NULL,'basic','','2025-04-15 23:37:12','2025-07-07 00:52:34'),
(78,'work_process.element','{\"title\":\"Choose an eSIM destination for your trip\",\"short_description\":\"Choose an eSIM destination for your trip and instantly access affordable mobile data plans tailored to your travel needs\",\"icon\":\"<i class=\\\"las la-plane-departure\\\"><\\/i>\",\"has_image\":\"1\",\"image\":\"6857adbf618d21750576575.png\"}',NULL,'basic','','2025-04-15 23:37:34','2025-06-22 01:16:15'),
(79,'work_process.element','{\"title\":\"Purchase a Plan\",\"short_description\":\"Securely complete your payment for the eSIM and get instant access to your mobile data plan.\",\"icon\":\"<i class=\\\"lar la-credit-card\\\"><\\/i>\",\"has_image\":\"1\",\"image\":\"6857adb70c2721750576567.png\"}',NULL,'basic','','2025-04-15 23:37:48','2025-07-07 00:53:30'),
(80,'work_process.element','{\"title\":\"Scan the eSIM QR code for activated eSIM & enjoy\",\"short_description\":\"Scan the eSIM QR code to activate your plan instantly. Once activated, you\'re all set to enjoy seamless connectivity wherever you go.\",\"icon\":\"<i class=\\\"las la-qrcode\\\"><\\/i>\",\"has_image\":\"1\",\"image\":\"6857adac9e8851750576556.png\"}',NULL,'basic','','2025-04-15 23:38:01','2025-06-22 01:15:57'),
(81,'coverage.content','{\"has_image\":\"1\",\"subheading\":\"Our coverage area\",\"description\":\"Our eSIM service offers extensive coverage, ensuring uninterrupted connectivity in major regions and travel destinations.\",\"button_text\":\"See Our All Coverage Area\",\"button_url\":\"destination\",\"icon\":\"<i class=\\\"las la-chevron-circle-right\\\"><\\/i>\",\"bg_image\":\"67ff4d9c46a511744784796.png\"}',NULL,'basic','','2025-04-16 00:19:05','2025-07-03 03:25:48'),
(83,'plan.content','{\"heading\":\"PRICING PLAN\",\"subheading\":\"Choose your plan\",\"description\":\"Select the perfect eSIM plan for your needs. Enjoy instant activation\"}',NULL,'basic','','2025-04-16 00:46:08','2025-04-16 00:46:08'),
(84,'easier.content','{\"has_image\":\"1\",\"image_text\":\"Connect instanty\",\"heading\":\"EASIER\",\"subheading\":\"Connect instantly\",\"description\":\"\\\"Connect Instantly\\\" refers to the ability of an eSIM (embedded SIM) to activate and provide mobile connectivity immediately, without needing a physical SIM card. This feature allows users to switch networks, purchase data plans, and get online in just a few steps\\u2014usually by scanning a QR code\",\"image\":\"67ff55b6056841744786870.png\"}',NULL,'basic','','2025-04-16 01:01:09','2025-04-16 01:01:10'),
(85,'easier.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Excellent service\"}',NULL,'basic','','2025-04-16 01:01:52','2025-04-16 01:01:52'),
(86,'easier.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Business analysis.\"}',NULL,'basic','','2025-04-16 01:02:13','2025-04-16 01:02:13'),
(87,'easier.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Business growth\"}',NULL,'basic','','2025-04-16 01:02:33','2025-04-16 01:02:33'),
(88,'easier.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"24\\/7 customer support\"}',NULL,'basic','','2025-04-16 01:02:59','2025-04-16 01:02:59'),
(89,'pricing_plan.content','{\"has_image\":\"1\",\"heading\":\"BEST PRICING PLAN\",\"subheading\":\"Best pricing plan ever\",\"description\":\"Get the best eSIM rates with flexible data options.High-speed data at budget-friendly prices.Transparent pricing with no surprises.Choose the perfect plan for your needs.Enjoy seamless coverage without overspending.\",\"image\":\"67ff5c657d1201744788581.png\"}',NULL,'basic','','2025-04-16 01:29:41','2025-04-16 01:29:41'),
(90,'pricing_plan.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Excellent service\"}',NULL,'basic','','2025-04-16 01:30:03','2025-04-16 01:30:03'),
(91,'pricing_plan.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Business analysis.\"}',NULL,'basic','','2025-04-16 01:30:15','2025-04-16 01:30:15'),
(92,'pricing_plan.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Business growth\"}',NULL,'basic','','2025-04-16 01:30:28','2025-04-16 01:30:28'),
(93,'pricing_plan.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"24\\/7 customer support\"}',NULL,'basic','','2025-04-16 01:30:41','2025-04-16 01:30:41'),
(94,'money_back.content','{\"has_image\":\"1\",\"image_amount\":\"500.00\",\"image_text\":\"Money Back\",\"heading\":\"MONEY BACK\",\"subheading\":\"Industry-first money back guarantee\",\"description\":\"Not satisfied? Get a full refund, hassle-free!If our eSIM doesn\\u2019t work, we\\u2019ll refund your money.We stand by our service, no questions asked.Get a refund if your eSIM doesn\\u2019t activate as promised.Reliable service with a money-back guarantee.\",\"image\":\"67ff6c62e1da51744792674.png\"}',NULL,'basic','','2025-04-16 02:37:42','2025-04-16 02:37:55'),
(95,'money_back.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Excellent service\"}',NULL,'basic','','2025-04-16 02:38:14','2025-04-16 02:38:14'),
(96,'money_back.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Business analysis.\"}',NULL,'basic','','2025-04-16 02:38:26','2025-04-16 02:38:26'),
(97,'money_back.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"Business growth\"}',NULL,'basic','','2025-04-16 02:38:39','2025-04-16 02:38:39'),
(98,'money_back.element','{\"icon\":\"<i class=\\\"far fa-check-circle\\\"><\\/i>\",\"title\":\"24\\/7 customer support\"}',NULL,'basic','','2025-04-16 02:38:50','2025-04-16 02:38:50'),
(99,'testimonial.element','{\"has_image\":\"1\",\"rating\":\"5\",\"customer_name\":\"Martin Jonas\",\"customer_designation\":\"Head of marketing, Intel\",\"description\":\"I have good experience with the company. eSIM.me works flawlessly on several of my Motorola phones. Support is great.\",\"customer_image\":\"67ff70cbaa6081744793803.png\"}',NULL,'basic','','2025-04-16 02:56:43','2025-04-16 02:56:44'),
(100,'testimonial.element','{\"has_image\":\"1\",\"rating\":\"4\",\"customer_name\":\"Robert Fox\",\"customer_designation\":\"Head of marketing, Intel\",\"description\":\"I have good experience with the company. eSIM.me works flawlessly on several of my Motorola phones. Support is great.\",\"customer_image\":\"6869342c4e9bc1751725100.png\"}',NULL,'basic','','2025-04-16 02:57:25','2025-07-05 08:18:20'),
(101,'faq.content','{\"subheading\":\"Frequently Asked Questions\",\"description\":\"Here are some frequently asked questions (FAQs) about eSIM services.\"}',NULL,'basic','','2025-04-16 03:09:55','2025-07-07 00:38:39'),
(102,'faq.element','{\"question\":\"What is an eSIM?\",\"answer\":\"An eSIM is a digital SIM that lets you activate a mobile plan without a physical card.\"}',NULL,'basic','','2025-04-16 03:10:22','2025-07-07 00:39:41'),
(103,'faq.element','{\"question\":\"Which devices support eSIM?\",\"answer\":\"Most new smartphones, tablets, and smartwatches from top brands.\"}',NULL,'basic','','2025-04-16 03:11:04','2025-07-07 00:40:49'),
(104,'faq.element','{\"question\":\"How do I switch between eSIM profiles?\",\"answer\":\"Through your device\\u2019s settings under mobile\\/network options.\"}',NULL,'basic','','2025-04-16 03:11:37','2025-07-07 00:40:56'),
(105,'faq.element','{\"question\":\"Is eSIM more secure than a physical SIM?\",\"answer\":\"Yes, it\\u2019s harder to steal or clone.\"}',NULL,'basic','','2025-04-16 03:12:30','2025-07-07 00:41:08'),
(110,'blog.content','{\"subheading\":\"Our Latest Blogs\",\"description\":\"Stay updated with tips, news, and guides about eSIM and travel connectivity.\"}',NULL,'basic','','2025-04-16 04:02:20','2025-07-07 00:55:59'),
(111,'blog.element','{\"has_image\":[\"1\"],\"title\":\"GSMA\\u2019s eSIM LITE2 November 2019\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"margin-right:0px;margin-left:0px;padding:0px;font-size:16px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin-right:0px;margin-left:0px;padding:0px;font-size:20px;color:rgb(117,117,117);font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin-bottom:20px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin-right:0px;margin-left:0px;padding:0px 0px 24px;font-size:16px;color:rgb(117,117,117);\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin-bottom:8px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin-right:0px;margin-left:0px;padding:0px;font-size:16px;color:rgb(117,117,117);\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM.\\u00a0<\\/p><\\/div>\",\"image\":\"686b70785c4a01751871608.png\"}',NULL,'basic','gsmas-esim-lite2-november-2019','2025-04-16 04:04:30','2025-07-07 01:00:09'),
(112,'blog.element','{\"has_image\":[\"1\"],\"title\":\"Top Benefits of Using eSIM for Travelers\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"color:rgb(117,117,117);font-size:16px;margin-right:0px;margin-left:0px;padding:0px;font-family:Inter, sans-serif;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:20px;padding:0px;font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin-bottom:20px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px 0px 24px;\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin-bottom:8px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px;\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM.\\u00a0<\\/p><\\/div>\",\"image\":\"686b72e31d0b71751872227.png\"}',NULL,'basic','top-benefits-of-using-esim-for-travelers','2025-04-16 04:18:23','2025-07-07 01:10:28'),
(113,'blog.element','{\"has_image\":[\"1\"],\"title\":\"How to Set Up an eSIM on Your Smartphone\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"color:rgb(117,117,117);font-size:16px;margin-right:0px;margin-left:0px;padding:0px;font-family:Inter, sans-serif;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:20px;padding:0px;font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin-bottom:20px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px 0px 24px;\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin-bottom:8px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px;\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM.\\u00a0<\\/p><\\/div>\",\"image\":\"686b71090c13f1751871753.png\"}',NULL,'basic','how-to-set-up-an-esim-on-your-smartphone','2025-04-16 04:20:09','2025-07-07 01:02:38'),
(114,'footer.content','{\"description\":\"eSIM is a leading provider of eSIM services, offering seamless and hassle-free connectivity for travelers, digital nomads, and businesses worldwide. With our innovative eSIM technology, you can enjoy instant activation and coverage in multiple countries.\"}',NULL,'basic','','2025-04-16 04:40:37','2025-07-03 03:26:03'),
(115,'social_icon.element','{\"social_icon\":\"<i class=\\\"fab fa-facebook-f\\\"><\\/i>\",\"url\":\"https:\\/\\/www.facebook.com\\/\"}',NULL,'basic','','2025-04-16 04:42:30','2025-04-16 04:48:45'),
(116,'social_icon.element','{\"social_icon\":\"<i class=\\\"fa-brands fa-x-twitter\\\"><\\/i>\",\"url\":\"https:\\/\\/x.com\\/\"}',NULL,'basic','','2025-04-16 04:42:51','2025-04-16 04:49:04'),
(117,'social_icon.element','{\"social_icon\":\"<i class=\\\"fab fa-linkedin-in\\\"><\\/i>\",\"url\":\"https:\\/\\/www.linkedin.com\\/\"}',NULL,'basic','','2025-04-16 04:43:15','2025-04-16 04:48:34'),
(118,'social_icon.element','{\"social_icon\":\"<i class=\\\"fab fa-instagram\\\"><\\/i>\",\"url\":\"https:\\/\\/www.instagram.com\\/\"}',NULL,'basic','','2025-04-16 04:43:32','2025-04-16 04:43:32'),
(119,'login_register.content','{\"has_image\":\"1\",\"heading\":\"Welcome back\",\"login_subheading\":\"Sign in account\",\"register_subheading\":\"Sign up account\",\"login_short_description\":\"Sign in your account to get service.\",\"register_short_description\":\"Create an account to get started with our services.\",\"image\":\"6856917ac00361750503802.png\"}',NULL,'basic','','2025-04-19 02:22:25','2025-06-21 05:03:25'),
(120,'blog.element','{\"has_image\":[\"1\"],\"title\":\"eSIM vs. Physical SIM: Which Is Better\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"margin-top:0px;margin-right:0px;margin-bottom:1rem;margin-left:0px;padding:0px;font-size:16px;font-weight:400;color:rgb(117,117,117);font-family:Inter, sans-serif;font-style:normal;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-bottom:0.5rem;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin:0px;padding:0px;font-size:20px;font-weight:500;color:rgb(117,117,117);font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin:0px 0px 20px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin:0px;padding:0px 0px 24px;font-size:16px;font-weight:400;color:rgb(117,117,117);\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin:0px 0px 8px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin:0px;padding:0px;font-size:16px;font-weight:400;color:rgb(117,117,117);\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM. <br \\/><\\/p><\\/div><br \\/>\",\"image\":\"686b713abd1031751871802.png\"}',NULL,'basic','esim-vs-physical-sim-which-is-better','2025-04-19 04:34:44','2025-07-07 01:03:24'),
(121,'blog.element','{\"has_image\":[\"1\"],\"title\":\"How to Use eSIM When Traveling Abroad\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"margin-top:0px;margin-right:0px;margin-bottom:1rem;margin-left:0px;padding:0px;font-size:16px;font-weight:400;color:rgb(117,117,117);font-family:Inter, sans-serif;font-style:normal;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-bottom:0.5rem;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin:0px;padding:0px;font-size:20px;font-weight:500;color:rgb(117,117,117);font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin:0px 0px 20px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin:0px;padding:0px 0px 24px;font-size:16px;font-weight:400;color:rgb(117,117,117);\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin:0px 0px 8px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin:0px;padding:0px;font-size:16px;font-weight:400;color:rgb(117,117,117);\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM. <br \\/><\\/p><\\/div><br \\/>\",\"image\":\"686b7149f37a11751871817.png\"}',NULL,'basic','how-to-use-esim-when-traveling-abroad','2025-04-19 04:37:42','2025-07-07 01:24:19'),
(122,'blog.element','{\"has_image\":[\"1\"],\"title\":\"The Future of Mobile Connectivity Is eSIM\\u2014Here\\u2019s Why\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"margin-top:0px;margin-right:0px;margin-bottom:1rem;margin-left:0px;padding:0px;font-size:16px;font-weight:400;color:rgb(117,117,117);font-family:Inter, sans-serif;font-style:normal;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\">Lorem\\r\\n ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi\\r\\n cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, \\r\\nquibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis\\r\\n atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. \\r\\nImpedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis \\r\\natque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. \\r\\nImpedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur \\r\\nadipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam \\r\\nillum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet \\r\\nconsectetur adipisicing elit. Quis atque excepturi cupiditate soluta \\r\\nnisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-bottom:0.5rem;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin:0px;padding:0px;font-size:20px;font-weight:500;color:rgb(117,117,117);font-style:italic;\\\">\\u201cThe\\r\\n Internet is the first thing that humanity has built that humanity \\r\\ndoesn\\u2019t understand, the largest experiment in anarchy that we have \\r\\neverhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin:0px 0px 20px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin:0px;padding:0px 0px 24px;font-size:16px;font-weight:400;color:rgb(117,117,117);\\\">Customer\\r\\n Development has tremendous benefits for organizations as it\\u00a0delays \\r\\ninvesting significant resources into building solutions until there is a\\r\\n proven, justifiable need for them and an assurance that the product \\r\\nwill actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;font-size:16px;font-style:normal;font-weight:400;letter-spacing:normal;text-indent:0px;text-transform:none;word-spacing:0px;white-space:normal;text-decoration-thickness:initial;text-decoration-style:initial;text-decoration-color:initial;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin:0px 0px 8px;padding:0px;font-weight:700;line-height:1.3;color:rgb(33,33,33);font-size:24px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin:0px;padding:0px;font-size:16px;font-weight:400;color:rgb(117,117,117);\\\">Global\\r\\n network security can refer to\\u00a0a company that distributes security \\r\\nequipment or to the security of networks that span the globe.\\u00a0Global \\r\\nNetwork Security (GNS) is a distributor of major camera, access control,\\r\\n and cable manufacturers, both brand name and OEM. <br \\/><\\/p><\\/div><br \\/><br \\/>\",\"image\":\"686b716eeb7c11751871854.png\"}',NULL,'basic','the-future-of-mobile-connectivity-is-esimheres-why','2025-04-19 04:39:42','2025-07-07 01:04:16'),
(123,'plan_selection.content','{\"heading\":\"Choose your plan\",\"description\":\"Select the perfect plan that fits your needs and budget. Flexible options designed just for you\"}',NULL,'basic','','2025-04-22 05:49:15','2025-07-05 08:37:53'),
(124,'about_us_second.content','{\"has_image\":\"1\",\"subheading\":\"Connect Instantly\",\"description\":\"\\\"Connect Instantly\\\" refers to the ability of an eSIM (embedded SIM) to activate and provide mobile connectivity immediately, without needing a physical SIM card. This feature allows users to switch networks, purchase data plans, and get online in just a few steps\\u2014usually by scanning a QR code\",\"image\":\"68691833dad8c1751717939.png\"}',NULL,'basic','','2025-04-23 06:10:42','2025-07-07 00:14:23'),
(125,'about_us_second.element','{\"has_image\":\"1\",\"title\":\"Select Destination\",\"icon\":\"686b8365bdec61751876453.png\"}',NULL,'basic','','2025-04-23 06:11:04','2025-07-07 02:20:53'),
(126,'about_us_second.element','{\"has_image\":\"1\",\"title\":\"Purchase Plan\",\"icon\":\"686b838b3791a1751876491.png\"}',NULL,'basic','','2025-04-23 06:11:25','2025-07-07 02:21:31'),
(127,'about_us_second.element','{\"has_image\":\"1\",\"title\":\"Scan QR Code\",\"icon\":\"686b8392a82811751876498.png\"}',NULL,'basic','','2025-04-23 06:11:38','2025-07-07 02:21:38'),
(128,'about_us_second.element','{\"has_image\":\"1\",\"title\":\"Enjoy your data plan\",\"icon\":\"686b88ef39e011751877871.png\"}',NULL,'basic','','2025-04-23 06:11:50','2025-07-07 02:44:31'),
(129,'about_us_third.content','{\"has_image\":\"1\",\"subheading\":\"Best Pricing Plan Ever\",\"description\":\"Get the best eSIM rates with flexible data options. High-speed data at budget-friendly prices. Transparent pricing with no surprises. Choose the perfect plan for your needs. Enjoy seamless coverage without overspending.\",\"image\":\"685798801276e1750571136.png\"}',NULL,'basic','','2025-04-23 06:15:24','2025-07-07 00:22:52'),
(130,'about_us_third.element','{\"has_image\":\"1\",\"title\":\"Affordable Rates\",\"icon\":\"686b83d07a98e1751876560.png\"}',NULL,'basic','','2025-04-23 06:15:40','2025-07-07 02:22:40'),
(131,'about_us_third.element','{\"has_image\":\"1\",\"title\":\"No Hidden Fees\",\"icon\":\"686b83dea05cf1751876574.png\"}',NULL,'basic','','2025-04-23 06:15:57','2025-07-07 02:22:54'),
(132,'about_us_third.element','{\"has_image\":\"1\",\"title\":\"Flexible Packages\",\"icon\":\"686b83ed43fa91751876589.png\"}',NULL,'basic','','2025-04-23 06:16:12','2025-07-07 02:23:09'),
(133,'about_us_third.element','{\"has_image\":\"1\",\"title\":\"Instant Activation\",\"icon\":\"686b83fa8a0ef1751876602.png\"}',NULL,'basic','','2025-04-23 06:16:28','2025-07-07 02:23:22'),
(134,'about_us_fourth.element','{\"has_image\":\"1\",\"title\":\"Risk-Free Purchase\",\"icon\":\"686b8322b58701751876386.png\"}',NULL,'basic','','2025-04-23 06:18:48','2025-07-07 02:19:47'),
(135,'about_us_fourth.content','{\"has_image\":\"1\",\"subheading\":\"Money-Back Guarantee\",\"description\":\"Not satisfied? Get a full refund, hassle-free! If our eSIM doesn\\u2019t work, we\\u2019ll refund your money. We stand by our service, no questions asked. Get a refund if your eSIM doesn\\u2019t activate as promised. Reliable service with a money-back guarantee.\",\"image\":\"68579a005c8bb1750571520.png\"}',NULL,'basic','','2025-04-23 06:19:27','2025-07-07 00:28:29'),
(136,'about_us_fourth.element','{\"has_image\":\"1\",\"title\":\"Easy Refund Process\",\"icon\":\"686b833d688b61751876413.png\"}',NULL,'basic','','2025-04-23 06:19:51','2025-07-07 02:20:13'),
(137,'about_us_fourth.element','{\"has_image\":\"1\",\"title\":\"Fast Processing\",\"icon\":\"686b83461a2221751876422.png\"}',NULL,'basic','','2025-04-23 06:20:09','2025-07-07 02:20:22'),
(138,'about_us_fourth.element','{\"has_image\":\"1\",\"title\":\"Customer Support Help\",\"icon\":\"68579a34c3c3b1750571572.png\"}',NULL,'basic','','2025-04-23 06:20:23','2025-07-07 00:31:50'),
(139,'referral.content','{\"has_image\":\"1\",\"subheading\":\"Invite Friends & Get Rewarded!\",\"description\":\"Earn commission every time someone signs up through your referral link.\\r\\nThe more you share, the more you earn \\u2014 there\\u2019s no limit!\\r\\nYour friend benefits too with exclusive rewards or discounts.\\r\\nIt\\u2019s quick, easy, and totally free to join.\\r\\nStart sharing today and turn your connections into real income!\",\"image\":\"686b7bd0dd0081751874512.png\"}',NULL,'basic','','2025-04-24 00:10:01','2025-07-07 01:48:33'),
(140,'referral_process.content','{\"subheading\":\"How to Refer a Friend\",\"description\":\"Share your referral link and earn commission.\"}',NULL,'basic','','2025-04-24 00:31:36','2025-07-07 00:38:15'),
(141,'referral_process.element','{\"title\":\"Share your referral link with your friends.\",\"has_image\":\"1\",\"image\":\"686b6a58957da1751870040.png\"}',NULL,'basic','','2025-04-24 00:32:09','2025-07-07 00:34:36'),
(142,'referral_process.element','{\"title\":\"Your friend signs up and buys a plan.\",\"has_image\":\"1\",\"image\":\"6809dafb1cb281745476347.png\"}',NULL,'basic','','2025-04-24 00:32:27','2025-07-07 00:36:49'),
(143,'referral_process.element','{\"title\":\"You earn a referral commission.\",\"has_image\":\"1\",\"image\":\"6809db0abe0061745476362.png\"}',NULL,'basic','','2025-04-24 00:32:42','2025-07-07 00:36:58'),
(144,'service.element','{\"has_image\":\"1\",\"title\":\"Internet Service\",\"description\":\"A connection to the internet that allows users to access services.\",\"image\":\"6824818710b561747222919.png\"}',NULL,'basic','','2025-05-13 00:28:02','2025-06-25 04:37:00'),
(145,'service.element','{\"has_image\":\"1\",\"title\":\"Buy Internet Data\",\"description\":\"You could buy mobile data through your Settings app.\",\"image\":\"6824817b4868d1747222907.png\"}',NULL,'basic','','2025-05-13 00:29:22','2025-07-07 02:33:51'),
(146,'service.element','{\"has_image\":\"1\",\"title\":\"Video & Audio Call\",\"description\":\"Video & audio calling allows you to directly call one another.\",\"image\":\"6824816ba32c51747222891.png\"}',NULL,'basic','','2025-05-13 00:31:04','2025-07-07 02:33:38'),
(147,'service.element','{\"has_image\":\"1\",\"title\":\"Bill Payment\",\"description\":\"eSIMs can be used to access banking services and make payments.\",\"image\":\"6822fbc1c22f81747123137.png\"}',NULL,'basic','','2025-05-13 00:34:01','2025-07-07 02:33:26'),
(149,'testimonial.element','{\"has_image\":\"1\",\"rating\":\"5\",\"customer_name\":\"Audra Shepard\",\"customer_designation\":\"CEO\",\"description\":\"I have used their plans. They are very professional, and the service was top-notch.\",\"customer_image\":\"686934255638b1751725093.png\"}',NULL,'basic','','2025-07-03 03:20:43','2025-07-05 08:18:14'),
(150,'blog.element','{\"has_image\":[\"1\"],\"title\":\"How eSIM Is Changing the Way We Stay Connected\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"color:rgb(117,117,117);font-size:16px;margin-right:0px;margin-left:0px;padding:0px;font-family:Inter, sans-serif;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:20px;padding:0px;font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin-bottom:20px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px 0px 24px;\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin-bottom:8px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px;\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM.\\u00a0<\\/p><\\/div>\",\"image\":\"686b7268210a31751872104.png\"}',NULL,'basic','how-esim-is-changing-the-way-we-stay-connected','2025-07-07 01:08:24','2025-07-07 01:08:25'),
(151,'blog.element','{\"has_image\":[\"1\"],\"title\":\"Troubleshooting Common eSIM Activation Issues\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"color:rgb(117,117,117);font-size:16px;margin-right:0px;margin-left:0px;padding:0px;font-family:Inter, sans-serif;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:20px;padding:0px;font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin-bottom:20px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px 0px 24px;\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin-bottom:8px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px;\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM.\\u00a0<\\/p><\\/div>\",\"image\":\"686b72893d6b51751872137.png\"}',NULL,'basic','troubleshooting-common-esim-activation-issues','2025-07-07 01:08:57','2025-07-07 01:08:58'),
(152,'blog.element','{\"has_image\":[\"1\"],\"title\":\"Which Devices Support eSIM in 2025\",\"description\":\"<p class=\\\"blog-details__desc mb-3\\\" style=\\\"color:rgb(117,117,117);font-size:16px;margin-right:0px;margin-left:0px;padding:0px;font-family:Inter, sans-serif;\\\">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum orem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis atque excepturi cupiditate soluta nisi aperiam illum maxime dolorum. Impedit, quibusdam.<\\/p><div class=\\\"quote-text\\\" style=\\\"margin:0px;padding:20px;background-color:rgba(33,33,33,0.05);color:rgb(117,117,117);border-left:10px solid rgb(67,182,119);font-family:Inter, sans-serif;\\\"><div class=\\\"quote-image mb-2\\\" style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;padding:0px;\\\"><\\/div><p class=\\\"quote-text__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:20px;padding:0px;font-style:italic;\\\">\\u201cThe Internet is the first thing that humanity has built that humanity doesn\\u2019t understand, the largest experiment in anarchy that we have everhad. Cybersecurity is not just about protecting your devices. \\u201d<\\/p><\\/div><div class=\\\"sim-replacement\\\" style=\\\"margin:0px;padding:32px 0px 0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><div class=\\\"sim-replacement__items\\\" style=\\\"margin:0px;padding:0px;\\\"><h4 class=\\\"sim-replacement__title\\\" style=\\\"margin-bottom:20px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Why consumer development<\\/h4><p class=\\\"sim-replacement__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px 0px 24px;\\\">Customer Development has tremendous benefits for organizations as it\\u00a0delays investing significant resources into building solutions until there is a proven, justifiable need for them and an assurance that the product will actually benefit the customer.<\\/p><\\/div><\\/div><div class=\\\"blog-security\\\" style=\\\"margin:32px 0px 0px;padding:0px;color:rgb(117,117,117);font-family:Inter, sans-serif;\\\"><h4 class=\\\"blog-security__title\\\" style=\\\"margin-bottom:8px;font-weight:700;line-height:1.3;font-size:24px;color:rgb(33,33,33);padding:0px;font-family:Montserrat, sans-serif;\\\">Global network security<\\/h4><p class=\\\"blog-security__desc\\\" style=\\\"margin-right:0px;margin-left:0px;color:rgb(117,117,117);font-size:16px;padding:0px;\\\">Global network security can refer to\\u00a0a company that distributes security equipment or to the security of networks that span the globe.\\u00a0Global Network Security (GNS) is a distributor of major camera, access control, and cable manufacturers, both brand name and OEM.\\u00a0<\\/p><\\/div>\",\"image\":\"686b72c6b7e8c1751872198.png\"}',NULL,'basic','which-devices-support-esim-in-2025','2025-07-07 01:09:58','2025-07-07 01:10:00'),
(153,'policy_pages.element','{\"title\":\"Refund Policy\",\"details\":\"<p class=\\\"mb-1\\\">At eSIM, we are committed to providing high-quality eSIM services and ensuring customer satisfaction.\\r\\n    Please read our refund policy carefully before making a purchase.<\\/p>\\r\\n<h4 class=\\\"mb-0 mt-3\\\">Digital Product Nature<\\/h4>\\r\\n<p>eSIM products are digital, non-returnable, and non-tangible goods. Once an eSIM profile is delivered\\r\\n    or activated, it cannot be returned, exchanged, or reused, and no refunds will be\\r\\n    issued, except under the conditions outlined below.<\\/p>\\r\\n<h4 class=\\\"mb-0 mt-3\\\">Refund Eligibility<\\/h4>\\r\\n<p>You may be eligible for a refund under the following circumstances:<\\/p>\\r\\n<ul>\\r\\n    <li>\\r\\n        <p>The eSIM was not delivered to your registered email within the promised time (usually within\\r\\n            X minutes\\/hours).<\\/p>\\r\\n    <\\/li>\\r\\n    <li>\\r\\n        <p>You were unable to activate the eSIM due to a technical error on our side,\\r\\n            and our support team is unable to resolve the issue.<\\/p>\\r\\n    <\\/li>\\r\\n    <li>\\r\\n        <p>The plan purchased was incompatible with your device, and this was clearly not\\r\\n            stated on our website.<\\/p>\\r\\n    <\\/li>\\r\\n<\\/ul>\\r\\n    <p>Please note: Refunds will not be granted if the issue is due to:<\\/p>\\r\\n    <ul>\\r\\n        <li>\\r\\n            <p>Incorrect device compatibility (when it was already stated that your device is not supported)<\\/p>\\r\\n        <\\/li>\\r\\n        <li>\\r\\n            <p>Improper installation or user error<\\/p>\\r\\n        <\\/li>\\r\\n        <li>\\r\\n            <p>Unused data or partial usage<\\/p>\\r\\n        <\\/li>\\r\\n        <li>\\r\\n            <p>Change of mind after purchase<\\/p>\\r\\n        <\\/li>\\r\\n    <\\/ul>\\r\\n<h4 class=\\\"mb-0 mt-3\\\">Refund Process<\\/h4>\\r\\n<p>To request a refund, you must:<\\/p>\\r\\n<ul>\\r\\n    <li>\\r\\n        <p>Contact our support team within 48 hours of purchase.<\\/p>\\r\\n    <\\/li>\\r\\n    <li>\\r\\n        <p>Provide your order number and a clear explanation of the issue.<\\/p>\\r\\n    <\\/li>\\r\\n    <li>\\r\\n        <p>Cooperate with our technical team if troubleshooting is required.<\\/p>\\r\\n    <\\/li>\\r\\n<\\/ul>\\r\\n<p>All approved refunds will be processed within 5\\u201310 business days via the original payment method.\\r\\n<\\/p>\\r\\n<h4 class=\\\"mb-0 mt-3\\\">Contact Us<\\/h4>\\r\\n<p>If you have questions or need help with a refund request, please reach out to us.<\\/p>\"}',NULL,'basic','refund-policy','2025-07-07 02:26:07','2025-07-07 02:30:14'),
(154,'client.element','{\"has_image\":\"1\",\"image\":\"686b8b40aa4c61751878464.png\"}',NULL,'basic','','2025-07-07 02:54:24','2025-07-07 02:54:24'),
(155,'client.element','{\"has_image\":\"1\",\"image\":\"686b8b4461f321751878468.png\"}',NULL,'basic','','2025-07-07 02:54:28','2025-07-07 02:54:28'),
(156,'client.element','{\"has_image\":\"1\",\"image\":\"686b8b4a62f701751878474.png\"}',NULL,'basic','','2025-07-07 02:54:34','2025-07-07 02:54:34'),
(157,'client.element','{\"has_image\":\"1\",\"image\":\"686b8b5881aad1751878488.png\"}',NULL,'basic','','2025-07-07 02:54:48','2025-07-07 02:54:48');
/*!40000 ALTER TABLE `frontends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateway_currencies`
--

DROP TABLE IF EXISTS `gateway_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateway_currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `currency` varchar(40) DEFAULT NULL,
  `symbol` varchar(40) DEFAULT NULL,
  `method_code` int(11) DEFAULT NULL,
  `gateway_alias` varchar(40) DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `gateway_parameter` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateway_currencies`
--

LOCK TABLES `gateway_currencies` WRITE;
/*!40000 ALTER TABLE `gateway_currencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `gateway_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateways`
--

DROP TABLE IF EXISTS `gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateways` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int(10) unsigned NOT NULL DEFAULT 0,
  `code` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `alias` varchar(40) NOT NULL DEFAULT 'NULL',
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `gateway_parameters` text DEFAULT NULL,
  `supported_currencies` text DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` VALUES
(1,0,101,'Paypal','Paypal','663a38d7b455d1715091671.png',1,'{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:21:11'),
(2,0,102,'Perfect Money','PerfectMoney','663a3920e30a31715091744.png',1,'{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}','{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:22:24'),
(3,0,103,'Stripe Hosted','Stripe','663a39861cb9d1715091846.png',1,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:24:06'),
(4,0,104,'Skrill','Skrill','663a39494c4a91715091785.png',1,'{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:23:05'),
(5,0,105,'PayTM','Paytm','663a390f601191715091727.png',1,'{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}','{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:22:07'),
(6,0,106,'Payeer','Payeer','663a38c9e2e931715091657.png',1,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}',0,'{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}',NULL,'2019-09-14 13:14:22','2024-05-07 08:20:57'),
(7,0,107,'PayStack','Paystack','663a38fc814e91715091708.png',1,'{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\"}}','{\"USD\":\"USD\",\"NGN\":\"NGN\"}',0,'{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n',NULL,'2019-09-14 13:14:22','2024-05-07 08:21:48'),
(9,0,109,'Flutterwave','Flutterwave','663a36c2c34d61715091138.png',1,'{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}','{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:12:18'),
(10,0,110,'RazorPay','Razorpay','663a393a527831715091770.png',1,'{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}','{\"INR\":\"INR\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:22:50'),
(11,0,111,'Stripe Storefront','StripeJs','663a3995417171715091861.png',1,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:24:21'),
(12,0,112,'Instamojo','Instamojo','663a384d54a111715091533.png',1,'{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}','{\"INR\":\"INR\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:18:53'),
(13,0,501,'Blockchain','Blockchain','663a35efd0c311715090927.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}','{\"BTC\":\"BTC\"}',1,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:08:47'),
(15,0,503,'CoinPayments','Coinpayments','663a36a8d8e1d1715091112.png',1,'{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"---------------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"---------------------\"}}','{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"USDT.BEP20\":\"Tether USD (BSC Chain)\",\"USDT.ERC20\":\"Tether USD (ERC20)\",\"USDT.TRC20\":\"Tether USD (Tron/TRC20)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}',1,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:11:52'),
(16,0,504,'CoinPayments Fiat','CoinpaymentsFiat','663a36b7b841a1715091127.png',1,'{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:12:07'),
(17,0,505,'Coingate','Coingate','663a368e753381715091086.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:11:26'),
(18,0,506,'Coinbase Commerce','CoinbaseCommerce','663a367e46ae51715091070.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n',0,'{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}',NULL,'2019-09-14 13:14:22','2024-05-07 08:11:10'),
(24,0,113,'Paypal Express','PaypalSdk','663a38ed101a61715091693.png',1,'{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}','{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:21:33'),
(25,0,114,'Stripe Checkout','StripeV3','663a39afb519f1715091887.png',1,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}','{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}',0,'{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}',NULL,'2019-09-14 13:14:22','2024-05-07 08:24:47'),
(27,0,115,'Mollie','Mollie','663a387ec69371715091582.png',1,'{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}','{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,'2019-09-14 13:14:22','2024-05-07 08:19:42'),
(30,0,116,'Cashmaal','Cashmaal','663a361b16bd11715090971.png',1,'{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}','{\"PKR\":\"PKR\",\"USD\":\"USD\"}',0,'{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}',NULL,NULL,'2024-05-07 08:09:31'),
(36,0,119,'Mercado Pago','MercadoPago','663a386c714a91715091564.png',1,'{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"APP_USR-7924565816849832-082312-21941521997fab717db925cf1ea2c190-1071840315\"}}','{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\",\"ARS\":\"ARS\",\"BRL\":\"BRL\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"MXN\":\"MXN\",\"PEN\":\"PEN\",\"UYU\":\"UYU\",\"VEF\":\"VEF\",\"BOB\":\"BOB\"}',0,NULL,NULL,NULL,'2024-05-07 08:19:24'),
(37,0,120,'Authorize.net','Authorize','663a35b9ca5991715090873.png',1,'{\"login_id\":{\"title\":\"Login ID\",\"global\":true,\"value\":\"59e4P9DBcZv\"},\"transaction_key\":{\"title\":\"Transaction Key\",\"global\":true,\"value\":\"47x47TJyLw2E7DbR\"}}','{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,'2024-05-07 08:07:53'),
(46,0,121,'NMI','NMI','663a3897754cf1715091607.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"2F822Rw39fx762MaV7Yy86jXGTC7sCDy\"}}','{\"AED\":\"AED\",\"ARS\":\"ARS\",\"AUD\":\"AUD\",\"BOB\":\"BOB\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"RUB\":\"RUB\",\"SEC\":\"SEC\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}',0,NULL,NULL,NULL,'2024-05-07 08:20:07'),
(50,0,507,'BTCPay','BTCPay','663a35cd25a8d1715090893.png',1,'{\"store_id\":{\"title\":\"Store Id\",\"global\":true,\"value\":\"HsqFVTXSeUFJu7caoYZc3CTnP8g5LErVdHhEXPVTheHf\"},\"api_key\":{\"title\":\"Api Key\",\"global\":true,\"value\":\"4436bd706f99efae69305e7c4eff4780de1335ce\"},\"server_name\":{\"title\":\"Server Name\",\"global\":true,\"value\":\"https:\\/\\/testnet.demo.btcpayserver.org\"},\"secret_code\":{\"title\":\"Secret Code\",\"global\":true,\"value\":\"SUCdqPn9CDkY7RmJHfpQVHP2Lf2\"}}','{\"BTC\":\"Bitcoin\",\"LTC\":\"Litecoin\"}',1,'{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.BTCPay\"}}',NULL,NULL,'2024-05-07 08:08:13'),
(51,0,508,'Now payments hosted','NowPaymentsHosted','663a38b8d57a81715091640.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"--------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------------\"}}','{\"BTG\":\"BTG\",\"ETH\":\"ETH\",\"XMR\":\"XMR\",\"ZEC\":\"ZEC\",\"XVG\":\"XVG\",\"ADA\":\"ADA\",\"LTC\":\"LTC\",\"BCH\":\"BCH\",\"QTUM\":\"QTUM\",\"DASH\":\"DASH\",\"XLM\":\"XLM\",\"XRP\":\"XRP\",\"XEM\":\"XEM\",\"DGB\":\"DGB\",\"LSK\":\"LSK\",\"DOGE\":\"DOGE\",\"TRX\":\"TRX\",\"KMD\":\"KMD\",\"REP\":\"REP\",\"BAT\":\"BAT\",\"ARK\":\"ARK\",\"WAVES\":\"WAVES\",\"BNB\":\"BNB\",\"XZC\":\"XZC\",\"NANO\":\"NANO\",\"TUSD\":\"TUSD\",\"VET\":\"VET\",\"ZEN\":\"ZEN\",\"GRS\":\"GRS\",\"FUN\":\"FUN\",\"NEO\":\"NEO\",\"GAS\":\"GAS\",\"PAX\":\"PAX\",\"USDC\":\"USDC\",\"ONT\":\"ONT\",\"XTZ\":\"XTZ\",\"LINK\":\"LINK\",\"RVN\":\"RVN\",\"BNBMAINNET\":\"BNBMAINNET\",\"ZIL\":\"ZIL\",\"BCD\":\"BCD\",\"USDT\":\"USDT\",\"USDTERC20\":\"USDTERC20\",\"CRO\":\"CRO\",\"DAI\":\"DAI\",\"HT\":\"HT\",\"WABI\":\"WABI\",\"BUSD\":\"BUSD\",\"ALGO\":\"ALGO\",\"USDTTRC20\":\"USDTTRC20\",\"GT\":\"GT\",\"STPT\":\"STPT\",\"AVA\":\"AVA\",\"SXP\":\"SXP\",\"UNI\":\"UNI\",\"OKB\":\"OKB\",\"BTC\":\"BTC\"}',1,'',NULL,NULL,'2024-05-07 08:20:40'),
(52,0,509,'Now payments checkout','NowPaymentsCheckout','663a38a59d2541715091621.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"---------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\"}',1,'',NULL,NULL,'2024-05-07 08:20:21'),
(53,0,122,'2Checkout','TwoCheckout','663a39b8e64b91715091896.png',1,'{\"merchant_code\":{\"title\":\"Merchant Code\",\"global\":true,\"value\":\"253248016872\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"eQM)ID@&vG84u!O*g[p+\"}}','{\"AFN\": \"AFN\",\"ALL\": \"ALL\",\"DZD\": \"DZD\",\"ARS\": \"ARS\",\"AUD\": \"AUD\",\"AZN\": \"AZN\",\"BSD\": \"BSD\",\"BDT\": \"BDT\",\"BBD\": \"BBD\",\"BZD\": \"BZD\",\"BMD\": \"BMD\",\"BOB\": \"BOB\",\"BWP\": \"BWP\",\"BRL\": \"BRL\",\"GBP\": \"GBP\",\"BND\": \"BND\",\"BGN\": \"BGN\",\"CAD\": \"CAD\",\"CLP\": \"CLP\",\"CNY\": \"CNY\",\"COP\": \"COP\",\"CRC\": \"CRC\",\"HRK\": \"HRK\",\"CZK\": \"CZK\",\"DKK\": \"DKK\",\"DOP\": \"DOP\",\"XCD\": \"XCD\",\"EGP\": \"EGP\",\"EUR\": \"EUR\",\"FJD\": \"FJD\",\"GTQ\": \"GTQ\",\"HKD\": \"HKD\",\"HNL\": \"HNL\",\"HUF\": \"HUF\",\"INR\": \"INR\",\"IDR\": \"IDR\",\"ILS\": \"ILS\",\"JMD\": \"JMD\",\"JPY\": \"JPY\",\"KZT\": \"KZT\",\"KES\": \"KES\",\"LAK\": \"LAK\",\"MMK\": \"MMK\",\"LBP\": \"LBP\",\"LRD\": \"LRD\",\"MOP\": \"MOP\",\"MYR\": \"MYR\",\"MVR\": \"MVR\",\"MRO\": \"MRO\",\"MUR\": \"MUR\",\"MXN\": \"MXN\",\"MAD\": \"MAD\",\"NPR\": \"NPR\",\"TWD\": \"TWD\",\"NZD\": \"NZD\",\"NIO\": \"NIO\",\"NOK\": \"NOK\",\"PKR\": \"PKR\",\"PGK\": \"PGK\",\"PEN\": \"PEN\",\"PHP\": \"PHP\",\"PLN\": \"PLN\",\"QAR\": \"QAR\",\"RON\": \"RON\",\"RUB\": \"RUB\",\"WST\": \"WST\",\"SAR\": \"SAR\",\"SCR\": \"SCR\",\"SGD\": \"SGD\",\"SBD\": \"SBD\",\"ZAR\": \"ZAR\",\"KRW\": \"KRW\",\"LKR\": \"LKR\",\"SEK\": \"SEK\",\"CHF\": \"CHF\",\"SYP\": \"SYP\",\"THB\": \"THB\",\"TOP\": \"TOP\",\"TTD\": \"TTD\",\"TRY\": \"TRY\",\"UAH\": \"UAH\",\"AED\": \"AED\",\"USD\": \"USD\",\"VUV\": \"VUV\",\"VND\": \"VND\",\"XOF\": \"XOF\",\"YER\": \"YER\"}',0,'{\"approved_url\":{\"title\": \"Approved URL\",\"value\":\"ipn.TwoCheckout\"}}',NULL,NULL,'2024-05-07 08:24:56'),
(54,0,123,'Checkout','Checkout','663a3628733351715090984.png',1,'{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"------\"},\"public_key\":{\"title\":\"PUBLIC KEY\",\"global\":true,\"value\":\"------\"},\"processing_channel_id\":{\"title\":\"PROCESSING CHANNEL\",\"global\":true,\"value\":\"------\"}}','{\"USD\":\"USD\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"AUD\":\"AUD\",\"CAN\":\"CAN\",\"CHF\":\"CHF\",\"SGD\":\"SGD\",\"JPY\":\"JPY\",\"NZD\":\"NZD\"}',0,NULL,NULL,NULL,'2024-05-07 08:09:44'),
(56,0,510,'Binance','Binance','663a35db4fd621715090907.png',1,'{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"tsu3tjiq0oqfbtmlbevoeraxhfbp3brejnm9txhjxcp4to29ujvakvfl1ibsn3ja\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"jzngq4t04ltw8d4iqpi7admfl8tvnpehxnmi34id1zvfaenbwwvsvw7llw3zdko8\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"231129033\"}}','{\"BTC\":\"Bitcoin\",\"USD\":\"USD\",\"BNB\":\"BNB\"}',1,'{\"cron\":{\"title\": \"Cron Job URL\",\"value\":\"ipn.Binance\"}}',NULL,NULL,'2024-05-07 08:08:27'),
(57,0,124,'SslCommerz','SslCommerz','663a397a70c571715091834.png',1,'{\"store_id\":{\"title\":\"Store ID\",\"global\":true,\"value\":\"---------\"},\"store_password\":{\"title\":\"Store Password\",\"global\":true,\"value\":\"----------\"}}','{\"BDT\":\"BDT\",\"USD\":\"USD\",\"EUR\":\"EUR\",\"SGD\":\"SGD\",\"INR\":\"INR\",\"MYR\":\"MYR\"}',0,NULL,NULL,NULL,'2024-05-07 08:23:54'),
(58,0,125,'Aamarpay','Aamarpay','663a34d5d1dfc1715090645.png',1,'{\"store_id\":{\"title\":\"Store ID\",\"global\":true,\"value\":\"---------\"},\"signature_key\":{\"title\":\"Signature Key\",\"global\":true,\"value\":\"----------\"}}','{\"BDT\":\"BDT\"}',0,NULL,NULL,NULL,'2024-05-07 08:04:05'),
(60,0,126,'bKash','BKash','67e1432683b5a1742816038.png',1,'{\"username\":{\"title\":\"Username\",\"global\":true,\"value\":\"01770618567\"},\"password\":{\"title\":\"Password\",\"global\":true,\"value\":\"D7DaC<*E*eG\"},\"app_key\":{\"title\":\"App Key\",\"global\":true,\"value\":\"0vWQuCRGiUX7EPVjQDr0EUAYtc\"},\"app_secret\":{\"title\":\"App Secret\",\"global\":true,\"value\":\"jcUNPBgbcqEDedNKdvE4G1cAK7D3hCjmJccNPZZBq96QIxxwAMEx\"}}','{\"BDT\":\"BDT\"}',0,NULL,NULL,NULL,'2025-03-24 05:33:58');
/*!40000 ALTER TABLE `gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(40) DEFAULT NULL,
  `cur_text` varchar(40) DEFAULT NULL COMMENT 'currency text',
  `cur_sym` varchar(40) DEFAULT NULL COMMENT 'currency symbol',
  `payment_cur_text` varchar(40) DEFAULT NULL COMMENT 'payment currency text',
  `email_from` varchar(40) DEFAULT NULL,
  `email_from_name` varchar(255) DEFAULT NULL,
  `email_template` text DEFAULT NULL,
  `sms_template` varchar(255) DEFAULT NULL,
  `sms_from` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `push_template` varchar(255) DEFAULT NULL,
  `plan_api` text DEFAULT NULL,
  `currency_api_key` varchar(255) DEFAULT NULL,
  `base_color` varchar(40) DEFAULT NULL,
  `secondary_color` varchar(40) DEFAULT NULL,
  `mail_config` text DEFAULT NULL COMMENT 'email configuration',
  `sms_config` text DEFAULT NULL,
  `firebase_config` text DEFAULT NULL,
  `global_shortcodes` text DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email verification, 0 - dont check, 1 - check',
  `en` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email notification, 0 - dont send, 1 - send',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'mobile verication, 0 - dont check, 1 - check',
  `sn` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sms notification, 0 - dont send, 1 - send',
  `pn` tinyint(1) NOT NULL DEFAULT 1,
  `force_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `in_app_payment` tinyint(1) NOT NULL DEFAULT 1,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `secure_password` tinyint(1) NOT NULL DEFAULT 0,
  `agree` tinyint(1) NOT NULL DEFAULT 0,
  `multi_language` tinyint(1) NOT NULL DEFAULT 1,
  `registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Off	, 1: On',
  `referral_amount` decimal(28,8) DEFAULT NULL,
  `active_template` varchar(40) DEFAULT NULL,
  `socialite_credentials` text DEFAULT NULL,
  `last_cron` datetime DEFAULT NULL,
  `available_version` varchar(40) DEFAULT NULL,
  `system_customized` tinyint(1) NOT NULL DEFAULT 0,
  `paginate_number` int(11) NOT NULL DEFAULT 0,
  `currency_format` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>Both\r\n2=>Text Only\r\n3=>Symbol Only',
  `config_progress` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_settings`
--

LOCK TABLES `general_settings` WRITE;
/*!40000 ALTER TABLE `general_settings` DISABLE KEYS */;
INSERT INTO `general_settings` VALUES
(1,'eSIM','USD','$','THB','info@viserlab.com','{{site_name}}','<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n  <!--[if !mso]><!-->\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n  <!--<![endif]-->\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n  <title></title>\n  <style type=\"text/css\">\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\n.ExternalClass { width: 100%; background-color: #ffffff; }\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\nhtml { width: 100%; }\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\ntable table table { table-layout: auto; }\n.yshortcuts a { border-bottom: none !important; }\nimg:hover { opacity: 0.9 !important; }\na { color: #0087ff; text-decoration: none; }\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\n.btn-link a { color:#FFFFFF !important;}\n\n@media only screen and (max-width: 480px) {\nbody { width: auto !important; }\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\n/* image */\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\n}\n</style>\n\n\n\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n    <tbody><tr>\n      <td height=\"50\"></td>\n    </tr>\n    <tr>\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n          <tbody><tr>\n            <td align=\"center\" width=\"600\">\n              <!--header-->\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n                <tbody><tr>\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n                      <tbody><tr>\n                        <td height=\"20\"></td>\n                      </tr>\n                      <tr>\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\n                      </tr>\n                      <tr>\n                        <td height=\"20\"></td>\n                      </tr>\n                    </tbody></table>\n                  </td>\n                </tr>\n              </tbody></table>\n              <!--end header-->\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n                <tbody><tr>\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n                      <tbody><tr>\n                        <td height=\"35\"></td>\n                      </tr>\n                      <!--logo-->\n                      <tr>\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\n                          <a href=\"#\">\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://script.viserlab.com/apps/cdn/demo-logo.png\" width=\"220\" alt=\"img\">\n                          </a>\n                        </td>\n                      </tr>\n                      <!--end logo-->\n                      <tr>\n                        <td height=\"40\"></td>\n                      </tr>\n                      <!--headline-->\n                      <tr>\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\n                      </tr>\n                      <!--end headline-->\n                      <tr>\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n                            <tbody><tr>\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\n                            </tr>\n                          </tbody></table>\n                        </td>\n                      </tr>\n                      <tr>\n                        <td height=\"20\"></td>\n                      </tr>\n                      <!--content-->\n                      <tr>\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\n                      </tr>\n                      <!--end content-->\n                      <tr>\n                        <td height=\"40\"></td>\n                      </tr>\n              \n                    </tbody></table>\n                  </td>\n                </tr>\n                <tr>\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n                      <tbody><tr>\n                        <td height=\"10\"></td>\n                      </tr>\n                      <!--preference-->\n                      <tr>\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\n                          © 2024 <a href=\"#\">{{site_name}}</a>&nbsp;. All Rights Reserved. \n                        </td>\n                      </tr>\n                      <!--end preference-->\n                      <tr>\n                        <td height=\"10\"></td>\n                      </tr>\n                    </tbody></table>\n                  </td>\n                </tr>\n              </tbody></table>\n            </td>\n          </tr>\n        </tbody></table>\n      </td>\n    </tr>\n    <tr>\n      <td height=\"60\"></td>\n    </tr>\n  </tbody></table>','hi {{fullname}} ({{username}}), {{message}}','{{site_name}}','{{site_name}}','hi {{fullname}} ({{username}}), {{message}}','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzaWQiOiJmZjQ5YTE3My0wN2IxLTQ5MzgtYjlhNy1mNmE4MmE5NDQ2ZmMiLCJpYXQiOjE3NDU3NTM3OTYsImV4cCI6MjYwOTY2NzM5Nn0.J1Sw_FUDUaxHWOwdp_YiAlTPC9oSnMU8X6ED26izaVY','ab6e956ba8e686235dfe876a4b5409f1','44B678','063862','{\"name\":\"php\"}','{\"name\":\"clickatell\",\"clickatell\":{\"api_key\":\"----------------\"},\"infobip\":{\"username\":\"------------8888888\",\"password\":\"-----------------\"},\"message_bird\":{\"api_key\":\"-------------------\"},\"nexmo\":{\"api_key\":\"----------------------\",\"api_secret\":\"----------------------\"},\"sms_broadcast\":{\"username\":\"----------------------\",\"password\":\"-----------------------------\"},\"twilio\":{\"account_sid\":\"-----------------------\",\"auth_token\":\"---------------------------\",\"from\":\"----------------------\"},\"text_magic\":{\"username\":\"-----------------------\",\"apiv2_key\":\"-------------------------------\"},\"custom\":{\"method\":\"get\",\"url\":\"https:\\/\\/hostname.com\\/demo-api-v1\",\"headers\":{\"name\":[\"api_key\"],\"value\":[\"test_api 555\"]},\"body\":{\"name\":[\"from_number\"],\"value\":[\"5657545757\"]}}}','{\"apiKey\":\"AIzaSyCmTNRy6QAvgWouAPukcupnrgysv0cQyJE\",\"authDomain\":\"laramin-99210.firebaseapp.com\",\"projectId\":\"laramin-99210\",\"storageBucket\":\"laramin-99210.firebasestorage.app\",\"messagingSenderId\":\"118330915856\",\"appId\":\"1:118330915856:web:be838cf07f656a9f44de95\",\"measurementId\":\"G-MWPV2VED4W\"}','{\n    \"site_name\":\"Name of your site\",\n    \"site_currency\":\"Currency of your site\",\n    \"currency_symbol\":\"Symbol of currency\"\n}',0,1,1,0,1,1,0,0,0,0,1,1,1,5.00000000,'basic','{\"google\":{\"client_id\":\"------------\",\"client_secret\":\"-------------\",\"status\":1},\"facebook\":{\"client_id\":\"------\",\"client_secret\":\"------\",\"status\":1},\"linkedin\":{\"client_id\":\"-----\",\"client_secret\":\"-----\",\"status\":1}}','2025-07-03 10:10:24','0',0,20,1,'[\"general_setting\", \"logo_favicon\", \"notification_template\", \"deposit_method\", \"seo\", \"region\", \"country\", \"plan\", \"api_keys\", \"policy_content\"]',NULL,'2025-07-05 06:37:45');
/*!40000 ALTER TABLE `general_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `code` varchar(40) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: not default language, 1: default language',
  `image` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES
(1,'English','en',1,'660b94fa876ac1712035066.png','2020-07-06 03:47:55','2024-04-01 23:17:46');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2014_10_12_000000_create_users_table',1),
(2,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2020_06_14_061757_create_support_tickets_table',3),
(5,'2020_06_14_061837_create_support_messages_table',3),
(6,'2020_06_14_061904_create_support_attachments_table',3),
(7,'2020_06_14_062359_create_admins_table',3),
(8,'2020_06_14_064604_create_transactions_table',4),
(9,'2020_06_14_065247_create_general_settings_table',5),
(12,'2014_10_12_100000_create_password_resets_table',6),
(13,'2020_06_14_060541_create_user_logins_table',6),
(14,'2020_06_14_071708_create_admin_password_resets_table',7),
(15,'2020_09_14_053026_create_countries_table',8),
(16,'2021_03_15_084721_create_admin_notifications_table',9),
(17,'2016_06_01_000001_create_oauth_auth_codes_table',10),
(18,'2016_06_01_000002_create_oauth_access_tokens_table',10),
(19,'2016_06_01_000003_create_oauth_refresh_tokens_table',10),
(20,'2016_06_01_000004_create_oauth_clients_table',10),
(21,'2016_06_01_000005_create_oauth_personal_access_clients_table',10),
(22,'2021_05_08_103925_create_sms_gateways_table',11),
(23,'2019_12_14_000001_create_personal_access_tokens_table',12),
(24,'2021_05_23_111859_create_email_logs_table',13),
(25,'2022_02_26_061836_create_forms_table',14),
(26,'2023_06_15_144908_create_update_logs_table',15),
(27,'2025_04_14_081045_create_plans_table',16),
(28,'2025_04_14_103714_create_locations_table',17),
(29,'2025_04_17_104354_create_plans_table',18),
(30,'2025_04_17_112141_create_countries_table',19),
(31,'2025_04_21_065534_create_plan_countries_table',20),
(32,'2025_04_22_054112_create_apis_table',21),
(33,'2025_04_26_084805_create_purchased_plans_table',22),
(34,'2025_04_28_063102_create_currencies_table',23),
(35,'2025_04_28_084605_create_currency_apis_table',24),
(36,'2025_04_29_071749_create_regions_table',25),
(37,'2025_05_04_105020_create_orders_table',26),
(38,'2025_05_04_105115_create_order_items_table',26),
(39,'2025_05_07_092256_create_esims_table',27);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `sender` varchar(40) DEFAULT NULL,
  `sent_from` varchar(40) DEFAULT NULL,
  `sent_to` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `notification_type` varchar(40) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_read` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(40) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `push_title` varchar(255) DEFAULT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `push_body` text DEFAULT NULL,
  `shortcodes` text DEFAULT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `email_sent_from_name` varchar(40) DEFAULT NULL,
  `email_sent_from_address` varchar(40) DEFAULT NULL,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_sent_from` varchar(40) DEFAULT NULL,
  `push_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_templates`
--

LOCK TABLES `notification_templates` WRITE;
/*!40000 ALTER TABLE `notification_templates` DISABLE KEYS */;
INSERT INTO `notification_templates` VALUES
(1,'BAL_ADD','Balance - Added','Your Account has been Credited','{{site_name}} - Balance Added','<div>We\'re writing to inform you that an amount of {{amount}} {{site_currency}} has been successfully added to your account.</div><div><br></div><div>Here are the details of the transaction:</div><div><br></div><div><b>Transaction Number: </b>{{trx}}</div><div><b>Current Balance:</b> {{post_balance}} {{site_currency}}</div><div><b>Admin Note:</b> {{remark}}</div><div><br></div><div>If you have any questions or require further assistance, please don\'t hesitate to contact us. We\'re here to assist you.</div>','We\'re writing to inform you that an amount of {{amount}} {{site_currency}} has been successfully added to your account.','{{amount}} {{site_currency}} has been successfully added to your account.','{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}',1,'{{site_name}} Finance',NULL,0,NULL,1,'2021-11-03 12:00:00','2025-03-06 03:59:29'),
(2,'BAL_SUB','Balance - Subtracted','Your Account has been Debited','{{site_name}} - Balance Subtracted','<div>We wish to inform you that an amount of {{amount}} {{site_currency}} has been successfully deducted from your account.</div><div><br></div><div>Below are the details of the transaction:</div><div><br></div><div><b>Transaction Number:</b> {{trx}}</div><div><b>Current Balance: </b>{{post_balance}} {{site_currency}}</div><div><b>Admin Note:</b> {{remark}}</div><div><br></div><div>Should you require any further clarification or assistance, please do not hesitate to reach out to us. We are here to assist you in any way we can.</div><div><br></div><div>Thank you for your continued trust in {{site_name}}.</div>','We wish to inform you that an amount of {{amount}} {{site_currency}} has been successfully deducted from your account.','{{amount}} {{site_currency}} debited from your account.','{\"trx\":\"Transaction number for the action\",\"amount\":\"Amount inserted by the admin\",\"remark\":\"Remark inserted by the admin\",\"post_balance\":\"Balance of the user after this transaction\"}',1,'{{site_name}} Finance',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:17:48'),
(3,'DEPOSIT_COMPLETE','Deposit - Automated - Successful','Deposit Completed Successfully','{{site_name}} - Deposit successful','<div>We\'re delighted to inform you that your deposit of {{amount}} {{site_currency}} via {{method_name}} has been completed.</div><div><br></div><div>Below, you\'ll find the details of your deposit:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge: </b>{{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Received:</b> {{method_amount}} {{method_currency}}</div><div><b>Paid via:</b> {{method_name}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><div>Your current balance stands at {{post_balance}} {{site_currency}}.</div><div><br></div><div>If you have any questions or need further assistance, feel free to reach out to our support team. We\'re here to assist you in any way we can.</div>','We\'re delighted to inform you that your deposit of {{amount}} {{site_currency}} via {{method_name}} has been completed.','Deposit Completed Successfully','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}',1,'{{site_name}} Billing',NULL,1,NULL,1,'2021-11-03 12:00:00','2024-05-08 07:20:34'),
(4,'DEPOSIT_APPROVE','Deposit - Manual - Approved','Deposit Request Approved','{{site_name}} - Deposit Request Approved','<div>We are pleased to inform you that your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been approved.</div><div><br></div><div>Here are the details of your deposit:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge: </b>{{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Received: </b>{{method_amount}} {{method_currency}}</div><div><b>Paid via: </b>{{method_name}}</div><div><b>Transaction Number: </b>{{trx}}</div><div><br></div><div>Your current balance now stands at {{post_balance}} {{site_currency}}.</div><div><br></div><div>Should you have any questions or require further assistance, please feel free to contact our support team. We\'re here to help.</div>','We are pleased to inform you that your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been approved.','Deposit of {{amount}} {{site_currency}} via {{method_name}} has been approved.','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after this transaction\"}',1,'{{site_name}} Billing',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:19:49'),
(5,'DEPOSIT_REJECT','Deposit - Manual - Rejected','Deposit Request Rejected','{{site_name}} - Deposit Request Rejected','<div>We regret to inform you that your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been rejected.</div><div><br></div><div>Here are the details of the rejected deposit:</div><div><br></div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Received:</b> {{method_amount}} {{method_currency}}</div><div><b>Paid via:</b> {{method_name}}</div><div><b>Charge:</b> {{charge}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><div>If you have any questions or need further clarification, please don\'t hesitate to contact us. We\'re here to assist you.</div><div><br></div><div>Rejection Reason:</div><div>{{rejection_message}}</div><div><br></div><div>Thank you for your understanding.</div>','We regret to inform you that your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been rejected.','Your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been rejected.','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"rejection_message\":\"Rejection message by the admin\"}',1,'{{site_name}} Billing',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:20:13'),
(6,'DEPOSIT_REQUEST','Deposit - Manual - Requested','Deposit Request Submitted Successfully',NULL,'<div>We are pleased to confirm that your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been submitted successfully.</div><div><br></div><div>Below are the details of your deposit:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge:</b> {{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Payable:</b> {{method_amount}} {{method_currency}}</div><div><b>Pay via: </b>{{method_name}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><div>Should you have any questions or require further assistance, please feel free to reach out to our support team. We\'re here to assist you.</div>','We are pleased to confirm that your deposit request of {{amount}} {{site_currency}} via {{method_name}} has been submitted successfully.','Your deposit request of {{amount}} {{site_currency}} via {{method_name}} submitted successfully.','{\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}',1,'{{site_name}} Billing',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-04-25 03:27:42'),
(7,'PASS_RESET_CODE','Password - Reset - Code','Password Reset','{{site_name}} Password Reset Code','<div>We\'ve received a request to reset the password for your account on <b>{{time}}</b>. The request originated from\r\n            the following IP address: <b>{{ip}}</b>, using <b>{{browser}}</b> on <b>{{operating_system}}</b>.\r\n    </div><br>\r\n    <div><span>To proceed with the password reset, please use the following account recovery code</span>: <span><b><font size=\"6\">{{code}}</font></b></span></div><br>\r\n    <div><span>If you did not initiate this password reset request, please disregard this message. Your account security\r\n            remains our top priority, and we advise you to take appropriate action if you suspect any unauthorized\r\n            access to your account.</span></div>','To proceed with the password reset, please use the following account recovery code: {{code}}','To proceed with the password reset, please use the following account recovery code: {{code}}','{\"code\":\"Verification code for password reset\",\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}',1,'{{site_name}} Authentication Center',NULL,0,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:24:57'),
(8,'PASS_RESET_DONE','Password - Reset - Confirmation','Password Reset Successful',NULL,'<div><div><span>We are writing to inform you that the password reset for your account was successful. This action was completed at {{time}} from the following browser</span>: <span>{{browser}}</span><span>on {{operating_system}}, with the IP address</span>: <span>{{ip}}</span>.</div><br><div><span>Your account security is our utmost priority, and we are committed to ensuring the safety of your information. If you did not initiate this password reset or notice any suspicious activity on your account, please contact our support team immediately for further assistance.</span></div></div>','We are writing to inform you that the password reset for your account was successful.','We are writing to inform you that the password reset for your account was successful.','{\"ip\":\"IP address of the user\",\"browser\":\"Browser of the user\",\"operating_system\":\"Operating system of the user\",\"time\":\"Time of the request\"}',1,'{{site_name}} Authentication Center',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-04-25 03:27:24'),
(9,'ADMIN_SUPPORT_REPLY','Support - Reply','Re: {{ticket_subject}} - Ticket #{{ticket_id}}','{{site_name}} - Support Ticket Replied','<div>\r\n    <div><span>Thank you for reaching out to us regarding your support ticket with the subject</span>:\r\n        <span>\"{{ticket_subject}}\"&nbsp;</span><span>and ticket ID</span>: {{ticket_id}}.</div><br>\r\n    <div><span>We have carefully reviewed your inquiry, and we are pleased to provide you with the following\r\n            response</span><span>:</span></div><br>\r\n    <div>{{reply}}</div><br>\r\n    <div><span>If you have any further questions or need additional assistance, please feel free to reply by clicking on\r\n            the following link</span>: <a href=\"{{link}}\" title=\"\" target=\"_blank\">{{link}}</a><span>. This link will take you to\r\n            the ticket thread where you can provide further information or ask for clarification.</span></div><br>\r\n    <div><span>Thank you for your patience and cooperation as we worked to address your concerns.</span></div>\r\n</div>','Thank you for reaching out to us regarding your support ticket with the subject: \"{{ticket_subject}}\" and ticket ID: {{ticket_id}}. We have carefully reviewed your inquiry. To check the response, please go to the following link: {{link}}','Re: {{ticket_subject}} - Ticket #{{ticket_id}}','{\"ticket_id\":\"ID of the support ticket\",\"ticket_subject\":\"Subject  of the support ticket\",\"reply\":\"Reply made by the admin\",\"link\":\"URL to view the support ticket\"}',1,'{{site_name}} Support Team',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:26:06'),
(10,'EVER_CODE','Verification - Email','Email Verification Code',NULL,'<div>\r\n    <div><span>Thank you for taking the time to verify your email address with us. Your email verification code\r\n            is</span>: <b><font size=\"6\">{{code}}</font></b></div><br>\r\n    <div><span>Please enter this code in the designated field on our platform to complete the verification\r\n            process.</span></div><br>\r\n    <div><span>If you did not request this verification code, please disregard this email. Your account security is our\r\n            top priority, and we advise you to take appropriate measures if you suspect any unauthorized access.</span>\r\n    </div><br>\r\n    <div><span>If you have any questions or encounter any issues during the verification process, please don\'t hesitate\r\n            to contact our support team for assistance.</span></div><br>\r\n    <div><span>Thank you for choosing us.</span></div>\r\n</div>','---','---','{\"code\":\"Email verification code\"}',1,'{{site_name}} Verification Center',NULL,0,NULL,0,'2021-11-03 12:00:00','2024-04-25 03:27:12'),
(11,'SVER_CODE','Verification - SMS','Verify Your Mobile Number',NULL,'---','Your mobile verification code is {{code}}. Please enter this code in the appropriate field to verify your mobile number. If you did not request this code, please ignore this message.','---','{\"code\":\"SMS Verification Code\"}',0,'{{site_name}} Verification Center',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-04-25 03:27:03'),
(12,'WITHDRAW_APPROVE','Withdraw - Approved','Withdrawal Confirmation: Your Request Processed Successfully','{{site_name}} - Withdrawal Request Approved','<div>We are writing to inform you that your withdrawal request of {{amount}} {{site_currency}} via {{method_name}} has been processed successfully.</div><div><br></div><div>Below are the details of your withdrawal:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge:</b> {{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>You will receive:</b> {{method_amount}} {{method_currency}}</div><div><b>Via:</b> {{method_name}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><hr><div><br></div><div><b>Details of Processed Payment:</b></div><div>{{admin_details}}</div><div><br></div><div>Should you have any questions or require further assistance, feel free to reach out to our support team. We\'re here to help.</div>','We are writing to inform you that your withdrawal request of {{amount}} {{site_currency}} via {{method_name}} has been processed successfully.','Withdrawal Confirmation: Your Request Processed Successfully','{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"admin_details\":\"Details provided by the admin\"}',1,'{{site_name}} Finance',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:26:37'),
(13,'WITHDRAW_REJECT','Withdraw - Rejected','Withdrawal Request Rejected','{{site_name}} - Withdrawal Request Rejected','<div>We regret to inform you that your withdrawal request of {{amount}} {{site_currency}} via {{method_name}} has been rejected.</div><div><br></div><div>Here are the details of your withdrawal:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge:</b> {{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Expected Amount:</b> {{method_amount}} {{method_currency}}</div><div><b>Via:</b> {{method_name}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><hr><div><br></div><div><b>Refund Details:</b></div><div>{{amount}} {{site_currency}} has been refunded to your account, and your current balance is {{post_balance}} {{site_currency}}.</div><div><br></div><hr><div><br></div><div><b>Reason for Rejection:</b></div><div>{{admin_details}}</div><div><br></div><div>If you have any questions or concerns regarding this rejection or need further assistance, please do not hesitate to contact our support team. We apologize for any inconvenience this may have caused.</div>','We regret to inform you that your withdrawal request of {{amount}} {{site_currency}} via {{method_name}} has been rejected.','Withdrawal Request Rejected','{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after fter this action\",\"admin_details\":\"Rejection message by the admin\"}',1,'{{site_name}} Finance',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:26:55'),
(14,'WITHDRAW_REQUEST','Withdraw - Requested','Withdrawal Request Confirmation','{{site_name}} - Requested for withdrawal','<div>We are pleased to inform you that your withdrawal request of {{amount}} {{site_currency}} via {{method_name}} has been submitted successfully.</div><div><br></div><div>Here are the details of your withdrawal:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge:</b> {{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Expected Amount:</b> {{method_amount}} {{method_currency}}</div><div><b>Via:</b> {{method_name}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><div>Your current balance is {{post_balance}} {{site_currency}}.</div><div><br></div><div>Should you have any questions or require further assistance, feel free to reach out to our support team. We\'re here to help.</div>','We are pleased to inform you that your withdrawal request of {{amount}} {{site_currency}} via {{method_name}} has been submitted successfully.','Withdrawal request submitted successfully','{\"trx\":\"Transaction number for the withdraw\",\"amount\":\"Amount requested by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the withdraw method\",\"method_currency\":\"Currency of the withdraw method\",\"method_amount\":\"Amount after conversion between base currency and method currency\",\"post_balance\":\"Balance of the user after fter this transaction\"}',1,'{{site_name}} Finance',NULL,1,NULL,0,'2021-11-03 12:00:00','2024-05-08 07:27:20'),
(15,'DEFAULT','Default Template','{{subject}}','{{subject}}','{{message}}','{{message}}','{{message}}','{\"subject\":\"Subject\",\"message\":\"Message\"}',1,NULL,NULL,1,NULL,1,'2019-09-14 13:14:22','2024-05-16 01:32:53'),
(16,'KYC_APPROVE','KYC Approved','KYC Details has been approved','{{site_name}} - KYC Approved','<div><div><span>We are pleased to inform you that your Know Your Customer (KYC) information has been successfully reviewed and approved. This means that you are now eligible to conduct any payout operations within our system.</span></div><br><div><span>Your commitment to completing the KYC process promptly is greatly appreciated, as it helps us ensure the security and integrity of our platform for all users.</span></div><br><div><span>With your KYC verification now complete, you can proceed with confidence to carry out any payout transactions you require. Should you encounter any issues or have any questions along the way, please don\'t hesitate to reach out to our support team. We\'re here to assist you every step of the way.</span></div><br><div><span>Thank you once again for choosing {{site_name}} and for your cooperation in this matter.</span></div></div>','We are pleased to inform you that your Know Your Customer (KYC) information has been successfully reviewed and approved. This means that you are now eligible to conduct any payout operations within our system.','Your  Know Your Customer (KYC) information has been approved successfully','[]',1,'{{site_name}} Verification Center',NULL,1,NULL,0,NULL,'2024-05-08 07:23:57'),
(17,'KYC_REJECT','KYC Rejected','KYC has been rejected','{{site_name}} - KYC Rejected','<div><div><span>We regret to inform you that the Know Your Customer (KYC) information provided has been reviewed and unfortunately, it has not met our verification standards. As a result, we are unable to approve your KYC submission at this time.</span></div><br><div><span>We understand that this news may be disappointing, and we want to assure you that we take these matters seriously to maintain the security and integrity of our platform.</span></div><br><div><span>Reasons for rejection may include discrepancies or incomplete information in the documentation provided. If you believe there has been a misunderstanding or if you would like further clarification on why your KYC was rejected, please don\'t hesitate to contact our support team.</span></div><br><div><span>We encourage you to review your submitted information and ensure that all details are accurate and up-to-date. Once any necessary adjustments have been made, you are welcome to resubmit your KYC information for review.</span></div><br><div><span>We apologize for any inconvenience this may cause and appreciate your understanding and cooperation in this matter.</span></div><br><div>Rejection Reason:</div><div>{{reason}}</div><div><br></div><div><span>Thank you for your continued support and patience.</span></div></div>','We regret to inform you that the Know Your Customer (KYC) information provided has been reviewed and unfortunately, it has not met our verification standards. As a result, we are unable to approve your KYC submission at this time. We encourage you to review your submitted information and ensure that all details are accurate and up-to-date. Once any necessary adjustments have been made, you are welcome to resubmit your KYC information for review.','Your  Know Your Customer (KYC) information has been rejected','{\"reason\":\"Rejection Reason\"}',1,'{{site_name}} Verification Center',NULL,1,NULL,0,NULL,'2024-05-08 07:24:13'),
(18,'ESIM_APPROVED','eSim Approved','eSim has been approved','{{site_name}} - eSim Approved','<div>\r\n  <div>\r\n    <span>We are pleased to inform you that your eSIM plan <strong>{{plan_name}}</strong> has been successfully activated.</span>\r\n  </div>\r\n  \r\n  <div>\r\n    <span>Your plan includes <strong>{{plan_capacity}}</strong> of data, and is associated with the phone number <strong>{{phone_number}}</strong>.</span>\r\n  </div>\r\n  \r\n  <div>\r\n    <span>The plan was purchased for <strong>{{plan_price}}</strong> and was activated on <strong>{{plan_activation}}</strong>. It will expire on <strong>{{expiry_date}}</strong>.</span>\r\n  </div>\r\n  \r\n  <div>\r\n    <span>Your transaction reference number is <strong>{{trx}}</strong>. Please keep this for your records.</span>\r\n  </div>\r\n  \r\n  <div>\r\n    <span>Thank you for choosing {{site_name}}. If you have any questions or need assistance, our support team is always here tohelp.</span>\r\n  </div>\r\n</div>','We are pleased to inform you that your eSIM plan <strong>{{plan_name}}</strong> has been successfully activated. ','Your eSIM plan has been activated','[]',1,'{{site_name}} Verification Center',NULL,1,NULL,0,NULL,'2025-05-11 08:17:37'),
(19,'PAYMENT_COMPLETED','Payment Completed','Payment Completed Successfully','{{site_name}} - Payment Successfull','<div>\r\n  <p>Dear {{username}},</p>\r\n\r\n  <p>We’re happy to confirm that your eSIM plan <strong>{{plan_name}}</strong> payment has been received successfully.</p>\r\n\r\n  <p>Your selected plan includes <strong>{{plan_capacity}}</strong> of data and was purchased for <strong>{{plan_price}}</strong>.</p>\r\n\r\n  <p>Your transaction reference number is <strong>{{trx}}</strong>. Please keep this number for your records.</p>\r\n\r\n  <p>Thank you for choosing {{site_name}}. If you need help or have any questions, feel free to contact our support team at any time.</p>\r\n\r\n  <p>Best regards,<br>{{site_name}} Team</p>\r\n</div>','<div>\r\n  We are pleased to inform you that your eSIM plan <strong>{{plan_name}}</strong> has been successfully purchased. \r\n  It includes <strong>{{plan_capacity}}</strong> of data for <strong>{{plan_price}}</strong>. \r\n  Your transaction reference is <strong>{{trx}}</strong>.\r\n</div>\r\n','Your eSIM plan has been purchased','{\r\n    \"order_number\": \"Order Number\",\r\n    \"plan\": \"Plan Name\",\r\n    \"amount\": \"Order Amount\",\r\n    \"trx\": \"Transaction Number\"\r\n}',1,'{{site_name}} Verification Center',NULL,1,NULL,0,NULL,'2025-05-12 03:07:44'),
(20,'REFERRAL_COMMISSION','Referral commision ','Referral commission received','{{site_name}} - Referral Commission','<div>We\'re writing to inform you that an amount of {{amount}} {{site_currency}} has been successfully added to your account as a referral commission for referring {{user}}.</div><div><br></div><div>Here are the details of the transaction:</div><div><br></div><div><b>Transaction Number: </b>{{trx}}</div><div><b>Current Balance:</b> {{post_balance}} {{site_currency}}</div><div><b>Admin Note:</b> {{remark}}</div><div><br></div><div>If you have any questions or require further assistance, please don\'t hesitate to contact us. We\'re here to assist you.</div>','We\'re writing to inform you that an amount of {{amount}} {{site_currency}} has been successfully added to your account as a referral commission for referring {{user}}.','{{amount}} {{site_currency}} referral commission amount has been received.','{ \"trx\": \"Transaction number for the action\", \"amount\": \"Amount inserted by the admin\", \"remark\": \"Remark inserted by the admin\", \"post_balance\": \"Balance of the user after this transaction\", \"user\": \"Username of the user\" }',1,'{{site_name}} Finance',NULL,1,NULL,0,NULL,NULL),
(21,'PLAN_UNAVAILABLE','Plan unavailable','Plan is not available','{{site_name}} - Plan unavailable','<div>We\'re writing to inform you that the selected plan associated with your recent transaction could not be processed because it is currently unavailable.</div>\r\n<div><br></div>\r\n<div>Here are the details of the transaction:</div>\r\n<div><br></div>\r\n<div><b>Transaction Number:</b> {{trx}}</div>\r\n<div><b>Plan Amount:</b> {{plan_price}} {{site_currency}}</div>\r\n<div><br></div>\r\n<div>Please explore our other available plans or contact support for assistance. Your funds are added to your wallet.</div>\r\n<div><br></div>\r\n<div>If you have any questions or require further assistance, please don\'t hesitate to contact us. We\'re here to help you.</div>\r\n','We\'re sorry, but the selected plan (TRX: {{trx}}) for {{plan_price}} {{site_currency}} is currently unavailable. Your funds are added to your wallet. You can select an alternative plan or contact our support team.\r\n','{{plan_price}} {{site_currency}} plan could not be activated. Plan is unavailable. (TRX: {{trx}})\r\n','{ \"trx\": \"Transaction number for the action\", \"plan_price\": \"Amount inserted of the plan\" }',1,'{{site_name}} Finance',NULL,1,NULL,0,NULL,NULL),
(24,'PAYMENT_REQUEST','Payment - Manual - Requested','Payment Request Submitted Successfully',NULL,'<div>We are pleased to confirm that your payment request for order {{order_number}}&nbsp;of {{amount}} {{site_currency}} via {{method_name}} has been submitted successfully.</div><div><br></div><div>Below are the details of your payment:</div><div><br></div><div><b>Amount:</b> {{amount}} {{site_currency}}</div><div><b>Charge:</b> {{charge}} {{site_currency}}</div><div><b>Conversion Rate:</b> 1 {{site_currency}} = {{rate}} {{method_currency}}</div><div><b>Payable:</b> {{method_amount}} {{method_currency}}</div><div><b>Pay via: </b>{{method_name}}</div><div><b>Transaction Number:</b> {{trx}}</div><div><br></div><div>Should you have any questions or require further assistance, please feel free to reach out to our support team. We\'re here to assist you.</div>','We are pleased to confirm that your payment request of {{amount}} {{site_currency}} via {{method_name}} has been submitted successfully.','Your payment request of {{amount}} {{site_currency}} via {{method_name}} submitted successfully.','{\"order_number\":\"Order Number\",\"trx\":\"Transaction number for the deposit\",\"amount\":\"Amount inserted by the user\",\"charge\":\"Gateway charge set by the admin\",\"rate\":\"Conversion rate between base currency and method currency\",\"method_name\":\"Name of the deposit method\",\"method_currency\":\"Currency of the deposit method\",\"method_amount\":\"Amount after conversion between base currency and method currency\"}',1,'{{site_name}} Billing',NULL,0,NULL,0,'2021-11-03 12:00:00','2025-07-07 06:00:33');
/*!40000 ALTER TABLE `notification_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(10) unsigned NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL,
  `purchase_id` varchar(255) NOT NULL DEFAULT '0',
  `price` decimal(28,8) NOT NULL DEFAULT 0.00000000 COMMENT 'user pay for plan',
  `paid_price` decimal(28,8) NOT NULL DEFAULT 0.00000000 COMMENT 'admin pay to dataplan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(255) DEFAULT NULL,
  `total_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = initiate, 2 = pending, 1 = complete\r\n',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) DEFAULT NULL,
  `slug` varchar(40) DEFAULT NULL,
  `tempname` varchar(40) DEFAULT NULL COMMENT 'template name',
  `secs` text DEFAULT NULL,
  `seo_content` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES
(1,'HOME','/','templates.basic.','[\"coverage\",\"client\",\"about\",\"about_us_second\",\"about_us_third\",\"about_us_fourth\",\"referral\",\"referral_process\",\"faq\",\"work_process\",\"service\",\"blog\",\"testimonial\"]',NULL,1,'2020-07-11 06:23:58','2025-06-25 00:43:47'),
(4,'Blog','blog','templates.basic.',NULL,NULL,1,'2020-10-22 01:14:43','2020-10-22 01:14:43'),
(5,'Contact','contact','templates.basic.','[\"faq\"]','',1,'2020-10-22 01:14:53','2025-04-24 03:58:41'),
(28,'About','about','templates.basic.','[\"about\",\"about_us_second\",\"about_us_third\",\"about_us_fourth\",\"faq\"]',NULL,0,'2025-04-23 06:06:48','2025-07-03 04:13:59'),
(30,'Destination','destination','templates.basic.',NULL,NULL,1,'2025-04-25 23:31:50','2025-04-25 23:41:21');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` int(11) DEFAULT NULL,
  `currency_id` bigint(20) DEFAULT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '0',
  `period` int(11) NOT NULL DEFAULT 0,
  `capacity` int(11) NOT NULL DEFAULT 0,
  `capacity_unit` varchar(255) NOT NULL DEFAULT '0',
  `retail_price` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `price_currency` varchar(40) NOT NULL DEFAULT '0',
  `prepaid_credit` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `prepaid_currency` varchar(40) NOT NULL DEFAULT '0',
  `reloadable` tinyint(1) NOT NULL DEFAULT 0,
  `phone_number` tinyint(1) NOT NULL DEFAULT 0,
  `operator_name` varchar(255) NOT NULL DEFAULT '0',
  `operator_slug` varchar(255) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `api_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `region_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regions`
--

LOCK TABLES `regions` WRITE;
/*!40000 ALTER TABLE `regions` DISABLE KEYS */;
/*!40000 ALTER TABLE `regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_attachments`
--

DROP TABLE IF EXISTS `support_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_message_id` int(10) unsigned NOT NULL DEFAULT 0,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_attachments`
--

LOCK TABLES `support_attachments` WRITE;
/*!40000 ALTER TABLE `support_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_id` int(10) unsigned NOT NULL DEFAULT 0,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
INSERT INTO `support_messages` VALUES
(1,1,0,'Hello,\r\n\r\nWe have a promotional offer for your website rainbow-nights.com.\r\n\r\nWorld’s First AI App That Rank Any Link We Want #1 In Google For Any Keyword We Want... In 30 Seconds Or Less…\r\nSending Us A Surge Of 1,478 Clicks A Day 100% FREE\r\nNo Technical Skills - No Experience - No Coding - No Setup - No Waiting\r\nWatch How We Generate 342 Clicks Per Hour In 27 Seconds Flat…\r\n\r\nSee it in action: https://goldsolutions.pro/SneakAI\r\n\r\nYou are receiving this message because we believe our offer may be relevant to you. \r\nIf you do not wish to receive further communications from us, please click here to UNSUBSCRIBE:\r\nhttps://goldsolutions.pro/unsubscribe?domain=rainbow-nights.com\r\nAddress: 209 West Street Comstock Park, MI 49321\r\nLooking out for you, Ethan Parker','2025-08-21 21:50:50','2025-08-21 21:50:50');
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT 0,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `ticket` varchar(40) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES
(1,0,'Victoria Blair','victoria.blair@googlemail.com','53422306','Why Are You Still Playing Google’s Game… When You Could Just Appear On Page #1?',0,2,'2025-08-21 17:50:50','2025-08-21 21:50:50','2025-08-21 21:50:50');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) DEFAULT NULL,
  `trx` varchar(40) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `remark` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `update_logs`
--

DROP TABLE IF EXISTS `update_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `update_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(40) DEFAULT NULL,
  `update_log` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `update_logs`
--

LOCK TABLES `update_logs` WRITE;
/*!40000 ALTER TABLE `update_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `update_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_ip` varchar(40) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `country` varchar(40) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `longitude` varchar(40) DEFAULT NULL,
  `latitude` varchar(40) DEFAULT NULL,
  `browser` varchar(40) DEFAULT NULL,
  `os` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(40) DEFAULT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `email` varchar(40) NOT NULL,
  `dial_code` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `ref_by` int(10) unsigned NOT NULL DEFAULT 0,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) NOT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `country_code` varchar(40) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: banned, 1: active',
  `kyc_data` text DEFAULT NULL,
  `kyc_rejection_reason` varchar(255) DEFAULT NULL,
  `kv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: KYC Unverified, 2: KYC pending, 1: KYC verified',
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: email unverified, 1: email verified',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: mobile unverified, 1: mobile verified',
  `profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `ver_code` varchar(40) DEFAULT NULL COMMENT 'stores verification code',
  `ver_code_send_at` datetime DEFAULT NULL COMMENT 'verification send time',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 2fa off, 1: 2fa on',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: 2fa unverified, 1: 2fa verified',
  `tsc` varchar(255) DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `provider` varchar(40) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-21 16:44:24
