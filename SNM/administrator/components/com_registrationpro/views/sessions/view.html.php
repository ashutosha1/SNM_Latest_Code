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

class registrationproViewSessions extends JViewLegacy
{
	function display($tpl = null) {
		global $mainframe, $option;
		
		$option = JRequest::getCMD('option'); // use this instead of global $option

		$user 			= JFactory::getUser();
		$db 			= JFactory::getDBO();
		$document		= JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	=  $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
		$template = $mainframe->getTemplate();

		// Load pane behavior	
		JHtmlBehavior::framework();			
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		
		// Get data from the model
		$model = $this->getModel('sessions');
		$rows  = $this->get('Data');
		$event_id = JRequest::getVar('event_id', 0);
		$pageNav = $this->get( 'Pagination' );

		$this->assignRef('rows'      	 , $rows);
		$this->assignRef('ordering'		 , $ordering);
		$this->assignRef('template'		 , $template);
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('event_id' 	 , $event_id);	
		
		parent::display($tpl);
		exit;
	}
}
?>