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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationproModelTicket extends JModelLegacy
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

		}
		else  $this->_initData();

		return $this->_data;
	}

	function _loadData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = "SELECT * FROM #__registrationpro_payment WHERE id=".$this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$tickets = new stdClass();
			$tickets->id 					= null;
			$tickets->regpro_dates_id 		= null;
			$tickets->product_name 			= null;
			$tickets->product_description 	= null;
			$tickets->product_price			= null;
			$tickets->tax 					= null;
			$tickets->total_price 			= null;
			$tickets->shipping 				= null;
			$tickets->ordering 				= null;
			$tickets->type 					= null;
			$tickets->product_quantity		= null;
			$tickets->ticket_start			= null;
			$tickets->ticket_end			= null;
			$this->_data					= $tickets;
			return (boolean) $this->_data;
		}
		return true;
	}

	function getTicketById($id) {
		$res = array();
		$query = "SELECT * FROM #__registrationpro_payment WHERE id=".$id;
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList();
		return $res[0];
	}

	function store($data) {
		$registrationproAdmin = new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		$user   = JFactory::getUser();
		$config = JFactory::getConfig();

		$tzoffset = $config->get('config.offset');

		$row = $this->getTable('registrationpro_payment', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		jimport('joomla.filesystem.file');
		$row->id = (int)$row->id;

		$row->ordering = $row->getNextOrder();
		$ticket = @$this->getTicketById($row->id);
		if(($ticket)&&(isset($ticket)&&(isset($ticket->ordering) && ($ticket->ordering !== '')))) {
			$row->ordering = (int) $ticket->ordering;
		}

		$nullDate = $this->_db->getNullDate();

		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		
		return $row->id;
	}


	function copytickets($copied_eventid, $assign_eventid) {
		// get all tickets of event
		$query = "SELECT * FROM #__registrationpro_payment WHERE regpro_dates_id=".$copied_eventid;
		$this->_db->setQuery($query);
		$tickets = $this->_db->loadObjectList();
		//echo '<pre>';print_r($tickets);echo '</pre>';die;
		foreach($tickets as $key=>$value){
			$row  = $this->getTable('registrationpro_payment', '');

			$row->regpro_dates_id		=	$assign_eventid;
			$row->product_name			=	$tickets[$key]->product_name;
			$row->product_description	=	$tickets[$key]->product_description;
			$row->product_price			=	$tickets[$key]->product_price;
			$row->tax					=	$tickets[$key]->tax;
			$row->total_price			=	$tickets[$key]->total_price;
			$row->shipping				=	$tickets[$key]->shipping;
			$row->ordering 				=	$tickets[$key]->ordering;
			$row->type					=	$tickets[$key]->type;
			$row->product_quantity		=	$tickets[$key]->product_quantity;
			$row->ticket_start		    =	$tickets[$key]->ticket_start;
			$row->ticket_end     		=	$tickets[$key]->ticket_end;


			// Store it in the db
			if (!$row->store()) {
				JError::raiseError(500, $this->_db->getErrorMsg() );
				return false;
			}
		}

		return true; //$row->id;
	}



}//Class end
?>