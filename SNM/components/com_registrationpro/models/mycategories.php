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

class registrationproModelMycategories extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;

		/*$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'mycategories_limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);*/
		
		// get component config settings
		$registrationproAdmin =new registrationproAdmin;
		
		// set page limit from config setting of component
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_registrationpro_mycategories.limit', 'limit', $regpro_config['eventslimit'], 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));

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
		
		/*$query = "SELECT c.*, u.name AS editor, g.name AS groupname"
			. "\nFROM #__registrationpro_categories AS c"
			. "\n LEFT JOIN #__groups AS g ON g.id = c.access"
			. "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"		
			. $where
			. $orderby;*/
		$query = "SELECT c.*, u.name AS editor, g.title AS groupname"
			. "\nFROM #__registrationpro_categories AS c"
			. "\n LEFT JOIN #__viewlevels AS g ON g.id = c.access"
			. "\n LEFT JOIN #__users AS u ON u.id = c.checked_out"		
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

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.mycategories.filter_order', 'filter_order', 'c.ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.mycategories.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY c.ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
		
		$user	= JFactory::getUser();

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );

		$where = array();
		
		$where[] = "c.user_id = ".$user->id;
		
		if ($search) {
			$where[] = "LOWER(c.catname) LIKE '%$search%'";				
		}
	

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'c.publishedcat = 1';
			} else if ($filter_state == 'U') {
				$where[] = 'c.publishedcat = 0';
			} else {
				$where[] = 'c.publishedcat >= 0';
			}
		} else {
			$where[] = 'c.publishedcat >= 0';
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

			$query = 'UPDATE #__registrationpro_categories'
				. ' SET publishedcat = '. (int) $publish
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
			$this->_db->setQuery("DELETE FROM #__registrationpro_categories WHERE id IN ($cid)");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			// end										
		}
		return true;
	}
	
	
	function move($inc, $cid){	
		//echo $cid; exit;
		$row  =& $this->getTable('registrationpro_categories', '');
		$row->load( $cid );
		$row->move( $inc, "");				
	}		
	
	/**
	 * Method to move a events
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		$row  =& $this->getTable('registrationpro_categories', '');
		$total		= count( $cid );
		//$order 	= josGetArrayInts( 'order' );
		$order		= JRequest::getVar('order', array(), 'default', 'array' );	
		//print_r($order);exit;
		
		$conditions = array();
		
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				} // if
				// remember to updateOrder this group
				$condition = "catsid = " . (int) $row->catsid;
				$found = false;
				foreach ( $conditions as $cond )
					if ($cond[1]==$condition) {
						$found = true;
						break;
					} // if
				if (!$found) $conditions[] = array($row->id, $condition);
			} // if
		} // for			
	
		// execute updateOrder for each group
		foreach ( $conditions as $cond ) {
			$row->load( $cond[0] );
		} // foreach
	
		// clean any existing cache files
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean('com_registrationpro');

		return true;
	}
	
	function saveaccess($uid, $access, $option)
	{		
		$row  =& $this->getTable('registrationpro_categories', '');
		$row->load( $uid );
		$row->access = $access;
	
		if ( !$row->check() ) {
			return $row->getError();
		}
	
		if ( !$row->store() ) {
			return $row->getError();
		}	
		
		// clean any existing cache files
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean('com_registrationpro');

		return true;	
	}		
	
}//Class end
?>