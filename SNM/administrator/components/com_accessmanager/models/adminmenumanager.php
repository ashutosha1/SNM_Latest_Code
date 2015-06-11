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

class accessmanagerModelAdminmenumanager extends JModelList{

	public function __construct($config = array()){
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'i.id',				
				'title', 'i.title',					
				'menu', 'i.menu'				
			);
		}

		parent::__construct($config);
	}	
	
	protected function populateState($ordering = NULL, $direction = NULL){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication('administrator');	
		$ds = DIRECTORY_SEPARATOR;	
		
		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search', '');		
		$this->setState('filter.search', $search);		
		
		$type = $app->getUserStateFromRequest($this->context.'.type', 'filter_type', '');
		if($type=='all' || !file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'controller.php')){
			$type = '';
		}elseif($type==''){			
			//get first menu			
			$query = $database->getQuery(true);
			$query->select('id');
			$query->from('#__adminmenumanager_menus');			
			$query->order('name');
			$rows = $database->setQuery($query);				
			$rows = $database->loadObjectList();
				
			foreach($rows as $row){		
				$type = $row->id;	
				break;
			}
		}
		$this->setState('filter.type', $type);		

		// List state information.
		parent::populateState('i.ordertotal', 'asc');	
	}
	
	protected function getStoreId($id = ''){			
		
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.type');	

		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){
	
		// Create a new query object.
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		$ds = DIRECTORY_SEPARATOR;
		
		if(!file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_adminmenumanager'.$ds.'controller.php')){
			//admin-menu-manager is not installed, so parse bogus query or else the database object goes bananas
			//would be easyer if we could parse an empty query and get an empty object back
			$query->select('*');
			$query->from('#__accessmanager_config');
			$query->where('id='.$db->q('nothing'));
		}else{
			//admin-menu-manager is installed
		
			// Select the required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'i.*'				
				)
			);
			$query->from('`#__adminmenumanager_menuitems` AS i');	
			
			// Filter by search
			$search = $this->getState('filter.search');
			if (!empty($search)) {
				if (stripos($search, 'id:') === 0) {
					$query->where('i.id = '.(int) substr($search, 3));
				} else {
					$search_id = (int)$search;
					$search = $db->Quote('%'.$db->escape($search, true).'%');
					$query->where('(i.title LIKE '.$search.' OR i.id = '.$search_id.')');
				}
			}
				
			// filter menu type
			if ($type = $this->getState('filter.type')) {
				$query->where('i.menu = '.$db->quote($type));
			}				
			
			// Add the list ordering clause.
			$orderCol	= $this->state->get('list.ordering');
			$orderDirn	= $this->state->get('list.direction');		
			$query->order($db->escape($orderCol.' '.$orderDirn));
		
		
		}
		
		//echo nl2br($query);
		return $query;
		
	}
}
?>