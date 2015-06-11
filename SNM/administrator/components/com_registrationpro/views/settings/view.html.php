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

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder');

class registrationproViewSettings extends JViewLegacy
{
	function display($tpl = null) {
		global $mainframe;

		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JRequest::setVar( 'hidemainmenu', 0 ); //hide menu ???
		$layout = JRequest::getCmd('layout');

		if($layout == 'selectusers'){
			$this->selectusers();
		} else {
			//initialise variables
			$acl 	  = JFactory::getACL();
			$editor   = JFactory::getEditor();
			$document = JFactory::getDocument();
			$user 	  = JFactory::getUser();
			$cid 	  = JRequest::getInt( 'cid' );

			//add css and js to document
			$registrationproHelper = new registrationproHelper;
			$registrationproHelper->add_regpro_scripts();

			// Get data from the model
			$model = $this->getModel();
			$row   = $this->get( 'Data');

			foreach ($row as $each) $$each[1] = $each[2];

			$List = array();

			$arr = array(
			  JHTML::_('select.option', '0', "No"),
			  JHTML::_('select.option', '1', "Yes")
			);

			$List['arr'] = $arr;

			// get currency lists from table
			$currencylist =  $this->get('CurrencyList');
			foreach ($currencylist as $key=>$value)	$curr[] = JHTML::_('select.option', $currencylist[$key]['2'],JText::_($currencylist[$key]['1']), 'id', 'title');
			$currencylist  = JHTML::_('select.genericlist',  $curr, 'currency_value', 'class="inputbox" style="width:130px"  onchange="javascript:getCurr()"', 'id', 'title', $currency_value);

			// create the select list for
			$arr_archiveby = array();
			$arr_archiveby[] = JHTML::_('select.option', "1",JText::_('ADMIN_EVENTS_SETT_DELOLD_BY_STARTDATE'), 'id', 'title');
			$arr_archiveby[] = JHTML::_('select.option', "2",JText::_('ADMIN_EVENTS_SETT_DELOLD_BY_ENDDATE'), 'id', 'title');

			$List['archiveby'] = JHTML::_('select.genericlist',  $arr_archiveby, 'archiveby', 'class="inputbox" style="width:130px" ', 'id', 'title', $archiveby);

			$users = $this->get('PublishUsers');
			foreach($users as $user) $users_arr[] = $user['name'];

			@$List['userslists'] = implode(", ",$users_arr);

			$Categorylists = array();
			$Categorylists = array_merge( $Categorylists, $registrationproHelper->getAllCategory());
			$List['Categorylists'] = JHTML::_('select.genericlist',  $Categorylists, 'user_categories[]', 'class="inputbox" style="width:130px" multiple="true"', 'value', 'text', unserialize($user_categories));

			$Locationslists = array();
			$Locationslists = array_merge( $Locationslists, $registrationproHelper->getAllLocations());
			$List['Locationslists'] = JHTML::_('select.genericlist',  $Locationslists, 'user_locations[]', 'class="inputbox" style="width:130px" multiple="true"', 'value', 'text', unserialize($user_locations));

			$Formslists = array();
			$Formslists = array_merge( $Formslists, $registrationproHelper->getAllForms());
			$List['Formslists'] = JHTML::_('select.genericlist',  $Formslists, 'user_forms[]', 'class="inputbox" style="width:130px" multiple="true"', 'value', 'text', unserialize($user_forms));

			$UserGroupslists = array();
			$UserGroupslists = array_merge( $UserGroupslists, $registrationproHelper->getAllUserGroups());
			$List['UserGroupslists'] = JHTML::_('select.genericlist',  $UserGroupslists, 'user_groups[]', 'class="inputbox" style="width:130px; height:150px;" multiple="true"', 'value', 'text', unserialize($user_groups));

		    // LOCALE SETTINGS
			$timeoffset = array (
			JHTML::_('select.option', -12, JText::_('(UTC -12:00) International Date Line West')),
			JHTML::_('select.option', -11, JText::_('(UTC -11:00) Midway Island, Samoa')),
			JHTML::_('select.option', -10, JText::_('(UTC -10:00) Hawaii')),
			JHTML::_('select.option', -9.5, JText::_('(UTC -09:30) Taiohae, Marquesas Islands')),
			JHTML::_('select.option', -9, JText::_('(UTC -09:00) Alaska')),
			JHTML::_('select.option', -8, JText::_('(UTC -08:00) Pacific Time (US &amp; Canada)')),
			JHTML::_('select.option', -7, JText::_('(UTC -07:00) Mountain Time (US &amp; Canada)')),
			JHTML::_('select.option', -6, JText::_('(UTC -06:00) Central Time (US &amp; Canada), Mexico City')),
			JHTML::_('select.option', -5, JText::_('(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima')),
			JHTML::_('select.option', -4.5, JText::_('(UTC -04:30) Venezuela')),
			JHTML::_('select.option', -4, JText::_('(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz')),
			JHTML::_('select.option', -3.5, JText::_('(UTC -03:30) St. John\'s, Newfoundland, Labrador')),
			JHTML::_('select.option', -3, JText::_('(UTC -03:00) Brazil, Buenos Aires, Georgetown')),
			JHTML::_('select.option', -2, JText::_('(UTC -02:00) Mid-Atlantic')),
			JHTML::_('select.option', -1, JText::_('(UTC -01:00) Azores, Cape Verde Islands')),
			JHTML::_('select.option', 0, JText::_('(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca')),
			JHTML::_('select.option', 1, JText::_('(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris')),
			JHTML::_('select.option', 2, JText::_('(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa')),
			JHTML::_('select.option', 3, JText::_('(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg')),
			JHTML::_('select.option', 3.5, JText::_('(UTC +03:30) Tehran')),
			JHTML::_('select.option', 4, JText::_('(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi')),
			JHTML::_('select.option', 4.5, JText::_('(UTC +04:30) Kabul')),
			JHTML::_('select.option', 5, JText::_('(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent')),
			JHTML::_('select.option', 5.5, JText::_('(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo')),
			JHTML::_('select.option', 5.75, JText::_('(UTC +05:45) Kathmandu')),
			JHTML::_('select.option', 6, JText::_('(UTC +06:00) Almaty, Dhaka')),
			JHTML::_('select.option', 6.5, JText::_('(UTC +06:30) Yagoon')),
			JHTML::_('select.option', 7, JText::_('(UTC +07:00) Bangkok, Hanoi, Jakarta')),
			JHTML::_('select.option', 8, JText::_('(UTC +08:00) Beijing, Perth, Singapore, Hong Kong')),
			JHTML::_('select.option', 8.75, JText::_('(UTC +08:00) Ulaanbaatar, Western Australia')),
			JHTML::_('select.option', 9, JText::_('(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk')),
			JHTML::_('select.option', 9.5, JText::_('(UTC +09:30) Adelaide, Darwin, Yakutsk')),
			JHTML::_('select.option', 10, JText::_('(UTC +10:00) Eastern Australia, Guam, Vladivostok')),
			JHTML::_('select.option', 10.5, JText::_('(UTC +10:30) Lord Howe Island (Australia)')),
			JHTML::_('select.option', 11, JText::_('(UTC +11:00) Magadan, Solomon Islands, New Caledonia')),
			JHTML::_('select.option', 11.5, JText::_('(UTC +11:30) Norfolk Island')),
			JHTML::_('select.option', 12, JText::_('(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka')),
			JHTML::_('select.option', 12.75, JText::_('(UTC +12:45) Chatham Island')),
			JHTML::_('select.option', 13, JText::_('(UTC +13:00) Tonga')),
			JHTML::_('select.option', 14, JText::_('(UTC +14:00) Kiribati')),);
			$List['offset'] = JHTML::_('select.genericlist',  $timeoffset, 'timezone_offset', 'class="inputbox" size="1"', 'value', 'text', $timezone_offset);

			// create the select list for calendar first day
			$calendarweekday = array (
			JHTML::_('select.option', 0, JText::_('ADMIN_EVENTS_SETT_CALENDAR_SUNDAY')),
			JHTML::_('select.option', 1, JText::_('ADMIN_EVENTS_SETT_CALENDAR_MONDAY')),
			JHTML::_('select.option', 2, JText::_('ADMIN_EVENTS_SETT_CALENDAR_TUESDAY')),
			JHTML::_('select.option', 3, JText::_('ADMIN_EVENTS_SETT_CALENDAR_WEDNESDAY')),
			JHTML::_('select.option', 4, JText::_('ADMIN_EVENTS_SETT_CALENDAR_THURSDAY')),
			JHTML::_('select.option', 5, JText::_('ADMIN_EVENTS_SETT_CALENDAR_FRIDAY')),
			JHTML::_('select.option', 6, JText::_('ADMIN_EVENTS_SETT_CALENDAR_SATURDAY')),);
			$List['calendar_weekdays'] = JHTML::_('select.genericlist',  $calendarweekday, 'calendar_weekday', 'class="inputbox" size="1"', 'value', 'text', $calendar_weekday);

			$year_arr = array(2000=>2000, 2001=>2001, 2002=>2002, 2003=>2003, 2004=>2004, 2005=>2005,2006=>2006, 2007=>2007, 2008=>2008, 2009=>2009, 2010=>2010, 2011=>2011, 2012=>2012, 2013=>2013, 2014=>2014,2015=>2015, 2016=>2016, 2017=>2017, 2018=>2018, 2019=>2019,2020=>2020,2021=>2021, 2022=>2022, 2023=>2023, 2024=>2024,2025=>2025, 2026=>2026, 2027=>2027, 2028=>2028, 2029=>2029,2030=>2030,2031=>2031, 2032=>2032, 2033=>2033, 2034=>2034,2035=>2035, 2036=>2036, 2037=>2037, 2038=>2038, 2039=>2039,2040=>2040,2041=>2041, 2042=>2042, 2043=>2043, 2044=>2044,2045=>2045, 2046=>2046, 2047=>2047, 2048=>2048, 2049=>2049,2050=>2050);
			$List['calendar_year_start'] = JHTML::_('select.genericlist',  $year_arr, 'cal_start_year', 'class="inputbox" ', 'value', 'text', $cal_start_year);
			$List['calendar_year_end'] = JHTML::_('select.genericlist',  $year_arr, 'cal_end_year', 'class="inputbox" ', 'value', 'text', $cal_end_year);
			$this->assignRef('currencylist' , $currencylist);
			$this->assignRef('row'      	, $row);
			$this->assignRef('pane'      	, $pane);
			$this->assignRef('editor'      	, $editor);
			$this->assignRef('settings'     , $settings);
			$this->assignRef('list'     	, $List);

			parent::display($tpl);
		}
	}

	function selectusers() {
		global $mainframe, $option;
		$option = JRequest::getCMD('option'); // use this instead of global $option
		$db = JFactory::getDBO();

		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');

		//get vars
		$filter_order	  = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter_order', 'filter_order', '', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 	  = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter_state', 'filter_state', '*', 'word' );
		$filter 		  = $mainframe->getUserStateFromRequest( $option.'.selectusers.filter', 'filter', '', 'int' );
		$search 		  = $mainframe->getUserStateFromRequest( $option.'.selectuserssearch', 'search', '', 'string' );
		$search 		  = $db->escape( trim(JString::strtolower( $search ) ) );

		//publish unpublished filter
		$lists['state']	= JHTML::_('grid.state', $filter_state );

		$filters = array();
		$filters[] = JHTML::_('select.option', '0', JText::_( '- Select -' ) );
		$filters[] = JHTML::_('select.option', '1', JText::_( 'ADMIN_EVENTS_TITEL_LI_EV' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'ADMIN_EVENTS_CLUB_LI_EV' ) );
		$filters[] = JHTML::_('select.option', '3', JText::_( 'ADMIN_EVENTS_CITY_LI_LO' ) );
		$filters[] = JHTML::_('select.option', '4', JText::_( 'ADMIN_EVENTS_CAT_LI_EV' ) );

		$lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );
		$lists['search'] = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;
		$model = $this->getModel('settings');
		$rows = $model->getUsersList();
		$pageNav = $model->getUsersPagination();
		$publish_users = $model->getPublishUsersIds();
		$moderator_users = $model->getModeratorUsersIds();

		foreach($rows as $key => $value) {
			if(@in_array($value->id, $moderator_users)){
				$value->moderator = 1;
			}else $value->moderator = 0;
		}

		$this->assignRef('rows', $rows);
		$this->assignRef('lists', $lists);
		$this->assignRef('pageNav', $pageNav);

		parent::display($tpl);
	}
}
?>