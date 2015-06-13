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

jimport('joomla.application.component.controller');

class registrationproControllerCart extends registrationproController
{
	var $regpro_config;
	
	function __construct()
	{			
		parent::__construct();
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
			//$regpro_config	= $registrationproAdmin->config();
		$this->regpro_config	= $registrationproAdmin->config();
		
	}
	
	function unauthorize()
	{
		echo JText::_('REGISTRATION_REQUIRED_MESSAGE');
	}
	
	function login_required()
	{
		$user	= JFactory::getUser();
		
		JRequest::setVar( 'view', 'event' );
						
		parent::display();		
	}
		
	/************************ Show Cart ***************/	
	function cart()
	{$registrationproHelper = new registrationproHelper;
		global $mainframe, $option, $Itemid;	
		
		// echo "<pre>"; print_r($_POST); exit;

		//JRequest::checkToken() or jexit('Invalid Token');						
		JRequest::setVar( 'view', 'cart' );

		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
		
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();					
		
		// check validation
		//$row->message .= $registrationproHelper->check_max_attendance($row, 0, $cart, $this->regpro_config, 2);		
		//$row->message .= $registrationproHelper->check_event_registration_enable($row, $this->regpro_config, 2);
		//$row->message .= $registrationproHelper->check_event_registration_date($row, $this->regpro_config, 2);
		
		$step	= JRequest::getVar('step');
		
		// check event tikcets selected by user or not
		$product_id		= JRequest::getVar('product_id',array(),'POST','int');
		$product_id_add	= JRequest::getVar('product_id_add',array(),'POST','int');

		if($step == 1){ // check if request is coming from event details page or not		
			if(count($product_id) > 0 || count($product_id_add) > 0){
					
				// Check group regsitration
				$groupregistration = JRequest::getVar('chkgroupregistration',array(),'POST');
				if($groupregistration && $row->allowgroup == 1){
					$row->allowgroup = 1;	// this should be per-ticket, not general option
				}else{
					$row->allowgroup = 0;
				}
				// end
				
				/*** Create main cart session ***/
				$cart_data 	= $this->manage_tickets($row, $this->regpro_config);
				/*** End ***/
				
				//echo "<pre>"; print_r($cart_data); exit;	
				// check max_attendance
				$totqty = 0;
				$totqty	= $cart_data['total_tqty'];
				$nothing = array();
				$row->message .= $registrationproHelper->check_max_attendance($row, $nothing, $cart_data, $this->regpro_config, 4);																			
				//echo $row->message; //echo "<pre>"; print_r($row); exit;
							
			}else{
				$msg 	= JText::_('EVENTS_SELECT_EVENT_TICKET').'<br/>';			
				$link 	= JRoute::_("index.php?option=com_registrationpro&view=event&Itemid=".$Itemid."&did=".$row->did, false);		
				$mainframe->redirect($link,$msg);		
			}
		}
				
		JRequest::setVar( 'row', $row);
		//echo "<pre>"; print_r($row); exit;			
		
		//parent::display();		
		
		if($step == 1){		
			//$session 		= JFactory::getSession();
			//$session->clear('registration_data_session');
		
			$link 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=cart&Itemid=".$Itemid, false);
			//$link 	= str_replace("&amp;", "&", $link);
			$mainframe->redirect($link,$msg);	
		}else{																
			parent::display();		
		}
	}

	/************************ Create Cart Session ***************/	
	function manage_tickets(&$row){
		global $mainframe;
		$registrationproHelper = new registrationproHelper;
		$database	= JFactory::getDBO();
								
		$tickets = array();
		$tickets_info = array();
		$tickets_id = array();
		
		// Event tickets
		$product_id  = JRequest::getVar('product_id',array(),'POST');		
		$product_qty = JRequest::getVar('product_qty',array(),'POST');				
		
		// Additional tickets
		$product_id_add  = JRequest::getVar('product_id_add',array(),'POST');
		$product_qty_add = JRequest::getVar('product_qty_add',array(),'POST');
		
		//echo "<pre>"; print_r($_POST); exit;
		
		// Event sessions
		$sessions	= array();	
		$event_sessions  = JRequest::getVar('sessions',array(),'POST');		
		if(count($event_sessions) > 0) {
			$sessions = $this->getSessionsRecords($row->did, $event_sessions);
		}		
		
		//echo "<pre>"; print_r($event_sessions); exit;
		
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

		//echo"<pre>";print_r($tickets); exit;
		
		// get tickets information from table 
			$payments = array();
			if($qty >= 1 || $addqty >= 1){
			
				// get the total sum of select tickets to check free/paid event
				$query = "SELECT SUM(total_price) as event_total FROM #__registrationpro_payment WHERE regpro_dates_id = '{$row->did}' AND id in (".implode(",",$tickets_id).")";
				$database->setQuery($query);
				$event_total = $database->loadResult();
				// end
												
				// get the tickets records				
				$payments = $this->getTicketRecords($row->did, $tickets_id);
												
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
		// end					
		
		// Creating array for cart
		$arr_cart = array();				
		if($row->allowgroup) $arr_cart['groupregistrations'][$row->did]= $row->did; // store event id if group registration enable for event
		$arr_cart['allowgroup'] 		= $row->allowgroup;							// this should be per-ticket, not general option				
		$arr_cart['eventid'] 			= $row->did;								// event id	
		$arr_cart['eventids'][$row->did]= $row->did;								// all event ids
		$arr_cart['event_discounts'] 	= $this->getEventDiscounts($arr_cart['eventids']); // event discounts array
		$arr_cart['event_short_desc'] 	= $row->shortdescription;   				// event short description
		$arr_cart['event_detail_desc'] 	= $row->datdescription;						// event detail description
		$arr_cart['event_form_id'] 		= $row->form_id;							// event form id
		$arr_cart['event_payment_method'] = explode(",",$row->payment_method);		// event payment method
		$arr_cart['ticktes'] 			= $payments;
		$arr_cart['sessions']			= $sessions;								// assign sessions to make cart array
		
		// Create the orignal ticktes array
			$grand_total 	= 0.00;
			$total_qty		= 0;
			$total_tqty		= 0;
			$total_addqty	= 0;
			$total_tax		= 0.00;			
			
			$total_tqty		= abs($qty);
			$total_addqty	= abs($addqty);
			$total_qty		= abs($addqty + $qty);
			
			//echo"<pre>";print_r($arr_cart); exit;
			
			// apply event discounts if critera match
			if(count($arr_cart['event_discounts']) > 0){				
				//$payments = $this->apply_event_discount($payments , $row->event_discounts, $total_tqty);				
				$payments = $this->apply_event_discount($arr_cart);				
			}
			// end
			
			$non_additinal_ticket_total = 0;
			
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
			
			// add event session amounts
			/*if(is_array($arr_cart['sessions']) && count($arr_cart['sessions']) >0 ) {
				foreach ($arr_cart['sessions'] as $cskey => $csvalue)
				{
					$grand_total 		+= $csvalue->fee;
				}
			}*/
			// end
			
		// end
	
		//echo "<pre>";print_r($payments); exit;
		//echo "<pre>";print_r($grand_total); exit;		
		$arr_cart['ticktes'] 			= $payments;						// tickets array		
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
				
		//echo "<pre>";print_r($arr_cart);
								
		$arr_cart = $this->calculate_group_early_discounts($arr_cart); // calculate event group and early discounts
		
		//echo "<pre>";print_r($arr_cart); exit;
				
		$arr_cart = $this->calculate_grandtotal_after_discounts($arr_cart); // calculate grand and sub total after group and early discounts				
		
		// check event total is greater then 0 or not
		if($arr_cart['grand_total'] > 0){
			$arr_cart['free_event'] = 0;
		}else{
			$arr_cart['free_event'] = 1;
		}
		// end
						
		//echo "<pre>";print_r($arr_cart); exit;
		
		$session = JFactory::getSession();		
		$cart 	 = $session->get('cart');
						
		//echo "<pre>";print_r($cart); exit;
						
		if(!$cart || !$cart['ticktes']){			
			$session->set('cart', $arr_cart);
			$cart 	 = $session->get('cart');
		}else{
			//echo "<pre>";print_r($row); exit;
			if($row->allowgroup) $cart['groupregistrations'][$row->did] = $row->did; // store event id if group registration enable for event	
			$cart['allowgroup'] 			= $row->allowgroup;						// this should be per-ticket, not general option		
			$cart['eventid'] 				= $row->did;							// event id
			$cart['eventids'][$row->did]	= $row->did;							// all event ids		
			$cart['event_short_desc'] 		= $row->shortdescription;   			// event short description
			$cart['event_detail_desc'] 		= $row->datdescription;					// event detail description
			$cart['event_form_id'] 			= $row->form_id;						// event form id
			$cart['event_discounts'] 		= $this->getEventDiscounts($cart['eventids']);	// event discounts array
			$cart['discount'] 				= 0.00;
			$cart['discount_type'] 			= "";
			$cart['error_message'] 			= "";
			$cart['success_message'] 		= "";	
					
			
			// if session array exists the calculate again, otherwise leave the current session array as it is
			//if(count($sessions) > 0) {				
				// $cart['sessions']			= $this->CheckandaddSessions($sessions); // add event session in existing cart array
			//}
			// end
			
			$session->set('cart', $cart);			
			//echo "<pre>";print_r($cart); exit;
			
			$cart = $this->add_cart_item($arr_cart['ticktes']);
			//$cart = $this->calculate_cart($cart);
		}
		
		//echo "<pre>";print_r($cart); exit;
				
		/*$session->set('cart', $arr_cart);
		$cart 	 = $session->get('cart');*/
		return $cart;		
	}
	
	
	/* Add another event ticket in cart */
	function add_cart_item($arr_cart)
	{
		$registrationproHelper = new registrationproHelper;
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart = $session->get('cart');
		//echo "<pre>";print_r($cart); exit;
		
		//echo "<pre>";print_r($arr_cart);
		//echo "<pre>";print_r($cart); exit;
		
		if($cart){
							
			$arrnew = array();	
			$existkey = array();
			
			foreach($cart['eventids'] as $ekey => $evalue)
			{	
				foreach($arr_cart as $arrkey => $arrvalue)
				{												
					if($evalue == $arrvalue->regpro_dates_id){					
						$flag = 0;
						$newqty = 0;				
						foreach($cart['ticktes'] as $tkey => $tvalue)		
						{
							if($tvalue->regpro_dates_id == $arrvalue->regpro_dates_id){											
								if($tvalue->id == $arrvalue->id){
									$flag 		= 1;									
									$tvalue->qty += $arrvalue->qty;	
								}			
								/*}elseif($arrvalue->id  <= 0){
									$flag = 0;																	
								}else{
									$flag = 0;									
								}
*/							}				
						}
						
						if($flag == 0){
							$existkey[$arrvalue->id] = $arrvalue;
						}
					}
				}
			}
			
			$cart['ticktes'] = array_merge($cart['ticktes'],$existkey);																									
			
			//echo "<pre>";print_r($cart); exit;
			//echo "<pre>";print_r($existkey); exit;
			//echo "<pre>";print_r($cart['ticktes']); exit;
		
			// calculate and create new cart session after calculations
			$newcart = $this->calculate_cart($cart);
			//echo "<pre>";print_r($cart);
			//echo "<pre>";print_r($cart['ticktes']); exit;
			// end
		}
				
		return $newcart;				
	}
	
	// Add event session in existing cart array
	function CheckandaddSessions($cart_tickets)
	{	$registrationproHelper = new registrationproHelper;
		$sessions = array();
		
		if(is_array($cart_tickets)){
			foreach($cart_tickets as $tkey => $tvalue)
			{
				
			}
		}
					
		/*$session = JFactory::getSession();		
		$cart = $session->get('cart');
		
		if(is_array($cart)){
			foreach($cart['eventids'] as $ekey => $evalue)
				if(is_array($cart['sessions']) && count($cart['sessions']) >0 ){
					foreach($cart['sessions'] as $skey => $svalue) 
					{
						if($evalue == $skey){
							foreach($svalue as $sskey => $ssvalue)
							{
								
							}
						}
					}
				}
			}													
		}*/				
	}
			
	// apply event discount
	//function apply_event_discount($tickets, $event_discount, $total_qty=0)
	function apply_event_discount($cart)
	{$registrationproHelper = new registrationproHelper;
		//echo "<pre>";print_r($cart); 
		
		// get applied discount record ids		
		$discount_records = array();
				
		// get current date
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
								}else{
									$total_discount	+= $ddvalue->discount_amount;
								}
								
								$event_discount_ids[] = $ddvalue->id;
							}
						}
					}
				}
									
				$tickets[$tkey]->event_discount_id				= $event_discount_ids;
				$tickets[$tkey]->event_total_discount_amount	= number_format($total_discount,2);									
			}else{
				$tickets[$tkey]->price_without_tax = $tickets[$tkey]->product_price;
			}
		}		
		
		//echo "<pre>";print_r($tickets); exit;
		
		return $tickets;
	}	
	
	// Calculate the group and early discounts
	function calculate_group_early_discounts(&$cart)
	{	$registrationproHelper = new registrationproHelper;
		//echo "<pre>";print_r($cart); exit;
		foreach ($cart['ticktes'] as $tck){	
			$perdiscount = 0.00;
			$fixdiscount = 0.00;
			$ticket_price_after_discount = 0.00;
			foreach($cart['event_discounts'] as $dt){				
				if($tck->event_discount_id && in_array($dt->id,$tck->event_discount_id)){				
					if($tck->type == 'E'){				
						if($dt->discount_name=='G'){
							if($dt->discount_type=='P'){																																
								//$cart['group_discount']		+= 	($tck->total_price * $dt->discount_amount/100) * $tck->qty;
								$cart['group_discount']			+= 	($tck->product_price * $dt->discount_amount/100) * $tck->qty;								
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
						//$tck->total_price				=  number_format($tck->product_price + $tck->tax_price,2); 
						//$tck->total_amount			=  number_format(($tck->product_price  + $tck->tax_price),2) * $tck->qty;				
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
	{	$registrationproHelper = new registrationproHelper;	
		//echo "<pre>"; print_r($cart); exit;
					
		$resub_total = 0.00;
		$regrand_total = 0.00;
		$cart_coupon_discount = 0.00;	
		
		//$cart['error_message'] 	= "";			
		
		$cart['both_discounts'] = $cart['group_discount'] + $cart['early_discount'];
		
		if($cart['discount'] > 0){ // calculate and apply coupon, group an early discounts
		
			/*if($cart['discount_type'] == 'P'){
				$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_PERCENT_DISCOUNT')." ".$cart['discount']."%";
			}else{
				$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_AMOUNT_CORRECT')." ".$this->regpro_config['currency_sign'].$cart['discount'];
			}*/
			
			$cart['error_message'] = "";
			$cart['success_message'] = "";							
			foreach ($cart['ticktes'] as $tck){	
				//if($tck->type == 'E'){
				$apply_coupon_flag = 0;
				if($cart['discount_coupon_events'] == 0){
				
					if($cart['discount_type'] == 'P'){
						//$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_PERCENT_DISCOUNT')." ".$cart['discount']."%";
						$cart['success_message'] 	= JText::_('EVENT_CART_MSG_COUPON_PERCENT_DISCOUNT')." ".$cart['discount']."%";
					}else{
						//$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_AMOUNT_CORRECT')." ".$this->regpro_config['currency_sign'].$cart['discount'];
						$cart['success_message'] 	= JText::_('EVENT_CART_MSG_COUPON_AMOUNT_CORRECT')." ".$this->regpro_config['currency_sign'].$cart['discount'];
					}
				
					$apply_coupon_flag = 1;
				}else{
					if(in_array($tck->regpro_dates_id,$cart['discount_coupon_events'])){
						
						$event_title = $registrationproHelper->getEventName($tck->regpro_dates_id); // get event titel to display in discount message
						
						if($cart['discount_type'] == 'P'){
							//$cart['error_message'] 	= sprintf(JText::_('EVENT_CART_MSG_COUPON_EVENT_PERCENT_DISCOUNT'),$event_title)." ".$cart['discount']."%";
							$cart['success_message']= sprintf(JText::_('EVENT_CART_MSG_COUPON_EVENT_PERCENT_DISCOUNT'),$event_title)." ".$cart['discount']."%";
						}else{
							//$cart['error_message'] 	= sprintf(JText::_('EVENT_CART_MSG_COUPON_EVENT_AMOUNT_CORRECT'),$event_title)." ".$this->regpro_config['currency_sign'].$cart['discount'];
							$cart['success_message'] = sprintf(JText::_('EVENT_CART_MSG_COUPON_EVENT_AMOUNT_CORRECT'),$event_title)." ".$this->regpro_config['currency_sign'].$cart['discount'];
						}
					
						$apply_coupon_flag = 1;																		
					}else{
						$apply_coupon_flag = 0;
						//$cart['error_message'] = "";
					}
				}
				/*if($tck->type == 'E' && $tck->product_price > 0 && $apply_coupon_flag){
					if($cart['discount_type'] == 'A'){							
						$cart_coupon_discount += $cart['discount'] * $tck->qty;
					}elseif($cart['discount_type'] == 'P'){
						$ddd = $cart['discount'] * $tck->qty;
						$cart_coupon_discount += ($tck->product_price * $ddd) / 100;
					}
				}*/
				
				
				// The coupon code only apply if ticket price is greater then 0 after event discount
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
						
						//echo $flagcart_coupon_discount; //exit;
						
						$ticket_price_after_event_discount = $tck->product_price - $tck->event_total_discount_amount;
						
						if($flag_cart_coupon_discount >= ($ticket_price_after_event_discount * $tck->qty)) {
							$cart_coupon_discount += $ticket_price_after_event_discount  * $tck->qty;
							$tck->discount_amount = $ticket_price_after_event_discount;
						}else {		
							$cart_coupon_discount += $flag_cart_coupon_discount;																																		
						}
					}																			
				}
				// end
				
			}
			
			//echo $cart_coupon_discount;
												
			//$cart['cart_total_discount'] = $cart_total_discount;
			$cart['cart_total_discount'] = $cart_coupon_discount + $cart['both_discounts'];
			
			/*if($cart['discount_type'] == 'P'){
				$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_PERCENT_DISCOUNT')." ".$cart['discount']."%";
			}else{
				$cart['error_message'] 	= JText::_('EVENT_CART_MSG_COUPON_AMOUNT_CORRECT')." ".$this->regpro_config['currency_sign'].$cart['discount'];
			}*/
						
			$cart['discount'] 		= $cart['cart_total_discount'];
		}else{			
			$cart['cart_total_discount'] = $cart['both_discounts'];
		}
		
		// If group/early discounts are exists
		//echo "<pre>";print_r($cart); exit;
		if($cart['cart_total_discount'] > 0){
			
			// add all ticket amounts
			foreach ($cart['ticktes'] as $tck){		
				$resub_total 		 += $tck->total_amount;	
				
				// add event session amounts
				if(is_array($tck->sessions) && count($tck->sessions) >0 ) {
					foreach ($tck->sessions as $cskey => $csvalue)
					{
						$resub_total += $csvalue->fee * $tck->qty;
					}
				}
				// end																																														
			}
			// end
			
			// add event session amounts
			/*if(is_array($cart['sessions']) && count($cart['sessions']) >0 ) {
				foreach ($cart['sessions'] as $cskey => $csvalue)
				{
					$resub_total 	+= $csvalue->fee;
				}
			}*/
			// end
						
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
		$registrationproHelper = new registrationproHelper;
		$tickets_ids = array();
		
		//echo "<pre>";print_r($cart_tickets); exit;
		
		foreach($cart_tickets as $key=>$value)
		{
			$tickets_ids[] = $value->id;
		}		
		return $tickets_ids;
	}
	
	// fetch ticket ids from cart ticket array
	function getTotalCartTQtyFromArray($cart_tickets)
	{	
		$registrationproHelper = new registrationproHelper;
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
					
	// fetch ticket records from database
	function getTicketRecords($event_id, $ticket_ids)
	{
		$registrationproHelper = new registrationproHelper;
		$database	= JFactory::getDBO();
		
		$query = "SELECT * FROM #__registrationpro_payment WHERE regpro_dates_id = $event_id AND id in (".implode(",",$ticket_ids).") ORDER BY type desc";
		//$query = "SELECT * FROM #__registrationpro_payment WHERE id in (".implode(",",$ticket_ids).") ORDER BY type desc";
		$database->setQuery($query);
		return $database->loadObjectList();
	}
	
	// fetch event sessions records from database
	function getSessionsRecords($event_id,$event_sessions)
	{
		$registrationproHelper = new registrationproHelper;
		$database	= JFactory::getDBO();
		
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id = $event_id AND id in (".implode(",",$event_sessions).") ORDER BY session_date, ordering";
		$database->setQuery($query);
		return $database->loadObjectList();
	}
	
	// Get event discounts
	function getEventDiscounts($eventids = array()){	
	$registrationproHelper = new registrationproHelper;
		//echo "<pre>"; print_r($eventids); exit;
		$model 				= $this->getModel('events');		
		$event_discounts 	= $model->getEventDiscounts($eventids);
		//echo "<pre>"; print_r($event_discounts); exit;
		return $event_discounts;		
	}
	
	#################### Ajax functions  ###################
	/************************ Cart Quantity update ***************/
	function update_cart()
	{		$registrationproHelper = new registrationproHelper;
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
	{	$registrationproHelper = new registrationproHelper;	
		//echo"<pre>";print_r($cart); exit;
		$grand_total 		= 0.00;
		$sub_total 			= 0.00;
		$total_qty			= 0;
		$total_tqty			= 0;
		$total_addqty		= 0;
		$total_tax			= 0.00;
		$additional_total 	= 0.00;	// store additional ticket total amount for discount process
		$nonaddional_ticket_total = 0.00; // store non-additional ticket total amount for discount process
		
		$event_sessions		= array();
		
		//echo"<pre>";print_r($cart['ticktes']); exit;
		
		if(is_array($cart) && is_array($cart['ticktes'])){
		
			// copy cart tickets array
			$cart_temp['ticktes'] 	= $cart['ticktes'];
		
			// apply event discounts if critera match
			if(count($cart['event_discounts']) > 0){
				$ticketids 					= $this->getTicketidsFormArray($cart['ticktes']);
				//echo"<pre>";print_r($ticketids); exit;
				//$payments 				= $this->getTicketRecords($cart['eventid'], $ticketids);					
				//echo"<pre>";print_r($payments); exit;
				$total_event_ticket_cart_qty = $this->getTotalCartTQtyFromArray($cart['ticktes']); 	//echo $total_cart_qty; exit;				
				//$cart['ticktes']			= $this->apply_event_discount($payments, $cart['event_discounts'], $total_event_ticket_cart_qty);										
				$cart['ticktes']			= $this->apply_event_discount($cart);										
			}
			// end
			
			
			//echo"<pre>";print_r($cart_temp['ticktes']); 
			//echo"<pre>";print_r($cart['ticktes']); exit;						
			foreach($cart['ticktes'] as $tkey => $tvalue)
			{
				foreach($cart_temp['ticktes'] as $tempkey => $tempvalue)
				{
					if($cart_temp['ticktes'][$tempkey]->id == $cart['ticktes'][$tkey]->id && $cart_temp['ticktes'][$tempkey]->regpro_dates_id == $cart['ticktes'][$tkey]->regpro_dates_id){						
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
								$cart['success_message'] = "";
							}
						}
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
							$cart['ticktes'][$tkey]->total_price	= $cart['ticktes'][$tkey]->product_price  + $cart['ticktes'][$tkey]->tax_price;							
							// end
													
							$temp_final_price = $cart['ticktes'][$tkey]->total_price - ($cart['ticktes'][$tkey]->discount_amount + $cart['ticktes'][$tkey]->event_total_discount_amount);	
							
							if($temp_final_price <= 0)
								$cart['ticktes'][$tkey]->final_price = 	0;		
							else
								$cart['ticktes'][$tkey]->final_price = 	$temp_final_price;
							
							// store total amount of all non additional ticket for discount feature	
							$nonaddional_ticket_total +=  $cart_temp['ticktes'][$tempkey]->qty * str_replace(",","",$cart['ticktes'][$tkey]->total_price);	
						}else{
							$cart['ticktes'][$tkey]->tax_price 		= ($cart['ticktes'][$tkey]->product_price * $cart['ticktes'][$tkey]->tax)/100;
							$cart['ticktes'][$tkey]->total_price	= number_format($cart['ticktes'][$tkey]->product_price  + $cart['ticktes'][$tkey]->tax_price,2);
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
						
						$sub_total += $cart['ticktes'][$tkey]->qty * str_replace(",","",$cart['ticktes'][$tkey]->total_price);
					}
				}
				//echo '<pre>'; print_r($cart['ticktes']);die;
				
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
			
			//echo"<pre>";print_r($cart['ticktes']); exit;
												
			// add event session amounts
			$cart['sessions'] = $event_sessions;
			/*if(is_array($cart['sessions']) && count($cart['sessions']) >0 ) {
				foreach ($cart['sessions'] as $cskey => $csvalue)
				{
					$sub_total			+= $csvalue->fee;
					$grand_total 		+= $csvalue->fee;
				}
			}*/
			// end
			
			
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
															
				
				//echo"<pre>";print_r($cart); exit;
				
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
			
		// check event total is greater then 0 or not
			if($cart['grand_total'] > 0){
				$cart['free_event'] = 0;
			}else{
				$cart['free_event'] = 1;
				$cart['grand_total'] = 0;
			}
			// end
		}
		//echo"<pre>";print_r($cart); exit;				
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
	$registrationproHelper = new registrationproHelper;
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('event');		
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
			//$cart['error_message'] = $registrationproHelper->check_max_attendance($row, array_sum($arr_qty), $this->regpro_config, 4);
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
		$registrationproHelper = new registrationproHelper;
		global $mainframe;
		JRequest::setVar( 'view', 'cart' );
				
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart = $session->get('cart');
		
		if($cart){											
			$event_id 	= JRequest::getVar('event_id',0,'POST','int');
			$session_id = JRequest::getVar('session_id',0,'POST','int');
															
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
				$session = JFactory::getSession();		
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
		$registrationproHelper = new registrationproHelper;
		global $mainframe;
		//echo "remove cart item";
		
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart = $session->get('cart');
		
		if($cart){											
			$event_id 	= JRequest::getVar('event_id',0,'POST','int');
			$ticket_id = JRequest::getVar('ticket_id',0,'POST','int');
							
			foreach($cart['ticktes'] as $tkey=>$tvalue)
			{			
				if($cart['ticktes'][$tkey]->id == $ticket_id && $cart['ticktes'][$tkey]->regpro_dates_id == $event_id){
					unset($cart['ticktes'][$tkey]);
				}
			}
			
			//$cart['error_message'] = "";
			$cart['discount'] = 0.00;			
			
			if(count($cart['ticktes'])>0){
			
				// remove if any additional ticket exist without non-additional ticket of event
				foreach($cart['eventids'] as $ekey => $evalue){					
					$flag = 0;
					foreach($cart['ticktes'] as $tkey => $tvalue)
					{
						if($evalue == $tvalue->regpro_dates_id && $tvalue->type == 'E'){
							$flag = 1;
						}
					}
					
					if($flag == 0){
						foreach($cart['ticktes'] as $tkey => $tvalue)
						{
							if($evalue == $tvalue->regpro_dates_id){
								unset($cart['ticktes'][$tkey]);
							}
						}
						unset($cart['eventids'][$ekey]);
						unset($cart['groupregistrations'][$ekey]);
					}					
				}
				// end
				
				//echo "<pre>"; print_r($cart);exit;
				
				if(count($cart['ticktes']) > 0) {																			
					// check maximum event attendence
					$nothing = array();
					$cart['error_message'] = $registrationproHelper->check_max_attendance($row, $nothing, $cart, $this->regpro_config, 4);
					// end			
				
					// calculate and create new cart session after calculations
					$newcart = $this->calculate_cart($cart);								
					// end
				}else{
					// set cart session empty
					$session = JFactory::getSession();		
					$cart 	 = $session->set('cart', "");
					$cart 	 = "";
				}										
			}else{				
				// set cart session empty
				$session = JFactory::getSession();		
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
	
	/****************** Update Cart after coupon code  ****************/	
	function apply_coupon()
	{		
	$registrationproHelper = new registrationproHelper;
		//echo "remove cart item";
		JRequest::setVar( 'view', 'cart' );
		
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();
		
		$session = JFactory::getSession();		
		$cart 	 = $session->get('cart');		
		//echo"<pre>";print_r($cart); 
		
		if($cart){		
			$cart['discount'] 		= 0.00;
			$cart['discount_type'] 	= "";
			$cart['discount_coupon_events'] = 0;
				
			$coupon_code = JRequest::getVar('coupon_code',0,'POST');
			// check valid coupon code
				$coupon_data = $model->chk_coupon_code($coupon_code);
				if($coupon_data){
					if($coupon_data->max_amount <=  $cart['grand_total']){
						$cart['discount_type'] 	= $coupon_data->discount_type;
						$cart['discount']		= $coupon_data->discount;
						$cart['coupon_code']	= $coupon_data->code;
												
						// get event ids of coupons
						if($coupon_data->eventids == 0){
							$cart['discount_coupon_events'] = 0;
						}else{
							$cart['discount_coupon_events'] = explode(",",$coupon_data->eventids);
						}
						// end
					}else{
						$cart['discount'] = 0.00;
						$cart['error_message'] = JText::_('EVENT_CART_MSG_COUPON_DISCOUNTED_AMOUNT_LESS')." ".$this->regpro_config['currency_sign'].$coupon_data->max_amount;
						$cart['success_message']= "";
					}					
				}else{
					$cart['discount'] = 0.00;
					$cart['error_message'] = JText::_('EVENT_CART_MSG_COUPON_INCORRECT');
					$cart['success_message']= "";
				}
			// end
			//echo "<pre>"; print_r($cart); exit;			
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
		$registrationproHelper = new registrationproHelper;
		global $mainframe, $Itemid;
		
		if($_POST['form'] != "") {
			$session = JFactory::getSession();
			$cart = $session->get('cart');
			$cart['form_data']	= $_POST;
			}
			//echo "<pre>";print_r($cart);die;
		// get component config settings
			$registrationproAdmin = new registrationproAdmin;
		$regproConfig	= $registrationproAdmin->config();	
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();
		$registration = new regpro_registrations($cart, $row);
			// check duplicate email address
			$error_duplicate_email		= $registration->check_duplicate_email();
			if($error_duplicate_email){
				JRequest::setVar( 'view', 'cart' );
				JRequest::setVar( 'row', $row);	
				parent::display();
			}else{	
				JRequest::setVar( 'view', 'cart' );
				JRequest::setVar( 'layout', 'finalcheckout');
				
				$model 	= $this->getModel('event');		
				$row 	= $model->getEvent();
				
				JRequest::setVar( 'row', $row);
				
				//echo "<pre>"; print_r($_POST);  exit;
				
				if($_POST['form'] != "") {
							
					$session = JFactory::getSession();
					
									
					$cart 	 					= $session->get('cart');
					$cart['form_data']	 		= $_POST;
					$cart['form_data']['files']	= $_FILES;	
					//$cart['payment_method']	 	= $payment_method; 
					$cart['additional_formfield_fees'] = "";
					$cart['additional_formfield_fees_total'] = "";
								
					// Apply fees fields amount in cart transaction	
					$model		= $this->getModel('event');
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
						//echo "<pre>";print_r($fee_fields);die;
						foreach($fee_fields as $valFFfields){
							$compareFfields[$valFFfields->id]=$valFFfields;
						}
						$i = 0;	
						//echo "<pre>";print_r($compareFfields);die;
						foreach($compareFfields as $fkey => $fvalue)
						{								
							
							// Create array for additional fees select values titles
							if($fvalue->values && $fvalue->fees) {	
								if($fvalue->inputtype != 'checkbox'){
									$arrfees		= explode(",",$fvalue->fees);
									$arrfeevalues 	= explode(",",$fvalue->values);
									//echo "<pre>";print_r($arrfeevalues);
								}else{
									//echo "<br/> Fees : ". $fvalue->fees;
									//echo "<br/> values : ". $fvalue->values;die;
									unset($arrfees);
									unset($arrfeevalues);
									$arrfees[]		= $fvalue->fees;
									$arrfeevalues[] 	= $fvalue->values;
									
								}
								
								if(count($arrfees) == count($arrfees)) {																																	
									$arrfinal_feesvalues = array_combine($arrfees, $arrfeevalues);
									$arrfinal_feesvalues_NEW = array_combine($arrfeevalues,$arrfees);
								}						
							}
							
							//echo '<pre>'; print_r($arrfees);//die;
							//echo '<pre>'; print_r($arrfinal_feesvalues_NEW);
							if(array_key_exists($fvalue->name, $cart['form_data']['form']) && is_array($cart['form_data']['users_tickets']['ticket_ids']) && count($cart['form_data']['users_tickets']['ticket_ids']) > 0 ) {		
							
								
								//echo '<pre>'; print_r($cart['form_data']['form'][$fvalue->name]);die;
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
													if($ffinvalue[$tvalue->id] > 0){
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
												
												if($tot_fees_amount > 0) {										
													$tvalue->additional_form_field_fees[$ffkey][$i]['ticket_id'] 	= $tvalue->id;
													$tvalue->additional_form_field_fees[$ffkey][$i]['event_id'] 	= $tvalue->regpro_dates_id;										
													$tvalue->additional_form_field_fees[$ffkey][$i]['amount'] 		= $tot_fees_amount;
													$tvalue->additional_form_field_fees[$ffkey][$i]['field_name'] 	= implode(", ",$tot_fees_lable); //$fees_field_value_name;
													$tvalue->additional_form_field_fees[$ffkey][$i]['qty'] 			= 1;
													
													// end																				
																						
													/*$fees_field_values[$i]['ticket_id'] = $tvalue->id;
													$fees_field_values[$i]['event_id'] 	= $tvalue->regpro_dates_id;
													$fees_field_values[$i]['amount'] 	= $tot_fees_amount;
													$fees_field_values[$i]['field_name'] = implode(", ",$tot_fees_lable);
													$fees_field_values[$i]['qty'] = 1;
													
													$total_fees_amount = $total_fees_amount + $tot_fees_amount;*/
												}
												
											}else{ // If additional form fields is select box, radio buttons or checkbox
																					
												/* if($ffvalue[$tvalue->id] > 0){ */
																//echo "<pre>";print_r($arrfinal_feesvalues_NEW);
													if($fvalue->fees_type == "P"){																															
														$fees_amount		= ($tvalue->total_amount * $arrfinal_feesvalues_NEW[$ffvalue[$tvalue->id]])/100;
													}else{
														
														//echo "<br/><pre>";print_r($ffvalue);
														//echo "<br/> i = ".$ffvalue[$tvalue->id];
														//echo "<br/><pre>";print_r($arrfinal_feesvalues_NEW);
														if($fvalue->inputtype != 'checkbox'){
														$fees_amount		= $arrfinal_feesvalues_NEW[$ffvalue[$tvalue->id]];
														}else{
															$fees_amount		= $ffvalue[$tvalue->id];
														}
														
													}
													//echo "<br/>id = ".$arrfinal_feesvalues_NEW[$ffvalue[$tvalue->id]];
													//echo "amount :  ".$fees_amount;								
													// Get the additional fees select values titles															

													 //echo '<pre>'; print_r($ffvalue);die;	
											 
													if(is_array($arrfinal_feesvalues)) {
														if($fvalue->inputtype != "checkbox"){
															$fees_field_value_name = $ffvalue[$tvalue->id];
														}else{
															$fees_field_value_name = $fvalue->name;
														}
													}else{
														$fees_field_value_name = $fvalue->name;
													}											
													//echo "field name : ".$fees_field_value_name;
													$cart['form_data']['form'][$fvalue->name][$ffkey][$tvalue->id] = $fees_field_value_name;			
													// end	
																																																							
													// add additional form fields fees array in ticket array to display in cart properly																					
													$tvalue->additional_form_field_fees[$ffkey][$i]['ticket_id'] 	= $tvalue->id;
													$tvalue->additional_form_field_fees[$ffkey][$i]['event_id'] 	= $tvalue->regpro_dates_id;										
													$tvalue->additional_form_field_fees[$ffkey][$i]['amount'] 		= $fees_amount;
													$tvalue->additional_form_field_fees[$ffkey][$i]['field_name'] 	= $fees_field_value_name; //$fvalue->name;
													$tvalue->additional_form_field_fees[$ffkey][$i]['qty'] 			= 1;											
													// end																				
																						
													/*$fees_field_values[$i]['ticket_id'] = $tvalue->id;
													$fees_field_values[$i]['event_id'] 	= $tvalue->regpro_dates_id;
													$fees_field_values[$i]['amount'] 	= $fees_amount;
													$fees_field_values[$i]['field_name'] = $fees_field_value_name;
													$fees_field_values[$i]['qty'] = 1;
													
													$total_fees_amount = $total_fees_amount + $fees_amount;*/																																										
												/* } */
											}
										}//also delete	
											
											
											
											
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
					
					//echo "<pre>"; print_r($cart); exit;		
								
					// Manage registration form			
					if($cart['added_by'] == "A"){
						$cart['form_data']['finalcheckout_form'] = $cart['form_data']['form']; // orignal form array display at final checkout page.
						$cart['form_data']['form']	= $registrationproHelper->checkInput_admin('form',$cart,$this->regpro_config);
					}else{
						$cart['form_data']['finalcheckout_form'] = $cart['form_data']['form']; // orignal form array display at final checkout page.
						$cart['form_data']['form'] 	= $registrationproHelper->checkInput('form',$cart,$this->regpro_config);
					}	
					// end
					
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
					
					//echo "<pre>"; print_r($additional_formfield_fees);			
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
					
					/* ADDED BY SUSHIL  FOR showing payment options when additional fees is added to the total amount*/
					
					if($cart['grand_total'] == 0 && $cart['additional_formfield_fees_total'] > 0)
					{
						$cart['free_event'] = 0;
					}					
					$cart 	 = $session->set('cart', $cart);							
					// end
														
					$link 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=final_checkout&Itemid=".$Itemid, false);
					//$link 	= str_replace("&amp;", "&", $link);
					//$mainframe->redirect($link,$msg);						
				}
				
				parent::display();	
		}		
	}		
				
	// Save registration
	function save_registration()
	{
		$registrationproHelper = new registrationproHelper;
		global $mainframe, $Itemid;
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig	= $registrationproAdmin->config();	
		
		//echo "<pre>"; print_r($_POST); echo "<pre>"; print_r($_FILES);  exit;
		
		// get cart session
		$session = JFactory::getSession();
		$cart 	 = $session->get('cart');
		$cart 	 = $session->get('cart');
		
		//echo "<pre>"; print_r($cart); exit;
		
		if(!$cart){
			echo JText::_('EVENT_CART_MSG_EMPTY'); 
			return false;
		}	
		// end
						
		$model	= $this->getModel('event');		
		$row	= $model->getEvent();
		$row->addedby = 'user';												
						
		$registration = new regpro_registrations($cart, $row);
		
		//echo "<pre>"; print_r($row); exit;	
		
		// check duplicate email address
		$error_duplicate_email		= $registration->check_duplicate_email();
		
		// check payment method selected by user or not				
		if(!$error_duplicate_email){
			$error_payment_method 	= $registration->checkPaymentMethodSelection();
		}
		if($error_payment_method){
			JRequest::setVar( 'view', 'cart' );
			JRequest::setVar( 'layout', 'finalcheckout');
			JRequest::setVar( 'row', $row);	
			parent::display();
			
				
		}
		/*else if($error_duplicate_email){
			// retrun to cart page if any error found
			JRequest::setVar( 'view', 'cart' );
			JRequest::setVar( 'row', $row);	
			parent::display();
		}*/else{		
			
			// save users data
			$user_ids 			= $registration->save_user_data();
			$imploded_user_ids 	= implode(",",$user_ids);
			
			//echo "<pre>";print_r($user_ids); exit;
			
			// save users transactions data
			if(count($user_ids > 0) && is_array($user_ids)){			
				
				// save users transactions data
				$trans_data 		= $registration->save_user_transaction_data($user_ids);
				//echo "<pre>"; print_r($trans_data); exit;
				$imploded_trans_ids = implode(",",$trans_data['id']);//echo $imploded_trans_ids;die;		
				$custom_key			= $trans_data['custom_key'];
					
				// set cart session empty		
				$cart 	 = $session->set('cart', "");				
				$cart 	 = "";
				//$registration_data_session 	 = $session->set('registration_data_session', "");
				//$registration_data_session = "";
				
				//echo "<pre>";print_r($cart); exit;
				
				// redirect to payment methods if amount is greater the 0
				if(floatval($row->total_amount) > 0 && $row->addedby == 'user'){
					$plugin_handler = new regProPlugins;					
					$res = $plugin_handler->performCheckout($row->total_amount, $imploded_user_ids, $imploded_trans_ids, $custom_key, $row); 
				}else{									
					// update user status (approve) according to config settings for free registrations
					if($this->regpro_config['default_userstatus_free_events']	== 1){
						$registration->updateUserStatus($imploded_user_ids,1);
						$registration->updatePaymentStatus($imploded_user_ids,'Completed');						
					}
					
					//$users = regpro_registrations_emails::getEventForEmailTemplate($row->did, $imploded_user_ids); // get registered users to whom sending emails
					//regpro_registrations_emails::send_registration_email($users); // send notifications
																																			
					// redirect to thankyou page
					//$this->thanks($row->did, $imploded_user_ids);
					
					// create and send email to registered users/admin
					foreach($row->eventids as $ekey => $evalue)
					{
						// create mail to send it to registered users	
						$users = regpro_registrations_emails::getEventForEmailTemplate($evalue, $imploded_user_ids); // get registered user to whom sending emails
						//echo "<pre>"; print_r($users); exit;
						regpro_registrations_emails::send_registration_email($users);
						// end
					}
					
					if(count($row->eventids) > 1){ // redirect to the thankspage if user registered with more then one event
						if(trim($regproConfig['thankspagelink']) != ""){
							$link = trim($regproConfig['thankspagelink']);
							$mainframe->redirect($link);
						}else{ // if thanks page link is not set
							$msg = JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
							$link = JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=$Itemid", false);
							//$link 	= str_replace("&amp;", "&", $link);
							$mainframe->redirect($link,$msg);
						}
					}else{ // if user registered with one event only
						//redirect to thankyou
						foreach($row->eventids as $ekey => $evalue)
						{
							$msg = JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
							$link = JRoute::_("index.php?option=com_registrationpro&controller=cart&task=thanks&did=".$evalue."&registerid=".$imploded_user_ids."&Itemid=$Itemid", false);
							//$link = str_replace("&amp;", "&", $link);
							$mainframe->redirect($link,$msg);
						}
						//end
					}	
															
				}							
			}
		}		
								
		//echo "<pre>"; print_r($regid); exit;					
		//echo "<pre>"; print_r($row); exit;		
	}
	
	// Payment plugins process
	function payments_process()
	{	$registrationproHelper = new registrationproHelper;
		global $mainframe;
		$registrationproAdmin = new registrationproAdmin;
		// get component config settings
		$regproConfig	= $registrationproAdmin->config();
				
		// get class instance
		$plugin_handler = new regProPlugins;											
								
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('regpro_gateways');
			
		$param = JRequest::get('REQUEST');
		$param['regproConfig'] = $regproConfig;
		
		$result = $dispatcher->trigger('onReceivePayment',array(& $param));
					
		if(is_array($result))
		{
			foreach ($result AS $res)
			{
				if(is_array($res))
				{
					if(@isset($res['msg']))
					$mainframe->enqueueMessage($res['msg']);						

					//echo"<pre>"; print_r($res); exit;
					if(!$res['sid'] || !$res['gateway'] || !$res['gateway_id'] || !$res['user_id'] || !array_key_exists('price', $res) || !$res['pay'])
					{
						$link = 'index.php?option=com_registrationpro&view=events&Itemid='.JRequest::getInt('Itemid');	
						$msg = JText::_('Not All Information is Passed to save registration');							
					}
					else
					{
						if($res['pay'] == 'success'){																				
							$plugin_handler->goToSuccessURL ($res);
							return;									
						}elseif($res['pay'] == 'fail'){														
							$plugin_handler->goToFailedURL ($res['sid']);
							return;
						}elseif($res['pay'] == 'refund'){																			
							$plugin_handler->goToRefundURL ($res['sid']);							
							return;
						}else{						
							$plugin_handler->goToPendingURL ($res['sid']);
							return;																								
						}																											
					}
					if($link) $mainframe->redirect($link, $msg);
					break;
				}					
			}
		}else{
			$mainframe->enqueueMessage('No value found');
			$mainframe->redirect('index.php?option=com_registrationpro&view=events&Itemid='.JRequest::getInt('Itemid'));
		}
	}
	
	function ccpayment()
	{	$registrationproHelper = new registrationproHelper;
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('regpro_gateways');
		
		$param = JRequest::get('REQUEST');
		
		//echo "<pre>"; print_r($param); exit;
				
		$param['controller'] = "cart";
		$param['task'] 		 = "payments_process";
		
		$result = $dispatcher->trigger('onSendPayment',array(& $param));			
	}
	
	// Thankyou page
	function thanks($event_id=0, $registerid=""){		
		global $mainframe, $Itemid;
			$registrationproHelper = new registrationproHelper;			
		if(empty($event_id))
			$event_id = JRequest::getVar('did',0,'','int');
		
			
		if(empty($registerid))
			$registerid = JRequest::getVar('registerid',0,'','int');
		
		$config	= JFactory::getConfig(); // get config settings
		
		$my = JFactory::getUser(); // get user details
		
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
			$params['eventstart'] 	= $registrationproHelper->getFormatdate($this->regpro_config['formatdate'].' '.$this->regpro_config['formattime'], $parameters->dates.' '.$parameters->times);
			$params['eventend'] 	= $registrationproHelper->getFormatdate($this->regpro_config['formatdate'].' '.$this->regpro_config['formattime'], $parameters->enddates.' '.$parameters->endtimes);
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
			// set cart session empty
			$session = JFactory::getSession();		
			$cart 	 = $session->set('cart', "");
			$mainframe->redirect("index.php?option=com_registrationpro&Itemid=$Itemid&view=event&did=".$event_id, JText::_('EVENTS_REGISTRA_SUCCESS'));
		}
		
		/*// set cart session empty
		$session = JFactory::getSession();		
		$cart 	 = $session->set('cart', "");*/
	}
		
}
?>