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
jimport('joomla.application.component.helper');

class registrationproHelper extends JComponentHelper
{
    function add_regpro_scripts() {
		$document	=  JFactory::getDocument();

		//add css and js to document
		//$document->addScript('../includes/js/joomla/popup.js');
		//$document->addStyleSheet('../includes/js/joomla/popup.css');
		$document->addScript('../components/com_registrationpro/assets/javascript/fvalidate/fValidate.config.js');
		$document->addScript('../components/com_registrationpro/assets/javascript/fvalidate/fValidate.core.js');
		$document->addScript('../components/com_registrationpro/assets/javascript/fvalidate/fValidate.lang-enUS.js');
		$document->addScript('../components/com_registrationpro/assets/javascript/fvalidate/fValidate.validators.js');
		$document->addScript('../components/com_registrationpro/assets/javascript/fvalidate/fValidate.controls.js');
		$document->addScript('../components/com_registrationpro/assets/javascript/fvalidate/fValidate.datetime.js');
		$document->addScript('components/com_registrationpro/assets/javascript/ColorPicker2.js');
		$document->addScript('components/com_registrationpro/assets/javascript/recurrence.js');
	} // end  function

	// add js/css files for backend section
	function add_regpro_frontend_scripts($css = array('regpro','regpro_calendar'),$jss = array('mootools','mootools_tooltip','fvalidator',))
	{
		$document	=  JFactory::getDocument();

		// add css
		foreach($css as $ckey => $cvalue) {
			$document->addStyleSheet(JURI::root().'components/com_registrationpro/assets/css/'.$cvalue.'.css','text/css',"screen");
		}

		// add js
		foreach($jss as $jkey => $jvalue) {
			$document->addScript(JURI::root().'components/com_registrationpro/assets/javascript/'.$jvalue.'.js');
		}

		$document->addScript(JURI::root().'components/com_registrationpro/assets/javascript/jquery1_9_1.js');

		/// add bootstrap css and js
		?>

		<script type="text/javascript">
			var bootjs = "<?php echo JURI::root().'components/com_registrationpro/assets/javascript/bootstrap/js/bootstrap.min.js';?>";
			var bootcss = "<?php echo JURI::root().'components/com_registrationpro/assets/javascript/bootstrap/css/bootstrap.css';?>";
			var bootcss1 = "<?php echo JURI::root().'components/com_registrationpro/assets/javascript/bootstrap/css/bootstrap.extended.css';?>";
			var j = jQuery.noConflict();
			if(typeof(j.fn.modal) === 'undefined'){
				  var fileref=document.createElement('script');
				  fileref.setAttribute("type","text/javascript");
				  fileref.setAttribute("src", bootjs);
				  if (typeof fileref!="undefined") {
				 	document.getElementsByTagName("head")[0].appendChild(fileref)
				  }

				  var fileref2=document.createElement("link");
				  fileref2.setAttribute("rel", "stylesheet");
				  fileref2.setAttribute("type", "text/css");
				  fileref2.setAttribute("href", bootcss);
				  if (typeof fileref2!="undefined") {
				 	document.getElementsByTagName("head")[0].appendChild(fileref2)
				  }

				  var fileref3=document.createElement("link");
				  fileref3.setAttribute("rel", "stylesheet");
				  fileref3.setAttribute("type", "text/css");
				  fileref3.setAttribute("href", bootcss1);
				  if (typeof fileref3!="undefined") {
				   document.getElementsByTagName("head")[0].appendChild(fileref3);
				  }

		}
		</script>

		<?php
		$document->addScript(JURI::root().'includes/js/joomla.javascript.js');
	}

	// check user login or not
	function check_user_login() {
		global $mainframe, $Itemid;
		$user	= JFactory::getUser();
		if(!$user->id){
			$link 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=unauthorize&Itemid=".$Itemid);
			$link 	= str_replace("&amp;", "&", $link);
			$mainframe->redirect($link);
		}
	}

	// get current date according to locale setting in global configuration
	function getCurrent_date($format = 'Y-m-d H:i:s') {
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig	= $registrationproAdmin->config();

		$config   = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		$nowdate  = JFactory::getDate('now',$tzoffset);

		return $nowdate->format($format, true, true);
	}

	// get current date according to locale setting in global configuration
	function getCurrent_date_unix($local = false) {
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig = $registrationproAdmin->config();
		$config   = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		$nowdate  = JFactory::getDate();
		return $nowdate->toUnix();
	}

	// get current date according to locale setting in global configuration
	function getCurrent_date_mysql($local = false) {
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig = $registrationproAdmin->config();
		$config   = JFactory::getConfig();
		$tzoffset = $config->get('config.offset');
		$nowdate  = JFactory::getDate();
		$nowdate->setTimezone($regproConfig['timezone_offset']);
		return $nowdate->toMySQL($local);
	}

	// get current date according to locale setting in global configuration
	function getFormatdate ($format = 'Y-M-d H:i:s', $date) {
		jimport('joomla.utilities.date');
		$date = new JDate($date);
		return $date->format($format);
	}

	// check mootools version
	function check_mootools_version() {
		global $mainframe, $Itemid;
		$database = JFactory::getDBO();
		$database->setQuery("SELECT count(*) FROM #__plugins WHERE folder = 'system' AND element = 'mtupgrade' AND published = 1");
		$row 	  = $database->loadResult();
		return $row;
	}

	// clean events automatically
	function clean_events() {
		$database = JFactory::getDBO();
		$registrationproAdmin = new registrationproAdmin;
		$regproConfig = $registrationproAdmin->config();
		if($regproConfig['oldevent'] != 0)
		{
			$minus        = intval($regproConfig['minus']);
			$archiveby    = intval($regproConfig['archiveby']);
			$current_date = registrationproHelper::getCurrent_date("Y-m-d H:i:s");

			$columnname = "enddates";
			if($archiveby == 1) $columnname = "dates";

			$query = "UPDATE #__registrationpro_dates SET status = 5 WHERE DATE_SUB('".$current_date."', INTERVAL ". $minus ." DAY) > ".$columnname;
			$database->setQuery($query);
			$database->query();
			
			$query = "UPDATE #__registrationpro_dates SET published = 0 WHERE DATE_SUB('".$current_date."', INTERVAL ". $minus ." DAY) > ".$columnname." AND published>0";
			$database->setQuery($query);
			$database->query();
			
			$query = "SELECT id FROM #__registrationpro_dates WHERE parent_id=0 OR ordering<10000";
			$database->setQuery($query);
			$parent_events = $database->loadObjectList();
			$kids = array();
			foreach($parent_events as $event) {
				if((!$kids[$event->id])||(!isset($kids[$event->id]))) $kids[$event->id] = array();
				$query = "SELECT id, ".$columnname." FROM #__registrationpro_dates WHERE parent_id=".$event->id." AND ordering>=10000 AND published>=0";
				$database->setQuery($query);
				$kid_events = $database->loadObjectList();
				foreach($kid_events as $kid) {
					if($kid->id) {
						if((!$kids[$kid->id])||(!isset($kids[$kid->id]))) $kids[$kid->id] = array();
						$kids[$event->id][$kid->id] = $kid->$columnname;
					}
				}
			}
			
			$events2proceed = array();
			foreach($kids as $key=>$val) if(count($val) == 0) $events2proceed[] = $key;

			if(count($events2proceed) > 0) {
				$evs = implode(',', $events2proceed);
				if ($regproConfig['oldevent'] == 1) $query = "DELETE FROM #__registrationpro_dates WHERE (DATE_SUB('".$current_date."', INTERVAL ".$minus." DAY) > ".$columnname. ") AND (id IN (". $evs ."))";
				if ($regproConfig['oldevent'] == 2) $query = "UPDATE #__registrationpro_dates SET published = -1 WHERE (DATE_SUB('".$current_date."', INTERVAL ".$minus." DAY) > ".$columnname. ") AND (id IN (". $evs ."))";
				$database->setQuery($query);
				$database->query();
			}
		}
	}

	// send email notification as reminder to registered users for event
	function reminder() {
		$regpro_registrations_emails = new regpro_registrations_emails;
		$regpro_registrations_emails->send_Reminder_email();
	}

	/**
	 * this methode calculate the next date
	 */
	function calculate_recurrence($recurrence_row) {
		// get the recurrence information
		$recurrence_number = $recurrence_row['recurrence_number'];
		$recurrence_type = $recurrence_row['recurrence_type'];

		$day_time = 86400;	// 60 sec. * 60 min. * 24 h
		$week_time = 604800;// $day_time * 7days
		$date_array = ELHelper::generate_date($recurrence_row['dates'], $recurrence_row['enddates']);

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
			default:
				$weekday_must = ($recurrence_row['recurrence_type'] - 3);	// the 'must' weekday
				if ($recurrence_number < 5) {	// 1. - 4. week in a month
					// the first day in the new month
					$start_day = mktime(1,0,0,($date_array["month"] + 1),1,$date_array["year"]);
					$weekday_is = date("w",$start_day);							// get the weekday of the first day in this month

					// calculate the day difference between these days
					$day_diff = ($weekday_must + 7) - $weekday_is;
					if ($weekday_is <= $weekday_must) $day_diff = $weekday_must - $weekday_is;

					$start_day = ($start_day + ($day_diff * $day_time)) + ($week_time * ($recurrence_number - 1));
				} else {	
					// the last or the before last week in a month
					// the last day in the new month
					$start_day = mktime(1,0,0,($date_array["month"] + 2),1,$date_array["year"]) - $day_time;
					$weekday_is = date("w",$start_day);
					
					// calculate the day difference between these days
					$day_diff = ($weekday_is - $weekday_must) + 7;
					if ($weekday_is >= $weekday_must) $day_diff = $weekday_is - $weekday_must;
					
					$start_day = ($start_day - ($day_diff * $day_time));
					if ($recurrence_number == 6) {	// before last?
						$start_day = $start_day - $week_time;
					}
				}
				break;
		}

		$recurrence_row['dates'] = date("Y-m-d", $start_day);
		if ($recurrence_row['enddates']) $recurrence_row['enddates'] = date("Y-m-d", $start_day + $date_array["day_diff"]);
		return $recurrence_row;
	}

	/**
	 * this method generate the date string to a date array
	 */
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

	// check community builder existance
	function chkCB() {
		global $mainframe, $ueConfig;
		jimport( 'joomla.application.component.helper' );

		$CBvalues = JComponentHelper::getComponent("com_comprofiler");

		if($CBvalues->id){
			// include CB files
				require_once(JPATH_SITE. "/administrator/components/com_comprofiler/plugin.foundation.php");
				require_once(JPATH_SITE . "/administrator/components/com_comprofiler/ue_config.php");
			// include CB language files
				$UElanguagePath	= JPATH_SITE.'/components/com_comprofiler/plugin/language';
				$UElanguage		= $mainframe->getCfg( 'lang' );
				if ( ! file_exists( $UElanguagePath . '/' . $UElanguage . '/' . $UElanguage . '.php' ) ) {
					$UElanguage = 'default_language';
				}
				require_once( $UElanguagePath . '/' . $UElanguage . '/' . $UElanguage . '.php' );
			// end
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	= $registrationproAdmin->config();

			if($ueConfig['reg_admin_allowcbregistration'] && $regpro_config['cbintegration'] == 1)	{
				return true;
			} else return false;
		} else return false;
	}

	// check chkJoomsocial existance
	function chkJoomsocial() {
		global $mainframe;
		$CBvalues = parent::getComponent("com_community");

		if($CBvalues->id){
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config 	= $registrationproAdmin->config();

			if($regpro_config['cbintegration'] == 2)	{
				return true;
			} else return false;
		} else return false;
	}


	function chkCoreProfiles() {
		global $mainframe;
		$plugin = JPluginHelper::isEnabled('user', 'profile');

        if ($plugin) {
			return true;
        } else return false;
	}


	// check event seats are full or not
	function check_is_event_full ($eventid) {
		$database = JFactory::getDBO();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	= $registrationproAdmin->config();

		$query = "SELECT * FROM #__registrationpro_dates WHERE id = ".$eventid;
		$database->setQuery($query);
		$row = $database->loadObject();

		if($row->max_attendance > 0) {

			$query = "SELECT count(*) as cnt FROM #__registrationpro_register WHERE active=1 AND rdid = ".$eventid;
			$database->setQuery($query);
			$registerdusers = $database->loadResult();
			if($registerdusers){
				if($row->max_attendance >= $registerdusers){
					return true;
				} else return false;
			} else return false;
		} else return false;
	}

	// check event registration dates are over or not
	function check_is_event_registration_over ($eventid) {
		$registrationproHelper = new registrationproHelper;
		$current_date = $registrationproHelper->getCurrent_date();
		$database = JFactory::getDBO();

		$database->setQuery("SELECT regstart, regstarttimes,regstop, regstoptimes, regstop_type FROM #__registrationpro_dates WHERE id = ".$eventid);
		$reg = $database->loadRow();

		$event_regstartdate = $reg[0]." ".$reg[1];
		$event_regenddate 	= $reg[2]." ".$reg[3];

		if($reg[0] != '0000-00-00' && $reg[2] != '0000-00-00'){
			if($current_date < $event_regstartdate || $current_date > $event_regenddate) {
				return true;
			}
		} else return false;
	}


	// check registration enabled or not
	function check_event_registration_enable($row, $regproConfig, $step) {
		global $mainframe, $Itemid;

		if ($row->registra == 0) {
			$msg = "<img src='".REGPRO_IMG_PATH."/error.png' align='absmiddle' border='0' title='Error'/>";
			$msg .= JText::_('EVENTS_REGISTRA_DISABLED');

			if($step == 1){
				return $msg."<br />";
			} else {
				$link = JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->did."&Itemid=$Itemid");
				$mainframe->redirect($link);
			}
		}
	}

	// check maximum attendance validation
	function check_max_attendance(&$row, $other = array(), $cart, $regproConfig, $step) {
		global $mainframe, $Itemid;
		$database	= JFactory::getDBO();

		/* Creating an array for total qty according to events id for validation of max limit for every event */
		$arr_tickets = array();
		if(is_array($cart['ticktes']) && is_array($cart['eventids'])) {
			$i = 0;
			$j = 0;
			$cnt = count($cart['ticktes']);

			foreach($cart['eventids'] as $ekey => $evalue) {
				foreach($cart['ticktes'] as $tkey => $tvalue) {
					if($tvalue->type == 'E') {
						if($evalue == $tvalue->regpro_dates_id) {

							$arr_tickets[$ekey]['regpro_dates_id'] 	= $tvalue->regpro_dates_id;

							if($other['ticket_id'] > 0){
								if($tvalue->id == $other['ticket_id']){
									$arr_tickets[$ekey]['qty'] += $other['qty'];
								}else{
									$arr_tickets[$ekey]['qty'] += $tvalue->qty;
								}
							}else if($other['ticket_id'] == 0){
								if($other['qty'] == 0){
									$arr_tickets[$ekey]['qty'] += $tvalue->qty;
								}else{
									$arr_tickets[$ekey]['qty'] += $other['qty'];
								}
							}else{
								$arr_tickets[$ekey]['qty'] += $tvalue->qty;
							}
						}
					}
				}
			}
		}else{
			$arr_tickets[0]['regpro_dates_id'] = $row->did;
			$arr_tickets[0]['qty'] = 1;

		}

		// checking the maxlimit for every event
		if(is_array($arr_tickets)) {
			foreach($arr_tickets as $arrtkey => $arrtvalue)
			{
				// get event record
				$query	= "SELECT a.id, a.max_attendance FROM #__registrationpro_dates as a WHERE id = ".$arrtvalue['regpro_dates_id']." and a.published = 1";
				$database->setQuery($query);
				$event = $database->loadObject();

				// get total resitered users according to event id
				$totqty = 0;
				$query = "SELECT count(*) FROM #__registrationpro_register WHERE rdid = ".$event->id." and active = 1";
				$database->setQuery($query);

				$registered_users 	   = $database->loadResult();

				if(is_array($other) && count($other) > 0){
					$registered_users  	+= $arrtvalue['qty'];
				}else{
					$registered_users = $registered_users + $arrtvalue['qty'];
				}

				if($registered_users > $event->max_attendance && $event->max_attendance!=0){
					$msg = "<img src='".REGPRO_IMG_PATH."/error.png' align='absmiddle' border='0' title='Error'/>";
					$msg .= sprintf(JText::_('EVENTS_REGISTRA_MAX_ATTN'),registrationproHelper::getEventName($arrtvalue['regpro_dates_id']));
					if($step == 1){
						return $msg.'<br/>';
					}elseif($step == 2){
						$link = JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->did."&Itemid=$Itemid");
						$mainframe->redirect($link);
					}elseif($step == 3){
						$link = JRoute::_("index.php?option=com_registrationpro&controller=cart&task=cart&Itemid=$Itemid");
						$mainframe->redirect($link, $msg);
					} else return $msg;
				}
			}
		}
	}

	// check event registration start and end date
	function check_event_registration_date(&$row, $regproConfig, $step) {
		global $mainframe, $Itemid;
		$current_date = registrationproHelper::getCurrent_date();
		$database = JFactory::getDBO();
		$database->setQuery("SELECT regstart, regstarttimes,regstop, regstoptimes, regstop_type FROM #__registrationpro_dates WHERE id = ".$row->did);
		$reg = $database->loadRow();

		$event_regstartdate = $reg[0]." ".$reg[1];
		//$event_regstartdate = strtotime($reg[0]." ".$reg[1]);
		$event_regenddate 	= $reg[2]." ".$reg[3];
		//$event_regenddate 	= strtotime($reg[2]." ".$reg[3]);

		$tzoffset = $regproConfig['timezone_offset'];
		/**
		 *	First set the time zone to UTC
		 *	So that we get correct time for the current region.
		 *	Added by Sushil on 13-02-2015
		 */
		date_default_timezone_set('UTC');
		/*************************************/
		
		$time     = time() + ($tzoffset*60*60);
		$today    = date( 'Y-m-d H:i:s',$time);
		$current_date = $today;
		//$current_date = strtotime($today);
		//echo '<br/>Current Date : '.$current_date;
		//echo '<br/>Start Date : '.$event_regstartdate;
		//echo '<br/>End Date : '.$event_regenddate;
		if($reg[0] != '0000-00-00' && $reg[2] != '0000-00-00'){
			if($current_date < $event_regstartdate || $current_date > $event_regenddate) {
				$msg = "<img src='".REGPRO_IMG_PATH."/error.png' align='absmiddle' border='0' title='Error'/>";
				$msg .= sprintf(JText::_('EVENTS_REGISTRA_REG_PERIOD').'<br/>',registrationproHelper::getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'], $reg[0]." ".$reg[1]), registrationproHelper::getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'], $reg[2]." ".$reg[3]));
				if($step == 1){
					return $msg;
				} else {
					$link = JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->did."&Itemid=$Itemid");
					$mainframe->redirect($link);
				}
			}
		}else{
			$msg = "<img src='".REGPRO_IMG_PATH."/error.png' align='absmiddle' border='0' title='Error'/>";
			$msg .=  JText::_('EVENTS_REGISTRA_REG_DATE_NOT_MENTION');

			if($step == 1){
				return $msg;
			}else{
				$link = JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->did."&Itemid=$Itemid");
				$mainframe->redirect($link);
			}
		}
	}

	// Check multiple event registration
	function check_event_multiple_registration($row, $regpro_config) {
		$session = JFactory::getSession();
		$cart = $session->get('cart');
		$msg = "";
		if($regpro_config["multiple_registration_button"] == 0){
			if($cart && $row) {
				if($cart['eventids'][$row->did] != $row->did){
					$msg = "<img src='".REGPRO_IMG_PATH."/error.png' align='absmiddle' border='0' title='Error'/>";
					$msg .=  JText::_('EVENT_MULTIPLE_EVENTS_REGSTRATION_NOT_ALLOWED');
				}
			}
		}
		return $msg;
	}

	// check form input
	function checkInput($input, &$cartdata, $regpro2Config){
		$payment_ids = array();
		$form = array();

		$req = $cartdata['form_data'];

		if(isset($req['users_tickets'])){
			if(is_array($req['users_tickets'])){
				$payment_ids = $req['users_tickets'];
			}
		} else $payment_ids[0] = 1;

		if(!isset($req['form'])) $req['form'] = array();
		$form = $req['form'];

		// Check group regsitration
		$allowgroupregistration = 0;
		if(is_array($cartdata['groupregistrations']) && count($cartdata['groupregistrations']) > 0 ){
			$allowgroupregistration = 1;
		}

		// Check group registration
		if($allowgroupregistration == 1){
			$form_fields = array_keys($form);
			if($cartdata['ticktes']){
				$firstticketid = 0;
				$newform = "";
				$formcount = 0;  //global $formcount;
				$gformcount = 0; //global $gformcount;
				$tempgcounter = 0;

				// loop to arrange all the tickets event wise
				foreach($cartdata['eventids'] as $ekey => $evalue){
					$gflag = 0;
					if(is_array($cartdata['groupregistrations']) && count($cartdata['groupregistrations']) > 0 ) {
						foreach($cartdata['groupregistrations'] as $gkey => $gvalue){
							if($evalue == $gvalue){
								$gflag = 1;
								foreach($cartdata['ticktes'] as $ckey=>$cvalue) {
									if($gvalue == $cartdata['ticktes'][$ckey]->regpro_dates_id) {
										$firstticketid = $cartdata['ticktes'][$ckey]->id;
										if($cartdata['ticktes'][$ckey]->type == "E") {
											for($j=0; $j < $cartdata['ticktes'][$ckey]->qty; $j++) {
												foreach($form_fields as $ffkey=>$ffvalue) {
													foreach($form[$ffvalue] as $k => $kvalue) {
														// get the start index for group registration
														$startkey = 0;
														$sflag	= 0;
														if(is_array($form[$ffvalue][$k])){
															foreach($form[$ffvalue][$k] as $kkkey => $kkvalue) {
																if(is_array($kkvalue)){
																	foreach($kkvalue as $kkkkey => $kkkvalue) {
																		if($kkkkey == $firstticketid) {
																			$startkey = $k;
																			$sflag	= 1;
																		}
																	}
																}
															}
														}

														if(is_array($form[$ffvalue][$startkey][0]) && $sflag == 1) {
															for($l=0; $l<count($form[$ffvalue][$startkey]); $l++) {
																if($form[$ffvalue][$k][$l][$firstticketid]) {
																	$newform[$ffvalue][$gformcount][][$firstticketid] = $form[$ffvalue][$k][$l][$firstticketid];
																}
															}
														} else {
															if($form[$ffvalue][$k][$firstticketid]){
																$newform[$ffvalue][$gformcount][$firstticketid] = $form[$ffvalue][$k][$firstticketid];
															}
														}
													}
												}
												$gformcount++;
											}
										}
									}
								}
							}
						}
					}

					if($gflag == 0){
						foreach($cartdata['ticktes'] as $ckey=>$cvalue) {
							if($evalue == $cartdata['ticktes'][$ckey]->regpro_dates_id){

								$paymentid = $cartdata['ticktes'][$ckey]->id;

								if($cartdata['ticktes'][$ckey]->type == "E"){

									// store the actuall counter values of group registraion
									$tempgcounter = $gformcount;

									foreach($form_fields as $ffkey=>$ffvalue)
									{
										// manage the counter for creating the form data array to save into the table
										if($gformcount > 0 && $tempgcounter > 0){
											$gformcount = $tempgcounter;
											$formcount = $gformcount;
										}

										$formcount = 0;
										foreach($form[$ffvalue] as $k => $kvalue)
										{
											// get the start index for group registration
												$startkey = 0;
												$sflag	= 0;
												if(is_array($form[$ffvalue][$k])){
													foreach($form[$ffvalue][$k] as $kkkey => $kkvalue)
													{
														if(is_array($kkvalue)){
															foreach($kkvalue as $kkkkey => $kkkvalue)
															{
																if($kkkkey == $paymentid){
																	$startkey = $k;
																	$sflag	= 1;
																}
															}
														}
													}
												}
											// end

											if(is_array($form[$ffvalue][$startkey][0]) && $sflag == 1){ // storing values of multicheckbox, multiselectbox etc..
												for($l=0; $l<count($form[$ffvalue][$startkey]); $l++)
												{
													if($form[$ffvalue][$startkey][$l][$paymentid]){
														$newform[$ffvalue][$formcount][][$paymentid] = $form[$ffvalue][$startkey][$l][$paymentid];
													}
												}
												$formcount++;
											}else{
												if($form[$ffvalue][$k][$paymentid]) {
													$newform[$ffvalue][$formcount][$paymentid] = $form[$ffvalue][$k][$paymentid];
													$formcount++;
												}
											}
											// updating the group registration counter
											if($formcount > $gformcount){
												$gformcount = $formcount;
											}
										}
									}
								}
							}
						}
					}
				} // eventids loop


				// If group registration exists then add additional form fields array in payment array
				$arr_additional = array();
				if(is_array($cartdata['groupregistrations']) && count($cartdata['groupregistrations']) > 0 ){
				foreach($cartdata['groupregistrations'] as $gkey => $gvalue){
				foreach($cartdata['ticktes'] as $ckey=>$cvalue)
				{
					if($cartdata['ticktes'][$ckey]->type == "E" && $cartdata['ticktes'][$ckey]->regpro_dates_id == $gvalue){
						$arr_additional = array();
						for($j=0; $j < $cartdata['ticktes'][$ckey]->qty; $j++)
						{
							foreach($newform as $nkey => $nvalue)
							{
								if($nkey == 'regpro_event_id') {
									foreach($newform[$nkey] as $l => $lvalue)
									{
										if($lvalue[$cartdata['ticktes'][$ckey]->id] == $gvalue) {
										if(is_array($cartdata['ticktes'][$ckey]->additional_form_field_fees) && count($cartdata['ticktes'][$ckey]->additional_form_field_fees) > 0)
										{
											foreach($cartdata['ticktes'][$ckey]->additional_form_field_fees as $afkey => $afvalue)
											{
												if(is_array($afvalue)) {
													foreach($afvalue as $affkey => $affvalue)
													{
														$arr_additional[$l][$affkey]['ticket_id'] = $affvalue['ticket_id'];
														$arr_additional[$l][$affkey]['event_id'] = $affvalue['event_id'];
														$arr_additional[$l][$affkey]['amount'] = $affvalue['amount'];
														$arr_additional[$l][$affkey]['field_name'] = $affvalue['field_name'];
														$arr_additional[$l][$affkey]['qty'] = $affvalue['qty'];
													}
												}
											}
										}
										}
									}
								}
							}
							$cartdata['ticktes'][$ckey]->additional_form_field_fees = $arr_additional;
						}
					}
				}
				}
				}

				if(is_array($newform)) $form = $newform;
			}
		}

		// upload file
		if($_FILES){
			$Files_data = registrationproHelper::uploadFormFile($_FILES);

			$form = array_merge($form, $Files_data);
		}

		if($input == 'users_tickets'){
			return $payment_ids;
		}elseif($input == 'form'){
			return $form;
		}else{
			return false;
		}
	}// end  function


	// check form input
	  function checkInput_admin($input, &$cartdata, $regpro2Config){
		//echo"<pre>";print_r($cartdata); exit;
		$payment_ids = array();
		$form = array();

		$req = $cartdata['form_data'];

		if(isset($req['users_tickets'])){
			if(is_array($req['users_tickets'])){
				$payment_ids = $req['users_tickets'];
			}
		} else $payment_ids[0] = 1;

		if(!isset($req['form'])) $req['form'] = array();
		$form = $req['form'];

		// Check group regsitration
		$groupregistration = JRequest::getVar('allowgroupregistration',array(),'POST');
		if($groupregistration && $cartdata['allowgroup'] == 1){
			$allowgroupregistration = 1;
		}else $allowgroupregistration = 0;

		// Check group registration
		if($allowgroupregistration == 1){
			$form_fields = array_keys($form);
			if($cartdata['ticktes']){
				$firstticketid = 0;
				$newform = "";
				$formcount = 0;
				// loop to arrange all the tickets event wise
	foreach($cartdata['eventids'] as $ekey => $evalue){
		$gflag = 0;
		if(is_array($cartdata['groupregistrations']) && count($cartdata['groupregistrations']) > 0 ){
			foreach($cartdata['groupregistrations'] as $gkey => $gvalue){
				if($evalue == $gvalue){
					$gflag = 1;
				foreach($cartdata['ticktes'] as $ckey=>$cvalue)
				{
					if($gvalue == $cartdata['ticktes'][$ckey]->regpro_dates_id){
						$firstticketid = $cartdata['ticktes'][$ckey]->id;
					}

					if($cartdata['ticktes'][$ckey]->type == "E"){
						for($j=0; $j < $cartdata['ticktes'][$ckey]->qty; $j++)
						{
							foreach($form_fields as $ffkey=>$ffvalue)
							{
								for($k=0; $k < count($form[$ffvalue]); $k++)
								{
									if(is_array($form[$ffvalue][0][0])){
										for($l=0; $l<count($form[$ffvalue][0]); $l++)
										{
											$newform[$ffvalue][$formcount][$l][$cartdata['ticktes'][$ckey]->id] = $form[$ffvalue][0][$l][$firstticketid];
										}
									}else{
										$newform[$ffvalue][$formcount][$cartdata['ticktes'][$ckey]->id] = $form[$ffvalue][0][$firstticketid];
									}
								}
							}
							$formcount++;
						}
					}
				}
			}
		}
	}
}

// If group registration exists then add additional form fields array in payment array
				$arr_additional = array();
				if(is_array($cartdata['groupregistrations']) && count($cartdata['groupregistrations']) > 0 ){
				foreach($cartdata['groupregistrations'] as $gkey => $gvalue){
				foreach($cartdata['ticktes'] as $ckey=>$cvalue)
				{
					if($cartdata['ticktes'][$ckey]->type == "E" && $cartdata['ticktes'][$ckey]->regpro_dates_id == $gvalue){
						//echo $gformcount."<br>";
						$arr_additional = array();
						for($j=0; $j < $cartdata['ticktes'][$ckey]->qty; $j++)
						{
							foreach($newform as $nkey => $nvalue)
							{
								if($nkey == 'regpro_event_id') {
									foreach($newform[$nkey] as $l => $lvalue)
									{
										if($lvalue[$cartdata['ticktes'][$ckey]->id] == $gvalue) {
										if(is_array($cartdata['ticktes'][$ckey]->additional_form_field_fees) && count($cartdata['ticktes'][$ckey]->additional_form_field_fees) > 0)
										{
											foreach($cartdata['ticktes'][$ckey]->additional_form_field_fees as $afkey => $afvalue)
											{
												//echo $afkey."<br/>";
												if(is_array($afvalue)) {
													foreach($afvalue as $affkey => $affvalue)
													{
														$arr_additional[$l][$affkey]['ticket_id'] = $affvalue['ticket_id'];
														$arr_additional[$l][$affkey]['event_id'] = $affvalue['event_id'];
														$arr_additional[$l][$affkey]['amount'] = $affvalue['amount'];
														$arr_additional[$l][$affkey]['field_name'] = $affvalue['field_name'];
														$arr_additional[$l][$affkey]['qty'] = $affvalue['qty'];
													}
												}
											}
										}
										}
									}
								}
							}
							$cartdata['ticktes'][$ckey]->additional_form_field_fees = $arr_additional;
						}
					}
				}
				}
				}
				// end


				if(is_array($newform)){
					$form = $newform;
				}
			}
			//echo "<pre>";print_r($form); exit;
		}

		// upload file
		if($_FILES){
			$Files_data = registrationproHelper::uploadFormFile($_FILES);

			$form = array_merge($form, $Files_data);
		}

		if($input == 'users_tickets'){
			return $payment_ids;
		}elseif($input == 'form'){
			return $form;
		}else{
			return false;
		}
	}// end  function


	// upload file from user form
	  function uploadFormFile($Files){
		//echo "<pre>"; print_r($Files); exit;

		global $mainframe, $Itemid;

		$block_extensions = explode(",",strtolower(REGPRO_FORM_INVALID_EXTENSIONS));
		//echo"<pre>"; print_r($block_extensions);

		if(!empty($Files['form']['name']) && count($Files['form']['name']) > 0){

			foreach($Files['form']['name'] as $fkey => $fvalue)
			{
				foreach($Files['form']['name'][$fkey] as $fkey1 => $fvalue1)
				{
					foreach($Files['form']['name'][$fkey][$fkey1] as $fkey2 => $fvalue2)
					{
						$uploaded_file_name = $Files['form']['name'][$fkey][$fkey1][$fkey2];
						// check if uploaded file is exsit
						if(trim($uploaded_file_name) != ""){
							$file_extension = strtolower(substr($uploaded_file_name, strrpos($uploaded_file_name, '.'))); // get the uploaded file extension
							 // check max file size
						 	if($Files['form']['error'][$fkey][$fkey1][$fkey2] != 1){
								if($Files['form']['size'][$fkey][$fkey1][$fkey2] < REGPRO_FORM_MAX_UPLOAD_FILESIZE){
									// check for allowed file extesions
									if(in_array(str_replace(".","",$file_extension), $block_extensions)) {
										$newfilename = registrationproHelper::str_makerand(10,12,1,0,1).$file_extension;
										$Files['form']['name'][$fkey][$fkey1][$fkey2] = $newfilename;
										$Files['form']['name'][$fkey][$fkey1][$fkey2 + 1] = "F";

										// check file is uploaded or not
										if(is_uploaded_file($Files['form']['tmp_name'][$fkey][$fkey1][$fkey2])){

											// check if fomrs directory exists or not, If not create the forms directory
											if(!is_dir(REGPRO_FORM_DOCUMENT_BASE_PATH)){
												mkdir(REGPRO_FORM_DOCUMENT_BASE_PATH, 0755, true);
											}
											// end

											// move uploded file to correct loaction under attachment folder
											if (move_uploaded_file($Files['form']['tmp_name'][$fkey][$fkey1][$fkey2], REGPRO_FORM_DOCUMENT_BASE_PATH."/".$newfilename)){
												// file uploded successfully
											} else {
												// ERROR
											}
											//end
										}else{
											// ERROR
										}
									}else{
										$msg = sprintf(JText::_('EVENTS_FORMS_EXTENSIONS_NOT_ALLOWED'),$file_extension);
										echo "<script> alert('".$msg."'); window.history.go(-1); </script>\n";
										exit();
									}
								}else{
									$msg = JText::_('EVENTS_FORMS_EXTENSIONS_MAXSIZE');
									echo "<script> alert('".$msg."'); window.history.go(-1); </script>\n";
									exit();
								}
							}else{
								$msg = JText::_('EVENTS_FORMS_EXTENSIONS_MAXSIZE');
								echo "<script> alert('".$msg."'); window.history.go(-1); </script>\n";
								exit();
							}
						}
					}
				}
			}
		}
		return $Files['form']['name'];
	}

	// get event info
	function getEventInfo($eventid = 0) {
		$database	= JFactory::getDBO();
		$query = "SELECT * FROM #__registrationpro_dates WHERE id = ".$eventid;
		$database->setQuery($query);
		$eventtitle = $database->loadObjectList();
		$eventtitle = $eventtitle[0];
		return $eventtitle;
	}

	// get event name
	function getEventName($eventid = 0) {
		$database	= JFactory::getDBO();
		$query = "SELECT titel FROM #__registrationpro_dates WHERE id = ".$eventid;
		$database->setQuery($query);
		$eventtitle = $database->loadResult();
		return $eventtitle;
	}

	// get event name
	function getCategoryName($catid = 0) {
		$database	= JFactory::getDBO();
		$query = "SELECT catname FROM #__registrationpro_categories WHERE id = ".$catid;
		$database->setQuery($query);
		$cattitle = $database->loadResult();
		return $cattitle;
	}

	// get fromid from event
	function getEventFormId($eventid = 0) {
		$database	= JFactory::getDBO();
		$query = "SELECT form_id FROM #__registrationpro_dates WHERE id = ".$eventid;
		$database->setQuery($query);
		$event_formid = $database->loadResult();
		return $event_formid;
	}

	// get ticket name
	function getTicketName($id = 0) {
		$database	= JFactory::getDBO();
		$query = "SELECT product_name FROM #__registrationpro_payment WHERE id = ".$id;
		$database->setQuery($query);
		$ticket_title = $database->loadResult();
		return $ticket_title;
	}

	// get all category for search form
	function getAllCategory() {
		$database	= JFactory::getDBO();
		$query = "SELECT id AS value, catname AS text FROM #__registrationpro_categories WHERE publishedcat = 1 ORDER BY ordering";
		$database->setQuery($query);
		return $database->loadObjectList();
	}

	// get all locations
	function getAllLocations() {
		$database	= JFactory::getDBO();
		$query = "SELECT id AS value, club AS text FROM #__registrationpro_locate WHERE publishedloc = 1 ORDER BY club";
		$database->setQuery($query);
		return $database->loadObjectList();
	}

	// get all locations
	function getAllForms() {
		$database	= JFactory::getDBO();
		$query = "SELECT id AS value, title AS text FROM #__registrationpro_forms WHERE published = 1 ORDER BY title";
		$database->setQuery($query);
		return $database->loadObjectList();
	}

	// get all joomla user groups
	function getAllUserGroups() {
		$database	= JFactory::getDBO();
		$query = "SELECT id AS value, title AS text FROM #__usergroups WHERE (id != 1 OR title != 'public') ORDER BY id";
		$database->setQuery($query);
		return $database->loadObjectList();
	}

	// Update confirmaion_email status of user
	function updateConfirmationEmailStatus($userid = 0) {
		$database	= JFactory::getDBO();
		$query = "UPDATE #__registrationpro_register SET confirmation_send = 1 WHERE rid in (".$userid.")";
		$database->setQuery($query);
		$database->query();
	}

	// Update ticket quantity after purchasing
	// Get sold ticket quantity to update the tickets table records
	function updateEventTicketQty($userid = 0) {
		$database	= JFactory::getDBO();
		$query = "SELECT DISTINCT(p_id), quantity FROM #__registrationpro_transactions WHERE reg_id in (".$userid.") ";
		$database->setQuery($query);
		$tickets_data = $database->loadObjectList();
		
		foreach($tickets_data as $ticket_data) {
			$query = "UPDATE #__registrationpro_payment SET product_quantity_sold = product_quantity_sold +".$ticket_data->quantity." WHERE id = ".$ticket_data->p_id;
			$database->setQuery($query);
			$database->query();
		}
	}

	// get paymment methods
	function getPaymentMethods() {
		$plugin_handler = new regProPlugins;

		// get all active payment methods
		$allpayment_methods = $plugin_handler->payment_plugins;

		foreach($allpayment_methods as $key=>$value) {
			$pluginParams = new JRegistry;
			$pluginParams->loadString($allpayment_methods[$key]->params);
			@$payment_methods[$key]->text = $pluginParams->get($allpayment_methods[$key]->name.'_label','');
			$payment_methods[$key]->value = $allpayment_methods[$key]->name;
		}
		return $payment_methods;
	}

	// create random key
	function str_makerand ($minlength, $maxlength, $useupper, $usespecial, $usenumbers) {
	    $charset = "abcdefghijklmnopqrstuvwxyz";
	    if ($useupper)   $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    if ($usenumbers) $charset .= "0123456789";
	    if ($usespecial) $charset .= "~@#$%^*()_+-={}|][";   // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";

	    if ($minlength > $maxlength) {
			$length = mt_rand ($maxlength, $minlength);
		} else {
		    $length = mt_rand ($minlength, $maxlength);
		}

	    for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
	    return $key;
	}

	// show payment methods select box
	function list_payment_methods() {
		global $mosConfig_live_site;

		$plugin_handler = new regProPlugins;

		// get all active payment methods
		$all_payment_methods = $plugin_handler->payment_plugins;
		foreach($all_payment_methods as $key=>$value)
		{
			// filtering those payment methods which is configured properly
			if($all_payment_methods[$key]->value != ""){
				$payment_methods[$key] = $all_payment_methods[$key];
			}
			// end
		}

		if(is_array($payment_methods) && count($payment_methods)>0){
			?>

			<script language="javascript">
				 function onchange_payment(selpayment)
				{
					if(selpayment.value == "offline"){
						document.getElementById("displayofflinedetails").style.display = "";
					}else{
						document.getElementById("displayofflinedetails").style.display = "none";
					}
				}
			</script>

			<tr>  <td colspan="3" height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td> </tr>
			<tr>
				<td colspan="3" class="regpro_outline" id="regpro_outline">
					<table border="0" cellpadding="3" cellspacing="0" width="100%">

						<tr> <td class="regpro_sectiontableheader" style="text-align:center" colspan="2"> <?php echo JText::_('EVENT_CART_PAYMENT_HEADING'); ?> </td> </tr>

						<tr>
							<td width="15%"><b><?php echo JText::_('PAYMENT_OPTIONS'); ?><b/></td>
							<td>
								<select name="selPaymentOption" alt="select" emsg="Please select the payment option." onchange="return onchange_payment(this);" class="textarea">
								<option value="0"><?php echo JText::_('EVENTS_SELECT_PAYMENT_OPTION'); ?></option>
								<?php
									if(is_array($payment_methods) && count($payment_methods)>0){
										foreach($payment_methods as $key=>$value)
										{
								?>
										<option value="<?php echo $key; ?>"><?php echo ucfirst($key);?></option>
								<?php
										}
									}
								?>
								</select>
							</td>
						</tr>
						<!-- display the offline deatils -->
						<tr id="displayofflinedetails" style="display:none">
							<td colspan="2">
								<?php
									echo JText::_('EVENTS_OFFLINE_PAYMENT_INSTRUCTION'),"<br /><br />";
									// check if offline payment values
										if(is_array($payment_methods) && count($payment_methods) > 0){
											foreach($payment_methods as $key=>$value)
											{
												if($key == strtolower("offline")){
													echo wordwrap($payment_methods[$key]->value,70,"<br />\n");
												}
											}
										}
									// end
								?>
							</td>
						</tr>
						<!-- end -->
					</table>
				</td>
			</tr>
		<?php
		}
	}


	/**
	 * Return the icon to move an item UP
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item up or a space
	 */
	 function regpro_orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'Move Up', $enabled = true, $extra = "") {
		$alt = JText::_($alt);

		if($extra){
			$extra = $extra.",";
		}

		$html = '&nbsp;';
		if (($i > 0 || ($i + $this->limitstart > 0)) && $condition)
		{
			if($enabled) {
				$html	= '<a href="#reorder" onclick="return '.$extra.' listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$alt.'">';
				$html	.= '   <img src="'.REGPRO_ADMIN_IMG_PATH.'/uparrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
				$html	.= '</a>';
			} else {
				$html	= '<img src="'.REGPRO_ADMIN_IMG_PATH.'/uparrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
			}
		}
		return $html;
	}

	/**
	 * Return the icon to move an item DOWN
	 *
	 * @access	public
	 * @param	int		$i The row index
	 * @param	int		$n The number of items in the list
	 * @param	boolean	$condition True to show the icon
	 * @param	string	$task The task to fire
	 * @param	string	$alt The image alternate text string
	 * @return	string	Either the icon to move an item down or a space
	 */
	 function regpro_orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = 'Move Down', $enabled = true, $extra = "")
	{
		$alt = JText::_($alt);

		$html = '&nbsp;';
		if (($i < $n -1 || $i + $this->limitstart < $this->total - 1) && $condition)
		{
			if($enabled) {
				$html	= '<a href="#reorder" onclick="return listItemTask(\'cb'.$i.'\',\''.$task.'\')" title="'.$alt.'">';
				$html	.= '  <img src="'.REGPRO_ADMIN_IMG_PATH.'/downarrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
				$html	.= '</a>';
			} else {
				$html	= '<img src="'.REGPRO_ADMIN_IMG_PATH.'/downarrow.png" width="16" height="16" border="0" alt="'.$alt.'" />';
			}
		}

		return $html;
	}

	  function checkUserAccount()
	{
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();

		$user	= JFactory::getUser();

		if($user->id){

			$database	= JFactory::getDBO();

			$config_groups = unserialize($regpro_config['user_groups']);

			$checkUserid = 0;
			if(is_array($user->groups) && count($user->groups) > 0 && is_array($config_groups)) {
				foreach($user->groups as $gkey => $gvalue)
				{
					if(is_array($config_groups)) {
						if(in_array($gvalue,$config_groups)){
							$checkUserid  = 1;
							break;
						}
					}
				}

				if($checkUserid == 0) {
					// check if admin add the particular user to manage event also
					$query = "SELECT count(*) FROM #__registrationpro_usersconfig WHERE published = 1 AND user_id =".$user->id;
					$database->setQuery($query);
					$checkUserid = $database->loadresult();
				}

			}else{
				// check user is able to manage there events
				$query = "SELECT count(*) FROM #__registrationpro_usersconfig WHERE published = 1 AND user_id =".$user->id;
				$database->setQuery($query);
				$checkUserid = $database->loadresult();
			}

			if($checkUserid){
				return true;
			} else return false;
		} else return false;
	}

	function checkUserModerator() {
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();

		if($regpro_config['event_moderation'] == 1){
			return true;
		} else return false;
	}

	// get the event manager name from main joomla users table by user id
	function getEventManagerName($userid) {
		$database	= JFactory::getDBO();

		// get sold ticket quantity to update the tickets table records
		$query = "SELECT name FROM #__users WHERE id =".$userid;
		$database->setQuery($query);
		$name = $database->loadresult();
		return $name;
	}

	// Get ticket price without tax
	function GetTicketPriceWithoutTax($ticketprice=0, $tax=0) {
		$price_without_tax = (100 * $ticketprice) / (100 + $tax);
		return $price_without_tax;
	}

	function checkColor($color) {
		$colorcheck=strtolower($color[3]);
		$rcolor = "#FFFFFF";
		if($colorcheck>6 || is_numeric($colorcheck)===false) $rcolor = "#000000";
		return $rcolor;
	}

	// Check info
	function getInfo() {
		$info = array();
        // Get installed version
		$registrationproHelper = new registrationproHelper;
        $info['version_installed'] = $registrationproHelper->getInstalledVersion();

        // Get latest version
		$info['version_latest'] = '?.?.?';
		$xml = @simplexml_load_file('http://joomlashowroom.com/media/versionsxml/extension_versions.xml');
		if (!$xml){} else {
			$i=0;
			foreach($xml->children() as $child) {
				if($i==0) {
					$string = $child->extension[3];
					$info['version_latest'] = $string->attributes()->{'version'};
				}
				$i++;
			}
			// Set the version status
			$info['version_status'] = version_compare($info['version_installed'], $info['version_latest']);
			$info['version_enabled'] = 1;
		}
        return $info;
    }

	function getInstalledVersion()  {
		$xmlFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'registrationpro.xml';
		$xml = @simplexml_load_file($xmlFile);
		if (!$xml){} else $version = $xml->version;
		return $version;
    }

	function getRemoteData($url) {
		$useragent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)";
		$data = false;

		// cURL
		if (extension_loaded('curl')) {
			// Init cURL
			$ch = @curl_init();

			// Set options
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

			// Set timeout
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);

			// Grab data
			$data = @curl_exec($ch);

			// Clean up
			@curl_close($ch);

			// Return data
			if ($data !== false) return $data;
		}

		// fsockopen
		if ( function_exists('fsockopen')) {
			$errno = 0;
			$errstr = '';

			$url_info = parse_url($url);
			if($url_info['host'] == 'localhost') $url_info['host'] = '127.0.0.1';

			// Set timeout
			$fsock = @fsockopen($url_info['scheme'].'://'.$url_info['host'], 80, $errno, $errstr, 5);

			if ($fsock) {
				@fputs($fsock, 'GET '.$url_info['path'].(!empty($url_info['query']) ? '?'.$url_info['query'] : '').' HTTP/1.1'."\r\n");
				@fputs($fsock, 'HOST: '.$url_info['host']."\r\n");
				@fputs($fsock, "User-Agent: ".$useragent."\n");
				@fputs($fsock, 'Connection: close'."\r\n\r\n");

				// Set timeout
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);

				$data = '';
				$passed_header = false;
				while (!@feof($fsock)) {
					if ($passed_header) {
						$data .= @fread($fsock, 1024);
					} else {
						if (@fgets($fsock, 1024) == "\r\n")
							$passed_header = true;
					}
				}
				@fclose($fsock);
				if ($data !== false) return $data;
			}
		}

		// fopen
		if ( function_exists('fopen') && ini_get('allow_url_fopen')) {
			// Set timeout
			if (ini_get('default_socket_timeout') < 5) {
				ini_set('default_socket_timeout', 5);
			}
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			@ini_set('user_agent',$useragent);

			$url = str_replace('://localhost', '://127.0.0.1', $url);

			$handle = @fopen ($url, 'r');

			if ($handle) {
				$data = '';
				while (!feof($handle)) $data .= @fread($handle, 8192);
				@fclose($handle);
				if ($data !== false) return $data;
			}
		}

		// file_get_contents
		if( function_exists('file_get_contents') && ini_get('allow_url_fopen')) {
			$url = str_replace('://localhost', '://127.0.0.1', $url);
			@ini_set('user_agent',$useragent);
			$data = @file_get_contents($url);
			if ($data !== false) return $data;
		}

		return $data;
	}

	function Invoicepdf($text, $user) {
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/config/lang/eng.php');
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/tcpdf.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// define barcode style
		$style = array(
			'position' => 'L',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => true,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => false,
			'font' => 'helvetica',
			'fontsize' => 8,
			'stretchtext' => 4
		);

		// set document information
		$pdf->SetCreator(JText::_('INVOICE_CREATEOR'));
		$pdf->SetAuthor(JText::_('INVOICE_AUTHOR'));
		$pdf->SetTitle(ucfirst($user['eventtitle'])." (".$user['eventstart']." - ".$user['eventend']." )" );
		$pdf->SetSubject(JText::_('INVOICE_SUBJECT'));
		$pdf->SetKeywords(JText::_('INVOICE_KEYWORDS'));

		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		//echo "<pre>";print_r($user);exit;
		
		// set default header data
		$pdf_logo = "/images/regpro/system/nopdfimage_720x240.jpg"; // 720x240pix
		if(($user['pdfimage']) && ($user['pdfimage'] == 1)) {
			if(file_exists('images/regpro/events/pdfevent_'.$user['eventid'].'.jpg'))
				$pdf_logo = '/images/regpro/events/pdfevent_'.$user['eventid'].'.jpg';
		}
		
		$pdf_title = ucwords($user['eventtitle']);
		$pdf_description = "\n".$user['eventstart'].JText::_('INVOICE_DATE_SEPARATOR').$user['eventend']."\n";
		$pdf_description .=  $user['location'].", ".$user['street'].", ".$user['city'].", (".$user['country'].") ".$user['zip'];

		$pdf->SetHeaderData($pdf_logo, PDF_HEADER_LOGO_WIDTH, $pdf_title, $pdf_description);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 10, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();
		$html = $text;

		// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
		$pdf->write1DBarcode($user['registrationid'], 'C39', '', '', '', 18, 0.4, $style, 'N');

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output(REGPRO_MEDIA_INVOICE_PDF_BASE_PATH.DS.'receipt_'.$user['registrationid'].'.pdf', 'F');
	}

	// MailChimp methods

	function getMailChimpList($selected){
		jimport( 'joomla.filesystem.file' );
		$mainframe =  JFactory::getApplication();
		if(!JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'regpro_mailchimp'.DS.'libraries'.DS.'MCAPI.class.php')) {
		    $mainframe->redirect('index.php',JText::_('JM_FILE_MISSING'),'error');
		} else {
		    require_once( JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'regpro_mailchimp'.DS.'libraries'.DS.'MCAPI.class.php');
		    require_once(JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'regpro_mailchimp'.DS.'libraries'.DS.'MCauth.php' );

			$plugin = JPluginHelper::getPlugin('user', 'regpro_mailchimp');
			$params = new JRegistry($plugin->params);

			$MCapi = $params->get('api_key','0');

		    $MCauth = new MCauth();

		    $api = new joomlamailerMCAPI($MCapi);
		    $lists = $api->lists();
		    $key = 'id';
		    $val = 'name';
		    $options[] = array($key=>'',$val=>JText::_('JM_PLEASE_SELECT_A_LIST'));
		    foreach ($lists as $list){
			$options[]=array($key=>$list[$key],$val=>$list[$val]);
		    }
			$attribs="";

		    $name = 'mailchimp_list';
		    if($options) $content =  JHtml::_('select.genericlist', $options, $name,$attribs, $key, $val, $selected, $this->id);

		    return $content;
		}
	}

	function subsribeUser($fname,$lname,$email,$list_id){
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.html.parameter' );
		$mainframe =  JFactory::getApplication();
		require_once( JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'regpro_mailchimp'.DS.'libraries'.DS.'MCAPI.class.php');

		$plugin = JPluginHelper::getPlugin('user', 'regpro_mailchimp');
		$params = new JRegistry($plugin->params);
		// get API Key
		$MCapi = $params->get('api_key','0');
		$api = new joomlamailerMCAPI($MCapi);
		$merge_vars = Array( 'FNAME' => $fname, 'LNAME' => $lname );
		// call API method to subscribe
		$lists = $api->listSubscribe($list_id, $email, $merge_vars, $email_type='html', $double_optin=true, $update_existing=false, $replace_interests=true, $send_welcome=false);

		return true;
	}
}

?>