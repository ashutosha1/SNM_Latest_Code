<?php
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class registrationproModelSession extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct() {
		parent::__construct();
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}
	function setId($id) {
		$this->_id   = $id;
		$this->_data = null;
	}

	function &getData() {
		if ($this->_loadData()) {} else  $this->_initData();
		return $this->_data;
	}

	function _loadData() {
		if (empty($this->_data)) {
			$query = "SELECT s.*, TIME_FORMAT(s.session_start_time, '%H:%i') as session_start_time, TIME_FORMAT(s.session_stop_time, '%H:%i') as session_stop_time FROM #__registrationpro_sessions as s WHERE id=".$this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData() {
		if (empty($this->_data)) {
			$sessions = new stdClass();
			$sessions->id 				  = null;
			$sessions->event_id 		  = null;
			$sessions->title 			  = null;
			$sessions->description 		  = null;
			$sessions->fee				  = null;
			$sessions->feetype            = null;
			$sessions->weekday 			  = null;
			$sessions->session_date 	  = null;
			$sessions->session_start_time = null;
			$sessions->session_stop_time  = null;
			$sessions->page_header 		  = null;
			$sessions->published 		  = null;
			$sessions->ordering			  = null;

			$this->_data = $sessions;
			return (boolean) $this->_data;
		}
		return true;
	}

	function store($data) {
		$tmp = '';
		foreach($data as $key=>$val){
			if(strpos($key, 'session_date') === false) {} else {
				if($key !== 'session_date'){
					$data['session_date'] = $val;
					$tmp = $key;
				}
			}
		}
		if ($tmp !== '') unset($data[$tmp]);
	
		$registrationproAdmin = new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		$user = JFactory::getUser();
		$config = JFactory::getConfig();
		$tzoffset = $config->get('config.offset');
		$row = $this->getTable('registrationpro_sessions', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		jimport('joomla.filesystem.file');
		$row->id = (int) $row->id;
		$row->reorder('event_id = '.(int) $row->event_id);
		$nullDate = $this->_db->getNullDate();

		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		return $row->id;
	}

	function copysessions($copied_eventid, $assign_eventid) {
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id=".$copied_eventid;
		$this->_db->setQuery($query);
		$sessions  = $this->_db->loadObjectList();

		foreach($sessions as $key=>$value){
			$row = $this->getTable('registrationpro_sessions', '');

			$row->event_id			 = $assign_eventid;
			$row->title				 = $sessions[$key]->title;
			$row->description		 = $sessions[$key]->description;
			$row->fee				 = $sessions[$key]->fee;
			$row->feetype			 = $sessions[$key]->feetype;
			$row->weekday			 = $sessions[$key]->weekday;
			$row->session_date		 = $sessions[$key]->session_date;
			$row->session_start_time = $sessions[$key]->session_start_time;
			$row->session_stop_time	 = $sessions[$key]->session_stop_time;
			$row->page_header		 = $sessions[$key]->page_header;
			$row->published			 = $sessions[$key]->published;
			$row->ordering			 = $sessions[$key]->ordering;

			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}

	function get_session_page_header($eventid) {
		$query = 'SELECT session_page_header FROM #__registrationpro_dates WHERE id ='.$eventid;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	function save_page_header($page_header, $eventid) {
		$query = 'UPDATE #__registrationpro_dates SET session_page_header = "'.$page_header.'" WHERE id ='.$eventid;
		$this->_db->setQuery($query);
		$this->_db->query();
	}

}
?>