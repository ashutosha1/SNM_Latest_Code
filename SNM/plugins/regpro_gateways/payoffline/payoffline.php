<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.menu');
jimport( 'joomla.html.parameter' );

class plgRegpro_gatewaysPayoffline extends JPlugin
{

	var $_db = null;
	function plgRegpro_gatewaysPayoffline(& $subject, $config)
	{
		$this->_db = JFactory :: getDBO();
		parent :: __construct($subject, $config);
	}
	function onReceivePayment( &$post )
	{
		global $mainframe;
		
		//echo "<pre>"; print_r($post); exit;
						    
		if($post['processor'] != 'payoffline') return 0;
		
		$user   			= & JFactory::getUser();
        $config 			= & JFactory::getConfig();
        
		$params 			= new JRegistry( $post['params'] );
		$default 			= $this->params;

		$out['sid']        	= $post['order_id'];
		$out['gateway']    	= $post['processor'];
		$out['gateway_id'] 	= $post['user_id'];
		$out['user_id']    	= $post['user_id'];
		$out['price']      	= $post['order_amount'];
		$out['pay']        	= $post['pay'];
		$out['email']      	= $post['email'];
		
		
		// update the user status to accpeted according to config setting
		if($post['regproConfig']['default_userstatus_offline_payment'] == 1){
			$query 	= "UPDATE #__registrationpro_register set status = 1 where rid in (".$out['sid'].")";
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		//
		
		// Insert offline payment details to transaction table
		$query =  "UPDATE #__registrationpro_transactions set offline_payment_details = '".$default->get('payoffline_body')."' where reg_id in (".$out['sid'].")";
		$query = "UPDATE `#__registrationpro_transactions` SET
					`offline_payment_details`	= '".$default->get('payoffline_body')."'
					 WHERE `reg_id` in (".$out['sid'].") AND `accesskey` ='".$out['user_id']."'";
		
		$this->_db->setQuery($query);
		$this->_db->query();
		// end
		
		
		// get event id
			$this->_db->setQuery("SELECT DISTINCT(rdid) FROM #__registrationpro_register WHERE rid in (".$out['sid'].")");
			$eventids = $this->_db->loadObjectList();			
			//echo "<pre>"; print_r($eventids); exit;			
		//end
		$regpro_registrations_emails = new regpro_registrations_emails;
		foreach($eventids as $ekey => $evalue)
		{
			// create mail to send it to registered users	
			$users = $regpro_registrations_emails->getEventForEmailTemplate($evalue->rdid, $out['sid']); // get registered user to whom sending emails
			//echo "<pre>"; print_r($users); exit;
			$regpro_registrations_emails->send_registration_email($users);
			// end
		}		
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->updateConfirmationEmailStatus($out['sid']); // update confirmation email status
									
		return $out;
	}
	
	function onSendPayment( &$post )
	{
		global $mainframe;
		
		$Itemid = @JRequest::getVar('Itemid');
		
		$params = $this->params;					

		if( $post['processor'] != 'payoffline' ) return false;
		
		$param['email']			= $post['email'];
		$param['option']    	= JRequest::getCmd('option');
		$param['controller']	= @$post['controller'];
		$param['task']   		= @$post['task'];		
		$param['processor'] 	= @$post['processor'];
		$param['user_id'] 		= @$post['user_id'];
		$param['order_id'] 		= @$post['order_id'];
		$param['order_amount']  = @floatval($post['order_amount']);
		$param['Itemid']  		= @$Itemid;
		$param['pay']  			= "pending";
		
		$param['offline_details'] = $params->get('payoffline_body');
		
		//echo "<pre>"; print_r($param); exit;														

		if( !isset( $post['order_amount'] ) || !$param['offline_details'] || !$param['processor'] || !$param['order_id'] ) {
			$mainframe->enqueueMessage( 'Some of the required information is missed for completing transaction<br>' );
			return;
		}
		
		$url = 'index.php?'.$this->jcsArray2Url( $param );
		$mainframe->redirect( $url );
	}
	
	function jcsArray2Url( $post )
	{
		foreach( $post AS $k => $v ) {
			$out[] = "$k=$v";
		}
		return implode( '&', $out );
	}
}
?>