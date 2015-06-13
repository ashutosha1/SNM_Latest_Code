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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class registrationproControllerUsers extends registrationproController {

	function __construct()
	{
		parent::__construct();		
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );
	}		
	
	function cancel()
	{		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );		
		$this->setRedirect( 'index.php?option=com_registrationpro&view=events');
		
		//$rdid 	= JRequest::getVar( 'rdid', 0);		
		//$this->setRedirect( 'index.php?option=com_registrationpro&view=users&rdid='.$rdid);
	}
	
	function delete()
	{		
		$model 	= $this->getModel('users');
		$user	= JFactory::getUser();
						
		parent::display();
	}
	
	function remove()
	{
		global $option;

		$rdid 	= JRequest::getVar( 'rdid', 0);

		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('users');

		$msg = $model->delete($cid).' '.JText::_('ADMIN_REGISTER_DEL');;

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=users&rdid='.$rdid, $msg );
	}				

	function edit( )
	{	
		JRequest::setVar( 'view', 'user' );
		JRequest::setVar( 'hidemainmenu', 1 );

		$user	= JFactory::getUser();
						
		parent::display();
	}
	
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	= $registrationproAdmin->config();
		$task			= JRequest::getVar('task');
		$post 			= JRequest::get( 'post', JREQUEST_ALLOWRAW);		
		$model 			= $this->getModel('user');
		
		// check duplicate email id If duplicate email is not allowed in registrations
		if($regpro_config['duplicate_email_registration']){
			$email_flag	= 0;
		}else{
			$email_flag = $model->check_duplicate_email($post);
		}
		
		// update record		
		if (!$email_flag) {
			
			$model->store($post); // update user info
			
			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option=com_registrationpro&controller=users&view=user&hidemainmenu=1&rcid[]='.$post['rid'].'&rdid='.$post['rdid'];
					break;

				default :
					$link = 'index.php?option=com_registrationpro&view=users&rdid='.$post['rdid'];
					break;
			}
			$msg	= JText::_('ADMIN_MSG_USER_INFO_UPDATED');

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		} else {
			$msg 	= JText::_('ADMIN_MSG_EMAIL_ALREADY_EXISTS');
			$link = 'index.php?option=com_registrationpro&view=users&hidemainmenu=1&cid[]='.$post['rid'].'&rdid='.$post['rdid'];
		}
		$this->setRedirect( $link, $msg );								
 	}	
	
	
	function user_cancel()
	{
		JRequest::checkToken() or die( 'Invalid Token' );		
		
		$rdid 	= JRequest::getVar( 'rdid', 0);		
		$this->setRedirect( 'index.php?option=com_registrationpro&view=users&rdid='.$rdid);
	}
	
	function pending_user()
	{
		global $Itemid;
		
		$rdid 	= JRequest::getVar( 'rdid', 0);
		
		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('users');

		if($model->changeStatus($cid, 0, $rdid))
			$msg = $total.JText::_('ADMIN_REGISTER_PENDING');
		else
			$msg = "Error";

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=users&Itemid='.$Itemid.'&rdid='.$rdid, $msg );		
	}
	
	function accept_user()
	{
		global $Itemid;
		
		$rdid 	= JRequest::getVar( 'rdid', 0);
		
		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('users');

		if($model->changeStatus($cid, 1, $rdid)){
			$msg = $total.JText::_('ADMIN_REGISTER_ACCEPTED');
			
			// rys: call back to plugin event	
			$argu['rdid'] = $rdid;
			$argu['cid'] = $cid;	
			$regpro_api = new regpro_api;			
			$api = $regpro_api->getInstance();
			$api -> triggerEvent('onRegistrationAccepted', $argu );
			// rys end
		}else{
			$msg = "Error";
		}
		
		
		######## send confirmation email if manually accepted by admin  ########
		$userlist = $model->getConfimationEmailStatus($cid, $rdid);
		$user_ids = implode(",",$userlist);
		$regpro_registrations_emails = new regpro_registrations_emails;
		$registrationproHelper = new registrationproHelper;
		if($user_ids){		
			// create mail to send the registered users of event
			$users = $regpro_registrations_emails->getEventForEmailTemplate($rdid, $user_ids); // get registered user to whom sending emails
			$regpro_registrations_emails->send_registration_email($users);						
			$registrationproHelper->updateConfirmationEmailStatus($user_ids);
			// end
		}						
		######## end ########
			

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=users&Itemid='.$Itemid.'&rdid='.$rdid, $msg );		
	}
			
	function waiting_user()
	{
		global $Itemid;
	
		$rdid 	= JRequest::getVar( 'rdid', 0);
		
		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('users');

		if($model->changeStatus($cid, 2, $rdid))
			$msg = $total.JText::_('ADMIN_REGISTER_WAITING');
		else
			$msg = "Error";

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=users&Itemid='.$Itemid.'&rdid='.$rdid, $msg );		
	}	
	
	function email_to_all()
	{
		JRequest::setVar( 'view', 'user' );		
		
		$rdid 	= JRequest::getVar( 'rdid', 0);										
		
		JRequest::setVar( 'layout', 'email');		
		JRequest::setVar( 'email_flag', 'A');		
		JRequest::setVar( 'eventid', $rdid);
		
		parent::display();									
	}
	
	function email_to_selected()
	{
		JRequest::setVar( 'view', 'user' );
		
		$rdid 	= JRequest::getVar( 'rdid', 0);
		
		$cid 	= JRequest::getVar( 'rcid', array(0), 'post', 'array' );		
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Please select the record first.' ) );
		}
								
		JRequest::setVar( 'layout', 'email');
		
		JRequest::setVar( 'email_flag', 'S');		
		JRequest::setVar( 'cid', $cid);
		JRequest::setVar( 'eventid', $rdid);
		
		parent::display();			
	}
	
	function send_email_to_user()
	{	
		global $Itemid, $regpro_mail;
			
		$config = JFactory::getConfig(); // get config settings
		
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
						
		$rdid = $post['rdid'];						
		
		if(is_array($post['emailIds'])){			
			$emailtoregistersubject = $post['emailtoregistersubject'];			
			$emailtoregisterbody	= $post['emailtoregisterbody'];
				
			$EmailIds 				= $_POST['emailIds'];
			foreach($EmailIds as $key=>$value){			
				if(!$regpro_mail->sendMail($config->getValue('config.mailfrom'), $config->getValue('config.fromname'), $value, $emailtoregistersubject, $emailtoregisterbody, 1)){
					$msg = "Email has not been sent, Please try again.";					
				}
			}			
			
			$model = $this->getModel('user');
			
			// update email subject and body in database
			$model->update_email_template($emailtoregistersubject, $emailtoregisterbody);
																
			$msg 	= "Email has been sent successfully";									
		}else{						
			$msg 	= "No Email ID to send email.";			
		}

		$link 	= "index.php?option=com_registrationpro&view=users&Itemid=".$Itemid."&rdid=".$rdid;
		$this->setRedirect($link, $msg );			
	}
	
	
	function transaction_details()
	{
		//echo "<pre>"; print_r($_REQUEST); exit;
		JRequest::setVar( 'view', 'user' );
		
		$rid 	= JRequest::getInt('rid');
		
		JRequest::setVar( 'layout', 'transaction');		
		
		JRequest::setVar( 'rid', $rid);
		
		parent::display();
		exit;		
	}
	
	function changepaymentstatus ()
	{
		global $Itemid;
		
		$config = JFactory::getConfig(); // get config settings		
		
		$rdid 	= JRequest::getVar( 'rdid', 0);
		$payment_status = JRequest::getVar( 'payment_status', 0);
		
		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		
		//echo "<pre>"; print_r($_REQUEST); exit;
		
		$model = $this->getModel('users');

		if($model->changePaymentStatus($cid, $payment_status, $rdid))
			$msg = $total.JText::_('ADMIN_PAYMENT_STATUS_CHANGED');
		else
			$msg = "Error";
		
		$link 	= "index.php?option=com_registrationpro&view=users&Itemid=".$Itemid."&rdid=".$rdid;
		$this->setRedirect($link, $msg );
	}
	
	function pending_paymentstatus ()
	{
		global $Itemid;
		
		$config = JFactory::getConfig(); // get config settings		
		
		$rdid 	= JRequest::getVar( 'rdid', 0);
						
		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		
		//echo "<pre>"; print_r($_REQUEST); exit;
		
		$model = $this->getModel('users');

		if($model->changePaymentStatus($cid, 1, $rdid))
			$msg = $total.JText::_('ADMIN_PAYMENT_STATUS_CHANGED');
		else
			$msg = "Error";
		
		$link 	= "index.php?option=com_registrationpro&view=users&Itemid=".$Itemid."&rdid=".$rdid;
		$this->setRedirect($link, $msg );
	}	
	
	function completed_paymentstatus ()
	{
		global $Itemid;
		
		$config = JFactory::getConfig(); // get config settings		
		
		$rdid 	= JRequest::getVar( 'rdid', 0);
						
		$cid = JRequest::getVar( 'rcid', array(0), 'post', 'array' );
		$total = count( $cid );
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		
		//echo "<pre>"; print_r($_REQUEST); exit;
		
		$model = $this->getModel('users');

		if($model->changePaymentStatus($cid, 0, $rdid))
			$msg = $total.JText::_('ADMIN_PAYMENT_STATUS_CHANGED');
		else
			$msg = "Error";
		
		$link 	= "index.php?option=com_registrationpro&view=users&Itemid=".$Itemid."&rdid=".$rdid;
		$this->setRedirect($link, $msg );
	}	
}
?>
