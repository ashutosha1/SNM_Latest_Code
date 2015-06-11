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

$calc_function 	= "calculate_tot_amt()";
$reset_function = "resetform()";
$total_amount_span_id = "totval";

?>
<span class="span12 y-offset session-heading">
	<?php echo JText::_('ADMIN_SESS_SUB1');?>
</span>
<div class="clearfix"></div>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_SESSION_HEADER');?>
</span>
<span class="span8 y-offset no-gutter">
	<textarea name="session_page_header" id="session_page_header" class="inputbox" style="width:95%;height:70px !important;">
		<?php echo $this->row->session_page_header; ?>
	</textarea>
</span>
<br/>
<span class="span12 y-offset no-gutter session-heading">
	<?php echo JText::_('ADMIN_SESS_SUB2');?>
</span>
<div class="clearfix"></div>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_MANDATORY_SYMBOL').JText::_('ADMIN_EVENTS_SESSION_TITLE');?>
</span>
<span class="span8 y-offset no-gutter">
	<input type="text" name="title" id="title" class="inputbox" style="width:95%;"/>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION');?>
</span>
<span class="span8 y-offset no-gutter">
	<textarea name="description" id="description" class="inputbox" style="width: 95%; height:80px !important;"></textarea>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_MANDATORY_SYMBOL').JText::_('ADMIN_EVENTS_SESSION_DATE');?>
</span>
<span class="span8 y-offset no-gutter">
	<div class="input-append">
		<input id="session_date" class="inputbox hasTooltip" type="text" maxlength=19 size=25 value="" name="session_date" title="" data-original-title="">
		<button id="session_date_img" class="btn" type="button"><i class="icon-calendar"></i></button>
	</div>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_MANDATORY_SYMBOL').JText::_('ADMIN_EVENTS_SESSION_TIME');?>
</span>
<span class="span8 y-offset no-gutter">
	<input type="text" name="session_start_time" id="session_start_time" class="inputbox" maxlength="5" style="width:50px;">
	<?php echo JText::_('ADMIN_EVENTS_SESSION_TIME_SAPERATOR');?>
	<input type="text" name="session_stop_time" id="session_stop_time" class="inputbox" maxlength="5" style="width:50px;">
	<b>( <?php echo JText::_('ADMIN_EVENTS_SESSION_TIME_NOTICE');?> )</b>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_SESSION_FEE');?>
</span>
<span class="span8 y-offset no-gutter">
	<input type="text" name="fee" id="fee" class="inputbox" size="8" />
</span>
<br/>
<span class="span12 y-offset no-gutter">
	<button class="btn btn-small btn-success" id="save_session">Add</button>
	<input type="button" class="button btn btn-inverse" value="Reset" onclick="resetform_session();" />
</span>
<script language="javascript">
	$('#save_session').click(function(e) {
		save_session(e);
	});
</script>