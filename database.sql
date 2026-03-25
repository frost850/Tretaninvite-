-- ============================================================
-- TretanInvite — Database Schema (Final State)
-- Generated from all 43 Laravel migrations
-- Import file ini ke phpMyAdmin SEBELUM deploy
-- Dibuat: 2026-03-07
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- Table: migrations  (Laravel internal — jangan dihapus)
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `otp_token` varchar(255) NULL DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `added_by` varchar(255) NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: password_reset_tokens
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: sessions
-- ============================================================
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) NULL DEFAULT NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: cache
-- ============================================================
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: cache_locks
-- ============================================================
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: jobs
-- ============================================================
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: job_batches
-- ============================================================
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext NULL DEFAULT NULL,
  `cancelled_at` int(11) NULL DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: failed_jobs
-- ============================================================
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: weddings
-- ============================================================
CREATE TABLE IF NOT EXISTS `weddings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,

  -- Mempelai wanita / yang berulang tahun / penerima ucapan
  `bride_name` varchar(255) NOT NULL,
  `bride_age` tinyint(3) UNSIGNED NULL DEFAULT NULL,
  `bride_fullname` varchar(150) NULL DEFAULT NULL,
  `bride_father` varchar(100) NULL DEFAULT NULL,
  `bride_mother` varchar(100) NULL DEFAULT NULL,
  `bride_photo` varchar(255) NULL DEFAULT NULL,
  `bride_parent` varchar(255) NULL DEFAULT NULL,
  `bride_bank` varchar(100) NULL DEFAULT NULL,
  `bride_norek` varchar(100) NULL DEFAULT NULL,
  `bride_gender` enum('female','male') NOT NULL DEFAULT 'female',
  `bride_wa` varchar(20) NULL DEFAULT NULL,

  -- Mempelai pria (null untuk non-wedding)
  `groom_name` varchar(255) NULL DEFAULT NULL,
  `groom_fullname` varchar(150) NULL DEFAULT NULL,
  `groom_father` varchar(100) NULL DEFAULT NULL,
  `groom_mother` varchar(100) NULL DEFAULT NULL,
  `groom_photo` varchar(255) NULL DEFAULT NULL,
  `couple_photo` varchar(255) NULL DEFAULT NULL,
  `groom_parent` varchar(255) NULL DEFAULT NULL,
  `groom_bank` varchar(100) NULL DEFAULT NULL,
  `groom_norek` varchar(100) NULL DEFAULT NULL,
  `groom_wa` varchar(20) NULL DEFAULT NULL,

  -- Jadwal acara
  `event_date` date NULL DEFAULT NULL,
  `akad_date` date NULL DEFAULT NULL,
  `akad_time` varchar(50) NULL DEFAULT NULL,
  `akad_location` varchar(255) NULL DEFAULT NULL,
  `reception_date` date NULL DEFAULT NULL,
  `reception_time` varchar(50) NULL DEFAULT NULL,
  `reception_location` varchar(255) NULL DEFAULT NULL,

  -- Lokasi
  `location` varchar(255) NULL DEFAULT NULL,
  `map_link` varchar(255) NULL DEFAULT NULL,
  `map_embed` text NULL DEFAULT NULL,

  -- Konten
  `dresscode` varchar(100) NULL DEFAULT NULL,
  `opening_text` text NULL DEFAULT NULL,
  `custom_texts` json NULL DEFAULT NULL,
  `music_url` varchar(255) NULL DEFAULT NULL,

  -- VIP fields
  `video_url` varchar(500) NULL DEFAULT NULL,
  `cover_photo` varchar(500) NULL DEFAULT NULL,
  `vip_password` varchar(255) NULL DEFAULT NULL,
  `guestbook_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `notify_email` varchar(255) NULL DEFAULT NULL,
  `extra_events` json NULL DEFAULT NULL,

  -- Pengaturan
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `template` varchar(50) NOT NULL DEFAULT 'classic',
  `tracking_token` varchar(32) NOT NULL,
  `has_gallery` tinyint(1) NOT NULL DEFAULT 0,
  `package` enum('trial','basic','premium','vip') NOT NULL DEFAULT 'basic',
  `trial_expires_at` timestamp NULL DEFAULT NULL,
  `expiry_notified_2d_at` timestamp NULL DEFAULT NULL,
  `creator_ip` varchar(45) NULL DEFAULT NULL,

  -- Background section photos (VIP)
  `bg_mempelai_photo` varchar(255) NULL DEFAULT NULL,
  `bg_acara_photo` varchar(255) NULL DEFAULT NULL,
  `bg_lokasi_photo` varchar(255) NULL DEFAULT NULL,

  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `weddings_slug_unique` (`slug`),
  UNIQUE KEY `weddings_tracking_token_unique` (`tracking_token`),
  KEY `weddings_tracking_token_index` (`tracking_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: guests
-- ============================================================
CREATE TABLE IF NOT EXISTS `guests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wedding_id` bigint(20) UNSIGNED NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `group_name` varchar(100) NULL DEFAULT NULL COMMENT 'Grup/keluarga tamu',
  `slug_name` varchar(255) NULL DEFAULT NULL COMMENT 'Kode unik untuk ?to=',
  `phone` varchar(20) NULL DEFAULT NULL,
  `email` varchar(255) NULL DEFAULT NULL,
  `notes` text NULL DEFAULT NULL,
  `is_attending` tinyint(1) NULL DEFAULT NULL COMMENT 'RSVP: true=hadir, false=tidak',
  `replied_at` timestamp NULL DEFAULT NULL,
  `pax` tinyint(3) UNSIGNED NULL DEFAULT NULL COMMENT 'Jumlah orang',
  `first_opened_at` timestamp NULL DEFAULT NULL,
  `open_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_in_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu check-in via scan QR di venue (VIP)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guests_wedding_id_slug_name_index` (`wedding_id`, `slug_name`),
  KEY `guests_wedding_id_guest_name_index` (`wedding_id`, `guest_name`),
  CONSTRAINT `guests_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: orders
-- ============================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_token` varchar(64) NULL DEFAULT NULL,
  `public_token` varchar(32) NULL DEFAULT NULL,
  `template` varchar(255) NOT NULL,
  `package` enum('basic','premium','vip') NOT NULL DEFAULT 'basic',
  `bride_name` varchar(255) NOT NULL,
  `groom_name` varchar(255) NULL DEFAULT NULL,
  `event_date` date NULL DEFAULT NULL,
  `location` varchar(255) NULL DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `customer_email` varchar(255) NULL DEFAULT NULL,
  `notes` text NULL DEFAULT NULL,
  `renewal_days` smallint(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Hari perpanjangan masa aktif, null = pesanan baru',
  `status` enum('baru','diproses','selesai') NOT NULL DEFAULT 'baru',
  `payment_status` enum('belum_bayar','menunggu_konfirmasi','lunas','ditolak') NOT NULL DEFAULT 'belum_bayar',
  `payment_proof` varchar(255) NULL DEFAULT NULL,
  `rejection_reason` varchar(255) NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `wedding_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_payment_token_unique` (`payment_token`),
  UNIQUE KEY `orders_public_token_unique` (`public_token`),
  KEY `orders_public_token_index` (`public_token`),
  KEY `orders_wedding_id_foreign` (`wedding_id`),
  CONSTRAINT `orders_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: wedding_galleries
-- ============================================================
CREATE TABLE IF NOT EXISTS `wedding_galleries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wedding_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `caption` varchar(255) NULL DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wedding_galleries_wedding_id_foreign` (`wedding_id`),
  CONSTRAINT `wedding_galleries_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: guestbook
-- ============================================================
CREATE TABLE IF NOT EXISTS `guestbook` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wedding_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `ip_address` varchar(45) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guestbook_wedding_id_created_at_index` (`wedding_id`, `created_at`),
  CONSTRAINT `guestbook_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: admin_audit_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `actor_email` varchar(255) NOT NULL,
  `actor_type` varchar(20) NOT NULL DEFAULT 'sub_admin',
  `action` varchar(100) NOT NULL,
  `target_type` varchar(100) NULL DEFAULT NULL,
  `target_id` varchar(100) NULL DEFAULT NULL,
  `details` json NULL DEFAULT NULL,
  `ip_address` varchar(45) NULL DEFAULT NULL,
  `user_agent` varchar(512) NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_audit_logs_actor_email_index` (`actor_email`),
  KEY `admin_audit_logs_action_index` (`action`),
  KEY `admin_audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Catat semua migration agar Laravel tidak menjalankan ulang
-- ============================================================
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_02_28_000001_create_weddings_table', 1),
('2025_02_28_000002_create_guests_table', 1),
('2025_02_28_100000_add_template_to_weddings_table', 1),
('2025_02_28_200000_add_guest_fields_rsvp_tracking', 1),
('2025_03_01_000000_add_package_to_weddings_table', 1),
('2025_03_01_000000_add_tracking_token_to_weddings_table', 1),
('2025_03_02_000001_add_package_to_orders_table', 1),
('2026_03_01_000001_add_extra_fields_to_weddings_table', 1),
('2026_03_01_071807_create_orders_table', 1),
('2026_03_01_080054_add_payment_to_orders_table', 1),
('2026_03_01_083601_add_token_expires_to_orders_table', 1),
('2026_03_01_083719_add_token_expires_to_orders_table', 1),
('2026_03_01_100158_add_public_token_to_orders_table', 1),
('2026_03_01_100206_add_public_token_to_orders_table', 1),
('2026_03_01_183045_create_wedding_galleries_table', 1),
('2026_03_01_183112_add_has_gallery_to_weddings_table', 1),
('2026_03_01_184411_add_profile_photos_to_weddings_table', 1),
('2026_03_02_000001_add_group_name_to_guests_table', 1),
('2026_03_02_000001_make_groom_name_nullable_in_weddings_table', 1),
('2026_03_02_185450_add_creator_ip_to_weddings_table', 1),
('2026_03_03_000001_add_section_bg_photos_to_weddings_table', 1),
('2026_03_03_000001_add_vip_fields_to_weddings_table', 1),
('2026_03_03_000001_add_vip_to_orders_package_enum', 1),
('2026_03_03_000002_add_vip_to_weddings_package_enum', 1),
('2026_03_03_000002_create_guestbook_table', 1),
('2026_03_03_123726_add_fullname_fields_to_weddings_table', 1),
('2026_03_04_000001_add_checked_in_at_to_guests_table', 1),
('2026_03_04_000002_add_bride_gender_to_weddings_table', 1),
('2026_03_04_000003_add_wa_to_weddings_table', 1),
('2026_03_04_154355_add_role_to_users_table', 1),
('2026_03_05_000001_create_admin_audit_logs_table', 1),
('2026_03_05_000001_drop_ig_from_weddings_table', 1),
('2026_03_06_000001_add_soft_deletes_to_weddings_and_orders', 1),
('2026_03_06_000002_add_custom_texts_to_weddings_table', 1),
('2026_03_06_000003_add_expiry_notification_fields', 1),
('2026_03_06_000004_refactor_expiry_notification_to_2d', 1),
('2026_03_06_000005_add_user_agent_to_admin_audit_logs', 1),
('2026_03_06_233651_add_ditolak_to_payment_status_enum', 1),
('2026_03_06_234237_add_rejection_reason_to_orders_table', 1),
('2026_03_07_000001_add_renewal_days_to_orders_table', 1);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
