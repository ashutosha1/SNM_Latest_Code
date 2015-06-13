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

class registrationproControllerEvent extends registrationproController
{
	function __construct()
	{
		parent::__construct();			
	}
	
	function session()
	{	
		/*global $mainframe, $Itemid;
						
		$model 					= $this->getModel('event');		
		$event_sessions 		= $model->getEventSession();
		$event_session_dates	= $model->getEventSessionDates();
		$event_session_header 	= $model->getEventSessionHeader();	
		
		if(count($event_sessions) > 0) {
			JRequest::setVar( 'view', 'event' );
			JRequest::setVar( 'layout', 'session');
			JRequest::setVar( 'event_sessions', $event_sessions);
			JRequest::setVar( 'event_session_dates', $event_session_dates);
			JRequest::setVar( 'event_session_header', $event_session_header);	
		}else{
			$link = JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&controller=cart&task=cart");		
			$mainframe->redirect($link,$msg);
		}					
		
		parent::display();*/
	}
	
	/*function terms_and_conditions()
	{
		$model 	= $this->getModel('event');		
		$row 	= $model->getEvent();						
	}*/
}
?>