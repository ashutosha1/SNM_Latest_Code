<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL		
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
 *
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationproModelMycategory extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		/*$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);*/
		$id = JRequest::getVar('id',0,'','int');
		$this->setId((int)$id);
		
	}
	function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}

	function &getData()
	{
		if ($this->_loadData())
		{
			// nothing
		}else{
			  $this->_initData();
		}

		return $this->_data;
	}
	
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = "SELECT * FROM #__registrationpro_categories WHERE id =".$this->_id;
			
			$this->_db->setQuery($query);

			$this->_data = $this->_db->loadObject();

			return (boolean) $this->_data;
		}
		return true;
	}
	
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$events = new stdClass();
			
			$events->id 			= null;
			$events->catname 		= null;
			$events->catdescription = null;
			$events->image 			= null;
			$events->background 	= null;
			$events->publishedcat 	= null;
			$events->checked_out 	= null;	
			$events->checked_out_time = null;
			$events->access 		= 1;
			$events->ordering 		= null;					
			$this->_data			= $events;
			return (boolean) $this->_data;
		}
		return true;
	}
	
	
	function store($data)
	{
		$repgrosettings = registrationproAdmin::config();
		$user		=  JFactory::getUser();
		$config 	=  JFactory::getConfig();

		$tzoffset 	= $config->get('config.offset');

		$row  =& $this->getTable('registrationpro_categories', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
				
		$row->id = (int) $row->id;
		
		
		/*if(!$row->id)
			$row->ordering = $row->getNextOrder();*/

		$nullDate	= $this->_db->getNullDate();
						
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		
		// Check the categories and update item order
		$row->checkin();
		$row->reorder();

		return $row->id;
	}
			
	function getEvents($event_id){
		$this->_db->setQuery("SELECT * FROM #__registrationpro_dates WHERE published = 1");
		$products = $this->_db->loadObjectList();
		return $prods;
	}					
}

?>