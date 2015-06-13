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

class registrationproViewSession extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
		
		// Load pane behavior	
		JHtmlBehavior::framework();			
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');	

		//initialise variables
		$editor 		=  JFactory::getEditor();
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$regpro_config 	=  registrationproAdmin::config();
		$regpro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($repgro_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
							
		// Get data from the model
		$model		=  $this->getModel('session');
		$row      	=  $this->get('Data');
		//echo "<pre>"; print_r($row); exit;
		
		$row->session_page_header = $model->get_session_page_header($row->event_id);

		//$total      = & $this->get( 'Total');
		$pageNav 	=  $this->get( 'Pagination' );								

		// table ordering
		$Lists['order_Dir'] = $filter_order_Dir;
		$Lists['order'] = $filter_order;
		
		// weekdays
		$weekdays 			= array();
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('MONDAY'));
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('TUESDAY'));
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('WEDNESDAY'));
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('THURSDAY'));
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('FRIDAY'));
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('SATURDAY'));
		$weekdays[] 		= JHTML::_('select.option',  JText::_('MONDAY'), JText::_('SUNDAY'));
		$Lists['weekdays'] 	= JHTML::_('select.genericlist', $weekdays, 'weekday', 'class="inputbox"', 'value', 'text', $row->weekday);
		// end

		//echo "<pre>"; print_r($Lists); exit;

		$ordering = ($lists['order'] == 'id');
		
		JHTML::_('behavior.modal', 'a.modal');

		//assign data to template
		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('regpro_config' , $regpro_config);
		
		parent::display($tpl);		
		exit;
	}
}
?>