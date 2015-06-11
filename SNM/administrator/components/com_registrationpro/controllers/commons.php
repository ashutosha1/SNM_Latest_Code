<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class registrationproControllerCommons extends registrationproController {

	function __construct() {
		parent::__construct();
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_registrationpro' );
	}

	function upgradeDB(){

		global $mainframe;

		$db =JFactory::getDBO();

		// insert fields in dates table
		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `terms_conditions` text NOT NULL default '' AFTER max_attendance");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `ordering` tinyint(4) NOT NULL default '0' AFTER terms_conditions");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` MODIFY `ordering` int(11) NOT NULL default '0'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` MODIFY `titel` varchar(200) NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `instructor` varchar(200) NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `allowgroup` tinyint(1) DEFAULT '0' NOT NULL");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `notifyemails` text NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `shw_attendees` int(2) NOT NULL default 0");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `recurrence_id` int(25) NOT NULL default 0");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `recurrence_type` int(2) NOT NULL");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `recurrence_number` int(2) NOT NULL");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `recurrence_weekday` int(2) NOT NULL");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `recurrence_counter` date NOT NULL");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` MODIFY `notifydate` varchar(10) NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` MODIFY `regstop` date NOT NULL default '0000-00-00'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `regstop_type` tinyint(1) NOT NULL default '0' AFTER regstop");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `force_groupregistration` tinyint(1) NOT NULL default '0' AFTER allowgroup");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `payment_method` text NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `user_id` int(25) NOT NULL default '0' AFTER id");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `regstarttimes` time NOT NULL default '00:00:00' AFTER regstart");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `regstoptimes` time NOT NULL default '00:00:00' AFTER regstop");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `moderator_notify` tinyint(1) NOT NULL default '0'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `moderating_status` tinyint(1) NOT NULL default '1'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `metadescription` text NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `metakeywords` text NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `metarobots` varchar(255) NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `viewaccess` int(11) NOT NULL default '1' AFTER access");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `session_page_header` text NOT NULL default '' AFTER metarobots");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `enable_mailchimp` int(11) NOT NULL default '0' AFTER session_page_header");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `mailchimp_list` varchar(255) NOT NULL default '' AFTER enable_mailchimp");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `enable_create_user` int(11) NOT NULL default '0' AFTER mailchimp_list");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_dates` ADD `enabled_user_group` int(11) NOT NULL default '2' AFTER enable_create_user");
		$db->query();


		// insert fields in transactions table
		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `p_id` int(25) NOT NULL default '0' AFTER reg_id");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `p_type` enum('E','A') NOT NULL default 'E' AFTER p_id");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `payment_method` varchar(100) NOT NULL default '' AFTER p_id");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `coupon_code` varchar(100) NOT NULL default '' AFTER test_ipn");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `discount_type` enum('A', 'P') NOT NULL default 'P' AFTER coupon_code");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `discount` float(10,2) NOT NULL default '0.00' AFTER discount_type");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `discount_amount` float(10,2) NOT NULL default '0.00' AFTER discount");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `quantity_gross` int(11) NOT NULL default '0' AFTER address_city");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `price_without_tax` float(10,2) NOT NULL default '0.00' AFTER test_ipn");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `price`  float(10,2) NOT NULL default '0.00' AFTER price_without_tax");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `final_price`  float(10,2) NOT NULL default '0.00' AFTER price");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `cart_order_id`  varchar(50) NOT NULL default '' AFTER accesskey");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `order_number`  varchar(50) NOT NULL default '' AFTER cart_order_id");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `payer_phone`  varchar(50) NOT NULL default '' AFTER order_number");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions`  ADD `ip_country`  varchar(50) NOT NULL default '' AFTER payer_phone");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `md5key` varchar(100) NOT NULL default '' AFTER ip_country");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `offline_payment_details`  text NOT NULL default '' AFTER md5key");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_transactions` ADD `tax_amount` float(10,2) NOT NULL default '0.00' AFTER tax");
		$db->query();

		// insert fields in register table
		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `firstname`  varchar(100) NOT NULL default '' AFTER products");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `lastname`  varchar(200) NOT NULL default '' AFTER firstname");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `email`  varchar(100) NOT NULL default '' AFTER lastname");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `active` tinyint(1) NOT NULL default '0' AFTER email");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `added_by` varchar(200) NOT NULL default 'user' AFTER active");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `temp_params` text NOT NULL default '' AFTER status");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `group_added_by` int(11) NOT NULL default '0' AFTER added_by");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_register` ADD `attended` int(11) NOT NULL default '0' AFTER group_added_by");
		$db->query();

		// insert fields in payment table
		$db->setQuery("ALTER TABLE `#__registrationpro_payment` ADD `tax`  float NOT NULL default '0' AFTER product_price");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_payment` ADD `total_price`  float NOT NULL default '0' AFTER tax");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_payment` ADD `ordering`  int(11) NOT NULL default '0' AFTER shipping");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_payment` ADD `type` enum('E','A') NOT NULL default 'E' AFTER ordering");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_payment` MODIFY `ordering`  int(11) NOT NULL default '0' AFTER shipping");
		$db->query();

		// modify field in field table
		$db->setQuery("ALTER TABLE `#__registrationpro_fields` MODIFY `batch_display` tinyint(4) NOT NULL default '1'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` MODIFY `ordering` tinyint(11) NOT NULL default '100'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `values` text NOT NULL default '' AFTER inputtype");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `groupid` int(25) NOT NULL default '0' AFTER batch_display");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `display_type` tinyint(1) NOT NULL default '1' AFTER groupid");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `confirm` text NOT NULL default '' AFTER validation_rule");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `is_conditional` tinyint(1) NOT NULL default '0'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `conditional_field` varchar(255) NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `conditional_field_values` text NOT NULL default ''");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_fields` ADD `conditional_field_name` varchar(255) NOT NULL default ''");
		$db->query();

		// modify cb_fields table
		$db->setQuery("ALTER TABLE `#__registrationpro_cbfields` ADD `joomfishfield_id ` int(11)");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_cbfields` ADD `corefield_id` varchar(200) NOT NULL default ''");
		$db->query();

		// modify field in forms table
		$db->setQuery("ALTER TABLE `#__registrationpro_forms` MODIFY `checked_out` int(11) NOT NULL default '0'");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_forms` ADD `user_id` int(25) NOT NULL default '0' AFTER id");
		$db->query();

		// modify category table
		$db->setQuery("ALTER TABLE `#__registrationpro_categories` ADD `user_id` int(25) NOT NULL default '0' AFTER id");
		$db->query();

		// modify locate table
		$db->setQuery("ALTER TABLE `#__registrationpro_locate` ADD `user_id` int(25) NOT NULL default '0' AFTER id");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_locate` ADD `latitude` varchar(255) NOT NULL default '0' ");
		$db->query();

		$db->setQuery("ALTER TABLE `#__registrationpro_locate` ADD `longitude` varchar(255) NOT NULL default '0' ");
		$db->query();

		// insert values in config table
		// check collapse category already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='collapse_categories'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='collapse_categories'");
			$db->query();
		}

		// check maxseat record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='maxseat'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='maxseat'");
			$db->query();
		}

		// check pendingseat record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='pendingseat'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='pendingseat'");
			$db->query();
		}

		// check registerdseat record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='registeredseat'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='registeredseat'");
			$db->query();
		}

		// check showcategory record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='showcategory'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='showcategory'");
			$db->query();
		}

		// check duplicate_email_registration record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='duplicate_email_registration'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='duplicate_email_registration'");
			$db->query();
		}

		// check default_userstatus_free_events record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='default_userstatus_free_events'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='default_userstatus_free_events'");
			$db->query();
		}

		// check default_userstatus_offline_payment record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='default_userstatus_offline_payment'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='default_userstatus_offline_payment'");
			$db->query();
		}

		// check quantitylimit record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='quantitylimit'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='10', setting_name='quantitylimit'");
			$db->query();
		}

		// check default_layout record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='default_layout'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='default_layout'");
			$db->query();
		}
		// end

		// check rss_enable record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='rss_enable'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='rss_enable'");
			$db->query();
		}
		// end

		// check archiveby record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='archiveby'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='archiveby'");
			$db->query();
		}
		// end

		// check showeventdates record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='showeventdates'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='showeventdates'");
			$db->query();
		}
		// end

		// check showeventtimes record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='showeventtimes'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='showeventtimes'");
			$db->query();
		}
		// end

		// check showpricecolumn record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='showpricecolumn'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='showpricecolumn'");
			$db->query();
		}
		// end

		// check thankspagelink record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='thankspagelink'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='thankspagelink'");
			$db->query();
		}
		// end

		// check multiple_registration_button record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='multiple_registration_button'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='multiple_registration_button'");
			$db->query();
		}
		// end

		// check enable_discount_code record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='enable_discount_code'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='enable_discount_code'");
			$db->query();
		}
		// end

		// check showlocationcolumn record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='showlocationcolumn'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='showlocationcolumn'");
			$db->query();
		}
		// end

		// check show_all_dates_in_calendar record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_all_dates_in_calendar'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_all_dates_in_calendar'");
			$db->query();
		}
		// end

		// check showshortdescriptioncolumn record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='showshortdescriptioncolumn'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='showshortdescriptioncolumn'");
			$db->query();
		}
		// end

		// check mainadminemailconfirmsubject record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='mainadminemailconfirmsubject'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='New user regsiteration with {eventtitle}', setting_name='mainadminemailconfirmsubject'");
			$db->query();
		}
		// end

		// check mainadminemailconfirmbody record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='mainadminemailconfirmbody'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} will take place at {location}.', setting_name='mainadminemailconfirmbody'");
			$db->query();
		}
		// end

		// check eventadminemailconfirmsubject record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='eventadminemailconfirmsubject'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='New user regsiteration with {eventtitle}', setting_name='eventadminemailconfirmsubject'");
			$db->query();
		}
		// end

		// check eventadminemailconfirmbody record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='eventadminemailconfirmbody'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='Dear Admin, <br /><br /> {fullname} is registered with the {eventtitle} will take place at {location}.', setting_name='eventadminemailconfirmbody'");
			$db->query();
		}
		// end



		// check eventadminemailconfirmbody record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='eventlistordering'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='eventlistordering'");
			$db->query();
		}
		// end

		// check event_terms_and_conditions record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='event_terms_and_conditions'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='event_terms_and_conditions'");
			$db->query();
		}
		// end

		// check disablethanksmessage record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='disablethanksmessage'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='disablethanksmessage'");
			$db->query();
		}
		// end

		// check user_ids record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='user_ids'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_ids'");
			$db->query();
		}
		// end

		// check user_categories record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='user_categories'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_categories'");
			$db->query();
		}
		// end

		// check user_locations record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='user_locations'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_locations'");
			$db->query();
		}
		// end

		// check frontend_help_link record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='frontend_help_link'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='frontend_help_link'");
			$db->query();
		}
		// end

		// check moderatoremailsubject record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='moderatoremailsubject'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='New event has been created by {fullname}', setting_name='moderatoremailsubject'");
			$db->query();
		}
		// end

		// check moderatoremailbody record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='moderatoremailbody'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='{fullname} is created the {eventtitle} will take place at {location}.', setting_name='moderatoremailbody'");
			$db->query();
		}
		// end

		// check moderatoremail record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='moderatoremail'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='moderatoremail'");
			$db->query();
		}
		// end

		// check event_moderation record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='event_moderation'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='event_moderation'");
			$db->query();
		}
		// end

		// check calendar_category_filter record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='calendar_category_filter'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='calendar_category_filter'");
			$db->query();
		}
		// end

		// check message_color already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='message_color'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='#cf0000', setting_name='message_color'");
			$db->query();
		}
		// end

		// check show_max_seats_on_details_page already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_max_seats_on_details_page'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_max_seats_on_details_page'");
			$db->query();
		}
		// end

		// check show_avaliable_seats_on_details_page already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_avaliable_seats_on_details_page'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_avaliable_seats_on_details_page'");
			$db->query();
		}
		// end

		// check show_registered_seats_on_details_page already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_registered_seats_on_details_page'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_registered_seats_on_details_page'");
			$db->query();
		}
		// end

		// check show_footer already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_footer'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='show_footer'");
			$db->query();
		}
		// end
		
		// check show_poster already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_poster'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='show_poster'");
			$db->query();
		}
		// end

		// check show_calendar_registration_flag already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='show_calendar_registration_flag'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='show_calendar_registration_flag'");
			$db->query();
		}
		// end
		// check enablepdf record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='enablepdf'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='enablepdf'");
			$db->query();
		}

		// check countrylist record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='countrylist'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){

			echo $query = 'INSERT INTO #__registrationpro_config SET setting_value=\'<option value="United States" >United States</option>
  <option value="Canada" >Canada</option>
  <option value="Afghanistan" >Afghanistan</option>
  <option value="Albania" >Albania</option>
  <option value="Algeria" >Algeria</option>
  <option value="American Samoa" >American Samoa</option>

  <option value="Andorra" >Andorra</option>
  <option value="Angola" >Angola</option>
  <option value="Anguilla" >Anguilla</option>
  <option value="Antarctica" >Antarctica</option>
  <option value="Antigua And Barbuda" >Antigua And Barbuda</option>
  <option value="Argentina" >Argentina</option>

  <option value="Armenia" >Armenia</option>
  <option value="Aruba" >Aruba</option>
  <option value="Australia" >Australia</option>
  <option value="Austria" >Austria</option>
  <option value="Azerbaijan" >Azerbaijan</option>
  <option value="Bahamas" >Bahamas</option>

  <option value="Bahrain" >Bahrain</option>
  <option value="Bangladesh" >Bangladesh</option>
  <option value="Barbados" >Barbados</option>
  <option value="Belarus" >Belarus</option>
  <option value="Belgium" >Belgium</option>
  <option value="Belize" >Belize</option>

  <option value="Benin" >Benin</option>
  <option value="Bermuda" >Bermuda</option>
  <option value="Bhutan" >Bhutan</option>
  <option value="Bolivia" >Bolivia</option>
  <option value="Bosnia And Herzegowina" >Bosnia And Herzegowina</option>
  <option value="Botswana" >Botswana</option>

  <option value="Bouvet Island" >Bouvet Island</option>
  <option value="Brazil" >Brazil</option>
  <option value="British Indian Ocean Territory" >British Indian Ocean Territory</option>
  <option value="Brunei Darussalam" >Brunei Darussalam</option>
  <option value="Bulgaria" >Bulgaria</option>
  <option value="Burkina Faso" >Burkina Faso</option>

  <option value="Burundi" >Burundi</option>
  <option value="Cambodia" >Cambodia</option>
  <option value="Cameroon" >Cameroon</option>
  <option value="Cape Verde" >Cape Verde</option>
  <option value="Cayman Islands" >Cayman Islands</option>
  <option value="Central African Republic" >Central African Republic</option>

  <option value="Chad" >Chad</option>
  <option value="Chile" >Chile</option>
  <option value="China" >China</option>
  <option value="Christmas Island" >Christmas Island</option>
  <option value="Cocos (Keeling) Islands" >Cocos (Keeling) Islands</option>
  <option value="Colombia" >Colombia</option>

  <option value="Comoros" >Comoros</option>
  <option value="Congo" >Congo</option>
  <option value="Congo" >Congo</option>
  <option value=" The Democratic Republic Of The" > The Democratic Republic Of The</option>
  <option value="Cook Islands" >Cook Islands</option>
  <option value="Costa Rica" >Costa Rica</option>

  <option value="Cote D&rsquo;Ivoire" >Cote D&rsquo;Ivoire</option>
  <option value="Croatia (Local Name: Hrvatska)" >Croatia (Local Name: Hrvatska)</option>
  <option value="Cuba" >Cuba</option>
  <option value="Cyprus" >Cyprus</option>
  <option value="Czech Republic" >Czech Republic</option>
  <option value="Denmark" >Denmark</option>

  <option value="Djibouti" >Djibouti</option>
  <option value="Dominica" >Dominica</option>
  <option value="Dominican Republic" >Dominican Republic</option>
  <option value="East Timor" >East Timor</option>
  <option value="Ecuador" >Ecuador</option>
  <option value="Egypt" >Egypt</option>

  <option value="El Salvador" >El Salvador</option>
  <option value="Equatorial Guinea" >Equatorial Guinea</option>
  <option value="Eritrea" >Eritrea</option>
  <option value="Estonia" >Estonia</option>
  <option value="Ethiopia" >Ethiopia</option>
  <option value="Falkland Islands (Malvinas)" >Falkland Islands (Malvinas)</option>

  <option value="Faroe Islands" >Faroe Islands</option>
  <option value="Fiji" >Fiji</option>
  <option value="Finland" >Finland</option>
  <option value="France" >France</option>
  <option value="France" >France</option>
  <option value=" Metropolitan" > Metropolitan</option>

  <option value="French Guiana" >French Guiana</option>
  <option value="French Polynesia" >French Polynesia</option>
  <option value="French Southern Territories" >French Southern Territories</option>
  <option value="Gabon" >Gabon</option>
  <option value="Gambia" >Gambia</option>
  <option value="Georgia" >Georgia</option>

  <option value="Germany" >Germany</option>
  <option value="Ghana" >Ghana</option>
  <option value="Gibraltar" >Gibraltar</option>
  <option value="Greece" >Greece</option>
  <option value="Greenland" >Greenland</option>
  <option value="Grenada" >Grenada</option>

  <option value="Guadeloupe" >Guadeloupe</option>
  <option value="Guam" >Guam</option>
  <option value="Guatemala" >Guatemala</option>
  <option value="Guinea" >Guinea</option>
  <option value="Guinea-Bissau" >Guinea-Bissau</option>
  <option value="Guyana" >Guyana</option>

  <option value="Haiti" >Haiti</option>
  <option value="Heard And Mc Donald Islands" >Heard And Mc Donald Islands</option>
  <option value="Holy See (Vatican City State)" >Holy See (Vatican City State)</option>
  <option value="Honduras" >Honduras</option>
  <option value="Hong Kong" >Hong Kong</option>
  <option value="Hungary" >Hungary</option>

  <option value="Iceland" >Iceland</option>
  <option value="India" >India</option>
  <option value="Indonesia" >Indonesia</option>
  <option value="Iran (Islamic Republic Of)" >Iran (Islamic Republic Of)</option>
  <option value="Iraq" >Iraq</option>
  <option value="Ireland" >Ireland</option>

  <option value="Israel" >Israel</option>
  <option value="Italy" >Italy</option>
  <option value="Jamaica" >Jamaica</option>
  <option value="Japan" >Japan</option>
  <option value="Jordan" >Jordan</option>
  <option value="Kazakhstan" >Kazakhstan</option>

  <option value="Kenya" >Kenya</option>
  <option value="Kiribati" >Kiribati</option>
  <option value="Korea" >Korea</option>
  <option value=" Democratic People&rsquo;s Republic Of" > Democratic People&rsquo;s Republic Of</option>
  <option value="Korea" >Korea</option>
  <option value=" Republic Of" > Republic Of</option>

  <option value="Kuwait" >Kuwait</option>
  <option value="Kyrgyzstan" >Kyrgyzstan</option>
  <option value="Lao People&rsquo;s Democratic Republic" >Lao People&rsquo;s Democratic Republic</option>
  <option value="Latvia" >Latvia</option>
  <option value="Lebanon" >Lebanon</option>
  <option value="Lesotho" >Lesotho</option>

  <option value="Liberia" >Liberia</option>
  <option value="Libyan Arab Jamahiriya" >Libyan Arab Jamahiriya</option>
  <option value="Liechtenstein" >Liechtenstein</option>
  <option value="Lithuania" >Lithuania</option>
  <option value="Luxembourg" >Luxembourg</option>
  <option value="Macau" >Macau</option>

  <option value="Macedonia" >Macedonia</option>
  <option value=" Former Yugoslav Republic Of" > Former Yugoslav Republic Of</option>
  <option value="Madagascar" >Madagascar</option>
  <option value="Malawi" >Malawi</option>
  <option value="Malaysia" >Malaysia</option>
  <option value="Maldives" >Maldives</option>

  <option value="Mali" >Mali</option>
  <option value="Malta" >Malta</option>
  <option value="Marshall Islands" >Marshall Islands</option>
  <option value="Martinique" >Martinique</option>
  <option value="Mauritania" >Mauritania</option>
  <option value="Mauritius" >Mauritius</option>

  <option value="Mayotte" >Mayotte</option>
  <option value="Mexico" >Mexico</option>
  <option value="Micronesia" >Micronesia</option>
  <option value=" Federated States Of" > Federated States Of</option>
  <option value="Moldova" >Moldova</option>
  <option value=" Republic Of" > Republic Of</option>

  <option value="Monaco" >Monaco</option>
  <option value="Mongolia" >Mongolia</option>
  <option value="Montserrat" >Montserrat</option>
  <option value="Morocco" >Morocco</option>
  <option value="Mozambique" >Mozambique</option>
  <option value="Myanmar" >Myanmar</option>

  <option value="Namibia" >Namibia</option>
  <option value="Nauru" >Nauru</option>
  <option value="Nepal" >Nepal</option>
  <option value="Netherlands" >Netherlands</option>
  <option value="Netherlands Antilles" >Netherlands Antilles</option>
  <option value="New Caledonia" >New Caledonia</option>

  <option value="New Zealand" >New Zealand</option>
  <option value="Nicaragua" >Nicaragua</option>
  <option value="Niger" >Niger</option>
  <option value="Nigeria" >Nigeria</option>
  <option value="Niue" >Niue</option>
  <option value="Norfolk Island" >Norfolk Island</option>

  <option value="Northern Mariana Islands" >Northern Mariana Islands</option>
  <option value="Norway" >Norway</option>
  <option value="Oman" >Oman</option>
  <option value="Pakistan" >Pakistan</option>
  <option value="Palau" >Palau</option>
  <option value="Panama" >Panama</option>

  <option value="Papua New Guinea" >Papua New Guinea</option>
  <option value="Paraguay" >Paraguay</option>
  <option value="Peru" >Peru</option>
  <option value="Philippines" >Philippines</option>
  <option value="Pitcairn" >Pitcairn</option>
  <option value="Poland" >Poland</option>

  <option value="Portugal" >Portugal</option>
  <option value="Puerto Rico" >Puerto Rico</option>
  <option value="Qatar" >Qatar</option>
  <option value="Reunion" >Reunion</option>
  <option value="Romania" >Romania</option>
  <option value="Russian Federation" >Russian Federation</option>

  <option value="Rwanda" >Rwanda</option>
  <option value="Saint Kitts And Nevis" >Saint Kitts And Nevis</option>
  <option value="Saint Lucia" >Saint Lucia</option>
  <option value="Saint Vincent And The Grenadines" >Saint Vincent And The Grenadines</option>
  <option value="Samoa" >Samoa</option>
  <option value="San Marino" >San Marino</option>

  <option value="Sao Tome And Principe" >Sao Tome And Principe</option>
  <option value="Saudi Arabia" >Saudi Arabia</option>
  <option value="Senegal" >Senegal</option>
  <option value="Seychelles" >Seychelles</option>
  <option value="Sierra Leone" >Sierra Leone</option>
  <option value="Singapore" >Singapore</option>

  <option value="Slovakia (Slovak Republic)" >Slovakia (Slovak Republic)</option>
  <option value="Slovenia" >Slovenia</option>
  <option value="Solomon Islands" >Solomon Islands</option>
  <option value="Somalia" >Somalia</option>
  <option value="South Africa" >South Africa</option>
  <option value="South Georgia" >South Georgia</option>

  <option value=" South Sandwich Islands" > South Sandwich Islands</option>
  <option value="Spain" >Spain</option>
  <option value="Sri Lanka" >Sri Lanka</option>
  <option value="St. Helena" >St. Helena</option>
  <option value="St. Pierre And Miquelon" >St. Pierre And Miquelon</option>
  <option value="Sudan" >Sudan</option>

  <option value="Suriname" >Suriname</option>
  <option value="Svalbard And Jan Mayen Islands" >Svalbard And Jan Mayen Islands</option>
  <option value="Swaziland" >Swaziland</option>
  <option value="Sweden" >Sweden</option>
  <option value="Switzerland" >Switzerland</option>
  <option value="Syrian Arab Republic" >Syrian Arab Republic</option>

  <option value="Taiwan" >Taiwan</option>
  <option value="Tajikistan" >Tajikistan</option>
  <option value="Tanzania" >Tanzania</option>
  <option value=" United Republic Of" > United Republic Of</option>
  <option value="Thailand" >Thailand</option>
  <option value="Togo" >Togo</option>

  <option value="Tokelau" >Tokelau</option>
  <option value="Tonga" >Tonga</option>
  <option value="Trinidad And Tobago" >Trinidad And Tobago</option>
  <option value="Tunisia" >Tunisia</option>
  <option value="Turkey" >Turkey</option>
  <option value="Turkmenistan" >Turkmenistan</option>

  <option value="Turks And Caicos Islands" >Turks And Caicos Islands</option>
  <option value="Tuvalu" >Tuvalu</option>
  <option value="Uganda" >Uganda</option>
  <option value="Ukraine" >Ukraine</option>
  <option value="United Arab Emirates" >United Arab Emirates</option>
  <option value="United Kingdom" >United Kingdom</option>

  <option value="United States Minor Outlying Islands" >United States Minor Outlying Islands</option>
  <option value="Uruguay" >Uruguay</option>
  <option value="Uzbekistan" >Uzbekistan</option>
  <option value="Vanuatu" >Vanuatu</option>
  <option value="Venezuela" >Venezuela</option>
  <option value="Viet Nam" >Viet Nam</option>

  <option value="Virgin Islands (British)" >Virgin Islands (British)</option>
  <option value="Virgin Islands (U.S.)" >Virgin Islands (U.S.)</option>
  <option value="Wallis And Futuna Islands" >Wallis And Futuna Islands</option>
  <option value="Western Sahara" >Western Sahara</option>
  <option value="Yemen" >Yemen</option>
  <option value="Yugoslavia" >Yugoslavia</option>

  <option value="Zambia" >Zambia</option>
  <option value="Zimbabwe" >Zimbabwe</option>\', setting_name="countrylist"';

			$db->setQuery($query);
			$db->query();
		}
		// end


		// check statelist record already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='statelist'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){

			$query = 'INSERT INTO #__registrationpro_config SET setting_value=\'  <option value="Alabama" >Alabama</option>
  <option value="Alaska" >Alaska</option>
  <option value="American Samoa" >American Samoa</option>
  <option value="Arizona" >Arizona</option>
  <option value="Arkansas" >Arkansas</option>
  <option value=" Armed Forces Africa" > Armed Forces Africa</option>
  <option value=" Armed Forces Americas" > Armed Forces Americas</option>
  <option value=" Armed Forces Canada" > Armed Forces Canada</option>
  <option value=" Armed Forces Europe" > Armed Forces Europe</option>
  <option value=" Armed Forces Middle East" > Armed Forces Middle East</option>
  <option value=" Armed Forces Pacific " > Armed Forces Pacific </option>
  <option value="California" >California</option>
  <option value="Colorado" >Colorado</option>
  <option value="Connecticut" >Connecticut</option>
  <option value="Delaware" >Delaware</option>
  <option value="District Of Columbia" >District Of Columbia</option>
  <option value="Federated States Of Micronesia " >Federated States Of Micronesia </option>
  <option value="Florida" >Florida</option>
  <option value="Georgia" >Georgia</option>
  <option value=" Guam" > Guam</option>
  <option value="Hawaii" >Hawaii</option>
  <option value="Idaho" >Idaho</option>
  <option value="Illinois" >Illinois</option>
  <option value="Indiana" >Indiana</option>
  <option value="Iowa" >Iowa</option>
  <option value="Kansas" >Kansas</option>
  <option value="Kentucky" >Kentucky</option>
  <option value="Louisiana" >Louisiana</option>
  <option value="Maine" >Maine</option>
  <option value="Marshall Islands" >Marshall Islands</option>
  <option value="Maryland" >Maryland</option>
  <option value="Massachusetts" >Massachusetts</option>
  <option value="Michigan" >Michigan</option>
  <option value="Minnesota" >Minnesota</option>
  <option value="Mississippi" >Mississippi</option>
  <option value="Missouri" >Missouri</option>
  <option value="Montana" >Montana</option>
  <option value="Nebraska" >Nebraska</option>
  <option value="Nevada" >Nevada</option>
  <option value="New Hampshire" >New Hampshire</option>
  <option value="New Jersey" >New Jersey</option>
  <option value="New Mexico" >New Mexico</option>
  <option value="New York" >New York</option>
  <option value="North Carolina" >North Carolina</option>
  <option value="North Dakota" >North Dakota</option>
  <option value="Northern Mariana Islands" >Northern Mariana Islands</option>
  <option value="Ohio" >Ohio</option>
  <option value="Oklahoma" >Oklahoma</option>
  <option value="Oregon" >Oregon</option>
  <option value="Palau" >Palau</option>
  <option value="Puerto Rico" >Puerto Rico</option>
  <option value="Pennsylvania" >Pennsylvania</option>
  <option value="Rhode Island" >Rhode Island</option>
  <option value="South Carolina" >South Carolina</option>
  <option value="South Dakota" >South Dakota</option>
  <option value="Tennessee" >Tennessee</option>
  <option value="Texas" >Texas</option>
  <option value="Utah" >Utah</option>
  <option value="Vermont" >Vermont</option>
  <option value="Virgin Islands" >Virgin Islands</option>
  <option value="Virginia" >Virginia</option>
  <option value="Washington" >Washington</option>
  <option value="West Virginia" >West Virginia</option>
  <option value="Wisconsin" >Wisconsin</option>
  <option value="Wyoming" >Wyoming</option>\', setting_name="statelist"';

			$db->setQuery($query);
			$db->query();
		}
		// end


		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='disable_remiders'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='disable_remiders'");
			$db->query();
		}
		// end

		// check user_forms already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='user_forms'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_forms'");
			$db->query();
		}
		// end

		// check user_groups already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='user_groups'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='', setting_name='user_groups'");
			$db->query();
		}
		// end

		// check timezone_offset already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='timezone_offset'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='timezone_offset'");
			$db->query();
		}
		// end

		// check enable_mandatory_field_note already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='enable_mandatory_field_note'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='enable_mandatory_field_note'");
			$db->query();
		}
		// end

		// check calendar_weekday already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='calendar_weekday'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='calendar_weekday'");
			$db->query();
		}
		// end

		// check session_dateformat already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='session_dateformat'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='d M,Y', setting_name='session_dateformat'");
			$db->query();
		}
		// end

		// check session_timeformat already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='session_timeformat'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='H:i:s', setting_name='session_timeformat'");
			$db->query();
		}
		// end

		// check listing_button already exists and if not exists then insert new
		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='listing_button'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='1', setting_name='listing_button'");
			$db->query();
		}

		$query = "SELECT count(*) FROM #__registrationpro_config WHERE setting_name='accepted_registration_reports'";
		$db->setQuery($query);
		$checkcnt = $db->loadResult();

		if($checkcnt == 0){
			$db->setQuery("INSERT INTO #__registrationpro_config SET setting_value='0', setting_name='accepted_registration_reports'");
			$db->query();
		}
		// end

		$msg =  JText::_('ADMIN_UPGRADE_DB_MSG');
		$link = 'index.php?option=com_registrationpro';
		$this->setRedirect( $link, $msg );
	}


	function sendFeedback()
	{
		global $mainframe, $option,$regpro_mail;

		$db =JFactory::getDBO();

		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);

		$name 	= $post['fdname'];
		$org 	= $post['fdorgname'];
		$mail 	= $post['fdmail'];
		$type 	= $post['fdtype'];
		$prod 	= $post['fdprod'];
		$msg 	= $post['fdmsg'];
		$ver 	= $post['version'];

		// check server side validations (added by sdei on 19-Feb-2008)
			if(empty($name) || empty($mail) || empty($type) || empty($msg))	{
				 $this->setRedirect("index.php?option=$option&task=feedback", JText::_('ADMIN_MSG_FEEDBACK_REQUIRED_EMPTY'));
			}
		// end

		$sql = "SELECT * FROM `#__users` WHERE `username` LIKE 'admin' LIMIT 0 , 30";
				$db->setQuery($sql);
				$user_r2 = $db->loadObjectList();
				$user_r2 = $user_r2[0];

		$message = "Name: ".$name."\n"
					."Organization: ".$org."\n"
					."Product: ".$prod."\n"
					."Feedback type : ".$type."\n"
					."Feedback : ".$msg."\n"
					."Website Name: ".$_SERVER['HTTP_HOST']."\n"
					."Website URL: ".JURI::root()."\n"
					."Admin Email: ".$user_r2->email."\n"
					."Component Name: ".$option."\n"
					."Component Version: ".$ver;

		//echo $message; exit;
		$to = "feedback@itdtraining.com";
		$from = $user_r2->email;
		$fromname = "Feedback From ".JURI::root();

		$header = "";
		$header .= "From: $name <$mail>";

		if (!$regpro_mail->sendMail($from, $fromname, $to, "Feedback", $message, 1)){
			$msg =  JText::_('ADMIN_FEEDBACK_NOT_SENT');
			$link = 'index.php?option=com_registrationpro&view=feedback';
		}else{
			$msg =  JText::_('ADMIN_FEEDBACK_SENT');
			$link = 'index.php?option=com_registrationpro';
		}

		$this->setRedirect( $link, $msg );
	}


	function SampleData(){

		$db =JFactory::getDBO();
		$registrationproHelper = new registrationproHelper;
		$current_date = $registrationproHelper->getCurrent_date('Y-m-d');
		$re_start_date = $current_date;
		$current_date = date('Y-m-d', strtotime($current_date. ' + 3 days'));
		$end_date = date('Y-m-d', strtotime($current_date. ' + 7 days'));
		$re_end_date = date('Y-m-d', strtotime($current_date. ' - 1 days'));

		// Add sample Location
		$db->setQuery("select count(*) from #__registrationpro_locate");
		$location = $db->loadResult();
		if(empty($location)){
			$sql="INSERT INTO `#__registrationpro_locate` (`id`, `club`, `url`, `street`, `plz`, `city`, `country`, `latitude`, `longitude`, `locdescription`, `locimage`, `sendernameloc`, `sendermailloc`, `deliveriploc`, `deliverdateloc`, `publishedloc`, `checked_out`, `checked_out_time`, `ordering`) VALUES (1, 'Woodfield Rd', 'http://www.joomlashowroom.com', '1501 E Woodfield Rd', '60173', 'Schaumburg', 'US', '42.0333607', '-88.0834059', 'Sample location description...', '', '', '', '', '', 1, 0, '0000-00-00 00:00:00', 1)";
			$db->setQuery($sql);
			$result=$db->query();

			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_LOCATION_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_LOCATION_ADDED')."</div>";
			}
		}else{
			//echo "<div><p align='center'><font color='red'>",JText::_('ADMIN_MSG_SAMPLE_LOCATION_ALREADY_ADDED'),"</font></p></div>";
			$msg .= "<div><font color='red'>".JText::_('ADMIN_MSG_SAMPLE_LOCATION_ALREADY_ADDED')."</font></div>";
		}
		// End

		// Add sample categories
		$db->setQuery("select count(*) from #__registrationpro_categories");
		$category = $db->loadResult();
		if(empty($category)){
			$sql="INSERT INTO `#__registrationpro_categories` VALUES (1,'0','0', 'Sample Category', 'Sample category description. Please ignore ...<br />', '', '', 1, 0, '0000-00-00 00:00:00', 1, 1)";
			$db->setQuery($sql);
			$result=$db->query();

			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_CATEGORY_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_CATEGORY_ADDED')."</div>";
			}
		}else{
			//echo "<div><p align='center'><font color='red'>",JText::_('ADMIN_MSG_SAMPLE_CATEGORY_ALREADY_ADDED'),"</font></p></div>";
			$msg .= "<div><font color='red'>".JText::_('ADMIN_MSG_SAMPLE_CATEGORY_ALREADY_ADDED')."</font></div>";
		}
		// End

		// Add sample form
		$db->setQuery("select count(*) from #__registrationpro_forms");
		$form = $db->loadResult();
		if(empty($form)){
			$sql="INSERT INTO `#__registrationpro_forms` VALUES (1, '0', 'sample form', 'sample form', 'Thanks for registration', 1, 0, '0000-00-00 00:00:00')";
			$db->setQuery($sql);
			$result=$db->query();

			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_FORM_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_FORM_ADDED')."</div>";
			}
		}else{
			//echo "<div><p align='center'><font color='red'>",JText::_('ADMIN_MSG_SAMPLE_FORM_ALREADY_ADDED'),"</font></p></div>";
			$msg .= "<div><font color='red'>".JText::_('ADMIN_MSG_SAMPLE_FORM_ALREADY_ADDED')."</font></div>";
		}
		//End

		// sample form field
		$db->setQuery("select count(*) from #__registrationpro_fields");
		$fields = $db->loadResult();
		if(empty($fields)){
			// Add sample form field
			$sql="INSERT INTO `#__registrationpro_fields` VALUES (1, 1, 'firstname', 'First Name','', 'text','', '','default','mandatory','',1,1,1,0,0,'','','',0,'','A')";
			$db->setQuery($sql);
			$result=$db->query();

			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_FIRSTNAME_FIELD_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_FIRSTNAME_FIELD_ADDED')."</div>";
			}

			$sql="INSERT INTO `#__registrationpro_fields` VALUES (2, 1, 'lastname', 'Last Name','', 'text','','','default','mandatory','',2,1,1,0,0,'','','',0,'','A')";
			$db->setQuery($sql);
			$result=$db->query();

			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_LASTNAME_FIELD_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_LASTNAME_FIELD_ADDED')."</div>";
			}

			$sql="INSERT INTO `#__registrationpro_fields` VALUES (3, 1, 'email', 'Email','', 'text','','','default','email','',3,1,1,0,0,'','','',0,'','A')";
			$db->setQuery($sql);
			$result=$db->query();
			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_EMAIL_FIELD_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_EMAIL_FIELD_ADDED')."</div>";
			}
		}else{
			//echo "<div><p align='center'><font color='red'>",JText::_('ADMIN_MSG_SAMPLE_FIELD_ALREADY_ADDED'),"</font></p></div>";
			$msg .= "<div><font color='red'>".JText::_('ADMIN_MSG_SAMPLE_FIELD_ALREADY_ADDED')."</font></div>";
		}
		//End

		// Add sample event
		$db->setQuery("select count(*) from #__registrationpro_dates");
		$event = $db->loadResult();
		if(empty($event)){
			$sql="INSERT INTO `#__registrationpro_dates` (`id`, `locid`, `catsid`, `dates`, `times`, `enddates`, `endtimes`, `titel`, `sendername`, `sendermail`, `deliverip`, `deliverdate`, `shortdescription`, `datdescription`, `datimage`, `checked_out`, `checked_out_time`, `registra`, `unregistra`, `notifydate`, `published`, `access`, `regstart`, `regstarttimes`,`regstop`,`regstoptimes`, `reqactivation`, `status`, `form_id`, `max_attendance`,`terms_conditions`) VALUES (1, 1, 1, '".$current_date."', '11:30:00', '".$end_date."', '10:30:00', 'Sample Event', '', '', '', '', 'Joomla Training in Chicago by joomlashowroom', '<p>The first Joomlearn Joomla training session in Chicago was a success. I would like to thank our attendees for braving the minus 22 degree weather that plagued Chicagoland. Lots of black ice and many wrecked and spun out vehicles were to be found on the Chicago roadways but we all made it to the class safely.</p><p> The participants asked a lot of very good questions and I had great pleasure in showing them the answers to their questions about the areas that they struggled with the most. Although the attendees were new to the Joomla CMS, they did have a decent amount of website development experience.  There was alot of great discussions and I thoroughly enjoyed teaching this first class and I cannot wait until the next Joomla Training class in Chicago.</p>', '', 0, '0000-00-00 00:00:00', 1, 1, '0', 1, 0, '".$re_start_date."','00:00','".$re_end_date."','00:00', 1, 0, 1, 5, '<b>Cancellation Policy</b> <br/> If you need to cancel your registration, <br/> you will receive a refund depending on
	<br/>the time of your cancellation:
	<br/>    *
		  28 days or more before event = 100% refund
	<br/>    *
	<br/>      15-27 days before event = 50% refund
	<br/>    *
	<br/>      1-14 days before event = no refunds
	<br/>
	<br/>We reserve the right to cancel an event
	<br/>(with a full refund) due to low enrollment
	<br/>or other factors.
	<br/>
	<br/>Please note that if paying by check, your
	<br/>registration will not be confirmed until we
	<br/>receive your check.')";

			$db->setQuery($sql);
			$result=$db->query();

			if(!$result){
				echo $db->stderr();
			}else{
				//echo "<div><p align='center'>",JText::_('ADMIN_MSG_SAMPLE_EVENT_ADDED'),"</p></div>";
				$msg .= "<div>".JText::_('ADMIN_MSG_SAMPLE_EVENT_ADDED')."</div>";
			}
		}else{
			//echo "<div><p align='center'><font color='red'>",JText::_('ADMIN_MSG_SAMPLE_EVENT_ALREADY_ADDED'),"</font></p></div>";
			$msg .= "<div><font color='red'>".JText::_('ADMIN_MSG_SAMPLE_EVENT_ALREADY_ADDED')."</font></div>";
		}

		$link = 'index.php?option=com_registrationpro';

		$this->setRedirect( $link, $msg );
	}
}
?>
