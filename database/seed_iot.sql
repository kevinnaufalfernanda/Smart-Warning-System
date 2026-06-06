SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `error_logs`;
DROP TABLE IF EXISTS `notification_logs`;
DROP TABLE IF EXISTS `alert_history`;
DROP TABLE IF EXISTS `sensor_logs`;
DROP TABLE IF EXISTS `thresholds`;
DROP TABLE IF EXISTS `devices`;
DROP TABLE IF EXISTS `stations`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tabel Users (Untuk Login Warga/Admin)
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Akun Default (Email: admin@test.com | Password: password)
INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `updated_at`) VALUES
('Admin Warga', 'admin@test.com', '$2y$12$N9H09LzP1PItKx0xX3aGaeVq4vE.Wb8vOq3F1o3yVq3F1o3yVq3F1', NOW(), NOW());

-- 2. Tabel Stations (Stasiun Induk)
CREATE TABLE `stations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stations` (`id`, `name`, `location`, `created_at`, `updated_at`) VALUES
(1, 'Pos Pantau Utama', 'Sungai Brantas Soekarno-Hatta', NOW(), NOW());

-- 3. Tabel Devices (Perangkat ESP32 / Sensor)
CREATE TABLE `devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` bigint(20) unsigned DEFAULT NULL,
  `mac_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `devices_mac_address_unique` (`mac_address`),
  KEY `devices_station_id_foreign` (`station_id`),
  CONSTRAINT `devices_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `stations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mendaftarkan ESP32 milik Anda (Terhubung ke Stasiun 1)
INSERT INTO `devices` (`id`, `station_id`, `mac_address`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'DEFAULT_MAC', 'EWS 1', 1, NOW(), NOW());

-- 4. Tabel Thresholds (Aturan Ambang Batas Air)
CREATE TABLE `thresholds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` bigint(20) unsigned NOT NULL,
  `water_min_cm` double(8,2) NOT NULL,
  `water_max_cm` double(8,2) NOT NULL,
  `level_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thresholds_station_id_foreign` (`station_id`),
  CONSTRAINT `thresholds_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `stations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Menghubungkan batas jarak (0-20 cm = Bahaya, 20-60 cm = Waspada, 60+ cm = Aman) ke Stasiun 1
INSERT INTO `thresholds` (`station_id`, `water_min_cm`, `water_max_cm`, `level_label`, `created_at`, `updated_at`) VALUES
(1, 0.00, 20.00, 'BAHAYA', NOW(), NOW()),
(1, 20.01, 60.00, 'WASPADA', NOW(), NOW()),
(1, 60.01, 400.00, 'AMAN', NOW(), NOW());

-- 5. Tabel Sensor Logs (Riwayat Pembacaan)
CREATE TABLE `sensor_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint(20) unsigned DEFAULT NULL,
  `distance_cm` double(8,2) NOT NULL,
  `water_level_cm` double(8,2) NOT NULL DEFAULT 0.00,
  `rain_intensity_raw` int(11) DEFAULT NULL,
  `flood_condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CERAH',
  `flood_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AMAN',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sensor_logs_device_id_foreign` (`device_id`),
  CONSTRAINT `sensor_logs_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Data Dummy agar Dashboard tidak kosong melompong saat pertama kali dicek
INSERT INTO `sensor_logs` (`device_id`, `distance_cm`, `rain_intensity_raw`, `flood_condition`, `flood_status`, `created_at`) VALUES
(1, 120.00, 4095, 'CERAH', 'AMAN', NOW() - INTERVAL 5 MINUTE),
(1, 45.00, 1200, 'HUJAN', 'WASPADA', NOW() - INTERVAL 2 MINUTE),
(1, 15.00, 800, 'HUJAN', 'BAHAYA', NOW());

-- 6. Tabel Alert History (Riwayat Peringatan Dini)
CREATE TABLE `alert_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` bigint(20) unsigned DEFAULT NULL,
  `alert_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `alert_history_station_id_foreign` (`station_id`),
  CONSTRAINT `alert_history_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `stations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel Notification Logs (Catatan Pengiriman Telegram)
CREATE TABLE `notification_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alert_id` bigint(20) unsigned NOT NULL,
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Telegram',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sent',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `notification_logs_alert_id_foreign` (`alert_id`),
  CONSTRAINT `notification_logs_alert_id_foreign` FOREIGN KEY (`alert_id`) REFERENCES `alert_history` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Error Logs (Catatan Anomali/Error)
CREATE TABLE `error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` bigint(20) unsigned DEFAULT NULL,
  `error_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `error_logs_device_id_foreign` (`device_id`),
  CONSTRAINT `error_logs_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
