<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class accessmanagerViewAccessvievving extends JViewLegacy{

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		//include languages. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('com_banners.sys', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_contact.sys', JPATH_ADMINISTRATOR, null, false);	
		$lang->load('com_weblinks.sys', JPATH_ADMINISTRATOR, null, false);	
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_ACCESS_VIEWING'), 'am_icon');
		
		if($this->helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($helper);	
		}		

		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=articles');	
				
		$helper->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
}
?>