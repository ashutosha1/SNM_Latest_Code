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

class registrationproModelEventdiscount extends JModelLegacy {
	var $_id   = null;
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
		if ($this->_loadData()){}else  $this->_initData();
		return $this->_data;
	}

	function _loadData() {
		if (empty($this->_data)) {
			$query = "SELECT * FROM #__registrationpro_event_discount WHERE id=".$this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData() {
		if (empty($this->_data)) {
			$tickets = new stdClass();

			$event_discount->id 				 = null;
			$event_discount->event_id 			 = null;
			$event_discount->discount_name 		 = null;
			$event_discount->discount_amount 	 = null;
			$event_discount->discount_type		 = null;
			$event_discount->discount_type 		 = null;
			$event_discount->early_discount_date = null;
			$event_discount->published 			 = null;
			$event_discount->checked_out 		 = null;
			$event_discount->checked_out_time 	 = null;
			$event_discount->ordering 			 = null;
			$this->_data = $event_discount;
			return (boolean) $this->_data;
		}
		return true;
	}

	function store($data) {
		$tmp = '';
		foreach($data as $key=>$val){
			if(strpos($key, 'early_discount_date') === false) {} else {
				if($key !== 'early_discount_date'){
					$data['early_discount_date'] = $val;
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
		$row = $this->getTable('registrationpro_event_discount', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		$row->id = (int) $row->id;
		$row->ordering 	= $row->getNextOrder();
		$nullDate = $this->_db->getNullDate();

		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg());
			return false;
		}

		return $row->id;
	}


	function copydiscounts($copied_eventid, $assign_eventid) {
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id=".$copied_eventid;
		$this->_db->setQuery($query);
		$tickets  = $this->_db->loadObjectList();

		foreach($tickets as $key=>$value){
			$row  =$this->getTable('registrationpro_event_discount', '');

			$row->event_id				=	$assign_eventid;
			$row->discount_name			=	$tickets[$key]->discount_name;
			$row->discount_amount		=	$tickets[$key]->discount_amount;
			$row->discount_type			=	$tickets[$key]->discount_type;
			$row->min_tickets			=	$tickets[$key]->min_tickets;
			$row->early_discount_date	=	$tickets[$key]->early_discount_date;
			$row->published				=	$tickets[$key]->published;
			$row->checked_out 			=	$tickets[$key]->checked_out;
			$row->checked_out_time		=	$tickets[$key]->checked_out_time;
			$row->ordering				=	$tickets[$key]->ordering;

			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}
		return true;
	}

}
?>