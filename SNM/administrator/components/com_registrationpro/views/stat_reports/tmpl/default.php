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
JHtml::_('formbehavior.chosen', 'select');
//create the toolbar
$pagetitle = JText::_('ADMIN_REPORT_HEADING');

JToolBarHelper::title( $pagetitle, 'searchdata' );
JToolBarHelper::cancel();
JToolBarHelper::spacer();

// Load pane behavior
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

?>
<script>
Joomla.submitbutton = function(pressbutton) {
	if(pressbutton == 'cancel'){
		window.location = 'index.php?option=com_registrationpro';
		return false;
	}
}
jQuery(document).on('click','#showreport',function(e){
	window.open('Event Registration Pro - Report', 'RegProReport', 'width=1200,height=800,status=yes,resizable=yes,scrollbars=yes');
});
jQuery(document).on('click','#showexcel',function(e){
	//e.preventDefault();
	jQuery('#adminForm').submit();
});
</script>
<style type="text/css" media="screen">#ajaxmessagebox {margin-bottom:10px;width:auto;padding:4px;border:solid 1px #DEDEDE;background:#FFFFCC;display:none;text-align:center;}</style>
<div class="span10">
<form name="adminForm" id="adminForm"action="index.php" target="RegProReport" onSubmit="">
	<span class="span12 y-offset">
		<h3><?php echo JText::_('ADMIN_REPORT_HEADING');?></h3>
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
		<select id="cat" class="inputbox" size="1" name="cat[]" multiple="multiple">
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
		<select id="event_id" class="inputbox" size="1" name="event_id[]" multiple="multiple">
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
		<input type="checkbox" name="show_archived" <?php if ($show_archived != '') echo "checked";?> style="margin-top:-4px;" />&nbsp;&nbsp;Archived<br/>
	</span>
	<br/>
	<span class="span4 no-gutter visible-desktop"></span>
	<span class="span6 y-offset no-gutter">
		<input type="submit" name="print_rpt" id="showreport" class="btn btn-small btn-success" value="<?php echo JText::_('ADMIN_REPORT_EVENT_BUTTON');?>" />
		<input type="submit" name="print_exl" id="showexcel" class="btn btn-small btn-success" value="<?php echo JText::_('ADMIN_EXCEL_REPORT_EVENT_BUTTON');?>" />
		
	</span>
<input type="hidden" name="option" value="com_registrationpro" />
<input type="hidden" name="controller" value="stat_reports" />
<input type="hidden" name="task" value="print_report" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="print" value="1" />
<input type="hidden" name="hidemainmenu" value="1" />
</form>

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