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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class registrationproControllerEvents extends registrationproController
{

	function __construct() {
		parent::__construct();
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );
	}

	function publish() {
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = $this->getModel('events');
		if(!$model->publish($cid, 1)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		if(!$model->moderate_publish($cid, 1)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";

		$total = count( $cid );
		$msg = $total.' '.JText::_('ADMIN_EVENTS_SUC_PUBL');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=events', $msg );
	}

	function unpublish() {
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		$model = $this->getModel('events');
		if(!$model->publish($cid, 0)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		$total = count( $cid );
		$msg = $total.' '.JText::_('ADMIN_EVENTS_SUC_UNPUBL');
		$this->setRedirect( 'index.php?option=com_registrationpro&view=events', $msg );
	}

	function archive() {
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		$model = $this->getModel('events');
		if(!$model->publish($cid, -1)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		$total = count( $cid );
		$msg = $total.' '.JText::_('ADMIN_EVENTS_SUC_ARCH');
		$this->setRedirect( 'index.php?option=com_registrationpro&view=events', $msg );
	}

	function unarchive() {
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		$model = $this->getModel('events');
		if(!$model->publish($cid, 0)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		$total = count( $cid );
		$msg = $total.' '.JText::_('ADMIN_EVENTS_SUC_UNARCH');
		$this->setRedirect( 'index.php?option=com_registrationpro&view=archives', $msg );
	}

	function archive_cancel() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_registrationpro' );
	}

	function cancel() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_registrationpro&view=events' );
	}

	function delete() {
		$model = $this->getModel('events');
		$user = JFactory::getUser();
		parent::display();
	}

	function remove() {
		global $option;
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		$model = $this->getModel('events');
		$msg = $model->delete($cid).' '.JText::_('ADMIN_EVENTS_DEL');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		$this->setRedirect( 'index.php?option=com_registrationpro&view=events', $msg );
	}

	function remove_archive() {
		global $option;
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		$model = $this->getModel('events');
		$msg = $model->delete($cid).' '.JText::_('ADMIN_EVENTS_DEL');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		$this->setRedirect( 'index.php?option=com_registrationpro&view=archives', $msg );
	}

	function copy() {
		JRequest::setVar( 'view', 'event' );
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();
	}

	function edit() {
		JRequest::setVar( 'view', 'event' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel('events');
		$user	=JFactory::getUser();
		parent::display();
	}

	function save() {
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$task	  = JRequest::getVar('task');
		$copy	  = JRequest::getVar('copy', 0);
		$event_id = JRequest::getVar('event_id', 0);
		$regpro_registrations_emails = new regpro_registrations_emails;
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);

		if($copy) {
			if(($post['event_image'] == 'old')&&($post['image'] == '1')) $post['event_image'] = 'copy';
			if($post['event_image'] == 'del'){
				$post['image'] = '0';
				$post['image_name'] = '';
			}
			$post['parent_id'] = '0';
		}
		
		// convert events array in comman separted value to store in database
		if(($post['payment_method'])&&(strpos($post['payment_method'], ',') !== false)) 
			$post['payment_method'] = implode(",", $post['payment_method']);

		$model = $this->getModel('event');

		if ($returnid = $model->store($post)) {

			if($copy) {
				// copy event tickets
				$ticket_model = $this->getModel('ticket');
				$ticket_model->copytickets($event_id, $returnid);

				// copy event discounts
				$discount_model = $this->getModel('eventdiscount');
				$discount_model->copydiscounts($event_id, $returnid);

				// copy event sessions
				$event_session = $this->getModel('session');
				$event_session->copysessions($event_id, $returnid);
			}

			// check recurrence event and insert rows for recurrence events
			if($post['recurrence_type'] <> 0 && $post['recurrence_number'] <> 0 && $returnid){
				$session = JFactory::getSession();
				$this->calculate_recurrence($returnid);	 // create events
				$session_eventids = $session->get('eventids');
				$delrecurrence = $model->clearrecurrence($session_eventids); // clear recurrence values of all created events
				$session->clear('eventids');
			}

			// check if admin select checkbox to notify the user for status change
			if($post['notify'])	$regpro_registrations_emails->send_StautsChange_email($post['id']);

			switch ($task) {
				case 'apply' :
					$link = 'index.php?option=com_registrationpro&controller=events&view=event&hidemainmenu=1&cid[]='.$returnid;
					break;
				default :
					$link = 'index.php?option=com_registrationpro&view=events';
					break;
			}
			$msg = JText::_( 'ADMIN_EVENTS_SAVE');
			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		} else {
			$msg = '';
			$link = 'index.php?option=com_registrationpro&view=events';
		}
		$this->setRedirect( $link, $msg );
 	}

    function orderupevents() {
        JRequest::checkToken() or jexit( 'Invalid Token' );
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		if (isset( $cid[0] )) {
			$row = & JTable::getInstance('registrationpro_dates','');
			$row->load( (int) $cid[0] );
			$row->move(-1, 'catsid = '.(int) $row->catsid);
			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		}
		 $link = 'index.php?option=com_registrationpro&view=events';
        $this->setRedirect( $link );
    }

    function orderdownevents() {
        JRequest::checkToken() or jexit( 'Invalid Token' );
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] )) {
			$row = & JTable::getInstance('registrationpro_dates','');
			$row->load( (int) $cid[0] );
			$row->move(1, 'catsid = '.(int) $row->catsid);
			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		}
		 $link = 'index.php?option=com_registrationpro&view=events';
        $this->setRedirect( $link );
    }

    function saveorder() {
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );

        JArrayHelper::toInteger($cid);
        JArrayHelper::toInteger($order);

        $model = $this->getModel('events');
        $model->saveorder($cid, $order);

        $link = 'index.php?option=com_registrationpro&view=events';
        $this->setRedirect( $link );
    }

	/**
	* this method calculate the next date
	*/
	function calculate_recurrence($recurrence_parentid = 0) {
		$db	= JFactory::getDBO();

		$nulldate = '0000-00-00';
		$query = 'SELECT * FROM #__registrationpro_dates WHERE (IF (enddates <> '.$nulldate.', enddates, dates)) AND recurrence_number <> "0" AND recurrence_type <> "0" AND id = '.$recurrence_parentid;
		$db->setQuery( $query );
		$recurrence_array = $db->loadAssocList();
		foreach($recurrence_array as $recurrence_row) {
			$insert_keys = '';
			$insert_values = '';
			$wherequery = '';
			
            /*code added by sushil on 27-08-2014*/
            $date1 =new DateTime($recurrence_row['dates']); 
			$dEnd = new DateTime($recurrence_row['regstart']); 
		    if($date1 >  $dEnd){
			    $dDiff = $dEnd ->diff( $date1 );
				$recurrence_row['regstart'] = date("Y-m-d", strtotime("-".$dDiff->days." days", strtotime($recurrence_row['dates']))); 
			}
		   else{
				$dDiff = $date1 ->diff( $dEnd );
				$recurrence_row['regstart'] =  date("Y-m-d", strtotime("+".$dDiff->days." days", strtotime($recurrence_row['dates'])));
				//echo '<pre>'; print_r ($recurrence_row['regstart']); die;
			}
		    $dStart = new DateTime($recurrence_row['regstop']);
			$dEnd  =new DateTime($recurrence_row['regstart']);
			$lastDate = $dEnd ->diff( $dStart );
            /*-------------------------------*/

			// get the recurrence information
			$recurrence_number = $recurrence_row['recurrence_number'];
			$recurrence_type = $recurrence_row['recurrence_type'];
            $day_time = 86400;	// 60 sec. * 60 min. * 24 h
			$week_time = 604800;// $day_time * 7days
			$date_array = $this->generate_date($recurrence_row['dates'], $recurrence_row['enddates']);
            //echo "<pre>"; print_r($date_array);  die;
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
					//echo "<pre>"; print_r($recurrence_row); echo "</pre>"; exit;
					if ($recurrence_number < 5) {	// 1. - 4. week in a month
						// the first day in the new month
						$start_day = mktime(1,0,0,($date_array["month"] + 1),1,$date_array["year"]);
						$weekday_is = date("w",$start_day);							// get the weekday of the first day in this month
						// calculate the day difference between these days
						if ($weekday_is <= $weekday_must) {
							$day_diff = $weekday_must - $weekday_is;
						} else $day_diff = ($weekday_must + 7) - $weekday_is;
						$start_day = ($start_day + ($day_diff * $day_time)) + ($week_time * ($recurrence_number - 1));
					} else {	// the last or the before last week in a month
						// the last day in the new month
						$start_day = mktime(1,0,0,($date_array["month"] + 2),1,$date_array["year"]) - $day_time;
						$weekday_is = date("w",$start_day);
						// calculate the day difference between these days
						if ($weekday_is >= $weekday_must) {
							$day_diff = $weekday_is - $weekday_must;
						} else $day_diff = ($weekday_is - $weekday_must) + 7;
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
			if($recurrence_row['enddates']) $recurrence_row['enddates'] = date("Y-m-d", $start_day + $date_array["day_diff"]);
			//$date1 =new DateTime($recurrence_row['dates']); 
			
            /* code added by sushil on 27-08-2014 */
            $dStart = new DateTime($recurrence_row['regstart']); 
			if($date1 >  $dStart){
				$dDiff = $dStart ->diff( $date1 );
				$recurrence_row['regstart'] = date("Y-m-d", strtotime("-".$dDiff->days." days", strtotime($recurrence_row['dates'])));
		    }
		   else{
		       $dDiff = $date1 ->diff( $dStart );
				//echo '<pre>'; print_r ($dDiff); die;
				$recurrence_row['regstart'] =  date("Y-m-d", strtotime("+".$dDiff->days." days", strtotime($recurrence_row['dates'])));
			}
		     $recurrence_row['regstop'] =date("Y-m-d", strtotime("+".$lastDate->days." days", strtotime($recurrence_row['regstart']))); 	
            /*----------------------------*/
			
			if($recurrence_row['recurrence_counter'] == "0000-00-00") $recurrence_row['recurrence_counter'] = REGPRO_RECURRING_UNLIMITED_DATE;
            if ($recurrence_row['dates'] <= $recurrence_row['recurrence_counter']) {
             // echo "here"; die;
				$arr_insert_key = array();
				$arr_insert_value = array();

				// create the INSERT query
				foreach ($recurrence_row as $key => $result) {
					if ($key != 'id') {
						if ($insert_keys != '') {
							if ($this->where_table_rows($key)) $wherequery .= ' AND ';
							$insert_keys .= ',';
						}
						$insert_keys .= $key;
						$arr_insert_key [] = $key;
						if (($key == "enddates" || $key == "times" || $key == "endtimes") && $result == "") {
							$arr_insert_value[] = "NULL";
							$wherequery .= '`'.$key.'` IS NULL';
						} else {
							$arr_insert_value[] = $result;
							if ($this->where_table_rows($key)) $wherequery .= '`'.$key.'` = "'.$result.'"';
						}
					}
				}

				$wherequery = substr($wherequery,4);
				$query = 'SELECT id FROM #__registrationpro_dates WHERE '.$wherequery.';';
				$wherequery = '';
				$db->setQuery( $query );

				if (count($db->loadAssocList()) == 0) {
					$expinsertkey = explode(',', $insert_keys);
					$expinsertvalue = explode(',', str_replace("'", "", $insert_values));

					foreach($arr_insert_key as $key => $value) $data[$value] = $arr_insert_value[$key];
                 
					 // add event record
					$model = $this->getModel('event');
					
					if($data['parent_id'] <= 0) $data['parent_id'] = $recurrence_parentid;
					$ord = $data['parent_id'];
					if ($recurrence_parentid > 0) $ord = ($data['parent_id'] * 1) + $ord;
					//if ($recurrence_parentid > 0) $ord = ($recurrence_parentid)+1;
					$data['ordering'] = ''.$ord.'';
					
					$data['image_name'] ="event_".$data['parent_id'].".jpg";
					$data['event_image'] ='copy';
					$insertid = $model->store($data);

					// copy event tickets of parent event
					$t_model = $this->getModel('ticket');
					$t_model->copytickets($recurrence_parentid, $insertid);

					// copy event discounts of parent event
					$d_model = $this->getModel('eventdiscount');
					$d_model->copydiscounts($recurrence_parentid, $insertid);

					// add eventids in session to create the recurrence date from every event at last
					$session = JFactory::getSession();
					$session_eventids = $session->get('eventids');

					if($session_eventids) {
						$arrflag = array($recurrence_parentid, $insertid);
						$arreventids = array_merge($session_eventids, $arrflag);
					} else $arreventids = array($recurrence_parentid, $insertid);

					$arreventids = array_unique($arreventids); // remove duplicate values
					$session->set('eventids', $arreventids);
					$this->calculate_recurrence($insertid);
				}
			}
		}
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
	function dates_recurrence($recurrence_row) {
		$db = JFactory::getDBO();
	   
		$recurrence_parentid = $recurrence_row['id'];
		$recurrence_dates = JRequest::getVar('recurrence_selectlist');
		if(is_array($recurrence_dates) && count($recurrence_dates > 0)) {
			foreach($recurrence_dates as $rkey => $rvalue) {

			// Date difference between event start and end date to set the event end date
				$startdate 	= explode("-", $recurrence_row['dates']);
			    $enddate 	= explode("-", $recurrence_row['enddates']);
				$day_diff 	= (mktime(1,0,0,$enddate[1],$enddate[2],$enddate[0]) - mktime(1,0,0,$startdate[1],$startdate[2],$startdate[0]));
			    $event_end_date = date("Y-m-d", strtotime($rvalue) + $day_diff);
				
				/* code added by sushil on 27-08-2014 */
			    $startdate =  new DateTime($recurrence_row['dates']); 
			    $dEnd = new DateTime($recurrence_row['regstart']); 
	            if($startdate >  $dEnd){
					$dDiff = $dEnd ->diff( $startdate );
					$recurrence_row['regstart'] = date("Y-m-d", strtotime("-".$dDiff->days." days", strtotime($recurrence_row['dates']))); 
			    }
		        else{
		             $dDiff = $startdate ->diff( $dEnd );
			         $recurrence_row['regstart'] =  date("Y-m-d", strtotime("+".$dDiff->days." days", strtotime($recurrence_row['dates'])));
			    }
			    $dStart = new DateTime($recurrence_row['regstop']);
                $dEnd  =new DateTime($recurrence_row['regstart']);
                $lastDate = $dEnd ->diff( $dStart );
				
                 /*--------------------------------*/
				$recurrence_row['dates'] = $rvalue;
 			    $recurrence_row['enddates'] =  $event_end_date;
				
				 /* code added by sushil on 27-08-2014 */				
				$dStart = new DateTime($recurrence_row['regstart']); 
			    if($startdate >  $dStart){
					$dDiff = $dStart ->diff( $startdate );
					$recurrence_row['regstart'] = date("Y-m-d", strtotime("-".$dDiff->days." days", strtotime($recurrence_row['dates'])));
		        }
		        else{
					$dDiff = $startdate ->diff( $dStart );
					$recurrence_row['regstart'] =  date("Y-m-d", strtotime("+".$dDiff->days." days", strtotime($recurrence_row['dates'])));
			    }
		        $recurrence_row['regstop'] =date("Y-m-d", strtotime("+".$lastDate->days." days", strtotime($recurrence_row['regstart']))); 	
                /*--------------------------------*/
     			$arr_insert_key = array();
				$arr_insert_value = array();

				// create the INSERT query
				foreach ($recurrence_row as $key => $result) {
					if ($key != 'id') {
						if ($insert_keys != '') {
							if ($this->where_table_rows($key)) $wherequery .= ' AND ';
							$insert_keys .= ',';
						}
						$insert_keys .= $key;
						$arr_insert_key [] = $key;
						if (($key == "enddates" || $key == "times" || $key == "endtimes") && $result == "") {
							$arr_insert_value[] = "NULL";
							$wherequery .= '`'.$key.'` IS NULL';
						} else {
							$arr_insert_value[] = $result;
							if ($this->where_table_rows($key)) $wherequery .= '`'.$key.'` = "'.$result.'"';
						}
					}
				}
				//echo "<pre>"; print_r($arr_insert_key); 
				//echo "<pre>"; print_r($arr_insert_value);  
				//exit;
				$wherequery = substr($wherequery,4);
				$query = 'SELECT id FROM #__registrationpro_dates WHERE '.$wherequery.';';
				$wherequery = '';
				$db->setQuery( $query );

				if (count($db->loadAssocList()) == 0) {
					$expinsertkey = explode(',',$insert_keys);
					$expinsertvalue = explode(',',str_replace("'","",$insert_values));
	               //echo "<pre>"; print_r($expinsertkey); echo "<pre>"; print_r($expinsertvalue); exit;
					foreach($arr_insert_key as $key => $value) $data[$value] = $arr_insert_value[$key];

					// add event record
					$model = $this->getModel('event');
					
					if($data['parent_id'] <= 0) $data['parent_id'] = $recurrence_parentid;
					$ord = $data['parent_id'];
					if ($recurrence_parentid > 0) $ord = ($data['parent_id'] * 1) + $ord;
					//if ($recurrence_parentid > 0) $ord =($recurrence_parentid)+1;
					$data['ordering'] = ''.$ord.'';
					$data['image_name'] ="event_".$data['parent_id'].".jpg";
					$data['event_image'] ='copy';
					$insertid = $model->store($data);

					// copy event tickets of parent event
					$t_model = $this->getModel('ticket');
					$t_model->copytickets($recurrence_parentid, $insertid);

					// copy event discounts of parent event
					$d_model = $this->getModel('eventdiscount');
					$d_model->copydiscounts($recurrence_parentid, $insertid);
				}

				// add eventids in session to create the recurrence date from every event at last
				$session = JFactory::getSession();
				$session_eventids = $session->get('eventids');

				if($session_eventids) {
					$arrflag = array($recurrence_parentid, $insertid);
					$arreventids = array_merge($session_eventids, $arrflag);
				} else $arreventids = array($recurrence_parentid, $insertid);

				$arreventids = array_unique($arreventids); // remove duplicate values
				$session->set('eventids', $arreventids);
			}

		}
	}

	/*** use only some importent keys of the eventlist_events - database table for the where query */
	function where_table_rows($key) {
		if ($key == 'locid' || $key == 'catsid' || $key == 'dates' || $key == 'enddates' || $key == 'times' || $key == 'endtimes') {
			return true;
		} else return false;
	}

	//*************************************************** Ticket section  ******************************************//

	function add_ticket() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'tickets' );

		$post    = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$eventid = JRequest::getVar('regpro_dates_id', 0);

		// Save event information first
		if(empty($eventid) || $eventid == 0){
			$model 	  = $this->getModel('event');
			$returnid = $model->store($post);
			$eventid  = $returnid;
		}

		// Save ticket information
		$model = $this->getModel('ticket');

		$post['regpro_dates_id'] = $eventid;

		$msg = '';
		if ($returnid = $model->store($post)) {
			$msg   = JText::_( 'ADMIN_EVENTS_SAVE');
			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		}

		JRequest::setVar( 'regpro_dates_id', $eventid);

		parent::display();
	}

	function edit_ticket() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'ticket' );
		$user = JFactory::getUser();
		parent::display();
	}

	function remove_ticket() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'tickets' );
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('tickets');
		$msg = $model->delete($cid).' '.JText::_('Ticket was Deleted');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		parent::display();
	}

	function movePayment($pos) {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'tickets' );
		
		$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$eventid = JRequest::getVar( 'regpro_dates_id', 0, 'post');
		$type	 = JRequest::getVar( 'type', '','post');
		$model   = $this->getModel('tickets');
		
        $model->move($pos, $cid[0], "regpro_dates_id = ".$eventid." AND type = '".$type."'");

		parent::display();
	}
	
	function orderuppayments() {
		$this->movePayment(-1);        
	}

	function orderdownpayments() {
		$this->movePayment(1);
	}

	function add_ticket_add() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'tickets' );

		$post    = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$eventid = JRequest::getVar('regpro_dates_id', 0);

		// Save event information first
		if(empty($eventid) || $eventid == 0){
			$model 	  = $this->getModel('event');
			$returnid = $model->store($post);
			$eventid  = $returnid;
		}

		// Save ticket information
		$model = $this->getModel('ticket');

		$post['regpro_dates_id'] = $eventid;

		$msg = '';
		if ($returnid = $model->store($post)) {
			$msg	= JText::_( 'ADMIN_EVENTS_SAVE');
			$cache 	= JFactory::getCache('com_registrationpro');
			$cache->clean();
		}

		JRequest::setVar( 'regpro_dates_id', $eventid);

		parent::display();
	}
	
	function edit_ticket_add() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'ticket' );
		$user = JFactory::getUser();
		parent::display();
	}
	
	function remove_ticket_add() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'tickets' );
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('tickets');
		$msg = $model->delete($cid).' '.JText::_('Product was Deleted');
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		parent::display();
	}
	
	//*************************************************** Event Group Discount section  ******************************************//

	function add_groupdiscount() {

		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscounts' );

		$task    = JRequest::getVar('task');
		$post    = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$eventid = JRequest::getVar('event_id', 0);

		// Save ticket information
		$model = $this->getModel('eventdiscount');

		if ($returnid = $model->store($post)) {
			$msg	= JText::_( 'ADMIN_EVENTS_GROUP_DISCOUNT_SAVE_MSG');

			$cache 	= JFactory::getCache('com_registrationpro');
			$cache->clean();
		} else $msg	= '';

		JRequest::setVar( 'event_id', $eventid);
		parent::display();
	}

	function edit_groupdiscount() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscount' );
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();
	}

	function remove_groupdiscount() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscounts' );
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to delete' ) );

		$model = $this->getModel('eventdiscounts');
		$msg = $model->delete($cid).' '.JText::_('Group Discount Deleted');
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		parent::display();
	}

	//*************************************************** Event Early Discount section  ******************************************//
	function add_earlydiscount()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'eventdiscounts' );
		$task = JRequest::getVar('task');
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$eventid = JRequest::getVar('event_id', 0);

		// Save ticket information
		$model = $this->getModel('eventdiscount');

		$msg = '';
		if ($returnid = $model->store($post)) {
			$msg = JText::_( 'ADMIN_EVENTS_EARLY_DISCOUNT_SAVE_MSG');
			$cache 	= JFactory::getCache('com_registrationpro');
			$cache->clean();
		}

		JRequest::setVar( 'event_id', $eventid);
		parent::display();
	}

	function edit_earlydiscount()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		JRequest::setVar( 'view', 'eventdiscount' );
		JRequest::setVar( 'hidemainmenu', 1 );

		parent::display();
	}

	function remove_earlydiscount()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		JRequest::setVar( 'view', 'eventdiscounts' );

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		//echo "<pre>";print_r($cid); exit;

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

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

		$task = JRequest::getVar('task');
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		
		$tmp = '';
		foreach($post as $key=>$val){
			if(strpos($key, 'session_date') === false) {} else {
				if($key !== 'session_date'){
					$post['session_date'] = $val;
					$tmp = $key;
				}
			}
		}
		if ($tmp !== '') unset($post[$tmp]);

		$eventid = JRequest::getVar('event_id', 0);
		
		$model = $this->getModel('session');
		$model->save_page_header($post['session_page_header'], $eventid);
		$post['event_id'] = $eventid;

		if($post['title'] != "" && $post['session_date'] != "" && $post['session_start_time'] != "" && $post['session_stop_time'] != "") {
			$msg = '';
			if ($returnid = $model->store($post)) {
				$msg = JText::_( 'ADMIN_EVENTS_SAVE');
				$cache = JFactory::getCache('com_registrationpro');
				$cache->clean();
			}
		}

		JRequest::setVar( 'event_id', $eventid);
		JRequest::setVar( 'view', 'sessions' );
		parent::display();
	}

	function edit_session() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'session' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$user = JFactory::getUser();
		parent::display();
	}

	function remove_session() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'sessions' );
		
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array($cid) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		
		$model = $this->getModel('sessions');
		$msg = $model->delete($cid).' '.JText::_('Session Deleted');;
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		parent::display();
	}

	function moveSession($pos) {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'sessions' );
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$eventid = JRequest::getVar( 'event_id', 0, 'post');
        $model = $this->getModel('sessions');
        $model->move($pos, $cid[0], "event_id = ".$eventid);
		parent::display();
	}
	
	function orderupsessions() {
		$this->moveSession(-1);        
	}

	function orderdownsessions() {
		$this->moveSession(1);
	}
	
	//*************************************************** Event Report section  ******************************************//
	function event_report(){
		//echo "<pre>"; print_r($_REQUEST); exit;
		JRequest::setVar( 'view', 'event' );
		JRequest::setVar( 'layout', 'event_report');
		parent::display();
	}

	function excel_report()
	{
		ob_clean();
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig	= $registrationproAdmin->config();
		//$regproConfig	= registrationproAdmin::config();

		$model = $this->getModel('event');
		$registrationproHelper = new registrationproHelper;
		$event_info = $registrationproHelper->getEventInfo($model->_id); // get event details
		

		$data					= $model->getUserinfoForExcelReport();			// get users details

		//echo"<pre>";print_r($data); exit;

		// add user form data
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
				//echo"<pre>";print_r($data[$key]->params);

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

				// add user form data
				foreach($arrFields as $k=>$v)
				{
					if(trim($arrF['firstname'][0][0]) == trim($data[$key]->firstname) && trim($arrF['lastname'][0][0]) == trim($data[$key]->lastname) && trim($arrF['email'][0][0]) == trim($data[$key]->email))
					{
						$FieldTitle = str_replace("cb_","",$v);
						// creat the columns title
							$colname = ucfirst($FieldTitle);
						// end

						// create the columns
						if(count($columns) > 0){
							if(!in_array($colname,$columns)){
								$columns[] = $colname;
							}
						}else{
							$columns[] = $colname;
						}
						// end

						$data1temp = "";
						for($i=0;$i<count($arrF[$v]);$i++)
						{
							$arrImpode = array();
							if(is_array($arrF[$v])){
								$Fieldvalue = "";

								if($arrF[$v][$i][$i+1] == 'F'){ // check if user has uploaded any file
									$Fieldvalue = REGPRO_FORM_DOCUMENT_URL_PATH."/".$arrF[$v][$i][$i];
								}else{
									if($arrF[$v][$i][$i]){
										$Fieldvalue = $arrF[$v][$i][$i];
									}
								}

								// add data in orignal data array
								if(count($arrF[$v]) > 1){
									$data1temp .= $Fieldvalue.", ";
								}else{
									$data1temp =  $Fieldvalue;
								}
								// end
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
						/*$arrFinancialFields[] = "Order id";
						$arrFinancialFields[] = "Registration id";*/
						$arrFinancialFields[] = "regdate";
						$arrFinancialFields[] = 'Payment Status';
						$arrFinancialFields[] = 'Payment Method';
						$arrFinancialFields[] = 'Event Ticket Name';
						$arrFinancialFields[] = 'Price';
						$arrFinancialFields[] = 'Tax';
						$arrFinancialFields[] = 'Total Price(Including Tax)';
						$arrFinancialFields[] = 'Coupon Code';
						$arrFinancialFields[] = 'Session fees';
						$arrFinancialFields[] = 'Additional field fees';
						$arrFinancialFields[] = 'Discount Amount';
						$arrFinancialFields[] = 'Admin Discount';
						$arrFinancialFields[] = 'Final Price';
						// end

						foreach($arrFinancialFields as $k=>$v)
						{
							$FieldTitle = $v;
							// creat the columns title
								$colname1 = ucfirst($FieldTitle);
							// end

							// create the columns
							if(count($columns1) > 0){
								if(!in_array($colname1,$columns1)){
									$columns1[] = $colname1;
								}
							}else{
								$columns1[] = $colname1;
							}
							// end

							// add registration date data
							if(!empty($data[$key]->uregdate)){
								//$data1[$key]['Regdate'] = strftime("%c",$data[$key]->uregdate + ($mosConfig_offset*60*60));
								$registrationproHelper = new registrationproHelper;
								$data1[$key]['Regdate'] = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'],  $data[$key]->uregdate + ($regproConfig['timezone_offset']*60*60));
							}else{
								$data1[$key]['Regdate'] = "NIL";
							}

							// add user financial data

							/*if(!empty($data[$key]->id)){
								$data1[$key]['Order id'] = $data[$key]->id;
							}

							if(!empty($data[$key]->id)){
								$data1[$key]['Registration id'] = $data[$key]->reg_id;
							}*/

							if(!empty($data[$key]->payment_status)){
								$data1[$key]['Payment Status'] = $data[$key]->payment_status;
							}
							if(!empty($data[$key]->payment_method)){
								$data1[$key]['Payment Method'] =$data[$key]->payment_method;
							}
							if(!empty($data[$key]->item_name)){
								$data1[$key]['Event Ticket Name'] =$data[$key]->item_name;
							}
							if(!empty($data[$key]->price_without_tax)){
								//$data1[$key]['Price'] = number_format($data[$key]->price_without_tax,2);
								if(empty($data[$key]->price_without_tax) || $data[$key]->price_without_tax == 0.00){
									// calculating the acutal amount with help of gorss amount and tax percentage
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
								$data1[$key]['Total Price(Including Tax)'] = number_format($data[$key]->price,2)-(!empty($data[$key]->AdminDiscount))?$data[$key]->AdminDiscount:0;
							}

							if(!empty($data[$key]->session_fees)){
								$data1[$key]['Session fees'] = $data[$key]->session_fees;
							}

							if(!empty($data[$key]->additional_field_fees)){
								$data1[$key]['Additional field fees'] = $data[$key]->additional_field_fees;
							}

							if(!empty($data[$key]->coupon_code)){
								$data1[$key]['Coupon Code'] = $data[$key]->coupon_code;
							}
							$adminDiscount = (!empty($data[$key]->AdminDiscount))?$data[$key]->AdminDiscount:0;
							if($data[$key]->discount_amount > 0.00){
								$data1[$key]['Discount Amount'] = number_format($data[$key]->discount_amount,2);
								$data1[$key]['Final Price'] 	= number_format($data[$key]->price + $data[$key]->additional_field_fees + $data[$key]->session_fees - $adminDiscount - $data[$key]->discount_amount,2);
							}else{
								$data1[$key]['Discount Amount'] = 0.00;
								$data1[$key]['Final Price'] 	= number_format($data[$key]->price + $data[$key]->additional_field_fees + $data[$key]->session_fees - $adminDiscount,2);
							}
							
							if(!empty($data[$key]->AdminDiscount)){
								$data1[$key]['Admin Discount'] = number_format($data[$key]->AdminDiscount,2);
								
							}
							// end
						}
					}
				}
			}
		}

		//echo "<pre>";print_r($columns); echo "<pre>";print_r($columns1);
		//echo "<pre>";print_r($data1); exit;

		$columns = array_merge($columns,$columns1);

		#### Creating final array to export data into .xls format #####
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
		###### END #######
//die;
		///echo "<pre>"; print_r($data2); exit;

		###### Create .xls file  ########
		if($data2){
			$flag = false;
			$filename = $event_info->titel ."_Report.xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");

			foreach($columns as $colkey=>$colvalue)
			{
				echo $colvalue."\t";
			}

			echo "\n";

			foreach($data2 as $datakey=>$datavalue)
			{
				if(is_array($data2[$datakey]))
				{
					foreach($data2[$datakey] as $k=>$v)
					{
						/*$show = preg_replace("[\n\r]", " ", $data2[$datakey][$k]);
						echo $show, "\t";*/

						// escape tab characters
						$str = preg_replace("/\t/", " ", $data2[$datakey][$k]);

						// escape new lines
						$str = preg_replace("/\r?\n/", " ", $str);
						// convert 't' and 'f' to boolean values
						if($str == 't') $str = 'TRUE'; if($str == 'f') $str = 'FALSE';
						// force certain number/date formats to be imported as strings
						if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) { $str = "$str"; }
						// escape fields that include double quotes
						if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';


						//echo $str, "\t";
						$str = str_replace(array('€','„','“'),array('EUR','"','"'),$str);
						$str = utf8_decode($str);
						echo $str, "\t";
					}
				}

				echo "\n";
			}
		}
		###### END #######
		exit;
	}

	function event_excel(){
		ob_clean();
		$model = $this->getModel('events');
		$rows  = $model->getData();
		//echo "<pre>";print_r($rows);echo "</pre>";die;
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=eventReport.xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		$columns = array();
		$columns[] = JText::_('ADMIN_EVENTS_SETT_USERS_ID');
		$columns[] = JText::_('ADMIN_SEARCH_CSV_EVENT_NAME');
		$columns[] = JText::_('EVENT_ADMIN_COUPONS_LBL_START_DATE');
		$columns[] = JText::_('EVENT_ADMIN_COUPONS_LBL_END_DATE');
		$columns[] = JText::_('EVENT_ADMIN_COUPONS_LBL_REGISTRATION_START_DATE');
		$columns[] = JText::_('EVENT_ADMIN_COUPONS_LBL_REGISTRATION_END_DATE');
		$columns[] = JText::_('ADMIN_SEARCH_CSV_LOCATION');
		$columns[] = JText::_('ADMIN_SEARCH_CSV_CATEGORY');
		$columns[] = JText::_('ADMIN_EVENT_LIST_TICKETS_COLUMN');
		$columns[] = JText::_('ADMIN_EVENT_LIST_SALES_COLUMN');
		$columns[] = JText::_('ADMIN_EVENTS_USER_ADDED_BY');

		foreach($columns as $colkey=>$colvalue)
		{
			echo $colvalue."\t";
		}

		echo "\n";
		$sales = 0;
		$registrationproHelper = new registrationproHelper;
		foreach($rows as $datakey=>$datavalue)
		{
			echo preg_replace("[\n\r]", " ", $datavalue->id);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->titel);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->dates);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->enddates);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->regstart);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->regstop);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->club).", ".preg_replace("[\n\r]", " ", $datavalue->city);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->catname);
			echo "\t";
			$ticket = '';
			foreach($datavalue->tickets as $key=>$val):
				$ticket .= $val->product_name.',';
			endforeach;
			echo preg_replace("[\n\r]", " ", $ticket);
			echo "\t";
			echo preg_replace("[\n\r]", " ", $datavalue->sales);
			echo "\t";
			if(empty($row->user_id)){
				$addedBy = JText::_('ADMIN_EVENTS_ADDED_BY_ADMIN');
			}else{
				$addedBy = $registrationproHelper->getEventManagerName($row->user_id);
			}
			echo preg_replace("[\n\r]", " ", $addedBy);
			echo "\t";
			echo "\n";
			$sales = 0;
		}
		exit;
	}
	
	/* 
	 * Function to fetch child events of the parent events
	 */
	public function getChildEvents()
	{
		ob_clean();
		$rowid = JRequest::getInt('pid');
		$count=0;
		$model = $this->getModel('events');
	    $pg= $this->get('Pagination');
		$rows1  = $model->getChildEvents($rowid);
		//echo '<pre>'; print_r ($rows1); die;
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	= $registrationproAdmin->config();	
				
		$html = "";
		$lastCount = 0;
		
		foreach($rows1 as $datakey)
		{ 	
			$lastCount++;
			$count++;
		    $ii = (($count + 1) * 1000 + $count)*1;
			$link      = 'index.php?option=com_registrationpro&amp;controller=events&amp;task=edit&amp;cid[]='. $datakey->id;
			$checked_sub   = JHTML::_('grid.checkedout', $datakey, $ii );
			$published = JHTML::_('grid.published', $datakey, $ii );
			include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tools.php';
			
			$imgPrefixSystem = JURI::root() . "images/regpro/system/";
			$imgPrefixEvents = JURI::root() . "images/regpro/events/";
			$imgCurr = getImageName($datakey->id, $datakey->user_id);
			if($datakey->image === '0') 
			{
				$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
			}else
			{
				$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();;
			}
			$short_descr = trim($datakey->shortdescription);
			if (strlen($short_descr) > 200) $short_descr = substr($short_descr, 0, 200) . " ...";
			
			$registrationproHelper = new registrationproHelper;
			$dt_start = $registrationproHelper->getFormatdate($regpro_config['formatdate'], $datakey->dates);
			$dt_end = $registrationproHelper->getFormatdate($regpro_config['formatdate'], $datakey->enddates);
			
			$html .='<tr id="kidrow_'.$rowid.'_'.$count.'" class="events_sub" >
					<td style="text-align:center;" id="checks_column">'.$checked_sub.'</td>
                    <td class="hidden-phone hidden-tablet">
						<img class="editlinktip hasTip thumbnail" title="<img src=\''.$imgName.'\' />" id="event_img" src="'.$imgName.'" width="60" />
					</td>

					<td>
						<span class="editlinktip hasTip" title="'.JText::_( 'ADMIN_EDIT_EVENT' ).'::'.$datakey->titel .'">
						<a href="'.$link.'"> '.htmlspecialchars($datakey->titel, ENT_QUOTES, 'UTF-8').'</a>
						</span>
						<div id="events_shortdescription">'.$short_descr.'</div>';
						if($datakey->published > 0 && $datakey->status < 5)
						{
							$html .= "<a href='".JURI::root().'index.php?option=com_registrationpro&view=event&did='.$row->id."' class='btn btn-success btn-mini' target='_blank'>Preview</a>";
						}
			$html .='
					</td>
						
					<td style="text-align:center;line-height:14px;">';
						if($dt_start !== $dt_end)
							{
								$html .= $star.$registrationproHelper->getFormatdate($regpro_config['formatdate'], $datakey->dates);
								$html .="<br/>-<br/>";
								$html .=$registrationproHelper->getFormatdate($regpro_config['formatdate'], $datakey->enddates);
							}else
							{
								$html .=$dt_start;						
							}
				    $html .='</td>

					<td style="text-align:center;">'. htmlspecialchars($datakey->club, ENT_QUOTES, 'UTF-8').'</td>
					<td style="text-align:center;">'. htmlspecialchars($datakey->catname, ENT_QUOTES, 'UTF-8').'</td>
                    <td id="tickets_column" style="text-align:center;">';
						$sales = 0;
						if(!empty($datakey->tickets)){
							$html .= "<table id='eventTicketsShow'>\n";
							$html .= "<tr>\n";
							$html .= "<th>".JText::_('ADMIN_EVENT_LIST_TICKETS_NAME_COLUMN')."</th>\n";
							$html .= "<th width=45 style=\"text-align:center;\">".JText::_('ADMIN_EVENT_LIST_SOLD_COLUMN')."</th>\n";
							$html .= "</tr>\n";
							foreach($datakey->tickets as $key=>$val) {
								$clr = '';
								$prod = "Tickets";
								if (trim($val->type) == "A")
								{
									$clr = ' id="product_row"';
									$prod = "Products";
								}

								$html .= "<tr valign=top".$clr.">\n";
								$html .= "<td>".$val->product_name."</td>\n";
								$tickets_sold = $val->product_quantity_sold * 1;
								if($val->product_quantity != 0)
								{
									$tickets_left = ($val->product_quantity * 1) - ($val->product_quantity_sold * 1);
								}else 
								{
									$tickets_left = "<span id='infinity_symbol'>&infin;</span>";
								}
								$html .= "<td style=\"text-align:center;color:#06a;\">\n";
								$html .="<span class=\"editlinktip hasTip\" title=\"$prod Sold : ".$tickets_sold."<br />$prod Left : ".$tickets_left."\">".$tickets_sold." / ".$tickets_left."</span>\n";
								$html .= "</td>\n";
								$html .= "</tr>\n";
								if($val->product_quantity_sold != 0) $sales += $val->product_quantity_sold * $val->total_price;
							}
							$html .= "<tr>\n";
							$html .= "<tr><td colspan=2 style=\"background-color:#ccc;padding:0px;margin:0px;height:0px;\"></td></tr>";
							$html .= "<td stye=\"text-align:left;\"><div style=\"margin-bottom:6px;font-style:bold;font-weight:700;margin-top:2px;\">Total: ".$regpro_config['currency_sign'].' '.$datakey->sales."</div>\n";
                            
							$nrregusers_array = $model->getRegistered($datakey->id);
							$nrregusers = 0;
							$text = '';
							$nrstatus = array();

							foreach ($nrregusers_array as $pid => $qty)
							{
								if(is_int($qty)) $nrregusers += $qty;
								else $nrstatus = $qty;
							}

							if(!empty($nrstatus))
							{
								foreach($nrstatus as $status => $qty)
								{
									$text .= JText::_('ADMIN_EVENTS_REGISTRATION_STATUS_'.$status) . ': '.$qty.'<br/>';
								}
								$text .= JText::_('ADMIN_EVENTS_REGISTRATION_STATUS_TOTAL'). ': '.$nrregusers. (($datakey->max_attendance!=0) ? ' / '.$datakey->max_attendance : '');
							}
							$linkreg = 'index.php?option=com_registrationpro&view=users&rdid='.$datakey->id.'&hidemainmenu=1';
							if ((trim($text) != '')&&(trim($linkreg) != ''))
								$html .= "<a href=\"$linkreg\" title=\"Edit Users\">$text</a>\n";


							$html .= '</td>
							<td style="text-align:center;" align=center>';
							
							if ($datakey->registra == 1)
							{
								$html .='<span class="editlinktip hasTip" title="Add new user">
									<a href="index.php?option=com_registrationpro&view=newuser&hidemainmenu=1&&did='. $datakey->id.'">
									<img src="components/com_registrationpro/assets/images/icon_events_add.png" width=16 height=16 border=0 />
									</a>
								</span>';
							}else
							{ 
								$html .='<span class="editlinktip hasTip" title="Registration disabled">
									<img src="components/com_registrationpro/assets/images/icon_events_noreg.png" width=16 height=16 border=0 />
								</span>';
							}
							$html .= "</td></tr></table>\n";
						} 
						else
						{
							$html .= "<div id=\"no_tickets\" style=\"line-height:15px;margin-bottom:5px;\">There are no tickets assigned to this event</div>\n";
							if ($datakey->registra == 1)
							{ 
								$html .='<span class="editlinktip hasTip" title="Add new user">
									<a href="index.php?option=com_registrationpro&view=newuser&hidemainmenu=1&&did='. $datakey->id .'">
									<img src="components/com_registrationpro/assets/images/icon_events_add.png" width=16 height=16 border=0 />
									</a>
								</span>';
							 } 
							 else 
							{ 
								$html .='<span class="editlinktip hasTip" title="Registration disabled">
									<img src="components/com_registrationpro/assets/images/icon_events_noreg.png" width=16 height=16 border=0 />
								</span>';
							}
						}
					$html .='</td>';
					$clr = "#080";
					if(($datakey->status == 1)||($datakey->status == 2)) $clr = "#800";
					$html .= "<td class='hidden-phone'style=\"text-align:center;color:$clr\"><b>".JText::_('ADMIN_EVENTS_STATUS_'.$datakey->status)."</b></td>\n";
					$html .= '<td align="center"  style="text-align:center;">';
				
						if(!$datakey->moderating_status)
						{
							$task = 'publish';
							$img = 'moderation.png';
							$alt = 'Need Moderation';
						}
						else
						{
							$task = $datakey->published ? 'unpublish' : 'publish';
							$img = $datakey->published ? 'publish_g.png' : 'publish_x.png';
							$alt = $datakey->published ? 'Published' : 'Unpublished';
							if($alt == 'Published'){
								$class = 'btn btn-micro active hasTooltip';
							}else{
								$class = 'btn btn-micro hasTooltip';
							}
						}
					
					     $html .= '<a href="javascript: void(0);" onclick="return listItemTask(\'cb'. $ii.'\',\''.$task.'\')" class=" '. $class.'"><img src=" '.REGPRO_ADMIN_IMG_PATH.'/'. $img.'" width="16px" height="16px" border="0" title="'. $alt .'" alt="'.$alt .'" /></a>';
                    $html .='</td>';
					//echo $count; die;
					if($count >1){
						$upbtn = '<a class="btn btn-micro" href="javascript:void(0);" onclick="return listItemTask(\'cb'.$ii.'\',\'orderupevents\')"><i class="icon-uparrow" style="float:right"></i></a>';
						
					}
					if($lastCount == (count($rows1))){
						
						$downbtn = '<br/>';
					}else{
						$downbtn = '<a class="btn btn-micro" href="javascript:void(0);" onclick="return listItemTask(\'cb'.$ii.'\',\'orderdownevents\')"><i class="icon-downarrow" style="float:right"></i></a><br/>';
					}
					$html .='<td class="hidden-phone"><table id="table_order">';
					
					$html .='<tr><td>'.$upbtn.$downbtn.'<input type="text" name="order[]" size="5" value="'.$datakey->ordering.'" '.$disabled.' class="text_area" style="text-align:center;width:25px;margin:0px;padding:2px;" />
					</td></tr>';
					$html .='</table>';
					
					$html .= '</td>';
					$html .='<td>'.$datakey->id.'</td>';
		}
		//sleep(1);
	    echo ($html); die;
		
    } 
}
?>