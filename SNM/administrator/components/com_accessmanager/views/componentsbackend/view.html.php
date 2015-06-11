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

class accessmanagerViewComponentsbackend extends JViewLegacy{

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');	
		
		$this->controller->get_backend_usergroups();	
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels('backend', false, true, 1);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db		
		$access_components = $helper->get_access_rights_backend('componentbackend', 'group');			
		$this->assignRef('access_components', $access_components);
				
		//toolbar			
		JToolBarHelper::apply('componentsbackend_apply');
		JToolBarHelper::save('componentsbackend_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );	
		
		//sidebar
		if($this->helper->joomla_version >= '3.0'){			
			$this->add_sidebar($helper);	
		}	
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS'), 'am_icon');		
		
		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=componentsbackend');	
				
		$helper->add_submenu();		
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
	
	
}
?>