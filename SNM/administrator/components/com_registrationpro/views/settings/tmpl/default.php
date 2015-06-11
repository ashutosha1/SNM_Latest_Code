<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
//JHTML::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_CONFIG' ), 'rconfig.png' );
JToolBarHelper::spacer();
JToolBarHelper::apply();
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel();
//JToolBarHelper::spacer();
//JToolBarHelper::help( 'screen.registrationpro', true );

foreach ($this->row as $each) $$each[1] = $each[2];
JHTML::_('behavior.modal', 'a.modal', array('onClose'=>'\function(){ConfigUsersUpdate();}'));

$validateDateFormat = false;
$validateTimeFormat = false;

$DateFormatMessage = "<table width=200 style='font-size:11px;'><tr valign=top><td align=left colspan=3><b>Date Format</b></td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>j</td><td align=left>- Day num without leading 0</td><td align=right>1 - 31</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>d</td><td align=left>- 2 digits Day of the month</td><td align=right>01 - 31</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>D</td><td align=left>- A textual name of a day</td><td align=right>Mon-Sun</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>n</td><td align=left>- Month # without leading 0</td><td align=right>1 - 12</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>m</td><td align=left>- Month num with leading 0</td><td align=right>01 - 12</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>M</td><td align=left>- A short name of a month</td><td align=right>Jan-Dec</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>Y</td><td align=left>- A 4 digits year</td><td align=right>e.g. 2014</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>y</td><td align=left>- A 2 digits year</td><td align=right>99 - 14</td></tr>".
	"<tr valign=top><td align=center colspan=3 style='color:#ccf;padding-top:5px;'>To see full manual, just click on icon</a></td></tr>".
	"</table>";

$TimeFormatMessage = "<table width=200 style='font-size:11px;'><tr valign=top><td align=left colspan=3><b>Time Format</b></td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>a</td><td align=left>- Lowercase am/pm</td><td align=right>am / pm</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>A</td><td align=left>- Uppercase AM/PM</td><td align=right>AM / PM</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>g</td><td align=left>- 12h without leading zero</td><td align=right>1 - 12</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>G</td><td align=left>- 24h without leading zero</td><td align=right>0 - 23</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>h</td><td align=left>- 12h with leading zero</td><td align=right>01 - 12</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>H</td><td align=left>- 24h with leading zero</td><td align=right>00 - 23</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>i</td><td align=left>- Minutes with leading zero</td><td align=right>00 - 59</td></tr>".
	"<tr valign=top><td align=left style='color:#ff5;'>s</td><td align=left>- Seconds with leading zero</td><td align=right>00 - 59</td></tr>".
	"<tr valign=top><td align=center colspan=3 style='color:#ccf;padding-top:5px;'>To see full manual, just click on icon</a></td></tr>".
	"</table>";

?>

<script language="javascript">
	var cp = new ColorPicker();
	var cp = new ColorPicker('window');

	function pickColor(color) {
		document.getElementById('message_color').value = color;
		document.getElementById('message_color').style.background = color;
	}

	function embox(id){
		 var emailText = document.getElementById(id).value;

		var pattern = /^[a-zA-Z0-9\-_]+(\.[a-zA-Z0-9\-_]+)*@[a-z0-9]+(\-[a-z0-9]+)*(\.[a-z0-9]+(\-[a-z0-9]+)*)*\.[a-z]{2,4}$/;
		if(emailText == ""){
		}else
		if (pattern.test(emailText)) {
			return true;
		} else {
			alert('<?php echo JText::_('ADMIN_CONFIG_SET_EMAIL_ERROR'); ?>');
			document.getElementById(id).value = "";
			return false;
		}
	}

	/************* FUNCTION TO VALIDATE DATE FORMATE *****************/
	function formdate(id){
		var data = document.getElementById(id).value;
		var pattern = /^(d,M,y)|(y,M,d)$/;
		if(pattern.test(data)){
			return true;
		}else{
			alert('<?php echo JText::_('ADMIN_CONFIG_SET_DATE_ERROR'); ?>');
			document.getElementById(id).value = "d,M,y";
			return false;
		}
	}

	/************* FUNCTION TO VALIDATE TIME FORMATE *****************/
	function formTime(id){
		var data = document.getElementById(id).value;
		var pattern = /^H:i:s$/;
		if(pattern.test(data)){
			return true;
		}else{
			alert('<?php echo JText::_('ADMIN_CONFIG_SET_TIME_ERROR'); ?>');
			document.getElementById(id).value = "H:i:s";
			return false;
		}
	}

	/************* FUNCTION TO VALIDATE EVENTLIMIT *****************/
	function eventLimit(id){
		var eventText = document.getElementById(id).value;
		if(eventText == ""){
		}else
		if(parseInt(eventText) == eventText){
			return true;
		}else{
			alert('<?php echo JText::_('ADMIN_CONFIG_SET_EVENTLIMIT_ERROR'); ?>');
			document.getElementById(id).value = '';
			return false;
		}
	}

	function quantityLimit(id){
		var eventText = document.getElementById(id).value;
		if(eventText == ''){
		}else if(parseInt(eventText) == eventText){
			return true;
		}else{
			alert('<?php echo JText::_('ADMIN_CONFIG_SET_QUANTITYLIMIT_ERROR'); ?>');
			document.getElementById(id).value = '';
			return false;
		}
	}

	function getCurr(){
		var code = document.getElementById('currency_value').value;
		var url = 'index.php?option=com_registrationpro&controller=settings&task=currencySymbol&cvalue='+code;
			jQuery.ajax({
				url: url,
				type:'post',
				beforeSend:function(){
					var image = '<img src="<?php echo REGPRO_ADMIN_BASE_URL.'/assets/images/icon_ajax.gif';?>">';
					document.getElementById('currency_sign_div').innerHTML = image;
				},
				success:function(response){
				},
				complete: function(response){
					document.getElementById('currency_sign_div').innerHTML = response.responseText;
					document.getElementById('currency_sign').value = response.responseText;
				},
				failure: function(response){
					alert('Did not get any thing');
				}
			});
	}
	jQuery(document).ready(function(){
		jQuery('.hasTooltip').tooltip({"html": true,"container": "body"});
		if(jQuery('#system-message-container').length > 0)
		{
			setInterval(function(){
				jQuery('#system-message-container').fadeOut();
			},3000);
			
		}
	});
	
</script>

<script language="JavaScript">cp.writeDiv()</script>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<ul class="nav nav-tabs" id="my-responsive-tabs">
		<li class="active"><a href="#currency_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETT_HEAD0'); ?></a></li>
		<li><a href="#general_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETT_HEAD3'); ?></a></li>
		<li><a href="#event_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETT_HEAD4'); ?></a></li>
		<li><a href="#registration_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENT_REGISTRATION_HAEDING'); ?></a></li>
		<li><a href="#display_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETT_HEAD1'); ?></a></li>
		<li><a href="#userdatabase_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETT_HEADREG1'); ?></a></li>
		<li><a href="#frontend_event_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETT_USERS_HEADER'); ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="currency_settings" style="border:none;padding:10px;">
			<div class="span12 y-offset">
				<span class="span4 text-center">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_CURRENCY_ACCEPTED_CURRENCY');?>">
						<?php echo JText::_('ADMIN_CURRENCY_CURRENCY');?>
					</b>
				</span>
				<span class="span4 text-center">
					<?php echo $this->currencylist; ?>
				</span>
			</div>
			<div class="span12 no-gutter">
				<span class="span4 text-center" style="padding-top:30px;">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_CURRENCY_SIGN_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_CURRENCY_SIGN');?>
					</b>
				</span>
				<span class="span4 currency-sign" id="currency_sign_div">
					<?php echo $currency_sign; ?>
				</span>
				<input type="hidden" name="currency_sign" id="currency_sign" value="<?php echo $currency_sign; ?>" size="5" maxlength="3"/>
			</div>
			<div class="clearfix"></div>
		</div> <!-- Closing currency setting div -->
		<div class="tab-pane" id="general_settings" style="border:none;padding:10px;">
			<table class="adminform" style="border:none;padding:0px;margin:0px;width:100%;">
				<tr valign=top><td><b><?php echo JText::_('ADMIN_EVENTS_SETT_INTROT');?></b></td></tr>
				<tr valign=top><td><div style="margin-bottom:10px;margin-top:10px;"><?php echo JText::_('ADMIN_EVENTS_SETT_INTROT_DESC');?></div></td></tr>
				<tr valign=top><td valign="top" style='max-width:50%;'><?php echo $this->editor->display('introtext', stripslashes( $introtext ),'90%;', '200', '20', '40' ) ;?></td></tr>
			</table>
		</div> 
		
		<div class="tab-pane" id="event_settings">
			<div class="span12">
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD_BY_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD_BY');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['archiveby']; ?>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD');?>
					</b>
				</span>
				<span class="span6 y-offset  no-gutter">
					<select name="oldevent" size="1" class="inputbox">
						<option value="0"<?php if ($oldevent == 0) { ?>selected="selected"<?php } ?>><?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD1').' '; ?></option>
						<option value="1"<?php if ($oldevent == 1) { ?>selected="selected"<?php } ?>><?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD2').' '; ?></option>
						<option value="2"<?php if ($oldevent == 2) { ?>selected="selected"<?php } ?>><?php echo JText::_('ADMIN_EVENTS_SETT_DELOLD3').' '; ?></option>
					</select>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_NUMBER_OF_DAYS_RIGHT_TEXT');?>">
						<?php echo JText::_('ADMIN_NUMBER_OF_DAYS');?>
					</b>
				</span>
				<span class="span6 y-offset  no-gutter">
					<input type="text" name="minus" value="<?php echo $minus; ?>" class="inputbox" size="10" id="minus" onblur="eventLimit(this.id)"/>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_REMINDER_EMAILS_DISABLE_DESC');?>">
						<?php echo JText::_('ADMIN_REMINDER_EMAILS_DISABLE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php echo JHTML::_('select.radiolist', $this->list['arr'], 'disable_remiders', 'class="radio-btn"', 'value', 'text', $disable_remiders); ?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_MODERATOR_ENABLE_DESC');?>">
						<?php echo JText::_('ADMIN_MODERATOR_ENABLE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php echo JHTML::_('select.radiolist', $this->list['arr'], 'event_moderation', 'class="inputbox"', 'value', 'text', $event_moderation); ?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_MODERATOR_EMAIL_ADDRESS_DESC');?>">
						<?php echo JText::_('ADMIN_MODERATOR_EMAIL_ADDRESS');?>
					</b>
				</span>
				<span class="span6 no-gutter">
					<input type="text" name="moderatoremail" value="<?php echo $moderatoremail; ?>" class="inputbox" size="20" id="moderatoremail" onblur="embox(this.id)"/>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_PDF_ENABLE_DESC');?>">
						<?php echo JText::_('ADMIN_PDF_ENABLE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php echo JHTML::_('select.radiolist', $this->list['arr'], 'enablepdf', 'class="inputbox"', 'value', 'text', $enablepdf); ?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter" id="old">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_ONLY_ACCEPTED_MAX_ATTENDANCE_DESC');?>">
						<?php echo JText::_('ADMIN_ONLY_ACCEPTED_MAX_ATTENDANCE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php echo JHTML::_('select.radiolist', $this->list['arr'], 'include_pending_reg', 'class="inputbox"', 'value', 'text', $include_pending_reg); ?>
					</fieldset>
				</span>
			</div>
			<div class="clearfix"></div>
		</div> <!-- Closing event handling setting div -->
		
		<div class="tab-pane" id="registration_settings" style="border:none;padding:10px;">
			<div class="span12">
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_REGNOTIFY_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_REGNOTIFY');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" size="30" name="register_notify" value="<?php echo $register_notify; ?>" class="inputbox" id="register_notify" onblur="embox(this.id)"/>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_REQUIRE_SITE_REGISTRATION_RIGHT_TEXT');?>">
						<?php echo JText::_('ADMIN_REQUIRE_SITE_REGISTRATION');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'require_registration', 'class="inputbox"', 'value', 'text', $require_registration);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_DUPLICATE_EMAIL_REGISTRATION_DESC');?>">
						<?php echo JText::_('ADMIN_DUPLICATE_EMAIL_REGISTRATION');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'duplicate_email_registration', 'class="inputbox"', 'value', 'text', $duplicate_email_registration);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_USER_STATUS_AFTER_FREE_REGISTRATION_DESC');?>">
						<?php echo JText::_('ADMIN_USER_STATUS_AFTER_FREE_REGISTRATION');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'default_userstatus_free_events', 'class="inputbox"', 'value', 'text', $default_userstatus_free_events);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_USER_STATUS_AFTER_OFFLINE_PAYMENT_DESC');?>">
						<?php echo JText::_('ADMIN_USER_STATUS_AFTER_OFFLINE_PAYMENT');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'default_userstatus_offline_payment', 'class="inputbox"', 'value', 'text', $default_userstatus_offline_payment);
						?>
					</fieldset>
				</span>	
			</div>
			<div class="clearfix"></div>
		</div> <!-- Closing Registration setting div -->
		
		<div class="tab-pane" id="display_settings">
			<div class="span12 no-gutter">
				<span class="span12 general-heading y-offset">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_HEAD3');?></b>
				</span>
				<div class="clearfix"></div>
				<br/>
				<div class="clearfix"></div>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_DEFUALT_ORDERING_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_DEFUALT_ORDERING');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php
						$arrOrdering = array(JHTML::_('select.option', 1, 'Event Created', 'id','title'),JHTML::_('select.option', 2, 'Event Start Date', 'id','title' ),JHTML::_('select.option', 3, 'Event End Date', 'id','title' ), JHTML::_('select.option', 4, 'Event Title', 'id','title' ));
						$html =  JHTML::_('select.genericlist', $arrOrdering, 'eventlistordering', 'class="inputbox" size="1"','id', 'title', $eventlistordering);
						echo $html;
					?>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWHEADER_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWHEADER');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showhead', 'class="inputbox"', 'value', 'text', $showhead);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWFOOTER_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWFOOTER');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_footer', 'class="inputbox"', 'value', 'text', $show_footer);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWPOSTER_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWPOSTER');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_poster', 'class="inputbox"', 'value', 'text', $show_poster);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWPOSTER_CAL_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWPOSTER_CAL');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_poster_cal', 'class="inputbox"', 'value', 'text', $show_poster_cal);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_LISTINGBUTTON_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_LISTINGBUTTON');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php
						$arrListButton = array();
						$arrListButton[] = JHTML::_('select.option', 1, 'Default View', 'id','title');
						$arrListButton[] = JHTML::_('select.option', 2, 'Clean View', 'id','title' );
						echo JHTML::_('select.genericlist', $arrListButton, 'listing_button', 'class="inputbox"', 'id', 'title', $listing_button);
					?>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b>
						<?php echo JText::_('ADMIN_EVENTS_SETT_FORDATE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="formatdate" value="<?php echo $formatdate; ?>" size="15" maxlength="15" id ="dateformat" <?php if($validateDateFormat) echo 'onblur="formdate(this.id)"';?> class="pull-left">
					<a href="http://php.net/manual/en/function.date.php" target="_blank" class="editlinktip hasTip" title="<?php echo $DateFormatMessage;?>">
						<img src="components/com_registrationpro/assets/images/info_icon_24x24.png" border="0" width="24" height="24" alt="More Info" class="x-offset"/>
					</a>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b>
						<?php echo JText::_('ADMIN_EVENTS_SETT_FORTIME');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="formattime" value="<?php echo $formattime; ?>" size="15" maxlength="15" id="timeformat" <?php if($validateTimeFormat) echo 'onblur="formTime(this.id)"';?> class="pull-left">
					<a href="http://php.net/manual/en/function.date.php" target="_blank" class="editlinktip hasTip" title="<?php echo $TimeFormatMessage;?>">
						<img src="components/com_registrationpro/assets/images/info_icon_24x24.png" border="0" width="24" height="24" alt="More Info" class="x-offset"/>
					</a>
					
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_FORTIMEZONE_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_FORTIMEZONE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['offset']; ?>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_RSS_ENABLE_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_RSS_ENABLE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php
							echo JHTML::_('select.radiolist', $this->list['arr'], 'rss_enable', 'class="inputbox"', 'value', 'text', $rss_enable);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_THANKS_PAGE_LINK_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_THANKS_PAGE_LINK');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<textarea name="thankspagelink"><?php echo $thankspagelink; ?></textarea>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_DISABLE_HEADER_THANKS_MSG_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_DISABLE_HEADER_THANKS_MSG');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'disablethanksmessage', 'class="inputbox"', 'value', 'text', $disablethanksmessage);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_TERMSANDCONDITION_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_TERMSANDCONDITION');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'event_terms_and_conditions', 'class="inputbox"', 'value', 'text', $event_terms_and_conditions);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_FRONTEND_HELP_LINK_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_FRONTEND_HELP_LINK');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'frontend_help_link', 'class="inputbox"', 'value', 'text', $frontend_help_link);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_USER_ACCEPTED_REPORT_ONLY_DESC');?>">
						<?php echo JText::_('ADMIN_USER_ACCEPTED_REPORT_ONLY');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'accepted_registration_reports', 'class="inputbox"', 'value', 'text', $accepted_registration_reports);
						?>
					</fieldset>
				</span>
				<div class="clearfix"></div>
				<br/>
				<span class="span12 general-heading no-gutter y-offset">
					<?php echo JText::_('ADMIN_EVENTS_SETT_HEAD7');?>
				</span>
				<div class="clearfix"></div>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWINTRO_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWINTRO');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showintro', 'class="inputbox"', 'value', 'text', $showintro);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_LIST_EVENTS_LIMIT_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_LIST_EVENTS_LIMIT');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="eventslimit" value="<?php echo $eventslimit; ?>" size="3" maxlength="3" id="eventlimit" onblur="eventLimit(this.id)">
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_EVENTSTART_END_DATE_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_EVENTSTART_END_DATE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showeventdates', 'class="inputbox"', 'value', 'text', $showeventdates);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_EVENTSTART_END_TIME_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_EVENTSTART_END_TIME');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showeventtimes', 'class="inputbox"', 'value', 'text', $showeventtimes);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_PRICE_COLOUM_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_PRICE_COLOUM');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showpricecolumn', 'class="inputbox"', 'value', 'text', $showpricecolumn);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_LOCATION_COLOUM_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_LOCATION_COLOUM');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showlocationcolumn', 'class="inputbox"', 'value', 'text', $showlocationcolumn);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_SHORT_DESCRIPTION_COLOUM_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_SHORT_DESCRIPTION_COLOUM');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showshortdescriptioncolumn', 'class="inputbox"', 'value', 'text', $showshortdescriptioncolumn);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_COLLAPSE_CATEGORIES_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_COLLAPSE_CATEGORIES');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
					<?php 
						echo JHTML::_('select.radiolist', $this->list['arr'], 'collapse_categories', 'class="inputbox"', 'value', 'text', $collapse_categories);
					?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MAX_USERS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MAX_USERS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'maxseat', 'class="inputbox"', 'value', 'text', $maxseat);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_PENDING_USERS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_PENDING_USERS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
					<?php 
						echo JHTML::_('select.radiolist', $this->list['arr'], 'pendingseat', 'class="inputbox"', 'value', 'text', $pendingseat);
					?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_REGISTERED_USERS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_REGISTERED_USERS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'registeredseat', 'class="inputbox"', 'value', 'text', $registeredseat);
						?>
					</fieldset>
				</span>
				<br/>
				<div class="clearfix"></div>
				<br/>
				<span class="span12 general-heading y-offset no-gutter">
					<?php echo JText::_('ADMIN_EVENTS_SETT_HEAD8');?>
				</span>
				<div class="clearfix"></div>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_EVENT_INALLDATES_CALENDAR_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_EVENT_INALLDATES_CALENDAR');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_all_dates_in_calendar', 'class="inputbox"', 'value', 'text', $show_all_dates_in_calendar);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_CALENDAR_REGISTRATION_STATUS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_CALENDAR_REGISTRATION_STATUS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_calendar_registration_flag', 'class="inputbox"', 'value', 'text', $show_calendar_registration_flag);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_CALENDAR_CATEGORY_FIELTER_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_CALENDAR_CATEGORY_FIELTER');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'calendar_category_filter', 'class="inputbox"', 'value', 'text', $calendar_category_filter);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_CALENDAR_FIRST_DAY_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_CALENDAR_FIRST_DAY');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['calendar_weekdays']; ?>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_CAL_YEAR_START_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_CAL_YEAR_START');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['calendar_year_start']; ?>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_CAL_YEAR_END_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_CAL_YEAR_END');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['calendar_year_end']; ?>
				</span>
				<br/>
				<div class="clearfix"></div>
				<br/>
				<span class="span12 y-offset general-heading no-gutter">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_HEAD9');?></b>
				</span>
				<div class="clearfix"></div>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_MSG_COLOR_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_MSG_COLOR');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="message_color" id="message_color" value="<?php echo $message_color; ?>" style="background-color:<?php echo $message_color; ?>;">
					<a href="#" onClick="cp.show('pick');return false;" name="pick" id="pick" title="<?php echo JText::_('ADMIN_EVENTS_SETT_MSG_COLOR_PICK'); ?>">
						<img style="margin-top:3px;margin-left:-3px;" src="components/com_registrationpro/assets/images/icon_colorpicker_24x24.png" width=24 height=24 border=0>
					</a>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MAX_USERS_ON_DETAILS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MAX_USERS_ON_DETAILS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_max_seats_on_details_page', 'class="inputbox"', 'value', 'text', $show_max_seats_on_details_page);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_PENDING_USERS_ON_DETAILS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_PENDING_USERS_ON_DETAILS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_avaliable_seats_on_details_page', 'class="inputbox"', 'value', 'text', $show_avaliable_seats_on_details_page);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_REGISTERED_USERS_ON_DETAILS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_REGISTERED_USERS_ON_DETAILS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'show_registered_seats_on_details_page', 'class="inputbox"', 'value', 'text', $show_registered_seats_on_details_page);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWTIMEDET_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWTIME');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showtime', 'class="inputbox"', 'value', 'text', $showtime);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWDETEVDESC_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWDETEVDESC');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showevdesc', 'class="inputbox"', 'value', 'text', $showevdesc);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWDETEVTITEL_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWDETEVTITEL');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showtitle', 'class="inputbox"', 'value', 'text', $showtitle);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWLOC_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWLOC');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showlocation', 'class="inputbox"', 'value', 'text', $showlocation);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWURL_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWURL');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showurl', 'class="inputbox"', 'value', 'text', $showurl);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWLINKMAP_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWLINKMAP');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
					<?php 
						echo JHTML::_('select.radiolist', $this->list['arr'], 'showmapserv', 'class="inputbox"', 'value', 'text', $showmapserv);
					?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWCATEGORY_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOWCATEGORY');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'showcategory', 'class="inputbox"', 'value', 'text', $showcategory);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b>
						<?php echo JText::_('ADMIN_EVENTS_SETT_SESSION_FORDATE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="session_dateformat" value="<?php echo $session_dateformat; ?>" size="15" maxlength="15"  id="session_dateformat" <?php if($validateDateFormat) echo 'onblur="formdate(this.id)"';?> class="pull-left">
					<a href="http://php.net/manual/en/function.date.php" target="_blank" class="editlinktip hasTip" title="<?php echo $DateFormatMessage;?>" class="pull-left">
						<img src="components/com_registrationpro/assets/images/info_icon_24x24.png" border="0" width="24" height="24" alt="More Info" />
					</a>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b>
						<?php echo JText::_('ADMIN_EVENTS_SETT_SESSION_FORTIME');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="session_timeformat" value="<?php echo $session_timeformat; ?>" size="15" maxlength="15"  id="session_timeformat" <?php if($validateTimeFormat) echo 'onblur="formTime(this.id)"';?> class="pull-left"/>
					<a href="http://php.net/manual/en/function.date.php" target="_blank" class="editlinktip hasTip" title="<?php echo $TimeFormatMessage;?>">
						<img src="components/com_registrationpro/assets/images/info_icon_24x24.png"  border="0" width="24" height="24" alt="More Info" />
					</a>
				
				</span>
				<br/>
				<div class="clearfix"></div>
				<br/>
				<span class="span12 no-gutter general-heading y-offset no-gutter">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_HEAD10');?></b>
				</span>
				<div class="clearfix"></div>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_CART_QTY_LIMIT_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_CART_QTY_LIMIT');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<input type="text" name="quantitylimit"id="quantitylimit" value="<?php echo $quantitylimit; ?>" size="2" maxlength="" onblur="quantityLimit(this.id)">
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MULTI_EVENT_BUTTON_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MULTI_EVENT_BUTTON');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'multiple_registration_button', 'class="inputbox"', 'value', 'text', $multiple_registration_button);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_DISCOUNT_COUPON_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_DISCOUNT_COUPON');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
						<?php 
							echo JHTML::_('select.radiolist', $this->list['arr'], 'enable_discount_code', 'class="inputbox"', 'value', 'text', $enable_discount_code);
						?>
					</fieldset>
				</span>
				<br/>
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MANDATORY_NOTE_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_SHOW_MANDATORY_NOTE');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<fieldset class="radio btn-group btn-group-yesno">
					<?php 
						echo JHTML::_('select.radiolist', $this->list['arr'], 'enable_mandatory_field_note', 'class="inputbox"', 'value', 'text', $enable_mandatory_field_note);
					?>
					</fieldset>
				</span>
			</div>
			<div class="clearfix"></div>
		</div> <!-- Closing Display setting div -->

		<div class="tab-pane" id="userdatabase_settings" style="border:none;padding:10px;">
			<div class="span12">
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_COMM_SOL_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_COMM_SOL');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php
						$arrIntegration = array(JHTML::_('select.option', 0, 'Select One', 'id','title'),JHTML::_('select.option', 1, 'Community Builder', 'id','title' ),JHTML::_('select.option', 2, 'JomSocial', 'id','title' ),JHTML::_('select.option', 3, 'Core Joomla Profiles', 'id','title' ));
						$html = JHTML::_('select.genericlist', $arrIntegration, 'cbintegration', 'class="inputbox" size="1"','id', 'title', $cbintegration);
						echo $html;
					?>
				</span>
			</div>
			<div class="clearfix"></div>
		</div>	 <!-- Closing Database setting div -->
		
		<div class="tab-pane" id="frontend_event_settings" style="border:none;padding:10px;">
			<div class="span12">
				<span class="span6 y-offset">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_USERS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<select class="inputbox" disabled="disabled" multiple="true" style="width:200px; height:150px;">
						<?php
							$arr = explode(',', $this->list['userslists']);
							if(count($arr) > 0){
								foreach($arr as $usr) {
									if(trim($usr) != "") echo "<option value='".trim($usr)."' selected='selected'>".trim($usr)."</option>\n";
								}
							}
						?>
					</select>
					<a class="modal btn btn-success btn-mini" href="index.php?option=com_registrationpro&view=settings&layout=selectusers&tmpl=component" rel="{handler: 'iframe', size: {x: 900, y: 500}}">
						<?php echo JText::_('COM_REGISTRATION_PRO_CONFIG_FRONT_END_MANAGEMENT');?> 
					</a>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_GROUPS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_GROUPS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['UserGroupslists']; ?>
				</span>
				<div class="clearfix"></div>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_CATEGORIES_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_CATEGORIES');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['Categorylists']; ?>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_LOCATIONS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_LOCATIONS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['Locationslists']; ?>
				</span>
				<br/>
				<span class="span6 y-offset no-gutter">
					<b class="hasTooltip" data-original-title="<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_FORMS_DESC');?>">
						<?php echo JText::_('ADMIN_EVENTS_SETT_USERS_FORMS');?>
					</b>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php echo $this->list['Formslists']; ?>
				</span>
			</div>
		</div> <!-- Closing the Front end Event settings tab-pan div -->
		<div class="clearfix"></div>
	</div>	<!-- Closing the main tab-pan div -->

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="table_update" value="Y">
<input type="hidden" name="id" value="">
<input type="hidden" name="task" value="">
<input type="hidden" name="controller" value="settings">
<input type="hidden" name="option" value="com_registrationpro">
</form>
<div class="clearfix"></div>
</div>
