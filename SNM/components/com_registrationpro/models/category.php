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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class registrationproModelCategory extends JModelLegacy
{

	var $id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();
			
		global $mainframe, $option;
		
		$mainframe = JFactory::getApplication();
		
		$this->id = JRequest::getVar('id',0,'','int');
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		
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
		
		$query = "SELECT a.id, a.dates, a.enddates, a.shortdescription, a.max_attendance, a.times, a.endtimes, a.titel, a.locid, a.status,a.shw_attendees, a.registra, l.club, l.url, l.street, l.plz, l.city, l.country, l.locdescription, c.catname, c.id AS catid"
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
		$search 		= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
	
		$my 		= JFactory::getUser();
		//$gid 		= (int) $my->get('aid', 0);
		
		$where 		= array();			
		$where[] 	= 'a.published = 1';	
		//$where[] 	= 'c.access <= '.$gid;
		// Filter by access level.
		$user		= JFactory::getUser();
		$groups		= implode(',', $user->getAuthorisedViewLevels());
		$where[] 	= 'c.access IN ('.$groups.')';
		$where[] 	= 'a.viewaccess IN ('.$groups.')';
		
		$where[] 	= 'a.catsid = '.$this->id;
		$where[] 	= 'c.id = '.$this->id.' OR c.parentid = '.$this->id;		
		$where[] 	= 'a.published = 1';	
		
		/*$event_title 		= trim(JRequest::getVar('txtEventName','','POST'));
		$event_start_date 	= JRequest::getVar('txtEventStartDate','','POST');
		$event_end_date 	= JRequest::getVar('txtEventEndDate','','POST');
		$event_location 	= $this->_db->getEscaped(trim(JRequest::getVar('txtEventLocation','','POST')));
		$event_category		= JRequest::getVar('selCategory','0','POST');*/
		
		$event_title 		= $mainframe->getUserStateFromRequest( $option.'.txtEventName', 'txtEventName', '', 'string' );
		$event_title 		= $this->_db->getEscaped( trim(JString::strtolower( $event_title ) ) );
		
		$event_start_date 	= $mainframe->getUserStateFromRequest( $option.'.txtEventStartDate', 'txtEventStartDate', '', 'string' );
		$event_start_date 	= $this->_db->getEscaped( trim(JString::strtolower( $event_start_date ) ) );
		
		$event_end_date 	= $mainframe->getUserStateFromRequest( $option.'.txtEventEndDate', 'txtEventEndDate', '', 'string' );
		$event_end_date 	= $this->_db->getEscaped( trim(JString::strtolower( $event_end_date ) ) );
		
		$event_location 	= $mainframe->getUserStateFromRequest( $option.'.txtEventLocation', 'txtEventLocation', '', 'string' );
		$event_location 	= $this->_db->getEscaped( trim(JString::strtolower( $event_location ) ) );
		
		$event_category		= $mainframe->getUserStateFromRequest( $option.'.selCategory', 'selCategory', '', 'int' );	
		$event_category 	= $this->_db->getEscaped( trim(JString::strtolower( $event_category ) ) );
		
		if ($event_title) {
			$where[] = ' LOWER(a.titel) LIKE '.$this->_db->quote( $this->_db->getEscaped('%'.$event_title.'%',false));
			//$where[] = ' LOWER(a.titel) LIKE \'%'.$event_title.'%\' ';
		}
		
		if ($event_start_date) {
			$where[] = ' LOWER(a.dates) LIKE '.$this->_db->quote( $this->_db->getEscaped('%'.$event_start_date.'%',false));
			//$where[] = ' LOWER(a.dates) LIKE \'%'.$event_start_date.'%\' ';
		}
		
		if ($event_end_date) {
			$where[] = ' LOWER(a.enddates) LIKE '.$this->_db->quote( $this->_db->getEscaped('%'.$event_end_date.'%',false));
			//$where[] = ' LOWER(a.enddates) LIKE \'%'.$event_end_date.'%\' ';
		}
		
		if ($event_location) {
			$where[] = ' LOWER(l.club) LIKE '.$this->_db->quote( $this->_db->getEscaped('%'.$event_location.'%',false));
			//$where[] = ' LOWER(l.club) LIKE \'%'.$event_location.'%\' ';
		}
		
		if (!empty($event_category)){
			$where[] = ' a.catsid = '.$event_category;
		}				
	
		$where 		 = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	
		//echo $where;
	
		return $where;
	}
	
	function _additionals($rows)
	{					
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
			$registrationproHelper = new registrationproHelper;
			if($registrationproHelper->check_is_event_registration_over ($rows[$i]->id)) {
				$rows[$i]->showattendance = 0;
				$rows[$i]->showprice = 0;
			}			
			// end
			
		}
		//echo "<pre>"; print_r($rows); exit;
				
		return $rows;
	}		
}
?>