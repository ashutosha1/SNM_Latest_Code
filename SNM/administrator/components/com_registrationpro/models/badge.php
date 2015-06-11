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
jimport('joomla.application.component.model');

class registrationproModelBadge extends JModelLegacy
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

		$query = "SELECT a.id as eventid, a.titel, a.dates, a.enddates, a.times, a.endtimes, l.id as loc_id, l.club, l.city, c.id as cat_id, c.catname, r.rid as id, r.firstname, r.lastname, r.email "
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
		$event			= JRequest::getVar('event','POST');
		$where = array();
		$where[] = ' a.id = "'.$event.'"';
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
			. "\nORDER BY club";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCategories(){
		$query = "SELECT id AS value, catname AS text"
			. "\nFROM #__registrationpro_categories"
			. "\nORDER BY catname";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getEvents(){
		$query = "SELECT id AS value, CONCAT(dates,' - ',titel) AS text"
			. "\nFROM #__registrationpro_dates"
			. "\nWHERE published >= 0"
			. "\nORDER BY CONCAT(dates,' - ',titel)";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getFormFields($eventid){
		/* Added one more column of fields i.e name */
		$query = "SELECT f.title,f.name "
			. "\nFROM #__registrationpro_dates AS e"
			. "\nLEFT JOIN #__registrationpro_fields AS f ON f.form_id = e.form_id"
			. "\nWHERE e.id=".$eventid
			. "\nORDER BY f.title";
		$this->_db->setQuery( $query );
		return $this->_db->loadRowList();
	}
}

?>