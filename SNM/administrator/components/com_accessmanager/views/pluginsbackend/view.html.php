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

class accessmanagerViewPluginsBackend extends JViewLegacy{

	protected $items;
	protected $pagination;
	protected $state;		

	function display($tpl = null){
	
		$ds = DIRECTORY_SEPARATOR;
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_plugins'.$ds.'helpers'.$ds.'plugins.php');
		
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');	
		
		$this->controller->get_backend_usergroups();				
		
		//toolbar		
		JToolBarHelper::apply('pluginsbackend_apply');
		JToolBarHelper::save('pluginsbackend_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
		
		//sidebar
		if($this->helper->joomla_version >= '3.0'){			
			$this->add_sidebar($helper);	
		}
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels('backend', false, true, 1);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db		
		$access_plugins = $helper->get_access_rights_backend('pluginbackend', 'group');	
		$this->assignRef('access_plugins', $access_plugins);
		
		//clean up rights in the table
		//$helper->clean_access_table('pluginsbackend', 'menu', 'published');
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_PLUGIN_ACCESS'), 'am_icon');	
		
		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=pluginsbackend');	
				
		$helper->add_submenu();			
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_enabled',
			JHtml::_('select.options', PluginsHelper::publishedOptions(), 'value', 'text', $this->state->get('filter.enabled'), true)
		);
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('COM_ACCESSMANAGER_SELECT_TYPE').' -',
			'filter_type',
			JHtml::_('select.options', PluginsHelper::folderOptions(), 'value', 'text', $this->state->get('filter.type'))
		);
				
		$this->sidebar = JHtmlSidebar::render();
	}
	
}
?>