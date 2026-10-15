-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: nexus_school
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `academic_calendar_events`
--

DROP TABLE IF EXISTS `academic_calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_calendar_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `calendar_year_id` bigint(20) unsigned NOT NULL,
  `calendar_term_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'event',
  `starts_on` date NOT NULL,
  `ends_on` date DEFAULT NULL,
  `is_all_day` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_calendar_events_uuid_unique` (`uuid`),
  KEY `academic_calendar_events_calendar_year_id_foreign` (`calendar_year_id`),
  KEY `academic_calendar_events_calendar_term_id_foreign` (`calendar_term_id`),
  KEY `academic_calendar_events_school_id_starts_on_index` (`school_id`,`starts_on`),
  KEY `academic_calendar_events_school_id_calendar_year_id_index` (`school_id`,`calendar_year_id`),
  KEY `academic_calendar_events_type_index` (`type`),
  CONSTRAINT `academic_calendar_events_calendar_term_id_foreign` FOREIGN KEY (`calendar_term_id`) REFERENCES `academic_calendar_terms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `academic_calendar_events_calendar_year_id_foreign` FOREIGN KEY (`calendar_year_id`) REFERENCES `academic_calendar_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_calendar_events`
--

LOCK TABLES `academic_calendar_events` WRITE;
/*!40000 ALTER TABLE `academic_calendar_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_calendar_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_calendar_terms`
--

DROP TABLE IF EXISTS `academic_calendar_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_calendar_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `calendar_year_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `starts_on` date NOT NULL,
  `ends_on` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_calendar_terms_school_id_calendar_year_id_name_unique` (`school_id`,`calendar_year_id`,`name`),
  UNIQUE KEY `academic_calendar_terms_uuid_unique` (`uuid`),
  KEY `academic_calendar_terms_calendar_year_id_foreign` (`calendar_year_id`),
  KEY `academic_calendar_terms_status_index` (`status`),
  KEY `academic_calendar_terms_school_id_starts_on_index` (`school_id`,`starts_on`),
  CONSTRAINT `academic_calendar_terms_calendar_year_id_foreign` FOREIGN KEY (`calendar_year_id`) REFERENCES `academic_calendar_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_calendar_terms`
--

LOCK TABLES `academic_calendar_terms` WRITE;
/*!40000 ALTER TABLE `academic_calendar_terms` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_calendar_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_calendar_years`
--

DROP TABLE IF EXISTS `academic_calendar_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_calendar_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_calendar_years_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `academic_calendar_years_uuid_unique` (`uuid`),
  KEY `academic_calendar_years_status_index` (`status`),
  KEY `academic_calendar_years_school_id_start_date_index` (`school_id`,`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_calendar_years`
--

LOCK TABLES `academic_calendar_years` WRITE;
/*!40000 ALTER TABLE `academic_calendar_years` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_calendar_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_classes`
--

DROP TABLE IF EXISTS `academic_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` varchar(255) DEFAULT NULL,
  `uuid` char(26) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `grade_level_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(10) unsigned NOT NULL DEFAULT 0,
  `display_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `homeroom_teacher_id` bigint(20) unsigned DEFAULT NULL,
  `classroom` varchar(100) DEFAULT NULL,
  `classroom_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_classes_academic_year_id_name_unique` (`academic_year_id`,`name`),
  UNIQUE KEY `academic_classes_code_unique` (`code`),
  UNIQUE KEY `academic_classes_uuid_unique` (`uuid`),
  KEY `academic_classes_program_id_foreign` (`program_id`),
  KEY `academic_classes_grade_level_id_foreign` (`grade_level_id`),
  KEY `academic_classes_section_id_foreign` (`section_id`),
  KEY `academic_classes_status_index` (`status`),
  KEY `academic_classes_academic_year_id_display_order_index` (`academic_year_id`,`display_order`),
  KEY `academic_classes_is_active_index` (`is_active`),
  KEY `academic_classes_classroom_id_foreign` (`classroom_id`),
  KEY `academic_classes_school_id_foreign` (`school_id`),
  CONSTRAINT `academic_classes_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `academic_classes_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `academic_classes_grade_level_id_foreign` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `academic_classes_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `academic_classes_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `academic_classes_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_classes`
--

LOCK TABLES `academic_classes` WRITE;
/*!40000 ALTER TABLE `academic_classes` DISABLE KEYS */;
INSERT INTO `academic_classes` VALUES (1,NULL,NULL,1,1,1,1,'6ème A','6E-A','Class 6ème - A',40,1,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:45','2026-07-23 12:50:45',NULL),(2,NULL,NULL,1,1,1,2,'6ème B','6E-B','Class 6ème - B',40,2,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:46','2026-07-23 12:50:46',NULL),(3,NULL,NULL,1,1,1,3,'6ème C','6E-C','Class 6ème - C',40,3,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:47','2026-07-23 12:50:47',NULL),(4,NULL,NULL,1,1,1,4,'6ème D','6E-D','Class 6ème - D',40,4,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:47','2026-07-23 12:50:47',NULL),(5,NULL,NULL,1,2,5,5,'Seconde Science','SEC-SCI','Class Seconde - Science',40,1,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:48','2026-07-23 12:50:48',NULL),(6,NULL,NULL,1,2,5,6,'Seconde Arts','SEC-ART','Class Seconde - Arts',40,2,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:48','2026-07-23 12:50:48',NULL),(7,NULL,NULL,1,2,5,7,'Seconde Commercial','SEC-COM','Class Seconde - Commercial',40,3,'active',1,NULL,NULL,NULL,'2026-07-23 12:50:48','2026-07-23 12:50:48',NULL);
/*!40000 ALTER TABLE `academic_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_programs`
--

DROP TABLE IF EXISTS `academic_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `education_level_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_programs_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `academic_programs_uuid_unique` (`uuid`),
  KEY `academic_programs_education_level_id_foreign` (`education_level_id`),
  KEY `academic_programs_status_index` (`status`),
  KEY `academic_programs_school_id_education_level_id_index` (`school_id`,`education_level_id`),
  CONSTRAINT `academic_programs_education_level_id_foreign` FOREIGN KEY (`education_level_id`) REFERENCES `education_levels` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_programs`
--

LOCK TABLES `academic_programs` WRITE;
/*!40000 ALTER TABLE `academic_programs` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_subjects`
--

DROP TABLE IF EXISTS `academic_subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `subject_group_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `abbreviation` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_subjects_school_id_code_unique` (`school_id`,`code`),
  UNIQUE KEY `academic_subjects_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `academic_subjects_uuid_unique` (`uuid`),
  KEY `academic_subjects_subject_group_id_foreign` (`subject_group_id`),
  KEY `academic_subjects_status_index` (`status`),
  KEY `academic_subjects_school_id_subject_group_id_index` (`school_id`,`subject_group_id`),
  CONSTRAINT `academic_subjects_subject_group_id_foreign` FOREIGN KEY (`subject_group_id`) REFERENCES `subject_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_subjects`
--

LOCK TABLES `academic_subjects` WRITE;
/*!40000 ALTER TABLE `academic_subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `academic_subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_terms`
--

DROP TABLE IF EXISTS `academic_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_terms_academic_year_id_name_unique` (`academic_year_id`,`name`),
  UNIQUE KEY `academic_terms_year_code_unique` (`academic_year_id`,`code`),
  UNIQUE KEY `academic_terms_uuid_unique` (`uuid`),
  KEY `academic_terms_school_id_foreign` (`school_id`),
  KEY `academic_terms_status_index` (`status`),
  KEY `academic_terms_academic_year_id_start_date_index` (`academic_year_id`,`start_date`),
  CONSTRAINT `academic_terms_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `academic_terms_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_terms`
--

LOCK TABLES `academic_terms` WRITE;
/*!40000 ALTER TABLE `academic_terms` DISABLE KEYS */;
INSERT INTO `academic_terms` VALUES (1,NULL,1,'01ky7gd0jm3wgn1bqba86pw1p9','Semester 1','S1','2025-10-01','2026-01-31',1,NULL,'draft','2026-07-23 12:50:34','2026-10-13 18:46:00',NULL),(2,NULL,1,'01ky7gd0jm3wgn1bqba86pw1p9','Semester 2','S2','2026-02-01','2026-07-31',2,NULL,'active','2026-07-23 12:50:34','2026-10-13 18:46:00',NULL);
/*!40000 ALTER TABLE `academic_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_years_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `academic_years_uuid_unique` (`uuid`),
  KEY `academic_years_status_index` (`status`),
  KEY `academic_years_school_id_start_date_index` (`school_id`,`start_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','2025-2026',NULL,'2025-10-01','2026-07-31',NULL,'active','2026-07-23 12:50:34','2026-07-23 12:50:34',NULL);
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `log_name` varchar(100) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `subject_type` varchar(100) DEFAULT NULL,
  `subject_id` varchar(36) DEFAULT NULL,
  `event` varchar(100) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `causer_id` varchar(36) DEFAULT NULL,
  `causer_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `activity_logs_school_id_created_at_index` (`school_id`,`created_at`),
  KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_logs_log_name_index` (`log_name`),
  KEY `activity_logs_event_index` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_conversations`
--

DROP TABLE IF EXISTS `ai_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_conversations` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `ai_provider_id` char(36) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'active',
  `context_type` varchar(100) DEFAULT NULL,
  `context_id` varchar(36) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `system_prompt` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `message_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_input_tokens` bigint(20) unsigned NOT NULL DEFAULT 0,
  `total_output_tokens` bigint(20) unsigned NOT NULL DEFAULT 0,
  `context_window_used` int(10) unsigned NOT NULL DEFAULT 0,
  `context_window_limit` int(10) unsigned NOT NULL DEFAULT 128000,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_conversations_school_id_index` (`school_id`),
  KEY `ai_conversations_user_id_index` (`user_id`),
  KEY `ai_conversations_ai_provider_id_index` (`ai_provider_id`),
  KEY `ai_conversations_status_index` (`status`),
  KEY `ai_conversations_context_type_index` (`context_type`),
  CONSTRAINT `ai_conversations_ai_provider_id_foreign` FOREIGN KEY (`ai_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_conversations`
--

LOCK TABLES `ai_conversations` WRITE;
/*!40000 ALTER TABLE `ai_conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_knowledge_sources`
--

DROP TABLE IF EXISTS `ai_knowledge_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_knowledge_sources` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'pending',
  `source_path` varchar(500) DEFAULT NULL,
  `source_type` varchar(100) DEFAULT NULL,
  `content_type` varchar(100) DEFAULT NULL,
  `content_size` bigint(20) unsigned DEFAULT NULL,
  `content_hash` varchar(64) DEFAULT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `chunk_count` int(10) unsigned NOT NULL DEFAULT 0,
  `total_tokens` bigint(20) unsigned NOT NULL DEFAULT 0,
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `indexed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_knowledge_sources_school_id_slug_unique` (`school_id`,`slug`),
  KEY `ai_knowledge_sources_school_id_index` (`school_id`),
  KEY `ai_knowledge_sources_type_index` (`type`),
  KEY `ai_knowledge_sources_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_knowledge_sources`
--

LOCK TABLES `ai_knowledge_sources` WRITE;
/*!40000 ALTER TABLE `ai_knowledge_sources` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_knowledge_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_messages`
--

DROP TABLE IF EXISTS `ai_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_messages` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `ai_conversation_id` char(36) NOT NULL,
  `role` varchar(36) NOT NULL,
  `content` text NOT NULL,
  `input_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `output_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `model` varchar(255) DEFAULT NULL,
  `finish_reason` varchar(50) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_messages_school_id_index` (`school_id`),
  KEY `ai_messages_ai_conversation_id_index` (`ai_conversation_id`),
  KEY `ai_messages_role_index` (`role`),
  KEY `ai_messages_created_at_index` (`created_at`),
  CONSTRAINT `ai_messages_ai_conversation_id_foreign` FOREIGN KEY (`ai_conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_messages`
--

LOCK TABLES `ai_messages` WRITE;
/*!40000 ALTER TABLE `ai_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_prompt_templates`
--

DROP TABLE IF EXISTS `ai_prompt_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_prompt_templates` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `system_prompt` text NOT NULL,
  `user_prompt_template` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `status` varchar(36) NOT NULL DEFAULT 'active',
  `version` int(10) unsigned NOT NULL DEFAULT 1,
  `target_model` varchar(255) DEFAULT NULL,
  `temperature` double NOT NULL DEFAULT 0.7,
  `max_tokens` int(10) unsigned NOT NULL DEFAULT 4096,
  `top_p` double DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_prompt_templates_school_id_slug_unique` (`school_id`,`slug`),
  KEY `ai_prompt_templates_school_id_index` (`school_id`),
  KEY `ai_prompt_templates_category_index` (`category`),
  KEY `ai_prompt_templates_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_prompt_templates`
--

LOCK TABLES `ai_prompt_templates` WRITE;
/*!40000 ALTER TABLE `ai_prompt_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_prompt_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_providers`
--

DROP TABLE IF EXISTS `ai_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_providers` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `driver` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `supported_models` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`supported_models`)),
  `default_model` varchar(255) DEFAULT NULL,
  `rate_limit_per_minute` int(11) NOT NULL DEFAULT 60,
  `daily_token_limit` int(11) DEFAULT NULL,
  `monthly_token_limit` int(11) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_providers_school_id_slug_unique` (`school_id`,`slug`),
  KEY `ai_providers_school_id_index` (`school_id`),
  KEY `ai_providers_driver_index` (`driver`),
  KEY `ai_providers_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_providers`
--

LOCK TABLES `ai_providers` WRITE;
/*!40000 ALTER TABLE `ai_providers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_usage_logs`
--

DROP TABLE IF EXISTS `ai_usage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_usage_logs` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `ai_provider_id` char(36) DEFAULT NULL,
  `ai_conversation_id` char(36) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `input_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `output_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `total_tokens` int(10) unsigned NOT NULL DEFAULT 0,
  `estimated_cost` double NOT NULL DEFAULT 0,
  `latency_ms` int(10) unsigned DEFAULT NULL,
  `was_cached` tinyint(1) NOT NULL DEFAULT 0,
  `is_billable` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(36) NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_usage_logs_school_id_index` (`school_id`),
  KEY `ai_usage_logs_ai_provider_id_index` (`ai_provider_id`),
  KEY `ai_usage_logs_ai_conversation_id_index` (`ai_conversation_id`),
  KEY `ai_usage_logs_user_id_index` (`user_id`),
  KEY `ai_usage_logs_action_index` (`action`),
  KEY `ai_usage_logs_created_at_index` (`created_at`),
  KEY `ai_usage_logs_school_id_created_at_index` (`school_id`,`created_at`),
  CONSTRAINT `ai_usage_logs_ai_conversation_id_foreign` FOREIGN KEY (`ai_conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_usage_logs_ai_provider_id_foreign` FOREIGN KEY (`ai_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_usage_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_usage_logs`
--

LOCK TABLES `ai_usage_logs` WRITE;
/*!40000 ALTER TABLE `ai_usage_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_usage_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analytics_snapshots`
--

DROP TABLE IF EXISTS `analytics_snapshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics_snapshots` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `snapshot_type` varchar(100) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `period_start` timestamp NULL DEFAULT NULL,
  `period_end` timestamp NULL DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_immutable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `analytics_snapshots_school_id_index` (`school_id`),
  KEY `analytics_snapshots_snapshot_type_index` (`snapshot_type`),
  KEY `analytics_snapshots_period_start_index` (`period_start`),
  KEY `analytics_snapshots_period_end_index` (`period_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytics_snapshots`
--

LOCK TABLES `analytics_snapshots` WRITE;
/*!40000 ALTER TABLE `analytics_snapshots` DISABLE KEYS */;
INSERT INTO `analytics_snapshots` VALUES ('01KY7HAG3A419KGTWCVY5DNTBR','01ky7gd0jm3wgn1bqba86pw1p9','enrollment','{\"total\":320,\"active\":310,\"completed\":5,\"withdrawn\":5}','2026-07-23 00:00:00','2026-07-23 23:59:59','2026-07-23 13:06:02',1,'2026-07-23 13:06:02','2026-07-23 13:06:02'),('01KY7HAG5BYF2F5SE5ATRHWAGZ','01ky7gd0jm3wgn1bqba86pw1p9','attendance','{\"total_records\":1500,\"present\":1420,\"absent\":50,\"late\":30}','2026-07-23 00:00:00','2026-07-23 23:59:59','2026-07-23 13:06:02',1,'2026-07-23 13:06:02','2026-07-23 13:06:02'),('01KY7HAG6FTXR656T3XAJVA4H1','01ky7gd0jm3wgn1bqba86pw1p9','library','{\"total_books\":500,\"active_loans\":45,\"overdue_loans\":3,\"total_fines\":15}','2026-07-23 00:00:00','2026-07-23 23:59:59','2026-07-23 13:06:02',1,'2026-07-23 13:06:02','2026-07-23 13:06:02'),('01KY7HAG872Y383T73KS070BDF','01ky7gd0jm3wgn1bqba86pw1p9','transport','{\"total_vehicles\":8,\"active_vehicles\":7,\"total_routes\":5,\"total_trips_today\":10}','2026-07-23 00:00:00','2026-07-23 23:59:59','2026-07-23 13:06:02',1,'2026-07-23 13:06:02','2026-07-23 13:06:02'),('01KY7HAGACWWYB4PBH6X922V3D','01ky7gd0jm3wgn1bqba86pw1p9','communication','{\"total_notifications\":200,\"unread_notifications\":15,\"total_announcements\":12,\"total_messages\":50}','2026-07-23 00:00:00','2026-07-23 23:59:59','2026-07-23 13:06:02',1,'2026-07-23 13:06:02','2026-07-23 13:06:02');
/*!40000 ALTER TABLE `analytics_snapshots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `scope` varchar(36) NOT NULL DEFAULT 'school_wide',
  `target_id` varchar(36) DEFAULT NULL,
  `priority` varchar(36) NOT NULL DEFAULT 'normal',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(36) NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_school_id_index` (`school_id`),
  KEY `announcements_author_id_index` (`author_id`),
  KEY `announcements_scope_index` (`scope`),
  KEY `announcements_status_index` (`status`),
  KEY `announcements_published_at_index` (`published_at`),
  CONSTRAINT `announcements_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES ('01KY7H7HTY4QDMQX5P3734TT1D','01ky7gd0jm3wgn1bqba86pw1p9',1,'New Academic Year Registration Open','Registration for the 2026/2027 academic year is now open. Please visit the admin office to complete your registration. Early bird discount available until August 30th.','school_wide',NULL,'high',1,'published','2026-07-23 13:04:25',NULL,'2026-07-23 13:04:25','2026-07-23 13:04:25',NULL),('01KY7H7HXVAFYVV8A7KGHQJ6XB','01ky7gd0jm3wgn1bqba86pw1p9',1,'Staff Meeting - Friday','All staff members are required to attend a mandatory meeting on Friday at 2:00 PM in the conference hall.','staff',NULL,'normal',0,'published','2026-07-23 13:04:25',NULL,'2026-07-23 13:04:26','2026-07-23 13:04:26',NULL),('01KY7H7J5QP82H3AWB8QHDZ509','01ky7gd0jm3wgn1bqba86pw1p9',1,'Parent-Teacher Conference','The quarterly parent-teacher conference will be held next week. Parents are encouraged to attend and discuss their children\'s progress.','parents',NULL,'normal',0,'published','2026-07-23 13:04:25','2026-07-30 13:04:25','2026-07-23 13:04:26','2026-07-23 13:04:26',NULL),('01KY7H7J8AMACR13A1E875ESEG','01ky7gd0jm3wgn1bqba86pw1p9',1,'Sports Day Preparations','All grade levels should begin preparations for the annual sports day. Practice sessions will begin next Monday.','school_wide',NULL,'low',0,'draft',NULL,NULL,'2026-07-23 13:04:26','2026-07-23 13:04:26',NULL);
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_audit_logs`
--

DROP TABLE IF EXISTS `api_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_audit_logs` (
  `id` char(36) NOT NULL,
  `api_client_id` char(36) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `api_token_id` char(36) DEFAULT NULL,
  `method` varchar(10) NOT NULL,
  `path` varchar(500) NOT NULL,
  `status_code` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `request_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_body`)),
  `response_time_ms` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `api_audit_logs_api_client_id_index` (`api_client_id`),
  KEY `api_audit_logs_school_id_index` (`school_id`),
  KEY `api_audit_logs_user_id_index` (`user_id`),
  KEY `api_audit_logs_method_index` (`method`),
  KEY `api_audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `api_audit_logs_api_client_id_foreign` FOREIGN KEY (`api_client_id`) REFERENCES `api_clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_audit_logs`
--

LOCK TABLES `api_audit_logs` WRITE;
/*!40000 ALTER TABLE `api_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_clients`
--

DROP TABLE IF EXISTS `api_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_clients` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `secret` varchar(255) NOT NULL,
  `callback_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `scopes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scopes`)),
  `rate_limit_per_minute` int(11) NOT NULL DEFAULT 60,
  `rate_limit_per_day` int(11) NOT NULL DEFAULT 10000,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_clients_school_id_slug_unique` (`school_id`,`slug`),
  KEY `api_clients_school_id_index` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_clients`
--

LOCK TABLES `api_clients` WRITE;
/*!40000 ALTER TABLE `api_clients` DISABLE KEYS */;
INSERT INTO `api_clients` VALUES ('01KY7HAKN18BDRNYJSRNVM1QRZ','01ky7gd0jm3wgn1bqba86pw1p9','Demo Integration Client','demo-integration','Pre-configured API client for demo purposes','nx1D5gzDkI7wXMFFbRt0BKb4C8YEpOVT5iOU2XU3DzllH8ojtGMFJkXqah6rXR1s','http://localhost/api/webhooks/demo',1,'[\"*\"]',120,50000,NULL,'2026-07-23 13:06:06','2026-07-23 13:06:06',NULL);
/*!40000 ALTER TABLE `api_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_tokens` (
  `id` char(36) NOT NULL,
  `api_client_id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `abilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`abilities`)),
  `expires_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_tokens_token_unique` (`token`),
  KEY `api_tokens_api_client_id_index` (`api_client_id`),
  KEY `api_tokens_school_id_index` (`school_id`),
  CONSTRAINT `api_tokens_api_client_id_foreign` FOREIGN KEY (`api_client_id`) REFERENCES `api_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_tokens`
--

LOCK TABLES `api_tokens` WRITE;
/*!40000 ALTER TABLE `api_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `api_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_results`
--

DROP TABLE IF EXISTS `assessment_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `student_enrollment_id` bigint(20) unsigned DEFAULT NULL,
  `assessment_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `academic_term_id` bigint(20) unsigned NOT NULL,
  `raw_score` decimal(8,2) NOT NULL,
  `adjusted_score` decimal(8,2) DEFAULT NULL,
  `maximum_score` decimal(8,2) NOT NULL,
  `percentage` decimal(8,2) DEFAULT NULL,
  `letter_grade` varchar(5) DEFAULT NULL,
  `grade_point` decimal(4,2) DEFAULT NULL,
  `coefficient` decimal(5,2) NOT NULL DEFAULT 1.00,
  `weighted_score` decimal(12,4) DEFAULT NULL,
  `rank_in_assessment` int(10) unsigned DEFAULT NULL,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `is_excused` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_assessment` (`student_id`,`assessment_id`),
  UNIQUE KEY `assessment_results_uuid_unique` (`uuid`),
  KEY `assessment_results_user_id_foreign` (`user_id`),
  KEY `assessment_results_academic_term_id_foreign` (`academic_term_id`),
  KEY `assessment_results_school_id_index` (`school_id`),
  KEY `assessment_results_student_enrollment_id_index` (`student_enrollment_id`),
  KEY `assessment_results_status_index` (`status`),
  KEY `assessment_results_assessment_id_status_index` (`assessment_id`,`status`),
  KEY `assessment_results_subject_id_academic_term_id_index` (`subject_id`,`academic_term_id`),
  KEY `assessment_results_academic_year_id_academic_term_id_index` (`academic_year_id`,`academic_term_id`),
  CONSTRAINT `assessment_results_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_results_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_results_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_results_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_results_student_enrollment_id_foreign` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assessment_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_results_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_results_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_results`
--

LOCK TABLES `assessment_results` WRITE;
/*!40000 ALTER TABLE `assessment_results` DISABLE KEYS */;
INSERT INTO `assessment_results` VALUES (1,'01KY7GTGENSB1BVH8W7KH0MNVQ','01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,1,1,NULL,1,1,38.00,NULL,100.00,38.00,'F',0.00,1.58,60.0400,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:18','2026-07-23 12:57:18',NULL),(2,'01KY7GTGTKX0183D1YMX155RGJ','01ky7gd0jm3wgn1bqba86pw1p9',5,NULL,1,1,NULL,1,1,50.00,NULL,100.00,50.00,'F',0.00,2.98,149.0000,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:18','2026-07-23 12:57:18',NULL),(3,'01KY7GTGWGDFT7CGZYRE9DY0ZG','01ky7gd0jm3wgn1bqba86pw1p9',4,NULL,1,1,NULL,1,1,26.00,NULL,100.00,26.00,'F',0.00,2.56,66.5600,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:18','2026-07-23 12:57:18',NULL),(4,'01KY7GTH100FRD7Z5S50ATPAQQ','01ky7gd0jm3wgn1bqba86pw1p9',1,NULL,1,1,NULL,1,1,85.00,NULL,100.00,85.00,'B',3.00,1.66,141.1000,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(5,'01KY7GTH3KMYTW4K4P6J6DK89E','01ky7gd0jm3wgn1bqba86pw1p9',5,NULL,2,2,NULL,1,1,9.00,NULL,100.00,9.00,'F',0.00,1.31,11.7900,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(6,'01KY7GTH59GDR693WY8YA91DX8','01ky7gd0jm3wgn1bqba86pw1p9',1,NULL,2,2,NULL,1,1,100.00,NULL,100.00,100.00,'A+',4.00,2.74,274.0000,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(7,'01KY7GTH7ASX5DZW0BT55A4MBT','01ky7gd0jm3wgn1bqba86pw1p9',4,NULL,2,2,NULL,1,1,56.00,NULL,100.00,56.00,'F',0.00,2.03,113.6800,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(8,'01KY7GTH7X58GAJ4W0VPFR1Q61','01ky7gd0jm3wgn1bqba86pw1p9',3,NULL,2,2,NULL,1,1,85.00,NULL,100.00,85.00,'B',3.00,1.52,129.2000,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(9,'01KY7GTHB4DJVJ5VSJDD1GQBMH','01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,2,2,NULL,1,1,46.00,NULL,100.00,46.00,'F',0.00,2.26,103.9600,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(10,'01KY7GTHCB82G2VNE9XRPQ6D2T','01ky7gd0jm3wgn1bqba86pw1p9',1,NULL,3,3,NULL,1,1,94.00,NULL,100.00,94.00,'A',4.00,2.79,262.2600,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(11,'01KY7GTHDVFBRX5GMKET14F9RM','01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,3,3,NULL,1,1,69.00,NULL,100.00,69.00,'D+',1.30,1.82,125.5800,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(12,'01KY7GTHEJAM9MT10WZTZNC8YF','01ky7gd0jm3wgn1bqba86pw1p9',5,NULL,3,3,NULL,1,1,89.00,NULL,100.00,89.00,'B+',3.30,2.65,235.8500,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(13,'01KY7GTHF6EV8XQGTFX7WTGY4Q','01ky7gd0jm3wgn1bqba86pw1p9',4,NULL,3,3,NULL,1,1,82.00,NULL,100.00,82.00,'B-',2.70,2.73,223.8600,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL),(14,'01KY7GTHGP0VAFMSDPP6JZBSXT','01ky7gd0jm3wgn1bqba86pw1p9',3,NULL,3,3,NULL,1,1,88.00,NULL,100.00,88.00,'B+',3.30,1.91,168.0800,NULL,0,0,0,0,'draft',NULL,'2026-07-23 12:57:19','2026-07-23 12:57:19',NULL);
/*!40000 ALTER TABLE `assessment_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `academic_term_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `academic_class_id` bigint(20) unsigned NOT NULL,
  `assessment_code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `assessment_type` varchar(255) NOT NULL,
  `assessment_category` varchar(255) NOT NULL DEFAULT 'formative',
  `maximum_score` decimal(8,2) NOT NULL,
  `passing_score` decimal(8,2) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `assessment_date` date DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `submission_deadline` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assessments_school_id_assessment_code_unique` (`school_id`,`assessment_code`),
  UNIQUE KEY `assessments_uuid_unique` (`uuid`),
  KEY `assessments_academic_term_id_foreign` (`academic_term_id`),
  KEY `assessments_user_id_foreign` (`user_id`),
  KEY `assessments_academic_class_id_index` (`academic_class_id`),
  KEY `assessments_subject_id_index` (`subject_id`),
  KEY `assessments_academic_year_id_academic_term_id_index` (`academic_year_id`,`academic_term_id`),
  CONSTRAINT `assessments_academic_class_id_foreign` FOREIGN KEY (`academic_class_id`) REFERENCES `academic_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessments_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessments`
--

LOCK TABLES `assessments` WRITE;
/*!40000 ALTER TABLE `assessments` DISABLE KEYS */;
INSERT INTO `assessments` VALUES (1,'01KY7GTDT1NWBMZXQ2NY0NWN97','01ky7gd0jm3wgn1bqba86pw1p9',1,1,1,1,7,'ASS-63OF','ipsum et provident','midterm','summative',100.00,50.00,4.10,'2008-12-03','2021-05-15','1983-08-27','draft',0,0,NULL,'2026-07-23 12:57:15','2026-07-23 12:57:15',NULL),(2,'01KY7GTDY7TSTNV95ZXWXZ363J','01ky7gd0jm3wgn1bqba86pw1p9',1,1,1,2,7,'ASS-32JT','ut provident ea','final','summative',100.00,50.00,4.19,'2025-09-22','1974-01-22','1996-03-28','draft',0,0,'Eligendi et quia in.','2026-07-23 12:57:15','2026-07-23 12:57:15',NULL),(3,'01KY7GTDZH0E2KSRM76TGV4HQC','01ky7gd0jm3wgn1bqba86pw1p9',1,1,1,3,7,'ASS-69BD','illum ab aut','midterm','summative',100.00,50.00,2.58,'2003-10-02',NULL,NULL,'draft',0,0,'Ex fugiat incidunt voluptatibus et pariatur.','2026-07-23 12:57:16','2026-07-23 12:57:16',NULL);
/*!40000 ALTER TABLE `assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_records` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'present',
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_records_school_id_date_index` (`school_id`,`date`),
  KEY `attendance_records_school_id_status_index` (`school_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_records`
--

LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `auditable_type` varchar(100) NOT NULL,
  `auditable_id` varchar(36) NOT NULL,
  `action` varchar(50) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `event` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audit_logs_school_id_created_at_index` (`school_id`,`created_at`),
  KEY `audit_logs_user_id_index` (`user_id`),
  KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_event_index` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authors_school_id_first_name_last_name_unique` (`school_id`,`first_name`,`last_name`),
  UNIQUE KEY `authors_uuid_unique` (`uuid`),
  KEY `authors_status_index` (`status`),
  KEY `authors_school_id_status_index` (`school_id`,`status`),
  CONSTRAINT `authors_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authors`
--

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` VALUES (1,'01KY7GTKJJXT9X0S713HP302XP','01ky7gd0jm3wgn1bqba86pw1p9','Kwame','Asante',NULL,'Award-winning Ghanaian author and professor of African Literature at the University of Ghana.','Ghana',NULL,NULL,NULL,'active','2026-07-23 12:57:21','2026-07-23 12:57:21',NULL),(2,'01KY7GTM4QQ942TQQ1H2HD9CMY','01ky7gd0jm3wgn1bqba86pw1p9','Ama','Mensah','Adwoa','Novelist and poet exploring themes of identity and cultural heritage in West Africa.','Ghana',NULL,NULL,'https://amamensah.com','active','2026-07-23 12:57:22','2026-07-23 12:57:22',NULL),(3,'01KY7GTM8J1C890F2KBED5V9P2','01ky7gd0jm3wgn1bqba86pw1p9','Kofi','Darko',NULL,'Science communicator and author of several popular science textbooks for schools.','Ghana',NULL,NULL,NULL,'active','2026-07-23 12:57:22','2026-07-23 12:57:22',NULL),(4,'01KY7GTMJ7R4FNSCSGG21SEFSY','01ky7gd0jm3wgn1bqba86pw1p9','Efua','Osei','Serwaa','Historian and writer specializing in pre-colonial Ghanaian history and oral traditions.','Ghana',NULL,NULL,NULL,'active','2026-07-23 12:57:22','2026-07-23 12:57:22',NULL),(5,'01KY7GTMMX9XJ8E5593VYEX0M8','01ky7gd0jm3wgn1bqba86pw1p9','Yaw','Boateng',NULL,'Mathematics educator and author of widely used secondary school mathematics guides.','Ghana',NULL,NULL,'https://yawboateng.edu','active','2026-07-23 12:57:22','2026-07-23 12:57:22',NULL);
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `automation_rules`
--

DROP TABLE IF EXISTS `automation_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `automation_rules` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `priority` smallint(5) unsigned NOT NULL DEFAULT 0,
  `trigger_type` varchar(30) NOT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`actions`)),
  `execution_count` int(10) unsigned NOT NULL DEFAULT 0,
  `success_count` int(10) unsigned NOT NULL DEFAULT 0,
  `failure_count` int(10) unsigned NOT NULL DEFAULT 0,
  `last_executed_at` timestamp NULL DEFAULT NULL,
  `cooldown_seconds` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_rules_school_id_is_active_index` (`school_id`,`is_active`),
  KEY `automation_rules_school_id_trigger_type_index` (`school_id`,`trigger_type`),
  KEY `automation_rules_event_name_index` (`event_name`),
  KEY `automation_rules_priority_index` (`priority`),
  CONSTRAINT `automation_rules_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `automation_rules`
--

LOCK TABLES `automation_rules` WRITE;
/*!40000 ALTER TABLE `automation_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `automation_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'manual',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `disk` varchar(50) NOT NULL DEFAULT 'local',
  `path` varchar(255) DEFAULT NULL,
  `size_bytes` bigint(20) unsigned DEFAULT NULL,
  `checksum` varchar(64) DEFAULT NULL,
  `tables_included` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tables_included`)),
  `notes` text DEFAULT NULL,
  `error` text DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `duration_ms` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `backups_school_id_status_index` (`school_id`,`status`),
  KEY `backups_status_index` (`status`),
  KEY `backups_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_author`
--

DROP TABLE IF EXISTS `book_author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_author` (
  `book_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`book_id`,`author_id`),
  KEY `book_author_author_id_foreign` (`author_id`),
  CONSTRAINT `book_author_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_author_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_author`
--

LOCK TABLES `book_author` WRITE;
/*!40000 ALTER TABLE `book_author` DISABLE KEYS */;
INSERT INTO `book_author` VALUES (1,1),(2,2),(3,3),(4,4),(5,5);
/*!40000 ALTER TABLE `book_author` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_categories`
--

DROP TABLE IF EXISTS `book_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `book_categories_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `book_categories_uuid_unique` (`uuid`),
  KEY `book_categories_status_index` (`status`),
  KEY `book_categories_school_id_status_index` (`school_id`,`status`),
  KEY `book_categories_parent_id_index` (`parent_id`),
  CONSTRAINT `book_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `book_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `book_categories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_categories`
--

LOCK TABLES `book_categories` WRITE;
/*!40000 ALTER TABLE `book_categories` DISABLE KEYS */;
INSERT INTO `book_categories` VALUES (1,'01KY7GTTKCSX8BVPMMZFNVBWE5','01ky7gd0jm3wgn1bqba86pw1p9','Fiction','Novels, short stories, and fictional works across all genres.',NULL,'active','2026-07-23 12:57:28','2026-07-23 12:57:28',NULL),(2,'01KY7GTV0MRKMF3K5M4P7VX9Q1','01ky7gd0jm3wgn1bqba86pw1p9','Science','Textbooks and reference materials for physics, chemistry, and biology.',NULL,'active','2026-07-23 12:57:29','2026-07-23 12:57:29',NULL),(3,'01KY7GTV5JAEZG2AKW0XTM1Q6Y','01ky7gd0jm3wgn1bqba86pw1p9','Mathematics','Mathematics textbooks, problem sets, and reference guides.',NULL,'active','2026-07-23 12:57:29','2026-07-23 12:57:29',NULL),(4,'01KY7GTV9SCJ2H9JN639DXYZ6B','01ky7gd0jm3wgn1bqba86pw1p9','History','Historical texts covering African and world history.',NULL,'active','2026-07-23 12:57:29','2026-07-23 12:57:29',NULL),(5,'01KY7GTVBH91QHNTPQHD38BZQT','01ky7gd0jm3wgn1bqba86pw1p9','Literature','Poetry, drama, literary criticism, and English language studies.',NULL,'active','2026-07-23 12:57:29','2026-07-23 12:57:29',NULL),(6,'01KY7GTVD0WBJKGJMWW436C7ES','01ky7gd0jm3wgn1bqba86pw1p9','Technology','Computer science, ICT, and technology-related materials.',NULL,'active','2026-07-23 12:57:29','2026-07-23 12:57:29',NULL);
/*!40000 ALTER TABLE `book_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_copies`
--

DROP TABLE IF EXISTS `book_copies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_copies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `book_id` bigint(20) unsigned NOT NULL,
  `barcode` varchar(50) NOT NULL,
  `inventory_number` varchar(50) NOT NULL,
  `acquisition_date` date DEFAULT NULL,
  `purchase_cost` decimal(10,2) DEFAULT NULL,
  `condition` varchar(20) NOT NULL DEFAULT 'good',
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `book_copies_school_id_barcode_unique` (`school_id`,`barcode`),
  UNIQUE KEY `book_copies_school_id_inventory_number_unique` (`school_id`,`inventory_number`),
  UNIQUE KEY `book_copies_uuid_unique` (`uuid`),
  KEY `book_copies_status_index` (`status`),
  KEY `book_copies_school_id_status_index` (`school_id`,`status`),
  KEY `book_copies_book_id_is_available_index` (`book_id`,`is_available`),
  KEY `book_copies_is_available_index` (`is_available`),
  CONSTRAINT `book_copies_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_copies_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_copies`
--

LOCK TABLES `book_copies` WRITE;
/*!40000 ALTER TABLE `book_copies` DISABLE KEYS */;
INSERT INTO `book_copies` VALUES (1,'01KY7GVHDC06BN1MTFF6RBEFEJ','01ky7gd0jm3wgn1bqba86pw1p9',1,'BC-51856','INV-18760','2026-05-23',54.28,'good',1,'available',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:52',NULL),(2,'01KY7GVHM6HKSKZTJ3FKFHW5K8','01ky7gd0jm3wgn1bqba86pw1p9',1,'BC-42249','INV-55362','2025-12-23',42.27,'good',0,'borrowed',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:56',NULL),(3,'01KY7GVHT921T8JJNYY2HVJZVD','01ky7gd0jm3wgn1bqba86pw1p9',2,'BC-98021','INV-03759','2025-08-23',106.89,'good',1,'available',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:52',NULL),(4,'01KY7GVHW0ZHG9S4RCY0FDET1T','01ky7gd0jm3wgn1bqba86pw1p9',2,'BC-61878','INV-65493','2025-07-23',87.16,'good',1,'available',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:52',NULL),(5,'01KY7GVHXFHTF7RD9N9SAPZCAD','01ky7gd0jm3wgn1bqba86pw1p9',3,'BC-33401','INV-25091','2025-09-23',44.00,'good',0,'borrowed',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:56',NULL),(6,'01KY7GVHZKZ141FFSRD4Y10AET','01ky7gd0jm3wgn1bqba86pw1p9',3,'BC-69793','INV-05128','2025-09-23',102.32,'good',1,'available',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:52',NULL),(7,'01KY7GVJ1AXZR1FM5HXK5XHAHK','01ky7gd0jm3wgn1bqba86pw1p9',3,'BC-79535','INV-34935','2026-05-23',99.89,'good',1,'available',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:52',NULL),(8,'01KY7GVJ330SCMA15724YYKCBP','01ky7gd0jm3wgn1bqba86pw1p9',4,'BC-64697','INV-10900','2026-06-23',51.59,'good',1,'available',NULL,'2026-07-23 12:57:52','2026-07-23 12:57:52',NULL),(9,'01KY7GVJB0QV8Z8D6Y46TFB53K','01ky7gd0jm3wgn1bqba86pw1p9',4,'BC-66259','INV-15725','2026-06-23',16.72,'good',1,'available',NULL,'2026-07-23 12:57:53','2026-07-23 12:57:53',NULL),(10,'01KY7GVJDEDMGTR6Y3H7WC8GJS','01ky7gd0jm3wgn1bqba86pw1p9',4,'BC-40833','INV-16334','2025-07-23',109.83,'good',0,'borrowed',NULL,'2026-07-23 12:57:53','2026-07-23 12:57:56',NULL),(11,'01KY7GVJEWSMFY67FFCQY75G7H','01ky7gd0jm3wgn1bqba86pw1p9',5,'BC-83736','INV-34027','2025-09-23',55.96,'good',1,'available',NULL,'2026-07-23 12:57:53','2026-07-23 12:57:53',NULL),(12,'01KY7GVJH80FW0WCQ8SC58ZWBT','01ky7gd0jm3wgn1bqba86pw1p9',5,'BC-57036','INV-10262','2025-09-23',41.31,'good',1,'available',NULL,'2026-07-23 12:57:53','2026-07-23 12:57:53',NULL),(13,'01KY7GVJK8Y857F84FH96WT937','01ky7gd0jm3wgn1bqba86pw1p9',5,'BC-86027','INV-69848','2025-12-23',70.10,'good',1,'available',NULL,'2026-07-23 12:57:53','2026-07-23 12:57:53',NULL);
/*!40000 ALTER TABLE `book_copies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_loans`
--

DROP TABLE IF EXISTS `book_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `book_loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `book_copy_id` bigint(20) unsigned NOT NULL,
  `borrower_id` bigint(20) unsigned NOT NULL,
  `borrower_type` varchar(20) NOT NULL DEFAULT 'student',
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `renewal_count` int(11) NOT NULL DEFAULT 0,
  `issued_by` bigint(20) unsigned DEFAULT NULL,
  `returned_to` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `book_loans_uuid_unique` (`uuid`),
  KEY `book_loans_status_index` (`status`),
  KEY `book_loans_school_id_status_index` (`school_id`,`status`),
  KEY `book_loans_borrower_id_borrower_type_index` (`borrower_id`,`borrower_type`),
  KEY `book_loans_book_copy_id_index` (`book_copy_id`),
  KEY `book_loans_due_date_index` (`due_date`),
  KEY `book_loans_status_due_date_index` (`status`,`due_date`),
  CONSTRAINT `book_loans_book_copy_id_foreign` FOREIGN KEY (`book_copy_id`) REFERENCES `book_copies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `book_loans_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_loans`
--

LOCK TABLES `book_loans` WRITE;
/*!40000 ALTER TABLE `book_loans` DISABLE KEYS */;
INSERT INTO `book_loans` VALUES (1,'01KY7GVMXX13F3FQ2YDBFK29AJ','01ky7gd0jm3wgn1bqba86pw1p9',5,1,'student','2026-07-13','2026-07-27',NULL,'active',0,NULL,NULL,NULL,'2026-07-23 12:57:55','2026-07-23 12:57:55',NULL),(2,'01KY7GVN3JHB60B6CJC27N23AT','01ky7gd0jm3wgn1bqba86pw1p9',10,1,'student','2026-07-03','2026-07-17',NULL,'overdue',0,NULL,NULL,NULL,'2026-07-23 12:57:56','2026-07-23 12:57:56',NULL),(3,'01KY7GVNAH231PH2TQ00TPCF8C','01ky7gd0jm3wgn1bqba86pw1p9',2,1,'student','2026-07-18','2026-08-01',NULL,'active',0,NULL,NULL,NULL,'2026-07-23 12:57:56','2026-07-23 12:57:56',NULL);
/*!40000 ALTER TABLE `book_loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `language` varchar(50) NOT NULL DEFAULT 'English',
  `edition` varchar(50) DEFAULT NULL,
  `publication_year` smallint(6) DEFAULT NULL,
  `publisher_id` bigint(20) unsigned DEFAULT NULL,
  `book_category_id` bigint(20) unsigned DEFAULT NULL,
  `shelf_location` varchar(100) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `books_school_id_isbn_unique` (`school_id`,`isbn`),
  UNIQUE KEY `books_uuid_unique` (`uuid`),
  KEY `books_status_index` (`status`),
  KEY `books_school_id_status_index` (`school_id`,`status`),
  KEY `books_publisher_id_index` (`publisher_id`),
  KEY `books_book_category_id_index` (`book_category_id`),
  KEY `books_title_index` (`title`),
  CONSTRAINT `books_book_category_id_foreign` FOREIGN KEY (`book_category_id`) REFERENCES `book_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `books_publisher_id_foreign` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `books_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,'01KY7GTXKFVYF6GCNBYZWKWSG2','01ky7gd0jm3wgn1bqba86pw1p9','978-0001-2345-6','The Gold Coast Memoirs','Stories from Independence','A sweeping novel about a family navigating the transition from colonial Gold Coast to independent Ghana. The narrative weaves through three generations, capturing the hopes and struggles of a nation finding its identity.','English','2nd',2018,1,1,'SH-01-01',NULL,'active','2026-07-23 12:57:32','2026-07-23 12:57:32',NULL),(2,'01KY7GTXXXG7WYBRNCSQBZEZVF','01ky7gd0jm3wgn1bqba86pw1p9','978-0002-3456-7','Ama and the Baobab Tree',NULL,'A young girl discovers the secret wisdom of the ancient baobab tree in her village. Through magical encounters, she learns about nature, community, and the importance of preserving Ghanaian traditions.','English','1st',2021,3,1,'SH-01-02',NULL,'active','2026-07-23 12:57:32','2026-07-23 12:57:32',NULL),(3,'01KY7GTYCS36Z250ZKWR8N142B','01ky7gd0jm3wgn1bqba86pw1p9','978-0003-4567-8','Understanding West African Science','From Traditional Knowledge to Modern Discovery','An exploration of scientific traditions in West Africa and their contributions to modern science. This textbook covers topics from traditional metallurgy to contemporary environmental science, with a focus on Ghanaian innovations.','English','3rd',2020,2,2,'SH-02-01',NULL,'active','2026-07-23 12:57:32','2026-07-23 12:57:32',NULL),(4,'01KY7GTYFH14S956YAX93X89SJ','01ky7gd0jm3wgn1bqba86pw1p9','978-0004-5678-9','Kings and Kingdoms of Pre-Colonial Ghana',NULL,'A comprehensive account of the great kingdoms that shaped the history of present-day Ghana before colonial rule. Covers the Ashanti Empire, the Dagbon Kingdom, and the Fante Confederacy with detailed maps and illustrations.','English','1st',2019,2,4,'SH-03-01',NULL,'active','2026-07-23 12:57:32','2026-07-23 12:57:32',NULL),(5,'01KY7GTYHQJB24MG5FCE55ZCHW','01ky7gd0jm3wgn1bqba86pw1p9',NULL,'Mastering Senior High Mathematics','A Complete Guide for WASSCE Preparation','A comprehensive mathematics textbook aligned with the Ghana Education Service curriculum for Senior High Schools. Includes worked examples, practice problems, and past WASSCE questions with detailed solutions.','English','4th',2023,1,3,'SH-04-01',NULL,'active','2026-07-23 12:57:32','2026-07-23 12:57:32',NULL);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('nexus-school-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6','i:2;',1791916589),('nexus-school-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6:timer','i:1791916589;',1791916589),('nexus-school-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:3:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";}s:11:\"permissions\";a:171:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"school.view\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:13:\"school.create\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:13:\"school.update\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:13:\"school.delete\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:9:\"user.view\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:11:\"user.create\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:11:\"user.update\";s:1:\"c\";s:3:\"web\";}i:7;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:11:\"user.delete\";s:1:\"c\";s:3:\"web\";}i:8;a:3:{s:1:\"a\";i:9;s:1:\"b\";s:9:\"role.view\";s:1:\"c\";s:3:\"web\";}i:9;a:3:{s:1:\"a\";i:10;s:1:\"b\";s:11:\"role.create\";s:1:\"c\";s:3:\"web\";}i:10;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:11:\"role.update\";s:1:\"c\";s:3:\"web\";}i:11;a:3:{s:1:\"a\";i:12;s:1:\"b\";s:11:\"role.delete\";s:1:\"c\";s:3:\"web\";}i:12;a:3:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"permission.view\";s:1:\"c\";s:3:\"web\";}i:13;a:3:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"permission.create\";s:1:\"c\";s:3:\"web\";}i:14;a:3:{s:1:\"a\";i:15;s:1:\"b\";s:17:\"permission.update\";s:1:\"c\";s:3:\"web\";}i:15;a:3:{s:1:\"a\";i:16;s:1:\"b\";s:17:\"permission.delete\";s:1:\"c\";s:3:\"web\";}i:16;a:3:{s:1:\"a\";i:17;s:1:\"b\";s:13:\"academic.view\";s:1:\"c\";s:3:\"web\";}i:17;a:3:{s:1:\"a\";i:18;s:1:\"b\";s:15:\"academic.create\";s:1:\"c\";s:3:\"web\";}i:18;a:3:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"academic.update\";s:1:\"c\";s:3:\"web\";}i:19;a:3:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"academic.delete\";s:1:\"c\";s:3:\"web\";}i:20;a:3:{s:1:\"a\";i:21;s:1:\"b\";s:19:\"academic_class.view\";s:1:\"c\";s:3:\"web\";}i:21;a:3:{s:1:\"a\";i:22;s:1:\"b\";s:21:\"academic_class.create\";s:1:\"c\";s:3:\"web\";}i:22;a:3:{s:1:\"a\";i:23;s:1:\"b\";s:21:\"academic_class.update\";s:1:\"c\";s:3:\"web\";}i:23;a:3:{s:1:\"a\";i:24;s:1:\"b\";s:21:\"academic_class.delete\";s:1:\"c\";s:3:\"web\";}i:24;a:3:{s:1:\"a\";i:25;s:1:\"b\";s:18:\"academic_term.view\";s:1:\"c\";s:3:\"web\";}i:25;a:3:{s:1:\"a\";i:26;s:1:\"b\";s:20:\"academic_term.create\";s:1:\"c\";s:3:\"web\";}i:26;a:3:{s:1:\"a\";i:27;s:1:\"b\";s:20:\"academic_term.update\";s:1:\"c\";s:3:\"web\";}i:27;a:3:{s:1:\"a\";i:28;s:1:\"b\";s:20:\"academic_term.delete\";s:1:\"c\";s:3:\"web\";}i:28;a:3:{s:1:\"a\";i:29;s:1:\"b\";s:18:\"academic_year.view\";s:1:\"c\";s:3:\"web\";}i:29;a:3:{s:1:\"a\";i:30;s:1:\"b\";s:20:\"academic_year.create\";s:1:\"c\";s:3:\"web\";}i:30;a:3:{s:1:\"a\";i:31;s:1:\"b\";s:20:\"academic_year.update\";s:1:\"c\";s:3:\"web\";}i:31;a:3:{s:1:\"a\";i:32;s:1:\"b\";s:20:\"academic_year.delete\";s:1:\"c\";s:3:\"web\";}i:32;a:3:{s:1:\"a\";i:33;s:1:\"b\";s:12:\"student.view\";s:1:\"c\";s:3:\"web\";}i:33;a:3:{s:1:\"a\";i:34;s:1:\"b\";s:14:\"student.create\";s:1:\"c\";s:3:\"web\";}i:34;a:3:{s:1:\"a\";i:35;s:1:\"b\";s:14:\"student.update\";s:1:\"c\";s:3:\"web\";}i:35;a:3:{s:1:\"a\";i:36;s:1:\"b\";s:14:\"student.delete\";s:1:\"c\";s:3:\"web\";}i:36;a:3:{s:1:\"a\";i:37;s:1:\"b\";s:23:\"student_enrollment.view\";s:1:\"c\";s:3:\"web\";}i:37;a:3:{s:1:\"a\";i:38;s:1:\"b\";s:25:\"student_enrollment.create\";s:1:\"c\";s:3:\"web\";}i:38;a:3:{s:1:\"a\";i:39;s:1:\"b\";s:25:\"student_enrollment.update\";s:1:\"c\";s:3:\"web\";}i:39;a:3:{s:1:\"a\";i:40;s:1:\"b\";s:25:\"student_enrollment.delete\";s:1:\"c\";s:3:\"web\";}i:40;a:3:{s:1:\"a\";i:41;s:1:\"b\";s:12:\"teacher.view\";s:1:\"c\";s:3:\"web\";}i:41;a:3:{s:1:\"a\";i:42;s:1:\"b\";s:14:\"teacher.create\";s:1:\"c\";s:3:\"web\";}i:42;a:3:{s:1:\"a\";i:43;s:1:\"b\";s:14:\"teacher.update\";s:1:\"c\";s:3:\"web\";}i:43;a:3:{s:1:\"a\";i:44;s:1:\"b\";s:14:\"teacher.delete\";s:1:\"c\";s:3:\"web\";}i:44;a:3:{s:1:\"a\";i:45;s:1:\"b\";s:13:\"guardian.view\";s:1:\"c\";s:3:\"web\";}i:45;a:3:{s:1:\"a\";i:46;s:1:\"b\";s:15:\"guardian.create\";s:1:\"c\";s:3:\"web\";}i:46;a:3:{s:1:\"a\";i:47;s:1:\"b\";s:15:\"guardian.update\";s:1:\"c\";s:3:\"web\";}i:47;a:3:{s:1:\"a\";i:48;s:1:\"b\";s:15:\"guardian.delete\";s:1:\"c\";s:3:\"web\";}i:48;a:3:{s:1:\"a\";i:49;s:1:\"b\";s:14:\"admission.view\";s:1:\"c\";s:3:\"web\";}i:49;a:3:{s:1:\"a\";i:50;s:1:\"b\";s:16:\"admission.create\";s:1:\"c\";s:3:\"web\";}i:50;a:3:{s:1:\"a\";i:51;s:1:\"b\";s:16:\"admission.update\";s:1:\"c\";s:3:\"web\";}i:51;a:3:{s:1:\"a\";i:52;s:1:\"b\";s:16:\"admission.delete\";s:1:\"c\";s:3:\"web\";}i:52;a:3:{s:1:\"a\";i:53;s:1:\"b\";s:15:\"attendance.view\";s:1:\"c\";s:3:\"web\";}i:53;a:3:{s:1:\"a\";i:54;s:1:\"b\";s:17:\"attendance.create\";s:1:\"c\";s:3:\"web\";}i:54;a:3:{s:1:\"a\";i:55;s:1:\"b\";s:17:\"attendance.update\";s:1:\"c\";s:3:\"web\";}i:55;a:3:{s:1:\"a\";i:56;s:1:\"b\";s:17:\"attendance.delete\";s:1:\"c\";s:3:\"web\";}i:56;a:3:{s:1:\"a\";i:57;s:1:\"b\";s:16:\"examination.view\";s:1:\"c\";s:3:\"web\";}i:57;a:3:{s:1:\"a\";i:58;s:1:\"b\";s:18:\"examination.create\";s:1:\"c\";s:3:\"web\";}i:58;a:3:{s:1:\"a\";i:59;s:1:\"b\";s:18:\"examination.update\";s:1:\"c\";s:3:\"web\";}i:59;a:3:{s:1:\"a\";i:60;s:1:\"b\";s:18:\"examination.delete\";s:1:\"c\";s:3:\"web\";}i:60;a:3:{s:1:\"a\";i:61;s:1:\"b\";s:14:\"gradebook.view\";s:1:\"c\";s:3:\"web\";}i:61;a:3:{s:1:\"a\";i:62;s:1:\"b\";s:16:\"gradebook.create\";s:1:\"c\";s:3:\"web\";}i:62;a:3:{s:1:\"a\";i:63;s:1:\"b\";s:16:\"gradebook.update\";s:1:\"c\";s:3:\"web\";}i:63;a:3:{s:1:\"a\";i:64;s:1:\"b\";s:16:\"gradebook.delete\";s:1:\"c\";s:3:\"web\";}i:64;a:3:{s:1:\"a\";i:65;s:1:\"b\";s:10:\"grade.view\";s:1:\"c\";s:3:\"web\";}i:65;a:3:{s:1:\"a\";i:66;s:1:\"b\";s:12:\"grade.create\";s:1:\"c\";s:3:\"web\";}i:66;a:3:{s:1:\"a\";i:67;s:1:\"b\";s:12:\"grade.update\";s:1:\"c\";s:3:\"web\";}i:67;a:3:{s:1:\"a\";i:68;s:1:\"b\";s:12:\"grade.delete\";s:1:\"c\";s:3:\"web\";}i:68;a:3:{s:1:\"a\";i:69;s:1:\"b\";s:16:\"grade_level.view\";s:1:\"c\";s:3:\"web\";}i:69;a:3:{s:1:\"a\";i:70;s:1:\"b\";s:18:\"grade_level.create\";s:1:\"c\";s:3:\"web\";}i:70;a:3:{s:1:\"a\";i:71;s:1:\"b\";s:18:\"grade_level.update\";s:1:\"c\";s:3:\"web\";}i:71;a:3:{s:1:\"a\";i:72;s:1:\"b\";s:18:\"grade_level.delete\";s:1:\"c\";s:3:\"web\";}i:72;a:3:{s:1:\"a\";i:73;s:1:\"b\";s:12:\"section.view\";s:1:\"c\";s:3:\"web\";}i:73;a:3:{s:1:\"a\";i:74;s:1:\"b\";s:14:\"section.create\";s:1:\"c\";s:3:\"web\";}i:74;a:3:{s:1:\"a\";i:75;s:1:\"b\";s:14:\"section.update\";s:1:\"c\";s:3:\"web\";}i:75;a:3:{s:1:\"a\";i:76;s:1:\"b\";s:14:\"section.delete\";s:1:\"c\";s:3:\"web\";}i:76;a:3:{s:1:\"a\";i:77;s:1:\"b\";s:14:\"classroom.view\";s:1:\"c\";s:3:\"web\";}i:77;a:3:{s:1:\"a\";i:78;s:1:\"b\";s:16:\"classroom.create\";s:1:\"c\";s:3:\"web\";}i:78;a:3:{s:1:\"a\";i:79;s:1:\"b\";s:16:\"classroom.update\";s:1:\"c\";s:3:\"web\";}i:79;a:3:{s:1:\"a\";i:80;s:1:\"b\";s:16:\"classroom.delete\";s:1:\"c\";s:3:\"web\";}i:80;a:3:{s:1:\"a\";i:81;s:1:\"b\";s:15:\"department.view\";s:1:\"c\";s:3:\"web\";}i:81;a:3:{s:1:\"a\";i:82;s:1:\"b\";s:17:\"department.create\";s:1:\"c\";s:3:\"web\";}i:82;a:3:{s:1:\"a\";i:83;s:1:\"b\";s:17:\"department.update\";s:1:\"c\";s:3:\"web\";}i:83;a:3:{s:1:\"a\";i:84;s:1:\"b\";s:17:\"department.delete\";s:1:\"c\";s:3:\"web\";}i:84;a:3:{s:1:\"a\";i:85;s:1:\"b\";s:12:\"program.view\";s:1:\"c\";s:3:\"web\";}i:85;a:3:{s:1:\"a\";i:86;s:1:\"b\";s:14:\"program.create\";s:1:\"c\";s:3:\"web\";}i:86;a:3:{s:1:\"a\";i:87;s:1:\"b\";s:14:\"program.update\";s:1:\"c\";s:3:\"web\";}i:87;a:3:{s:1:\"a\";i:88;s:1:\"b\";s:14:\"program.delete\";s:1:\"c\";s:3:\"web\";}i:88;a:3:{s:1:\"a\";i:89;s:1:\"b\";s:12:\"subject.view\";s:1:\"c\";s:3:\"web\";}i:89;a:3:{s:1:\"a\";i:90;s:1:\"b\";s:14:\"subject.create\";s:1:\"c\";s:3:\"web\";}i:90;a:3:{s:1:\"a\";i:91;s:1:\"b\";s:14:\"subject.update\";s:1:\"c\";s:3:\"web\";}i:91;a:3:{s:1:\"a\";i:92;s:1:\"b\";s:14:\"subject.delete\";s:1:\"c\";s:3:\"web\";}i:92;a:3:{s:1:\"a\";i:93;s:1:\"b\";s:21:\"subject_category.view\";s:1:\"c\";s:3:\"web\";}i:93;a:3:{s:1:\"a\";i:94;s:1:\"b\";s:23:\"subject_category.create\";s:1:\"c\";s:3:\"web\";}i:94;a:3:{s:1:\"a\";i:95;s:1:\"b\";s:23:\"subject_category.update\";s:1:\"c\";s:3:\"web\";}i:95;a:3:{s:1:\"a\";i:96;s:1:\"b\";s:23:\"subject_category.delete\";s:1:\"c\";s:3:\"web\";}i:96;a:3:{s:1:\"a\";i:97;s:1:\"b\";s:24:\"subject_coefficient.view\";s:1:\"c\";s:3:\"web\";}i:97;a:3:{s:1:\"a\";i:98;s:1:\"b\";s:26:\"subject_coefficient.create\";s:1:\"c\";s:3:\"web\";}i:98;a:3:{s:1:\"a\";i:99;s:1:\"b\";s:26:\"subject_coefficient.update\";s:1:\"c\";s:3:\"web\";}i:99;a:3:{s:1:\"a\";i:100;s:1:\"b\";s:26:\"subject_coefficient.delete\";s:1:\"c\";s:3:\"web\";}i:100;a:3:{s:1:\"a\";i:101;s:1:\"b\";s:12:\"finance.view\";s:1:\"c\";s:3:\"web\";}i:101;a:3:{s:1:\"a\";i:102;s:1:\"b\";s:14:\"finance.create\";s:1:\"c\";s:3:\"web\";}i:102;a:3:{s:1:\"a\";i:103;s:1:\"b\";s:14:\"finance.update\";s:1:\"c\";s:3:\"web\";}i:103;a:3:{s:1:\"a\";i:104;s:1:\"b\";s:14:\"finance.delete\";s:1:\"c\";s:3:\"web\";}i:104;a:3:{s:1:\"a\";i:105;s:1:\"b\";s:12:\"library.view\";s:1:\"c\";s:3:\"web\";}i:105;a:3:{s:1:\"a\";i:106;s:1:\"b\";s:14:\"library.create\";s:1:\"c\";s:3:\"web\";}i:106;a:3:{s:1:\"a\";i:107;s:1:\"b\";s:14:\"library.update\";s:1:\"c\";s:3:\"web\";}i:107;a:3:{s:1:\"a\";i:108;s:1:\"b\";s:14:\"library.delete\";s:1:\"c\";s:3:\"web\";}i:108;a:3:{s:1:\"a\";i:109;s:1:\"b\";s:7:\"hr.view\";s:1:\"c\";s:3:\"web\";}i:109;a:3:{s:1:\"a\";i:110;s:1:\"b\";s:9:\"hr.create\";s:1:\"c\";s:3:\"web\";}i:110;a:3:{s:1:\"a\";i:111;s:1:\"b\";s:9:\"hr.update\";s:1:\"c\";s:3:\"web\";}i:111;a:3:{s:1:\"a\";i:112;s:1:\"b\";s:9:\"hr.delete\";s:1:\"c\";s:3:\"web\";}i:112;a:3:{s:1:\"a\";i:113;s:1:\"b\";s:14:\"inventory.view\";s:1:\"c\";s:3:\"web\";}i:113;a:3:{s:1:\"a\";i:114;s:1:\"b\";s:16:\"inventory.create\";s:1:\"c\";s:3:\"web\";}i:114;a:3:{s:1:\"a\";i:115;s:1:\"b\";s:16:\"inventory.update\";s:1:\"c\";s:3:\"web\";}i:115;a:3:{s:1:\"a\";i:116;s:1:\"b\";s:16:\"inventory.delete\";s:1:\"c\";s:3:\"web\";}i:116;a:3:{s:1:\"a\";i:117;s:1:\"b\";s:14:\"transport.view\";s:1:\"c\";s:3:\"web\";}i:117;a:3:{s:1:\"a\";i:118;s:1:\"b\";s:16:\"transport.create\";s:1:\"c\";s:3:\"web\";}i:118;a:3:{s:1:\"a\";i:119;s:1:\"b\";s:16:\"transport.update\";s:1:\"c\";s:3:\"web\";}i:119;a:3:{s:1:\"a\";i:120;s:1:\"b\";s:16:\"transport.delete\";s:1:\"c\";s:3:\"web\";}i:120;a:3:{s:1:\"a\";i:121;s:1:\"b\";s:18:\"communication.view\";s:1:\"c\";s:3:\"web\";}i:121;a:3:{s:1:\"a\";i:122;s:1:\"b\";s:20:\"communication.create\";s:1:\"c\";s:3:\"web\";}i:122;a:3:{s:1:\"a\";i:123;s:1:\"b\";s:20:\"communication.update\";s:1:\"c\";s:3:\"web\";}i:123;a:3:{s:1:\"a\";i:124;s:1:\"b\";s:20:\"communication.delete\";s:1:\"c\";s:3:\"web\";}i:124;a:3:{s:1:\"a\";i:125;s:1:\"b\";s:11:\"report.view\";s:1:\"c\";s:3:\"web\";}i:125;a:3:{s:1:\"a\";i:126;s:1:\"b\";s:13:\"report.create\";s:1:\"c\";s:3:\"web\";}i:126;a:3:{s:1:\"a\";i:127;s:1:\"b\";s:13:\"report.update\";s:1:\"c\";s:3:\"web\";}i:127;a:3:{s:1:\"a\";i:128;s:1:\"b\";s:13:\"report.delete\";s:1:\"c\";s:3:\"web\";}i:128;a:3:{s:1:\"a\";i:129;s:1:\"b\";s:12:\"setting.view\";s:1:\"c\";s:3:\"web\";}i:129;a:3:{s:1:\"a\";i:130;s:1:\"b\";s:14:\"setting.create\";s:1:\"c\";s:3:\"web\";}i:130;a:3:{s:1:\"a\";i:131;s:1:\"b\";s:14:\"setting.update\";s:1:\"c\";s:3:\"web\";}i:131;a:3:{s:1:\"a\";i:132;s:1:\"b\";s:14:\"setting.delete\";s:1:\"c\";s:3:\"web\";}i:132;a:3:{s:1:\"a\";i:133;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:3:\"web\";}i:133;a:3:{s:1:\"a\";i:134;s:1:\"b\";s:16:\"dashboard.create\";s:1:\"c\";s:3:\"web\";}i:134;a:3:{s:1:\"a\";i:135;s:1:\"b\";s:16:\"dashboard.update\";s:1:\"c\";s:3:\"web\";}i:135;a:3:{s:1:\"a\";i:136;s:1:\"b\";s:16:\"dashboard.delete\";s:1:\"c\";s:3:\"web\";}i:136;a:3:{s:1:\"a\";i:137;s:1:\"b\";s:14:\"audit_log.view\";s:1:\"c\";s:3:\"web\";}i:137;a:3:{s:1:\"a\";i:138;s:1:\"b\";s:16:\"audit_log.create\";s:1:\"c\";s:3:\"web\";}i:138;a:3:{s:1:\"a\";i:139;s:1:\"b\";s:16:\"audit_log.update\";s:1:\"c\";s:3:\"web\";}i:139;a:3:{s:1:\"a\";i:140;s:1:\"b\";s:16:\"audit_log.delete\";s:1:\"c\";s:3:\"web\";}i:140;a:3:{s:1:\"a\";i:141;s:1:\"b\";s:7:\"ai.view\";s:1:\"c\";s:3:\"web\";}i:141;a:3:{s:1:\"a\";i:142;s:1:\"b\";s:9:\"ai.create\";s:1:\"c\";s:3:\"web\";}i:142;a:3:{s:1:\"a\";i:143;s:1:\"b\";s:9:\"ai.update\";s:1:\"c\";s:3:\"web\";}i:143;a:3:{s:1:\"a\";i:144;s:1:\"b\";s:9:\"ai.delete\";s:1:\"c\";s:3:\"web\";}i:144;a:3:{s:1:\"a\";i:145;s:1:\"b\";s:14:\"analytics.view\";s:1:\"c\";s:3:\"web\";}i:145;a:3:{s:1:\"a\";i:146;s:1:\"b\";s:16:\"analytics.create\";s:1:\"c\";s:3:\"web\";}i:146;a:3:{s:1:\"a\";i:147;s:1:\"b\";s:16:\"analytics.update\";s:1:\"c\";s:3:\"web\";}i:147;a:3:{s:1:\"a\";i:148;s:1:\"b\";s:16:\"analytics.delete\";s:1:\"c\";s:3:\"web\";}i:148;a:3:{s:1:\"a\";i:149;s:1:\"b\";s:8:\"api.view\";s:1:\"c\";s:3:\"web\";}i:149;a:3:{s:1:\"a\";i:150;s:1:\"b\";s:10:\"api.create\";s:1:\"c\";s:3:\"web\";}i:150;a:3:{s:1:\"a\";i:151;s:1:\"b\";s:10:\"api.update\";s:1:\"c\";s:3:\"web\";}i:151;a:3:{s:1:\"a\";i:152;s:1:\"b\";s:10:\"api.delete\";s:1:\"c\";s:3:\"web\";}i:152;a:3:{s:1:\"a\";i:153;s:1:\"b\";s:11:\"backup.view\";s:1:\"c\";s:3:\"web\";}i:153;a:3:{s:1:\"a\";i:154;s:1:\"b\";s:13:\"backup.create\";s:1:\"c\";s:3:\"web\";}i:154;a:3:{s:1:\"a\";i:155;s:1:\"b\";s:13:\"backup.update\";s:1:\"c\";s:3:\"web\";}i:155;a:3:{s:1:\"a\";i:156;s:1:\"b\";s:13:\"backup.delete\";s:1:\"c\";s:3:\"web\";}i:156;a:3:{s:1:\"a\";i:157;s:1:\"b\";s:23:\"maintenance_window.view\";s:1:\"c\";s:3:\"web\";}i:157;a:3:{s:1:\"a\";i:158;s:1:\"b\";s:25:\"maintenance_window.create\";s:1:\"c\";s:3:\"web\";}i:158;a:3:{s:1:\"a\";i:159;s:1:\"b\";s:25:\"maintenance_window.update\";s:1:\"c\";s:3:\"web\";}i:159;a:3:{s:1:\"a\";i:160;s:1:\"b\";s:25:\"maintenance_window.delete\";s:1:\"c\";s:3:\"web\";}i:160;a:3:{s:1:\"a\";i:161;s:1:\"b\";s:25:\"notification_channel.view\";s:1:\"c\";s:3:\"web\";}i:161;a:3:{s:1:\"a\";i:162;s:1:\"b\";s:27:\"notification_channel.create\";s:1:\"c\";s:3:\"web\";}i:162;a:3:{s:1:\"a\";i:163;s:1:\"b\";s:27:\"notification_channel.update\";s:1:\"c\";s:3:\"web\";}i:163;a:3:{s:1:\"a\";i:164;s:1:\"b\";s:27:\"notification_channel.delete\";s:1:\"c\";s:3:\"web\";}i:164;a:3:{s:1:\"a\";i:165;s:1:\"b\";s:13:\"workflow.view\";s:1:\"c\";s:3:\"web\";}i:165;a:3:{s:1:\"a\";i:166;s:1:\"b\";s:15:\"workflow.create\";s:1:\"c\";s:3:\"web\";}i:166;a:3:{s:1:\"a\";i:167;s:1:\"b\";s:15:\"workflow.update\";s:1:\"c\";s:3:\"web\";}i:167;a:3:{s:1:\"a\";i:168;s:1:\"b\";s:15:\"workflow.delete\";s:1:\"c\";s:3:\"web\";}i:168;a:3:{s:1:\"a\";i:169;s:1:\"b\";s:16:\"workflow.execute\";s:1:\"c\";s:3:\"web\";}i:169;a:3:{s:1:\"a\";i:170;s:1:\"b\";s:15:\"workflow.manage\";s:1:\"c\";s:3:\"web\";}i:170;a:3:{s:1:\"a\";i:171;s:1:\"b\";s:16:\"workflow.publish\";s:1:\"c\";s:3:\"web\";}}s:5:\"roles\";a:0:{}}',1791973676),('nexus-school-cache-superadmin.revenue_chart','a:2:{s:8:\"datasets\";a:1:{i:0;a:6:{s:5:\"label\";s:7:\"Revenus\";s:4:\"data\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;}s:11:\"borderColor\";s:7:\"#10b981\";s:15:\"backgroundColor\";s:23:\"rgba(16, 185, 129, 0.1)\";s:4:\"fill\";b:1;s:7:\"tension\";d:0.4;}}s:6:\"labels\";a:12:{i:0;s:3:\"Jan\";i:1;s:4:\"Fév\";i:2;s:3:\"Mar\";i:3;s:3:\"Avr\";i:4;s:3:\"Mai\";i:5;s:3:\"Jun\";i:6;s:3:\"Jul\";i:7;s:4:\"Aoû\";i:8;s:3:\"Sep\";i:9;s:3:\"Oct\";i:10;s:3:\"Nov\";i:11;s:4:\"Déc\";}}',1791916559),('nexus-school-cache-superadmin.schools_chart','a:2:{s:8:\"datasets\";a:1:{i:0;a:3:{s:5:\"label\";s:16:\"Écoles créées\";s:4:\"data\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:1;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;}s:15:\"backgroundColor\";s:7:\"#3b82f6\";}}s:6:\"labels\";a:12:{i:0;s:3:\"Jan\";i:1;s:4:\"Fév\";i:2;s:3:\"Mar\";i:3;s:3:\"Avr\";i:4;s:3:\"Mai\";i:5;s:3:\"Jun\";i:6;s:3:\"Jul\";i:7;s:4:\"Aoû\";i:8;s:3:\"Sep\";i:9;s:3:\"Oct\";i:10;s:3:\"Nov\";i:11;s:4:\"Déc\";}}',1791916568),('nexus-school-cache-superadmin.stats_overview','a:8:{s:12:\"totalSchools\";i:1;s:13:\"activeSchools\";i:1;s:10:\"totalUsers\";i:4;s:13:\"totalStudents\";i:5;s:19:\"activeSubscriptions\";i:0;s:10:\"totalPlans\";i:4;s:14:\"monthlyRevenue\";i:0;s:13:\"yearlyRevenue\";i:0;}',1791917164),('nexus-school-cache-superadmin.subscription_status_chart','a:2:{s:8:\"datasets\";a:1:{i:0;a:2:{s:4:\"data\";a:4:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;}s:15:\"backgroundColor\";a:4:{i:0;s:7:\"#10b981\";i:1;s:7:\"#f59e0b\";i:2;s:7:\"#ef4444\";i:3;s:7:\"#6b7280\";}}}s:6:\"labels\";a:4:{i:0;s:5:\"Actif\";i:1;s:7:\"Expiré\";i:2;s:8:\"Suspendu\";i:3;s:7:\"Annulé\";}}',1791916576);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campuses`
--

DROP TABLE IF EXISTS `campuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `display_order` int(10) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campuses_school_id_code_unique` (`school_id`,`code`),
  KEY `campuses_school_id_status_index` (`school_id`,`status`),
  CONSTRAINT `campuses_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campuses`
--

LOCK TABLES `campuses` WRITE;
/*!40000 ALTER TABLE `campuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `campuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_sections`
--

DROP TABLE IF EXISTS `class_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `class_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `capacity` int(10) unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_sections_school_id_school_class_id_name_unique` (`school_id`,`school_class_id`,`name`),
  UNIQUE KEY `class_sections_uuid_unique` (`uuid`),
  KEY `class_sections_school_class_id_foreign` (`school_class_id`),
  KEY `class_sections_status_index` (`status`),
  CONSTRAINT `class_sections_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_sections`
--

LOCK TABLES `class_sections` WRITE;
/*!40000 ALTER TABLE `class_sections` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classrooms`
--

DROP TABLE IF EXISTS `classrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classrooms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `building` varchar(100) DEFAULT NULL,
  `floor` varchar(50) DEFAULT NULL,
  `capacity` int(10) unsigned NOT NULL DEFAULT 0,
  `room_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classrooms_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `classrooms_school_code_unique` (`school_id`,`code`),
  UNIQUE KEY `classrooms_uuid_unique` (`uuid`),
  KEY `classrooms_status_index` (`status`),
  KEY `classrooms_school_id_display_order_index` (`school_id`,`display_order`),
  CONSTRAINT `classrooms_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classrooms`
--

LOCK TABLES `classrooms` WRITE;
/*!40000 ALTER TABLE `classrooms` DISABLE KEYS */;
INSERT INTO `classrooms` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Room 101','R101','Building A','1',40,'standard_classroom','Standard classroom on ground floor',1,'active','2026-07-23 12:50:48','2026-07-23 12:50:48',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Room 102','R102','Building A','1',35,'standard_classroom','Standard classroom on ground floor',2,'active','2026-07-23 12:50:49','2026-07-23 12:50:49',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Science Lab 1','SL1','Building A','2',30,'science_laboratory','Fully equipped science laboratory',3,'active','2026-07-23 12:50:51','2026-07-23 12:50:51',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Computer Lab 1','CL1','Building B','1',35,'computer_laboratory','Computer lab with 35 workstations',4,'active','2026-07-23 12:50:51','2026-07-23 12:50:51',NULL),(5,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Main Library','LIB','Building B','2',60,'library','School main library',5,'active','2026-07-23 12:50:52','2026-07-23 12:50:52',NULL),(6,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Exam Hall','EXH','Building C','1',100,'examination_hall','Large examination hall',6,'active','2026-07-23 12:50:52','2026-07-23 12:50:52',NULL);
/*!40000 ALTER TABLE `classrooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coefficients`
--

DROP TABLE IF EXISTS `coefficients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coefficients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `school_class_id` bigint(20) unsigned NOT NULL,
  `academic_subject_id` bigint(20) unsigned NOT NULL,
  `coefficient` decimal(5,2) NOT NULL DEFAULT 1.00,
  `is_optional` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coefficients_school_class_id_foreign` (`school_class_id`),
  KEY `coefficients_academic_subject_id_foreign` (`academic_subject_id`),
  CONSTRAINT `coefficients_academic_subject_id_foreign` FOREIGN KEY (`academic_subject_id`) REFERENCES `academic_subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coefficients_school_class_id_foreign` FOREIGN KEY (`school_class_id`) REFERENCES `school_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coefficients`
--

LOCK TABLES `coefficients` WRITE;
/*!40000 ALTER TABLE `coefficients` DISABLE KEYS */;
/*!40000 ALTER TABLE `coefficients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_channels`
--

DROP TABLE IF EXISTS `communication_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_channels` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `channel_type` varchar(36) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `from_address` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communication_channels_school_id_index` (`school_id`),
  KEY `communication_channels_channel_type_index` (`channel_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_channels`
--

LOCK TABLES `communication_channels` WRITE;
/*!40000 ALTER TABLE `communication_channels` DISABLE KEYS */;
INSERT INTO `communication_channels` VALUES ('01KY7H9C7XBD9FV3G3G4TPNH5Y','01ky7gd0jm3wgn1bqba86pw1p9','School Email','email',1,'{\"driver\":\"smtp\",\"host\":\"smtp.mailtrap.io\",\"port\":587}','noreply@nexus-demo.com','Nexus School','2026-07-23 13:05:25','2026-07-23 13:05:25'),('01KY7H9C9VDGVSVP3CV7EGHMMB','01ky7gd0jm3wgn1bqba86pw1p9','SMS Gateway','sms',1,'{\"provider\":\"twilio\",\"from\":\"+233000000000\"}',NULL,'NEXUS','2026-07-23 13:05:25','2026-07-23 13:05:25'),('01KY7H9CBSJC8462ZV53TG7R8W','01ky7gd0jm3wgn1bqba86pw1p9','WhatsApp Business','whatsapp',0,'{\"provider\":\"whatsapp_business_api\"}',NULL,NULL,'2026-07-23 13:05:25','2026-07-23 13:05:25'),('01KY7H9CD15P7AN81KP6W2BZR2','01ky7gd0jm3wgn1bqba86pw1p9','Push Notifications','push',1,'{\"driver\":\"fcm\"}',NULL,NULL,'2026-07-23 13:05:25','2026-07-23 13:05:25'),('01KY7H9CEM5KZVC6Y0CVJN41TE','01ky7gd0jm3wgn1bqba86pw1p9','In-App Notifications','in_app',1,'[]',NULL,NULL,'2026-07-23 13:05:25','2026-07-23 13:05:25');
/*!40000 ALTER TABLE `communication_channels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard_widgets`
--

DROP TABLE IF EXISTS `dashboard_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dashboard_widgets` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `dashboard_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `widget_type` varchar(36) NOT NULL,
  `data_source` varchar(255) NOT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `position` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_widgets_school_id_index` (`school_id`),
  KEY `dashboard_widgets_dashboard_id_index` (`dashboard_id`),
  CONSTRAINT `dashboard_widgets_dashboard_id_foreign` FOREIGN KEY (`dashboard_id`) REFERENCES `dashboards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard_widgets`
--

LOCK TABLES `dashboard_widgets` WRITE;
/*!40000 ALTER TABLE `dashboard_widgets` DISABLE KEYS */;
INSERT INTO `dashboard_widgets` VALUES ('01KY7HA83VAHHCXBNCMWEM55HK','01ky7gd0jm3wgn1bqba86pw1p9','01KY7HA5NFRHZT58JVEDEVQAAG','Total Students','metric','enrollment_stats',NULL,0,1,'2026-07-23 13:05:54','2026-07-23 13:05:54',NULL),('01KY7HA85KC675M8H15CEE7344','01ky7gd0jm3wgn1bqba86pw1p9','01KY7HA5NFRHZT58JVEDEVQAAG','Attendance Rate','metric','attendance_stats',NULL,1,1,'2026-07-23 13:05:54','2026-07-23 13:05:54',NULL),('01KY7HA8B6WM2ARVHP2EW9K4N9','01ky7gd0jm3wgn1bqba86pw1p9','01KY7HA5NFRHZT58JVEDEVQAAG','Fee Collection','metric','finance_stats',NULL,2,1,'2026-07-23 13:05:54','2026-07-23 13:05:54',NULL),('01KY7HA8CKSQBXY15FT92Q2G6F','01ky7gd0jm3wgn1bqba86pw1p9','01KY7HA5NFRHZT58JVEDEVQAAG','Enrollment Trend','chart','enrollment_stats',NULL,3,1,'2026-07-23 13:05:54','2026-07-23 13:05:54',NULL),('01KY7HA8D9K9KMJP39NG63ZZF4','01ky7gd0jm3wgn1bqba86pw1p9','01KY7HA5NFRHZT58JVEDEVQAAG','Recent Announcements','list','communication_stats',NULL,4,1,'2026-07-23 13:05:54','2026-07-23 13:05:54',NULL);
/*!40000 ALTER TABLE `dashboard_widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboards`
--

DROP TABLE IF EXISTS `dashboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dashboards` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(36) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `layout` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`layout`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dashboards_school_id_slug_unique` (`school_id`,`slug`),
  KEY `dashboards_school_id_index` (`school_id`),
  KEY `dashboards_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboards`
--

LOCK TABLES `dashboards` WRITE;
/*!40000 ALTER TABLE `dashboards` DISABLE KEYS */;
INSERT INTO `dashboards` VALUES ('01KY7HA5NFRHZT58JVEDEVQAAG','01ky7gd0jm3wgn1bqba86pw1p9','School Overview','school-overview','Main school dashboard with key metrics','school',1,1,NULL,NULL,'2026-07-23 13:05:51','2026-07-23 13:05:51',NULL),('01KY7HA5R9F10F42597GVXCFM0','01ky7gd0jm3wgn1bqba86pw1p9','Academic Dashboard','academic-dashboard','Academic performance and enrollment metrics','academic',0,1,NULL,NULL,'2026-07-23 13:05:51','2026-07-23 13:05:51',NULL),('01KY7HA5TZ1992ZMY66S120RN3','01ky7gd0jm3wgn1bqba86pw1p9','Finance Dashboard','finance-dashboard','Financial overview and fee collection','finance',0,1,NULL,NULL,'2026-07-23 13:05:51','2026-07-23 13:05:51',NULL),('01KY7HA5W6KG7CGNDA0MT6MED6','01ky7gd0jm3wgn1bqba86pw1p9','Library Dashboard','library-dashboard','Library usage and circulation stats','library',0,1,NULL,NULL,'2026-07-23 13:05:52','2026-07-23 13:05:52',NULL),('01KY7HA5X8F9HMTEGW3G3EYE1Z','01ky7gd0jm3wgn1bqba86pw1p9','Transport Dashboard','transport-dashboard','Transport fleet and trip metrics','transport',0,1,NULL,NULL,'2026-07-23 13:05:52','2026-07-23 13:05:52',NULL);
/*!40000 ALTER TABLE `dashboards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `head_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `departments_school_code_unique` (`school_id`,`code`),
  UNIQUE KEY `departments_uuid_unique` (`uuid`),
  KEY `departments_status_index` (`status`),
  KEY `departments_school_id_order_index` (`school_id`,`order`),
  CONSTRAINT `departments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Département Sciences','SCI','Département Sciences de l\'école.','#22c55e',1,NULL,'active','2026-07-23 12:50:35','2026-07-23 12:50:35',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Département Lettres','ART','Département Lettres de l\'école.','#3b82f6',2,NULL,'active','2026-07-23 12:50:35','2026-07-23 12:50:35',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Département Commerce','COM','Département Commerce de l\'école.','#f59e0b',3,NULL,'active','2026-07-23 12:50:35','2026-07-23 12:50:35',NULL);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_templates`
--

DROP TABLE IF EXISTS `document_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_templates` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `header_html` text DEFAULT NULL,
  `footer_html` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_templates_school_id_type_index` (`school_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_templates`
--

LOCK TABLES `document_templates` WRITE;
/*!40000 ALTER TABLE `document_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `tenant_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (1,'nexus-demo.nexus.school','01ky7gd0jm3wgn1bqba86pw1p9','2026-07-24 03:23:21','2026-07-24 03:23:21');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drivers` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `license_expiry` date NOT NULL,
  `license_category` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_school_id_license_number_unique` (`school_id`,`license_number`),
  KEY `drivers_school_id_index` (`school_id`),
  KEY `drivers_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
INSERT INTO `drivers` VALUES ('01KY7GW25K2KJ0C4JMVJVX83AT','01ky7gd0jm3wgn1bqba86pw1p9','Kwame','Mensah','kmensah@email.com','+233201234567','DL-GH-00001','2029-07-23','C',NULL,NULL,NULL,'1985-01-15',NULL,NULL,'available',NULL,'2026-07-23 12:58:09','2026-07-23 12:58:09',NULL),('01KY7GW2A0KWWFNZDNRBG5KJGJ','01ky7gd0jm3wgn1bqba86pw1p9','Ama','Owusu','aowusu@email.com','+233202345678','DL-GH-00002','2029-07-23','C',NULL,NULL,NULL,'1985-01-15',NULL,NULL,'available',NULL,'2026-07-23 12:58:09','2026-07-23 12:58:09',NULL),('01KY7GW2G71FQ9ER0EW8210GJS','01ky7gd0jm3wgn1bqba86pw1p9','Kofi','Asare','kasare@email.com','+233203456789','DL-GH-00003','2029-07-23','D',NULL,NULL,NULL,'1985-01-15',NULL,NULL,'on_trip',NULL,'2026-07-23 12:58:09','2026-07-23 12:58:09',NULL),('01KY7GW2JVWCHS8XTXD0EV884M','01ky7gd0jm3wgn1bqba86pw1p9','Efua','Boateng','eboateng@email.com','+233204567890','DL-GH-00004','2029-07-23','C',NULL,NULL,NULL,'1985-01-15',NULL,NULL,'available',NULL,'2026-07-23 12:58:09','2026-07-23 12:58:09',NULL),('01KY7GW2ME8974AMYGEYK8J0VN','01ky7gd0jm3wgn1bqba86pw1p9','Yaw','Adjei','yadjei@email.com','+233205678901','DL-GH-00005','2029-07-23','D',NULL,NULL,NULL,'1985-01-15',NULL,NULL,'on_leave',NULL,'2026-07-23 12:58:09','2026-07-23 12:58:09',NULL);
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education_levels`
--

DROP TABLE IF EXISTS `education_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `education_levels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `education_levels_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `education_levels_uuid_unique` (`uuid`),
  KEY `education_levels_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `education_levels`
--

LOCK TABLES `education_levels` WRITE;
/*!40000 ALTER TABLE `education_levels` DISABLE KEYS */;
/*!40000 ALTER TABLE `education_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fines` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `book_loan_id` bigint(20) unsigned DEFAULT NULL,
  `borrower_id` bigint(20) unsigned NOT NULL,
  `borrower_type` varchar(20) NOT NULL DEFAULT 'student',
  `reason` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `waived_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fine_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `waived_date` date DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fines_uuid_unique` (`uuid`),
  KEY `fines_status_index` (`status`),
  KEY `fines_school_id_status_index` (`school_id`,`status`),
  KEY `fines_borrower_id_borrower_type_index` (`borrower_id`,`borrower_type`),
  KEY `fines_book_loan_id_index` (`book_loan_id`),
  CONSTRAINT `fines_book_loan_id_foreign` FOREIGN KEY (`book_loan_id`) REFERENCES `book_loans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fines_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fines`
--

LOCK TABLES `fines` WRITE;
/*!40000 ALTER TABLE `fines` DISABLE KEYS */;
INSERT INTO `fines` VALUES (1,'01KY7GVVS3YBBD7D8HZ0CVYFF7','01ky7gd0jm3wgn1bqba86pw1p9',NULL,1,'student','Overdue book',5.00,0.00,0.00,'2026-07-23',NULL,NULL,NULL,NULL,'pending',NULL,'Fine for returning The Gold Coast Memoirs 5 days late.','2026-07-23 12:58:02','2026-07-23 12:58:02',NULL);
/*!40000 ALTER TABLE `fines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_logs`
--

DROP TABLE IF EXISTS `fuel_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fuel_logs` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `driver_id` char(36) DEFAULT NULL,
  `liters` decimal(8,2) NOT NULL,
  `cost_per_liter` decimal(8,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `fuel_station` varchar(255) DEFAULT NULL,
  `odometer_reading` int(10) unsigned NOT NULL,
  `fuel_date` date NOT NULL,
  `receipt_document` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fuel_logs_driver_id_foreign` (`driver_id`),
  KEY `fuel_logs_school_id_index` (`school_id`),
  KEY `fuel_logs_vehicle_id_index` (`vehicle_id`),
  KEY `fuel_logs_fuel_date_index` (`fuel_date`),
  CONSTRAINT `fuel_logs_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fuel_logs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_logs`
--

LOCK TABLES `fuel_logs` WRITE;
/*!40000 ALTER TABLE `fuel_logs` DISABLE KEYS */;
INSERT INTO `fuel_logs` VALUES ('01KY7H2EXGZH2PNMNH19N3XPH4','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZGVH6KK385W7H6N79ZQ','01KY7GW25K2KJ0C4JMVJVX83AT',45.50,750.00,34125.00,'diesel','Total Ouagadougou',45230,'2026-07-13',NULL,'Plein avant les trajets matinaux','2026-07-23 13:01:39','2026-07-23 13:01:39',NULL),('01KY7H2EZSNNV4J7D16WK63XZA','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZJZGDS1JGBAWJFCRTS6','01KY7GW2A0KWWFNZDNRBG5KJGJ',60.00,750.00,45000.00,'diesel','Shell Bobo-Dioulasso',31450,'2026-07-16',NULL,'Ravitaillement pour longue distance','2026-07-23 13:01:39','2026-07-23 13:01:39',NULL),('01KY7H2F32Z0868V61ES2R2PWM','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZPN404FR6WCZ9RD5QF0','01KY7GW2G71FQ9ER0EW8210GJS',50.25,755.00,37939.00,'diesel','Total Kossodo',12800,'2026-07-18',NULL,'Ravitaillement hebdomadaire','2026-07-23 13:01:39','2026-07-23 13:01:39',NULL);
/*!40000 ALTER TABLE `fuel_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generated_documents`
--

DROP TABLE IF EXISTS `generated_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `generated_documents` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `template_id` char(36) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `file_path` varchar(255) DEFAULT NULL,
  `generated_by` bigint(20) unsigned DEFAULT NULL,
  `signed_by` bigint(20) unsigned DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `generated_documents_reference_number_unique` (`reference_number`),
  KEY `generated_documents_template_id_foreign` (`template_id`),
  KEY `generated_documents_student_id_foreign` (`student_id`),
  KEY `generated_documents_generated_by_foreign` (`generated_by`),
  KEY `generated_documents_signed_by_foreign` (`signed_by`),
  KEY `generated_documents_school_id_type_index` (`school_id`,`type`),
  KEY `generated_documents_school_id_student_id_index` (`school_id`,`student_id`),
  CONSTRAINT `generated_documents_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `generated_documents_signed_by_foreign` FOREIGN KEY (`signed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `generated_documents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  CONSTRAINT `generated_documents_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generated_documents`
--

LOCK TABLES `generated_documents` WRITE;
/*!40000 ALTER TABLE `generated_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `generated_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_levels`
--

DROP TABLE IF EXISTS `grade_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grade_levels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` varchar(255) DEFAULT NULL,
  `uuid` char(26) DEFAULT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `short_name` varchar(20) DEFAULT NULL,
  `level_order` smallint(5) unsigned NOT NULL,
  `educational_cycle` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_levels_program_id_name_unique` (`program_id`,`name`),
  UNIQUE KEY `grade_levels_program_code_unique` (`program_id`,`code`),
  UNIQUE KEY `grade_levels_program_order_unique` (`program_id`,`level_order`),
  UNIQUE KEY `grade_levels_uuid_unique` (`uuid`),
  KEY `grade_levels_status_index` (`status`),
  KEY `grade_levels_program_id_level_order_index` (`program_id`,`level_order`),
  KEY `grade_levels_school_id_foreign` (`school_id`),
  CONSTRAINT `grade_levels_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grade_levels_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_levels`
--

LOCK TABLES `grade_levels` WRITE;
/*!40000 ALTER TABLE `grade_levels` DISABLE KEYS */;
INSERT INTO `grade_levels` VALUES (1,NULL,NULL,1,'6ème','6E','6ème',1,'junior_secondary','6ème level.','active','2026-07-23 12:50:39','2026-07-23 12:50:39',NULL),(2,NULL,NULL,1,'5ème','5E','5ème',2,'junior_secondary','5ème level.','active','2026-07-23 12:50:40','2026-07-23 12:50:40',NULL),(3,NULL,NULL,1,'4ème','4E','4ème',3,'junior_secondary','4ème level.','active','2026-07-23 12:50:41','2026-07-23 12:50:41',NULL),(4,NULL,NULL,1,'3ème','3E','3ème',4,'junior_secondary','3ème level.','active','2026-07-23 12:50:41','2026-07-23 12:50:41',NULL),(5,NULL,NULL,2,'Seconde','SEC','Sec',1,'senior_secondary','Seconde level.','active','2026-07-23 12:50:41','2026-07-23 12:50:41',NULL),(6,NULL,NULL,2,'Première','PRE','Pre',2,'senior_secondary','Première level.','active','2026-07-23 12:50:41','2026-07-23 12:50:41',NULL),(7,NULL,NULL,2,'Terminale','TER','Ter',3,'senior_secondary','Terminale level.','active','2026-07-23 12:50:41','2026-07-23 12:50:41',NULL);
/*!40000 ALTER TABLE `grade_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guardians`
--

DROP TABLE IF EXISTS `guardians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guardians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `guardian_number` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `preferred_name` varchar(100) DEFAULT NULL,
  `gender` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `relationship_type` varchar(30) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(30) NOT NULL,
  `alternate_phone` varchar(30) DEFAULT NULL,
  `occupation` varchar(200) DEFAULT NULL,
  `employer` varchar(200) DEFAULT NULL,
  `residential_address` text DEFAULT NULL,
  `postal_address` text DEFAULT NULL,
  `emergency_contact` text DEFAULT NULL,
  `preferred_communication_language` varchar(50) DEFAULT NULL,
  `preferred_contact_method` varchar(30) NOT NULL DEFAULT 'phone',
  `is_primary_guardian` tinyint(1) NOT NULL DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guardians_school_id_guardian_number_unique` (`school_id`,`guardian_number`),
  UNIQUE KEY `guardians_school_id_phone_number_unique` (`school_id`,`phone_number`),
  UNIQUE KEY `guardians_school_id_email_unique` (`school_id`,`email`),
  UNIQUE KEY `guardians_uuid_unique` (`uuid`),
  KEY `guardians_status_index` (`status`),
  KEY `guardians_school_id_status_index` (`school_id`,`status`),
  KEY `guardians_user_id_index` (`user_id`),
  CONSTRAINT `guardians_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardians`
--

LOCK TABLES `guardians` WRITE;
/*!40000 ALTER TABLE `guardians` DISABLE KEYS */;
INSERT INTO `guardians` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','GUA-001','Kwame','Asante',NULL,NULL,'male',NULL,'Ghanaian','father','kwame.asante@example.com','+233 24 100 0001',NULL,'Business Executive','Asante Group Ltd',NULL,NULL,NULL,NULL,'phone',1,NULL,NULL,'active','2026-07-23 12:51:10','2026-07-23 12:51:10',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','GUA-002','Akosua','Asante',NULL,NULL,'female',NULL,'Ghanaian','mother','akosua.asante@example.com','+233 24 100 0002',NULL,'Medical Doctor','Korle Bu Teaching Hospital',NULL,NULL,NULL,NULL,'email',0,NULL,NULL,'active','2026-07-23 12:51:10','2026-07-23 12:51:10',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','GUA-003','Yaw','Mensah',NULL,NULL,'male',NULL,'Ghanaian','legal_guardian','yaw.mensah@example.com','+233 24 100 0003',NULL,'Teacher','Accra High School',NULL,NULL,NULL,NULL,'whatsapp',1,NULL,NULL,'active','2026-07-23 12:51:11','2026-07-23 12:51:11',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','GUA-004','Esi','Ofori',NULL,NULL,'female',NULL,'Ghanaian','aunt','esi.ofori@example.com','+233 24 100 0004',NULL,'Lawyer','Ofori & Associates',NULL,NULL,NULL,NULL,'sms',1,NULL,NULL,'active','2026-07-23 12:51:12','2026-07-23 12:51:12',NULL),(5,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','GUA-005','Nana','Boateng',NULL,NULL,'male',NULL,'Ghanaian','grandparent',NULL,'+233 24 100 0005',NULL,'Retired',NULL,NULL,NULL,NULL,NULL,'phone',0,NULL,NULL,'active','2026-07-23 12:51:12','2026-07-23 12:51:12',NULL);
/*!40000 ALTER TABLE `guardians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incident_student`
--

DROP TABLE IF EXISTS `incident_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incident_student` (
  `incident_id` char(36) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`incident_id`,`student_id`),
  KEY `incident_student_student_id_foreign` (`student_id`),
  CONSTRAINT `incident_student_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `incident_student_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incident_student`
--

LOCK TABLES `incident_student` WRITE;
/*!40000 ALTER TABLE `incident_student` DISABLE KEYS */;
/*!40000 ALTER TABLE `incident_student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incidents`
--

DROP TABLE IF EXISTS `incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `incidents` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `severity` varchar(255) NOT NULL DEFAULT 'low',
  `status` varchar(255) NOT NULL DEFAULT 'open',
  `incident_date` timestamp NULL DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `witnesses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`witnesses`)),
  `actions_taken` text DEFAULT NULL,
  `follow_up` text DEFAULT NULL,
  `reported_by` bigint(20) unsigned DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidents_reported_by_foreign` (`reported_by`),
  KEY `incidents_assigned_to_foreign` (`assigned_to`),
  KEY `incidents_school_id_status_index` (`school_id`,`status`),
  KEY `incidents_school_id_severity_index` (`school_id`,`severity`),
  KEY `incidents_incident_date_index` (`incident_date`),
  CONSTRAINT `incidents_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `incidents_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incidents`
--

LOCK TABLES `incidents` WRITE;
/*!40000 ALTER TABLE `incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kpis`
--

DROP TABLE IF EXISTS `kpis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kpis` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `formula` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`formula`)),
  `target_value` decimal(15,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `calculation_frequency` varchar(36) DEFAULT NULL,
  `last_calculated_at` timestamp NULL DEFAULT NULL,
  `cached_value` decimal(15,2) DEFAULT NULL,
  `cached_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpis_school_id_slug_unique` (`school_id`,`slug`),
  KEY `kpis_school_id_index` (`school_id`),
  KEY `kpis_category_index` (`category`),
  KEY `kpis_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kpis`
--

LOCK TABLES `kpis` WRITE;
/*!40000 ALTER TABLE `kpis` DISABLE KEYS */;
INSERT INTO `kpis` VALUES ('01KY7HDR63M513V5HZJF45CMEE','01ky7gd0jm3wgn1bqba86pw1p9','Student Enrollment','student-enrollment','Total active student enrollments','enrollment',NULL,500.00,'students','active','daily',NULL,NULL,NULL,'2026-07-23 13:07:49','2026-07-23 13:07:49',NULL),('01KY7HDR92508TGWE9PM16Z2DB','01ky7gd0jm3wgn1bqba86pw1p9','Attendance Rate','attendance-rate','Daily attendance rate percentage','attendance',NULL,95.00,'%','active','daily',NULL,NULL,NULL,'2026-07-23 13:07:49','2026-07-23 13:07:49',NULL),('01KY7HDRCGQ5P74682G7FPFABK','01ky7gd0jm3wgn1bqba86pw1p9','Fee Collection Rate','fee-collection-rate','Percentage of fees collected','finance',NULL,90.00,'%','active','monthly',NULL,NULL,NULL,'2026-07-23 13:07:49','2026-07-23 13:07:49',NULL),('01KY7HDRE9E7MQSBSGJVJDY15Z','01ky7gd0jm3wgn1bqba86pw1p9','Library Utilization','library-utilization','Number of active library loans','library',NULL,100.00,'loans','active','weekly',NULL,NULL,NULL,'2026-07-23 13:07:49','2026-07-23 13:07:49',NULL),('01KY7HDRH7WPRBH099XAVVATB9','01ky7gd0jm3wgn1bqba86pw1p9','Transport Usage','transport-usage','Daily transport trip completion rate','transport',NULL,98.00,'%','active','daily',NULL,NULL,NULL,'2026-07-23 13:07:49','2026-07-23 13:07:49',NULL);
/*!40000 ALTER TABLE `kpis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_windows`
--

DROP TABLE IF EXISTS `maintenance_windows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance_windows` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'scheduled',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `is_readonly` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `actual_start_at` timestamp NULL DEFAULT NULL,
  `actual_end_at` timestamp NULL DEFAULT NULL,
  `affected_services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`affected_services`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintenance_windows_status_starts_at_index` (`status`,`starts_at`),
  KEY `maintenance_windows_school_id_status_index` (`school_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_windows`
--

LOCK TABLES `maintenance_windows` WRITE;
/*!40000 ALTER TABLE `maintenance_windows` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_windows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_recipients`
--

DROP TABLE IF EXISTS `message_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_recipients` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `message_id` char(36) NOT NULL,
  `recipient_id` bigint(20) unsigned NOT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'pending',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_recipients_message_id_index` (`message_id`),
  KEY `message_recipients_recipient_id_index` (`recipient_id`),
  KEY `message_recipients_status_index` (`status`),
  CONSTRAINT `message_recipients_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_recipients_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_recipients`
--

LOCK TABLES `message_recipients` WRITE;
/*!40000 ALTER TABLE `message_recipients` DISABLE KEYS */;
INSERT INTO `message_recipients` VALUES ('01KY7H9EVNT3ZX61WX4EP6FGDX','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H9ENRRCCKBK99XTJ1KSRG',2,'delivered',NULL,'2026-07-23 13:05:28','2026-07-23 13:05:28'),('01KY7H9F4DHVJNY2GTYBT5DSYP','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H9EXJCY27YACKEQE4HSRE',2,'delivered',NULL,'2026-07-23 13:05:28','2026-07-23 13:05:28'),('01KY7H9F6QHF2PGD8JZ3Y22BP9','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H9F5NRVXF9H41BB75HY2N',2,'delivered',NULL,'2026-07-23 13:05:28','2026-07-23 13:05:28');
/*!40000 ALTER TABLE `message_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `type` varchar(36) NOT NULL DEFAULT 'direct',
  `status` varchar(36) NOT NULL DEFAULT 'draft',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_school_id_index` (`school_id`),
  KEY `messages_sender_id_index` (`sender_id`),
  KEY `messages_type_index` (`type`),
  KEY `messages_status_index` (`status`),
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES ('01KY7H9ENRRCCKBK99XTJ1KSRG','01ky7gd0jm3wgn1bqba86pw1p9',1,'Welcome to the System','Hello! Welcome to the Nexus School communication system. You can use this to send and receive messages.','direct','sent','2026-07-23 13:05:28','2026-07-23 13:05:28','2026-07-23 13:05:28',NULL),('01KY7H9EXJCY27YACKEQE4HSRE','01ky7gd0jm3wgn1bqba86pw1p9',1,'Schedule Update','Please note that the class schedule has been updated for next week. Check the timetable for details.','broadcast','sent','2026-07-23 13:05:28','2026-07-23 13:05:28','2026-07-23 13:05:28',NULL),('01KY7H9F5NRVXF9H41BB75HY2N','01ky7gd0jm3wgn1bqba86pw1p9',1,'Meeting Notes','Here are the notes from today\'s department meeting. Please review and provide feedback.','direct','draft',NULL,'2026-07-23 13:05:28','2026-07-23 13:05:28',NULL);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'2019_09_15_000010_create_tenants_table',1),(3,'2019_09_15_000020_create_domains_table',1),(4,'2026_07_13_000001_create_cache_table',1),(5,'2026_07_14_000001_create_schools_table',1),(6,'2026_07_14_000002_create_permission_tables',1),(7,'2026_07_14_000003_create_academic_years_table',1),(8,'2026_07_14_000004_create_academic_terms_table',1),(9,'2026_07_14_000005_create_departments_table',1),(10,'2026_07_14_000006_create_programs_table',1),(11,'2026_07_14_000007_create_grade_levels_table',1),(12,'2026_07_14_000008_create_sections_table',1),(13,'2026_07_14_000009_create_academic_classes_table',1),(14,'2026_07_14_000010_create_classrooms_table',1),(15,'2026_07_14_000011_add_classroom_id_to_academic_classes_table',1),(16,'2026_07_14_000012_create_subject_categories_table',1),(17,'2026_07_14_000013_create_subjects_table',1),(18,'2026_07_14_000014_create_subject_coefficients_table',1),(19,'2026_07_15_000015_create_guardians_table',1),(20,'2026_07_15_000016_create_students_table',1),(21,'2026_07_15_000017_create_student_enrollments_table',1),(22,'2026_07_15_000018_create_timetable_entries_table',1),(23,'2026_07_15_000019_create_assessments_table',1),(24,'2026_07_15_000020_create_assessment_results_table',1),(25,'2026_07_16_000021_create_authors_table',1),(26,'2026_07_16_000022_create_publishers_table',1),(27,'2026_07_16_000023_create_book_categories_table',1),(28,'2026_07_16_000024_create_books_table',1),(29,'2026_07_16_000025_create_book_copies_table',1),(30,'2026_07_16_000026_create_book_loans_table',1),(31,'2026_07_16_000027_create_reservations_table',1),(32,'2026_07_16_000028_create_fines_table',1),(33,'2026_07_16_000029_create_book_author_table',1),(34,'2026_07_16_000030_create_vehicles_table',1),(35,'2026_07_16_000031_create_drivers_table',1),(36,'2026_07_16_000032_create_routes_table',1),(37,'2026_07_16_000033_create_route_stops_table',1),(38,'2026_07_16_000034_create_trips_table',1),(39,'2026_07_16_000035_create_student_transport_assignments_table',1),(40,'2026_07_16_000036_create_vehicle_maintenances_table',1),(41,'2026_07_16_000037_create_fuel_logs_table',1),(42,'2026_07_16_000038_create_notification_templates_table',1),(43,'2026_07_16_000040_create_notifications_table',1),(44,'2026_07_16_000041_create_announcements_table',1),(45,'2026_07_16_000041_create_notification_deliveries_table',1),(46,'2026_07_16_000042_create_messages_table',1),(47,'2026_07_16_000043_create_message_recipients_table',1),(48,'2026_07_16_000044_create_communication_channels_table',1),(49,'2026_07_16_000045_create_dashboards_table',1),(50,'2026_07_16_000046_create_dashboard_widgets_table',1),(51,'2026_07_16_000047_create_reports_table',1),(52,'2026_07_16_000048_create_kpis_table',1),(53,'2026_07_16_000049_create_analytics_snapshots_table',1),(54,'2026_07_16_000050_create_personal_access_tokens_table',1),(55,'2026_07_16_000051_create_api_clients_table',1),(56,'2026_07_16_000052_create_api_tokens_table',1),(57,'2026_07_16_000053_create_webhooks_table',1),(58,'2026_07_16_000054_create_webhook_deliveries_table',1),(59,'2026_07_16_000055_create_api_audit_logs_table',1),(60,'2026_07_17_000056_create_ai_providers_table',1),(61,'2026_07_17_000057_create_ai_conversations_table',1),(62,'2026_07_17_000058_create_ai_messages_table',1),(63,'2026_07_17_000059_create_ai_knowledge_sources_table',1),(89,'2026_07_17_000060_create_ai_prompt_templates_table',1),(90,'2026_07_17_000061_create_ai_usage_logs_table',1),(91,'2026_07_17_000062_create_workflows_table',1),(92,'2026_07_17_000080_fix_users_school_id_fk',1),(93,'2026_07_17_000081_fix_subject_categories_display_order',1),(94,'2026_07_17_000063_create_workflow_versions_table',2),(95,'2026_07_17_000064_create_workflow_triggers_table',2),(96,'2026_07_17_000065_create_workflow_executions_table',2),(97,'2026_07_17_000066_create_workflow_tasks_table',2),(98,'2026_07_17_000067_create_workflow_schedules_table',2),(99,'2026_07_17_000068_create_automation_rules_table',2),(100,'2026_07_17_000069_create_system_settings_table',2),(101,'2026_07_17_000070_create_audit_logs_table',2),(102,'2026_07_17_000071_create_activity_logs_table',2),(103,'2026_07_17_000072_create_scheduled_tasks_table',2),(104,'2026_07_17_000073_create_backups_table',2),(105,'2026_07_17_000074_create_system_health_table',1),(106,'2026_07_17_000075_create_maintenance_windows_table',3),(107,'2026_07_17_000076_create_notification_channels_table',3),(108,'2026_07_17_000077_add_school_id_to_grade_levels_table',3),(109,'2026_07_17_000078_add_school_id_to_sections_table',3),(110,'2026_07_17_000079_add_school_id_to_academic_classes_table',3),(111,'2026_07_17_000082_create_attendance_records_table',3),(112,'2026_07_18_000001_create_plugins_table',3),(113,'2026_07_18_000002_create_plugin_settings_table',3),(114,'2026_07_24_000001_create_plans_table',4),(116,'2026_07_24_000002_create_subscriptions_table',5),(117,'2026_07_24_000003_create_payments_table',6),(118,'2026_07_24_000004_create_document_templates_table',6),(119,'2026_07_24_000005_create_generated_documents_table',6),(120,'2026_07_24_000006_create_incidents_table',6),(121,'2026_07_24_000007_create_incident_student_table',6),(122,'2026_07_24_000010_add_indexes_for_dashboard_widgets',7),(123,'2026_07_24_000011_create_campuses_table',8),(124,'2026_07_24_185135_add_deleted_at_to_attendance_records_table',9),(125,'2026_08_01_000001_create_academic_calendar_years_table',10),(126,'2026_08_01_000002_create_education_levels_table',10),(127,'2026_08_01_000003_create_academic_programs_table',10),(128,'2026_08_01_000004_create_streams_table',10),(129,'2026_08_01_000005_create_school_classes_table',10),(130,'2026_08_01_000006_create_class_sections_table',10),(131,'2026_08_01_000007_create_subject_groups_table',10),(132,'2026_08_01_000008_create_academic_subjects_table',10),(133,'2026_08_01_000009_create_academic_calendar_terms_table',10),(134,'2026_08_01_000010_create_academic_calendar_events_table',10);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',3),(7,'App\\Models\\User',2);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_channels`
--

DROP TABLE IF EXISTS `notification_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_channels` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `description` text DEFAULT NULL,
  `total_sent` int(10) unsigned NOT NULL DEFAULT 0,
  `total_failed` int(10) unsigned NOT NULL DEFAULT 0,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_channels_school_id_type_index` (`school_id`,`type`),
  KEY `notification_channels_school_id_is_active_index` (`school_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_channels`
--

LOCK TABLES `notification_channels` WRITE;
/*!40000 ALTER TABLE `notification_channels` DISABLE KEYS */;
INSERT INTO `notification_channels` VALUES ('01M4TD7PQP23WQ2X1GETDNZJFD',NULL,'Bonne annee scolaire','push',1,'[]','[]',NULL,0,0,NULL,'2026-10-13 10:34:21','2026-10-13 10:34:43','2026-10-13 10:34:43');
/*!40000 ALTER TABLE `notification_channels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_deliveries`
--

DROP TABLE IF EXISTS `notification_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_deliveries` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `notification_id` char(36) NOT NULL,
  `recipient_id` bigint(20) unsigned NOT NULL,
  `channel` varchar(36) NOT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_deliveries_notification_id_index` (`notification_id`),
  KEY `notification_deliveries_recipient_id_index` (`recipient_id`),
  KEY `notification_deliveries_channel_index` (`channel`),
  KEY `notification_deliveries_status_index` (`status`),
  CONSTRAINT `notification_deliveries_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_deliveries_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_deliveries`
--

LOCK TABLES `notification_deliveries` WRITE;
/*!40000 ALTER TABLE `notification_deliveries` DISABLE KEYS */;
INSERT INTO `notification_deliveries` VALUES ('01KY7H97X455YNQW1KK1A7QH8K','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H7AX5CQ5VSF3A894997VG',1,'in_app','delivered',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:21','2026-07-23 13:05:21'),('01KY7H9817284V5AK0JBZYR92R','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H7AYTVMGGWM4HV2CNTN25',1,'email','delivered',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:21','2026-07-23 13:05:21'),('01KY7H984S8JAJ9EY9XZ1M1989','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H7B0473JAHCWENRXC2JDH',1,'in_app','delivered',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:21','2026-07-23 13:05:21'),('01KY7H9866RY6V16DK89NQHVF7','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H7B267AA0Q3JSNWN4VH2R',1,'in_app','delivered',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:21','2026-07-23 13:05:21'),('01KY7H987MDHHJ3GD00SR16VRQ','01ky7gd0jm3wgn1bqba86pw1p9','01KY7H7B4TAD4STC25826SGG8T',1,'sms','delivered',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:21','2026-07-23 13:05:21');
/*!40000 ALTER TABLE `notification_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_templates` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `channel` varchar(36) NOT NULL DEFAULT 'in_app',
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_templates_school_id_slug_unique` (`school_id`,`slug`),
  KEY `notification_templates_school_id_index` (`school_id`),
  KEY `notification_templates_category_index` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_templates`
--

LOCK TABLES `notification_templates` WRITE;
/*!40000 ALTER TABLE `notification_templates` DISABLE KEYS */;
INSERT INTO `notification_templates` VALUES ('01KY7H78D9PNPQ981VZGQPGX4W','01ky7gd0jm3wgn1bqba86pw1p9','Fee Reminder','fee-reminder','fee_reminder','email','Fee Payment Reminder - {{ student_name }}','Dear {{ parent_name }}, this is a reminder that {{ student_name }} has an outstanding fee of {{ amount }}. Please pay by {{ due_date }}.','[\"student_name\",\"parent_name\",\"amount\",\"due_date\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78FC41A60MZVZC08HC9E','01ky7gd0jm3wgn1bqba86pw1p9','Attendance Alert','attendance-alert','attendance_alert','sms',NULL,'Dear {{ parent_name }}, {{ student_name }} was marked {{ status }} on {{ date }}. Reason: {{ reason }}.','[\"student_name\",\"parent_name\",\"status\",\"date\",\"reason\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78M1YZWGQTE4MRX0FV9G','01ky7gd0jm3wgn1bqba86pw1p9','Grade Publication','grade-publication','grade_publication','in_app','Results Available - {{ term_name }}','Dear {{ student_name }}, your {{ term_name }} results are now available. Average: {{ average }}.','[\"student_name\",\"term_name\",\"average\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78MSW8EPA532F90P530D','01ky7gd0jm3wgn1bqba86pw1p9','Library Overdue','library-overdue','library_reminder','email','Overdue Book Reminder','Dear {{ borrower_name }}, the book \"{{ book_title }}\" is {{ days_overdue }} days overdue. Please return it to avoid fines.','[\"borrower_name\",\"book_title\",\"days_overdue\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78NE196C1EHJ9NYY9YEJ','01ky7gd0jm3wgn1bqba86pw1p9','Transport Update','transport-update','transport_update','push','Transport Update','Route {{ route_name }} has been updated. {{ update_details }}','[\"route_name\",\"update_details\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78PXZC95P8YMTW4VY1GP','01ky7gd0jm3wgn1bqba86pw1p9','Welcome Message','welcome-message','welcome','email','Welcome to {{ school_name }}','Dear {{ user_name }}, welcome to {{ school_name }}! Your account has been created successfully.','[\"user_name\",\"school_name\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78RK7GG9RX8JF91FETHR','01ky7gd0jm3wgn1bqba86pw1p9','Password Reset','password-reset','password_reset','email','Password Reset Request','Dear {{ user_name }}, you have requested a password reset. Click the link to reset: {{ reset_url }}','[\"user_name\",\"reset_url\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL),('01KY7H78SD8ARS34ZWP56JYXM1','01ky7gd0jm3wgn1bqba86pw1p9','Payroll Notice','payroll-notice','payroll_notice','email','Salary Credit Notification','Dear {{ staff_name }}, your salary for {{ month }} has been credited. Amount: {{ amount }}.','[\"staff_name\",\"month\",\"amount\"]',1,'2026-07-23 13:04:16','2026-07-23 13:04:16',NULL);
/*!40000 ALTER TABLE `notification_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `template_id` char(36) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `status` varchar(36) NOT NULL DEFAULT 'pending',
  `channel` varchar(36) NOT NULL DEFAULT 'in_app',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `retry_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `max_retries` smallint(5) unsigned NOT NULL DEFAULT 3,
  `last_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_school_id_index` (`school_id`),
  KEY `notifications_sender_id_index` (`sender_id`),
  KEY `notifications_template_id_index` (`template_id`),
  KEY `notifications_status_index` (`status`),
  KEY `notifications_channel_index` (`channel`),
  KEY `notifications_scheduled_at_index` (`scheduled_at`),
  CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notifications_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `notification_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('01KY7H7AX5CQ5VSF3A894997VG','01ky7gd0jm3wgn1bqba86pw1p9',NULL,NULL,'system','Welcome to Nexus School ERP','Welcome to the Nexus School ERP system. Explore the dashboard to get started.',NULL,'delivered','in_app',NULL,NULL,NULL,NULL,0,3,NULL,'2026-07-23 13:04:18','2026-07-23 13:04:18',NULL),('01KY7H7AYTVMGGWM4HV2CNTN25','01ky7gd0jm3wgn1bqba86pw1p9',NULL,NULL,'fee_reminder','Fee Payment Reminder','This is a reminder that the first term fees are due by the end of the month.',NULL,'sent','email',NULL,NULL,NULL,NULL,0,3,NULL,'2026-07-23 13:04:18','2026-07-23 13:04:18',NULL),('01KY7H7B0473JAHCWENRXC2JDH','01ky7gd0jm3wgn1bqba86pw1p9',NULL,NULL,'attendance','Attendance Report Available','The weekly attendance report is now available for review.',NULL,'delivered','in_app',NULL,NULL,NULL,NULL,0,3,NULL,'2026-07-23 13:04:18','2026-07-23 13:04:18',NULL),('01KY7H7B267AA0Q3JSNWN4VH2R','01ky7gd0jm3wgn1bqba86pw1p9',NULL,NULL,'grade','Mid-Term Results Published','Mid-term examination results have been published. Check your grades now.',NULL,'pending','in_app',NULL,NULL,NULL,NULL,0,3,NULL,'2026-07-23 13:04:19','2026-07-23 13:04:19',NULL),('01KY7H7B4TAD4STC25826SGG8T','01ky7gd0jm3wgn1bqba86pw1p9',NULL,NULL,'transport','Route Change Notification','Route A has been updated with a new stop at Market Square.',NULL,'sent','sms',NULL,NULL,NULL,NULL,0,3,NULL,'2026-07-23 13:04:19','2026-07-23 13:04:19',NULL);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `student_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'XOF',
  `payment_method` varchar(255) NOT NULL DEFAULT 'cash',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reference` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `academic_term_id` bigint(20) unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `received_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_reference_unique` (`reference`),
  KEY `payments_student_id_foreign` (`student_id`),
  KEY `payments_academic_year_id_foreign` (`academic_year_id`),
  KEY `payments_academic_term_id_foreign` (`academic_term_id`),
  KEY `payments_received_by_foreign` (`received_by`),
  KEY `payments_school_id_status_index` (`school_id`,`status`),
  KEY `payments_school_id_student_id_index` (`school_id`,`student_id`),
  KEY `payments_due_date_index` (`due_date`),
  CONSTRAINT `payments_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'school.view','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(2,'school.create','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(3,'school.update','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(4,'school.delete','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(5,'user.view','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(6,'user.create','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(7,'user.update','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(8,'user.delete','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(9,'role.view','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(10,'role.create','web','2026-07-23 12:49:58','2026-07-23 12:49:58'),(11,'role.update','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(12,'role.delete','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(13,'permission.view','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(14,'permission.create','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(15,'permission.update','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(16,'permission.delete','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(17,'academic.view','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(18,'academic.create','web','2026-07-23 12:49:59','2026-07-23 12:49:59'),(19,'academic.update','web','2026-07-23 12:50:00','2026-07-23 12:50:00'),(20,'academic.delete','web','2026-07-23 12:50:00','2026-07-23 12:50:00'),(21,'academic_class.view','web','2026-07-23 12:50:00','2026-07-23 12:50:00'),(22,'academic_class.create','web','2026-07-23 12:50:00','2026-07-23 12:50:00'),(23,'academic_class.update','web','2026-07-23 12:50:00','2026-07-23 12:50:00'),(24,'academic_class.delete','web','2026-07-23 12:50:01','2026-07-23 12:50:01'),(25,'academic_term.view','web','2026-07-23 12:50:01','2026-07-23 12:50:01'),(26,'academic_term.create','web','2026-07-23 12:50:01','2026-07-23 12:50:01'),(27,'academic_term.update','web','2026-07-23 12:50:01','2026-07-23 12:50:01'),(28,'academic_term.delete','web','2026-07-23 12:50:01','2026-07-23 12:50:01'),(29,'academic_year.view','web','2026-07-23 12:50:01','2026-07-23 12:50:01'),(30,'academic_year.create','web','2026-07-23 12:50:02','2026-07-23 12:50:02'),(31,'academic_year.update','web','2026-07-23 12:50:02','2026-07-23 12:50:02'),(32,'academic_year.delete','web','2026-07-23 12:50:02','2026-07-23 12:50:02'),(33,'student.view','web','2026-07-23 12:50:02','2026-07-23 12:50:02'),(34,'student.create','web','2026-07-23 12:50:02','2026-07-23 12:50:02'),(35,'student.update','web','2026-07-23 12:50:02','2026-07-23 12:50:02'),(36,'student.delete','web','2026-07-23 12:50:03','2026-07-23 12:50:03'),(37,'student_enrollment.view','web','2026-07-23 12:50:03','2026-07-23 12:50:03'),(38,'student_enrollment.create','web','2026-07-23 12:50:03','2026-07-23 12:50:03'),(39,'student_enrollment.update','web','2026-07-23 12:50:03','2026-07-23 12:50:03'),(40,'student_enrollment.delete','web','2026-07-23 12:50:03','2026-07-23 12:50:03'),(41,'teacher.view','web','2026-07-23 12:50:04','2026-07-23 12:50:04'),(42,'teacher.create','web','2026-07-23 12:50:04','2026-07-23 12:50:04'),(43,'teacher.update','web','2026-07-23 12:50:04','2026-07-23 12:50:04'),(44,'teacher.delete','web','2026-07-23 12:50:04','2026-07-23 12:50:04'),(45,'guardian.view','web','2026-07-23 12:50:04','2026-07-23 12:50:04'),(46,'guardian.create','web','2026-07-23 12:50:05','2026-07-23 12:50:05'),(47,'guardian.update','web','2026-07-23 12:50:05','2026-07-23 12:50:05'),(48,'guardian.delete','web','2026-07-23 12:50:05','2026-07-23 12:50:05'),(49,'admission.view','web','2026-07-23 12:50:05','2026-07-23 12:50:05'),(50,'admission.create','web','2026-07-23 12:50:05','2026-07-23 12:50:05'),(51,'admission.update','web','2026-07-23 12:50:06','2026-07-23 12:50:06'),(52,'admission.delete','web','2026-07-23 12:50:06','2026-07-23 12:50:06'),(53,'attendance.view','web','2026-07-23 12:50:06','2026-07-23 12:50:06'),(54,'attendance.create','web','2026-07-23 12:50:07','2026-07-23 12:50:07'),(55,'attendance.update','web','2026-07-23 12:50:07','2026-07-23 12:50:07'),(56,'attendance.delete','web','2026-07-23 12:50:07','2026-07-23 12:50:07'),(57,'examination.view','web','2026-07-23 12:50:08','2026-07-23 12:50:08'),(58,'examination.create','web','2026-07-23 12:50:08','2026-07-23 12:50:08'),(59,'examination.update','web','2026-07-23 12:50:08','2026-07-23 12:50:08'),(60,'examination.delete','web','2026-07-23 12:50:08','2026-07-23 12:50:08'),(61,'gradebook.view','web','2026-07-23 12:50:08','2026-07-23 12:50:08'),(62,'gradebook.create','web','2026-07-23 12:50:09','2026-07-23 12:50:09'),(63,'gradebook.update','web','2026-07-23 12:50:09','2026-07-23 12:50:09'),(64,'gradebook.delete','web','2026-07-23 12:50:09','2026-07-23 12:50:09'),(65,'grade.view','web','2026-07-23 12:50:10','2026-07-23 12:50:10'),(66,'grade.create','web','2026-07-23 12:50:10','2026-07-23 12:50:10'),(67,'grade.update','web','2026-07-23 12:50:10','2026-07-23 12:50:10'),(68,'grade.delete','web','2026-07-23 12:50:10','2026-07-23 12:50:10'),(69,'grade_level.view','web','2026-07-23 12:50:11','2026-07-23 12:50:11'),(70,'grade_level.create','web','2026-07-23 12:50:11','2026-07-23 12:50:11'),(71,'grade_level.update','web','2026-07-23 12:50:11','2026-07-23 12:50:11'),(72,'grade_level.delete','web','2026-07-23 12:50:12','2026-07-23 12:50:12'),(73,'section.view','web','2026-07-23 12:50:12','2026-07-23 12:50:12'),(74,'section.create','web','2026-07-23 12:50:13','2026-07-23 12:50:13'),(75,'section.update','web','2026-07-23 12:50:13','2026-07-23 12:50:13'),(76,'section.delete','web','2026-07-23 12:50:14','2026-07-23 12:50:14'),(77,'classroom.view','web','2026-07-23 12:50:14','2026-07-23 12:50:14'),(78,'classroom.create','web','2026-07-23 12:50:14','2026-07-23 12:50:14'),(79,'classroom.update','web','2026-07-23 12:50:15','2026-07-23 12:50:15'),(80,'classroom.delete','web','2026-07-23 12:50:15','2026-07-23 12:50:15'),(81,'department.view','web','2026-07-23 12:50:15','2026-07-23 12:50:15'),(82,'department.create','web','2026-07-23 12:50:15','2026-07-23 12:50:15'),(83,'department.update','web','2026-07-23 12:50:15','2026-07-23 12:50:15'),(84,'department.delete','web','2026-07-23 12:50:15','2026-07-23 12:50:15'),(85,'program.view','web','2026-07-23 12:50:16','2026-07-23 12:50:16'),(86,'program.create','web','2026-07-23 12:50:16','2026-07-23 12:50:16'),(87,'program.update','web','2026-07-23 12:50:16','2026-07-23 12:50:16'),(88,'program.delete','web','2026-07-23 12:50:16','2026-07-23 12:50:16'),(89,'subject.view','web','2026-07-23 12:50:16','2026-07-23 12:50:16'),(90,'subject.create','web','2026-07-23 12:50:17','2026-07-23 12:50:17'),(91,'subject.update','web','2026-07-23 12:50:17','2026-07-23 12:50:17'),(92,'subject.delete','web','2026-07-23 12:50:17','2026-07-23 12:50:17'),(93,'subject_category.view','web','2026-07-23 12:50:17','2026-07-23 12:50:17'),(94,'subject_category.create','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(95,'subject_category.update','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(96,'subject_category.delete','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(97,'subject_coefficient.view','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(98,'subject_coefficient.create','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(99,'subject_coefficient.update','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(100,'subject_coefficient.delete','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(101,'finance.view','web','2026-07-23 12:50:18','2026-07-23 12:50:18'),(102,'finance.create','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(103,'finance.update','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(104,'finance.delete','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(105,'library.view','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(106,'library.create','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(107,'library.update','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(108,'library.delete','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(109,'hr.view','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(110,'hr.create','web','2026-07-23 12:50:19','2026-07-23 12:50:19'),(111,'hr.update','web','2026-07-23 12:50:20','2026-07-23 12:50:20'),(112,'hr.delete','web','2026-07-23 12:50:20','2026-07-23 12:50:20'),(113,'inventory.view','web','2026-07-23 12:50:20','2026-07-23 12:50:20'),(114,'inventory.create','web','2026-07-23 12:50:20','2026-07-23 12:50:20'),(115,'inventory.update','web','2026-07-23 12:50:20','2026-07-23 12:50:20'),(116,'inventory.delete','web','2026-07-23 12:50:20','2026-07-23 12:50:20'),(117,'transport.view','web','2026-07-23 12:50:21','2026-07-23 12:50:21'),(118,'transport.create','web','2026-07-23 12:50:21','2026-07-23 12:50:21'),(119,'transport.update','web','2026-07-23 12:50:21','2026-07-23 12:50:21'),(120,'transport.delete','web','2026-07-23 12:50:21','2026-07-23 12:50:21'),(121,'communication.view','web','2026-07-23 12:50:22','2026-07-23 12:50:22'),(122,'communication.create','web','2026-07-23 12:50:22','2026-07-23 12:50:22'),(123,'communication.update','web','2026-07-23 12:50:22','2026-07-23 12:50:22'),(124,'communication.delete','web','2026-07-23 12:50:22','2026-07-23 12:50:22'),(125,'report.view','web','2026-07-23 12:50:22','2026-07-23 12:50:22'),(126,'report.create','web','2026-07-23 12:50:22','2026-07-23 12:50:22'),(127,'report.update','web','2026-07-23 12:50:23','2026-07-23 12:50:23'),(128,'report.delete','web','2026-07-23 12:50:23','2026-07-23 12:50:23'),(129,'setting.view','web','2026-07-23 12:50:23','2026-07-23 12:50:23'),(130,'setting.create','web','2026-07-23 12:50:23','2026-07-23 12:50:23'),(131,'setting.update','web','2026-07-23 12:50:24','2026-07-23 12:50:24'),(132,'setting.delete','web','2026-07-23 12:50:24','2026-07-23 12:50:24'),(133,'dashboard.view','web','2026-07-23 12:50:24','2026-07-23 12:50:24'),(134,'dashboard.create','web','2026-07-23 12:50:24','2026-07-23 12:50:24'),(135,'dashboard.update','web','2026-07-23 12:50:25','2026-07-23 12:50:25'),(136,'dashboard.delete','web','2026-07-23 12:50:25','2026-07-23 12:50:25'),(137,'audit_log.view','web','2026-07-23 12:50:25','2026-07-23 12:50:25'),(138,'audit_log.create','web','2026-07-23 12:50:25','2026-07-23 12:50:25'),(139,'audit_log.update','web','2026-07-23 12:50:26','2026-07-23 12:50:26'),(140,'audit_log.delete','web','2026-07-23 12:50:26','2026-07-23 12:50:26'),(141,'ai.view','web','2026-07-23 12:50:27','2026-07-23 12:50:27'),(142,'ai.create','web','2026-07-23 12:50:28','2026-07-23 12:50:28'),(143,'ai.update','web','2026-07-23 12:50:28','2026-07-23 12:50:28'),(144,'ai.delete','web','2026-07-23 12:50:29','2026-07-23 12:50:29'),(145,'analytics.view','web','2026-07-23 12:50:29','2026-07-23 12:50:29'),(146,'analytics.create','web','2026-07-23 12:50:29','2026-07-23 12:50:29'),(147,'analytics.update','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(148,'analytics.delete','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(149,'api.view','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(150,'api.create','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(151,'api.update','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(152,'api.delete','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(153,'backup.view','web','2026-07-23 12:50:30','2026-07-23 12:50:30'),(154,'backup.create','web','2026-07-23 12:50:31','2026-07-23 12:50:31'),(155,'backup.update','web','2026-07-23 12:50:31','2026-07-23 12:50:31'),(156,'backup.delete','web','2026-07-23 12:50:31','2026-07-23 12:50:31'),(157,'maintenance_window.view','web','2026-07-23 12:50:31','2026-07-23 12:50:31'),(158,'maintenance_window.create','web','2026-07-23 12:50:32','2026-07-23 12:50:32'),(159,'maintenance_window.update','web','2026-07-23 12:50:32','2026-07-23 12:50:32'),(160,'maintenance_window.delete','web','2026-07-23 12:50:32','2026-07-23 12:50:32'),(161,'notification_channel.view','web','2026-07-23 12:50:32','2026-07-23 12:50:32'),(162,'notification_channel.create','web','2026-07-23 12:50:32','2026-07-23 12:50:32'),(163,'notification_channel.update','web','2026-07-23 12:50:32','2026-07-23 12:50:32'),(164,'notification_channel.delete','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(165,'workflow.view','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(166,'workflow.create','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(167,'workflow.update','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(168,'workflow.delete','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(169,'workflow.execute','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(170,'workflow.manage','web','2026-07-23 12:50:33','2026-07-23 12:50:33'),(171,'workflow.publish','web','2026-07-23 12:50:34','2026-07-23 12:50:34');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plans` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'XOF',
  `billing_period` varchar(255) NOT NULL DEFAULT 'monthly',
  `max_schools` int(11) NOT NULL DEFAULT 1,
  `max_students` int(11) NOT NULL DEFAULT 50,
  `max_teachers` int(11) NOT NULL DEFAULT 10,
  `max_users` int(11) NOT NULL DEFAULT 20,
  `storage_limit` varchar(255) NOT NULL DEFAULT '1GB',
  `modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`modules`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`),
  KEY `plans_is_active_index` (`is_active`),
  KEY `plans_billing_period_index` (`billing_period`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES ('01KY8ZV4XB1JGPYSJ2DSJG2STF','Gratuit','gratuit','Plan d\'essai gratuit pour découvrir la plateforme',0.00,'XOF','monthly',1,30,5,10,'500MB','[\"notes\",\"presences\",\"emploi_du_temps\"]',1,1,'2026-07-24 02:39:02','2026-07-24 02:39:02',NULL),('01KY8ZV53QSRMNMNZG8YCGJYJY','Essentiel','essentiel','Pour les petites écoles débutant leur numérisation',25000.00,'XOF','monthly',1,200,20,50,'2GB','[\"notes\",\"presences\",\"emploi_du_temps\",\"bulletins\",\"paiements\",\"messagerie\"]',1,2,'2026-07-24 02:39:02','2026-07-24 02:39:02',NULL),('01KY8ZV59YDD3MGAX6MNSMZ76C','Professionnel','professionnel','Pour les écoles avec des besoins avancés',75000.00,'XOF','monthly',1,1000,50,100,'10GB','[\"notes\",\"presences\",\"emploi_du_temps\",\"bulletins\",\"paiements\",\"messagerie\",\"transport\",\"bibliotheque\",\"examens\",\"statistiques\"]',1,3,'2026-07-24 02:39:02','2026-07-24 02:39:02',NULL),('01KY8ZV5EQN5PPH030TFR3E37D','Institution','institution','Pour les groupes scolaires et universités',150000.00,'XOF','monthly',5,5000,200,500,'50GB','[\"notes\",\"presences\",\"emploi_du_temps\",\"bulletins\",\"paiements\",\"messagerie\",\"transport\",\"bibliotheque\",\"examens\",\"statistiques\",\"hr\",\"comptabilite\",\"api\"]',1,4,'2026-07-24 02:39:03','2026-07-24 02:39:03',NULL);
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_settings`
--

DROP TABLE IF EXISTS `plugin_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_settings` (
  `id` char(36) NOT NULL,
  `plugin_id` char(36) NOT NULL,
  `school_id` char(36) DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugin_settings_plugin_id_school_id_key_unique` (`plugin_id`,`school_id`,`key`),
  CONSTRAINT `plugin_settings_plugin_id_foreign` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_settings`
--

LOCK TABLES `plugin_settings` WRITE;
/*!40000 ALTER TABLE `plugin_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `path` varchar(255) NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugins_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `educational_level` varchar(50) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `programs_school_code_unique` (`school_id`,`code`),
  UNIQUE KEY `programs_uuid_unique` (`uuid`),
  KEY `programs_status_index` (`status`),
  KEY `programs_school_id_order_index` (`school_id`,`order`),
  KEY `programs_department_id_index` (`department_id`),
  CONSTRAINT `programs_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `programs_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programs`
--

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',NULL,'Junior High School','JHS','Junior High School program.','junior_secondary','3 years',1,'active','2026-07-23 12:50:36','2026-07-23 12:50:36',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',NULL,'Senior High School','SHS','Senior High School program.','senior_secondary','3 years',2,'active','2026-07-23 12:50:36','2026-07-23 12:50:36',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',NULL,'Science Program','SCI','Science Program program.','senior_secondary','3 years',3,'active','2026-07-23 12:50:37','2026-07-23 12:50:37',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',NULL,'Arts Program','ART','Arts Program program.','senior_secondary','3 years',4,'active','2026-07-23 12:50:37','2026-07-23 12:50:37',NULL);
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publishers`
--

DROP TABLE IF EXISTS `publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publishers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publishers_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `publishers_uuid_unique` (`uuid`),
  KEY `publishers_status_index` (`status`),
  KEY `publishers_school_id_status_index` (`school_id`,`status`),
  CONSTRAINT `publishers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publishers`
--

LOCK TABLES `publishers` WRITE;
/*!40000 ALTER TABLE `publishers` DISABLE KEYS */;
INSERT INTO `publishers` VALUES (1,'01KY7GTQ5Q8AG6NFTPSJPDVKZ9','01ky7gd0jm3wgn1bqba86pw1p9','Ghana Publishing Corporation','P.O. Box GP 1864','Accra','Greater Accra','Ghana','00233','+233 30 222 1234','info@ghanapublishing.gov.gh','https://ghanapublishing.gov.gh','Government-owned publishing house established in 1963.','active','2026-07-23 12:57:25','2026-07-23 12:57:25',NULL),(2,'01KY7GTQFQ3T8X7KCK63H9KBEV','01ky7gd0jm3wgn1bqba86pw1p9','African Minds','14 Loop Street','Cape Town','Western Cape','South Africa','8001','+27 21 424 5678','info@africanminds.co.za','https://africanminds.co.za','Independent pan-African publisher focusing on scholarly works.','active','2026-07-23 12:57:25','2026-07-23 12:57:25',NULL),(3,'01KY7GTQM3VM4010CQNPTVMN3X','01ky7gd0jm3wgn1bqba86pw1p9','Sublime Books','15 Cantonments Road','Accra','Greater Accra','Ghana','00233','+233 30 277 9012','hello@sublimebooks.com','https://sublimebooks.com','Boutique publisher of children and young adult literature.','active','2026-07-23 12:57:25','2026-07-23 12:57:25',NULL);
/*!40000 ALTER TABLE `publishers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `format` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `scheduled_frequency` varchar(36) DEFAULT NULL,
  `last_generated_at` timestamp NULL DEFAULT NULL,
  `generated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reports_school_id_slug_unique` (`school_id`,`slug`),
  KEY `reports_school_id_index` (`school_id`),
  KEY `reports_status_index` (`status`),
  KEY `reports_scheduled_at_index` (`scheduled_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES ('01KY7HAAFS0E8652A0Q9PRNBHR','01ky7gd0jm3wgn1bqba86pw1p9','Monthly Enrollment Report','monthly-enrollment-report','Monthly student enrollment summary','enrollment','pdf','scheduled',NULL,NULL,'monthly',NULL,NULL,'2026-07-23 13:05:56','2026-07-23 13:05:56',NULL),('01KY7HAAHYC92ZD4QJKAVRG3RH','01ky7gd0jm3wgn1bqba86pw1p9','Attendance Summary','attendance-summary','Weekly attendance summary for all classes','attendance','excel','draft',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:56','2026-07-23 13:05:56',NULL),('01KY7HAANFJSF40D01V0MCA6Q7','01ky7gd0jm3wgn1bqba86pw1p9','Library Usage Report','library-usage-report','Monthly library circulation statistics','library','pdf','draft',NULL,NULL,NULL,NULL,NULL,'2026-07-23 13:05:56','2026-07-23 13:05:56',NULL);
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `book_id` bigint(20) unsigned NOT NULL,
  `borrower_id` bigint(20) unsigned NOT NULL,
  `borrower_type` varchar(20) NOT NULL DEFAULT 'student',
  `reservation_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `fulfilled_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_uuid_unique` (`uuid`),
  KEY `reservations_status_index` (`status`),
  KEY `reservations_school_id_status_index` (`school_id`,`status`),
  KEY `reservations_borrower_id_borrower_type_index` (`borrower_id`,`borrower_type`),
  KEY `reservations_book_id_index` (`book_id`),
  KEY `reservations_expiry_date_index` (`expiry_date`),
  CONSTRAINT `reservations_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,'01KY7GVREKNK7A5FWPJRD3Q2RJ','01ky7gd0jm3wgn1bqba86pw1p9',5,1,'student','2026-07-23','2026-07-30',NULL,'pending',NULL,'2026-07-23 12:57:59','2026-07-23 12:57:59',NULL),(2,'01KY7GVRHA6GN0K7FZHJF9RTBG','01ky7gd0jm3wgn1bqba86pw1p9',1,1,'student','2026-07-23','2026-07-30',NULL,'pending',NULL,'2026-07-23 12:57:59','2026-07-23 12:57:59',NULL);
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super_admin','web','2026-07-23 12:49:56','2026-07-23 12:49:56'),(2,'school_admin','web','2026-07-23 12:49:56','2026-07-23 12:49:56'),(3,'director','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(4,'vice_director','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(5,'secretary','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(6,'accountant','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(7,'teacher','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(8,'librarian','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(9,'registrar','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(10,'student','web','2026-07-23 12:49:57','2026-07-23 12:49:57'),(11,'parent','web','2026-07-23 12:49:58','2026-07-23 12:49:58');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route_stops`
--

DROP TABLE IF EXISTS `route_stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route_stops` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `route_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `stop_order` smallint(5) unsigned NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `estimated_time_minutes` int(10) unsigned DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `route_stops_school_id_index` (`school_id`),
  KEY `route_stops_route_id_index` (`route_id`),
  KEY `route_stops_stop_order_index` (`stop_order`),
  CONSTRAINT `route_stops_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_stops`
--

LOCK TABLES `route_stops` WRITE;
/*!40000 ALTER TABLE `route_stops` DISABLE KEYS */;
INSERT INTO `route_stops` VALUES ('01KY7GWPNWY8TFZXWX9AXY778T','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWM7E3C3CBA56N5MK9QB5','Market Square',1,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWPRGFMNWPA1HA17X8K62','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWM7E3C3CBA56N5MK9QB5','Hospital Junction',2,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWPTKE98GGGVBYS1JSFK8','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWM7E3C3CBA56N5MK9QB5','Town Hall',3,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWPZG86A2798YES5HQ9B3','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWM9Z26B9T3HWD33ED9AS','Community Center',1,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ0HS4GGKJ8F4FG5XHE1','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWM9Z26B9T3HWD33ED9AS','Police Station',2,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ2Q9SNGTJX3YHFVQ4TS','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMDGBNFHFCKPPXC83JV4','Industrial Area',1,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ395RSBW8A9G5V6P961','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMDGBNFHFCKPPXC83JV4','School Gate',2,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ3ZF6HG6GJMPTAD6EXA','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMDGBNFHFCKPPXC83JV4','Church Road',3,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ4QEF0963AP42MWG8E6','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMFC85BBNFVMSPAMH5AE','Traffic Light',1,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ5EBZJCRAMAP57J98WJ','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMFC85BBNFVMSPAMH5AE','Stadium',2,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ62WRQ7KYCZ8P800QY8','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMFC85BBNFVMSPAMH5AE','Library',3,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:30','2026-07-23 12:58:30',NULL),('01KY7GWQ7VHJZ4YE7RJHFTTQBM','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMFZJ3DNNA8PYN6FNF4T','City Center',1,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:31','2026-07-23 12:58:31',NULL),('01KY7GWQ9P7B5W884H5BPVRYE3','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GWMFZJ3DNNA8PYN6FNF4T','Fountain',2,NULL,NULL,NULL,NULL,NULL,'2026-07-23 12:58:31','2026-07-23 12:58:31',NULL);
/*!40000 ALTER TABLE `route_stops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `distance_km` decimal(8,2) DEFAULT NULL,
  `estimated_duration_minutes` int(10) unsigned DEFAULT NULL,
  `fare` decimal(10,2) DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `routes_school_id_index` (`school_id`),
  KEY `routes_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
INSERT INTO `routes` VALUES ('01KY7GWM7E3C3CBA56N5MK9QB5','01ky7gd0jm3wgn1bqba86pw1p9','Route A - North Circle','Covers the northern residential area',12.50,45,200.00,'active','2026-07-23 12:58:27','2026-07-23 12:58:27',NULL),('01KY7GWM9Z26B9T3HWD33ED9AS','01ky7gd0jm3wgn1bqba86pw1p9','Route B - South Express','Direct route to southern suburbs',8.75,30,150.00,'active','2026-07-23 12:58:28','2026-07-23 12:58:28',NULL),('01KY7GWMDGBNFHFCKPPXC83JV4','01ky7gd0jm3wgn1bqba86pw1p9','Route C - East Corridor','Covers eastern industrial and residential zones',15.20,55,250.00,'active','2026-07-23 12:58:28','2026-07-23 12:58:28',NULL),('01KY7GWMFC85BBNFVMSPAMH5AE','01ky7gd0jm3wgn1bqba86pw1p9','Route D - West Link','Western route through market areas',10.00,40,175.00,'active','2026-07-23 12:58:28','2026-07-23 12:58:28',NULL),('01KY7GWMFZJ3DNNA8PYN6FNF4T','01ky7gd0jm3wgn1bqba86pw1p9','Route E - Central Loop','Short central district loop',5.30,20,100.00,'inactive','2026-07-23 12:58:28','2026-07-23 12:58:28',NULL);
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_tasks`
--

DROP TABLE IF EXISTS `scheduled_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheduled_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `command` varchar(255) NOT NULL,
  `arguments` text DEFAULT NULL,
  `cron_expression` varchar(100) NOT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_singleton` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `last_run_at` timestamp NULL DEFAULT NULL,
  `next_run_at` timestamp NULL DEFAULT NULL,
  `total_runs` int(10) unsigned NOT NULL DEFAULT 0,
  `failed_runs` int(10) unsigned NOT NULL DEFAULT 0,
  `last_error` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scheduled_tasks_uuid_unique` (`uuid`),
  KEY `scheduled_tasks_is_active_next_run_at_index` (`is_active`,`next_run_at`),
  KEY `scheduled_tasks_command_index` (`command`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_tasks`
--

LOCK TABLES `scheduled_tasks` WRITE;
/*!40000 ALTER TABLE `scheduled_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_classes`
--

DROP TABLE IF EXISTS `school_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_classes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `academic_program_id` bigint(20) unsigned NOT NULL,
  `education_level_id` bigint(20) unsigned DEFAULT NULL,
  `stream_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `capacity` int(10) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `school_classes_school_id_academic_program_id_name_unique` (`school_id`,`academic_program_id`,`name`),
  UNIQUE KEY `school_classes_uuid_unique` (`uuid`),
  KEY `school_classes_academic_program_id_foreign` (`academic_program_id`),
  KEY `school_classes_education_level_id_foreign` (`education_level_id`),
  KEY `school_classes_stream_id_foreign` (`stream_id`),
  KEY `school_classes_status_index` (`status`),
  KEY `school_classes_school_id_education_level_id_index` (`school_id`,`education_level_id`),
  CONSTRAINT `school_classes_academic_program_id_foreign` FOREIGN KEY (`academic_program_id`) REFERENCES `academic_programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `school_classes_education_level_id_foreign` FOREIGN KEY (`education_level_id`) REFERENCES `education_levels` (`id`),
  CONSTRAINT `school_classes_stream_id_foreign` FOREIGN KEY (`stream_id`) REFERENCES `streams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_classes`
--

LOCK TABLES `school_classes` WRITE;
/*!40000 ALTER TABLE `school_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schools` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `database` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `locale` varchar(10) NOT NULL DEFAULT 'en',
  `logo_url` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `school_type` varchar(255) NOT NULL DEFAULT 'secondary',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schools_slug_unique` (`slug`),
  UNIQUE KEY `schools_database_unique` (`database`),
  UNIQUE KEY `schools_uuid_unique` (`uuid`),
  UNIQUE KEY `schools_domain_unique` (`domain`),
  UNIQUE KEY `schools_registration_number_unique` (`registration_number`),
  KEY `schools_status_index` (`status`),
  KEY `schools_country_index` (`country`),
  KEY `schools_school_type_index` (`school_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` varchar(255) DEFAULT NULL,
  `uuid` char(26) DEFAULT NULL,
  `grade_level_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sections_grade_level_id_name_unique` (`grade_level_id`,`name`),
  UNIQUE KEY `sections_grade_level_code_unique` (`grade_level_id`,`code`),
  UNIQUE KEY `sections_uuid_unique` (`uuid`),
  KEY `sections_status_index` (`status`),
  KEY `sections_grade_level_id_order_index` (`grade_level_id`,`order`),
  KEY `sections_school_id_foreign` (`school_id`),
  CONSTRAINT `sections_grade_level_id_foreign` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sections_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,NULL,NULL,1,'A','A','Section A.',1,'active','2026-07-23 12:50:42','2026-07-23 12:50:42',NULL),(2,NULL,NULL,1,'B','B','Section B.',2,'active','2026-07-23 12:50:43','2026-07-23 12:50:43',NULL),(3,NULL,NULL,1,'C','C','Section C.',3,'active','2026-07-23 12:50:44','2026-07-23 12:50:44',NULL),(4,NULL,NULL,1,'D','D','Section D.',4,'active','2026-07-23 12:50:44','2026-07-23 12:50:44',NULL),(5,NULL,NULL,5,'Science','SCI','Science section.',1,'active','2026-07-23 12:50:44','2026-07-23 12:50:44',NULL),(6,NULL,NULL,5,'Arts','ART','Arts section.',2,'active','2026-07-23 12:50:45','2026-07-23 12:50:45',NULL),(7,NULL,NULL,5,'Commercial','COM','Commercial section.',3,'active','2026-07-23 12:50:45','2026-07-23 12:50:45',NULL);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('AtNKOURW00B4xQWUGst97UifRC4rct4atfxqaTT3',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoibzNwUnY2MXFnVVpDWEpBaHpad0Q0YlVEanNxTGNjdHZKQTN1R0V6USI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zY2hvb2wtYWRtaW4vc3RhZmYiO3M6NToicm91dGUiO3M6NDM6ImZpbGFtZW50LnNjaG9vbC1hZG1pbi5yZXNvdXJjZXMuc3RhZmYuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI2N2FmYjFkNWJmZWNkNDNjYmIzMWQ0ZTA1YmVhZjc5MjM2NTIxMjI3N2EzZmI0MjAyMTQ0NGVhMWIzMzU4NzBjIjtzOjY6InRhYmxlcyI7YToxMjp7czo0MDoiMzRmZDFkNTk5Mjk0OWFiMjkwNmQ5MzkzY2E2ODkzZTVfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjQ6IkNvZGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoiaXNfbWFpbiI7czo1OiJsYWJlbCI7czo5OiJQcmluY2lwYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNpdHkiO3M6NToibGFiZWwiO3M6NDoiQ2l0eSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJwaG9uZSI7czo1OiJsYWJlbCI7czo1OiJQaG9uZSI7czo4OiJpc0hpZGRlbiI7YjoxO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkNyZWF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjU4ODc1ZDkwNmU5ZjBmZTYxN2E2MjdkYzJjYWY2OTUwX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NDoiTmFtZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJDb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFydF9kYXRlIjtzOjU6ImxhYmVsIjtzOjEwOiJTdGFydCBkYXRlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJlbmRfZGF0ZSI7czo1OiJsYWJlbCI7czo4OiJFbmQgZGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMDoiQ3JlYXRlZCBhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiIxZTAzNmM5MTU5YzRmMTgxMzljMWYxOTU2ZjZlZDg1NF9jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNzoiYWNhZGVtaWNZZWFyLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiQW5uw6llIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NDoiQ29kZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRfZGF0ZSI7czo1OiJsYWJlbCI7czoxMDoiU3RhcnQgZGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiZW5kX2RhdGUiO3M6NToibGFiZWwiO3M6ODoiRW5kIGRhdGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToib3JkZXIiO3M6NToibGFiZWwiO3M6NToiT3JkZXIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkNyZWF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiNzBmNDdjMzliMWM5ZjliZDQyYjdjYWM5MzIwNTM2MDJfY29sdW1ucyI7YTo0OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJDb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJlZHVjYXRpb25MZXZlbC5uYW1lIjtzOjU6ImxhYmVsIjtzOjY6Ik5pdmVhdSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiOTRmMzZhZThmMGE5MjA0NzdmYTA2ZjQ5ZDlmMDU3ZjhfY29sdW1ucyI7YTo0OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJDb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzb3J0X29yZGVyIjtzOjU6ImxhYmVsIjtzOjU6Ik9yZHJlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiJjMDc1NGRmMzNmYTA4NDI2MzE5OWNmNjhhODcyMDAwMF9jb2x1bW5zIjthOjU6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NDoiQ29kZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InNjaG9vbENsYXNzLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiQ2xhc3NlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJjYXBhY2l0eSI7czo1OiJsYWJlbCI7czo4OiJDYXBhY2l0eSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiMjE2MDljMGY0YjM5MzdlNmMxMjg4MWIyMjdmOGM3MWFfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJDb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJwcm9ncmFtLm5hbWUiO3M6NToibGFiZWwiO3M6OToiUHJvZ3JhbW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToiZWR1Y2F0aW9uTGV2ZWwubmFtZSI7czo1OiJsYWJlbCI7czo2OiJOaXZlYXUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJzdHJlYW0ubmFtZSI7czo1OiJsYWJlbCI7czo4OiJGaWxpw6hyZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJjYXBhY2l0eSI7czo1OiJsYWJlbCI7czo5OiJDYXBhY2l0w6kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6Ijc0ZThiMjVkMTc1MzQzYzk2ZDZlZjg5MDYyNjY1ZmIzX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NDoiQ29kZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNzoic3ViamVjdEdyb3VwLm5hbWUiO3M6NToibGFiZWwiO3M6MTk6Ikdyb3VwZSBkZSBtYXRpw6hyZXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImFiYnJldmlhdGlvbiI7czo1OiJsYWJlbCI7czoxMjoiQWJicmV2aWF0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiI5NTYyNWU1ZWU1M2E5NGUwOTE2NzY3ZWZjOTI3MDc0MF9jb2x1bW5zIjthOjQ6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoic2Nob29sQ2xhc3MubmFtZSI7czo1OiJsYWJlbCI7czo2OiJDbGFzc2UiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJzdWJqZWN0Lm5hbWUiO3M6NToibGFiZWwiO3M6ODoiTWF0acOocmUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJjb2VmZmljaWVudCI7czo1OiJsYWJlbCI7czoxMToiQ29lZmZpY2llbnQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJpc19vcHRpb25hbCI7czo1OiJsYWJlbCI7czo5OiJPcHRpb25uZWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjQ3MzNkODI1MjE4OGU1ZmU1NDY5ZmMxMWI4YWYzMWFlX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRpdGxlIjtzOjU6ImxhYmVsIjtzOjU6IlRpdGxlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJ0eXBlIjtzOjU6ImxhYmVsIjtzOjQ6IlR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE3OiJjYWxlbmRhclllYXIubmFtZSI7czo1OiJsYWJlbCI7czoxNToiQW5uw6llIHNjb2xhaXJlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJzdGFydHNfb24iO3M6NToibGFiZWwiO3M6OToiU3RhcnRzIG9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiaXNfYWxsX2RheSI7czo1OiJsYWJlbCI7czoxNzoiSm91cm7DqWUgZW50acOocmUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6ImIxODVkZTRlMDAxMTUyY2FkNTk5OTVlMGM3YTI1NmJlX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NDoiQ29kZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoicHJvZ3JhbS5uYW1lIjtzOjU6ImxhYmVsIjtzOjk6IlByb2dyYW1tZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6ImVkdWNhdGlvbkxldmVsLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiTml2ZWF1IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiJlNDI0NWE3N2U4ZjJkOGRkOTEyODllZDk5MmNkNWVkZV9jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic3RhZmZfbnVtYmVyIjtzOjU6ImxhYmVsIjtzOjEzOiJOwrAgbWF0cmljdWxlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJmdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTE6Ik5vbSBjb21wbGV0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoic3BlY2lhbGl6YXRpb24iO3M6NToibGFiZWwiO3M6MTU6IlNww6ljaWFsaXNhdGlvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InBob25lX251bWJlciI7czo1OiJsYWJlbCI7czoxMToiVMOpbMOpcGhvbmUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToiZW1haWwiO3M6NToibGFiZWwiO3M6NToiRW1haWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6ImVtcGxveW1lbnRzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjg6IkNvbnRyYXRzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHV0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo5OiJDcsOpw6kgbGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO319fX0=',1791917302),('MNqL6buEIAAn7aCdJNsUoe2lcahHUNrpej6tjc7v',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ05ZSVUyWEZ1ZEs2T2JBWmNqdVZ1YmFtemdDMzk5VFhmVEJxaHNMbiI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNjdhZmIxZDViZmVjZDQzY2JiMzFkNGUwNWJlYWY3OTIzNjUyMTIyNzdhM2ZiNDIwMjE0NDRlYTFiMzM1ODcwYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zdXBlci1hZG1pbiI7czo1OiJyb3V0ZSI7czo0ODoiZmlsYW1lbnQuc3VwZXItYWRtaW4ucGFnZXMuc3VwZXItYWRtaW4tZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1791917134);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streams`
--

DROP TABLE IF EXISTS `streams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streams` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `academic_program_id` bigint(20) unsigned NOT NULL,
  `education_level_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `streams_school_id_academic_program_id_name_unique` (`school_id`,`academic_program_id`,`name`),
  UNIQUE KEY `streams_uuid_unique` (`uuid`),
  KEY `streams_academic_program_id_foreign` (`academic_program_id`),
  KEY `streams_education_level_id_foreign` (`education_level_id`),
  KEY `streams_status_index` (`status`),
  CONSTRAINT `streams_academic_program_id_foreign` FOREIGN KEY (`academic_program_id`) REFERENCES `academic_programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `streams_education_level_id_foreign` FOREIGN KEY (`education_level_id`) REFERENCES `education_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streams`
--

LOCK TABLES `streams` WRITE;
/*!40000 ALTER TABLE `streams` DISABLE KEYS */;
/*!40000 ALTER TABLE `streams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_enrollments`
--

DROP TABLE IF EXISTS `student_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_enrollments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `academic_class_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `grade_level_id` bigint(20) unsigned NOT NULL,
  `section_id` bigint(20) unsigned NOT NULL,
  `classroom_id` bigint(20) unsigned DEFAULT NULL,
  `academic_term_id` bigint(20) unsigned DEFAULT NULL,
  `enrollment_number` varchar(255) NOT NULL,
  `enrollment_date` date NOT NULL,
  `effective_date` date NOT NULL,
  `enrollment_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `transfer_reason` varchar(255) DEFAULT NULL,
  `exit_date` date DEFAULT NULL,
  `exit_reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_enrollments_school_id_enrollment_number_unique` (`school_id`,`enrollment_number`),
  UNIQUE KEY `student_enrollments_uuid_unique` (`uuid`),
  KEY `student_enrollments_classroom_id_foreign` (`classroom_id`),
  KEY `student_enrollments_academic_term_id_foreign` (`academic_term_id`),
  KEY `student_enrollments_student_id_index` (`student_id`),
  KEY `student_enrollments_academic_year_id_index` (`academic_year_id`),
  KEY `student_enrollments_academic_class_id_index` (`academic_class_id`),
  KEY `student_enrollments_program_id_index` (`program_id`),
  KEY `student_enrollments_grade_level_id_index` (`grade_level_id`),
  KEY `student_enrollments_section_id_index` (`section_id`),
  KEY `student_enrollments_student_id_academic_year_id_status_index` (`student_id`,`academic_year_id`,`status`),
  CONSTRAINT `student_enrollments_academic_class_id_foreign` FOREIGN KEY (`academic_class_id`) REFERENCES `academic_classes` (`id`),
  CONSTRAINT `student_enrollments_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`),
  CONSTRAINT `student_enrollments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `student_enrollments_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`),
  CONSTRAINT `student_enrollments_grade_level_id_foreign` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`),
  CONSTRAINT `student_enrollments_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`),
  CONSTRAINT `student_enrollments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  CONSTRAINT `student_enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_enrollments`
--

LOCK TABLES `student_enrollments` WRITE;
/*!40000 ALTER TABLE `student_enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_transport_assignments`
--

DROP TABLE IF EXISTS `student_transport_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_transport_assignments` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `route_id` char(36) NOT NULL,
  `pickup_stop_id` char(36) DEFAULT NULL,
  `dropoff_stop_id` char(36) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `academic_term_id` bigint(20) unsigned DEFAULT NULL,
  `monthly_fee` decimal(10,2) DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'active',
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sta_student_route_year_uniq` (`student_id`,`route_id`,`academic_year_id`),
  KEY `student_transport_assignments_route_id_foreign` (`route_id`),
  KEY `student_transport_assignments_pickup_stop_id_foreign` (`pickup_stop_id`),
  KEY `student_transport_assignments_dropoff_stop_id_foreign` (`dropoff_stop_id`),
  KEY `student_transport_assignments_academic_term_id_foreign` (`academic_term_id`),
  KEY `student_transport_assignments_school_id_index` (`school_id`),
  KEY `student_transport_assignments_student_id_index` (`student_id`),
  KEY `student_transport_assignments_academic_year_id_index` (`academic_year_id`),
  CONSTRAINT `student_transport_assignments_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_assignments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_assignments_dropoff_stop_id_foreign` FOREIGN KEY (`dropoff_stop_id`) REFERENCES `route_stops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_assignments_pickup_stop_id_foreign` FOREIGN KEY (`pickup_stop_id`) REFERENCES `route_stops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_assignments_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_transport_assignments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_transport_assignments`
--

LOCK TABLES `student_transport_assignments` WRITE;
/*!40000 ALTER TABLE `student_transport_assignments` DISABLE KEYS */;
INSERT INTO `student_transport_assignments` VALUES ('01KY7H2ABZC6P6TYFJBN6V5P26','01ky7gd0jm3wgn1bqba86pw1p9',1,'01KY7GWM7E3C3CBA56N5MK9QB5',NULL,NULL,1,NULL,25000.00,'active',NULL,'Transport scolaire','2026-07-23 13:01:34','2026-07-23 13:01:34',NULL),('01KY7H2AEM23H5WS04SM61MEXW','01ky7gd0jm3wgn1bqba86pw1p9',2,'01KY7GWM9Z26B9T3HWD33ED9AS',NULL,NULL,1,NULL,25000.00,'active',NULL,'Transport scolaire','2026-07-23 13:01:34','2026-07-23 13:01:34',NULL),('01KY7H2AK81A582AFQ6STWSZDJ','01ky7gd0jm3wgn1bqba86pw1p9',3,'01KY7GWMDGBNFHFCKPPXC83JV4',NULL,NULL,1,NULL,25000.00,'active',NULL,'Transport scolaire','2026-07-23 13:01:34','2026-07-23 13:01:34',NULL);
/*!40000 ALTER TABLE `student_transport_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `student_number` varchar(50) NOT NULL,
  `admission_number` varchar(50) DEFAULT NULL,
  `national_id` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `preferred_name` varchar(100) DEFAULT NULL,
  `gender` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(200) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `residential_address` text DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_school_id_student_number_unique` (`school_id`,`student_number`),
  UNIQUE KEY `students_school_id_admission_number_unique` (`school_id`,`admission_number`),
  UNIQUE KEY `students_uuid_unique` (`uuid`),
  KEY `students_status_index` (`status`),
  KEY `students_school_id_status_index` (`school_id`,`status`),
  KEY `students_user_id_index` (`user_id`),
  CONSTRAINT `students_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','STU-2024-001','ADM-2024-001',NULL,'Kofi','Asante',NULL,NULL,'male','2010-03-15',NULL,'Ghanaian',NULL,NULL,NULL,'kofi.asante@student.edu',NULL,NULL,'2024-09-01',NULL,NULL,NULL,NULL,'active','2026-07-23 12:51:12','2026-07-23 12:51:12',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','STU-2024-002','ADM-2024-002',NULL,'Ama','Mensah',NULL,NULL,'female','2009-07-22',NULL,'Ghanaian',NULL,NULL,NULL,'ama.mensah@student.edu',NULL,NULL,'2024-09-01',NULL,NULL,NULL,NULL,'active','2026-07-23 12:51:13','2026-07-23 12:51:13',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','STU-2024-003','ADM-2024-003',NULL,'Kwadwo','Ofori',NULL,NULL,'male','2011-01-10',NULL,'Ghanaian',NULL,NULL,NULL,'kwadwo.ofori@student.edu',NULL,NULL,'2024-09-01',NULL,NULL,NULL,NULL,'active','2026-07-23 12:51:13','2026-07-23 12:51:13',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','STU-2024-004','ADM-2024-004',NULL,'Efua','Boateng',NULL,NULL,'female','2010-11-05',NULL,'Ghanaian',NULL,NULL,NULL,'efua.boateng@student.edu',NULL,NULL,'2024-09-01',NULL,NULL,NULL,NULL,'active','2026-07-23 12:51:13','2026-07-23 12:51:13',NULL),(5,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','STU-2024-005','ADM-2024-005',NULL,'Yaw','Amponsah',NULL,NULL,'male','2009-05-30',NULL,'Ghanaian',NULL,NULL,NULL,'yaw.amponsah@student.edu',NULL,NULL,'2024-09-01',NULL,NULL,NULL,NULL,'active','2026-07-23 12:51:14','2026-07-23 12:51:14',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_categories`
--

DROP TABLE IF EXISTS `subject_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `color` varchar(20) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_categories_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `subject_categories_school_code_unique` (`school_id`,`code`),
  UNIQUE KEY `subject_categories_display_order_unique` (`display_order`),
  UNIQUE KEY `subject_categories_uuid_unique` (`uuid`),
  KEY `subject_categories_status_index` (`status`),
  KEY `subject_categories_school_id_display_order_index` (`school_id`,`display_order`),
  CONSTRAINT `subject_categories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_categories`
--

LOCK TABLES `subject_categories` WRITE;
/*!40000 ALTER TABLE `subject_categories` DISABLE KEYS */;
INSERT INTO `subject_categories` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Languages','LANG','Language studies including English, French, and local languages',1,'#3B82F6','book-open','active','2026-07-23 12:50:53','2026-07-23 12:50:53',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Sciences','SCI','Natural and physical sciences',2,'#10B981','beaker','active','2026-07-23 12:50:54','2026-07-23 12:50:54',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Humanities','HUM','History, geography, and social studies',3,'#F59E0B','globe-alt','active','2026-07-23 12:50:55','2026-07-23 12:50:55',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Technical','TECH','Technical and vocational subjects',4,'#8B5CF6','calculator','active','2026-07-23 12:50:55','2026-07-23 12:50:55',NULL),(5,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Business','BIZ','Business and commerce studies',5,'#EC4899','calculator','active','2026-07-23 12:50:55','2026-07-23 12:50:55',NULL),(6,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Arts','ARTS','Visual and performing arts',6,'#F97316','musical-note','active','2026-07-23 12:50:56','2026-07-23 12:50:56',NULL),(7,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Sports','SPT','Physical education and sports',7,'#06B6D4','academic-cap','active','2026-07-23 12:50:56','2026-07-23 12:50:56',NULL),(8,NULL,'01ky7gd0jm3wgn1bqba86pw1p9','Religious Education','RE','Moral and religious instruction',8,'#64748B','academic-cap','active','2026-07-23 12:50:56','2026-07-23 12:50:56',NULL);
/*!40000 ALTER TABLE `subject_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_coefficients`
--

DROP TABLE IF EXISTS `subject_coefficients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject_coefficients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `grade_level_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `coefficient` decimal(5,2) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_coefficients_unique_active` (`school_id`,`subject_id`,`program_id`,`grade_level_id`,`academic_year_id`,`status`),
  UNIQUE KEY `subject_coefficients_uuid_unique` (`uuid`),
  KEY `subject_coefficients_subject_id_foreign` (`subject_id`),
  KEY `subject_coefficients_program_id_foreign` (`program_id`),
  KEY `subject_coefficients_grade_level_id_foreign` (`grade_level_id`),
  KEY `subject_coefficients_academic_year_id_foreign` (`academic_year_id`),
  KEY `subject_coefficients_school_id_subject_id_index` (`school_id`,`subject_id`),
  KEY `subject_coefficients_school_id_program_id_index` (`school_id`,`program_id`),
  KEY `subject_coefficients_school_id_grade_level_id_index` (`school_id`,`grade_level_id`),
  KEY `subject_coefficients_school_id_academic_year_id_index` (`school_id`,`academic_year_id`),
  KEY `subject_coefficients_school_id_status_index` (`school_id`,`status`),
  KEY `subject_coefficients_effective_from_effective_to_index` (`effective_from`,`effective_to`),
  CONSTRAINT `subject_coefficients_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subject_coefficients_grade_level_id_foreign` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_coefficients_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_coefficients_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subject_coefficients_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_coefficients`
--

LOCK TABLES `subject_coefficients` WRITE;
/*!40000 ALTER TABLE `subject_coefficients` DISABLE KEYS */;
INSERT INTO `subject_coefficients` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',6,4,1,NULL,1.12,'2026-01-05','2026-10-28',NULL,'active','2026-07-23 12:51:02','2026-07-23 12:51:02',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',6,4,2,NULL,2.31,'2025-10-08','2027-05-24','Itaque sint expedita omnis ut ea dolor.','active','2026-07-23 12:51:02','2026-07-23 12:51:02',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',6,4,3,NULL,0.91,'2026-02-05',NULL,'Doloribus excepturi fuga incidunt cum consequatur.','active','2026-07-23 12:51:04','2026-07-23 12:51:04',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',6,1,1,NULL,1.14,'2026-02-14',NULL,'Reprehenderit numquam quia nulla perspiciatis quod quos ad.','active','2026-07-23 12:51:04','2026-07-23 12:51:04',NULL),(5,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',6,1,2,NULL,1.94,'2026-01-24',NULL,NULL,'active','2026-07-23 12:51:04','2026-07-23 12:51:04',NULL),(6,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',6,1,3,NULL,1.91,'2026-05-29','2027-05-17',NULL,'active','2026-07-23 12:51:04','2026-07-23 12:51:04',NULL),(7,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,4,1,NULL,1.39,'2026-01-28',NULL,'Numquam eligendi rerum est excepturi rerum doloribus qui.','active','2026-07-23 12:51:04','2026-07-23 12:51:04',NULL),(8,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,4,2,NULL,1.51,'2025-10-07',NULL,NULL,'active','2026-07-23 12:51:05','2026-07-23 12:51:05',NULL),(9,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,4,3,NULL,1.73,'2026-03-07','2026-09-17',NULL,'active','2026-07-23 12:51:05','2026-07-23 12:51:05',NULL),(10,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,1,1,NULL,2.08,'2025-08-04','2027-01-21','Odio et beatae est atque quidem.','active','2026-07-23 12:51:05','2026-07-23 12:51:05',NULL),(11,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,1,2,NULL,2.84,'2025-12-13','2027-07-08','Quidem unde ut enim iusto consequatur nobis voluptatibus.','active','2026-07-23 12:51:05','2026-07-23 12:51:05',NULL),(12,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,1,3,NULL,2.42,'2025-08-14','2026-11-22','Delectus non dolores suscipit.','active','2026-07-23 12:51:06','2026-07-23 12:51:06',NULL),(13,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,4,1,NULL,2.66,'2026-04-14','2026-10-31',NULL,'active','2026-07-23 12:51:06','2026-07-23 12:51:06',NULL),(14,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,4,2,NULL,1.17,'2026-02-08','2026-12-18','Et id exercitationem accusamus voluptatem qui.','active','2026-07-23 12:51:06','2026-07-23 12:51:06',NULL),(15,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,4,3,NULL,1.03,'2026-05-26',NULL,'Voluptatibus nisi modi qui similique accusamus.','active','2026-07-23 12:51:06','2026-07-23 12:51:06',NULL),(16,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,1,1,NULL,2.51,'2026-06-16',NULL,'Deserunt et non nisi optio et sit quaerat labore.','active','2026-07-23 12:51:07','2026-07-23 12:51:07',NULL),(17,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,1,2,NULL,1.65,'2025-10-16',NULL,'Est corrupti recusandae impedit consectetur id aut id.','active','2026-07-23 12:51:07','2026-07-23 12:51:07',NULL),(18,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,1,3,NULL,2.48,'2026-01-14','2027-07-03',NULL,'active','2026-07-23 12:51:07','2026-07-23 12:51:07',NULL),(19,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',10,4,1,NULL,2.51,'2025-09-09','2027-06-08','Velit architecto debitis asperiores et natus.','active','2026-07-23 12:51:07','2026-07-23 12:51:07',NULL),(20,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',10,4,2,NULL,2.91,'2026-06-26',NULL,'Consectetur nam suscipit ullam veritatis eos.','active','2026-07-23 12:51:07','2026-07-23 12:51:07',NULL),(21,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',10,4,3,NULL,1.04,'2026-03-22','2026-11-02',NULL,'active','2026-07-23 12:51:07','2026-07-23 12:51:07',NULL),(22,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',10,1,1,NULL,1.50,'2025-10-05','2027-03-27',NULL,'active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(23,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',10,1,2,NULL,1.43,'2026-05-25',NULL,NULL,'active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(24,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',10,1,3,NULL,0.88,'2026-07-18','2026-12-22',NULL,'active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(25,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',11,4,1,NULL,2.10,'2025-09-16','2026-09-19',NULL,'active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(26,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',11,4,2,NULL,2.55,'2026-05-28','2027-02-16',NULL,'active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(27,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',11,4,3,NULL,2.78,'2026-01-26',NULL,'Nesciunt alias rerum ipsam suscipit aperiam expedita aliquid.','active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(28,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',11,1,1,NULL,0.99,'2026-05-17',NULL,'Magni quidem accusamus ullam.','active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(29,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',11,1,2,NULL,0.66,'2026-03-23','2027-05-28',NULL,'active','2026-07-23 12:51:08','2026-07-23 12:51:08',NULL),(30,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',11,1,3,NULL,1.35,'2026-05-12',NULL,NULL,'active','2026-07-23 12:51:09','2026-07-23 12:51:09',NULL);
/*!40000 ALTER TABLE `subject_coefficients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject_groups`
--

DROP TABLE IF EXISTS `subject_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_groups_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `subject_groups_uuid_unique` (`uuid`),
  KEY `subject_groups_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject_groups`
--

LOCK TABLES `subject_groups` WRITE;
/*!40000 ALTER TABLE `subject_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `subject_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(36) NOT NULL,
  `subject_category_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `program_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `short_name` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `color` varchar(20) DEFAULT NULL,
  `is_elective` tinyint(1) NOT NULL DEFAULT 0,
  `is_core_subject` tinyint(1) NOT NULL DEFAULT 0,
  `weekly_hours` smallint(5) unsigned NOT NULL DEFAULT 1,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_school_id_name_unique` (`school_id`,`name`),
  UNIQUE KEY `subjects_school_code_unique` (`school_id`,`code`),
  UNIQUE KEY `subjects_uuid_unique` (`uuid`),
  KEY `subjects_status_index` (`status`),
  KEY `subjects_school_id_display_order_index` (`school_id`,`display_order`),
  KEY `subjects_subject_category_id_index` (`subject_category_id`),
  KEY `subjects_department_id_index` (`department_id`),
  KEY `subjects_program_id_index` (`program_id`),
  CONSTRAINT `subjects_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subjects_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subjects_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subjects_subject_category_id_foreign` FOREIGN KEY (`subject_category_id`) REFERENCES `subject_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,NULL,'Mathématiques','MATH','Math','Mathématiques et algèbre',1,'#3B82F6',0,1,5,'active','2026-07-23 12:50:57','2026-07-23 12:50:57',NULL),(2,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,NULL,'Physique','PHY','Phys','Sciences physiques',2,'#10B981',0,1,4,'active','2026-07-23 12:50:58','2026-07-23 12:50:58',NULL),(3,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,NULL,'Chimie','CHEM','Chim','Sciences chimiques',3,'#8B5CF6',0,1,4,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(4,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',2,NULL,NULL,'Biologie','BIO','Bio','Sciences du vivant',4,'#06B6D4',0,1,3,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(5,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',1,NULL,NULL,'Français','FRE','Fr','Études de la langue française',5,'#EC4899',0,1,4,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(6,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',1,NULL,NULL,'Anglais','ENG','En','Études de la langue anglaise',6,'#F59E0B',0,1,4,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(7,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',3,NULL,NULL,'Histoire-Géographie','HIST','H/G','Études historiques et géographiques',7,'#F97316',0,1,3,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(8,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,NULL,NULL,'Informatique','CS','Info','Informatique et programmation',8,'#14B8A6',1,0,3,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(9,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',5,NULL,NULL,'Économie','ECON','Éco','Études économiques',9,'#A855F7',1,0,3,'active','2026-07-23 12:51:00','2026-07-23 12:51:00',NULL),(10,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',5,NULL,NULL,'Comptabilité','ACC','Compta','Principes de comptabilité',10,'#EF4444',1,0,3,'active','2026-07-23 12:51:01','2026-07-23 12:51:01',NULL),(11,NULL,'01ky7gd0jm3wgn1bqba86pw1p9',4,NULL,NULL,'Dessin Technique','TD','DT','Dessin et conception technique',11,'#0EA5E9',1,0,2,'active','2026-07-23 12:51:01','2026-07-23 12:51:01',NULL);
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` char(36) NOT NULL,
  `school_id` char(36) NOT NULL,
  `plan_id` char(36) NOT NULL,
  `starts_at` date NOT NULL,
  `expires_at` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `auto_renew` tinyint(1) NOT NULL DEFAULT 1,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_school_id_foreign` (`school_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_status_index` (`status`),
  KEY `subscriptions_created_at_index` (`created_at`),
  KEY `subscriptions_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscriptions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_health`
--

DROP TABLE IF EXISTS `system_health`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_health` (
  `id` char(36) NOT NULL,
  `service` varchar(100) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'unknown',
  `response_time_ms` smallint(5) unsigned DEFAULT NULL,
  `message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `cpu_usage_percent` smallint(5) unsigned DEFAULT NULL,
  `memory_usage_percent` smallint(5) unsigned DEFAULT NULL,
  `disk_used_bytes` bigint(20) unsigned DEFAULT NULL,
  `disk_total_bytes` bigint(20) unsigned DEFAULT NULL,
  `checked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_health`
--

LOCK TABLES `system_health` WRITE;
/*!40000 ALTER TABLE `system_health` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_health` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `group` varchar(100) NOT NULL,
  `key` varchar(200) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'string',
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 0,
  `is_readonly` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_school_group_key_unique` (`school_id`,`group`,`key`),
  KEY `system_settings_school_id_group_index` (`school_id`,`group`),
  KEY `system_settings_school_id_key_index` (`school_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenants` (
  `id` varchar(255) NOT NULL,
  `uuid` char(26) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `database` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'UTC',
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `locale` varchar(10) NOT NULL DEFAULT 'en',
  `logo_url` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `school_type` varchar(255) NOT NULL DEFAULT 'secondary',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`),
  UNIQUE KEY `tenants_database_unique` (`database`),
  UNIQUE KEY `tenants_uuid_unique` (`uuid`),
  UNIQUE KEY `tenants_domain_unique` (`domain`),
  UNIQUE KEY `tenants_registration_number_unique` (`registration_number`),
  KEY `tenants_status_index` (`status`),
  KEY `tenants_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES ('01ky7gd0jm3wgn1bqba86pw1p9','01KY7GD0FFAJ5DAQP6REV8T8JZ','Nexus Demo Academy','nexus-demo','school_nexus_demo','nexus-demo.nexus.school','admin@demo.nexus.school','+226 01 00 00 00','123 Avenue de l\'Éducation','Ouagadougou','Centre','Burkina Faso','01000','Africa/Ouagadougou','XOF','fr',NULL,'REG-00001','secondary','active','{\"grading_system\":\"standard\",\"max_students_per_class\":40,\"academic_week_starts\":\"monday\"}',NULL,'2026-07-23 12:49:56','2026-07-24 03:25:55',NULL,'{\"created_at\":\"2026-07-23 12:49:56\",\"updated_at\":\"2026-07-23 12:49:56\",\"deleted_at\":null}');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetable_entries`
--

DROP TABLE IF EXISTS `timetable_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timetable_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) NOT NULL,
  `academic_year_id` bigint(20) unsigned NOT NULL,
  `academic_term_id` bigint(20) unsigned DEFAULT NULL,
  `academic_class_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `subject_id` bigint(20) unsigned NOT NULL,
  `classroom_id` bigint(20) unsigned NOT NULL,
  `day_of_week` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `period_number` int(10) unsigned DEFAULT NULL,
  `session_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `version` varchar(255) DEFAULT NULL,
  `is_override` tinyint(1) NOT NULL DEFAULT 0,
  `override_reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timetable_entries_uuid_unique` (`uuid`),
  KEY `timetable_entries_academic_term_id_foreign` (`academic_term_id`),
  KEY `timetable_entries_school_id_index` (`school_id`),
  KEY `timetable_entries_academic_year_id_index` (`academic_year_id`),
  KEY `timetable_entries_academic_class_id_index` (`academic_class_id`),
  KEY `timetable_entries_user_id_index` (`user_id`),
  KEY `timetable_entries_subject_id_index` (`subject_id`),
  KEY `timetable_entries_classroom_id_index` (`classroom_id`),
  KEY `timetable_entries_day_of_week_index` (`day_of_week`),
  KEY `timetable_entries_status_index` (`status`),
  KEY `timetable_entries_user_id_day_of_week_index` (`user_id`,`day_of_week`),
  KEY `timetable_entries_classroom_id_day_of_week_index` (`classroom_id`,`day_of_week`),
  KEY `timetable_entries_academic_class_id_day_of_week_index` (`academic_class_id`,`day_of_week`),
  CONSTRAINT `timetable_entries_academic_class_id_foreign` FOREIGN KEY (`academic_class_id`) REFERENCES `academic_classes` (`id`),
  CONSTRAINT `timetable_entries_academic_term_id_foreign` FOREIGN KEY (`academic_term_id`) REFERENCES `academic_terms` (`id`),
  CONSTRAINT `timetable_entries_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `timetable_entries_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`),
  CONSTRAINT `timetable_entries_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `timetable_entries_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `timetable_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable_entries`
--

LOCK TABLES `timetable_entries` WRITE;
/*!40000 ALTER TABLE `timetable_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `timetable_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trips`
--

DROP TABLE IF EXISTS `trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trips` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `driver_id` char(36) NOT NULL,
  `route_id` char(36) NOT NULL,
  `direction` varchar(20) NOT NULL,
  `academic_date` date NOT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `actual_departure` timestamp NULL DEFAULT NULL,
  `actual_arrival` timestamp NULL DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trips_vehicle_id_foreign` (`vehicle_id`),
  KEY `trips_driver_id_foreign` (`driver_id`),
  KEY `trips_route_id_foreign` (`route_id`),
  KEY `trips_school_id_index` (`school_id`),
  KEY `trips_academic_date_index` (`academic_date`),
  KEY `trips_status_index` (`status`),
  CONSTRAINT `trips_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trips_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trips_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trips`
--

LOCK TABLES `trips` WRITE;
/*!40000 ALTER TABLE `trips` DISABLE KEYS */;
INSERT INTO `trips` VALUES ('01KY7H4582DE9VK1N4NMB7GQ3C','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZGVH6KK385W7H6N79ZQ','01KY7GW25K2KJ0C4JMVJVX83AT','01KY7GWM7E3C3CBA56N5MK9QB5','outbound','2026-07-22','06:30:00','07:15:00','2026-07-22 06:32:00','2026-07-22 07:12:00','completed','Trajet matinal effectué','2026-07-23 13:02:34','2026-07-23 13:02:34',NULL),('01KY7H45AK9R1Q72J7RDGFH67F','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZGVH6KK385W7H6N79ZQ','01KY7GW25K2KJ0C4JMVJVX83AT','01KY7GWM7E3C3CBA56N5MK9QB5','inbound','2026-07-22','15:00:00','15:45:00','2026-07-22 15:02:00','2026-07-22 15:42:00','completed','Retour après-midi effectué','2026-07-23 13:02:34','2026-07-23 13:02:34',NULL),('01KY7H45DZGG1QQ23NWCJ1RPYN','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZJZGDS1JGBAWJFCRTS6','01KY7GW2A0KWWFNZDNRBG5KJGJ','01KY7GWM9Z26B9T3HWD33ED9AS','outbound','2026-07-24','06:30:00',NULL,NULL,NULL,'scheduled','Trajet planifié pour demain','2026-07-23 13:02:34','2026-07-23 13:02:34',NULL),('01KY7H6P9Z8KSVBF3A67W2ZFY8','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZGVH6KK385W7H6N79ZQ','01KY7GW25K2KJ0C4JMVJVX83AT','01KY7GWM7E3C3CBA56N5MK9QB5','outbound','2026-07-22','06:30:00','07:15:00','2026-07-22 06:32:00','2026-07-22 07:12:00','completed','Trajet matinal effectué','2026-07-23 13:03:57','2026-07-23 13:03:57',NULL),('01KY7H6PD2TPATD7CXCHE0WJDJ','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZGVH6KK385W7H6N79ZQ','01KY7GW25K2KJ0C4JMVJVX83AT','01KY7GWM7E3C3CBA56N5MK9QB5','inbound','2026-07-22','15:00:00','15:45:00','2026-07-22 15:02:00','2026-07-22 15:42:00','completed','Retour après-midi effectué','2026-07-23 13:03:57','2026-07-23 13:03:57',NULL),('01KY7H6PEMTMC3VXWDSXGW64FD','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZJZGDS1JGBAWJFCRTS6','01KY7GW2A0KWWFNZDNRBG5KJGJ','01KY7GWM9Z26B9T3HWD33ED9AS','outbound','2026-07-24','06:30:00',NULL,NULL,NULL,'scheduled','Trajet planifié pour demain','2026-07-23 13:03:57','2026-07-23 13:03:57',NULL),('01KY7H6PF7FZ3PCBJ58R4404WC','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZT50VVTQ1W3X21WTDB0','01KY7GW2A0KWWFNZDNRBG5KJGJ','01KY7GWM9Z26B9T3HWD33ED9AS','outbound','2026-07-23','06:30:00',NULL,NULL,NULL,'started','Trajet en cours','2026-07-23 13:03:57','2026-07-23 13:03:57',NULL);
/*!40000 ALTER TABLE `trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(26) DEFAULT NULL,
  `school_id` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  KEY `users_status_index` (`status`),
  KEY `users_school_id_index` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'01KY7GTAC12DNFNTJRBBCM0PMA','01ky7gd0jm3wgn1bqba86pw1p9','Admin','Système','admin@nexusschool.local',NULL,NULL,'$2y$12$RAmHMYq.s28i5.CH7gAvx.tdZAZB/8o78HtbIec0DPERs4/5dNCcG','active',NULL,'2026-07-23 12:57:12','GwP3SN533RN5rJRM5qULEGhbyezMOea8QuQqFXlm9m1SkoZU32CyAIc3aq0B','2026-07-23 12:57:12','2026-07-23 12:57:12',NULL),(2,'01KY7GTBCQZ3ANH8XN87S0TRDB','01ky7gd0jm3wgn1bqba86pw1p9','Jean','Dupont','teacher@nexusdemo.edu',NULL,NULL,'$2y$12$PTUnoujgKcyIEFo0U9d/1O2510KkVHw8iUX5rYha/49PtueqWza.K','active',NULL,'2026-07-23 12:57:13',NULL,'2026-07-23 12:57:13','2026-07-23 12:57:13',NULL),(3,'01KYA0PGK4JVWQY9NA4K34BHZZ',NULL,'Admin','School','admin@nexus.dev',NULL,NULL,'$2y$12$vMMfX2RE7UkSPXpPald1l.h1b2Sz3gkSrSWKdTWjMeBgAW7ApoQaG','active',NULL,NULL,NULL,'2026-07-24 12:13:13','2026-07-24 12:13:13',NULL),(4,'01KYBKP451BM8J32JC3981WHZ9',NULL,'Kwamena','Shine','sylvestrezongo7@gmail.com','51337713','avatars/01KYBKP3GWRERTE4DRZTPYV347.png','$2y$12$hiBWffJ1JIExJ/koXn346OMWtE3GItzZze0G8rGESRRvticPhKSjm','active',NULL,NULL,NULL,'2026-07-25 03:04:18','2026-07-25 03:04:18',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_maintenances`
--

DROP TABLE IF EXISTS `vehicle_maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicle_maintenances` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `vehicle_id` char(36) NOT NULL,
  `type` varchar(36) NOT NULL,
  `description` text NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `service_provider` varchar(255) DEFAULT NULL,
  `service_date` date NOT NULL,
  `next_service_date` date DEFAULT NULL,
  `odometer_reading` int(10) unsigned DEFAULT NULL,
  `receipt_document` varchar(255) DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'scheduled',
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_maintenances_school_id_index` (`school_id`),
  KEY `vehicle_maintenances_vehicle_id_index` (`vehicle_id`),
  KEY `vehicle_maintenances_status_index` (`status`),
  CONSTRAINT `vehicle_maintenances_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_maintenances`
--

LOCK TABLES `vehicle_maintenances` WRITE;
/*!40000 ALTER TABLE `vehicle_maintenances` DISABLE KEYS */;
INSERT INTO `vehicle_maintenances` VALUES ('01KY7H2CNWX7FVGVWDM4Z0DK7X','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZGVH6KK385W7H6N79ZQ','preventive','Vidange et remplacement du filtre',75000.00,'Garage Central','2026-06-23',NULL,NULL,NULL,'completed','2026-06-23 13:01:36',NULL,'2026-07-23 13:01:36','2026-07-23 13:01:36',NULL),('01KY7H2CRSWEW6MSFAAYFZNFVR','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZJZGDS1JGBAWJFCRTS6','inspection','Inspection annuelle de sécurité',35000.00,'Centre Technique Auto','2026-07-09',NULL,NULL,NULL,'completed','2026-07-09 13:01:36',NULL,'2026-07-23 13:01:36','2026-07-23 13:01:36',NULL),('01KY7H2CVEQDRQCB14NWSWFGM0','01ky7gd0jm3wgn1bqba86pw1p9','01KY7GVZPN404FR6WCZ9RD5QF0','corrective','Remplacement des plaquettes de frein',120000.00,'Garage Central','2026-07-30',NULL,NULL,NULL,'scheduled',NULL,NULL,'2026-07-23 13:01:37','2026-07-23 13:01:37',NULL);
/*!40000 ALTER TABLE `vehicle_maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `vin` varchar(17) NOT NULL,
  `manufacturer` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `capacity` smallint(5) unsigned NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `inspection_expiry` date DEFAULT NULL,
  `insurance_document` varchar(255) DEFAULT NULL,
  `status` varchar(36) NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicles_school_id_index` (`school_id`),
  KEY `vehicles_registration_number_index` (`registration_number`),
  KEY `vehicles_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES ('01KY7GVZGVH6KK385W7H6N79ZQ','01ky7gd0jm3wgn1bqba86pw1p9','GR-1234-23','1HGCM82633A123456','Toyota','HiAce',2023,'White',14,'diesel',NULL,NULL,NULL,'available',NULL,'2026-07-23 12:58:06','2026-07-23 12:58:06',NULL),('01KY7GVZJZGDS1JGBAWJFCRTS6','01ky7gd0jm3wgn1bqba86pw1p9','GR-5678-23','2HGBH4J5XFM123456','Hyundai','County',2022,'Blue',25,'diesel',NULL,NULL,NULL,'available',NULL,'2026-07-23 12:58:06','2026-07-23 12:58:06',NULL),('01KY7GVZPN404FR6WCZ9RD5QF0','01ky7gd0jm3wgn1bqba86pw1p9','GR-9012-23','3NPECAJ01FM123456','Nissan','Civilian',2024,'Silver',18,'diesel',NULL,NULL,NULL,'in_service',NULL,'2026-07-23 12:58:06','2026-07-23 12:58:06',NULL),('01KY7GVZRJCVN19PHE8SZYXP25','01ky7gd0jm3wgn1bqba86pw1p9','GR-3456-23','4T1BG1FK5FU123456','Toyota','Coaster',2021,'Yellow',30,'diesel',NULL,NULL,NULL,'maintenance',NULL,'2026-07-23 12:58:06','2026-07-23 12:58:06',NULL),('01KY7GVZT50VVTQ1W3X21WTDB0','01ky7gd0jm3wgn1bqba86pw1p9','GR-7890-23','5YJSA1E14HF123456','Mercedes-Benz','Sprinter',2025,'White',20,'diesel',NULL,NULL,NULL,'available',NULL,'2026-07-23 12:58:07','2026-07-23 12:58:07',NULL);
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhook_deliveries`
--

DROP TABLE IF EXISTS `webhook_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhook_deliveries` (
  `id` char(36) NOT NULL,
  `webhook_id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `event` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `response_status` int(11) DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `delivered_at` datetime DEFAULT NULL,
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `max_retries` int(11) NOT NULL DEFAULT 3,
  `next_retry_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhook_deliveries_webhook_id_index` (`webhook_id`),
  KEY `webhook_deliveries_school_id_index` (`school_id`),
  KEY `webhook_deliveries_event_index` (`event`),
  KEY `webhook_deliveries_success_index` (`success`),
  CONSTRAINT `webhook_deliveries_webhook_id_foreign` FOREIGN KEY (`webhook_id`) REFERENCES `webhooks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhook_deliveries`
--

LOCK TABLES `webhook_deliveries` WRITE;
/*!40000 ALTER TABLE `webhook_deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `webhook_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhooks`
--

DROP TABLE IF EXISTS `webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webhooks` (
  `id` char(36) NOT NULL,
  `api_client_id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `url` varchar(500) NOT NULL,
  `secret` varchar(255) NOT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `failure_count` int(11) NOT NULL DEFAULT 0,
  `last_triggered_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhooks_api_client_id_index` (`api_client_id`),
  KEY `webhooks_school_id_index` (`school_id`),
  CONSTRAINT `webhooks_api_client_id_foreign` FOREIGN KEY (`api_client_id`) REFERENCES `api_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhooks`
--

LOCK TABLES `webhooks` WRITE;
/*!40000 ALTER TABLE `webhooks` DISABLE KEYS */;
INSERT INTO `webhooks` VALUES ('01KY7HDTMXZN6WHP1JDEPR26NC','01KY7HAKN18BDRNYJSRNVM1QRZ','01ky7gd0jm3wgn1bqba86pw1p9','http://localhost/api/webhooks/demo/receive','d9l9bhpn2C1uYRqhIdsKcSxeiEgAJaoOjXZthLcSzASdAgInoZgWPmT68QA6lnjW','[\"student.created\",\"enrollment.created\"]',1,'Webhook de démonstration pour les événements d\'élèvements et d\'inscriptions',0,NULL,'2026-07-23 13:07:51','2026-07-23 13:07:51',NULL);
/*!40000 ALTER TABLE `webhooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_executions`
--

DROP TABLE IF EXISTS `workflow_executions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_executions` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `workflow_id` varchar(255) NOT NULL,
  `workflow_version_id` varchar(255) NOT NULL,
  `trigger_id` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `current_node_id` varchar(100) DEFAULT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`result`)),
  `error` text DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `duration_ms` int(10) unsigned DEFAULT NULL,
  `retry_count` smallint(5) unsigned NOT NULL DEFAULT 0,
  `max_retries` smallint(5) unsigned NOT NULL DEFAULT 3,
  `idempotency_key` varchar(255) DEFAULT NULL,
  `initiated_by` varchar(255) DEFAULT NULL,
  `initiated_by_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wf_exec_idempotency_unique` (`school_id`,`idempotency_key`),
  KEY `workflow_executions_workflow_version_id_foreign` (`workflow_version_id`),
  KEY `workflow_executions_workflow_id_status_index` (`workflow_id`,`status`),
  KEY `workflow_executions_school_id_status_index` (`school_id`,`status`),
  KEY `workflow_executions_started_at_index` (`started_at`),
  KEY `workflow_executions_status_index` (`status`),
  CONSTRAINT `workflow_executions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_executions_workflow_id_foreign` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_executions_workflow_version_id_foreign` FOREIGN KEY (`workflow_version_id`) REFERENCES `workflow_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_executions`
--

LOCK TABLES `workflow_executions` WRITE;
/*!40000 ALTER TABLE `workflow_executions` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_executions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_schedules`
--

DROP TABLE IF EXISTS `workflow_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_schedules` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `workflow_id` varchar(255) NOT NULL,
  `trigger_id` varchar(255) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `cron_expression` varchar(100) NOT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `last_run_at` timestamp NULL DEFAULT NULL,
  `next_run_at` timestamp NULL DEFAULT NULL,
  `total_runs` int(10) unsigned NOT NULL DEFAULT 0,
  `failed_runs` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_schedules_school_id_foreign` (`school_id`),
  KEY `workflow_schedules_workflow_id_is_active_index` (`workflow_id`,`is_active`),
  KEY `workflow_schedules_is_active_next_run_at_index` (`is_active`,`next_run_at`),
  CONSTRAINT `workflow_schedules_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_schedules_workflow_id_foreign` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_schedules`
--

LOCK TABLES `workflow_schedules` WRITE;
/*!40000 ALTER TABLE `workflow_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_tasks`
--

DROP TABLE IF EXISTS `workflow_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_tasks` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `workflow_execution_id` varchar(255) NOT NULL,
  `node_id` varchar(100) NOT NULL,
  `type` varchar(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `assigned_to` varchar(255) DEFAULT NULL,
  `assigned_to_type` varchar(50) DEFAULT NULL,
  `assigned_by` varchar(255) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `input_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`input_data`)),
  `output_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`output_data`)),
  `notes` text DEFAULT NULL,
  `error` text DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `due_at` timestamp NULL DEFAULT NULL,
  `duration_ms` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_tasks_school_id_foreign` (`school_id`),
  KEY `workflow_tasks_workflow_execution_id_status_index` (`workflow_execution_id`,`status`),
  KEY `workflow_tasks_assigned_to_status_index` (`assigned_to`,`status`),
  KEY `workflow_tasks_due_at_index` (`due_at`),
  KEY `workflow_tasks_type_index` (`type`),
  CONSTRAINT `workflow_tasks_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_tasks_workflow_execution_id_foreign` FOREIGN KEY (`workflow_execution_id`) REFERENCES `workflow_executions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_tasks`
--

LOCK TABLES `workflow_tasks` WRITE;
/*!40000 ALTER TABLE `workflow_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_triggers`
--

DROP TABLE IF EXISTS `workflow_triggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_triggers` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `workflow_id` varchar(255) NOT NULL,
  `type` varchar(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `event_name` varchar(255) DEFAULT NULL,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schedule`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `cooldown_seconds` int(10) unsigned NOT NULL DEFAULT 0,
  `max_executions_per_hour` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_triggers_school_id_foreign` (`school_id`),
  KEY `workflow_triggers_workflow_id_is_active_index` (`workflow_id`,`is_active`),
  KEY `workflow_triggers_type_index` (`type`),
  KEY `workflow_triggers_event_name_index` (`event_name`),
  CONSTRAINT `workflow_triggers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_triggers_workflow_id_foreign` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_triggers`
--

LOCK TABLES `workflow_triggers` WRITE;
/*!40000 ALTER TABLE `workflow_triggers` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_triggers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_versions`
--

DROP TABLE IF EXISTS `workflow_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflow_versions` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `workflow_id` varchar(255) NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `changelog` text DEFAULT NULL,
  `nodes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`nodes`)),
  `edges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`edges`)),
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `published_by` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflow_versions_workflow_id_version_unique` (`workflow_id`,`version`),
  KEY `workflow_versions_school_id_foreign` (`school_id`),
  KEY `workflow_versions_workflow_id_status_index` (`workflow_id`,`status`),
  CONSTRAINT `workflow_versions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workflow_versions_workflow_id_foreign` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_versions`
--

LOCK TABLES `workflow_versions` WRITE;
/*!40000 ALTER TABLE `workflow_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflows`
--

DROP TABLE IF EXISTS `workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workflows` (
  `id` char(36) NOT NULL,
  `school_id` varchar(255) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `priority` smallint(5) unsigned NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `current_version` int(10) unsigned NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflows_school_id_slug_unique` (`school_id`,`slug`),
  CONSTRAINT `workflows_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflows`
--

LOCK TABLES `workflows` WRITE;
/*!40000 ALTER TABLE `workflows` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-13 18:57:17
