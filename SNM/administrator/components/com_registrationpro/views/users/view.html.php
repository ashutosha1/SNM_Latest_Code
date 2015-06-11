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

class registrationproViewUsers extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		$option = JRequest::getCMD('option'); // use this instead of global $option
		
		//echo "<pre>"; print_r($_POST); exit;

		//initialise variables
		$user 			= JFactory::getUser();
		$db 			= JFactory::getDBO();
		$document		= JFactory::getDocument();
		$registrationproAdmin = new  registrationproAdmin; $regpro_config 	= $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
						
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.users.filter_order', 'filter_order', 'rid', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.users.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.users.filter_state', 'filter_state', '*', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.users.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
		
		// get event id		
		$eventid	= JRequest::getVar( 'rdid');
		$registrationproHelper = new registrationproHelper;
		$eventInfo = $registrationproHelper->getEventInfo($eventid);
		
		// Get data from the model
		$model 		=$this->getModel('users');
		$rows      	=$model->getData($eventid);
		//echo "<pre>"; print_r($rows); exit;

		$total      =$model->getTotal();
		$pageNav 	=$model->getPagination();		

		//publish unpublished filter
		//$lists['state']	= JHTML::_('grid.state', $filter_state );		
		$filter_states		= array();
		$filter_states[]	= JHTML::_('select.option', '0', JText::_( '- Select -' ) );	
		$filter_states[]	= JHTML::_('select.option', 'A', JText::_( 'ADMIN_USERS_ACCEPTED_FILTER' ) );
		$filter_states[] 	= JHTML::_('select.option', 'P', JText::_( 'ADMIN_USERS_PENDING_FILTER' ) );
		$filter_states[] 	= JHTML::_('select.option', 'W', JText::_( 'ADMIN_USERS_WAITING_FILTER' ) );
		$filter_states[] 	= JHTML::_('select.option', 'PP', JText::_( 'ADMIN_USERS_PENDING_PAYMENT_FILTER' ) );
		$filter_states[] 	= JHTML::_('select.option', 'CP', JText::_( 'ADMIN_USERS_COMPLETE_PAYMENT_FILTER' ) );
		$lists['state'] 	= JHTML::_('select.genericlist', $filter_states, 'filter_state', 'size="1" class="inputbox" onchange="submitform( );"', 'value', 'text', $filter_state);	

		$filters 	= array();
		$filters[] 	= JHTML::_('select.option', '0', JText::_( 'Select' ) );
		$filters[] 	= JHTML::_('select.option', '1', JText::_( 'ADMIN_USERS_FIRSTNAME_FILTER' ) );
		$filters[] 	= JHTML::_('select.option', '2', JText::_( 'ADMIN_USERS_LASTTNAME_FILTER' ) );
		$filters[] 	= JHTML::_('select.option', '3', JText::_( 'ADMIN_USERS_EMAIL_FILTER' ) );

		$lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox" style="width: 80px;"', 'value', 'text', $filter );

		// search filter
		$lists['search']= $search;

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;						

		//assign data to template
		$this->assignRef('lists'      	, $lists);
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