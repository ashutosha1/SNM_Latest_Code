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

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder');

class registrationproViewEvents extends JViewLegacy
{
	function display($tpl = null) {
		global $mainframe, $option;
		
		$option = JRequest::getCMD('option');
		$user 	= JFactory::getUser();
		$db 	= JFactory::getDBO();
		$registrationproAdmin = new  registrationproAdmin; $regpro_config 	= $registrationproAdmin->config();
		
		$filter_order	  = $mainframe->getUserStateFromRequest( $option.'.events.filter_order', 'filter_order', '', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'.events.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 	  = $mainframe->getUserStateFromRequest( $option.'.events.filter_state', 'filter_state', '*', 'word' );
		$filter 		  = $mainframe->getUserStateFromRequest( $option.'.events.filter', 'filter', '', 'int' );
		$search 		  = $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 		  = $db->escape( trim(JString::strtolower( $search ) ) );
		$template		  = $mainframe->getTemplate();
		
		if (!$filter_order) $filter_order = 'c.ordering';
				
		// Get data from the model
		$rows    = $this->get( 'Data');
		$total   = $this->get( 'Total');
		$pageNav = $this->get( 'Pagination' );
		
		//publish unpublished filter
		$lists['state']	= JHTML::_('grid.state', $filter_state );

		$filters = array();
		$filters[] = JHTML::_('select.option', '0', JText::_( 'Select' ) );
		$filters[] = JHTML::_('select.option', '1', JText::_( 'ADMIN_EVENTS_TITEL_LI_EV' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'ADMIN_EVENTS_CLUB_LI_EV' ) );
		$filters[] = JHTML::_('select.option', '3', JText::_( 'ADMIN_EVENTS_CITY_LI_LO' ) );
		$filters[] = JHTML::_('select.option', '4', JText::_( 'ADMIN_EVENTS_CAT_LI_EV' ) );

		$lists['filter'] 	= JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox searchOption"', 'value', 'text', $filter );
		
		// search filter
		$lists['search']	= $search;

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('regpro_config' , $regpro_config);
		
		parent::display($tpl);
	}
}
?>