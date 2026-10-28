/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `properties` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_created_at_index` (`created_at`),
  KEY `activity_logs_action_index` (`action`),
  KEY `activity_logs_module_index` (`module`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','published') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  KEY `articles_category_id_foreign` (`category_id`),
  KEY `articles_author_id_foreign` (`author_id`),
  CONSTRAINT `articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asisten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asisten` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` int DEFAULT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usia` int DEFAULT NULL,
  `agama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pernikahan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latar_belakang_ortu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_domisili` text COLLATE utf8mb4_unicode_ci,
  `no_ktp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenjang_pendidikan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `universitas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fakultas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jurusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipk` decimal(4,2) DEFAULT NULL,
  `tahun_lulus` int DEFAULT NULL,
  `no_ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asal_rekrut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jalur_rekrutmen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `batch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periode_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_mulai_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_selesai_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_angkatan` int DEFAULT NULL,
  `nilai_krepelin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_ist` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_disc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_interview` text COLLATE utf8mb4_unicode_ci,
  `disiplin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keaktifan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_trainer` text COLLATE utf8mb4_unicode_ci,
  `nilai_psikotest` decimal(6,2) DEFAULT NULL,
  `nilai_interview_hr` decimal(6,2) DEFAULT NULL,
  `nilai_interview_user` decimal(6,2) DEFAULT NULL,
  `status_mcu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ranking_seleksi` int DEFAULT NULL,
  `nilai_akademik` decimal(6,2) DEFAULT NULL,
  `nilai_praktek` decimal(6,2) DEFAULT NULL,
  `nilai_sikap` decimal(6,2) DEFAULT NULL,
  `nilai_akhir_training` decimal(6,2) DEFAULT NULL,
  `status_kelulusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_kelulusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan_awal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `golongan_awal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pt_penempatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regional_penempatan_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_kerja_penempatan_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik_employee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_sk_awal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_karyawan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `golongan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_saat_ini` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_kerja_saat_ini` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atasan_langsung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pengangkatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_resign` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_resign` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_resign` text COLLATE utf8mb4_unicode_ci,
  `status_ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pengambilan_ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penerima_ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_tanda_terima_ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_bpjs_tk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_bpjs_kes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asisten_program_monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asisten_program_monitoring` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` int DEFAULT NULL,
  `kode_kategori` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori_program` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_rekrut` int DEFAULT NULL,
  `tahun_training` int DEFAULT NULL,
  `tahun_lulus` int DEFAULT NULL,
  `jumlah_trainee` int NOT NULL DEFAULT '0',
  `jumlah_culled` int NOT NULL DEFAULT '0',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asisten_program_monitoring_nama_program_index` (`nama_program`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_class_id` bigint unsigned NOT NULL,
  `attendance_date` date DEFAULT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `status` enum('belum','hadir','sakit','izin','alpha') COLLATE utf8mb4_unicode_ci DEFAULT 'belum',
  `scanned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_passed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_class_trainee_date_unique` (`daily_class_id`,`trainee_id`,`attendance_date`),
  KEY `attendances_trainee_id_foreign` (`trainee_id`),
  CONSTRAINT `attendances_daily_class_id_foreign` FOREIGN KEY (`daily_class_id`) REFERENCES `daily_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_asset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unit',
  `jenis_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `biaya_rekruit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `biaya_rekruit` (
  `id_biaya_rekruit` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun` smallint NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_biaya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jenis_rekruit` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penanggung_jawab` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_biaya_rekruit`),
  KEY `biaya_rekruit_tahun_tanggal_index` (`tahun`,`tanggal`),
  KEY `biaya_rekruit_tahun_index` (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `capex_rab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capex_rab` (
  `rab_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun` smallint unsigned NOT NULL DEFAULT '2026',
  `lokasi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kantor',
  `kel_barang_kapital` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_unit` decimal(12,2) DEFAULT '0.00',
  `harga_satuan` decimal(15,2) DEFAULT '0.00',
  `total_rp` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`rab_id`),
  KEY `capex_rab_tahun_lokasi_index` (`tahun`,`lokasi`),
  KEY `capex_rab_tahun_index` (`tahun`),
  KEY `capex_rab_lokasi_index` (`lokasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `capex_realisasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `capex_realisasi` (
  `realisasi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rab_id` bigint unsigned DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2026',
  `lokasi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kantor',
  `kel_barang_kapital` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kredit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_rp` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`realisasi_id`),
  KEY `capex_realisasi_tahun_lokasi_index` (`tahun`,`lokasi`),
  KEY `capex_realisasi_rab_id_index` (`rab_id`),
  KEY `capex_realisasi_tahun_index` (`tahun`),
  KEY `capex_realisasi_lokasi_index` (`lokasi`),
  KEY `capex_realisasi_tanggal_index` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `career_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `career_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `qualifications` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `gform_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `career_programs_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `competencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dictionary_id` bigint unsigned NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `definition` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `competencies_dictionary_id_sort_order_index` (`dictionary_id`,`sort_order`),
  CONSTRAINT `competencies_dictionary_id_foreign` FOREIGN KEY (`dictionary_id`) REFERENCES `competency_dictionaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `competency_dictionaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competency_dictionaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` smallint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competency_dictionaries_code_year_unique` (`code`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `competency_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competency_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `competency_id` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `competency_items_competency_id_type_sort_order_index` (`competency_id`,`type`,`sort_order`),
  CONSTRAINT `competency_items_competency_id_foreign` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `competency_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competency_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `competency_id` bigint unsigned NOT NULL,
  `position_id` bigint unsigned NOT NULL,
  `level` tinyint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competency_levels_competency_id_position_id_level_unique` (`competency_id`,`position_id`,`level`),
  KEY `competency_levels_position_id_foreign` (`position_id`),
  CONSTRAINT `competency_levels_competency_id_foreign` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `competency_levels_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `competency_positions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `competency_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `competency_positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dictionary_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competency_positions_dictionary_id_name_unique` (`dictionary_id`,`name`),
  CONSTRAINT `competency_positions_dictionary_id_foreign` FOREIGN KEY (`dictionary_id`) REFERENCES `competency_dictionaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `costs` (
  `cost_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `GL_ACCOUNT` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_cost` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2026',
  `bulan` tinyint unsigned DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_item` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget` decimal(20,2) DEFAULT '0.00',
  `realisasi` decimal(20,2) DEFAULT '0.00',
  `budget_tahunan` decimal(20,2) DEFAULT '0.00',
  `tanggal` date DEFAULT NULL,
  `no_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cuti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuti` (
  `cuti_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_jabatan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmk` date DEFAULT NULL,
  `tahun` int NOT NULL,
  `hak_cuti` int NOT NULL DEFAULT '12',
  `sisa_hak_cuti_carryover` int NOT NULL DEFAULT '0',
  `cuti_diambil` int NOT NULL DEFAULT '0',
  `jan` int NOT NULL DEFAULT '0',
  `feb` int NOT NULL DEFAULT '0',
  `mar` int NOT NULL DEFAULT '0',
  `apr` int NOT NULL DEFAULT '0',
  `mei` int NOT NULL DEFAULT '0',
  `jun` int NOT NULL DEFAULT '0',
  `jul` int NOT NULL DEFAULT '0',
  `agu` int NOT NULL DEFAULT '0',
  `sep` int NOT NULL DEFAULT '0',
  `okt` int NOT NULL DEFAULT '0',
  `nov` int NOT NULL DEFAULT '0',
  `des` int NOT NULL DEFAULT '0',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cuti_id`),
  UNIQUE KEY `cuti_nik_tahun_unique` (`nik`,`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_program_id` bigint unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trainer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_evaluation_open` tinyint(1) NOT NULL DEFAULT '0',
  `qr_code_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `material_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mess_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_signer_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_signer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_description` text COLLATE utf8mb4_unicode_ci,
  `cert_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_classes_qr_code_token_unique` (`qr_code_token`),
  KEY `daily_classes_training_program_id_foreign` (`training_program_id`),
  CONSTRAINT `daily_classes_training_program_id_foreign` FOREIGN KEY (`training_program_id`) REFERENCES `training_programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `homepage_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homepage_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `homepage_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homepage_stats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mahasiswa` (
  `alamat_mhs` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `moas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `moas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_program_id` bigint unsigned NOT NULL,
  `trainer_id` bigint unsigned NOT NULL,
  `week_start_date` date NOT NULL,
  `week_end_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moas_training_program_id_foreign` (`training_program_id`),
  KEY `moas_trainer_id_foreign` (`trainer_id`),
  CONSTRAINT `moas_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `moas_training_program_id_foreign` FOREIGN KEY (`training_program_id`) REFERENCES `training_programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monitoring_afdeling`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_afdeling` (
  `afeliding_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`afeliding_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monitoring_angkatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_angkatan` (
  `id_angkatan` bigint unsigned NOT NULL AUTO_INCREMENT,
  `angkatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_awal` int NOT NULL DEFAULT '0',
  `lulus` int NOT NULL DEFAULT '0',
  `resign_in_class` int NOT NULL DEFAULT '0',
  `resign_ojt` int NOT NULL DEFAULT '0',
  `tidak_lulus` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_angkatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monitoring_culled`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_culled` (
  `id_culled` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_angkatan` bigint unsigned DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci,
  `kategori_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Culled',
  `jenis_culled` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_culled`),
  KEY `monitoring_culled_id_angkatan_foreign` (`id_angkatan`),
  CONSTRAINT `monitoring_culled_id_angkatan_foreign` FOREIGN KEY (`id_angkatan`) REFERENCES `monitoring_angkatan` (`id_angkatan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monitoring_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_employees` (
  `id_monitoring` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `angkatan_fr_academy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alumni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_terminate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bulan_terminate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_terminate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lama_bekerja_bulan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lama_bekerja_tahun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lama_bekerja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_resign` text COLLATE utf8mb4_unicode_ci,
  `detail` text COLLATE utf8mb4_unicode_ci,
  `lokasi_awal_penempatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_awal_penempatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gm` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_monitoring`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `monitoring_talents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_talents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` int DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posisi_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `talent_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mentor_lapangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mentor_frlc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `periode_penugasan_lama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci,
  `periode_penugasan_baru` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_penugasan_baru` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_tambahan` text COLLATE utf8mb4_unicode_ci,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `monitoring_talents_employee_id_index` (`employee_id`),
  KEY `monitoring_talents_employee_name_index` (`employee_name`),
  KEY `monitoring_talents_unit_index` (`unit`),
  KEY `monitoring_talents_region_index` (`region`),
  KEY `monitoring_talents_status_index` (`status`),
  KEY `monitoring_talents_kategori_index` (`kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mpp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mpp` (
  `mpp_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jabatan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL DEFAULT '0',
  `tahun` int NOT NULL,
  `bulan` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mpp_id`),
  UNIQUE KEY `mpp_nama_jabatan_jenis_jabatan_tahun_bulan_unique` (`nama_jabatan`,`jenis_jabatan`,`tahun`,`bulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ojt_placements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ojt_placements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_session_id` bigint unsigned NOT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `location_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ojt_placements_training_session_id_foreign` (`training_session_id`),
  KEY `ojt_placements_trainee_id_foreign` (`trainee_id`),
  CONSTRAINT `ojt_placements_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ojt_placements_training_session_id_foreign` FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembelian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_id` bigint unsigned NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `lokasi_spesifik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembelian_barang_id_foreign` (`barang_id`),
  CONSTRAINT `pembelian_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profil_trainees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil_trainees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trainee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_trainee` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asal_universitas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jurusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usia` int DEFAULT NULL,
  `suku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tinggi_badan` decimal(5,1) DEFAULT NULL,
  `berat_badan` decimal(5,1) DEFAULT NULL,
  `daerah_kabupaten_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota_provinsi_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_lulus` int DEFAULT NULL,
  `masa_studi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipk` decimal(3,2) DEFAULT NULL,
  `pengalaman_organisasi` text COLLATE utf8mb4_unicode_ci,
  `pengalaman_praktek_kerja_magang` text COLLATE utf8mb4_unicode_ci,
  `prestasi_diri` text COLLATE utf8mb4_unicode_ci,
  `hobi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riwayat_kesehatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_riwayat_kesehatan` text COLLATE utf8mb4_unicode_ci,
  `pekerjaan_orangtua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profil_trainees_trainee_id_index` (`trainee_id`),
  KEY `profil_trainees_nama_trainee_index` (`nama_trainee`),
  KEY `profil_trainees_nama_jabatan_index` (`nama_jabatan`),
  KEY `profil_trainees_asal_universitas_index` (`asal_universitas`),
  KEY `profil_trainees_jurusan_index` (`jurusan`),
  KEY `profil_trainees_jenis_kelamin_index` (`jenis_kelamin`),
  KEY `profil_trainees_pendidikan_terakhir_index` (`pendidikan_terakhir`),
  KEY `profil_trainees_daerah_kabupaten_asal_index` (`daerah_kabupaten_asal`),
  KEY `profil_trainees_kota_provinsi_asal_index` (`kota_provinsi_asal`),
  KEY `profil_trainees_tahun_lulus_index` (`tahun_lulus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `program_non_reguler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_non_reguler` (
  `id_nonreguler` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trainer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_jam` int unsigned DEFAULT NULL,
  `total_hari` int unsigned DEFAULT NULL,
  `peserta_non_staf` int unsigned NOT NULL DEFAULT '0',
  `peserta_ast` int unsigned NOT NULL DEFAULT '0',
  `peserta_askep` int unsigned NOT NULL DEFAULT '0',
  `peserta_mgr` int unsigned NOT NULL DEFAULT '0',
  `peserta_gm` int unsigned NOT NULL DEFAULT '0',
  `peserta_head` int unsigned NOT NULL DEFAULT '0',
  `target_staf` int unsigned NOT NULL DEFAULT '0',
  `target_non_staf` int unsigned NOT NULL DEFAULT '0',
  `target_total` int unsigned NOT NULL DEFAULT '0',
  `training_staf` int unsigned NOT NULL DEFAULT '0',
  `training_non_staf` int unsigned NOT NULL DEFAULT '0',
  `training_total` int unsigned NOT NULL DEFAULT '0',
  `td_staf` int unsigned NOT NULL DEFAULT '0',
  `td_non_staf` int unsigned NOT NULL DEFAULT '0',
  `td_total` int unsigned NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `realisasi_batch` int unsigned NOT NULL DEFAULT '0',
  `lokasi_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `sub_kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pelaksanaan` json DEFAULT NULL,
  `TARGET_BATCH` int unsigned DEFAULT NULL,
  `TARGET_PESERTA` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  PRIMARY KEY (`id_nonreguler`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `program_reguler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `program_reguler` (
  `Program_reguler_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `Kategori_Program` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Training_Program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Batch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Lokasi_training` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Mulai_program` date DEFAULT NULL,
  `Selesai_program` date DEFAULT NULL,
  `jumlah_bulan_training` int unsigned DEFAULT NULL,
  `jumlah_hari` int unsigned DEFAULT NULL,
  `Total_training_days` int unsigned DEFAULT NULL,
  `Promosi_mandor` int unsigned NOT NULL DEFAULT '0',
  `Fresh_Graduate` int unsigned NOT NULL DEFAULT '0',
  `target_peserta` int unsigned NOT NULL DEFAULT '0',
  `Uang_saku` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jenis_program_reguler` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pelaksanaan` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `kelas` int DEFAULT NULL,
  `resign` int DEFAULT NULL,
  `staff` int DEFAULT NULL,
  `lulus` int DEFAULT NULL,
  PRIMARY KEY (`Program_reguler_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscribable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subscribable_id` bigint unsigned NOT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `public_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_encoding` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  KEY `push_subscriptions_subscribable_type_subscribable_id_index` (`subscribable_type`,`subscribable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quiz_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `weekly_report_id` bigint unsigned NOT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `materi_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_scores_weekly_report_id_foreign` (`weekly_report_id`),
  KEY `quiz_scores_trainee_id_foreign` (`trainee_id`),
  CONSTRAINT `quiz_scores_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_scores_weekly_report_id_foreign` FOREIGN KEY (`weekly_report_id`) REFERENCES `weekly_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rankings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rankings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `weekly_report_id` bigint unsigned NOT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `average_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `rank` int NOT NULL,
  `rank_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remedial_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rankings_weekly_report_id_foreign` (`weekly_report_id`),
  KEY `rankings_trainee_id_foreign` (`trainee_id`),
  CONSTRAINT `rankings_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rankings_weekly_report_id_foreign` FOREIGN KEY (`weekly_report_id`) REFERENCES `weekly_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `realisasi_vs_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `realisasi_vs_batch` (
  `realisasi_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_id` bigint unsigned NOT NULL,
  `tahun` smallint unsigned NOT NULL,
  `bulan` tinyint unsigned NOT NULL,
  `legacy_realized_batch` int unsigned DEFAULT NULL,
  `legacy_realized_participants` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`realisasi_id`),
  UNIQUE KEY `realisasi_program_period_unique` (`program_type`,`program_id`,`tahun`,`bulan`),
  KEY `realisasi_vs_batch_tahun_bulan_program_type_index` (`tahun`,`bulan`,`program_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rekrutmen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rekrutmen` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` int DEFAULT NULL,
  `lokasi_rekrut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode_rekrutmen` enum('Online','Offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Offline',
  `program` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `jumlah_peserta_hadir` int NOT NULL DEFAULT '0',
  `jumlah_lolos_psikotest` int NOT NULL DEFAULT '0',
  `jumlah_hadir_interview_awal` int NOT NULL DEFAULT '0',
  `jumlah_lolos_interview_awal` int NOT NULL DEFAULT '0',
  `jumlah_lolos_interview_user` int NOT NULL DEFAULT '0',
  `jumlah_ikut_mcu` int NOT NULL DEFAULT '0',
  `jumlah_lolos_mcu` int NOT NULL DEFAULT '0',
  `join_fr` int NOT NULL DEFAULT '0',
  `persen_lulus` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resignations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resignations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `weekly_report_id` bigint unsigned DEFAULT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `avg_inclass_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `inclass_rank` int NOT NULL DEFAULT '0',
  `avg_practice_score` decimal(8,2) NOT NULL DEFAULT '0.00',
  `practice_rank` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resignations_trainee_id_foreign` (`trainee_id`),
  KEY `resignations_weekly_report_id_foreign` (`weekly_report_id`),
  CONSTRAINT `resignations_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resignations_weekly_report_id_foreign` FOREIGN KEY (`weekly_report_id`) REFERENCES `weekly_reports` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resigned_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resigned_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_date` date DEFAULT NULL,
  `resign_date` date DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kebun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_resign` text COLLATE utf8mb4_unicode_ci,
  `jenis_resign` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cluster_resign` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alumni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resigned_staff_employee_id_index` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resigned_trainees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resigned_trainees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `session_evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_evaluations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_session_id` bigint unsigned NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `materi_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `trainer_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fasilitas_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_evaluations_training_session_id_foreign` (`training_session_id`),
  CONSTRAINT `session_evaluations_training_session_id_foreign` FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `session_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_session_id` bigint unsigned NOT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `participation_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_participants_training_session_id_foreign` (`training_session_id`),
  KEY `session_participants_trainee_id_foreign` (`trainee_id`),
  CONSTRAINT `session_participants_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `session_participants_training_session_id_foreign` FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `session_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_session_id` bigint unsigned NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dokumentasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_photos_training_session_id_foreign` (`training_session_id`),
  CONSTRAINT `session_photos_training_session_id_foreign` FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_opname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_opname` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_id` bigint unsigned NOT NULL,
  `tanggal_stock_opname` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Selesai',
  `catatan_sesi` text COLLATE utf8mb4_unicode_ci,
  `lokasi_spesifik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_baik` int NOT NULL DEFAULT '0',
  `jumlah_rusak` int NOT NULL DEFAULT '0',
  `grand_total` int NOT NULL DEFAULT '0',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `foto_kerusakan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_opname_barang_id_foreign` (`barang_id`),
  CONSTRAINT `stock_opname_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `talent_pools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `talent_pools` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no` int DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posisi_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `talent_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mentor_lapangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mentor_frlc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Penempatan',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `lokasi_penempatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_penempatan` date DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `jenis_kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `talent_pools_employee_id_index` (`employee_id`),
  KEY `talent_pools_employee_name_index` (`employee_name`),
  KEY `talent_pools_unit_index` (`unit`),
  KEY `talent_pools_region_index` (`region`),
  KEY `talent_pools_status_index` (`status`),
  KEY `talent_pools_kategori_index` (`kategori`),
  KEY `talent_pools_tahun_index` (`tahun`),
  KEY `talent_pools_jenis_kategori_index` (`jenis_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `trainee_id` bigint unsigned NOT NULL,
  `file_path` text COLLATE utf8mb4_unicode_ci,
  `status` enum('belum','sudah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_submissions_task_id_trainee_id_unique` (`task_id`,`trainee_id`),
  KEY `task_submissions_trainee_id_foreign` (`trainee_id`),
  CONSTRAINT `task_submissions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_submissions_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_class_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deadline` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_daily_class_id_foreign` (`daily_class_id`),
  CONSTRAINT `tasks_daily_class_id_foreign` FOREIGN KEY (`daily_class_id`) REFERENCES `daily_classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trainee_evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainee_evaluations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trainee_id` bigint unsigned NOT NULL,
  `daily_class_id` bigint unsigned NOT NULL,
  `answers` json NOT NULL,
  `explanation_de` text COLLATE utf8mb4_unicode_ci,
  `kesan_pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trainee_evaluations_trainee_id_daily_class_id_unique` (`trainee_id`,`daily_class_id`),
  KEY `trainee_evaluations_daily_class_id_foreign` (`daily_class_id`),
  CONSTRAINT `trainee_evaluations_daily_class_id_foreign` FOREIGN KEY (`daily_class_id`) REFERENCES `daily_classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trainee_evaluations_trainee_id_foreign` FOREIGN KEY (`trainee_id`) REFERENCES `trainees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trainees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `talent_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `university` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `major` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `training_program_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ojt_company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ojt_region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ojt_location_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trainees_access_token_unique` (`access_token`),
  KEY `trainees_training_program_id_foreign` (`training_program_id`),
  CONSTRAINT `trainees_training_program_id_foreign` FOREIGN KEY (`training_program_id`) REFERENCES `training_programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trainers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Tidak Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trainers_user_id_foreign` (`user_id`),
  CONSTRAINT `trainers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_programs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quote` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `program_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_id` bigint unsigned DEFAULT NULL,
  `participant_identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `participant_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `internal_external` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bulan` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun` smallint unsigned DEFAULT NULL,
  `jml_hari` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_jam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `training_date` date DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'valid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nama_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_pelatihan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reg_non_reg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_records_program_type_program_id_index` (`program_type`,`program_id`),
  KEY `training_records_training_date_status_index` (`training_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `weekly_report_id` bigint unsigned NOT NULL,
  `session_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_right_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FR Academy',
  `trainer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trainer_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `program_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `training_purpose` text COLLATE utf8mb4_unicode_ci,
  `training_result` text COLLATE utf8mb4_unicode_ci,
  `results_or_impressions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_sessions_weekly_report_id_foreign` (`weekly_report_id`),
  CONSTRAINT `training_sessions_weekly_report_id_foreign` FOREIGN KEY (`weekly_report_id`) REFERENCES `weekly_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tv_playlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tv_playlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint unsigned NOT NULL,
  `duration_seconds` int NOT NULL DEFAULT '15',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tv_playlists_article_id_foreign` (`article_id`),
  CONSTRAINT `tv_playlists_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ukm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ukm` (
  `sejak` char(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nik_unique` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vra` (
  `vra_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_polisi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kendaraan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` smallint unsigned NOT NULL DEFAULT '2026',
  `bulan` tinyint unsigned NOT NULL COMMENT '1-12',
  `upah` decimal(15,2) NOT NULL DEFAULT '0.00',
  `BBM` decimal(15,2) NOT NULL DEFAULT '0.00',
  `BBM_tahun` decimal(15,2) NOT NULL DEFAULT '0.00',
  `preventif_maintenance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `biaya_lainnya` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_biaya_bulan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `KM_awal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `KM_akhir` decimal(12,2) NOT NULL DEFAULT '0.00',
  `KM` decimal(12,2) NOT NULL DEFAULT '0.00',
  `BBM_liter` decimal(12,2) NOT NULL DEFAULT '0.00',
  `BBM_KM_liter` decimal(12,2) NOT NULL DEFAULT '0.00',
  `VRA` decimal(15,2) NOT NULL DEFAULT '0.00',
  `budget_tahun` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`vra_id`),
  KEY `vra_tahun_bulan_nomor_polisi_index` (`tahun`,`bulan`,`nomor_polisi`),
  KEY `vra_tahun_index` (`tahun`),
  KEY `vra_bulan_index` (`bulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `weekly_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weekly_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_program_id` bigint unsigned NOT NULL,
  `trainer_id` bigint unsigned NOT NULL,
  `week_start_date` date NOT NULL,
  `week_end_date` date NOT NULL,
  `training_purpose` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quote` text COLLATE utf8mb4_unicode_ci,
  `quiz_notes` text COLLATE utf8mb4_unicode_ci,
  `cover_photo_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo_4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo_5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo_6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo_main` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reguler',
  `is_merged` tinyint(1) NOT NULL DEFAULT '0',
  `parent_report_id` bigint unsigned DEFAULT NULL,
  `merged_program_names` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `training_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'FR Academy',
  `feedback_notes` text COLLATE utf8mb4_unicode_ci,
  `eval_materi_score` int DEFAULT NULL,
  `eval_trainer_score` int DEFAULT NULL,
  `eval_fasilitas_score` int DEFAULT NULL,
  `strip_photos` json DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weekly_reports_training_program_id_foreign` (`training_program_id`),
  KEY `weekly_reports_trainer_id_foreign` (`trainer_id`),
  CONSTRAINT `weekly_reports_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `weekly_reports_training_program_id_foreign` FOREIGN KEY (`training_program_id`) REFERENCES `training_programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_09_01_031004_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_09_01_031005_create_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_09_01_031006_create_articles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_09_01_031007_create_homepage_stats_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_09_01_031008_create_testimonials_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_09_01_031009_create_tv_playlists_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_09_02_000000_create_vra_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_09_02_000001_create_biaya_rekruit_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_09_02_000002_create_capex_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_09_02_000003_create_costs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_09_02_021734_create_homepage_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_09_02_034328_create_cuti_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_09_02_034748_create_mpp_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_09_02_100000_create_inventaris_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_09_04_000000_create_program_batch_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_09_04_010000_create_training_records_and_realisasi_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_09_08_050000_create_rekrutmen_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_09_08_080000_create_asisten_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_09_08_090000_create_asisten_program_monitoring_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_09_09_000000_create_monitoring_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_09_09_035037_create_training_programs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_09_09_035038_create_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_09_09_035039_create_trainers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_09_09_035040_create_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_09_09_035041_create_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_09_09_035042_create_quiz_scores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_09_09_035043_create_report_participants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_09_09_035044_create_moas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_09_09_035045_create_report_photos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_09_09_080012_add_advanced_fields_to_weekly_reports_and_trainees',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_09_09_084516_add_detail_fields_to_trainers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_09_09_215020_refactor_training_reguler_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_09_09_230500_add_trainer_photo_path_to_training_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_09_09_234014_add_merged_fields_to_reports_and_sessions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_09_10_000001_create_monitoring_talents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_09_10_073611_add_training_result_to_training_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_09_10_075543_add_cover_photos_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_09_10_092727_add_strip_photos_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_09_11_033622_add_quote_to_training_programs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_09_11_080718_create_resigned_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_09_11_081407_add_weekly_report_id_to_resignations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_09_11_083453_add_remedial_note_to_rankings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_09_12_181847_add_cover_photo_5_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_09_12_182909_add_cover_photo_6_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_09_15_000000_add_tahun_to_program_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_09_15_023941_add_banner_right_text_to_training_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_09_15_030855_add_type_to_session_photos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_09_15_074147_add_soft_deletes_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_09_16_080444_add_theme_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_09_17_000000_create_app_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_09_17_000001_create_activity_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_09_17_025936_add_ojt_fields_to_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_09_17_033716_drop_ojt_start_date_from_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_09_17_045206_add_quiz_notes_to_weekly_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_09_20_140016_add_nik_to_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_09_20_142735_create_daily_classes_and_related_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_09_20_225757_alter_status_in_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_09_20_232417_change_file_path_to_text_in_task_submissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_09_21_000000_rename_total_batch_to_realisasi_batch',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_09_21_000001_add_extra_fields_to_asisten_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_09_21_031859_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_09_21_055745_alter_status_in_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_09_22_041229_create_career_programs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_09_22_082806_create_trainee_evaluations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_09_22_090925_add_is_evaluation_open_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_09_23_084718_add_non_reguler_fields_to_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_09_23_095851_add_end_date_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_09_23_095858_add_attendance_date_to_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_09_23_113719_add_material_and_mess_urls_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_09_23_113720_add_access_token_to_trainees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_09_23_124635_make_date_and_trainer_nullable_on_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_09_23_143554_update_unique_constraint_on_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_09_23_203307_add_room_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_09_23_211746_add_training_name_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_09_25_090324_create_push_subscriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_09_25_103932_add_details_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_09_27_202946_add_is_passed_to_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_09_27_212451_add_certificate_fields_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_09_28_053247_add_certificate_link_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_10_02_151622_add_location_to_daily_classes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_10_02_154834_fix_old_training_record_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_10_03_000001_add_department_and_total_jam_to_training_records_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_10_03_063007_create_resigned_staff_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_10_07_120000_create_competency_tables',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_10_07_120100_add_manage_competency_permission',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_10_08_091701_add_imported_fields_to_training_records_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_10_16_000000_add_kelas_resign_staff_to_program_reguler_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_10_16_153336_add_image_to_career_programs_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_10_25_000001_create_talent_pools_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_10_26_000001_create_profil_trainees_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_10_28_000001_add_tahun_and_jenis_kategori_to_talent_pools_table',7);
