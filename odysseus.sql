DROP TABLE IF EXISTS `odyssey`;
CREATE TABLE IF NOT EXISTS `odyssey` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `password_hash` varchar(100) NOT NULL,
  `expiration_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;