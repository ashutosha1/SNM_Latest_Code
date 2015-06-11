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

class accessmanagerModelArticles extends JModelList{

	public function __construct($config = array()){
	
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'c.id',				
				'title', 'c.title',				
				'access', 'c.position',				
				'language', 'c.language'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = NULL, $direction = NULL){
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.		
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');					
		$this->setState('filter.search', $search);	
		
		// filter access
		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', '0', 'int');					
		$this->setState('filter.access', $access);	
		
		// filter published
		$published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');					
		$this->setState('filter.published', $published);	
		
		// filter category
		$category = $app->getUserStateFromRequest($this->context.'.filter.category', 'items_category_filter', '', 'int');					
		$this->setState('filter.category', $category);	

		// filter language
		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');					
		$this->setState('filter.language', $language);	
		
		$accesscolumn = $app->getUserStateFromRequest('accessmanager.accesscolumn', 'accesscolumn', 'yes');
		$this->setState('accesscolumn', $accesscolumn);
		
		// List state information.
		parent::populateState('c.title', 'asc');		
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.category');
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
				'c.id, c.title, c.state '				
			)
		);
		$query->from('`#__content` AS c');
		
		// not content items from the trash
		//$query->where("(c.state='-1' OR c.state='0' OR c.state='1')");
		
		// join level titles
		$query->select('l.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS l ON c.access=l.id ');
		
		// join categories for category titles
		$query->select('d.title AS categorytitle');	
		$query->join('LEFT', '#__categories AS d ON d.id=c.catid ');
		
		// Filter by search
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.id = '.(int) substr($search, 3));
			} else {
				$search_id = (int)$search;
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(c.title LIKE '.$search.' OR c.id = '.$search_id.')');
			}
		}
		
		// filter access
		if($this->getState('filter.access')){	
			$query->where("(c.access='".$this->getState('filter.access')."')");
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('c.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(c.state = 0 OR c.state = 1)');
		}
		
		// filter categories
		if($this->getState('filter.category')){	
			$query->where("(c.catid='".$this->getState('filter.category')."')");
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('c.language = '.$db->quote($language));
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
		
	}
	
}
?>