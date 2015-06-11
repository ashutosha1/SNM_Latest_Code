<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license      GNU/GPL
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class RegproInstaller {

	var $dbo;
	var $logList;
	var	$sqlStatements;
	var	$table;
	var	$tables;
	var $tableCols;
	var $tableCreates;
	var $tableChanges;
	var $imgLibs;

	function RegproInstaller() {
		$this->__construct();
	}

	function __construct() {
		$this->dbo = JFactory::getDBO();
		$this->dbo->debug(0);
		$this->logList = array();
		$this->sqlStatements = array();
		$this->tables = array();
		$this->tableCols = array();
		$this->tableCreates = array();
		$this->imgLibs = array('gd' => 0, 'imagemagick' => 0, 'imagick' => 0);

		$this->tableChanges = array(
			'#__registrationpro_dates' => array(
				array('column', 'add', 'parent_id', "ALTER TABLE `#__registrationpro_dates` ADD `parent_id` int(11) NOT NULL default '0'"),
				array('column', 'add', 'image', "ALTER TABLE `#__registrationpro_dates` ADD `image` tinyint(1) NOT NULL default '0'"),
				array('column', 'add', 'pdfimage', "ALTER TABLE `#__registrationpro_dates` ADD `pdfimage` tinyint(1) NOT NULL default '0'"),
				array('column', 'add', 'terms_conditions', "ALTER TABLE `#__registrationpro_dates` ADD `terms_conditions` text NOT NULL default '' AFTER max_attendance"),
				array('column', 'add', 'ordering', "ALTER TABLE `#__registrationpro_dates` ADD `ordering` int(11) NOT NULL default '0' AFTER terms_conditions"),
				array('column', 'modify', 'titel', "ALTER TABLE `#__registrationpro_dates` MODIFY `titel` varchar(200) NOT NULL default ''"),
				array('column', 'add', 'allowgroup', "ALTER TABLE `#__registrationpro_dates` ADD `allowgroup` TINYINT NOT NULL default '0'"),
				array('column', 'add', 'notifyemails', "ALTER TABLE `#__registrationpro_dates` ADD `notifyemails` text NOT NULL default ''"),
				array('column', 'add', 'shw_attendees', "ALTER TABLE `#__registrationpro_dates` ADD `shw_attendees` int(2) NOT NULL default 0"),
				array('column', 'add', 'recurrence_id',"ALTER TABLE `#__registrationpro_dates` ADD `recurrence_id` int(25) NOT NULL default 0"),
				array('column', 'add', 'recurrence_type', "ALTER TABLE `#__registrationpro_dates` ADD `recurrence_type` int(2) NOT NULL"),
				array('column', 'add', 'recurrence_number', "ALTER TABLE `#__registrationpro_dates` ADD `recurrence_number` int(2) NOT NULL"),
				array('column', 'add', 'recurrence_weekday', "ALTER TABLE `#__registrationpro_dates` ADD `recurrence_weekday` int(2) NOT NULL"),				
				array('column', 'add', 'recurrence_counter', "ALTER TABLE `#__registrationpro_dates` ADD `recurrence_counter` date NOT NULL"),				
				array('column', 'modify', 'notifydate', "ALTER TABLE `#__registrationpro_dates` MODIFY `notifydate` varchar(10) NOT NULL default ''"),
				array('column', 'modify', 'regstop', "ALTER TABLE `#__registrationpro_dates` MODIFY `regstop` varchar(10) NOT NULL default ''"),
				array('column', 'add', 'regstop_type', "ALTER TABLE `#__registrationpro_dates` ADD `regstop_type` tinyint(0) NOT NULL default '0' AFTER regstop"),	
				array('column', 'add', 'gateway_account', "ALTER TABLE `#__registrationpro_dates` ADD `gateway_account` VARCHAR(45) NOT NULL default ''"),
				array('column', 'add', 'force_groupregistration', "ALTER TABLE `#__registrationpro_dates` ADD `force_groupregistration` tinyint(0) NOT NULL default '0' AFTER allowgroup"),	
				array('column', 'add', 'user_id', "ALTER TABLE `#__registrationpro_dates` ADD `user_id` int(25) NOT NULL default '0' AFTER id"),
				array('column', 'add', 'payment_method', "ALTER TABLE `#__registrationpro_dates` ADD `payment_method` text NOT NULL default ''"),	
				array('column', 'add', 'regstarttimes', "ALTER TABLE `#__registrationpro_dates` ADD `regstarttimes` time NOT NULL default '00:00:00' AFTER regstart"),	
				array('column', 'add', 'regstoptimes', "ALTER TABLE `#__registrationpro_dates` ADD `regstoptimes` time NOT NULL default '00:00:00' AFTER regstop"),
				array('column', 'add', 'moderator_notify', "ALTER TABLE `#__registrationpro_dates` ADD `moderator_notify` tinyint(1) NOT NULL default '0'"),
				array('column', 'add', 'moderating_status', "ALTER TABLE `#__registrationpro_dates` ADD `moderating_status` tinyint(1) NOT NULL default '1'"),
				array('column', 'add', 'metadescription', "ALTER TABLE `#__registrationpro_dates` ADD `metadescription` text NOT NULL default ''"),	
				array('column', 'add', 'metakeywords', "ALTER TABLE `#__registrationpro_dates` ADD `metakeywords` text NOT NULL default ''"),
				array('column', 'add', 'metarobots', "ALTER TABLE `#__registrationpro_dates` ADD `metarobots` varchar(255) NOT NULL default ''"),
				array('column', 'add', 'viewaccess', "ALTER TABLE `#__registrationpro_dates` ADD `viewaccess` int(11) NOT NULL default '1' AFTER access"),
				array('column', 'add', 'session_page_header', "ALTER TABLE `#__registrationpro_dates` ADD `session_page_header` text NOT NULL default '' AFTER metarobots"),
				array('column', 'add', 'instructor', "ALTER TABLE `#__registrationpro_dates` ADD `instructor` varchar(200) NOT NULL default ''"),	
				array('column', 'add', 'enable_mailchimp', "ALTER TABLE `#__registrationpro_dates` ADD `enable_mailchimp` int(11) NOT NULL default '0'"),	
				array('column', 'add', 'mailchimp_list', "ALTER TABLE `#__registrationpro_dates` ADD `mailchimp_list` varchar(255) NOT NULL default ''"),
				array('column', 'add', 'enable_create_user', "ALTER TABLE `#__registrationpro_dates` ADD `enable_create_user` int(11) NOT NULL default '0'"),
				array('column', 'add', 'enabled_user_group', "ALTER TABLE `#__registrationpro_dates` ADD `enabled_user_group` int(11) NOT NULL default '2'"),
				),
			'#__registrationpro_transactions' => array(
			    array('column', 'modify', 'id', "ALTER TABLE `#__registrationpro_transactions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT"),
				array('column', 'add', 'p_id', "ALTER TABLE `#__registrationpro_transactions` ADD `p_id` int(25) NOT NULL default '0' AFTER reg_id"),
				array('column', 'add', 'AdminDiscount', "ALTER TABLE  `#__registrationpro_transactions` ADD  `AdminDiscount` VARCHAR( 23 ) NOT NULL DEFAULT  '' AFTER  `discount_amount`"),				
				array('column', 'add', 'p_type', "ALTER TABLE `#__registrationpro_transactions` ADD `p_type` enum('E','A') NOT NULL default 'E' AFTER p_id"),				
				array('column', 'add', 'payment_method', "ALTER TABLE `#__registrationpro_transactions` ADD `payment_method` varchar(100) NOT NULL default '' AFTER p_id"),				
				array('column', 'add', 'coupon_code', "ALTER TABLE `#__registrationpro_transactions` ADD `coupon_code` varchar(100) NOT NULL default '' AFTER test_ipn"),				
				array('column', 'add', 'discount_type', "ALTER TABLE `#__registrationpro_transactions` ADD `discount_type` enum('A', 'P') NOT NULL default 'P' AFTER coupon_code"),				
				array('column', 'add', 'discount', "ALTER TABLE `#__registrationpro_transactions` ADD `discount` float(10,2) NOT NULL default '0.00' AFTER discount_type"),				
				array('column', 'add', 'discount_amount', "ALTER TABLE `#__registrationpro_transactions` ADD `discount_amount` float(10,2) NOT NULL default '0.00' AFTER discount"),				
				array('column', 'add', 'quantity_gross', "ALTER TABLE `#__registrationpro_transactions` ADD `quantity_gross` int(11) NOT NULL default '0' AFTER address_city"),				
				array('column', 'add', 'price_without_tax', "ALTER TABLE `#__registrationpro_transactions` ADD `price_without_tax` float(10,2) NOT NULL default '0.00' AFTER test_ipn"),
				array('column', 'add', 'price', "ALTER TABLE `#__registrationpro_transactions` ADD `price` float(10,2) NOT NULL default '0.00' AFTER price_without_tax"),				
				array('column', 'add', 'final_price', "ALTER TABLE `#__registrationpro_transactions` ADD `final_price` float(10,2) NOT NULL default '0.00' AFTER price"),				
				array('column', 'add', 'cart_order_id', "ALTER TABLE `#__registrationpro_transactions` ADD `cart_order_id` varchar(50) NOT NULL default '' AFTER accesskey"),								
				array('column', 'add', 'order_number', "ALTER TABLE `#__registrationpro_transactions` ADD `order_number` varchar(50) NOT NULL default '' AFTER cart_order_id"),								
				array('column', 'add', 'payer_phone', "ALTER TABLE `#__registrationpro_transactions` ADD `payer_phone` varchar(50) NOT NULL default '' AFTER order_number"),				
				array('column', 'add', 'ip_country', "ALTER TABLE `#__registrationpro_transactions`  ADD `ip_country`  varchar(50) NOT NULL default '' AFTER payer_phone"),								
				array('column', 'add', 'md5key', "ALTER TABLE `#__registrationpro_transactions` ADD `md5key` varchar(100) NOT NULL default '' AFTER ip_country"),									
				array('column', 'add', 'offline_payment_details', "ALTER TABLE `#__registrationpro_transactions` ADD `offline_payment_details` text NOT NULL default '' AFTER md5key"),								
				array('column', 'add', 'tax_amount', "ALTER TABLE `#__registrationpro_transactions` ADD `tax_amount` float(10,2) NOT NULL default '0.00' AFTER tax"),	
			),
			'#__registrationpro_register' => array(
			    array('column', 'add', 'firstname', "ALTER TABLE `#__registrationpro_register` ADD `firstname` varchar(100) NOT NULL default '' AFTER products"),				
				array('column', 'add', 'lastname', "ALTER TABLE `#__registrationpro_register` ADD `lastname`  varchar(200) NOT NULL default '' AFTER firstname"),				
				array('column', 'add', 'email', "ALTER TABLE `#__registrationpro_register` ADD `email`  varchar(100) NOT NULL default '' AFTER lastname"),				
				array('column', 'add', 'active', "ALTER TABLE `#__registrationpro_register` ADD `active` tinyint(1) NOT NULL default '0' AFTER email"),				
				array('column', 'add', 'added_by', "ALTER TABLE `#__registrationpro_register` ADD `added_by` varchar(200) NOT NULL default 'user' AFTER active"),				
				array('column', 'add', 'temp_params', "ALTER TABLE `#__registrationpro_register` ADD `temp_params` text NOT NULL default '' AFTER status"),
				array('column', 'add', 'confirmation_send', "ALTER TABLE `#__registrationpro_register` ADD `confirmation_send` tinyint(1) NOT NULL default '0' AFTER notified"),
				array('column', 'add', 'group_added_by', "ALTER TABLE `#__registrationpro_register` ADD `group_added_by` int(11) NOT NULL default '0' AFTER added_by"),
				array('column', 'add', 'attended', "ALTER TABLE `#__registrationpro_register` ADD `attended` int(11) NOT NULL default '0' AFTER group_added_by"),	
			),
			'#__registrationpro_payment' => array(
			    array('column', 'add', 'tax', "ALTER TABLE `#__registrationpro_payment` ADD `tax`  float NOT NULL default '0' AFTER product_price"),				
				array('column', 'add', 'total_price', "ALTER TABLE `#__registrationpro_payment` ADD `total_price` float NOT NULL default '0' AFTER tax"),				
				array('column', 'add', 'ordering', "ALTER TABLE `#__registrationpro_payment` ADD `ordering` tinyint(4) NOT NULL default '0' AFTER shipping"),				
				array('column', 'add', 'type', "ALTER TABLE `#__registrationpro_payment` ADD `type` enum('E','A') NOT NULL default 'E' AFTER ordering"),
				array('column', 'add', 'product_quantity', "ALTER TABLE `#__registrationpro_payment` ADD `product_quantity` int(11) NOT NULL default '0' AFTER type"),	
				array('column', 'add', 'product_quantity_sold', "ALTER TABLE `#__registrationpro_payment` ADD `product_quantity_sold` int(11) NOT NULL default '0' AFTER product_quantity"),
				array('column', 'modify', 'ordering', "ALTER TABLE `#__registrationpro_payment` MODIFY `ordering` int(11) NOT NULL default '0'"),
				array('column', 'add', 'ticket_start', "ALTER TABLE `#__registrationpro_payment` ADD `ticket_start` date"),
				array('column', 'add', 'ticket_end', "ALTER TABLE `#__registrationpro_payment` ADD `ticket_end` date"),
				),
			'#__registrationpro_fields' => array(
			    array('column', 'modify', 'batch_display', "ALTER TABLE `#__registrationpro_fields` MODIFY `batch_display` tinyint(4) NOT NULL default '1'"),				
				array('column', 'modify', 'ordering', "ALTER TABLE `#__registrationpro_fields` MODIFY `ordering` int(11) NOT NULL default '100'"),				
				array('column', 'add', 'values', "ALTER TABLE `#__registrationpro_fields` ADD `values` text NOT NULL default '' AFTER inputtype"),				
				array('column', 'add', 'groupid', "ALTER TABLE `#__registrationpro_fields` ADD `groupid` int(25) NOT NULL default '0' AFTER batch_display"),				
				array('column', 'add', 'display_type', "ALTER TABLE `#__registrationpro_fields` ADD `display_type` tinyint(1) NOT NULL default '1' AFTER groupid"),
				array('column', 'add', 'confirm', "ALTER TABLE `#__registrationpro_fields` ADD `confirm` text NOT NULL default '' AFTER validation_rule"),								
				array('column', 'add', 'conditional_field', "ALTER TABLE `#__registrationpro_fields` ADD `conditional_field` varchar(255) NOT NULL default ''"),
				array('column', 'add', 'conditional_field_values', "ALTER TABLE `#__registrationpro_fields` ADD `conditional_field_values` text NOT NULL default ''"),
				array('column', 'add', 'conditional_field_name', "ALTER TABLE `#__registrationpro_fields` ADD `conditional_field_name` varchar(255) NOT NULL default ''"),				
				array('column', 'add', 'fees_field', "ALTER TABLE `#__registrationpro_fields` ADD `fees_field` tinyint(1) NOT NULL default '0'"),
				array('column', 'add', 'fees', "ALTER TABLE `#__registrationpro_fields` ADD `fees` text NOT NULL default ''"),
				array('column', 'add', 'fees_type', "ALTER TABLE `#__registrationpro_fields` ADD `fees_type` enum('A', 'P') NOT NULL default 'A'"),
				array('column', 'modify', 'published', "ALTER TABLE `#__registrationpro_fields` MODIFY `published` tinyint(4) NOT NULL default '1'"),
				),
				
			'#__registrationpro_cbfields' => array(
			    array('column', 'add', 'joomfishfield_id', "ALTER TABLE `#__registrationpro_cbfields` ADD `joomfishfield_id` int(11)"),
				array('column', 'add', 'corefield_id', "ALTER TABLE `#__registrationpro_cbfields` ADD `corefield_id` varchar(200) NOT NULL default ''"),
				),
			'#__registrationpro_forms' => array(
			    array('column', 'modify', 'checked_out', "ALTER TABLE `#__registrationpro_forms` MODIFY `checked_out` int(11) NOT NULL default '0'"),
				array('column', 'modify', 'published', "ALTER TABLE `#__registrationpro_forms` MODIFY `published` TINYINT(4) NOT NULL default '1'"),
				array('column', 'add', 'user_id', "ALTER TABLE `#__registrationpro_forms` ADD `user_id` int(25) NOT NULL default '0'"),
			),
			'#__registrationpro_coupons' => array(
			    array('column', 'add', 'eventids', "ALTER TABLE `#__registrationpro_coupons` ADD `eventids` varchar(255) NOT NULL default '0'"),
			),
			'#__registrationpro_categories' => array(
				array('column', 'add', 'user_id', "ALTER TABLE `#__registrationpro_categories` ADD `user_id` int(25) NOT NULL default '0' AFTER parentid"),
				array('column', 'add', 'parentid', "ALTER TABLE `#__registrationpro_categories` ADD `parentid` int(25) NOT NULL default '0' AFTER id"),
			),
			'#__registrationpro_locate' => array(
			    array('column', 'add', 'user_id', "ALTER TABLE `#__registrationpro_locate` ADD `user_id` int(25) NOT NULL default '0' AFTER id"),
				array('column', 'add', 'latitude', "ALTER TABLE `#__registrationpro_locate` ADD `latitude` varchar(255) NOT NULL default '0' "),
				array('column', 'add', 'longitude', "ALTER TABLE `#__registrationpro_locate` ADD `longitude` varchar(255) NOT NULL default '0' "),
			),
			'#__registrationpro_config' => array(
				array('value', 'insert', 'enablepdf', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='enablepdf'"),
				array('value', 'insert', 'cal_start_year', "INSERT INTO #__registrationpro_config SET setting_value='2000', setting_name='cal_start_year'"),
				array('value', 'insert', 'cal_end_year', "INSERT INTO #__registrationpro_config SET setting_value='2050', setting_name='cal_end_year'"),
				array('value', 'insert', 'collapse_categories', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='collapse_categories'"),				
				array('value', 'insert', 'maxseat', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='maxseat'"),				
				array('value', 'insert', 'pendingseat', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='pendingseat'"),				
				array('value', 'insert', 'registeredseat', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='registeredseat'"),				
				array('value', 'insert', 'showcategory', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='showcategory'"),				
				array('value', 'insert', 'duplicate_email_registration', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='duplicate_email_registration'"),				
				array('value', 'insert', 'default_userstatus_free_events', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='default_userstatus_free_events'"),				
				array('value', 'insert', 'default_userstatus_offline_payment', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='default_userstatus_offline_payment'"),				
				array('value', 'insert', 'quantitylimit', "INSERT INTO #__registrationpro_config SET setting_value='10', setting_name='quantitylimit'"),				
				array('value', 'insert', 'default_layout', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='default_layout'"),				
				array('value', 'insert', 'rss_enable', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='rss_enable'"),				
				array('value', 'insert', 'archiveby', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='archiveby'"),				
				array('value', 'insert', 'showeventdates', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='showeventdates'"),				
				array('value', 'insert', 'showeventtimes', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='showeventtimes'"),				
				array('value', 'insert', 'showpricecolumn', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='showpricecolumn'"),				
				array('value', 'insert', 'thankspagelink', "INSERT INTO #__registrationpro_config SET setting_value='', setting_name='thankspagelink'"),				
				array('value', 'insert', 'multiple_registration_button', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='multiple_registration_button'"),				
				array('value', 'insert', 'enable_discount_code', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='enable_discount_code'"),				
				array('value', 'insert', 'showlocationcolumn', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='showlocationcolumn'"),				
				array('value', 'insert', 'show_all_dates_in_calendar', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_all_dates_in_calendar'"),				
				array('value', 'insert', 'showshortdescriptioncolumn', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='showshortdescriptioncolumn'"),				
				array('value', 'insert', 'mainadminemailconfirmsubject', "INSERT INTO #__registrationpro_config SET setting_value='New user registration with {eventtitle}', setting_name='mainadminemailconfirmsubject'"),
				array('value', 'insert', 'mainadminemailconfirmbody', "INSERT INTO #__registrationpro_config SET setting_value='Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} which will take place at {location}.', setting_name='mainadminemailconfirmbody'"),
				array('value', 'insert', 'eventadminemailconfirmsubject', "INSERT INTO #__registrationpro_config SET setting_value='New user registration with {eventtitle}', setting_name='eventadminemailconfirmsubject'"),
				array('value', 'insert', 'eventadminemailconfirmbody', "INSERT INTO #__registrationpro_config SET setting_value='Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} which will take place at {location}.', setting_name='eventadminemailconfirmbody'"),	
				array('value', 'insert', 'eventlistordering', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='eventlistordering'"),
				array('value', 'insert', 'event_terms_and_conditions', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='event_terms_and_conditions'"),
				array('value', 'insert', 'disablethanksmessage', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='disablethanksmessage'"),
				array('value', 'update', 'formatdate', "UPDATE #__registrationpro_config SET setting_value='d m,Y' WHERE setting_name='formatdate'"),
				array('value', 'update', 'formattime', "UPDATE #__registrationpro_config SET setting_value='H:i:s' WHERE setting_name='formattime'"),
				array('value', 'insert', 'user_ids', "INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_ids'"),
				array('value', 'insert', 'user_categories', "INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_categories'"),
				array('value', 'insert', 'user_locations', "INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_locations'"),
				array('value', 'insert', 'frontend_help_link', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='frontend_help_link'"),
				array('value', 'insert', 'moderatoremailsubject', "INSERT INTO #__registrationpro_config SET setting_value='New event has been created by {fullname}', setting_name='moderatoremailsubject'"),		
				array('value', 'insert', 'moderatoremailbody', "INSERT INTO #__registrationpro_config SET setting_value='{fullname} is created the {eventtitle} will take place at {location}.', setting_name='moderatoremailsubject'"),
				array('value', 'insert', 'moderatoremail', "INSERT INTO #__registrationpro_config SET setting_value='', setting_name='moderatoremail'"),
				array('value', 'insert', 'event_moderation', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='event_moderation'"),
				array('value', 'insert', 'calendar_category_filter', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='calendar_category_filter'"),
				
				array('value', 'insert', 'message_color', "INSERT INTO #__registrationpro_config SET setting_value='#cf0000', setting_name='message_color'"),
				array('value', 'insert', 'show_max_seats_on_details_page', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_max_seats_on_details_page'"),
				array('value', 'insert', 'show_avaliable_seats_on_details_page', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_avaliable_seats_on_details_page'"),
				array('value', 'insert', 'show_registered_seats_on_details_page', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_registered_seats_on_details_page'"),
				array('value', 'insert', 'show_footer', "INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='show_footer'"),				
				array('value', 'insert', 'include_pending_reg', "INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='include_pending_reg'"),				
			),
		);
	}

	function runSQLFile($file) {
		$path = JPath::clean(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_registrationpro' . DS . 'sql' . DS . $file);
		$buffer = file_get_contents($path);

		if ( $buffer === false ) {
			$this->log('jssInstaller::runSQLFile: ' . JText::_('File not found') . ' ' . $file, 'error');
			return false;
		}

		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($buffer);

		if (count($queries) == 0) return true;

		$this->runQueryArray($queries);
		return count($queries);
	}
	
	function run() {
		$this->dbo->setQuery("SELECT setting_value FROM #__registrationpro_config WHERE setting_name = 'table_update'");
	    $config_table_update = $this->dbo->loadResult();

		$this->log('Detecting config table updation: ' . htmlentities($config_table_update));
		if($config_table_update != 'Y'){
			$this->log('Performing fresh installation');
			if (!$this->runSQLFile('regpro_config_mysql.sql')) {
				$this->log('Fresh config installation failed', 'error');
			}
		}
		$this->initUpgrade();
		$this->buildUpgradeStatements();
		$this->log('Performing upgrade');
		$this->runQueryArray($this->sqlStatements);
	}

	function errorHandler($msg) {
		// ERROR HANDLER
	}
	
	function initUpgrade() {
		$this->log('Initializing upgrade');
		$this->tables = array_keys($this->tableChanges);

		foreach ($this->tables as $tkey => $tvalue) {
			$this->tableCols[$tvalue] = $this->dbo->getTableColumns($tvalue);
			$this->tableCreates = $this->dbo->getTableCreate($tvalue);
			$this->tableCreates = array_map('splitCreates', $this->tableCreates);
		}
	}

	function log($message, $type = 'info') {
		$entry = new stdClass();
		$entry->message = $message;
		$entry->type = $type;
		$this->logList[] = $entry;
	}

	function runQueryArray($queries) {
		if (is_array($queries)) {
			foreach ($queries as $query) {
				$query = trim($query);
				if ($query != '' && $query{0} != '#') {
					$this->dbo->setQuery($query);
					if (!$this->dbo->query()) {
						$this->log('SQL "'.$query.'" failed', 'notice');
					}
				}
			}
			return count($queries);
		}
		return 0;
	}

	// Adds statements to the sqlStatements array if they can be executed E.g: Add a table column only if it does not already exist
	function buildUpgradeStatements() {
		foreach ($this->tableChanges as $table => $props) {
			foreach($props as $prop) {
				$type = $prop[0];
				$oper = $prop[1];
				$item = $prop[2];
				$stmnt = $prop[3];

				if ($oper == 'add' && !$this->itemExists($table, $type, $item)) {
					$this->sqlStatements[] = $stmnt;
				} elseif ($oper == 'drop' &$this->itemExists($table, $type, $item)) {
					$this->sqlStatements[] = $stmnt;
				} elseif ($oper == 'modify' &$this->itemExists($table, $type, $item)) {
					$this->sqlStatements[] = $stmnt;
				} elseif ($oper == 'insert' &$this->itemExists($table, $type, $item)) {
					$this->sqlStatements[] = $stmnt;
				} elseif ($oper == 'update' && !$this->itemExists($table, $type, $item)) {
					$this->sqlStatements[] = $stmnt;
				}
			}
		}
	}

	// Checks if an item (able columns, indexes) exists in the database
	function itemExists($table, $type, $item) {
		if ($type == 'column') {
			if (array_key_exists($item, $this->tableCols[$table])) {
				return true;
			}
		} elseif($type == 'index') {
			foreach ($this->tableCreates[$table] as $prop) {
				if (strpos($prop,'KEY') !== false && strpos($prop,"`$this->item`") !== false) {
					return true;
				}
			}
		} elseif ($type == 'table') {
			if (in_array($item, $this->tables)) {
				return true;
			}
		} elseif($type == 'value') {
			$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='".$item."'";
			$this->dbo->setQuery($query);
			$checkcnt = $this->dbo->loadResult();
			if($checkcnt == 0){
				return true;
			}
		}
		return false;
	}

	function insert_default_config_values() {
		$this->dbo->setQuery("DELETE FROM `#__registrationpro_config`"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (1, 'oldevent', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (2, 'minus', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (3, 'paypalemail', '')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (4, 'paypalmode', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (5, 'classsuffix', '-regpro')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (6, 'showtime', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (7, 'showevdesc', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (8, 'showtitle', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (9, 'showlocation', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (10,'showdetails', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (11,'showlongdesc', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (12,'showurl', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (13,'showmapserv', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (14,'map24id', '')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (15,'showhead', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (16,'showintro', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (17,'formatdate', 'd M,Y')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (18,'formattime', 'H:i:s')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (19,'introtext', '')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (20,'cbintegration', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (21,'cbchoose', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (22,'emailconfirmsubject', 'Congratulations! You registered to {eventtitle}')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (23,'emailconfirmbody', 'Dear {fullname}, <br /><br /> the {eventtitle} which will take place at {location}, starting from {eventstart}.')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (24,'emailstatussubject', 'The {eventtitle} changed it''s status to {eventstatus}...')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (25,'emailstatusbody', 'New status for {eventtitle}: {eventstatus}')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (26,'emailremindersubject', '{eventtitle} reminder')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (27,'emailreminderbody', 'This e-mail is a reminder for the {eventtitle}.')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (28,'currency_sign', '$')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (29,'currency_value', 'USD')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (30,'transaction_name', 'Payment for Registration Pro Ticket(s)')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (31,'register_notify', '')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (32,'showurl', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (33,'eventslimit', '10')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (34,'paymentmethod', 'Paypal Payment')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (35,'checkout_vendorid', '123')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (36,'checkout_secretword', '')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (37,'checkout_mode', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (38,'offlinepayment', 'This is offline payment for the event component.')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (39,'taxrate', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (40,'require_registration', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (41,'numberofdays', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (42,'emailtoregistersubject', 'Test subject')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (43,'emailtoregisterbody', 'Test Body')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (44,'table_update', '')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (45,'collapse_categories', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (46,'maxseat', '1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (47,'pendingseat', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (48,'registeredseat', '0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (49,'showcategory',0)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (50,'duplicate_email_registration',0)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (51,'default_userstatus_free_events',0)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (52,'default_userstatus_offline_payment',0)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (53,'quantitylimit',10)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (54,'default_layout',1)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (55,'rss_enable',0)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (56,'archiveby',1)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (57,'showeventdates',1)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (58,'showeventtimes',1)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (59,'showpricecolumn',1)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (60,'showlocationcolumn',0)"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (61,'thankspagelink','')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (62,'multiple_registration_button','1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (63,'enable_discount_code','1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (64,'show_all_dates_in_calendar','0')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (65,'showshortdescriptioncolumn','1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (66,'mainadminemailconfirmsubject','New user registration with {eventtitle}')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (67,'mainadminemailconfirmbody','Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} which will take place at {location}.')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (68,'eventadminemailconfirmsubject','New user registration with {eventtitle}')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (69,'eventadminemailconfirmbody','Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} which will take place at {location}.')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (70,'eventlistordering','1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (71,'event_terms_and_conditions','1')"); $this->dbo->query();
		$this->dbo->setQuery("INSERT INTO `#__registrationpro_config` VALUES (72,'disablethanksmessage','0')"); $this->dbo->query();
	}
}

function splitCreates($value) {
	return explode(',', $value);
}

?>