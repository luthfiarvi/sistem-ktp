-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Nov 2025 pada 07.28
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ktp`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akte`
--

CREATE TABLE `akte` (
  `id_akte` int(11) NOT NULL,
  `id_formulir` int(11) DEFAULT NULL,
  `file_akte` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `formulir`
--

CREATE TABLE `formulir` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT 'L',
  `alamat` text DEFAULT NULL,
  `rt` varchar(3) DEFAULT NULL,
  `rw` varchar(3) DEFAULT NULL,
  `ttd` varchar(255) DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status_permohonan` varchar(32) DEFAULT 'Diajukan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelurahan`
--

CREATE TABLE `kelurahan` (
  `id_kelurahan` int(11) NOT NULL,
  `nama_kelurahan` varchar(100) DEFAULT 'Munjul',
  `kecamatan` varchar(100) DEFAULT 'Cipayung',
  `kota` varchar(100) DEFAULT 'Jakarta Timur',
  `provinsi` varchar(100) DEFAULT 'DKI Jakarta',
  `kode_pos` varchar(10) DEFAULT '13850',
  `luas_wilayah` decimal(5,2) DEFAULT 4.85,
  `jumlah_rw` int(11) DEFAULT 9,
  `jumlah_rt` int(11) DEFAULT 78,
  `kepala_kelurahan` varchar(100) DEFAULT NULL,
  `sejarah` text DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelurahan`
--

INSERT INTO `kelurahan` (`id_kelurahan`, `nama_kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `luas_wilayah`, `jumlah_rw`, `jumlah_rt`, `kepala_kelurahan`, `sejarah`, `lokasi`, `foto_url`) VALUES
(1, 'Munjul', 'Cipayung', 'Jakarta Timur', 'DKI Jakarta', '13850', 4.85, 9, 78, 'Nama Kepala Kelurahan (isi sesuai data resmi)', 'Kelurahan Munjul dahulu bagian dari Desa Setu yang kemudian dimekarkan untuk meningkatkan pelayanan masyarakat. Wilayah ini dikenal dengan karakter semi-perkotaan, memiliki area hijau luas dan komunitas aktif dalam kegiatan sosial serta keagamaan.', 'https://cipayung.jakarta.go.id/', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kk`
--

CREATE TABLE `kk` (
  `id_kk` int(11) NOT NULL,
  `id_formulir` int(11) DEFAULT NULL,
  `file_kk` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_ktp_lengkap`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_ktp_lengkap` (
`id_formulir` int(11)
,`nik` varchar(20)
,`nama` varchar(100)
,`tempat_lahir` varchar(50)
,`tanggal_lahir` date
,`jenis_kelamin` enum('L','P')
,`alamat` text
,`rt` varchar(3)
,`rw` varchar(3)
,`ttd` varchar(255)
,`agama` varchar(50)
,`pekerjaan` varchar(100)
,`status` varchar(50)
,`foto` varchar(255)
,`file_kk` varchar(255)
,`file_akte` varchar(255)
,`nama_kelurahan` varchar(100)
,`kecamatan` varchar(100)
,`kota` varchar(100)
,`provinsi` varchar(100)
,`kode_pos` varchar(10)
,`kepala_kelurahan` varchar(100)
,`sejarah` text
,`lokasi` varchar(255)
,`status_permohonan` varchar(32)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_ktp_lengkap`
--
DROP TABLE IF EXISTS `view_ktp_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_ktp_lengkap`  AS SELECT `f`.`id` AS `id_formulir`, `f`.`nik` AS `nik`, `f`.`nama` AS `nama`, `f`.`tempat_lahir` AS `tempat_lahir`, `f`.`tanggal_lahir` AS `tanggal_lahir`, `f`.`jenis_kelamin` AS `jenis_kelamin`, `f`.`alamat` AS `alamat`, `f`.`rt` AS `rt`, `f`.`rw` AS `rw`, `f`.`ttd` AS `ttd`, `f`.`agama` AS `agama`, `f`.`pekerjaan` AS `pekerjaan`, `f`.`status` AS `status`, `f`.`foto` AS `foto`, `k`.`file_kk` AS `file_kk`, `a`.`file_akte` AS `file_akte`, `kel`.`nama_kelurahan` AS `nama_kelurahan`, `kel`.`kecamatan` AS `kecamatan`, `kel`.`kota` AS `kota`, `kel`.`provinsi` AS `provinsi`, `kel`.`kode_pos` AS `kode_pos`, `kel`.`kepala_kelurahan` AS `kepala_kelurahan`, `kel`.`sejarah` AS `sejarah`, `kel`.`lokasi` AS `lokasi`, `f`.`status_permohonan` AS `status_permohonan` FROM (((`formulir` `f` left join `kk` `k` on(`f`.`id` = `k`.`id_formulir`)) left join `akte` `a` on(`f`.`id` = `a`.`id_formulir`)) left join `kelurahan` `kel` on(1 = 1)) ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_history`
--

CREATE TABLE `status_history` (
  `id` int(11) NOT NULL,
  `id_formulir` int(11) NOT NULL,
  `status` varchar(32) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akte`
--
ALTER TABLE `akte`
  ADD PRIMARY KEY (`id_akte`),
  ADD KEY `id_formulir` (`id_formulir`);

--
-- Indeks untuk tabel `formulir`
--
ALTER TABLE `formulir`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indeks untuk tabel `kelurahan`
--
ALTER TABLE `kelurahan`
  ADD PRIMARY KEY (`id_kelurahan`);

--
-- Indeks untuk tabel `kk`
--
ALTER TABLE `kk`
  ADD PRIMARY KEY (`id_kk`),
  ADD KEY `id_formulir` (`id_formulir`);

--
-- Indeks untuk tabel `status_history`
--
ALTER TABLE `status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_formulir` (`id_formulir`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akte`
--
ALTER TABLE `akte`
  MODIFY `id_akte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `formulir`
--
ALTER TABLE `formulir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kelurahan`
--
ALTER TABLE `kelurahan`
  MODIFY `id_kelurahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `kk`
--
ALTER TABLE `kk`
  MODIFY `id_kk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `status_history`
--
ALTER TABLE `status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `akte`
--
ALTER TABLE `akte`
  ADD CONSTRAINT `akte_ibfk_1` FOREIGN KEY (`id_formulir`) REFERENCES `formulir` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kk`
--
ALTER TABLE `kk`
  ADD CONSTRAINT `kk_ibfk_1` FOREIGN KEY (`id_formulir`) REFERENCES `formulir` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `status_history`
  ADD CONSTRAINT `fk_status_formulir` FOREIGN KEY (`id_formulir`) REFERENCES `formulir` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
