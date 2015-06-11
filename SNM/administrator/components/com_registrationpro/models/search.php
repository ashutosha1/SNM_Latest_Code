<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL		
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
 *
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationproModelSearch extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		
		$mainframe = JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( $option.'search.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'search.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	 
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $this->_additionals($this->_data);
			$this->_data = $this->_getTickets($this->_data);
		}

		return $this->_data;
	}

	/*
	 * Function to get the tickets bought by the user
	 */
	function _getTickets($row){
		
		foreach($row as $key=>$val){
			$this->_db->setQuery("SELECT item_name FROM #__registrationpro_transactions WHERE reg_id=".$val->rid);
			$val->tickets = $this->_db->loadObjectList();
			//echo "<pre>";print_r($val->tickets);echo "</pre>";
			$row[$key]->tickets = $val->tickets;
			//unset($val->tickets);
		}
		//echo "<pre>";print_r($row);echo "</pre>";die;
		return $row;
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
		
		/*$query = "SELECT a.id, a.titel, a.dates, a.enddates, a.times, a.endtimes, l.club, l.city, c.catname,r.firstname, r.lastname, r.email "
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\nLEFT JOIN #__registrationpro_register AS r ON r.rdid = a.id"
				. $where
				. $orderby;*/
		
		$query = "SELECT a.id, a.titel, a.dates, a.enddates, a.times, a.endtimes, l.id as loc_id, l.club, l.city, c.id as cat_id, c.catname, r.rid, r.firstname, r.lastname, r.email "
				. "\nFROM #__registrationpro_dates AS a"
				. "\n JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\n JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\n JOIN #__registrationpro_register AS r ON r.rdid = a.id"
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

		//$filter_order		= $mainframe->getUserStateFromRequest( $option.'.events.filter_order', 'filter_order', 'c.ordering, a.ordering', 'cmd' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.search_filter_order', 'filter_order', '', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.search_filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY a.dates';
		}else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
				
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search	 			= strtolower( $search );
		//$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );	
		$event	 			= $mainframe->getUserStateFromRequest( $option.'.search_event', 'event', '', 'int' );	
		$start_date 		= $mainframe->getUserStateFromRequest( $option.'.search_start_date', 'start_date', '', 'string' );
		$end_date 			= $mainframe->getUserStateFromRequest( $option.'.search_end_date', 'end_date', '', 'string' );
		$firstname 			= $mainframe->getUserStateFromRequest( $option.'.search_firstname', 'firstname', '', 'string' );
		//$firstname 			= $this->_db->escape( trim(JString::strtolower( $firstname ) ) );
		$firstname	 			= strtolower( $firstname );
		$lastname 			= $mainframe->getUserStateFromRequest( $option.'.search_lastname', 'lastname', '', 'string' );
		$lastname	 			= strtolower( $lastname );
		//$lastname 			= $this->_db->escape( trim(JString::strtolower( $lastname ) ) );
		$email	 			= $mainframe->getUserStateFromRequest( $option.'.search_email', 'email', '', 'string' );
		//$email 				= $this->_db->escape( trim(JString::strtolower( $email ) ) );
		$email	 			= strtolower( $email );
		
		$reset				= JRequest::getVar('reset','POST');
		if($reset){	
			$location	 	= $mainframe->getUserStateFromRequest( $option.'.search_location', 'l', '', 'array' );
			$category	 	= $mainframe->getUserStateFromRequest( $option.'.search_category','c', '', 'array' );
		}else{	
			$location	 	= $mainframe->getUserStateFromRequest( $option.'.search_location', 'location', '', 'array' );
			$category	 	= $mainframe->getUserStateFromRequest( $option.'.search_category', 'category', '', 'array' );
		}
			
		$where = array();
						
		if ($search) {
			$where[] = ' LOWER(a.titel) LIKE \'%'.$search.'%\' ';
		}
		
		if ($event) {
			$where[] = ' a.id = '.$event;
		}
						
		if ($start_date && $end_date){
			$where[] = " (a.dates >= '".$start_date."' AND a.dates <= '".$end_date."')" ;
		}elseif($start_date){
			$where[] = " a.dates >= '".$start_date."'";
		}elseif($end_date) {
			$where[] = " a.dates <= '".$end_date."'";
		}					
		
		if ($firstname) {
			$where[] = ' LOWER(r.firstname) LIKE \'%'.$firstname.'%\' ';
		}
		
		if ($lastname) {
			$where[] = ' LOWER(r.lastname) LIKE \'%'.$lastname.'%\' ';
		}
		
		if ($email) {
			$where[] = ' LOWER(r.email) LIKE \'%'.$email.'%\' ';
		}

		if (!count($location) == 0 && !$location[0] == 0) {
			$where[] = ' l.id in ('.implode(",",$location).')';
		}
		
		if (!count($category) == 0 && !$category[0] == 0) {
			$where[] = ' c.id in ('.implode(",",$category).')';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _additionals($rows)
	{		
		return $rows;
	}	
	
	function getLocations(){			
		$query = "SELECT id AS value, TRIM(club) AS text"
			. "\nFROM #__registrationpro_locate"
			//. "\nWHERE publishedloc = 1"
			. "\nORDER BY club";
		$this->_db->setQuery( $query );				
		return $this->_db->loadObjectList();
	}
	
	function getCategories(){			
		$query = "SELECT id AS value, catname AS text"
			. "\nFROM #__registrationpro_categories"
			//. "\nWHERE publishedcat = 1"
			. "\nORDER BY catname";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();				
	}
	
	function getEvents(){			
		$query = "SELECT id AS value, titel AS text"
			. "\nFROM #__registrationpro_dates"
			//. "\nWHERE published = 1"
			. "\nORDER BY titel";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();				
	}
}

?>