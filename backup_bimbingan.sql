-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: BimbinganKonsultasi
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_username_unique` (`username`),
  UNIQUE KEY `admin_email_unique` (`email`),
  KEY `admin_role_id_foreign` (`role_id`),
  CONSTRAINT `admin_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','Administrator','admin@example.com','$2y$12$M2p.JYTSIQYDyzP63SMsZOTcCq2pcC8GBVXVOL4MNNnmSq5JPOwsi',4,'c7R0mnuy6Hxc50LjqhsAx8Yr0bXvwGfW3AIV3KsmUzN7OJiAXPljlG0EaAoj','2025-05-20 13:04:54','2025-05-21 04:26:56');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_bimbingan`
--

DROP TABLE IF EXISTS `booking_bimbingan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking_bimbingan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jadwal_id` bigint unsigned NOT NULL,
  `mahasiswa_id` bigint unsigned NOT NULL,
  `status_booking` enum('aktif','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_bimbingan_jadwal_id_foreign` (`jadwal_id`),
  CONSTRAINT `booking_bimbingan_jadwal_id_foreign` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_bimbingans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_bimbingan`
--

LOCK TABLES `booking_bimbingan` WRITE;
/*!40000 ALTER TABLE `booking_bimbingan` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_bimbingan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `dosens`
--

DROP TABLE IF EXISTS `dosens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dosens` (
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_singkat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `google_access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_refresh_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_token_expires_in` int DEFAULT NULL,
  `google_token_created_at` timestamp NULL DEFAULT NULL,
  `is_koordinator` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nip`),
  UNIQUE KEY `dosens_email_unique` (`email`),
  KEY `dosens_prodi_id_foreign` (`prodi_id`),
  KEY `dosens_role_id_foreign` (`role_id`),
  CONSTRAINT `dosens_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`),
  CONSTRAINT `dosens_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dosens`
--

LOCK TABLES `dosens` WRITE;
/*!40000 ALTER TABLE `dosens` DISABLE KEYS */;
INSERT INTO `dosens` VALUES ('197404282002121003','Feri Candra, S.T., M.T., Ph.D','FC','feri@eng.unri.ac.id','$2y$12$poqqGmpzMSImwOhwbNoWiuicc/6UKfT5UOr8zB/B12VJJtDepeIi6',NULL,NULL,'2025-06-01 17:30:40','2025-06-01 17:30:40',2,3,NULL,NULL,NULL,NULL,0),('198501012015041001','Contoh Dosen 1','UA','ummul.azhari4051@student.unri.ac.id','$2y$12$uzGzEkEyYQsmWm006AvGnewkEfSBcvFO.9GYizMU7MV4d.DChuL4u',NULL,NULL,'2025-04-27 20:37:51','2025-05-28 12:07:36',2,1,NULL,NULL,NULL,NULL,0),('1985010120150410027','syahirah Tri','ST','syahirahtrimeilinaa25@gmail.com','$2y$12$1O8nwxVdlpvZb1u6ZiF1DeWV5OkSLKx.7noaDTKLpvEKpl.jYwUj6',NULL,NULL,'2025-05-28 15:38:03','2025-05-28 15:38:03',2,1,NULL,NULL,NULL,NULL,0),('198501012015041025','Contoh Dosen 3','CD','adrian.marchel@student.unri.ac.id','$2y$12$ngbMgxuuG7hwKTcrkc0mkeaWXXbl3WyhZM0J/GtT7S9BECMLf.ASe',NULL,NULL,'2025-04-27 20:37:52','2025-05-27 12:05:08',2,3,'eyJpdiI6ImZXL1FRY3BFdW1VeUZ3c3dzSG52YUE9PSIsInZhbHVlIjoic29PQ0hEVmtKbVJoQzFPbjhQdUhHRFhCVXFFWGQ5S2wwZmFHK3Y4TGttR29ia3RXL1RrSFNVZzhMVGM5SWdSeWRzeUtOQjJRZEk1ZEd1TnVDNDQxRHpzVk01YXhFOTNCOTh0TlMzNU81RDlITERWUVZ1azZSdzBwTVBRMEhISFFVNmlzMHpqdm83OEVaZVZuZ09XeXJUTm51V01ITzZ3Q0xlWWVMckpHQmYzd0taLzdGSksvQ0gxQndCbUNYR1cxMUEyeFlDUXV1VzM2d2NyMC9ZWEl3K1JldzdsY0JyTGpYN2JmQ05nRC81Q3dsYUM3WTJHWm4xRkZmUW9RN3A4bWM3emVndWZEeXNqUnZwSy9xTGpNTTFrWjFZbDYrdmlleHc4YmdRVTJOWXl6bkJ5bWp6U21RUXZXL2ZvS3N4OWsiLCJtYWMiOiJjODllOTk3NzVhY2Q3ZTcxMGY3MmRkMzRmYjQxMGI1MDdiNGJkMjg4NTc5NzY1OWE4Mzg1YzI1ZGU0ZTNhNGU1IiwidGFnIjoiIn0=','eyJpdiI6ImpNKzlQQVpWVklER1VNWWFZVGthUHc9PSIsInZhbHVlIjoiR09SQkpnbDRQNy9vYlFESGsxZlpQdHhQa1BiZnM2YXIvRUdHa2t6SHJtWTRVQXFQcHFLakVTZmRDS3BDQVQ2OXNzMFdORUVKeUt5eEF1S1dXYkdPaWVzU1FxZTNzSFhycWs0MFY1WTg4OUtnVnNBUFZJSmxmMXFGN2hHYXdvajFLNlZycTlOL05jME5peTBRYnF5ZjhRPT0iLCJtYWMiOiJmY2QyNDI2NTNjZTBjYTE3YWM3NTk2YzA1YzViODk2ZmJmOTRkYWNhYzk3MjEyOGZjODI3MzZmYTI3ZTdhZWE4IiwidGFnIjoiIn0=',3599,'2025-05-27 05:05:08',0);
/*!40000 ALTER TABLE `dosens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_bimbingans`
--

DROP TABLE IF EXISTS `jadwal_bimbingans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_bimbingans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('tersedia','tidak_tersedia','penuh') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `kapasitas` int NOT NULL DEFAULT '1',
  `sisa_kapasitas` int NOT NULL DEFAULT '1',
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jumlah_pendaftar` int NOT NULL DEFAULT '0',
  `jenis_bimbingan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_kuota_limit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `jadwal_bimbingans_event_id_unique` (`event_id`),
  KEY `jadwal_bimbingans_nip_index` (`nip`),
  KEY `jadwal_bimbingans_status_index` (`status`),
  KEY `jadwal_bimbingans_waktu_mulai_waktu_selesai_index` (`waktu_mulai`,`waktu_selesai`),
  KEY `jadwal_bimbingans_event_id_index` (`event_id`),
  CONSTRAINT `jadwal_bimbingans_nip_foreign` FOREIGN KEY (`nip`) REFERENCES `dosens` (`nip`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_bimbingans`
--

LOCK TABLES `jadwal_bimbingans` WRITE;
/*!40000 ALTER TABLE `jadwal_bimbingans` DISABLE KEYS */;
INSERT INTO `jadwal_bimbingans` VALUES (1,'vg16uns86arhmcgvbiuhj1ldbc','198501012015041025','2025-04-29 10:39:00','2025-04-29 11:39:00',NULL,'penuh',2,0,NULL,'2025-04-27 13:39:25','2025-04-28 09:46:00',2,'skripsi',1),(2,'g9l1c61e2iorvotc663u5ndp9s','198501012015041025','2025-04-30 15:54:00','2025-04-30 16:54:00',NULL,'tersedia',0,0,NULL,'2025-04-27 13:54:22','2025-04-29 04:54:17',0,NULL,0),(3,'i4cj18vraucbkqbofmg8ofuvhg','198501012015041025','2025-04-30 16:49:00','2025-04-30 18:49:00',NULL,'penuh',2,0,NULL,'2025-04-28 09:50:14','2025-04-28 09:51:19',2,'akademik',1),(4,'5jrp29vbg0vligfdp4pgq9v7ag','198501012015041025','2025-04-29 14:35:00','2025-04-29 15:35:00',NULL,'tersedia',3,2,NULL,'2025-04-29 03:35:55','2025-04-29 04:31:13',2,'mbkm',1),(5,'o5ahssqo1mj1jr3nvc3an57v1o','198501012015041025','2025-05-01 11:55:00','2025-05-01 12:55:00',NULL,'tersedia',0,0,NULL,'2025-04-29 04:55:30','2025-04-29 04:55:30',0,NULL,0),(9,'n8pek42ehrj34s1pkr5uf5eh40','198501012015041025','2025-04-30 12:06:00','2025-04-30 13:06:00',NULL,'tersedia',2,2,NULL,'2025-04-29 11:06:35','2025-04-29 11:07:42',0,'konsultasi',1),(10,'1g8rraa4gu0m7pg7lijt36e2m0','198501012015041025','2025-05-01 14:46:00','2025-05-01 15:47:00',NULL,'tersedia',2,2,NULL,'2025-04-30 07:47:25','2025-05-17 07:54:01',0,'akademik',1),(11,'fjt6pafmdd8coju5tpeu6fhalg','198501012015041025','2025-05-01 16:30:00','2025-05-01 17:30:00',NULL,'tersedia',0,0,NULL,'2025-04-30 08:30:53','2025-04-30 08:30:53',0,NULL,0),(12,'4a4mren43ocf5d1caus9rsl29s','198501012015041025','2025-05-02 10:21:00','2025-05-02 11:21:00',NULL,'tersedia',2,2,NULL,'2025-05-01 12:21:33','2025-05-19 11:02:56',0,'kp',1),(13,'lvan797cuhj06fq1kedek6k21c','198501012015041025','2025-05-02 13:24:00','2025-05-02 14:25:00',NULL,'tersedia',3,1,NULL,'2025-05-01 13:25:30','2025-05-02 04:50:35',2,'konsultasi',1),(14,'q826jibo8khqqeqoeu1s2nf2sc','198501012015041025','2025-05-05 10:19:00','2025-05-05 11:19:00',NULL,'tersedia',3,3,NULL,'2025-05-04 03:20:02','2025-05-19 09:04:17',0,'skripsi',1),(15,'oqjgrltbsk646pdbef5dj4nvu0','198501012015041025','2025-05-07 10:51:00','2025-05-07 11:52:00',NULL,'tersedia',3,3,NULL,'2025-05-06 03:52:16','2025-05-20 09:03:51',0,'konsultasi',1),(21,'43bbsdmn5mc4320l9rhd1oe1i4','198501012015041025','2025-05-07 13:26:00','2025-05-07 14:26:00',NULL,'penuh',2,0,NULL,'2025-05-07 05:26:43','2025-05-22 15:36:01',2,'skripsi',1),(24,'v5eshfrev152p4uak40d3jcotg','198501012015041025','2025-05-08 08:28:00','2025-05-08 09:28:00',NULL,'penuh',1,0,NULL,'2025-05-07 12:28:30','2025-05-19 09:03:32',1,'lainnya',1),(25,'gmkpbjk68jle6skdtdrb69gtt4','198501012015041025','2025-05-08 13:46:00','2025-05-08 14:47:00',NULL,'penuh',3,0,NULL,'2025-05-07 12:47:29','2025-05-19 09:08:50',3,'lainnya',1),(26,'7gtce5r4c1mtosann2qdijnrdc','198501012015041025','2025-05-08 10:02:00','2025-05-08 11:02:00',NULL,'tersedia',2,2,NULL,'2025-05-08 02:02:48','2025-05-19 09:11:53',0,'mbkm',1),(36,'vaatb7oj0sfl8brovu7pkushrg','198501012015041025','2025-05-09 11:36:00','2025-05-09 12:36:00',NULL,'tersedia',2,1,NULL,'2025-05-08 12:37:20','2025-05-19 09:03:24',1,'lainnya',1),(37,'4bjlcf57gvcvg0i2i8qm2lmg3s','198501012015041025','2025-05-09 08:19:00','2025-05-09 09:19:00',NULL,'tersedia',2,1,NULL,'2025-05-08 13:20:01','2025-05-19 09:03:28',1,'kp',1),(38,'9v3f6si0q6e6o379l43bhmp7ik','198501012015041025','2025-05-12 09:57:00','2025-05-12 10:57:00',NULL,'tersedia',3,1,NULL,'2025-05-09 13:58:01','2025-05-22 15:35:53',2,'skripsi',1),(40,'c0incjmsre5vsn1to41om973bs','198501012015041025','2025-05-15 16:03:00','2025-05-15 17:03:00',NULL,'tersedia',0,0,NULL,'2025-05-14 09:03:59','2025-05-22 15:35:48',0,'skripsi',0),(52,'gvdm1l6t5uof72ltrg8un5qjic','198501012015041025','2025-05-19 16:40:00','2025-05-19 17:40:00',NULL,'tersedia',3,1,NULL,'2025-05-18 09:41:12','2025-05-19 12:40:36',2,'lainnya',1),(57,'luhvoumegjhh52t1iitcddjr44','198501012015041025','2025-05-19 13:00:00','2025-05-19 14:00:00',NULL,'tersedia',2,2,NULL,'2025-05-19 05:01:12','2025-05-19 10:53:51',0,'konsultasi',1),(58,'neencha3q6b089aarkrku2jme0','198501012015041025','2025-05-20 08:01:00','2025-05-20 08:40:00',NULL,'penuh',1,0,NULL,'2025-05-19 11:02:36','2025-05-22 15:35:39',1,'akademik',1),(62,'r0nr9sq9a4inb1boi899t0q29k','198501012015041025','2025-05-26 13:43:00','2025-05-26 14:43:00',NULL,'tersedia',3,2,NULL,'2025-05-26 04:43:49','2025-05-26 16:21:39',1,'skripsi',1);
/*!40000 ALTER TABLE `jadwal_bimbingans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konsentrasi`
--

DROP TABLE IF EXISTS `konsentrasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konsentrasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_konsentrasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konsentrasi`
--

LOCK TABLES `konsentrasi` WRITE;
/*!40000 ALTER TABLE `konsentrasi` DISABLE KEYS */;
INSERT INTO `konsentrasi` VALUES (1,'Rekayasa Perangkat Lunak','2025-04-27 20:37:50','2025-04-27 20:37:50'),(2,'Komputer Cerdas & Visualisasi','2025-04-27 20:37:50','2025-04-27 20:37:50'),(3,'Komputer Berbasis Jaringan','2025-04-27 20:37:50','2025-04-27 20:37:50'),(8,'Komputer Berbasis','2025-05-28 16:44:06','2025-05-28 16:44:06');
/*!40000 ALTER TABLE `konsentrasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahasiswas`
--

DROP TABLE IF EXISTS `mahasiswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswas` (
  `nim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `prodi_id` bigint unsigned NOT NULL,
  `konsentrasi_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `google_access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_refresh_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `google_token_expires_in` int DEFAULT NULL,
  `google_token_created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`nim`),
  UNIQUE KEY `mahasiswas_email_unique` (`email`),
  KEY `mahasiswas_prodi_id_foreign` (`prodi_id`),
  KEY `mahasiswas_konsentrasi_id_foreign` (`konsentrasi_id`),
  KEY `mahasiswas_role_id_foreign` (`role_id`),
  CONSTRAINT `mahasiswas_konsentrasi_id_foreign` FOREIGN KEY (`konsentrasi_id`) REFERENCES `konsentrasi` (`id`),
  CONSTRAINT `mahasiswas_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`),
  CONSTRAINT `mahasiswas_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswas`
--

LOCK TABLES `mahasiswas` WRITE;
/*!40000 ALTER TABLE `mahasiswas` DISABLE KEYS */;
INSERT INTO `mahasiswas` VALUES ('2107110255','Syahirah Tri Meilina',2021,'syahirah.tri0255@student.unri.ac.id','$2y$12$p3LdMeQRioljpHQh4KFQTO/qQm1rghdFpTg5UN6ORLqF9u8kMRpmS',NULL,NULL,'2025-04-27 20:37:53','2025-06-01 11:35:31',2,1,2,'eyJpdiI6InoydDVmSDhPblB3RElMYUdRWFZGRFE9PSIsInZhbHVlIjoiT1piK2gybXRZcTV0dTI0SmN2NkZHZ1BPakcxZjNwNWUzeXA1emRSNUQ4aExjTW04YnBOZjgyZGlGVkMvN3V3MGVTWDF1KzBlOE1hT0ZKS1Z3c0FBb1pubzd4by84Qk9pK3dFUzlyT2hORnVDK0V0c0N0b1lScmM3K3VmMjUvS2N1SmR4THVJNnpaM3RUbXlTTUhGbk11MFYwWWtWTWRmM09LZzJjRWdZZGo5MkpiMjlEdGRwcUU1RnVudnFZVFQ2OHoyWjJZL1NKYnl2U1hFeFlSNlNYTmR0NUJCby9aeE80N1FXbUExMXcveG5qR2VpSkU2LzBCVnNEQmFramZoQ0gyTG1PcDdaWTNkUW9xazNUN04wazNvSWxEUlFvdnJVelJvcVZHTmFERUZ3SlZ4NGdsL24xSWR5RHZuZTJ5RXYiLCJtYWMiOiI2MjU3YzQ2OTdkMzQzZWM4ZmEzNDgyMjVlZjFkOWMyNGNlZTBjM2U5MmEwYTQxYmM1YTEyMDFiOGQyNDI0YzZhIiwidGFnIjoiIn0=','eyJpdiI6IlZnK1lBRkZTTmc4Mks4QVRFbFJ2b1E9PSIsInZhbHVlIjoicjVtOEEvZUE4Sis5QjlpNmdIZkIydk9QMUQ5eXJrNFQycnJ3cWFmc2RXUk0vL01jNDE2L2RHYTRXODA0RE1Hc0Z6L25MZDFTVjM2ZVNVcnI4M3VxWkl6T2N0cjhCMGJKVnlJN2NjN2tBUE5OSmV5eVllS0EySDY4SWtDd3lNM0gvTERva0JndFVMTFNvNjd6UWJaT1hBPT0iLCJtYWMiOiI3NjVhYjYzN2FjYjE2ZmI1NTY3ODAxZjY1ZGU2NDA1MjkxYjNiMzVhOTQ1ZTcwYmNiYTczYzA4NzBhNDEzZTVkIiwidGFnIjoiIn0=',3599,'2025-05-28 08:38:20'),('2107110257','Cut Muthia Ramadhani',2021,'cut.muthia0257@student.unri.ac.id','$2y$12$rrN0cAstTaDu5VKPAAHmzuA0GFD5D1J/BF565/fwYOEEm6cHXviau',NULL,NULL,'2025-05-28 11:28:24','2025-05-28 11:28:47',2,1,2,NULL,NULL,NULL,NULL),('2107110665','Desi Maya Sari',2021,'desi.maya0665@student.unri.ac.id','$2y$12$slvZwlkROqnpn./WH7oOyOe0sAXM1vzGmf6FRG4m3DpUPQjBtwqm.',NULL,NULL,'2025-04-27 20:37:52','2025-05-19 18:06:04',2,1,2,'eyJpdiI6IjV4MzVqMVlXajl6TWJhWkk5bFJhakE9PSIsInZhbHVlIjoiczRDQXA0SkdrdFBxTnZwOGxKditxV09aK1V1eTQvT0phbTNHNGIyYk5tYWlLeFV1eXBGejB5UG5STXBWZ2tMcit5NnFRdHBNanJuVjNPRzNrZHlselFEMW03dm5HS3VpclFJcVZ5QllydnNZVVVOZVdlMzJIQ2IyOW9JUEJGNm0zakQ0QVpJVWFJdURxWTN3blRkdElyeHFtVUdHY21teW04NmJmelE3dlh4WDRRb1hSNHgreDBuSVhINTRqYjBDQzZFWXh4dFpXRmdhczNza1ZnQnVpMTA4bUhtckxQS3R5Q3BUVjlIcHMzc0tlTnVCWTFyS0xMQ2ZRNkphcUFCbFFzdmI5bGd5U3NNL05OeTlDa01aNGhELytkNTZ4TXNGTTRaSThYK3phMHNzN2pTaStMbzlobk0zdUhJeWNmUUUiLCJtYWMiOiI5OTI0M2RhYTc4NjIzYjYxYmNkZGZjODI2ZWI1MzdkNDZiOGRiNGY5OWFlZThjZjE1NGM0YWM3NGRiMWQzM2NhIiwidGFnIjoiIn0=','eyJpdiI6IkRHZnpZTC92VFh6Nk05ZlpmaEFPeHc9PSIsInZhbHVlIjoiVWpWQ1czVmh0V3ZZSEFpNjNJT25FdUhNbzlFb3l3TmV1UDRlQ0tTUGY4bHR6R29wK0xYdEl3UWJvK25QQzR0NEdTcklxci9YTVhPQlljSDBRbTJIM2NpMTJwZS9nelAzK0R1MGl6bVNKNnFFaUVmNC9mclNCUnlJOUtZVjg5YjZUV005dmp0V0tpcDBZUjBJajlENXVRPT0iLCJtYWMiOiJhMjRkZDUwNmExNDEyMWE0Yjc5NGJiNjNkOWRmZTgwYTQyYTMxYTM0YTAyYmQ5NWE1ODkwZDlkYTVjNzU4ZDk1IiwidGFnIjoiIn0=',3599,'2025-05-19 11:06:04'),('2107112735','Tri Murniati',2021,'tri.murniati2735@student.unri.ac.id','$2y$12$mxicnuN7NdkK11isRfZK.uj3t2KS.LgJJMcqyaTQOH1fqjc2Z1oJy',NULL,NULL,'2025-04-27 20:37:52','2025-05-28 12:20:00',2,1,2,NULL,NULL,NULL,NULL),('2207135776','Rayhan Al Farassy',2022,'rayhan.al5776@student.unri.ac.id','$2y$12$3avjwdz4XDBtg8xyPbDyo.RCL0Pd7v6q03STEKCQzsvFCfvFX9OyW',NULL,NULL,'2025-04-27 20:37:53','2025-05-19 19:24:14',2,1,2,'eyJpdiI6IlY5WHVkblYwYjFqQWhkbHQ3dFJtL3c9PSIsInZhbHVlIjoiY0dqVGlZMmdqTDdGemRTeFU1WjFYeGtIbit0L0pJcFM1b2JPRkhwT3dFUGlqSTZpUDRQdWpQdlJBdmkvbHRqVUJsaU5Xdk10OVFtRkhSdXdIUmsrNnY4SDFvTTQ1QytDY05kR1Q2d1REdnpaanhYd0xRbEpDSmJBOC9VYTdNNEJRVGxreWFISkZLVVBqbVUwTk1TS0pabUFxa2taL2lSZ2xYbnZ4MktmVUdjbXJoMTVSVXlEZlN3cGdxbmNSeFc1UEVhb0FnNVYzTHdJTStOclhiNzJIRnhjdHgrSkw4aWsyKyt5eFF4V3ozUlV1dkxJOHRMRTRKNlM1Z3o5VFdwRitMeU15TDRaZ1ROWjRUMGFLUmlmcU4vemY4c3JQSWhBUTlFZXlZbHlrSWhBZ2hIZy9UU215QVN3NzF5WmJVUUQiLCJtYWMiOiIxMzBiY2EwODEyOWU1NDJiZmYzZTdkNTVmNTVmYzVjOWU3MWE3MmE4MTlhMTc5N2RiNmI2ZTZmNWJjY2Y5NDUyIiwidGFnIjoiIn0=','eyJpdiI6InNZN0creDFsZnZqU0RHeWk5UjMvaGc9PSIsInZhbHVlIjoiWHlxaktUc1VDNGNIdVlNeEFsWEVmUnJVTkxQeWpsQVk5QTZHUEZsdjh1K1VmM0lueEp4SjRNR1JCbDVScVJCSGRWTW0rZW5Sakg5bmkxSDh0cTY3Q0VTak1oRGdSZVp3R2plQmI3NXRHNDhjdk5WUnNRbTI4bWtZQmZJNWgvMUowcjhxdzJPVzcreStHZHdodGRzYzZ3PT0iLCJtYWMiOiI5ZmYxYmI2ODJlZGRkMGE4MGJjMTA3MzMzMTg4N2M0OTMyMjRkODNjNTViYWIzZWNlYTFmNDkzM2NmYWE3MTM2IiwidGFnIjoiIn0=',3599,'2025-05-19 12:24:14');
/*!40000 ALTER TABLE `mahasiswas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_10_28_122716_create_role_table',1),(5,'2024_10_28_130154_create_prodi_table',1),(6,'2024_10_28_130317_create_konsentrasi_table',1),(7,'2024_10_28_143505_create_dosens_table',1),(8,'2024_10_28_143525_create_mahasiswas_table',1),(9,'2024_11_04_132613_create_jadwal_bimbingans_table',1),(10,'2024_11_05_090404_create_usulan_bimbingans_table',1),(11,'2024_11_12_140112_create_pesans_table',1),(12,'2024_11_14_081225_create_pesan_balasan_table',1),(17,'2025_03_08_150327_add_missing_columns_to_jadwal_bimbingans',2),(18,'2025_03_15_153527_add_dibatalkan_status_to_usulan_bimbingans',2),(19,'2025_03_18_212013_add_is_koordinator_to_dosens_table',2),(20,'2025_03_20_204332_update_jenis_bimbingan_column',2),(21,'2025_04_27_200006_create_booking_bimbingan_table',3),(22,'2025_04_27_200826_add_jumlah_pendaftar_to_jadwal_bimbingans_table',4),(23,'2025_05_16_163432_allow_null_event_id_in_usulan_bimbingans',5),(24,'2025_05_16_171528_fix_usulan_bimbingans_foreign_key',6),(25,'2025_05_20_193930_create_admin_table',7),(26,'2025_05_20_194431_add_admin_role',7),(27,'2025_05_22_213630_create_user_photos_table',8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `pesan`
--

DROP TABLE IF EXISTS `pesan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pesan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mahasiswa_nim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosen_nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subjek` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prioritas` enum('mendesak','umum') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_reply_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_reply_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pesan_mahasiswa_nim_foreign` (`mahasiswa_nim`),
  KEY `pesan_dosen_nip_foreign` (`dosen_nip`),
  CONSTRAINT `pesan_dosen_nip_foreign` FOREIGN KEY (`dosen_nip`) REFERENCES `dosens` (`nip`) ON DELETE CASCADE,
  CONSTRAINT `pesan_mahasiswa_nim_foreign` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswas` (`nim`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesan`
--

LOCK TABLES `pesan` WRITE;
/*!40000 ALTER TABLE `pesan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pesan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesan_balasan`
--

DROP TABLE IF EXISTS `pesan_balasan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pesan_balasan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pesan_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `pengirim_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pesan_balasan_pesan_id_foreign` (`pesan_id`),
  KEY `pesan_balasan_pengirim_id_index` (`pengirim_id`),
  KEY `pesan_balasan_role_id_index` (`role_id`),
  CONSTRAINT `pesan_balasan_pesan_id_foreign` FOREIGN KEY (`pesan_id`) REFERENCES `pesan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pesan_balasan_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesan_balasan`
--

LOCK TABLES `pesan_balasan` WRITE;
/*!40000 ALTER TABLE `pesan_balasan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pesan_balasan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prodi`
--

DROP TABLE IF EXISTS `prodi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_prodi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodi`
--

LOCK TABLES `prodi` WRITE;
/*!40000 ALTER TABLE `prodi` DISABLE KEYS */;
INSERT INTO `prodi` VALUES (1,'Teknik Elektro','2025-04-27 20:37:50','2025-04-27 20:37:50'),(2,'Teknik Informatika','2025-04-27 20:37:50','2025-04-27 20:37:50');
/*!40000 ALTER TABLE `prodi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_akses` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'dosen','2025-04-27 20:37:50','2025-04-27 20:37:50'),(2,'mahasiswa','2025-04-27 20:37:50','2025-04-27 20:37:50'),(3,'koordinator_prodi','2025-04-27 20:37:50','2025-04-27 20:37:50'),(4,'admin','2025-05-20 20:04:15','2025-05-20 20:04:15');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('q3q5K4fZ6ItJNTf7jN59ibsRDJHywkLTBTxjAhTx',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YToxNDp7czo2OiJfdG9rZW4iO3M6NDA6IndWeHBXdjBmd3BYWWlVZ0c0OGN1TWpMeTNlV1lTSFlYWmV0QjZxNG0iO3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWwiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0ODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3VzdWxhbmJpbWJpbmdhbj90YWI9amFkd2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1NjoibG9naW5fbWFoYXNpc3dhXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MTA6IjIxMDcxMTAyNTUiO3M6NDg6ImxvZ2luX21haGFzaXN3YV81Nzg3YmUzOGVlMDNhOWFlNTM2MGY1NGQ5MDI2NDY1ZiI7aToxO3M6NDoicm9sZSI7czo5OiJtYWhhc2lzd2EiO3M6MTA6InJvbGVfYWtzZXMiO3M6OToibWFoYXNpc3dhIjtzOjc6InJvbGVfaWQiO2k6MjtzOjc6InVzZXJfaWQiO047czo5OiJ1c2VyX25hbWUiO3M6MjA6IlN5YWhpcmFoIFRyaSBNZWlsaW5hIjtzOjM6Im5pbSI7czoxMDoiMjEwNzExMDI1NSI7czo0ODoibG9naW5fbWFoYXNpc3dhXzUzYzA1NDEyN2EwMGNkOTgzOTE4M2E2ZjQ4N2MyOWZhIjtpOjE7czoxMDoicGFnZV90b2tlbiI7czo0MDoid1Z4cFd2MGZ3cFhZaVVnRzQ4Y3VNakx5M2VXWVNIWVhaZXRCNnE0bSI7fQ==',1748775175),('tEQ21R6t1enddS7yF8lWtbHYP39yMMvtkRZRebzO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YToxMTp7czo2OiJfdG9rZW4iO3M6NDA6IlNlTlluVVhOeWl6RTRwb09hUUFnaXJ0TG9yVG10SGRGdTROWHJiNEQiO3M6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjt9czo1MjoibG9naW5fYWRtaW5fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDQ6ImxvZ2luX2FkbWluXzIxMjMyZjI5N2E1N2E1YTc0Mzg5NGEwZTRhODAxZmMzIjtpOjE7czo0OiJyb2xlIjtzOjU6ImFkbWluIjtzOjEwOiJyb2xlX2Frc2VzIjtzOjU6ImFkbWluIjtzOjc6InJvbGVfaWQiO2k6NDtzOjc6InVzZXJfaWQiO2k6MTtzOjk6InVzZXJfbmFtZSI7czoxMzoiQWRtaW5pc3RyYXRvciI7czo4OiJ1c2VybmFtZSI7czo1OiJhZG1pbiI7fQ==',1748775181),('yeP9FNwKoxaFmWTDeQ41FgOvOvZwJKp2zXX7DsnZ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36','YToxMzp7czo2OiJfdG9rZW4iO3M6NDA6ImQ5UzBvRlpUT1FMd1gxQVV1aENEc0gybUhSR3pSREtHbDZHZUZXQkMiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcGVyc2V0dWp1YW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUyOiJsb2dpbl9kb3Nlbl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjE4OiIxOTc0MDQyODIwMDIxMjEwMDMiO3M6NDQ6ImxvZ2luX2Rvc2VuX2NlMjhlZWQxNTExZjYzMWFmNmIyYTdiYjBhODVkNjM2IjtpOjE7czo0OiJyb2xlIjtzOjU6ImRvc2VuIjtzOjEwOiJyb2xlX2Frc2VzIjtzOjE3OiJrb29yZGluYXRvcl9wcm9kaSI7czo3OiJyb2xlX2lkIjtpOjM7czo3OiJ1c2VyX2lkIjtOO3M6OToidXNlcl9uYW1lIjtzOjI5OiJGZXJpIENhbmRyYSwgUy5ULiwgTS5ULiwgUGguRCI7czozOiJuaXAiO3M6MTg6IjE5NzQwNDI4MjAwMjEyMTAwMyI7czo0NDoibG9naW5fZG9zZW5fNjU3ODAwMDI1YTJmM2JlNzAyYzExMTZiNDMzZTljY2UiO2k6MTtzOjEwOiJwYWdlX3Rva2VuIjtzOjQwOiJkOVMwb0ZaVE9RTHdYMUFVdWhDRHNIMm1IUkd6UkRLR2w2R2VGV0JDIjt9',1748775178);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_photos`
--

DROP TABLE IF EXISTS `user_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('mahasiswa','dosen','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_base64` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image/jpeg',
  `file_size` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_photos_user_id_user_type_unique` (`user_id`,`user_type`),
  KEY `user_photos_user_type_user_id_index` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_photos`
--

LOCK TABLES `user_photos` WRITE;
/*!40000 ALTER TABLE `user_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('mahasiswa','dosen') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nim_unique` (`nim`),
  UNIQUE KEY `users_nip_unique` (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usulan_bimbingans`
--

DROP TABLE IF EXISTS `usulan_bimbingans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usulan_bimbingans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosen_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mahasiswa_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_bimbingan` enum('skripsi','kp','akademik','konsultasi','mbkm','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nomor_antrian` int DEFAULT NULL,
  `status` enum('USULAN','DITERIMA','DISETUJUI','DITOLAK','SELESAI','DIBATALKAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `event_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_bimbingans_nim_index` (`nim`),
  KEY `usulan_bimbingans_nip_index` (`nip`),
  KEY `usulan_bimbingans_tanggal_waktu_mulai_index` (`tanggal`,`waktu_mulai`),
  KEY `usulan_bimbingans_status_index` (`status`),
  KEY `usulan_bimbingans_event_id_index` (`event_id`),
  CONSTRAINT `usulan_bimbingans_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `jadwal_bimbingans` (`event_id`) ON DELETE SET NULL,
  CONSTRAINT `usulan_bimbingans_nim_foreign` FOREIGN KEY (`nim`) REFERENCES `mahasiswas` (`nim`),
  CONSTRAINT `usulan_bimbingans_nip_foreign` FOREIGN KEY (`nip`) REFERENCES `dosens` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usulan_bimbingans`
--

LOCK TABLES `usulan_bimbingans` WRITE;
/*!40000 ALTER TABLE `usulan_bimbingans` DISABLE KEYS */;
INSERT INTO `usulan_bimbingans` VALUES (1,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-04-29','10:39:00','11:39:00','Lt 2 jurusan',NULL,2,'SELESAI',NULL,'vg16uns86arhmcgvbiuhj1ldbc','2025-04-28 08:25:08','2025-04-30 08:43:00'),(2,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','skripsi','2025-04-29','10:39:00','11:39:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'vg16uns86arhmcgvbiuhj1ldbc','2025-04-28 08:26:43','2025-04-30 08:42:52'),(3,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','skripsi','2025-04-29','10:39:00','11:39:00',NULL,NULL,NULL,'DITOLAK','penuh','vg16uns86arhmcgvbiuhj1ldbc','2025-04-28 08:30:35','2025-04-28 09:10:09'),(4,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','skripsi','2025-04-29','10:39:00','11:39:00',NULL,NULL,NULL,'DITOLAK','penuh','vg16uns86arhmcgvbiuhj1ldbc','2025-04-28 09:11:34','2025-04-28 09:12:26'),(5,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','skripsi','2025-04-29','10:39:00','11:39:00',NULL,NULL,NULL,'DITOLAK','salah','vg16uns86arhmcgvbiuhj1ldbc','2025-04-28 09:14:30','2025-04-28 15:07:12'),(6,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-04-30','16:49:00','18:49:00',NULL,NULL,NULL,'DITOLAK','ada rapat mendadak','i4cj18vraucbkqbofmg8ofuvhg','2025-04-28 09:50:41','2025-04-29 04:25:22'),(7,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','akademik','2025-04-30','16:49:00','18:49:00',NULL,NULL,NULL,'DITOLAK','ada rapat mendadak','i4cj18vraucbkqbofmg8ofuvhg','2025-04-28 09:51:02','2025-04-29 04:21:27'),(8,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','mbkm','2025-04-29','14:35:00','15:35:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat','5jrp29vbg0vligfdp4pgq9v7ag','2025-04-29 04:18:53','2025-05-07 04:56:20'),(9,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','mbkm','2025-04-29','14:35:00','15:35:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat','5jrp29vbg0vligfdp4pgq9v7ag','2025-04-29 04:31:13','2025-05-07 04:56:20'),(10,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-04-30','15:54:00','16:54:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'g9l1c61e2iorvotc663u5ndp9s','2025-04-29 04:54:17','2025-05-08 10:54:58'),(11,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','konsultasi','2025-04-30','12:06:00','13:06:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat mendadak','n8pek42ehrj34s1pkr5uf5eh40','2025-04-29 11:07:42','2025-04-30 12:17:16'),(12,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-05-01','14:46:00','15:47:00',NULL,NULL,NULL,'DITOLAK','c','1g8rraa4gu0m7pg7lijt36e2m0','2025-04-30 07:48:38','2025-05-17 07:54:01'),(13,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','akademik','2025-05-01','14:46:00','15:47:00',NULL,NULL,NULL,'DITOLAK','terlewat','1g8rraa4gu0m7pg7lijt36e2m0','2025-04-30 07:49:56','2025-05-07 04:55:30'),(14,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','kp','2025-05-02','10:21:00','11:21:00',NULL,NULL,NULL,'DITOLAK','sudah telat waktu','4a4mren43ocf5d1caus9rsl29s','2025-05-01 12:22:08','2025-05-19 11:02:56'),(15,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','kp','2025-05-02','10:21:00','11:21:00',NULL,NULL,NULL,'DITOLAK','waktu lewat','4a4mren43ocf5d1caus9rsl29s','2025-05-01 12:24:01','2025-05-17 08:51:44'),(16,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','konsultasi','2025-05-02','13:24:00','14:25:00',NULL,NULL,NULL,'USULAN',NULL,'lvan797cuhj06fq1kedek6k21c','2025-05-02 01:54:23','2025-05-02 01:54:23'),(17,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','konsultasi','2025-05-02','13:24:00','14:25:00',NULL,NULL,NULL,'USULAN',NULL,'lvan797cuhj06fq1kedek6k21c','2025-05-02 04:36:08','2025-05-02 04:36:08'),(18,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','konsultasi','2025-05-07','10:51:00','11:52:00',NULL,NULL,NULL,'DITOLAK','salah','oqjgrltbsk646pdbef5dj4nvu0','2025-05-06 04:28:55','2025-05-06 04:46:58'),(19,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','konsultasi','2025-05-07','10:51:00','11:52:00',NULL,NULL,NULL,'DITOLAK','salah','oqjgrltbsk646pdbef5dj4nvu0','2025-05-06 05:06:58','2025-05-06 05:19:21'),(24,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-05-07','13:26:00','14:26:00',NULL,NULL,NULL,'USULAN',NULL,'43bbsdmn5mc4320l9rhd1oe1i4','2025-05-07 05:27:03','2025-05-07 05:27:03'),(25,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','skripsi','2025-05-07','13:26:00','14:26:00',NULL,NULL,NULL,'USULAN',NULL,'43bbsdmn5mc4320l9rhd1oe1i4','2025-05-07 05:27:47','2025-05-07 05:27:47'),(27,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','lainnya','2025-05-08','08:28:00','09:28:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'v5eshfrev152p4uak40d3jcotg','2025-05-07 12:28:47','2025-05-07 12:44:15'),(28,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','lainnya','2025-05-08','13:46:00','14:47:00',NULL,NULL,NULL,'DITOLAK','ada rapat','gmkpbjk68jle6skdtdrb69gtt4','2025-05-07 12:48:12','2025-05-07 12:49:38'),(29,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','lainnya','2025-05-08','13:46:00','14:47:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'gmkpbjk68jle6skdtdrb69gtt4','2025-05-07 12:59:49','2025-05-16 13:23:30'),(30,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','mbkm','2025-05-08','10:02:00','11:02:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat','7gtce5r4c1mtosann2qdijnrdc','2025-05-08 02:03:48','2025-05-08 11:21:59'),(31,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','lainnya','2025-05-08','13:46:00','14:47:00',NULL,NULL,NULL,'USULAN',NULL,'gmkpbjk68jle6skdtdrb69gtt4','2025-05-08 04:44:24','2025-05-08 04:44:24'),(32,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','lainnya','2025-05-08','13:46:00','14:47:00',NULL,NULL,NULL,'USULAN',NULL,'gmkpbjk68jle6skdtdrb69gtt4','2025-05-08 04:45:18','2025-05-08 04:45:18'),(39,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','lainnya','2025-05-09','11:36:00','12:36:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba','vaatb7oj0sfl8brovu7pkushrg','2025-05-08 12:41:44','2025-05-08 12:43:50'),(40,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','lainnya','2025-05-09','11:36:00','12:36:00',NULL,NULL,NULL,'USULAN',NULL,'vaatb7oj0sfl8brovu7pkushrg','2025-05-08 13:01:01','2025-05-08 13:01:01'),(41,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','lainnya','2025-05-09','11:36:00','12:36:00',NULL,NULL,NULL,'DITOLAK','coba','vaatb7oj0sfl8brovu7pkushrg','2025-05-08 13:02:32','2025-05-08 13:02:59'),(44,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','kp','2025-05-09','08:19:00','09:19:00',NULL,NULL,NULL,'DITOLAK','coba','4bjlcf57gvcvg0i2i8qm2lmg3s','2025-05-08 13:26:12','2025-05-08 13:26:41'),(45,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','kp','2025-05-09','08:19:00','09:19:00',NULL,NULL,NULL,'USULAN',NULL,'4bjlcf57gvcvg0i2i8qm2lmg3s','2025-05-08 13:27:11','2025-05-08 13:27:11'),(46,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-05-12','09:57:00','10:57:00','Lt 2 jurusan',NULL,2,'SELESAI',NULL,'9v3f6si0q6e6o379l43bhmp7ik','2025-05-09 13:59:15','2025-05-16 09:43:31'),(47,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','skripsi','2025-05-12','09:57:00','10:57:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'9v3f6si0q6e6o379l43bhmp7ik','2025-05-09 14:21:03','2025-05-16 10:40:01'),(48,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','kp','2025-05-19','16:40:00','17:40:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-16 09:40:56','2025-05-16 11:46:50'),(50,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','akademik','2025-05-19','09:31:00','10:31:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat',NULL,'2025-05-16 12:45:57','2025-05-16 23:34:35'),(51,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','akademik','2025-05-19','09:31:00','10:31:00','Lt 2 jurusan',NULL,3,'DIBATALKAN','ada rapat',NULL,'2025-05-16 12:49:36','2025-05-16 23:34:35'),(53,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','kp','2025-05-19','13:56:00','14:57:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','COBA',NULL,'2025-05-16 16:29:29','2025-05-16 16:37:33'),(61,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','kp','2025-05-19','13:56:00','14:57:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-16 23:06:13','2025-05-16 23:12:43'),(62,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-05-20','08:07:00','09:07:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-16 23:08:35','2025-05-16 23:31:40'),(63,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-05-19','09:31:00','10:31:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-17 04:38:14','2025-05-18 08:53:20'),(64,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','skripsi','2025-05-20','08:07:00','09:07:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-17 06:03:12','2025-05-18 08:59:13'),(65,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','skripsi','2025-05-20','08:07:00','09:07:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','coba',NULL,'2025-05-17 06:06:25','2025-05-18 08:59:13'),(66,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','konsultasi','2025-05-21','13:07:00','14:08:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat',NULL,'2025-05-17 06:09:13','2025-05-17 06:21:45'),(67,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','konsultasi','2025-05-21','13:07:00','14:08:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat',NULL,'2025-05-17 06:10:11','2025-05-17 06:21:45'),(68,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','mbkm','2025-05-21','15:23:00','16:23:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','test',NULL,'2025-05-17 06:28:41','2025-05-17 06:47:19'),(69,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','kp','2025-05-21','09:49:00','10:50:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-17 06:51:24','2025-05-17 07:31:11'),(70,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-05-21','10:32:00','11:32:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat mendadak',NULL,'2025-05-17 07:33:53','2025-05-18 09:03:26'),(71,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','akademik','2025-05-21','10:32:00','11:32:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat mendadak',NULL,'2025-05-17 08:20:21','2025-05-18 09:03:26'),(72,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','akademik','2025-05-21','10:32:00','11:32:00','Lt 2 jurusan',NULL,3,'DIBATALKAN','ada rapat mendadak',NULL,'2025-05-17 08:24:03','2025-05-18 09:03:26'),(73,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-05-22','15:29:00','16:29:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-18 08:31:39','2025-05-18 08:38:25'),(74,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','lainnya','2025-05-19','16:40:00','17:40:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'gvdm1l6t5uof72ltrg8un5qjic','2025-05-18 09:42:10','2025-05-18 09:55:21'),(77,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','kp','2025-05-20','08:08:00','09:08:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','salah',NULL,'2025-05-18 10:09:16','2025-05-18 10:25:23'),(78,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','kp','2025-05-20','08:08:00','09:08:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','salah',NULL,'2025-05-18 10:09:43','2025-05-18 10:25:23'),(79,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-05-26','17:16:00','18:16:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-18 10:17:51','2025-05-18 10:18:41'),(80,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-05-26','10:37:00','11:37:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat',NULL,'2025-05-18 10:38:41','2025-05-18 10:43:39'),(81,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','akademik','2025-05-26','10:37:00','11:37:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat',NULL,'2025-05-18 10:39:04','2025-05-18 10:43:39'),(82,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','mbkm','2025-05-26','13:18:00','14:18:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','coba',NULL,'2025-05-18 11:19:01','2025-05-18 11:25:35'),(83,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','lainnya','2025-05-19','16:40:00','17:40:00',NULL,NULL,NULL,'USULAN',NULL,'gvdm1l6t5uof72ltrg8un5qjic','2025-05-19 08:21:44','2025-05-19 08:21:44'),(84,'2107110665','198501012015041025','Contoh Dosen 3','Desi Maya Sari','lainnya','2025-05-19','16:40:00','17:40:00',NULL,NULL,NULL,'DITOLAK','coba lagi','gvdm1l6t5uof72ltrg8un5qjic','2025-05-19 08:23:14','2025-05-19 08:47:07'),(85,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','akademik','2025-05-20','08:01:00','08:40:00','Lt 2 jurusan',NULL,1,'SELESAI',NULL,'neencha3q6b089aarkrku2jme0','2025-05-19 11:05:52','2025-05-19 11:28:38'),(87,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-05-20','11:04:00','12:04:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat',NULL,'2025-05-19 11:15:45','2025-05-19 11:51:17'),(88,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','skripsi','2025-05-20','11:04:00','12:04:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat',NULL,'2025-05-19 11:18:14','2025-05-19 11:51:17'),(89,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','lainnya','2025-05-20','14:22:00','15:23:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat',NULL,'2025-05-19 12:24:00','2025-05-19 12:34:03'),(90,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','lainnya','2025-05-20','14:22:00','15:23:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat',NULL,'2025-05-19 12:24:28','2025-05-19 12:35:06'),(91,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','mbkm','2025-05-21','09:40:00','10:40:00','Lt 2 jurusan',NULL,2,'DIBATALKAN','ada rapat mendadak jadi saya batalkan saja yaa',NULL,'2025-05-19 12:41:52','2025-05-19 12:52:13'),(92,'2207135776','198501012015041025','Contoh Dosen 3','Rayhan Al Farassy','mbkm','2025-05-21','09:40:00','10:40:00','Lt 2 jurusan',NULL,1,'DIBATALKAN','ada rapat mendadak jadi saya batalkan saja yaa',NULL,'2025-05-19 12:44:07','2025-05-19 12:52:13'),(93,'2107110255','198501012015041025','Contoh Dosen 3','Syahirah Tri Meilina','skripsi','2025-05-26','13:43:00','14:43:00','Lt 2 jurusan',NULL,1,'DISETUJUI',NULL,'r0nr9sq9a4inb1boi899t0q29k','2025-05-26 04:45:28','2025-05-26 04:45:56');
/*!40000 ALTER TABLE `usulan_bimbingans` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-01 17:53:08
