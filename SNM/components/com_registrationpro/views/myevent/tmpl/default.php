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

JHtmlBehavior::framework(); 

JHTML::_('behavior.modal', 'a.modal');

//echo $this->upgrade_mootools;
?>

<div id="regpro">

<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();
?>

<script language="javascript" type="text/javascript">
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

function submitbutton(pressbutton) {
	var form = document.adminForm;
	var email = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
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
	
	// do field validation (added by sdei on 23-Jan)
	if(!validateForm(form,false,false,false,false)){
		
	}else{
		var startDate = form.dates.value;
		var endDate	= form.enddates.value;
		startDate = startDate.split('-');
		endDate = endDate.split('-');
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
			alert('Please check your event date');
			form.enddates.focus();
		}else if ((endDate[0] == startDate[0]) && (endDate[1] < startDate[1])){
			alert('Please check your event date');
			form.enddates.focus();
		}else if ((endDate[0] == startDate[0]) && (endDate[1] == startDate[1]) && (endDate[2] < startDate[2])){
			alert('Please check your event date');
			form.enddates.focus();
		}else if(!email.test(form.notifyemails.value) && form.notifyemails.value !=''){
			alert('please enter valid notify email address under registration tab');
			form.notifyemails.focus();
		}else if(!parseInt(form.notifydate.value) && form.notifydate.value != ''){
			alert('Please enter valid day under registration tab');
			form.notifydate.focus();
		}else if (form.registra.value == 1) {
		
			if(form.regstarttimes.value >= "24:00") {
				form.regstarttimes.value = "00:00";
			}
			
			if(form.regstoptimes.value >= "24:00") {
				form.regstoptimes.value = "00:00";
			}
								
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
		}else{										
		// end
			submitform( pressbutton );
		}
	}
	// end			
}

</script>
<div id="regpro_outline" class="regpro_outline">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table cellpadding="4" cellspacing="1" border="0" class="adminform">
      	<tr>
			<td valign="top" align="left">
      			<table>
					<tr>
    					<td valign="top">
							<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
							<?php echo JText::_('ADMIN_EVENTS_TITEL')." "; ?>
						</td>
						<td>
							<input name="titel" alt="blank" emsg="<?php echo JText::_('EVENTS_DEL_TITEL_EMPT'); ?>" value="<?php echo htmlspecialchars(stripslashes($this->row->titel), ENT_QUOTES, 'UTF-8'); ?>" size="80" maxlength="200">
						</td>
					</tr>

					<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top"><?php echo JText::_('ADMIN_EVENTS_STATUS')." "; ?></td>
						<td>
							<?php														
							//create the event_status list							
							echo $this->Lists['event_status'];
							?>
						
							<?php echo $event_status; ?>
							&nbsp;
							<input class="button" value="1" name="notify" type="checkbox">
							<?php echo JText::_('ADMIN_EVENTS_NOTIFY'); ?>
						</td>
					</tr>

      				<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top">
							<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
							<?php echo JText::_('ADMIN_EVENTS_DATE')." "; ?>
						</td>
						<td>
							<?php
							echo JHTML::_('calendar'
							          , $this->row->dates
							          , 'dates'
							          , 'dates'
							          , '%Y-%m-%d'
							          , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
							?>
							<b><?php echo JText::_('ADMIN_EVENTS_DATE_NOTICE')." "; ?></b>
							
						</td>
   					</tr>

      				<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top">
							<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
							<?php echo JText::_('ADMIN_EVENTS_TIME')." "; ?>
						</td>
						<td><input name="times" class="starttime" alt="blank" emsg="<?php echo JText::_('EVENTS_DEL_TIME_EMPT'); ?>" value="<?php echo substr($this->row->times, 0, 5); ?>" size="15" maxlength="8">
							<b><?php
							if ( $layoutsettings[ $idx['time']]->set_show == 1 ) {
								echo JText::_('ADMIN_EVENTS_TIME_NOTICE')." ";
							} else {
								echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE')." ";
							}
						?></b>
						</td>
   					</tr>
					
      				<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top">
							<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
							<?php echo JText::_('ADMIN_EVENTS_ENDDATE')." "; ?>
						</td>
						<td>
						<?php
							echo JHTML::_('calendar'
							          , $this->row->enddates
							          , 'enddates'
							          , 'enddates'
							          , '%Y-%m-%d'
							          , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
							?>
							
							<b><?php echo JText::_('ADMIN_EVENTS_DATE_NOTICE')." "; ?></b>
						</td>
   					</tr>

      				<tr>
						<td valign="top">&nbsp;</td>
      					<td valign="top">
							<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
							<?php echo JText::_('ADMIN_EVENTS_ENDTIME')." "; ?>
						</td>
      					<td><input name="endtimes" class="endtime" alt="blank" emsg="<?php echo JText::_('EVENTS_DEL_ENDTIME_EMPT'); ?>" value="<?php echo substr($this->row->endtimes, 0, 5); ?>" size="15" maxlength="8">
			  				<b><?php echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE')." "; ?></b>
			  			</td>
   					</tr>

      				<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top"><?php echo JText::_('ADMIN_EVENTS_MAX_ATTENDANCE')." "; ?></td>
						<td>
							<input name="max_attendance"class="max_attendance" value="<?php echo substr($this->row->max_attendance, 0, 5); ?>" size="15" maxlength="8">
							<b><?php echo JText::_('ADMIN_EVENTS_MAX_ATTENDANCE_NOTICE')." "; ?></b>
						</td>
   					</tr>

      				<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGCOUNT')." "; ?></td>
						<td>
							<?php 
								if ($this->row->registra == 1) {		
									$nrregusers_array = $this->getModel()->getRegistered($row->id);
									//echo "<pre>";print_r($nrregusers_array);exit;
									$nrregusers = 0;
									foreach ($nrregusers_array as $pid=>$qty){
										if(is_int($qty)) $nrregusers+=$qty; 
									}		
									$linkreg 	= 'index.php?option=com_registrationpro&task=showregevusers&rdid='.$this->row->id.'&hidemainmenu=1';
									if($nrregusers > 0){
							?>
									<a href="<?php echo $linkreg; ?>" title="Edit Users">
									<?php echo $nrregusers . (($this->row->max_attendance==0) ? '': ' / ' . $this->row->max_attendance); ?>
									</a>
							<?php
								}else{
									echo $nrregusers;
								}
							}else {
							?>
								<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/publish_x.png" width="12" height="12" border="0" alt="Registration disabled" />
							<?php
							}
							?>
						</td>
   					</tr>
   				  
					<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top" colspan="2"><?php echo JText::_('ADMIN_EVENTS_SHORT_DESCR')." "; ?><br />
							<?php 
							// parameters : areaname, content, hidden field, width, height, rows, cols							 
							 echo $this->editor->display( 'shortdescription',  $this->row->shortdescription , '80%', '200', '75', '20', array('pagebreak', 'readmore')) ;
							?>
						</td>
					</tr>
					<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top" colspan="2"><?php echo JText::_('ADMIN_EVENTS_DESCR')." "; ?><br />
							<?php 
								// parameters : areaname, content, hidden field, width, height, rows, cols
								echo $this->editor->display( 'datdescription',  $this->row->datdescription , '80%', '200', '75', '20', array('pagebreak', 'readmore')) ;
							?>
						</td>
					</tr>
					<tr>
						<td valign="top">&nbsp;</td>
						<td valign="top" colspan="2"><?php echo JText::_('ADMIN_EVENTS_TERMS_AND_CONDITION'); ?><br /> 						
							<?php 
								// parameters : areaname, content, hidden field, width, height, rows, cols
								echo $this->editor->display( 'terms_conditions',  $this->row->terms_conditions , '80%', '200', '75', '20', array('pagebreak','readmore')) ; 
							?>
						</td>
					</tr>
				</table>
			</td>			
		</tr>
		<tr>
			<td valign="top" align="right" width="45%">
				<table class="adminform">																								
					<tr>
						<td valign="top"> 
						<?php
							/* jimport('joomla.html.pane');
							$tabs = JPane::getInstance('tabs', array('allowAllClose' => true));
							echo $tabs->startPane("content-pane");
							echo $tabs->startPanel(JText::_('ADMIN_EVENTS_SETTINGS'),"publish-page"); */
							echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); 
							echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('ADMIN_EVENTS_SETTINGS'), true);	
						?>   
							<table border="0" class="adminform">
								<tr>
									<th colspan="3"><?php echo JText::_('ADMIN_EVENTS_SETTINGS')." "; ?></th>
								</tr>

								<tr>
									<td valign="top" width="2px">&nbsp;</td>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_ACCESS')." "; ?></td>
									<td valign="top">
									<?php
										echo $this->Lists['access'];
									?>
									</td>
								</tr>
								
								<tr>
									<td valign="top" width="2px">&nbsp;</td>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_VIEW_ACCESS')." "; ?></td>
									<td valign="top">
									<?php
										echo $this->Lists['viewaccess'];
									?>
									</td>
								</tr>
								
								<tr>
									<td valign="top">&nbsp;</td>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PUBLI')." "; ?></td>
									<td valign="top">
									<?php
									$arrPublish = array(JHTML::_('select.option', 1, 'Yes', 'id','title'),JHTML::_('select.option', 0, 'No', 'id','title' ));
									$html =  JHTML::_('select.genericlist', $arrPublish, 'published', 'class="inputbox" size="1"','id', 'title', $this->row->published);
									echo $html;
									?>
									</td>
								</tr>
								<tr>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo JText::_('ADMIN_EVENTS_SHOW_ATTENDIES_LISTS'); ?></td>
                                <td valign="top"><select name="shw_attendees">
                                <option value="0" <?php  if($this->row->shw_attendees==0){ echo 'selected';}?>>No</option>
                                <option value="1" <?php if($this->row->shw_attendees==1){ echo 'selected';}?>>Yes</option>
                                </select></td>
                                </tr>							
								<tr>
									<td valign="top">&nbsp;</td>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_GROUPREG')." "; ?></td>
									<td valign="top">
									<?php
									if (!$this->row->allowgroup) $this->row->allowgroup = 0;
									$arrPublish = array(JHTML::_('select.option', 1, 'Yes', 'id','title'),JHTML::_('select.option', 0, 'No', 'id','title' ));
									$html =  JHTML::_('select.genericlist', $arrPublish, 'allowgroup', 'class="inputbox" size="1"','id', 'title', $this->row->allowgroup);
									echo $html;
									?>
									</td>
								</tr>								
								<tr>
									<td valign="top">&nbsp;</td>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_FORCE_GROUPREG')." "; ?></td>
									<td valign="top">
									<?php
									if (!$this->row->force_groupregistration) $this->row->force_groupregistration = 0;
									$arrforcegroups = array(JHTML::_('select.option', 1, 'Yes', 'id','title'),JHTML::_('select.option', 0, 'No', 'id','title' ));
									$html =  JHTML::_('select.genericlist', $arrforcegroups, 'force_groupregistration', 'class="inputbox" size="1"','id', 'title', $this->row->force_groupregistration);
									echo $html;
									?>
									</td>
								</tr>																																
								<tr>
									<td valign="top"></td>
									<td valign="top">
										<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
										<?php echo JText::_('ADMIN_EVENTS_CLUB_ID')." "; ?>
									</td>
									<td valign="top"><?php echo $this->Lists['locations']; ?> </td>
								</tr>

								<tr>
									<td valign="top"></td>
									<td valign="top">
										<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
										<?php echo JText::_('ADMIN_EVENTS_CAT_ID')." "; ?>
									</td>
									<td valign="top"><?php echo $this->Lists['categories']; ?> </td>
								</tr>
								<?php
									$payment_method_hide = "";
									if($this->regpro_config['multiple_registration_button'] == 1){
										$payment_method_hide = "style='display:none'";
									}
								?>									
								
								<tr <?php echo $payment_method_hide; ?>>
									<td valign="top"></td>
									<td valign="top">
										<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
										<?php echo JText::_('ADMIN_EVENTS_PAYMENT_ID')." "; ?>
									</td>
									<td valign="top"><?php echo $this->Lists['payment_method']; ?> </td>
								</tr>																
							</table>

					<?php
						/* echo $tabs->endPanel();
						echo $tabs->startPanel(JText::_('ADMIN_EVENTS_REG'),'registration-page'); */
						echo JHtml::_('bootstrap.endTab');
						echo JHtml::_('bootstrap.addTab', 'myTab', 'registration-page', JText::_('ADMIN_EVENTS_REG'), true);	
					?>
							<table class="adminform">
								<tr>
									<th colspan="2"><?php echo JText::_('ADMIN_EVENTS_REG'); ?></th>
								</tr>
								<tr>
									<td valign="top" width="30%"><?php echo JText::_('ADMIN_EVENTS_REGISTRA'); ?></td>
									<td valign="top" width="70%">
									<?php
									$arrRegenable = array(JHTML::_('select.option', 1, 'Yes', 'id','title'),JHTML::_('select.option', 0, 'No', 'id','title' ));
									$html =  JHTML::_('select.genericlist', $arrRegenable, 'registra', 'class="inputbox" size="1"','id', 'title', $this->row->registra);
									echo $html;								
									?>
									</td>
								</tr>
								
								<tr>
									<td valign="top">
										<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EVENTS_REGISTRA_NOTIFY_EMAILS_DESC' );?>">
										<?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY_EMAILS'); ?>
										</span>
									</td>
									<td valign="top"> <input id="notifyemails" name="notifyemails" value="<?php echo $this->row->notifyemails; ?>" size="60" maxlength="255"> </td>
								</tr>																

								<tr>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY'); ?></td>
									<td valign="top">
										<input id="notifydate" name="notifydate" value="<?php echo $this->row->notifydate; ?>" size="4" maxlength="10"> <?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFYDAYS')." ";?>
										<!--<input class="button" value="..." onclick="return showCalendar('notifydate', '%Y-%m-%d');" type="reset">-->
            							<br/><?php echo JText::_('ADMIN_EVENTS_REGISTRA_NOTIFY_NOTICE')." "; ?>
									</td>
								</tr>
								<tr>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGFORM_SELECT')." "; ?></td>
									<td valign="top"><?php echo $this->Lists['forms']; ?> </td>
								</tr>
								
								<tr>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGSTART')." "; ?></td>
									<td valign="top">
										<?php
											echo JHTML::_('calendar'
											          , $this->row->regstart
											          , 'regstart'
											          , 'regstart'
											          , '%Y-%m-%d'
											          , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
										?>
										
            							<b><?php echo JText::_('ADMIN_EVENTS_DATE_NOTICE')." "; ?></b>
									</td>
								</tr>
								
								<tr>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGSTART_TIME')." "; ?></td>
									<td><input name="regstarttimes" value="<?php echo substr($this->row->regstarttimes, 0, 5); ?>" size="15" maxlength="8">
										<b><?php echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE')." "; ?></b>
									</td>
								</tr>

								<tr>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGSTOP')." "; ?></td>
									<td valign="top">
										<?php
											echo JHTML::_('calendar'
											          , $this->row->regstop
											          , 'regstop'
											          , 'regstop'
											          , '%Y-%m-%d'
											          , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
										?>										
										<b><?php echo JText::_('ADMIN_EVENTS_DATE_NOTICE')." "; ?></b>
									</td>
								</tr>
								
								<tr>
									<td valign="top"><?php echo JText::_('ADMIN_EVENTS_REGSTOP_TIME')." "; ?></td>
									<td><input name="regstoptimes" value="<?php echo substr($this->row->regstoptimes, 0, 5); ?>" size="15" maxlength="8">
										<b><?php echo JText::_('ADMIN_EVENTS_ENDTIME_NOTICE')." "; ?></b>
									</td>
								</tr>														

							</table>
							<?php echo JHTML::_( 'form.token' ); ?>
							<input type="hidden" name="option" value="com_registrationpro" />

							<?php if ($this->task != "copy") { ?>				
								<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
							<?php } else { ?>							
								<input type="hidden" name="copy" value="1" />
								<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
							<?php } ?>							
							<input type="hidden" name="controller" value="myevents" />
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="boxchecked" value="0" />
							<input type="hidden" name="user_id" value="<?php echo $this->user->id;?>" />
							<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
		<?php
			/* $title = JText::_( 'RECURRING EVENTS' );
			echo $tabs->endPanel();
			echo $tabs->startPanel( $title, 'recurrence' ); */
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'myTab', 'recurrence', JText::_('RECURRING EVENTS'), true);	
			?>
										
				<div style="height: auto">
				<table width="100%">
					<tr>
						<td><?php echo JText::_( 'RECURRENCE' ); ?>:</td>
						<td>
						  <select id="recurrence_select" name="recurrence_select" size="1">
						    <option value="0"><?php echo JText::_( 'NOTHING' ); ?></option>
						    <option value="1"><?php echo JText::_( 'DAYLY' ); ?></option>
						    <option value="2"><?php echo JText::_( 'WEEKLY' ); ?></option>
						    <option value="3"><?php echo JText::_( 'MONTHLY' ); ?></option>
						    <option value="4"><?php echo JText::_( 'WEEKDAY' ); ?></option>
							<option value="5"><?php echo JText::_( 'DATES' ); ?></option>
						  </select>
						</td>
					</tr>
					<tr>
						<td colspan="2" id="recurrence_output">&nbsp;</td>
					</tr>
					<tr id="counter_row" style="display:none;">
						<td><?php echo JText::_( 'RECURRENCE COUNTER' ); ?>:</td>
						<td>
					        <?php echo JHTML::_('calendar', ($this->row->recurrence_counter <> 0000-00-00)? $this->row->recurrence_counter: JText::_( 'UNLIMITED' ), "recurrence_counter", "recurrence_counter"); ?><a href="#" onclick="include_unlimited('<?php echo JText::_( 'UNLIMITED' ); ?>'); return false;"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/unlimited.png" width="16" height="16" alt="<?php echo JText::_( 'UNLIMITED' ); ?>" /></a>
						</td>
					</tr>
				</table>
				
				</div>
			<br/>
			<input type="hidden" name="recurrence_id" id="recurrence_id" value="<?php echo $this->row->id; ?>" />			
			<input type="hidden" name="recurrence_type" id="recurrence_type" value="<?php echo $this->row->recurrence_type; ?>" />
			<input type="hidden" name="recurrence_number" id="recurrence_number" value="<?php echo $this->row->recurrence_number; ?>" />			
			<input type="hidden" name="recurrence_weekday" id="recurrence_weekday" value="<?php echo $this->row->recurrence_weekday; ?>" />
			</form>
			<script type="text/javascript">
				var $select_output = new Array();
				$select_output[1] = "<?php echo JText::_( 'OUTPUT DAY' ); ?>";
				$select_output[2] = "<?php echo JText::_( 'OUTPUT WEEK' ); ?>";
				$select_output[3] = "<?php echo JText::_( 'OUTPUT MONTH' ); ?>";
				$select_output[4] = "<?php echo JText::_( 'OUTPUT WEEKDAY' ); ?>";
				$select_output[5] = "<?php echo JText::_( 'OUTPUT DATES' ); ?>";

				var $weekday = new Array();
				$weekday[0] = "<?php echo JText::_( 'MONDAY' ); ?>";
				$weekday[1] = "<?php echo JText::_( 'TUESDAY' ); ?>";
				$weekday[2] = "<?php echo JText::_( 'WEDNESDAY' ); ?>";
				$weekday[3] = "<?php echo JText::_( 'THURSDAY' ); ?>";
				$weekday[4] = "<?php echo JText::_( 'FRIDAY' ); ?>";
				$weekday[5] = "<?php echo JText::_( 'SATURDAY' ); ?>";
				$weekday[6] = "<?php echo JText::_( 'SUNDAY' ); ?>";

				var $before_last = "<?php echo JText::_( 'BEFORE LAST' ); ?>";
				var $last = "<?php echo JText::_( 'LAST' ); ?>";
				
				var cal1 = new CalendarPopup(); // open pop calender for recurrece

				start_recurrencescript();								
			</script>																												
		<?php
			//echo $tabs->endPanel();
			echo JHtml::_('bootstrap.endTab');
						
		?>
			
		
		<?php	
		
			if($this->task != 'copy') {
			echo JHtml::_('bootstrap.addTab', 'myTab', 'ticket-page', JText::_('ADMIN_EVENTS_PAYMENT'), true);	
			//echo $tabs->startPanel(JText::_('ADMIN_EVENTS_PAYMENT'),'ticket-page');
		?>		
		<form name="paymentform" id="paymentform" action="" method="post">									
			<style type="text/css" media="screen">
				#ajaxmessagebox {
					margin-bottom:10px;
					width: auto;
					padding: 4px;
					border: solid 1px #DEDEDE;
					background: #FFFFCC;
					display: none;
					text-align:center;								
				}
			</style>
									
			<div id="ticket_form">
			<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform" width="300px" style="height:auto">																								
				<tr>
					<td width="100px" valign="top">
						<?php
							//this is where we show the add new product fields
							echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_NAME');
						?>
					</td>									
					<td><input  type="text" name="product_name" id="product_name" class="inputbox" size="20" value="<?php echo $ticket_name;?>"/><br /></td>
				</tr>

				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_PRICE');?> </td>
					<td valign="top"> <input id="add_price" type="text" name="product_price" id="product_price" class="inputbox" size="8" onblur="calculate_tot_amt();" value="<?php echo $ticket_price;?>" /> <?php echo $this->regpro_config['currency_sign'];?></td>
					
				</tr>
				
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_TAX'); ?></td>
					<td valign="top"><input id="tax" type="text" class="inputbox" name="tax" id="tax" size="4" onblur="calculate_tot_amt();" value="<?php echo $ticket_tax;?>"/>&nbsp;%&nbsp; &nbsp;&nbsp; <span id="totval"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL').": ".$total_price;?></span></td>
				</tr>
				
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_QTY');?></td>
					<td valign="top"> <input type="text" name="product_quantity" id="product_quantity" class="inputbox" size="8px" value="<?php echo $product_quantity;?>"  /></td>
				</tr>
													
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_DESC');?></td>
					<td valign="top"> <textarea name="product_description" id="product_description" class="inputbox" cols="20" rows="2"><?php echo $ticket_desc;?></textarea></td>
				</tr>
				
				<tr>
					<td id="label"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_START_DATE');?></td>
					<td><?php echo JHTML::_('calendar', $this->row->ticket_start, 'ticket_start', 'ticket_start', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));?></td>
				</tr>
				
				<tr>
					<td id="label"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_END_DATE');?></td>
					<td><?php echo JHTML::_('calendar', $this->row->ticket_end, 'ticket_end', 'ticket_end', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25', 'readonly'=>'true', 'maxlength'=>'19'));?></td>
				</tr>
				
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">										
						<input type="submit" class="button" id="add" value="add" />
						<input type="button" class="button" value="reset" onclick="resetform();" />
					</td>
				</tr>							
			</table>
			</div>
			<div id="ajaxmessagebox"></div>
			<table border="" class="adminform" cellpadding="0" cellspacing="0" >							
				<tr>
				
					<td colspan="7">										
						&nbsp;<a class="toolbar" id="editlink" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_EDIT');?> </a> &nbsp;&nbsp; <a class="toolbar" id="removelink" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_REMOVE'); ?> </a>
					</td>												
				</tr>
				<tr>
					<td colspan="2" style="vertical-align:top">
						<div id="list_ticket" style="overflow:auto; height: 200px;" class="list-my-tickets">
						<table cellpadding="2" cellspacing="0"  class="adminform">
						<tr>
							<td>
								<input type="checkbox" name="toggle" value="" onClick="paymentcheckAll(<?php echo count( $this->row->products); ?>);" />
							</td>
							<td>
								<strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_NAME'); ?></strong>
							</td>
							<td>
								<strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_PRICE'); ?></strong>
							</td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TAX'); ?></strong></td>
							<td>
								<strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL'); ?></strong>
							</td>
							<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_QTY'); ?></strong></td>
							<td colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_LIST_ORDER'); ?></strong></td>
						</tr>

						<?php
							$n = count($this->row->products);
							$i=0;
							$k = 0;
																		
							foreach ($this->row->products as $product)
							{
								if($product->type == 'E'){
									$pchecked 	= JHTML::_('grid.checkedout',   $product, $i );
							?>

								<tr> 												
									<td><?php echo $pchecked;?></td>
									<td><?php echo $product->product_name;?></td>
									<td><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->product_price; ?></td>
									<td><?php echo $product->tax. '&nbsp;%';?></td>
									<td><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->total_price; ?></td>
									<td><?php echo $product->product_quantity; ?></td>
									
									<td><?php 
										if ($i > 0) { ?>
											<a href="javascript: void(0);" id="orderuppayments" onclick="return payment_uporder('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
									  <?php	
										} ?>
									</td>
									<td style="text-align:left"><?php
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
							echo "</table>";
							if(count($this->row->products) <= 0)
								echo JText::_('FRONTEND_NO_RECORD');
							?>										
					  
					  </div>
				  </td>
			  </tr>									
			</table>				
			<?php echo JHTML::_( 'form.token' ); ?>					
			<input type="hidden" value="0" name="total_price" id="total_price" />	
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="myevents" />	
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="type" value="E" />
			<input type="hidden" name="user_id" value="<?php echo $this->user->id;?>" />
			<?php 
				if($this->row->id > 0){
			?>
				<input type="hidden" name="regpro_dates_id" value="<?php echo $this->row->id; ?>" />	
			<?php		
				}
			?>							
			<input type="hidden" name="boxchecked" value="0" />
			
			<!-- Event records hiddden fields-->
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
			<!-- End hidden fields -->				
		
		</form>
		<script language="javascript">																
				function calculate_tot_amt()
				{
					var frm = document.paymentform;
					var price;
					var tax;
					var totalprice;
					if(frm.tax.value != '' && !isNaN(frm.tax.value)){
						price 	= frm.product_price.value;
						tax 	= frm.tax.value;
						totalprice= (price * tax) / 100;
						price = Number(price)+Number(totalprice);											
					}else{
						price = frm.total_price.value = frm.product_price.value;											
						document.getElementById("totval").innerHTML = "<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL');?>:  " + price;
					}	
					
					frm.total_price.value= price;
					document.getElementById("totval").innerHTML = "<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL');?>:  " + price;
				}
				
				
				function paymentcheckAll( n, fldName ) {
				  if (!fldName) {
					 fldName = 'cb';
				  }
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
					if (c) {
						f.boxchecked.value = n2;
					} else {
						f.boxchecked.value = 0;
					}
				}
				
				function resetform(){
					var frm = document.paymentform;
					frm.product_quantity.value = "";
					frm.ticket_start.value = "";
					frm.ticket_end.value = "";
					frm.product_name.value = "";
					frm.product_price.value = "";
					frm.total_price.value = "";
					frm.product_description.value = "";
					frm.tax.value = "";
					//frm.id.value = "";
					document.getElementById("totval").innerHTML = "";
					if(frm.id) frm.id.value = "";
					/*if(document.getElementById("PaymentId")){
						frm.PaymentId.value = "";																																																	
					}*/
				}
																						
				function payment_uporder(id) {
					var f = document.paymentform;
					cb = eval( 'f.' + id );
					if (cb) {
						for (i = 0; true; i++) {
							cbx = eval('f.cb'+i);
							if (!cbx) break;
							cbx.checked = false;
						} // for
						cb.checked = true;
						f.boxchecked.value = 1;
						//submitbutton(task);
						f.task.value = 'orderuppayments';
						
						//alert("hello");
						
						/*var el = $('paymentform');
						el.send({
							update: 'list_ticket'												
						});*/
						
						var box = $('ajaxmessagebox');
						var el = $('paymentform');
						
						var log = $('list_ticket');
						
						el.set('send', {
							onRequest: function() {
								box.style.display="block";
								box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {
								box.set('text', 'Done.');															
								(function() {box.fade('out')}).delay(2000);	
								box.style.display="";
								if(document.paymentform.task.value == "orderuppayments"){						
									log.set('html', response);
								}							
							}
						});
						el.send();																
					}
				}
				
				function payment_downorder(id) {
					var f = document.paymentform;
					cb = eval( 'f.' + id );
					if (cb) {
						for (i = 0; true; i++) {
							cbx = eval('f.cb'+i);
							if (!cbx) break;
							cbx.checked = false;
						} // for
						cb.checked = true;
						f.boxchecked.value = 1;
						
						f.task.value = 'orderdownpayments';
						
						/*var el = $('paymentform');
						el.send({
							update: 'list_ticket'										
						});	*/
						var box = $('ajaxmessagebox');
						var el = $('paymentform');
						
						var log = $('list_ticket');
						
						el.set('send', {
							onRequest: function() {
								box.style.display="block";
								box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {
								box.set('text', 'Done.');															
								(function() {box.fade('out')}).delay(2000);	
								box.style.display="";
								if(document.paymentform.task.value == "orderdownpayments")	{						
									log.set('html', response);
								}							
							}
						});
						el.send();
					}
				}									
												
				window.addEvent('domready', function(e){
																						
					//var box = $('ajaxmessagebox');
																															
					$('editlink').addEvent('click', function(e) {
						var itemSelected = false;
						j('#paymentform input[name="cid[]"]').each(function(){
							if(j(this).is(":checked") && !itemSelected)
								itemSelected = true;
						});
					
						if(!itemSelected){
							alert("Please select a Ticket you want to edit!");
							return false;
						}
						document.paymentform.task.value = 'edit_ticket';
						
						var box = $('ajaxmessagebox');
						
						var el = $('paymentform');
						//e.stop();
						e.stop();
						
						var log = $('ticket_form');
						
						el.set('send', {
							onRequest: function() {
								box.style.display="block";
								box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {
								box.set('text', 'Done.');															
								(function() {box.fade('out')}).delay(1000);	
								if(document.paymentform.task.value == "edit_ticket")	{						
									log.set('html', response);
									bindCalAfterAjax("ticket_start");
									bindCalAfterAjax("ticket_end");
								}							
							}
						});
						el.send();								
					});
					
					$('removelink').addEvent('click', function(e) {
						resetform();
						document.paymentform.task.value = 'remove_ticket';	
						
						var box = $('ajaxmessagebox');
															
						var el = $('paymentform');

						//e.stop();
						e.stop();
						
						var log = $('list_ticket');						
						
						el.set('send', {						
							onRequest: function() {
								box.style.display="block";
								(function() {box.fade('in')}).delay(100);							
								box.set('text', '<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {								
								box.set('text', '<?php echo JText::_('ADMIN_RECORD_DELETED'); ?>');
								(function() {box.fade('out')}).delay(1000);	
								
								if(document.paymentform.task.value == "remove_ticket")	{
									log.set('html', response);
								}
							}	
						});	
						el.send();							
					});																																																																													
																														
					$('paymentform').addEvent('submit', function(e) {
						//calculate_tot_amt();									
						document.paymentform.task.value= "add_ticket";
						
						var box = $('ajaxmessagebox');

							//var el = $('paymentform');
							//e.stop();																						
							var form;
							form = document.adminForm;
							
							// copy event fields data into payment form fields												
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

			
							// end
							if(!validateForm(form,false,false,false,false)){
								alert('Please fill up the required fields of event first');
								return false;
							}else{
								
								/* Validate the ticket fields */
								if(document.paymentform.product_name.value == "")
								{
									alert('Ticket name can not be empty');
									document.paymentform.product_name.focus();
									return false;
								}
								// check the date and time format
								if (!form.dates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
									alert("<?php echo JText::_('ADMIN_EVENTS_DELFORM')." "; ?>");
									form.dates.focus();	
									return false;
								}else if (!form.times.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
									alert("<?php echo JText::_('EVENTS_DEL_TIME_FORM')." "; ?>");
									form.times.focus();
									return false;									
								}else if (!form.enddates.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi)) {
									alert("<?php echo JText::_('ADMIN_EVENTS_ENDDATE_FORMAT')." "; ?>");
									form.enddates.focus();	
									return false;
								}else if (!form.endtimes.value.match(/[0-2][0-9]:[0-5][0-9]/gi)) {
									alert("<?php echo JText::_('EVENTS_DEL_ENDTIME_FORM')." "; ?>");					
									form.endtimes.focus();
									return false;
								}else if (form.registra.value == 1) {						
									if(form.form_id.value == 0){
										alert("<?php echo JText::_('EVENTS_REGISTER_FORM')." "; ?>");
										form.form_id.focus();
										return false;
									}else if(form.regstart.value == "" || form.regstart.value == "0000-00-00"){
										alert("<?php echo JText::_('EVENTS_REGISTER_START_DATE')." "; ?>");
										form.regstart.focus();
										return false;
									}else if(form.regstop.value == "" || form.regstop.value == "0000-00-00"){
										alert("<?php echo JText::_('EVENTS_REGISTER_END_DATE')." "; ?>");
										form.regstop.focus();
										return false;
									}else{	
																
										//e.stop();
										e.stop();																																						
										var log = $('list_ticket');
																														
										//$('paymentform').send({	
										this.set('send', {
											onRequest: function() {
												box.style.display="block";	
												(function() {box.fade('in')}).delay(100);											
												box.set('text', '<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS'); ?>');
											},
											onComplete: function(response) {												
												box.set('text', 'Saved');												
												(function() {box.fade('out')}).delay(1000);	
												box.style.display="none";
												//alert(document.paymentform.task.value);
												//alert(response);	
												//alert(log.id);					
												if(document.paymentform.task.value == "add_ticket")	{																	
													log.set('html', response);	
												}											
											}																								
										});

										this.send();
									}						
								}else{										
								// end
									
									e.stop();
																	
									var log = $('list_ticket');
									this.set('send', {								
										onRequest: function() {
											box.style.display="block";
											box.setHTML('<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS'); ?>');
										},
										onComplete: function(response) {
											box.set('text', 'Saved');										
										 	(function() {box.fade('out')}).delay(1000);	
											
											if(document.paymentform.task.value == "add_ticket")	{
												log.set('html', response);
											}
										}																								
									});

									this.send();
								}
							}
						});
				});																								
			</script>
			<?php
			//echo $tabs->endPanel();
			echo JHtml::_('bootstrap.endTab');
			
		?>
		<?php	
			if($this->row->id){
				echo JHtml::_('bootstrap.addTab', 'myTab', 'additional_ticket-page', JText::_('ADMIN_EVENTS_ADDITIONAL_PAYMENT'), true);
			//echo $tabs->startPanel(JText::_('ADMIN_EVENTS_ADDITIONAL_PAYMENT'),'additional_ticket-page');
		?>

		<form name="paymentform_add" id="paymentform_add" action="index.php" method="post">
											
			<style type="text/css" media="screen">
				#ajaxmessagebox_add {
					margin-bottom:10px;
					width: auto;
					padding: 4px;
					border: solid 1px #DEDEDE;
					background: #FFFFCC;
					display: none;
					text-align:center;								
				}
			</style>

			<script language="javascript">
			
					function calculate_tot_amt_add()
					{
						var frm = document.paymentform_add;
						var price;
						var tax;
						var totalprice;
						if(frm.tax.value != '' && !isNaN(frm.tax.value)){
							price = frm.product_price.value;
							tax = frm.tax.value;
							totalprice= (price * tax) / 100;
							price = Number(price)+Number(totalprice);											
						}else{
							price = frm.total_price.value = frm.product_price.value;											
							document.getElementById("totval_add").innerHTML = "<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL');?>:  " + price;
						}	
						
						frm.total_price.value= price;
						document.getElementById("totval_add").innerHTML = "<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL');?>:  " + price;
					}

			
					function paymentcheckAll_add( n, fldName ) {
					  if (!fldName) {
						 fldName = 'cb';
					  }
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
						if (c) {
							f.boxchecked.value = n2;
						} else {
							f.boxchecked.value = 0;
						}
					}
						
					function resetform_add(){
						var frm = document.paymentform_add
						frm.product_quantity.value = "";
						frm.ticket_start.value = "";
						frm.ticket_end.value = "";
						frm.product_name.value 	= "";
						frm.product_price.value 	= "";
						frm.total_price.value 		= "";
						frm.product_description.value = "";
						frm.tax.value 				= "";
						document.getElementById("totval_add").innerHTML	= "";
						
						if(frm.id) frm.id.value = "";
						/*if(document.getElementById("PaymentId")){
							frm.PaymentId.value = "";																																																	
						}*/
					}

					function payment_uporder_add(id) {
						var f = document.paymentform_add;
						cb = eval( 'f.' + id );
						if (cb) {
							for (i = 0; true; i++) {
								cbx = eval('f.cb'+i);
								if (!cbx) break;
								cbx.checked = false;
							} // for
							cb.checked = true;
							f.boxchecked.value = 1;
							//submitbutton(task);
							
							//f.action.value = 'orderuppayments';
							f.task.value = 'orderuppayments';
							
							/*var el = $('paymentform_add');
							el.send({
								update: 'list_ticket_add'												
							});*/	
							var box = $('ajaxmessagebox_add');
							var el = $('paymentform_add');
							
							var log = $('list_ticket_add');
							
							el.set('send', {
								onRequest: function() {
									box.style.display="block";
									box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {
									box.set('text', 'Done.');															
									(function() {box.fade('out')}).delay(1000);	
									if(document.paymentform_add.task.value == "orderuppayments")	{						
										log.set('html', response);
									}							
								}
							});
							el.send();

						}
					}
					
					function payment_downorder_add(id) {
						var f = document.paymentform_add;
						cb = eval( 'f.' + id );
						if (cb) {
							for (i = 0; true; i++) {
								cbx = eval('f.cb'+i);
								if (!cbx) break;
								cbx.checked = false;
							} // for
							cb.checked = true;
							f.boxchecked.value = 1;
							
							f.task.value = 'orderdownpayments';
							
							/*var el = $('paymentform_add');
							el.send({
								update: 'list_ticket_add'											
							});	*/
							
							var box = $('ajaxmessagebox_add');
							var el = $('paymentform_add');
							
							var log = $('list_ticket_add');
							
							el.set('send', {
								onRequest: function() {
									box.style.display="block";
									box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {
									box.set('text', 'Done.');															
									(function() {box.fade('out')}).delay(1000);	
									if(document.paymentform_add.task.value == "orderdownpayments")	{						
										log.set('html', response);
									}							
								}
							});
							el.send();
							
						}
					}									
													
					window.addEvent('domready', function(e){
																
						var box = $('ajaxmessagebox_add');
						//var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});	
																											
						$('editlink_add').addEvent('click', function(e) {
							//document.paymentform_add.action.value = 'edit_payment';											
							document.paymentform_add.task.value = 'edit_ticket';											
							
							//var el = $('paymentform_add');
							
							var log = $('ticket_form_add');
							
							//el.send({
							$('paymentform_add').set('send', {
								update: 'ticket_form_add',
								onRequest: function() {
									box.style.display="block";
									(function() {box.fade('in')}).delay(1000);							
									box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {								
									box.set('text', 'Done...');
									(function() {box.fade('out')}).delay(1000);								
									if(document.paymentform_add.task.value == "edit_ticket")	{							
										log.set('html', response);
										bindCalAfterAjax("ticket_start_add");
										bindCalAfterAjax("ticket_end_add");
									}
								}
							});
							$('paymentform_add').send();							
						});
						
						$('removelink_add').addEvent('click', function(e) {
							resetform_add();
							//document.paymentform_add.action.value = 'remove_payment';										
							document.paymentform_add.task.value = 'remove_ticket';	
							//var el = $('paymentform_add');
							
							var log = $('list_ticket_add');
							
							$('paymentform_add').set('send', {
								update: 'list_ticket_add',
								onRequest: function() {
									box.style.display="block";
									(function() {box.fade('in')}).delay(100);							
									box.set('text', '<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {								
									box.set('text', '<?php echo JText::_('ADMIN_RECORD_DELETED'); ?>');
									(function() {box.fade('out')}).delay(1000);
																										
									if(document.paymentform_add.task.value == "remove_ticket")	{							
										log.set('html', response);
									}
								}
							});	
							$('paymentform_add').send();							
						});																																																																													
																															
						$('paymentform_add').addEvent('submit', function(e) {	
							calculate_tot_amt_add();									
							//document.paymentform_add.action.value= "saveticket";
							document.paymentform_add.task.value= "add_ticket";						
							
							var log = $('list_ticket_add');
							
							e.stop();
							if(document.paymentform_add.regpro_dates_id.value == 0){
								alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG'); ?>");
								return false;
							}else if(document.paymentform_add.product_name.value == ""){
								alert('Product name can not be empty');
								document.paymentform_add.product_name.focus();
								return false;
							}else{	
								//this.set('send', {																								
								this.set('send', {
									update: 'list_ticket_add',
									onRequest: function() {
										box.style.display="block";
										(function() {box.fade('in')}).delay(100);							
										box.set('text', '<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS'); ?>');
									},
									onComplete: function(response) {								
										box.set('text', 'Done...');
										(function() {box.fade('out')}).delay(1000);	
										
										if(document.paymentform_add.task.value == "add_ticket")	{							
											log.set('html', response);
										}
									}						
								});
								this.send();
							}
						});
					});																																				
				</script>
			<div id="ticket_form_add">
			<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform" width="300px" style="height:auto">
				<tr>
					<td width="100px" valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_NAME');?></td>									
					<td><input  type="text" name="product_name" id="product_name" class="inputbox" size="20" value="<?php echo $ticket_name;?>"/><br /></td>
				</tr>

				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_PRICE');?> </td>
					<td valign="top"> <input id="add_price" type="text" name="product_price" id="product_price" class="inputbox" size="8" onblur="calculate_tot_amt_add();" value="<?php echo $ticket_price; ?>" /> <?php echo $this->regpro_config['currency_sign'];?></td>									
				</tr>
				
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_TAX'); ?></td>
					<td valign="top"><input id="tax" type="text" class="inputbox" name="tax" id="tax" size="4" onblur="calculate_tot_amt_add();" value="<?php echo $ticket_tax;?>"/>&nbsp;%&nbsp; &nbsp;&nbsp; <span id="totval_add"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL').": ".$ticket_total;?></span></td>
				</tr>
				
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_QTY');?></td>
					<td valign="top"> <input type="text" name="product_quantity" id="product_quantity" class="inputbox" size="8px" value="<?php echo $product_quantity;?>"  /></td>
				</tr>
													
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_DESC');?></td>
					<td valign="top"> <textarea name="product_description" id="product_description" class="inputbox" cols="20" rows="2"><?php echo $ticket_desc;?></textarea></td>
				</tr>

				<tr>
					<td id="label"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_PROD_START_DATE');?></td>
					<td><?php echo JHTML::_('calendar', $this->row->ticket_start, 'ticket_start', 'ticket_start_prod', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));?></td>
				</tr>

				<tr>
					<td id="label"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_PROD_END_DATE');?></td>
					<td><?php echo JHTML::_('calendar', $this->row->ticket_end, 'ticket_end', 'ticket_end_prod', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));?></td>
				</tr>

				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">										
						<input type="submit" class="button" id="add" value="add" />
						<input type="button" class="button" value="reset" onclick="resetform_add();" />
					</td>
				</tr>							
			</table>
			</div>
			<div id="ajaxmessagebox_add"></div>
			<table border="0" class="adminform" cellpadding="0" cellspacing="0">							
				<tr>								
					<td colspan="7">										
						&nbsp;<a class="toolbar" id="editlink_add" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_EDIT');?> </a> &nbsp;&nbsp; <a class="toolbar" id="removelink_add" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_REMOVE'); ?> </a>
					</td>												
				</tr>
				<tr>
					<td colspan="2" style="vertical-align:top">
						<div id="list_ticket_add" class="list-my-tickets">										
						<table cellpadding="2" cellspacing="0" id="list-my-tickets" class="adminform">
							<tr>
								<td>
									<input type="checkbox" name="toggle" value="" onClick="paymentcheckAll_add(<?php echo count( $this->row->products); ?>);" />
								</td>
								<td>
									<strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_NAME'); ?></strong>
								</td>
								<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_PRICE'); ?></strong></td>
								<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TAX'); ?></strong></td>
								<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL'); ?></strong></td>
								<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_QTY'); ?></strong></td>
								<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_LIST_ORDER'); ?></strong></td>
							</tr>

						<?php
							$n = count($this->row->products);
							$i=0;
							$k = 0;
																		
							foreach ($this->row->products as $product)
							{
								//$pchecked 	= mosCommonHTML::CheckedOutProcessing( $product, $i );
								if($product->type == 'A'){
									$pchecked 	= JHTML::_('grid.checkedout',   $product, $i );
							?>

							<tr> 												
								<td><?php echo $pchecked;?></td>
								<td><?php echo $product->product_name;?></td>
								<td><?php echo $this->regpro_config['currency_sign'].$product->product_price; ?></td>
								<td><?php echo $product->tax;?></td>
								<td><?php echo $this->regpro_config['currency_sign'].$product->total_price; ?></td>
								<td><?php echo $product->product_quantity; ?></td>
								
								<td>												
								<?php 
									if ($i > 0) { ?>
										<a href="javascript: void(0);" id="orderuppayments" onclick="return payment_uporder_add('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
								  <?php	
									} ?>
								</td>
								<td><?php
									if ($i < $n-1) { ?>
										<a href="javascript: void(0);" id="orderdownpayments" onclick="return payment_downorder_add('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
								  <?php		
									}?>
								</td>
							</tr>
							<?php
									}
								$i++;																								
							}
							
							if(count($this->row->products) <= 0)
								echo "<tr><td colspan='6' style='text-align:center'>".JText::_('FRONTEND_NO_RECORD')."</td></tr>";
							?>										
					  </table>
					  </div>
				  </td>
			  </tr>									
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>	
			<input type="hidden" value="0" name="total_price" id="total_price" />
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="myevents" />	
			<input type="hidden" name="task" value="" />
			<input type="hidden" value="saveticket" name="action" id="action" />	
			<input type="hidden" name="regpro_dates_id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="boxchecked" value="0" />	
			<input type="hidden" name="type" value="A" />							
						
		<?php
			echo JHtml::_('bootstrap.endTab');
			//echo $tabs->endPanel();
		?>
		
		</form>
		
		<?php
			echo JHtml::_('bootstrap.addTab', 'myTab', 'payment-page', JText::_('ADMIN_EVENTS_GROUP_DISCOUNT'), true);
			//echo $tabs->startPanel(JText::_('ADMIN_EVENTS_GROUP_DISCOUNT'),'payment-page');
		?>

		<form name="groupdiscount" id="groupdiscount" action="index.php" method="post">
											
			<style type="text/css" media="screen">
				#ajaxmessagebox_group {
					margin-bottom:10px;
					width: auto;
					padding: 4px;
					border: solid 1px #DEDEDE;
					background: #FFFFCC;
					display: none;
					text-align:center;
														
				}
			</style>

			<script language="javascript">
																			
					function paymentcheckAll_discount_group( n, fldName ) {
					  if (!fldName) {
						 fldName = 'cb';
					  }
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
						} else {
							f.boxchecked.value = 0;
						}
					}
						
					function resetform_groupdiscount(){
						var frm = document.groupdiscount;
						frm.min_tickets.value 		= "";
						frm.discount_amount.value 	= "";						
						if(frm.id) frm.id.value = "";
																						
					}											
													
					window.addEvent('domready', function(e){
																
						//var box = $('ajaxmessagebox_group');
						//var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});	
																											
						$('editlink_groupdiscount').addEvent('click', function(e) {																					
							document.groupdiscount.task.value = 'edit_groupdiscount';
							
							var box = $('ajaxmessagebox_group');							
							var log = $('add_group_discount');
																				
							$('groupdiscount').set('send', {
								update: 'add_group_discount',
								onRequest: function() {
									box.style.display="block";
									(function() {box.fade('in')}).delay(1000);														
									box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {								
									box.set('text', 'Done...');
									(function() {box.fade('out')}).delay(1000);		
									//box.style.display="";
									if(document.groupdiscount.task.value == "edit_groupdiscount"){															
										log.set('html', response);
									}
								}
							});
							$('groupdiscount').send();							
						});
						
						$('removelink_groupdiscount').addEvent('click', function(e) {
							resetform_groupdiscount();
							document.groupdiscount.task.value = 'remove_groupdiscount';	
														
							var box = $('ajaxmessagebox_group');
							var log = $('list_group_discount');
														
							$('groupdiscount').set('send', {
								update: 'list_group_discount',
								onRequest: function() {
									box.style.display="block";
									(function() {box.fade('in')}).delay(1000);															
									box.set('text', '<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {								
									box.set('text', '<?php echo JText::_('ADMIN_RECORD_DELETED'); ?>');
									(function() {box.fade('out')}).delay(1000);	
									//box.style.display="";	
									if(document.groupdiscount.task.value == "remove_groupdiscount"){						
										log.set('html', response);
									}
								}	
							});	
							$('groupdiscount').send();							
						});																																																																													
																															
						$('groupdiscount').addEvent('submit', function(e) {	
							document.groupdiscount.task.value= "add_groupdiscount";						
							
							var box = $('ajaxmessagebox_group');
							var log = $('list_group_discount');
							
							e.stop();
							if(document.groupdiscount.event_id.value == 0){
								alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG'); ?>");												
								return false;
							}else{																							
								//$('groupdiscount').send({
								this.set('send', {	
									update: 'list_group_discount',
									onRequest: function() {
										box.style.display="block";
										(function() {box.fade('in')}).delay(1000);																
										box.set('text', '<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS'); ?>');
									},
									onComplete: function(response) {								
										box.set('text', 'Saved...');
										(function() {box.fade('out')}).delay(1000);		
										//box.style.display="";
										if(document.groupdiscount.task.value == "add_groupdiscount")	{							
											log.set('html', response);
										}
									}						
								});
								this.send();
							}
						});
					});																																				
				</script>
			<div id="add_group_discount">
			<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform" width="300px" style="height:auto">
				<tr>
					<td width="150px" valign="top"> <?php echo JText::_('ADMIN_EVENTS_GROUP_NUMBER_TICKET'); ?> </td>									
					<td valign="top"><input  type="text" name="min_tickets" id="min_tickets" class="inputbox" size="8" /></td>
				</tr>

				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_AMOUNT');?> </td>
					<td valign="top"> 
						<input type="text" name="discount_amount" id="discount_amount" class="inputbox" size="8" />
					</td>									
				</tr>
				
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_TYPE'); ?></td>
					<td> 
						<input type="radio" class="inputbox" id="discount_type" name="discount_type" value="P" checked /> %
						<input type="radio" class="inputbox" id="discount_type" name="discount_type" value="A" /> <?php echo $this->regpro_config['currency_sign'];?>
					</td>
				</tr>
																													
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">										
						<input type="submit" class="button" id="add" value="add" />
						<input type="button" class="button" value="reset" onclick="resetform_groupdiscount();" />
					</td>
				</tr>							
			</table>
			</div>
			<div id="ajaxmessagebox_group"></div>
			<table border="0" class="adminform" cellpadding="0" cellspacing="0">							
				<tr>								
					<td colspan="7">										
						&nbsp;<a class="toolbar" id="editlink_groupdiscount" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_EDIT');?> </a> &nbsp;&nbsp; <a class="toolbar" id="removelink_groupdiscount" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_REMOVE'); ?> </a>
					</td>												
				</tr>
				<tr>
					<td colspan="2" style="vertical-align:top">
						<div id="list_group_discount" style="overflow:auto; height: 150px;" class="my-group-discount">										
						<table border="1" cellpadding="2" cellspacing="0" class="adminform">
						<tr>
							<td width="10px">
								<input type="checkbox" name="toggle" value="" onClick="paymentcheckAll_discount_group(<?php echo count( $this->row->event_discounts); ?>);" />
							</td>
							<td>
								<strong><?php echo JText::_('ADMIN_EVENTS_GROUP_MINIMUM_TICKETS'); ?></strong>
							</td>										
							<td>
								<strong><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_PER_TICKET'); ?></strong>
							</td>
						</tr>

						<?php
							$n = count($this->row->event_discounts);
							$i=0;
							$k = 0;
																		
							foreach ($this->row->event_discounts as $discount)
							{
								if($discount->discount_name == 'G'){
									$pchecked 	= JHTML::_('grid.checkedout',   $discount, $i );
							?>

							<tr> 												
								<td><?php echo $pchecked;?></td>
								<td style="text-align:center"><?php echo $discount->min_tickets;?></td>
								<td style="text-align:center">
									<?php
										if($discount->discount_type == 'A'){
											echo $this->regpro_config['currency_sign']."&nbsp;".$discount->discount_amount;
										}else{
											echo $discount->discount_amount."&nbsp;%";
										}
									?>
								</td>												
							</tr>
							<?php							
									}
									
								$i++;	
							}
							echo "</table>";
							if(count($this->row->event_discounts) <= 0)
								echo JText::_('FRONTEND_NO_RECORD');
							?>										
					  
					  </div>
				  </td>
			  </tr>									
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>								
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="myevents" />	
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="discount_name" id="discount_name" value="G"/>
			<input type="hidden" name="boxchecked" value="0" />									
				
		<?php	//echo $tabs->endPanel();
			echo JHtml::_('bootstrap.endTab'); ?>					
		
		
		</form>

		<?php //echo $tabs->startPanel(JText::_('ADMIN_EVENTS_EARLY_DISCOUNT'),'payment-page'); 
		echo JHtml::_('bootstrap.addTab', 'myTab', 'payment-page1', JText::_('ADMIN_EVENTS_EARLY_DISCOUNT'), true);?>

		<form name="earlydiscount" id="earlydiscount" action="index.php" method="post">
																							
			<style type="text/css" media="screen">
				#ajaxmessagebox_early {
					margin-bottom:10px;
					width: auto;
					padding: 4px;
					border: solid 1px #DEDEDE;
					background: #FFFFCC;
					display: none;
					text-align:center;
														
				}
			</style>

			<link rel="stylesheet" href="<?php echo JURI::base().'components/com_registrationpro/assets/css/jquery-ui.css'; ?>">
			<script src="<?php echo JURI::base().'components/com_registrationpro/assets/javascript/jquery-ui.js'; ?>"></script>
			<script language="javascript">		
						jQuery(document).ready(function(){
								 jQuery( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
							});								
					function paymentcheckAll_discount_early( n, fldName ) {
					  if (!fldName) {
						 fldName = 'cb';
					  }
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
						} else {
							f.boxchecked.value = 0;
						}
					}
						
					function resetform_earlydiscount(){
						var frm = document.earlydiscount;
						frm.early_discount_date.value	= "";
						frm.discount_amount.value 		= "";
						if(frm.id) frm.id.value			= "";
					}											
													
					window.addEvent('domready', function(e){
																
						var box = $('ajaxmessagebox_early');
						//var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});	
																											
						$('editlink_earlydiscount').addEvent('click', function(e) {																					
							document.earlydiscount.task.value = 'edit_earlydiscount';											
							
							var log = $('add_early_discount');
													
							$('earlydiscount').set('send', {
								update: 'add_early_discount',
								onRequest: function() {
									box.style.display="block";
									(function() {box.fade('in')}).delay(100);							
									box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {								
									box.set('text', 'Done...');
									(function() {box.fade('out')}).delay(1000);		
									
									if(document.earlydiscount.task.value == "edit_earlydiscount"){							
										log.set('html', response);
										jQuery('#datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
									}
								}
							});	
							$('earlydiscount').send();							
						});
						
						$('removelink_earlydiscount').addEvent('click', function(e) {
							resetform_earlydiscount();
							document.earlydiscount.task.value = 'remove_earlydiscount';	
							
							var log = $('list_early_discount');
														
							$('earlydiscount').set('send', {
								update: 'list_early_discount',
								onRequest: function() {
									box.style.display="block";
									(function() {box.fade('in')}).delay(100);							
									box.set('text', '<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS'); ?>');
								},
								onComplete: function(response) {								
									box.set('text', '<?php echo JText::_('ADMIN_RECORD_DELETED'); ?>');
									(function() {box.fade('out')}).delay(1000);	
									
									if(document.earlydiscount.task.value == "remove_earlydiscount"){							
										log.set('html', response);
									}								
								}	
							});
							$('earlydiscount').send();								
						});																																																																													
																															
						$('earlydiscount').addEvent('submit', function(e) {	
							document.earlydiscount.task.value= "add_earlydiscount";	
							
							var log = $('list_early_discount');					
							
							e.stop();
							if(document.earlydiscount.event_id.value == 0){
								alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG'); ?>");												
								return false;
							}else{																							
								this.set('send', {
									update: 'list_early_discount',
									onRequest: function() {
										box.style.display="block";
										(function() {box.fade('in')}).delay(100);							
										box.set('text', '<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS'); ?>');
									},
									onComplete: function(response) {								
										box.set('text', 'Saved...');
										(function() {box.fade('out')}).delay(1000);								
										
										if(document.earlydiscount.task.value == "add_earlydiscount"){							
											log.set('html', response);
										}
									}						
								});
								this.send();
							}
						});
					});																																				
				</script>
			<div id="add_early_discount">
			<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform" width="300px" style="height:auto">
				<tr>
					<td width="150px" valign="top"> <?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_DATE'); ?> </td>									
					<td valign="top">
					<!-- <input id="early_discount_date" name="early_discount_date" value="<?php echo $this->row->discount_amount; ?>" size="15" maxlength="10" readonly> 
					<input class="button" value="..." onclick="return showCalendar('early_discount_date', '%Y-%m-%d');" type="reset"> -->
					<?php
											  /* echo JHTML::_('calendar'
											   , $this->row->early_discount_date
											  , 'early_discount_date'
											  , 'early_discount_date'
											  , '%Y-%m-%d'
											  , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); */
								?>
								<input id="datepicker" name="early_discount_date" value="<?php echo $this->row->early_discount_date; ?>" size="15" maxlength="10" />
					</td>
				</tr>

				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_AMOUNT');?> </td>
					<td valign="top"> 
						<input type="text" name="discount_amount" id="discount_amount" class="inputbox" size="8" />
					</td>									
				</tr>
				
				<tr>
					<td valign="top"><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_TYPE'); ?></td>
					<td> 
						<input type="radio" class="inputbox" id="discount_type" name="discount_type" value="P" checked /> %
						<input type="radio" class="inputbox" id="discount_type" name="discount_type" value="A" /> <?php echo $this->regpro_config['currency_sign'];?>
					</td>
				</tr>
																													
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">										
						<input type="submit" class="button" id="add" value="add" />
						<input type="button" class="button" value="reset" onclick="resetform_earlydiscount();" />
					</td>
				</tr>							
			</table>
			</div>
			<div id="ajaxmessagebox_early"></div>
			<table border="0" class="adminform" cellpadding="0" cellspacing="0">							
				<tr>								
					<td colspan="7">										
						&nbsp;<a class="toolbar" id="editlink_earlydiscount" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_EDIT');?> </a> &nbsp;&nbsp; <a class="toolbar" id="removelink_earlydiscount" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_REMOVE'); ?> </a>
					</td>												
				</tr>
				<tr>
					<td colspan="2" style="vertical-align:top">
						<div id="list_early_discount" style="overflow:auto; height: 150px;" class="my-early-discount">										
						<table border="1" cellpadding="2" cellspacing="0" align="center" class="adminform">
						<tr>
						<td width="10px"><input type="checkbox" name="toggle" value="" onClick="paymentcheckAll_discount_early(<?php echo count( $this->row->event_discounts); ?>);" /></td>
						<td width="150px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_DATE'); ?></strong></td>										
						<td width="150px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_PER_TICKET'); ?></strong></td>
						</tr>

						<?php
							$n = count($this->row->event_discounts);
							$i=0;
							$k = 0;
																		
							foreach ($this->row->event_discounts as $discount)
							{
								if($discount->discount_name == 'E'){
									$pchecked 	= JHTML::_('grid.checkedout',   $discount, $i );
							?>

							<tr> 												
								<td><?php echo $pchecked;?></td>
								<td style="text-align:center"><?php echo $discount->early_discount_date;?></td>
								<td style="text-align:center">									
									<?php
										if($discount->discount_type == 'A'){
											echo $this->regpro_config['currency_sign']."&nbsp;".$discount->discount_amount;
										}else{
											echo $discount->discount_amount."&nbsp;%";
										}
									?>
								</td>												
							</tr>
							<?php
								}
								$i++;	
							}
							echo "</table>";
							if(count($this->row->event_discounts) <= 0)
								echo JText::_('FRONTEND_NO_RECORD');
							?>										
					  
					  </div>
				  </td>
			  </tr>									
			</table>
			<?php echo JHTML::_( 'form.token' ); ?>								
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="myevents" />	
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="discount_name" id="discount_name" value="E"/>
			<input type="hidden" name="boxchecked" value="0" />														
		<?php
			echo JHtml::_('bootstrap.endTab');
		
			//$tabs->endPane();
		?>
		
		</form>	
		<!-- Session Tab Starts (17th Oct 2013)-->
		
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'session', JText::_('ADMIN_EVENTS_SESSION'), true);?>
	<form name="session" id="session" action="index.php" method="post">																					
	<style type="text/css" media="screen">
		#ajaxmessagebox_session {
			margin-bottom:10px;
			width: auto;
			padding: 4px;
			border: solid 1px #DEDEDE;
			background: #FFFFCC;
			display: none;
			text-align:center;
												
		}
	</style>

	<script language="javascript">
			jQuery(document).ready(function(){
				jQuery('#datepicker1').datepicker({ dateFormat: 'yy-mm-dd' });
			});																																		
			function sessioncheckAll( n, fldName ) {
			  if (!fldName) {
				 fldName = 'cb';
			  }
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
				} else {
					f.boxchecked.value = 0;
				}
			}
				
			function resetform_session(){
				var frm = document.session;
				//frm.page_header.value	= "";
				frm.title.value 		= "";
				frm.description.value	= "";
				//frm.weekday.value 		= "";				
				frm.session_date.value	= "";
				frm.session_start_time.value	= "";
				frm.session_stop_time.value	= "";
				frm.fee.value 			= "";								
				if(frm.id) frm.id.value	= "";
			}											
			
			function session_uporder(id) {
					var f = document.session;
					cb = eval( 'f.' + id );
					if (cb) {
						for (i = 0; true; i++) {
							cbx = eval('f.cb'+i);
							if (!cbx) break;
							cbx.checked = false;
						} // for
						cb.checked = true;
						f.boxchecked.value = 1;
						//submitbutton(task);
						f.task.value = 'orderupsessions';
						
						/*var el = $('paymentform');
						el.send({
							update: 'list_ticket'												
						});*/	
						
						var box = $('ajaxmessagebox_session');
						var el = $('session');
						
						var log = $('list_session');
						
						el.set('send', {
							onRequest: function() {
								box.style.display="block";
								box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {
								box.set('text', 'Done.');															
								(function() {box.fade('out')}).delay(2000);	
								box.style.display="";
								if(document.session.task.value == "orderupsessions")	{						
									log.set('html', response);
								}							
							}
						});
						el.send();
				
					}
				}
				
				function session_downorder(id) {
					var f = document.session;
					cb = eval( 'f.' + id );
					if (cb) {
						for (i = 0; true; i++) {
							cbx = eval('f.cb'+i);
							if (!cbx) break;
							cbx.checked = false;
						} // for
						cb.checked = true;
						f.boxchecked.value = 1;
						
						f.task.value = 'orderdownsessions';
						
						/*var el = $('paymentform');
						el.send({
							update: 'list_ticket'										
						});*/	
						
						var box = $('ajaxmessagebox');
						var el = $('session');
						
						var log = $('list_session');
						
						el.set('send', {
							onRequest: function() {
								box.style.display="block";
								box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {
								box.set('text', 'Done.');															
								(function() {box.fade('out')}).delay(2000);	
								box.style.display="";
								if(document.session.task.value == "orderdownsessions")	{						
									log.set('html', response);
								}							
							}
						});
						el.send();
					}
				}																			
											
			window.addEvent('domready', function(e){
														
				var box = $('ajaxmessagebox_session');
				//var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});																	
																									
				$('editlink_session').addEvent('click', function(e) {																					
					document.session.task.value = 'edit_session';											
					
					var log = $('add_session');
											
					$('session').set('send', {
						update: 'add_session',
						onRequest: function() {
							box.style.display="block";
							(function() {box.fade('in')}).delay(1000);							
							box.set('text', '<?php echo JText::_('ADMIN_REQUEST_IN_PROGRESS'); ?>');
						},
						onComplete: function(response) {																							
							box.set('text', 'Done...');
							(function() {box.fade('out')}).delay(1000);								
							if(document.session.task.value == "edit_session"){							
								log.set('html', response);
								jQuery('#datepicker1').datepicker({ dateFormat: 'yy-mm-dd' });
							}
						}
					});	
					$('session').send();							
				});
				
				$('removelink_session').addEvent('click', function(e) {
					resetform_session();
					document.session.task.value = 'remove_session';	
					
					var log = $('list_session');
												
					$('session').set('send', {
						update: 'list_session',
						onRequest: function() {
							box.style.display="block";
							(function() {box.fade('in')}).delay(1000);							
							box.set('text', '<?php echo JText::_('ADMIN_DELETE_IN_PROGRESS'); ?>');
						},
						onComplete: function(response) {								
							box.set('text', '<?php echo JText::_('ADMIN_RECORD_DELETED'); ?>');
							(function() {box.fade('out')}).delay(1000);								
							if(document.session.task.value == "remove_session"){						
								log.set('html', response);
							}
						}	
					});
					$('session').send();								
				});																																																																													
																													
				$('session').addEvent('submit', function(e) {	
					
					// form validations									
					/*if (document.session.title.value == "") {
						alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_ENTER_TITLE')." "; ?>");
						document.session.title.focus();
						return false;						
					}else*/ if (!document.session.session_date.value.match(/[0-9]{4}-[0-1][0-9]-[0-3][0-9]/gi) && document.session.session_date.value != "") {
						alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_WRONG_DATE_MSG')." "; ?>");
						document.session.session_date.focus();
						return false;
					}else if (!document.session.session_start_time.value.match(/[0-2][0-9]:[0-5][0-9]/gi) && document.session.session_start_time.value != "") {
						alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_WRONG_TIME_MSG')." "; ?>");
						document.session.session_start_time.focus();	
						return false;
					}else if (!document.session.session_stop_time.value.match(/[0-2][0-9]:[0-5][0-9]/gi) && document.session.session_stop_time.value != "") {
						alert("<?php echo JText::_('ADMIN_EVENTS_SESSION_WRONG_TIME_MSG')." "; ?>");
						document.session.session_stop_time.focus();	
						return false;
					}
					// end
					
					document.session.task.value= "add_session";																
																														
					var log = $('list_session');					
					
					e.stop();
					if(document.session.event_id.value == 0){
						alert("<?php echo JText::_('ADMIN_EVENTS_ADD_EVENT_TICKET_FIRST_MSG'); ?>");												
						return false;
					}else{																							
						this.set('send', {
							update: 'list_session',
							onRequest: function() {
								box.style.display="block";
								(function() {box.fade('in')}).delay(1000);							
								box.set('text', '<?php echo JText::_('ADMIN_SAVE_IN_PROGRESS'); ?>');
							},
							onComplete: function(response) {										
								box.set('text', 'Saved...');
								(function() {box.fade('out')}).delay(1000);								
								if(document.session.task.value == "add_session"){							
									log.set('html', response);
								}
							}						
						});
						this.send();
					}
					resetform_session();
				});												
			});																																				
		</script>
	
	<div id="add_session">
	<table cellpadding="2" cellspacing="0" align="center" class="adminform">		
		<tr>
			<td style="vertical-align:top; text-align:right;" width="150px;">
				<?php echo JText::_('ADMIN_EVENTS_SESSION_HEADER'); ?>
			</td>			
			<td>		
				<textarea name="session_page_header" id="session_page_header" class="inputbox" style="width:450px; height:120px;"><?php echo $this->row->session_page_header; ?></textarea>	
			</td>
		</tr>					
		
		<tr>	
			<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?> <?php echo JText::_('ADMIN_EVENTS_SESSION_TITLE'); ?></td>
			<td style="vertical-align:top;"> <input type="text" name="title" id="title" class="inputbox" style="width:450px;" /> </td>	
		</tr>
		
		<tr>	
			<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION'); ?></td>
			<td style="vertical-align:top;"> <textarea name="description" id="description" class="inputbox" style="width:450px; height:80px;"></textarea> </td>	
		</tr>
		
		<!--<tr>	
			<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_WEEKDAY'); ?></td>
			<td style="vertical-align:top;"><?php echo $this->Lists['weekdays']; ?> </td>
		</tr>-->
		
		<tr>
			<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?> <?php echo JText::_('ADMIN_EVENTS_SESSION_DATE'); ?> </td>
			<td style="vertical-align:top;">
				<?php
				/* echo JHTML::_('calendar'
						  , $this->row->session_date
						  , 'session_date'
						  , 'session_date'
						  , '%Y-%m-%d'
						  , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); */
				?><input id="datepicker1" name="session_date" value="<?php echo $this->row->session_date; ?>" size="15" maxlength="10"/>
				<b>( <?php echo JText::_('ADMIN_EVENTS_SESSION_DATE_NOTICE'); ?> )</b>
			</td>
		</tr>
		
		<tr>	
			<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?> <?php echo JText::_('ADMIN_EVENTS_SESSION_TIME'); ?></td>
			<td style="vertical-align:top;">
				<div style="float:left;"><input type="text" name="session_start_time" id="session_start_time" class="inputbox" size="5" maxlength="5" ></div>
				<div style="float:left; margin-left:5px; margin-right:5px;"><?php echo JText::_('ADMIN_EVENTS_SESSION_TIME_SAPERATOR'); ?></div>
				<div style="float:left;"><input type="text" name="session_stop_time" id="session_stop_time" class="inputbox" size="5" maxlength="5" ></div>
				<div style="float:left; margin-left:5px; margin-right:5px;"><b>( <?php echo JText::_('ADMIN_EVENTS_SESSION_TIME_NOTICE'); ?> )</b></div>				
			</td>
		</tr>
		
		<tr>
			<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_FEE'); ?></td>
			<td style="vertical-align:top;"> <input type="text" name="fee" id="fee" class="inputbox" size="8" /> </td>		
		</tr>
																																						
		<tr>
			<td valign="top">&nbsp;</td>
			<td valign="top">										
				<input type="submit" class="button" id="add" value="add" />
				<input type="button" class="button" value="reset" onclick="resetform_session();" />
			</td>
		</tr>							
	</table>
	</div>
	<div id="ajaxmessagebox_session"></div>
	<table border="0" class="adminform" cellpadding="0" cellspacing="0">							
		<tr>								
			<td>										
				&nbsp;<a class="toolbar" id="editlink_session" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_SESSION_EDIT');?> </a> &nbsp;&nbsp; <a class="toolbar" id="removelink_session" href="javascript:void(0);"><?php echo JText::_('ADMIN_EVENTS_SESSION_REMOVE'); ?> </a>
			</td>												
		</tr>
		<tr>
			<td >
				<!--<div id="list_session" style="overflow:auto; height: 150px;">-->
				<div id="list_session">										
				<table border="1" cellpadding="2" cellspacing="0" class="adminform">
				<tr>
				<td width="10px"><input type="checkbox" name="toggle" value="" onClick="sessioncheckAll(<?php echo count( $this->row->event_sessions); ?>);" /></td>
				<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_TITLE'); ?></strong></td>										
				<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION'); ?></strong></td>
				<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_FEE'); ?></strong></td>			
				<td><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DAY'); ?></strong></td>			
				<td width="5px" colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_ORDER'); ?></strong></td>			
				</tr>

				<?php
					$n = count($this->row->event_sessions);
					$i=0;
					$k = 0;
					if(is_array($this->row->event_sessions)){											
					foreach ($this->row->event_sessions as $session)
					{						
						$pchecked 	= JHTML::_('grid.checkedout',   $session, $i );
					?>

					<tr> 												
						<td><?php echo $pchecked;?></td>
						<td style="text-align:center"><?php echo $session->title;?></td>
						<td style="text-align:center"><?php echo $session->description;?></td>
						<td style="text-align:center">									
							<?php
								if($session->feetype == 'A'){
									echo $this->regpro_config['currency_sign']."&nbsp;".$session->fee;
								}else{
									echo $session->fee."&nbsp;%";
								}
							?>
						</td>
						
						<td style="text-align:center">
							<?php echo registrationproHelper::getFormatdate($this->regpro_config['session_dateformat'], $session->session_date); ?> <br/>
							<?php echo registrationproHelper::getFormatdate($this->regpro_config['session_timeformat'], $session->session_start_time); ?> -  <?php echo registrationproHelper::getFormatdate($this->regpro_config['formattime'], $session->session_stop_time); ?>							
						</td>
						
						<td style="text-align:right">												
						<?php 
							if ($i > 0) { ?>
								<a href="javascript: void(0);" id="orderupsessions" onclick="return session_uporder('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
						  <?php	
							} ?>
						</td>
						<td style="text-align:left"><?php
							if ($i < $n-1) { ?>
								<a href="javascript: void(0);" id="orderdownsessions" onclick="return session_downorder('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
						  <?php		
							}?>
						</td>
																		
					</tr>
					<?php						
						$i++;																								
					}
					}
					
					if(count($this->row->event_sessions) <= 0)
						echo "<tr><td colspan='6' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
					?>										
			  </table>
			  </div>
		  </td>
	  </tr>									
	</table>
		
			<?php echo JHTML::_( 'form.token' ); ?>								
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="myevents" />	
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="event_id" value="<?php echo $this->row->id; ?>" />	
			<input type="hidden" name="boxchecked" value="0" />														
		</form>
		<?php 
		echo JHtml::_('bootstrap.endTab');
		} // end event id condition
			} // end copy event condition
		?>
		
		<?php echo JHtml::_('bootstrap.endTabSet');?>						
			</td>
			</tr>
			<tr><td> <?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></td></tr>
			
			
			<?php
				if($this->task == 'copy') {
			?>
			<tr><td> <?php echo JText::_('ADMIN_COPY_EVENT_NOTE'); ?></td></tr>
			<?php 
				}
			?>
			
		</table>	
		</td>
		</tr>	
		
		<tr>
			<td> <input type="button" value="Save Event" onclick="return submitbutton('save');" /> </td>
		</tr>
	</table>
</div>	
</div>