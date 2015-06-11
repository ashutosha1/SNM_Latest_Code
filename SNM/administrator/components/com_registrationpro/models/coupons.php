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

class registrationproModelCoupons extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		
		$mainframe = JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'coupons_limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}
	 
	function getData()
	{
		// Update coupons status
		$this->update_coupon_status();
		
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
		
		$query = "SELECT a.*, CASE a.discount_type WHEN 'P' THEN '%' WHEN 'A' THEN '$' END AS dis_type,"
			. "\n CASE a.status WHEN 'A' THEN 'Active' WHEN 'O' THEN 'Over' END AS coupon_status"
			. "\nFROM #__registrationpro_coupons AS a "
			. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
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

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY a.ordering '.$filter_order_Dir;
		} else {
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
		//$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );
		$search				= strtolower( $search );
		
		$where = array();
		
		if ($search && $filter == 1) {
			$where[] = "LOWER(a.title) LIKE '%$search%'";		
		}

		if ($search && $filter == 2) {
			$where[] = "a.start_date LIKE '%$search%'";			
		}
	
		if ($search && $filter == 3) {
			$where[] = "a.end_date LIKE '%$search%'";		
		}
	
		if ($search && $filter == 4) {
			$where[] = "a.code LIKE '%$search%'";			
		}								

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'a.published = 1';
			} else if ($filter_state == 'U') {
				$where[] = 'a.published = 0';
			} else {
				$where[] = 'a.published >= 0';
			}
		} else {
			$where[] = 'a.published >= 0';
		}		

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _additionals($rows)
	{		
		return $rows;
	}

	function publish($cid = array(), $publish = 1)
	{		

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__registrationpro_coupons'
				. ' SET published = '. (int) $publish
				. ' WHERE id IN ('. $cids .')'
				;
			$this->_db->setQuery( $query );

			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
	}

	function delete($cid = array())
	{
		$result = false;
		
		$total 	= count( $cid );
		$cid = implode( ',', $cid );
						
		if (count( $cid ))
		{
			// delete events
			$this->_db->setQuery("DELETE FROM #__registrationpro_coupons WHERE id IN ($cid)");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			// end										
		}
		return $total;
	}
	
	function update_coupon_status()
	{
		// change stauts to OVER if current date is greater then end date
		$query = "UPDATE #__registrationpro_coupons SET status ='O' WHERE CURRENT_DATE() > end_date";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		if (!$this->_db->query()) {
			echo "<script> alert('".$$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		// change status to Active if current date is lesser then end date
		$query = "UPDATE #__registrationpro_coupons SET status ='1' WHERE CURRENT_DATE() < end_date";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		if (!$this->_db->query()) {
			echo "<script> alert('".$$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
		
}//Class end
?>