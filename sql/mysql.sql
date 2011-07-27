CREATE TABLE `fnma_voting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_ip` varchar(30) NOT NULL,
  `site` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fnma_vote_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hostname` varchar(255) NOT NULL,
  `votelink` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `reset_time` int(16) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `fnma_donate_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_id` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `account` int(8) DEFAULT NULL,
  `item_number` int(11) DEFAULT NULL,
  `buyer_email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `payment_type` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `payment_status` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pending_reason` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `reason_code` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `amount` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `item_given` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `fnma_donate_packages` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `desc` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `cost` varchar(11) CHARACTER SET latin1 NOT NULL DEFAULT '1.00',
  `points` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `fnma_account_groups` (
  `account_level` smallint(2) NOT NULL DEFAULT '1',
  `title` text CHARACTER SET latin1,
  PRIMARY KEY (`account_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE fnma_account_extend (
  `account_id` int(10) unsigned NOT NULL,
  `account_level` smallint(3) NOT NULL DEFAULT '1',
  `theme` smallint(3) NOT NULL DEFAULT '0',
  `last_visit` int(25) DEFAULT NULL,
  `registration_ip` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '0.0.0.0',
  `activation_code` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `secret_q1` text CHARACTER SET latin1,
  `secret_a1` text CHARACTER SET latin1,
  `secret_q2` text CHARACTER SET latin1,
  `secret_a2` text CHARACTER SET latin1,
  `web_points` int(3) NOT NULL DEFAULT '0',
  `points_earned` int(11) NOT NULL DEFAULT '0',
  `points_spent` int(11) NOT NULL DEFAULT '0',
  `date_points` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `points_today` int(11) NOT NULL DEFAULT '0',
  `total_donations` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT '0.00',
  `total_votes` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `fnma_account_keys` (
  `id` int(11) unsigned NOT NULL,
  `key` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `assign_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `fnma_online` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `user_name` varchar(200) NOT NULL DEFAULT 'Guest',
  `user_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0',
  `logged` int(10) NOT NULL DEFAULT '0',
  `currenturl` varchar(255) NOT NULL DEFAULT './',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=826 DEFAULT CHARSET=utf8;

CREATE TABLE `fnma_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(255) NOT NULL,
  `news_text` tinytext NOT NULL,
  `news_date` datetime DEFAULT NULL,
  `news_author` int(3) NOT NULL,
  `news_modifiy` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `news_sort_order` int(3) DEFAULT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;