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

class accessmanagerViewAdminmenumanager extends JViewLegacy{

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
		JToolBarHelper::apply('adminmenumanager_apply');
		JToolBarHelper::save('adminmenumanager_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );
		
		//sidebar
		if($this->helper->joomla_version >= '3.0'){			
			$this->add_sidebar($helper);	
		}
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels(true, true, false, true);		
		$this->assignRef('am_grouplevels', $am_grouplevels);		
		
		//get access from db
		$helper = new accessmanagerHelper();
		$access_menuitems = $helper->get_access_rights('adminmenumanager', $this->controller->am_config['based_on']);	
		$this->assignRef('access_menuitems', $access_menuitems);
		
		//clean up rights in the table
		if(count($this->items)){
			$helper->clean_access_table('adminmenumanager', 'adminmenumanager_menuitems', 'published');
		}
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_ADMINMENUMANAGER_ACCESS'), 'am_icon');	
		
		parent::display($tpl);
	}
	
	static function get_menu_type_options(){		
		
		$db = JFactory::getDBO();
		$ds = DIRECTORY_SEPARATOR;
		
		$options = array();
		if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'controller.php')){
			
			$db->setQuery("SHOW COLUMNS FROM #__adminmenumanager_menus ");
			$columns = $db->loadColumn();			
			if(in_array('ordering', $columns)){
				$order_column = 'ordering';	
			}else{
				$order_column = 'name';
			}	
			
			$query = $db->getQuery(true);
			$query->select('id, name');
			$query->from('#__adminmenumanager_menus');
			$query->order($order_column);
			$menutypes = $db->setQuery($query);				
			$menutypes = $db->loadObjectList();				
			
			foreach($menutypes as $menutype){
				$options[] = JHtml::_('select.option', $menutype->id, $menutype->name);					
			}
		}			
				
		return $options;

	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=adminmenumanager');	
				
		$helper->add_submenu();			
		
		JHtmlSidebar::addFilter(
			'- '.JText::_('COM_ACCESSMANAGER_SELECT_MENU_TYPE').' -',
			'filter_type',
			JHtml::_('select.options', $this->get_menu_type_options(), 'value', 'text', $this->state->get('filter.type'), true)
		);
		
		$this->sidebar = JHtmlSidebar::render();
	}
	
	protected function getSortFields(){
	
		return array(
			'i.title' => JText::_('JFIELD_TITLE_DESC'),
			'i.ordertotal' => JText::_('JFIELD_ORDERING_LABEL')			
		);
	}
	
}
?>