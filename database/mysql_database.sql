CREATE TABLE IF NOT EXISTS `links` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link` varchar(255) NOT NULL,
  `link_text` varchar(50) NOT NULL,
  `image` varchar(155) DEFAULT NULL,
  `image_width` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `image_height` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `unique_page` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `social_bookmarks` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location` (`location`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `config` (`setting`, `value`) VALUES
('table_links', 'links'),
('table_content', 'pages'),
('table_social_bookmarks', 'social_bookmarks')