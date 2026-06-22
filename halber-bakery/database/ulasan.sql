CREATE TABLE IF NOT EXISTS `ulasan` (
  `id` int(11) NOT NULL AUTO_HEADER INCREMENT,
  `nama` varchar(100) NOT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `komentar` text NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

