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

class registrationproViewCalendar extends JViewLegacy
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

		if ($menu) {
			$this->params->def('page_heading', $menu->title);
		} else $this->params->def('page_heading', JText::_('COM_FINDER_DEFAULT_PAGE_TITLE'));

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
		JHTML::_('behavior.tooltip'); 
		
		$layout = JRequest::getCmd('layout');
		
		if($layout != 'category') $mainframe->setUserState( "com_registrationpro_calender_categoryid", 0);
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$my = JFactory::getUser();
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
						
		JHTML::_('behavior.tooltip');
		$mootoolflag = 1;
			
		$this->assignRef('upgrade_mootools', $mootoolflag);						
		$this->assignRef('my', $my);
		$this->assignRef('regproConfig', $regpro_config);
		$this->assignRef('Itemid', $Itemid);
		
		parent::display($tpl);
	}	
}
?>