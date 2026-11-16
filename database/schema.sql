SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tlhp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `lhp_2022`
--

CREATE TABLE `lhp_2022` (
  `id_lhp` int(11) NOT NULL,
  `no_lhp` varchar(100) NOT NULL,
  `judul_lhp` varchar(255) NOT NULL,
  `pengelola` varchar(50) NOT NULL,
  `jumlah_uang` bigint(20) DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `file_lhp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sasaran` varchar(255) DEFAULT NULL,
  `perangkat_daerah` varchar(255) DEFAULT NULL,
  `desa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lhp_2023`
--

CREATE TABLE `lhp_2023` (
  `id_lhp` int(11) NOT NULL,
  `no_lhp` varchar(100) NOT NULL,
  `judul_lhp` varchar(255) NOT NULL,
  `pengelola` varchar(50) NOT NULL,
  `jumlah_uang` bigint(20) DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `file_lhp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sasaran` varchar(255) DEFAULT NULL,
  `perangkat_daerah` varchar(255) DEFAULT NULL,
  `desa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lhp_2024`
--

CREATE TABLE `lhp_2024` (
  `id_lhp` int(11) NOT NULL,
  `no_lhp` varchar(100) NOT NULL,
  `judul_lhp` varchar(255) NOT NULL,
  `pengelola` varchar(50) NOT NULL,
  `jumlah_uang` bigint(20) DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `file_lhp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sasaran` varchar(255) DEFAULT NULL,
  `perangkat_daerah` varchar(255) DEFAULT NULL,
  `desa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lhp_2025`
--

CREATE TABLE `lhp_2025` (
  `id_lhp` int(11) NOT NULL,
  `no_lhp` varchar(100) NOT NULL,
  `judul_lhp` varchar(255) NOT NULL,
  `pengelola` varchar(50) NOT NULL,
  `jumlah_uang` bigint(20) DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `file_lhp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sasaran` varchar(255) DEFAULT NULL,
  `perangkat_daerah` varchar(255) DEFAULT NULL,
  `desa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lhp_2026`
--

CREATE TABLE `lhp_2026` (
  `id_lhp` int(11) NOT NULL,
  `no_lhp` varchar(100) NOT NULL,
  `judul_lhp` varchar(255) NOT NULL,
  `pengelola` varchar(50) NOT NULL,
  `jumlah_uang` bigint(20) DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `file_lhp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sasaran` varchar(255) DEFAULT NULL,
  `perangkat_daerah` varchar(255) DEFAULT NULL,
  `desa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi_2022`
--

CREATE TABLE `rekomendasi_2022` (
  `id_rekomendasi` int(11) NOT NULL,
  `id_temuan` int(11) NOT NULL,
  `rekomendasi` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('sesuai','tidak_sesuai') DEFAULT NULL,
  `catatan_pemeriksa` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi_2023`
--

CREATE TABLE `rekomendasi_2023` (
  `id_rekomendasi` int(11) NOT NULL,
  `id_temuan` int(11) NOT NULL,
  `rekomendasi` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('sesuai','tidak_sesuai') DEFAULT NULL,
  `catatan_pemeriksa` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi_2024`
--

CREATE TABLE `rekomendasi_2024` (
  `id_rekomendasi` int(11) NOT NULL,
  `id_temuan` int(11) NOT NULL,
  `rekomendasi` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('sesuai','tidak_sesuai') DEFAULT NULL,
  `catatan_pemeriksa` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi_2025`
--

CREATE TABLE `rekomendasi_2025` (
  `id_rekomendasi` int(11) NOT NULL,
  `id_temuan` int(11) NOT NULL,
  `rekomendasi` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('sesuai','tidak_sesuai') DEFAULT NULL,
  `catatan_pemeriksa` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi_2026`
--

CREATE TABLE `rekomendasi_2026` (
  `id_rekomendasi` int(11) NOT NULL,
  `id_temuan` int(11) NOT NULL,
  `rekomendasi` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('sesuai','tidak_sesuai') DEFAULT NULL,
  `catatan_pemeriksa` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temuan_2022`
--

CREATE TABLE `temuan_2022` (
  `id_temuan` int(11) NOT NULL,
  `id_lhp` int(11) NOT NULL,
  `temuan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temuan_2023`
--

CREATE TABLE `temuan_2023` (
  `id_temuan` int(11) NOT NULL,
  `id_lhp` int(11) NOT NULL,
  `temuan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temuan_2024`
--

CREATE TABLE `temuan_2024` (
  `id_temuan` int(11) NOT NULL,
  `id_lhp` int(11) NOT NULL,
  `temuan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temuan_2025`
--

CREATE TABLE `temuan_2025` (
  `id_temuan` int(11) NOT NULL,
  `id_lhp` int(11) NOT NULL,
  `temuan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temuan_2026`
--

CREATE TABLE `temuan_2026` (
  `id_temuan` int(11) NOT NULL,
  `id_lhp` int(11) NOT NULL,
  `temuan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `level` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lhp_2022`
--
ALTER TABLE `lhp_2022`
  ADD PRIMARY KEY (`id_lhp`);

--
-- Indexes for table `lhp_2023`
--
ALTER TABLE `lhp_2023`
  ADD PRIMARY KEY (`id_lhp`);

--
-- Indexes for table `lhp_2024`
--
ALTER TABLE `lhp_2024`
  ADD PRIMARY KEY (`id_lhp`);

--
-- Indexes for table `lhp_2025`
--
ALTER TABLE `lhp_2025`
  ADD PRIMARY KEY (`id_lhp`);

--
-- Indexes for table `lhp_2026`
--
ALTER TABLE `lhp_2026`
  ADD PRIMARY KEY (`id_lhp`);

--
-- Indexes for table `rekomendasi_2022`
--
ALTER TABLE `rekomendasi_2022`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `idx_rekomendasi_temuan` (`id_temuan`);

--
-- Indexes for table `rekomendasi_2023`
--
ALTER TABLE `rekomendasi_2023`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `idx_rekomendasi_temuan` (`id_temuan`);

--
-- Indexes for table `rekomendasi_2024`
--
ALTER TABLE `rekomendasi_2024`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `idx_rekomendasi_temuan` (`id_temuan`);

--
-- Indexes for table `rekomendasi_2025`
--
ALTER TABLE `rekomendasi_2025`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `idx_rekomendasi_temuan` (`id_temuan`);

--
-- Indexes for table `rekomendasi_2026`
--
ALTER TABLE `rekomendasi_2026`
  ADD PRIMARY KEY (`id_rekomendasi`),
  ADD KEY `idx_rekomendasi_temuan` (`id_temuan`);

--
-- Indexes for table `temuan_2022`
--
ALTER TABLE `temuan_2022`
  ADD PRIMARY KEY (`id_temuan`),
  ADD KEY `idx_temuan_lhp` (`id_lhp`);

--
-- Indexes for table `temuan_2023`
--
ALTER TABLE `temuan_2023`
  ADD PRIMARY KEY (`id_temuan`),
  ADD KEY `idx_temuan_lhp` (`id_lhp`);

--
-- Indexes for table `temuan_2024`
--
ALTER TABLE `temuan_2024`
  ADD PRIMARY KEY (`id_temuan`),
  ADD KEY `idx_temuan_lhp` (`id_lhp`);

--
-- Indexes for table `temuan_2025`
--
ALTER TABLE `temuan_2025`
  ADD PRIMARY KEY (`id_temuan`),
  ADD KEY `idx_temuan_lhp` (`id_lhp`);

--
-- Indexes for table `temuan_2026`
--
ALTER TABLE `temuan_2026`
  ADD PRIMARY KEY (`id_temuan`),
  ADD KEY `idx_temuan_lhp` (`id_lhp`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lhp_2022`
--
ALTER TABLE `lhp_2022`
  MODIFY `id_lhp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lhp_2023`
--
ALTER TABLE `lhp_2023`
  MODIFY `id_lhp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lhp_2024`
--
ALTER TABLE `lhp_2024`
  MODIFY `id_lhp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lhp_2025`
--
ALTER TABLE `lhp_2025`
  MODIFY `id_lhp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lhp_2026`
--
ALTER TABLE `lhp_2026`
  MODIFY `id_lhp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rekomendasi_2022`
--
ALTER TABLE `rekomendasi_2022`
  MODIFY `id_rekomendasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rekomendasi_2023`
--
ALTER TABLE `rekomendasi_2023`
  MODIFY `id_rekomendasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rekomendasi_2024`
--
ALTER TABLE `rekomendasi_2024`
  MODIFY `id_rekomendasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rekomendasi_2025`
--
ALTER TABLE `rekomendasi_2025`
  MODIFY `id_rekomendasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rekomendasi_2026`
--
ALTER TABLE `rekomendasi_2026`
  MODIFY `id_rekomendasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temuan_2022`
--
ALTER TABLE `temuan_2022`
  MODIFY `id_temuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temuan_2023`
--
ALTER TABLE `temuan_2023`
  MODIFY `id_temuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temuan_2024`
--
ALTER TABLE `temuan_2024`
  MODIFY `id_temuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temuan_2025`
--
ALTER TABLE `temuan_2025`
  MODIFY `id_temuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temuan_2026`
--
ALTER TABLE `temuan_2026`
  MODIFY `id_temuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rekomendasi_2022`
--
ALTER TABLE `rekomendasi_2022`
  ADD CONSTRAINT `fk_rekomendasi_temuan_2022` FOREIGN KEY (`id_temuan`) REFERENCES `temuan_2022` (`id_temuan`) ON DELETE CASCADE;

--
-- Constraints for table `rekomendasi_2023`
--
ALTER TABLE `rekomendasi_2023`
  ADD CONSTRAINT `fk_rekomendasi_temuan_2023` FOREIGN KEY (`id_temuan`) REFERENCES `temuan_2023` (`id_temuan`) ON DELETE CASCADE;

--
-- Constraints for table `rekomendasi_2024`
--
ALTER TABLE `rekomendasi_2024`
  ADD CONSTRAINT `fk_rekomendasi_temuan_2024` FOREIGN KEY (`id_temuan`) REFERENCES `temuan_2024` (`id_temuan`) ON DELETE CASCADE;

--
-- Constraints for table `rekomendasi_2025`
--
ALTER TABLE `rekomendasi_2025`
  ADD CONSTRAINT `fk_rekomendasi_temuan_2025` FOREIGN KEY (`id_temuan`) REFERENCES `temuan_2025` (`id_temuan`) ON DELETE CASCADE;

--
-- Constraints for table `rekomendasi_2026`
--
ALTER TABLE `rekomendasi_2026`
  ADD CONSTRAINT `fk_rekomendasi_temuan` FOREIGN KEY (`id_temuan`) REFERENCES `temuan_2026` (`id_temuan`) ON DELETE CASCADE;

--
-- Constraints for table `temuan_2022`
--
ALTER TABLE `temuan_2022`
  ADD CONSTRAINT `fk_temuan_lhp_2022` FOREIGN KEY (`id_lhp`) REFERENCES `lhp_2022` (`id_lhp`) ON DELETE CASCADE;

--
-- Constraints for table `temuan_2023`
--
ALTER TABLE `temuan_2023`
  ADD CONSTRAINT `fk_temuan_lhp_2023` FOREIGN KEY (`id_lhp`) REFERENCES `lhp_2023` (`id_lhp`) ON DELETE CASCADE;

--
-- Constraints for table `temuan_2024`
--
ALTER TABLE `temuan_2024`
  ADD CONSTRAINT `fk_temuan_lhp_2024` FOREIGN KEY (`id_lhp`) REFERENCES `lhp_2024` (`id_lhp`) ON DELETE CASCADE;

--
-- Constraints for table `temuan_2025`
--
ALTER TABLE `temuan_2025`
  ADD CONSTRAINT `fk_temuan_lhp_2025` FOREIGN KEY (`id_lhp`) REFERENCES `lhp_2025` (`id_lhp`) ON DELETE CASCADE;

--
-- Constraints for table `temuan_2026`
--
ALTER TABLE `temuan_2026`
  ADD CONSTRAINT `fk_temuan_lhp` FOREIGN KEY (`id_lhp`) REFERENCES `lhp_2026` (`id_lhp`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
