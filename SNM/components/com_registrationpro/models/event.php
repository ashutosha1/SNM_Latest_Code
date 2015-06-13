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

class registrationproModelEvent extends JModelLegacy
{

	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();
		
		$event_id = JRequest::getVar('did',0,'','int');
		$this->setId((int)$event_id);
	}
	
	function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}
	// get accepted event tickets count in event to be deducted from max attendance
	function getAcceptedTicketsCount($eventid){	
		$query = "SELECT count(*) FROM #__registrationpro_register WHERE rdid = $eventid  AND status=1";			
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	// get pending event tickets count in event  to be deducted from max attendance
	function getPendingTicketsCount($eventid){	
		$query = "SELECT count(*) FROM #__registrationpro_register WHERE rdid = $eventid AND status=0";			
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	} 
	
 	// get accepted event tickets count in event to be deducted from drop-down
	function getAcceptedTicketsCountAlone($eventid,$tid){	
		$query = "SELECT count(*) FROM #__registrationpro_register WHERE rdid = $eventid  AND products = $tid AND status=1";			
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	// get pending event tickets count in event to be deducted from drop-down
	function getPendingTicketsCountAlone($eventid,$tid){	
		$query = "SELECT count(*) FROM #__registrationpro_register WHERE rdid = $eventid  AND products = $tid  AND status=0";			
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	} 
	function getEvent()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{			
			$query = "SELECT a.id AS did, a.id, a.image, a.parent_id, a.user_id, a.dates, a.titel, a.times, a.endtimes, a.enddates, a.endtimes, a.datdescription, a.datimage, a.registra, a.unregistra, a.locid, a.catsid, a.max_attendance, a.regstart, a.regstarttimes, a.regstop, a.regstoptimes, a.form_id, a.terms_conditions, a.access as eventaccess, a.viewaccess, a.allowgroup,a.shw_attendees, a.regstop_type, a.force_groupregistration, a.payment_method,  a.metadescription, a.metakeywords, a.metarobots,a.instructor,a.enable_mailchimp, a.mailchimp_list,a.enable_create_user,a.enabled_user_group,a.status, "
					. "\n l.id as lid, l.club, l.city, l.url, l.locdescription, l.locimage, l.city, l.plz, l.street, l.country,"
					. "\n c.id as cid, c.catname, c.image, c.catdescription, c.access"
					. "\n FROM #__registrationpro_dates AS a"
					. "\n LEFT JOIN #__registrationpro_locate AS l ON a.locid = l.id"
					. "\n LEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid WHERE a.published = 1 AND a.moderating_status = 1 AND a.id=".$this->_id;
			
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();									
			$this->_data = $this->_additionals($this->_data,$regpro_include_pending);
		}
	
		return $this->_data;
	}	
	function getEvent_new($regpro_include_pending)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{						
			$query = "SELECT a.id AS did, a.id, a.image AS poster, a.parent_id, a.user_id, a.dates, a.titel, a.times, a.endtimes, a.enddates, a.endtimes, a.datdescription, a.shortdescription, a.datimage, a.registra, a.unregistra, a.locid, a.catsid, a.max_attendance, a.regstart, a.regstarttimes, a.regstop, a.regstoptimes, a.form_id, a.terms_conditions, a.access as eventaccess, a.viewaccess, a.allowgroup,a.shw_attendees, a.regstop_type, a.force_groupregistration, a.payment_method,  a.metadescription, a.metakeywords, a.metarobots,a.instructor,a.enable_mailchimp, a.mailchimp_list,a.enable_create_user,a.enabled_user_group,a.status, "
					. "\n l.id as lid, l.club, l.city, l.url, l.locdescription, l.locimage, l.city, l.plz, l.street, l.country,"
					. "\n c.id as cid, c.catname, c.image, c.catdescription, c.access"
					. "\n FROM #__registrationpro_dates AS a"
					. "\n LEFT JOIN #__registrationpro_locate AS l ON a.locid = l.id"
					. "\n LEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid WHERE a.published = 1 AND a.moderating_status = 1 AND a.id=".$this->_id;
			
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();									
			$this->_data = $this->_additionals($this->_data,$regpro_include_pending);
		}
	
		return $this->_data;
	}	
	
	function _additionals($row,$regpro_include_pending)
	{
		$row = $this->getAvailable($row,$regpro_include_pending); // get available ticket for event 
		
		return $row;
	}			
					
	function getEventDiscounts()
	{	
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id = ".$this->_id." ORDER BY early_discount_date, min_tickets";	
		$this->_db->setQuery($query);
		$event_discounts = $this->_db->loadObjectList();
					
		return $event_discounts;
	}
	
	//check event is paid of free
	function is_event_free($eventid)
	{
		$query = "SELECT count(*) cnt FROM #__registrationpro_payment WHERE regpro_dates_id = ".$eventid;		
		$this->_db->setQuery($query);		
		return $this->_db->loadResult();				
	}
	
	// check total registered user in event
	function getRegistered($eventid){
		$query = "SELECT count(*) FROM #__registrationpro_register WHERE rdid = $eventid and active = 1";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	// check all event tickets in event
	function getEventTickets($eventid,$regpro_include_pending){	
		$query = "SELECT * FROM #__registrationpro_payment WHERE regpro_dates_id = $eventid ORDER BY ordering";			
		$this->_db->setQuery($query);
		$tickets = $this->_db->loadObjectList();
		//echo "Pending registration : ".$regpro_include_pending;
		foreach($tickets as $ticket){
			if( $ticket->type== "A")
				continue;
			if($regpro_include_pending == 0){
				$accepted_tickets_count = $this->getAcceptedTicketsCountAlone($eventid,$ticket->id);
				$ticket->product_quantity_sold = $accepted_tickets_count;
			}else{
				$accepted_tickets_count = $this->getPendingTicketsCountAlone($eventid,$ticket->id);
				if(!empty($ticket->product_quantity_sold))
				{
					$accepted_tickets_count = $this->getAcceptedTicketsCountAlone($eventid,$ticket->id);
					$ticket->product_quantity_sold = $accepted_tickets_count;
				}
			}
			
		}//echo '<pre>'; print_r($tickets);die;
		return $tickets;
	}
	
	// Get only event tickets total in event, Not additional tickets
	function getEventTicketsOnly($eventid, $ticket_ids){	
		$query = "SELECT id FROM #__registrationpro_payment WHERE regpro_dates_id = $eventid AND type = 'E' AND id in (".implode(",",$ticket_ids).")";			
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}	
	
	// check total registered user in event
	function getEventTermsandconditions($eventid){	
		$query = "SELECT * FROM #__registrationpro_payment WHERE regpro_dates_id = $eventid ORDER BY ordering";			
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	// Check Coupon code function
	function chk_coupon_code($coupon_code)
	{		
		$query 	= "SELECT * FROM #__registrationpro_coupons WHERE BINARY code = '".$coupon_code."'"
					. "\n AND start_date <= CURRENT_DATE() AND end_date >= CURRENT_DATE() "
					. "\n AND published = 1";
					
		$this->_db->setQuery($query);		
		$row 	= $this->_db->loadObject();
		
		/*foreach($row as $key => $value){
			if($value->eventids == 0 || $value->eventids == ""){
				$value->	
			}else{
				
			}
		}		
			*/	
		return $row;		
	}
	
	// Get event discount records
	function getEventDiscount($ids)
	{
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE id in (".implode(",",$ids).")";			
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	// Get event terms and conditions records
	function getEventsTermsAndConditions ($eventids)
	{
		// Get events terms and conditions	
		$query = "SELECT titel,terms_conditions FROM #__registrationpro_dates WHERE id in  (".$eventids.") ORDER BY ordering";	
		$this->_db->setQuery($query);
		$event_terms = $this->_db->loadObjectList();
		
		return $event_terms;
	}
	
	// Get event session records
	function getEventSession()
	{
		$db= JFactory::getDBO();
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id = ".$this->_id." ORDER BY session_date, ordering";	
		$db->setQuery($query);
		$event_sessions = $db->loadAssocList();	
		return $event_sessions;						
	}
	
	// Get event session dates
	function getEventSessionDates()
	{
		$db= JFactory::getDBO();
		$query = "SELECT DISTINCT(session_date) FROM #__registrationpro_sessions WHERE event_id = ".$this->_id." ORDER BY session_date, ordering";	
		$db->setQuery($query);
		$event_session_dates = $db->loadAssocList();	
		return $event_session_dates;						
	}
	
	// Get event session header
	function getEventSessionHeader()
	{
		$query = "SELECT session_page_header FROM #__registrationpro_dates WHERE id = ".$this->_id;	
		$this->_db->setQuery($query);
		$event_session_header = $this->_db->loadResult();		
		return $event_session_header;						
	}
	
	//get available sheet
	function getAvailable($row,$regpro_include_pending)
	{
	  		// Get filled seats for Events
		if($regpro_include_pending == 0){
			$accepted_tickets_count = $this->getAcceptedTicketsCount($this->_id);
			@$row->avaliable = $row->max_attendance-$accepted_tickets_count;	
		}else{
			$pending_tickets_count = $this->getPendingTicketsCount($this->_id);
			$accepted_tickets_count = $this->getAcceptedTicketsCount($this->_id);
			@$row->avaliable = $row->max_attendance - ($pending_tickets_count + $accepted_tickets_count);	
		}
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
			$row->registered 	= $registerdusers;
			$row->avaliable 	= $row->max_attendance - $registerdusers;
			
		}else{
			@$row->registered = 0;
			$row->avaliable  = $row->max_attendance;
		}	 
		// end
		//@$row->avaliable = $row->max_attendance-$accepted_tickets_count;		
		if($row->max_attendance <= 0){
			  $row->avaliable='U';
		}
						
		return $row;
	}
	
	
	// check event forms fees fields
	function getFeesFields($eventids = array()){	
		$query = "SELECT f.* FROM #__registrationpro_forms as frm "
				."\n LEFT JOIN #__registrationpro_fields as f ON frm.id = f.form_id "
				."\n LEFT JOIN #__registrationpro_dates as e ON e.form_id = frm.id "
				."\n WHERE f.fees_field = 1 AND f.published = 1 AND f.fees != '' AND e.id IN (".implode(",",$eventids).")";			
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
				
	// **************  Event report functions ************************ //	
	function getEventReportData()
	{
		$query = "SELECT e.*,r.*,l.* FROM #__registrationpro_dates as e,#__registrationpro_register as r,#__registrationpro_locate as l, #__registrationpro_transactions as t WHERE l.publishedloc=1 AND e.locid=l.id AND e.id=r.rdid AND e.id = ".$this->_id." AND r.rid = t.reg_id AND r.active=1 GROUP BY r.rid"; 
		
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		foreach($data as $key=>$value)
		{
			$data[$key]->params = unserialize($data[$key]->params);
		}
				
		return $data;
	}
	
	function getEventTransactionData()
	{
		$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type FROM #__registrationpro_register as r, #__registrationpro_transactions as t "
				 ."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				 ."\n WHERE r.rid = t.reg_id AND r.rdid =".$this->_id;
		$this->_db->setQuery($query);			
		$productdata = $this->_db->loadObjectList();
		
		return $productdata;
	}
	
	function getUserinfoForExcelReport()
	{
		/*$query = "SELECT r.params,r.firstname,r.lastname,r.email,r.uregdate, t.*, edt.event_discount_amount, edt.event_discount_type "
				."\n FROM #__registrationpro_dates as e , #__registrationpro_register as r,#__registrationpro_locate as l, #__registrationpro_transactions as t,"
				."\n #__registrationpro_event_discount_transactions AS edt "
				."\n where l.publishedloc=1 "
				."\n AND e.locid=l.id "
				."\n AND e.id=r.rdid "
				."\n AND e.id = ".$this->_id
				."\n AND r.rid = t.reg_id "
				."\n AND t.id = edt.trans_id"
				."\n AND r.active=1";	*/	
				
		$query = "SELECT r.params,r.firstname,r.lastname,r.email,r.uregdate, t.*, edt.event_discount_amount, edt.event_discount_type "
				."\n FROM #__registrationpro_dates as e , #__registrationpro_register as r,#__registrationpro_locate as l, #__registrationpro_transactions as t "
				."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				."\n where l.publishedloc=1 "
				."\n AND e.locid=l.id "
				."\n AND e.id=r.rdid "
				."\n AND e.id = ".$this->_id
				."\n AND r.rid = t.reg_id "				
				."\n AND r.active=1";					
		
		//echo $query; exit;		
		$this->_db->setQuery($query);
		
		$data = $this->_db->loadObjectList();
		
		//echo "<pre>"; print_r($data); exit;
		
		// apply event discount upon user tickets
		foreach($data as $pkey => $pvalue)
		{
			if($pvalue->event_discount_amount > 0){
				if($pvalue->event_discount_type == 'P'){
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$actual_price_without_per 		= ($pvalue->price * 100) / (100 - $pvalue->event_discount_amount);					
					$event_discounted_amount_price 	= $actual_price_without_per * $pvalue->event_discount_amount / 100;					
					$pvalue->discount_amount		+= $event_discounted_amount_price;
					$pvalue->price 			 		= $actual_price_without_per;
				}else{
					$pvalue->discount_amount		+= $pvalue->event_discount_amount;
				}
				
				$pvalue->final_price = 	$pvalue->price - $pvalue->discount_amount;
				
				if($pvalue->final_price <= 0){
					$pvalue->final_price = 0;
				}
			}
		}
		// end
		
		//echo "<pre>"; print_r($data); exit;
		
		return $data;
	}
	function getFormStatus($id = null){
		$query = "SELECT published FROM #__registrationpro_forms WHERE id = " .$id;
		$this->_db->setQuery($query);
		$status = $this->_db->loadResult();
		return $status;
	}
}
?>