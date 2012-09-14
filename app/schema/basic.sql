/* Syed Abidur Rahman <<<===>>> 15/09/2012 */
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `mtt_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL DEFAULT '',
  `ow` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `d_created` int(10) unsigned NOT NULL DEFAULT '0',
  `d_edited` int(10) unsigned NOT NULL DEFAULT '0',
  `sorting` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `taskview` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mtt_tag2task` (
  `tag_id` int(10) unsigned NOT NULL,
  `task_id` int(10) unsigned NOT NULL,
  `list_id` int(10) unsigned NOT NULL,
  KEY `tag_id` (`tag_id`),
  KEY `task_id` (`task_id`),
  KEY `list_id` (`list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mtt_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mtt_todolist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL DEFAULT '',
  `list_id` int(10) unsigned NOT NULL DEFAULT '0',
  `d_created` int(10) unsigned NOT NULL DEFAULT '0',
  `d_completed` int(10) unsigned NOT NULL DEFAULT '0',
  `d_edited` int(10) unsigned NOT NULL DEFAULT '0',
  `compl` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL,
  `note` text,
  `prio` tinyint(4) NOT NULL DEFAULT '0',
  `ow` int(11) NOT NULL DEFAULT '0',
  `tags` varchar(600) NOT NULL DEFAULT '',
  `tags_ids` varchar(250) NOT NULL DEFAULT '',
  `duedate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `list_id` (`list_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS=1;