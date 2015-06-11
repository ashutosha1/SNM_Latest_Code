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

class registrationproViewNewuser extends JViewLegacy
{		
	function display($tpl = null){
		global $mainframe, $Itemid;
		
		$document	=JFactory::getDocument();
						
		$layout = JRequest::getCmd('layout');
		
		if($layout=='session'){
			$this->event_session(); // show event sessions
		}else{
															
			// get component config settings
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	=  $registrationproAdmin->config();
			$my 			=JFactory::getUser();
							
			//add css and js to document
			//$registrationproHelper->add_regpro_frontend_scripts();
					
			$model 		= $this->getModel('newuser');
			$row 		= $model->getEvent();
			
			//echo "<pre>"; print_r($row); exit;		
	
			//if event is free, skip payment step		
			$has_payments = $model->is_event_free($row->did);	
	
			//$row = $this->lookForErrors($row, $regpro_config);
			//echo "<pre>"; print_r($row); exit;
			$nothing = array();
			$row->message = "";
			$registrationproHelper = new registrationproHelper;
			$row->message .= $registrationproHelper->check_max_attendance($row, $nothing, $nothing, $regpro_config, 1);	
			//$row->message .= $registrationproHelper->check_max_attendance($row, 0, $regpro_config, 1);		
			$row->message .= $registrationproHelper->check_event_registration_enable($row, $regpro_config, 1);
			$row->message .= $registrationproHelper->check_event_registration_date($row, $regpro_config, 1);
					
			// get event tickets
			$tickets = $model->getEventTickets($row->did);
			
			if(empty($tickets)){
				$tickets[0] = new stdClass();
				$tickets[0]->id = 0;
				$tickets[0]->regpro_dates_id = $row->did;
				$tickets[0]->product_name = JText::_('ADMIN_EVENTS_DEFAULT_PRODUCT');
				$tickets[0]->product_description = '';
				$tickets[0]->total_price = 0;
				$tickets[0]->shipping = 0;
				$tickets[0]->type = 'E';
			}	
			
			//echo "<pre>"; print_r($tickets); exit;
											
			//$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=event&task=cart");
			//$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart&hidemainmenu=1");
			
			// check if session exists with event
			$event_sessions = $model->getEventSession();
			
			if(count($event_sessions) > 0) {			
				$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&view=newuser&layout=session&hidemainmenu=1");
			}else{
				$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart&hidemainmenu=1");
			}
			
																																													
			$this->assignRef('action', $action);
			$this->assignRef('tickets', $tickets);
			$this->assignRef('row', $row);
			$this->assignRef('my', $my);
			$this->assignRef('regproConfig', $regpro_config);
			$this->assignRef('Itemid', $Itemid);
			
			parent::display($tpl);
		}
	}
		
	function event_session()
	{
		global $mainframe, $Itemid;
		
		global $mainframe, $Itemid;
						
		$model 					= $this->getModel('newuser');		
		$event_sessions 		= $model->getEventSession();
		$event_session_dates	= $model->getEventSessionDates();
		$event_session_header 	= $model->getEventSessionHeader();	
		
		if(count($event_sessions) > 0) {
			/*JRequest::setVar( 'view', 'event' );
			JRequest::setVar( 'layout', 'session');
			JRequest::setVar( 'event_sessions', $event_sessions);
			JRequest::setVar( 'event_session_dates', $event_session_dates);
			JRequest::setVar( 'event_session_header', $event_session_header);*/	
						
		
			//add css and js to document
			//$registrationproHelper->add_regpro_frontend_scripts();
			//$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
			
			// get component config settings
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	=  $registrationproAdmin->config();
			//echo '<pre>'; print_r($repgro_config);die;
			$my 			=JFactory::getUser();
			
			$eventid  				= JRequest::getVar('did');
			
			/*	
			$eventid  				= JRequest::getVar('did'); // get event id		
			$event_sessions 		= JRequest::getVar('event_sessions'); // get event session records
			$event_session_dates 	= JRequest::getVar('event_session_dates'); // get event session dates to display the multiple session under same date
			$event_session_header 	= JRequest::getVar('event_session_header'); // get event session header 
			*/
			
			$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart");
			
			// get selected event tickets/additional tickets to forward on cart page
			// check event tikcets selected by user or not
			$product_id			= JRequest::getVar('product_id',array(),'POST');
			$product_qty		= JRequest::getVar('product_qty',array(),'POST');		
			$product_id_add		= JRequest::getVar('product_id_add',array(),'POST');
			$product_qty_add	= JRequest::getVar('product_qty_add',array(),'POST');		
			$productids			= JRequest::getVar('productids',array(),'POST');
											
			if(count($product_id) > 0 || count($product_id_add) > 0){														
				
				//echo "<pre>"; print_r($_POST); exit;
				$this->assignRef('event_session_header', $event_session_header);
				$this->assignRef('event_session_dates', $event_session_dates);
				$this->assignRef('event_sessions', $event_sessions);
				$this->assignRef('eventid', $eventid);	
				$this->assignRef('action', $action);
				$this->assignRef('my', $my);
				$this->assignRef('Itemid', $Itemid);		
				$this->assignRef('regpro_config', $regpro_config);
				
				parent::display($tpl);
			}else{
				$link = JRoute::_("index.php?option=com_registrationpro&view=newuser&Itemid=$Itemid&did=".$eventid,false);	
				$msg  = JText::_('PLEASE_SELECT_REGISTRATION_OPTION');
				$mainframe->redirect($link,$msg);
			}
		
		}else{
			$link = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart");		
			$mainframe->redirect($link,$msg);
		}
		
	}
	
							
}
?>