-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 22, 2025 at 05:29 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rento`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_places`
--

DROP TABLE IF EXISTS `active_places`;
CREATE TABLE IF NOT EXISTS `active_places` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `user_type` enum('landlord','tenant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available_from` date DEFAULT NULL,
  `booking_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `active_places_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `active_places`
--

INSERT INTO `active_places` (`id`, `user_id`, `user_type`, `name`, `city`, `area`, `available_from`, `booking_type`, `price`, `price_unit`, `rating`, `image`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 5, 'landlord', 'فيلا حديثة بإطلالة', 'طرابلس', 'حي الأندلس', '2025-11-24', 'إيجار يومي', 30.00, 'د.ل / اليوم', 4.8, 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&h=500&q=60', 1, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(2, 5, 'landlord', 'شقة فاخرة قريبة من الخدمات', 'طرابلس', 'النوفليين', '2025-11-29', 'إيجار ليلي', 45.00, 'د.ل / ليلة', 4.6, 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&h=500&q=60', 1, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(3, 6, 'landlord', 'فيلا حديثة بإطلالة', 'بنغازي', 'حي الأندلس', '2025-11-24', 'إيجار يومي', 30.00, 'د.ل / اليوم', 4.8, 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&h=500&q=60', 1, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(4, 6, 'landlord', 'شقة فاخرة قريبة من الخدمات', 'بنغازي', 'النوفليين', '2025-11-29', 'إيجار ليلي', 45.00, 'د.ل / ليلة', 4.6, 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&h=500&q=60', 1, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(5, 7, 'tenant', 'آخر إقامة في مصراتة', 'مصراتة', 'وسط المدينة', '2025-10-30', 'إيجار قصير', 22.00, 'د.ل / يوم', 4.7, 'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=800&h=500&q=60', 1, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(6, 8, 'tenant', 'آخر إقامة في سبها', 'سبها', 'وسط المدينة', '2025-10-30', 'إيجار قصير', 22.00, 'د.ل / يوم', 4.7, 'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=800&h=500&q=60', 1, '2025-11-09 19:23:18', '2025-11-19 04:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `phone`, `image`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'مشرف النظام', 'meropubg250@gmail.com', '$2y$12$UgElQXVmXxB5TnxnnMHGzusfvBbc0fnmBFysPIfSQ1r9w8kRSC1Du', NULL, 'storage/admins/kwg7LBPtTH9GPamf5lP9yYIDdsAJWLE8cUJIALDv.jpg', NULL, '2025-11-09 19:23:16', '2025-11-09 19:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

DROP TABLE IF EXISTS `amenities`;
CREATE TABLE IF NOT EXISTS `amenities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'الانترنت لاسلكي', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(2, 'شاشة مسطحة', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(3, 'مكيف هواء مركزي', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(4, 'حمام سباحة خاص', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(5, 'حديقة خاصة', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(6, 'موقف خاص للسيارات', '2025-11-09 19:23:18', '2025-11-09 19:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `amenity_property`
--

DROP TABLE IF EXISTS `amenity_property`;
CREATE TABLE IF NOT EXISTS `amenity_property` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` bigint UNSIGNED NOT NULL,
  `amenity_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `amenity_property_property_id_foreign` (`property_id`),
  KEY `amenity_property_amenity_id_foreign` (`amenity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenity_property`
--

INSERT INTO `amenity_property` (`id`, `property_id`, `amenity_id`, `created_at`, `updated_at`) VALUES
(1, 1, 6, NULL, NULL),
(2, 1, 3, NULL, NULL),
(3, 1, 5, NULL, NULL),
(4, 2, 4, NULL, NULL),
(5, 2, 3, NULL, NULL),
(6, 2, 6, NULL, NULL),
(7, 2, 5, NULL, NULL),
(8, 2, 2, NULL, NULL),
(9, 2, 1, NULL, NULL),
(10, 3, 3, NULL, NULL),
(11, 3, 4, NULL, NULL),
(12, 3, 5, NULL, NULL),
(13, 3, 1, NULL, NULL),
(14, 4, 6, NULL, NULL),
(15, 4, 4, NULL, NULL),
(16, 4, 1, NULL, NULL),
(17, 5, 2, NULL, NULL),
(18, 5, 5, NULL, NULL),
(19, 5, 3, NULL, NULL),
(20, 5, 6, NULL, NULL),
(21, 5, 4, NULL, NULL),
(22, 6, 4, NULL, NULL),
(23, 6, 6, NULL, NULL),
(24, 6, 5, NULL, NULL),
(25, 6, 3, NULL, NULL),
(26, 6, 1, NULL, NULL),
(27, 6, 2, NULL, NULL),
(28, 7, 1, NULL, NULL),
(29, 7, 2, NULL, NULL),
(30, 7, 3, NULL, NULL),
(31, 7, 6, NULL, NULL),
(32, 7, 5, NULL, NULL),
(33, 8, 5, NULL, NULL),
(34, 8, 3, NULL, NULL),
(35, 8, 6, NULL, NULL),
(36, 9, 2, NULL, NULL),
(37, 9, 4, NULL, NULL),
(38, 9, 5, NULL, NULL),
(39, 9, 1, NULL, NULL),
(40, 10, 2, NULL, NULL),
(41, 10, 4, NULL, NULL),
(42, 10, 6, NULL, NULL),
(43, 10, 1, NULL, NULL),
(44, 10, 5, NULL, NULL),
(45, 10, 3, NULL, NULL),
(46, 11, 5, NULL, NULL),
(47, 11, 2, NULL, NULL),
(48, 11, 4, NULL, NULL),
(49, 11, 3, NULL, NULL),
(50, 11, 6, NULL, NULL),
(51, 1, 4, NULL, NULL),
(52, 1, 2, NULL, NULL),
(53, 1, 1, NULL, NULL),
(54, 3, 6, NULL, NULL),
(55, 4, 5, NULL, NULL),
(56, 7, 4, NULL, NULL),
(57, 8, 2, NULL, NULL),
(58, 8, 1, NULL, NULL),
(59, 9, 3, NULL, NULL),
(60, 9, 6, NULL, NULL),
(61, 3, 2, NULL, NULL),
(62, 4, 2, NULL, NULL),
(63, 4, 3, NULL, NULL),
(64, 5, 1, NULL, NULL),
(65, 8, 4, NULL, NULL),
(66, 11, 1, NULL, NULL),
(67, 12, 5, NULL, NULL),
(68, 12, 1, NULL, NULL),
(69, 12, 2, NULL, NULL),
(70, 12, 4, NULL, NULL),
(71, 12, 6, NULL, NULL),
(72, 12, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `areas_slug_unique` (`slug`),
  KEY `areas_city_id_foreign` (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `property_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `guests` int UNSIGNED NOT NULL DEFAULT '1',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `canceled_by` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancel_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_property_id_foreign` (`property_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `property_id`, `start_date`, `end_date`, `guests`, `status`, `created_at`, `updated_at`, `canceled_at`, `canceled_by`, `cancel_reason`) VALUES
(1, 1, 1, '2025-01-20', '2025-02-05', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(2, 1, 2, '2025-03-10', '2025-03-15', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(3, 1, 3, '2025-04-01', '2025-04-07', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(4, 3, 4, '2025-01-20', '2025-02-05', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(5, 3, 5, '2025-03-10', '2025-03-15', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(6, 3, 6, '2025-04-01', '2025-04-07', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(7, 3, 7, '2025-05-12', '2025-05-15', 2, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(8, 4, 4, '2025-03-22', '2025-03-27', 1, 'completed', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL, NULL, NULL),
(9, 7, 8, '2025-09-30', '2025-10-05', 3, 'confirmed', '2025-11-09 19:23:18', '2025-11-09 19:23:18', NULL, NULL, NULL),
(10, 7, 10, '2025-10-30', '2025-11-02', 4, 'completed', '2025-11-09 19:23:18', '2025-11-09 19:23:18', NULL, NULL, NULL),
(11, 8, 9, '2025-10-20', '2025-10-25', 2, 'completed', '2025-11-09 19:23:18', '2025-11-09 19:23:18', NULL, NULL, NULL),
(12, 8, 11, '2025-09-30', '2025-10-05', 4, 'completed', '2025-11-09 19:23:18', '2025-11-09 19:23:18', NULL, NULL, NULL),
(13, 7, 8, '2025-10-06', '2025-10-11', 4, 'completed', '2025-11-15 17:42:59', '2025-11-15 17:42:59', NULL, NULL, NULL),
(14, 7, 10, '2025-11-05', '2025-11-08', 4, 'completed', '2025-11-15 17:42:59', '2025-11-15 17:42:59', NULL, NULL, NULL),
(15, 8, 9, '2025-10-26', '2025-10-31', 1, 'completed', '2025-11-15 17:42:59', '2025-11-15 17:42:59', NULL, NULL, NULL),
(16, 8, 11, '2025-10-06', '2025-10-11', 4, 'completed', '2025-11-15 17:42:59', '2025-11-15 17:42:59', NULL, NULL, NULL),
(17, 12, 11, '2025-05-20', '2025-06-22', 2, 'paid', '2025-11-19 04:08:09', '2025-11-19 04:26:33', NULL, NULL, NULL),
(18, 14, 12, '2025-11-14', '2025-11-17', 2, 'confirmed', '2025-11-19 04:32:31', '2025-11-19 04:32:31', NULL, NULL, NULL),
(19, 7, 8, '2025-10-10', '2025-10-15', 1, 'completed', '2025-11-19 04:32:43', '2025-11-19 04:32:43', NULL, NULL, NULL),
(20, 7, 10, '2025-11-09', '2025-11-12', 3, 'completed', '2025-11-19 04:32:43', '2025-11-19 04:32:43', NULL, NULL, NULL),
(21, 8, 9, '2025-10-30', '2025-11-04', 2, 'completed', '2025-11-19 04:32:43', '2025-11-19 04:32:43', NULL, NULL, NULL),
(22, 8, 11, '2025-10-10', '2025-10-15', 4, 'completed', '2025-11-19 04:32:43', '2025-11-19 04:32:43', NULL, NULL, NULL),
(23, 16, 13, '2025-11-09', '2025-11-17', 3, 'confirmed', '2025-11-19 04:35:00', '2025-11-19 04:35:00', NULL, NULL, NULL),
(24, 16, 14, '2025-11-16', '2025-11-18', 1, 'confirmed', '2025-11-19 04:35:00', '2025-11-19 04:35:00', NULL, NULL, NULL),
(25, 16, 15, '2025-11-15', '2025-11-17', 1, 'confirmed', '2025-11-19 04:35:00', '2025-11-19 04:35:00', NULL, NULL, NULL),
(26, 16, 16, '2025-11-13', '2025-11-18', 3, 'confirmed', '2025-11-19 04:35:00', '2025-11-19 04:35:00', NULL, NULL, NULL),
(27, 7, 10, '2025-11-20', '2025-11-22', 2, 'canceled', '2025-11-19 22:55:17', '2025-11-19 22:56:45', '2025-11-19 22:56:45', 'renter', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaints_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2025_11_03_000000_create_admins_table', 1),
(7, '2025_11_03_105511_create_permission_tables', 1),
(8, '2025_11_03_105651_create_properties_table', 1),
(9, '2025_11_03_105651_create_property_types_table', 1),
(10, '2025_11_03_105652_create_amenities_table', 1),
(11, '2025_11_03_105652_create_bookings_table', 1),
(12, '2025_11_03_105653_create_payment_cards_table', 1),
(13, '2025_11_03_105653_create_transactions_table', 1),
(14, '2025_11_03_105653_create_wallets_table', 1),
(15, '2025_11_03_105654_create_complaints_table', 1),
(16, '2025_11_03_105654_create_notifications_table', 1),
(17, '2025_11_03_105654_create_withdraw_requests_table', 1),
(18, '2025_11_03_105655_create_system_settings_table', 1),
(19, '2025_11_03_111122_create_cities_table', 1),
(20, '2025_11_03_111133_create_areas_table', 1),
(21, '2025_11_04_000000_create_reviews_table', 1),
(22, '2025_11_04_000001_add_image_to_properties_table', 1),
(23, '2025_11_04_000100_add_points_balance_to_wallets_table', 1),
(24, '2025_11_04_000101_create_points_transactions_table', 1),
(25, '2025_11_04_000200_add_booking_meta_to_transactions_table', 1),
(26, '2025_11_05_120000_create_active_places_table', 1),
(27, '2025_11_05_130000_create_suspended_users_table', 1),
(28, '2025_11_05_140500_add_is_published_to_active_places_table', 1),
(29, '2025_11_06_000010_update_properties_add_fields', 1),
(30, '2025_11_06_000011_update_property_types_add_name', 1),
(31, '2025_11_06_000012_update_amenities_add_name', 1),
(32, '2025_11_06_000013_create_amenity_property_table', 1),
(33, '2025_11_08_000014_add_deactivation_fields_to_properties', 1),
(34, '2025_11_08_000015_update_notifications_table_add_fields', 1),
(35, '2025_11_08_000100_update_property_types_add_rental_and_active', 1),
(36, '2025_11_08_120000_create_property_images_table', 1),
(37, '2025_11_08_130500_add_keywords_to_properties_table', 1),
(38, '2025_11_09_000300_add_cancellation_fields_to_bookings', 1),
(39, '2025_11_09_150000_ensure_permission_tables_exist', 1),
(40, '2025_11_09_211247_create_support_tickets_table', 1),
(41, '2025_11_09_211830_create_support_ticket_replies_table', 1),
(42, '2025_11_09_213136_add_channel_and_target_to_notifications_table', 2),
(44, '2025_11_10_113426_add_status_to_transactions_table', 3),
(45, '2025_11_10_114652_create_payment_cards_table', 3),
(46, '2025_11_10_155215_create_refunds_table', 4),
(47, '2025_11_10_160027_add_wallet_balance_to_users_table', 5),
(48, '2025_11_10_160743_create_settings_table', 6),
(49, '2025_11_10_165653_create_penalties_table', 7),
(50, '2025_11_10_165826_add_user_id_to_transactions_table', 8),
(51, '2025_11_19_120000_backfill_user_id_on_transactions', 9),
(52, '2025_11_21_120000_add_explicit_links_to_support_tickets', 10),
(53, '2025_11_21_123000_add_admin_id_to_support_ticket_replies', 11),
(54, '2025_11_21_170000_update_notifications_values', 12),
(55, '2025_11_21_120001_add_admin_read_at_to_support_tickets_table', 13),
(56, '2025_11_22_000450_recreate_reviews_table_if_missing', 14),
(57, '2025_11_22_000500_add_tenant_metrics_to_reviews_table', 15);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'sms, push, email',
  `target_users` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'all, tenants, landlords, specific',
  `meta` json DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  KEY `notifications_type_index` (`type`),
  KEY `notifications_read_at_index` (`read_at`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `created_at`, `updated_at`, `user_id`, `title`, `message`, `type`, `channel`, `target_users`, `meta`, `read_at`, `sent_at`) VALUES
(1, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 2, 'عرض خاص - خصم 20% على جميع الحجوزات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-10-31 19:36:37'),
(2, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 6, 'عرض خاص - خصم 20% على جميع الحجوزات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-10-31 19:36:37'),
(3, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'عرض خاص - خصم 20% على جميع الحجوزات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-10-27 18:36:37'),
(4, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'عرض خاص - خصم 20% على جميع الحجوزات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-10-19 18:36:37'),
(5, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'عرض خاص - خصم 20% على جميع الحجوزات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-11-03 19:36:37'),
(6, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 1, 'تأكيد حجز جديد', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'tenants', NULL, NULL, '2025-10-14 18:36:37'),
(7, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 6, 'تأكيد حجز جديد', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'tenants', NULL, NULL, '2025-11-06 19:36:37'),
(8, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'تأكيد حجز جديد', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'tenants', NULL, NULL, '2025-10-15 18:36:37'),
(9, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 3, 'تأكيد حجز جديد', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'tenants', NULL, NULL, '2025-10-30 18:36:37'),
(10, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 2, 'إعلان عن صيانة دورية', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'email', 'landlords', NULL, NULL, '2025-10-12 18:36:37'),
(11, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'إعلان عن صيانة دورية', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'email', 'landlords', NULL, NULL, '2025-10-22 18:36:37'),
(12, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'إعلان عن صيانة دورية', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'email', 'landlords', NULL, NULL, '2025-10-30 18:36:37'),
(13, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'إعلان عن صيانة دورية', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'email', 'landlords', NULL, NULL, '2025-10-12 18:36:37'),
(14, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'إلغاء حجز', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'specific', NULL, NULL, '2025-11-08 19:36:37'),
(15, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 1, 'تنبيه: تحديث السياسات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-10-24 18:36:37'),
(16, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 6, 'تنبيه: تحديث السياسات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-11-06 19:36:37'),
(17, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'تنبيه: تحديث السياسات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-10-28 18:36:37'),
(18, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'تنبيه: تحديث السياسات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-11-06 19:36:37'),
(19, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'تنبيه: تحديث السياسات', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'sms', 'all', NULL, NULL, '2025-11-08 19:36:37'),
(20, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 2, 'عرض ترويجي للمستأجرين', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-10-30 18:36:37'),
(21, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'عرض ترويجي للمستأجرين', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-10-25 18:36:37'),
(22, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 3, 'عرض ترويجي للمستأجرين', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-05 19:36:37'),
(23, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'عرض ترويجي للمستأجرين', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-08 19:36:37'),
(24, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'عرض ترويجي للمستأجرين', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-10-20 18:36:37'),
(25, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 2, 'دليل التطبيق داخل التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'landlords', NULL, NULL, '2025-11-04 19:36:37'),
(27, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 2, 'تنبيه أمني هام', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'all', NULL, NULL, '2025-11-02 19:36:37'),
(28, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 6, 'تنبيه أمني هام', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'all', NULL, NULL, '2025-10-31 19:36:37'),
(29, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'تنبيه أمني هام', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'all', NULL, NULL, '2025-10-28 18:36:37'),
(30, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 3, 'تنبيه أمني هام', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'all', NULL, NULL, '2025-10-13 18:36:37'),
(31, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'تنبيه أمني هام', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'all', NULL, NULL, '2025-10-25 18:36:37'),
(32, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'تنبيه أمني هام', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'app', 'all', NULL, NULL, '2025-11-01 19:36:37'),
(33, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'تأكيد الدفع', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'booking_confirm', 'sms', 'specific', NULL, NULL, '2025-11-03 19:36:37'),
(34, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 1, 'ميزات جديدة في التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'all', NULL, NULL, '2025-11-09 19:36:37'),
(35, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 6, 'ميزات جديدة في التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'all', NULL, NULL, '2025-10-17 18:36:37'),
(36, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 4, 'ميزات جديدة في التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'all', NULL, NULL, '2025-11-08 19:36:37'),
(37, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 3, 'ميزات جديدة في التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'all', NULL, NULL, '2025-10-26 18:36:37'),
(38, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 5, 'ميزات جديدة في التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'all', NULL, NULL, '2025-10-24 18:36:37'),
(39, '2025-11-09 19:36:37', '2025-11-09 19:36:37', 7, 'ميزات جديدة في التطبيق', 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...', 'alert', 'email', 'all', NULL, NULL, '2025-10-17 18:36:37'),
(40, '2025-11-18 15:07:08', '2025-11-18 15:07:08', 9, 'تمت الموافقة على طلب السحب', 'تمت الموافقة على طلب سحب الرصيد وسيتم تحويل المبلغ.', 'refund', 'app', 'specific', '{\"amount\": \"150.00\", \"status\": \"approved\", \"refund_id\": 7}', NULL, '2025-11-18 15:07:08'),
(42, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 2, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(43, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 3, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(44, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 4, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(45, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 7, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(46, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 8, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(47, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 10, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(48, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 11, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(49, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 13, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(50, '2025-11-21 05:29:52', '2025-11-21 05:29:52', 14, 'yyyy', 'yyyyyyyyyyy', 'booking_confirm', 'sms', 'tenants', NULL, NULL, '2025-11-21 05:29:52'),
(52, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 2, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(53, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 3, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(54, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 4, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(55, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 7, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(56, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 8, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(57, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 10, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(58, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 11, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(59, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 13, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(60, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 14, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58'),
(61, '2025-11-21 05:42:58', '2025-11-21 05:42:58', 16, 'xxxxxxxxx', 'xxxxxxxxxx', 'booking_confirm', 'app', 'tenants', NULL, NULL, '2025-11-21 05:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_cards`
--

DROP TABLE IF EXISTS `payment_cards`;
CREATE TABLE IF NOT EXISTS `payment_cards` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `card_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('pending','active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_cards_card_number_unique` (`card_number`),
  KEY `payment_cards_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_cards`
--

INSERT INTO `payment_cards` (`id`, `user_id`, `card_number`, `card_type`, `amount`, `balance`, `issue_date`, `expiry_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, '1469 - 8961 - 7509 - 2016', 'standard', 200.00, 35.00, '2025-10-17', '2025-11-22', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(2, 4, '4501 - 0888 - 2529 - 1473', 'standard', 50.00, 38.00, '2025-11-01', '2025-12-05', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(3, 4, '7832 - 3357 - 4232 - 5614', 'standard', 500.00, 161.00, '2025-10-17', '2025-11-20', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:53:45'),
(4, 4, '2123 - 5823 - 9563 - 7908', 'standard', 50.00, 32.00, '2025-10-29', '2025-12-01', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(5, 2, '2002 - 5428 - 7339 - 3066', 'standard', 25.00, 21.00, '2025-10-17', '2025-11-23', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(6, 4, '8342 - 9256 - 8120 - 6599', 'standard', 100.00, 15.00, '2025-11-12', '2025-12-22', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(7, 4, '9870 - 5223 - 3528 - 6137', 'standard', 50.00, 46.00, '2025-10-30', '2025-12-12', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(8, 5, '7327 - 2856 - 8971 - 6148', 'standard', 25.00, 24.00, '2025-11-05', '2025-12-12', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(9, NULL, '6014 - 2170 - 2964 - 4301', 'standard', 100.00, 21.00, '2025-10-17', '2025-11-26', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(10, NULL, '9891 - 4705 - 8876 - 4021', 'standard', 200.00, 146.00, '2025-11-06', '2025-12-05', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(11, NULL, '3370 - 9870 - 0566 - 8002', 'standard', 500.00, 138.00, '2025-11-12', '2025-12-27', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(12, 4, '1079 - 6580 - 7037 - 6308', 'standard', 200.00, 74.00, '2025-11-15', '2025-12-18', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(13, 8, '2862 - 8260 - 2302 - 8642', 'standard', 100.00, 44.00, '2025-10-23', '2025-11-30', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(14, NULL, '5798 - 8682 - 8362 - 7122', 'standard', 50.00, 24.00, '2025-11-04', '2025-12-01', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(15, NULL, '3359 - 7778 - 6221 - 0405', 'standard', 500.00, 401.00, '2025-11-07', '2025-12-07', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(16, NULL, '3577 - 2042 - 4095 - 2618', 'standard', 500.00, 104.00, '2025-11-06', '2025-11-21', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(17, NULL, '7464 - 8292 - 8141 - 1331', 'standard', 50.00, 37.00, '2025-11-08', '2025-12-21', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(18, NULL, '0846 - 6023 - 7858 - 7888', 'standard', 500.00, 28.00, '2025-11-05', '2025-11-25', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(19, 6, '4694 - 1823 - 0850 - 3372', 'standard', 100.00, 2.00, '2025-11-05', '2025-11-25', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(20, NULL, '6217 - 4814 - 8273 - 7345', 'standard', 100.00, 59.00, '2025-11-10', '2025-12-04', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(21, 7, '4875 - 6966 - 0622 - 4625', 'standard', 25.00, 19.00, '2025-11-15', '2025-12-06', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(22, 8, '2841 - 3959 - 3914 - 0070', 'standard', 50.00, 40.00, '2025-10-24', '2025-11-13', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(23, NULL, '6716 - 8630 - 5646 - 1571', 'standard', 100.00, 76.00, '2025-10-30', '2025-12-02', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(24, 1, '7874 - 6389 - 9990 - 0450', 'standard', 500.00, 129.00, '2025-10-25', '2025-11-21', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(25, 7, '6201 - 1540 - 4443 - 7784', 'standard', 200.00, 95.00, '2025-10-20', '2025-11-13', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(26, NULL, '2094 - 4899 - 2341 - 6217', 'standard', 200.00, 136.00, '2025-10-21', '2025-11-27', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(27, 5, '9745 - 9365 - 6595 - 2388', 'standard', 50.00, 8.00, '2025-11-02', '2025-12-14', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(28, 6, '4997 - 0583 - 6238 - 0734', 'standard', 200.00, 153.00, '2025-11-08', '2025-12-10', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(29, NULL, '7189 - 1715 - 9835 - 1664', 'standard', 500.00, 190.00, '2025-10-21', '2025-11-25', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(30, NULL, '8755 - 2092 - 3029 - 1277', 'standard', 100.00, 16.00, '2025-10-23', '2025-12-07', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(31, NULL, '5931 - 3670 - 6581 - 9176', 'standard', 25.00, 8.00, '2025-10-23', '2025-11-23', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(32, NULL, '9243 - 8638 - 0476 - 7954', 'standard', 50.00, 34.00, '2025-11-15', '2025-12-17', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(33, 2, '3964 - 6945 - 1830 - 0378', 'standard', 25.00, 4.00, '2025-10-31', '2025-11-15', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(34, 1, '4501 - 4761 - 1656 - 0988', 'standard', 500.00, 483.00, '2025-10-18', '2025-11-09', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(35, NULL, '9163 - 7636 - 9811 - 6789', 'standard', 500.00, 445.00, '2025-10-30', '2025-11-21', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(36, NULL, '4776 - 7998 - 8588 - 0540', 'standard', 500.00, 458.00, '2025-11-07', '2025-11-26', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(37, NULL, '2882 - 8858 - 6348 - 6933', 'standard', 100.00, 22.00, '2025-10-17', '2025-11-14', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(38, NULL, '2906 - 3617 - 0455 - 4933', 'standard', 100.00, 46.00, '2025-11-02', '2025-11-27', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(39, 1, '3929 - 7897 - 6735 - 4076', 'standard', 50.00, 21.00, '2025-10-30', '2025-11-21', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(40, NULL, '0542 - 2724 - 0686 - 7333', 'standard', 100.00, 69.00, '2025-10-25', '2025-11-23', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(41, NULL, '5641 - 3201 - 6697 - 6919', 'standard', 25.00, 24.00, '2025-11-13', '2025-12-17', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(42, 4, '0957 - 9202 - 6456 - 3863', 'standard', 25.00, 8.00, '2025-11-13', '2025-12-01', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(43, 3, '2704 - 5942 - 4765 - 0926', 'standard', 50.00, 2.00, '2025-10-19', '2025-11-20', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(44, 6, '8964 - 2757 - 3060 - 3197', 'standard', 200.00, 149.00, '2025-10-20', '2025-11-19', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(45, NULL, '7465 - 3746 - 8830 - 8567', 'standard', 50.00, 18.00, '2025-10-23', '2025-11-19', 'expired', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(46, NULL, '6417 - 0068 - 1220 - 6944', 'standard', 200.00, 162.00, '2025-10-16', '2025-11-10', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(47, NULL, '6569 - 3000 - 3604 - 6768', 'standard', 25.00, 0.00, '2025-10-17', '2025-11-11', 'cancelled', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(48, 2, '2673 - 4863 - 3591 - 1298', 'standard', 50.00, 0.00, '2025-11-09', '2025-12-03', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(49, NULL, '1504 - 3457 - 4212 - 3855', 'standard', 100.00, 22.00, '2025-10-22', '2025-11-12', 'active', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(50, NULL, '9123 - 2633 - 1745 - 5781', 'standard', 500.00, 145.00, '2025-11-06', '2025-12-09', 'pending', NULL, '2025-11-15 17:42:59', '2025-11-15 17:42:59'),
(51, NULL, '8007 - 6583 - 7566 - 5177', 'standard', 200.00, 147.00, '2025-11-19', '2025-12-12', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(52, NULL, '3325 - 2476 - 8059 - 8115', 'standard', 500.00, 249.00, '2025-11-13', '2025-12-16', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(53, 3, '2456 - 7686 - 1257 - 2763', 'standard', 50.00, 21.00, '2025-11-11', '2025-12-24', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(54, 12, '9346 - 1954 - 2657 - 8364', 'standard', 200.00, 50.00, '2025-10-26', '2025-12-07', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(55, NULL, '3017 - 3466 - 8884 - 1035', 'standard', 25.00, 0.00, '2025-11-01', '2025-12-16', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(56, 15, '8924 - 3816 - 3121 - 1042', 'standard', 50.00, 13.00, '2025-10-31', '2025-11-30', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(57, 6, '5046 - 2029 - 8970 - 1429', 'standard', 200.00, 84.00, '2025-10-31', '2025-11-21', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(58, 3, '2395 - 7773 - 5635 - 0157', 'standard', 100.00, 19.00, '2025-11-18', '2025-12-28', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(59, 1, '3785 - 9334 - 2458 - 2973', 'standard', 25.00, 18.00, '2025-10-20', '2025-11-04', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(60, NULL, '0832 - 9441 - 9191 - 3867', 'standard', 500.00, 252.00, '2025-10-28', '2025-12-01', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(61, 14, '3990 - 2314 - 4334 - 6521', 'standard', 50.00, 4.00, '2025-10-31', '2025-11-25', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(62, NULL, '6345 - 0545 - 5526 - 2597', 'standard', 25.00, 13.00, '2025-11-13', '2025-12-15', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(63, 7, '8009 - 6930 - 3414 - 2860', 'standard', 200.00, 64.00, '2025-10-27', '2025-11-17', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(64, NULL, '7844 - 1162 - 2793 - 7214', 'standard', 50.00, 11.00, '2025-10-26', '2025-11-22', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(65, NULL, '4127 - 2709 - 8620 - 2302', 'standard', 200.00, 31.00, '2025-11-01', '2025-11-18', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(66, NULL, '8870 - 0618 - 9975 - 3946', 'standard', 500.00, 220.00, '2025-11-12', '2025-12-15', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(67, NULL, '1999 - 2338 - 3427 - 4839', 'standard', 25.00, 23.00, '2025-11-02', '2025-12-17', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(68, NULL, '3897 - 1932 - 9738 - 0895', 'standard', 25.00, 17.00, '2025-11-09', '2025-12-01', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(69, NULL, '0586 - 6290 - 5856 - 9050', 'standard', 100.00, 27.00, '2025-10-31', '2025-11-25', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(70, NULL, '0690 - 0772 - 0798 - 2383', 'standard', 500.00, 309.00, '2025-10-23', '2025-11-19', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(71, 15, '9415 - 3887 - 7761 - 0953', 'standard', 25.00, 3.00, '2025-11-17', '2025-12-13', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(72, 11, '7008 - 4739 - 3983 - 7720', 'standard', 200.00, 53.00, '2025-10-24', '2025-11-17', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(73, NULL, '8773 - 7962 - 1105 - 7179', 'standard', 25.00, 18.00, '2025-11-15', '2025-12-03', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(74, NULL, '8484 - 8839 - 0213 - 8130', 'standard', 50.00, 14.00, '2025-10-24', '2025-11-11', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(75, 6, '6312 - 1752 - 9507 - 1659', 'standard', 100.00, 48.00, '2025-10-27', '2025-11-27', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(76, NULL, '2924 - 8958 - 4190 - 3142', 'standard', 200.00, 145.00, '2025-10-30', '2025-12-03', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(77, NULL, '6503 - 3560 - 3376 - 8660', 'standard', 100.00, 23.00, '2025-10-21', '2025-11-22', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(78, 13, '3878 - 5298 - 9910 - 8167', 'standard', 25.00, 13.00, '2025-10-31', '2025-11-18', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(79, 14, '9163 - 1288 - 3259 - 7728', 'standard', 50.00, 43.00, '2025-11-12', '2025-12-26', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(80, NULL, '7667 - 6859 - 3985 - 2215', 'standard', 25.00, 19.00, '2025-10-29', '2025-11-25', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(81, 5, '3190 - 5792 - 2166 - 4414', 'standard', 200.00, 168.00, '2025-10-30', '2025-12-02', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(82, NULL, '9812 - 0838 - 3607 - 5015', 'standard', 25.00, 2.00, '2025-10-28', '2025-12-06', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(83, 15, '1903 - 7951 - 6219 - 5071', 'standard', 100.00, 13.00, '2025-10-28', '2025-12-07', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(84, NULL, '6278 - 8714 - 7446 - 7262', 'standard', 100.00, 26.00, '2025-10-26', '2025-12-07', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(85, 11, '5954 - 1194 - 8074 - 4636', 'standard', 25.00, 0.00, '2025-11-08', '2025-12-19', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(86, 13, '6118 - 6215 - 4066 - 9388', 'standard', 25.00, 24.00, '2025-11-17', '2025-12-12', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(87, NULL, '8547 - 1222 - 0029 - 2815', 'standard', 200.00, 77.00, '2025-11-14', '2025-12-19', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(88, 13, '5853 - 6788 - 0424 - 1518', 'standard', 25.00, 16.00, '2025-10-21', '2025-11-23', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(89, 11, '2856 - 2649 - 5684 - 5196', 'standard', 500.00, 131.00, '2025-11-13', '2025-12-22', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(90, NULL, '2177 - 5190 - 9532 - 6805', 'standard', 100.00, 99.00, '2025-11-16', '2025-12-31', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(91, NULL, '1009 - 1865 - 5352 - 3406', 'standard', 100.00, 63.00, '2025-11-10', '2025-11-26', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(92, 5, '6597 - 6038 - 2614 - 5523', 'standard', 200.00, 1.00, '2025-11-11', '2025-12-05', 'expired', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(93, NULL, '2383 - 6188 - 5812 - 8823', 'standard', 200.00, 23.00, '2025-11-16', '2025-12-03', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(94, 13, '7256 - 7001 - 1797 - 6795', 'standard', 100.00, 7.00, '2025-11-16', '2025-12-30', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(95, NULL, '2151 - 4977 - 4547 - 0225', 'standard', 50.00, 42.00, '2025-10-29', '2025-11-26', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(96, 10, '2040 - 3523 - 1785 - 4346', 'standard', 100.00, 76.00, '2025-11-09', '2025-12-04', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(97, NULL, '0779 - 8465 - 0817 - 7568', 'standard', 100.00, 71.00, '2025-11-04', '2025-12-18', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(98, 5, '1850 - 0104 - 6480 - 6188', 'standard', 200.00, 169.00, '2025-10-30', '2025-11-16', 'active', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(99, 1, '3472 - 1506 - 5869 - 2369', 'standard', 500.00, 369.00, '2025-11-10', '2025-11-28', 'pending', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43'),
(100, 5, '8861 - 0915 - 8863 - 5610', 'standard', 25.00, 23.00, '2025-10-24', '2025-11-14', 'cancelled', NULL, '2025-11-19 04:32:43', '2025-11-19 04:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

DROP TABLE IF EXISTS `penalties`;
CREATE TABLE IF NOT EXISTS `penalties` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('late_payment','damage','cancellation','violation','compensation') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'late_payment',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penalties_user_id_foreign` (`user_id`),
  KEY `penalties_booking_id_foreign` (`booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penalties`
--

INSERT INTO `penalties` (`id`, `user_id`, `booking_id`, `amount`, `type`, `reason`, `status`, `paid_at`, `notes`, `created_at`, `updated_at`) VALUES
(66, 1, 12, 150.00, 'late_payment', 'تأخير الدفع عن موعد الحجز', 'pending', NULL, NULL, '2025-11-19 21:55:07', '2025-11-19 21:55:07'),
(67, 7, 27, 50.00, 'cancellation', 'إلغاء حجز من المستأجر', 'paid', '2025-11-19 22:58:39', NULL, '2025-11-19 22:56:45', '2025-11-19 22:58:39'),
(68, 6, 27, 50.00, 'compensation', 'تعويض عن إلغاء من المستأجر', 'paid', '2025-11-19 22:58:39', NULL, '2025-11-19 22:58:39', '2025-11-19 22:58:39'),
(69, 1, NULL, 150.00, 'late_payment', 'تأخير الدفع عن موعد الحجز', 'pending', NULL, NULL, '2025-11-19 22:59:59', '2025-11-19 22:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 9, 'api', '9ed1a4d8854ab95db0e8285be87f61d9268ac16b79853ef6faa6e053b4d6c12b', '[\"*\"]', '2025-11-19 03:48:42', NULL, '2025-11-18 15:00:41', '2025-11-19 03:48:42'),
(2, 'App\\Models\\User', 10, 'api', 'cf04bfeb0e02fb3d8f6e43e4584a23a82c9ac05f3a2a73ed3d4bbcdf29df4b3c', '[\"*\"]', NULL, NULL, '2025-11-19 03:33:06', '2025-11-19 03:33:06'),
(3, 'App\\Models\\User', 11, 'api', 'acb4274b8ae16b7a29d02be9c868e41607981804f0339f6ea628fa0c2c573526', '[\"*\"]', NULL, NULL, '2025-11-19 03:46:28', '2025-11-19 03:46:28'),
(4, 'App\\Models\\User', 12, 'api', '47cc6c20792420915abeff5b305b2662658026af96bb067e21c3aa90b61635f2', '[\"*\"]', '2025-11-19 04:25:38', NULL, '2025-11-19 03:53:21', '2025-11-19 04:25:38'),
(5, 'App\\Models\\User', 13, 'api', '28ed4b103b374ecb23ef62d73f52e63e9b41fdb6685b0dd3144f21c6a595e683', '[\"*\"]', NULL, NULL, '2025-11-19 03:54:24', '2025-11-19 03:54:24'),
(6, 'App\\Models\\User', 6, 'api', '2232100e9dd892884661e30c623a80c5305d1c651510040f839891a2aba40659', '[\"*\"]', '2025-11-19 22:48:39', NULL, '2025-11-19 04:22:34', '2025-11-19 22:48:39'),
(7, 'App\\Models\\User', 7, 'api', '279e43ff73b3fbbe2856c303f88d05bec0327273b3c097801b20b498bc7e667d', '[\"*\"]', '2025-11-21 03:32:44', NULL, '2025-11-19 22:48:34', '2025-11-21 03:32:44'),
(8, 'App\\Models\\User', 6, 'api', 'c7159e848f05b4278f555200f5de74f988862ce6edad40770a83702907946b91', '[\"*\"]', '2025-11-22 02:59:59', NULL, '2025-11-21 03:33:36', '2025-11-22 02:59:59'),
(9, 'App\\Models\\User', 6, 'api', 'b8b2010090ef4af42d33066e0d50ff1b13ec20bb88e1ce368d1f9a57f4afc663', '[\"*\"]', NULL, NULL, '2025-11-22 03:01:11', '2025-11-22 03:01:11'),
(10, 'App\\Models\\User', 7, 'api', '6d42094095309846c585589bd16887e3d00081a984bcc19877e9f4301f1cce8c', '[\"*\"]', '2025-11-22 03:08:20', NULL, '2025-11-22 03:01:40', '2025-11-22 03:08:20');

-- --------------------------------------------------------

--
-- Table structure for table `points_transactions`
--

DROP TABLE IF EXISTS `points_transactions`;
CREATE TABLE IF NOT EXISTS `points_transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `points` bigint NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `points_transactions_wallet_id_foreign` (`wallet_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `points_transactions`
--

INSERT INTO `points_transactions` (`id`, `wallet_id`, `points`, `type`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 25, 'earn', 'من مكافآت النقاط', '2025-03-27 12:40:00', '2025-03-27 12:40:00'),
(2, 1, 5, 'earn', 'حجز مكتمل', '2025-11-09 19:23:17', '2025-11-09 19:23:17'),
(3, 1, 10, 'earn', 'عرض ترويجي', '2025-11-09 19:23:17', '2025-11-19 04:32:42'),
(4, 2, 200, 'earn', 'نقاط ترحيب', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(5, 2, -50, 'spend', 'استبدال نقاط', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(6, 3, 200, 'earn', 'نقاط ترحيب', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(7, 3, -50, 'spend', 'استبدال نقاط', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(8, 4, 200, 'earn', 'نقاط ترحيب', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(9, 4, -50, 'spend', 'استبدال نقاط', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(10, 5, 200, 'earn', 'نقاط ترحيب', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(11, 5, -50, 'spend', 'استبدال نقاط', '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(12, 1, 25, 'earn', 'من مكافآت النقاط', '2025-03-27 12:40:00', '2025-03-27 12:40:00'),
(13, 7, 300, 'earn', 'Referral', '2025-11-19 03:33:06', '2025-11-19 03:33:06'),
(14, 7, 300, 'earn', 'Referral', '2025-11-19 03:35:54', '2025-11-19 03:35:54'),
(15, 7, 500, 'redeem', 'Points to money', '2025-11-19 03:38:16', '2025-11-19 03:38:16'),
(16, 7, 300, 'earn', 'Referral', '2025-11-19 03:42:39', '2025-11-19 03:42:39'),
(17, 7, 300, 'earn', 'Referral', '2025-11-19 03:42:42', '2025-11-19 03:42:42'),
(18, 7, 300, 'earn', 'Referral', '2025-11-19 03:43:11', '2025-11-19 03:43:11'),
(19, 7, 300, 'earn', 'Referral', '2025-11-19 03:45:22', '2025-11-19 03:45:22'),
(20, 7, 300, 'earn', 'Referral:11:FP:f86a1493b72217d34b025d08f5baf073', '2025-11-19 03:46:28', '2025-11-19 03:46:28'),
(21, 7, 1000, 'redeem', 'Points to money', '2025-11-19 03:47:15', '2025-11-19 03:47:15'),
(22, 7, 500, 'redeem', 'Points to money', '2025-11-19 03:47:30', '2025-11-19 03:47:30'),
(23, 8, 300, 'earn', 'Referral:13:FP:f86a1493b72217d34b025d08f5baf073', '2025-11-19 03:54:24', '2025-11-19 03:54:24'),
(24, 8, 300, 'earn', 'Referral', '2025-11-19 03:55:18', '2025-11-19 03:55:18'),
(25, 8, 300, 'earn', 'Referral', '2025-11-19 03:55:21', '2025-11-19 03:55:21'),
(26, 8, 300, 'earn', 'Referral', '2025-11-19 04:01:01', '2025-11-19 04:01:01'),
(27, 8, 1000, 'redeem', 'Points to money', '2025-11-19 04:01:05', '2025-11-19 04:01:05'),
(28, 8, 300, 'earn', 'Referral', '2025-11-19 04:04:40', '2025-11-19 04:04:40'),
(29, 8, 300, 'earn', 'Referral', '2025-11-19 04:04:41', '2025-11-19 04:04:41'),
(30, 8, 300, 'earn', 'Referral', '2025-11-19 04:04:42', '2025-11-19 04:04:42'),
(31, 8, 1000, 'redeem', 'Points to money', '2025-11-19 04:04:48', '2025-11-19 04:04:48'),
(32, 8, 300, 'earn', 'Referral', '2025-11-19 04:17:31', '2025-11-19 04:17:31'),
(33, 8, 300, 'earn', 'Referral', '2025-11-19 04:17:33', '2025-11-19 04:17:33'),
(34, 8, 300, 'earn', 'Referral', '2025-11-19 04:17:33', '2025-11-19 04:17:33'),
(35, 8, 1000, 'redeem', 'Points to money', '2025-11-19 04:17:38', '2025-11-19 04:17:38'),
(36, 1, 25, 'earn', 'من مكافآت النقاط', '2025-03-27 12:40:00', '2025-03-27 12:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `property_type_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rental_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` smallint UNSIGNED DEFAULT NULL,
  `bedrooms` tinyint UNSIGNED DEFAULT NULL,
  `bathrooms` tinyint UNSIGNED DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `keywords` json DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `deactivation_reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deactivated_at` timestamp NULL DEFAULT NULL,
  `deactivated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `properties_user_id_foreign` (`user_id`),
  KEY `properties_property_type_id_foreign` (`property_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `user_id`, `property_type_id`, `title`, `city`, `address`, `rental_type`, `capacity`, `bedrooms`, `bathrooms`, `price`, `description`, `keywords`, `image`, `approved`, `deactivation_reason`, `deactivated_at`, `deactivated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 4, 'شقة فاخرة بالقرب من وسط المدينة', 'طرابلس', 'طرابلس - حي مركزي', 'يومي', 6, 3, 2, 20.00, 'شقة نظيفة ومُرتبة وقريبة من جميع الخدمات.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(2, 2, 1, 'استوديو حديث بإطلالة جميلة', 'بنغازي', 'بنغازي - حي مركزي', 'يومي', 6, 3, 2, 25.00, 'استوديو مناسب للإقامة القصيرة، مجهز بالكامل.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(3, 2, 1, 'منزل عائلي واسع', 'مصراتة', 'مصراتة - حي مركزي', 'يومي', 6, 3, 2, 30.00, 'منزل مناسب للعائلات الكبيرة مع مساحة خارجية.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(4, 1, 1, 'شقة فاخرة بالقرب من وسط المدينة', 'طرابلس', 'طرابلس - حي مركزي', 'يومي', 6, 3, 2, 20.00, 'شقة نظيفة ومُرتبة وقريبة من جميع الخدمات.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(5, 1, 2, 'استوديو حديث بإطلالة جميلة', 'بنغازي', 'بنغازي - حي مركزي', 'يومي', 6, 3, 2, 25.00, 'استوديو مناسب للإقامة القصيرة، مجهز بالكامل.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(6, 1, 3, 'منزل عائلي واسع', 'مصراتة', 'مصراتة - حي مركزي', 'يومي', 6, 3, 2, 30.00, 'منزل مناسب للعائلات الكبيرة مع مساحة خارجية.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(7, 1, 4, 'سكن عملي لرحلات العمل', 'سبها', 'سبها - حي مركزي', 'يومي', 6, 3, 2, 18.00, 'مناسب لرحلات العمل القصيرة.', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-09 19:23:17', '2025-11-09 19:23:18'),
(8, 5, 1, 'شقة حديثة في طرابلس', 'طرابلس', 'طرابلس - حي مركزي', 'يومي', 6, 3, 2, 25.00, 'شقة مجهزة بالكامل وقريبة من الخدمات.', NULL, 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&h=500&q=60', 1, NULL, NULL, NULL, '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(9, 5, 3, 'استوديو مريح بإطلالة جميلة', 'طرابلس', 'طرابلس - حي مركزي', 'يومي', 6, 3, 2, 18.50, 'مناسب للإقامات القصيرة مع واي فاي سريع.', NULL, 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&h=500&q=60', 1, NULL, NULL, NULL, '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(10, 6, 4, 'شقة حديثة في بنغازي', 'بنغازي', 'بنغازي - حي مركزي', 'يومي', 6, 3, 2, 25.00, 'شقة مجهزة بالكامل وقريبة من الخدمات.', NULL, 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&h=500&q=60', 1, NULL, NULL, NULL, '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(11, 6, 4, 'استوديو مريح بإطلالة جميلة', 'بنغازي', 'بنغازي - حي مركزي', 'يومي', 6, 3, 2, 18.50, 'مناسب للإقامات القصيرة مع واي فاي سريع.', NULL, 'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=800&h=500&q=60', 1, NULL, NULL, NULL, '2025-11-09 19:23:18', '2025-11-09 19:23:18'),
(12, 15, 1, 'غرفة اختبار عمولة', 'طرابلس', 'طرابلس - حي مركزي', 'يومي', 6, 3, 2, 100.00, 'غرفة لاختبار إظهار إجمالي العمولات.', NULL, 'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=800&h=500&q=60', 1, NULL, NULL, NULL, '2025-11-19 04:32:31', '2025-11-19 04:32:43'),
(13, 17, NULL, 'غرفة نسبة 30% - A1', 'طرابلس', NULL, NULL, NULL, NULL, NULL, 80.00, 'نسبة 30%', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-19 04:35:00', '2025-11-19 04:35:00'),
(14, 17, NULL, 'غرفة نسبة 30% - A2', 'طرابلس', NULL, NULL, NULL, NULL, NULL, 120.00, 'نسبة 30%', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-19 04:35:00', '2025-11-19 04:35:00'),
(15, 17, NULL, 'غرفة قيمة ثابتة 15 - B1', 'طرابلس', NULL, NULL, NULL, NULL, NULL, 50.00, 'قيمة ثابتة', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-19 04:35:00', '2025-11-19 04:35:00'),
(16, 17, NULL, 'غرفة قيمة ثابتة 15 - B2', 'طرابلس', NULL, NULL, NULL, NULL, NULL, 200.00, 'قيمة ثابتة', NULL, NULL, 1, NULL, NULL, NULL, '2025-11-19 04:35:00', '2025-11-19 04:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

DROP TABLE IF EXISTS `property_images`;
CREATE TABLE IF NOT EXISTS `property_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` bigint UNSIGNED NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_images_property_id_foreign` (`property_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property_types`
--

DROP TABLE IF EXISTS `property_types`;
CREATE TABLE IF NOT EXISTS `property_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rental_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'شهري',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_types`
--

INSERT INTO `property_types` (`id`, `name`, `rental_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'شقة', 'شهري', 1, '2025-11-09 19:23:18', '2025-11-13 17:08:12'),
(2, 'فيلا', 'شهري', 1, '2025-11-09 19:23:18', '2025-11-13 17:08:12'),
(3, 'شاليه', 'شهري', 1, '2025-11-09 19:23:18', '2025-11-13 17:08:12'),
(4, 'استوديو', 'شهري', 1, '2025-11-09 19:23:18', '2025-11-13 17:08:12');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `request_type` enum('bank','wallet','cash') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank',
  `amount` decimal(10,2) NOT NULL,
  `account_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'investor',
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_holder` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refunds_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`id`, `user_id`, `request_type`, `amount`, `account_type`, `account_number`, `account_holder`, `bank_name`, `status`, `notes`, `admin_notes`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'bank', 150.00, 'مستأجر', '9274639463-32084', 'Jane Cooper', 'مصرف الجمهورية', 'approved', NULL, NULL, '2025-11-19 04:32:41', '2025-11-16 13:30:14', '2025-11-19 04:32:41'),
(2, 1, 'wallet', 150.00, 'مستأجر', '+218 94 548 8765', 'Jane Cooper', 'G-Pay', 'pending', NULL, NULL, NULL, '2025-11-16 13:30:14', '2025-11-16 13:30:14'),
(3, 1, 'wallet', 150.00, 'مستأجر', '+218 91 548 8765', 'Jane Cooper', 'المدار الجديد', 'approved', NULL, NULL, '2025-11-18 13:49:55', '2025-11-16 13:30:14', '2025-11-18 13:49:55'),
(4, 1, 'wallet', 150.00, 'مستأجر', '+218 94 548 8765', 'Jane Cooper', 'G-Pay', 'approved', NULL, NULL, '2025-11-19 04:32:41', '2025-11-16 13:30:14', '2025-11-19 04:32:41'),
(5, 1, 'wallet', 150.00, 'مستأجر', '+218 91 548 8765', 'Jane Cooper', 'المدار الجديد', 'rejected', NULL, NULL, '2025-11-19 04:32:41', '2025-11-16 13:30:14', '2025-11-19 04:32:41'),
(6, 1, 'bank', 150.00, 'مستأجر', '9274639463-32084', 'Jane Cooper', 'مصرف الجمهورية', 'approved', NULL, NULL, '2025-11-16 13:30:14', '2025-11-16 13:30:14', '2025-11-16 13:30:14'),
(7, 9, 'bank', 150.00, 'مؤجر', '9274639463-32084', NULL, 'مصرف الجمهورية', 'approved', NULL, NULL, '2025-11-18 15:07:08', '2025-11-18 15:02:41', '2025-11-18 15:07:08'),
(8, 9, 'bank', 150.00, 'مؤجر', '9274639463-32084', NULL, 'مصرف الجمهورية', 'approved', NULL, NULL, '2025-11-19 01:52:20', '2025-11-19 01:51:59', '2025-11-19 01:52:20'),
(9, 1, 'bank', 150.00, 'مستأجر', '9274639463-32084', 'Jane Cooper', 'مصرف الصداري', 'pending', NULL, NULL, NULL, '2025-11-19 04:32:41', '2025-11-19 04:32:41'),
(10, 1, 'wallet', 150.00, 'مستأجر', '+218 91 548 8765', 'Jane Cooper', 'المدار الجديد', 'pending', NULL, NULL, NULL, '2025-11-19 04:32:41', '2025-11-19 04:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `reviewed_user_id` bigint UNSIGNED NOT NULL,
  `reviewer_user_id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint UNSIGNED NOT NULL DEFAULT '5',
  `property_care` decimal(2,1) DEFAULT NULL,
  `cleanliness` decimal(2,1) DEFAULT NULL,
  `rules_compliance` decimal(2,1) DEFAULT NULL,
  `timely_delivery` decimal(2,1) DEFAULT NULL,
  `inquiry_response` decimal(3,1) DEFAULT NULL,
  `booking_acceptance_speed` decimal(3,1) DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_reviewed_user_id_foreign` (`reviewed_user_id`),
  KEY `reviews_reviewer_user_id_foreign` (`reviewer_user_id`),
  KEY `reviews_booking_id_foreign` (`booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `reviewed_user_id`, `reviewer_user_id`, `booking_id`, `rating`, `property_care`, `cleanliness`, `rules_compliance`, `timely_delivery`, `inquiry_response`, `booking_acceptance_speed`, `comment`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 16, 6, NULL, 5, NULL, NULL, NULL, 4.8, 4.8, 4.7, 'مستأجر ملتزم ونظيف', NULL, NULL, '2025-11-22 02:51:13', '2025-11-22 02:51:13'),
(2, 15, 7, NULL, 4, 4.0, 4.0, 5.0, 4.0, NULL, NULL, 'المؤجر متعاون', NULL, NULL, '2025-11-22 03:02:03', '2025-11-22 03:02:03'),
(3, 17, 7, NULL, 4, 4.0, 4.0, 5.0, 4.0, NULL, NULL, 'المؤجر متعاون', NULL, NULL, '2025-11-22 03:02:37', '2025-11-22 03:02:37');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-11-09 19:23:16', '2025-11-09 19:23:16'),
(2, 'landlord', 'web', '2025-11-09 19:23:16', '2025-11-09 19:23:16');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'commission_percentage', '25', '2025-11-10 14:08:14', '2025-11-19 04:35:00'),
(2, 'commission_calculation_method', 'fixed', '2025-11-10 14:08:14', '2025-11-19 04:45:01'),
(3, 'points_enabled', '1', '2025-11-10 14:08:14', '2025-11-19 01:48:59'),
(4, 'points_per_transaction', '300', '2025-11-10 14:08:14', '2025-11-19 01:49:27'),
(5, 'min_points_conversion', '5', '2025-11-10 14:08:14', '2025-11-10 14:08:14'),
(6, 'commission_fixed_value', '60', '2025-11-19 00:59:39', '2025-11-19 04:35:00'),
(7, 'points_per_dinar', '100', '2025-11-19 00:59:39', '2025-11-19 00:59:39'),
(8, 'compensation_method', 'full', '2025-11-19 19:11:37', '2025-11-19 22:24:22'),
(9, 'cancel_penalty_method', 'fixed', '2025-11-19 19:11:40', '2025-11-19 22:19:10'),
(10, 'cancel_penalty_percentage', '0', '2025-11-19 19:11:45', '2025-11-19 22:24:13'),
(11, 'compensation_fixed_extra', '0', '2025-11-19 19:11:59', '2025-11-19 22:24:16'),
(12, 'cancel_penalty_fixed_value', '50', '2025-11-19 22:06:12', '2025-11-19 22:20:45'),
(13, 'compensation_percentage', '0', '2025-11-19 22:06:30', '2025-11-19 22:20:51');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE IF NOT EXISTS `support_tickets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `submitted_by` enum('landlord','tenant') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_replied_at` timestamp NULL DEFAULT NULL,
  `admin_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `booking_id` bigint UNSIGNED DEFAULT NULL,
  `property_id` bigint UNSIGNED DEFAULT NULL,
  `landlord_id` bigint UNSIGNED DEFAULT NULL,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  KEY `support_tickets_assigned_to_foreign` (`assigned_to`),
  KEY `support_tickets_booking_id_foreign` (`booking_id`),
  KEY `support_tickets_property_id_foreign` (`property_id`),
  KEY `support_tickets_landlord_id_foreign` (`landlord_id`),
  KEY `support_tickets_tenant_id_foreign` (`tenant_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `submitted_by`, `subject`, `description`, `status`, `priority`, `assigned_to`, `category`, `last_replied_at`, `admin_read_at`, `created_at`, `updated_at`, `booking_id`, `property_id`, `landlord_id`, `tenant_id`) VALUES
(1, 3, NULL, 'مشكلة في عملية الدفع', 'أواجه صعوبة في إتمام عملية الدفع عبر بطاقة الائتمان. الرجاء المساعدة في حل هذه المشكلة في أقرب وقت ممكن.', 'open', 'high', 1, 'الدفع والمعاملات المالية', '2025-11-21 03:29:15', '2025-11-21 05:59:48', '2025-11-09 19:23:26', '2025-11-21 05:59:48', NULL, NULL, NULL, NULL),
(2, 3, NULL, 'استفسار عن إلغاء الحجز', 'أود الاستفسار عن سياسة إلغاء الحجز وكيفية استرداد المبلغ المدفوع.', 'in_progress', 'medium', 1, 'الحجوزات', '2025-11-21 02:40:03', NULL, '2025-11-09 19:23:26', '2025-11-21 02:40:03', NULL, NULL, NULL, NULL),
(3, 3, NULL, 'تحديث بيانات الملف الشخصي', 'لا أستطيع تحديث بيانات الملف الشخصي الخاص بي. الرجاء المساعدة.', 'closed', 'low', 1, 'الحساب والملف الشخصي', '2025-11-08 23:23:26', '2025-11-21 05:59:49', '2025-11-09 19:23:26', '2025-11-21 05:59:49', NULL, NULL, NULL, NULL),
(4, 3, NULL, 'شكوى بخصوص العقار', 'العقار الذي حجزته لا يطابق الوصف والصور المعروضة على المنصة. هناك العديد من المشاكل التي لم تذكر في الإعلان.', 'closed', 'urgent', 1, 'شكاوى العقارات', NULL, NULL, '2025-11-09 19:23:26', '2025-11-21 03:52:08', NULL, NULL, NULL, NULL),
(5, 8, NULL, 'مشكلة تقنية في التطبيق', 'التطبيق يتوقف عن العمل بشكل متكرر عند محاولة تصفح العقارات المتاحة.', 'in_progress', 'high', 1, 'المشاكل التقنية', '2025-11-08 12:23:26', NULL, '2025-11-09 19:23:26', '2025-11-09 19:23:26', NULL, NULL, NULL, NULL),
(6, 6, NULL, 'طلب فاتورة', 'أحتاج إلى فاتورة رسمية للحجز رقم #12345 لأغراض المحاسبة.', 'closed', 'low', 1, 'الفواتير والإيصالات', '2025-11-08 04:23:26', NULL, '2025-11-09 19:23:26', '2025-11-09 19:23:26', NULL, NULL, NULL, NULL),
(7, 3, NULL, 'استفسار عن برنامج الولاء', 'كيف يمكنني الاستفادة من نقاط المكافآت في برنامج الولاء؟', 'closed', 'low', 1, 'برامج المكافآت', '2025-11-08 04:23:26', NULL, '2025-11-09 19:23:26', '2025-11-21 04:04:43', NULL, NULL, NULL, NULL),
(8, 3, NULL, 'مشكلة في التواصل مع المؤجر', 'حاولت التواصل مع المؤجر عدة مرات لكن لم أحصل على أي رد.', 'closed', 'medium', 1, 'التواصل', NULL, NULL, '2025-11-09 19:23:26', '2025-11-21 03:54:31', NULL, NULL, NULL, NULL),
(9, 7, 'tenant', 'طلب تعويض عن اتلاف جهاز تلفزيون', 'تم اتلاف جهاز التلفزيون خلال فترة الحجز...', 'open', 'high', NULL, 'إتلاف الممتلكات', NULL, '2025-11-21 05:59:53', '2025-11-21 03:12:36', '2025-11-21 05:59:53', 1, 1, 2, 7),
(10, 7, 'tenant', 'المكان لم يكن نظيف عند الاستلام للايجار', 'المكان لم يكن نظيف من قبل المالك عند الايجار للمستاجر', 'open', 'high', NULL, 'نظافة المكان', '2025-11-21 03:28:44', '2025-11-21 05:59:46', '2025-11-21 03:22:22', '2025-11-21 05:59:46', 10, 10, 6, 7),
(11, 6, 'landlord', 'المستاجر كسر الشقة', 'قام المستاجر بكسر الاثاث و الغسالة و التلاجة و البوتجاز', 'open', 'high', NULL, 'تخريب الممتلكات', NULL, '2025-11-21 05:59:45', '2025-11-21 03:36:16', '2025-11-21 05:59:45', 10, 10, 6, 7);

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_replies`
--

DROP TABLE IF EXISTS `support_ticket_replies`;
CREATE TABLE IF NOT EXISTS `support_ticket_replies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin_reply` tinyint(1) NOT NULL DEFAULT '0',
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_ticket_replies_ticket_id_foreign` (`ticket_id`),
  KEY `support_ticket_replies_user_id_foreign` (`user_id`),
  KEY `support_ticket_replies_admin_id_foreign` (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `support_ticket_replies`
--

INSERT INTO `support_ticket_replies` (`id`, `ticket_id`, `user_id`, `message`, `is_admin_reply`, `attachments`, `created_at`, `updated_at`, `admin_id`) VALUES
(1, 2, 3, 'شكراً لكم على الاهتمام. أود إضافة بعض التفاصيل الإضافية حول المشكلة...', 0, NULL, '2025-11-08 01:23:26', '2025-11-09 19:23:26', NULL),
(2, 2, 1, 'شكراً لتواصلك معنا. نحن نعمل على حل المشكلة وسنقوم بإبلاغك بالتحديثات قريباً.', 1, NULL, '2025-11-09 03:23:26', '2025-11-09 19:23:26', NULL),
(3, 3, 3, 'شكراً لكم على الاهتمام. أود إضافة بعض التفاصيل الإضافية حول المشكلة...', 0, NULL, '2025-11-08 08:23:26', '2025-11-09 19:23:26', NULL),
(4, 3, 1, 'شكراً لتواصلك معنا. نحن نعمل على حل المشكلة وسنقوم بإبلاغك بالتحديثات قريباً.', 1, NULL, '2025-11-09 00:23:26', '2025-11-09 19:23:26', NULL),
(5, 3, 1, 'تم حل المشكلة بنجاح. نتمنى أن تكون الخدمة قد أرضتكم.', 1, NULL, '2025-11-09 09:23:26', '2025-11-09 19:23:26', NULL),
(6, 5, 8, 'شكراً لكم على الاهتمام. أود إضافة بعض التفاصيل الإضافية حول المشكلة...', 0, NULL, '2025-11-08 13:23:26', '2025-11-09 19:23:26', NULL),
(7, 5, 1, 'شكراً لتواصلك معنا. نحن نعمل على حل المشكلة وسنقوم بإبلاغك بالتحديثات قريباً.', 1, NULL, '2025-11-09 00:23:26', '2025-11-09 19:23:26', NULL),
(8, 6, 6, 'شكراً لكم على الاهتمام. أود إضافة بعض التفاصيل الإضافية حول المشكلة...', 0, NULL, '2025-11-08 02:23:26', '2025-11-09 19:23:26', NULL),
(9, 6, 1, 'شكراً لتواصلك معنا. نحن نعمل على حل المشكلة وسنقوم بإبلاغك بالتحديثات قريباً.', 1, NULL, '2025-11-09 05:23:26', '2025-11-09 19:23:26', NULL),
(10, 6, 1, 'تم حل المشكلة بنجاح. نتمنى أن تكون الخدمة قد أرضتكم.', 1, NULL, '2025-11-09 12:23:26', '2025-11-09 19:23:26', NULL),
(11, 7, 3, 'شكراً لكم على الاهتمام. أود إضافة بعض التفاصيل الإضافية حول المشكلة...', 0, NULL, '2025-11-08 04:23:26', '2025-11-09 19:23:26', NULL),
(12, 7, 1, 'شكراً لتواصلك معنا. نحن نعمل على حل المشكلة وسنقوم بإبلاغك بالتحديثات قريباً.', 1, NULL, '2025-11-09 04:23:26', '2025-11-09 19:23:26', NULL),
(13, 7, 1, 'تم حل المشكلة بنجاح. نتمنى أن تكون الخدمة قد أرضتكم.', 1, NULL, '2025-11-09 12:23:26', '2025-11-09 19:23:26', NULL),
(14, 2, 1, 'hhhhhhhh', 1, NULL, '2025-11-21 02:39:56', '2025-11-21 02:39:56', NULL),
(15, 2, 1, 'hhhh', 1, NULL, '2025-11-21 02:40:03', '2025-11-21 02:40:03', NULL),
(16, 10, 1, 'حسنا', 1, NULL, '2025-11-21 03:23:36', '2025-11-21 03:23:36', NULL),
(17, 10, 1, 'لوك', 1, NULL, '2025-11-21 03:27:04', '2025-11-21 03:27:04', NULL),
(18, 10, 7, 'نعم', 1, NULL, '2025-11-21 03:28:44', '2025-11-21 03:28:44', 1),
(19, 1, 7, 'تفاصيل إضافية حول المشكلة...', 0, NULL, '2025-11-21 03:29:15', '2025-11-21 03:29:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `suspended_users`
--

DROP TABLE IF EXISTS `suspended_users`;
CREATE TABLE IF NOT EXISTS `suspended_users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'suspended',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suspended_users_user_id_foreign` (`user_id`),
  KEY `suspended_users_admin_id_foreign` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `booking_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_wallet_id_foreign` (`wallet_id`),
  KEY `transactions_booking_id_foreign` (`booking_id`),
  KEY `transactions_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `wallet_id`, `amount`, `type`, `status`, `meta`, `created_at`, `updated_at`, `booking_id`) VALUES
(1, 1, 1, 291.20, 'deposit', 'completed', '{\"days\": 16, \"total\": 320, \"commission\": 28.8, \"daily_price\": \"20.00\", \"commission_rate\": 0.09}', '2025-03-27 12:35:00', '2025-03-27 12:35:00', 4),
(2, 1, 1, 2350.00, 'deposit', 'completed', '{\"days\": 5, \"total\": 2585, \"commission\": 233, \"daily_price\": 517, \"room_charge\": 2585, \"commission_rate\": 0.09}', '2025-03-27 12:35:00', '2025-03-27 12:35:00', 8),
(3, 1, 1, 125.00, 'withdraw', 'completed', '{\"note\": \"طلب سحب تم الموافقة عليه\"}', '2025-11-09 19:23:17', '2025-11-09 19:23:17', NULL),
(4, 5, 2, 1000.00, 'credit', 'completed', '{\"reason\": \"إيداع أولي\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(5, 5, 2, -200.00, 'payment', 'completed', '{\"reason\": \"دفع حجز\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(6, 5, 2, 300.00, 'credit', 'completed', '{\"reason\": \"مكافأة\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(7, 6, 3, 1000.00, 'credit', 'completed', '{\"reason\": \"إيداع أولي\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(8, 6, 3, -200.00, 'payment', 'completed', '{\"reason\": \"دفع حجز\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(9, 6, 3, 300.00, 'credit', 'completed', '{\"reason\": \"مكافأة\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(10, 7, 4, 1000.00, 'credit', 'completed', '{\"reason\": \"إيداع أولي\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(11, 7, 4, -200.00, 'payment', 'completed', '{\"reason\": \"دفع حجز\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(12, 7, 4, 300.00, 'credit', 'completed', '{\"reason\": \"مكافأة\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(13, 8, 5, 1000.00, 'credit', 'completed', '{\"reason\": \"إيداع أولي\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(14, 8, 5, -200.00, 'payment', 'completed', '{\"reason\": \"دفع حجز\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(15, 8, 5, 300.00, 'credit', 'completed', '{\"reason\": \"مكافأة\"}', '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(16, 5, 2, 25.00, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 8}', '2025-11-09 19:23:18', '2025-11-09 19:23:18', 9),
(17, 6, 3, 25.00, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 10}', '2025-11-09 19:23:18', '2025-11-09 19:23:18', 10),
(18, 5, 2, 18.50, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 9}', '2025-11-09 19:23:18', '2025-11-09 19:23:18', 11),
(19, 6, 3, 18.50, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 11}', '2025-11-09 19:23:18', '2025-11-09 19:23:18', 12),
(20, 1, 1, 291.20, 'deposit', 'completed', '{\"days\": 16, \"total\": 320, \"commission\": 28.8, \"daily_price\": \"20.00\", \"commission_rate\": 0.09}', '2025-03-27 12:35:00', '2025-03-27 12:35:00', 4),
(21, 1, 1, 2350.00, 'deposit', 'completed', '{\"days\": 5, \"total\": 2585, \"commission\": 233, \"daily_price\": 517, \"room_charge\": 2585, \"commission_rate\": 0.09}', '2025-03-27 12:35:00', '2025-03-27 12:35:00', 8),
(22, 1, 1, 125.00, 'withdraw', 'completed', '{\"note\": \"طلب سحب تم الموافقة عليه\"}', '2025-11-15 17:42:58', '2025-11-15 17:42:58', NULL),
(23, 5, 2, 25.00, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 8}', '2025-11-15 17:42:59', '2025-11-15 17:42:59', 13),
(24, 6, 3, 25.00, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 10}', '2025-11-15 17:42:59', '2025-11-15 17:42:59', 14),
(25, 5, 2, 18.50, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 9}', '2025-11-15 17:42:59', '2025-11-15 17:42:59', 15),
(26, 6, 3, 18.50, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 11}', '2025-11-15 17:42:59', '2025-11-15 17:42:59', 16),
(27, 9, 7, 150.00, 'deposit', 'completed', '{\"note\": \"إيداع تجريبي\", \"property_id\": \"11\"}', '2025-11-18 15:02:30', '2025-11-18 15:02:30', 12),
(28, 9, 7, 5.00, 'deposit', 'completed', '{\"reason\": \"تحويل نقاط إلى رصيد\", \"points_spent\": 500, \"points_per_dinar\": 100, \"min_points_conversion_dinar\": 5}', '2025-11-19 03:38:16', '2025-11-19 03:38:16', NULL),
(29, 9, 7, 10.00, 'deposit', 'completed', '{\"reason\": \"تحويل نقاط إلى رصيد\", \"points_spent\": 1000, \"points_per_dinar\": 100, \"min_points_conversion_dinar\": 5}', '2025-11-19 03:47:15', '2025-11-19 03:47:15', NULL),
(30, 9, 7, 5.00, 'deposit', 'completed', '{\"reason\": \"تحويل نقاط إلى رصيد\", \"points_spent\": 500, \"points_per_dinar\": 100, \"min_points_conversion_dinar\": 5}', '2025-11-19 03:47:30', '2025-11-19 03:47:30', NULL),
(31, 12, 8, 10.00, 'deposit', 'completed', '{\"reason\": \"تحويل نقاط إلى رصيد\", \"points_spent\": 1000, \"points_per_dinar\": 100, \"min_points_conversion_dinar\": 5}', '2025-11-19 04:01:05', '2025-11-19 04:01:05', NULL),
(32, 1, 8, 10.00, 'deposit', 'completed', '{\"reason\": \"تحويل نقاط إلى رصيد\", \"points_spent\": 1000, \"points_per_dinar\": 100, \"min_points_conversion_dinar\": 5}', '2025-11-19 04:04:48', '2025-11-19 04:04:48', NULL),
(33, 12, 8, 10.00, 'deposit', 'completed', '{\"reason\": \"تحويل نقاط إلى رصيد\", \"points_spent\": 1000, \"points_per_dinar\": 100, \"min_points_conversion_dinar\": 5}', '2025-11-19 04:17:38', '2025-11-19 04:17:38', NULL),
(34, 15, 9, 240.00, 'payment', 'completed', '{\"days\": 3, \"total\": 300, \"reason\": \"تحصيل حجز مؤكد (Seeder)\", \"tenant_id\": 14, \"commission\": 60, \"daily_price\": 100, \"property_id\": 12, \"net_to_wallet\": 240, \"commission_rate\": null, \"commission_value\": 60, \"commission_method\": \"fixed\"}', '2025-11-19 04:32:31', '2025-11-19 04:32:31', 18),
(35, NULL, 1, 291.20, 'deposit', 'completed', '{\"days\": 16, \"total\": 320, \"commission\": 28.8, \"daily_price\": \"20.00\", \"commission_rate\": 0.09}', '2025-03-27 12:35:00', '2025-03-27 12:35:00', 4),
(36, NULL, 1, 2350.00, 'deposit', 'completed', '{\"days\": 5, \"total\": 2585, \"commission\": 233, \"daily_price\": 517, \"room_charge\": 2585, \"commission_rate\": 0.09}', '2025-03-27 12:35:00', '2025-03-27 12:35:00', 8),
(37, NULL, 1, 125.00, 'withdraw', 'completed', '{\"note\": \"طلب سحب تم الموافقة عليه\"}', '2025-11-19 04:32:42', '2025-11-19 04:32:42', NULL),
(38, 5, 2, 25.00, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 8}', '2025-11-19 04:32:43', '2025-11-19 04:32:43', 19),
(39, 6, 3, 25.00, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 10}', '2025-11-19 04:32:43', '2025-11-19 04:32:43', 20),
(40, 5, 2, 18.50, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 9}', '2025-11-19 04:32:43', '2025-11-19 04:32:43', 21),
(41, 6, 3, 18.50, 'credit', 'completed', '{\"reason\": \"تحصيل حجز\", \"property_id\": 11}', '2025-11-19 04:32:43', '2025-11-19 04:32:43', 22),
(42, 17, 10, 448.00, 'payment', 'completed', '{\"days\": 8, \"total\": 640, \"reason\": \"تحصيل حجز مؤكد (MultiSeeder)\", \"tenant_id\": 16, \"commission\": 192, \"daily_price\": 80, \"property_id\": 13, \"net_to_wallet\": 448, \"commission_rate\": 0.3, \"commission_value\": null, \"commission_method\": \"percentage\"}', '2025-11-19 04:35:00', '2025-11-19 04:35:00', 23),
(43, 17, 10, 168.00, 'payment', 'completed', '{\"days\": 2, \"total\": 240, \"reason\": \"تحصيل حجز مؤكد (MultiSeeder)\", \"tenant_id\": 16, \"commission\": 72, \"daily_price\": 120, \"property_id\": 14, \"net_to_wallet\": 168, \"commission_rate\": 0.3, \"commission_value\": null, \"commission_method\": \"percentage\"}', '2025-11-19 04:35:00', '2025-11-19 04:35:00', 24),
(44, 17, 10, 85.00, 'payment', 'completed', '{\"days\": 2, \"total\": 100, \"reason\": \"تحصيل حجز مؤكد (MultiSeeder)\", \"tenant_id\": 16, \"commission\": 15, \"daily_price\": 50, \"property_id\": 15, \"net_to_wallet\": 85, \"commission_rate\": null, \"commission_value\": 15, \"commission_method\": \"fixed\"}', '2025-11-19 04:35:00', '2025-11-19 04:35:00', 25),
(45, 17, 10, 985.00, 'payment', 'completed', '{\"days\": 5, \"total\": 1000, \"reason\": \"تحصيل حجز مؤكد (MultiSeeder)\", \"tenant_id\": 16, \"commission\": 15, \"daily_price\": 200, \"property_id\": 16, \"net_to_wallet\": 985, \"commission_rate\": null, \"commission_value\": 15, \"commission_method\": \"fixed\"}', '2025-11-19 04:35:00', '2025-11-19 04:35:00', 26),
(46, 6, 3, 50.00, 'credit', 'completed', '{\"reason\": \"تعويض\", \"compensation_method\": \"full\"}', '2025-11-19 22:58:39', '2025-11-19 22:58:39', 27),
(47, 7, 4, -50.00, 'payment', 'completed', '{\"reason\": \"خصم مقابل تعويض\", \"compensation_method\": \"full\"}', '2025-11-19 22:58:39', '2025-11-19 22:58:39', 27);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('tenant','landlord','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tenant',
  `id_verified` tinyint(1) NOT NULL DEFAULT '0',
  `face_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_influencer` tinyint(1) NOT NULL DEFAULT '0',
  `needs_renewal` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','suspended','banned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `job` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_pet` tinyint(1) NOT NULL DEFAULT '0',
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `reviews_count` int NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wallet_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `avatar`, `user_type`, `id_verified`, `face_verified`, `is_influencer`, `needs_renewal`, `status`, `job`, `city`, `has_pet`, `rating`, `reviews_count`, `remember_token`, `wallet_balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'أسلام محي الدين', 'tenant@example.com', NULL, NULL, '$2y$12$Lf0cig/wEl2a6HcQk00uJ.ehJzCt3X30CASawQ4U0AxCVTfFxebzm', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=22', 'landlord', 1, 1, 0, 0, 'active', 'البرمجيات', 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:17', '2025-11-19 04:32:41', NULL),
(2, 'اسم المؤجر', 'landlord@example.com', NULL, NULL, '$2y$12$fwvlBcfviWrJ3VPb1b5YDekN/a9wgKVcfBkbBOvlFTlZvecFt5y1u', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, NULL, 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:17', '2025-11-19 04:32:41', NULL),
(3, 'مستأجر تجريبي', 'tenant1@example.com', NULL, NULL, '$2y$12$zbvR9i7r/rMFyOEDsyf4c.TfWM01LKfzFTt0V3pRjv9hhYqEcSroq', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:17', '2025-11-19 04:32:42', NULL),
(4, 'محمود مرسي', 'tenant2@example.com', NULL, NULL, '$2y$12$9Q9PM2yGVQ3GKvKfqciytuclAtLHksHbpS03nh0VLb0mZRsP7vYIW', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:17', '2025-11-19 04:32:42', NULL),
(5, 'محمد المؤجر', 'landlord1@local.test', NULL, NULL, '$2y$12$xaxpchZDCzw3XAKKaxEIVe7xThqSM/Hgu19s5dM9bAb72RA0Mmn7S', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=11', 'landlord', 1, 1, 0, 0, 'active', 'مقاول', 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:17', '2025-11-19 04:32:42', NULL),
(6, 'سالم المؤجر', 'landlord2@local.test', NULL, NULL, '$2y$12$jg2rUM5H9/pLQ8mhxE0VleP5rAPjKiDlzD61hyAGtoV2OEP22Wubq', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=12', 'landlord', 1, 1, 0, 0, 'active', 'تاجر', 'بنغازي', 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:18', '2025-11-19 04:32:42', NULL),
(7, 'بلال المستأجر', 'tenant1@local.test', NULL, NULL, '$2y$12$3WhjeKtqWjaueLU3mXE4HOPHaitBt9oGNwFF7cYxY3heI4jDbACTq', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=21', 'tenant', 1, 0, 0, 0, 'active', 'مهندس', 'مصراتة', 1, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(8, 'رامي المستأجر', 'tenant2@local.test', NULL, NULL, '$2y$12$7VTWItGCQuuppT7FkWLp6.6yFEIB0dY8UYDSL4aqsqfrBYJrEk.Hq', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=22', 'tenant', 1, 0, 0, 0, 'active', 'محاسب', 'سبها', 0, 0.00, 0, NULL, 0.00, '2025-11-09 19:23:18', '2025-11-19 04:32:43', NULL),
(9, 'مؤجر محمود', 'mahmoud@gmail.com', '0912345671', NULL, '$2y$12$d06kZCbNPyZhTD0osnLMyOidmSsonJ4CQwP4ncEY5lyLIkvR9hI62', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=45', 'landlord', 1, 0, 0, 0, 'active', 'مهندس', 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-18 15:00:03', '2025-11-18 15:00:03', NULL),
(10, 'mahmoud', 'mahmoudelsayed@gmail.com', NULL, NULL, '$2y$12$aK2pYgAGGg1JA7pck.m.O.tEmGYGYGFt.ZYBFnsJqeMsO72oIERkO', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, NULL, 0, 0.00, 0, NULL, 0.00, '2025-11-19 03:33:06', '2025-11-19 03:33:06', NULL),
(11, 'shady', 'shady@gmail.com', NULL, NULL, '$2y$12$JCrHgHCf6YH7oh7hpzWtUujFBdPswDggvrPvHbArwFeWeq5lqxUyi', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, NULL, 0, 0.00, 0, NULL, 0.00, '2025-11-19 03:46:28', '2025-11-19 03:46:28', NULL),
(12, 'محمود السيد المؤجر', 'mahmoudelsayeda@gmail.com', '01024805915', NULL, '$2y$12$rj9pp/ZpuAH7k520lX0kPOCsfMUUv.0uGJSfMAn2hEjRYoe/vgA16', NULL, NULL, NULL, 'https://i.pravatar.cc/150?img=45', 'landlord', 1, 1, 0, 0, 'active', 'مهندس', 'العاشر', 1, 0.00, 0, NULL, 0.00, '2025-11-19 03:53:03', '2025-11-19 03:53:03', NULL),
(13, 'ahmed', 'ahmed@gmail.com', NULL, NULL, '$2y$12$ZtCRGxMYaY8O9Cn8vngQieFaqU0W1tVRQR./.LU7.m2wOZ3810Owa', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, NULL, 0, 0.00, 0, NULL, 0.00, '2025-11-19 03:54:24', '2025-11-19 03:54:24', NULL),
(14, 'مستأجر اختبار عمولة', 'commission.tenant@local.test', NULL, NULL, '$2y$12$verxG3nPzCfbtv03CoLTy.egJLaKjocg1lT7wfyY/hwePjOMr6GTK', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, NULL, 0, 0.00, 0, NULL, 0.00, '2025-11-19 04:32:31', '2025-11-19 04:32:43', NULL),
(15, 'مؤجر اختبار عمولة', 'commission.landlord@local.test', NULL, NULL, '$2y$12$OPJqBhGXZ4YUWVH49CuIzOQzL1GWuw00CYgg3gOhptlz0HSFoyAEW', NULL, NULL, NULL, NULL, 'landlord', 1, 1, 0, 0, 'active', NULL, 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-19 04:32:31', '2025-11-19 04:32:43', NULL),
(16, 'فادي المستاجر', 'commission.multi.tenant@local.test', NULL, NULL, '$2y$12$CCO6PxiVbUxzwwvgP6DEZ.VECht6zdNm8EcOKvmf8DtfXdiQO4iUu', NULL, NULL, NULL, NULL, 'tenant', 0, 0, 0, 0, 'active', NULL, NULL, 0, 0.00, 0, NULL, 0.00, '2025-11-19 04:35:00', '2025-11-19 04:35:00', NULL),
(17, 'مؤجر عمتا', 'commission.multi.landlord@local.test', NULL, NULL, '$2y$12$dE3IjR5z/7b.FS26US/OsuTqN9bAVgz.WWpbmyMrzHsdcjtMNOrM6', NULL, NULL, NULL, NULL, 'landlord', 1, 1, 0, 0, 'active', NULL, 'طرابلس', 0, 0.00, 0, NULL, 0.00, '2025-11-19 04:35:00', '2025-11-19 04:35:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

DROP TABLE IF EXISTS `wallets`;
CREATE TABLE IF NOT EXISTS `wallets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `points_balance` bigint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `points_balance`, `created_at`, `updated_at`) VALUES
(1, 1, 2375.00, 25, '2025-11-09 19:23:17', '2025-11-19 04:32:42'),
(2, 5, 2328.00, 163, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(3, 6, 2516.00, 1842, '2025-11-09 19:23:18', '2025-11-19 22:58:39'),
(4, 7, 2228.00, 1734, '2025-11-09 19:23:18', '2025-11-19 22:58:39'),
(5, 8, 840.00, 1825, '2025-11-09 19:23:18', '2025-11-19 04:32:43'),
(6, 2, 650.00, 0, '2025-11-16 13:30:14', '2025-11-19 04:32:41'),
(7, 9, 170.00, 100, '2025-11-18 15:00:03', '2025-11-19 03:47:30'),
(8, 12, 30.00, 0, '2025-11-19 03:53:03', '2025-11-19 04:17:38'),
(9, 15, 240.00, 0, '2025-11-19 04:32:31', '2025-11-19 04:32:31'),
(10, 17, 1686.00, 0, '2025-11-19 04:35:00', '2025-11-19 04:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

DROP TABLE IF EXISTS `withdraw_requests`;
CREATE TABLE IF NOT EXISTS `withdraw_requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
