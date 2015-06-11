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

$pagetitle = JText::_('ADMIN_REPORT_CHARTS_HEADING');
JToolBarHelper::title( $pagetitle, 'searchdata' );
JToolBarHelper::cancel();
JToolBarHelper::spacer();

jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$db	= JFactory::getDBO();

$get = JRequest::get();
//echo "<pre>"; print_r($get); echo "</pre>";

$show_total_income = JRequest::getVar('show_total_income', 'on');
$show_published = JRequest::getVar('show_published', 'on');
$show_unpublished = JRequest::getVar('show_unpublished', 'on');
$show_archived = JRequest::getVar('show_archived', 'on');
$event_id = JRequest::getVar('event_id', 0);
$month = JRequest::getVar('month', date('n'));
$year = JRequest::getVar('year', date('Y'));
$cat = JRequest::getVar('cat', 0);
$pay = JRequest::getVar('payment_status', 0);
$chart_type = JRequest::getVar('chart_type', 0);
$cht = 'LineChart';
$dates = JRequest::getVar('dates', date('Y-m-').'01');
$datef = JRequest::getVar('datef', date('Y-m-d'));
$tsk = JRequest::getVar('tsk', '');

$arr = array();
if(($tsk != '')&&($tsk == 'show_chart')) {
	$registrationproHelper = new registrationproHelper;
	foreach($this->data as $key=>$value) {
		$regdt = $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $value->uregdate + ($this->regpro_config['timezone_offset']*60*60));
		if (!array_key_exists($regdt, $arr)) {
			$arr[$regdt] = array();
			$arr[$regdt]['cnt'] = 0;
			$arr[$regdt]['tax'] = 0;
			$arr[$regdt]['price'] = 0;
		}
		$arr[$regdt]['cnt']++;
		$arr[$regdt]['tax'] = $arr[$regdt]['tax'] + $value->tax_amount * 1;
		if((($value->final_price)*1) == 0) {
			$arr[$regdt]['price'] = $arr[$regdt]['price'] + $value->price;
		} else {
			$arr[$regdt]['price'] = $arr[$regdt]['price'] + $value->final_price;
		}
	}
}

$chart_title = "Events Registrations";
if ($tsk != '') {
	$chart_title = $chart_title . ' ' . $dates . ' - ' . $datef;
}

?>
<script>
Joomla.submitbutton = function(pressbutton) {
	if(pressbutton == 'cancel'){
		window.location = 'index.php?option=com_registrationpro';
		return false;
	}
}
</script>
<style type="text/css" media="screen">#ajaxmessagebox {margin-bottom:10px;width:auto;padding:4px;border:solid 1px #DEDEDE;background:#FFFFCC;display:none;text-align:center;}</style>
<div class="span10">
<form name="adminForm" action="index.php">
<input type="hidden" name="option" value="com_registrationpro" />
<input type="hidden" name="view" value="stat_charts" />
<input type="hidden" name="tsk" value="show_chart" />
	<span class="span12 y-offset no-gutter">
		<h3><?php echo JText::_('ADMIN_REPORT_CHARTS_HEADING');?></h3>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_REPORT_START_DATE');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<?php echo JHTML::_('calendar', $dates, 'dates', 'dates', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19'));?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_REPORT_END_DATE');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<?php echo JHTML::_('calendar', $datef, 'datef', 'datef', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'20',  'maxlength'=>'19'));?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_REPORT_CATEGORY');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<select id="cat" class="inputbox" size="1" name="cat">
			<option value="0" <?php if($cat == 0) echo "selected";?>>--- All Categories ---</option>
			<?php 
				foreach($this->listC as $lm){ 
					$sel = ''; 
					if($cat == $lm->value){ 
						$sel = 'selected'; 
						$chart_title = $chart_title.", Category - ".$lm->text;
					} 
					echo "<option value=\"".$lm->value."\" ".$sel.">".$lm->text."</option>\n";
				} 
			?>
		</select>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		Select an Event :
	</span>
	<span class="span6 y-offset no-gutter">
		<select id="event_id" class="inputbox" size="1" name="event_id">
			<option value="0" <?php if($event_id == 0) echo "selected";?>>--- All Events ---</option>
			<?php
				foreach($this->listE as $lm) {
					$sel = '';
					if($event_id == $lm[1]){ 
						$sel = 'selected'; 
						$chart_title = $chart_title.", Event - ".$lm[2];
					}
					echo "<option id=\"category_".$lm[0]."\" value=\"".$lm[1]."\" ".$sel.">".$lm[2]."</option>\n";
				}
			?>
		</select>
		<?php if ($cat == 0) { ?>
			<script type="text/javascript">
				$("#event_selector").css({
					display: "none",
					visibility: "hidden"
				});
			</script>
		<?php } ?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_REPORT_PAYMENT_STATUS');?>
	</span>
	<span class="span6 y-offset no-gutter">
		<select id="payment_status" class="inputbox" size="1" name="payment_status">
			<option value="0" <?php if($pay == 0) echo "selected";?>>--- All Statuses ---</option>
			<?php 
				$cnt=1; 
				foreach($this->listP as $lm){ 
					$sel = ''; 
					if($pay == $cnt){ 
						$sel = 'selected'; 
						$chart_title = $chart_title.", Payment Status - ".$lm;
					} 
					echo "<option value=\"".$cnt."\" ".$sel.">".$lm."</option>\n"; $cnt++;
				} 
			?>
		</select>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		Include Events :
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="checkbox" name="show_published" <?php if ($show_published != '') echo "checked";?> style="margin-top:-4px;" />&nbsp;&nbsp;Published<br />
		<input type="checkbox" name="show_unpublished" <?php if ($show_unpublished != '') echo "checked";?> style="margin-top:-4px;" />&nbsp;&nbsp;Unpublished<br />
		<input type="checkbox" name="show_archived" <?php if ($show_archived != '') echo "checked";?> style="margin-top:-4px;" />&nbsp;&nbsp;Archived<br />
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		Show Total Income Graph :
	</span>
	<span class="span6 y-offset no-gutter">
		<input type="checkbox" name="show_total_income" <?php if ($show_total_income != '') echo "checked";?> style="margin-top:-4px;" />
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		Chart Visualization :
	</span>
	<span class="span6 y-offset no-gutter">
		<select class="inputbox" size="1" name="chart_type">
			<option value="0" <?php if($chart_type == 0) {echo "selected"; $cht = 'LineChart';}?>>Line Chart</option>
			<option value="1" <?php if($chart_type == 1) {echo "selected"; $cht = 'AreaChart';}?>>Area Chart</option>
			<option value="2" <?php if($chart_type == 2) {echo "selected"; $cht = 'SteppedAreaChart';}?>>Stepped Area Chart</option>
			<option value="3" <?php if($chart_type == 3) {echo "selected"; $cht = 'ColumnChart';}?>>Column Chart</option>
		</select>
	</span>
	<br/>
	<span class="span4 no-gutter visible-desktop"></span>
	<span class="span6 y-offset no-gutter">
		<input type="submit" id="showreport" class="btn btn-small btn-success" value="<?php echo JText::_('ADMIN_REPORT_EVENT_BUTTON');?>" />
	</span>
</form>
	
<div id="ajaxmessagebox"></div>

<?php if(($arr)&&(isset($arr))&&(count($arr)>0)) { ?>

<div id="chart_div" style="width:100%;height:500px;margin-bottom:50px;"></div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
Array.prototype.reduce = undefined;
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
	var data = google.visualization.arrayToDataTable([
	
		<?php
		if ($show_total_income != '') {
			$header = "['Date', 'Number of Registrants', 'Tax Amount', 'Daily Amount', 'Total Registrants', 'Total Income'],";
		} else {
			$header = "['Date', 'Number of Registrants', 'Tax Amount', 'Daily Amount', 'Total Registrants'],";
		}
		echo $header."\n";
		?>
	
		<?php
				$total_q = 0;
				$total_p = 0;
				$i = 0;
				$len = count($array);
				foreach($arr as $key=>$val) {
					$total_q = $total_q + $val['cnt'];
					$total_p = $total_p + $val['price'];
					if ($show_total_income != '') {
						$row = "['".$key."', ".$val['cnt'].", ".$val['tax'].", ".$val['price'].", ".$total_q.", ".$total_p."]";
					} else {
						$row = "['".$key."', ".$val['cnt'].", ".$val['tax'].", ".$val['price'].", ".$total_q."]";
					}
					echo $row;
					if ($i != $len - 1) {
						echo ",";
					}
					echo "\n";
					$i++;
				}
		?>
	]);
	var options = {
		title: '<?php echo $chart_title; ?>',
		//curveType: 'function',
		legend: { position: 'bottom' },
		hAxis: {textStyle: {color: 'black', fontSize: 10}},
		vAxis: {textStyle: {color: 'black', fontSize: 10}},
		chartArea:{left:50,top:50,width:"90%",height:"70%"}
		};
	var chart = new google.visualization.<?php echo $cht;?>(document.getElementById('chart_div'));
	chart.draw(data, options);
}

</script>

<?php
	} else {
		if ($tsk != '') {
			echo "<div style=\"padding:20px;font-size:14px;color:#800;font-style:bold;font-weight:700;margin-bottom:20px;\">Nothing found by your request. Try to select other conditions...</div>\n";
		}
	}
?>

<script type="text/javascript">

var curr_cat = $('#cat').val();

function eventSelectorChange() {
	var sel = $('#cat').val();
	if(sel == 0){
		$("#event_selector").css({
			display: "none",
			visibility: "collapse"
		});
	} else {
		$("#event_selector").css({
			display: "table-row",
			visibility: "visible"
		});
		
		var inputTags = document.getElementsByTagName('option');
		var count=0;
		for(var i=0;i<inputTags.length;i++) {
			if(inputTags[i].id.contains('category_'+sel)) {
				inputTags[i].style.display = "block";
				inputTags[i].style.visibility = "visible";
			} else {
				if(inputTags[i].id.contains('category_')) {
					inputTags[i].style.display = "none";
					inputTags[i].style.visibility = "collapse";
				}
			};
		}
	}
	if(curr_cat != sel) {$("#event_id").val(0); curr_cat = sel;}
}

$('#cat').change(function(e) {
	eventSelectorChange();
});

eventSelectorChange();

</script>
</div>
<div class="span10">
	<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer( ); ?>
</div>
