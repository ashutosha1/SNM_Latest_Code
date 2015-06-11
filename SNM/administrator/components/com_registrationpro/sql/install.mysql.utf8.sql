DROP TABLE IF EXISTS `#__registrationpro_settings`, `#__registrationpro_settings_jfish`, `#__registrationpro_currency`;

CREATE TABLE IF NOT EXISTS `#__registrationpro_dates` (
`id` int(11) unsigned NOT NULL auto_increment,
`user_id` int(25) NOT NULL default '0',
`locid` int(11) NOT NULL default '0',
`catsid` int(11) NOT NULL default '0',
`dates` date NOT NULL default '0000-00-00',
`times` time NOT NULL default '00:00:00',
`enddates` date NOT NULL default '0000-00-00',
`endtimes` time NOT NULL default '00:00:00',
`titel` varchar(200) NOT NULL default '',
`sendername` varchar(20) NOT NULL default '',
`sendermail` varchar(50) NOT NULL default '',
`deliverip` varchar(15) NOT NULL default '',
`deliverdate` varchar(20) NOT NULL default '',
`shortdescription` text NOT NULL,
`datdescription` text NOT NULL,
`datimage` varchar(100) NOT NULL default '',
`checked_out` int(11) NOT NULL default '0',
`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
`registra` tinyint(1) NOT NULL default '0',
`unregistra` tinyint(1) NOT NULL default '0',
`notifydate` varchar(10) NOT NULL default '0',
`published` tinyint(1) NOT NULL default '0',
`access` int(11) NOT NULL default '1',
`viewaccess` int(11) NOT NULL default '1',
`shw_attendees` int(2) NOT NULL default '0',
`regstart` date NOT NULL default '0000-00-00',
`regstarttimes` time NOT NULL default '00:00:00',
`regstop` varchar(10) NOT NULL default '0',
`regstoptimes` time NOT NULL default '00:00:00',
`regstop_type` tinyint(1) NOT NULL default '0',
`reqactivation` tinyint(1) NOT NULL default '1',
`status` tinyint(1) NOT NULL default '0',
`form_id` int(11) NOT NULL default '0',
`max_attendance` int(11) NOT NULL default '0',
`terms_conditions` text NOT NULL,
`ordering` int(11) NOT NULL default '0',
`datarange` TEXT , 
`allowgroup` tinyint(1) NOT NULL default '0',
`force_groupregistration` tinyint(1) NOT NULL default '0',
`notifyemails` text NOT NULL,
`recurrence_id` int(11) NOT NULL default '0',
`recurrence_type` int(11) NOT NULL default '0',
`recurrence_number` int(11) NOT NULL default '0',
`recurrence_weekday` int(11) NOT NULL default '0',
`recurrence_counter`  date NOT NULL default '0000-00-00',
`gateway_account` varchar(45) NULL default '',
`payment_method` text NOT NULL default '',
`moderator_notify` tinyint(1) NOT NULL default '0',
`moderating_status` tinyint(1) NOT NULL default '1',
`metadescription` text NOT NULL default '',
`metakeywords` text NOT NULL default '',
`metarobots` varchar(255) NOT NULL default '',
`session_page_header` text NOT NULL default '',
`instructor` varchar(200) NOT NULL default '',
`enable_create_user` int(11) NOT NULL default '0',
`enabled_user_group` int(11) NOT NULL default '2',
PRIMARY KEY  (`id`),
FULLTEXT KEY `titel` (`titel`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_locate` (
`id` int(11) unsigned NOT NULL auto_increment,
`user_id` int(25) NOT NULL default '0',
`club` varchar(50) NOT NULL default '',
`url` varchar(200) NOT NULL default '',
`street` varchar(50) default NULL,
`plz` varchar(20) default NULL,
`city` varchar(50) default NULL,
`country` char(2) default NULL,
`latitude` varchar(255) NOT NULL default '',
`longitude` varchar(255) NOT NULL default '',
`locdescription` text NOT NULL,
`locimage` varchar(100) NOT NULL default '',
`sendernameloc` varchar(20) NOT NULL default '',
`sendermailloc` varchar(50) NOT NULL default '',
`deliveriploc` varchar(15) NOT NULL default '',
`deliverdateloc` varchar(20) NOT NULL default '',
`publishedloc` tinyint(1) NOT NULL default '0',
`checked_out` int(11) NOT NULL default '0',
`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
`ordering` int(11) NOT NULL default '9999',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_categories` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`parentid` int(25) NOT NULL default '0',
	`user_id` int(25) NOT NULL default '0',
	`catname` varchar(100) NOT NULL default '',
	`catdescription` text NOT NULL,
	`image` varchar(100) NOT NULL default '',
	`background` varchar(6) NOT NULL default '',
	`publishedcat` tinyint(1) NOT NULL default '0',
	`checked_out` int(11) NOT NULL default '0',
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`access` int(11) unsigned NOT NULL default '0',
	`ordering` int(11) NOT NULL default '9999',
	PRIMARY KEY  (`id`)
)ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_register` (
	`rid` int(11) unsigned NOT NULL auto_increment,
	`rdid` int(11) NOT NULL default '0',
	`uid` int(11) NOT NULL default '0',
	`urname` varchar(20) NOT NULL default '0',
	`uregdate` varchar(50) NOT NULL default '',
	`uip` varchar(15) NOT NULL default '',
	`status` tinyint(4) NOT NULL default '0',
	`temp_params` text NOT NULL default '',
	`params` text NOT NULL default '',
	`notify` tinyint(4) NOT NULL default '0',
	`notified` tinyint(1) NOT NULL default '0',
	`confirmation_send` tinyint(1) NOT NULL default '0',
	`products` text NOT NULL default '',
	`firstname` varchar(100) NOT NULL default '',
	`lastname` varchar(200) NOT NULL default '',
	`email` varchar(100) NOT NULL default '',
	`active` tinyint(1) NOT NULL default '0',
	`added_by` varchar(200) NOT NULL default 'user',
	`group_added_by` int(11) NOT NULL default '0',
	`attended` int(11) NOT NULL default '0',
	PRIMARY KEY  (`rid`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_payment` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`regpro_dates_id` int(11) NOT NULL default '0',
	`product_name` varchar(255) NOT NULL default '',
	`product_description` text NOT NULL,
	`product_price` float(15,2) NOT NULL default '0.00',
	`tax` float NOT NULL default '0',
	`total_price` float(15,2) NOT NULL default '0.00',
	`shipping` tinyint(1) NOT NULL default '0',
	`ordering` int(11) NOT NULL default '9999',
	`type` enum('A','E') NOT NULL default 'E',
	`product_quantity` int(11) NOT NULL default '0',
	`product_quantity_sold` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_cbfields` (
	`id` INT(11) NOT NULL auto_increment,
	`form_id` INT(11) NOT NULL ,
	`cbfield_id` INT(11) NOT NULL,
	`joomfishfield_id` INT(11) NOT NULL,
	`corefield_id` varchar(200) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_settings_jfish` (
	`jfid` int(11) NOT NULL auto_increment,
	`thistable` varchar(50) default NULL,
	`datename` varchar(50) default NULL,
	`titlename` varchar(50) default NULL,
	`infobuttonname` varchar(50) default NULL,
	`locationname` varchar(50) default NULL,
	`cityname` varchar(50) default NULL,
	`catfroname` varchar(50) default NULL,
	`introtext` text,
	`shortdescription` varchar(50) default NULL,
	`max_attendance` varchar(50) default NULL,
	`price` varchar(50) default NULL,
	`availability` varchar(50) default NULL,
	PRIMARY KEY  (`jfid`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_config` (
	`id` int(11) NOT NULL auto_increment,
	`setting_name` varchar(64) NOT NULL default '',
	`setting_value` text NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_fields` (
	`id` int(11) NOT NULL auto_increment,
	`form_id` int(11) NOT NULL default '0',
	`name` varchar(255) NOT NULL default '',
	`title` varchar(255) NOT NULL default '',
	`description` text NOT NULL,
	`inputtype` varchar(255) NOT NULL default 'text',
	`values` text NOT NULL default '',
	`default_value` text NOT NULL,
	`params` text NOT NULL,
	`validation_rule` varchar(50) NOT NULL default '',
	`confirm` text NOT NULL default '',
	`ordering` int(11) NOT NULL default '100',
	`published` tinyint(4) NOT NULL default '0',
	`batch_display` tinyint(4) NOT NULL default '1',
	`groupid` int(25) NOT NULL default '0',
	`display_type` tinyint(1) NOT NULL default '1',	
	`conditional_field` varchar(255) NOT NULL default '',
	`conditional_field_values` text NOT NULL default '',
	`conditional_field_name` varchar(255) NOT NULL default '',
	`fees_field` tinyint(1) NOT NULL DEFAULT '0',
  	`fees` text NOT NULL,
  	`fees_type` enum('A','P') NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_forms` (
	`id` int(11) NOT NULL auto_increment,
	`user_id` int(25) NOT NULL default '0',
	`name` varchar(255) NOT NULL default '',
	`title` varchar(255) NOT NULL default '',
	`thankyou` text NOT NULL,
	`published` tinyint(4) NOT NULL default '0',
	`checked_out` int(11) NOT NULL default '0',
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_transactions` (
	`id` int(11) NOT NULL auto_increment,
	`reg_id` int(11) NOT NULL default '0',
	`p_id` int(25) NOT NULL default '0',
	`p_type` enum('E','A') NOT NULL default 'E',
	`payment_method` varchar(100) NOT NULL default '',
	`mc_gross` varchar(255) NOT NULL default '',
	`address_status` varchar(50) NOT NULL default '',
	`payer_id` varchar(50) NOT NULL default '',
	`tax` float NOT NULL default '0',
	`tax_amount` float(10,2) NOT NULL default '0.00',
	`address_street` varchar(255) NOT NULL default '',
	`payment_date` text NOT NULL,
	`payment_status` varchar(50) NOT NULL default '',
	`charset` varchar(50) NOT NULL default '',
	`address_zip` varchar(50) NOT NULL default '',
	`first_name` varchar(255) NOT NULL default '',
	`address_country_code` varchar(50) NOT NULL default '',
	`address_name` varchar(255) NOT NULL default '',
	`notify_version` varchar(50) NOT NULL default '',
	`custom` varchar(50) NOT NULL default '',
	`payer_status` varchar(50) NOT NULL default '',
	`address_country` varchar(50) NOT NULL default '',
	`address_city` varchar(50) NOT NULL default '',
	`quantity` int(11) NOT NULL default '0',
	`quantity_gross` int(11) NOT NULL default '0',
	`verify_sign` varchar(255) NOT NULL default '',
	`payer_email` varchar(128) NOT NULL default '',
	`txn_id` varchar(50) NOT NULL default '',
	`payment_type` varchar(50) NOT NULL default '',
	`last_name` varchar(255) NOT NULL default '',
	`address_state` varchar(50) NOT NULL default '',
	`pending_reason` varchar(255) NOT NULL default '',
	`receiver_email` varchar(255) NOT NULL default '',
	`txn_type` varchar(255) NOT NULL default '',
	`item_name` varchar(255) NOT NULL default '',
	`mc_currency` varchar(50) NOT NULL default '',
	`item_number` varchar(50) NOT NULL default '',
	`residence_country` varchar(50) NOT NULL default '',
	`test_ipn` varchar(50) NOT NULL default '',
	`coupon_code` varchar(100) NOT NULL,
	`discount_type` enum('A','P') NOT NULL default 'P',
	`discount` float(10,2) NOT NULL default '0.00',
	`discount_amount` float(10,2) NOT NULL default '0.00',
	`AdminDiscount` VARCHAR(23) NOT NULL default '',
	`price_without_tax` float(10,2) NOT NULL default '0.00',
	`price` float(10,2) NOT NULL default '0.00',
	`final_price` float(10,2) NOT NULL default '0.00',
	`payment_gross` float(10,2) NOT NULL default '0.00',
	`shipping` float(10,2) NOT NULL default '0.00',
	`accesskey` varchar(10) NOT NULL default '',
	`cart_order_id` varchar(50) NOT NULL,
	`order_number` varchar(100) NOT NULL,
	`payer_phone` varchar(30) NOT NULL,
	`ip_country` varchar(200) NOT NULL,
	`md5key` varchar(100) NOT NULL,
	`offline_payment_details` text NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(64) DEFAULT NULL,
  `currency_code` char(3) DEFAULT NULL,
  `currency_symbol` text CHARACTER SET latin1 COLLATE latin1_bin,
  PRIMARY KEY (`currency_id`),
  KEY `idx_currency_name` (`currency_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=158 ;

CREATE TABLE IF NOT EXISTS `#__registrationpro_coupons` (
	`id` int(25) NOT NULL auto_increment,
	`title` varchar(200) NOT NULL,
	`code` varchar(100) NOT NULL,
	`discount` float(9,2) NOT NULL default '0.00',
	`discount_type` enum('A','P') NOT NULL default 'P',
	`max_amount` float(9,2) NOT NULL default '0.00',
	`start_date` varchar(50) NOT NULL,
	`end_date` varchar(50) NOT NULL,
	`published` tinyint(1) NOT NULL default '0',
	`status` enum('A','O') NOT NULL default 'A',
	`eventids` varchar(255) NOT NULL default '0',
	`checked_out` int(11) NOT NULL default '0',
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
	`ordering` varchar(10) NOT NULL default '999',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_event_discount` (
   `id` int(11) NOT NULL auto_increment,
  `event_id` int(11) NOT NULL,
  `discount_name` varchar(255) NOT NULL,
  `discount_amount` float(9,2) NOT NULL,
  `discount_type` enum('P','A') NOT NULL default 'P',
  `min_tickets` tinyint(4) NOT NULL,
  `early_discount_date` date NOT NULL,
  `published` tinyint(1) NOT NULL default '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `ordering` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_event_discount_transactions` (
  `id` int(11) NOT NULL auto_increment,
  `trans_id` int(11) NOT NULL,
  `event_discount_amount` float(9,2) NOT NULL,
  `event_discount_type` enum('P','A') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_additional_from_field_fees` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `reg_id` int(25) NOT NULL,
  `additional_field_name` varchar(255) NOT NULL,
  `additional_field_fees` float(9,2) NOT NULL,
  `type` enum('A','P') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__registrationpro_usersconfig` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `user_id` int(25) NOT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `moderator` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';


CREATE TABLE IF NOT EXISTS `#__registrationpro_sessions` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `event_id` int(25) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `description` text NOT NULL,
  `fee` float(9,2) NOT NULL,
  `feetype` enum('A','P') NOT NULL DEFAULT 'A',
  `weekday` varchar(255) NOT NULL,
  `session_date` date NOT NULL,
  `session_start_time` time NOT NULL,
  `session_stop_time` time NOT NULL,
  `page_header` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';


CREATE TABLE IF NOT EXISTS `#__registrationpro_session_transactions` (
  `id` int(25) NOT NULL AUTO_INCREMENT,
  `reg_id` int(25) NOT NULL,
  `sessionid` int(25) NOT NULL,
  `sessionname` varchar(255) NOT NULL,
  `session_fees` float(9,2) NOT NULL,
  `type` enum('A','P') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `#__regpro_plugins`;

INSERT INTO `#__registrationpro_settings_jfish` VALUES (1, 'Layout Texts', 'Date', 'Title', 'Inf', 'Venue', 'City', 'Category', 'Insert your Introduction Text Here','Short Description','Max Attendance','Price','Availability');

INSERT INTO `#__registrationpro_currency` (`currency_id`, `currency_name`, `currency_code`, `currency_symbol`) VALUES
(1, 'Andorran Peseta', 'ADP', '&#x4c;&#x65;&#x6b;'),
(2, 'United Arab Emirates Dirham', 'AED', '&#x2f;&#x62e;&#x27;&#x655;&#x6;'),
(3, 'Afghanistan Afghani', 'AFN', '&#x60b;'),
(4, 'Albanian Lek', 'ALL', '&#x4c;&#x65;&#x6b;'),
(5, 'Netherlands Antillian Guilder', 'ANG', '&#192;'),
(6, 'Angolan Kwanza', 'AOK', 'Kz'),
(7, 'Argentinian Pesos', 'ARS', '&#x24;'),
(9, 'Australian Dollar', 'AUD', '&#x24;'),
(10, 'Aruban Florin', 'AWG', '&#192;'),
(11, 'Barbados Dollar', 'BBD', '&#x24;'),
(12, 'Bangladeshi Taka', 'BDT', '&#x54;&#x6b;'),
(14, 'Bulgarian Lev', 'BGN', '&#x43b;&#x432;'),
(15, 'Bahraini Dinar', 'BHD', '&#1576;.&#1583;'),
(16, 'Burundi Franc', 'BIF', '&#x46;&#x42;&#x75;'),
(17, 'Bermudian Dollar', 'BMD', '&#x24;'),
(18, 'Brunei Dollar', 'BND', '&#x24;'),
(19, 'Bolivian Boliviano', 'BOB', '&#x24;&#x62;'),
(20, 'Brazilian Real', 'BRL', '&#x52;&#x24;'),
(21, 'Bahamian Dollar', 'BSD', '&#x24;'),
(22, 'Bhutan Ngultrum', 'BTN', '&#x4e;&#x75;&#x2e;'),
(23, 'Burma Kyat', 'BUK', '&#x4b;'),
(24, 'Botswanian Pula', 'BWP', '&#x50;'),
(25, 'Belize Dollar', 'BZD', '&#x42;&#x5a;&#x24;'),
(26, 'Canadian Dollar', 'CAD', '&#x24;'),
(27, 'Swiss Franc', 'CHF', '&#x43;&#x48;&#x46;'),
(28, 'Chilean Unidades de Fomento', 'CLF', '&#x24;'),
(29, 'Chilean Peso', 'CLP', '&#x24;'),
(30, 'Yuan (Chinese) Renminbi', 'CNY', '&#xa5;'),
(31, 'Colombian Peso', 'COP', '&#x24;'),
(32, 'Costa Rican Colon', 'CRC', '&#x20a1;'),
(33, 'Czech Koruna', 'CZK', '&#x4b;&#x10d;'),
(34, 'Cuban Peso', 'CUP', '&#x20b1;'),
(35, 'Cape Verde Escudo', 'CVE', 'CV$'),
(36, 'Cyprus Pound', 'CYP', 'CY£'),
(40, 'Denmark Krone', 'DKK', '&#x6b;&#x72;'),
(41, 'Dominican Peso', 'DOP', '&#x52;&#x44;&#x24;'),
(42, 'Algerian Dinar', 'DZD', '&#1583;&#1580;'),
(43, 'Ecuador Sucre', 'ECS', '&#x24;'),
(44, 'Egyptian Pound', 'EGP', '&#xa3;'),
(46, 'Ethiopian Birr', 'ETB', '&#x42;&#x72;'),
(47, 'Euro', 'EUR', '&#x20ac;'),
(49, 'Fiji Dollar', 'FJD', '&#x24;'),
(50, 'Falkland Islands Pound', 'FKP', '&#xa3;'),
(52, 'British Pound', 'GBP', '&#xa3;'),
(53, 'Ghanaian Cedi', 'GHC', '&#xa2;'),
(54, 'Gibraltar Pound', 'GIP', '&#xa3;'),
(55, 'Gambian Dalasi', 'GMD', '&#x44;'),
(56, 'Guinea Franc', 'GNF', '&#x46;&#x47;'),
(58, 'Guatemalan Quetzal', 'GTQ', '&#x51;'),
(59, 'Guinea-Bissau Peso', 'XOF', 'CFA'),
(60, 'Guyanan Dollar', 'GYD', '&#x24;'),
(61, 'Hong Kong Dollar', 'HKD', '&#x24;'),
(62, 'Honduran Lempira', 'HNL', '&#x4c;'),
(63, 'Haitian Gourde', 'HTG', '&#x47;'),
(64, 'Hungarian Forint', 'HUF', '&#x46;&#x74;'),
(65, 'Indonesian Rupiah', 'IDR', '&#x52;&#x70;'),
(66, 'Irish Punt', 'IEP', '&#x00a3'),
(67, 'Israeli Shekel', 'ILS', '&#x20aa;'),
(68, 'Indian Rupee', 'INR', '&#2352;&#2369;'),
(69, 'Iraqi Dinar', 'IQD', '&#1593;.&#1583;'),
(70, 'Iranian Rial', 'IRR', '&#xfdfc;'),
(73, 'Jamaican Dollar', 'JMD', '&#x4a;&#x24;'),
(74, 'Jordanian Dinar', 'JOD', '&#x4a;&#x4f;&#x44;'),
(75, 'Japanese Yen', 'JPY', '&#xa5;'),
(76, 'Kenyan Schilling', 'KES', '&#x4b;&#x53;&#x68;'),
(77, 'Kampuchean (Cambodian) Riel', 'KHR', '&#x17db;'),
(78, 'Comoros Franc', 'KMF', '&#x4b;&#x4d;&#x46;'),
(79, 'North Korean Won', 'KPW', '&#x20a9;'),
(80, '(South) Korean Won', 'KRW', '&#x20a9;'),
(81, 'Kuwaiti Dinar', 'KWD', '&#1583;.&#1603;'),
(82, 'Cayman Islands Dollar', 'KYD', '&#x24;'),
(83, 'Lao Kip', 'LAK', '&#x20ad;'),
(84, 'Lebanese Pound', 'LBP', '&#xa3;'),
(85, 'Sri Lanka Rupee', 'LKR', '&#x20a8;'),
(86, 'Liberian Dollar', 'LRD', '&#x24;'),
(87, 'Lesotho Loti', 'LSL', '&#x4C;'),
(89, 'Libyan Dinar', 'LYD', '&#1604;.&#1583;'),
(90, 'Moroccan Dirham', 'MAD', '&#1583;.&#1605;'),
(91, 'Malagasy Franc', 'MGF', '&#x46;&#x4D;&#x47;'),
(92, 'Mongolian Tugrik', 'MNT', '&#x20ae;'),
(93, 'Macau Pataca', 'MOP', 'MO$'),
(94, 'Mauritanian Ouguiya', 'MRO', '&#x55;&#x4d;'),
(95, 'Maltese Lira', 'MTL', 'Lm'),
(96, 'Mauritius Rupee', 'MUR', '&#x20a8;'),
(97, 'Maldive Rufiyaa', 'MVR', '&#x52;&#x66;'),
(98, 'Malawi Kwacha', 'MWK', '&#x4d;&#x4b;'),
(99, 'Mexican Peso', 'MXN', '&#x24;'),
(100, 'Malaysian Ringgit', 'MYR', '&#x52;&#x4d;'),
(101, 'Mozambique Metical', 'MZM', 'MTn'),
(102, 'Nigerian Naira', 'NGN', '&#x20a6;'),
(103, 'Nicaraguan Cordoba', 'NIO', '&#x43;&#x24;'),
(105, 'Norwegian Kroner', 'NOK', '&#x6b;&#X72;'),
(106, 'Nepalese Rupee', 'NPR', '&#x20a8;'),
(107, 'New Zealand Dollar', 'NZD', '&#x24;'),
(108, 'Omani Rial', 'OMR', '&#xfdfc;'),
(109, 'Panamanian Balboa', 'PAB', '&#x42;&#x2f;&#x2e;'),
(110, 'Peruvian Inti', 'PEI', '&#x53;&#x2f;&#x2e;'),
(111, 'Papua New Guinea Kina', 'PGK', '&#x4b;'),
(112, 'Philippine Peso', 'PHP', '&#x20b1;'),
(113, 'Pakistan Rupee', 'PKR', '&#x20a8;'),
(114, 'Polish Zloty', 'PLN', '&#x7a;&#x142;'),
(116, 'Paraguay Guarani', 'PYG', '&#x47;&#x73;'),
(117, 'Qatari Rial', 'QAR', '&#xfdfc;'),
(118, 'Romanian Leu', 'RON', '&#x6c;&#x65;&#x69;'),
(119, 'Rwanda Franc', 'RWF', '&#x52;&#x46;'),
(120, 'Saudi Arabian Riyal', 'SAR', '&#xfdfc;'),
(121, 'Solomon Islands Dollar', 'SBD', '&#x24;'),
(122, 'Seychelles Rupee', 'SCR', '&#x20a8'),
(123, 'Sudanese Pound', 'SDG', '&#xa3;'),
(124, 'Swedish Krona', 'SEK', '&#x6b;&#x72;'),
(125, 'Singapore Dollar', 'SGD', '&#x24;'),
(126, 'St. Helena Pound', 'SHP', '&#xa3;'),
(127, 'Sierra Leone Leone', 'SLL', '&#x4c;&#x65;'),
(128, 'Somali Schilling', 'SOS', '&#x53;'),
(129, 'Suriname Guilder', 'SRG', '&#x24;'),
(130, 'Sao Tome and Principe Dobra', 'STD', '&#x44;&#x62;'),
(131, 'Russian Ruble', 'RUB', '&#x440;&#x443;&#x431;'),
(132, 'El Salvador Colon', 'SVC', '&#x24;'),
(133, 'Syrian Pound', 'SYP', '&#xa3;'),
(134, 'Swaziland Lilangeni', 'SZL', '&#x53;&#x5a;&#x4c;'),
(135, 'Thai Bath', 'THB', '&#xe3f;'),
(136, 'Tunisian Dinar', 'TND', '&#1583;.&#1578;'),
(137, 'Tongan Pa''anga', 'TOP', '&#x54;&#x24;'),
(138, 'East Timor Escudo', 'TPE', '&#x24;'),
(139, 'Turkish Lira', 'TRL', '&#x20a4;'),
(140, 'Trinidad and Tobago Dollar', 'TTD', '&#x54&#x54;&#x24;'),
(141, 'Taiwan Dollar', 'TWD', '&#x4e;&#x54;&#x24;'),
(142, 'Tanzanian Schilling', 'TZS', 'TSh'),
(143, 'Uganda Shilling', 'UGX', '&#x55&#x53;&#x68;'),
(144, 'US Dollar', 'USD', '&#x24;'),
(145, 'Uruguayan Peso', 'UYU', '&#x24;&#x55;'),
(146, 'Venezualan Bolivar', 'VEF', '&#x42;&#x73;'),
(147, 'Vietnamese Dong', 'VND', '&#x20ab;'),
(148, 'Vanuatu Vatu', 'VUV', '&#x56;&#x74;'),
(149, 'Samoan Tala', 'WST', '&#x57;&#x53;&#x24;'),
(150, 'Democratic Yemeni Dinar', 'YDD', 'YER'),
(151, 'Yemeni Rial', 'YER', '&#xfdfc;'),
(152, 'New Yugoslavia Dinar', 'YUD', '&#1056;&#1057;&#1044;'),
(153, 'South African Rand', 'ZAR', '&#x52;'),
(154, 'Zambian Kwacha', 'ZMK', '&#x5a;&#x4d;&#x4b;'),
(155, 'Zaire Zaire', 'ZRZ', 'F'),
(156, 'Zimbabwe Dollar', 'ZWD', '&#x5a;&#x24;'),
(157, 'Slovak Koruna', 'SKK', '&#x53;&#x6b;');