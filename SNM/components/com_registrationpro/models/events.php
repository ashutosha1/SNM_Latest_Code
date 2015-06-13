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

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

class registrationproModelEvents extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
		global $mainframe, $option;

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();

		// set page limit from config setting of component
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_registrationpro_events.limit', 'limit', $regpro_config['eventslimit'], 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));
	}

	function getEvents()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $this->_additionals($this->_data);
		}

		return $this->_data;
	}

	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = "SELECT a.id, a.parent_id, a.user_id, a.image, a.dates, a.enddates, a.shortdescription, a.max_attendance, a.times, a.endtimes, a.titel, a.locid, a.status,a.shw_attendees, a.registra, l.club, l.url, l.street, l.plz, l.city, l.country, l.locdescription, c.catname, c.id AS catid"
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. $where
				. $orderby;

		return $query;
	}

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$layout = JRequest::getVar('layout');
		if($layout=='listing'){
			if($regpro_config['eventlistordering'] == 2){
				$Default_orderby	= ' a.dates, a.times ';
			}elseif($regpro_config['eventlistordering'] == 3){
				$Default_orderby	= ' a.enddates, a.endtimes ';
			}elseif($regpro_config['eventlistordering'] == 4){
				$Default_orderby	= ' a.titel ';
			}else{
				$Default_orderby	= ' a.ordering ';
			}
		}else{
			if($regpro_config['eventlistordering'] == 2){
				$Default_orderby	= ' c.ordering, a.dates, a.times ';
			}elseif($regpro_config['eventlistordering'] == 3){
				$Default_orderby	= ' c.ordering, a.enddates, a.endtimes ';
			}elseif($regpro_config['eventlistordering'] == 4){
				$Default_orderby	= ' c.ordering, a.titel ';
			}else{
				$Default_orderby	= ' c.ordering, a.ordering ';
			}
		}
		//echo $Default_orderby;

		//echo $filter_order		= $mainframe->getUserStateFromRequest( $option.'events.list.filter_order', 'filter_order','', 'cmd' );

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'events.list.filter_order', 'filter_order', $Default_orderby, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'events.list.filter_order_Dir', 'filter_order_Dir', '', 'cmd' );

		if ($filter_order == ''){
			$orderby	= ' ORDER BY '.$Default_orderby.' '.$filter_order_Dir;
		}else{
			$orderby	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}

		//echo $orderby; exit;

		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state = $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter  = $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search  = $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search  = $this->_db->escape( trim(JString::strtolower( $search ) ) );
		$my 	 = JFactory::getUser();
		$gid 	 = (int) $my->get('aid', 0);
		$where 	 = array();
		$where[] = 'a.published = 1';
		$where[] = 'a.moderating_status = 1';
		$where[] = 'c.publishedcat = 1';

		// Filter by access level.
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$where[] = 'c.access IN ('.$groups.')';
		$where[] = 'a.viewaccess IN ('.$groups.')';
		$event_title 	  = $mainframe->getUserStateFromRequest( $option.'.txtEventName', 'txtEventName', '', 'string' );
		$event_start_date = $mainframe->getUserStateFromRequest( $option.'.txtEventStartDate', 'txtEventStartDate', '', 'string' );
		$event_start_date = $this->_db->escape( trim(JString::strtolower( $event_start_date ) ) );
		$event_end_date   = $mainframe->getUserStateFromRequest( $option.'.txtEventEndDate', 'txtEventEndDate', '', 'string' );
		$event_end_date   = $this->_db->escape( trim(JString::strtolower( $event_end_date ) ) );
		$event_location   = $mainframe->getUserStateFromRequest( $option.'.txtEventLocation', 'txtEventLocation', '', 'string' );
		$event_category	  = $mainframe->getUserStateFromRequest( $option.'.selCategory', 'selCategory', '', 'int' );
		$event_category   = $this->_db->escape( trim(JString::strtolower( $event_category ) ) );

		if ($event_title) $where[] = ' LOWER(a.titel) LIKE '.$this->_db->quote( $this->_db->escape('%'.$event_title.'%',false));
		if ($event_start_date) $where[] = ' LOWER(a.dates) LIKE '.$this->_db->quote( $this->_db->escape('%'.$event_start_date.'%',false));
		if ($event_end_date) $where[] = ' LOWER(a.enddates) LIKE '.$this->_db->quote( $this->_db->escape('%'.$event_end_date.'%',false));
		if ($event_location) $where[] = ' LOWER(l.club) LIKE '.$this->_db->quote( $this->_db->escape('%'.$event_location.'%',false));
		if (!empty($event_category)) $where[] = ' a.catsid = '.$event_category;

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		return $where;
	}

	function _additionals($rows)
	{

		foreach($rows as $i=>$row){
			// Get Events tickets prices
			$query = "SELECT product_name, total_price, product_description, type, ticket_start, ticket_end FROM #__registrationpro_payment WHERE type = 'E' AND regpro_dates_id = '$row->id'";
			$this->_db->setQuery($query);
			$rows[$i]->price = $this->_db->loadObjectList();
			
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
			
			// Get filled seats for Events
			if($regpro_config['include_pending_reg'] == 0)
			{
				$query = "SELECT count(*) as cnt FROM #__registrationpro_register WHERE status=1 AND rdid = '$row->id'";
			}
			else
			{
				$query = "SELECT count(*) as cnt FROM #__registrationpro_register WHERE active=1 AND rdid = '$row->id'";
			}
			$this->_db->setQuery($query);
			$registerdusers = $this->_db->loadResult();
			if($registerdusers){
				$rows[$i]->registered = $registerdusers;
				$rows[$i]->avaliable = $rows[$i]->max_attendance - $registerdusers;
			}else{
				$rows[$i]->registered = 0;
				$rows[$i]->avaliable = $rows[$i]->max_attendance;
			}

			// flag to show the price and max attendance column
			$rows[$i]->showprice = 1;
			if($rows[$i]->max_attendance == 0){
				$rows[$i]->showprice = 1;
			}elseif($rows[$i]->max_attendance > 0 && $rows[$i]->registered >= $rows[$i]->max_attendance){
				$rows[$i]->showprice = 0;
			}

			$rows[$i]->showattendance = 1;
			if($rows[$i]->registra == 0){
				$rows[$i]->showattendance = 0;
				$rows[$i]->showprice = 0;
			}
			$registrationproHelper =new registrationproHelper;
			// check event seats are full or not
			if($registrationproHelper->check_is_event_registration_over ($rows[$i]->id)) {
				$rows[$i]->showattendance = 0;
				$rows[$i]->showprice = 0;
			}
			// end

		}


		return $rows;
	}

	// get events listing for Rss Feed
	function getEventForRss()
	{
		//$query = "SELECT * FROM #__registrationpro_dates WHERE published = 1 ORDER BY ordering";

		$query = "SELECT a.id, a.dates, a.enddates, a.shortdescription, a.datdescription, a.max_attendance, a.times, a.endtimes, a.titel, a.locid, a.status, l.club, l.url, l.street, l.plz, l.city, l.country, l.locdescription, c.catname, c.id AS catid"
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\n WHERE published = 1 ORDER BY a.dates,a.times LIMIT ".REGPRO_RSS_ITEM_COUNT;

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();

		return $rows;
	}

	// Get events discounts
	function getEventDiscounts($eventids = array())
	{
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id in  (".implode(",",$eventids).") ORDER BY early_discount_date, min_tickets";
		$this->_db->setQuery($query);
		$event_discounts = $this->_db->loadObjectList();

		return $event_discounts;
	}
}
?>