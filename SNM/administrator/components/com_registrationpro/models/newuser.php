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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class registrationproModelNewuser extends JModelLegacy
{

	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();
		
		$event_id = JRequest::getVar('did', 0);
		$this->setId((int)$event_id);
	}
	
	function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}
	 
	function getEvent()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{								
			$query = "SELECT a.id AS did, a.dates, a.titel, a.times, a.endtimes, a.enddates, a.endtimes, a.datdescription, a.datimage, a.registra, a.unregistra, a.locid, a.catsid, a.max_attendance, a.regstart, a.regstop, a.form_id, a.terms_conditions, a.access as eventaccess, a.allowgroup, a.shw_attendees, a.regstop_type, a.force_groupregistration,a.enable_mailchimp, a.mailchimp_list, a.enable_create_user,a.enabled_user_group, "
					. "\n l.id as lid, l.club, l.city, l.url, l.locdescription, l.locimage, l.city, l.plz, l.street, l.country,"
					. "\n c.id as cid, c.catname, c.image, c.catdescription, c.access"
					. "\n FROM #__registrationpro_dates AS a"
					. "\n LEFT JOIN #__registrationpro_locate AS l ON a.locid = l.id"
					. "\n LEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid WHERE a.published = 1 AND a.id=".$this->_id;
			
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();									
			$this->_data = $this->_additionals($this->_data);
		}
	
		return $this->_data;
	}				
					
	function _additionals($rows)
	{			
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id = ".$this->_id;	
		$this->_db->setQuery($query);
		$rows->event_discounts = $this->_db->loadObjectList();
					
		return $rows;
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
	
	// check total registered user in event
	function getEventTickets($eventid){	
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
				
		return $row;		
	}
	
	// Get event discount records
	function getEventDiscount($ids)
	{
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE id in (".implode(",",$ids).")";			
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
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
	
	// Get event session records
	function getEventSession()
	{
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id = ".$this->_id." ORDER BY session_date, ordering";	
		$this->_db->setQuery($query);
		$event_sessions = $this->_db->loadObjectList();		
		return $event_sessions;						
	}
	
	// Get event session dates
	function getEventSessionDates()
	{
		$query = "SELECT DISTINCT(session_date) FROM #__registrationpro_sessions WHERE event_id = ".$this->_id." ORDER BY session_date, ordering";	
		$this->_db->setQuery($query);
		$event_session_dates = $this->_db->loadAssocList();		
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
}
?>