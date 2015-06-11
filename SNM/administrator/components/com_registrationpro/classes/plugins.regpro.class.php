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

class regProPlugins {

	var $allowed_types = array('payment');
	var $plugin_instances = array();
	var $plugins = array();
	var $payment_plugins = array();
	var $encoding_plugins = array();
	var $req_methods = array ('getBEData');
	var $plugins_loaded = 0;
	var $default_payment = -1;
	
	//global JPATH_SITE;
	//JPATH_SITE = JPATH_SITE;
	
	function regProPlugins() {
		$this->loadPlugins();
	}
				
	/*
	 * Includes plugins code.
	 */
	function loadPlugins () {
		if ($this->plugins_loaded == 1) return;	
		
		$database =JFactory::getDBO();
		
		$this->payment_plugins = JPluginHelper::getPlugin('regpro_gateways');
		$this->plugins_loaded = 1;
		return;
	}
			
	function goToHTTPS($sid) {
		global $configs, $Itemid;
		
		$database =JFactory::getDBO();
		
		$my =JFactory::getUser();
		
		if (getenv('HTTPS') != 'on'){
			//global $mosConfig_live_site;
			$url = str_replace("http://", "https://", REGPRO_SITE_URL);
			///$mosConfig_live_site = $url;

			$reg_id = $sid;
	
			$explod_regid = explode(",",$reg_id);
				
			$database->setQuery("SELECT * FROM #__registrationpro_transactions rt, #__registrationpro_register rr WHERE rr.rid = '$reg_id' AND rt.reg_id = ".$explod_regid[0]." AND rt.p_id <> '' ");				
			$reg = $database->loadObjectList();
			//print_R($reg);
			//print_r($_REQUEST);die;
			$sid2 = $reg[0]->rdid;
			
			$po = strtolower(JRequest::getVar("selPaymentOption",NULL));

			$result = '
				<form name="dataform" method="post" action="'.$url.'/index.php?option=com_registrationpro&controller=cart&task=save_registration&Itemid='.$Itemid.'">
					<input type="hidden" name="userid" value="'.$my->id.'" />
					<input type="hidden" name="sid" value="'.$sid.'" />				
					<input type="hidden" name="did" value="'.$sid2.'" />				
					<input type="hidden" name="step" value="3" />				
					<input type="hidden" name="req" value="'.base64_encode(serialize($_REQUEST)).'" />
					<input type="hidden" name="selPaymentOption" value="'.$po.'" /> 			
				</form>
			';
			//die ($result);
			$result .= "<script language='javascript'>
							document.dataform.submit();
						</script>";

			return $result;
			
		} else {
			return;
		}
	}
	
	/*
	 * Generates fe html-output, so fe-user may make payment via chooses system.
	 */
	function FEPluginHandler ($pay_type,  $items, $tax, $redir = 0, $profile, $sid) {
		global $digistor_plugins, $configs;
		
		$my 			=JFactory::getUser();
		$database 		=JFactory::getDBO();		
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();;	
		
		$result = array();
		$total = $tax['taxed'];
		if (count($this->payment_plugins) < 1 ) return -1;
		$plugin_exists = 0;
		
		$configs = array();
		$configs['currency'] = $regproConfig['currency_value'];
		
		if (!isset($this->payment_plugins[$pay_type])) {	//if selected payment method is not available
			if (is_object($this->default_payment)) {		//try default payment gateway
				$plugin = $this->default_payment;	
			} elseif (!is_object($this->default_payment)) {//no default available
				foreach ($this->payment_plugins as $plug) {//select first gateway available
					$plugin = $plug;
					break;	
				}	
			} else {
				die (JText::_('CANTPROCESSPAYMENT'));	
			}
		} else { //all os ok - use normal gateway
			$plugin = $this->payment_plugins[$pay_type];
		}
		
		if ($plugin->reqhttps != 0 && getenv('HTTPS') != 'on') {//plugin requires https connection to perform checkout
			return $this->goToHTTPS ($sid);	
			
		}
		//now we're ready to call plugin	
		$result = $this->plugin_instances[$plugin->name]->getFEData($items, $tax, $redir, $profile, $plugin, $sid);
	 	
		if (strlen(trim($result)) < 1) {
				echo "<script language='javascript'>alert ('".JText::_('INTERNAL_ERROR')."'); self.history.go(-1);</script>";
				return;
		}
		
		return $result;		
		
	}
			
	/*
	 * After user returns from payment system we should receive responce on if transaction 
	 * was successfull, tell result to user and store info into db. Has to have
	 * real ip.
	 */
	function payment_notify ($plugin) {
		//	$result = array();
		$database =JFactory::getDBO();
	   
		$result = $this->plugin_instances[$plugin->classname]->notify($plugin);
		return $result;
	
	}		
	
	function payment_return ($plugin) {
		$database =JFactory::getDBO();
		
		session_start ();
		$_SESSION['in_trans'] = 1;
		
		$result = $this->plugin_instances[$plugin->classname]->return1($plugin);
		return $result;
	
	}
	
	function payment_fail ($plugin) {
		session_start ();
		$_SESSION['in_trans'] = 1;
			
		$result = $this->plugin_instances[$plugin->classname]->return2($plugin);
		return $result;
	
	}		
	
	function addOrder ($items, $data) {
		global $regpro_mail;
		$database 		=JFactory::getDBO();
		$registrationproAdmin = new registrationproAdmin;
		$regpro2Config	= $registrationproAdmin->config();
		//$regpro2Config	= registrationproAdmin::config(); 	// get component config settings
		$config			=JFactory::getConfig(); 			// get config settings
		
		$x = fopen (JPATH_SITE."/administrator/components/com_registrationpro/lasttransres.txt", "w");
		
		fwrite($x, "Payment : ".$data['gateway']."\n");
		fwrite($x, "User ids : ".$data['sid']."\n");
		fwrite($x, "Accesskey : ".$data['user_id']."\n");
				
		$user_ids	= $data['sid'];
		
		// update transaction table 
			$query = "UPDATE `#__registrationpro_transactions` SET
					`payment_status`	= 'Completed', 
					`txn_id` 			= '".$data['transid']."'
					 WHERE `reg_id` in (".$user_ids.") AND `accesskey` ='".$data['user_id']."'";
			fwrite($x, $query."\n");
			$database->setQuery($query);
			$database->query();
		// end
										
		//Update register table set status to accepted
			$query = "UPDATE #__registrationpro_register SET status=1, active=1 WHERE rid in ($user_ids)";
			fwrite($x, $query);		 	 		 		 
			$database->setQuery($query);
			$database->query();
		// end
		
		
		// send notification and return to thanks page
		$this->goToSuccessURL ($user_ids);
		// end
		
		//get confirmation email				
			$query = "SELECT r.rid AS uid, e.id AS eid, r.firstname, r.lastname, r.email, e.titel, l.club, l.url, l.city, l.plz, l.street, l.country, l.locdescription, e.status, e.dates, e.times, e.enddates, e.endtimes, e.shortdescription, e.datdescription FROM"
				."\n #__registrationpro_dates e, #__registrationpro_locate l, #__registrationpro_register r"
				."\nWHERE e.id=r.rdid AND r.status=1 AND e.notifydate <= '".date('Y-m-d')."' AND l.id=e.locid AND e.published=1 AND r.rid in ($user_ids)";	
			
			$database->setQuery($query);
			$users = $database->loadObjectList();
			$pms = array();				
	
			$email_sent = array();
			foreach($users as $key=>$value){
				$name = $users[$key]->firstname." ".$users[$key]->lastname;
				$email_subject = JText::_('YOUR_TRANSACTION_CONFIRMED_BY_ADMIN_SUBJECT'); //"Your transaction has been confirmed by admin.";
				$email_body = sprintf(JText::_('YOUR_TRANSACTION_CONFIRMED_BY_ADMIN_BODY'),$name);
				//"Dear $name, <br/><br/> Your transaction has been confirmed by admin. So your registration  status has been changed to accepted.<br/><br/>Thanks.";				
				if (!in_array($users[$key]->email, $email_sent)) {
					$regpro_mail->sendMail($config->get('config.mailfrom'), $config->get('config.fromname'), $users[$key]->email, $email_subject, $email_body, 1);	
				// end
	
					// mail to admin
					if($regpro2Config['register_notify']){
						
						$email_subject 	= sprintf(JText::_('YOUR_TRANSACTION_CONFIRMED_TO_ADMIN_SUBJECT'),$name);
						$email_body 	= sprintf(JText::_('YOUR_TRANSACTION_CONFIRMED_TO_ADMIN_BODY'),$name); 
						
						$tt = $regpro_mail->sendMail($config->get('config.mailfrom'), $config->get('config.fromname'), $regpro2Config['register_notify'], $email_subject, $email_body, 1);
					}
				}
				$email_sent[] = $users[$key]->email;
				// end
			}
		// end confirmation email
												
		fclose($x);
	} // end validate_ipn() if

	
	// recursive_remove_directory( directory to delete, empty )
	// expects path to directory and optional TRUE / FALSE to empty
	// of course PHP has to have the rights to delete the directory
	// you specify and all files and folders inside the directory
	
	// to use this function to totally remove a directory, write:
	// recursive_remove_directory('path/to/directory/to/delete');
	
	// to use this function to empty a directory, write:
	// recursive_remove_directory('path/to/full_directory',TRUE);		
	
	function recursive_remove_directory($directory, $empty=FALSE)
	{
	    // if the path has a slash at the end we remove it here
	    if(substr($directory,-1) == '/')
	    {
	        $directory = substr($directory,0,-1);
	    }
	 
	    // if the path is not valid or is not a directory ...
	    if(!file_exists($directory) || !is_dir($directory))
	    {
	        // ... we return false and exit the function
	        return FALSE;
	 
	    // ... if the path is not readable
	    }elseif(!is_readable($directory))
	    {
	        // ... we return false and exit the function
	        return FALSE;
	 
	    // ... else if the path is readable
	    }else{
	 
	        // we open the directory
	        $handle = opendir($directory);
	 
	        // and scan through the items inside
	        while (FALSE !== ($item = readdir($handle)))
	        {
	            // if the filepointer is not the current directory
	            // or the parent directory
	            if($item != '.' && $item != '..')
	            {
	                // we build the new path to delete
	                $path = $directory.'/'.$item;
	 
	                // if the new path is a directory
	                if(is_dir($path)) 
	                {
	                    // we call this function with the new path
	                    regProPlugins::recursive_remove_directory($path);
	 
	                // if the new path is a file
	                }else{
	                    // we remove the file
	                    unlink($path);
	                }
	            }
	        }
	        // close the directory
	        closedir($handle);
	 
	        // if the option to empty is not set to true
	        if($empty == FALSE)
	        {
	            // try to delete the now empty directory
	            if(!rmdir($directory))
	            {
	                // return false if not possible
	                return FALSE;
	            }
	        }
	        // return success
	        return TRUE;
	    }
	}
	
								
	function interceptPaymentResponse($task) {
		$flag = 0;
		//$task = mosGetParam($_REQUEST, "task", "");
		if ( count ($this->payment_plugins  ) >0 ){
			foreach ($this->payment_plugins as $plugin) {
	    		if ($task == $plugin->classname."_notify") {
			        $content = $this->payment_notify($plugin);
			        echo ($content);
			        $flag = 1;
			        break;
	
	    		}
			}
	
			foreach ($this->payment_plugins as $plugin) {
			    if ($task == $plugin->classname."_return") {
			        $content = $this->payment_return($plugin);
			        echo ($content);
			        $flag = 1;
			        break;
			
			    }
			}
	
			foreach ($this->payment_plugins as $plugin) {
			    if ($task == $plugin->classname."_fail") {
			        $content = $this->payment_fail($plugin);
			        echo ($content);
			        $flag = 1;
			        break;			
			    }
			}
		}
		return $flag;	
	}
	
	function fillMy ($id) {	
		
		$my 		=JFactory::getUser();
		$database 	=JFactory::getDBO();
		
		$query = "SELECT id, name, email, block, sendEmail, registerDate, lastvisitDate, activation, params"
						. "\n FROM #__users"
						. "\n WHERE id = ". intval( $id )
						;
		$database->setQuery( $query );
		$database->loadObject( $my );
		
	}
			
	function goToSuccessURL ($data, $msg = '') {
		global $configs, $mainframe, $Itemid;
		
		$my 			=JFactory::getUser();
		$database 		=JFactory::getDBO();
		$config			=JFactory::getConfig(); 
		// get config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro2Config	= $registrationproAdmin->config();
		//$regpro2Config	= registrationproAdmin::config(); 	// get registration pro settings				
		
		$x = fopen (JPATH_SITE."/administrator/components/com_registrationpro/lasttransres.txt", "w");
		
		fwrite($x, "Payment : ".$data['gateway']."\n");
		fwrite($x, "User ids : ".$data['sid']."\n");
		fwrite($x, "Accesskey : ".$data['user_id']."\n");
				
		$user_ids	= $data['sid'];
		
		// update transaction table 
			$query = "UPDATE `#__registrationpro_transactions` SET
					`payment_status`	= 'Completed', 
					`txn_id` 			= '".$data['transid']."'
					 WHERE `reg_id` in (".$user_ids.") AND `accesskey` ='".$data['user_id']."'";
			fwrite($x, $query."\n");
			$database->setQuery($query);
			$database->query();
		// end
										
		//Update register table set status to accepted
			$query = "UPDATE #__registrationpro_register SET status=1, active=1 WHERE rid in ($user_ids)";
			fwrite($x, $query."\n");	 		 		 
			$database->setQuery($query);
			$database->query();
		// end
		$registrationproHelper = new registrationproHelper;
		// update ticket quantity
		//$registrationproHelper->updateEventTicketQty($user_ids);
		// end
								
		// get event ids to send the confirmation emails
			$query = "SELECT DISTINCT(rdid) FROM #__registrationpro_register WHERE rid in ($user_ids)";  
			fwrite($x, $query."\n");
			$database->setQuery($query);
			$eids = $database->loadObjectList();
		//end
		
		// create mail to send the registered users of all events		
		foreach($eids as $ekey => $evalue)
		{
			$users = regpro_registrations_emails::getEventForEmailTemplate($evalue->rdid, $user_ids); // get registered user to whom sending emails
			regpro_registrations_emails::send_registration_email($users);
		}
		
		if($data['gateway'] != "paypaypal"){ // condition to not redirect by IPN process
							
			if(count($eids) > 1){ // redirect to the thankspage if user registered with more then one event
				if(trim($regpro2Config['thankspagelink']) != ""){
					$link = trim($regpro2Config['thankspagelink']);
					$mainframe->redirect($link);
				}else{ // if thanks page link is not set
					$msg 	= JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
					$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=$Itemid", false);
					$mainframe->redirect($link,$msg);
				}
			}else{ // if user registered with one event only
				$did 	= $eids[0]->rdid;
				$reg_id = $user_ids;
				//redirect to thankyou
					$msg 	= JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
					$link 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=thanks&did=$did&registerid=$reg_id&Itemid=$Itemid", false);
										
					if($regpro2Config['disablethanksmessage'] == 1){
						$mainframe->redirect($link);
					}else{
						$mainframe->redirect($link,$msg);
					}
				//end
			}	
		}
	}
	
	function goToFailedURL ($sid, $msg = '') {
		global $configs, $mainframe;
		
		$Itemid = JRequest::getVar('Itemid');			
				
		$my 		=JFactory::getUser();
		$database 	=JFactory::getDBO();
		
	 	$website_url = str_replace("https://","http://", REGPRO_SITE_URL);
  		$failed_url = JRoute::_($website_url."/index.php?option=com_registrationpro&view=events"); 

		$reg_id = $sid;
		
		
		// get transaction ids for "registrationpro_event_discount_transactions" records deletions
		$query = "SELECT id FROM #__registrationpro_transactions WHERE reg_id in ($reg_id)";
		$database->setQuery($query);
		$trasaction_ids = $database->loadObjectList();		
		// end
		
		// Delte transaction details records
		$query = "DELETE FROM #__registrationpro_transactions WHERE reg_id in ($reg_id)";
		$database->setQuery($query);
		$database->query();
		// End

		// Delete register table records
		$query = "DELETE FROM #__registrationpro_register WHERE rid in ($reg_id)";
		$database->setQuery($query);
		$database->query();
		// end
		
		// Delete registrationpro_event_discount_transactions table records
		foreach($trasaction_ids as $tkey => $tvalue)
		{
			$query = "DELETE FROM #__registrationpro_event_discount_transactions WHERE trans_id = ".$tvalue->id;
			$database->setQuery($query);
			$database->query();
		}
		// End

		$msg = JText::_('EVENT_CART_PAYMENT_PROCESS_CANCELED');
		$mainframe->redirect($failed_url."&Itemid=".$Itemid,$msg);			
	}	

	function goToRefundURL ($sid, $msg = '') {
		global $configs, $mainframe, $Itemid;
		
		$my 			=JFactory::getUser();
		$database 		=JFactory::getDBO();
		$config			=JFactory::getConfig(); 			// get config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro2Config	= $registrationproAdmin->config();	// get registration pro settings
		
		$reg_id = $sid;		
		// enalbe user account in register table
		//mail("sushil@itdwebdesign.com","test",$reg_id);
		$query = "UPDATE #__registrationpro_transactions set payment_status = 'Refunded' where reg_id in ($reg_id)";	
		$database->setQuery($query);
		$database->query();		
		
		// enalbe user account in register table
		$database->setQuery("UPDATE #__registrationpro_register set status = 0 where rid in ($reg_id)");
		$database->query();						 
		//end			
			
	}
	
	function goToPendingURL ($sid, $msg = '') {
		global $configs, $mainframe, $Itemid;
		
		$my 			=JFactory::getUser();
		$database 		=JFactory::getDBO();
		$config			=JFactory::getConfig(); 			// get config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro2Config	= $registrationproAdmin->config(); 	// get registration pro settings
		
		$reg_id = $sid;		
		// enalbe user account in register table
			$database->setQuery("UPDATE #__registrationpro_register set active = 1 where rid in ($reg_id)");
			$database->query();						 
		//end
		
		/*// get event id
			$database->setQuery("SELECT rdid FROM #__registrationpro_register WHERE rid in ($reg_id)");
			$thk = $database->loadObjectList();
			
			//echo "<pre>"; print_r($thk); exit;
			
			$did = $thk[0]->rdid;
		//end*/
		
		// update ticket quantity
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->updateEventTicketQty($reg_id);
		
		
		// get event ids to send the confirmation emails
			$query = "SELECT DISTINCT(rdid) FROM #__registrationpro_register WHERE rid in ($reg_id)"; 
			$database->setQuery($query);
			$eids = $database->loadObjectList();
			
			//echo "<pre>"; print_r($eids);
		//end
		
		/*// create mail to send the registered users of all events		
		foreach($eids as $ekey => $evalue)
		{
			$users = regpro_registrations_emails::getEventForEmailTemplate($evalue->rdid, $user_ids); // get registered user to whom sending emails
			regpro_registrations_emails::send_registration_email($users);
		}*/
		
		//echo "<pre>"; print_r($eids); exit;
		
		if(count($eids) > 1){ // redirect to the thankspage if user registered with more then one event
				if(trim($regpro2Config['thankspagelink']) != ""){
					$link = trim($regpro2Config['thankspagelink']);
					$mainframe->redirect($link);
				}else{ // if thanks page link is not set
					$msg 	= JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
					$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=$Itemid", false);
					$mainframe->redirect($link,$msg);
				}
		}else{ // if user registered with one event only
			$did = $eids[0]->rdid;
			//redirect to thankyou
				$msg	= JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
				$link 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=thanks&did=$did&registerid=$reg_id&Itemid=$Itemid", false);
								
				if($regpro2Config['disablethanksmessage'] == 1){
					$mainframe->redirect($link);
				}else{
					$mainframe->redirect($link,$msg);
				}
			//end
		}				
																																	
		/*//redirect to thankyou
			$msg = JText::_('EVENT_CART_THANKS_FOR_REGISTRATION');
			$link = JRoute::_("index.php?option=com_registrationpro&controller=cart&task=thanks&did=$did&registerid=$reg_id&Itemid=$Itemid");
			$mainframe->redirect($link,$msg);
		//end*/
	}
	
		
	function performCheckout($total_amount = 0, $userids, $transids, $custom_key, $row) {
		global $configs, $mainframe;
		
		$my 			=JFactory::getUser();
		$database 		=JFactory::getDBO();
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig	= $registrationproAdmin->config();	// get regpro settings
		
		//echo"<pre>";print_r($row); exit;

		/*//we're under https (redirection occured) - need to try to restore user session object
		if ( getenv('HTTPS') == 'on' ) {
			$id = JRequest::getVar("userid", 0);
	        if (!$my->id && $id > 0) {
	        	$this->fillMy($id);
	        }	  	     
	    }*/
													  
		//$sid 	= 1;			
		//$sid 	= $transids;
		$reg_id = $userids;				
	
		$explod_regid = explode(",",$reg_id);
								
		//$query = "SELECT * FROM #__registrationpro_transactions rt, #__registrationpro_register rr WHERE rr.rid in ($reg_id) AND rt.reg_id in ($reg_id) AND rt.p_id <> '' GROUP BY rt.p_id";		
		
		$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type FROM #__registrationpro_transactions t"
				. "\nLEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				. "\n WHERE t.reg_id in ($reg_id)  AND t.p_id <> '' GROUP BY t.p_id";
		
		$database->setQuery($query);				
		$reg = $database->loadObjectList();	
		// end
		
		// get additional form field fees information to create items for paypal gateway product list
		$query = "SELECT a.* FROM #__registrationpro_additional_from_field_fees as a"
				. "\n WHERE a.reg_id in ($reg_id)";
		
		$database->setQuery($query);				
		$form_field_fees = $database->loadObjectList();		
		// end 
		
		// get sessions information to create items for paypal gateway product list
		$query = "SELECT a.* FROM #__registrationpro_session_transactions as a"
				. "\n WHERE a.reg_id in ($reg_id)";
		
		$database->setQuery($query);				
		$sessions = $database->loadObjectList();		
		// end 
		
		
		/*if($reg[0]->uid != $my->id) {
			echo ('<script> alert("'.JText::_('PAYMENTNOTPROCESSED').'"); history.go(-1);</script>');
			return;
		}*/

		// create a product list
        $items = $this->buildItemList($total_amount, $reg, $form_field_fees, $sessions);

		if ($userids < 1 || count ($items) < 1) 
			echo ('<script language="javascript"> alert ("'.JText::_('CANTGETCART').'"); history.go(-1);</script>');      
       		       		              	
		$payment_type 			= $row->payment_method;
				
		$params['processor'] 	= $payment_type;
		$params['items'] 		= $items;
		$params['order_amount'] = $total_amount;
		$params['user_id'] 		= $custom_key;
		//$params['user_ids'] 	= $userids;
		$params['order_id']		= $userids;
		
		$params['controller'] 	= "cart";
		$params['task'] 		= "payments_process";
		//$params['custom_key'] 	= $custom_key;
		$params['currency']		= $regproConfig['currency_value'];
									
		$dispatcher =JDispatcher::getInstance();
		JPluginHelper::importPlugin('regpro_gateways');
		$dispatcher->trigger('onSendPayment', array (& $params));			    				
	}

	function buildItemList($total, $reg, $form_field_fees, $sessions) {
		$items = array();
		
		//echo "<pre>"; print_r($reg); exit;
		$j = 0;
		for($i=0;$i<count($reg);$i++){
		
			// calculate discount amount pe ticket
			if($reg[$i]->event_discount_amount > 0){
				if($reg[$i]->event_discount_type == 'P'){
					$event_discounted_amount_price 	= 0;
					/*$actual_price_without_per 		= 0;
					$actual_price_without_per 		= ($reg[$i]->price * 100) / (100 - $reg[$i]->event_discount_amount);				
					$event_discounted_amount_price 	= $actual_price_without_per * $reg[$i]->event_discount_amount / 100;					
					$reg[$i]->discount_amount 		+= $event_discounted_amount_price;*/
					$reg[$i]->discount_amount 		+= $reg[$i]->event_discount_amount;
					//$reg[$i]->price 				= $actual_price_without_per;
				}else{
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$event_discounted_amount_price 	= $reg[$i]->event_discount_amount;					
					$reg[$i]->discount_amount 		+= $event_discounted_amount_price;
				}
			}
			// end
				
			$items[$i] 	= new stdClass();
			//$items[$i]->cart_price	= $reg[$i]->price;						
			$items[$i]->cart_price	= $reg[$i]->price - $reg[$i]->discount_amount;
			
			if($items[$i]->cart_price <= 0){
				$items[$i]->cart_price = 0;
			}
			
			//echo "<pre>"; print_r($reg); exit;
							
			$items[$i]->product_name= $reg[$i]->item_name;
			$items[$i]->quantity	= $reg[$i]->quantity;
			$items[$i]->p_id 		= $reg[$i]->p_id;
			
			$j = $i;
		}
		
		//echo "<pre>"; print_r($form_field_fees); exit;
		
		// if additional form fields exists
		if(is_array($form_field_fees) && count($form_field_fees) > 0) {
			$j = $j +1;
			foreach ($form_field_fees as $fkey => $fvalue)
			{
				@$items[$j]->cart_price	= @$fvalue->additional_field_fees;
			
				if($items[$j]->cart_price <= 0){
					$items[$j]->cart_price = 0;
				}
				
				//echo "<pre>"; print_r($reg); exit;								
				$items[$j]->product_name= $fvalue->additional_field_name;
				$items[$j]->quantity	= 1;
				$items[$j]->p_id 		= $fvalue->id;
				
				$j++;
			}
		}
			
		// if sessions exists
		if(is_array($sessions) && count($sessions) > 0)	 {
		
			if($j == 0) {
				$j = $j +1;
			}
			
			foreach ($sessions as $skey => $svalue)
			{
				$items[$j]->cart_price	= $svalue->session_fees;
			
				if($items[$j]->cart_price <= 0){
					$items[$j]->cart_price = 0;
				}
				
				//echo "<pre>"; print_r($reg); exit;								
				$items[$j]->product_name= $svalue->sessionname;
				$items[$j]->quantity	= 1;
				$items[$j]->p_id 		= $svalue->id;
				
				$j++;
			} 
		}	
		//echo "<pre>"; print_r($items); exit;

		return $items;
	}

	function getPrice($items, $total) {
		$tax = array();
		$tax['total'] = $total;
		$tax['taxed'] = $total;
		$tax['value'] = 0;
		$tax['shipping'] = 0;
		return $tax;
	}
	
	function getSocialsettings($view = null,$position=null)
	{
		//$detaillink = JRoute::_('index.php?option=com_registrationpro&amp;view=event&amp;Itemid='. $this->Itemid .'&amp;did='.$row->id);
		//echo $detaillink;
		
		$plugin = JPluginHelper::getPlugin('regpro','regprosocialmedia');
		$params = json_decode($plugin->params);
		if(count($params) >0)
		{
			foreach($params as $k=>$v)
			{
				$social_links[$k] = $v;
			}
		}
		//echo "<pre>";print_r($social_links); echo "</pre>";
		
		$img_path= JRoute::_(JURI::root().'plugins/regpro/regprosocialmedia/images');
		if($social_links['eventListing'] && $view == 'events')
		{
			$flag = 1;
		}elseif($social_links['eventDesc'] && $view == 'event')
		{
			if($social_links['iconPosition'] == 'top' &&  $position == 'top')
			{
				$flag = 1;
			}
			elseif($social_links['iconPosition'] == 'bottom' &&  $position == 'bottom'){
				$flag = 1;
			}else{
				$flag = 0;
			}
		}else{
			$flag = 0;
		}
		if($flag)
		{
		
			if($social_links['facebook'] == '1' && !empty($social_links['fbAppId']))
			{
				$arr["l_facebook"]= '<a class="a_social" href="https://www.facebook.com/dialog/feed?app_id='.$social_links['fbAppId'].'&display=popup&redirect_uri=%s&caption=%s&picture=%s&link=%s&description=%s" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"><img title="Facebook" src="'.$img_path.'/fb.png" alt="Facebook"/></a>';
			}
			if($social_links['twitter'] == '1')
			{
				$arr["l_twitter"]= '<a class="a_social" href="https://twitter.com/intent/tweet?text=%s" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"><img title="Twitter" src="'.$img_path.'/tw.png" alt="Twitter"/></a>';
			}
			if($social_links['linkedin'] == '1')
			{
				$arr["l_linkedin"]= '<a class="a_social" href="http://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s&summary=%s" onclick="javascript:window.open(this.href,\'\',\'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"><img title="Linkedin" src="'.$img_path.'/linked.png" alt="Linkedin"/></a>';
			}
			if($social_links['googlePlus'] == '1')
			{
				$arr["l_googlePlus"]= '<a class="a_social" href="https://plus.google.com/share?url=%s" onclick="javascript:window.open(this.href,\'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;"><img title="Google Plus" src="'.$img_path.'/gplus.png" alt="Google Plus"/></a>';
			}
		}
			if($view == 'event')
			{
				$arr["share_text"] = $social_links['sharelinkText'];
			} 
		return $arr;
	}
}

?>