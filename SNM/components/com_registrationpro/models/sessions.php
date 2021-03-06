<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright � 2009 - All rights reserved.
 * @license  	GNU/GPL		
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
 *
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationproModelSessions extends JModelLegacy
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
		$limitstart = $mainframe->getUserStateFromRequest( $option.'sessions_limitstart', 'limitstart', 0, 'int' );

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
		}
		//echo $query;

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

		$query = "SELECT * FROM #__registrationpro_sessions AS a"
				. $where
				. $orderby;
		//exit;
		return $query;
	}

	/**
	 * Build the order clause
	 * @access private
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.sessions.filter_order', 'filter_order', 'session_date, ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.sessions.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;		
		//$orderby = '';
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		
		//echo"<pre>"; print_r($_POST); exit;		
		$event_id	= JRequest::getVar('event_id',0,'','int');		

		$where = array();

		if ($event_id) {			
			$where[] = 'event_id = '.$event_id;
		}
				
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	function delete($cid = array())
	{
		$result = false;

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__registrationpro_sessions '
					. ' WHERE id IN ('. $cids .')'
					;

			$this->_db->setQuery( $query );

			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	
	function move($inc, $cid, $where_condition){	
		//echo $where_condition; exit;
		$row  =& $this->getTable('registrationpro_sessions', '');
		$row->load( $cid );
		$row->move( $inc, $where_condition);			
	}				
				
}//Class end
?>