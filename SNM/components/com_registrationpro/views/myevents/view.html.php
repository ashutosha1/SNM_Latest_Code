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

class registrationproViewMyevents extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option, $Itemid;
		
		$uri = JFactory::getURI();
		
		//initialise variables
		$user 		=  JFactory::getUser();
		$db 		=  JFactory::getDBO();
		$registrationproAdmin =new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$registrationproHelper = new registrationproHelper;
		if(!$registrationproHelper->checkUserAccount()) {
			$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid, false);		
			$mainframe->redirect($link);
		}
		
		
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
				
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.events.filter_order', 'filter_order', '', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.events.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.events.filter_state', 'filter_state', '*', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.events.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
		
		//echo $filter_order; exit;
				
		/*if ($filter_order == 'a.ordering') {
			$order = ' ORDER BY a.catsid, a.ordering '. $filter_order_Dir;
		} else {
			$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', a.catsid, a.ordering';
		}*/
		
		if (!$filter_order) {
			$filter_order = 'c.ordering';
		}
				
		// Get data from the model
		$rows      	=  $this->get( 'Data');
		//echo "<pre>"; print_r($rows); exit;				

		$total      =  $this->get( 'Total');
		$pageNav 	=  $this->get( 'Pagination' );
		
		//echo "<pre>"; print_r($pageNav); exit;

		//publish unpublished filter
		$lists['state']	= JHTML::_('grid.state', $filter_state );

		$filters = array();
		$filters[] = JHTML::_('select.option', '0', JText::_( '- Select -' ) );
		$filters[] = JHTML::_('select.option', '1', JText::_( 'ADMIN_EVENTS_TITEL_LI_EV' ) );
		$filters[] = JHTML::_('select.option', '2', JText::_( 'ADMIN_EVENTS_CLUB_LI_EV' ) );
		$filters[] = JHTML::_('select.option', '3', JText::_( 'ADMIN_EVENTS_CITY_LI_LO' ) );
		$filters[] = JHTML::_('select.option', '4', JText::_( 'ADMIN_EVENTS_CAT_LI_EV' ) );

		$lists['filter'] 	= JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $filter );
		
		// search filter
		$lists['search']	= $search;

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;
		//echo "<pre>"; print_r($lists['order']); exit;

		//echo "<pre>"; print_r($pageNav); exit;

		//assign data to template
		$this->assign('action', str_replace('&', '&amp;', $uri->toString()));
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		//$this->assignRef('ordering'		, $ordering);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('Itemid' , $Itemid);
		
		parent::display($tpl);
	}
}
?>