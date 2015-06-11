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

class registrationproModelUsers extends JModelLegacy
{
	var $_data = null;
	var $_total = null;
	var $_pagination = null;
	var $_eventid = null;
	
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		
		$mainframe = JFactory::getApplication();
				
		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'users_limitstart', 'limitstart', 0, 'int' );

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
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
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
				
		$query 		= "SELECT r.*, a.id, t.*, SUM(t.price) as amount , SUM(t.discount_amount) as tot_discount, SUM(edt.event_discount_amount) AS event_discount_amount, edt.event_discount_type"
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

	/**
	 * Build the order clause
	 *
	 * @access private
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.users.filter_order', 'filter_order', 'r.uregdate', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.users.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY r.uregdate'.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , r.uregdate ';
		}
		//echo $orderby,"<br>";
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'r.status = 0';
			} else if ($filter_state == 'A') {
				$where[] = 'r.status = 1';
			} else if ($filter_state == 'W') {
				$where[] = 'r.status = 2';
			} else if ($filter_state == 'PP') {
				$where[] = 't.payment_status = "pending"';
			} else if ($filter_state == 'CP') {
				$where[] = 't.payment_status = "completed"';
			}
		}				

		if ($search && $filter == 1) {
			$where[] = ' LOWER(r.firstname) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 2) {
			$where[] = ' LOWER(r.lastname) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 3) {
			$where[] = ' LOWER(r.email) LIKE \'%'.$search.'%\' ';
		}				
		
		$where[] 	= "r.rdid = ".$this->_eventid;
		$where[] 	= "r.rid = t.reg_id";
		//$where[] 	= "t.p_type = 'E'";
		$where[] 	= "r.active=1 group by t.reg_id";				

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _additionals($rows)
	{		
	
		if(count($rows) > 0) {
			foreach($rows as $rkey => $rvalue)
			{
				// add additional form fees 
				$query = "SELECT SUM(additional_field_fees) AS additional_field_fees FROM #__registrationpro_additional_from_field_fees WHERE reg_id =".$rvalue->rid;
				$this->_db->setQuery($query);				
				$data = $this->_db->loadResult();
				
				if($data){
					$rvalue->additional_field_fees = $data;
					$rvalue->amount += $data;
				}
				// end
				
				// add session feeds
				$query = "SELECT SUM(session_fees) AS session_fees FROM #__registrationpro_session_transactions WHERE reg_id =".$rvalue->rid;
				$this->_db->setQuery($query);				
				$data1 = $this->_db->loadResult();
				
				if($data1 > 0){
					$rvalue->session_fees = $data1;
					$rvalue->amount	+= $data1;
				}
				// end							
			}
		}
		return $rows;
	}

	function publish($cid = array(), $publish = 1)
	{
		$user 	=JFactory::getUser();
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
		$users 	= implode( ',', $cid );
						
		if (count( $cid ))
		{		
			echo "<pre>";print_r($users);echo "</pre>";
			$this->_db->setQuery("SELECT p_id FROM #__registrationpro_transactions WHERE reg_id IN ($users)");
			$tids = $this->_db->loadAssoclist();
			//echo "<pre>";print_r($tids);
			foreach($tids as $k=>$v)
			{
				$tids[$k] = $v['p_id'];
			}
			//echo "<pre>";print_r($tids);
			
			$tids= array_count_values($tids);
			$uid 	= count( $tids );
			$u_id 	= implode( ',', $tids );
			echo "<pre>";print_r($tids);echo "</pre>";
			
			foreach($tids as $x=>$y)
			{
				$query = "UPDATE #__registrationpro_payment SET product_quantity_sold = (product_quantity_sold - ".$y.") WHERE id = ".$x;
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			/* foreach( $tids as $ukey => $uvalue)
			{
				$query = "SELECT product_quantity_sold FROM #__registrationpro_payment WHERE id = ".$ukey;
				$this->_db->setQuery( $query );
				$product_quantity_sold = $this->_db->loadResult();
				$product_quantity_sold = $product_quantity_sold - $uvalue;
				/* if($product_quantity_sold < 0){ // if value is nagative, then set to 0
					$product_quantity_sold = 0;
				} 
			
				echo $query = "UPDATE #__registrationpro_payment SET product_quantity_sold = ".$product_quantity_sold." WHERE id = ".$ukey;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}	 */
			
			//$count_pid = array_count_values($tids);
			echo "<pre>";print_r($tids);
			
			
			// Delete user from database
			$this->_db->setQuery("DELETE FROM #__registrationpro_register WHERE rid IN ($users)");
			$this->_db->query();
			
			if ( !$this->_db->query() ) {
					$this->setError($this->_db->getErrorMsg());
					return false;
			}			
			// end
			
			// get transaction ids from transaction table to clean the records from event discount transaction table
			$this->_db->setQuery("SELECT id FROM #__registrationpro_transactions WHERE reg_id IN ($users)");
			$trans_ids = $this->_db->loadResultArray();
			$trans_ids = implode(",",$trans_ids);
			// end
			
			// get ticket ids from transaction table to update the ticket sold counts in tikets table
			$this->_db->setQuery("SELECT p_id FROM #__registrationpro_transactions WHERE reg_id IN ($users)");
			$ticket_ids = $this->_db->loadResultArray();
			
			foreach($ticket_ids as $tkey=>$tvalue)
			{
				$query = "SELECT product_quantity_sold FROM #__registrationpro_payment WHERE id = ".$tvalue;
				$this->_db->setQuery( $query );
				$product_quantity_sold = $this->_db->loadResult();
				$product_quantity_sold = $product_quantity_sold - 1;
				if($product_quantity_sold < 0){ // if value is nagative, then set to 0
					$product_quantity_sold = 0;
				}
			
				$query = "UPDATE #__registrationpro_payment SET product_quantity_sold = ".$product_quantity_sold." WHERE id = ".$tvalue;
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}					
			// end
						
			// delete transaction records with related event id			
			$this->_db->setQuery("DELETE FROM #__registrationpro_transactions WHERE reg_id IN ($users)");
			$this->_db->query();
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
			// end
			
			// delete event discount transactions records with related transaction id
			if($trans_ids) {				
				$this->_db->setQuery("DELETE FROM #__registrationpro_event_discount_transactions WHERE trans_id IN ($trans_ids)");
				$this->_db->query();
			
				if ( !$this->_db->query() ) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}	
			// end
			
			
			// delete registrationpro_additional_from_field_fees records with related user id			
			$this->_db->setQuery("DELETE FROM #__registrationpro_additional_from_field_fees WHERE reg_id IN ($users)");
			$this->_db->query();
			if ( !$this->_db->query() ) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}			
			// end
			
			
			// delete registrationpro_session_transactions records with related user id			
			$this->_db->setQuery("DELETE FROM #__registrationpro_session_transactions WHERE reg_id IN ($users)");
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
	
	function changeStatus($rid, $status=0, $eventid)				
	{		
		$user_ids = implode(",", $rid);
		
		$query = "UPDATE #__registrationpro_register SET status = $status WHERE rid IN ($user_ids) AND rdid = $eventid";
		$this->_db->setQuery($query);
		$this->_db->query();
			
		if ( !$this->_db->query() ) {	
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";	
			exit();	
		}
		
		return true;
	}
	
	function changePaymentStatus($rid, $status=0, $eventid)				
	{		
		$user_ids = implode(",", $rid);
		
		if($status == 0){
			$payment_status = "completed";
		}else{
			$payment_status = "pending";
		}
		
		$query = "UPDATE #__registrationpro_transactions SET payment_status = '$payment_status' WHERE reg_id IN ($user_ids)";
		$this->_db->setQuery($query);
		$this->_db->query();
			
		if ( !$this->_db->query() ) {	
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";	
			exit();	
		}
		
		return true;
	}
	
	function getConfimationEmailStatus($rid, $eventid)				
	{		
		$user_ids = implode(",", $rid);
		
		$query = "SELECT rid FROM #__registrationpro_register WHERE rid IN ($user_ids) AND rdid = $eventid AND confirmation_send = 0";
		$this->_db->setQuery($query);
		return $this->_db->loadResultArray();					
	}
	
	function moveuser($user_ids, $event_id)
	{		
		$query = "UPDATE #__registrationpro_register SET rdid = ".$event_id." WHERE rid IN ($user_ids)";
		$this->_db->setQuery($query);
		$this->_db->query();
			
		if ( !$this->_db->query() ) {	
			echo "<script> alert('".$this->_db->getErrorMsg()."'); window.history.go(-1); </script>\n";	
			exit();	
		}		
	}
	
	
	function changeAttendStatus($id,$att,$rdid)
	{
		if($att==0){
			$change_att = 1;
		}else{
			$change_att = 0;
		}
		$query = 'UPDATE #__registrationpro_register SET attended = '.$change_att. ' WHERE rid = '. $id;
		$this->_db->setQuery( $query );
		$this->_db->query();
		return true;
		
	}	
	
}//Class end
?>