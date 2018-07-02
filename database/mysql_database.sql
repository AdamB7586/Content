DROP TABLE IF EXISTS `links`;
CREATE TABLE IF NOT EXISTS `links` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link` varchar(255) NOT NULL,
  `link_text` varchar(50) NOT NULL,
  `image` varchar(155) DEFAULT NULL,
  `image_width` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `image_height` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uri` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `description` varchar(200) NOT NULL,
  `additional` TEXT NULL DEFAULT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `required` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page` (`site_id`,`url`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;