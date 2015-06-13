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

class registrationproViewEvents extends JViewLegacy
{		
	protected $params;
	function display($tpl = null){
		global $mainframe, $Itemid;
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $menu->title);
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_FINDER_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);
		//$layout = JRequest::getCmd('layout');						
								
		$uri = JFactory::getURI();

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();
		
		
		// check to show the "Max Attendance" attendance column
		if(!$regpro_config['maxseat'] && !$regpro_config['pendingseat'] && !$regpro_config['registeredseat']){
			$show_attendance_column = 0;
		}else{
			$show_attendance_column = 1;
		}		
									
		//add css and js to document
		//registrationproHelper::add_regpro_frontend_scripts();	
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
	
	function _buildSortLists()
	{
		global $mainframe;
				
		// Table ordering values
		$filter				= JRequest::getString('filter');
		
		$itemid 			= JRequest::getInt('id',0) . ':' . JRequest::getInt('Itemid',0);
		$filter_order  		= $mainframe->getUserStateFromRequest($option.'events.list.filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir 	= $mainframe->getUserStateFromRequest($option.'events.list.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$lists['task']      = 'events';
		$lists['filter']    = $filter;
		$lists['order']     = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
				
		//echo "<pre>"; print_r($lists); 
		return $lists;
	}			
}
?>