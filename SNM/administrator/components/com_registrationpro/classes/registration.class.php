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

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

// Event Registration class
class regpro_registrations extends JModelLegacy
{
	var $cart;
	var $row;
	var $_db;
	var $regpro_config;
	var $helper;

	function __construct($cart, $row)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');

		$this->cart 		= $cart;
		$this->row 			= $row;
		$this->row->message	= "";

		$this->_db = JFactory::getDBO();

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$this->regpro_config	= $registrationproAdmin->config();

		// check max attendace for event
		$this->checkMaxAttendance();

		// assign orignal cart values
		$this->row->eventids	 		= $this->cart['eventids']; 		// get all event ids those ticket are added by users in cart
		$this->row->groupregistrations	= $this->cart['groupregistrations']; // get all event ids those have groupregistation selected by users
		$this->row->payments 			= $this->cart['ticktes']; 		// get information of selected tickets from cart
		$this->row->free_event			= $this->cart['free_event']; 	// set free event flagfrom cart
		$this->row->total_amount		= $this->cart['grand_total'] + $this->cart['additional_formfield_fees_total']; // set total amount from cart
		$this->row->discount			= $this->cart['discount'];		// set total discount from cart
		$this->row->total_tqty			= $this->cart['total_tqty'];	// set total cart event tickets quatity from cart
		$this->row->total_addqty		= $this->cart['total_addqty'];	// set total cart additional tickets quatity from cart
		$this->row->total_qty			= $this->cart['total_qty'];		// set total cart tickets quat/ity from cart
		$this->row->total_tax			= $this->cart['total_tax'];		// set total tax amount from cart
		$this->row->currency_sign		= $this->cart['currency_sign'];	// set currency sign
		$this->row->form_data			= $this->cart['form_data'];		// set registratino form data
		$this->row->form				= $this->cart['form_data']['form'];	// set registratino form data
		$this->row->payment_method		= strtolower(JRequest::getVar("selPaymentOption",NULL));
		$this->row->event_discounts		= $this->cart['event_discounts'];

	}//end

	// check event max attendence
	function checkMaxAttendance()
	{
		$nothing = array();
		$registrationproHelper = new registrationproHelper; $registrationproHelper->check_max_attendance($this->row, $nothing, $this->cart, $this->regpro_config, 3);
	}//end

	// check registration activation
	function checkEventRegistrationEnable()
	{
		$registrationproHelper = new registrationproHelper; $registrationproHelper->check_event_registration_enable($this->row, $this->regpro_config, 3);
	}//end

	// check payment method selected by user or not
	function checkPaymentMethodSelection()
	{
		if($this->row->total_amount > 0 && empty($this->row->payment_method)){
			$this->row->message = JText::_('EVENTS_IS_PAID_SELECT_PAYMENT_METHOD');
			return true;
		}
	}

	// check registration duplicate email address
	function check_duplicate_email()
	{
		$row = $this->row;

		//echo "<pre>"; print_r($row); exit;

		if($this->regpro_config['duplicate_email_registration'] != 1){

			// check duplicate email address in forms
			$this->row->message = $this->check_dulicate_email_in_forms($row);
			if(is_array($this->row->message)){
				$this->row->message = '"'.$row->message['existing_email'].'" '.$row->message['error_message'];
				return true;
			}
			// end

			// check already existance of email id in table
			$reg =JTable::getInstance('registrationpro_register', '');

			for($i=0;$i<count($row->form['firstname']);$i++)
			{
				foreach($row->payments as $pkey=>$pvalue)
				{
					if($row->form['email'][$i][$row->payments[$pkey]->id] != ""){
						$postEmalID = $row->form['email'][$i][$row->payments[$pkey]->id];
					}
				}
				$this->row->message = $reg->check_existing_email($row->eventids, $postEmalID);

				if(is_array($this->row->message)){
					$this->row->message = '"'.$row->message['existing_email'].'" '.$row->message['error_message'];
					return true;
				}
			}
			// end
		}
	}//end

	// check for duplicate entry of email address in forms
	function check_dulicate_email_in_forms($row)
	{
		if($row->eventids){
			foreach($row->eventids as $ekey => $evalue) // event ids loop
			{
				if($row->payments){
					foreach($row->payments as $pkey=>$pvalue) // payments loop
					{
						if($evalue == $row->payments[$pkey]->regpro_dates_id) {
							for($i=0;$i<count($row->form['firstname']);$i++)
							{
								if($row->form['regpro_event_id'][$i][$row->payments[$pkey]->id] == $evalue){
									if($row->form['email'][$i][$row->payments[$pkey]->id] != ""){
										$email_compare = $row->form['email'][$i][$row->payments[$pkey]->id];
									}

									for($j=$i+1;$j<count($row->form['firstname']);$j++)
									{
										if( $row->form['email'][$j][$row->payments[$pkey]->id] == $email_compare && $row->allowgroup != 1 ){
											$arrReturn['error_message'] .= JText::_('EVENTS_EMAIL_DUPLICATE_ENTRY') . "<br/>";
											$arrReturn['existing_email'] = $email_compare;
											//$this->row->message = $arrReturn;
											return $arrReturn;
										}
									}
								}
							}
						}
					} // end payment loop
				}
			} // end event ids loop
		}
	}//end

	// Save user
	function save_user_data()
	{
		$row = $this->row;
		$user	= JFactory::getUser(); // get login user info
		$registrationproHelper = new registrationproHelper;
		$arrRegIds = array();
		foreach($row->payments as $pkey=>$pvalue)
		{
			$group_added_by = 0;
			$firstuser = 1;
			foreach($row->form['firstname'] as $i => $ivalue)
			{
				// Filter user records from form data
				$finalparams 	= "";
				$params_field 	= "";

				foreach($row->form as $key=>$value){
					if(is_array($row->form[$key][$i][0])){
						// script to filter the multiple options values (like, check box, multiple select select box etc.)
						for($j=0;$j<=count($row->form[$key][$i]); $j++)
						{
							if($row->form[$key][$i][$j][$row->payments[$pkey]->id] && $key != 'regpro_event_id'){
								$finalparams[$key][$j][$j] = $row->form[$key][$i][$j][$row->payments[$pkey]->id];
							}
						}
						// end
					}else{
						if($row->form[$key][$i][$row->payments[$pkey]->id] && $key != 'regpro_event_id'){
							if($row->form[$key][$i][$row->payments[$pkey]->id + 1] == 'F'){ // add flag for file type field
								$finalparams[$key][0][0] = $row->form[$key][$i][$row->payments[$pkey]->id];
								$finalparams[$key][0][0 + 1] = $row->form[$key][$i][$row->payments[$pkey]->id + 1];
							}else{

								$finalparams[$key][0][0] = $row->form[$key][$i][$row->payments[$pkey]->id];
							}
						}
					}
				}

				$params_field = serialize($finalparams);

				if($row->form['firstname'][$i][$row->payments[$pkey]->id] != "" && $row->form['regpro_event_id'][$i][$row->payments[$pkey]->id] == $row->payments[$pkey]->regpro_dates_id){
					$reg 				=JTable::getInstance('registrationpro_register', '');
					$reg->rdid 			= $row->form['regpro_event_id'][$i][$row->payments[$pkey]->id];
					$reg->uid 			= $user->id;
					$reg->urname 		= $user->username;
					$reg->uregdate 		= $registrationproHelper->getCurrent_date_unix(true);//$nowdate->toUnix(true);
					$reg->uip 			= getenv('REMOTE_ADDR');
					$reg->status 		= 0;
					$reg->temp_params 	= $params_field; // store a backup copy of registered users values
					$reg->params 		= $params_field;
					$reg->notify 		= $row->notify;
					$reg->notified 		= 0;
					$reg->products 		= $row->payments[$pkey]->id;
					$reg->firstname 	= $row->form['firstname'][$i][$row->payments[$pkey]->id];
					$reg->lastname 		= $row->form['lastname'][$i][$row->payments[$pkey]->id];
					$reg->email 		= $row->form['email'][$i][$row->payments[$pkey]->id];
					$reg->added_by		= $row->addedby;

					// group registation check
					if(is_array($row->groupregistrations) && count($row->groupregistrations) > 0) {
						foreach($row->groupregistrations as $gkey => $gvalue)
						{
							if($row->payments[$pkey]->regpro_dates_id == $gvalue){
								$reg->group_added_by = $group_added_by;
							}
						}
					}

					// store user info in the db
					if (!$reg->store()) {
						if($reg -> getError()) {
							echo "<script> alert('".$reg -> getError()."'); window.history.go(-2); </script>\n";
							exit();
						}
						$error_message .= $reg -> getError() . "<br/>";
						$query = "DELETE FROM #__registrationpro_register WHERE rdid in ($regid)";
						$this->_db->setQuery($query);
						$this->_db->query();

						$query = "DELETE FROM #__registrationpro_transactions WHERE reg_id in ($regid)";
						$this->_db->setQuery($query);
						$this->_db->query();
						$flag = false;
					}

					// push inserted registraion
					array_push($arrRegIds,$this->_db->insertid());
					$regid = $this->_db->insertid();

					// assign group_added_by id to second regsitration
					$firstuser++;
					if($firstuser == 2){
						$group_added_by = $this->_db->insertid();
					}

					if($row->enable_create_user ==1){
						$joomla_user_id = $this->saveJoomlaRegistration($reg->firstname.$reg->lastname, $reg->email,$row->enabled_user_group);

						$query1 = "UPDATE #__registrationpro_register SET uid=".$joomla_user_id." WHERE rdid in ($regid)";
						$this->_db->setQuery($query1);
						$this->_db->query();
					}

					// save session fees transaction data
					if(count($row->payments[$pkey]->sessions) > 0 && $regid){
						foreach($row->payments[$pkey]->sessions as $skey => $svalue)
						{
							$session					=JTable::getInstance('registrationpro_session_transactions', '');
							$session->reg_id			= $regid;
							$session->sessionid			= $svalue->id;
							$session->sessionname		= $svalue->title;
							$session->session_fees		= $svalue->fee;
							$session->type				= 'A';

							if(!$session->store()){
								die(html_entity_decode($session->getError()));
								echo "<script> alert('".html_entity_decode($session->getError())."'); window.history.go(-1); </script>\n";
								exit();
							}
						}
					}
					if(count($row->payments[$pkey]->additional_form_field_fees[$i]) > 0 && $regid){

						foreach($row->payments[$pkey]->additional_form_field_fees[$i] as $afffkey => $afffvalue)
						{
							$additional_from_field_fees							=JTable::getInstance('registrationpro_additional_from_field_fees', '');
							$additional_from_field_fees->reg_id					= $regid;
							$additional_from_field_fees->additional_field_name	= $afffvalue['field_name'];
							$additional_from_field_fees->additional_field_fees	= $afffvalue['amount'];
							$additional_from_field_fees->type					= 'A';

							if(!$additional_from_field_fees->store()){
								die(html_entity_decode($additional_from_field_fees->getError()));
								echo "<script> alert('".html_entity_decode($additional_from_field_fees->getError())."'); window.history.go(-1); </script>\n";
								exit();
							}
						}
					}
					// end form additional field fees transaction loop
					if($row->enable_mailchimp==1 && $row->mailchimp_list !=""){
						$registrationproHelper = new registrationproHelper;
						$registrationproHelper->subsribeUser($reg->firstname,$reg->lastname,$reg->email,$row->mailchimp_list);
						}
				}
			}
		}

		return $arrRegIds;
	}//end

	function saveJoomlaRegistration($name, $email,$group_id) {
		global $regpro_mail;
		jimport('joomla.user.helper');
		$registrationproHelper = new registrationproHelper;
		$user = array();
		$user['fullname'] 		= $name;
		$user['email'] 			= $email;
		$user['password_clear'] = $registrationproHelper->str_makerand(6,8,0,0,1);
		$user['username'] 		= $name."_".$registrationproHelper->str_makerand(2,4,0,0,0);

		// encrypt the password
		$salt  = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($user['password_clear'], $salt);
		$password = $crypt.':'.$salt;

		// Check that password is not greater than 100 characters
		if (strlen($password) > 100) {
			$password = substr($password, 0, 100);
		}

		$instance = JUser::getInstance();

		jimport('joomla.application.component.helper');
		$config = JComponentHelper::getParams('com_users');
		// Default to Registered.
		$defaultUserGroup = $group_id;

		$acl = JFactory::getACL();

		$instance->set('id'         , 0);
		$instance->set('name'           , $user['fullname']);
		$instance->set('username'       , $user['username']);
		$instance->set('password_clear' , $user['password_clear']);
		$instance->set('password' 		, $password);
		$instance->set('email'          , $user['email']);  // Result should contain an email (check)
		$instance->set('usertype'       , 'deprecated');
		$instance->set('groups'     	, array($defaultUserGroup));

		// Check if the user needs to activate their account.
		$useractivation = $config->get('useractivation');
		if (($useractivation == 1) || ($useractivation == 2)) {
			jimport('joomla.user.helper');
			$instance->set('activation' , JApplication::getHash(JUserHelper::genRandomPassword()));
			$instance->set('block', 1);
		}

		//If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] :  $config->get('autoregister', 1);

		if ($autoregister) {
			if (!$instance->save()) {
				JError::raiseWarning('SOME_ERROR_CODE', $instance->getError());
				return 0;
			}
		}
		else {
			// No existing user and autoregister off, this is a temporary user.
			$instance->set('tmp_user', true);
		}

		$site_config = JFactory::getConfig();
		$data = $instance->getProperties();
		$data['fromname']	= $site_config->get('fromname');
		$data['mailfrom']	= $site_config->get('mailfrom');
		$data['sitename']	= $site_config->get('sitename');
		$data['siteurl']	= JUri::base();

		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = REGPRO_SITE_URL.'index.php?option=com_users&task=registration.activate&token='.$data['activation'];

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['activate'],
				REGPRO_SITE_URL,
				$data['username'],
				$data['password_clear']
			);
		}
		else if ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] =  REGPRO_SITE_URL.'index.php?option=com_users&task=registration.activate&token='.$data['activation'];

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],

				$data['sitename'],

				$data['activate'],

				REGPRO_SITE_URL,

				$data['username'],

				$data['password_clear']
			);
		} else {

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl']
			);
		}
		// Send the registration email.
		$return = $regpro_mail->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody,false,null,null,null,null,null,null);

		// Check for an error.
		if ($return !== true) {
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = JFactory::getDBO();
			$q = "SELECT id
				FROM #__users
				WHERE block = 0
				AND sendEmail = 1";
			$db->setQuery($q);
			$sendEmail = $db->loadResultArray();
			if (count($sendEmail) > 0) {
				$jdate = new JDate();
				// Build the query to add the messages
				$q = "INSERT INTO `#__messages` (`user_id_from`, `user_id_to`, `date_time`, `subject`, `message`)
					VALUES ";
				$messages = array();
				foreach ($sendEmail as $userid) {
					$messages[] = "(".$userid.", ".$userid.", '".$jdate->toMySQL()."', '".JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')."', '".JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username'])."')";
				}
				$q .= implode(',', $messages);
				$db->setQuery($q);
				$db->query();
			}
			//return false;
		}


		//$return["message"] = $message;

		if($instance->id > 0) {
			$return = $instance->id;
		}else{
			$return = 0;
		}

		return $return;
	}

	// save user transactions data
	function save_user_transaction_data($user_ids = array())
	{
		$row = $this->row;
		$trans_data		= array(); // store new inserted transaction ids and custom key

		$reg_id = implode(",",$user_ids);
		$total_amount = $row->total_amount;
		$qty_gross  = $row->total_qty;
		$access_key = $this->create_random_key(); // create random key
		$nowdate 	= JFactory::getDate(); // get date object

		if($total_amount > 0 || !empty($row->payments[0]->id)){	 // check whether the ticket record exist with 0 amount
			if(!empty($row->payments)){

				// event ids loop
				foreach($row->eventids as $ekey => $evalue)
				{
					//$ticketidForAdditionalTotal = array();	// arry to filter the additional tickets for every first register user per event
					$firstuser_flag = 0;

					// userid loop
					foreach($user_ids as $key=>$value)
					{
						$user_data = $this->getUserInfo($value);

						if($evalue == $user_data->rdid){

							if($firstuser_flag == 0){
								$firstuser_flag = 1;
							}

							$additonalflag = 0;
							// get all additional tickets to assign the first regisered user
							if($row->total_addqty > 0){
								$additional_tickets = array();
								foreach($row->payments as $pkey=>$pvalue)
								{
									if($row->payments[$pkey]->type == 'A' && $row->payments[$pkey]->regpro_dates_id == $evalue && $firstuser_flag == 1){
											$additional_tickets[] = $row->payments[$pkey];
											$additonalflag = 1;
									}
								}
							}

							foreach($row->payments as $pkey=>$pvalue)
							{
								if($row->payments[$pkey]->regpro_dates_id == $evalue){
									// Assign first registered user id to all additional tickets
									if($row->total_addqty > 0 && $additonalflag == 1){
										foreach($additional_tickets as $akey=>$avalue)
										{
											for($k=0;$k<$additional_tickets[$akey]->qty;$k++)
											{
												// save transaction data
												$trans_id  = 0;
												$transaction 						=JTable::getInstance('registrationpro_transactions', '');

												$transaction->reg_id 				= $user_data->rid;
												$transaction->p_id 					= $additional_tickets[$akey]->id;
												$transaction->p_type				= $additional_tickets[$akey]->type;
												$transaction->tax 					= $additional_tickets[$akey]->tax;
												$transaction->tax_amount			= $additional_tickets[$akey]->tax_price;
												$transaction->item_name 			= $additional_tickets[$akey]->product_name;
												$transaction->coupon_code  			= $additional_tickets[$akey]->coupon_code;
												$transaction->discount_type  		= $additional_tickets[$akey]->discount_type;
												$transaction->discount  			= $additional_tickets[$akey]->discount;
												$transaction->discount_amount		= $additional_tickets[$akey]->discount_amount;
												$transaction->AdminDiscount			= $this->cart['AdminDiscount'];
												$transaction->price_without_tax		= $additional_tickets[$akey]->price_without_tax;
												$transaction->price  				= $additional_tickets[$akey]->total_price;
												$transaction->final_price  			= $additional_tickets[$akey]->final_price;
												$transaction->quantity  			= $additional_tickets[$akey]->qty;
												$transaction->payment_gross			= $row->grand_total;
												$transaction->quantity_gross		= $row->total_qty;
												$transaction->accesskey 			= $access_key;
												$transaction->receiver_email 		= $transaction_email;
												$transaction->first_name			= $user_data->firstname;
												$transaction->last_name				= $user_data->lastname;
												$transaction->payer_email			= $user_data->email;
												$transaction->payment_method		= $row->payment_method;
												$registrationproHelper = new registrationproHelper;
												$transaction->payment_date			= $registrationproHelper->getCurrent_date(); //$nowdate->format(); //date('M-d-Y H:i:s');
												$transaction->payment_status		= "pending";
												$transaction->mc_currency			= $row->currency_sign;

												if(!$transaction->store()){
													die(html_entity_decode($transaction->getError()));
													echo "<script> alert('".html_entity_decode($transaction->getError())."'); window.history.go(-1); </script>\n";
													exit();
												}
												$trans_id = $this->_db->insertid();
												$trans_data['id'][] = $trans_id;
												// end
											}
											$additonalflag = 0;
										}
									}
									// end

									// Store tickets information in transaction table
									if($row->payments[$pkey]->id == $user_data->products){

										$trans_id  = 0;
										$transaction 					=JTable::getInstance('registrationpro_transactions', '');

										$transaction->reg_id 			= $user_data->rid;
										$transaction->p_id 				= $row->payments[$pkey]->id;
										$transaction->p_type			= $row->payments[$pkey]->type;
										$transaction->tax 				= $row->payments[$pkey]->tax;
										$transaction->tax_amount		= $row->payments[$pkey]->tax_price;
										$transaction->item_name 		= $row->payments[$pkey]->product_name;
										$transaction->coupon_code  		= $row->payments[$pkey]->coupon_code;
										$transaction->discount_type  	= $row->payments[$pkey]->discount_type;
										$transaction->discount  		= $row->payments[$pkey]->discount;
										$transaction->discount_amount	= $row->payments[$pkey]->discount_amount;
										$transaction->AdminDiscount		= $this->cart['AdminDiscount'];
										$transaction->price_without_tax	= $row->payments[$pkey]->price_without_tax;
										$transaction->price  			= str_replace(",","",$row->payments[$pkey]->total_price);
										$transaction->final_price  		= $row->payments[$pkey]->final_price;
										$transaction->quantity  		= $row->payments[$pkey]->qty;
										$transaction->payment_gross		= $row->grand_total;
										$transaction->quantity_gross	= $row->total_qty;
										$transaction->accesskey 		= $access_key;
										$transaction->receiver_email 	= $transaction_email;
										$transaction->first_name		= $user_data->firstname;
										$transaction->last_name			= $user_data->lastname;
										$transaction->payer_email		= $user_data->email;
										$transaction->payment_method	= $row->payment_method;
										$registrationproHelper = new registrationproHelper;
										$transaction->payment_date		= $registrationproHelper->getCurrent_date(); //$nowdate->format(); //date('M-d-Y H:i:s');
										$transaction->payment_status	= "pending";
										$transaction->mc_currency		= $row->currency_sign;

										if(!$transaction->store()){
											die(html_entity_decode($transaction->getError()));
											echo "<script> alert('".html_entity_decode($transaction->getError())."'); window.history.go(-1); </script>\n";
											exit();
										}
										$trans_id = $this->_db->insertid();
										$trans_data['id'][] = $trans_id;
										// end

										// save discount coupons transaction data
										//echo "<pre>"; print_r($row->event_discounts); exit;
										if(count($row->payments[$pkey]->event_discount_id) > 0 && $trans_id){
											foreach($row->payments[$pkey]->event_discount_id as $eidkey => $eidvalue)
											{
												foreach($row->event_discounts as $edkey => $edvalue)
												{
													if($edvalue->id == $eidvalue && $trans_id > 0){
														$coupon_transaction							=JTable::getInstance('registrationpro_event_discount_transactions', '');
														$coupon_transaction->trans_id				= $trans_id;
														//$coupon_transaction->event_id				= $row->payments[$pkey]->regpro_dates_id; //$row->event_id;
														$coupon_transaction->event_discount_amount	= $row->payments[$pkey]->event_total_discount_amount;
														$coupon_transaction->event_discount_type	= "A";//$edvalue->discount_type;

														if(!$coupon_transaction->store()){
															die(html_entity_decode($coupon_transaction->getError()));
															echo "<script> alert('".html_entity_decode($coupon_transaction->getError())."'); window.history.go(-1); </script>\n";
															exit();
														}
														$trans_id = 0;
													}
												}
											}
										}// end discount transaction data loop

									}
								}// end event id condition
								$firstuser_flag++;
							} // end payment loop
						}// end event id condition
					} // end users ids loop
				} // end event ids loop
			}
		}else{
			//echo "<pre>"; print_r($user_ids); exit;

			$arrExistIds = array();

			foreach($row->eventids as $ekey => $evalue) // event ids loop
			{
				foreach($user_ids as $key=>$value)
				{
					$user_data = $this->getUserInfo($value);

					foreach($row->payments as $pkey=>$pvalue)
					{
						if($row->payments[$pkey]->regpro_dates_id == $evalue){
							if($row->payments[$pkey]->id == $user_data->products && !in_array($user_data->rid,$arrExistIds)){

								$transaction 					=JTable::getInstance('registrationpro_transactions', '');

								$transaction->reg_id 			= $user_data->rid;
								$transaction->p_id 				= 0;
								$transaction->p_type			= 'E';
								$transaction->tax 				= 0.00;
								$transaction->item_name 		= "Free";
								$transaction->price  			= 0.00;
								$transaction->quantity  		= $row->payments[$pkey]->qty;
								$transaction->payment_gross		= 0.00;
								$transaction->quantity_gross	= $row->total_qty;
								$transaction->receiver_email 	= $transaction_email;
								$transaction->first_name		= $user_data->firstname;
								$transaction->last_name			= $user_data->lastname;
								$transaction->payer_email		= $user_data->email;
								$transaction->payment_method	= $row->payment_method;
								$transaction->payment_status	= "pending";
								$transaction->accesskey 		= $access_key;
								$transaction->AdminDiscount		= $this->cart['AdminDiscount'];
								$registrationproHelper = new registrationproHelper;
								$transaction->payment_date		=  $registrationproHelper->getCurrent_date(); //$nowdate->format();

								if(!$transaction->store()){
									die(html_entity_decode($transaction->getError()));
									echo "<script> alert('".html_entity_decode($transaction->getError())."'); window.history.go(-1); </script>\n";
									exit();
								}
								$trans_id 			= $this->_db->insertid();
								$trans_data['id'][] = $trans_id;

								array_push($arrExistIds,$user_data->rid);
							}
						}
					} // payment loop
				} // user id loop
			} // event id loop
		}

		// enable user account in register table
			$query = "UPDATE #__registrationpro_register set active = 1 where rid in ($reg_id)";
			$this->_db->setQuery($query);
			$this->_db->query();
		//end

		$trans_data['custom_key'] = $access_key;

		return $trans_data;
	}//end


	// create random key
	function create_random_key()
	{
		$registrationproHelper = new registrationproHelper;
		$key = $registrationproHelper->str_makerand(10,10,1,0,1);
		return $key;
	}//end

	// get user registration info
	function getUserInfo($userid)
	{
		$query = "SELECT * FROM #__registrationpro_register WHERE rid = ".$userid;
		$this->_db->setQuery($query);
		$user_data = $this->_db->loadObjectList();
		$user_data = $user_data[0];
		return $user_data;
	}//end

	// Update registered users status after free events registrations
	function updateUserStatus($user_ids = 0, $status = 1)
	{
		$database = JFactory::getDBO();

		$query 	= "UPDATE #__registrationpro_register set status = $status WHERE rid IN (".$user_ids.")";
		$database->setQuery($query);
		$database->query();
	}

	// Update Payment status (pending, complete etc.)
	function updatePaymentStatus($user_ids = 0, $status = 'pending')
	{
		$database = JFactory::getDBO();

		$query 	= "UPDATE #__registrationpro_transactions set payment_status = '".$status."' WHERE reg_id IN (".$user_ids.")";
		$database->setQuery($query);
		$database->query();
	}

}// end class


class regpro_registrations_emails
{

	// get Event full details to create the email template
	function getEventForEmailTemplate($eventid, $user_ids = 0)
	{
		$database = JFactory::getDBO();

		$query = "SELECT r.rid AS uid, e.id AS eid, e.user_id, e.titel, e.status, e.dates, e.times, e.enddates, "
				."\n e.endtimes, e.shortdescription, e.datdescription, e.allowgroup, e.notifyemails, "
				."\n r.firstname, r.lastname, r.email, r.params, l.club, l.url, l.city, l.plz, l.street, l.country, l.locdescription FROM"
				."\n #__registrationpro_dates e, #__registrationpro_locate l, #__registrationpro_register r"
				."\n WHERE e.id=r.rdid "
				."\n AND l.id=e.locid "
				."\n AND e.published=1 "
				."\n AND r.group_added_by=0 "
				."\n AND r.rid in ($user_ids) "
				."\n AND r.rdid=".$eventid;

		$database->setQuery($query);
		$user_data = $database->loadObjectList();

		return $user_data;
	}

	function send_registration_email($users = array())
	{
		global $regpro_mail;
		$database = JFactory::getDBO();
		$config	= JFactory::getConfig();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_registrations_emails = new regpro_registrations_emails;
		$regpro_config	= $registrationproAdmin->config();
		$pms = array();
		$email_sent = array();
		$registrationproHelper = new registrationproHelper;
		
		foreach($users as $user){
			$pms['registrationid'] 	= $user->uid;
			$name 					= $user->firstname." ".$user->lastname;
			$pms['sitename'] 		= $config->get('sitename');
			$pms['eventid'] 		= $user->eid;
			$pms['eventtitle'] 		= $user->titel;
			$pms['eventstart'] 		= $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->dates.' '.$user->times);
			$pms['eventend'] 		= $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->enddates.' '.$user->endtimes);
			$pms['shortdesc'] 		= $user->shortdescription;
			$pms['longdesc'] 		= $user->datdescription;
			$pms['eventstatus'] 	= JText::_('ADMIN_EVENTS_STATUS_'. $user->status);
			$pms['location'] 		= $user->club;
			$pms['url'] 			= $user->url;
			$pms['street'] 			= $user->street;
			$pms['zip'] 			= $user->plz;
			$pms['city'] 			= $user->city;
			$pms['country'] 		= $user->country;
			$pms['locdescription'] = $user->locdescription;
			$pms['fullname'] 	   = $name;
			$pms['email'] 			= $user->email;

			$query = "SELECT form_id FROM #__registrationpro_dates WHERE id=".$user->eid;
			$database->setQuery($query);
			$form_id = $database->loadResult();
			
			$query = "SELECT pdfimage FROM #__registrationpro_dates WHERE id=".$user->eid;
			$database->setQuery($query);
			$pms['pdfimage'] = $database->loadResult();

			$pms['registrationdetails'] = $regpro_registrations_emails->create_registration_info_invoice($user->params,$form_id);

			$group_registration = "";
			$group_registration = $regpro_registrations_emails->check_groupregistration($user->uid);

			if ($group_registration) {
				$pms['groupregistrationcount'] = count($group_registration);
				$pms['transactiondetails'] = $regpro_registrations_emails->create_groupregistration_info($group_registration);
			}else{
				$pms['transactiondetails'] 	= $regpro_registrations_emails->create_transaction_info($user->uid);
				$pms['groupregistrationcount'] = 1;
			}

			//get email subject and body
			$email_subject = $regpro_config['emailconfirmsubject'];
			$email_body = $regpro_config['emailconfirmbody'];
			$pms = array($pms);

			foreach($pms as $key=>$value){
				foreach($value as $tag=>$tag_value){
					 $email_subject 	= str_replace('{'.$tag.'}',$tag_value,$email_subject);
					 $email_body 	= str_replace('{'.$tag.'}',$tag_value,$email_body);
				}
			}
			if($regpro_config['enablepdf']==1){
				$pdf_text =  JText::_('INVOICE_USER_DETAILS')."<br/><br/>". $pms[$key]['registrationdetails']."<br/><br/>";
				$pdf_text .= JText::_('INVOICE_TRANSACTION_DETAILS')."<br/><br/>".$pms[$key]['transactiondetails'];
				$registrationproHelper->Invoicepdf($pdf_text, $pms[$key]);
				$receipt_attachment = array();
				$receipt_attachment[] = REGPRO_MEDIA_INVOICE_PDF_BASE_PATH.DS.'receipt_'.$pms[$key]['registrationid'].".pdf";
			} else $receipt_attachment = '';

			$regpro_mail1   = JMail::getInstance();
			$regpro_mail1->ClearAllRecipients();
			$regpro_mail1->ClearReplyTos();
			$regpro_mail1->ClearAttachments();
			if($regpro_mail1->sendMail($config->get('mailfrom'), $config->get('fromname'), $user->email, $email_subject, $email_body, 1, '','',$receipt_attachment,'','')){
				$registrationproHelper = new registrationproHelper;
				$registrationproHelper->updateConfirmationEmailStatus($user->uid); // update confirmation email status
			}

			$group_array = array();
			$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id=".$user->user_id;

			$database->setQuery($query);

			$group_id = $database->loadAssocList();
			foreach($group_id as $key=>$val){
				$group_array[$key] = $val['group_id'];
			}
			if((!in_array('7',$group_array))&&(!in_array('8',$group_array))){

			// send notification to fontend user who created the event form his frontend section
			if($user->user_id > 0){
				//get email subject and body
				$admin_email_subject		= $regpro_config['eventadminemailconfirmsubject'];
				$admin_email_message 		= $regpro_config['eventadminemailconfirmbody'];

				//prepare registered users email
				foreach($pms as $key=>$value){
				foreach($value as $tag=>$tag_value){
						$admin_email_subject 	= str_replace('{'.$tag.'}',$tag_value,$admin_email_subject);
						$admin_email_message 	= str_replace('{'.$tag.'}',$tag_value,$admin_email_message);
					}
				}
				
				// get user email id and name from joomla users table
				$query = "SELECT email FROM #__users WHERE id=".$user->user_id;
				$database->setQuery($query);
				$event_user_email = $database->loadResult();

				$regpro_mail2   = JMail::getInstance();
				$regpro_mail2->ClearAllRecipients();
				$regpro_mail2->ClearReplyTos();
				$regpro_mail2->ClearAttachments();
				$tt = $regpro_mail2->sendMail($config->get('mailfrom'), $config->get('fromname'), $event_user_email, $admin_email_subject , $admin_email_message,1);
			}
			}
			//mail to multiple notify emails setup in event settings
			if($user->notifyemails){
				//get email subject and body
				$admin_email_subject		= $regpro_config['eventadminemailconfirmsubject'];
				$admin_email_message 		= $regpro_config['eventadminemailconfirmbody'];

				//prepare registered users email
				foreach($pms as $key=>$value){
				foreach($value as $tag=>$tag_value){
						$admin_email_subject 	= str_replace('{'.$tag.'}',$tag_value,$admin_email_subject);
						$admin_email_message 	= str_replace('{'.$tag.'}',$tag_value,$admin_email_message);
					}
				}
				$arr_emails = explode(",",$user->notifyemails);

				//echo count($arr_emails);
				foreach($arr_emails as $ekey => $evalue)
				{
					$regpro_mail3   = JMail::getInstance();
					$regpro_mail3->ClearAllRecipients();
					$regpro_mail3->ClearReplyTos();
					$regpro_mail3->ClearAttachments();
					$tt = $regpro_mail3->sendMail($config->get('mailfrom'), $config->get('fromname'), $evalue, $admin_email_subject , $admin_email_message,1);
				}
			// mail to admin email setup in config setting of component
			}elseif($regpro_config['register_notify']){

				//get email subject and body
				$admin_email_subject		= $regpro_config['mainadminemailconfirmsubject'];
				$admin_email_message 		= $regpro_config['mainadminemailconfirmbody'];

				//prepare registered users email
				foreach($pms as $key=>$value){
				foreach($value as $tag=>$tag_value){
						$admin_email_subject 	= str_replace('{'.$tag.'}',$tag_value,$admin_email_subject);
						$admin_email_message 	= str_replace('{'.$tag.'}',$tag_value,$admin_email_message);
					}
				}
				$regpro_mail4   = JMail::getInstance();
				$regpro_mail4->ClearAllRecipients();
				$regpro_mail4->ClearReplyTos();
				$regpro_mail4->ClearAttachments();
				$tt = $regpro_mail4->sendMail($config->get('mailfrom'), $config->get('fromname'), $regpro_config['register_notify'], $admin_email_subject , $admin_email_message,1);
			}
			$email_sent[] = $user->email;
		}//die;
	}// end

	// send Status change email notifications to registerd users of event.
	function send_StautsChange_email($eventid)
	{
		global $mainframe,$regpro_mail;

		$database 		= JFactory::getDBO();
		$config			=JFactory::getConfig(); // get config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config(); // get component config settings

		$query = "SELECT r.rid AS uid, e.id AS eid, e.titel, e.status, e.dates, e.times, e.enddates, "
				."\n e.endtimes, e.shortdescription, e.datdescription, e.allowgroup, e.notifyemails, "
				."\n r.firstname, r.lastname, r.email, l.club, l.url, l.city, l.plz, l.street, l.country, l.locdescription FROM"
				."\n #__registrationpro_dates e, #__registrationpro_locate l, #__registrationpro_register r"
				."\n WHERE e.id=r.rdid AND l.id=e.locid AND e.published = 1 AND r.status = 1 AND r.rdid=".$eventid;

		$database->setQuery($query);
		$users = $database->loadObjectList();

		$pms 		= array();
		$email_sent = array();
		$registrationproHelper = new registrationproHelper;

		foreach($users as $user){
			$pms['registrationid'] 	= $user->uid;
			$name 					= $user->firstname." ".$user->lastname;
			$pms['sitename'] 		= $config->get('sitename');
			$pms['eventtitle'] 		= $user->titel;
			$pms['eventstart'] 		=  $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->dates.' '.$user->times);
			$pms['eventend'] 		= $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->enddates.' '.$user->endtimes);
			$pms['shortdesc'] 		= $user->shortdescription;
			$pms['longdesc'] 		= $user->datdescription;
			$pms['eventstatus'] 	= JText::_('ADMIN_EVENTS_STATUS_'. $user->status);
			$pms['location'] 		= $user->club;
			$pms['url'] 			= $user->url;
			$pms['street'] 			= $user->street;
			$pms['zip'] 			= $user->plz;
			$pms['city'] 			= $user->city;
			$pms['country'] 		= $user->country;
			$pms['locdescription'] 	= $user->locdescription;
			$pms['fullname'] 		= $name;
			$pms['email'] 			= $user->email;

			//get email subject and body
			$email_subject 			= $regpro_config['emailstatussubject'];
			$email_body 			= $regpro_config['emailstatusbody'];

			//prepare registered users email
			foreach($pms as $tag=>$tag_value){
				$email_subject 	= str_replace('{'.$tag.'}',$tag_value,$email_subject);
				$email_body 	= str_replace('{'.$tag.'}',$tag_value,$email_body);
			}

			if ($user->allowgroup && $user->allowgroup == 1)  {
				if (!in_array($user->email, $email_sent)) {
					$regpro_mail->ClearAllRecipients();
					$regpro_mail->ClearReplyTos();
					$regpro_mail->ClearAttachments();
					$regpro_mail->sendMail($config->get('mailfrom'), $config->get('fromname'), $user->email, $email_subject, $email_body, 1);
				}
			} else {
				$regpro_mail->ClearAllRecipients();
				$regpro_mail->ClearReplyTos();
				$regpro_mail->ClearAttachments();
				$regpro_mail->sendMail($config->get('mailfrom'), $config->get('fromname'), $user->email, $email_subject, $email_body, 1);
			}
			$email_sent[] = $user->email;
		}
	}// end

	// send event Reminder email notifications to registerd users of event.
	 function send_Reminder_email()
	{
		global $mainframe,$regpro_mail;

		$database 		= JFactory::getDBO();
		$config			=JFactory::getConfig(); // get config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config(); // get component config settings
		$registrationproHelper = new registrationproHelper;
		$current_date	= $registrationproHelper->getCurrent_date("Y-m-d");

		$query = "SELECT e.id AS eid, r.rid as uid, r.firstname, r.lastname, r.email, e.titel, l.club, l.url, l.city, l.plz, l.street, l.country, l.locdescription, "
		 		."\n e.status, e.dates, e.times, e.enddates, e.endtimes, e.shortdescription, e.datdescription FROM"
				."\n #__registrationpro_dates e, #__registrationpro_locate l, #__registrationpro_register r"
				."\n WHERE r.notified=0 AND r.status=1 AND e.id=r.rdid AND l.id=e.locid AND e.published=1 "
				."\n AND '".$current_date."' >= DATE_SUB(e.dates ,INTERVAL e.notifydate DAY) AND e.notifydate != '0' AND e.status!=2 GROUP BY r.rid";

		$database->setQuery($query);
		$users = $database->loadObjectList();

		$pms 		= array();
		$email_sent = array();

		foreach($users as $user){
			$pms['registrationid'] 	= $user->uid;
			$name 					= $user->firstname." ".$user->lastname;
			$pms['sitename'] 		= $config->get('sitename');
			$pms['eventtitle'] 		= $user->titel;
			$pms['eventstart'] 		=  $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->dates.' '.$user->times);
			$pms['eventend'] 		= $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->enddates.' '.$user->endtimes);
			$pms['shortdesc'] 		= $user->shortdescription;
			$pms['longdesc'] 		= $user->datdescription;
			$pms['eventstatus'] 	= JText::_('ADMIN_EVENTS_STATUS_'. $user->status);
			$pms['location'] 		= $user->club;
			$pms['url'] 			= $user->url;
			$pms['street'] 			= $user->street;
			$pms['zip'] 			= $user->plz;
			$pms['city'] 			= $user->city;
			$pms['country'] 		= $user->country;
			$pms['locdescription'] 	= $user->locdescription;
			$pms['fullname'] 		= $name;
			$pms['email'] 			= $user->email;

			//get email subject and body
			$email_subject 			= $regpro_config['emailremindersubject'];
			$email_body 			= $regpro_config['emailreminderbody'];

			//prepare registered users email
			foreach($pms as $tag=>$tag_value){
					$email_subject 	= str_replace('{'.$tag.'}',$tag_value,$email_subject);
					$email_body 	= str_replace('{'.$tag.'}',$tag_value,$email_body);
				}

			if ($user->allowgroup && $user->allowgroup == 1)  {
				if (!in_array($user->email, $email_sent)) {
					$regpro_mail   = JMail::getInstance();
					$regpro_mail->ClearAllRecipients();
					$regpro_mail->ClearReplyTos();
					$regpro_mail->ClearAttachments();
					$regpro_mail->sendMail($config->get('mailfrom'), $config->get('fromname'), $user->email, $email_subject, $email_body, 1);
				}
			} else {
				$regpro_mail   = JMail::getInstance();
				$regpro_mail->ClearAllRecipients();
				$regpro_mail->ClearReplyTos();
				$regpro_mail->ClearAttachments();
				$regpro_mail->sendMail($config->get('mailfrom'), $config->get('fromname'), $user->email, $email_subject, $email_body, 1);
			}

			$email_sent[] = $user->email;

			//update notified
			$query = "UPDATE #__registrationpro_register SET notified=1 WHERE rid = ".$user->uid." AND rdid = ".$user->eid;
			$database->setQuery($query);
			$database->query();
		}
	}// end

	// send moderator email notifications for event created by the frontend user
	function send_Moderator_email($eventid)
	{
		global $mainframe,$regpro_mail;

		$database 		= JFactory::getDBO();
		$config			=JFactory::getConfig(); // get config settings
		$registrationproAdmin = new registrationproAdmin; $regpro_config	= $registrationproAdmin->config(); // get component config settings
		$registrationproHelper = new registrationproHelper;
		$current_date	= $registrationproHelper->getCurrent_date("%Y-%m-%d");

		$query = "SELECT e.id AS eid, u.id as uid, u.name, u.email, e.titel, l.club, l.url, l.city, l.plz, l.street, l.country, l.locdescription, "
		 		."\n e.status, e.dates, e.times, e.enddates, e.endtimes, e.shortdescription, e.datdescription FROM"
				."\n #__registrationpro_dates e, #__registrationpro_locate l, #__registrationpro_usersconfig as uc, #__users as u "
				."\n WHERE e.moderator_notify=0 AND l.id=e.locid AND uc.user_id = e.user_id "
				."\n AND u.id = uc.user_id AND e.id =".$eventid;

		$database->setQuery($query);
		$users = $database->loadObjectList();

		$pms 		= array();
		$email_sent = array();

		foreach($users as $user){
			$pms['registrationid'] 	= $user->uid;
			$name 					= $user->name;
			$pms['sitename'] 		= $config->get('sitename'); //$mosConfig_sitename;
			$pms['eventtitle'] 		= $user->titel;
			$pms['eventstart'] 		= $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->dates.' '.$user->times);
			$pms['eventend'] 		=  $registrationproHelper->getFormatdate($regpro_config['formatdate'].' '.$regpro_config['formattime'], $user->enddates.' '.$user->endtimes);
			$pms['shortdesc'] 		= $user->shortdescription;
			$pms['longdesc'] 		= $user->datdescription;
			$pms['location'] 		= $user->club;
			$pms['url'] 			= $user->url;
			$pms['street'] 			= $user->street;
			$pms['zip'] 			= $user->plz;
			$pms['city'] 			= $user->city;
			$pms['country'] 		= $user->country;
			$pms['locdescription'] 	= $user->locdescription;
			$pms['fullname'] 		= $name;
			$pms['email'] 			= $user->email;

			//get email subject and body
			$email_subject 			= $regpro_config['moderatoremailsubject'];
			$email_body 			= $regpro_config['moderatoremailbody'];

			//prepare registered users email
			foreach($pms as $tag=>$tag_value){
				$email_subject 	= str_replace('{'.$tag.'}',$tag_value,$email_subject);
				$email_body 	= str_replace('{'.$tag.'}',$tag_value,$email_body);
			}

			if($regpro_config['moderatoremail']){
				$regpro_mail   = JMail::getInstance();
				$regpro_mail->ClearAllRecipients();
				$regpro_mail->ClearReplyTos();
				$regpro_mail->ClearAttachments();
				$regpro_mail->sendMail($config->get('mailfrom'), $config->get('fromname'), $regpro_config['moderatoremail'], $email_subject, $email_body, 1);
			}

			//update notified
			$query = "UPDATE #__registrationpro_dates SET moderator_notify=1 WHERE id = ".$eventid;
			$database->setQuery($query);
			$database->query();
		}
	}// end

	// this function is use to create the registration data to send with confirmation email
	function create_registration_info($data)
	{
		$registration_data = "";
		$user_form_data = unserialize($data);

		if($user_form_data){
			$registration_data = "<table border='0'>";
			foreach($user_form_data as $rkey=>$rvalue)
			{
				$registration_data .= "<tr>";
				$registration_data .= "<td>".ucfirst($rkey)."</td>";
				$registration_data .= "<td> &nbsp;-&nbsp; </td>";

				if($rvalue[0][1] == 'F'){
					$registration_data .= "<td> <a href='".REGPRO_FORM_DOCUMENT_URL_PATH."/".$rvalue[0][0]."'>".$rvalue[0][0]."</a></td>";
				}elseif(count($user_form_data[$rkey]) > 1){
					$registration_data .= "<td>";
					foreach($user_form_data[$rkey] as $vkey => $vvalue)
					{
						$registration_data .= $user_form_data[$rkey][$vkey][$vkey].", "; // insert multiple checkboxes etc.
					}
					$registration_data .= "</td>";
				}else{
					$registration_data .= "<td>".$rvalue[0][0]."</td>";
				}
				$registration_data .= "</tr>";
			}
			$registration_data .= "</table>";
		}

		return $registration_data;
	}

	// this function is use to create the registration data in invoices to send with confirmation email
	function create_registration_info_invoice($data,$fid)

	{
		$database 		= JFactory::getDBO();
		$registration_data = "";
		$user_form_data = unserialize($data);

		if($user_form_data){
			$registration_data = "<table border='0'>";
			foreach($user_form_data as $rkey=>$rvalue){
				$query = "SELECT title FROM #__registrationpro_fields WHERE form_id=".$fid." AND name='".$rkey."'";
				$database->setQuery($query);
				$field_title = $database->loadResult();
				$registration_data .= "<tr>";
				$registration_data .= "<td>".ucfirst($field_title)."</td>";
				$registration_data .= "<td> &nbsp;-&nbsp; </td>";

				if($rvalue[0][1] == 'F'){
					$registration_data .= "<td> <a href='".REGPRO_FORM_DOCUMENT_URL_PATH."/".$rvalue[0][0]."'>".$rvalue[0][0]."</a></td>";
				}elseif(count($user_form_data[$rkey]) > 1){
					$registration_data .= "<td>";
					foreach($user_form_data[$rkey] as $vkey => $vvalue){
						$registration_data .= $user_form_data[$rkey][$vkey][$vkey].", "; // insert multiple checkboxes etc.
					}
					$registration_data .= "</td>";
				}else{
					$registration_data .= "<td>".$rvalue[0][0]."</td>";
				}
				$registration_data .= "</tr>";
			}
			$registration_data .= "</table>";
		}
		return $registration_data;
	}

	// this function is use to create the transaction data to send with confirmation email
	function create_transaction_info($user_id = 0)
	{
		$database 		= JFactory::getDBO();
		$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type FROM #__registrationpro_transactions as t "
				 ."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				 ."\n WHERE t.reg_id =".$user_id;

		$database->setQuery($query);
		$user_transaction_data = $database->loadObjectList();

		// get Additional form field fees
		$query = "SELECT * FROM #__registrationpro_additional_from_field_fees WHERE reg_id=".$user_id;
		$database->setQuery($query);
		$user_additional_form_field_fees = $database->loadObjectList();

		// get session fees
		$query = "SELECT * FROM #__registrationpro_session_transactions WHERE reg_id=".$user_id;
		$database->setQuery($query);
		$user_session_fees = $database->loadObjectList();
		$registrationproHelper = new registrationproHelper;

		foreach($user_transaction_data as $tkey => $tvalue)	{
			if(empty($tvalue->price_without_tax) || $tvalue->price_without_tax == 0.00){
				// calculating the acutal amount with help of gorss amount and tax percentage
				if(!empty($tvalue->price)){
					$productprice = (100 * $tvalue->price) / (100 + $tvalue->tax);
					$tvalue->price_without_tax = $productprice;
				}
			}
		}

		// apply event discount upon user tickets
		foreach($user_transaction_data as $tkey => $tvalue)
		{
			if($tvalue->event_discount_amount > 0){
				if($tvalue->event_discount_type == 'P'){
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$actual_price_without_per 		= ($tvalue->price * 100) / (100 - $tvalue->event_discount_amount);
					$event_discounted_amount_price 	= $actual_price_without_per * $tvalue->event_discount_amount / 100;
					$tvalue->discount_amount 		+= $event_discounted_amount_price;
					$tvalue->price 					= $actual_price_without_per;
				}else{
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$event_discounted_amount_price 	= $tvalue->event_discount_amount;
					$tvalue->discount_amount 		+= $event_discounted_amount_price;
				}
			}
		}
		$table_style = 'style="border-spacing:1px; border-width:1px 1px 1px 1px; border-style:none; border-color:#cccccc; border-collapse:separate; background-color:#cccccc; color:#000000;"';
		$th_style = 'style="padding:1px; background-color:#cccccc; font-family:Arial,Georgia,Serif; color:#000000;"';
		$td_style = 'style="padding:2px; background-color:#ffffff; font-family:Arial,Georgia,Serif; color:#000000;"';

		$transaction_data = "";

		$transaction_data = '
		<table cellpadding="0" cellspacing="0" width="100%" align="left">
		<tr>
		<td style="text-align:left">
		<table cellpadding="4" cellspacing="0" width="100%" '.$table_style.'>

			<tr>
				<th width="30%" align="left" '.$th_style.'>'.$user_transaction_data[0]->payment_date.'</th>
				<th width="70%" align="right" '.$th_style.'>'.JText::_('REGPRO_TRANSACTION_PAYMENT_METHOD').' : '.$user_transaction_data[0]->payment_method.'</th>
			</tr>

			<tr>
				<td colspan="2" '.$td_style.'>';
						if($user_transaction_data[0]->payment_method == 'payoffline')
						{
							$transaction_data .= '<table border="0" width="100%" '.$table_style.'>';

							if($user_transaction_data[0]->payer_email){
								$transaction_data .= "<tr><td ".$td_style."><b>".JText::_('TRANS_PAYER_EMAIL')."</b> :</td>";
								$transaction_data .= "<td ".$td_style."> <a href='mailto:".$user_transaction_data[0]->payer_email."'>".$user_transaction_data[0]->payer_email."</a></td></tr>";
							}

							$transaction_data .= '<tr><td valign="top" width="30%" '.$td_style.'>';
							$transaction_data .= "<b>".JText::_('REGPRO_OFFLINE_INSTRUCTIONS')." </b></td>";
							$transaction_data .= "<td ".$td_style.">".$user_transaction_data[0]->offline_payment_details."</td></tr>";
							$transaction_data .= "</table><br/>";

						}else{
							$transaction_data .= '<table border="0" width="100%" cellspacing="0" cellpadding="0" '.$table_style.'>';

							if($user_transaction_data[0]->payment_status){
								$transaction_data .= "<tr><td width='20%' ".$td_style."><b>".JText::_('TRANS_PAYMENT_STATUS')."</b> :</td>";
								$transaction_data .= "<td ".$td_style.">".$user_transaction_data[0]->payment_status."</td></tr>";
							}
							if($user_transaction_data[0]->txn_id){
								$transaction_data .= "<tr><td ".$td_style."><b>".JText::_('TRANS_TXN_ID')."</b> :</td>";
								$transaction_data .= "<td ".$td_style.">".$user_transaction_data[0]->txn_id."</td></tr>";
							}
							$transaction_data .= "</table>";
						}

		$transaction_data .='
				</td>
			</tr>
			<tr>
				<td colspan="2" '.$td_style.'>
					<table cellpadding="2" cellspacing="0" width="100%" '.$table_style.'>

						<tr>
							<td '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_NAME').'</b></td>
							<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_DISCOUNT').'</b></td>
							<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_PRICE').'</b></td>
							<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_TAX').'</b></td>
							<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_PRICE').'</b></td>
						</tr>';

					foreach($user_transaction_data as $key=>$value){

						$price_wihout_tax =  $registrationproHelper->GetTicketPriceWithoutTax($user_transaction_data[$key]->price, $user_transaction_data[$key]->tax);

						$transaction_data .= '<tr><td '.$td_style.'>'.$user_transaction_data[$key]->item_name.'</td>';
						$transaction_data .= '<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency." ".number_format($user_transaction_data[$key]->discount_amount,2).'</td>';
						$transaction_data .= '<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency." ".number_format($user_transaction_data[$key]->price_without_tax,2).'</td>';
						$transaction_data .= '<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency." ".number_format($user_transaction_data[$key]->tax_amount,2).'</td>';
						$transaction_data .= '<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency." ".number_format($user_transaction_data[$key]->price,2).'</td></tr>';
						$subtotal += $user_transaction_data[$key]->price;
						$discount += $user_transaction_data[$key]->discount_amount;
					}

					// Additional form field fees
					if(is_array($user_additional_form_field_fees) && count($user_additional_form_field_fees) > 0) {
						foreach($user_additional_form_field_fees as $akey=>$avalue){
							$transaction_data .= '<tr><td>'.$avalue->additional_field_name.'</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency.'0.00</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency." ".number_format($avalue->additional_field_fees,2).'</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency.'0.00</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency." ".number_format($avalue->additional_field_fees,2).'</td></tr>';

							$subtotal += $avalue->additional_field_fees;
						}
					}

					// Session fees
					if(is_array($user_session_fees) && count($user_session_fees) > 0) {
						foreach($user_session_fees as $skey=>$svalue){
							$transaction_data .= '<tr><td>'.$svalue->sessionname.'</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency.'0.00</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency." ".number_format($svalue->session_fees,2).'</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency.'0.00</td>';
							$transaction_data .= '<td align="right">'.$user_transaction_data[0]->mc_currency." ".number_format($svalue->session_fees,2).'</td></tr>';

							$subtotal += $svalue->session_fees;
						}
					}
					if(!empty($user_transaction_data[0]->AdminDiscount))
					{
						$adminDiscount = $user_transaction_data[0]->AdminDiscount;
					}else{
						$adminDiscount = 0;
					}
					$total = $subtotal - $discount - $adminDiscount;

					if($total <= 0){
						$total = 0;
					}

					$transaction_data .='
					<tr>
						<td colspan="4" align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_SUB_TOTAL').'</b></td>
						<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency.' '.number_format($subtotal,2).'</td>
					 </tr>
					<tr>
						<td colspan="4" align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_DISCOUNT').'</b></td>
						<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency.' '.number_format($discount,2).'</td>
					</tr>';
					if($adminDiscount != 0)
					{
						$transaction_data .='<tr>
						<td colspan="4" align="right" '.$td_style.'><b>'.JText::_('COM_REGISTRATIONPRO_ADMIN_DISCOUNT_LABEL').'</b></td>
						<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency.' '.number_format($adminDiscount,2).'</td>
					</tr>';
					}
					$transaction_data .='<tr>
						<td colspan="4" align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_FINAL_PRICE').'</b></td>
						<td align="right" '.$td_style.'>'.$user_transaction_data[0]->mc_currency.' '.number_format($total,2).'</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
		</tr>
		</table>';

		return $transaction_data;
	}

	function create_groupregistration_info($group_registration = array())
	{
		$database 		= JFactory::getDBO();

		if(count($group_registration) > 0) {

			$counter = 0;
			$transaction_data = "";
			$user_transaction_data = array();

			// get registration id in single array
			$registration_ids = array();
			foreach($group_registration as $key => $value) array_push($registration_ids,$value->rid);

			// record to display the general details like (paymet method, payment date etc..)
			$query = "SELECT * FROM #__registrationpro_transactions as t WHERE t.p_type = 'E' AND t.reg_id in (".implode(",",$registration_ids).") GROUP BY t.p_id LIMIT 1";
			$database->setQuery($query);
			$main_record = $database->loadObjectList();

			$query = "SELECT t.id FROM #__registrationpro_transactions as t WHERE t.reg_id in (".implode(",",$registration_ids).") GROUP BY t.p_id";
			$database->setQuery($query);
			$transaction_id = $database->loadAssocList();
			$transaction_id= implode(",",$transaction_id[0]);

			$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type FROM #__registrationpro_transactions as t "
					 ."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
					 ."\n WHERE t.id in (".$transaction_id.")";

			$database->setQuery($query);
			$temp_user_transaction_data = $database->loadObjectList();

			foreach($temp_user_transaction_data as $tkey => $tvalue)
			{
				if($tvalue->event_discount_amount > 0){
					if($tvalue->event_discount_type == 'P'){
						$event_discounted_amount_price 	= 0;
						$actual_price_without_per 		= 0;
						$actual_price_without_per 		= ($tvalue->price * 100) / (100 - $tvalue->event_discount_amount);
						$event_discounted_amount_price 	= $actual_price_without_per * $tvalue->event_discount_amount / 100;
						$tvalue->discount_amount 		+= $event_discounted_amount_price;
						$tvalue->price 					= $actual_price_without_per;
					}else{
						$event_discounted_amount_price 	= 0;
						$actual_price_without_per 		= 0;
						$event_discounted_amount_price 	= $tvalue->event_discount_amount;
						$tvalue->discount_amount 		+= $event_discounted_amount_price;
					}
				}
			}

			foreach($main_record as $mkey => $mvalue)
			{
				foreach($temp_user_transaction_data as $tkey => $tvalue)
				{
					$mvalue->records[$counter] =  $temp_user_transaction_data[$tkey];
					$counter++;
				}
			}
			$user_transaction_data = $main_record;

			$table_style = 'style="border-spacing:1px; border-width:1px 1px 1px 1px; border-style:none; border-color:#cccccc; border-collapse:separate; background-color:#cccccc; color:#000000;"';
			$th_style = 'style="padding:1px; background-color:#cccccc; font-family:Arial,Georgia,Serif; color:#000000;"';
			$td_style = 'style="padding:2px; background-color:#ffffff; font-family:Arial,Georgia,Serif; color:#000000;"';

			$transaction_data .= '
			<table cellpadding="0" cellspacing="0" width="100%" align="left">
			<tr>
			<td style="text-align:left">
			<table cellpadding="4" cellspacing="0" width="100%" '.$table_style.'>

				<tr>
					<th width="30%" align="left" '.$th_style.'>'.$user_transaction_data[0]->payment_date.'</th>
					<th width="70%" align="right" '.$th_style.'>'.JText::_('REGPRO_TRANSACTION_PAYMENT_METHOD').' : '.$user_transaction_data[0]->payment_method.'</th>
				</tr>

				<tr>
					<td colspan="2" '.$td_style.'>';
							if($user_transaction_data[0]->payment_method == 'payoffline')
							{
								$transaction_data .= '<table border="0" width="100%" '.$table_style.'>';

								if($user_transaction_data[0]->payer_email){
									$transaction_data .= "<tr><td ".$td_style."><b>".JText::_('TRANS_PAYER_EMAIL')."</b> :</td>";
									$transaction_data .= "<td ".$td_style."> <a href='mailto:".$user_transaction_data[0]->payer_email."'>".$user_transaction_data[0]->payer_email."</a></td></tr>";
								}

								$transaction_data .= '<tr><td valign="top" width="30%" '.$td_style.'>';
								$transaction_data .= "<b>".JText::_('REGPRO_OFFLINE_INSTRUCTIONS')." </b></td>";
								$transaction_data .= "<td ".$td_style.">".$user_transaction_data[0]->offline_payment_details."</td></tr>";
								$transaction_data .= "</table><br/>";

							}else{
								$transaction_data .= '<table border="0" width="100%" cellspacing="0" cellpadding="0" '.$table_style.'>';

								if($user_transaction_data[0]->payment_status){
									$transaction_data .= "<tr><td width='20%' ".$td_style."><b>".JText::_('TRANS_PAYMENT_STATUS')."</b> :</td>";
									$transaction_data .= "<td ".$td_style.">".$user_transaction_data[0]->payment_status."</td></tr>";
								}
								if($user_transaction_data[0]->txn_id){
									$transaction_data .= "<tr><td ".$td_style."><b>".JText::_('TRANS_TXN_ID')."</b> :</td>";
									$transaction_data .= "<td ".$td_style.">".$user_transaction_data[0]->txn_id."</td></tr>";
								}
								$transaction_data .= "</table>";
							}

			$transaction_data .='
					</td>
				</tr>
				<tr>
					<td colspan="2" '.$td_style.'>
						<table cellpadding="2" cellspacing="0" width="100%" '.$table_style.'>

							<tr>
								<td '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_NAME').'</b></td>
								<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_DISCOUNT').'</b></td>
								<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_PRICE').'</b></td>
								<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_TAX').'</b></td>
								<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_QTY').'</b></td>
								<td align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_PRICE').'</b></td>
							</tr>';

						$registrationproHelper = new registrationproHelper;
						foreach($user_transaction_data[0]->records as $key=>$value){

							$price_wihout_tax = $registrationproHelper->GetTicketPriceWithoutTax($value->price, $value->tax);

							$trans_discount = $value->discount_amount * $value->quantity;
							$transaction_data .= '<tr><td '.$td_style.'>'.$value->item_name.'</td>';
							$transaction_data .= '<td align="right" '.$td_style.'>'.$value->mc_currency." ".number_format($trans_discount,2).'</td>';
							$transaction_data .= '<td align="right" '.$td_style.'>'.$value->mc_currency." ".number_format($price_wihout_tax,2).'</td>';
							$transaction_data .= '<td align="right" '.$td_style.'>'.$value->mc_currency." ".number_format($value->tax_amount,2).'</td>';
							$transaction_data .= '<td align="right" '.$td_style.'>'.$value->quantity.'</td>';
							$transaction_data .= '<td align="right" '.$td_style.'>'.$value->mc_currency." ".number_format($value->price,2).'</td></tr>';
							$subtotal += $value->price * $value->quantity;
							$discount += $trans_discount;
						}

						$total = $subtotal - $discount;

						if($total <= 0){
							$total = 0;
						}

						$transaction_data .='
						<tr>
							<td colspan="5" align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_SUB_TOTAL').'</b></td>
							<td align="right" '.$td_style.'>'.$value->mc_currency.' '.number_format($subtotal,2).'</td>
						 </tr>
						<tr>
							<td colspan="5" align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_DISCOUNT').'</b></td>
							<td align="right" '.$td_style.'>'.$value->mc_currency.' '.number_format($discount,2).'</td>
						</tr>
						<tr>
							<td colspan="5" align="right" '.$td_style.'><b>'.JText::_('REGPRO_TRANSACTION_PRODUCT_FINAL_PRICE').'</b></td>
							<td align="right" '.$td_style.'>'.$value->mc_currency.' '.number_format($total,2).'</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
			</td>
			</tr>
			</table>';

			return $transaction_data;
		}
	}

	function check_groupregistration($user_id = 0)
	{
		$database 		= JFactory::getDBO();

		$group_users = array();
		@$group_users[0]->rid = $user_id;

		$query = "SELECT rid FROM #__registrationpro_register WHERE group_added_by =".$user_id;
		$database->setQuery($query);
		$group_users_data = $database->loadObjectList();

		if(count($group_users_data) > 0){
			$final_array = array_merge($group_users, $group_users_data);
			return $final_array;
		} else return false;
	}

}
?>