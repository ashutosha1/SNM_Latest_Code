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

class registrationproModelDay extends JModelLegacy
{

	var $_data = null;
	var $_total = null;
	var $_date = null;
	var $_pagination = null;

	function __construct() {
		parent::__construct();
		global $mainframe, $option;

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();

		// Get the pagination request variables
		$limit		= JRequest::getVar('limit', 0, '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// set page limit from config setting of component
		$this->setState('limit', $regpro_config['eventslimit']);

		// set if limit selected by limit select box by user
		if($limit) $this->setState('limit', $limit);

		$this->setState('limitstart', $limitstart);
		$rawday = JRequest::getInt('id', 0, 'request');

		if(empty($rawday)) $rawday = JRequest::getInt('days', 0, 'request');
		$this->setDate($rawday);
	}

	function getEvents() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)){
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $this->_additionals($this->_data);
		}
		return $this->_data;
	}

	function getTotal() {
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	function getPagination() {
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
		$where	 = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = "SELECT a.id, a.image AS poster, a.parent_id, a.user_id, a.dates, a.enddates, a.shortdescription, a.max_attendance, a.times, a.endtimes, a.titel, a.locid, a.status,a.shw_attendees, a.registra, l.club, l.url, l.street, l.plz, l.city, l.country, l.locdescription, c.catname, c.id AS catid"
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

		if($regpro_config['eventlistordering'] == 2){
			$Default_orderby	= ' c.ordering, a.dates, a.times ';
		}elseif($regpro_config['eventlistordering'] == 3){
			$Default_orderby	= ' c.ordering, a.enddates, a.endtimes ';
		}elseif($regpro_config['eventlistordering'] == 4){
			$Default_orderby	= ' c.ordering, a.titel ';
		}else{
			$Default_orderby	= ' c.ordering, a.ordering ';
		}

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'events.list.filter_order', 'filter_order', $Default_orderby, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'events.list.filter_order_Dir', 'filter_order_Dir', '', 'cmd' );

		if ($filter_order == ''){
			$orderby	= ' ORDER BY '.$Default_orderby.' '.$filter_order_Dir;
		}else{
			if(!strpos($filter_order, "c.ordering")){
				$filter_order = "c.ordering, ".$filter_order;
			}

			$orderby	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}

		//echo $orderby;

		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 	= $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter 		= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 		= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 		= $this->_db->escape( trim(JString::strtolower( $search ) ) );

		$my 		= JFactory::getUser();
		$gid 		= (int) $my->get('aid', 0);
		$nulldate 	= '0000-00-00';

		$where 		= array();
		$where[] 	= 'a.published = 1';

		// Third is to only select events of the specified day
		$where[]  	= "a.dates = '".$this->_date."'";

		// Filter by access level.
		$user	 = JFactory::getUser();
		$groups	 = implode(',', $user->getAuthorisedViewLevels());
		$where[] = 'c.access IN ('.$groups.')';
		$where[] = 'a.viewaccess IN ('.$groups.')';
		$where 	 = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _additionals($rows)
	{
		$registrationproHelper = new registrationproHelper;
		foreach($rows as $i=>$row){
			// Get Events tickets prices
			$query = "SELECT product_name, total_price, product_description, type FROM #__registrationpro_payment WHERE type = 'E' AND regpro_dates_id = '$row->id'";
			$this->_db->setQuery($query);
			$rows[$i]->price = $this->_db->loadObjectList();
			// end

			// Get filled seats for Events
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
					$rows[$i]->registered 	= $registerdusers;
					$rows[$i]->avaliable 	= $rows[$i]->max_attendance - $registerdusers;

				}else{
					$rows[$i]->registered = 0;
					$rows[$i]->avaliable = $rows[$i]->max_attendance;
				}
			// end

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

			// check event seats are full or not
			if($registrationproHelper->check_is_event_registration_over ($rows[$i]->id)) {
				$rows[$i]->showattendance = 0;
				$rows[$i]->showprice = 0;
			}
		}
		return $rows;
	}

	/**
	 * Method to set the date
	 * @access	public
	 * @param	string
	 */
	function setDate($date) {
		global $mainframe;

		// Get the paramaters of the active menu item
		$params =  $mainframe->getParams('com_registrationpro');

		//0 means we have a direct request from a menuitem and without any parameters (eg: calendar module)
		if ($date == 0) {

			$now = JFactory::getDate();
		   	$today['mday'] = $now->Format('d') * 1;
		   	$today["year"] = $now->Format('Y') * 1;
	   		$today["mon"]  = $now->Format('m') * 1;
			$dayoffset = $params->get('days');
			$timestamp = mktime(0, 0, 0, $today['mon'], $today["mday"] + $dayoffset, $today["year"]);
			$date = strftime('%Y-%m-%d', $timestamp);

		//a valid date has 8 characters
		} elseif (strlen($date) == 8) {
			$year 	= substr($date, 0, -4);
			$month	= substr($date, 4, -2);
			$tag	= substr($date, 6);

			//check if date is valid
			if (checkdate($month, $tag, $year)) {
				$date = $year.'-'.$month.'-'.$tag;
			} else {
				$date = date('Ymd');
				JError::raiseNotice( 'SOME_ERROR_CODE', JText::_('INVALID_DATE_REQUESTED_USING_CURRENT') );
			}
		} else {
			$registrationproAdmin = new registrationproAdmin;
			$regproConfig	= $registrationproAdmin->config();
			$tzoffset 	= $regproConfig['timezone_offset'];
			$time 		= time()  + ($tzoffset*60*60);
			$today['mday'] = date('d', $time) * 1;
		   	$today["year"] = date('Y', $time) * 1;
	   		$today["mon"]  = date('m', $time) * 1;
			$dayoffset	= $date;
			$timestamp	= mktime(0, 0, 0, $today['mon'], $today["mday"] + $dayoffset, $today["year"]);
			$date		= strftime('%Y-%m-%d', $timestamp);
		}
		$this->_date = $date;
	}
}
?>