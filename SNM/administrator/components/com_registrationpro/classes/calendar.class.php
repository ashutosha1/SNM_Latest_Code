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

class RegPro_Calendar
{
    function RegPro_Calendar(){}

    /*
        Get the array of strings used to label the days of the week. This array contains seven
        elements, one for each day of the week. The first entry in this array represents Sunday.
    */
    function getDayNames() {
        return $this->dayNames;
    }

    /*
        Set the array of strings used to label the days of the week. This array must contain seven
        elements, one for each day of the week. The first entry in this array represents Sunday.
    */
    function setDayNames($names) {
        $this->dayNames = $names;
    }

    /*
        Get the array of strings used to label the months of the year. This array contains twelve
        elements, one for each month of the year. The first entry in this array represents January.
    */
    function getMonthNames() {
        return $this->monthNames;
    }

    /*
        Set the array of strings used to label the months of the year. This array must contain twelve
        elements, one for each month of the year. The first entry in this array represents January.
    */
    function setMonthNames($names)
    {
        $this->monthNames = $names;
    }



    /*
        Gets the start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
      function getStartDay()
    {
        return $this->startDay;
    }

    /*
        Sets the start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
    function setStartDay($day)
    {
        $this->startDay = $day;
    }


    /*
        Gets the start month of the year. This is the month that appears first in the year
        view. January = 1.
    */
    function getStartMonth()
    {
        return $this->startMonth;
    }

    /*
        Sets the start month of the year. This is the month that appears first in the year
        view. January = 1.
    */
    function setStartMonth($month)
    {
        $this->startMonth = $month;
    }


    /*
        Return the URL to link to in order to display a calendar for a given month/year.
        You must override this method if you want to activate the "forward" and "back"
        feature of the calendar.

        Note: If you return an empty string from this function, no navigation link will
        be displayed. This is the default behaviour.

        If the calendar is being displayed in "year" view, $month will be set to zero.
    */
    function getCalendarLink($month, $year)
    {
        return "";
    }

    /*
        Return the URL to link to  for a given date.
        You must override this method if you want to activate the date linking
        feature of the calendar.

        Note: If you return an empty string from this function, no navigation link will
        be displayed. This is the default behaviour.
    */
    function getDateLink($day, $month, $year)
    {
        return "";
    }


    /*
        Return the HTML for the current month
    */
    function getCurrentMonthView($start_year, $end_year)
    {
        $d = getdate(time());
        return $this->getMonthView($d["mon"], $d["year"],$start_year, $end_year);
    }


    /*
        Return the HTML for the current year
    */
    function getCurrentYearView()
    {
        $d = getdate(time());
        return $this->getYearView($d["year"]);
    }


    /*
        Return the HTML for a specified month
    */
    function getMonthView($month, $year ,$s_year,$e_year)
    {

        return $this->getMonthHTML($month, $year, $s_year,$e_year);
    }


    /*
        Return the HTML for a specified year
    */
    function getYearView($year)
    {
        return $this->getYearHTML($year);
    }

    function getDaysInMonth($month, $year){
        if ($month < 1 || $month > 12) return 0;

        $d = $this->daysInMonth[$month - 1];

        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...

            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }

        return $d;
    }


    /*
        Generate the HTML for a given month
    */
    function getMonthHTML($m, $y, $s_year, $e_year, $showYear = 1)
    {
        $s = "";

		$legends = array();

        $a = $this->adjustDate($m, $y);
        $month = $a[0];
        $year = $a[1];
		//$month_name = $a[2];

    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));

    	$first = $date["wday"];
    	$monthName = $this->monthNames[$month - 1];

    	$prev = $this->adjustDate($month - 1, $year);
    	$next = $this->adjustDate($month + 1, $year);

    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    	}
    	else
    	{
    	    $prevMonth = "";
    	    $nextMonth = "";
    	}


		$arr_month = $this->monthNames;
		$arr_year = array();
		//$arr_month = array(1=>"January", 2=>"February", 3=>"March", 4=>"April", 5=>"May", 6=>"June", 7=>"July", 8=>"August", 9=>"September", 10=>"October", 11=>"November", 12=>"December");
		if($s_year!="" && $e_year!=""){
		    for($i=$s_year; $i<=$e_year;$i++){
				$arr_year[$i] = $i;
			}
		}else{
			$arr_year  = array(2009=>2009, 2010=>2010, 2011=>2011, 2012=>2012, 2013=>2013, 2014=>2014);
		}

    	$header = $monthName . (($showYear > 0) ? " " . $year : "");

		if(trim($this->regproConfig['introtext']) != ""){
			$s .= "<table width='100%' border='0' cellpadding='2' cellspacing='0'> <tr><td>".stripslashes($this->regproConfig['introtext'])."</td></tr></table>";
		}

		$s .= "<div class='regpro_calendar' id='regpro_calendar' width='100%'>";

		$s .= "<table border='0' width='100%' class=\"regpro-table-noborder\" id=\"regpro_calendarMonthHeaderss\"> \n";
    	$s .= "<tr>\n";

		$s .= "<td width=\"25%\" class=\"regpro_calendar_premonth\">";
			if($prevMonth != ""){
				$s .= "<div class='btn-toolbar'>
						<div class='btn-group'>
							<a class='btn' href='".$prevMonth."'><i class='icon-chevron-left'></i>&nbsp;".$prev[1]." ".$prev[2]."</a>
						</div>
						</div>";
			}else{
				$s .= "&nbsp;";
			}
		$s .= "</td>\n";

		$s .= "<td align=\"center\" valign=\"middle\">\n";
		if($this->layout == "category") {
			$monthchange = JRoute::_("index.php?option=com_registrationpro&view=calendar&layout=category&categoryid=".$this->catid."&Itemid=".$this->Itemid."&listview=2&month=monthnumber&year=".$year);
		}else{
			$monthchange = JRoute::_("index.php?option=com_registrationpro&view=calendar&Itemid=".$this->Itemid."&listview=2&month=monthnumber&year=".$year."&catid=".$this->catid);
		}

		$s .= "<select class='regpro_calendar_months' name='cal_month' onchange='cal_month_change(this.value,\"{$monthchange}\");'>";
				foreach($arr_month as $mkey=>$mvalue){
					if($month == $mkey)
						$mselected = "Selected";
					else
						$mselected = "";
		$s .=		"<option value='$mkey' $mselected>".$mvalue."</option>";
				}
		$s .= "</select> &nbsp;&nbsp;";

		if($this->layout == "category") {
			$yearchange = JRoute::_("index.php?option=com_registrationpro&view=calendar&layout=category&categoryid=".$this->catid."&Itemid=".$this->Itemid."&listview=2&month=".$month."&year=yearnumber");
		}else{
			$yearchange = JRoute::_("index.php?option=com_registrationpro&view=calendar&Itemid=".$this->Itemid."&listview=2&month=".$month."&year=yearnumber&catid=".$this->catid);
		}

		$s .= "<select class='regpro_calendar_years' name='cal_year' onchange='cal_year_change(this.value,\"{$yearchange}\");'>";
				foreach($arr_year as $ykey=>$yvalue){
					if($year == $ykey)
						$yselected = "selected";
					else
						$yselected = "";
		$s .=		"<option value='$ykey' $yselected>".$yvalue."</option>";
				}
		$s .= "</select> &nbsp;&nbsp;";

		if($this->regproConfig['calendar_category_filter'] == 1 &$this->layout != "category"){ // if category filter is enabled
			$s .= $this->getCategories($month,$year);
		}
		$s .= "</td>\n";
		$s .= "<td width=\"25%\" class=\"regpro_calendar_nextmonth\">";
			if($nextMonth != ""){
				$s .= "<div class='btn-toolbar'>
						<div class='btn-group'>
							<a class='btn' href='".$nextMonth."'>".$next[1]." ".$next[2]."&nbsp; <i class='icon-chevron-right'></i></a>
						</div>
						</div>";
			}else{
				$s .= "&nbsp;";
			}
		$s .= "</td>\n";
    	$s .= "</tr>\n";
    	$s .= "</table>\n";
		$s .= "<div><img src='".$this->images_path."/blank.png' border='0' height='3px'></div>\n";

		$s .= "<table border='0' width='100%' cellpadding='0' cellspacing='0' class='regpro-table-bordered'> \n";
		$s .= "<tr>\n";
    	$s .= "<th align=\"center\" valign=\"top\">". $this->dayNames[($this->startDay)%7] . "</td>\n";
    	$s .= "<th align=\"center\" valign=\"top\">" . $this->dayNames[($this->startDay+1)%7] . "</td>\n";
    	$s .= "<th align=\"center\" valign=\"top\">" . $this->dayNames[($this->startDay+2)%7] . "</td>\n";
    	$s .= "<th align=\"center\" valign=\"top\">" . $this->dayNames[($this->startDay+3)%7] . "</td>\n";
    	$s .= "<th align=\"center\" valign=\"top\">" . $this->dayNames[($this->startDay+4)%7] . "</td>\n";
    	$s .= "<th align=\"center\" valign=\"top\">" . $this->dayNames[($this->startDay+5)%7] . "</td>\n";
    	$s .= "<th align=\"center\" valign=\"top\">" . $this->dayNames[($this->startDay+6)%7] . "</td>\n";
    	$s .= "</tr>\n";

    	// We need to work out what date to start at so that the first appears in the correct column
    	$d = $this->startDay + 1 - $first;
    	while ($d > 1) $d -= 7;

		$registrationproHelper = new registrationproHelper;
		$today['mday']  = $registrationproHelper->getCurrent_date('%d');
	   	$today["year"]	= $registrationproHelper->getCurrent_date('%Y');
	   	$today["mon"] 	= $registrationproHelper->getCurrent_date('%m');

    	while ($d <= $daysInMonth)
    	{
    	    $s .= "<tr>\n";

    	    for ($i = 0; $i < 7; $i++)
    	    {
        	    $class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "regpro_calendarToday" : "regpro_calendarDay regproblack";
    	        $s .= "<td class=\"$class\" onmouseover=\"this.className = 'regpro_calenderday_highlight';\" onmouseout=\"this.className = '$class';\">";
    	        if ($d > 0 && $d <= $daysInMonth)
    	        {
    	            $link = $this->getDateLink($d, $month, $year);

					// add category info for legends
					$legends = $this->getLinkCategories($day, $month, $year);

					// create event details link
					if(is_array($link)){
						$s .= $d;
						$img_cnt = 0;
						$mgt = "-15px";
						foreach($link as $key=>$value) {
							if($link[$key]['background'] == "" || $link[$key]['background'] == "#" || $link[$key]['background'] == "#FFFFFF") {
								$link[$key]['background'] = "#FFFFFF";
							}else $link[$key]['color'] = "#FFFFFF";

							// set link color on basis of background color light/dark
							$link[$key]['color'] = $registrationproHelper->checkColor($link[$key]['background']);
							$link[$key]['detail'] = str_replace("'","&rsquo;",$link[$key]['detail']);

							if($this->regproConfig['show_poster_cal'] == 1) {
								$s .= "<div style='margin:0px;padding:0px;text-align:center;margin-top:".$mgt.";'>";
								if($img_cnt == 0) { $img_cnt++; $mgt = "0px";}
								$s .= "<a href='".$link[$key]['link']."' class='Tips4' title='".$link[$key]['detail']."'><img src=\"".$link[$key]['poster']."\" style=\"max-width:50px;margin:0px;padding:0px;margin-bottom:4px;\"></a>";
								if($link[$key]['status']) $s .= "&nbsp;<span class=\"editlinktip hasTip\" title='".$link[$key]['status_title']."'>".$link[$key]['status']."</span>";
								$s .= "</div>";
							} else {
								$s .= "<div style='margin-left:2px;margin-right:4px;padding-left:4px;padding-bottom:4px;text-align:left;'>";
								$s .= "<a href='".$link[$key]['link']."' class='Tips4 label' title='".$link[$key]['detail']."' style='background-color :".$link[$key]['background']."; color:".$link[$key]['color'].";'>".$link[$key]['title']."</a>";
								if($link[$key]['status']) $s .= "&nbsp;<span class=\"editlinktip hasTip\" title='".$link[$key]['status_title']."'>".$link[$key]['status']."</span>";
								$s .= "</div>";
							}
							//$s .= "<img src='".$this->images_path."/blank.png' border='0' height='3px'>";
						}
					}else{
						$s .= $d;
					}
    	        }
    	        else
    	        {
    	            $s .= "&nbsp;";
    	        }
      	        $s .= "</td>\n";
        	    $d++;
    	    }
    	    $s .= "</tr>\n";
    	}

    	$s .= "</table>\n";

		// Legend vode
		if(is_array($legends) && count($legends) > 0){


			$s .=	"<div class='regpro_calendar_legends'>";

			$s .= "<div class='regpro_calendar_legends_title'><p><span class=\"label\">".JText::_('EVENTS_CALENDAR_LEGENDS')."</span><i class=\"icon-hand-down\"></i></p>   </div>";
			foreach($legends as $lkey => $lvalue)
			{
				// set text color black if backgroud is white.
				$fcolor = "";
				if($lvalue->background == "" || $lvalue->background == "#") {
					$lvalue->background = "FFFFFF";
				}

				// set link color on basis of background color light/dark
				$fcolor = $registrationproHelper->checkColor($lvalue->background);

				$s .= "<span class='label' style='background-color : #".$lvalue->background."; color : ".$fcolor."'>".$lvalue->catname."</span> ";
			}
			$s .= "</div>";
		}
		$s .= "</div>";
    	return $s;
	}

    /* Generate the HTML for a given year */
    function getYearHTML($year)
    {
        $s = "";
    	$prev = $this->getCalendarLink(0, $year - 1);
    	$next = $this->getCalendarLink(0, $year + 1);

        $s .= "<table class=\"regpro_calendar\" border='0'>\n";
        $s .= "<tr>";
    	$s .= "<td align=\"center\" valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\"><img src='".$this->images_path."/calendar_previous.png' border='0' /></a>")  . "</td>\n";
        $s .= "<td class=\"calendarHeader\" valign=\"top\" align=\"center\">" . (($this->startMonth > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";
    	$s .= "<td align=\"center\" valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(0 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(1 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(2 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(3 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(4 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(5 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(6 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(7 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(8 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(9 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(10 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "<td class=\"regpro_calendar\" valign=\"top\">" . $this->getMonthHTML(11 + $this->startMonth, $year, 0) ."</td>\n";
        $s .= "</tr>\n";
        $s .= "</table>\n";

        return $s;
    }

    /*
        Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
        e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
    */
    function adjustDate($month, $year)
    {
        $a = array();
        $a[0] = $month;
        $a[1] = $year;
		$a[2] = $this->monthNames[$month];

        while ($a[0] > 12)
        {
            $a[0] -= 12;
            $a[1]++;
			$a[2] = $this->monthNames[$a[0]];
        }

        while ($a[0] <= 0)
        {
            $a[0] += 12;
            $a[1]--;
			$a[2] = $this->monthNames[$a[0]];
        }

        return $a;
    }

    /*
        The start day of the week. This is the day that appears in the first column
        of the calendar. Sunday = 0.
    */
    var $startDay = 0;
    var $startMonth = 1;
	var $dayNames = array();
	var $monthNames = array();
    var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
}

// Now define the Calendar class
class WebCamCalendar extends RegPro_Calendar {

	function WebCamCalendar($regproConfig, $categoryid = 0, $layout = ""){
		global $Itemid, $mainframe;

		$this->website_url 			 = REGPRO_SITE_URL;
		$this->website_absoulte_path = REGPRO_SITE_BASE;
		$this->component_path		 = REGPRO_BASE_URL;
		$this->images_path 			 = REGPRO_IMG_PATH;
		$this->Itemid 			 	 = $Itemid;
		$this->regproConfig			 = $regproConfig;
		$this->catid				 = $categoryid;
		$this->layout				 = $layout;
		$this->startDay				 = $this->regproConfig['calendar_weekday'];

		if(!$this->catid) {
			$this->catid	= $mainframe->getUserStateFromRequest( $option.'calender_catid', 'catid', 0, 'int' );
			if($this->regproConfig['calendar_category_filter'] == 0) $this->catid = 0;
		}

		// assign weekdays
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_SUNDAY');
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_MONDAY');
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_TUESDAY');
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_WEDNESDAY');
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_THURSDAY');
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_FRIDAY');
		$this->dayNames[] = JText::_('REGPRO_CALENDAR_SATURDAY');

		// assign months
		$this->monthNames[1] = JText::_('REGPRO_CALENDAR_JANUARY');
		$this->monthNames[2] = JText::_('REGPRO_CALENDAR_FEBRUARY');
		$this->monthNames[3] = JText::_('REGPRO_CALENDAR_MARCH');
		$this->monthNames[4] = JText::_('REGPRO_CALENDAR_APRIL');
		$this->monthNames[5] = JText::_('REGPRO_CALENDAR_MAY');
		$this->monthNames[6] = JText::_('REGPRO_CALENDAR_JUNE');
		$this->monthNames[7] = JText::_('REGPRO_CALENDAR_JULY');
		$this->monthNames[8] = JText::_('REGPRO_CALENDAR_AUGUST');
		$this->monthNames[9] = JText::_('REGPRO_CALENDAR_SEPTEMBER');
		$this->monthNames[10] = JText::_('REGPRO_CALENDAR_OCTOBER');
		$this->monthNames[11] = JText::_('REGPRO_CALENDAR_NOVEMBER');
		$this->monthNames[12] = JText::_('REGPRO_CALENDAR_DECEMBER');
		//echo "<pre>"; print_r( $this->dayNames); exit;
	}

	function getCalendarLink($month, $year) {
		$layout = JRequest::getCmd('layout');
		if($layout == "category") {
			$link = JRoute::_("index.php?option=com_registrationpro&view=calendar&layout=category&Itemid=".$this->Itemid."&categoryid=".$this->catid."&listview=2&month=$month&year=$year");
		} else $link = JRoute::_("index.php?option=com_registrationpro&view=calendar&Itemid=".$this->Itemid."&listview=2&month=$month&year=$year");
		return $link;
	}

	function getLinkCategories($day, $month, $year) {
		$database	= JFactory::getDBO();

		$my = JFactory::getUser(); // get user details
		$gid = (int) $my->get('aid', 0);

		// Filter by access level.
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$where[] 	= 'c.access IN ('.$groups.')';

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();

		if($regpro_config['eventlistordering'] == 2){
			$Default_orderby = ' c.ordering, a.dates, a.times ';
		}elseif($regpro_config['eventlistordering'] == 3){
			$Default_orderby = ' c.ordering, a.enddates, a.endtimes ';
		}elseif($regpro_config['eventlistordering'] == 4){
			$Default_orderby = ' c.ordering, a.titel ';
		}else{
			$Default_orderby = ' c.ordering, a.ordering ';
		}

		$query = "SELECT DISTINCT(c.id) AS catid, c.catname, c.background"
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\n WHERE a.published = 1 AND a.moderating_status = 1 AND c.access IN (".$groups.") AND a.viewaccess IN (".$groups.")";

		if($this->regproConfig['show_all_dates_in_calendar'] != 1) {
			$query .= "\n AND YEAR(a.dates) = ".$year." AND MONTH(a.dates) = ".$month;
		}

		if(!empty($this->catid)) $query .= "\n AND c.id = ".$this->catid;
		$query .= "\n ORDER BY ".$Default_orderby;
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		return $rows;
	}

	function getDateLink($day, $month, $year) {
		$database = JFactory::getDBO(); 

		$my = JFactory::getUser(); // get user details
		$gid = (int) $my->get('aid', 0);

		// Filter by access level.
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$where[] 	= 'c.access IN ('.$groups.')';

		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$registrationproHelper = new registrationproHelper;
		if($regpro_config['eventlistordering'] == 2){
			$Default_orderby = ' c.ordering, a.dates, a.times ';
		}elseif($regpro_config['eventlistordering'] == 3){
			$Default_orderby = ' c.ordering, a.enddates, a.endtimes ';
		}elseif($regpro_config['eventlistordering'] == 4){
			$Default_orderby = ' c.ordering, a.titel ';
		}else{
			$Default_orderby = ' c.ordering, a.ordering ';
		}

		$query = "SELECT a.id, a.parent_id, a.user_id, a.image AS poster, a.dates, a.enddates, a.shortdescription, a.max_attendance, a.times, a.endtimes, a.titel, a.locid, a.status,a.shw_attendees, a.registra, l.club, l.url, l.street, l.plz, l.city, l.country, l.locdescription, c.catname, c.id as catid, c.background"
				. "\nFROM #__registrationpro_dates AS a"
				. "\nLEFT JOIN #__registrationpro_locate AS l ON l.id = a.locid"
				. "\nLEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid"
				. "\n WHERE a.published = 1 AND a.moderating_status = 1 AND c.access IN (".$groups.") AND a.viewaccess IN (".$groups.")";

		if($this->regproConfig['show_all_dates_in_calendar'] != 1) {
			$query .= "\n AND YEAR(a.dates) = ".$year." AND MONTH(a.dates) = ".$month;
		}
		if(!empty($this->catid)){
			$query .= "\n AND c.id = ".$this->catid;
		}

		$query .= "\n ORDER BY ".$Default_orderby;

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		if(is_array($rows)){

			$currDate = mktime(0,0,0,$month,$day,$year) ;

			foreach($rows as $key=>$value) {
				$rows[$key]->sold = $this->checkEventStatus($rows[$key]); // check event registration enalbe or not and event registration dates

				$stardatetime = "";
				$enddatetime = "";

				if($this->regproConfig['show_all_dates_in_calendar'] == 1){ /** Show events from start to end dates ***/

					$startdateArr  = explode("-",$rows[$key]->dates);
					$startdate = mktime(0,0,0,$startdateArr[1],$startdateArr[2],$startdateArr[0]) ;

					$enddateArr  = explode("-",$rows[$key]->enddates);
					$enddate = mktime(0,0,0,$enddateArr[1],$enddateArr[2],$enddateArr[0]) ;

					if($currDate >= $startdate && $currDate <= $enddate){

						$datelink[$key]['background'] = "#".$rows[$key]->background;

						$datelink[$key]['link'] =  JRoute::_("index.php?option=com_registrationpro&view=event&did=".$rows[$key]->id."&Itemid=".$this->Itemid."&shw_attendees=".$rows[$key]->shw_attendees);
						$datelink[$key]['title'] = substr($rows[$key]->titel,0,45)."...";
						$registrationproHelper = new registrationproHelper;
						if($this->regproConfig['showeventdates'] == 1){
							$stardatetime = $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $rows[$key]->dates)."&nbsp;";
						}

						if($this->regproConfig['showeventtimes'] == 1){
							$stardatetime .= $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $rows[$key]->times);
						}

						if($this->regproConfig['showeventdates'] == 1){
							$enddatetime = $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $rows[$key]->enddates)."&nbsp;";
						}

						if($this->regproConfig['showeventtimes'] == 1){
							$enddatetime .= $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $rows[$key]->endtimes);
						}

						$location = $rows[$key]->club.", ".$rows[$key]->city;

						$datelink[$key]['detail']  =  ucfirst($rows[$key]->titel) ."::";
						$datelink[$key]['detail'] .= "<table width= \"100%\"cellpadding=\"0\" cellspacing=\"0\">";

						if($rows[$key]->enddates!=$rows[$key]->dates){

							if(trim($stardatetime) != ''){
							$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td width=\"25%\">".JText::_('EVENTS_CALENDAR_START_DATETIME')."</td><td> :&nbsp;</td><td>".$stardatetime."</td></tr>";
							}
							if(trim($enddatetime) != ''){
							$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td>".JText::_('EVENTS_CALENDAR_END_DATETIME')."</td><td> :&nbsp;</td><td>".$enddatetime."</td></tr>";
							}
						}else{
							if(trim($stardatetime) != ""){
								$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td width=\"25%\">".JText::_('EVENTS_CALENDAR_DATE')."</td><td> :&nbsp;</td><td>".$stardatetime."</td></tr>";
							}
						}

						$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td>".JText::_('EVENTS_CALENDAR_EVENT_SHORT_DESC')."</td><td> :&nbsp;</td><td>".ucfirst(substr($rows[$key]->shortdescription,0,400))."...</td></tr>";
						$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td>".JText::_('EVENTS_CALENDAR_EVENT_LOCATION')."</td><td> :&nbsp;</td><td>".$location."</td></tr>";
						
						if($this->regproConfig['show_poster'] == 1) {
							$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td colspan=10 align=center>";

							include_once 'administrator/components/com_registrationpro/helpers/tools.php';
							$imgPrefixSystem = JURI::root() . "images/regpro/system/";
							$imgPrefixEvents = JURI::root() . "images/regpro/events/";
							$imgCurr = getImageName($rows[$key]->id, $rows[$key]->user_id);
							$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
							if($rows[$key]->poster !== '0') {
								$tmpName = $imgPrefixEvents . $imgCurr;
								$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();
							}
							$datelink[$key]['poster'] = $imgName;
							$datelink[$key]['detail'] .= "<img id=\"event_img\" src=\"".$datelink[$key]['poster']."\" height=200 style=\"max-height:200px;margin-top:6px;margin-bottom:5px;\">";
							$datelink[$key]['detail'] .= "</td></tr>";
						}
						
						$datelink[$key]['detail'] .= "</table>";


						if($this->regproConfig['show_calendar_registration_flag'] == 1){
							if($rows[$key]->sold == 1){
								$datelink[$key]['status_title'] = JText::_('EVENTS_CALENDAR_STATUS_SOLD');
								$datelink[$key]['status'] = '(<span style="color:red">S</span>)';
							}elseif($rows[$key]->sold == 0){
								$datelink[$key]['status_title'] = JText::_('EVENTS_CALENDAR_STATUS_REGISTRAION_AVBL');
								$datelink[$key]['status'] = '(<span style="color:green">R</span>)';
							}else{
								$datelink[$key]['status_title'] = "";
								$datelink[$key]['status'] = "";
							}
						}
					}
				} else { /** Show events only on start date ***/
					$registrationproHelper = new registrationproHelper;
					$date_day = date("j",strtotime($rows[$key]->dates));
					$date_month = date("n",strtotime($rows[$key]->dates));
					$date_year = date("Y",strtotime($rows[$key]->dates));

					if($day == $date_day && $month == $date_month && $year == $date_year){
						$datelink[$key]['background'] = "#".$rows[$key]->background;
						$datelink[$key]['link'] =  JRoute::_("index.php?option=com_registrationpro&view=event&did=".$rows[$key]->id."&Itemid=".$this->Itemid."&shw_attendees=".$rows[$key]->shw_attendees);
						$datelink[$key]['title'] = substr($rows[$key]->titel,0,45)."...";

						if($this->regproConfig['showeventdates'] == 1) $stardatetime = $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $rows[$key]->dates)."&nbsp;";
						if($this->regproConfig['showeventtimes'] == 1) $stardatetime .= $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $rows[$key]->times);
						if($this->regproConfig['showeventdates'] == 1) $enddatetime	= $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $rows[$key]->enddates)."&nbsp;";
						if($this->regproConfig['showeventtimes'] == 1) $enddatetime .= $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $rows[$key]->endtimes);

						$location = $rows[$key]->club.", ".$rows[$key]->city;

						$datelink[$key]['detail'] = ucfirst($rows[$key]->titel) ."::";
						
						$datelink[$key]['detail'] .= "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">";
						if($rows[$key]->enddates!=$rows[$key]->dates){
							if(trim($stardatetime) != '') $datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td width=\"25%\">".JText::_('EVENTS_CALENDAR_START_DATETIME')."</td><td> :&nbsp;</td><td>".$stardatetime."</td></tr>";
							if(trim($enddatetime) != '')  $datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td>".JText::_('EVENTS_CALENDAR_END_DATETIME')."</td><td> :&nbsp;</td><td>".$enddatetime."</td></tr>";
						} else {
							if(trim($stardatetime) != "") $datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td width=\"25%\">".JText::_('EVENTS_CALENDAR_DATE')."</td><td> :&nbsp;</td><td>".$stardatetime."</td></tr>";
						}
						$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td>".JText::_('EVENTS_CALENDAR_EVENT_SHORT_DESC')."</td><td> :&nbsp;</td><td>".ucfirst(substr($rows[$key]->shortdescription,0,400))."...</td></tr>";
						$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td>".JText::_('EVENTS_CALENDAR_EVENT_LOCATION')."</td><td> :&nbsp;</td><td>".$location."</td></tr>";
						
						
						if($this->regproConfig['show_poster'] == 1) {
							$datelink[$key]['detail'] .= "<tr class=\"tooptip_detail\"><td colspan=10 align=center>";

							include_once 'administrator/components/com_registrationpro/helpers/tools.php';
							$imgPrefixSystem = JURI::root() . "images/regpro/system/";
							$imgPrefixEvents = JURI::root() . "images/regpro/events/";
							$imgCurr = getImageName($rows[$key]->id, $rows[$key]->user_id);
							$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
							if($rows[$key]->poster !== '0') {
								$tmpName = $imgPrefixEvents . $imgCurr;
								$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();
							}
							$datelink[$key]['poster'] = $imgName;
							$datelink[$key]['detail'] .= "<img id=\"event_img\" src=\"".$datelink[$key]['poster']."\" height=200 style=\"max-height:200px;margin-top:6px;margin-bottom:5px;\">";
							$datelink[$key]['detail'] .= "</td></tr>";
						}						
						
						$datelink[$key]['detail'] .= "</table>";
						
						if($this->regproConfig['show_calendar_registration_flag'] == 1){
							if($rows[$key]->sold == 1){
								$datelink[$key]['status_title'] = JText::_('EVENTS_CALENDAR_STATUS_SOLD');
								$datelink[$key]['status'] = '(<span style="color:red">S</span>)';
							}elseif($rows[$key]->sold == 0){
								$datelink[$key]['status_title'] = JText::_('EVENTS_CALENDAR_STATUS_REGISTRAION_AVBL');
								$datelink[$key]['status'] = '(<span style="color:green">R</span>)';
							}else{
								$datelink[$key]['status_title'] = "";
								$datelink[$key]['status'] = "";
							}
						}
					}
				}
			}
			return $datelink;
		}
	}

	function checkEventStatus($row) {

		$database	= JFactory::getDBO();
		$registrationproHelper = new registrationproHelper;
		if($row->registra){
			// check event registration date validations
			$current_date	= $registrationproHelper->getCurrent_date();
			$database->setQuery("SELECT regstart, regstarttimes,regstop, regstoptimes, regstop_type FROM #__registrationpro_dates WHERE id = ".$row->id);
			$reg = $database->loadRow();

			$event_regstartdate = $reg[0]." ".$reg[1];
			$event_regenddate 	= $reg[2]." ".$reg[3];

			//echo $reg_enddate;
			if($reg[0] != '0000-00-00' && $reg[2] != '0000-00-00'){
				if($current_date < $event_regstartdate || $current_date > $event_regenddate) {
					$event_status = 2;
				}
			} else $event_status = 2;

			if($event_status != 2) {
				// Get filled seats for Events
				$query = "SELECT count(*) as cnt FROM #__registrationpro_register WHERE active=1 AND rdid = ".$row->id;
				$database->setQuery($query);
				$registerdusers = $database->loadResult();
				if($registerdusers){
					$row->registered 	= $registerdusers;
					$row->avaliable 	= $row->max_attendance - $registerdusers;

				}else{
					$row->registered 	= 0;
					$row->avaliable  	= $row->max_attendance;
				}

				if($row->max_attendance <= 0){
					$event_status = 0;
				}elseif($row->registered >= $row->max_attendance) {
					$event_status = 1;
				} else $event_status = 0;
			}
		} else $event_status = 2;

		return $event_status;
	}

	function getCategories($month, $year){
		$database	= JFactory::getDBO();
		$categories	= array();

		// Filter by access level.
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$query = "SELECT * "
			. "\nFROM #__registrationpro_categories"
			. "\nWHERE publishedcat = 1 AND access IN (".$groups.")"
			. "\nORDER BY ordering";
		$database->setQuery( $query );
		$all_categories	= $database->loadObjectList();

		$categories 	= array_merge( $categories, $all_categories);
		$yearchange = JRoute::_("index.php?option=com_registrationpro&view=calendar&Itemid=".$this->Itemid."&listview=2&month=".$month."&year=".$year."&catid=categoryid");
		$ctselect = '<select name="catsid" class="regpro_calendar_categories" onchange="cal_category_change(this.value,\''.$yearchange.'\');">';
		$ctselect .= '<option value="0">'.JText::_("CALENDAR_ALL_CATEGORY").'</option>';

		foreach($categories as $ckey => $cvalue){

			if($cvalue->id == $this->catid) {
				$selected = "selected";

				if(trim($cvalue->background) == ""){
					$showbackgroudcolor = "";
				}else{
					$showbackgroudcolor = "#".$cvalue->background;
				}

			}else{
				$selected = "";
			}

			if(trim($cvalue->background) == ""){
				$backgroudcolor = "";
			}else{
				$backgroudcolor = "#".$cvalue->background;
			}

			$ctselect .= '<option style="color:'.$backgroudcolor.';" value="'.$cvalue->id.'" '.$selected.'>'.$cvalue->catname.'</option>';
		}

		$ctselect .= '</select>';
		return $ctselect;
	}
}

?>