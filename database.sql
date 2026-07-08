-- =====================================================================
-- Sistem Manajemen Inventaris - PT Telkomsel
-- Database Schema (MySQL 8.x / MariaDB 10.x)
--
-- File ini berisi struktur tabel yang identik dengan hasil
-- `php artisan migrate` + data awal yang identik dengan `php artisan db:seed`
-- (8 kategori, 52 barang, 90 riwayat peminjaman dengan variasi status
-- dipinjam/terlambat/dikembalikan, tersebar sepanjang 12+ bulan terakhir).
--
-- File ini sudah diuji dengan cara diimpor langsung ke instance MariaDB
-- (CREATE TABLE + seluruh INSERT tervalidasi tanpa error, foreign key aktif).
--
-- Cara pakai (opsional, jika tidak ingin menjalankan migration Laravel):
--   mysql -u root -p -e "CREATE DATABASE inventaris_telkomsel CHARACTER SET utf8mb4"
--   mysql -u root -p inventaris_telkomsel < database.sql
--
-- Catatan: cara yang direkomendasikan tetap menjalankan
--   php artisan migrate --seed
-- karena lebih terjamin konsisten dengan versi Laravel yang terpasang,
-- dan tanggal peminjaman akan dihitung otomatis relatif terhadap hari itu.
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- Tabel: roles
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `label`, `created_at`, `updated_at`) VALUES
(1, 'admin',   'Administrator', NOW(), NOW()),
(2, 'staff',   'Staff Gudang',  NOW(), NOW()),
(3, 'manager', 'Manager',       NOW(), NOW());

-- ---------------------------------------------------------------------
-- Tabel: users
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password untuk seluruh akun demo di bawah ini adalah: password
-- (hash bcrypt standar Laravel)
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `is_active`, `password`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin Inventaris',    'admin@telkomsel.test',   NULL, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
(2, 2, 'Staff Gudang',        'staff@telkomsel.test',   NULL, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
(3, 3, 'Manager Operasional', 'manager@telkomsel.test', NULL, 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- ---------------------------------------------------------------------
-- Tabel: password_reset_tokens (dibutuhkan fitur Forgot Password)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: sessions (driver session = database)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: categories
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Elektronik', 'Perangkat elektronik kantor: laptop, monitor, printer, kamera, dsb.', NOW(), NOW()),
(2, 'Furniture', 'Perabotan kantor: kursi, meja, lemari, sofa, dsb.', NOW(), NOW()),
(3, 'ATK', 'Alat Tulis Kantor & perlengkapan administrasi', NOW(), NOW()),
(4, 'Jaringan', 'Perangkat jaringan & infrastruktur telekomunikasi', NOW(), NOW()),
(5, 'Kendaraan Operasional', 'Kendaraan dinas & operasional kantor', NOW(), NOW()),
(6, 'Audio Visual', 'Perangkat audio, video, dan multimedia untuk presentasi/meeting', NOW(), NOW()),
(7, 'Peralatan K3', 'Peralatan Keselamatan dan Kesehatan Kerja', NOW(), NOW()),
(8, 'Kebersihan', 'Peralatan kebersihan & kenyamanan ruangan', NOW(), NOW());

-- ---------------------------------------------------------------------
-- Tabel: products
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `stock` int unsigned NOT NULL DEFAULT 0,
  `total_stock` int unsigned NOT NULL DEFAULT 0,
  `location` varchar(255) DEFAULT NULL,
  `condition` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_code_unique` (`code`),
  KEY `products_name_index` (`name`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `code`, `name`, `category_id`, `stock`, `total_stock`, `location`, `condition`, `created_at`, `updated_at`) VALUES
(1, 'BRG-0001', 'Laptop Dell Latitude 5440', 1, 8, 8, 'Gudang IT Lt. 2', 'baik', NOW(), NOW()),
(2, 'BRG-0002', 'Laptop Lenovo ThinkPad E14', 1, 6, 6, 'Gudang IT Lt. 2', 'baik', NOW(), NOW()),
(3, 'BRG-0003', 'Proyektor Epson EB-X51', 1, 3, 3, 'Ruang AV Lt. 1', 'baik', NOW(), NOW()),
(4, 'BRG-0004', 'Proyektor BenQ MX535', 1, 2, 2, 'Ruang AV Lt. 1', 'baik', NOW(), NOW()),
(5, 'BRG-0005', 'Monitor LG 24 Inch', 1, 10, 10, 'Gudang IT Lt. 2', 'baik', NOW(), NOW()),
(6, 'BRG-0006', 'Monitor Samsung 27 Inch', 1, 4, 4, 'Gudang IT Lt. 2', 'baik', NOW(), NOW()),
(7, 'BRG-0007', 'Printer Epson L3210', 1, 5, 5, 'Ruang Admin Lt. 1', 'rusak_ringan', NOW(), NOW()),
(8, 'BRG-0008', 'Printer HP LaserJet Pro', 1, 3, 3, 'Ruang Admin Lt. 1', 'baik', NOW(), NOW()),
(9, 'BRG-0009', 'Kamera DSLR Canon EOS 90D', 1, 1, 1, 'Ruang Marketing Lt. 4', 'baik', NOW(), NOW()),
(10, 'BRG-0010', 'Scanner Canon CanoScan', 1, 0, 0, 'Ruang Admin Lt. 1', 'rusak_berat', NOW(), NOW()),
(11, 'BRG-0011', 'Kursi Kantor Ergonomis', 2, 25, 25, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(12, 'BRG-0012', 'Kursi Rapat', 2, 40, 40, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(13, 'BRG-0013', 'Meja Rapat Lipat', 2, 4, 4, 'Gudang Umum Lt. 1', 'rusak_ringan', NOW(), NOW()),
(14, 'BRG-0014', 'Meja Kerja Staff', 2, 18, 18, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(15, 'BRG-0015', 'Lemari Arsip Besi', 2, 6, 6, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(16, 'BRG-0016', 'Sofa Ruang Tunggu', 2, 2, 2, 'Lobby Lt. 1', 'baik', NOW(), NOW()),
(17, 'BRG-0017', 'Partisi Kantor', 2, 9, 9, 'Gudang Umum Lt. 1', 'rusak_ringan', NOW(), NOW()),
(18, 'BRG-0018', 'Rak Buku Kayu', 2, 5, 5, 'Ruang Perpustakaan Lt. 2', 'baik', NOW(), NOW()),
(19, 'BRG-0019', 'Whiteboard 120x90', 3, 6, 6, 'Gudang ATK', 'baik', NOW(), NOW()),
(20, 'BRG-0020', 'Flipchart Stand', 3, 3, 3, 'Gudang ATK', 'baik', NOW(), NOW()),
(21, 'BRG-0021', 'Proyektor Screen Portable', 3, 4, 4, 'Ruang AV Lt. 1', 'baik', NOW(), NOW()),
(22, 'BRG-0022', 'Stapler Besar Heavy Duty', 3, 12, 12, 'Gudang ATK', 'baik', NOW(), NOW()),
(23, 'BRG-0023', 'Mesin Fotokopi Kecil', 3, 1, 1, 'Ruang Admin Lt. 1', 'baik', NOW(), NOW()),
(24, 'BRG-0024', 'Laminating Machine', 3, 2, 2, 'Gudang ATK', 'baik', NOW(), NOW()),
(25, 'BRG-0025', 'Router Mikrotik RB1100', 4, 5, 5, 'Server Room Lt. 3', 'baik', NOW(), NOW()),
(26, 'BRG-0026', 'Switch HP 24 Port', 4, 4, 4, 'Server Room Lt. 3', 'baik', NOW(), NOW()),
(27, 'BRG-0027', 'Switch Cisco 48 Port', 4, 2, 2, 'Server Room Lt. 3', 'baik', NOW(), NOW()),
(28, 'BRG-0028', 'Kabel UTP Cat6 (roll)', 4, 30, 30, 'Gudang IT Lt. 2', 'baik', NOW(), NOW()),
(29, 'BRG-0029', 'Access Point Ubiquiti', 4, 8, 8, 'Server Room Lt. 3', 'baik', NOW(), NOW()),
(30, 'BRG-0030', 'Modem Fiber Optik', 4, 6, 6, 'Server Room Lt. 3', 'baik', NOW(), NOW()),
(31, 'BRG-0031', 'Server Rack 12U', 4, 1, 1, 'Server Room Lt. 3', 'baik', NOW(), NOW()),
(32, 'BRG-0032', 'UPS APC 1000VA', 4, 3, 3, 'Server Room Lt. 3', 'rusak_ringan', NOW(), NOW()),
(33, 'BRG-0033', 'Mobil Operasional Avanza', 5, 2, 2, 'Parkiran Basement', 'baik', NOW(), NOW()),
(34, 'BRG-0034', 'Mobil Operasional Innova', 5, 1, 1, 'Parkiran Basement', 'baik', NOW(), NOW()),
(35, 'BRG-0035', 'Motor Operasional Honda Vario', 5, 3, 3, 'Parkiran Basement', 'baik', NOW(), NOW()),
(36, 'BRG-0036', 'Sepeda Lipat Kantor', 5, 4, 4, 'Parkiran Basement', 'baik', NOW(), NOW()),
(37, 'BRG-0037', 'Speaker Aktif JBL', 6, 4, 4, 'Ruang AV Lt. 1', 'baik', NOW(), NOW()),
(38, 'BRG-0038', 'Mic Wireless Shure', 6, 6, 6, 'Ruang AV Lt. 1', 'baik', NOW(), NOW()),
(39, 'BRG-0039', 'Layar TV LED 55 Inch', 6, 2, 2, 'Ruang Rapat Utama Lt. 5', 'baik', NOW(), NOW()),
(40, 'BRG-0040', 'Video Conference Kit', 6, 3, 3, 'Ruang Rapat Utama Lt. 5', 'baik', NOW(), NOW()),
(41, 'BRG-0041', 'Tripod Kamera', 6, 5, 5, 'Ruang Marketing Lt. 4', 'baik', NOW(), NOW()),
(42, 'BRG-0042', 'Lighting Ring Light', 6, 4, 4, 'Ruang Marketing Lt. 4', 'baik', NOW(), NOW()),
(43, 'BRG-0043', 'APAR (Alat Pemadam Api Ringan)', 7, 15, 15, 'Setiap Lantai', 'baik', NOW(), NOW()),
(44, 'BRG-0044', 'Helm Safety', 7, 20, 20, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(45, 'BRG-0045', 'Rompi Safety', 7, 25, 25, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(46, 'BRG-0046', 'Kotak P3K', 7, 10, 10, 'Setiap Lantai', 'baik', NOW(), NOW()),
(47, 'BRG-0047', 'Safety Shoes', 7, 12, 12, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(48, 'BRG-0048', 'Vacuum Cleaner', 8, 5, 5, 'Gudang Umum Lt. 1', 'baik', NOW(), NOW()),
(49, 'BRG-0049', 'Dispenser Air', 8, 8, 8, 'Setiap Lantai', 'baik', NOW(), NOW()),
(50, 'BRG-0050', 'Kipas Angin Berdiri', 8, 6, 6, 'Gudang Umum Lt. 1', 'rusak_ringan', NOW(), NOW()),
(51, 'BRG-0051', 'AC Portable', 8, 3, 3, 'Ruang Server Lt. 3', 'baik', NOW(), NOW()),
(52, 'BRG-0052', 'Air Purifier', 8, 2, 2, 'Ruang Direksi Lt. 5', 'baik', NOW(), NOW());


-- ---------------------------------------------------------------------
-- Tabel: borrowings
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `borrowings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `borrower_name` varchar(255) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrowings_status_index` (`status`),
  KEY `borrowings_borrow_date_index` (`borrow_date`),
  KEY `borrowings_user_id_foreign` (`user_id`),
  CONSTRAINT `borrowings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabel: borrowing_details
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `borrowing_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `borrowing_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT 1,
  `condition_on_return` enum('baik','rusak_ringan','rusak_berat') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrowing_details_borrowing_id_foreign` (`borrowing_id`),
  KEY `borrowing_details_product_id_foreign` (`product_id`),
  CONSTRAINT `borrowing_details_borrowing_id_foreign` FOREIGN KEY (`borrowing_id`) REFERENCES `borrowings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `borrowing_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data peminjaman (auto-generated, konsisten dengan BorrowingSeeder.php)
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Budi Santoso', 2, DATE_SUB(CURDATE(), INTERVAL 6 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL, 'dipinjam', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Siti Rahma', 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL, 'dipinjam', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Andi Wijaya', 2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL, 'dipinjam', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(4, 'Dewi Lestari', 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), NULL, 'dipinjam', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Rian Pratama', 2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), NULL, 'dipinjam', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(6, 'Fajar Nugroho', 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), NULL, 'dipinjam', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(7, 'Maya Sari', 2, DATE_SUB(CURDATE(), INTERVAL 8 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL, 'terlambat', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(8, 'Agus Setiawan', 1, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(9, 'Nur Aini', 2, DATE_SUB(CURDATE(), INTERVAL 12 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(10, 'Bayu Kurniawan', 1, DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_SUB(CURDATE(), INTERVAL 7 DAY), NULL, 'terlambat', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(11, 'Lina Marlina', 2, DATE_SUB(CURDATE(), INTERVAL 16 DAY), DATE_SUB(CURDATE(), INTERVAL 9 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(12, 'Hendra Gunawan', 1, DATE_SUB(CURDATE(), INTERVAL 18 DAY), DATE_SUB(CURDATE(), INTERVAL 11 DAY), DATE_SUB(CURDATE(), INTERVAL 9 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(13, 'Wulan Ramadhani', 2, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), NULL, 'terlambat', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(14, 'Yusuf Ramadhan', 1, DATE_SUB(CURDATE(), INTERVAL 22 DAY), DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(15, 'Indah Permatasari', 2, DATE_SUB(CURDATE(), INTERVAL 24 DAY), DATE_SUB(CURDATE(), INTERVAL 17 DAY), DATE_SUB(CURDATE(), INTERVAL 18 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(16, 'Doni Saputra', 1, DATE_SUB(CURDATE(), INTERVAL 26 DAY), DATE_SUB(CURDATE(), INTERVAL 19 DAY), NULL, 'terlambat', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(17, 'Tika Anggraini', 2, DATE_SUB(CURDATE(), INTERVAL 28 DAY), DATE_SUB(CURDATE(), INTERVAL 21 DAY), DATE_SUB(CURDATE(), INTERVAL 22 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(18, 'Eko Prasetyo', 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_SUB(CURDATE(), INTERVAL 23 DAY), DATE_SUB(CURDATE(), INTERVAL 21 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(19, 'Ratna Dewi', 2, DATE_SUB(CURDATE(), INTERVAL 31 DAY), DATE_SUB(CURDATE(), INTERVAL 24 DAY), DATE_SUB(CURDATE(), INTERVAL 26 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(20, 'Arif Rahman', 1, DATE_SUB(CURDATE(), INTERVAL 36 DAY), DATE_SUB(CURDATE(), INTERVAL 29 DAY), DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(21, 'Nadia Kusuma', 2, DATE_SUB(CURDATE(), INTERVAL 40 DAY), DATE_SUB(CURDATE(), INTERVAL 33 DAY), DATE_SUB(CURDATE(), INTERVAL 33 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(22, 'Wahyu Hidayat', 1, DATE_SUB(CURDATE(), INTERVAL 45 DAY), DATE_SUB(CURDATE(), INTERVAL 38 DAY), DATE_SUB(CURDATE(), INTERVAL 37 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(23, 'Sri Wahyuni', 2, DATE_SUB(CURDATE(), INTERVAL 50 DAY), DATE_SUB(CURDATE(), INTERVAL 43 DAY), DATE_SUB(CURDATE(), INTERVAL 41 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(24, 'Rudi Hartono', 1, DATE_SUB(CURDATE(), INTERVAL 55 DAY), DATE_SUB(CURDATE(), INTERVAL 48 DAY), DATE_SUB(CURDATE(), INTERVAL 50 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(25, 'Putri Amelia', 2, DATE_SUB(CURDATE(), INTERVAL 59 DAY), DATE_SUB(CURDATE(), INTERVAL 52 DAY), DATE_SUB(CURDATE(), INTERVAL 53 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(26, 'Taufik Hidayat', 1, DATE_SUB(CURDATE(), INTERVAL 64 DAY), DATE_SUB(CURDATE(), INTERVAL 57 DAY), DATE_SUB(CURDATE(), INTERVAL 57 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(27, 'Dian Puspita', 2, DATE_SUB(CURDATE(), INTERVAL 69 DAY), DATE_SUB(CURDATE(), INTERVAL 62 DAY), DATE_SUB(CURDATE(), INTERVAL 61 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(28, 'Ilham Maulana', 1, DATE_SUB(CURDATE(), INTERVAL 73 DAY), DATE_SUB(CURDATE(), INTERVAL 66 DAY), DATE_SUB(CURDATE(), INTERVAL 64 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(29, 'Fitri Handayani', 2, DATE_SUB(CURDATE(), INTERVAL 78 DAY), DATE_SUB(CURDATE(), INTERVAL 71 DAY), NULL, 'terlambat', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(30, 'Galih Prakoso', 1, DATE_SUB(CURDATE(), INTERVAL 83 DAY), DATE_SUB(CURDATE(), INTERVAL 76 DAY), DATE_SUB(CURDATE(), INTERVAL 77 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(31, 'Budi Santoso', 2, DATE_SUB(CURDATE(), INTERVAL 87 DAY), DATE_SUB(CURDATE(), INTERVAL 80 DAY), DATE_SUB(CURDATE(), INTERVAL 80 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(32, 'Siti Rahma', 1, DATE_SUB(CURDATE(), INTERVAL 92 DAY), DATE_SUB(CURDATE(), INTERVAL 85 DAY), DATE_SUB(CURDATE(), INTERVAL 84 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(33, 'Andi Wijaya', 2, DATE_SUB(CURDATE(), INTERVAL 97 DAY), DATE_SUB(CURDATE(), INTERVAL 90 DAY), DATE_SUB(CURDATE(), INTERVAL 88 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(34, 'Dewi Lestari', 1, DATE_SUB(CURDATE(), INTERVAL 102 DAY), DATE_SUB(CURDATE(), INTERVAL 95 DAY), DATE_SUB(CURDATE(), INTERVAL 97 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(35, 'Rian Pratama', 2, DATE_SUB(CURDATE(), INTERVAL 106 DAY), DATE_SUB(CURDATE(), INTERVAL 99 DAY), DATE_SUB(CURDATE(), INTERVAL 100 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(36, 'Fajar Nugroho', 1, DATE_SUB(CURDATE(), INTERVAL 111 DAY), DATE_SUB(CURDATE(), INTERVAL 104 DAY), DATE_SUB(CURDATE(), INTERVAL 104 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(37, 'Maya Sari', 2, DATE_SUB(CURDATE(), INTERVAL 116 DAY), DATE_SUB(CURDATE(), INTERVAL 109 DAY), DATE_SUB(CURDATE(), INTERVAL 108 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(38, 'Agus Setiawan', 1, DATE_SUB(CURDATE(), INTERVAL 120 DAY), DATE_SUB(CURDATE(), INTERVAL 113 DAY), DATE_SUB(CURDATE(), INTERVAL 111 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(39, 'Nur Aini', 2, DATE_SUB(CURDATE(), INTERVAL 125 DAY), DATE_SUB(CURDATE(), INTERVAL 118 DAY), DATE_SUB(CURDATE(), INTERVAL 120 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(40, 'Bayu Kurniawan', 1, DATE_SUB(CURDATE(), INTERVAL 130 DAY), DATE_SUB(CURDATE(), INTERVAL 123 DAY), DATE_SUB(CURDATE(), INTERVAL 124 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(41, 'Lina Marlina', 2, DATE_SUB(CURDATE(), INTERVAL 134 DAY), DATE_SUB(CURDATE(), INTERVAL 127 DAY), DATE_SUB(CURDATE(), INTERVAL 127 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(42, 'Hendra Gunawan', 1, DATE_SUB(CURDATE(), INTERVAL 139 DAY), DATE_SUB(CURDATE(), INTERVAL 132 DAY), DATE_SUB(CURDATE(), INTERVAL 131 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(43, 'Wulan Ramadhani', 2, DATE_SUB(CURDATE(), INTERVAL 144 DAY), DATE_SUB(CURDATE(), INTERVAL 137 DAY), DATE_SUB(CURDATE(), INTERVAL 135 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(44, 'Yusuf Ramadhan', 1, DATE_SUB(CURDATE(), INTERVAL 149 DAY), DATE_SUB(CURDATE(), INTERVAL 142 DAY), DATE_SUB(CURDATE(), INTERVAL 144 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(45, 'Indah Permatasari', 2, DATE_SUB(CURDATE(), INTERVAL 153 DAY), DATE_SUB(CURDATE(), INTERVAL 146 DAY), DATE_SUB(CURDATE(), INTERVAL 147 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(46, 'Doni Saputra', 1, DATE_SUB(CURDATE(), INTERVAL 158 DAY), DATE_SUB(CURDATE(), INTERVAL 151 DAY), DATE_SUB(CURDATE(), INTERVAL 151 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(47, 'Tika Anggraini', 2, DATE_SUB(CURDATE(), INTERVAL 163 DAY), DATE_SUB(CURDATE(), INTERVAL 156 DAY), DATE_SUB(CURDATE(), INTERVAL 155 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(48, 'Eko Prasetyo', 1, DATE_SUB(CURDATE(), INTERVAL 167 DAY), DATE_SUB(CURDATE(), INTERVAL 160 DAY), DATE_SUB(CURDATE(), INTERVAL 158 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(49, 'Ratna Dewi', 2, DATE_SUB(CURDATE(), INTERVAL 172 DAY), DATE_SUB(CURDATE(), INTERVAL 165 DAY), DATE_SUB(CURDATE(), INTERVAL 167 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(50, 'Arif Rahman', 1, DATE_SUB(CURDATE(), INTERVAL 177 DAY), DATE_SUB(CURDATE(), INTERVAL 170 DAY), DATE_SUB(CURDATE(), INTERVAL 171 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(51, 'Nadia Kusuma', 2, DATE_SUB(CURDATE(), INTERVAL 182 DAY), DATE_SUB(CURDATE(), INTERVAL 175 DAY), DATE_SUB(CURDATE(), INTERVAL 175 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(52, 'Wahyu Hidayat', 1, DATE_SUB(CURDATE(), INTERVAL 186 DAY), DATE_SUB(CURDATE(), INTERVAL 179 DAY), DATE_SUB(CURDATE(), INTERVAL 178 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(53, 'Sri Wahyuni', 2, DATE_SUB(CURDATE(), INTERVAL 191 DAY), DATE_SUB(CURDATE(), INTERVAL 184 DAY), DATE_SUB(CURDATE(), INTERVAL 182 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(54, 'Rudi Hartono', 1, DATE_SUB(CURDATE(), INTERVAL 196 DAY), DATE_SUB(CURDATE(), INTERVAL 189 DAY), DATE_SUB(CURDATE(), INTERVAL 191 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(55, 'Putri Amelia', 2, DATE_SUB(CURDATE(), INTERVAL 200 DAY), DATE_SUB(CURDATE(), INTERVAL 193 DAY), DATE_SUB(CURDATE(), INTERVAL 194 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(56, 'Taufik Hidayat', 1, DATE_SUB(CURDATE(), INTERVAL 205 DAY), DATE_SUB(CURDATE(), INTERVAL 198 DAY), DATE_SUB(CURDATE(), INTERVAL 198 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(57, 'Dian Puspita', 2, DATE_SUB(CURDATE(), INTERVAL 210 DAY), DATE_SUB(CURDATE(), INTERVAL 203 DAY), DATE_SUB(CURDATE(), INTERVAL 202 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(58, 'Ilham Maulana', 1, DATE_SUB(CURDATE(), INTERVAL 214 DAY), DATE_SUB(CURDATE(), INTERVAL 207 DAY), DATE_SUB(CURDATE(), INTERVAL 205 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(59, 'Fitri Handayani', 2, DATE_SUB(CURDATE(), INTERVAL 219 DAY), DATE_SUB(CURDATE(), INTERVAL 212 DAY), DATE_SUB(CURDATE(), INTERVAL 214 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(60, 'Galih Prakoso', 1, DATE_SUB(CURDATE(), INTERVAL 224 DAY), DATE_SUB(CURDATE(), INTERVAL 217 DAY), DATE_SUB(CURDATE(), INTERVAL 218 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(61, 'Budi Santoso', 2, DATE_SUB(CURDATE(), INTERVAL 229 DAY), DATE_SUB(CURDATE(), INTERVAL 222 DAY), DATE_SUB(CURDATE(), INTERVAL 222 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(62, 'Siti Rahma', 1, DATE_SUB(CURDATE(), INTERVAL 233 DAY), DATE_SUB(CURDATE(), INTERVAL 226 DAY), DATE_SUB(CURDATE(), INTERVAL 225 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(63, 'Andi Wijaya', 2, DATE_SUB(CURDATE(), INTERVAL 238 DAY), DATE_SUB(CURDATE(), INTERVAL 231 DAY), DATE_SUB(CURDATE(), INTERVAL 229 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(64, 'Dewi Lestari', 1, DATE_SUB(CURDATE(), INTERVAL 243 DAY), DATE_SUB(CURDATE(), INTERVAL 236 DAY), NULL, 'terlambat', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(65, 'Rian Pratama', 2, DATE_SUB(CURDATE(), INTERVAL 247 DAY), DATE_SUB(CURDATE(), INTERVAL 240 DAY), DATE_SUB(CURDATE(), INTERVAL 241 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(66, 'Fajar Nugroho', 1, DATE_SUB(CURDATE(), INTERVAL 252 DAY), DATE_SUB(CURDATE(), INTERVAL 245 DAY), DATE_SUB(CURDATE(), INTERVAL 245 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(67, 'Maya Sari', 2, DATE_SUB(CURDATE(), INTERVAL 257 DAY), DATE_SUB(CURDATE(), INTERVAL 250 DAY), DATE_SUB(CURDATE(), INTERVAL 249 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(68, 'Agus Setiawan', 1, DATE_SUB(CURDATE(), INTERVAL 262 DAY), DATE_SUB(CURDATE(), INTERVAL 255 DAY), DATE_SUB(CURDATE(), INTERVAL 253 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(69, 'Nur Aini', 2, DATE_SUB(CURDATE(), INTERVAL 266 DAY), DATE_SUB(CURDATE(), INTERVAL 259 DAY), DATE_SUB(CURDATE(), INTERVAL 261 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(70, 'Bayu Kurniawan', 1, DATE_SUB(CURDATE(), INTERVAL 271 DAY), DATE_SUB(CURDATE(), INTERVAL 264 DAY), DATE_SUB(CURDATE(), INTERVAL 265 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(71, 'Lina Marlina', 2, DATE_SUB(CURDATE(), INTERVAL 276 DAY), DATE_SUB(CURDATE(), INTERVAL 269 DAY), DATE_SUB(CURDATE(), INTERVAL 269 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(72, 'Hendra Gunawan', 1, DATE_SUB(CURDATE(), INTERVAL 280 DAY), DATE_SUB(CURDATE(), INTERVAL 273 DAY), DATE_SUB(CURDATE(), INTERVAL 272 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(73, 'Wulan Ramadhani', 2, DATE_SUB(CURDATE(), INTERVAL 285 DAY), DATE_SUB(CURDATE(), INTERVAL 278 DAY), DATE_SUB(CURDATE(), INTERVAL 276 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(74, 'Yusuf Ramadhan', 1, DATE_SUB(CURDATE(), INTERVAL 290 DAY), DATE_SUB(CURDATE(), INTERVAL 283 DAY), DATE_SUB(CURDATE(), INTERVAL 285 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(75, 'Indah Permatasari', 2, DATE_SUB(CURDATE(), INTERVAL 294 DAY), DATE_SUB(CURDATE(), INTERVAL 287 DAY), DATE_SUB(CURDATE(), INTERVAL 288 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(76, 'Doni Saputra', 1, DATE_SUB(CURDATE(), INTERVAL 299 DAY), DATE_SUB(CURDATE(), INTERVAL 292 DAY), DATE_SUB(CURDATE(), INTERVAL 292 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(77, 'Tika Anggraini', 2, DATE_SUB(CURDATE(), INTERVAL 304 DAY), DATE_SUB(CURDATE(), INTERVAL 297 DAY), DATE_SUB(CURDATE(), INTERVAL 296 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(78, 'Eko Prasetyo', 1, DATE_SUB(CURDATE(), INTERVAL 309 DAY), DATE_SUB(CURDATE(), INTERVAL 302 DAY), DATE_SUB(CURDATE(), INTERVAL 300 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(79, 'Ratna Dewi', 2, DATE_SUB(CURDATE(), INTERVAL 313 DAY), DATE_SUB(CURDATE(), INTERVAL 306 DAY), DATE_SUB(CURDATE(), INTERVAL 308 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(80, 'Arif Rahman', 1, DATE_SUB(CURDATE(), INTERVAL 318 DAY), DATE_SUB(CURDATE(), INTERVAL 311 DAY), DATE_SUB(CURDATE(), INTERVAL 312 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(81, 'Nadia Kusuma', 2, DATE_SUB(CURDATE(), INTERVAL 323 DAY), DATE_SUB(CURDATE(), INTERVAL 316 DAY), DATE_SUB(CURDATE(), INTERVAL 316 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(82, 'Wahyu Hidayat', 1, DATE_SUB(CURDATE(), INTERVAL 327 DAY), DATE_SUB(CURDATE(), INTERVAL 320 DAY), DATE_SUB(CURDATE(), INTERVAL 319 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(83, 'Sri Wahyuni', 2, DATE_SUB(CURDATE(), INTERVAL 332 DAY), DATE_SUB(CURDATE(), INTERVAL 325 DAY), DATE_SUB(CURDATE(), INTERVAL 323 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(84, 'Rudi Hartono', 1, DATE_SUB(CURDATE(), INTERVAL 337 DAY), DATE_SUB(CURDATE(), INTERVAL 330 DAY), DATE_SUB(CURDATE(), INTERVAL 332 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(85, 'Putri Amelia', 2, DATE_SUB(CURDATE(), INTERVAL 341 DAY), DATE_SUB(CURDATE(), INTERVAL 334 DAY), DATE_SUB(CURDATE(), INTERVAL 335 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(86, 'Taufik Hidayat', 1, DATE_SUB(CURDATE(), INTERVAL 346 DAY), DATE_SUB(CURDATE(), INTERVAL 339 DAY), DATE_SUB(CURDATE(), INTERVAL 339 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(87, 'Dian Puspita', 2, DATE_SUB(CURDATE(), INTERVAL 351 DAY), DATE_SUB(CURDATE(), INTERVAL 344 DAY), DATE_SUB(CURDATE(), INTERVAL 343 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(88, 'Ilham Maulana', 1, DATE_SUB(CURDATE(), INTERVAL 356 DAY), DATE_SUB(CURDATE(), INTERVAL 349 DAY), DATE_SUB(CURDATE(), INTERVAL 347 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(89, 'Fitri Handayani', 2, DATE_SUB(CURDATE(), INTERVAL 360 DAY), DATE_SUB(CURDATE(), INTERVAL 353 DAY), DATE_SUB(CURDATE(), INTERVAL 355 DAY), 'dikembalikan', NOW(), NOW());
INSERT INTO `borrowings` (`id`, `borrower_name`, `user_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `updated_at`) VALUES
(90, 'Galih Prakoso', 1, DATE_SUB(CURDATE(), INTERVAL 365 DAY), DATE_SUB(CURDATE(), INTERVAL 358 DAY), DATE_SUB(CURDATE(), INTERVAL 359 DAY), 'dikembalikan', NOW(), NOW());

-- Detail barang yang dipinjam per transaksi
INSERT INTO `borrowing_details` (`id`, `borrowing_id`, `product_id`, `quantity`, `condition_on_return`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, NOW(), NOW()),
(2, 1, 8, 1, NULL, NOW(), NOW()),
(3, 2, 2, 2, NULL, NOW(), NOW()),
(4, 3, 3, 1, NULL, NOW(), NOW()),
(5, 4, 4, 2, NULL, NOW(), NOW()),
(6, 4, 11, 2, NULL, NOW(), NOW()),
(7, 5, 5, 1, NULL, NOW(), NOW()),
(8, 6, 6, 2, NULL, NOW(), NOW()),
(9, 7, 7, 1, NULL, NOW(), NOW()),
(10, 7, 14, 1, NULL, NOW(), NOW()),
(11, 8, 8, 2, 'baik', NOW(), NOW()),
(12, 9, 9, 1, 'baik', NOW(), NOW()),
(13, 10, 17, 2, NULL, NOW(), NOW()),
(14, 11, 11, 1, 'baik', NOW(), NOW()),
(15, 12, 12, 2, 'baik', NOW(), NOW()),
(16, 13, 13, 1, NULL, NOW(), NOW()),
(17, 13, 20, 1, NULL, NOW(), NOW()),
(18, 14, 14, 2, 'baik', NOW(), NOW()),
(19, 15, 15, 1, 'baik', NOW(), NOW()),
(20, 16, 16, 2, NULL, NOW(), NOW()),
(21, 16, 23, 1, NULL, NOW(), NOW()),
(22, 17, 17, 1, 'baik', NOW(), NOW()),
(23, 18, 18, 2, 'baik', NOW(), NOW()),
(24, 19, 19, 1, 'baik', NOW(), NOW()),
(25, 19, 26, 1, 'baik', NOW(), NOW()),
(26, 20, 20, 2, 'baik', NOW(), NOW()),
(27, 21, 21, 1, 'baik', NOW(), NOW()),
(28, 22, 22, 2, 'baik', NOW(), NOW()),
(29, 22, 29, 2, 'baik', NOW(), NOW()),
(30, 23, 23, 1, 'baik', NOW(), NOW()),
(31, 24, 24, 2, 'baik', NOW(), NOW()),
(32, 25, 25, 1, 'baik', NOW(), NOW()),
(33, 25, 32, 1, 'baik', NOW(), NOW()),
(34, 26, 26, 2, 'baik', NOW(), NOW()),
(35, 27, 27, 1, 'baik', NOW(), NOW()),
(36, 28, 28, 2, 'baik', NOW(), NOW()),
(37, 28, 35, 2, 'baik', NOW(), NOW()),
(38, 29, 29, 1, NULL, NOW(), NOW()),
(39, 30, 30, 2, 'baik', NOW(), NOW()),
(40, 31, 31, 1, 'baik', NOW(), NOW()),
(41, 31, 38, 1, 'baik', NOW(), NOW()),
(42, 32, 32, 2, 'baik', NOW(), NOW()),
(43, 33, 33, 1, 'baik', NOW(), NOW()),
(44, 34, 34, 2, 'baik', NOW(), NOW()),
(45, 34, 41, 2, 'baik', NOW(), NOW()),
(46, 35, 35, 1, 'baik', NOW(), NOW()),
(47, 36, 36, 2, 'baik', NOW(), NOW()),
(48, 37, 37, 1, 'baik', NOW(), NOW()),
(49, 37, 44, 1, 'baik', NOW(), NOW()),
(50, 38, 38, 2, 'baik', NOW(), NOW()),
(51, 39, 39, 1, 'baik', NOW(), NOW()),
(52, 40, 40, 2, 'baik', NOW(), NOW()),
(53, 40, 47, 2, 'baik', NOW(), NOW()),
(54, 41, 41, 1, 'baik', NOW(), NOW()),
(55, 42, 42, 2, 'baik', NOW(), NOW()),
(56, 43, 43, 1, 'baik', NOW(), NOW()),
(57, 43, 50, 1, 'baik', NOW(), NOW()),
(58, 44, 44, 2, 'baik', NOW(), NOW()),
(59, 45, 45, 1, 'baik', NOW(), NOW()),
(60, 46, 46, 2, 'baik', NOW(), NOW()),
(61, 46, 1, 2, 'baik', NOW(), NOW()),
(62, 47, 47, 1, 'baik', NOW(), NOW()),
(63, 48, 48, 2, 'baik', NOW(), NOW()),
(64, 49, 49, 1, 'baik', NOW(), NOW()),
(65, 49, 4, 1, 'baik', NOW(), NOW()),
(66, 50, 50, 2, 'baik', NOW(), NOW()),
(67, 51, 51, 1, 'baik', NOW(), NOW()),
(68, 52, 52, 2, 'baik', NOW(), NOW()),
(69, 52, 7, 2, 'baik', NOW(), NOW()),
(70, 53, 1, 1, 'baik', NOW(), NOW()),
(71, 54, 2, 2, 'baik', NOW(), NOW()),
(72, 55, 3, 1, 'baik', NOW(), NOW()),
(73, 55, 10, 1, 'baik', NOW(), NOW()),
(74, 56, 4, 2, 'baik', NOW(), NOW()),
(75, 57, 5, 1, 'baik', NOW(), NOW()),
(76, 58, 6, 2, 'baik', NOW(), NOW()),
(77, 58, 13, 2, 'baik', NOW(), NOW()),
(78, 59, 7, 1, 'baik', NOW(), NOW()),
(79, 60, 8, 2, 'baik', NOW(), NOW()),
(80, 61, 9, 1, 'baik', NOW(), NOW()),
(81, 61, 16, 1, 'baik', NOW(), NOW()),
(82, 62, 10, 2, 'baik', NOW(), NOW()),
(83, 63, 11, 1, 'baik', NOW(), NOW()),
(84, 64, 12, 2, NULL, NOW(), NOW()),
(85, 64, 19, 2, NULL, NOW(), NOW()),
(86, 65, 13, 1, 'baik', NOW(), NOW()),
(87, 66, 14, 2, 'baik', NOW(), NOW()),
(88, 67, 15, 1, 'baik', NOW(), NOW()),
(89, 67, 22, 1, 'baik', NOW(), NOW()),
(90, 68, 16, 2, 'baik', NOW(), NOW()),
(91, 69, 17, 1, 'baik', NOW(), NOW()),
(92, 70, 18, 2, 'baik', NOW(), NOW()),
(93, 70, 25, 2, 'baik', NOW(), NOW()),
(94, 71, 19, 1, 'baik', NOW(), NOW()),
(95, 72, 20, 2, 'baik', NOW(), NOW()),
(96, 73, 21, 1, 'baik', NOW(), NOW()),
(97, 73, 28, 1, 'baik', NOW(), NOW()),
(98, 74, 22, 2, 'baik', NOW(), NOW()),
(99, 75, 23, 1, 'baik', NOW(), NOW()),
(100, 76, 24, 2, 'baik', NOW(), NOW()),
(101, 76, 31, 2, 'baik', NOW(), NOW()),
(102, 77, 25, 1, 'baik', NOW(), NOW()),
(103, 78, 26, 2, 'baik', NOW(), NOW()),
(104, 79, 27, 1, 'baik', NOW(), NOW()),
(105, 79, 34, 1, 'baik', NOW(), NOW()),
(106, 80, 28, 2, 'baik', NOW(), NOW()),
(107, 81, 29, 1, 'baik', NOW(), NOW()),
(108, 82, 30, 2, 'baik', NOW(), NOW()),
(109, 82, 37, 2, 'baik', NOW(), NOW()),
(110, 83, 31, 1, 'baik', NOW(), NOW()),
(111, 84, 32, 2, 'baik', NOW(), NOW()),
(112, 85, 33, 1, 'baik', NOW(), NOW()),
(113, 85, 40, 1, 'baik', NOW(), NOW()),
(114, 86, 34, 2, 'baik', NOW(), NOW()),
(115, 87, 35, 1, 'baik', NOW(), NOW()),
(116, 88, 36, 2, 'baik', NOW(), NOW()),
(117, 88, 43, 2, 'baik', NOW(), NOW()),
(118, 89, 37, 1, 'baik', NOW(), NOW()),
(119, 90, 38, 2, 'baik', NOW(), NOW());

-- Sinkronisasi stok akhir barang berdasarkan peminjaman yang masih aktif (dipinjam/terlambat)
UPDATE `products` SET `stock` = 7 WHERE `id` = 1;
UPDATE `products` SET `stock` = 4 WHERE `id` = 2;
UPDATE `products` SET `stock` = 2 WHERE `id` = 3;
UPDATE `products` SET `stock` = 0 WHERE `id` = 4;
UPDATE `products` SET `stock` = 9 WHERE `id` = 5;
UPDATE `products` SET `stock` = 2 WHERE `id` = 6;
UPDATE `products` SET `stock` = 4 WHERE `id` = 7;
UPDATE `products` SET `stock` = 2 WHERE `id` = 8;
UPDATE `products` SET `stock` = 23 WHERE `id` = 11;
UPDATE `products` SET `stock` = 38 WHERE `id` = 12;
UPDATE `products` SET `stock` = 3 WHERE `id` = 13;
UPDATE `products` SET `stock` = 17 WHERE `id` = 14;
UPDATE `products` SET `stock` = 0 WHERE `id` = 16;
UPDATE `products` SET `stock` = 7 WHERE `id` = 17;
UPDATE `products` SET `stock` = 4 WHERE `id` = 19;
UPDATE `products` SET `stock` = 2 WHERE `id` = 20;
UPDATE `products` SET `stock` = 0 WHERE `id` = 23;
UPDATE `products` SET `stock` = 7 WHERE `id` = 29;

-- ---------------------------------------------------------------------
-- Tabel: personal_access_tokens (dibuat otomatis oleh `artisan install:api` / Sanctum)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- ERD (ringkas) - relasi antar tabel:
--
--   roles (1) ────< users (1) ────< borrowings (1) ────< borrowing_details >──── (1) products (1) ────< borrowing_details
--                                                                                         │
--                                                                              categories (1) ────< products
-- =====================================================================
