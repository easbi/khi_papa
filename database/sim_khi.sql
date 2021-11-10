-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2021 at 07:56 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sim_khi`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_activity`
--

CREATE TABLE `daily_activity` (
  `id` int(11) NOT NULL,
  `nip` varchar(18) NOT NULL,
  `wfo_wfh` varchar(10) NOT NULL,
  `fungsional` varchar(50) DEFAULT NULL,
  `butir_kegiatan` varchar(50) DEFAULT NULL,
  `kegiatan` text NOT NULL,
  `berkas` varchar(100) DEFAULT NULL,
  `satuan` varchar(100) NOT NULL,
  `kuantitas` int(3) NOT NULL,
  `is_internet` varchar(1) NOT NULL,
  `is_done` int(1) NOT NULL DEFAULT 2,
  `tgl` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `daily_activity`
--

INSERT INTO `daily_activity` (`id`, `nip`, `wfo_wfh`, `fungsional`, `butir_kegiatan`, `kegiatan`, `berkas`, `satuan`, `kuantitas`, `is_internet`, `is_done`, `tgl`, `created_at`, `updated_at`, `created_by`) VALUES
(4, '199602182019011002', 'WFO', NULL, NULL, 'Membuat aplikasi KHI - Part 1 : Rancangan DB', NULL, 'Modul Program', 1, '1', 1, '2021-07-30', '2021-08-09 06:10:20', '2021-08-08 23:10:20', '199602182019011002'),
(6, '199602182019011002', 'WFO', NULL, NULL, 'Mengikuti Apel Pagi  (Host, Menyiapkan Lagu2, Pembacaan UUD)', NULL, 'Kegiatan', 1, '1', 1, '2021-08-02', '2021-08-01 22:43:31', '2021-08-01 22:43:31', '199602182019011002'),
(7, '199602182019011002', 'WFO', NULL, NULL, 'Instalasi VIMK Tahunan 2021 di Laptop Kak Chesil', NULL, 'Kegiatan', 1, '1', 1, '2021-08-02', '2021-08-01 22:44:16', '2021-08-01 22:44:16', '199602182019011002'),
(8, '199602182019011002', 'WFO', NULL, NULL, 'Menyiapkan Kelas Elearning SAKERNAS Petugas Kota Padang Panjang', NULL, 'Kegiatan', 1, '1', 1, '2021-08-02', '2021-08-01 22:45:09', '2021-08-01 22:45:09', '199602182019011002'),
(9, '199602182019011002', 'WFO', NULL, NULL, 'Memeriksa Dokumen SPTK 2021', NULL, 'Dokumen', 4, '1', 1, '2021-08-02', '2021-08-02 07:56:34', '2021-08-02 00:56:34', '199602182019011002'),
(10, '199602182019011002', 'WFO', NULL, NULL, 'Menyiapkan Apel Pagi - Zoom + Operator + Petugas Pembaca UUD 1945', NULL, 'Kegiatan', 1, '1', 1, '2021-08-09', '2021-08-08 22:12:37', '2021-08-08 22:12:37', '199602182019011002'),
(11, '199602182019011002', 'WFO', NULL, NULL, 'SWAB PCR Ke Puskesmas Sikolos', NULL, 'Kegiatan', 1, '2', 1, '2021-08-09', '2021-08-08 22:13:06', '2021-08-08 22:13:06', '199602182019011002'),
(12, '199602182019011002', 'WFO', NULL, NULL, 'Melengkapi Pemutakhiran MySAPK - Progres 9/12 Tab', NULL, 'Kegiatan', 1, '1', 1, '2021-08-09', '2021-08-08 22:13:53', '2021-08-08 22:13:53', '199602182019011002'),
(13, '199602182019011002', 'WFO', NULL, NULL, 'Memeriksa Dokumen SPTK 2021', NULL, 'Dokumen', 60, '1', 1, '2021-08-09', '2021-08-19 03:01:02', '2021-08-18 20:01:02', '199602182019011002'),
(14, '199602182019011002', 'WFH', NULL, NULL, 'Mengikuti Sosialisasi Peraturan Kepala BPS No. 2 Tahun 2021 Terkait Petunjuk Teknis AK Pranata Komputer', NULL, 'Kegiatan', 1, '1', 1, '2021-08-12', '2021-08-19 03:01:17', '2021-08-18 20:01:17', '199602182019011002'),
(15, '199602182019011002', 'WFO', NULL, NULL, 'Memeriksa Dokumen SPTK 2021', NULL, 'Dokumen', 6, '1', 2, '2021-08-13', '2021-08-12 18:26:03', '2021-08-12 18:26:03', '199602182019011002'),
(16, '199602182019011002', 'WFO', NULL, NULL, 'Mengikuti Jumat Berbagi', NULL, 'Kegiatan', 1, '1', 1, '2021-08-13', '2021-08-19 04:24:18', '2021-08-19 04:24:18', '199602182019011002'),
(17, '199602182019011002', 'WFO', NULL, NULL, 'Update VIMK Tahunan Patch 1.0.2', NULL, 'Kegiatan', 1, '1', 2, '2021-08-13', '2021-08-12 21:25:12', '2021-08-12 21:25:12', '199602182019011002'),
(18, '199602182019011002', 'WFO', NULL, NULL, 'Mengikuti Apel Pagi (Host, Menyiapkan Lagu2, Pembacaan UUD)', NULL, 'Kegiatan', 1, '1', 2, '2021-08-16', '2021-08-15 20:30:41', '2021-08-15 20:30:41', '199602182019011002'),
(19, '199602182019011002', 'WFO', NULL, NULL, 'Memeriksa 3 Salinan Pertama PCL SAKERNAS 2021', NULL, 'Dokumen', 6, '1', 2, '2021-08-16', '2021-08-15 21:16:28', '2021-08-15 21:16:28', '199602182019011002'),
(20, '199602182019011002', 'WFO', NULL, NULL, 'Melanjutkan Aplikasi KHI', NULL, 'Kegiatan', 1, '1', 2, '2021-08-19', '2021-08-18 19:53:05', '2021-08-18 19:53:05', '199602182019011002'),
(21, '196506241991021001', 'WFO', NULL, NULL, 'Monitoring', NULL, 'Kegiatan', 1, '1', 2, '2021-08-19', '2021-08-19 04:56:41', '2021-08-19 04:56:41', '196506241991021001'),
(22, '199602182019011002', 'WFO', NULL, NULL, 'Aplikasi KHI - Membuat Display Chart Progreess Pengentrian', NULL, 'Kegiatan', 1, '1', 2, '2021-08-19', '2021-08-19 08:17:01', '2021-08-19 08:17:01', '199602182019011002'),
(23, '199602182019011002', 'WFO', NULL, NULL, 'Mengikuti Apel Pagi  (Host, Menyiapkan Lagu2)', NULL, 'Kegiatan', 1, '1', 1, '2021-08-23', '2021-08-23 01:18:59', '2021-08-23 01:18:59', '199602182019011002'),
(24, '199602182019011002', 'WFO', NULL, NULL, 'Menyiapkan Akun dan Username Pengisian CAWI SUPLEMEN SAK kepada PCL', NULL, 'Kegiatan', 1, '1', 1, '2021-08-23', '2021-08-23 05:46:32', '2021-08-23 05:46:32', '199602182019011002'),
(25, '199602182019011002', 'WFH', NULL, NULL, 'Menyiapkan bahan paparan running SAE', NULL, 'Kegiatan', 1, '1', 1, '2021-08-23', '2021-08-23 05:46:37', '2021-08-23 05:46:37', '199602182019011002'),
(26, '199602182019011002', 'WFO', NULL, NULL, 'Menyiapkan Akun dan Username Pengisian CAWI SUPLEMEN SAK kepada PCL', NULL, 'Kegiatan', 1, '1', 1, '2021-08-24', '2021-08-24 06:12:08', '2021-08-24 06:12:08', '199602182019011002'),
(27, '199602182019011002', 'WFO', NULL, NULL, 'Membahas dokumen PODES RT', NULL, 'Kegiatan', 1, '2', 2, '2021-08-24', '2021-08-24 03:10:19', '2021-08-24 03:10:19', '199602182019011002'),
(28, '199602182019011002', 'WFO', NULL, NULL, 'Membuat aplikasi KHI - Part 4 : Desain Dashboard KHI', NULL, 'Kegiatan', 1, '1', 2, '2021-08-24', '2021-08-24 04:42:38', '2021-08-24 04:42:38', '199602182019011002'),
(29, '199602182019011002', 'WFH', NULL, NULL, 'Sharing Knowledge SAE EBLUP FH Ke TIM SAE PUSAT', NULL, 'Kegiatan', 1, '1', 2, '2021-08-24', '2021-08-24 08:44:49', '2021-08-24 08:44:49', '199602182019011002'),
(30, '199602182019011002', 'WFO', NULL, NULL, 'Rapat Pembahasan Kuesioner PODES RT', NULL, 'Kegiatan', 1, '1', 2, '2021-08-25', '2021-08-25 06:32:38', '2021-08-25 06:32:38', '199602182019011002'),
(31, '199602182019011002', 'WFH', NULL, NULL, 'Sharing Knowledge SAE EBLUP FH Ke TIM SAE PUSAT', NULL, 'Kegiatan', 1, '1', 2, '2021-08-25', '2021-08-25 08:53:22', '2021-08-25 08:53:22', '199602182019011002'),
(32, '199602182019011002', 'WFO', NULL, NULL, 'Split, Merge, dan Penarikan sampel IMK Tahunan', NULL, 'Kegiatan', 1, '1', 2, '2021-08-25', '2021-08-25 08:53:49', '2021-08-25 08:53:49', '199602182019011002'),
(33, '199602182019011002', 'WFO', NULL, NULL, 'Cek SPTK 1 Dokumen Yang Sampel U', NULL, 'Kegiatan', 1, '1', 2, '2021-08-25', '2021-08-25 08:56:43', '2021-08-25 08:56:43', '199602182019011002'),
(34, '199602182019011002', 'WFO', NULL, NULL, 'Pelatihan SITASI 2021', NULL, 'Kegiatan', 1, '1', 2, '2021-09-01', '2021-09-01 02:27:47', '2021-09-01 02:27:47', '199602182019011002'),
(35, '199602182019011002', 'WFO', NULL, NULL, 'Memeriksa Dokumen SAKERNAS 2021', NULL, 'Kegiatan', 1, '2', 2, '2021-09-01', '2021-09-01 02:28:13', '2021-09-01 02:28:13', '199602182019011002'),
(36, '199602182019011002', 'WFO', NULL, NULL, 'Menyiapkan dokumen dan mensubmit untuk keperluan Promosi Statistik 1374', NULL, 'Kegiatan', 1, '1', 2, '2021-09-07', '2021-09-07 05:03:03', '2021-09-07 05:03:03', '199602182019011002'),
(37, '199602182019011002', 'WFO', NULL, NULL, 'Pergi dengan Ka BPS ke Diskominfo Mengenai Pencanangan PODES RT dan Kelurahan Cinta Statistik', NULL, 'Kegiatan', 1, '2', 2, '2021-09-07', '2021-09-07 05:03:53', '2021-09-07 05:03:53', '199602182019011002'),
(38, '199602182019011002', 'WFO', NULL, NULL, 'Memantau Perbaikan Jaringan Internet Kantor', NULL, 'Kegiatan', 1, '1', 2, '2021-09-07', '2021-09-07 05:04:45', '2021-09-07 05:04:45', '199602182019011002'),
(39, '199602182019011002', 'WFO', NULL, NULL, 'Finalisasi Pemeriksaan SAKERNAS 2021 Part 2', NULL, 'Dokumen', 50, '1', 2, '2021-09-06', '2021-09-07 05:05:20', '2021-09-07 05:05:20', '199602182019011002');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2021_08_09_053654_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('UmJMPuOWRBJwXr49oGbOBbCBgvhDeEbWLnlgNIXC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiejBuOXk3ZGg2cW90NlZ5WnU5WTBlMnV1OUNsckhCUVhPWmREZ0hGNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hY3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEwJEU2eEx3S3NLWTl3ZGhPbS8yTEYvMy5VL0czVjBzTng5Umg3dU5ZbHpuYnZFQXUyR1R0MGIyIjt9', 1630991132);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fullname` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organisasi` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_kerja` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `nip`, `organisasi`, `unit_kerja`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Easbi Ikhsan', 'easbi', '199602182019011002', 'Seksi Integrasi Pengolahan dan Diseminasi Statistik', 'BPS Kota Padang Panjang', 'easbi@bps.go.id', NULL, '$2y$10$E6xLwKsKY9wdhOm/2LF/3.U/G3V0sNx9Rh7uNYlznbvEAu2GTt0b2', NULL, NULL, NULL, NULL, NULL, '2021-08-08 22:55:07', '2021-08-12 20:57:31'),
(2, 'Arius Jonnaidi SE, M.E', 'ariusjon', '196506241991021001', 'Kepala BPS Kota Padang Panjang', 'BPS Kota Padang Panjang', 'ariusjon@bps.go.ida', NULL, '$2y$10$vwzJf6PJyBgqU1ryOa8Wze24rZo4PUnbVi1MjwLrWNgIbr0C1GIiS', NULL, NULL, NULL, NULL, NULL, '2021-08-12 20:37:19', '2021-08-19 05:05:31'),
(3, 'Nove Ira S.Psi', 'nove', '197611092011012005', 'Subbagian Tata Usaha', 'BPS Kota Padang Panjang', 'nove@bps.go.id', NULL, '$2y$10$Ssd3K6M7crPdAQk.IpOc.uEpIMEO4uo7bqgfAh3xqbPPYxB0u4NP2', NULL, NULL, NULL, NULL, NULL, '2021-08-19 06:55:10', '2021-08-19 06:55:10'),
(4, 'Mega Novita', 'mega.novita', '198508032005022001', 'Subbagian Tata Usaha', 'BPS Kota Padang Panjang', 'mega.novita@bps.go.id', NULL, '$2y$10$W4NlpZXCJDcQba/dr8XAp.qcx0g3MtM2lDjjRflN.5F4eDTYwFsFy', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:39:25', '2021-08-19 07:39:25'),
(5, 'Dwithia Handriani SST', 'dhandriani', '199007172014102001', 'Seksi Statistik Sosial', 'BPS Kota Padang Panjang', 'dhandriani@bps.go.id', NULL, '$2y$10$EgLeAwDmnv363uCY20/EWOhcPrRwzsxrdd/g2sEsmvbns4CDlBQnC', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:40:29', '2021-08-19 07:40:29'),
(6, 'Lina Ferdianty Lubis SST', 'lina_ferdianty', '198002162002122005', 'Seksi Statistik Produksi', 'BPS Kota Padang Panjang', 'lina_ferdianty@bps.go.id', NULL, '$2y$10$LsucNVwPf9PY7MeKjwshhuFK9H28wl6UAbvJzOS6I0978W2lzs0tW', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:41:20', '2021-08-19 07:41:20'),
(7, 'Chesilia Amora Jofipasi S.Stat.', 'chesilia.jofipasi', '199507222019032001', 'Seksi Statistik Produksi', 'BPS Kota Padang Panjang', 'chesilia.jofipasi@bps.go.id', NULL, '$2y$10$B5zikLfBdgMRPCPlctP54eAhgwhDd04dDFHHZemanKIOPYPDHu7/C', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:42:35', '2021-08-19 07:43:24'),
(8, 'Rindy Primadini SST', 'rindyprimadini', '199105192013112001', 'Seksi Statistik Distribusi', 'BPS Kota Padang Panjang', 'rindyprimadini@bps.go.id', NULL, '$2y$10$ZJO8szZ/HPwIccSW7VJZjemF3HOgTdzIPcCt6WA52BCKpWNMylsAy', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:45:44', '2021-08-19 07:45:44'),
(9, 'Atika Surya Ananda SST', 'atika.ananda', '199104052014122001', 'Seksi Statistik Distribusi', 'BPS Kota Padang Panjang', 'atika.ananda@bps.go.id', NULL, '$2y$10$U8VDWp/3of0n8U8J1j7bEO.o1zKqZaw.f7YfXyI0YPnH2MFr0Eb0C', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:46:33', '2021-08-19 07:46:33'),
(10, 'Nurhayati S.E', 'nurhay', '197111211994032002', 'Seksi Neraca Wilayah dan Analisis Statistik', 'BPS Kota Padang Panjang', 'nurhay@bps.go.id', NULL, '$2y$10$uSwowz2mJvrw6g3xdFNtb.b6sy/FRKWBvwPA1Eg8w1iJd4qaZVxeq', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:47:28', '2021-08-19 07:47:28'),
(11, 'Masruqi Arrazy SST.,M.M.', 'mas.ruqi', '198701242009021003', 'Seksi Neraca Wilayah dan Analisis Statistik', 'BPS Kota Padang Panjang', 'mas.ruqi@bps.go.id', NULL, '$2y$10$aZSwjo6bsRnfYlGSpg7rMOBGib/uz1SCT1llolYC/UvPuQt4Vo6dG', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:48:16', '2021-08-19 07:48:16'),
(12, 'Utari Azalika Rahmi SST', 'utari.ar', '199111052014102001', 'Seksi Integrasi Pengolahan dan Diseminasi Statistik', 'BPS Kota Padang Panjang', 'utari.ar@bps.go.id', NULL, '$2y$10$Yb9TTM3Po7zAWyRp4QNTpOsmV82BenA54IpUe66L74fH4zTlbmche', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:49:06', '2021-08-19 07:49:06'),
(13, 'Fitri Ananda S.Si', 'fitri.ananda', '198612152009022005', 'Seksi Integrasi Pengolahan dan Diseminasi Statistik', 'BPS Kota Padang Panjang', 'fitri.ananda@bps.go.id', NULL, '$2y$10$zV6/E6ogZqxa79h28PZaM.P7PPrMc7Y6ANUjk2//BAcUnGHBg.5sC', NULL, NULL, NULL, NULL, NULL, '2021-08-19 07:49:56', '2021-08-19 07:49:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_activity`
--
ALTER TABLE `daily_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_activity`
--
ALTER TABLE `daily_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
