-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Des 2025 pada 00.01
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wedding_system`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `wedding_id` int(11) NOT NULL,
  `nama_tamu` varchar(255) NOT NULL,
  `komentar` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `comments`
--

INSERT INTO `comments` (`id`, `wedding_id`, `nama_tamu`, `komentar`, `is_approved`, `created_at`) VALUES
(10, 6, 'yuliana', 'happy wedding! bahagia selalu', 1, '2025-12-12 18:23:29'),
(11, 7, 'devina', 'happy wedding!', 1, '2025-12-12 18:49:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rsvp`
--

CREATE TABLE `rsvp` (
  `id` int(11) NOT NULL,
  `wedding_id` int(11) NOT NULL,
  `nama_tamu` varchar(255) NOT NULL,
  `jumlah_tamu` int(11) DEFAULT 1,
  `status` enum('hadir','tidak_hadir','belum_pasti') NOT NULL,
  `pesan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rsvp`
--

INSERT INTO `rsvp` (`id`, `wedding_id`, `nama_tamu`, `jumlah_tamu`, `status`, `pesan`, `created_at`) VALUES
(10, 6, 'yuliana', 1, 'hadir', '', '2025-12-12 18:29:48'),
(11, 7, 'devina', 2, 'hadir', '', '2025-12-12 18:49:41'),
(12, 6, 'Devina', 2, 'hadir', '', '2025-12-12 19:25:53'),
(13, 6, 'yaya', 5, 'hadir', '', '2025-12-12 19:26:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `nama_template` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `preview_url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `templates`
--

INSERT INTO `templates` (`id`, `nama_template`, `slug`, `preview_url`, `thumbnail`, `harga`, `created_at`, `file_name`) VALUES
(1, 'Lumiere', 'lumiere', '/preview/lumiere', 'image/templates/lumiere.png', 50000.00, '2025-12-06 13:01:35', 'lumiere.php'),
(2, 'Antariksa', 'antariksa', '/preview/antariksa', 'image/templates/antariksa.png', 50000.00, '2025-12-06 13:01:35', 'antariksa.php'),
(3, 'Classic', 'classic', '/preview/classic', 'image/templates/classic.png', 50000.00, '2025-12-06 13:01:35', 'classic.php');

-- --------------------------------------------------------

--
-- Struktur dari tabel `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `slug_url` varchar(255) NOT NULL,
  `kalimat_pembuka` text DEFAULT NULL,
  `nama_panggilan` varchar(100) DEFAULT NULL,
  `nama_mempelai_pria` varchar(255) NOT NULL,
  `nama_mempelai_wanita` varchar(255) NOT NULL,
  `anak_ke_pria` varchar(50) DEFAULT NULL,
  `anak_ke_wanita` varchar(50) DEFAULT NULL,
  `ayah_pria` varchar(255) DEFAULT NULL,
  `ibu_pria` varchar(255) DEFAULT NULL,
  `ayah_wanita` varchar(255) DEFAULT NULL,
  `ibu_wanita` varchar(255) DEFAULT NULL,
  `google_maps` text DEFAULT NULL,
  `tanggal_acara` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nomor_wa` varchar(20) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `status_pembayaran` enum('pending','paid','cancelled') DEFAULT 'pending',
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `website_sent` tinyint(1) DEFAULT 0,
  `website_ready` tinyint(1) DEFAULT 0,
  `lokasi_acara_pemberkatan` text DEFAULT NULL,
  `lokasi_acara_resepsi` text DEFAULT NULL,
  `jam_acara_resepsi` text DEFAULT NULL,
  `jam_acara_pemberkatan` text DEFAULT NULL,
  `dress_code` varchar(255) DEFAULT NULL,
  `foto_mempelai_pria` varchar(255) DEFAULT NULL,
  `foto_mempelai_wanita` varchar(255) DEFAULT NULL,
  `foto_1` varchar(255) DEFAULT NULL,
  `foto_2` varchar(255) DEFAULT NULL,
  `foto_3` varchar(255) DEFAULT NULL,
  `foto_4` varchar(255) DEFAULT NULL,
  `foto_5` varchar(255) DEFAULT NULL,
  `foto_6` varchar(255) DEFAULT NULL,
  `foto_7` varchar(255) DEFAULT NULL,
  `foto_8` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `weddings`
--

INSERT INTO `weddings` (`id`, `template_id`, `slug_url`, `kalimat_pembuka`, `nama_panggilan`, `nama_mempelai_pria`, `nama_mempelai_wanita`, `anak_ke_pria`, `anak_ke_wanita`, `ayah_pria`, `ibu_pria`, `ayah_wanita`, `ibu_wanita`, `google_maps`, `tanggal_acara`, `email`, `nomor_wa`, `harga`, `status_pembayaran`, `is_active`, `created_at`, `updated_at`, `website_sent`, `website_ready`, `lokasi_acara_pemberkatan`, `lokasi_acara_resepsi`, `jam_acara_resepsi`, `jam_acara_pemberkatan`, `dress_code`, `foto_mempelai_pria`, `foto_mempelai_wanita`, `foto_1`, `foto_2`, `foto_3`, `foto_4`, `foto_5`, `foto_6`, `foto_7`, `foto_8`) VALUES
(6, 1, 'reihan-montague-ana-capulet-748348', 'Dengan memohon kasih karunia Tuhan', 'Reihan &amp; Ana', 'Reihan Montague', 'Ana Capulet', 'Kedua', 'Kedua', 'Rendi', 'Wulan', 'Marco', 'Meri', 'https://www.google.com/maps/place/Swiss-Belinn+Hotel/@1.1318653,104.0120934,15.68z/data=!4m5!3m4!1s0x31da3c8c254ad11f:0xd703ac9d9b85ac28!8m2!3d1.1336207!4d104.006989?hl=id&amp;entry=ttu&amp;g_ep=EgoyMDI1MTIwOS4wIKXMDSoASAFQAw%3D%3D', 'Sabtu, 10 Januari 2026', 'ana@gmail.com', '082386977743', 150000.00, 'paid', 1, '2025-12-12 18:20:53', '2025-12-12 21:11:47', 0, 0, 'Invyra Venue, Batam', 'Golden Venue, Batam', '18:00 - Selesai', '09:00 - Selesai', 'Formal Attire - Light Blue', 'wedding_6_foto_mempelai_pria_1765563654.jpeg', 'wedding_6_foto_mempelai_wanita_1765563654.jpeg', 'wedding_6_foto_1_1765563654.jpeg', 'wedding_6_foto_2_1765563654.jpeg', 'wedding_6_foto_3_1765563654.jpeg', 'wedding_6_foto_4_1765563654.jpeg', 'wedding_6_foto_5_1765563654.jpeg', 'wedding_6_foto_6_1765563654.jpeg', 'wedding_6_foto_7_1765563654.jpeg', 'wedding_6_foto_8_1765563654.jpeg'),
(7, 2, 'burhan-montague-amanda-capulet-fae316', 'Dengan memohon kasih karunia Tuhan', 'Burhan &amp; Amanda', 'Burhan Montague', 'Amanda Capulet', 'Pertama', 'Ketiga', 'Rendi', 'Wulan', 'Marco', 'Meri', 'https://www.google.com/maps/place/Swiss-Belinn+Hotel/@1.1318653,104.0120934,15.68z/data=!4m5!3m4!1s0x31da3c8c254ad11f:0xd703ac9d9b85ac28!8m2!3d1.1336207!4d104.006989?hl=id&amp;entry=ttu&amp;g_ep=EgoyMDI1MTIwOS4wIKXMDSoASAFQAw%3D%3D', 'Jumat, 26 Desember 2025', 'burhan@gmail.com', '082178094532', 150000.00, 'paid', 1, '2025-12-12 18:47:01', '2025-12-12 20:34:46', 0, 0, 'Invyra Venue, Batam', 'Golden Venue, Batam', '18:00 - Selesai', '09:00 - Selesai', 'Formal Attire - Red', 'wedding_7_foto_mempelai_pria_1765565221.jpeg', 'wedding_7_foto_mempelai_wanita_1765565221.jpeg', 'wedding_7_foto_1_1765565221.jpeg', 'wedding_7_foto_2_1765565221.jpeg', 'wedding_7_foto_3_1765565221.jpeg', 'wedding_7_foto_4_1765565221.jpeg', 'wedding_7_foto_5_1765565221.jpeg', 'wedding_7_foto_6_1765565221.jpeg', 'wedding_7_foto_7_1765565221.jpeg', 'wedding_7_foto_8_1765565221.jpeg'),
(8, 3, 'andi-montague-mila-capulet-1a0447', 'Dengan memohon kasih karunia Tuhan', 'Andi &amp; Mila', 'Andi Montague', 'Mila Capulet', 'Ketiga', 'Kelima', 'Rendi', 'Wulan', 'Marco', 'Meri', 'https://www.google.com/maps/place/Swiss-Belinn+Hotel/@1.1318653,104.0120934,15.68z/data=!4m5!3m4!1s0x31da3c8c254ad11f:0xd703ac9d9b85ac28!8m2!3d1.1336207!4d104.006989?hl=id&amp;entry=ttu&amp;g_ep=EgoyMDI1MTIwOS4wIKXMDSoASAFQAw%3D%3D', 'Rabu, 28 Januari 2026', 'mina@gmail.com', '085678235644', 200000.00, 'paid', 1, '2025-12-12 18:54:33', '2025-12-12 20:11:08', 0, 0, 'Invyra Venue, Batam', 'Golden Venue, Batam', '18:00 - Selesai', '09:00 - Selesai', 'Formal Attire - Light Purple', 'wedding_8_foto_mempelai_pria_1765565673.jpeg', 'wedding_8_foto_mempelai_wanita_1765565673.jpeg', 'wedding_8_foto_1_1765565673.jpeg', 'wedding_8_foto_2_1765565674.jpeg', 'wedding_8_foto_3_1765565674.jpeg', 'wedding_8_foto_4_1765565674.jpeg', 'wedding_8_foto_5_1765565674.jpeg', 'wedding_8_foto_6_1765565674.jpeg', 'wedding_8_foto_7_1765565674.jpeg', 'wedding_8_foto_8_1765565674.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wedding` (`wedding_id`),
  ADD KEY `idx_approved` (`is_approved`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indeks untuk tabel `rsvp`
--
ALTER TABLE `rsvp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wedding` (`wedding_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indeks untuk tabel `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indeks untuk tabel `weddings`
--
ALTER TABLE `weddings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug_url` (`slug_url`),
  ADD KEY `idx_slug` (`slug_url`),
  ADD KEY `idx_template` (`template_id`),
  ADD KEY `idx_status` (`status_pembayaran`),
  ADD KEY `idx_active` (`is_active`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `rsvp`
--
ALTER TABLE `rsvp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rsvp`
--
ALTER TABLE `rsvp`
  ADD CONSTRAINT `rsvp_ibfk_1` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `weddings`
--
ALTER TABLE `weddings`
  ADD CONSTRAINT `weddings_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
