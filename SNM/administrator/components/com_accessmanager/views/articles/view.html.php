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

class accessmanagerViewArticles extends JViewLegacy{

	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null){	
	
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);	
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		//clean up rights in the table
		$helper->clean_access_table('article', 'content', 'state');
		
		$plugin_search_contentaccessmanager_enabled = $this->helper->check_plugin_enabled('search', 'contentaccessmanager');
		$this->assignRef('plugin_search_contentaccessmanager_enabled', $plugin_search_contentaccessmanager_enabled);
		
		$plugin_search_content_enabled = $this->helper->check_plugin_enabled('search', 'content');
		$this->assignRef('plugin_search_content_enabled', $plugin_search_content_enabled);
		
		$plugin_search_categoriesaccessmanager_enabled = $this->helper->check_plugin_enabled('search', 'categoriesaccessmanager');
		$this->assignRef('plugin_search_categoriesaccessmanager_enabled', $plugin_search_categoriesaccessmanager_enabled);
		
		$plugin_search_categories_enabled = $this->helper->check_plugin_enabled('search', 'categories');
		$this->assignRef('plugin_search_categories_enabled', $plugin_search_categories_enabled);
		
		//toolbar	
		$this->add_toolbar();		
		
		//sidebar
		if($this->helper->joomla_version >= '3.0'){			
			$this->add_sidebar($helper);	
		}	
		
		//get usergroups from db
		$am_grouplevels = $controller->get_grouplevels(0, 1, 0, 1);
		$this->assignRef('am_grouplevels', $am_grouplevels);
		
		//get access from db
		$helper = new accessmanagerHelper();
		$access_articles = $helper->get_access_rights('article', $this->controller->am_config['based_on']);	
		$this->assignRef('access_articles', $access_articles);	
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'), 'am_icon');
		
		parent::display($tpl);
	}
	
	function add_toolbar(){
	
		JToolBarHelper::apply('access_articles_apply');
		JToolBarHelper::save('access_articles_save');
		JToolBarHelper::divider();		
		JToolBarHelper::custom('back', 'back.png', 'back.png', JText::_('JTOOLBAR_BACK'), false, false);		
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=articles');	
				
		$helper->add_submenu();			
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'items_category_filter',
			JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);
		$this->sidebar = JHtmlSidebar::render();
	}
	
	

}
?>