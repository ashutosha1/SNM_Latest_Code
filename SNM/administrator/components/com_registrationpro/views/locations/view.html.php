<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL		
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

class registrationproViewLocations extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		$option = JRequest::getCMD('option'); // use this instead of global $option

		//initialise variables
		$user 		= JFactory::getUser();
		$db 		= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$repgro_config 	=  $registrationproAdmin->config();
		$repgor_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($repgor_config); exit;
		
		
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.locations.filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.locations.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.locations.filter_state', 'filter_state', '*', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.locations.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		//$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();

		JHTML::_('behavior.tooltip');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_LIST_LOCATIONS' ), 'events' );
		JToolBarHelper::addNew();
		JToolBarHelper::spacer();
		JToolBarHelper::publishList();
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList();
		JToolBarHelper::spacer();
		JToolBarHelper::editList();
		JToolBarHelper::spacer();
		JToolBarHelper::deleteList('Selected records data will be lost and cannot be undone!', 'remove', 'Remove' );
		//JToolBarHelper::spacer();
		//JToolBarHelper::help( 'screen.registrationpro', true );

		// Get data from the model
		$rows      	= $this->get( 'Data');
		//echo "<pre>"; print_r($rows); exit;

		$total      = $this->get( 'Total');
		$pageNav 	= $this->get( 'Pagination' );		
		//echo "<pre>"; print_r($pageNav); exit;
		
		$lists = array();
		
		$filters = array();
		$filters[] = JHTML::_('select.option',  '1', JText::_('ADMIN_EVENTS_CLUB_LI_LO'), 'value', 'text' ); 
		$filters[] = JHTML::_('select.option',  '2', JText::_('ADMIN_EVENTS_CITY_LI_LO'), 'value', 'text' );
		$lists['filter'] = JHTML::_('select.genericlist',   $filters, 'filter', 'class="inputbox" size="1" style="width:100px"','value', 'text', $filter); 	
		
		//publish unpublished filter
		$lists['state']	= JHTML::_('grid.state', $filter_state );
		
		// search filter
		$lists['search']= $search;
				
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$ordering = ($lists['order'] == 'id');
				

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('settings'     , $settings);
		
		parent::display($tpl);
	}
}
?>