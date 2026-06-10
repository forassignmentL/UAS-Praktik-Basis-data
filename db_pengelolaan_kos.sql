-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2026 at 02:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pengelolaan_kos`
--

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas_kamar`
--

CREATE TABLE `fasilitas_kamar` (
  `id_fasilitas` int(11) NOT NULL,
  `id_kamar` int(11) NOT NULL,
  `nama_fasilitas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fasilitas_kamar`
--

INSERT INTO `fasilitas_kamar` (`id_fasilitas`, `id_kamar`, `nama_fasilitas`) VALUES
(1, 1, 'Kasur'),
(2, 1, 'Lemari'),
(3, 1, 'WiFi'),
(4, 2, 'Kasur'),
(5, 2, 'Lemari'),
(6, 2, 'WiFi'),
(7, 3, 'Kasur'),
(8, 3, 'Lemari'),
(9, 3, 'AC'),
(10, 3, 'WiFi'),
(11, 3, 'Kamar Mandi Dalam'),
(12, 4, 'Kasur'),
(13, 4, 'Lemari'),
(14, 4, 'AC'),
(15, 4, 'WiFi');

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id_kamar` int(11) NOT NULL,
  `id_kos` int(11) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `tipe` varchar(50) NOT NULL,
  `harga_per_bulan` decimal(12,2) NOT NULL,
  `status` enum('Tersedia','Terisi','Perbaikan') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`id_kamar`, `id_kos`, `nomor_kamar`, `tipe`, `harga_per_bulan`, `status`) VALUES
(1, 1, 'A01', 'Standar', 750000.00, 'Terisi'),
(2, 1, 'A02', 'Standar', 800000.00, 'Tersedia'),
(3, 1, 'B01', 'Deluxe', 1200000.00, 'Terisi'),
(4, 1, 'B02', 'Deluxe', 1200000.00, 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `kontrak_sewa`
--

CREATE TABLE `kontrak_sewa` (
  `id_kontrak` int(11) NOT NULL,
  `id_penyewa` int(11) NOT NULL,
  `id_kamar` int(11) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `harga_disepakati` decimal(12,2) NOT NULL,
  `status_kontrak` enum('Aktif','Selesai','Dibatalkan') NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontrak_sewa`
--

INSERT INTO `kontrak_sewa` (`id_kontrak`, `id_penyewa`, `id_kamar`, `tgl_mulai`, `tgl_selesai`, `harga_disepakati`, `status_kontrak`) VALUES
(1, 1, 1, '2025-01-01', '2025-12-31', 750000.00, 'Aktif'),
(2, 2, 3, '2025-02-01', '2026-01-31', 1150000.00, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `kos`
--

CREATE TABLE `kos` (
  `id_kos` int(11) NOT NULL,
  `id_pemilik` int(11) NOT NULL,
  `nama_kos` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kos`
--

INSERT INTO `kos` (`id_kos`, `id_pemilik`, `nama_kos`, `alamat`, `deskripsi`) VALUES
(1, 1, 'Kos Melati Indah', 'Jl. Merdeka No. 7, Surabaya', 'Kos lokasi strategis.');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_kerusakan`
--

CREATE TABLE `laporan_kerusakan` (
  `id_laporan` int(11) NOT NULL,
  `id_kamar` int(11) NOT NULL,
  `id_penyewa` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text NOT NULL,
  `prioritas` enum('Rendah','Sedang','Tinggi') NOT NULL DEFAULT 'Sedang',
  `status` enum('Dilaporkan','Dikerjakan','Selesai') NOT NULL DEFAULT 'Dilaporkan',
  `dilaporkan_pada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_kerusakan`
--

INSERT INTO `laporan_kerusakan` (`id_laporan`, `id_kamar`, `id_penyewa`, `judul`, `deskripsi`, `prioritas`, `status`, `dilaporkan_pada`) VALUES
(1, 1, 1, 'Kran air bocor', 'Kran wastafel menetes meski sudah diputar rapat.', 'Sedang', 'Selesai', '2026-06-09 18:42:38'),
(2, 3, 2, 'AC tidak dingin', 'AC tidak mengeluarkan udara dingin sejak 2 hari.', 'Tinggi', 'Selesai', '2026-06-09 18:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `pesan` text NOT NULL,
  `tipe` enum('Tagihan','Pembayaran','Kerusakan','Kontrak','Umum') NOT NULL,
  `sudah_dibaca` tinyint(1) NOT NULL DEFAULT 0,
  `dikirim_pada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `id_pengguna`, `judul`, `pesan`, `tipe`, `sudah_dibaca`, `dikirim_pada`) VALUES
(1, 3, 'Tagihan April 2025', 'Tagihan Rp750.000 jatuh tempo 1 April 2025.', 'Tagihan', 0, '2026-06-09 18:42:38'),
(2, 4, 'Tagihan April 2025', 'Tagihan Rp1.150.000 jatuh tempo 1 April 2025.', 'Tagihan', 0, '2026-06-09 18:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `pekerjaan_perbaikan`
--

CREATE TABLE `pekerjaan_perbaikan` (
  `id_pekerjaan` int(11) NOT NULL,
  `id_laporan` int(11) NOT NULL,
  `id_teknisi` int(11) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `biaya` decimal(12,2) NOT NULL DEFAULT 0.00,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pekerjaan_perbaikan`
--

INSERT INTO `pekerjaan_perbaikan` (`id_pekerjaan`, `id_laporan`, `id_teknisi`, `tgl_mulai`, `tgl_selesai`, `biaya`, `catatan`) VALUES
(1, 1, 5, '2025-03-06', '2025-03-06', 50000.00, 'Kran diganti baru.'),
(2, 2, 5, '2025-04-03', '2025-04-05', 350000.00, 'Freon diisi ulang, AC kembali normal.');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_kontrak` int(11) NOT NULL,
  `tgl_tagihan` date NOT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `denda` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metode_bayar` enum('Transfer','Tunai','QRIS','Lainnya') DEFAULT NULL,
  `status_bayar` enum('Belum Bayar','Lunas','Terlambat') NOT NULL DEFAULT 'Belum Bayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_kontrak`, `tgl_tagihan`, `tgl_bayar`, `jumlah`, `denda`, `metode_bayar`, `status_bayar`) VALUES
(1, 1, '2025-01-01', '2025-01-01', 750000.00, 0.00, 'Transfer', 'Lunas'),
(2, 1, '2025-02-01', '2025-02-03', 750000.00, 0.00, 'Transfer', 'Lunas'),
(3, 1, '2025-03-01', '2025-03-10', 750000.00, 37500.00, 'QRIS', 'Terlambat'),
(4, 1, '2025-04-01', '2025-04-08', 750000.00, 37500.00, 'Transfer', 'Lunas'),
(5, 2, '2025-02-01', '2025-02-01', 1150000.00, 0.00, 'Transfer', 'Lunas'),
(6, 2, '2025-03-01', '2025-03-01', 1150000.00, 0.00, 'Transfer', 'Lunas'),
(7, 2, '2025-04-01', NULL, 1150000.00, 0.00, NULL, 'Belum Bayar');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik_kos`
--

CREATE TABLE `pemilik_kos` (
  `id_pemilik` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `nama_usaha` varchar(150) NOT NULL,
  `alamat_usaha` text NOT NULL,
  `no_rekening` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemilik_kos`
--

INSERT INTO `pemilik_kos` (`id_pemilik`, `id_pengguna`, `nama_usaha`, `alamat_usaha`, `no_rekening`) VALUES
(1, 1, 'Kos Tanaka Property', 'Jl. Merdeka No. 7, Surabaya', '1234567890');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `peran` enum('Pemilik','Admin','Penyewa','Teknisi') NOT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `email`, `no_hp`, `password_hash`, `peran`, `dibuat_pada`) VALUES
(1, 'Tanaka Hiroshi', 'Tanaka@email.com', '081234567890', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Pemilik', '2026-06-09 18:42:38'),
(2, 'Satou Kenji', 'Satou@email.com', '082345678901', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Admin', '2026-06-09 18:42:38'),
(3, 'Nakamura Daiki', 'Nakamura@email.com', '083456789012', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Penyewa', '2026-06-09 18:42:38'),
(4, 'Fujimoto Ryouta', 'Fujimoto@email.com', '084567890123', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Penyewa', '2026-06-09 18:42:38'),
(5, 'Aizawa shun', 'Aizawa@email.com', '085678901234', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', 'Teknisi', '2026-06-09 18:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `penyewa`
--

CREATE TABLE `penyewa` (
  `id_penyewa` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `no_ktp` char(16) NOT NULL,
  `pekerjaan` varchar(100) NOT NULL,
  `kontak_darurat` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyewa`
--

INSERT INTO `penyewa` (`id_penyewa`, `id_pengguna`, `no_ktp`, `pekerjaan`, `kontak_darurat`) VALUES
(1, 3, '3578010101990001', 'Mahasiswa', '081111111111'),
(2, 4, '3578010202990002', 'Karyawan Swasta', '082222222222');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fasilitas_kamar`
--
ALTER TABLE `fasilitas_kamar`
  ADD PRIMARY KEY (`id_fasilitas`),
  ADD KEY `id_kamar` (`id_kamar`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id_kamar`),
  ADD UNIQUE KEY `id_kos` (`id_kos`,`nomor_kamar`);

--
-- Indexes for table `kontrak_sewa`
--
ALTER TABLE `kontrak_sewa`
  ADD PRIMARY KEY (`id_kontrak`),
  ADD KEY `id_penyewa` (`id_penyewa`),
  ADD KEY `id_kamar` (`id_kamar`);

--
-- Indexes for table `kos`
--
ALTER TABLE `kos`
  ADD PRIMARY KEY (`id_kos`),
  ADD KEY `id_pemilik` (`id_pemilik`);

--
-- Indexes for table `laporan_kerusakan`
--
ALTER TABLE `laporan_kerusakan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_kamar` (`id_kamar`),
  ADD KEY `id_penyewa` (`id_penyewa`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pekerjaan_perbaikan`
--
ALTER TABLE `pekerjaan_perbaikan`
  ADD PRIMARY KEY (`id_pekerjaan`),
  ADD KEY `id_laporan` (`id_laporan`),
  ADD KEY `id_teknisi` (`id_teknisi`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_kontrak` (`id_kontrak`);

--
-- Indexes for table `pemilik_kos`
--
ALTER TABLE `pemilik_kos`
  ADD PRIMARY KEY (`id_pemilik`),
  ADD UNIQUE KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD PRIMARY KEY (`id_penyewa`),
  ADD UNIQUE KEY `id_pengguna` (`id_pengguna`),
  ADD UNIQUE KEY `no_ktp` (`no_ktp`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fasilitas_kamar`
--
ALTER TABLE `fasilitas_kamar`
  MODIFY `id_fasilitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id_kamar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kontrak_sewa`
--
ALTER TABLE `kontrak_sewa`
  MODIFY `id_kontrak` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kos`
--
ALTER TABLE `kos`
  MODIFY `id_kos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laporan_kerusakan`
--
ALTER TABLE `laporan_kerusakan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pekerjaan_perbaikan`
--
ALTER TABLE `pekerjaan_perbaikan`
  MODIFY `id_pekerjaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pemilik_kos`
--
ALTER TABLE `pemilik_kos`
  MODIFY `id_pemilik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penyewa`
--
ALTER TABLE `penyewa`
  MODIFY `id_penyewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fasilitas_kamar`
--
ALTER TABLE `fasilitas_kamar`
  ADD CONSTRAINT `fasilitas_kamar_ibfk_1` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`) ON DELETE CASCADE;

--
-- Constraints for table `kamar`
--
ALTER TABLE `kamar`
  ADD CONSTRAINT `kamar_ibfk_1` FOREIGN KEY (`id_kos`) REFERENCES `kos` (`id_kos`) ON DELETE CASCADE;

--
-- Constraints for table `kontrak_sewa`
--
ALTER TABLE `kontrak_sewa`
  ADD CONSTRAINT `kontrak_sewa_ibfk_1` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`),
  ADD CONSTRAINT `kontrak_sewa_ibfk_2` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`);

--
-- Constraints for table `kos`
--
ALTER TABLE `kos`
  ADD CONSTRAINT `kos_ibfk_1` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik_kos` (`id_pemilik`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_kerusakan`
--
ALTER TABLE `laporan_kerusakan`
  ADD CONSTRAINT `laporan_kerusakan_ibfk_1` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`),
  ADD CONSTRAINT `laporan_kerusakan_ibfk_2` FOREIGN KEY (`id_penyewa`) REFERENCES `penyewa` (`id_penyewa`);

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;

--
-- Constraints for table `pekerjaan_perbaikan`
--
ALTER TABLE `pekerjaan_perbaikan`
  ADD CONSTRAINT `pekerjaan_perbaikan_ibfk_1` FOREIGN KEY (`id_laporan`) REFERENCES `laporan_kerusakan` (`id_laporan`),
  ADD CONSTRAINT `pekerjaan_perbaikan_ibfk_2` FOREIGN KEY (`id_teknisi`) REFERENCES `pengguna` (`id_pengguna`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_kontrak`) REFERENCES `kontrak_sewa` (`id_kontrak`);

--
-- Constraints for table `pemilik_kos`
--
ALTER TABLE `pemilik_kos`
  ADD CONSTRAINT `pemilik_kos_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;

--
-- Constraints for table `penyewa`
--
ALTER TABLE `penyewa`
  ADD CONSTRAINT `penyewa_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
