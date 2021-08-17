SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `category`;

DROP TABLE IF EXISTS `category_manga`;

DROP TABLE IF EXISTS `chapter`;

DROP TABLE IF EXISTS `item_ratings`;

DROP TABLE IF EXISTS `manga`;

DROP TABLE IF EXISTS `options`;

DROP TABLE IF EXISTS `page`;

DROP TABLE IF EXISTS `status`;

DROP TABLE IF EXISTS `users`;

DROP TABLE IF EXISTS `roles`;

DROP TABLE IF EXISTS `assigned_roles`;

DROP TABLE IF EXISTS `permissions`;

DROP TABLE IF EXISTS `permission_role`;

DROP TABLE IF EXISTS `password_reminders`;

DROP TABLE IF EXISTS `bookmarks`;

DROP TABLE IF EXISTS `ad`;

DROP TABLE IF EXISTS `placement`;

DROP TABLE IF EXISTS `ad_placement`;

DROP TABLE IF EXISTS `comictype`;

DROP TABLE IF EXISTS `posts`;

DROP TABLE IF EXISTS `queues`;

DROP TABLE IF EXISTS `failed_jobs`;

DROP TABLE IF EXISTS `comments`;

DROP TABLE IF EXISTS `tag`;

DROP TABLE IF EXISTS `manga_tag`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

CREATE TABLE `category_manga` (
  `manga_id` int(20) NOT NULL,
  `category_id` int(20) NOT NULL,
  PRIMARY KEY (`manga_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `chapter` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `slug` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `volume` int(50) DEFAULT NULL,
  `manga_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `item_ratings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `score` tinyint(4) NOT NULL DEFAULT '1',
  `added_on` timestamp NOT NULL DEFAULT NOW(),
  `ip_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_ratings_item_id_index` (`item_id`),
  KEY `item_ratings_ip_address_index` (`ip_address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `manga` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status_id` int(50) DEFAULT NULL,
  `author` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `otherNames` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `releaseDate` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `summary` varchar(5000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `cover` tinyint(1) DEFAULT NULL,
  `hot` tinyint(1) DEFAULT NULL,
  `caution` tinyint(1) NOT NULL DEFAULT '0',
  `views` int(50) DEFAULT '0',
  `bulkStatus` longtext CHARACTER SET utf8,
  `artist` VARCHAR(255) DEFAULT NULL,
  `type_id` int(50) DEFAULT NULL,
  `user_id` int(50) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `options_key_unique` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10;

CREATE TABLE `page` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `image` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` int(50) NOT NULL,
  `external` tinyint(1) NOT NULL DEFAULT '0',
  `chapter_id` int(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `status` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `label` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `confirmation_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `notify` TINYINT(1) DEFAULT '0',
  `avatar` TINYINT(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4;

CREATE TABLE IF NOT EXISTS `assigned_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assigned_roles_user_id_foreign` (`user_id`),
  KEY `assigned_roles_role_id_foreign` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16;

CREATE TABLE IF NOT EXISTS `permission_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_role_permission_id_foreign` (`permission_id`),
  KEY `permission_role_role_id_foreign` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27;

CREATE TABLE IF NOT EXISTS `password_reminders` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `user_id` int(50) NOT NULL,
  `manga_id` int(50) NOT NULL,
  `chapter_id` int(50) NOT NULL,
  `page_id` int(50) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ad` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `bloc_id` varchar(255) DEFAULT NULL,
  `code` text,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ad_placement` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `ad_id` int(50) NOT NULL,
  `placement_id` int(50) NOT NULL,
  `placement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `placement` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `comictype` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `label` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(50) NOT NULL DEFAULT '1',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `keywords` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `manga_id` INT(10) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `queues` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `retries` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payload` longtext COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` text COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `comment` longtext,
  `user_id` int(50) DEFAULT NULL,
  `post_id` int(50) DEFAULT NULL,
  `post_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parent_comment` int(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT NOW(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `manga_tag` (
  `manga_id` int(11) NOT NULL,
  `tag_id` varchar(50) NOT NULL,
  PRIMARY KEY (`manga_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(1, 'Action', 'action'),
(2, 'Adventure', 'adventure'),
(3, 'Comedy', 'comedy'),
(4, 'Doujinshi', 'doujinshi'),
(5, 'Drama', 'drama'),
(6, 'Ecchi', 'ecchi'),
(7, 'Fantasy', 'fantasy'),
(8, 'Gender Bender', 'gender-bender'),
(9, 'Harem', 'harem'),
(10, 'Historical', 'historical'),
(11, 'Horror', 'horror'),
(12, 'Josei', 'josei'),
(13, 'Martial Arts', 'martial-arts'),
(14, 'Mature', 'mature'),
(15, 'Mecha', 'mecha'),
(16, 'Mystery', 'mystery'),
(17, 'One Shot', 'one-shot'),
(18, 'Psychological', 'psychological'),
(19, 'Romance', 'romance'),
(20, 'School Life', 'school-life'),
(21, 'Sci-fi', 'sci-fi'),
(22, 'Seinen', 'seinen'),
(23, 'Shoujo', 'shoujo'),
(24, 'Shoujo Ai', 'shoujo-ai'),
(25, 'Shounen', 'shounen'),
(26, 'Shounen Ai', 'shounen-ai'),
(27, 'Slice of Life', 'slice-of-life'),
(28, 'Sports', 'sports'),
(29, 'Supernatural', 'supernatural'),
(30, 'Tragedy', 'tragedy'),
(31, 'Yaoi', 'yaoi'),
(32, 'Yuri', 'yuri');

INSERT INTO `options` (`id`, `key`, `value`) VALUES
(1, 'site.name', 'My Manga Reader'),
(2, 'site.slogan', 'Read Manga Online for Free'),
(3, 'site.description', 'Read your favorite manga scans and scanlations online at <em>my Manga Reader</em>. Read Manga Online, Absolutely Free and Updated Daily.'),
(4, 'site.theme', 'default.sandstone'),
(5, 'seo.keywords', 'manga,read manga,read manga online,manga online,online manga,manga reader, manga scans,english manga,naruto manga,bleach manga, one piece manga,manga chapter,read free manga,free manga,read free manga online,manga viewer'),
(6, 'seo.google.analytics', ''),
(7, 'seo.google.webmaster', ''),
(8, 'seo.title', 'My Manga Reader - Read Manga Online for Free'),
(9, 'seo.description', 'Read your favorite manga scans and scanlations online at my Manga Reader. Read Manga Online, Absolutely Free and Updated Daily.'),
(10, 'site.lang', 'en'),
(11, 'site.subscription', '{"subscribe":"false","admin_confirm":"false","email_confirm":"false","default_role":"2","address":"admin@mydomain.com","name":"my Manga Reader","mailing":"sendmail","host":"","port":"","username":"","password":""}'),
(12, 'site.orientation', 'ltr'),
(13, 'manga.options', ''),
(14, 'site.pagination', '{"homepage":"40","mangalist":"20","latest_release":"40","news_homepage":"10","newslist":"15"}'),
(15, 'reader.type', 'ppp'),
(16, 'site.menu', '{"home":"1","mangalist":"1","latest_release":"1","news":"1","random":"1"}'),
(17, 'site.comment', ''),
(18, 'seo.advanced', '{"info":{"title":{"value":"%manga_name% by %manga_author% - Info Page"},"description":{"value":"%manga_description%"},"keywords":{"value":"%manga_name%, %manga_author%, %manga_categories%"}},"reader":{"title":{"value":"%manga_name% Chapter %chapter_number% - Page %page_number%"},"description":{"value":"%manga_name% Chapter %chapter_number%:  %chapter_title% - Page %page_number%"},"keywords":{"value":"%manga_name%, %manga_author%, %manga_categories%"}},"news":{"title":{"value":"%post_title%"},"description":{"value":"%post_content%"},"keywords":{"value":"%post_keywords%"}},"mangalist":{"title":{"global":"1","value":""},"description":{"global":"1","value":""},"keywords":{"global":"1","value":""}},"latestrelease":{"title":{"global":"1","value":""},"description":{"global":"1","value":""},"keywords":{"global":"1","value":""}},"latestnews":{"title":{"global":"1","value":""},"description":{"global":"1","value":""},"keywords":{"global":"1","value":""}}}'),
(19, 'site.widgets', '{"0":{"type":"site_description"},"1":{"type":"social_buttons"},"1462221131778":{"type":"top_rates","title":"Top Manga","number":"10"}}'),
(20, 'site.cache', '{"reader":"120"}'),
(21, 'site.gdrive', ''),
(22, 'storage.type', 'server'),
(23, 'reader.mode', 'noreload');

INSERT INTO `status` (`id`, `label`) VALUES
(1, 'Ongoing'),
(2, 'Complete');

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `confirmation_code`, `status`, `remember_token`, `confirmed`) VALUES
(1, 'Administrator', 'admin', 'admin@yourdomain.com', '$2y$10$68L/jA405KNKD5Au7PIqiuNfssuwEfG1hlpfaRqG7p5ZwNgGIZ1NS', '', 'active', 'CppMD71VV91y9pVOu3AgBSAUXflRH4vbfxk9LcBbYQ6bMC6b60czINl5xaya', 1);

INSERT INTO `permissions` (`id`, `name`, `display_name`) VALUES
(1, 'manage_users', 'Manage Users'),
(2, 'view_manga', 'View Manga'),
(3, 'add_manga', 'Add Manga'),
(4, 'edit_manga', 'Edit Manga'),
(5, 'delete_manga', 'Delete Manga'),
(6, 'view_chapter', 'View Chapter'),
(7, 'add_chapter', 'Add Chapter'),
(8, 'edit_chapter', 'Edit Chapter'),
(9, 'delete_chapter', 'Delete Chapter'),
(10, 'manage_hotmanga', 'Manga Hot Manga'),
(11, 'manage_categories', 'Manage Categories'),
(12, 'edit_general', 'Edit General Settings'),
(13, 'edit_seo', 'Edit SEO'),
(14, 'edit_themes', 'Edit Themes'),
(15, 'edit_profile', 'Edit Profile'),
(16, 'view_permissions', 'View Permissions'),
(17, 'manage_roles', 'Manage Roles'),
(18, 'manage_teams', 'Manage Teams'),
(19, 'manage_posts', 'Manage Posts'),
(20, 'edit_subscription', 'Edit Subscription Options');

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Contributor'),
(3, 'Guest');

INSERT INTO `permission_role` (`id`, `permission_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1), 
(17, 17, 1), 
(18, 18, 1), 
(19, 19, 1), 
(20, 20, 1),
(21, 2, 2),
(22, 3, 2),
(23, 4, 2),
(24, 5, 2),
(25, 6, 2),
(26, 7, 2),
(27, 8, 2),
(28, 9, 2),
(29, 15, 2),
(30, 2, 3),
(31, 6, 3);

INSERT INTO `assigned_roles` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1);

INSERT INTO `placement` (`id`, `page`) VALUES
(1, 'READER'),
(2, 'HOMEPAGE'),
(3, 'MANGAINFO');
