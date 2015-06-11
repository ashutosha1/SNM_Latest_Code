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

class accessmanagerViewMenuaccess extends JViewLegacy{

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
		
		//toolbar		
		JToolBarHelper::apply('menuaccess_apply');
		JToolBarHelper::save('menuaccess_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
		
		//sidebar
		if($this->helper->joomla_version >= '3.0'){			
			$this->add_sidebar($helper);	
		}
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels(0, 1, 0, 1);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db
		$helper = new accessmanagerHelper();
		$access_menuitems = $helper->get_access_rights('menuitem', $this->controller->am_config['based_on']);	
		$this->assignRef('access_menuitems', $access_menuitems);
		
		//clean up rights in the table
		$helper->clean_access_table('menuitem', 'menu', 'published');
		
		//include extra language file
		$lang = JFactory::getLanguage();
		$lang->load('com_languages', JPATH_ADMINISTRATOR, null, false);
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_MENU_ACCESS'), 'am_icon');	
		
		parent::display($tpl);
	}
	
	static function get_menu_type_options(){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT menutype, title FROM #__menu_types ORDER BY title ASC"  );
		$menutypes = $database->loadObjectList();
		$options = array();
		foreach($menutypes as $menutype){
			$options[] = JHtml::_('select.option', $menutype->menutype, $menutype->title);					
		}		
		return $options;
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=menuaccess');	
				
		$helper->add_submenu();			
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('COM_ACCESSMANAGER_SELECT_MENU_TYPE').' -',
			'filter_type',
			JHtml::_('select.options', $this->get_menu_type_options(), 'value', 'text', $this->state->get('filter.type'), true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);
		$this->sidebar = JHtmlSidebar::render();
	}
	
	protected function getSortFields(){
	
		return array(
			'm.title' => JText::_('JFIELD_TITLE_DESC'),
			'm.lft' => JText::_('JFIELD_ORDERING_LABEL')			
		);
	}
	
}
?>