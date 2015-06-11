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

class registrationproModelArchives extends JModelLegacy
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
		$limitstart = $mainframe->getUserStateFromRequest( $option.'archives.limitstart', 'limitstart', 0, 'int' );

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
		
		$query = "SELECT a.*, l.club, l.city, c.catname"
		. "\nFROM #__registrationpro_dates AS a"
		. "\n LEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
		. "\n LEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
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

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.archiveevents.filter_order', 'filter_order', 'a.dates, a.times', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.archiveevents.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY c.ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , a.ordering ';
		}
		
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.archiveevents.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.archiveevents.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		//$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );
		$search				= strtolower( $search );
		$where = array();
		
		$where[] = "a.published = -1";
		
		//echo "<pre>"; print_r($filter); exit;
		
		if ($search && $filter == 1) {
			$where[] = ' LOWER(a.titel) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 2) {
			$where[] = ' LOWER(l.club) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 3) {
			$where[] = ' LOWER(l.city) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 4) {
			$where[] = ' LOWER(c.catname) LIKE \'%'.$search.'%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _additionals($rows)
	{		
		return $rows;
	}		
				
}//Class end
?>