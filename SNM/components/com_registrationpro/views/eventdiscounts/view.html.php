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

class registrationproViewEventdiscounts extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();	
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();		
		//$regpro_config 	=  registrationproAdmin::config();				

		//get vars		
		$template		= $mainframe->getTemplate();
				
		// Get data from the model
		$model	=  $this->getModel('eventdiscounts');
		$rows   =  $this->get('Data');
		// echo "<pre>"; print_r($rows); exit;

		$discount_name = JRequest::getVar('discount_name','N');				
		$event_id	   = JRequest::getVar('event_id',0,'','int');

		//assign data to template
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('template'		, $template);
		$this->assignRef('regpro_config', $regpro_config);
		$this->assignRef('event_id' 	, $event_id);
		$this->assignRef('discount_name', $discount_name);		
		
		parent::display($tpl);
		exit;
	}
}
?>