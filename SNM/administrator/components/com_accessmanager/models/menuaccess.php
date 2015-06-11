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

jimport('joomla.application.component.modellist');

class accessmanagerModelMenuaccess extends JModelList{	

	public function __construct($config = array()){
	
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'm.id',				
				'title', 'm.title',								
				'language', 'm.language'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');		
		
		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search', '');		
		$this->setState('filter.search', $search);		
		
		$type = $app->getUserStateFromRequest($this->context.'.type', 'filter_type', '');
		if($type=='all'){
			$type = '';
		}elseif($type==''){
			$type = 'mainmenu';
			//get default menu itemtype
			$database->setQuery("SELECT menutype "
			."FROM #__menu "
			."WHERE home='1' "
			);
			$rows = $database->loadObjectList();
			foreach($rows as $row){	
				$type = $row->menutype;
			}
		}		
		$this->setState('filter.type', $type);		

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('m.lft', 'asc');		
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.type');		
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){
	
		// Create a new query object.
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'm.id, m.menutype, m.title, m.published, m.type, m.level, m.access'				
			)
		);
		$query->from('`#__menu` AS m');
			
		// join level titles
		//$query->select('l.title AS leveltitle');	
		//$query->join('LEFT', '#__viewlevels AS l ON m.access=l.id ');	
		
		//no backend menu items
		$query->where('m.client_id<>"1" ');			
		
		// Filter by search
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('m.id = '.(int) substr($search, 3));
			} else {
				$search_id = (int)$search;
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(m.title LIKE '.$search.' OR m.id = '.$search_id.')');
			}
		}
		
		// filter menu type
		if ($type = $this->getState('filter.type')) {
			$query->where('m.menutype = '.$db->quote($type));
		}		
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('m.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(m.published IN (0, 1))');
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('m.language = '.$db->quote($language));
		}
		
		//not the root
		$query->where('m.id<>"1" ');		
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		//echo nl2br($query);
		return $query;
		
	}
}
?>