CREATE TABLE IF NOT EXISTS `#__obrss` (
	`id`					int(11) unsigned NOT NULL auto_increment,
	`name`					varchar(255) NOT NULL default '',
	`alias`					varchar(255) NOT NULL default '',
	`description`			text NOT NULL default '',
	`published`				tinyint(1)	NOT NULL default '0',
	`feeded`				tinyint(1)	NOT NULL default '1',
	`display_feed_module`	tinyint(1)	NOT NULL default '1',
	`feed_type`				varchar(255) NOT NULL default 'RSS2.0',
	`feed_button`			varchar(255) NOT NULL default 'rss_2.0.png',
	`params`				text NOT NULL,
	`components`			varchar(50) NOT NULL default '',
	`paramsforowncomponent`	text NOT NULL,
	`created`				datetime NOT NULL default '0000-00-00 00:00:00',
	`created_by`			int(11) unsigned NOT NULL default '0',
	`modified`				datetime NOT NULL default '0000-00-00 00:00:00',
	`modified_by`			int(11) unsigned NOT NULL default '0',
	`checked_out_time`		datetime NOT NULL default '0000-00-00 00:00:00',
	`checked_out`			int(11)	unsigned NOT NULL default '0',
	`ordering`				int(11)	NOT NULL default '0',
	`hits` 					int(11)	NOT NULL default '0',
	`uri` 					varchar(255) NOT NULL default '',
	`use_feedburner`		tinyint(1)	NOT NULL default '2',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET `utf8`;