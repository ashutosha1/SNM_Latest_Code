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

class registrationproModelMyevents extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;		

		/*$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'events_limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);*/
		
		// get component config settings
		$registrationproAdmin =new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		
		// set page limit from config setting of component
		$this->setState('limit', $mainframe->getUserStateFromRequest('com_registrationpro_events.limit', 'limit', $regpro_config['eventslimit'], 'int'));
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
		
		/*$query = "SELECT a.*, l.club, l.city, c.catname, u.name AS editor, g.name as groupname"
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\nLEFT JOIN #__groups AS g ON a.access = g.id"
				. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out"
				. $where
				. $orderby;*/
		$query = "SELECT a.*, l.club, l.city, c.catname, u.name AS editor "
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				//. "\nLEFT JOIN #__groups AS g ON a.access = g.id"
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

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.events.filter_order', 'filter_order', 'c.ordering, a.ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.events.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY c.ordering, a.ordering';
		}
		if ($filter_order == 'c.ordering') {
			$orderby 	= ' ORDER BY c.ordering, a.ordering '.$filter_order_Dir;
		}elseif ($filter_order == 'a.ordering') {
			$orderby 	= ' ORDER BY c.ordering, a.ordering '.$filter_order_Dir;
		}else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
		
		//$orderby;
		
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
		
		$where[] = 'a.user_id = '.$user->id;

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

	function publish($cid = array(), $publish = 1)
	{
		$user 	= JFactory::getUser();
		$userid = (int) $user->get('id');

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__registrationpro_dates'
				. ' SET published = '. (int) $publish
				. ' WHERE id IN ('. $cids .')';
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
		$events = implode( ',', $cid );
						
		if (count( $cid ))
		{
			// delete events
			$this->_db->setQuery("DELETE FROM #__registrationpro_dates WHERE id IN ($events)");
			$this->_db->query();
			
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			// end
					
			// get user id from register table to clean the records from transaction table
			$this->_db->setQuery("SELECT rid FROM #__registrationpro_register WHERE rdid IN ($events)");
			$uids = $this->_db->loadResultArray();
			$uids = implode(",",$uids);
			// end
												
			// delete regsitered user with related event id			
			$this->_db->setQuery("DELETE FROM #__registrationpro_register WHERE rdid IN ($events)");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			// end
									
			// delete transaction records with related event id
			if($uids){
			
				// get transaction ids from transaction table to clean the records from event discount transaction table
				$this->_db->setQuery("SELECT id FROM #__registrationpro_transactions WHERE reg_id IN ($uids)");
				$trans_ids = $this->_db->loadResultArray();
				$trans_ids = implode(",",$trans_ids);
				// end
			
				// delete transaction records
				$this->_db->setQuery("DELETE FROM #__registrationpro_transactions WHERE reg_id IN ($uids)");
				$this->_db->query();
			
				if ( !$this->_db->query() ) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				
				// delete event discount transactions records with related transaction id
				$this->_db->setQuery("DELETE FROM #__registrationpro_event_discount_transactions WHERE trans_id IN ($trans_ids)");
				$this->_db->query();
			
				if ( !$this->_db->query() ) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
				// end
			}
			// end
												
			// delete event discount records with related event id
			$this->_db->setQuery("DELETE FROM #__registrationpro_event_discount WHERE event_id IN ($events)");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}		
			// end
			
			// delete payment details records with related event id
			$this->_db->setQuery("DELETE FROM #__registrationpro_payment WHERE regpro_dates_id IN ($events)");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}		
			// end
			
			// delete payment details records which is not related to any event id
			$this->_db->setQuery("DELETE FROM #__registrationpro_payment WHERE regpro_dates_id = 0 || regpro_dates_id = ''");
			$this->_db->query();
		
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}		
			// end						
		}
		//return true;
		return $total;
	}
	
	
	function move($inc, $cid){	
		//echo $cid; exit;
		$row  = $this->getTable('registrationpro_dates', '');
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
		$row		= $this->getTable('registrationpro_dates', '');
		$total		= count( $cid );
		//$order 	= josGetArrayInts( 'order' );
		$order		= JRequest::getVar('order', array(), 'default', 'array' );	
		//print_r($order);exit;
		
		$conditions = array();
						
		/*// update ordering values
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
		} // foreach*/
		
		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
				// remember to updateOrder this group
				//$condition = 'catid = '.(int) $row->catid.' AND state >= 0';
				$condition = "catsid = " . (int) $row->catsid;
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
			}
		}

		// execute updateOrder for each group				
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}
	
		// clean any existing cache files
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean('com_registrationpro');

		return true;
	}
	
	
	function getRegistered($rdid){		
		
		$this->_db->setQuery("SELECT products,status FROM #__registrationpro_register as r, #__registrationpro_transactions as t  WHERE r.rdid = '$rdid' and r.rid = t.reg_id and r.active=1 GROUP BY r.rid");

		$products = $this->_db->loadObjectList();

		$prods = array(0=>0);

		foreach($products as $product){
			$expl = explode("�\n",$product->products); 						
			
			foreach($expl as $rw){
				if($rw!=''){
					$expl2= explode("=",$rw);
					if(isset($expl2[1])){
							if(!isset($prods[$expl2[0]]))$prods[$expl2[0]]=0;
							if(!isset($prods['status'][$product->status]))$prods['status'][$product->status]=0;
							$prods[$expl2[0]] += $expl2[1];
							$prods['status'][$product->status] += $expl2[1];
					}else{
						$prods[0]+=1;
						$prods['status'][$product->status] += 1;
					}
				}
			}			
		}		
		return $prods;
	}
				
}//Class end
?>