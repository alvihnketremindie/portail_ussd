CREATE DATABASE IF NOT EXISTS `portailmoovci` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `portailmoovci`;

CREATE TABLE IF NOT EXISTS `numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msisdn` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `msisdn` (`msisdn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `postpaid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msisdn` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `msisdn` (`msisdn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msisdn` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `msisdn` (`msisdn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `test_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `telephone` varchar(11) NOT NULL,
  `canal` enum('USSD','SMS','IVR') NOT NULL DEFAULT 'USSD',
  `libelle` text NOT NULL,
  `urlORcontext` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telephone_2` (`telephone`,`code`),
  KEY `telephone` (`telephone`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
