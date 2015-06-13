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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class registrationproControllerMyevents extends registrationproController
{
	function __construct() {
		parent::__construct();
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );
	}

	function publish()
	{
		global $Itemid;
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = $this->getModel('myevents');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_PUBL');
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect( $link, $msg );
	}

	function unpublish()
	{
		global $Itemid;
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}
		$model = $this->getModel('myevents');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}
		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_UNPUBL');
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect($link, $msg );
	}

	function archive()
	{
		global $Itemid;
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}
		$model = $this->getModel('myevents');
		if(!$model->publish($cid, -1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}
		$total = count( $cid );
		$msg 	= $total.' '.JText::_('_ADMIN_EVENTS_SUC_ARCH');
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect( $link, $msg );
	}

	function unarchive()
	{
		global $Itemid;
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}
		$model = $this->getModel('myevents');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}
		$total = count( $cid );
		$msg 	= $total.' '.JText::_('_ADMIN_EVENTS_SUC_UNARCH');
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect($link, $msg );
	}

	function archive_cancel()
	{
		global $Itemid;
		JRequest::checkToken() or die( 'Invalid Token' );
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect($link, $msg );
	}

	function cancel()
	{
		global $Itemid;
		JRequest::checkToken() or die( 'Invalid Token' );
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect($link, $msg );
	}

	function delete()
	{
		$model 	= $this->getModel('myevents');
		$user	= JFactory::getUser();
		parent::display();
	}

	function remove()
	{
		global $option, $Itemid;
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		$model = $this->getModel('myevents');
		$msg = $model->delete($cid).' '.JText::_('ADMIN_EVENTS_DEL');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		$link	= JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
		$this->setRedirect($link, $msg );
	}

	function remove_archive()
	{
		global $option, $Itemid;

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to delete' ) );

		$model = $this->getModel('myevents');
		$msg = $model->delete($cid).' '.JText::_('ADMIN_EVENTS_DEL');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		$link	= JRoute::_('index.php?option=com_registrationpro&view=archives&Itemid='.$Itemid,false);
		$this->setRedirect($link, $msg );
	}

	function copy()
	{
		JRequest::setVar( 'view', 'myevent' );
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();
	}


	function edit( )
	{
		JRequest::setVar( 'view', 'myevent' );
		parent::display();
	}


	function save()
	{
		global $Itemid;
		JRequest::checkToken() or die( 'Invalid Token' );

		$task		= JRequest::getVar('task');
		$copy		= JRequest::getVar('copy', 0);
		$event_id	= JRequest::getVar('event_id', 0,'','int');
		$regpro_registrations_emails = new regpro_registrations_emails;
		$registrationproHelper = new registrationproHelper;
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);

		if($registrationproHelper->checkUserModerator() && !$post['id']) $post['moderating_status'] = 0;
		$post['payment_method'] = implode(",",$post['payment_method']);
		$model = $this->getModel('myevent');

		if ($returnid = $model->store($post)) {

			if($copy)
			{
				$ticket_model = $this->getModel('ticket');
				$ticket_model->copytickets($event_id, $returnid);
				$discount_model = $this->getModel('eventdiscount');
				$discount_model->copydiscounts($event_id, $returnid);
			}

			if($post['recurrence_type'] <> 0 && $post['recurrence_number'] <> 0 && $returnid){
				$session			= JFactory::getSession();
				$this->calculate_recurrence($returnid);	 // create events
				$session_eventids	= $session->get('eventids');
				$delrecurrence 		= $model->clearrecurrence($session_eventids); // clear recurrence values of all created events
				$session->clear('eventids');
			}

			if($post['notify'])	$regpro_registrations_emails->send_StautsChange_email($post['id']);
			$post['returnid'] = $returnid;
			$api =& regpro_api::getInstance();
			if ($post['id'] == NULL) $api->triggerEvent('onEventCreate', $post );
			else $api->triggerEvent('onEventCreate', $post );

			$registrationproHelper = new registrationproHelper;
			if($registrationproHelper->checkUserModerator()) {
				$regpro_registrations_emails->send_Moderator_email($returnid);
			}
			switch ($task)
			{
				case 'apply' :
					$link = JRoute::_('index.php?option=com_registrationpro&controller=myevents&view=myevent&hidemainmenu=1&cid[]='.$returnid.'&Itemid='.$Itemid,false);
					break;
				default :
					$link = JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);
					break;
			}

			$msg	= JText::_( 'ADMIN_EVENTS_SAVE');



			$cache = JFactory::getCache('com_registrationpro');

			$cache->clean();



		} else {



			$msg 	= '';

			$link = JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);

		}



		$this->setRedirect( $link, $msg );

 	}



	 /**

     * Handle the task 'orderup'

     * @access private

     */

    function orderupevents()

    {

		global $Itemid;



        JRequest::checkToken() or jexit( 'Invalid Token' );



		/*$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );



		//echo "<pre>"; print_r($_POST); exit;

        $model = $this->getModel('events');

        $model->move(-1,$cid[0]);



        $link = 'index.php?option=com_registrationpro&view=events';

        $this->setRedirect( $link );*/

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');



		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );



		if (isset( $cid[0] ))

		{

			$row = & JTable::getInstance('registrationpro_dates','');

			$row->load( (int) $cid[0] );

			$row->move(-1, 'catsid = '.(int) $row->catsid);



			$cache =  JFactory::getCache('com_registrationpro');

			$cache->clean();

		}



		$link = JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);



        $this->setRedirect( $link );

    }





    /**

     * Handle the task 'orderdown'

     * @access private

     */

    function orderdownevents()

    {

		global $Itemid;



        JRequest::checkToken() or jexit( 'Invalid Token' );



		/*$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );



       	$model = $this->getModel('events');

        $model->move(1,$cid[0]);



        $link = 'index.php?option=com_registrationpro&view=events';

        $this->setRedirect( $link );*/



		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');



		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );



		if (isset( $cid[0] ))

		{

			$row = & JTable::getInstance('registrationpro_dates','');

			$row->load( (int) $cid[0] );

			$row->move(1, 'catsid = '.(int) $row->catsid);



			$cache =  JFactory::getCache('com_registrationpro');

			$cache->clean();

		}



		$link = JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);



        $this->setRedirect( $link );

    }





    /**

     * Handle the task 'saveorder'

     * @access private

     */

    function saveorder()

    {

		global $Itemid;



        JRequest::checkToken() or jexit( 'Invalid Token' );

        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );

        $order = JRequest::getVar( 'order', array(), 'post', 'array' );



        JArrayHelper::toInteger($cid);

        JArrayHelper::toInteger($order);



        $model = $this->getModel('events');

        $model->saveorder($cid, $order);



		$link = JRoute::_('index.php?option=com_registrationpro&view=myevents&Itemid='.$Itemid,false);



        $this->setRedirect( $link );

    }



	/**

	* this method calculate the next date

	*/

	function calculate_recurrence($recurrence_parentid = 0) {

		$db			=  JFactory::getDBO();



		$nulldate = '0000-00-00';

		$query = 'SELECT * FROM #__registrationpro_dates WHERE (IF (enddates <> '.$nulldate.', enddates, dates)) AND recurrence_number <> "0" AND recurrence_type <> "0" AND id = '.$recurrence_parentid;

		$db->setQuery( $query );

		$recurrence_array = $db->loadAssocList();



		//echo "<pre>"; print_r($recurrence_array); exit;



		foreach($recurrence_array as $recurrence_row) {

			$insert_keys = '';

			$insert_values = '';

			$wherequery = '';



			// get the recurrence information

			$recurrence_number = $recurrence_row['recurrence_number'];

			$recurrence_type = $recurrence_row['recurrence_type'];



			$day_time = 86400;	// 60 sec. * 60 min. * 24 h

			$week_time = 604800;// $day_time * 7days

			$date_array = $this->generate_date($recurrence_row['dates'], $recurrence_row['enddates']);



			//echo "<pre>"; print_r($date_array);

			//echo "<pre>"; print_r($recurrence_number);

			//echo "<pre>"; print_r($recurrence_type);

			//exit;



			switch($recurrence_type) {

				case "1":

					// +1 hour for the Summer to Winter clock change

					$start_day = mktime(1,0,0,$date_array["month"],$date_array["day"],$date_array["year"]);

					$start_day = $start_day + ($recurrence_number * $day_time);

					break;

				case "2":

					// +1 hour for the Summer to Winter clock change

					$start_day = mktime(1,0,0,$date_array["month"],$date_array["day"],$date_array["year"]);

					$start_day = $start_day + ($recurrence_number * $week_time);

					break;

				case "3":

					$start_day = mktime(1,0,0,($date_array["month"] + $recurrence_number),$date_array["day"],$date_array["year"]);;

					break;

				case "4":

					//$weekday_must = ($recurrence_row['recurrence_type'] - 3);	// the 'must' weekday

					$weekday_must = $recurrence_row['recurrence_weekday']+1;	// the 'must' weekday

					if ($recurrence_number < 5) {	// 1. - 4. week in a month

						// the first day in the new month

						$start_day = mktime(1,0,0,($date_array["month"] + 1),1,$date_array["year"]);

						$weekday_is = date("w",$start_day);							// get the weekday of the first day in this month

						// calculate the day difference between these days

						if ($weekday_is <= $weekday_must) {

							$day_diff = $weekday_must - $weekday_is;

						} else {

							$day_diff = ($weekday_must + 7) - $weekday_is;

						}

						$start_day = ($start_day + ($day_diff * $day_time)) + ($week_time * ($recurrence_number - 1));

						//echo date("Y-m-d",$start_day); exit;

					} else {	// the last or the before last week in a month

						// the last day in the new month

						$start_day = mktime(1,0,0,($date_array["month"] + 2),1,$date_array["year"]) - $day_time;

						$weekday_is = date("w",$start_day);

						// calculate the day difference between these days

						if ($weekday_is >= $weekday_must) {

							$day_diff = $weekday_is - $weekday_must;

						} else {

							$day_diff = ($weekday_is - $weekday_must) + 7;

						}

						$start_day = ($start_day - ($day_diff * $day_time));

						if ($recurrence_number == 6) {	// before last?

							$start_day = $start_day - $week_time;

						}

					}

					break;

				default:

					$this->dates_recurrence($recurrence_row);

					return;

					break;

			}



			$recurrence_row['dates'] = date("Y-m-d", $start_day);

			if ($recurrence_row['enddates']) {

				$recurrence_row['enddates'] = date("Y-m-d", $start_day + $date_array["day_diff"]);

			}



			if($recurrence_row['recurrence_counter'] == "0000-00-00"){

					$recurrence_row['recurrence_counter'] = REGPRO_RECURRING_UNLIMITED_DATE;

			}



			//echo "<pre>"; print_r($recurrence_row);  //exit;



			//if (($recurrence_row['dates'] <= $recurrence_row['recurrence_counter']) || ($recurrence_row['recurrence_counter'] == "0000-00-00")) {

			if ($recurrence_row['dates'] <= $recurrence_row['recurrence_counter']) {



				$arr_insert_key = array();

				$arr_insert_value = array();



				// create the INSERT query

				foreach ($recurrence_row as $key => $result) {

					if ($key != 'id') {

						if ($insert_keys != '') {

							if ($this->where_table_rows($key)) {

								$wherequery .= ' AND ';

							}

							$insert_keys .= ',';

							//$insert_values .= ',';

						}

						$insert_keys .= $key;

						$arr_insert_key [] = $key;

						if (($key == "enddates" || $key == "times" || $key == "endtimes") && $result == "") {

							//$insert_values .= "NULL";

							$arr_insert_value[] = "NULL";

							$wherequery .= '`'.$key.'` IS NULL';

						} else {

							//$insert_values .= "'".$result."'";

							$arr_insert_value[] = $result;

							if ($this->where_table_rows($key)) {

								$wherequery .= '`'.$key.'` = "'.$result.'"';

							}



						}

					}

				}



				//echo "<pre>"; print_r($arr_insert_key); echo "<pre>"; print_r($arr_insert_value);  exit;

				$wherequery = substr($wherequery,4);

				$query = 'SELECT id FROM #__registrationpro_dates WHERE '.$wherequery.';';

				$db->setQuery( $query );



				if (count($db->loadAssocList()) == 0) {

					$expinsertkey = explode(',',$insert_keys);

					$expinsertvalue = explode(',',str_replace("'","",$insert_values));



					//echo "<pre>"; print_r($expinsertkey); echo "<pre>"; print_r($expinsertvalue); exit;



					foreach($arr_insert_key as $key => $value)

					{

						$data[$value] = $arr_insert_value[$key];

					}

					//echo "<pre>"; print_r($data); exit;



					// add event record

					$model 		= $this->getModel('myevent');

					$insertid   = $model->store($data);

					// end



					/*$query = 'INSERT INTO #__registrationpro_dates ('.$insert_keys.') VALUES ('.$insert_values.');';

					$db->setQuery( $query );

					$db->query();

					$insertid = $db->insertid();*/



					// copy event tickets of parent event

					$t_model = $this->getModel('ticket');

					$t_model->copytickets($recurrence_parentid, $insertid);

					// end



					// copy event discounts of parent event

					$d_model = $this->getModel('eventdiscount');

					$d_model->copydiscounts($recurrence_parentid, $insertid);

					// end



					// add eventids in session to create the recurrence date from every event at last

					$session 	= JFactory::getSession();

					$session_eventids = $session->get('eventids');



					if($session_eventids) {

						$arrflag = array($recurrence_parentid, $insertid);

						$arreventids = array_merge($session_eventids, $arrflag);

					}else{

						$arreventids = array($recurrence_parentid, $insertid);

					}



					$arreventids = array_unique($arreventids); // remove duplicate values

					$session->set('eventids', $arreventids);

					// end



					$this->calculate_recurrence($insertid);

				}

			}

		}



		//return $recurrence_row;

	}



	/* this method generate the date string to a date array */

	function generate_date($startdate, $enddate) {

		$startdate = explode("-",$startdate);

		$date_array = array("year" => $startdate[0],

							"month" => $startdate[1],

							"day" => $startdate[2],

							"weekday" => date("w",mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0])));

		if ($enddate) {

			$enddate = explode("-", $enddate);

			$day_diff = (mktime(1,0,0,$enddate[1],$enddate[2],$enddate[0]) - mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0]));

			$date_array["day_diff"] = $day_diff;

		}

		return $date_array;

	}



	/* This is seprate function for only dates recurrence type */

	function dates_recurrence($recurrence_row)

	{

		$db			=  JFactory::getDBO();



		//echo "<pre>"; print_r($recurrence_row); exit;



		$recurrence_parentid = $recurrence_row['id'];



		$recurrence_dates = JRequest::getVar('recurrence_selectlist');



		if(is_array($recurrence_dates) && count($recurrence_dates > 0)){



			foreach($recurrence_dates as $rkey => $rvalue)

			{



				// Date difference between event start and end date to set the event end date

				$startdate 	= explode("-", $recurrence_row['dates']);

				$enddate 	= explode("-", $recurrence_row['enddates']);

				$day_diff 	= (mktime(1,0,0,$enddate[1],$enddate[2],$enddate[0]) - mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0]));

				$event_end_date = date("Y-m-d", strtotime($rvalue) + $day_diff);

				// end



				$recurrence_row['dates'] = $rvalue;

				$recurrence_row['enddates'] =  $event_end_date;



				$arr_insert_key = array();

				$arr_insert_value = array();



				// create the INSERT query

				foreach ($recurrence_row as $key => $result) {

					if ($key != 'id') {

						if ($insert_keys != '') {

							if ($this->where_table_rows($key)) {

								$wherequery .= ' AND ';

							}

							$insert_keys .= ',';

							//$insert_values .= ',';

						}

						$insert_keys .= $key;

						$arr_insert_key [] = $key;

						if (($key == "enddates" || $key == "times" || $key == "endtimes") && $result == "") {

							//$insert_values .= "NULL";

							$arr_insert_value[] = "NULL";

							$wherequery .= '`'.$key.'` IS NULL';

						} else {

							//$insert_values .= "'".$result."'";

							$arr_insert_value[] = $result;

							if ($this->where_table_rows($key)) {

								$wherequery .= '`'.$key.'` = "'.$result.'"';

							}



						}

					}

				}



			//	echo "<pre>"; print_r($arr_insert_key); echo "<pre>"; print_r($arr_insert_value);  exit;

				$wherequery = substr($wherequery,4);

				$query = 'SELECT id FROM #__registrationpro_dates WHERE '.$wherequery.';';

				$db->setQuery( $query );



				if (count($db->loadAssocList()) == 0) {

					$expinsertkey = explode(',',$insert_keys);

					$expinsertvalue = explode(',',str_replace("'","",$insert_values));



					//echo "<pre>"; print_r($expinsertkey); echo "<pre>"; print_r($expinsertvalue); exit;



					foreach($arr_insert_key as $key => $value)

					{

						$data[$value] = $arr_insert_value[$key];

					}

					//echo "<pre>"; print_r($data); exit;



					// add event record

					$model 		= $this->getModel('myevent');

					$insertid   = $model->store($data);

					// end



					// copy event tickets of parent event

					$t_model = $this->getModel('ticket');

					$t_model->copytickets($recurrence_parentid, $insertid);

					// end



					// copy event discounts of parent event

					$d_model = $this->getModel('eventdiscount');

					$d_model->copydiscounts($recurrence_parentid, $insertid);

					// end

				}



				// add eventids in session to create the recurrence date from every event at last

				$session 	= JFactory::getSession();

				$session_eventids =& $session->get('eventids');



				if($session_eventids) {

					$arrflag = array($recurrence_parentid, $insertid);

					$arreventids = array_merge($session_eventids, $arrflag);

				}else{

					$arreventids = array($recurrence_parentid, $insertid);

				}



				$arreventids = array_unique($arreventids); // remove duplicate values

				$session->set('eventids', $arreventids);

				// end

			}



		}

	}



	/*** use only some importent keys of the eventlist_events - database table for the where query */

	function where_table_rows($key) {

		if ($key == 'locid' || $key == 'catsid' || $key == 'dates' || $key == 'enddates' || $key == 'times' || $key == 'endtimes') {

			return true;

		} else {

			return false;

		}

	}



	//*************************************************** Ticket section  ******************************************//



	function add_ticket()

	{

		// Check for request forgeries

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'tickets' );



		$task 	= JRequest::getVar('task');

		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);



		$eventid = JRequest::getVar('regpro_dates_id', 0,'','int');



		//echo $eventid;



		//echo "<pre>"; print_r($post); exit;



		// Save event information first

		if(empty($eventid) || $eventid == 0){

			$model 		= $this->getModel('myevent');

			$returnid 	= $model->store($post);

			$eventid 	= $returnid;

		}

		// end



		// Save ticket information

		$model = $this->getModel('ticket');



		//echo "<pre>"; print_r($post); exit;

		$post['regpro_dates_id'] = $eventid;



		if ($returnid = $model->store($post)) {

			$msg	= JText::_( 'ADMIN_EVENTS_SAVE');



			$cache 	= JFactory::getCache('com_registrationpro');

			$cache->clean();

		} else {

			$msg	= '';

		}

		// End



		JRequest::setVar( 'regpro_dates_id', $eventid);



		parent::display();

	}



	function edit_ticket()

	{

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'ticket' );

		JRequest::setVar( 'hidemainmenu', 1 );



		$user	= JFactory::getUser();



		parent::display();

	}



	function remove_ticket()

	{

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'tickets' );



		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );



		//echo "<pre>";print_r($cid); exit;



		if (!is_array( $cid ) || count( $cid ) < 1) {

			JError::raiseError(500, JText::_( 'Select an item to delete' ) );

		}



		$model = $this->getModel('tickets');



		$msg = $model->delete($cid).' '.JText::_('Ticket Deleted');;



		$cache = JFactory::getCache('com_registrationpro');

		$cache->clean();



		parent::display();

	}



	function orderuppayments()

	{

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'tickets' );



		$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$eventid = JRequest::getVar( 'regpro_dates_id', 0, 'post');

		$type	 = JRequest::getVar( 'type', '','post');



		//echo "<pre>"; print_r($_POST); exit;

        $model = $this->getModel('tickets');



        $model->move(-1,$cid[0],"regpro_dates_id = ".$eventid." AND type = '".$type."'");



		parent::display();

	}



	function orderdownpayments()

	{

		JRequest::setVar( 'view', 'tickets' );



		JRequest::checkToken() or jexit( 'Invalid Token' );



		$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$eventid = JRequest::getVar( 'regpro_dates_id', 0, 'post','int');

		$type	 = JRequest::getVar( 'type', '','post');



		//echo "<pre>"; print_r($_POST); exit;

        $model = $this->getModel('tickets');

        $model->move(1,$cid[0],"regpro_dates_id = ".$eventid." AND type = '".$type."'");



		parent::display();

	}



	//*************************************************** Event Group Discount section  ******************************************//



	function add_groupdiscount()

	{

		// Check for request forgeries

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'eventdiscounts' );



		$task 	= JRequest::getVar('task');

		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);



		$eventid = JRequest::getVar('event_id', 0,'','int');



		// Save ticket information

		$model = $this->getModel('eventdiscount');



		//echo "<pre>"; print_r($post); exit;



		if ($returnid = $model->store($post)) {

			$msg	= JText::_( 'ADMIN_EVENTS_GROUP_DISCOUNT_SAVE_MSG');



			$cache 	= JFactory::getCache('com_registrationpro');

			$cache->clean();

		} else {

			$msg	= '';

		}

		// End



		JRequest::setVar( 'event_id', $eventid);



		parent::display();

	}



	function edit_groupdiscount()

	{

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'eventdiscount' );

		JRequest::setVar( 'hidemainmenu', 1 );



		parent::display();

	}



	function remove_groupdiscount()

	{

		JRequest::checkToken() or jexit( 'Invalid Token' );



		JRequest::setVar( 'view', 'eventdiscounts' );



		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );



		//echo "<pre>";print_r($cid); exit;



		if (!is_array( $cid ) || count( $cid ) < 1) {

			JError::raiseError(500, JText::_( 'Select an item to delete' ) );

		}



		$model = $this->getModel('eventdiscounts');



		$msg = $model->delete($cid).' '.JText::_('Group Discount Deleted');



		$cache = JFactory::getCache('com_registrationpro');

		$cache->clean();



		parent::display();

	}



	//*************************************************** Event Early Discount section  ******************************************//

	function add_earlydiscount() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscounts' );
		$task 	= JRequest::getVar('task');
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$eventid = JRequest::getVar('event_id', 0,'','int');
		$model = $this->getModel('eventdiscount');

		if ($returnid = $model->store($post)) {
			$msg	= JText::_( 'ADMIN_EVENTS_EARLY_DISCOUNT_SAVE_MSG');
			$cache 	= JFactory::getCache('com_registrationpro');
			$cache->clean();
		} else $msg	= '';
		JRequest::setVar( 'event_id', $eventid);
		parent::display();
	}

	function edit_earlydiscount() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscount' );
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();
	}

	function remove_earlydiscount() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscounts' );
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to delete' ) );

		$model = $this->getModel('eventdiscounts');
		$msg = $model->delete($cid).' '.JText::_('Eearly Registration Discount Deleted');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		parent::display();
	}

	//*************************************************** Sessions section  ******************************************//

	function add_session() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'sessions' );
		$task 	= JRequest::getVar('task');
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$eventid = JRequest::getVar('event_id', 0,'','int');
		$model = $this->getModel('session');
		$model->save_page_header($post['session_page_header'], $eventid);
		$post['event_id'] = $eventid;

		if($post['title'] != "" && $post['session_date'] != "" && $post['session_start_time'] != "" && $post['session_stop_time'] != "") {
			if ($returnid = $model->store($post)) {
				$msg	= JText::_( 'ADMIN_EVENTS_SAVE');
				$cache 	= JFactory::getCache('com_registrationpro');
				$cache->clean();
			} else {
				$msg	= '';
			}
		}

		JRequest::setVar( 'event_id', $eventid);
		JRequest::setVar( 'view', 'sessions' );
		parent::display();
	}

	function edit_session()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'session' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$user	= JFactory::getUser();
		parent::display();
	}

	function remove_session()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'sessions' );
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}
		$model = $this->getModel('sessions');
		$msg = $model->delete($cid).' '.JText::_('Session Deleted');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		parent::display();
	}

	function orderupsessions()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'sessions' );
		$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$eventid = JRequest::getVar( 'event_id', 0, 'post','int');
        $model = $this->getModel('sessions');
        $model->move(-1,$cid[0],"event_id = ".$eventid);
		parent::display();
	}

	function orderdownsessions()
	{
		JRequest::setVar( 'view', 'sessions' );
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$eventid = JRequest::getVar( 'event_id', 0, 'post','int');
        $model = $this->getModel('sessions');
        $model->move(1,$cid[0],"event_id = ".$eventid);
		parent::display();
	}

	//*************************************************** Event Report section  ******************************************//
	function event_report(){
		JRequest::setVar( 'view', 'myevent' );
		JRequest::setVar( 'layout', 'event_report');
		parent::display();
	}

	function excel_report()
	{
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig	= $registrationproAdmin->config();
		$model = $this->getModel('myevent');
		$registrationproHelper = new registrationproHelper;
		$event_info = $registrationproHelper->getEventInfo($model->_id);	// get event details
		$data		= $model->getUserinfoForExcelReport();	// get users details

		if($data){
			$data1 = array();
			$columns = array();
			$columns1 = array();
			$j = 0;
			$z = 0;

			foreach($data as $key=>$value)
			{
				if(!empty($data[$key]->reg_id)){

					if(count($columns) > 0){
						if(!in_array('Registration id',$columns)){
							$columns[] = 'Registration id';
						}
					}else{
						$columns[] = "Registration id";
					}
					$data1[$key]['Registration id'] = $data[$key]->reg_id;
				}

				$data[$key]->params = unserialize($data[$key]->params);
				$arrF = $data[$key]->params;
				$arrcount = count($arrF['firstname']);

				$arrFields   =array();
				$arrFields1 = array_keys($arrF);
				foreach($arrFields1 as $k1=>$v1){
					if($v1=='firstname'|| $v1=='lastname' || $v1=='email'){
						$arrFields[] =$v1;
					}
				}

				$arrFields1 = array_keys($arrF);
				foreach($arrFields1 as $k1=>$v1){
					if($v1!='firstname'|| $v1!='lastname' || $v1!='email'){
						$arrFields[] =$v1;
					}
				}

				foreach($arrFields as $k=>$v){
					if(trim($arrF['firstname'][0][0]) == trim($data[$key]->firstname) && trim($arrF['lastname'][0][0]) == trim($data[$key]->lastname) && trim($arrF['email'][0][0]) == trim($data[$key]->email)){
						$FieldTitle = str_replace("cb_","",$v);
						$colname = ucfirst($FieldTitle);

						if(count($columns) > 0){
							if(!in_array($colname,$columns)) $columns[] = $colname;
						} else $columns[] = $colname;

						$data1temp = "";
						for($i=0;$i<count($arrF[$v]);$i++)
						{
							$arrImpode = array();
							if(is_array($arrF[$v])){
								$Fieldvalue = "";

								if($arrF[$v][$i][$i+1] == 'F'){ // check if user has uploaded any file
									$Fieldvalue = REGPRO_FORM_DOCUMENT_URL_PATH."/".$arrF[$v][$i][$i];
								}else{
									if($arrF[$v][$i][$i]) $Fieldvalue = $arrF[$v][$i][$i];
								}

								// add data in orignal data array
								if(count($arrF[$v]) > 1){
									$data1temp .= $Fieldvalue.", ";
								}else $data1temp =  $Fieldvalue;
							}
							$data1[$key][$colname] = $data1temp;
						}
					}
				}

				// add user financial data
				foreach($arrFields as $k=>$v)
				{
					if(trim($arrF['firstname'][0][0]) == trim($data[$key]->firstname) && trim($arrF['lastname'][0][0]) == trim($data[$key]->lastname) && trim($arrF['email'][0][0]) == trim($data[$key]->email))
					{
						// add a new coloums
						$arrFinancialFields = array();
						$arrFinancialFields[] = "regdate";
						$arrFinancialFields[] = 'Payment Status';
						$arrFinancialFields[] = 'Payment Method';
						$arrFinancialFields[] = 'Event Ticket Name';
						$arrFinancialFields[] = 'Price';
						$arrFinancialFields[] = 'Tax';
						$arrFinancialFields[] = 'Total Price(Including Tax)';
						$arrFinancialFields[] = 'Coupon Code';
						$arrFinancialFields[] = 'Discount Amount';
						$arrFinancialFields[] = 'Final Price';

						foreach($arrFinancialFields as $k=>$v)
						{
							$FieldTitle = $v;
							$colname1 = ucfirst($FieldTitle);
							if(count($columns1) > 0){
								if(!in_array($colname1,$columns1)){
									$columns1[] = $colname1;
								}
							}else{
								$columns1[] = $colname1;
							}
							
							$registrationproHelper = new registrationproHelper;
							if(!empty($data[$key]->uregdate)){
								$data1[$key]['Regdate'] = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'],  $data[$key]->uregdate + ($regproConfig['timezone_offset']*60*60));
							}else $data1[$key]['Regdate'] = "NIL";

							// add user financial data
							// if(!empty($data[$key]->id)) $data1[$key]['Order id'] = $data[$key]->id;
							// if(!empty($data[$key]->id)) $data1[$key]['Registration id'] = $data[$key]->reg_id;

							if(!empty($data[$key]->payment_status)) $data1[$key]['Payment Status'] = $data[$key]->payment_status;
							if(!empty($data[$key]->payment_method)) $data1[$key]['Payment Method'] =$data[$key]->payment_method;
							if(!empty($data[$key]->item_name)) $data1[$key]['Event Ticket Name'] =$data[$key]->item_name;

							if(!empty($data[$key]->price_without_tax)){
								if(empty($data[$key]->price_without_tax) || $data[$key]->price_without_tax == 0.00){
									if(!empty($data[$key]->price)){
										$productprice = (100 * $data[$key]->price) / (100 + $data[$key]->tax);
										$data1[$key]['Price'] = number_format($productprice,2);
									}
								}else{
									$data1[$key]['Price'] = number_format($data[$key]->price_without_tax,2);
								}
							}

							if(!empty($data[$key]->tax)){
								$data1[$key]['Tax'] = $data[$key]->tax.'%';
							}else{
								$data1[$key]['Tax'] = '0%';
							}

							if(!empty($data[$key]->price)){
								$data1[$key]['Total Price(Including Tax)'] = number_format($data[$key]->price,2);
							}

							if(!empty($data[$key]->coupon_code)){
								$data1[$key]['Coupon Code'] = $data[$key]->coupon_code;
							}

							if($data[$key]->discount_amount > 0.00){
								$data1[$key]['Discount Amount'] = number_format($data[$key]->discount_amount,2);
								$data1[$key]['Final Price'] 	= number_format($data[$key]->final_price,2);
							}else{
								$data1[$key]['Discount Amount'] = 0.00;
								$data1[$key]['Final Price'] 	= number_format($data[$key]->price,2);
							}
							// end
						}
					}
				}
			}
		}
	$columns = array_merge($columns,$columns1);
	$data2 = array();
	foreach($data1 as $datakey => $datavalue)
	{
		foreach($columns as $colkey=>$colvalue)
		{
			if(array_key_exists($colvalue,$datavalue)){
				$data2[$datakey][$colvalue] = $data1[$datakey][$colvalue];
			}else{
				$data2[$datakey][$colvalue] = "";
			}
		}
	}
		if($data2){
		$flag = false;
		$filename = $event_info->titel ."_Report.xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		foreach($columns as $colkey=>$colvalue)	echo $colvalue."\t";
		echo "\n";
		foreach($data2 as $datakey=>$datavalue)
		{
			if(is_array($data2[$datakey]))
			{
				foreach($data2[$datakey] as $k=>$v)
				{
					$show = preg_replace("[\n\r]", " ", $data2[$datakey][$k]);
					echo $show, "\t";
				}
			}
			echo "\n";
		}
	}
	exit;
}
}
?>