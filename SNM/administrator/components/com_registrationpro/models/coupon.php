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

class registrationproModelCoupon extends JModelLegacy
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
			$query = "SELECT * FROM #__registrationpro_coupons WHERE id =".$this->_id;
			
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
			$coupons = new stdClass();						
			$coupons->id 			= null;
			$coupons->title 		= null;
			$coupons->code 			= null;
			$coupons->discount 		= null;
			$coupons->discount_type = null;
			$coupons->max_amount 	= null;
			$coupons->start_date 	= null;
			$coupons->end_date 		= null;
			$coupons->published 	= null;
			$coupons->status 		= null;
			$coupons->eventids 		= null;		
			$coupons->checked_out 	= null;
			$coupons->checked_out_time 	= null;
			$coupons->ordering		= null;														
			$this->_data			= $coupons;
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

		$row  =$this->getTable('registrationpro_coupons', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
				
		$row->id = (int) $row->id;
				
		if(!$row->id)
			$row->ordering = $row->getNextOrder();

		$nullDate	= $this->_db->getNullDate();
						
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}
		
		return $row->id;
	}
	
	function check_code_exists($code, $coupon_id)
	{
		if($coupon_id){
			$where = " AND id != $coupon_id";
		}else{
			$where = "";
		}

		$query = "SELECT count(*) FROM #__registrationpro_coupons WHERE code='".$code."'".$where;
		$this->_db->setQuery($query);
		$code_exists = $this->_db->loadResult();
		
		if($code_exists > 0){
			return true;
		}else{
			return false;
		}				
	}
	
	function getEvents(){			
		$query = "SELECT id AS value, titel AS text"
			. "\nFROM #__registrationpro_dates"
			. "\nWHERE published = 1"
			. "\nORDER BY titel";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();				
	}		
}
?>