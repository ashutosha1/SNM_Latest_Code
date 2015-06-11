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

class registrationproModelLocation extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		
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
			$query = "SELECT * FROM #__registrationpro_locate WHERE id =".$this->_id;
			
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
			$locate = new stdClass();
			
			$locate->id 			= null;
			$locate->club 			= null;
			$locate->url 			= null;
			$locate->street 		= null;
			$locate->plz 			= null;
			$locate->city 			= null;
			$locate->country 		= null;
			$locate->latitude 		= null;
			$locate->longitude 		= null;
			$locate->locdescription = null;
			$locate->locimage 		= null;
			$locate->sendernameloc 	= null;	
			$locate->sendermailloc 	= null;
			$locate->deliveriploc 	= null;
			$locate->deliverdateloc	= null;
			$locate->publishedloc 	= null;
			$locate->checked_out	= null;
			$locate->checked_out_time = null;	
			$locate->ordering 		= null;													
			$this->_data			= $locate;
			return (boolean) $this->_data;
		}
		return true;
	}
	
	
	function store($data)
	{
		$registrationproAdmin = new registrationproAdmin;		
		$repgrosettings	= $registrationproAdmin->config();
		$user		= JFactory::getUser();
		$config 	= JFactory::getConfig();

		$tzoffset 	= $config->get('config.offset');

		$row  =$this->getTable('registrationpro_locate', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
				
		$row->id = (int) $row->id;
								
		$nullDate	= $this->_db->getNullDate();
						
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		
		// Check the locations and update item order
		$row->checkin();
		$row->reorder();

		return $row->id;
	}			
}

?>