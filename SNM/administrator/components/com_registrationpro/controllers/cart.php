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

jimport('joomla.application.component.controller');

class registrationproControllerCart extends registrationproController
{
	var $regpro_config;
	
	function __construct()
	{
		global $mainframe;
		
		parent::__construct();
		
		$mainframe =JFactory::getApplication();
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$this->regpro_config	= $registrationproAdmin->config();	
	}
		
	/************************ Show Cart ***************/	
	function cart()
	{
		global $mainframe, $option;	

		//JRequest::checkToken() or jexit('Invalid Token');		
						
		JRequest::setVar( 'view', 'cart' );

		$db		=JFactory::getDBO();
		$user	=JFactory::getUser();
		
		$model 	= $this->getModel('newuser');		
		$row 	= $model->getEvent();		
		//echo "<pre>"; print_r($row); exit;				
		
		// check validation
		/*$row->message .= registrationproHelper::check_max_attendance($row, 0, $this->regpro_config, 2);		
		$row->message .= registrationproHelper::check_event_registration_enable($row, $this->regpro_config, 2);
		$row->message .= registrationproHelper::check_event_registration_date($row, $this->regpro_config, 2);*/
		
		// check event tikcets selected by user or not
		$product_id	 = JRequest::getVar('product_id',array(),'POST');
		if(count($product_id) > 0){
		
			// Check group regsitration
			$groupregistration = JRequest::getVar('chkgroupregistration',array(),'POST');
			if($groupregistration && $row->allowgroup == 1){
				$row->allowgroup = 1;	// this should be per-ticket, not general option
			}else{
				$row->allowgroup = 0;
			}
			// end
			
			/*** Create main cart session ***/
			//$cart 	 	= $session->set('cart',''); // clear cart session
			$cart_data 	= $this->manage_tickets($row, $this->regpro_config);
			/*** End ***/
			
			//echo "<pre>"; print_r($cart_data); exit;

			// check max_attendance
			$totqty = 0;
			$totqty	= $cart_data['total_qty'];			
			$nothing = array();
			//$row->message .= registrationproHelper::check_max_attendance($row, $totqty, $cart_data, $this->regpro_config, 4);		
			$registrationproHelper = new registrationproHelper;			
			$row->message .= $registrationproHelper->check_max_attendance($row, $nothing, $cart_data, $this->regpro_config, 4);
			
			//$row->message .= registrationproHelper::check_max_attendance($row, $totqty, $this->regpro_config, 4);																			
									
			//echo "<pre>"; print_r($row); exit;
						
		}else{
			$msg 	= JText::_('EVENTS_SELECT_EVENT_TICKET').'<br/>';	
			$link 	= JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->did."&Itemid=".$Itemid);
			$this->redirect($link,$msg);		
		}
				
		JRequest::setVar( 'row', $row);
		
		parent::display();
	}			


	/************************ Create Cart Session ***************/	
	function manage_tickets(&$row){
		global $mainframe;
		
		$database	=JFactory::getDBO();
								
		$tickets = array();
		$tickets_info = array();
		$tickets_id = array();
		
		// Event tickets
		$product_id  = JRequest::getVar('product_id',array(),'POST');		
		$product_qty = JRequest::getVar('product_qty',array(),'POST');				
		
		// Additional tickets
		$product_id_add  = JRequest::getVar('product_id_add',array(),'POST');
		$product_qty_add = JRequest::getVar('product_qty_add',array(),'POST');
		
		
		// Event sessions
		$sessions	= array();	
		$event_sessions  = JRequest::getVar('sessions',array(),'POST');		
		if(count($event_sessions) > 0) {
			$sessions = $this->getSessionsRecords($row->did, $event_sessions);
		}
		
		// get event ticket total quantity
		$qty = 0;
		$i = 0;		
		foreach($product_id as $key=>$value)
		{
			$tickets[$i]['id'] = $key;
			foreach($product_qty as $qkey=>$qvalue)
			{
				if($key == $qkey){
					$tickets[$i]['qty'] = $qvalue;
					$qty += $qvalue;
				}
			}
			$i++;
			$tickets_id[] = $key;
		}
		
		// get additional ticket total quantity
		$addqty = 0;				
		foreach($product_id_add as $addkey=>$addvalue)
		{
			$tickets[$i]['id'] = $addkey;
			foreach($product_qty_add as $addqkey=>$addqvalue)
			{			
				if($addkey == $addqkey){
					$tickets[$i]['qty'] = $addqvalue;
					$addqty += $addqvalue;
				}
			}
			$i++;
			$tickets_id[] = $addkey;
		}

		//echo"<pre>";print_r($tickets);	exit;
		
		// get tickets information from table 
			$payments = array();
			if($qty >= 1){
			
				// get the total sum of select tickets to check free/paid event
				$query = "SELECT SUM(total_price) as event_total FROM #__registrationpro_payment WHERE regpro_dates_id = '{$row->did}' AND id in (".implode(",",$tickets_id).")";
				$database->setQuery($query);
				$event_total = $database->loadResult();
				// end
				
				// get the tickets records				
				$payments = $this->getTicketRecords($row->did, $tickets_id);							
				//echo "<pre>";print_r($event_total); 
				//echo "<pre>";print_r($payments); exit;
												
				if($event_total <= 0 && empty($payments)){									
					$payments[0] = new stdClass();
					$payments[0]->id = 0;
					$payments[0]->regpro_dates_id = $row->did;
					$payments[0]->product_name = JText::_('ADMIN_EVENTS_DEFAULT_PRODUCT');
					$payments[0]->product_description = '';
					$payments[0]->product_price = '0.00';
					$payments[0]->total_price = '0.00';
					$payments[0]->shipping = 0;
					$payments[0]->tax = '0.00';
					$payments[0]->type = 'E';
					//$freeevent = 1;
				}else{
					// add qty field after fetching tickets records from database tables
					foreach($payments as $pkey => $pvalue)
					{
						foreach($tickets as $tkey => $tvalue)
						{
							if($payments[$pkey]->id == $tickets[$tkey]['id']){																	
								$payments[$pkey]->qty = $tickets[$tkey]['qty'];
								
								// assign event sessions to cart ticket array
								if($payments[$pkey]->type == 'E') {
									foreach($sessions as $skey => $svalue)
									{
										$svalue->qty = $payments[$pkey]->qty;
									}
									$payments[$pkey]->sessions = $sessions; 
								}
								// end
							}
						}
					}
					// end
				}							
			}
			//echo "<pre>";print_r($payments); exit;
		// end
		
		$arr_cart = array();		
		if($row->allowgroup) $arr_cart['groupregistrations'][$row->did]= $row->did; // store event id if group registration enable for event		
		$arr_cart['allowgroup'] 		= $row->allowgroup;					// this should be per-ticket, not general option		
		$arr_cart['eventid'] 			= $row->did;						// event id
		$arr_cart['eventids'][$row->did]= $row->did;						// all event ids
		$arr_cart['event_discounts'] 	= $this->getEventDiscounts($arr_cart['eventids']); // event discounts array		
		$arr_cart['event_short_desc'] 	= $row->shortdescription;   		// event short description
		$arr_cart['event_detail_desc'] 	= $row->datdescription;				// event detail description
		//$arr_cart['free_event']		= $freevent;						// free event flag
		$arr_cart['event_form_id'] 		= $row->form_id;					// event form id
		$arr_cart['ticktes'] 			= $payments;						// tickets array
		$arr_cart['sessions']			= $sessions;						// assign sessions to make cart array
		
		// Create the orignal ticktes array
			$grand_total 	= 0.00;
			$total_qty		= 0;
			$total_tqty		= 0;
			$total_addqty	= 0;
			$total_tax		= 0.00;	
			
			$total_tqty		= abs($qty);
			$total_addqty	= abs($addqty);
			$total_qty		= abs($addqty + $qty);
			
			// apply event discounts if critera match
			if(count($row->event_discounts) > 0){
				//$payments = $this->apply_event_discount($payments , $row->event_discounts, $total_qty);
				$payments = $this->apply_event_discount($arr_cart);
				//echo "<pre>";print_r($payments); exit;
			}
			// end
									
			foreach($payments as $pkey => $pvalue)
			{
				foreach($tickets as $tkey => $tvalue)
				{
					if($payments[$pkey]->id == $tickets[$tkey]['id']){																	
						$payments[$pkey]->qty 			= $tickets[$tkey]['qty'];
						$payments[$pkey]->total_amount 	= $tickets[$tkey]['qty'] * $payments[$pkey]->total_price;
						
						if($payments[$pkey]->type=='A'){
							$additinal_ticket_total += $payments[$pkey]->total_amount;	
						}
												
						$grand_total 					+= $payments[$pkey]->total_amount;										
						$total_tax   					+= $payments[$pkey]->tax;
						//$payments[$pkey]->tax_price	= ($payments[$pkey]->price_without_tax * $payments[$pkey]->tax)/100;
						$payments[$pkey]->tax_price		= ($payments[$pkey]->product_price * $payments[$pkey]->tax)/100;
						
						if($payments[$pkey]->price_without_tax > 0) {
							$payments[$pkey]->price_without_tax = $payments[$pkey]->price_without_tax;
						}
						
						// add event session amounts
						if(is_array($payments[$pkey]->sessions) && count($payments[$pkey]->sessions) >0 ) {
							foreach ($payments[$pkey]->sessions as $cskey => $csvalue)
							{
								$grand_total 		+= $csvalue->fee * $payments[$pkey]->qty;
							}
						}
						// end
					}
				}
			}
		// end
	
		//echo "<pre>";print_r($payments); exit;
		//echo "<pre>";print_r($grand_total); exit;		
		$arr_cart['event_discounts'] 	= $row->event_discounts;			// event discounts array
		$arr_cart['total_tqty'] 		= $total_tqty;						// total event tickets qty
		$arr_cart['total_addqty'] 		= $total_addqty;					// total additinal ticket qty
		$arr_cart['total_qty'] 			= $total_qty;						// total tickets qty
		$arr_cart['total_tax'] 			= $total_tax;						// total tax
		$arr_cart['sub_total'] 			= $grand_total;						// total tickets total
		$arr_cart['grand_total'] 		= $grand_total;						// total cart total
		$arr_cart['discount'] 			= 0.00;								// total discount	
		$arr_cart['group_discount']		= 0.00;								// total group discount				
		$arr_cart['early_discount']		= 0.00;								// total early discount
		$arr_cart['both_discounts']		= 0.00;								// group + early discounts
		$arr_cart['currency_sign']		= $this->regpro_config['currency_sign'];	// currency sign		

		//echo "<pre>";print_r($arr_cart); exit;
								
		$arr_cart = $this->calculate_group_early_discounts($arr_cart); // calculate event group and early discounts
				
		$arr_cart = $this->calculate_grandtotal_after_discounts($arr_cart); // calculate grand and sub total after group and early discounts
					
		// check event total is greater then 0 or not
		if($arr_cart['grand_total'] > 0){
			$arr_cart['free_event'] = 0;
		}else{
			$arr_cart['free_event'] = 1;
		}
		// end
		
		$arr_cart['added_by']		= "A";
		
						
		//echo "<pre>";print_r($arr_cart); exit;
		
		$session =JFactory::getSession();
		$session->set('cart', $arr_cart);
		$cart 	 = $session->get('cart');
		return $cart;		
	}
	
	// apply event discount
	function apply_event_discount($cart)
	{	
		$discount_records = array();			
		// get current date
		$registrationproHelper = new registrationproHelper;
		$current_date	= $registrationproHelper->getCurrent_date("Y-m-d");	
		if(is_array($cart)){
			if(is_array($cart['eventids'])){
				foreach($cart['eventids'] as $ekey => $evalue)
				{
					$e_flag = 0;
					foreach($cart['event_discounts'] as $dkey => $dvalue)
					{
						if($evalue == $dvalue->event_id){
							$totalqty = 0;
							foreach($cart['ticktes'] as $tkey => $tvalue)
							{
								if($tvalue->type == 'E' && $tvalue->regpro_dates_id == $dvalue->event_id){
									$totalqty += $tvalue->qty;
								}
							}
							
							// Apply Group discounts
							if($dvalue->discount_name == 'G'){
								if($totalqty >= $dvalue->min_tickets){																	
									$discount_records[$dvalue->event_id]['G']  = $dvalue;									
								}
							}		
									
							// Apply Eearly registration discounts			
							if($dvalue->discount_name == 'E'){
								if($dvalue->early_discount_date >= $current_date && $e_flag == 0){									
									$discount_records[$dvalue->event_id]['E']   = $dvalue;									
									$e_flag = 1;		
								}
							}	
						}
					}
				}
			}
		}
			
		// get applied event discounts records						
		$tickets = $cart['ticktes']; //echo "<pre>";print_r($tickets); exit;
		foreach($tickets as $tkey => $tvalue)
		{				
			$total_price 	= 0.00;
			$product_price 	= 0.00;										
			$tax_amount		= 0.00;
			$total_discount = 0.00;
			
			$event_discount_ids = array();
			
			if($tickets[$tkey]->type == "E"){ // discount apply only on non-additional tickets
			
				$tickets[$tkey]->price_without_tax = $tickets[$tkey]->product_price;
																										
				foreach($discount_records as $dkey => $dvalue)
				{
					if(is_array($dvalue)){
						foreach($dvalue as $ddkey => $ddvalue)
						{
							if( $ddvalue->event_id == $tickets[$tkey]->regpro_dates_id){
								if($ddvalue->discount_type == 'P'){
									$total_discount	+= $tickets[$tkey]->product_price * $ddvalue->discount_amount / 100;
									echo 'Total Discount : '.$total_discount;die;
								}else{
									$total_discount	+= $ddvalue->discount_amount;
								}
								
								$event_discount_ids[] = $ddvalue->id;
							}
						}
					}
				}
					//echo '<pre>';print_r($discount_records);echo '</pre>';
					//print('Total Discount : '.$total_discount);				
				$tickets[$tkey]->event_discount_id				= $event_discount_ids;
				$tickets[$tkey]->event_total_discount_amount	= number_format($total_discount,2);									
			}else{
				$tickets[$tkey]->price_without_tax = $tickets[$tkey]->product_price;
			}
		}		
		return $tickets;
	}
	// Calculate the group and early discounts
	function calculate_group_early_discounts(&$cart)
	{	
		foreach ($cart['ticktes'] as $tck){	
			$perdiscount = 0.00;
			$fixdiscount = 0.00;
			$ticket_price_after_discount = 0.00;
			foreach($cart['event_discounts'] as $dt){				
				if($tck->event_discount_id && in_array($dt->id,$tck->event_discount_id)){				
					if($tck->type == 'E'){				
						if($dt->discount_name=='G'){
							if($dt->discount_type=='P'){
								$cart['group_discount']			+= 	($tck->product_price * $dt->discount_amount/100)* $tck->qty;
								$perdiscount 					+= 	$dt->discount_amount;							
							}else{												
								$cart['group_discount']			+= 	$dt->discount_amount * $tck->qty;
								$fixdiscount					+=  $dt->discount_amount;
							}
						}
						
						if($dt->discount_name=='E'){										
							if($dt->discount_type=='P'){
								//$cart['early_discount'] 		+= ($tck->total_price * $dt->discount_amount/100) * $tck->qty;
								$cart['early_discount'] 		+= ($tck->product_price * $dt->discount_amount/100)* $tck->qty;																						
								$perdiscount 					+= $dt->discount_amount;					
							}else{
								$cart['early_discount']   		+= $dt->discount_amount * $tck->qty;
								$fixdiscount					+= $dt->discount_amount;
							}
						}
						
						$ticket_price_after_discount 	=  ($tck->product_price - ($tck->product_price * $perdiscount/100)) - $fixdiscount;			
						$tck->tax_price 				=  ($ticket_price_after_discount * $tck->tax)/100;	
						$tck->total_price				=  number_format($tck->product_price + $tck->tax_price,2); 
						$tck->total_amount				=  ($tck->product_price  + $tck->tax_price) * $tck->qty;		
					}				
				}														
			}
		}				
						
		return $cart;
	}
	
	// Calculate sub and grand total after group and early discounts
	function calculate_grandtotal_after_discounts(&$cart, $nonaddional_ticket_total=0)
	{					
		$resub_total = 0.00;
		$regrand_total = 0.00;
		$cart_coupon_discount = 0.00;
		
		$cart['both_discounts'] = $cart['group_discount'] + $cart['early_discount'];
		
		if($cart['discount'] > 0){ // calculate and apply coupon, group an early discounts
			
			$cart['error_message'] = "";		
			foreach ($cart['ticktes'] as $tck){
			
				$apply_coupon_flag = 0;
				if($cart['discount_coupon_events'] == 0){
				
					if($cart['discount_type'] == 'P'){
						$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_PERCENT_DISCOUNT')." ".$cart['discount']."%";
					}else{
						$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_AMOUNT_CORRECT')." ".$this->regpro_config['currency_sign'].$cart['discount'];
					}
				
					$apply_coupon_flag = 1;
				}else{
					if(in_array($tck->regpro_dates_id,$cart['discount_coupon_events'])){
						$registrationproHelper = new registrationproHelper;
						$event_title = $registrationproHelper->getEventName($tck->regpro_dates_id); // get event titel to display in discount message
						
						if($cart['discount_type'] == 'P'){
							$cart['error_message'] 	= sprintf(JText::_('EVENT_CART_MSG_COUPON_EVENT_PERCENT_DISCOUNT'),$event_title)." ".$cart['discount']."%";
						}else{
							$cart['error_message'] 	= sprintf(JText::_('EVENT_CART_MSG_COUPON_EVENT_AMOUNT_CORRECT'),$event_title)." ".$this->regpro_config['currency_sign'].$cart['discount'];
						}
					
						$apply_coupon_flag = 1;																		
					}else{
						$apply_coupon_flag = 0;
						//$cart['error_message'] = "";
					}
				}
				
				//if($tck->type == 'E'){
				if($tck->type == 'E' && $tck->product_price > 0 && $apply_coupon_flag){
					// check if ticket price already lower then coupon discount after event discount calculations																			
					if($tck->product_price > $tck->event_total_discount_amount) {
																						
						$flag_cart_coupon_discount = 0.00;
						if($cart['discount_type'] == 'A'){							
							$flag_cart_coupon_discount = $cart['discount'] * $tck->qty;
						}elseif($cart['discount_type'] == 'P'){
							$flag_ddd = $cart['discount'] * $tck->qty;
							$flag_cart_coupon_discount = ($tck->product_price * $flag_ddd) / 100;
						}
						$ticket_price_after_event_discount = $tck->product_price - $tck->event_total_discount_amount;
						if($flag_cart_coupon_discount >= ($ticket_price_after_event_discount * $tck->qty)) {
							$cart_coupon_discount += $ticket_price_after_event_discount  * $tck->qty;
							$tck->discount_amount = $ticket_price_after_event_discount;
						}else {		
							$cart_coupon_discount += $flag_cart_coupon_discount;
						}
					}
				}
			}
												
			//$cart['cart_total_discount'] = $cart_total_discount;
			$cart['cart_total_discount'] = $cart_coupon_discount + $cart['both_discounts'];
			$cart['discount'] 		= $cart['cart_total_discount'];
		}else{
			$cart['cart_total_discount'] = $cart['both_discounts'];
		}
		if($cart['cart_total_discount'] > 0){
			foreach ($cart['ticktes'] as $tck){		
				$resub_total 		+= 	$tck->total_amount;	
				
				// add event session amounts
				if(is_array($tck->sessions) && count($tck->sessions) >0 ) {
					foreach ($tck->sessions as $cskey => $csvalue)
					{
						$resub_total += $csvalue->fee * $tck->qty;
					}
				}
				// end
																																														
			}
						
			$cart['sub_total'] 	 = number_format($resub_total,2);
			$cart['grand_total'] = $cart['sub_total'];
			
			$cart['grand_total'] = str_replace(",","",$cart['grand_total']) - str_replace(",","",$cart['cart_total_discount']);
		}
		
		//echo "<pre>";print_r($cart); exit;
		return $cart;
	}
			
	// fetch ticket ids from cart ticket array
	function getTicketidsFormArray($cart_tickets)
	{
		$tickets_ids = array();
		
		//echo "<pre>";print_r($cart_tickets); exit;
		
		foreach($cart_tickets as $key=>$value)
		{
			$tickets_ids[] = $value->id;
		}		
		return $tickets_ids;
	}
	
	// fetch ticket ids from cart ticket array
	function getTotalCartQtyFromArray($cart_tickets)
	{
		$total_cart_qty = 0;
		
		foreach($cart_tickets as $key=>$value)
		{
			$total_cart_qty	+= $value->qty;
		}		
		return $total_cart_qty;
	}
	
	// fetch ticket ids from cart ticket array
	function getTotalCartTQtyFromArray($cart_tickets)
	{
		//echo "<pre>"; print_r($cart_tickets); exit;
		$total_event_ticket_cart_qty = 0;
		
		foreach($cart_tickets as $key=>$value)
		{
			if($value->type == 'E'){
				$total_event_ticket_cart_qty += $value->qty;
			}
		}		
		return $total_event_ticket_cart_qty;
	}
	
	// fetch event sessions records from database
	function getSessionsRecords($event_id,$event_sessions)
	{
		$database	=JFactory::getDBO();
		
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id = $event_id AND id in (".implode(",",$event_sessions).") ORDER BY session_date, ordering";
		$database->setQuery($query);
		return $database->loadObjectList();
	}
	
	// fetch ticket records from database
	function getTicketRecords($event_id, $ticket_ids)
	{
		$database	=JFactory::getDBO();
		
		$query = "SELECT * FROM #__registrationpro_payment WHERE regpro_dates_id = $event_id AND id in (".implode(",",$ticket_ids).") ORDER BY type desc";
		$database->setQuery($query);
		return $database->loadObjectList();
	}
	
	// Get events discounts
	function getEventDiscounts($eventids = array())
	{
		$database	=JFactory::getDBO();
		
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id in  (".implode(",",$eventids).") ORDER BY early_discount_date, min_tickets";	
		$database->setQuery($query);
		$event_discounts = $database->loadObjectList();
					
		return $event_discounts;									
	}
			
	#################### Ajax functions  ###################
	/************************ Cart Quantity update ***************/
	function update_cart()
	{		
		$action = JRequest::getVar('action','','POST');
		
		switch($action){
			case "update_cart_qty":
				$this->update_cart_qty();
				break;
			case "remove_cart_item":
				$this->remove_cart_item();
				break;
			case "update_cart":
				$this->apply_coupon();
				break;	
			case "remove_cart_session_item":
				$this->remove_cart_session_item();
				break;						
			default :
				$this->cart();
				break;
		}						
	}
	
	/********************** Cart Calculation Function ***********/
	function calculate_cart($cart)
	{		
		//echo"<pre>";print_r($cart); exit;
		$grand_total 		= 0.00;
		$sub_total 			= 0.00;
		$total_qty			= 0;
		$total_tqty			= 0;
		$total_addqty		= 0;
		$total_tax			= 0.00;
		$additional_total 	= 0.00;	// store additional ticket total amount for discount process
		$nonaddional_ticket_total = 0.00; // store non-additional ticket total amount for discount process
		
		//echo"<pre>";print_r($cart['ticktes']); exit;
		
		if(is_array($cart) && is_array($cart['ticktes'])){
		
			// copy cart tickets array
			$cart_temp['ticktes'] 	= $cart['ticktes'];
		
			// apply event discounts if critera match
			if(count($cart['event_discounts']) > 0){
				$ticketids 					= $this->getTicketidsFormArray($cart['ticktes']);
				$payments 					= $this->getTicketRecords($cart['eventid'], $ticketids); 	//echo"<pre>";print_r($payments); exit;
				$total_event_ticket_cart_qty = $this->getTotalCartTQtyFromArray($cart['ticktes']); 	//echo $total_cart_qty; exit;				
				$cart['ticktes']			= $this->apply_event_discount($cart);//$this->apply_event_discount($payments, $cart['event_discounts'], $total_event_ticket_cart_qty);										
			}
			// end
			//echo '<pre>';print_r($cart);echo '</pre>';
			//echo"<pre>";print_r($cart_temp['ticktes']); echo"<pre>";print_r($cart['ticktes']); exit;						
			foreach($cart['ticktes'] as $tkey => $tvalue)
			{
				foreach($cart_temp['ticktes'] as $tempkey => $tempvalue)
				{
					if($cart_temp['ticktes'][$tempkey]->id == $cart['ticktes'][$tkey]->id){
						// Assign discount on individual tickets to save records in database table
						$cart['ticktes'][$tkey]->discount 		= 0;
						$cart['ticktes'][$tkey]->discount_type 	= "";
						$cart['ticktes'][$tkey]->coupon_code	= "";
						$cart['ticktes'][$tkey]->discount_amount = 0;
						
						$apply_coupon_flag = 0;
						if($cart['discount_coupon_events'] == 0){
							$apply_coupon_flag = 1;
						}else{
							if(in_array($cart['ticktes'][$tkey]->regpro_dates_id,$cart['discount_coupon_events'])){
								$apply_coupon_flag = 1;
							}else{
								$apply_coupon_flag = 0;
								$cart['error_message'] = "";
							}
						}
						
						//if($cart['discount'] > 0 && $cart['ticktes'][$tkey]->type == 'E'){ // discount apply only on non-additional tickets
						if($cart['discount'] > 0 && $cart['ticktes'][$tkey]->type == 'E' && $apply_coupon_flag){ // discount apply only on non-additional tickets
							$temp_final_price = 0;
							$cart['ticktes'][$tkey]->discount 		= $cart['discount'];
							$cart['ticktes'][$tkey]->discount_type 	= $cart['discount_type'];
							$cart['ticktes'][$tkey]->coupon_code	= $cart['coupon_code'];
														
							if($cart['discount_type'] == 'A'){						
								$cart['ticktes'][$tkey]->discount_amount = $cart['discount'];
							}elseif($cart['discount_type'] == 'P'){						
								//$cart['ticktes'][$tkey]->discount_amount = ($cart['ticktes'][$tkey]->total_price * $cart['discount']) / 100;
								$cart['ticktes'][$tkey]->discount_amount = ($cart['ticktes'][$tkey]->product_price * $cart['discount']) / 100;
							}
							
							// appy tax after discount on product price
							$ticket_price_after_discount 			=  $cart['ticktes'][$tkey]->product_price - $cart['ticktes'][$tkey]->discount_amount;					
							$cart['ticktes'][$tkey]->tax_price 		= ($ticket_price_after_discount * $cart['ticktes'][$tkey]->tax)/100;
							$cart['ticktes'][$tkey]->total_price	= number_format($cart['ticktes'][$tkey]->product_price  + $cart['ticktes'][$tkey]->tax_price,2);						
							// end
													
							$temp_final_price = $cart['ticktes'][$tkey]->total_price - ($cart['ticktes'][$tkey]->discount_amount + $cart['ticktes'][$tkey]->event_total_discount_amount);	
							
							if($temp_final_price <= 0)
								$cart['ticktes'][$tkey]->final_price = 	0;		
							else
								$cart['ticktes'][$tkey]->final_price = 	$temp_final_price;
							
							// store total amount of all non additional ticket for discount feature	
							$nonaddional_ticket_total +=  $cart_temp['ticktes'][$tempkey]->qty * $cart['ticktes'][$tkey]->total_price;	
						}else{
							$cart['ticktes'][$tkey]->tax_price 		= ($cart['ticktes'][$tkey]->product_price * $cart['ticktes'][$tkey]->tax)/100;
						}
						// end
							
						$cart['ticktes'][$tkey]->qty 			= $cart_temp['ticktes'][$tempkey]->qty;
						$cart['ticktes'][$tkey]->total_amount 	= $cart['ticktes'][$tkey]->qty * str_replace(",","",$cart['ticktes'][$tkey]->total_price);
						
						if($cart['ticktes'][$tkey]->type=='A'){							
							$additional_total 					+=  $cart['ticktes'][$tkey]->total_amount;	
						}
						
						$grand_total 						   += $cart['ticktes'][$tkey]->total_amount;
						//$cart['ticktes'][$tkey]->total_amount	= $cart['ticktes'][$tkey]->total_amount;							
						$total_tax							   += $cart['ticktes'][$tkey]->tax;
						//$cart['ticktes'][$tkey]->tax_price   	= ($cart['ticktes'][$tkey]->product_price * $cart['ticktes'][$tkey]->tax)/100;
						//$cart['ticktes'][$tkey]->tax_price;
						
						if($cart['ticktes'][$tkey]->type == 'E'){
							$total_tqty	+= $cart['ticktes'][$tkey]->qty;
						}else{
							$total_addqty += $cart['ticktes'][$tkey]->qty;
						}
						
						$sub_total += $cart['ticktes'][$tkey]->qty * $cart['ticktes'][$tkey]->total_price;
					}
				}
				
				// recreate event session array
				if(is_array($tvalue->sessions) && count($tvalue->sessions)){
					foreach($tvalue->sessions as $eskey => $esvalue)
					{
						$esvalue->qty = $cart['ticktes'][$tkey]->qty;
						$event_sessions[] = $esvalue;
						
						$sub_total 		+= $esvalue->qty * $esvalue->fee;
						$grand_total 	+= $esvalue->qty * $esvalue->fee;						
					}					
				}
				// end
			}
			//echo '<pre>';print_r($cart);echo '</pre>';
			//echo"<pre>";print_r($cart['ticktes']); exit;
			
			$total_qty = abs($total_tqty + $total_addqty);
												
			$cart['total_tqty'] 	= $total_tqty;
			$cart['total_addqty'] 	= $total_addqty;
			$cart['total_qty'] 		= $total_qty;
			$cart['total_tax'] 		= $total_tax;
			
			if($sub_total <= 0) 
				$cart['sub_total'] 	= 0;
			else
				$cart['sub_total'] 	= $sub_total;				
				
			if($grand_total <= 0){
				$cart['grand_total']= 0;						
			}else{
				$cart['grand_total']= $grand_total;											
				
				if($cart['total_tqty'] > 0){
					$cart['group_discount']	= 0.00;
					$cart['early_discount']	= 0.00;
					$cart['both_discounts'] = 0.00;
					
					// calculate event group and early discounts					
					$cart = $this->calculate_group_early_discounts($cart);
					
					// calculate grand and sub total after group, early and coupon discounts
					$cart = $this->calculate_grandtotal_after_discounts($cart, $nonaddional_ticket_total); 
				}else{
				   $cart['both_discounts'] 	= 0;
				   $cart['grand_total']		= $cart['grand_total'];					
				}
			}
			//echo "<pre>";print_r($cart);echo '</pre>';die;
			$cart['grand_total'] = $cart['grand_total'] - $cart['AdminDiscount'];
			// check event total is greater then 0 or not
			if($cart['grand_total'] > 0){
				$cart['free_event'] = 0;
			}else{
				$cart['free_event'] = 1;
			}
			// end
		}
						
		// intialize the session
		$session = JFactory::getSession();
		
		// remove previous cart
		//$session->clear('cart'); 						
		
		// create new cart session after updations
		$session->set('cart', $cart);
		
		$newcart = $session->get('cart');		
		//echo"<pre>";print_r($newcart); 	exit;
		
		return $newcart;
	}
	
	/************************ Cart Quantity update ***************/
	function update_cart_qty()
	{
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('newuser');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart = $session->get('cart');
		
		if($cart){
			$ticket_id 	= JRequest::getVar('ticket_id',0,'POST');
			$arr_qty	= JRequest::getVar('qty',0,'POST');			
			$qty		= $arr_qty[$ticket_id];
			
			$other = array();
			$other['ticket_id'] = $ticket_id;
			$other['qty'] 		= $qty;
						
			// check maximum event attendence
			//$cart['error_message'] = registrationproHelper::check_max_attendance($row, array_sum($arr_qty), $this->regpro_config, 4);
			$registrationproHelper = new registrationproHelper;
			$cart['error_message'] = $registrationproHelper->check_max_attendance($row, $other, $cart, $this->regpro_config, 4);
			if($cart['error_message']){
				// nothing
			}else{		
				
				//echo"<pre>"; print_r($cart); exit;
				
				foreach($cart['ticktes'] as $tkey=>$tvalue)
				{
					if($cart['ticktes'][$tkey]->id == $ticket_id){
						$cart['ticktes'][$tkey]->qty = $qty;
					}
				}
				
				$cart['error_message'] = "";
				$cart['discount'] = 0.00;
				
				// calculate and create new cart session after calculations
				$newcart = $this->calculate_cart($cart);
				// end
			}		
		}
				
		$row->allowgroup = $newcart['allowgroup'];
		
		$row->message = "";
		JRequest::setVar( 'row', $row);
		JRequest::setVar( 'ajaxflag', 1);
				
		//echo "<pre>"; print_r($row); exit;
		parent::display();
		//exit;
	}
	
	
	/****************** Removing Session Item from cart ***************** */
	function remove_cart_session_item()
	{
		global $mainframe;
		JRequest::setVar( 'view', 'cart' );
				
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart = $session->get('cart');
		
		if($cart){											
			$event_id 	= JRequest::getVar('event_id',0,'POST');
			$session_id = JRequest::getVar('session_id',0,'POST');
															
			//$cart['error_message'] = "";
			$cart['discount'] = 0.00;			
			
			if(count($cart['ticktes'])>0){											
				foreach($cart['ticktes'] as $tkey => $tvalue)
				{
					if(is_array($tvalue->sessions) && count($tvalue->sessions) > 0 ){
						foreach($tvalue->sessions as $eskey => $esvalue)
						{
							if($esvalue->id == $session_id && $esvalue->event_id == $event_id){
								unset($tvalue->sessions[$eskey]);
							}
						}
					}
				}
															
				// calculate and create new cart session after calculations
				$newcart = $this->calculate_cart($cart);								
				// end										
			}else{				
				// set cart session empty
				$session =JFactory::getSession();		
				$cart 	 = $session->set('cart', "");
				$cart 	 = "";
				// end
			}
		}
			
		JRequest::setVar( 'row', $row);
		JRequest::setVar( 'ajaxflag', 1);
				
		//echo "<pre>"; print_r($row);
		//echo "<pre>"; print_r($cart); exit;
		parent::display();
		//exit;
	}
	
	/****************** Removing Cart Item from cart ***************** */
	function remove_cart_item()
	{
		//echo "remove cart item";
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('newuser');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart = $session->get('cart');
		
		if($cart){											
			$ticket_id = JRequest::getVar('ticket_id',0,'POST');
							
			foreach($cart['ticktes'] as $tkey=>$tvalue)
			{			
				if($cart['ticktes'][$tkey]->id == $ticket_id){
					unset($cart['ticktes'][$tkey]);
				}
			}
			
			//$cart['error_message'] = "";
			$cart['discount'] = 0.00;
			
			if(count($cart['ticktes'])>0){		
				
				// check maximum event attendence
				//$cart['error_message'] = registrationproHelper::check_max_attendance($row, $cart['total_qty'], $this->regpro_config, 4);
				$nothing = array();
				$registrationproHelper = new registrationproHelper;
				$cart['error_message'] = $registrationproHelper->check_max_attendance($row, $nothing, $cart, $this->regpro_config, 4);
				// end
			
				// calculate and create new cart session after calculations
				$newcart = $this->calculate_cart($cart);								
				// end										
			}else{
				// set cart session empty
				$newcart = $session->close('cart');
				$link = JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->did."&Itemid=".$Itemid);								
				// create new cart session after updations
				$session->set('cart', "");
			}
		}
			
		JRequest::setVar( 'row', $row);
		JRequest::setVar( 'ajaxflag', 1);
				
		//echo "<pre>"; print_r($row); exit;
		parent::display();
		//exit;
	}
	
	/****************** Update Cart after coupon code  ****************/	
	function apply_coupon()
	{		
		//echo "remove cart item";
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('newuser');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart 	 = $session->get('cart');		
		if($cart){					
			$coupon_code = JRequest::getVar('coupon_code',0,'POST');
			$adminDiscount = JRequest::getVar('admin_discount',0,'POST');	
				/* First check if coupon code is empty */
				$coupon_data = $model->chk_coupon_code($coupon_code);
				if(!empty($coupon_code))
				{
					// check valid coupon code
					
					//echo 'Coupon data : <pre>';print_r($coupon_data);echo '</pre>';
					if($coupon_data){
						if($coupon_data->max_amount <=  $cart['grand_total']){
							$cart['discount_type'] 	= $coupon_data->discount_type;
							$cart['discount']		= $coupon_data->discount;
							$cart['coupon_code']	= $coupon_data->code;
							$cart['AdminDiscount']	= $adminDiscount;
							$session->set('coupon_discount',$coupon_data->discount);
							// get event ids of coupons
							if($coupon_data->eventids == 0){
								$cart['discount_coupon_events'] = 0;
							}else{
								$cart['discount_coupon_events'] = explode(",",$coupon_data->eventids);
							}
							// end
						}else{
							$cart['discount'] = 0.00;
							$cart['error_message'] = JText::_('EVENT_CART_MSG_COUPON_DISCOUNTED_AMOUNT_LESS').$coupon_data->max_amount;
						}					
					}else{
						$cart['discount'] = 0.00;
						$cart['error_message'] = JText::_('EVENT_CART_MSG_COUPON_INCORRECT');
					}
				}else{
					//$cart['discount_type'] 	= $coupon_data->discount_type;
					$cart['discount']		= $session->get('coupon_discount');
					//$cart['coupon_code']	= $coupon_data->code;
					$cart['AdminDiscount']	= $adminDiscount;
				}
			// end
			//echo "<pre>";print_r($cart);die;
			// calculate and create new cart session after calculations
			$newcart = $this->calculate_cart($cart);
			// End						
		}
				
		$row->allowgroup = $newcart['allowgroup'];
		
		JRequest::setVar( 'row', $row);
		JRequest::setVar( 'ajaxflag', 1);
				
		//echo "<pre>"; print_r($row); exit;
		parent::display();
		//exit;		
		// end				
	}
	
	#################### End Ajax functions  ###################
	
		// Final checkout
	function final_checkout()
	{
		global $mainframe, $Itemid;
				
		JRequest::setVar( 'view', 'cart' );
		JRequest::setVar( 'layout', 'finalcheckout');
		
		//echo "<pre>"; print_r($_POST);  exit;
		
		if($_POST['form'] != "") {
					
			$session =JFactory::getSession();
			
			// get payment method selection
			//$payment_method = strtolower(JRequest::getVar("selPaymentOption",NULL));
			
			// get/set session			
			//$registration_data_session 	 = $session->set('registration_data_session', "");
			/*$session->clear('registration_data_session');		
			$registration_data_session 	 = $session->set('registration_data_session', $_POST);
			$registration_data_session 	 = $session->get('registration_data_session');*/								
			
			$cart 	 					= $session->get('cart');
			$cart['form_data']	 		= $_POST;
			$cart['form_data']['files']	= $_FILES;	
			//$cart['payment_method']	 	= $payment_method; 
			$cart['additional_formfield_fees'] = "";
			$cart['additional_formfield_fees_total'] = "";
						
			// Apply fees fields amount in cart transaction	
			$model		= $this->getModel('newuser');
			$fee_fields = $model->getFeesFields($cart['eventids']);	 // get all fees fields records to calculate the total fees	
					
			
			// first clear previous values from additional_form_field_fees array
			foreach($cart['ticktes'] as $tkey => $tvalue)
			{
				unset($tvalue->additional_form_field_fees);
			}
			// end
			
			$fees_field_values = array();			
			$total_fees_amount = 0;
			$fees_amount = 0;
			$arrfinal_feesvalues = array();
			if(is_array($cart['form_data']['form']) && count($cart['form_data']['form']) > 0 && count($fee_fields) > 0 && is_array($fee_fields)) {	
				foreach($fee_fields as $valFFfields){
							$compareFfields[$valFFfields->id]=$valFFfields;
						}
						$i = 0;	
				foreach($fee_fields as $fkey => $fvalue)
				{								
				
					// Create array for additional fees select values titles
					if($fvalue->values && $fvalue->fees) {												
						$arrfees 		= explode(",",$fvalue->fees);
						$arrfeevalues 	= explode(",",$fvalue->values);
						
						if(count($arrfees) == count($arrfeevalues)) {																																	
							$arrfinal_feesvalues = array_combine($arrfees, $arrfeevalues);
							$arrfinal_feesvalues_NEW = array_combine($arrfeevalues,$arrfees);
						}						
					}
					
					if(array_key_exists($fvalue->name, $cart['form_data']['form']) && is_array($cart['form_data']['users_tickets']['ticket_ids']) && count($cart['form_data']['users_tickets']['ticket_ids']) > 0 ) {																					
						foreach($cart['form_data']['form'][$fvalue->name] as $ffkey => $ffvalue)
						{
								foreach($cart['ticktes'] as $tkey => $tvalue)
								{	
									$ffvalue_Compare = $ffvalue;
									reset($ffvalue_Compare);
									if($tvalue->id==key($ffvalue_Compare)){
									// Check If additional form fees is multicheckbox
									if(is_array($ffvalue) && count($ffvalue > 0) && is_array($ffvalue[0]) && count($ffvalue[0]) > 0){
										$multivalue_flag = 1;										
									}else{
										$multivalue_flag = 0;
									}
																																																
									//if(count($ffvalue) > 1){ // Check If additional form fees is multicheckbox
									if($multivalue_flag == 1){ // Check If additional form fees is multicheckbox
										$tot_fees_amount = 0;
										$tot_fees_lable = array();
										foreach($ffvalue as $ffinkey => $ffinvalue)
										{											
											if($ffinvalue[$tvalue->id]){
												if($fvalue->fees_type == "P"){																															
													$fees_amount		= ($tvalue->total_amount * $ffinvalue[$tvalue->id])/100;
												}else{
													$fees_amount		= $ffinvalue[$tvalue->id];
												}
																																				
												// Get the additional fees select values titles
												if(is_array($arrfinal_feesvalues)) { 																										
													$fees_field_value_name = $arrfinal_feesvalues[$ffinvalue[$tvalue->id]];																									
												}else{
													$fees_field_value_name = $fvalue->name;
												}											
												$cart['form_data']['form'][$fvalue->name][$ffkey][$ffinkey][$tvalue->id] = $fees_field_value_name;																																												
												// end	
																																				
												$tot_fees_amount  = $tot_fees_amount + $fees_amount;
												$tot_fees_lable[] = $fees_field_value_name;																																																
											}											
										}										
										
										if($tot_fees_amount) {										
											$tvalue->additional_form_field_fees[$ffkey][$i]['ticket_id'] 	= $tvalue->id;
											$tvalue->additional_form_field_fees[$ffkey][$i]['event_id'] 	= $tvalue->regpro_dates_id;										
											$tvalue->additional_form_field_fees[$ffkey][$i]['amount'] 		= $tot_fees_amount;
											$tvalue->additional_form_field_fees[$ffkey][$i]['field_name'] 	= implode(", ",$tot_fees_lable); //$fees_field_value_name;
											$tvalue->additional_form_field_fees[$ffkey][$i]['qty'] 			= 1;
											
											// end																				
																				
											$fees_field_values[$i]['ticket_id'] = $tvalue->id;
											$fees_field_values[$i]['event_id'] 	= $tvalue->regpro_dates_id;
											$fees_field_values[$i]['amount'] 	= $tot_fees_amount;
											$fees_field_values[$i]['field_name'] = implode(", ",$tot_fees_lable); //$fees_field_value_name;
											$fees_field_values[$i]['qty'] = 1;
											
											$total_fees_amount = $total_fees_amount + $tot_fees_amount;
										}
										
									}else{ // If additional form fields is select box, radio buttons or checkbox
																			
										/* if($ffvalue[$tvalue->id]){ */
																					
											if($fvalue->fees_type == "P"){																															
												$fees_amount		= ($tvalue->total_amount * $arrfinal_feesvalues_NEW[$ffvalue[$tvalue->id]])/100;
													}else{
														$fees_amount		= $arrfinal_feesvalues_NEW[$ffvalue[$tvalue->id]];
													}
																					
											// Get the additional fees select values titles																								
											if(is_array($arrfinal_feesvalues)) {
														$fees_field_value_name = $ffvalue[$tvalue->id];																									
													}else{
														$fees_field_value_name = $fvalue->name;
													}											
													
													$cart['form_data']['form'][$fvalue->name][$ffkey][$tvalue->id] = $fees_field_value_name;																				
											// end	
																																																					
											// add additional form fields fees array in ticket array to display in cart properly
																						
											$tvalue->additional_form_field_fees[$ffkey][$i]['ticket_id'] 	= $tvalue->id;
											$tvalue->additional_form_field_fees[$ffkey][$i]['event_id'] 	= $tvalue->regpro_dates_id;										
											$tvalue->additional_form_field_fees[$ffkey][$i]['amount'] 		= $fees_amount;
											$tvalue->additional_form_field_fees[$ffkey][$i]['field_name'] 	= $fees_field_value_name; //$fvalue->name;
											$tvalue->additional_form_field_fees[$ffkey][$i]['qty'] 			= 1;
											
											// end																				
										/* 										
											$fees_field_values[$i]['ticket_id'] = $tvalue->id;
											$fees_field_values[$i]['event_id'] 	= $tvalue->regpro_dates_id;
											$fees_field_values[$i]['amount'] 	= $fees_amount;
											$fees_field_values[$i]['field_name'] = $fees_field_value_name; //$fvalue->name;
											$fees_field_values[$i]['qty'] = 1;
											
											$total_fees_amount = $total_fees_amount + $fees_amount; */																																										
										/* } */
									}
									}
								}							
							$i++;													
						}																																			
					}							
				}							
			}
												
			if(count($fees_field_values) > 0) {																					
				$cart['additional_formfield_fees'] 			= $fees_field_values;
				$cart['additional_formfield_fees_total'] 	= $total_fees_amount;																
			}		
			
			$cart['form_data']['finalcheckout_form'] = $cart['form_data']['form']; // orignal form array display at final checkout page.
			$registrationproHelper = new registrationproHelper;
			$cart['form_data']['form'] 	= $registrationproHelper->checkInput_admin('form',$cart,$this->regpro_config);
			
			
			// Manage additional form fields and recalcualte 
					$additional_formfield_fees = array();
					$i = 0; 			
					foreach($cart['ticktes'] as $tkey => $tvalue)
					{
						if(is_array($tvalue->additional_form_field_fees) && count($tvalue->additional_form_field_fees) >0) {
							foreach($tvalue->additional_form_field_fees as $affkey => $affvalue)
							{
								foreach($affvalue as $afffkey => $afffvalue)
								{					
									$additional_formfield_fees[$i] = $afffvalue;																					
									$i++;
								}
							}
						}
					}
					
					if(count($additional_formfield_fees) > 0 && count($additional_formfield_fees) > 0){
						$cart['additional_formfield_fees'] = $additional_formfield_fees;				
					}
					
					// calcualate total form fees amount
					$total_fees_amount = 0;
					if(is_array($cart['additional_formfield_fees']) && count($cart['additional_formfield_fees']) > 0){								
						foreach($cart['additional_formfield_fees'] as $adfkey => $adfvalue)
						{						
							$total_fees_amount = $total_fees_amount + $adfvalue['amount'];
						}												
						$cart['additional_formfield_fees_total'] 	= $total_fees_amount;				
					}
						// End
					//echo "<pre>"; print_r($cart); exit;						
					$cart 	 = $session->set('cart', $cart);							
					// end
			$link 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=final_checkout&Itemid=".$Itemid, false);
			//$link 	= str_replace("&amp;", "&", $link);
			$mainframe->redirect($link,$msg);
			
					
		}
				
		parent::display();				
	}
	
	// Save registration
	function save_registration()
	{
		global $mainframe;	
		$regpro_registrations_emails = new regpro_registrations_emails;
		// get cart session
		$session =JFactory::getSession();
		$cart 	 = $session->get('cart');						
		// end
						
		if(!$cart){
			echo JText::_('EVENT_CART_MSG_EMPTY'); 
			return false;
		}
		
		$model 	= $this->getModel('newuser');		
		$row 	= $model->getEvent();
		$row->addedby = 'admin';
		
		//echo "<pre>"; print_r($row); exit;
		
		$registration = new regpro_registrations($cart, $row);
		
		//echo "<pre>"; print_r($cart); 
		//echo "<pre>"; print_r($row); exit;
		
		// check duplicate email address
		$error_duplicate_email		= $registration->check_duplicate_email();
						
		if($error_duplicate_email){	
			// retrun to cart page if any error found
			JRequest::setVar( 'view', 'cart' );
			JRequest::setVar( 'row', $row);	
			parent::display();
		}else{			
			
			// event id
			$eventid = $row->form_data['did'];
			
			// save users data
			$user_ids 			= $registration->save_user_data();
			$imploded_user_ids 	= implode(",",$user_ids);
			
			//echo "<pre>";print_r($user_ids); exit;
			
			// save users transactions data
			if(count($user_ids > 0) && is_array($user_ids)){			
				
				// save users transactions data
				$registration->save_user_transaction_data($user_ids);
								
				// create and send email to registered users/admin
				$users = $regpro_registrations_emails->getEventForEmailTemplate($eventid, $imploded_user_ids); // get registered user to whom sending emails
				$regpro_registrations_emails->send_registration_email($users); // send notifications
				
				// update user status (approve) according to config settings for free registrations
				//if($this->regpro_config['default_userstatus_free_events']	== 1){
					$registration->updateUserStatus($imploded_user_ids,1);
				//}
				
				// update ticket quantity
				$registrationproHelper = new registrationproHelper;
				$registrationproHelper->updateEventTicketQty($imploded_user_ids);
				
				// redirect to thankyou page
				//$this->thanks($row->did, $imploded_user_ids);	
				//echo "<pre>"; print_r($row); exit;		
				$mainframe->redirect("index.php?option=com_registrationpro&view=users&rdid=".$eventid."&hidemainmenu=1");
			}
		}		
				
		//echo "<pre>"; print_r($regid); exit;					
		//echo "<pre>"; print_r($row); exit;		
	}
		
	// Thankyou page
	function thanks($event_id=0, $registerid=""){		
		global $mainframe, $Itemid;
		
		if(empty($event_id))
			$event_id = JRequest::getVar('did',0,'GET');
			
		if(empty($registerid))
			$registerid = JRequest::getVar('registerid',0,'GET');
		
		$config	=JFactory::getConfig(); // get config settings
		
		$my =JFactory::getUser(); // get user details
		
		$db = JFactory::getDBO();
						
		//check if form has a thank you message
		$query = "SELECT fo.thankyou FROM #__registrationpro_dates e, #__registrationpro_forms fo WHERE fo.id = e.form_id AND fo.published = 1 AND e.id =".$event_id;
		$db->setQuery($query);
		$thankyou = $db->loadResult();
		
		if($thankyou!='') {
			//get all parameters
				$query = "SELECT e.titel, e.dates, e.times, e.enddates, e.endtimes, e.shortdescription, e.datdescription, e.status, l.club, l.url, l.street, l.plz, l.city, l.country, l.locdescription FROM #__registrationpro_dates e, #__registrationpro_locate l WHERE e.locid = l.id AND e.id=".$event_id;
				$db->setQuery($query);				
																		
				$parameters = $db->loadObjectList();		
				$parameters = $parameters[0];									
			//end
	
			$params['sitename'] 	= $config->get('config.sitename');
			$params['eventtitle'] 	= $parameters->titel; //$parameters[0]; 		
			$params['eventstart'] 	= date($this->regpro_config['formatdate'].' '.$this->regpro_config['formattime'],strtotime($parameters->dates.' '.$parameters->times));
			$params['eventend'] 	= date($this->regpro_config['formatdate'].' '.$this->regpro_config['formattime'],strtotime($parameters->enddates.' '.$parameters->endtimes));
			$params['shortdesc'] 	= $parameters->shortdescription; // $parameters[5];
			$params['longdesc'] 	= $parameters->datdescription; // $parameters[6];
			$params['eventstatus'] 	= JText::_('ADMIN_EVENTS_STATUS_'.$parameters->status); // $parameters[7]);
			$params['location'] 	= $parameters->club; //$parameters[8];
			$params['url'] 			= $parameters->url; //$parameters[9];
			$params['street'] 		= $parameters->street; //$parameters[10];
			$params['zip']			= $parameters->plz; //$parameters[11];
			$params['city'] 		= $parameters->city; //$parameters[12];
			$params['country'] 		= $parameters->country; //$parameters[13];
			$params['locdescription'] = $parameters->locdescription; //$parameters[14];
	
			// get the name and email of register users
				if(!empty($registerid))
				{
					$query = "SELECT r.firstname,r.lastname,r.email FROM #__registrationpro_register r,#__registrationpro_dates e WHERE e.id = $event_id AND r.rdid = $event_id AND r.rid in ($registerid)";
					$db->setQuery($query);
					$user = $db->loadObjectList();
					$user = $user[0];				
					$params['fullname'] = $user->firstname."&nbsp;".$user->lastname;
					$params['email'] 	= $user->email;
				}			
			//echo"<pre>";print_r($user);
			//end
			
			//echo "<pre>";print_r($parameters);exit;				
			//echo "<pre>";print_r($params);exit;				
				
			//prepare email
			foreach($params as $tag=>$tag_value){
				$thankyou = str_replace('{'.$tag.'}',$tag_value,$thankyou);
			}
					
			echo '<div class="regpro_outline" id="regpro_outline">';
			echo $thankyou;
			echo '<br><center><input type="button" name="ok" class="button" value="'.JText::_('EVENTS_REGISTER_THANKYOU').'" onclick="document.location=\''.JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&view=event&did=".$event_id).'\'"/></center>';
			echo '</div>';
		}else{
			$mainframe->redirect("index.php?option=com_registrationpro&Itemid=$Itemid&view=event&did=".$event_id, JText::_('EVENTS_REGISTRA_SUCCESS'));
		}
	}
		
}
?>