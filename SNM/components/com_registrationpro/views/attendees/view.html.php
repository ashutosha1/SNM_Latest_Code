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
jimport( 'joomla.filesystem.folder');
jimport('joomla.utilities.date');

class registrationproViewAttendees extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	= $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
		
		//add css and js to document
		//$registrationproHelper->add_regpro_frontend_scripts();
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
		
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
		
		// get event id		
		$eventid	= JRequest::getVar( 'did',0,'','int');
		$eventInfo = $registrationproHelper->getEventInfo($eventid);
		
		// Get data from the model
		$model 		= $this->getModel('attendees');
		$rows      	= $model->getData($eventid);
		//echo "<pre>"; print_r($rows); exit;

		$total      = $model->getTotal();
		$pageNav 	= $model->getPagination();		
		//assign data to template
		
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('eventid'      , $eventid);
		$this->assignRef('eventInfo'   , $eventInfo);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('regpro_config', $regpro_config);
						
		parent::display($tpl);
	}
}
?>