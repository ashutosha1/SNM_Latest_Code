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

class accessmanagerModelComponentsbackend extends JModelList{	

	protected function populateState($ordering = NULL, $direction = NULL){
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.		
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');					
		$this->setState('filter.search', $search);	

		// List state information.
		parent::populateState('e.name', 'asc');		
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');

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
				'e.extension_id, e.name, e.element'				
			)
		);
		$query->from('`#__extensions` AS e');
		
		// join level titles
		$query->select('l.title AS leveltitle');	
		$query->join('LEFT', '#__viewlevels AS l ON e.access=l.id ');	
		
		$query->where('e.type="component" ');		
		
		// Filter by search
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('e.extension_id = '.(int) substr($search, 3));
			} else {
				$search_id = (int)$search;
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(e.name LIKE '.$search.' OR e.element LIKE '.$search.' OR e.extension_id = '.$search_id.')');
			}
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