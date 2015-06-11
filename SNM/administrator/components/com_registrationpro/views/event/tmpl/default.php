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
JHTML::_('behavior.modal', 'a.modal');
JHtml::_('formbehavior.chosen', 'select');
//create the toolbar
JToolBarHelper::title( JText::_('ADMIN_LBL_CONTROPANEL_EDIT_EVENTS' ), 'eventsedit' );
JToolBarHelper::apply();
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel();
?>
<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	var email = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
	var emails = /^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/;
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	if(document.paymentform) {
		if(document.paymentform.regpro_dates_id && !document.adminForm.copy_task){
			if(document.paymentform.regpro_dates_id.value != '' && form.id.value == 0){
				form.id.value = document.paymentform.regpro_dates_id.value;
			}
		}
	}

	if(!validateForm(form,false,false,false,false)){
	} else {
		var startDate		= form.dates.value;
		var endDate			= form.enddates.value;
		var regStartDate 	= form.regstart.value;
		var regEndDate 		= form.regstop.value;

		startDate    = startDate.split('-');
		endDate      = endDate.split('-');
		regStartDate = regStartDate.split('-');
		regEndDate   = regEndDate.split('-');

		// check the date and time format
		if (!form.dates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_('ADMIN_EVENTS_DELFORM')." "; ?>");
			form.dates.focus();
		}else if (!form.times.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
			alert("<?php echo JText::_('EVENTS_DEL_TIME_FORM')." "; ?>");
			form.times.focus();
		}else if (!form.enddates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_('ADMIN_EVENTS_ENDDATE_FORMAT')." "; ?>");
			form.enddates.focus();
		}else if (!form.endtimes.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
			alert("<?php echo JText::_('EVENTS_DEL_ENDTIME_FORM')." "; ?>");
			form.endtimes.focus();

		}else if(endDate[0] < startDate[0]){
			alert('<?php echo JText::_('ADMIN_EVENT_EVENT_DETAIL_TAB_DATE_ERROR');?>');
			form.enddates.focus();
		}else if ((endDate[0] == startDate[0]) && (endDate[1] < startDate[1])){
			alert('<?php echo JText::_('ADMIN_EVENT_EVENT_DETAIL_TAB_DATE_ERROR');?>');
			form.enddates.focus();
		}else if ((endDate[0] == startDate[0]) && (endDate[1] == startDate[1]) && (endDate[2] < startDate[2])){
			alert('<?php echo JText::_('ADMIN_EVENT_EVENT_DETAIL_TAB_DATE_ERROR');?>');
			form.enddates.focus();

		}else if(regEndDate[0] < regStartDate[0]){
			alert('<?php echo JText::_('ADMIN_EVENT_REGISTRATION_TAB_REGDATE_ERROR');?>');
			form.regstop.focus();
		}else if ((regEndDate[0] == regStartDate[0]) && (regEndDate[1] < regStartDate[1])){
			alert('<?php echo JText::_('ADMIN_EVENT_REGISTRATION_TAB_REGDATE_ERROR');?>');
			form.regstop.focus();
		}else if ((regEndDate[0] == regStartDate[0]) && (regEndDate[1] == regStartDate[1]) && (regEndDate[2] < regStartDate[2])){
			alert('<?php echo JText::_('ADMIN_EVENT_REGISTRATION_TAB_REGDATE_ERROR');?>');
			form.regstop.focus();
		}else if(!emails.test(form.notifyemails.value) && form.notifyemails.value !=''){
			alert('<?php echo JText::_('ADMIN_EVENT_REGISTRATION_TAB_EMAIL_ERROR');?>');
			form.notifyemails.focus();
		}else if(form.notifydate.value != '' && (parseInt(form.notifydate.value) != form.notifydate.value)){
				alert('<?php echo JText::_('ADMIN_EVENT_REGISTRATION_TAB_DATE_ERROR');?>');
				form.notifydate.focus();
		}else if (form.registra.value == 1) {

			if(form.regstarttimes.value >= "24:00") form.regstarttimes.value = "00:00";
			if(form.regstoptimes.value >= "24:00")  form.regstoptimes.value = "00:00";

			if(form.form_id.value == 0){
				alert("<?php echo JText::_('EVENTS_REGISTER_FORM')." "; ?>");
				form.form_id.focus();
			}else if(form.regstart.value == "" || form.regstart.value == "0000-00-00"){
				alert("<?php echo JText::_('EVENTS_REGISTER_START_DATE')." "; ?>");
				form.regstart.focus();
			}else if (!form.regstarttimes.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
				alert("<?php echo JText::_('EVENTS_REGISTER_START_TIME')." "; ?>");
				form.regstarttimes.focus();
			}else if(form.regstop.value == "" || form.regstop.value == "0000-00-00"){
				alert("<?php echo JText::_('EVENTS_REGISTER_END_DATE')." "; ?>");
				form.regstop.focus();
			}else if (!form.regstoptimes.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
				alert("<?php echo JText::_('EVENTS_REGISTER_STOP_TIME')." "; ?>");
				form.regstoptimes.focus();
			}else{
				submitform( pressbutton );
			}
		} else { submitform( pressbutton );}
	}
}

function checkLink(data) {
	var check = document.getElementsByName("cid[]");
	if(check.length == 0) {
		return false;
	}
	var flag = 0;
	for(i=0;i<check.length;i++) {
		if(check[i].checked==false) {
			flag = 1;
		} else {
			flag = 0;
			break;
		}
	}
	if(flag == 1) {
		alert(data);
		return false;
	}
}

function eventTickets(){
	var ticket = document.getElementById('product_quantity').value;
	var product  = document.paymentform_add.product_quantity.value;
	if(document.getElementById('product_quantity').value == 0){
	} else if(parseInt(ticket) == ticket){
	} else {
		alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_QUANTITY_ERROR');?>');
		document.getElementById('product_quantity').value = 0;
		document.getElementById('product_quantity').focus();
		return false;
	}

	if(product == 0){
	} else if(parseInt(product) == product){
	} else {
		alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_QUANTITY_ERROR');?>');
		document.paymentform_add.product_quantity.value = 0;
		document.paymentform_add.product_quantity.focus();
		return false;
	}
}
</script>
<div class="span10">
<fieldset>
	<ul class="nav nav-tabs" id="my-responsive-tabs">
		<li class="active"><a href="#event_details" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_DETAILS');?> </a></li>
		<li><a href="#event_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SETTINGS');?> </a></li>
		<li><a href="#registration_details" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_REG');?> </a></li>
		<?php if ($this->row->ordering < 10000) { ?>
		<li><a href="#recurring_details" data-toggle="tab"><?php echo  JText::_('RECURRING_EVENTS');?> </a></li>
		<?php } ?>
		<li><a href="#event_metadata" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_METADATA_SETTINGS');?> </a></li>
		<?php
			if($this->task != 'copy') { ?>
				<li><a href="#event_ticket" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_PAYMENT');?> </a></li>
				<?php if($this->row->id) { ?>
				<li><a href="#event_additional_ticket" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_ADDITIONAL_PAYMENT');?> </a></li>
				<li><a href="#group_discount" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_GROUP_DISCOUNT');?> </a></li>
				<li><a href="#early_discount" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_EARLY_DISCOUNT');?> </a></li>
				<li><a href="#session_page" data-toggle="tab"><?php echo  JText::_('ADMIN_EVENTS_SESSION');?> </a></li>
				<?php
					$plugin = JPluginHelper::getPlugin('user', 'regpro_mailchimp');
					if(!empty($plugin)) echo "<li><a href=\"#regpro_mailchimp\" data-toggle=\"tab\">".JText::_('ADMIN_EVENTS_MAILCHIMP')."</a></li>\n";
				}
			}
		?>
	</ul>
</fieldset>

<?php
	//include_once '/components/com_registrationpro/helpers/tools.php';
	include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tools.php';
	$event_image = 'old';
	$imgPrefixSystem = "../images/regpro/system/";
	$imgPrefixEvents = "../images/regpro/events/";
	$imgTemp = "temporary_uploaded_image.jpg";
	$pdfImgTemp = "temporary_uploaded_pdfimage.jpg";
	
	$imgCurr = getImageName($this->row->id, $this->row->user_id);
	if(!isset($this->row->image) || (trim($this->row->image) == '') || ($this->row->image == '0')) {
		$this->row->image = '0';
		$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
	} else {
		$this->row->image = '1';
		$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();;
	}
	
	$pdfImgCurr = 'pdf'.getImageName($this->row->id, $this->row->user_id);
	if(!isset($this->row->pdfimage) || (trim($this->row->pdfimage) == '') || ($this->row->pdfimage == '0')) {
		$this->row->pdfimage = '0';
		$pdfImgName = $imgPrefixSystem . "nopdfimage_720x240.jpg".getUniqFck();
	} else {
		$this->row->pdfimage = '1';
		$pdfImgName = $imgPrefixEvents . $pdfImgCurr . getUniqFck();;
	}
	
	if (!isset($this->row->parent_id) || ($this->row->parent_id < 0)) {
		$parent_id = $this->row->id;
	} else $parent_id = $this->row->parent_id;
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="event_image" id="event_image" value="<?php echo $event_image;?>" />
<input type="hidden" name="image_name" id="image_name" value="<?php echo $imgCurr;?>" />
<input type="hidden" name="image" value="<?php echo $this->row->image;?>" />
<input type="hidden" name="event_pdfimage" id="event_pdfimage" value="<?php echo $event_pdfimage;?>" />
<input type="hidden" name="pdfimage_name" id="pdfimage_name" value="<?php echo $pdfImgCurr;?>" />
<input type="hidden" name="pdfimage" value="<?php echo $this->row->pdfimage;?>" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $parent_id;?>" />
	<div class="tab-content">
		<div class="tab-pane active" id="event_details">
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_TITEL');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="titel" alt="blank" emsg="<?php echo JText::_('EVENTS_DEL_TITEL_EMPT');?>" value="<?php echo htmlspecialchars(stripslashes($this->row->titel), ENT_QUOTES, 'UTF-8'); ?>" maxlength="200" class="inputbox" style="width:40%;">
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_STATUS')." "; ?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php echo $this->Lists['event_status']; echo $event_status; ?>&nbsp;&nbsp;
				<input style="vertical-align:top;" value="1" name="notify" type="checkbox">
				<span style="vertical-align:top;">&nbsp;<?php echo JText::_('ADMIN_EVENTS_NOTIFY');?></span>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_INSTRUCTOR_NAME')." "; ?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="instructor" value="<?php echo $this->row->instructor; ?>" size="25" maxlength="200">
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_DATE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo JHTML::_('calendar', $this->row->dates, 'dates', 'dates', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
				?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_TIME');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="times" alt="blank" emsg="<?php echo JText::_('EVENTS_DEL_TIME_EMPT');?>" value="<?php echo substr($this->row->times, 0, 5);?>" size="15" maxlength="8">
				&nbsp;&nbsp;<b>
				<?php
					if ( $layoutsettings[ $idx['time']]->set_show == 1 ) {
						echo JText::_('ADMIN_EVENTS_TIME_NOTICE')." ";
					} else echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE')." ";
				?>
				</b>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_ENDDATE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo JHTML::_('calendar', $this->row->enddates, 'enddates', 'enddates', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
				?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_ENDTIME');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="endtimes" alt="blank" emsg="<?php echo JText::_('EVENTS_DEL_ENDTIME_EMPT');?>" value="<?php echo substr($this->row->endtimes, 0, 5);?>" size="15" maxlength="8">
				&nbsp;&nbsp;<b><?php echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE')." "; ?></b>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('COM_REGISTRATIONPRO_EVENT_DISPLAY_LABEL_IMAGE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<img id="cat_image" src="<?php echo $imgName; ?>" class="thumbnail">
				<div id="config_image_form">
					<form id="config_imageForm" enctype="multipart/form-data" method="POST" style="margin:0px;padding:0px;">
					<input type="file" name="fileToUpload_selector" id="fileToUpload_selector" />
					</form>
					<br />
					<div id="response"></div>
					<br />
					<?php if (isset($this->row->image) && ($this->row->image == '1')) { $btn_delete_style="visible"; } else { $btn_delete_style="hidden"; }?>
					<a href="javascript:void(0);" onclick="deleteImage();" class="toolbar btn btn-small btn-danger" id="btn_delete_image" style="visibility:<?php echo $btn_delete_style?>;">Delete Image</a>
				</div>
			</span>
			<div class="clearfix"></div>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_MAX_ATTENDANCE')." "; ?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="max_attendance" value="<?php echo substr($this->row->max_attendance, 0, 5);?>" size="15" maxlength="8">
				&nbsp;&nbsp;<b><?php echo JText::_('ADMIN_EVENTS_MAX_ATTENDANCE_NOTICE')." "; ?></b>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_REGCOUNT')." "; ?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php
					if ($this->row->registra == 1) {
						$nrregusers_array = $this->getModel()->getRegistered($row->id);
						$nrregusers = 0;
						foreach ($nrregusers_array as $pid=>$qty) if(is_int($qty)) $nrregusers += $qty;
						$linkreg = 'index.php?option=com_registrationpro&task=showregevusers&rdid='.$this->row->id.'&hidemainmenu=1';
						if($nrregusers > 0){
				?>
							<a href="<?php echo $linkreg; ?>" title="Edit Users">
								<?php echo $nrregusers . (($this->row->max_attendance==0) ? '': ' / ' . $this->row->max_attendance);?>
							</a>
						<?php
							} else echo $nrregusers;
						} else {
						?>
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/publish_x.png" width="12" height="12" border="0" alt="Registration disabled" />
						<?php
						}
						?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_SHORT_DESCR')." "; ?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo $this->editor->display( 'shortdescription',  $this->row->shortdescription , '80%', '200', '75', '20', array('pagebreak', 'readmore'));
				?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_DESCR')." "; ?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo $this->editor->display( 'datdescription',  $this->row->datdescription , '80%', '200', '75', '20', array('pagebreak', 'readmore'));
				?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_TERMS_AND_CONDITION');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo $this->editor->display( 'terms_conditions',  $this->row->terms_conditions , '80%', '200', '75', '20', array('pagebreak','readmore'));
				?>
			</span>
			<br/>
			<script language="JavaScript">function submitForm(){document.config_imageForm.submit();}</script> 
			<script language="JavaScript">

				function deleteImage() {
					var img = $('#cat_image');
					var src = img.attr('src');
					d = new Date();
					img.attr('src', "<?php echo $imgPrefixSystem?>" + 'noimage_200x200.jpg?dummy=' + d.getTime());
					var btn_delete_image = $('#btn_delete_image');
					btn_delete_image.attr('style', 'visibility:hidden;')
					var event_image = $('#event_image');
					event_image.val('del')
				}
				
				function refreshImage() {
					var img = $('#cat_image');
					var src = img.attr('src');
					d = new Date();
					img.attr('src', "<?php echo $imgPrefixSystem?>" + "<?php echo $imgTemp?>" + '?fck=' + d.getTime());
					var btn_delete_image = $('#btn_delete_image');
					btn_delete_image.attr('style', 'visibility:visible;')
					var event_image = $('#event_image');
					event_image.val('new')
				}
				
				jQuery('document').ready(function(){
					var input = document.getElementById("fileToUpload_selector");
					var formdata = false;
					if (window.FormData) {
						formdata = new FormData();
					}
					input.addEventListener("change", function (evt) {
						var i = 0, len = this.files.length, img, reader, file;

						for ( ; i < len; i++ ) {
							file = this.files[i];

							if (!!file.type.match(/image.*/)) {
								if ( window.FileReader ) {
									reader = new FileReader();
									reader.readAsDataURL(file);
								}

								if (formdata) {
									formdata.append("image", file);
									formdata.append("path2save", "<?php echo $imgPrefixSystem?>" + "<?php echo $imgTemp?>");
								}

								if (formdata) {
									var img = document.getElementById("cat_image");
									img.src = "";
									jQuery('div#response').html('<br />Loading...');
									jQuery.ajax({
										url: "components/com_registrationpro/helpers/imager.php",
										type: "POST",
										data: formdata,
										processData: false,
										contentType: false,
										success: function (res) {
											jQuery('div#response').html("Successfully uploaded");
											refreshImage();
										}
									});
								}
							}
							else { alert('Not a vaild image format!'); }
						}
					}, false);
				});
			</script>
		</div>
		<div class="tab-pane" id="event_settings">
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_ACCESS');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php echo $this->Lists['access']; ?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_VIEW_ACCESS');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php echo $this->Lists['viewaccess']; ?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_PUBLI');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php
						$arrPublish = array(JHTML::_('select.option', 0, 'No'),JHTML::_('select.option', 1, 'Yes'));
						$html =  JHTML::_('select.radiolist', $arrPublish, 'published', 'class="inputbox" size="1"','value', 'text', $this->row->published);
						echo $html;
					?>
				</fieldset>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_SHOW_ATTENDIES_LISTS');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php
						if ($this->row->shw_attendees == '')
						{
							$this->row->shw_attendees = 0;
						}
						$arrPublish = array(JHTML::_('select.option', 0, 'No'),JHTML::_('select.option', 1, 'Yes'));
						$html =  JHTML::_('select.radiolist', $arrPublish, 'shw_attendees', 'class="inputbox" size="1"','value', 'text',$this->row->shw_attendees);
						echo $html;
					?>
				</fieldset>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_GROUPREG');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php
						if (!$this->row->allowgroup)
						{
							$this->row->allowgroup = 0;
						}
						$arrPublish = array(JHTML::_('select.option', 0, 'No'),JHTML::_('select.option', 1, 'Yes'));
						$html =  JHTML::_('select.radiolist', $arrPublish, 'allowgroup', 'class="inputbox" size="1"','value', 'text', $this->row->allowgroup);
						echo $html;
					?>
				</fieldset>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_FORCE_GROUPREG');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php
						if(!$this->row->force_groupregistration)
						{	
							$this->row->force_groupregistration = 0;
						}
						$arrforcegroups = array(JHTML::_('select.option', 0, 'No'),JHTML::_('select.option', 1, 'Yes'));
						$html =  JHTML::_('select.radiolist', $arrforcegroups, 'force_groupregistration', 'class="inputbox" size="1"','value', 'text', $this->row->force_groupregistration);
						echo $html;
					?>
				</fieldset>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_CLUB_ID');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php echo $this->Lists['locations']; ?>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_CAT_ID');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php echo $this->Lists['categories']; ?>
			</span>
			<br/>
			<?php
				$payment_method_hide = "";
				if($this->regpro_config['multiple_registration_button'] == 1){
					$payment_method_hide = "style='display:none'";
				}
			?>
			<span class="span3 y-offset no-gutter" <?php echo $payment_method_hide; ?>>
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_PAYMENT_ID');?>
			</span>
			<span class="span8 y-offset no-gutter" <?php echo $payment_method_hide; ?>>
				<?php echo $this->Lists['payment_method']; ?>
			</span>
		</div>

		<div class="tab-pane" id="registration_details">
			<script type="text/javascript">
				function user_enable_change(enableUser){
					if(enableUser == 1){
						document.getElementById('user_group_row').removeAttribute("style");
					} else document.getElementById('user_group_row').style.display = 'none';
				}

			</script>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_REGISTRA');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php
						if($this->row->registra == '')
						{
							$this->row->registra = 1;
						}
						$arrRegenable = array(JHTML::_('select.option', 0, 'No'),JHTML::_('select.option', 1, 'Yes'));
						$html =  JHTML::_('select.radiolist', $arrRegenable, 'registra', 'class="inputbox" size="1"','value', 'text', $this->row->registra);
						echo $html;
					?>
				</fieldset>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY_EMAILS');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text"id="notifyemails" name="notifyemails" value="<?php echo $this->row->notifyemails; ?>" maxlength="255" class="pull-left">
				<img class="editlinktip hasTip" title="<?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY_EMAILS_DESC');?>" src="components/com_registrationpro/assets/images/info_icon_24x24.png" border="0" width="24" height="24" />
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text"id="notifydate" name="notifydate" value="<?php echo $this->row->notifydate; ?>" maxlength="3" class="pull-left">
				<?php echo "&nbsp;".JText::_('ADMIN_EVENTS_REGISTRA_NOTIFYDAYS');?>
				<a href="index.php?option=com_registrationpro&view=emails" target="_blank">
					<img class="editlinktip hasTip" title="<?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY_NOTICE');?>" src="components/com_registrationpro/assets/images/info_icon_24x24.png" border="0" width="24" height="24" />
				</a>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_REGFORM_SELECT');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php echo $this->Lists['forms']; ?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_REGSTART');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo JHTML::_('calendar', $this->row->regstart, 'regstart', 'regstart', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
				?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_REGSTART_TIME');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="regstarttimes" value="<?php echo substr($this->row->regstarttimes, 0, 5);?>" maxlength="5">
				&nbsp;&nbsp;<b><?php echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE');?></b>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_REGSTOP');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo JHTML::_('calendar', $this->row->regstop, 'regstop', 'regstop', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
				?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_REGSTOP_TIME');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text"name="regstoptimes" value="<?php echo substr($this->row->regstoptimes, 0, 5);?>" maxlength="5">
				&nbsp;&nbsp;<b><?php echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE');?></b>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_ENABLE_CREATE_USER');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php
						if($this->row->enable_create_user == '')
						{
							$this->row->enable_create_user = 0;
						}
						$arrRegenable = array(JHTML::_('select.option', 0, 'No'),JHTML::_('select.option', 1, 'Yes'));
						$html =  JHTML::_('select.radiolist', $arrRegenable, 'enable_create_user', 'class="inputbox" size="1" onChange="javascript:user_enable_change(this.value)"','value', 'text', $this->row->enable_create_user);
						echo $html;
					?>
				</fieldset>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter" id="user_group_row" <?php if($this->row->enable_create_user==0){ echo 'style="display:none;"';} ?>>
				<?php echo JText::_('ADMIN_EVENTS_ENABLED_USER_GROUP');?>
			</span>
			<span class="span8 y-offset no-gutter" <?php if($this->row->enable_create_user==0){ echo 'style="display:none;"';} ?>>
				<select name="enabled_user_group">
					<?php
						foreach($this->userGroups as $val){
							$selected = '';
							if($this->row->enabled_user_group==$val->id) $selected = 'selected="selected"';
							echo '<option value="'.$val->id.'"  '.$selected.'>'.$val->title.'</option>';
						}
					?>
				</select>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_PDFIMAGE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<img id="cat_pdfimage" src="<?php echo $pdfImgName; ?>" class="thumbnail">
				<div id="config_pdfimage_form">
					<form id="config_pdfimageForm" enctype="multipart/form-data" method="POST" style="margin:0px;padding:0px;">
					<input type="file" name="pdffileToUpload_selector" id="pdffileToUpload_selector" />
					</form>
					<br />
					<div id="pdfresponse"></div>
					<br />
					<?php if (isset($this->row->pdfimage) && ($this->row->pdfimage == '1')) { $btn_deletepdf_style="visible"; } else { $btn_deletepdf_style="hidden"; }?>
					<a href="javascript:void(0);" onclick="deletePdfImage();" class="toolbar btn btn-small btn-danger" id="btn_delete_pdfimage" style="visibility:<?php echo $btn_deletepdf_style?>;">Delete Image</a>
				</div>
			</span>
			<script language="JavaScript">function submitForm(){document.config_pdfimageForm.submit();}</script> 
			<script language="JavaScript">
				function deletePdfImage() {
					var img = $('#cat_pdfimage');
					var src = img.attr('src');
					d = new Date();
					img.attr('src', "<?php echo $imgPrefixSystem;?>" + 'nopdfimage_720x240.jpg?dummy=' + d.getTime());
					var btn_delete_image = $('#btn_delete_pdfimage');
					btn_delete_image.attr('style', 'visibility:hidden;')
					var event_image = $('#event_pdfimage');
					event_image.val('del')
				}
				
				function refreshPdfImage() {
					var img = $('#cat_pdfimage');
					var src = img.attr('src');
					d = new Date();
					img.attr('src', "<?php echo $imgPrefixSystem;?>" + "<?php echo $pdfImgTemp;?>" + '?fck=' + d.getTime());
					var btn_delete_image = $('#btn_delete_pdfimage');
					btn_delete_image.attr('style', 'visibility:visible;')
					var event_image = $('#event_pdfimage');
					event_image.val('new')
				}
				
				jQuery('document').ready(function(){
					var input = document.getElementById("pdffileToUpload_selector");
					var formdata = false;
					if (window.FormData) {
						formdata = new FormData();
					}
					input.addEventListener("change", function (evt) {
						var i = 0, len = this.files.length, img, reader, file;

						for ( ; i < len; i++ ) {
							file = this.files[i];

							if (!!file.type.match(/image.*/)) {
								if ( window.FileReader ) {
									reader = new FileReader();
									reader.readAsDataURL(file);
								}

								if (formdata) {
									formdata.append("image", file);
									formdata.append("pdfpath2save", "<?php echo $imgPrefixSystem;?>" + "<?php echo $pdfImgTemp;?>");
									var img = document.getElementById("cat_pdfimage");
									img.src = "";
									jQuery('div#pdfresponse').html('<br />Loading...');
									jQuery.ajax({
										url: "components/com_registrationpro/helpers/imager.php",
										type: "POST",
										data: formdata,
										processData: false,
										contentType: false,
										success: function (res) {
											jQuery('div#pdfresponse').html("Successfully uploaded");
											refreshPdfImage();
										}
									});
								}
							}
							else { alert('Not a vaild image format!'); }
						}
					}, false);
				});
			</script>

			<?php echo JHTML::_('form.token');?>
			<input type="hidden" name="option" value="com_registrationpro" />

			<?php if ($this->task != "copy") { ?>
				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<?php } else { ?>
				<input type="hidden" name="copy" value="1" />
				<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
			<?php } ?>
			<input type="hidden" name="controller" value="events" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
		</div>

		<?php if ($this->row->ordering < 10000) { ?>
		<div class="tab-pane" id="recurring_details">
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('RECURRENCE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<select id="recurrence_select" name="recurrence_select" onchange="output_recurrencescript();" >
					<option value="0"><?php echo JText::_('NOTHING');?></option>
					<option value="1"><?php echo JText::_('DAYLY');?></option>
					<option value="2"><?php echo JText::_('WEEKLY');?></option>
					<option value="3"><?php echo JText::_('MONTHLY');?></option>
					<option value="4"><?php echo JText::_('WEEKDAY');?></option>
					<option value="5"><?php echo JText::_('DATES');?></option>
				</select>
			</span>
			<br/>
			<span class="span12 y-offset no-gutter" id="recc" style="display:none;">
				<span id="recc_label" class="span3 y-offset no-gutter">
					<p id="repeat_every" style="display:none;"><?php echo JText::_('RECURRENCE_REPEAT_EVERY');?></p>
					<p id="repeat_on" style="display:none;"><?php echo JText::_('RECURRENCE_REPEAT_ON');?></p>
				</span>
				<span id="recurrence_output" class="span6 no-gutter y-offset">
				</span>
			</span>
			<br/>
			<span class="span12 y-offset no-gutter" id="counter_row" style="display:none;">
				<span class="span3 y-offset no-gutter">
					<?php echo JText::_('RECURRENCE_COUNTER');?>
				</span>
				<span class="span6 y-offset no-gutter">
					<?php 
						echo JHTML::_('calendar', ($this->row->recurrence_counter <> 0000-00-00)? $this->row->recurrence_counter: JText::_('UNLIMITED' ), "recurrence_counter", "recurrence_counter");
					?>
					&nbsp;
					<a href="#" onclick="include_unlimited('<?php echo JText::_('UNLIMITED');?>'); return false;" title="<?php echo JText::_('UNLIMITED');?>">
						<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/unlimited.png" width="20" height="20" alt="<?php echo JText::_('UNLIMITED');?>" />
					</a>
				</span>
			</span>
			<input type="hidden" name="recurrence_id" id="recurrence_id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="recurrence_type" id="recurrence_type" value="<?php echo $this->row->recurrence_type; ?>" />
			<input type="hidden" name="recurrence_number" id="recurrence_number" value="<?php echo $this->row->recurrence_number; ?>" />
			<input type="hidden" name="recurrence_weekday" id="recurrence_weekday" value="<?php echo $this->row->recurrence_weekday; ?>" />
			<script type="text/javascript">
				var $select_output = new Array();
				$select_output[1] = "<?php echo JText::_('OUTPUT_DAY');?>";
				$select_output[2] = "<?php echo JText::_('OUTPUT_WEEK');?>";
				$select_output[3] = "<?php echo JText::_('OUTPUT_MONTH');?>";
				$select_output[4] = "<?php echo JText::_('OUTPUT_WEEKDAY');?>";
				$select_output[5] = "<?php echo JText::_('OUTPUT_DATES');?>";

				var $weekday = new Array();
				$weekday[0] = "<?php echo JText::_('MONDAY');?>";
				$weekday[1] = "<?php echo JText::_('TUESDAY');?>";
				$weekday[2] = "<?php echo JText::_('WEDNESDAY');?>";
				$weekday[3] = "<?php echo JText::_('THURSDAY');?>";
				$weekday[4] = "<?php echo JText::_('FRIDAY');?>";
				$weekday[5] = "<?php echo JText::_('SATURDAY');?>";
				$weekday[6] = "<?php echo JText::_('SUNDAY');?>";

				var $before_last = "<?php echo JText::_('BEFORE_LAST');?>";
				var $last = "<?php echo JText::_('LAST');?>";

				var cal1 = new CalendarPopup();
				start_recurrencescript();
			</script>
		</div>
		<?php } ?>
		
		<div class="tab-pane" id="event_metadata">
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_METADATA_DESCRIPTION');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<textarea name="metadescription" id="metadescription" style="width:100%;height:100px !important;">
					<?php echo $this->row->metadescription; ?>
				</textarea>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_METADATA_KEYWORDS');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<textarea name="metakeywords" id="metakeywords" style="width:100%;height:100px !important;">
					<?php echo $this->row->metakeywords; ?>
				</textarea>
			</span>
			<br/>
			<span class="span3 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_METADATA_ROBOTS');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="metarobots" id="metarobots" value="<?php echo $this->row->metarobots; ?>"/>
			</span>
		</div>
</form>

<?php if($this->task != 'copy') { ?>
<!----------------------------------------------- Event Ticket section  --------------------------------------->
	<div class="tab-pane" id="event_ticket">
		<span class="span12 y-offset no-gutter" id="ajaxmessagebox"></span>
		<form name="paymentform" id="paymentform" action="" method="post">
			<!-- This section displayes the ticket form-->
			<div class="span5 y-offset no-gutter pull-left" id="ticket_form">
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_EVENTS_PAYMENT_TICKET_NAME');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<input  type="text" name="product_name" id="product_name" class="inputbox" size="20" value="<?php echo $ticket_name;?>"/>
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_PRICE');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<input id="add_price" type="text" name="product_price" id="product_price" class="inputbox pull-left" size="8" onblur="calculate_tot_amt();" value="<?php echo $ticket_price;?>" style="float:left;" />
					<div id="curr_sign"><b><?php echo $this->regpro_config['currency_sign'];?></b></div>
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_TAX');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<input id="tax" type="text" class="inputbox" name="tax" id="tax" size="4" onblur="calculate_tot_amt();" value="<?php echo $ticket_tax;?>"/>
					<b style="font-size:18px;font-style:bold;font-weight:700;margin-left:5px;">%</b>
					<?php 
						if(trim($total_price) == '') {
							$ttlp = "Total Price with Taxes : ".$total_price . " " . $this->regpro_config['currency_sign'];
						} else {
							$ttlp = "Total Price with Taxes : 0 " . $this->regpro_config['currency_sign'];
						}
					?>
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					&nbsp;
				</span>
				<span class="span8 y-offset no-gutter" id="totval">
					<?php echo $ttlp?>
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_QTY');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<input type="text" name="product_quantity" id="product_quantity" class="inputbox" size="8px" value="<?php echo ($product_quantity!="" ? $product_quantity:0);?>" onblur="eventTickets();" />
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_DESC');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<textarea name="product_description" id="product_description" class="inputbox" cols="20" rows="2">
						<?php echo $ticket_desc;?>
					</textarea>
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_START_DATE');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<?php 
						echo JHTML::_('calendar', $this->row->ticket_start, 'ticket_start', 'ticket_start', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));
					?>
				</span>
				<br/>
				<span class="span4 y-offset no-gutter">
					<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_END_DATE');?>
				</span>
				<span class="span8 y-offset no-gutter">
					<?php 
						echo JHTML::_('calendar', $this->row->ticket_end, 'ticket_end', 'ticket_end', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25', 'readonly'=>'true', 'maxlength'=>'19'));
					?>
				</span>
				<br/>
				<span class="span8 y-offset no-gutter">
					<button class="btn btn-small btn-success" id="save_ticket">Add</button>
					<input type="button" class="button btn btn-small btn-inverse" value="Reset" onclick="reset_form();" />
				</span>
			</div>
			
			<!-- This section displays the tickets -->
			<div class="span6 y-offset no-gutter" >
				<span class="span12 y-offset no-gutter">
					<a class="toolbar btn btn-small btn-success" id="edit_ticket" href="javascript:void(0);">
						<?php echo JText::_('ADMIN_EVENTS_EDIT');?>
					</a>
					<a class="toolbar btn btn-small btn-danger" id="remove_ticket" href="javascript:void(0);">
						<?php echo JText::_('ADMIN_EVENTS_REMOVE');?>
					</a>
				</span>
			</div>
			<div class="span6 y-offset no-gutter" id="list_ticket">
				<span class="span12 y-offset no-gutter">
					<table class="table_tickets">
						<tr id="table_tickets_header">
							<td><input type="checkbox" name="toggle" value="" onClick="paymentcheckAll(<?php echo count( $this->row->products);?>);" /></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_NAME');?></strong></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_PRICE');?></strong></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TAX');?></strong></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL');?></strong></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_QTY');?></strong></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_START');?></strong></td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_END');?></strong></td>
							<td colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_LIST_ORDER');?></strong></td>
						</tr>

						<?php
							$n = count($this->row->products);
							$i = 0;
							$k = 0;

							foreach ($this->row->products as $product) {
								if($product->type == 'E'){
							?>

								<tr>
									<td><input id="cb<?php echo $i?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $product->id?>" name="cid[]"></td>
									<td><?php echo $product->product_name;?></td>
									<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->product_price; ?></td>
									<td style="text-align:right"><?php echo $product->tax. '&nbsp;%';?></td>
									<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->total_price; ?></td>
									<td style="text-align:right"><?php echo $product->product_quantity; ?></td>
									<td style="text-align:right"><?php echo $product->ticket_start; ?></td>
									<td style="text-align:right"><?php echo $product->ticket_end; ?></td>
									<td width=20><?php
										if ($i > 0) { ?>
											<a href="javascript: void(0);" id="orderuppayments" onclick="return payment_uporder('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
									  <?php
										} ?>
									</td>
									<td width=20><?php
										if ($i < $n-1) { ?>
											<a href="javascript: void(0);" id="orderdownpayments" onclick="return payment_downorder('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
									  <?php
										}?>
									</td>
								</tr>
							<?php
									}
								$i++;
							}

							if(count($this->row->products) <= 0)
								echo "<tr><td colspan='9' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
							?>
					</table>
				</span>
			</div>
				<?php
				echo JHTML::_('form.token' );
				if($this->row->id > 0) echo "<input type=\"hidden\" name=\"regpro_dates_id\" value=\"".$this->row->id."\" />\n";
			?>
			
			<input type="hidden" value="0" name="total_price" id="total_price" />
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="events" />
			<input type="hidden" name="task" id="pform_task" value="" />
			<input type="hidden" name="type" value="E" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" value="" name="titel" id="titel" />
			<input type="hidden" value="" name="status" id="status" />
			<input type="hidden" value="" name="dates" id="dates" />
			<input type="hidden" value="" name="times" id="times" />
			<input type="hidden" value="" name="enddates" id="enddates" />
			<input type="hidden" value="" name="endtimes" id="endtimes" />
			<input type="hidden" value="" name="max_attendance" id="max_attendance" />
			<input type="hidden" value="" name="shortdescription" id="shortdescription" />
			<input type="hidden" value="" name="datdescription" id="datdescription" />
			<input type="hidden" value="" name="terms_conditions" id="terms_conditions" />
			<input type="hidden" value="" name="access" id="access" />
			<input type="hidden" value="" name="published" id="published" />
			<input type="hidden" value="" name="locid" id="locid" />
			<input type="hidden" value="" name="catsid" id="catsid" />
			<input type="hidden" value="" name="registra" id="registra" />
			<input type="hidden" value="" name="notifydate" id="notifydate" />
			<input type="hidden" value="" name="form_id" id="form_id" />
			<input type="hidden" value="" name="regstart" id="regstart" />
			<input type="hidden" value="" name="regstop" id="regstop" />
			<input type="hidden" value="" name="allowgroup" id="allowgroup" />
		</form>
	<script language="javascript">

	function checks_num(cb_name) {
		var cnt = 0;
		for (i = 0; i < 100; i++) {
			var cb = document.getElementById(cb_name + i);
			if ((cb) && (cb.checked)) cnt++;
		}
		return cnt;
	}
	
	function calculate_tot_amt() {
		var frm = document.paymentform;
		var price;
		var tax;
		var totalprice;
		var curr_sign = $('#curr_sign').html();
		if(frm.add_price.value == ""){
		}else if(!isNaN(frm.add_price.value)){
		}else{
			alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_PRICE_ERROR');?>');
			frm.add_price.value = "";
			frm.add_price.focus();
			return false;
		}
		if(frm.tax.value == ""){
		}else if(!isNaN(frm.tax.value)){
		}else{
			alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_TAX_ERROR');?>');
			frm.tax.value = "";
			frm.tax.focus();
			return false;
		}
		if(frm.tax.value != '' && !isNaN(frm.tax.value)){
			price 	= frm.product_price.value;
			tax 	= frm.tax.value;
			totalprice= (price * tax) / 100;
			price = Number(price)+Number(totalprice);
			price = Math.round(price * 100) / 100;
		}else{
			price = frm.total_price.value = frm.product_price.value;
			price = Math.round(price * 100) / 100;
			document.getElementById("totval").innerHTML = "Total Price with Taxes : " + price + " " + curr_sign;
		}

		frm.total_price.value= price;
		document.getElementById("totval").innerHTML = "Total Price with Taxes : " + price + " " + curr_sign;
	}

	function reset_form(){
		var frm = document.paymentform;
		frm.product_name.value = "";
		frm.product_price.value = "";
		frm.product_price.disabled = '';
		frm.tax.value = "";
		frm.tax.disabled = '';
		frm.total_price.value = "";
		frm.product_quantity.value = "0";
		frm.product_description.value = "";
		frm.ticket_start.value = "";
		frm.ticket_end.value = "";
		if (frm.id) frm.id.value = "";
		var curr_sign = $('#curr_sign').html();
		document.getElementById("totval").innerHTML = "Total Price with Taxes : 0 " + curr_sign;
		var save_ticket = $('#save_ticket').text('Add');
	}

	function save_ticket(e) {
		e.preventDefault();
		add_ticket(e);
		reset_form();
	}
	
	function bindCalAfterAjax( ID , argus ) {
		var default_argus = {
			inputField: ID,          // Id of the input field
			ifFormat:   "%Y-%m-%d",  // Format of the input field
			button:     ID + "_img", // Trigger for the calendar (button ID)
			align:      "Tl",        // Alignment (defaults to "Bl")
			singleClick: true,
			firstDay: 0
		}
		$.extend( default_argus, argus );
		Calendar.setup( default_argus );
	}

	function paymentcheckAll( n, fldName ) {
	  if (!fldName) { fldName = 'cb'; }
		var f = document.paymentform;
		var c = f.toggle.checked;
		var n2 = 0;
		for (i=0; i < n; i++) {
			cb = eval( 'f.' + fldName + '' + i );
			if (cb) {
				cb.checked = c;
				n2++;
			}
		}
		f.boxchecked.value = 0;
		if (c) { f.boxchecked.value = n2; }
	}
	
	function payment_uporder(id) {
		var frm = document.getElementById("paymentform");
		cb = eval('frm.' + id );
		if (cb) {
			for (i = 0; true; i++) {
				cbx = eval('frm.cb'+i);
				if (!cbx) break;
				cbx.checked = false;
			}
			cb.checked = true;
			frm.boxchecked.value = 1;

			var box = $('#ajaxmessagebox');
			var log = $('#list_ticket');
			var pform_task = $('#pform_task');
			pform_task.attr('value', 'orderuppayments')
			var frmdata = false;
			if (window.FormData) {frmdata = new FormData(frm);}

			jQuery.ajax({
				url: "",
				type: "POST",
				data: frmdata,
				processData: false,
				contentType: false,
				beforeSend: function() {
					box.attr('style', 'display="block"');
					box.fadeIn();
					box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
				},
				success: function (res) {
					box.text('Done');
					box.fadeOut();
					log.html(res);
				}
			});
		}
	}

	function payment_downorder(id) {
		var frm = document.getElementById("paymentform");
		cb = eval('frm.' + id );
		if (cb) {
			for (i = 0; true; i++) {
				cbx = eval('frm.cb'+i);
				if (!cbx) break;
				cbx.checked = false;
			}
			cb.checked = true;
			frm.boxchecked.value = 1;

			var box = $('#ajaxmessagebox');
			var log = $('#list_ticket');
			var pform_task = $('#pform_task');
			pform_task.attr('value', 'orderdownpayments')
			var frmdata = false;
			if (window.FormData) {frmdata = new FormData(frm);}

			jQuery.ajax({
				url: "",
				type: "POST",
				data: frmdata,
				processData: false,
				contentType: false,
				beforeSend: function() {
					box.attr('style', 'display="block"');
					box.fadeIn();
					box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
				},
				success: function (res) {
					box.text('Done');
					box.fadeOut();
					log.html(res);
				}
			});
		}
	}

	function add_ticket(e) {
		e.preventDefault();
		calculate_tot_amt();
		var log = $('#list_ticket');
		var box = $('#ajaxmessagebox');
		var form = document.adminForm;

		document.paymentform.titel.value 			= form.titel.value;
		document.paymentform.status.value 			= form.status.value;
		document.paymentform.dates.value 			= form.dates.value;
		document.paymentform.times.value 			= form.times.value;
		document.paymentform.enddates.value 		= form.enddates.value;
		document.paymentform.endtimes.value 		= form.endtimes.value;
		document.paymentform.max_attendance.value 	= form.max_attendance.value;
		document.paymentform.shortdescription.value = form.shortdescription.value;
		document.paymentform.datdescription.value 	= form.datdescription.value;
		document.paymentform.terms_conditions.value = form.terms_conditions.value;
		document.paymentform.access.value 			= form.access.value;
		document.paymentform.published.value 		= form.published.value;
		document.paymentform.locid.value 			= form.locid.value;
		document.paymentform.catsid.value 			= form.catsid.value;
		document.paymentform.registra.value 		= form.registra.value;
		document.paymentform.notifydate.value 		= form.notifydate.value;
		document.paymentform.form_id.value 			= form.form_id.value;
		document.paymentform.regstart.value 		= form.regstart.value;
		document.paymentform.regstop.value 			= form.regstop.value;
		document.paymentform.allowgroup.value 		= form.allowgroup.value;
		
		if(!validateForm(form,false,false,false,false)){
			alert('Please fill up the required fields of event first');
			return false;
		}
		
		// check the date and time format
		if (!form.dates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_('ADMIN_EVENTS_DELFORM')." "; ?>");
			form.dates.focus();
		} else if (!form.times.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
			alert("<?php echo JText::_('EVENTS_DEL_TIME_FORM')." "; ?>");
			form.times.focus();
		} else if (!form.enddates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
			alert("<?php echo JText::_('ADMIN_EVENTS_ENDDATE_FORMAT')." "; ?>");
			form.enddates.focus();
		} else if (!form.endtimes.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
			alert("<?php echo JText::_('EVENTS_DEL_ENDTIME_FORM')." "; ?>");
			form.endtimes.focus();
		} else if (form.registra.value == 1) {
			var ticketStart = document.getElementById('ticket_start').value;
			var ticketEnd	= document.getElementById('ticket_end').value;
			ticketStart		= ticketStart.split('-');
			ticketEnd		= ticketEnd.split('-');
			if(ticketStart == ''){
				alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_TICKET_PUBLISH_DATE_ERROR');?>');
				document.getElementById('ticket_start').focus();
				return false;
			} else if(ticketEnd == ''){
				alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_TICKET_UNPUBLISH_DATE_ERROR');?>');
				document.getElementById('ticket_end').focus();
				return false;
			} else if(ticketEnd[0] < ticketStart[0]){
				alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_TICKET_UNPUBLISH_ERROR');?>');
				document.getElementById('ticket_end').focus();
				return false;
			} else if ((ticketEnd[0] == ticketStart[0]) && (ticketEnd[1] < ticketStart[1])){
				alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_TICKET_UNPUBLISH_ERROR');?>');
				document.getElementById('ticket_end').focus();
				return false;
			} else if ((ticketEnd[0] == ticketStart[0])
						&& (ticketEnd[1] == ticketStart[1])
						&& (ticketEnd[2] < ticketStart[2])){
				alert('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_TICKET_UNPUBLISH_ERROR');?>');
				document.getElementById('ticket_end').focus();
				return false;
			}

			if(form.form_id.value == 0) {
				alert("<?php echo JText::_('EVENTS_REGISTER_FORM')." "; ?>");
				form.form_id.focus();
			}
			if(form.regstart.value == "" || form.regstart.value == "0000-00-00"){
				alert("<?php echo JText::_('EVENTS_REGISTER_START_DATE')." "; ?>");
				form.regstart.focus();
			}
			if(form.regstop.value == "" || form.regstop.value == "0000-00-00"){
				alert("<?php echo JText::_('EVENTS_REGISTER_END_DATE')." "; ?>");
				form.regstop.focus();
			}
			if (document.paymentform.product_name.value=="") {
				alert("<?php echo JText::_('EVENTS_TICKET_NAME_EMPT')." "; ?>");
				document.paymentform.product_name.focus();
				return false;
			}
			if (document.paymentform.product_price.value == "") {
				alert("<?php echo JText::_('EVENTS_TICKET_PRICE_EMPT')." "; ?>");
				document.paymentform.product_price.focus();
				return false;
			} else {
				if (!document.paymentform.product_price.value.match(/[0-9]/gi)) {
					alert("<?php echo JText::_('EVENT_TICKET_PRICE_FORMAT')." "; ?>");
					document.paymentform.product_price.focus();
					return false;
				}
			}
			
			if (document.paymentform.product_quantity.value == "") {
				alert("<?php echo JText::_('EVENTS_TICKET_QTY_EMPT')." "; ?>");
				document.paymentform.product_quantity.focus();
				return false;
			} else {
				if (!document.paymentform.product_quantity.value.match(/[0-9]/gi)) {
					alert("<?php echo JText::_('EVENT_TICKET_QTY_FORMAT')." "; ?>");
					document.paymentform.product_quantity.focus();
					return false;
				}
			}
		}

		var pform_task = $('#pform_task');
		pform_task.attr('value', 'add_ticket')
		var frm = document.getElementById("paymentform");
		var frmdata = false;
		if (window.FormData) frmdata = new FormData(frm);

		jQuery.ajax({
			url: "",
			type: "POST",
			data: frmdata,
			processData: false,
			contentType: false,
			beforeSend: function() {
				box.attr('style', 'display="block"');
				box.fadeIn();
				box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
			},
			success: function (res) {
				box.text('Saved');
				box.fadeOut();
				log.html(res);
			}
		});
		reset_form();
	}
	
	$('#edit_ticket').click(function(e) {
		if(checks_num('cb') > 1) {
			alert('Please select just ONE ticket');
			return false;
		}
		if(checks_num('cb') == 0) {
			alert('Please select a ticket first');
			return false;
		}
		var check = checkLink('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_EDITLINK_ERROR');?>');
		if(check == false){	return false; }

		var log = $('#ticket_form');
		var box = $('#ajaxmessagebox');
		var pform_task = $('#pform_task');
		pform_task.attr('value', 'edit_ticket')
		var frm = document.getElementById("paymentform");
		var frmdata = false;
		if (window.FormData) {frmdata = new FormData(frm);}

		jQuery.ajax({
			url: "",
			type: "POST",
			data: frmdata,
			processData: false,
			contentType: false,
			beforeSend: function() {
				box.attr('style', 'display="block"');
				box.fadeIn();
				box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
			},
			success: function (res) {
				box.text('Done');
				box.fadeOut();
				log.html(res);
				bindCalAfterAjax( "ticket_start" );
				bindCalAfterAjax( "ticket_end" );
			}
		});
	});

	$('#remove_ticket').click(function(e) {
		var check = checkLink('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_EDITLINK_ERROR');?>');
		if(check == false){ return false; }

		var log = $('#list_ticket');
		var box = $('#ajaxmessagebox');
		var pform_task = $('#pform_task');
		pform_task.attr('value', 'remove_ticket')
		var frm = document.getElementById("paymentform");
		var frmdata = false;
		if (window.FormData) {frmdata = new FormData(frm);}

		jQuery.ajax({
			url: "",
			type: "POST",
			data: frmdata,
			processData: false,
			contentType: false,
			beforeSend: function() {
				box.attr('style', 'display="block"');
				box.fadeIn();
				box.text('<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS');?>');
			},
			success: function (res) {
				box.text('<?php echo JText::_('ADMIN_RECORD_DELETED');?>');
				box.fadeOut();
				log.html(res);
			}
		});
		
		reset_form();
	});

	$('#save_ticket').click(function(e) {
		add_ticket(e);
	});
	
	reset_form();
	
	</script>
		
	</div>

<?php if($this->row->id > 0) { ?>

<!--------------------- Additional Event Ticket section   --------------->
<div class="tab-pane" id="event_additional_ticket">
	<span class="span12 y-offset no-gutter" id="ajaxmessagebox_add"></span>
	<form name="paymentform_add" id="paymentform_add" action="" method="post">
		
		<!-- This section is for displaying the products form -->
		<div class="span5 y-offset no-gutter" id="ticket_form_add">
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . " Product Name :"?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input  type="text" name="product_name" id="product_name" class="inputbox" size="20" value="<?php echo $ticket_name;?>"/>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_PRICE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input id="add_price" type="text" name="product_price" id="product_price" class="inputbox" size="8" onblur="calculate_tot_amt_add();" value="<?php echo $ticket_price;?>" style="float:left;" />
				<div id="curr_sign_add"><b><?php echo $this->regpro_config['currency_sign'];?></b></div>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_TAX');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input id="tax" type="text" class="inputbox" name="tax" id="tax" size="4" onblur="calculate_tot_amt_add();" value="<?php echo $ticket_tax;?>"/>
				<b style="font-size:18px;font-style:bold;font-weight:700;margin-left:5px;">%</b>
				<?php 
					if(trim($total_price) == '') {
						$ttlp = "Total Price with Taxes : ".$total_price . " " . $this->regpro_config['currency_sign'];
					} else {
						$ttlp = "Total Price with Taxes : 0 " . $this->regpro_config['currency_sign'];
					}
				?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				&nbsp;
			</span>
			<span class="span8 y-offset no-gutter" id="totval_add">
				<?php echo $ttlp;?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_QTY');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="product_quantity" id="product_quantity" class="inputbox" size="8px" value="<?php echo ($product_quantity!="" ? $product_quantity:0);?>" onblur="eventTickets();" />
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_DESC');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<textarea name="product_description" id="product_description" class="inputbox" cols="20" rows="2">
					<?php echo $ticket_desc;?>
				</textarea>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_PROD_START_DATE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php 
					echo JHTML::_('calendar', $this->row->ticket_start, 'ticket_start', 'ticket_start_prod', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));
				?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_PROD_END_DATE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<?php
					echo JHTML::_('calendar', $this->row->ticket_end, 'ticket_end', 'ticket_end_prod', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));
				?>
			</span>
			<br/>
			<span class="span12 y-offset no-gutter">
				<button class="btn btn-small btn-success" id="save_ticket_add">Add</button>
				<input type="button" class="button btn btn-small btn-inverse" value="Reset" onclick="reset_form_add();" />
			</span>
		</div>
		
		<!-- This section to show the listing of the product -->
		<div class="span6 y-offset no-gutter" id="list_ticket_add">
			<span class="span12 y-offset no-gutter">
				<a class="toolbar btn btn-small btn-success" id="edit_prod" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_EDIT');?>
				</a>
				<a class="toolbar btn btn-small btn-danger" id="remove_prod" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_REMOVE');?>
				</a>
			</span>
			<span class="span12 y-offset no-gutter">
				<table class="table_tickets">
					<tr id="table_tickets_header">
						<td><input type="checkbox" name="toggle" value="" onClick="paymentcheckAll_add(<?php echo count( $this->row->products);?>);" /></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_NAME');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_PRICE');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TAX');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_QTY');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_START');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_END');?></strong></td>
						<td colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_LIST_ORDER');?></strong></td>
					</tr>

						<?php
						$n = count($this->row->products);
						$i = 0;
						$k = 0;

						foreach ($this->row->products as $product) {
							if($product->type == 'A'){
						?>

						<tr>
							<td><input id="cb_add<?php echo $i;?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $product->id;?>" name="cid[]"></td>
							<td><?php echo $product->product_name;?></td>
							<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->product_price; ?></td>
							<td style="text-align:right"><?php echo $product->tax. '&nbsp;%';?></td>
							<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->total_price; ?></td>
							<td style="text-align:right"><?php echo $product->product_quantity; ?></td>
							<td style="text-align:right"><?php echo $product->ticket_start; ?></td>
							<td style="text-align:right"><?php echo $product->ticket_end; ?></td>
							<td style="text-align:right">
							<?php
								if ($i > 0) { ?>
									<a href="javascript:void(0);" id="orderuppayments" onclick="return payment_uporder_add('cb_add<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
							  <?php
								} ?>
							</td>
							<td style="text-align:left"><?php
								if ($i < $n-1) { ?>
									<a href="javascript:void(0);" id="orderdownpayments" onclick="return payment_downorder_add('cb_add<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
							  <?php
								}?>
							</td>
						</tr>
						<?php
								}
							$i++;
						}

						if(count($this->row->products) <= 0)
							echo "<tr><td colspan='9' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
						?>
				</table>
			</span>
		</div>
		<?php echo JHTML::_('form.token');?>
		<input type="hidden" value="0" name="total_price" id="total_price" />
		<input type="hidden" name="option" value="com_registrationpro" />
		<input type="hidden" name="controller" value="events" />
		<input type="hidden" name="task" id="pform_task_add" value="" />
		<input type="hidden" value="saveticket" name="action" id="action" />
		<input type="hidden" name="regpro_dates_id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="type" value="A" />
	</form>
</div>

		<script language="javascript">
			function calculate_tot_amt_add() {
				var frm = document.paymentform_add;
				var price;
				var tax;
				var totalprice;
				var curr_sign = $('#curr_sign_add').html();
				if(frm.add_price.value == ""){}
				else if(!isNaN(frm.add_price.value)){}
				else{
					alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_PRICE_ERROR');?>');
					frm.add_price.value = "";
					frm.add_price.focus();
					return false;
				}
				if(frm.tax.value == ""){}
				else if(!isNaN(frm.tax.value)){}
				else{
					alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_PRICE_ERROR');?>');
					frm.tax.value = "";
					frm.tax.focus();
					return false;
				}

				if(frm.tax.value != '' && !isNaN(frm.tax.value)){
					price = frm.product_price.value;
					tax = frm.tax.value;
					totalprice= (price * tax) / 100;
					price = Number(price)+Number(totalprice);
					price = Math.round(price * 100) / 100;
				}else{
					price = frm.total_price.value = frm.product_price.value;
					price = Math.round(price * 100) / 100;
					document.getElementById("totval_add").innerHTML = "Total Price with Taxes : " + price + " " + curr_sign;
				}

				frm.total_price.value = price;
				document.getElementById("totval_add").innerHTML = "Total Price with Taxes : " + price + " " + curr_sign;
			}

			function reset_form_add(){
				var frm = document.paymentform_add;
				frm.product_name.value = "";
				frm.product_price.value = "";
				frm.product_price.disabled = '';
				frm.tax.value = "";
				frm.tax.disabled = '';
				frm.total_price.value = "";
				frm.product_quantity.value = "0";
				frm.product_description.value = "";
				frm.ticket_start.value = "";
				frm.ticket_end.value = "";
				if (frm.id) frm.id.value = "";
				var curr_sign = $('#curr_sign_add').html();
				document.getElementById("totval_add").innerHTML = "Total Price with Taxes : 0 " + curr_sign;
				var save_ticket = $('#save_ticket_add').text('Add');
			}
			
			function save_product(e) {
				e.preventDefault();
				add_product(e);
				reset_form_add();
			}
			
			function paymentcheckAll_add( n, fldName ) {
			  if (!fldName) { fldName = 'cb_add'; }
				var f = document.paymentform_add;
				var c = f.toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					cb = eval( 'f.' + fldName + '' + i );
					if (cb) {
						cb.checked = c;
						n2++;
					}
				}
				f.boxchecked.value = 0;
				if (c) { f.boxchecked.value = n2; }
			}
			
			function payment_uporder_add(id) {
				var frm = document.getElementById("paymentform_add");
				cb = eval('frm.' + id );
				if (cb) {
					for (i = 0; true; i++) {
						cbx = eval('frm.cb_add' + i);
						if (!cbx) break;
						cbx.checked = false;
					}
					cb.checked = true;
					frm.boxchecked.value = 1;

					var box = $('#ajaxmessagebox_add');
					var log = $('#list_ticket_add');
					var pform_task = $('#pform_task_add');
					pform_task.attr('value', 'orderuppayments')
					var frmdata = false;
					if (window.FormData) {frmdata = new FormData(frm);}

					jQuery.ajax({
						url: "",
						type: "POST",
						data: frmdata,
						processData: false,
						contentType: false,
						beforeSend: function() {
							box.attr('style', 'display="block"');
							box.fadeIn();
							box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
						},
						success: function (res) {
							box.text('Done');
							box.fadeOut();
							log.html(res);
						}
					});
				}
			}

			function payment_downorder_add(id) {
				var frm = document.getElementById("paymentform_add");
				cb = eval('frm.' + id );
				if (cb) {
					for (i = 0; true; i++) {
						cbx = eval('frm.cb'+i);
						if (!cbx) break;
						cbx.checked = false;
					}
					cb.checked = true;
					frm.boxchecked.value = 1;

					var box = $('#ajaxmessagebox_add');
					var log = $('#list_ticket_add');
					var pform_task = $('#pform_task_add');
					pform_task.attr('value', 'orderdownpayments')
					var frmdata = false;
					if (window.FormData) {frmdata = new FormData(frm);}

					jQuery.ajax({
						url: "",
						type: "POST",
						data: frmdata,
						processData: false,
						contentType: false,
						beforeSend: function() {
							box.attr('style', 'display="block"');
							box.fadeIn();
							box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
						},
						success: function (res) {
							box.text('Done');
							box.fadeOut();
							log.html(res);
						}
					});
				}
			}

	function save_ticket_add(e) {
		e.preventDefault();
		calculate_tot_amt_add();
		var log = $('#list_ticket_add');
		var box = $('#ajaxmessagebox_add');
		var form = document.adminForm;

		var ticketStart = document.getElementById('ticket_start_prod').value;
		var ticketEnd   = document.getElementById('ticket_end_prod').value;
		ticketStart = ticketStart.split('-');
		ticketEnd = ticketEnd.split('-');
		if(ticketStart == ''){
			alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_PRODUCT_PUBLISH_DATE_ERROR');?>');
			document.getElementById('ticket_start_prod').focus();
			return false;
		}else if(ticketEnd == ''){
			alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_PRODUCT_UNPUBLISH_DATE_ERROR');?>');
			document.getElementById('ticket_end_prod').focus();
			return false;
		}else if(ticketEnd[0] < ticketStart[0]){
			alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_TICKET_UNPUBLISH_ERROR');?>');
			document.getElementById('ticket_end_prod').focus();
			return false;
		}else if ((ticketEnd[0] == ticketStart[0]) && (ticketEnd[1] < ticketStart[1])){
			alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_TICKET_UNPUBLISH_ERROR');?>');
			document.getElementById('ticket_end_prod').focus();
			return false;
		}else if ((ticketEnd[0] == ticketStart[0])
					&& (ticketEnd[1] == ticketStart[1])
					&& (ticketEnd[2] < ticketStart[2])){
			alert('<?php echo JText::_('ADMIN_EVENT_PRODUCT_TAB_TICKET_UNPUBLISH_ERROR');?>');
			document.getElementById('ticket_end_prod').focus();
			return false;
		}

		if (document.paymentform_add.product_name.value=="") {
			alert("<?php echo JText::_('EVENTS_PROD_NAME_EMPT')." "; ?>");
			document.paymentform_add.product_name.focus();
			return false;
		}
		if (document.paymentform_add.product_price.value=="") {
			alert("<?php echo JText::_('EVENTS_PROD_PRICE_EMPT')." "; ?>");
			document.paymentform_add.product_price.focus();
			return false;
		}
		if (document.paymentform_add.product_price.value!="") {
			if (!document.paymentform_add.product_price.value.match(/[0-9]/gi)) {
				alert("<?php echo JText::_('PROD_PRICE_FORMAT')." "; ?>");
				document.paymentform_add.product_price.focus();
				return false;
			}
		}
		if (document.paymentform_add.product_quantity.value=="") {
			alert("<?php echo JText::_('EVENTS_PROD_QTY_EMPT')." "; ?>");
			document.paymentform_add.product_quantity.focus();
			return false;
		}
		if (document.paymentform_add.product_quantity.value!="") {
			if (!document.paymentform_add.product_quantity.value.match(/[0-9]/gi)) {
				alert("<?php echo JText::_('PROD_QTY_FORMAT')." "; ?>");
				document.paymentform_add.product_quantity.focus();
				return false;
			}
		}
		
		var pform_task = $('#pform_task_add');
		pform_task.attr('value', 'add_ticket_add')
		var frm = document.getElementById("paymentform_add");
		var frmdata = false;
		if (window.FormData) {frmdata = new FormData(frm);}

		jQuery.ajax({
			url: "",
			type: "POST",
			data: frmdata,
			processData: false,
			contentType: false,
			beforeSend: function() {
				box.attr('style', 'display="block"');
				box.fadeIn();
				box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
			},
			success: function (res) {
				box.text('Saved');
				box.fadeOut();
				log.html(res);
			}
		});
		reset_form_add();
	}
			
	$('#edit_prod').click(function(e) {
		if(checks_num('cb_add') > 1) {
			alert('Please select just ONE product');
			return false;
		}
		if(checks_num('cb_add') == 0) {
			alert('Please select a product first');
			return false;
		}
		var check = checkLink('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_EDITLINK_ERROR');?>');
		if(check == false){	return false; }

		var log = $('#ticket_form_add');
		var box = $('#ajaxmessagebox_add');
		var pform_task = $('#pform_task_add');
		pform_task.attr('value', 'edit_ticket_add')
		var frm = document.getElementById("paymentform_add");
		var frmdata = false;
		if (window.FormData) {frmdata = new FormData(frm);}

		jQuery.ajax({
			url: "",
			type: "POST",
			data: frmdata,
			processData: false,
			contentType: false,
			beforeSend: function() {
				box.attr('style', 'display="block"');
				box.fadeIn();
				box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
			},
			success: function (res) {
				box.text('Done');
				box.fadeOut();
				log.html(res);
				bindCalAfterAjax( "ticket_start_prod" );
				bindCalAfterAjax( "ticket_end_prod" );
			}
		});
	});

	$('#remove_prod').click(function(e) {
		var check = checkLink('<?php echo JText::_('ADMIN_EVENT_EVENT_TICKETS_TAB_EDITLINK_ERROR');?>');
		if(check == false){ return false; }

		var log = $('#list_ticket_add');
		var box = $('#ajaxmessagebox_add');
		var pform_task = $('#pform_task_add');
		pform_task.attr('value', 'remove_ticket_add')
		var frm = document.getElementById("paymentform_add");
		var frmdata = false;
		if (window.FormData) {frmdata = new FormData(frm);}

		jQuery.ajax({
			url: "",
			type: "POST",
			data: frmdata,
			processData: false,
			contentType: false,
			beforeSend: function() {
				box.attr('style', 'display="block"');
				box.fadeIn();
				box.text('<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS');?>');
			},
			success: function (res) {
				box.text('<?php echo JText::_('ADMIN_RECORD_DELETED');?>');
				box.fadeOut();
				log.html(res);
			}
		});
		
		reset_form_add();
	});
			
			
			$('#save_ticket_add').click(function(e) {
				save_ticket_add(e);
			});
	
			reset_form_add();
			
		</script>

<!---------------------------------- Group Discount section  --------------------------------->
<div class="tab-pane" id="group_discount">
	<span class="span12 y-offset no-gutter" id="ajaxmessagebox_group"></span>
	<form name="groupdiscount" id="groupdiscount" action="" method="post">
		<!-- This section shows the discount form -->
		<div class="span5 y-offset no-gutter"id="add_group_discount">
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_GROUP_NUMBER_TICKET');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="min_tickets" id="min_tickets" class="inputbox" size="8" />
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_AMOUNT');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="discount_amount" id="discount_amount" class="inputbox" size="8" />
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_TYPE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input style="margin-top:-5px;margin-right:4px;" type="radio" class="inputbox" id="discount_type" name="discount_type" value="P" checked />%
				<input style="margin-top:-5px;margin-left:15px;margin-right:4px;" type="radio" class="inputbox" id="discount_type" name="discount_type" value="A" />
				<?php echo $this->regpro_config['currency_sign'];?>
			</span>
			<br/>
			<span class="span12 y-offset no-gutter">
				<button class="btn btn-small btn-success" id="save_discount">Add</button>
				<input type="button" class="button btn btn-inverse" value="Reset" onclick="resetform_groupdiscount();" />
			</span>
		</div>
		
		<!-- This section display the discont coupons -->
		<div class="span7 y-offset no-gutter" id="list_group_discount">
			<span class="span12 y-offset no-gutter">
				<a class="toolbar btn btn-small btn-success" id="editlink_groupdiscount" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_EDIT');?>
				</a>
				<a class="toolbar btn btn-small btn-danger" id="removelink_groupdiscount" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_REMOVE');?>
				</a>
			</span>
			<span class="span12 y-offset no-gutter">
				<table class="table_tickets">
					<tr id="table_tickets_header">
					<td><input type="checkbox" name="toggle" value="" onClick="paymentcheckAll_discount_group(<?php echo count( $this->row->event_discounts);?>);" /></td>
					<td><strong><?php echo JText::_('ADMIN_EVENTS_GROUP_MINIMUM_TICKETS');?></strong></td>
					<td><strong><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_PER_TICKET');?></strong></td>
					</tr>

					<?php
						$n = count($this->row->event_discounts);
						$i = 0;
						$k = 0;

						foreach ($this->row->event_discounts as $discount) {
							if($discount->discount_name == 'G'){
						?>

						<tr>
							<td><input id="cb_gr<?php echo $i?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $discount->id?>" name="cid[]"></td>
							<td style="text-align:center"><?php echo $discount->min_tickets;?></td>
							<td style="text-align:center">
								<?php
									if($discount->discount_type == 'A'){
										echo $this->regpro_config['currency_sign']."&nbsp;".$discount->discount_amount;
									} else echo $discount->discount_amount."&nbsp;%";
								?>
							</td>
						</tr>
						<?php
							}
							$i++;
						}

						if(count($this->row->event_discounts) <= 0)	echo "<tr><td colspan='6' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
						?>
					</table>
			</span>
		</div>
		<?php echo JHTML::_('form.token');?>
		<input type="hidden" name="option" value="com_registrationpro" />
		<input type="hidden" name="controller" value="events" />
		<input type="hidden" name="task" id="discountform_task" value="" />
		<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="discount_name" id="discount_name" value="G"/>
		<input type="hidden" name="boxchecked" value="0" />
	</form>
		<script language="javascript">
			function paymentcheckAll_discount_group(n, fldName ) {
				if (!fldName) fldName = 'cb_gr';
				var f = document.groupdiscount;
				var c = f.toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					cb = eval( 'f.' + fldName + '' + i );
					if (cb) {
						cb.checked = c;
						n2++;
					}
				}
				if (c) {
					f.boxchecked.value = n2;
				} else f.boxchecked.value = 0;
			}

			function resetform_groupdiscount() {
				var frm = document.groupdiscount;
				frm.min_tickets.value     = "";
				frm.discount_amount.value = "";
				if (frm.id) frm.id.value  = "";
				var save_discount = $('#save_discount').text('Add');
			}
			
			function checks_num(cb_name) {
				var cnt = 0;
				for (i = 0; i < 100; i++) {
					var cb = document.getElementById(cb_name + i);
					if ((cb) && (cb.checked)) cnt++;
				}
				return cnt;
			}
			
			$('#editlink_groupdiscount').click(function(e) {
				var box = $('#ajaxmessagebox_group');
				var log = $('#add_group_discount');
				
				if(checks_num('cb_gr') > 1) {
					alert('Please select just ONE Discount to Edit');
					return false;
				}
				if(checks_num('cb_gr') == 0) {
					alert('Please select a Discount first');
					return false;
				}
				var check = checkLink('<?php echo JText::_('ADMIN_EVENT_GROUP_DISCOUNT_TAB_EDITLINK_ERROR');?>');
				if (check == false) return false;

				var pform_task = $('#discountform_task');
				pform_task.attr('value', 'edit_groupdiscount')
				var frm = document.getElementById("groupdiscount");
				var frmdata = false;
				if (window.FormData) {frmdata = new FormData(frm);}

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Done');
						box.fadeOut();
						log.html(res);
					}
				});
			});

			$('#removelink_groupdiscount').click(function(e) {
				var box = $('#ajaxmessagebox_group');
				var log = $('#list_group_discount');
			
				var check = checkLink('<?php echo JText::_('ADMIN_EVENT_GROUP_DISCOUNT_TAB_EDITLINK_ERROR');?>');
				if (check == false) return false;

				var pform_task = $('#discountform_task');
				pform_task.attr('value', 'remove_groupdiscount')
				var frm = document.getElementById("groupdiscount");
				var frmdata = false;
				if (window.FormData) frmdata = new FormData(frm);
				
				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Saved');
						box.fadeOut();
						log.html(res);
					}
				});
				resetform_groupdiscount();
			});

			function save_discount(e) {
				e.preventDefault();
				var box = $('#ajaxmessagebox_group');
				var log = $('#list_group_discount');

				if(document.groupdiscount.event_id.value == 0){
					alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG');?>");
					return false;
				}
				if (document.groupdiscount.min_tickets.value == "") {
						alert("<?php echo JText::_('GROUP_MIN_EMPT')." "; ?>");
						document.groupdiscount.min_tickets.focus();
						return false;
				}
				if (document.groupdiscount.min_tickets.value > 125) {
						alert("<?php echo JText::_('GROUP_MIN_125')." "; ?>");
						document.groupdiscount.min_tickets.focus();
						return false;
				}
				if (document.groupdiscount.min_tickets.value != "") {
					if (!document.groupdiscount.min_tickets.value.match(/[0-9]/gi)) {
						alert("<?php echo JText::_('GROUP_MIN_TICKETS_FORMAT')." "; ?>");
						document.groupdiscount.min_tickets.focus();
						return false;
					}
				}
				if (document.groupdiscount.discount_amount.value == "") {
					alert("<?php echo JText::_('GROUP_PRICE_EMPT')." "; ?>");
					document.groupdiscount.discount_amount.focus();
					return false;
				}
				if (document.groupdiscount.discount_amount.value != "") {
					if (!document.groupdiscount.discount_amount.value.match(/[0-9]/gi)) {
						alert("<?php echo JText::_('GROUP_TICKETS_AMT_FORMAT')." "; ?>");
						document.groupdiscount.discount_amount.focus();
						return false;
					}
				}

				var pform_task = $('#discountform_task');
				pform_task.attr('value', 'add_groupdiscount')
				var frm = document.getElementById("groupdiscount");
				var frmdata = false;
				if (window.FormData) frmdata = new FormData(frm);

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Saved');
						box.fadeOut();
						log.html(res);
					}
				});
				resetform_groupdiscount();
			}
			
			$('#save_discount').click(function(e) {
				save_discount(e);
			});
		</script>
</div>

<!------------------------------------------- Early Discount section  ----------------------------------------->
<div class="tab-pane" id="early_discount">
	<span class="span12 y-offset no-gutter" id="ajaxmessagebox_early"></span>
	<form name="earlydiscount" id="earlydiscount" action="" method="post">
		<!-- This section will display the discount form -->
		<div class="span5 y-ooset no-gutter" id="add_early_discount">
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_DATE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<div class="input-append">
					<input id="early_discount_date" class="inputbox hasTooltip" type="text" maxlength="19" size="25" value="" name="early_discount_date" title="" data-original-title="">
					<button id="early_discount_date_img" class="btn" type="button"><i class="icon-calendar"></i></button>
				</div>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_AMOUNT');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input type="text" name="discount_amount" id="discount_amount" class="inputbox" size=8 />
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_TYPE');?>
			</span>
			<span class="span8 y-offset no-gutter">
				<input style="margin-top:-5px;margin-right:4px;" type="radio" class="inputbox" id="discount_type" name="discount_type" value="P" checked />%
				<input style="margin-top:-5px;margin-left:15px;margin-right:4px;" type="radio" class="inputbox" id="discount_type" name="discount_type" value="A" />
				<?php echo $this->regpro_config['currency_sign'];?>
			</span>
			<br/>
			<span class="span12 y-offset no-gutter">
				<button class="btn btn-small btn-success" id="save_early">Add</button>
				<input type="button" class="button btn btn-inverse" value="Reset" onclick="resetform_earlydiscount();" />
			</span>
		</div>
		
		<!-- This section display the discounts -->
		<div class="span7 y-offset no-gutter" id="list_early_discount">
			<span class="span12 y-offset no-gutter">
				<a class="toolbar btn btn-small btn-success" id="editlink_earlydiscount" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_EDIT');?>
				</a>
				<a class="toolbar btn btn-small btn-danger" id="removelink_earlydiscount" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_REMOVE');?>
				</a>
			</span>
			<span class="span12 y-offset no-gutter">
				<table class="table_tickets">
					<tr id="table_tickets_header">
					<td width="10px"><input type="checkbox" name="toggle" value="" onClick="paymentcheckAll_discount_early(<?php echo count( $this->row->event_discounts);?>);" /></td>
					<td width="150px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_DATE');?></strong></td>
					<td width="150px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_PER_TICKET');?></strong></td>
					</tr>

					<?php
						$n = count($this->row->event_discounts);
						$i = 0;
						$k = 0;

						foreach ($this->row->event_discounts as $discount) {
							if($discount->discount_name == 'E'){
						?>

						<tr>
							<td><input id="cb_ea<?php echo $i?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $discount->id?>" name="cid[]"></td>
							<td style="text-align:center"><?php echo $discount->early_discount_date;?></td>
							<td style="text-align:center">
								<?php
									if($discount->discount_type == 'A'){
										echo $this->regpro_config['currency_sign']."&nbsp;".$discount->discount_amount;
									} else echo $discount->discount_amount."&nbsp;%";
								?>
							</td>
						</tr>
						<?php
							}
							$i++;
						}

						if(count($this->row->event_discounts) <= 0) echo "<tr><td colspan='6' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
						?>
				  </table>
			</span>
		</div>
		<?php echo JHTML::_('form.token');?>
		<input type="hidden" name="option" value="com_registrationpro" />
		<input type="hidden" name="controller" value="events" />
		<input type="hidden" name="task" id="ediscountform_task" value="" />
		<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="discount_name" id="discount_name" value="E"/>
		<input type="hidden" name="boxchecked" value="0" />
	</form>
		<script language="javascript">
		
			var $cal_id = 'early_discount_date';
		
			function paymentcheckAll_discount_early( n, fldName ) {
				if (!fldName) fldName = 'cb_ea';
				var f = document.earlydiscount;
				var c = f.toggle.checked;
				var n2 = 0;
				for (i=0; i < n; i++) {
					cb = eval( 'f.' + fldName + '' + i );
					if (cb) {
						cb.checked = c;
						n2++;
					}
				}
				if (c) {
					f.boxchecked.value = n2;
				} else f.boxchecked.value = 0;
			}

			function resetform_earlydiscount() {
				var frm = document.earlydiscount;
				$('#'+$cal_id).val('');
				frm.discount_amount.value = "";
				if (frm.id) frm.id.value = "";
				var save_early = $('#save_early').text('Add');
			}

			function checks_num(cb_name) {
				var cnt = 0;
				for (i = 0; i < 100; i++) {
					var cb = document.getElementById(cb_name + i);
					if ((cb) && (cb.checked)) cnt++;
				}
				return cnt;
			}
			
			$('#editlink_earlydiscount').click(function(e) {
				var check = checkLink('<?php echo JText::_('ADMIN_EVENT_EARLY_BIRD_DISCOUNT_TAB_DATE_ERROR');?>');
				if(check == false) return false;
				
				if(checks_num('cb_ea') > 1) {
					alert('Please select just ONE Discount to Edit');
					return false;
				}
				if(checks_num('cb_ea') == 0) {
					alert('Please select a Discount first');
					return false;
				}
				
				var box = $('#ajaxmessagebox_early');
				var log = $('#add_early_discount');
				
				var pform_task = $('#ediscountform_task');
				pform_task.attr('value', 'edit_earlydiscount')
				var frm = document.getElementById("earlydiscount");
				var frmdata = false;
				if (window.FormData) {frmdata = new FormData(frm);}

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Done');
						box.fadeOut();
						log.html(res);
					}
				});
			});

			$('#removelink_earlydiscount').click(function(e) {
				var check = checkLink('<?php echo JText::_('ADMIN_EVENT_EARLY_BIRD_DISCOUNT_TAB_DATE_ERROR');?>');
				if(check == false) return false;

				var box = $('#ajaxmessagebox_early');
				var log = $('#list_early_discount');
				
				var pform_task = $('#ediscountform_task');
				pform_task.attr('value', 'remove_earlydiscount')
				var frm = document.getElementById("earlydiscount");
				var frmdata = false;
				if (window.FormData) {frmdata = new FormData(frm);}

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('<?php echo JText::_('ADMIN_RECORD_DELETED');?>');
						box.fadeOut();
						log.html(res);
					}
				});
				resetform_earlydiscount();
			});

			function save_early(e) {
				e.preventDefault();
				var box = $('#ajaxmessagebox_early');
				var log = $('#list_early_discount');
				
				if(document.earlydiscount.event_id.value == 0){
					alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG');?>");
					return false;
				}
				var $edd = $('#'+$cal_id).val();
				if ($edd == "") {
					alert("<?php echo JText::_('EARLY_MIN_DATE')." "; ?>");
					$('#'+$cal_id).focus();
					return false;
				}
				if ($edd != "") {
					if (!$edd.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
						alert("<?php echo JText::_('EARLY_DATE_FORMAT')." "; ?>");
						$('#'+$cal_id).focus();
						return false;
					}else {
						if(document.earlydiscount.discount_amount.value==""){
							alert("<?php echo JText::_('EARLY_PRICE_EMPT')." "; ?>");
							document.earlydiscount.discount_amount.focus();
							return false;
						}
						if (!document.earlydiscount.discount_amount.value.match(/[0-9]/gi)) {
								alert("<?php echo JText::_('EARLY_PRICE_FORMAT')." "; ?>");
								document.earlydiscount.discount_amount.focus();
								return false;
							}
						}

				}
				
				var pform_task = $('#ediscountform_task');
				pform_task.attr('value', 'add_earlydiscount')
				var frm = document.getElementById("earlydiscount");
				var frmdata = false;
				if (window.FormData) {frmdata = new FormData(frm);}

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Done');
						box.fadeOut();
						log.html(res);
					}
				});
				resetform_earlydiscount();
			};
			
			$('#save_early').click(function(e) {
				save_early(e);
			});
			
			function addCalendar($id) {
				$cal_id = $id;
				Calendar.setup({
					inputField: $id,      // id of the input field
					ifFormat: '%Y-%m-%d', // format of the input field
					button: $id+'_img',   // trigger for the calendar (button ID)
					align: 'Tl',          // alignment (defaults to "Bl")
					singleClick: true
				});
			}
			
			addCalendar('early_discount_date');
			
		</script>
</div>
	
<div class="tab-pane" id="session_page">
	<span class="span12 y-offset no-gutter" id="ajaxmessagebox_session"></span>
	<span class="span12 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE');?>
		<?php if($this->task == 'copy') { echo "<br /><div style=\"float:right;\">".JText::_('ADMIN_COPY_EVENT_NOTE')."</div>"; } ?>
	</span>
	<form name="session" id="session" action="" method="post">
		<!-- This section displays the session form -->
		<div id="add_session" class="span12 y-offset no-gutter">
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
		</div>
		<div class="span12 y-offset no-gutter" id="list_session" style="overflow:auto;">
			<span class="span12 y-offset">
				<a class="toolbar btn btn-small btn-success pull-right" id="editlink_session" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_SESSION_EDIT');?>
				</a>
				<a class="toolbar btn btn-small btn-danger pull-right" id="removelink_session" href="javascript:void(0);">
					<?php echo JText::_('ADMIN_EVENTS_SESSION_REMOVE');?>
				</a>
			</span>
			<span class="span12 y-offset no-gutter">
				<table class="table_tickets">
					<tr id="table_tickets_header">
						<td>
							<input type="checkbox" name="toggle" value="" onClick="sessioncheckAll(<?php echo count( $this->row->event_sessions);?>);" />
						</td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_TITLE');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_FEE');?></strong></td>
						<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DAY');?></strong></td>
						<td colspan="2" style="text-align:center">
							<strong>
								<?php echo JText::_('ADMIN_EVENTS_SESSION_ORDER');?>
							</strong>
						</td>
					</tr>

					<?php
						$n = count($this->row->event_sessions);
						$i = 0;
						$k = 0;
						if(is_array($this->row->event_sessions)){
							foreach ($this->row->event_sessions as $session) {
							?>
								<tr>
									<td><input id="cb_ss<?php echo $i?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $session->id?>" name="cid[]"></td>
									<td style="text-align:center"><?php echo $session->title;?></td>
									<td style="text-align:center"><?php echo $session->description;?></td>
									<td style="text-align:center">
										<?php
											if($session->feetype == 'A') {
												echo $this->regpro_config['currency_sign']."&nbsp;".$session->fee;
											} else echo $session->fee."&nbsp;%";
										?>
									</td>

									<td style="text-align:center">
										<?php
										$registrationproHelper = new registrationproHelper;
										echo $registrationproHelper->getFormatdate($this->regpro_config['session_dateformat'], $session->session_date);?> <br/>
										<?php echo $registrationproHelper->getFormatdate($this->regpro_config['session_timeformat'], $session->session_start_time);?> - <?php echo $registrationproHelper->getFormatdate($this->regpro_config['formattime'], $session->session_stop_time);?>
									</td>

									<td style="text-align:right">
									<?php if($i>0) echo "<a href=\"javascript:void(0);\" id=\"orderupsessions\" onclick=\"return session_uporder('cb_ss".$i."');\"><img src=\"".REGPRO_ADMIN_IMG_PATH."/uparrow.png\" width=12 height=12 border=0></a>";?>
									</td>
									<td style="text-align:left">
									<?php if($i<($n-1)) echo "<a href=\"javascript:void(0);\" id=\"orderdownsessions\" onclick=\"return session_downorder('cb_ss".$i."');\"><img src=\"".REGPRO_ADMIN_IMG_PATH."/downarrow.png\" width=12 height=12 border=0></a>";?>
									</td>

								</tr>
							<?php
								$i++;
							}
						}

						if(count($this->row->event_sessions) <= 0) echo "<tr><td colspan='6' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
						?>
			  </table>
			</span>
		</div>
		<?php echo JHTML::_('form.token');?>
		<input type="hidden" name="option" value="com_registrationpro" />
		<input type="hidden" name="controller" value="events" />
		<input type="hidden" id="ses_task" name="task" value="" />
		<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
	</form>
	<script language="javascript">
	
		var $ses_cal_id = 'session_date';
	
		function checks_num(cb_name) {
			var cnt = 0;
			for (i = 0; i < 100; i++) {
				var cb = document.getElementById(cb_name + i);
				if ((cb) && (cb.checked)) cnt++;
			}
			return cnt;
		}
	
		function sessioncheckAll( n, fldName ) {
			if (!fldName) fldName = 'cb_ss';
			var f = document.session;
			var c = f.toggle.checked;
			var n2 = 0;
			for (i=0; i < n; i++) {
				cb = eval( 'f.' + fldName + '' + i );
				if (cb) {
					cb.checked = c;
					n2++;
				}
			}
			if (c) {
				f.boxchecked.value = n2;
			} else f.boxchecked.value = 0;
		}

		function resetform_session(){
			var frm = document.session;
			$('#'+$ses_cal_id).val('');
			frm.session_page_header.value = "";
			frm.title.value 	   = "";
			frm.description.value  = "";
			frm.session_start_time.value = "";
			frm.session_stop_time.value	= "";
			frm.fee.value           = "";
			if(frm.id) frm.id.value	= "";
			var save_session = $('#save_session').text('Add');
		}

		function session_uporder(id) {
			var f = document.session;
			var cb = eval( 'f.' + id );
			if (cb) {
				for (i = 0; true; i++) {
					cbx = eval('f.cb_ss'+i);
					if (!cbx) break;
					cbx.checked = false;
				}
				cb.checked = true;
				f.boxchecked.value = 1;
				
				var box = $('#ajaxmessagebox_session');
				var log = $('#list_session');

				var pform_task = $('#ses_task');
				pform_task.attr('value', 'orderupsessions')
				var frm = document.getElementById("session");
				var frmdata = false;
				if (window.FormData) frmdata = new FormData(frm);

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Done');
						box.fadeOut();
						log.html(res);
					}
				});
			}
		}

		function session_downorder(id) {
			var f = document.session;
			var cb = eval( 'f.' + id );
			if (cb) {
				for (i = 0; true; i++) {
					cbx = eval('f.cb_ss'+i);
					if (!cbx) break;
					cbx.checked = false;
				}
				cb.checked = true;
				f.boxchecked.value = 1;
				
				var box = $('#ajaxmessagebox_session');
				var log = $('#list_session');

				var pform_task = $('#ses_task');
				pform_task.attr('value', 'orderdownsessions')
				var frm = document.getElementById("session");
				var frmdata = false;
				if (window.FormData) frmdata = new FormData(frm);

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Done');
						box.fadeOut();
						log.html(res);
					}
				});
			}
		}

		$('#editlink_session').click(function(e) {
			document.session.task.value = 'edit_session';
			var check = checkLink('<?php echo JText::_('ADMIN_EVENT_SESSION_TAB_EDITLINK_ERROR');?>');
			if (check == false) return false;
			
			if(checks_num('cb_ss') > 1) {
				alert('Please select just ONE Session to Edit');
				return false;
			}
			if(checks_num('cb_ss') == 0) {
				alert('Please select a Session first');
				return false;
			}
			
			var box = $('#ajaxmessagebox_session');
			var log = $('#add_session');

			var pform_task = $('#ses_task');
			pform_task.attr('value', 'edit_session')
			var frm = document.getElementById("session");
			var frmdata = false;
			if (window.FormData) {frmdata = new FormData(frm);}

			jQuery.ajax({
				url: "",
				type: "POST",
				data: frmdata,
				processData: false,
				contentType: false,
				beforeSend: function() {
					box.attr('style', 'display="block"');
					box.fadeIn();
					box.text('<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS');?>');
				},
				success: function (res) {
					box.text('Done');
					box.fadeOut();
					log.html(res);
				}
			});
		});

		$('#removelink_session').click(function(e) {
			var check = checkLink('<?php echo JText::_('ADMIN_EVENT_SESSION_TAB_EDITLINK_ERROR');?>');
			if (check == false) return false;
			
			var log = $('#list_session');
			var box = $('#ajaxmessagebox_session');			
			
			var pform_task = $('#ses_task');
			pform_task.attr('value', 'remove_session')
			var frm = document.getElementById("session");
			var frmdata = false;
			if (window.FormData) {frmdata = new FormData(frm);}

			jQuery.ajax({
				url: "",
				type: "POST",
				data: frmdata,
				processData: false,
				contentType: false,
				beforeSend: function() {
					box.attr('style', 'display="block"');
					box.fadeIn();
					box.text('<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS');?>');
				},
				success: function (res) {
					box.text('<?php echo JText::_('ADMIN_RECORD_DELETED');?>');
					box.fadeOut();
					log.html(res);
				}
			});
			resetform_session();
		});

		function save_session(e) {
			e.preventDefault();
			var box = $('#ajaxmessagebox_session');
			var log = $('#list_session');
		
			var $sdd = $('#'+$ses_cal_id).val();
		
			if (document.session.title.value == "") {
				alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_EMPTY_TITLE'); ?>");
				document.session.title.focus();
				return false;
			}else if (!$sdd.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi) || ($sdd == "")) {
				alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_WRONG_DATE_MSG'); ?>");
				$('#'+$ses_cal_id).focus();
				return false;
			}else if (!document.session.session_start_time.value.match(/[0-2][0-9]:[0-5][0-9]/gi) || (document.session.session_start_time.value == "")) {
				alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_WRONG_TIME_MSG'); ?>");
				document.session.session_start_time.focus();
				return false;
			}else if (!document.session.session_stop_time.value.match(/[0-2][0-9]:[0-5][0-9]/gi) || (document.session.session_stop_time.value == "")) {
				alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_WRONG_TIME_MSG'); ?>");
				document.session.session_stop_time.focus();
				return false;
			}
			
			if(document.session.event_id.value == 0){
				alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG');?>");
				return false;
			} else {
				var pform_task = $('#ses_task');
				pform_task.attr('value', 'add_session')
				var frm = document.getElementById("session");
				var frmdata = false;
				if (window.FormData) frmdata = new FormData(frm);

				jQuery.ajax({
					url: "",
					type: "POST",
					data: frmdata,
					processData: false,
					contentType: false,
					beforeSend: function() {
						box.attr('style', 'display="block"');
						box.fadeIn();
						box.text('<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS');?>');
					},
					success: function (res) {
						box.text('Done');
						box.fadeOut();
						log.html(res);
					}
				});
			}
			resetform_session();
		}
		
		$('#save_session').click(function(e) {
			save_session(e);
		});
		
		function addSesCalendar($id) {
			$ses_cal_id = $id;
			Calendar.setup({
				inputField: $id,      // id of the input field
				ifFormat: '%Y-%m-%d', // format of the input field
				button: $id+'_img',   // trigger for the calendar (button ID)
				align: 'Tl',          // alignment (defaults to "Bl")
				singleClick: true
			});
		}
			
		addSesCalendar('session_date');
		
		resetform_session();
	</script>
</div>

<?php } // end event id condition ?>

<?php } // end copy condition ?>

<?php
	$plugin = JPluginHelper::getPlugin('user', 'regpro_mailchimp');
	if(!empty($plugin)){
		require_once( JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'regpro_mailchimp'.DS.'libraries'.DS.'MCAPI.class.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'regpro_mailchimp'.DS.'libraries'.DS.'MCauth.php' );
		$params = new JRegistry($plugin->params);
		$MCapi = $params->get('api_key');
		$MCauth = new MCauth();
		$checked = '';  // if plugin enabled
		if($this->row->enable_mailchimp==1) $checked = 'checked="checked"';
		?>
			<div class="tab-pane" id="regpro_mailchimp">
			<?php
			if( !(!$MCapi || !$MCauth->MCauth()) ) { 
				$registrationproHelper = new registrationproHelper;
				$content = $registrationproHelper->getMailChimpList($this->row->mailchimp_list);
			?>
			<table border="0" class="adminform" cellpadding="0" cellspacing="0">
				<tr>
					<td width="20%"><?php echo JText::_('MAILCHIMP_LIST_ENABLE')?></td>
					<td width="80%"><input type="checkbox" name="enable_mailchimp" value="1" <?php echo $checked; ?> ></td>
				</tr>
				<tr>
					<td width="20%"><?php echo JText::_('MAILCHIMP_LIST_SHOW')?></td>
					<td width="80%"><?php echo $content; ?></td>
				</tr>
			</table>
		<?php } else echo JText::_('MAILCHIMP_API_KEY_ERROR');
		echo '</div>';
	}
?>
	<div class="clearfix"></div>
</div>
</div>