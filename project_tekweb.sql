-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 11:36 AM
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
-- Database: `project_tekweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absensi` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `jam` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_laporan`
--

CREATE TABLE `detail_laporan` (
  `id_detail_laporan` int(50) NOT NULL,
  `id_detprod` int(50) NOT NULL,
  `quantity` int(50) NOT NULL,
  `status_in_out` varchar(10) NOT NULL,
  `tanggal_in_out` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_produk`
--

CREATE TABLE `detail_produk` (
  `id_detprod` int(50) NOT NULL,
  `id_barang` int(50) NOT NULL,
  `id_ukuran` int(50) NOT NULL,
  `stok_toko` int(50) NOT NULL,
  `stok_gudang` int(50) NOT NULL,
  `status_aktif` int(10) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int(50) NOT NULL,
  `id_detprod` int(50) NOT NULL,
  `jumlah` int(50) NOT NULL,
  `subtotal` int(50) NOT NULL,
  `id_transaksi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(50) NOT NULL,
  `kode_karyawan` int(50) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nomor_telepon` int(30) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `jabatan` varchar(50) NOT NULL,
  `gaji` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nomor_telepon` int(10) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_barang` int(50) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `harga` int(50) DEFAULT NULL,
  `status_aktif` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_harga`
--

CREATE TABLE `riwayat_harga` (
  `id_rharga` int(50) NOT NULL,
  `id_barang` int(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `perubahan_harga` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(50) NOT NULL,
  `id_pelanggan` int(50) NOT NULL,
  `kategori_penjualan` varchar(50) NOT NULL,
  `harga_total` int(50) NOT NULL,
  `status_transaksi` varchar(50) NOT NULL DEFAULT 'selesai',
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ukuran`
--

CREATE TABLE `ukuran` (
  `id_ukuran` int(10) NOT NULL,
  `ukuran` varchar(10) NOT NULL,
  `status_aktif` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absensi`),
  ADD KEY `fk_id_karyawan` (`id_karyawan`);

--
-- Indexes for table `detail_laporan`
--
ALTER TABLE `detail_laporan`
  ADD PRIMARY KEY (`id_detail_laporan`),
  ADD KEY `fk_detprod_laporan` (`id_detprod`);

--
-- Indexes for table `detail_produk`
--
ALTER TABLE `detail_produk`
  ADD PRIMARY KEY (`id_detprod`),
  ADD KEY `fk_barang` (`id_barang`),
  ADD KEY `fk_ukuran` (`id_ukuran`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_transaksi` (`id_transaksi`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  ADD PRIMARY KEY (`id_rharga`),
  ADD KEY `fk_barang_riwayat` (`id_barang`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `ukuran`
--
ALTER TABLE `ukuran`
  ADD PRIMARY KEY (`id_ukuran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absensi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_laporan`
--
ALTER TABLE `detail_laporan`
  MODIFY `id_detail_laporan` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_produk`
--
ALTER TABLE `detail_produk`
  MODIFY `id_detprod` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_barang` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  MODIFY `id_rharga` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ukuran`
--
ALTER TABLE `ukuran`
  MODIFY `id_ukuran` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_id_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_laporan`
--
ALTER TABLE `detail_laporan`
  ADD CONSTRAINT `fk_detprod_laporan` FOREIGN KEY (`id_detprod`) REFERENCES `detail_produk` (`id_detprod`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_produk`
--
ALTER TABLE `detail_produk`
  ADD CONSTRAINT `fk_barang` FOREIGN KEY (`id_barang`) REFERENCES `produk` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ukuran` FOREIGN KEY (`id_ukuran`) REFERENCES `ukuran` (`id_ukuran`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `fk_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  ADD CONSTRAINT `fk_barang_riwayat` FOREIGN KEY (`id_barang`) REFERENCES `produk` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_id_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


INSERT INTO `ukuran` (`id_ukuran`, `ukuran`, `status_aktif`) VALUES
(1, 'S', 1),
(2, 'M', 1),
(3, 'L', 1);

INSERT INTO `produk` (`id_barang`, `kode_barang`, `harga`, `status_aktif`) VALUES
(1, 'TW001', 200000, 1),
(2, 'TM001', 300000, 1),
(3, 'BW001', 250000, 1),
(4, 'BM001', 150000, 1);

INSERT INTO `detail_produk` (`id_detprod`, `id_barang`, `id_ukuran`, `stok_toko`, `stok_gudang`, `status_aktif`) VALUES
(1, 1, 1, 1000, 2000, 1),
(2, 1, 2, 2000, 2500, 1),
(3, 1, 3, 1000, 4000, 1),
(4, 2, 1, 1500, 5000, 1),
(5, 2, 2, 1000, 3000, 1),
(6, 2, 3, 2000, 2000, 1),
(7, 3, 1, 1500, 5000, 1),
(8, 3, 2, 3000, 3000, 1),
(9, 3, 3, 1000, 2500, 1),
(10, 4, 1, 1500, 5000, 1),
(11, 4, 2, 1300, 3500, 1),
(12, 4, 3, 2000, 2000, 1);

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `nomor_telepon`, `alamat`) VALUES
(1, 'Kiana', '', ''),
(2, 'Ari', '081233547688', 'Jl. A.Yani no.19 Surabaya'),
(3, 'Brone', '08224356789', 'Jl. Pahlawan No.3 Surabaya'),
(4, 'Handi', '', ''),
(5, 'Jason', '', ''),
(6, 'Pina', '082245888755', 'Jl. Manyar no.10 Surabaya'),
(7, 'Kira', '', ''),
(8, 'Arya', '081287747698', 'Jl. A.Yani no.17 Surabaya'),
(9, 'Charles', '08224356555', 'Jl. Pahlawan No.39 Surabaya'),
(10, 'Hoya', '', ''),
(11, 'Jia', '', ''),
(12, 'Laura', '082245456212', 'Jl. Kertajaya no.11 Surabaya');


INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `kategori_penjualan`, `harga_total`, `status_transaksi`, `tanggal_transaksi`) VALUES
(1, 1, 'retail', 2200000, 'selesai', '2024-11-02 12:03:24'),
(2, 2, 'PO', 1950000, 'selesai', '2024-11-02 17:00:00'),
(3, 3, 'PO', 3600000, 'selesai', '2024-11-03 12:07:32'),
(4, 4, 'retail', 3000000, 'selesai', '2024-11-05 12:11:18'),
(5, 5, 'retail', 7500000, 'selesai', '2024-11-06 12:11:59'),
(6, 6, 'PO', 5000000, 'selesai', '2024-11-08 12:13:44'),
(7, 7, 'retail', 2200000, 'selesai', '2024-12-02 12:03:24'),
(8, 8, 'PO', 1950000, 'selesai', '2024-12-02 17:00:00'),
(9, 9, 'PO', 3600000, 'selesai', '2024-12-03 12:07:32'),
(10, 10, 'retail', 3000000, 'selesai', '2024-12-05 12:11:18'),
(11, 11, 'retail', 7500000, 'selesai', '2024-12-06 12:11:59'),
(12, 12, 'PO', 5000000, 'selesai', '2024-12-08 12:13:44');


INSERT INTO `detail_transaksi` (`id_detail`, `id_detprod`, `jumlah`, `subtotal`, `id_transaksi`) VALUES
(1, 1, 5, 1000000, 1),
(2, 6, 4, 1200000, 1),
(3, 6, 5, 1500000, 2),
(4, 10, 3, 450000, 2),
(5, 5, 12, 3600000, 3),
(6, 10, 20, 3000000, 4),
(7, 7, 30, 7500000, 5),
(8, 6, 5, 1500000, 6),
(9, 1, 10, 2000000, 6),
(10, 10, 10, 1500000, 6),
(11, 1, 5, 1000000, 7),
(12, 6, 4, 1200000, 7),
(13, 6, 5, 1500000, 8),
(14, 10, 3, 450000, 8),
(15, 5, 12, 3600000, 9),
(16, 10, 20, 3000000, 10),
(17, 7, 30, 7500000, 11),
(18, 6, 5, 1500000, 12),
(19, 1, 10, 2000000, 12),
(20, 10, 10, 1500000, 12);

