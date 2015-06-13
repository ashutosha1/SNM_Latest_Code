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

class registrationproViewTickets extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$user 		=  JFactory::getUser();
		$db 		=  JFactory::getDBO();
		$document	=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($repgor_config); exit;
						
		//get vars		
		$template			= $mainframe->getTemplate();

		JHTML::_('behavior.tooltip');
			
		// Get data from the model
		$model	=  $this->getModel('tickets');
		$rows   =  $this->get('Data');
		//echo "<pre>"; print_r($rows); exit;
		
		
		$ticket_type   = JRequest::getVar('type','E');
		$event_id	   = JRequest::getVar('regpro_dates_id',0,'','int');

		//$total      = & $this->get( 'Total');
		$pageNav 	=  $this->get( 'Pagination' );

		//assign data to template		
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('ticket_type'	, $ticket_type);
		$this->assignRef('template'		, $template);
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('event_id' , $event_id);	
		
		//echo $event_id; exit;	
		
		parent::display($tpl);
		exit;
	}
}
?>