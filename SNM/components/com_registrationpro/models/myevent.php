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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationproModelMyevent extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		/*$id = JRequest::getVar('id', 0);
		$this->setId((int)$id);*/
		
	}
	function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}

	function &getData()
	{
		if ($this->_loadData())
		{

		}
		else  $this->_initData();

		return $this->_data;
	}
	
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			/*$query = "SELECT a.*, l.club, l.city, c.catname, u.name AS editor, g.name as groupname"
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\nLEFT JOIN #__groups AS g ON a.access = g.id"
				. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out WHERE a.id=".$this->_id;*/
			
			$query = "SELECT a.*, l.club, l.city, c.catname "
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out WHERE a.id=".$this->_id;					

			$this->_db->setQuery($query);

			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}
	
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$events = new stdClass();
			
			$events->id 				= null;
			$events->user_id			= null;
			$events->locid 				= null;
			$events->catsid 			= null;
			$events->dates 				= null;
			$events->times 				= null;
			$events->enddates 			= null;
			$events->endtimes 			= null;	
			$events->titel 				= null;
			$events->datdescription 	= null;
			$events->shortdescription 	= null;
			$events->datimage 			= null;
			$events->sendername 		= null;
			$events->sendermail 		= null;
			$events->deliverip 			= null;
			$events->deliverdate 		= null;
			$events->published 			= null;
			$events->registra 			= null;
			$events->unregistra 		= null;
			$events->notifydate 		= null;
			$events->checked_out 		= null;
			$events->checked_out_time 	= null;
			$events->access 			= 1;
			$events->viewaccess 		= 1;
			$events->regstart 			= null;
			$events->regstarttimes		= null;
			$events->regstop 			= null;
			$events->regstoptimes		= null;
			$events->regstop_type		= null;
			$events->reqactivation 		= null;
			$events->status 			= null;
			$events->form_id 			= null;
			$events->max_attendance 	= null;
			$events->terms_conditions 	= null;
			$events->ordering 			= null;
			$events->datarange			= null;
			$events->allowgroup			= null;			
			$events->notifyemails		= null;
			$events->recurrence_id		= null;
			$events->recurrence_type	= null;
			$events->recurrence_number	= null;
			$events->recurrence_weekday	= null;
			$events->recurrence_counter	= null;
			$events->gateway_account	= null;
			$events->force_groupregistration = null;
			$events->payment_method = null;
			$events->moderator_notify = null;
			$events->moderating_status = null;
												
		/*	$events->id					= 0;
			$events->setting_name		= null;
			$events->setting_value		= null;		*/				
			$this->_data				= $events;
			return (boolean) $this->_data;
		}
		return true;
	}
	
	
	function store($data)
	{
		//echo "<pre>"; print_r($data); exit;
		
		$registrationproAdmin =new registrationproAdmin;
		$user		=  JFactory::getUser();
		$config 	=  JFactory::getConfig();
		$repgrosettings = $registrationproAdmin->config();
		$tzoffset 	= $config->get('config.offset');

		$row  = $this->getTable('registrationpro_dates', '');
		if($repgrosettings["event_moderation"] == 1){
			$data["moderating_status"] = 0;
		}
		
		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
				
		$row->id = (int) $row->id;
		
		//echo "<pre>"; print_r($row); exit;
				
		$nullDate	= $this->_db->getNullDate();
						
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		
		// Check the events and update item order
		$row->checkin();
		$row->reorder('catsid = '.(int) $row->catsid);
		
		return $row->id;
	}
			
	function getRegistered($rdid){		
		$this->_db->setQuery("SELECT products,status FROM #__registrationpro_register as r, #__registrationpro_transactions as t  WHERE r.rdid = '$rdid' AND r.rid = t.reg_id AND r.active=1 GROUP BY r.rid");

		$products = $this->_db->loadObjectList();

		$prods = array(0=>0);

		foreach($products as $product){
			$expl = explode("¶\n",$product->products); 						
			
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
			
	function getTickets($eventid)
	{
		$query = "SELECT *"
		. "\nFROM #__registrationpro_payment"
		. "\nWHERE regpro_dates_id = '$eventid'"	
		. "\nORDER BY ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();		
	}
	
	function getEvent_discounts($eventid)
	{
		$query = "SELECT *"
		. "\nFROM #__registrationpro_event_discount"
		. "\nWHERE event_id = '$eventid'"	
		. "\nORDER BY ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
		
	function getLocations(){
	
		$user		=  JFactory::getUser();
		
		// get location avaliable for user
		$registrationproAdmin =new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		//$registrationproAdmin =new registrationproAdmin;
		$location_ids	= unserialize($repgrosettings['user_locations']);
		
		if(is_array($location_ids) && count($location_ids) > 0){
			$user_location_ids = implode(",",$location_ids);
		}else{
			$user_location_ids = 0;
		}				
		// end
				
		$query = "SELECT id AS value, club AS text"
			. "\nFROM #__registrationpro_locate"
			. "\nWHERE publishedloc = 1 AND (id in (".$user_location_ids.") OR user_id = ".$user->id.")"
			. "\nORDER BY ordering";
		$this->_db->setQuery( $query );				
		return $this->_db->loadObjectList();
	}
	
	function getCategories(){
	
		$user		=  JFactory::getUser();
	
		// get category avaliable for user
		$registrationproAdmin =new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		
		$category_ids	= unserialize($repgrosettings['user_categories']);
		
		if(is_array($category_ids) && count($category_ids) > 0){
			$user_category_ids = implode(",",$category_ids);
		}else{
			$user_category_ids = 0;
		}		
		// end
				
		$query = "SELECT id AS value, catname AS text"
			. "\nFROM #__registrationpro_categories"
			. "\nWHERE publishedcat = 1 AND (id in (".$user_category_ids.") OR user_id = ".$user->id.")"
			. "\nORDER BY ordering";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();				
	}
	
	function getForms(){	
		
		$user  =  JFactory::getUser();
		
		// get category avaliable for user
		$registrationproAdmin = new registrationproAdmin;

		$repgrosettings	= $registrationproAdmin->config();		
		$user_forms	= unserialize($repgrosettings['user_forms']);
		
		if(is_array($user_forms) && count($user_forms) > 0){
			$user_form_ids = implode(",",$user_forms);
		}else{
			$user_form_ids = 0;
		}		
		// end
				
		$query = "SELECT id AS value, title AS text"
			. "\nFROM #__registrationpro_forms"
			. "\nWHERE published = 1 AND (id in (".$user_form_ids.") OR user_id=".$user->id.")"
			. "\nORDER BY title";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();				
	}
	
	function clearrecurrence($eventids = array())
	{
		if(is_array($eventids) && count($eventids) > 0){
			$query = "UPDATE #__registrationpro_dates SET recurrence_id = 0, recurrence_type = 0, recurrence_number = 0, recurrence_weekday = 0,recurrence_counter = '0000-00-00' WHERE id in (".implode(",", $eventids).")";						
			$this->_db->setQuery($query);		
			///$this->_db->query();
					
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}
	
	// **************  Event report functions ************************ //	
	function getEventReportData()
	{
		$registrationproAdmin = new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		
		
		if($repgrosettings['accepted_registration_reports'] == 1)
		{
			$accepted = " r.status=1 ";
		}else{
			$accepted = " r.active=1 ";
		}
		$query  = "SELECT e.*,r.*,l.* FROM #__registrationpro_dates as e,#__registrationpro_register as r,#__registrationpro_locate as l,";
		$query .= "#__registrationpro_transactions as t WHERE l.publishedloc=1 AND e.locid=l.id AND e.id=r.rdid AND e.id = ".$this->_id;
		$query .= " AND r.rid = t.reg_id AND".$accepted."GROUP BY r.rid";
		
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
	
	function getEvent_sessions($eventid)
	{
		$query = "SELECT *"
		. "\nFROM #__registrationpro_sessions"
		. "\nWHERE event_id = '$eventid'"	
		. "\nORDER BY session_date, ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

?>