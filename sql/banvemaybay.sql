-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 03:21 PM
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
-- Database: `banvemaybay`
--

-- --------------------------------------------------------

--
-- Table structure for table `chuyen_bay`
--

CREATE TABLE `chuyen_bay` (
  `id` int(11) NOT NULL,
  `so_hieu` varchar(50) NOT NULL,
  `noi_di` varchar(100) NOT NULL,
  `noi_den` varchar(100) NOT NULL,
  `gio_khoi_hanh` datetime NOT NULL,
  `gio_ha_canh` datetime NOT NULL,
  `gia_thuong` decimal(10,2) NOT NULL,
  `gia_thuong_gia` decimal(10,2) NOT NULL,
  `ghe_con` int(11) NOT NULL DEFAULT 0,
  `may_bay_id` int(11) DEFAULT NULL,
  `tao_luc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chuyen_bay`
--

INSERT INTO `chuyen_bay` (`id`, `so_hieu`, `noi_di`, `noi_den`, `gio_khoi_hanh`, `gio_ha_canh`, `gia_thuong`, `gia_thuong_gia`, `ghe_con`, `may_bay_id`, `tao_luc`) VALUES
(1, 'VN101', 'Hà Nội', 'TP. Hồ Chí Minh', '2025-12-10 08:00:00', '2025-12-10 10:00:00', 1200000.00, 2500000.00, 52, 1, '2025-12-29 15:53:24'),
(2, 'VN102', 'TP. Hồ Chí Minh', 'Hà Nội', '2025-12-10 14:00:00', '2025-12-10 16:00:00', 1200000.00, 2500000.00, 43, 5, '2025-12-29 15:53:24'),
(5, 'VN301', 'TP. Hồ Chí Minh', 'Cần Thơ', '2025-12-10 07:00:00', '2025-12-10 08:30:00', 650000.00, 1600000.00, 30, 1, '2025-12-29 15:53:24'),
(6, 'VN302', 'Cần Thơ', 'TP. Hồ Chí Minh', '2025-12-10 18:00:00', '2025-12-10 19:30:00', 650000.00, 1500000.00, 28, 4, '2025-12-29 15:53:24'),
(8, 'VN010', 'Hà Nội', 'Moscow', '2025-12-18 02:42:00', '2025-12-25 03:43:00', 900000.00, 90000000.00, 100, 1, '2025-12-29 17:41:33'),
(11, 'VN764', 'TP. Hồ Chí Minh', 'Đà Nẵng', '2026-01-13 15:53:00', '2026-01-09 15:53:00', 7786.00, 86886.00, 80, 1, '2026-01-11 08:53:47'),
(12, 'VN1126', 'Cần Thơ', 'TP. Hồ Chí Minh', '2026-01-16 15:54:00', '2026-01-30 15:54:00', 6866.00, 7557.00, 89, 5, '2026-01-11 08:54:16'),
(13, 'VN1000', 'Cần Thơ', 'Hà Nội', '2026-01-14 00:30:00', '2026-01-15 00:30:00', 80000.00, 160000.00, 20, 4, '2026-01-12 17:30:42'),
(14, 'VN10000', 'Hà Nội', 'Moscow', '2026-01-14 05:34:00', '2026-01-15 05:34:00', 9000.00, 1800.00, 8, 4, '2026-01-12 22:35:06'),
(15, 'VN123456', 'Nam định', 'Moscow', '2026-01-21 21:11:00', '2026-01-29 21:11:00', 9000.00, 10000.00, 88, 4, '2026-01-13 14:11:43');

-- --------------------------------------------------------

--
-- Table structure for table `dat_ve`
--

CREATE TABLE `dat_ve` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) NOT NULL,
  `chuyen_bay_id` int(11) NOT NULL,
  `ve_id` int(11) DEFAULT NULL,
  `trang_thai` enum('cart','paid','cancelled') NOT NULL DEFAULT 'cart',
  `ten_thanh_toan` varchar(150) DEFAULT NULL,
  `email_thanh_toan` varchar(150) DEFAULT NULL,
  `dien_thoai_thanh_toan` varchar(20) DEFAULT NULL,
  `dia_chi_thanh_toan` varchar(255) DEFAULT NULL,
  `da_xac_nhan` tinyint(1) NOT NULL DEFAULT 0,
  `so_ghe_dat` int(11) NOT NULL DEFAULT 1,
  `tong_tien` decimal(10,2) NOT NULL,
  `dat_luc` timestamp NOT NULL DEFAULT current_timestamp(),
  `thanh_toan_luc` timestamp NULL DEFAULT NULL,
  `phuong_thuc_thanh_toan` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dat_ve`
--

INSERT INTO `dat_ve` (`id`, `nguoi_dung_id`, `chuyen_bay_id`, `ve_id`, `trang_thai`, `ten_thanh_toan`, `email_thanh_toan`, `dien_thoai_thanh_toan`, `dia_chi_thanh_toan`, `da_xac_nhan`, `so_ghe_dat`, `tong_tien`, `dat_luc`, `thanh_toan_luc`, `phuong_thuc_thanh_toan`) VALUES
(44, 4, 15, 10, 'paid', 'kji', 'hopthuthom@gmail.com', '9088', 'nam dinh', 0, 2, 20000.00, '2026-01-13 14:16:15', '2026-01-13 14:16:36', 'atm');

-- --------------------------------------------------------

--
-- Table structure for table `hanh_khach`
--

CREATE TABLE `hanh_khach` (
  `id` int(11) NOT NULL,
  `dat_ve_id` int(11) NOT NULL,
  `ten_hanh_khach` varchar(150) NOT NULL,
  `dien_thoai` varchar(20) NOT NULL,
  `email_hanh_khach` varchar(150) NOT NULL,
  `gioi_tinh` enum('Nam','Nu','Khac') NOT NULL DEFAULT 'Nam',
  `tuoi` int(11) NOT NULL DEFAULT 18,
  `loai_ve` enum('Thuong','Thuong gia') NOT NULL DEFAULT 'Thuong',
  `gia_ve` decimal(10,2) NOT NULL,
  `so_ghe` int(11) NOT NULL,
  `tao_luc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hanh_khach`
--

INSERT INTO `hanh_khach` (`id`, `dat_ve_id`, `ten_hanh_khach`, `dien_thoai`, `email_hanh_khach`, `gioi_tinh`, `tuoi`, `loai_ve`, `gia_ve`, `so_ghe`, `tao_luc`) VALUES
(99, 44, 'hop', '9584', 'hopthuthom@gmail.com', 'Nam', 18, 'Thuong gia', 10000.00, 1, '2026-01-13 14:16:15'),
(100, 44, 'hop', '9584', 'hopthuthom@gmail.com', 'Nam', 18, 'Thuong gia', 10000.00, 2, '2026-01-13 14:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `may_bay`
--

CREATE TABLE `may_bay` (
  `id` int(11) NOT NULL,
  `ma_may_bay` varchar(50) NOT NULL,
  `ten_may_bay` varchar(150) NOT NULL,
  `hang_may_bay` varchar(50) NOT NULL DEFAULT 'Khác',
  `tao_luc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `may_bay`
--

INSERT INTO `may_bay` (`id`, `ma_may_bay`, `ten_may_bay`, `hang_may_bay`, `tao_luc`) VALUES
(1, 'A320', 'Vietjet', 'Vietjet Air', '2026-01-11 09:23:39'),
(4, 'A123', 'VietnamElines', 'Vietnam Airlines', '2026-01-11 21:33:29'),
(5, 'B123', 'Bamboo', 'Bamboo Airways', '2026-01-11 21:33:45'),
(6, 'A124', 'Bambo 123', 'Bamboo Airways', '2026-01-12 10:24:21'),
(8, 'A129', 'Bambo 124', 'Bamboo Airways', '2026-01-13 14:14:27');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int(11) NOT NULL,
  `ten` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `vai_tro` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `tao_luc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ten`, `email`, `mat_khau`, `vai_tro`, `tao_luc`) VALUES
(4, 'hop', 'hop@gmail.com', '123456', 'customer', '2025-12-29 15:56:19'),
(5, 'Admin', 'admin@gmail.com', '123456', 'admin', '2025-12-29 16:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `ve`
--

CREATE TABLE `ve` (
  `id` int(11) NOT NULL,
  `ma_ve` varchar(50) NOT NULL,
  `chuyen_bay_id` int(11) NOT NULL,
  `hang_ve` enum('Thuong','Thuong gia') NOT NULL DEFAULT 'Thuong',
  `gia` decimal(10,2) NOT NULL,
  `so_luong_con` int(11) NOT NULL DEFAULT 0,
  `tao_luc` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ve`
--

INSERT INTO `ve` (`id`, `ma_ve`, `chuyen_bay_id`, `hang_ve`, `gia`, `so_luong_con`, `tao_luc`) VALUES
(1, 'VN1126-THUONG', 12, 'Thuong', 1200000.00, 8, '2026-01-11 17:41:32'),
(2, 'VN1126-THUONG GIA', 12, 'Thuong gia', 2000000.00, 9, '2026-01-11 17:42:42'),
(3, 'VN010-THUONG', 8, 'Thuong', 8000000.00, 30, '2026-01-11 20:37:53'),
(4, 'VN010-THUONG GIA', 8, 'Thuong gia', 2000000.00, 30, '2026-01-11 20:38:19'),
(6, 'VN1000-THUONG GIA', 13, 'Thuong gia', 160000.00, 10, '2026-01-12 17:31:30'),
(7, 'VN10000-THUONG', 14, 'Thuong', 80000.00, 10, '2026-01-12 22:36:05'),
(8, 'VN10000-THUONG GIA', 14, 'Thuong', 9000.00, 10, '2026-01-12 22:36:58'),
(9, 'VN123456-THUONG', 15, 'Thuong', 9000.00, 45, '2026-01-13 14:12:51'),
(10, 'VN123456-THUONG GIA', 15, 'Thuong gia', 10000.00, 40, '2026-01-13 14:13:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chuyen_bay`
--
ALTER TABLE `chuyen_bay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chuyen_bay_may_bay_id` (`may_bay_id`);

--
-- Indexes for table `dat_ve`
--
ALTER TABLE `dat_ve`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nguoi_dung_id` (`nguoi_dung_id`),
  ADD KEY `chuyen_bay_id` (`chuyen_bay_id`),
  ADD KEY `idx_dat_ve_ve_id` (`ve_id`),
  ADD KEY `idx_dat_ve_user_status` (`nguoi_dung_id`,`trang_thai`);

--
-- Indexes for table `hanh_khach`
--
ALTER TABLE `hanh_khach`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dat_ve_id` (`dat_ve_id`);

--
-- Indexes for table `may_bay`
--
ALTER TABLE `may_bay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ma_may_bay` (`ma_may_bay`),
  ADD KEY `idx_may_bay_hang` (`hang_may_bay`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ve`
--
ALTER TABLE `ve`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ve_ma_ve` (`ma_ve`),
  ADD KEY `idx_ve_chuyen_bay_id` (`chuyen_bay_id`),
  ADD KEY `idx_ve_hang_ve` (`hang_ve`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chuyen_bay`
--
ALTER TABLE `chuyen_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dat_ve`
--
ALTER TABLE `dat_ve`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `hanh_khach`
--
ALTER TABLE `hanh_khach`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `may_bay`
--
ALTER TABLE `may_bay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ve`
--
ALTER TABLE `ve`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chuyen_bay`
--
ALTER TABLE `chuyen_bay`
  ADD CONSTRAINT `fk_chuyen_bay_may_bay` FOREIGN KEY (`may_bay_id`) REFERENCES `may_bay` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dat_ve`
--
ALTER TABLE `dat_ve`
  ADD CONSTRAINT `dat_ve_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dat_ve_ibfk_2` FOREIGN KEY (`chuyen_bay_id`) REFERENCES `chuyen_bay` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dat_ve_ve` FOREIGN KEY (`ve_id`) REFERENCES `ve` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `hanh_khach`
--
ALTER TABLE `hanh_khach`
  ADD CONSTRAINT `hanh_khach_ibfk_1` FOREIGN KEY (`dat_ve_id`) REFERENCES `dat_ve` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ve`
--
ALTER TABLE `ve`
  ADD CONSTRAINT `fk_ve_chuyen_bay` FOREIGN KEY (`chuyen_bay_id`) REFERENCES `chuyen_bay` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
