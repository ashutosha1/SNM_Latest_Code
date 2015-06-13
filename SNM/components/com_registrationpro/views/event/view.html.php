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

class registrationproViewEvent extends JViewLegacy
{		
	protected $params;
	function display($tpl = null){
		global $mainframe, $Itemid;	
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			//echo "<pre>";print_r($menu);
			$this->params->def('page_heading', $menu->title);
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_FINDER_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);
		$document	= JFactory::getDocument();
						
		$layout = JRequest::getCmd('layout');
		
		if($layout=='vcs'){
			$db =  JFactory::getDBO();
			$model = $this->getModel('event');			
			$location = $model->getEvent();
						
			$start 	= $location->dates." ".$location->times;
			$end	= $location->enddates." ".$location->endtimes;					
			
			$offset = $mainframe->getCfg( 'offset'); // get website offset settings
			
			if($offset > 0){
				$sec_ago = $offset * 3600;
				$start_date = date('Y-m-d H:i:s',strtotime($sec_ago.' seconds ago' , strtotime($start)));
				$end_date = date('Y-m-d H:i:s',strtotime($sec_ago.' seconds ago' , strtotime($end))); //exit;
			}elseif($offset < 0){
				$sec_after = abs($offset) * 3600;				
				$start_date = date('Y-m-d H:i:s',strtotime('+'.$sec_after.' seconds' , strtotime($start)));
				$end_date = date('Y-m-d H:i:s',strtotime('+'.$sec_after.' seconds' , strtotime($end))); //exit;
			}else{			
				$start_date = $start;
				$end_date 	= $end; //exit;
			}
			
			
			$event_start = $start_date;
			$event_end 	 = $end_date;
												
			$location->dates=strtotime($event_start);
			$location->enddates=strtotime($event_end);
			$Filename = 'Event-'.$location->did.'.vcs';
			header("Content-Type: text/x-vCalendar");
			header("Content-Disposition: inline; filename=$Filename");		
			$vCalStart = date("Ymd\THi00", $location->dates);
			$vCalEnd = date("Ymd\THi00", $location->enddates);
echo'
BEGIN:VCALENDAR
VERSION:1.0
BEGIN:VEVENT
SUMMARY:'.$location->titel.'
DESCRIPTION:'.strip_tags($location->datdescription).'		
LOCATION:'.$location->city.' - '.$location->country.' - '.$location->street.'
DTSTART:'.$vCalStart.'
DTEND:'.$vCalEnd.'
END:VEVENT
END:VCALENDAR
';
exit();
      	}
				
		if($layout == 'terms_and_conditions'){		
			$this->terms_and_conditions(); // show terms_and_conditions			
		}else if($layout == 'event_report'){
			$this->event_report(); // show event report
		}else if($layout == 'session'){
			$this->event_session(); // show event sessions
		}else{
									
			// get component config settings
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
			$my 			= JFactory::getUser();
						
			//add css and js to document
			//$registrationproHelper->add_regpro_frontend_scripts();
			$registrationproHelper = new registrationproHelper;
			$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
					
			$model 		= $this->getModel('event');
			$row 		= $model->getEvent_new($regpro_config['include_pending_reg']);
			$formStatus = $model->getFormStatus($row->form_id);						
			// Filter by access level.
			$user	= JFactory::getUser();
			$groups	= $user->getAuthorisedViewLevels();		
			if(in_array($row->viewaccess, $groups)){
				$event_view_access = 1;
			}else{
				$event_view_access = 0;
			}
			
			// get event discounts to display on event deails page
			$this_event_discounts = $model->getEventDiscounts();
			
			//if event is free, skip payment step		
			$has_payments = $model->is_event_free($row->did);	
	
			$nothing = array();
			$row->message = "";
			$row->message .= $registrationproHelper->check_max_attendance($row, $nothing, $nothing, $regpro_config, 1);		
			$row->message .= $registrationproHelper->check_event_registration_enable($row, $regpro_config, 1);
			$row->message .= $registrationproHelper->check_event_registration_date($row, $regpro_config, 1);
			$row->message .= $registrationproHelper->check_event_multiple_registration($row, $regpro_config);
			
			//echo 	$row->message; exit;	
					
			// get event tickets
			$tickets = $model->getEventTickets($row->did,$regpro_config['include_pending_reg']);
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
			
			$document->setTitle($row->titel);
			
			if(trim($row->metadescription) != "") {
				$document->setDescription( $row->metadescription );
			}
			if(trim($row->metakeywords) != "") {
				$document->setMetadata('keywords',$row->metakeywords);
			}
			
			if(trim($row->metarobots) != "") {
				$document->setMetadata('robots',$row->metarobots);
			}
			
			if ($layout == 'printevent') {
				$document->setMetaData('robots', 'noindex, nofollow');
			}
						
			// Process the content plugins.						
			$row->text = $row->datdescription;
			$dispatcher = JDispatcher::getInstance();			
			JPluginHelper::importPlugin('content');
			$result = $dispatcher->trigger('onContentPrepare', array ('com_registrationpro.eventdesc', &$row, &$params, 0));
			// End
			
			// check if session exists with event
			$event_sessions = $model->getEventSession();
			
			if(count($event_sessions) > 0) {
				//$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=event&task=session");
				//$action = "index.php?option=com_registrationpro&Itemid=$Itemid&controller=event&task=session";
				$action = JRoute::_("index.php?option=com_registrationpro&view=event&layout=session&Itemid=$Itemid");
			}else{
				$action = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart");
			}
																					
																																													
			$this->assignRef('action', $action);
			$this->assignRef('tickets', $tickets);			
			$this->assignRef('event_view_access', $event_view_access);
			$this->assignRef('row', $row);
			$this->assignRef('this_event_discounts',$this_event_discounts);
			$this->assignRef('my', $my);
			$this->assignRef('regproConfig', $regpro_config);
			$this->assignRef('formStatus', $formStatus);
			$this->assignRef('Itemid', $Itemid);
			
			parent::display($tpl);
		}
	}
		
	function terms_and_conditions()
	{
		global $mainframe;
		
		$eventids  = JRequest::getVar('eventids',0,'','int');	
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
		$my 			= JFactory::getUser();
						
		//add css and js to document
		$registrationproHelper = new registrationproHelper;	
		$registrationproHelper->add_regpro_frontend_scripts();
				
		$model 		= $this->getModel('event');
		$rows 		= $model->getEventsTermsAndConditions($eventids);
		$task 		= JRequest::getVar( 'task' );	
		
		//echo "<pre>"; print_r($rows); exit;	
			
		//assign data to template
		$this->assignRef('rows', $rows);
		$this->assignRef('my', $my);		
		$this->assignRef('regpro_config', $regpro_config);
		
		parent::display($tpl);			
	}
	
	function event_session()
	{
		global $mainframe, $Itemid;
		
		$model 					= $this->getModel('event');		
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
			$registrationproHelper = new registrationproHelper;	
			$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
			
			// get component config settings
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
			$my 			= JFactory::getUser();
			
			$eventid  				= JRequest::getVar('did',0,'','int');
			
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
				$link = JRoute::_("index.php?option=com_registrationpro&view=event&Itemid=$Itemid&did=".$eventid,false);	
				$msg  = JText::_('PLEASE_SELECT_REGISTRATION_OPTION');
				$mainframe->redirect($link,$msg);
			}
		
		}else{
			$link = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart");		
			$mainframe->redirect($link,$msg);
		}
		
	}
	
	function event_report()
	{
		global $mainframe;
				
		//initialise variables
		$editor 		=  JFactory::getEditor();
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
				
		//echo "<pre>"; print_r($regpro_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper;	
		$registrationproHelper->add_regpro_frontend_scripts();		
		
		// Get data from the model
		$model	=  $this->getModel('event');
				
		$layout = JRequest::getCmd('layout');		
					
		$data 			= $model->getEventReportData();
		$productdata 	= $model->getEventTransactionData();			
	
		//echo "<pre>"; print_r($data); exit;
		// apply event discount upon user tickets
		foreach($productdata as $pkey => $pvalue)
		{
			if($pvalue->event_discount_amount > 0){
				if($pvalue->event_discount_type == 'P'){
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$actual_price_without_per 		= ($pvalue->price * 100) / (100 - $pvalue->event_discount_amount);					
					$event_discounted_amount_price 	= $actual_price_without_per * $pvalue->event_discount_amount / 100;					
					$pvalue->discount_amount		+= $event_discounted_amount_price;
					$pvalue->price 			 		= $actual_price_without_per;
				}
			}
		}
		// end
		
		//echo "<pre>"; print_r($productdata); exit;
		$this->assignRef('data', $data);
		$this->assignRef('productdata', $productdata);
		$this->assignRef('rdid', $model->_id);
			
		//assign data to template
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('regpro_config', $regpro_config);
		$this->assignRef('task' 		, $task);		
		
		parent::display($tpl);			
	}
	
	// Function to set the correct timezone for events, http://addthisevent.com/#install.		
	function get_Addthisevent_timezone_value($zone)
	{			
		$arr_addthisevent_zone["-12"] 	= "1";		
		$arr_addthisevent_zone["-11"] 	= "2";
		$arr_addthisevent_zone["-10"] 	= "3";
		$arr_addthisevent_zone["-9"] 	= "45";
		$arr_addthisevent_zone["-8"] 	= "4";
		$arr_addthisevent_zone["-7"] 	= "7";
		$arr_addthisevent_zone["-6"] 	= "13";
		$arr_addthisevent_zone["-5"] 	= "14";
		$arr_addthisevent_zone["-4.5"] 	= "17";
		$arr_addthisevent_zone["-4"] 	= "18";
		$arr_addthisevent_zone["-3.5"] 	= "23";
		$arr_addthisevent_zone["-3"] 	= "25";
		$arr_addthisevent_zone["-2"] 	= "30";
		$arr_addthisevent_zone["-1"] 	= "33";
		$arr_addthisevent_zone["0"] 	= "35";
		$arr_addthisevent_zone["1"]	 	= "42";
		$arr_addthisevent_zone["2"] 	= "49";
		$arr_addthisevent_zone["3"] 	= "54";
		$arr_addthisevent_zone["3.5"] 	= "58";
		$arr_addthisevent_zone["4"] 	= "59";
		$arr_addthisevent_zone["4.5"] 	= "65";
		$arr_addthisevent_zone["5"] 	= "66";
		$arr_addthisevent_zone["5.5"] 	= "68";
		$arr_addthisevent_zone["5.75"] 	= "70";
		$arr_addthisevent_zone["6"] 	= "72";
		$arr_addthisevent_zone["6.5"] 	= "74";
		$arr_addthisevent_zone["7"] 	= "75";
		$arr_addthisevent_zone["8"] 	= "77";
		$arr_addthisevent_zone["9"] 	= "84";
		$arr_addthisevent_zone["9.5"] 	= "87";
		$arr_addthisevent_zone["10"] 	= "90";
		$arr_addthisevent_zone["10.5"] 	= "90";  // doesn't exists in addthisevent timezone settings;
		$arr_addthisevent_zone["11"] 	= "93";
		$arr_addthisevent_zone["12"] 	= "95";
		$arr_addthisevent_zone["12.75"] = "95"; // doesn't exists in addthisevent timezone settings
		$arr_addthisevent_zone["13"] 	= "99";
		$arr_addthisevent_zone["14"] 	= "99"; // doesn't exists in addthisevent timezone settings	
		
		if($arr_addthisevent_zone[$zone]){
			return $arr_addthisevent_zone[$zone];	
		}else{
			return 0;
		}							
	}
							
}
?>