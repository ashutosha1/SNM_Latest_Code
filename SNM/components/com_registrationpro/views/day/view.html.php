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

class registrationproViewDay extends JViewLegacy
{
	function display($tpl = null){
		global $mainframe, $Itemid;

		$uri = JFactory::getURI();

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();

		// check to show the "Max Attendance" attendance column
		$show_attendance_column = 1;
		if(!$regpro_config['maxseat'] && !$regpro_config['pendingseat'] && !$regpro_config['registeredseat']) $show_attendance_column = 0;

		//add css and js to document
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());

		// manage front end ordering
		$lists	= $this->_buildSortLists();

		$model 		= $this->getModel();
		$event_list = $this->get('Events');
		$pageNav 	= $this->get('Pagination');

		$this->assign('total',	$total);
		$this->assign('action', str_replace('&', '&amp;', $uri->toString()));
		$this->assign('lists',	$lists);

		$this->assignRef('Itemid', $Itemid);
		$this->assignRef('rows', $event_list);
		$this->assignRef('pageNav', $pageNav);
		$this->assignRef('show_attendance_column', $show_attendance_column);
		$this->assignRef('regproConfig', $regpro_config);

		parent::display($tpl);
	}

	function _buildSortLists() {
		global $mainframe;

		$filter	= JRequest::getString('filter');
		$itemid = JRequest::getInt('id',0) . ':' . JRequest::getInt('Itemid',0);
		$filter_order  		= $mainframe->getUserStateFromRequest($option.'events.list.filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir 	= $mainframe->getUserStateFromRequest($option.'events.list.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$lists['task']      = 'events';
		$lists['filter']    = $filter;
		$lists['order']     = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;

		return $lists;
	}
}
?>