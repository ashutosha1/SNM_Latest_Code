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

jimport( 'joomla.application.component.view' );

class registrationproViewUser extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
		$registrationproHelper = new registrationproHelper;
		if(!$registrationproHelper->checkUserAccount()) {
			$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid, false);		
			$mainframe->redirect($link);
		}
				
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		JHTML::_('behavior.modal', 'a.modal');
		
		$layout   	= JRequest::getCmd('layout');
		
		if($layout == 'email'){		
			$this->send_emails();
		}elseif($layout == 'transaction'){		
			$this->transaction_detials();
		}else{	
			//initialise variables
			$editor 		=  JFactory::getEditor();
			$user 			=  JFactory::getUser();
			$db 			=  JFactory::getDBO();
			$document		=  JFactory::getDocument();
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	=  $registrationproAdmin->config();
			$regpro_config['joomlabase'] = JPATH_SITE;
					
			//echo "<pre>"; print_r($regpro_config); exit;					
			//get vars
			$cid 			= JRequest::getInt( 'cid' );
			$task 			= JRequest::getVar( 'task' );
			$layout     	= JRequest::getCmd('layout');		
			
			//add css and js to document
			$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());		
							
			// Get data from the model
			$model	=  $this->getModel('user');
															
			$row    =  $model->getData();
			//echo "<pre>"; print_r($row); exit;	
			
			// take backup of orignal values of user
			$model->orignal_values_backup($row); 
			
			// get the user values for params field
			$user_values =	$model->get_user_params_values($row);
											
			//assign data to template
			$this->assignRef('row' , $row);
			$this->assignRef('event_id' , $row->rdid);
			$this->assignRef('user_values' , $user_values);								
			$this->assignRef('template'	, $template);
			$this->assignRef('editor' , $editor);
			$this->assignRef('regpro_config' , $regpro_config);
			$this->assignRef('task' , $task);		
			
			parent::display($tpl);
		}
	}
	
	function transaction_detials()
	{
		global $mainframe;
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		JHTML::_('behavior.modal', 'a.modal');

		//initialise variables
		$editor 		=  JFactory::getEditor();
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	=  $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($regpro_config); exit;
				
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		$layout     	= JRequest::getCmd('layout');		
		$registrationproHelper = new registrationproHelper;
		//add css and js to document
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());		
						
		// Get data from the model
		$model	=  $this->getModel('user');
		
		$user_id = JRequest::getInt('rid');
		
		// get user transaction details	
		$user_details  	= $model->getTransaction_details($user_id);
		//echo "<pre>"; print_r($user_details); exit;
		
		// apply event discount upon user tickets
		foreach($user_details->transaction as $tkey => $tvalue)
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
					//$actual_price_without_per 	= ($tvalue->price * 100) / (100 - $tvalue->event_discount_amount);				
					//$actual_price_without_per 		= $tvalue->price + $tvalue->event_discount_amount;				
					$event_discounted_amount_price 	= $tvalue->event_discount_amount;					
					$tvalue->discount_amount 		+= $event_discounted_amount_price;
					//$tvalue->price 				= $actual_price_without_per;
				}
			}
		}
		// end
		
		//echo "<pre>"; print_r($user_details); exit;
		
		$this->assignRef('details' , $user_details);		
	
		$this->assignRef('template'	, $template);
		$this->assignRef('editor' , $editor);
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('task' , $task);
		
		parent::display($tpl);
		exit;
	}
	
	function send_emails()
	{
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		JHTML::_('behavior.modal', 'a.modal');

		//initialise variables
		$editor 		=  JFactory::getEditor();
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	=  $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($regpro_config); exit;
				
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		$layout     	= JRequest::getCmd('layout');		
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_scripts();		
						
		// Get data from the model
		$model	=  $this->getModel('user');
		
		$email_flag		= JRequest::getVar( 'email_flag');
		$user_ids		= JRequest::getVar( 'cid',0,'','int');
		$event_id		= JRequest::getVar( 'eventid',0,'','int');
				
		$emailids = $model->getEmails($event_id, $user_ids, $email_flag);	// get all email ids of registered users				
		
		$this->assignRef('emailids' , $emailids);
		$this->assignRef('email_to' , $email_flag);	
		$this->assignRef('event_id' , $event_id);		
		$this->assignRef('template'	, $template);
		$this->assignRef('editor' , $editor);
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('task' , $task);		
		
		parent::display($tpl);
	}
}
?>