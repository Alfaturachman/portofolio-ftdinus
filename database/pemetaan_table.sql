-- SQL untuk membuat tabel pemetaan
-- Jalankan di phpMyAdmin atau MySQL client

CREATE TABLE IF NOT EXISTS `pemetaan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_portofolio` varchar(255) NOT NULL,
  `id_cpl` int(11) unsigned NOT NULL,
  `id_cpmk` int(11) unsigned NOT NULL,
  `id_sub_cpmk` int(11) unsigned NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_portofolio` (`id_portofolio`),
  KEY `id_cpl` (`id_cpl`),
  KEY `id_cpmk` (`id_cpmk`),
  KEY `id_sub_cpmk` (`id_sub_cpmk`),
  CONSTRAINT `pemetaan_ibfk_1` FOREIGN KEY (`id_portofolio`) REFERENCES `portofolio` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pemetaan_ibfk_2` FOREIGN KEY (`id_cpl`) REFERENCES `cpl` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pemetaan_ibfk_3` FOREIGN KEY (`id_cpmk`) REFERENCES `cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pemetaan_ibfk_4` FOREIGN KEY (`id_sub_cpmk`) REFERENCES `sub_cpmk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
