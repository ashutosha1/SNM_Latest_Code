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

class accessmanagerViewWeblinks extends JViewLegacy{

	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null){
	
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
	
		$database = JFactory::getDBO();
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		//include languages. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('com_weblinks', JPATH_ADMINISTRATOR, null, false);			
				
		//clean up rights in the table
		$helper->clean_access_table('weblink', 'weblinks', 'state');			
		
		//toolbar		
		JToolBarHelper::apply('weblinks_apply');
		JToolBarHelper::save('weblinks_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false );		
		
		if($this->helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($helper);	
		}
	
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels(0, 1, 0, 1);
		$this->assignRef('am_grouplevels', $am_grouplevels);
		
		//get access from db
		$helper = new accessmanagerHelper();
		$access_weblinks = $helper->get_access_rights('weblink', $this->controller->am_config['based_on']);	
		$this->assignRef('access_weblinks', $access_weblinks);		
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.$this->helper->am_strtolower(JText::_('COM_WEBLINKS')), 'am_icon');	

		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=weblinks');	
				
		$helper->add_submenu();			
		
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
			'c.title' => JText::_('COM_ACCESSMANAGER_NAME'),
			'c.ordering' => JText::_('JFIELD_ORDERING_LABEL')			
		);
	}
}
?>