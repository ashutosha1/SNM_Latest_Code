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

jimport('joomla.application.component.model');

class registrationproModelAttendees extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_eventid = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
				
		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	 
	function getData($event_id)
	{
	
		$this->_eventid = $event_id;
		
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			//$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $this->_getList($query);
			$this->_data = $this->_additionals($this->_data);
		}

		//echo "<pre>"; print_r($this->_data); exit;

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
				
		 $query 		= "SELECT r.*, a.id, t.*, SUM(t.price) as amount , SUM(t.discount_amount) as tot_discount, edt.event_discount_amount, edt.event_discount_type"
						. "\nFROM #__registrationpro_register AS r"
						. "\nLEFT JOIN #__registrationpro_dates AS a ON r.rdid = a.id"						
						. "\nLEFT JOIN #__registrationpro_transactions AS t ON r.rid = t.reg_id"						
						. "\nLEFT JOIN #__registrationpro_payment AS p ON t.p_id = p.id"
						. "\nLEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"					
						. $where
						. $orderby;
						
		//echo $query; 				
		return $query;
	}

	function _buildContentOrderBy()
	{
		global $mainframe, $option;

	$orderby 	= ' ORDER BY  r.uregdate DESC';
			
	return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		$where = array();
		$where[]    = 'r.status = 1';
		$where[] 	= "r.rdid = ".$this->_eventid;
		$where[] 	= "r.rid = t.reg_id";
		$where[] 	= "r.active=1 group by t.reg_id";				
		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		return $where;
	}

	function _additionals($rows)
	{		
		return $rows;
	}

	
								
}//Class end
?>