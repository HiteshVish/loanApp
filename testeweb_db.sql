-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2025 at 06:08 AM
-- Server version: 8.0.43-0ubuntu0.24.04.1
-- PHP Version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testeweb_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-admin@gmail.com|127.0.0.1', 'i:2;', 1760088675),
('laravel-cache-admin@gmail.com|127.0.0.1:timer', 'i:1760088675;', 1760088675),
('laravel-cache-hitesh.v@ebrandz.us|127.0.0.1', 'i:2;', 1760505770),
('laravel-cache-hitesh.v@ebrandz.us|127.0.0.1:timer', 'i:1760505770;', 1760505770);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_applications`
--

CREATE TABLE `kyc_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternate_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `current_zip_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permanent_address` text COLLATE utf8mb4_unicode_ci,
  `permanent_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permanent_zip_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_same_as_current` tinyint(1) NOT NULL DEFAULT '0',
  `residential_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `years_at_current_address` int NOT NULL,
  `employment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_income` decimal(15,2) NOT NULL,
  `other_income` decimal(15,2) DEFAULT NULL,
  `employment_tenure_months` int NOT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `loan_tenure_months` int NOT NULL,
  `loan_purpose` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `estimated_emi` decimal(15,2) DEFAULT NULL,
  `aadhar_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pan_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photograph_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhar_card_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pan_card_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','under_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_applications`
--

INSERT INTO `kyc_applications` (`id`, `loan_id`, `user_id`, `full_name`, `date_of_birth`, `gender`, `nationality`, `mobile_number`, `email`, `alternate_contact`, `current_address`, `current_city`, `current_state`, `current_zip_code`, `permanent_address`, `permanent_city`, `permanent_state`, `permanent_zip_code`, `address_same_as_current`, `residential_status`, `years_at_current_address`, `employment_type`, `employer_name`, `designation`, `monthly_income`, `other_income`, `employment_tenure_months`, `loan_amount`, `loan_tenure_months`, `loan_purpose`, `interest_rate`, `estimated_emi`, `aadhar_number`, `pan_number`, `photograph_path`, `address_proof_path`, `aadhar_card_path`, `pan_card_path`, `status`, `admin_notes`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 'LON001', 2, 'Hitesh ebrandz', '2000-01-14', 'Male', 'Indian', '+919554022084', 'hiteshebrandz@gmail.com', '+919554022080', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400063', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400063', 1, 'Own', 2, 'Salaried', 'deafds', 'devce', 2541.00, 0.00, 2512, 100000.00, 3, 'Home', 10.00, 33890.43, '142536698452', 'dfgdf54d65', 'kyc/photographs/Dn7By9hMsC18ow8jX5JkhBmsbigKgnlLVAV0t0NK.png', 'kyc/address_proofs/6P0fpfz7g1U9FGsxIp9PJmzSqDbeBtxCIeJ6sGnD.pdf', 'kyc/aadhar/nq1MPoBL9BNrceuAKi8mf2KUIbcerwEAlELnKZKn.pdf', 'kyc/pan/xMAoo8qWeuSe5LFt0Hfom2kLMqEYqn4D2DNcjMb0.pdf', 'approved', NULL, 3, '2025-10-14 23:32:39', '2025-10-09 23:14:48', '2025-10-14 23:32:39'),
(2, 'LON002', 4, 'Hitesh Vishwakarma', '2000-11-11', 'Male', 'Indian', '+919554022084', 'test1@gmail.com', '+919554022080', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400078', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400078', 1, 'Own', 20, 'Salaried', 'deafds', 'devce', 142424.00, 0.00, 10, 4000000.00, 12, 'Home', 10.00, 351663.55, '111222555444', 'dfgdf54d65', 'kyc/photographs/c5UPUq8bZ4gAmZa3waRBeaMDu9YHjBUM9qG9TDNg.png', 'kyc/address_proofs/L7qARSoykzXze6WrV8JVYbrqMOIq2dRHPUnYBEvI.pdf', 'kyc/aadhar/lwXyAjCd96nVTkQGWajriyAOXBxjNwlcY0683e06.pdf', 'kyc/pan/sWS2sCgAxBp5BfLsmWORXYLjkyJ83JpZrHcSOZYb.pdf', 'rejected', 'Status changed from approved to rejected', 3, '2025-10-12 23:02:37', '2025-10-09 23:34:22', '2025-10-13 23:08:16'),
(3, 'LON003', 1, 'test', '2000-11-11', NULL, 'Indian', '+919554022082', 'test@gmail.com', '+919554022082', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400078', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400078', 1, 'Rent', 10, 'Salaried', 'deafds', 'devce', 5156.00, 1.00, 10, 52000.00, 12, 'Education', 10.00, 4571.63, '142536698452', 'dfgdf54d65', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-10-10 03:59:23', '2025-10-13 23:08:16'),
(4, 'LON004', 6, 'hii', '2000-11-11', NULL, 'Indian', '+919554022084', 'hii@gmail.com', NULL, 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400063', 'Unit No. 8, Ground Floor, Italian Compound, E, Mulund Link Road, Malad, Itt Bhatti, Yashodham, Goregaon\r\nYashodham, Goregaon, Mumbai, Maharashtra 400063', 'Mumbai', 'Maharashtra', '400078', 0, 'Own', 20, 'Salaried', 'deafds', 'devce', 500000.00, 0.00, 100, 500000.00, 10, '1542320', 10.00, 52320.19, '221144556633', 'dfgdf54d65', 'kyc/photographs/rPRyMzRHOeBkYTuzZBuFcrKoTkSkDXADW5McelCD.png', 'kyc/address_proofs/mh8uHDkmo1vcBgJZctTBoCnQwqbWKze2hJGAxGqS.pdf', 'kyc/aadhar/nUtp473li0fyrX071hO5dfHA4OZ83wfukNijdxR2.pdf', NULL, 'pending', NULL, 3, '2025-10-13 23:18:29', '2025-10-13 23:18:29', '2025-10-13 23:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_08_042639_create_personal_access_tokens_table', 1),
(5, '2025_10_08_054048_add_google_fields_to_users_table', 2),
(6, '2025_10_09_050952_add_role_to_users_table', 3),
(7, '2025_10_09_053220_add_kyc_status_to_users_table', 4),
(8, '2025_10_09_053220_create_kyc_applications_table', 4),
(9, '2025_10_13_050614_create_kyc_contacts_table', 5),
(10, '2025_10_13_050616_create_user_locations_table', 5),
(11, '2025_10_14_043655_add_loan_id_to_kyc_applications_table', 6),
(12, '2025_10_14_045445_rename_kyc_contacts_to_user_reference_phone', 7),
(13, '2025_10_14_045654_update_loan_id_type_in_user_reference_phone', 8),
(14, '2025_10_14_050142_rename_contact_number_to_name_in_user_reference_phone', 9),
(17, '2025_10_14_050238_rename_contact_number_to_name_in_user_reference_phone', 10),
(18, '2025_10_14_050856_fix_user_reference_phone_columns', 10),
(19, '2025_10_14_050911_fix_user_reference_phone_columns', 10),
(20, '2025_10_14_051801_update_user_locations_table_use_loan_id', 11),
(21, '2025_10_14_053134_update_user_locations_table_use_loan_id', 11),
(22, '2025_10_15_051547_create_system_settings_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('test@gmail.com', '$2y$12$roT9SoeviwM9ifGYYTUEfuXrJs4KHQkr5bOHgRdu.30ZoBpY16o6S', '2025-10-07 23:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('JrnSZCWwklNr9a7UcbeDE6fZ3ZLC3qFDHoBO6PEX', 3, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiajZlZm1qZTg4ekdaTzhyZTdHSG9QUThSYTBPQ1V0VXc4MUllOWgydyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1760508127),
('SnEHPE2b1aLUqDYtvFhaC8qve2JaMxKCoFzKPhNh', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiREs1Y3lHZzR5aTlIRGVQQjVpb2d2elJhQkNMU2NwTUQ1bTNUWDU2RiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdXRoL2dvb2dsZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NToic3RhdGUiO3M6NDA6InFLcmp1bWNsQ09qRXc2MXA0N1E3eUFMbmtnRG9idzFpaUdGa09CdVQiO30=', 1760504123);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'default_interest_rate', '20', 'number', 'Default annual interest rate for loan applications', '2025-10-14 23:46:33', '2025-10-14 23:48:40'),
(2, 'interest_rate_type', 'fixed', 'string', NULL, '2025-10-14 23:47:56', '2025-10-14 23:47:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `kyc_status` enum('not_submitted','pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_submitted',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `name`, `email`, `role`, `kyc_status`, `avatar`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'test', 'test@gmail.com', 'user', 'pending', NULL, NULL, '$2y$12$omh8oVDtIPr/woXJQkH8bu2sSYzkpAP9VrRzCs/Mz5Mf84rj6nv4u', NULL, '2025-10-07 23:20:26', '2025-10-10 03:59:23'),
(2, '114705308623677001805', 'Hitesh ebrandz', 'hiteshebrandz@gmail.com', 'user', 'approved', 'https://lh3.googleusercontent.com/a/ACg8ocIp7De-ax4nKa2JqAwAeK_S452DL5hr-IvcT3EutLDOBvvfdw=s96-c', '2025-10-14 23:27:21', '$2y$12$ZPYs5Rylm9G26M1ICScNRevFwaoJufWCxk2dHZEAovmwd7ZaqX2.K', 'End9NzsTCpiAKrOwsSkd9E3VKSVTJe8V1Vf3Cuj3HnIJvuATE7O76ODAPGjU', '2025-10-08 00:32:44', '2025-10-14 23:27:21'),
(3, NULL, 'admin', 'admin@example.com', 'admin', 'not_submitted', NULL, '2025-10-08 23:40:59', '$2y$12$OmVdksniUN96qhyg7iObvejtV1SftGngjK3poCALUP5G4hTRvDeh.', NULL, '2025-10-08 23:40:59', '2025-10-08 23:40:59'),
(4, NULL, 'Hitesh Vishwakarma', 'test1@gmail.com', 'user', 'rejected', NULL, NULL, '$2y$12$M5JZk6L7/N4TMbXssxwETOTlUwFNAmwUxoC2RD0pTa2VqdSDxcmo2', NULL, '2025-10-09 23:17:57', '2025-10-12 23:02:37'),
(5, NULL, 'test2', 'test2@gmail.com', 'user', 'not_submitted', NULL, NULL, '$2y$12$wagGFWQ/ABjcdaeH/Dq5Fen4IMUg/RaWpAxoUFWYJuKOcYmDkKA8e', NULL, '2025-10-10 00:28:24', '2025-10-10 00:28:24'),
(6, NULL, 'hii', 'hii@gmail.com', 'user', 'pending', NULL, NULL, '$2y$12$LZNzk6JjKk3fWeGvIBtbR.RH5PojDxMHcj5bW9a458j.F.bQnYPxC', NULL, '2025-10-13 23:18:29', '2025-10-13 23:18:29'),
(7, NULL, 'Hitesh', 'hitesh.v@ebrandz.us', 'user', 'not_submitted', NULL, NULL, '$2y$12$f2MJXD01Xm91Xr5.NEf/euL7i/Ma7lYAWSH0kqJENfjrIrZl6ZH9G', NULL, '2025-10-14 23:52:52', '2025-10-14 23:52:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_locations`
--

CREATE TABLE `user_locations` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_locations`
--

INSERT INTO `user_locations` (`id`, `loan_id`, `latitude`, `longitude`, `address`, `created_at`, `updated_at`) VALUES
(1, 'LON001', 19.07600000, 72.87770000, 'Mumbai, Maharashtra, India', '2025-10-14 00:03:16', '2025-10-14 00:03:16'),
(2, 'LON001', 28.61390000, 77.20900000, 'New Delhi, Delhi, India', '2025-10-14 00:03:16', '2025-10-14 00:03:16'),
(3, 'LON001', 12.97160000, 77.59460000, 'Bangalore, Karnataka, India', '2025-10-14 00:03:16', '2025-10-14 00:03:16'),
(4, 'LON001', 13.08270000, 80.27070000, 'Chennai, Tamil Nadu, India', '2025-10-14 00:03:16', '2025-10-14 00:03:16'),
(5, 'LON001', 22.57260000, 88.36390000, 'Kolkata, West Bengal, India', '2025-10-14 00:03:16', '2025-10-14 00:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_reference_phone`
--

CREATE TABLE `user_reference_phone` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_reference_phone`
--

INSERT INTO `user_reference_phone` (`id`, `loan_id`, `contact_number`, `name`, `created_at`, `updated_at`) VALUES
(1, 'LON001', '+91 9876543210', 'primary', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(2, 'LON001', '+91 8765432109', 'alternate', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(3, 'LON001', '+91 7654321098', 'emergency', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(4, 'LON001', '+91 6543210987', 'work', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(5, 'LON001', '+91 5432109876', 'home', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(6, 'LON001', '+91 4321098765', 'office', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(7, 'LON001', '+91 3210987654', 'friend', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(8, 'LON001', '+91 2109876543', 'family', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(9, 'LON001', '+91 1098765432', 'relative', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(10, 'LON001', '+91 0987654321', 'colleague', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(11, 'LON001', '+91 9988776655', 'neighbor', '2025-10-13 23:28:01', '2025-10-13 23:28:01'),
(12, 'LON001', '+91 8877665544', 'other', '2025-10-13 23:28:01', '2025-10-13 23:28:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_applications`
--
ALTER TABLE `kyc_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kyc_applications_loan_id_unique` (`loan_id`),
  ADD KEY `kyc_applications_user_id_foreign` (`user_id`),
  ADD KEY `kyc_applications_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_locations_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `user_reference_phone`
--
ALTER TABLE `user_reference_phone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_reference_phone_loan_id_foreign` (`loan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_applications`
--
ALTER TABLE `kyc_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_locations`
--
ALTER TABLE `user_locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_reference_phone`
--
ALTER TABLE `user_reference_phone`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kyc_applications`
--
ALTER TABLE `kyc_applications`
  ADD CONSTRAINT `kyc_applications_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kyc_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_locations`
--
ALTER TABLE `user_locations`
  ADD CONSTRAINT `user_locations_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `kyc_applications` (`loan_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_reference_phone`
--
ALTER TABLE `user_reference_phone`
  ADD CONSTRAINT `user_reference_phone_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `kyc_applications` (`loan_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
