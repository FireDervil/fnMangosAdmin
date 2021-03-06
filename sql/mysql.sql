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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fnma_news` (
  `storyid` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `created` int(10) NOT NULL DEFAULT '0',
  `published` int(10) NOT NULL DEFAULT '0',
  `expired` int(10) NOT NULL DEFAULT '0',
  `hostname` varchar(20) NOT NULL DEFAULT '',
  `nohtml` tinyint(5) NOT NULL DEFAULT '0',
  `nosmile` tinyint(5) NOT NULL DEFAULT '0',
  `hometext` text NOT NULL,
  `bodytext` text NOT NULL,
  `counter` int(8) NOT NULL DEFAULT '0',
  `topicid` smallint(4) NOT NULL DEFAULT '1',
  `ihome` tinyint(1) NOT NULL DEFAULT '0',
  `notifypub` tinyint(1) NOT NULL DEFAULT '0',
  `storie_type` varchar(5) NOT NULL DEFAULT '',
  `topicsdisplay` tinyint(1) NOT NULL DEFAULT '0',
  `topicsalgin` char(1) NOT NULL DEFAULT 'R',
  `comments` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`storyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE fnma_news_topics (
  `topic_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `topic_pid` smallint(4) unsigned NOT NULL DEFAULT '0',
  `topic_imgurl` varchar(20) NOT NULL DEFAULT '',
  `topic_title` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`topic_id`),
  KEY `pid` (`topic_pid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fnma_shop_items` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `item_number` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `itemset` int(10) NOT NULL DEFAULT '0',
  `gold` int(25) NOT NULL DEFAULT '0',
  `quanity` int(25) NOT NULL DEFAULT '1',
  `desc` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `wp_cost` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `realms` int(100) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fnma_acc_captcha` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(200) NOT NULL default '',
  `key` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;