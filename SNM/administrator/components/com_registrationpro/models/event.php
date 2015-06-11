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

class registrationproModelEvent extends JModelLegacy
{
	var $_id = null;
	var $_data = null;

	function __construct() {
		parent::__construct();
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	function setId($id)	{
		$this->_id = $id;
		$this->_data = null;
	}

	function &getData()	{
		if ($this->_loadData()){}
		else  $this->_initData();
		return $this->_data;
	}

	function _loadData() {
		if (empty($this->_data)) {
			$query = "SELECT a.*, l.club, l.city, c.catname, u.name AS editor "
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\n LEFT JOIN #__users AS u ON u.id = a.checked_out WHERE a.id=".$this->_id;

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$events = new stdClass();
			$events->id 				= null;
			$events->parent_id			= 0;
			$events->locid 				= null;
			$events->catsid 			= null;
			$events->dates 				= null;
			$events->times 				= null;
			$events->enddates 			= null;
			$events->endtimes 			= null;
			$events->titel 				= null;
			$events->image 				= 0;
			$events->datdescription 	= null;
			$events->shortdescription 	= null;
			$events->datimage 			= null;
			$events->sendername 		= null;
			$events->sendermail 		= null;
			$events->deliverip 			= null;
			$events->deliverdate 		= null;
			$events->published 			= 1;
			$events->registra 			= null;
			$events->unregistra 		= null;
			$events->notifydate 		= null;
			$events->checked_out 		= null;
			$events->checked_out_time 	= null;
			$events->access 			= null;
			$events->regstart 			= null;
			$events->regstop 			= null;
			$events->regstop_type		= null;
			$events->reqactivation 		= null;
			$events->status 			= null;
			$events->form_id 			= null;
			$events->max_attendance 	= null;
			$events->terms_conditions 	= null;
			$events->ordering 			= null;
			$events->datarange			= null;
			$events->allowgroup			= null;
			$events->notifyemails		= null;
			$events->recurrence_id		= null;
			$events->recurrence_type	= null;
			$events->recurrence_number	= null;
			$events->recurrence_weekday	= null;
			$events->recurrence_counter	= null;
			$events->gateway_account	= null;
			$events->force_groupregistration = null;
			$events->session_page_header = null;
			$events->enable_mailchimp	 = null;
			$events->mailchimp_list		 = null;
			$events->enable_create_user	 = null;
			$events->enabled_user_group	 = null;
			$this->_data = $events;
			return (boolean) $this->_data;
		}
		return true;
	}

	function store($data) {
	
		$user = JFactory::getUser();
		$uid = $user->id;
		$data['user_id'] = $uid;

		if(isset($data['enable_mailchimp'])){
			$data['enable_mailchimp'] = $data['enable_mailchimp'];
		}else $data['enable_mailchimp'] = '0';

		$registrationproAdmin = new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		$config	= JFactory::getConfig();
		$tzoffset = $config->get('config.offset');

		$row = $this->getTable('registrationpro_dates', '');

		if (!$row->bind($data)) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		$row->id = (int)$row->id;
		$nullDate = $this->_db->getNullDate();

		$img = $data['event_image']; // old, new, del
		if(strtolower($img) == 'new') $row->image = 1;
		if(strtolower($img) == 'del') $row->image = 0;
		
		$pdfimg = $data['event_pdfimage']; // old, new, del
		if(strtolower($pdfimg) == 'new') $row->pdfimage = 1;
		if(strtolower($pdfimg) == 'del') $row->pdfimage = 0;
		
		if($row->ordering >= 10000) $row->status = '0';
		
		// Store it in the db
		if (!$row->store()) {
			JError::raiseError(500, $this->_db->getErrorMsg() );
			return false;
		}

		$prefix = "../images/regpro/";
		if(strtolower($img) == 'new') {
			$tmpImage = $prefix."system/temporary_uploaded_image.jpg";
			$newImage = $prefix."events/event_".$row->id.".jpg";
			if (!copy($tmpImage, $newImage)) {
				//echo "failed to copy $tmpImage to $newImage...\n"; exit;
			};
		}
		
		if(strtolower($img) == 'copy') {
			$tmpImage = $prefix."events/".$data['image_name'];
			$newImage = $prefix."events/event_".$row->id.".jpg";
			if (!copy($tmpImage, $newImage)) {
				//echo "failed to copy $tmpImage to $newImage...\n"; exit;
			};
		}
		
		if((trim($img) == '') && ($row->ordering >= 10000)) {
			$tmpImage = $prefix."events/event_".$row->parent_id.".jpg";
			$newImage = $prefix."events/event_".$row->id.".jpg";
			if (!copy($tmpImage, $newImage)) {
				//echo "failed to copy $tmpImage to $newImage...\n"; exit;
			};
		}
		
		if(strtolower($pdfimg) == 'new') {
			$tmpImage = $prefix."system/temporary_uploaded_pdfimage.jpg";
			$newImage = $prefix."events/pdfevent_".$row->id.".jpg";
			if (!copy($tmpImage, $newImage)) {
				//echo "failed to copy $tmpImage to $newImage...\n"; exit;
			};
		}
		
		if(strtolower($img) == 'copy') {
			$tmpImage = $prefix."events/".$data['pdfimage_name'];
			$newImage = $prefix."events/pdfevent_".$row->id.".jpg";
			if (!copy($tmpImage, $newImage)) {
				//echo "failed to copy $tmpImage to $newImage...\n"; exit;
			};
		}
		
		if((trim($img) == '') && ($row->ordering >= 10000)) {
			$tmpImage = $prefix."events/pdfevent_".$row->parent_id.".jpg";
			$newImage = $prefix."events/pdfevent_".$row->id.".jpg";
			if (!copy($tmpImage, $newImage)) {
				//echo "failed to copy $tmpImage to $newImage...\n"; exit;
			};
		}

		// Check the events and update item order
		$row->checkin();
		$row->reorder('catsid = '.(int)$row->catsid.' AND ordering<10000');

		return $row->id;
	}

	function getRegistered($rdid){
		$this->_db->setQuery("SELECT products,status FROM #__registrationpro_register as r, #__registrationpro_transactions as t  WHERE r.rdid = '$rdid' AND r.rid = t.reg_id AND r.active=1 GROUP BY r.rid");
		$products = $this->_db->loadObjectList();
		$prods = array(0=>0);

		foreach($products as $product){
			$expl = explode("¶\n",$product->products);

			foreach($expl as $rw){
				if($rw!=''){
					$expl2= explode("=",$rw);
					if(isset($expl2[1])){
							if(!isset($prods[$expl2[0]]))$prods[$expl2[0]]=0;
							if(!isset($prods['status'][$product->status]))$prods['status'][$product->status]=0;
							$prods[$expl2[0]] += $expl2[1];
							$prods['status'][$product->status] += $expl2[1];
					}else{
						$prods[0]+=1;
						$prods['status'][$product->status] += 1;
					}
				}
			}
		}
		return $prods;
	}

	function getTickets($eventid) {
		$query = "SELECT * FROM #__registrationpro_payment WHERE regpro_dates_id = '$eventid' ORDER BY ordering";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getEvent_discounts($eventid) {
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id = '$eventid' ORDER BY ordering";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getEvent_sessions($eventid) {
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id = '$eventid' ORDER BY session_date, ordering";
		$query = "SELECT * FROM #__registrationpro_sessions WHERE event_id = '$eventid' ORDER BY ordering";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getLocations(){
		$query = "SELECT id AS value, club AS text"
			. "\nFROM #__registrationpro_locate"
			. "\nWHERE publishedloc = 1"
			. "\nORDER BY ordering";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getCategories(){
		$query = "SELECT id AS value, catname AS text"
			. "\nFROM #__registrationpro_categories"
			. "\nWHERE publishedcat = 1"
			. "\nORDER BY ordering";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getForms(){
		$query = "SELECT id AS value, title AS text"
			. "\nFROM #__registrationpro_forms"
			. "\nWHERE published = 1"
			. "\nORDER BY title = 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function clearrecurrence($eventids = array())
	{
		if(is_array($eventids) && count($eventids) > 0){
			$query = "UPDATE #__registrationpro_dates SET recurrence_id = 0, recurrence_type = 0, recurrence_number = 0, recurrence_weekday = 0,recurrence_counter = '0000-00-00' WHERE id in (".implode(",", array_filter($eventids)).")";
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			} else return true;
		} else return true;
	}

	// **************  Event report functions ************************ //
	function getEventReportData()
	{
		$registrationproAdmin = new registrationproAdmin;
		$repgrosettings	= $registrationproAdmin->config();
		
		
		if($repgrosettings['accepted_registration_reports'] == 1)
		{
			$accepted = " r.status=1 ";
		}else{
			$accepted = " r.active=1 ";
		}
		$query  = "SELECT e.*,r.*,l.* FROM #__registrationpro_dates as e,#__registrationpro_register as r,#__registrationpro_locate as l,";
		$query .= "#__registrationpro_transactions as t WHERE l.publishedloc=1 AND e.locid=l.id AND e.id=r.rdid AND e.id = ".$this->_id;
		$query .= " AND r.rid = t.reg_id AND".$accepted."GROUP BY r.rid";
		
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();
		/* echo "<pre>";
		print_r($data);die(); */
		foreach($data as $key=>$value)
		{
			$data[$key]->params = unserialize($data[$key]->params);
		}

		return $data;
	}

	function getEventTransactionData()
	{
		$query = "SELECT t.*, edt.event_discount_amount, edt.event_discount_type FROM #__registrationpro_register as r, #__registrationpro_transactions as t "
				 ."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				 ."\n WHERE r.rid = t.reg_id AND r.rdid =".$this->_id;

		$this->_db->setQuery($query);
		$productdata = $this->_db->loadObjectList();

		return $productdata;
	}

	function getAdditionalFormFeesTransactionData()
	{
		$query = "SELECT a.* FROM #__registrationpro_additional_from_field_fees as a "
				."\n LEFT JOIN #__registrationpro_register AS r ON r.rid = a.reg_id"
				."\n WHERE r.rdid = ".$this->_id;

		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		return $data;
	}

	function getSessionsfeesTransactionData()
	{
		$query = "SELECT a.* FROM #__registrationpro_session_transactions as a "
				."\n LEFT JOIN #__registrationpro_register AS r ON r.rid = a.reg_id"
				."\n WHERE r.rdid = ".$this->_id;

		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		return $data;
	}

	function getUserinfoForExcelReport()
	{
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		
		$query = "SELECT r.params,r.firstname,r.lastname,r.email,r.uregdate, t.*, edt.event_discount_amount, edt.event_discount_type "
				."\n FROM #__registrationpro_dates as e , #__registrationpro_register as r,#__registrationpro_locate as l, #__registrationpro_transactions as t "
				."\n LEFT JOIN #__registrationpro_event_discount_transactions AS edt ON t.id = edt.trans_id"
				. $where
				. $orderby;

		//echo $query; exit;
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		$additional_form_fees 	= $this->getAdditionalFormFeesTransactionData(); // get additional form fees data
		$session_fees 			= $this->getSessionsfeesTransactionData(); // get session fees data

		// apply event discount upon user tickets
		foreach($data as $pkey => $pvalue)
		{
			if($pvalue->event_discount_amount > 0){
				if($pvalue->event_discount_type == 'P'){
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$actual_price_without_per 		= ($pvalue->price * 100) / (100 - $pvalue->event_discount_amount);
					$event_discounted_amount_price 	= $actual_price_without_per * $pvalue->event_discount_amount / 100;
					$pvalue->discount_amount		+= $event_discounted_amount_price;
					$pvalue->price 			 		= $actual_price_without_per;
				}else{
					$pvalue->discount_amount		+= $pvalue->event_discount_amount;
				}

				$pvalue->final_price = 	$pvalue->price - $pvalue->discount_amount;

				if($pvalue->final_price <= 0){
					$pvalue->final_price = 0;
				}
			}

			// assign additional form fields fees
			$additional_field_fees_total = 0.00;
			if(count($additional_form_fees) > 0 && is_array($additional_form_fees) && $pvalue->p_type == "E"){
				foreach($additional_form_fees as $akey => $avalue)
				{
					if($avalue->reg_id == $pvalue->reg_id) {
						$additional_field_fees_total += $avalue->additional_field_fees;
					}
				}
			}

			$pvalue->additional_field_fees = $additional_field_fees_total;
			// end

			// assign session fees
			$session_fees_total = 0.00;
			if(count($session_fees) > 0 && is_array($session_fees) && $pvalue->p_type == "E"){
				foreach($session_fees as $skey => $svalue)
				{
					if($svalue->reg_id == $pvalue->reg_id) {
						$session_fees_total += $svalue->session_fees;
					}
				}
			}

			$pvalue->session_fees = $session_fees_total;
			// end

		}

		return $data;
	}

	function getUserGroups()
	{
		$query = "SELECT a.id,a.title FROM #__usergroups as a";

		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();

		return $data;
	}
	
	function _buildContentOrderBy()
	{
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.users.filter_order', 'filter_order', 'r.uregdate', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.users.filter_order_Dir', 'filter_order_Dir', '', 'word' );	
		
		if ($filter_order == ''){
			$orderby 	= ' ORDER BY r.uregdate'.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , r.uregdate ';
		}
		//echo $orderby,"<br>";
		return $orderby;
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.filter_state', 'filter_state', '', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $this->_db->escape( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = 'r.status = 0';
			} else if ($filter_state == 'A') {
				$where[] = 'r.status = 1';
			} else if ($filter_state == 'W') {
				$where[] = 'r.status = 2';
			} else if ($filter_state == 'PP') {
				$where[] = 't.payment_status = "pending"';
			} else if ($filter_state == 'CP') {
				$where[] = 't.payment_status = "completed"';
			}
		}				

		if ($search && $filter == 1) {
			$where[] = ' LOWER(r.firstname) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 2) {
			$where[] = ' LOWER(r.lastname) LIKE \'%'.$search.'%\' ';
		}
		
		if ($search && $filter == 3) {
			$where[] = ' LOWER(r.email) LIKE \'%'.$search.'%\' ';
		}				
		
		$where[] 	= "l.publishedloc=1 ";
		$where[] 	= "e.locid=l.id ";
		$where[] 	= "e.id=r.rdid";
		$where[] 	= "e.id = ".$this->_id;
		$where[] 	= "r.rid = t.reg_id";
		//$where[] 	= "t.p_type = 'E'";
		$where[] 	= "r.active=1 group by t.reg_id";				

		$where		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

}

?>