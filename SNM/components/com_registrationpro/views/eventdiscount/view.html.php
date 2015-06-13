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

class registrationproViewEventdiscount extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		//initialise variables
		$editor 		=  JFactory::getEditor();
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();
		
		//get vars
		$cid	= JRequest::getInt( 'cid' );
							
		// Get data from the model
		$model	=  $this->getModel();
		$row    =  $this->get('Data');
		//echo "<pre>"; print_r($row); exit;
		
		$pageNav 	=  $this->get( 'Pagination' );								

		// table ordering
		$Lists['order_Dir'] = $filter_order_Dir;
		$Lists['order'] 	= $filter_order;
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