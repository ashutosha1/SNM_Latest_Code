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

class accessmanagerViewTools extends JViewLegacy{

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = new accessmanagerHelper();
		$this->assignRef('helper', $helper);
		
		//include languages. Reuse or die ;-)#
		$lang = JFactory::getLanguage();
		$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
		
		$grouplevels_selector = $this->get_grouplevels_selector($controller->am_config);
		$this->assignRef('grouplevels_selector', $grouplevels_selector);
		
		$groups_selector = $this->get_groups_selector();
		$this->assignRef('groups_selector', $groups_selector);
		
		
		
		if($this->helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($helper);	
		}	
		
		//set header		
		JToolBarHelper::title('Access Manager :: '.JText::_('COM_ACCESSMANAGER_TOOLS'), 'am_icon');		

		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=tools');	
				
		$helper->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}	
	
	function get_grouplevels_selector($am_config){
		
		$db = JFactory::getDBO();		
		static $grouplevels_select;		
		
		if(!$grouplevels_select){
		
			//query
			$query = $db->getQuery(true);	
			if($am_config['based_on']=='level'){				
				$query->select('a.id, a.title');
				$query->from('#__viewlevels AS a');
				$query->order($am_config['level_sort']);
			}else{								
				$query->select('a.id as id, a.title as title, a.parent_id as parent_id, COUNT(DISTINCT b.id) AS hyrarchy');
				$query->from('#__usergroups AS a');
				$query->leftJoin('#__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt');							
				$query->group('a.id');
				$query->order('a.lft');									
			}			
			$grouplevels = $db->setQuery((string)$query);	
			$grouplevels = $db->loadObjectList();	
			
			$grouplevels_select = '<select name="grouplevels_select[]" class="chzn-done">';
			$grouplevels_select .= '<option value="0">';
			$grouplevels_select .= JText::_('COM_ACCESSMANAGER_IGNORE');
			$grouplevels_select .= '</option>';
			foreach($grouplevels as $grouplevel){
				$grouplevels_select .= '<option value="'.$grouplevel->id.'">';					
				if($am_config['based_on']=='group'){	
					$grouplevels_select .= str_repeat('- ',$grouplevel->hyrarchy);	
				}						
				$grouplevels_select .= $grouplevel->title;						
				$grouplevels_select .= '</option>';
			}
			$grouplevels_select .= '</select>';
				
			
		}
		return $grouplevels_select;
	}
	
	function get_groups_selector(){
		
		$db = JFactory::getDBO();		
		static $grouplevels_select;		
		
		if(!$grouplevels_select){
		
			//query
			$query = $db->getQuery(true);											
			$query->select('a.id as id, a.title as title, a.parent_id as parent_id, COUNT(DISTINCT b.id) AS hyrarchy');
			$query->from('#__usergroups AS a');
			$query->leftJoin('#__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt');							
			$query->group('a.id');
			$query->order('a.lft');
			$grouplevels = $db->setQuery((string)$query);	
			$grouplevels = $db->loadObjectList();	
			
			$grouplevels_select = '<select name="joomlagroup_select" class="chzn-done">';
			$grouplevels_select .= '<option value="0">';
			$grouplevels_select .= JText::_('COM_USERS_BATCH_GROUP');
			$grouplevels_select .= '</option>';
			foreach($grouplevels as $grouplevel){
				$grouplevels_select .= '<option value="'.$grouplevel->id.'">';
				$grouplevels_select .= str_repeat('- ',$grouplevel->hyrarchy);	
				$grouplevels_select .= $grouplevel->title;						
				$grouplevels_select .= '</option>';
			}
			$grouplevels_select .= '</select>';
			
		}
		return $grouplevels_select;
	}
	
	function get_fua_groups(){
	
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$table_exists = 0;
		$groups = array();
		
		//check if tables exist				
		$config = JFactory::getConfig();
		if($this->helper->joomla_version >= '3.0'){
			$database_name = $config->get('db');	
		}else{
			$database_name = $config->getValue('db');	
		}						
		$db->setQuery("SHOW TABLES FROM `".$database_name."` ");
		$tables = $db->loadColumn();		
		$prefix = $app->getCfg('dbprefix');		
		if(in_array($prefix.'fua_usergroups', $tables)){
			//table exists	
			$table_exists = 1;		
			//get fua groups
			$query = $db->getQuery(true);
			$query->select('id, name');
			$query->from('#__fua_usergroups');			
			$query->order('ordering');
			$rows = $db->setQuery($query);				
			$rows = $db->loadObjectList();	
			
			foreach($rows as $row){
				$groups[] = array($row->id, $row->name);
			}				
		}
		
		return array($table_exists, $groups);
	}
	
}
?>