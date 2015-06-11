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
?>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == '') {form.task.value = "";}
			submitform( pressbutton );
		}
	</script>
	<style>.nav-tabs li{width: 157px;text-align: center;}</style>
		<div class="email-tab">
			<ul class="nav nav-tabs" id="my-responsive-tabs">
				<li class="active"><a href="#admin_confirm_email" data-toggle="tab"><?php echo JText::_('ADMIN_EVENTS_SETT_MAIN_ADMIN_EMAIL_CONFIRM'); ?></a></li>
				<li><a href="#event_admin_confirm_email" data-toggle="tab"><?php echo JText::_('ADMIN_EVENTS_SETT_EVENT_ADMIN_EMAIL_CONFIRM'); ?></a></li>
				<li><a href="#user_confirm_email" data-toggle="tab"><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_CONFIRM'); ?><br /><br /></a></li>
				<li><a href="#event_status_email" data-toggle="tab"><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_STATUS'); ?><br /><br /></a></li>
				<li><a href="#event_reminder_email" data-toggle="tab"><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_REMINDER'); ?><br /><br /></a></li>
				<li><a href="#email_to_registered" data-toggle="tab"><?php echo JText::_('ADMIN_EMAIL_TO_REGISTERUSERS'); ?><br /><br /></a></li>
				<li><a href="#email_to_moderators" data-toggle="tab"><?php echo JText::_('ADMIN_EMAIL_TO_MODERATORUSERS'); ?><br /><br /></a></li>
			</ul>
		</div>
		<div class="tab-content">

			<div class="tab-pane active" id="admin_confirm_email" style="border:none;padding:10px;">
				<span class="span4 y-offset">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_MAIN_ADMIN_EMAIL_CONFIRM_SUBJ');?></b>
				</span>
				<span class="span8 y-offset no-gutter">			
					<input type="text" name="mainadminemailconfirmsubject" value="<?php echo $this->row['mainadminemailconfirmsubject']; ?>" style="width:100%;">
				</span>
				<span class="span8 no-gutter" style="margin-right:5px;">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_MAIN_ADMIN_EMAIL_CONFIRM_BODY');?></b>
					<?php 
						echo $this->editor->display('mainadminemailconfirmbody', $this->row['mainadminemailconfirmbody'], '100%', '300', '75', '20', array("readmore","pagebreak"));
					?>
				</span>
				<span class="span4 no-gutter">
					<br/>
					<?php echo JText::_('ADMIN_EVENTS_SETT_MAIN_ADMIN_EMAIL_CONFIRM_DESC'); ?>
				</span>
			</div>
			
			<div class="tab-pane" id="event_admin_confirm_email" style="border:none;padding:10px;">
				<span class="span4 y-offset">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EVENT_ADMIN_EMAIL_CONFIRM_SUBJ'); ?></b>
				</span>
				<span class="span8 y-offset no-gutter">			
					<input type="text" name="eventadminemailconfirmsubject" value="<?php echo $this->row['eventadminemailconfirmsubject']; ?>" style="width:100%;">
				</span>
				<span class="span8 no-gutter" style="margin-right:5px;">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EVENT_ADMIN_EMAIL_CONFIRM_BODY');?></b>
						<?php 
							echo $this->editor->display('eventadminemailconfirmbody', $this->row['eventadminemailconfirmbody'], '100%', '300', '75', '20', array("readmore","pagebreak"));
						?>
				</span>
				
				<span class="span4 no-gutter">
					<br/>
					<?php echo JText::_('ADMIN_EVENTS_SETT_EVENT_ADMIN_EMAIL_CONFIRM_DESC');?>
				</span>
			</div>
			
			<div class="tab-pane" id="user_confirm_email" style="border:none;padding:10px;">
				<span class="span4 y-offset">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_CONFIRM_SUBJ');?></b>
				</span>
				<span class="span8y-offset no-gutter">			
					<input type="text" name="emailconfirmsubject" value="<?php echo $this->row['emailconfirmsubject']; ?>" style="width:100%;">
				</span>
				<span class="span8 y-offset no-gutter" style="margin-right:5px;">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_CONFIRM_BODY'); ?></b>
						<?php 
							echo $this->editor->display( 'emailconfirmbody',  $this->row['emailconfirmbody'] , '100%', '300', '75', '20', array("readmore","pagebreak"));
						?>
				</span>
				
				<span class="span4 y-offset no-gutter">
					<br/>
					<?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_CONFIRM_DESC'); ?>
				</span>
			</div>

			<div class="tab-pane" id="event_status_email" style="border:none;padding:10px;">
				<span class="span4 y-offset no-gutter">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_STATUS_SUBJ');?></b>
				</span>
				<span class="span8 y-offset no-gutter">
					<input type="text" name="emailstatussubject" value="<?php echo $this->row['emailstatussubject']; ?>" style="width:100%;">
				</span>
				<span class="span8 y-offset no-gutter" style="margin-right:5px;">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_STATUS_BODY');?></b>
						<?php 
							echo $this->editor->display( 'emailstatusbody',  $this->row['emailstatusbody'] , '100%', '300', '75', '20', array("readmore","pagebreak"));
						?>
				</span>		
				<span class="span4 y-offset no-gutter">
					<br/>
					<?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_STATUS_DESC');?>
				</span>
			</div>

			<div class="tab-pane" id="event_reminder_email" style="border:none;padding:10px;">
				<span class="span4 y-offset">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_REMINDER_SUBJ');?></b>
				</span>
				<span class="span8 y-offset no-gutter">			
					<input type="text" name="emailremindersubject" value="<?php echo $this->row['emailremindersubject']; ?>" style="width:100%;">
				</span>
				<span class="span8 no-gutter" style="margin-right:5px;">
					<b><?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_REMINDER_BODY');?></b>
						<?php 
							echo $this->editor->display( 'emailreminderbody',  $this->row['emailreminderbody'], '100%', '300', '75', '20',array("readmore","pagebreak"));
						?>
				</span>		
				<span class="span4 no-gutter">
					<br/>
					<?php echo JText::_('ADMIN_EVENTS_SETT_EMAIL_REMINDER_DESC');?>
				</span>
			</div>

			<div class="tab-pane" id="email_to_registered" style="border:none;padding:10px;">
				<span class="span3 y-offset">
					<b><?php echo JText::_('ADMIN_EMAIL_TO_REGISTERUSERS_SUBJECT');?></b>
				</span>
				<span class="span9 y-offset no-gutter">			
					<input type="text" name="emailtoregistersubject" value="<?php echo $this->row['emailtoregistersubject']; ?>" style="width:100%;">
				</span>
				<span class="span12 no-gutter">
					<b><?php echo JText::_('ADMIN_EMAIL_TO_REGISTERUSERS_BODY');?></b>
						<?php 
							echo $this->editor->display( 'emailtoregisterbody',  $this->row['emailtoregisterbody'] , '100%', '300', '75', '20',array("readmore","pagebreak"));
						?>
				</span>		
				
			</div>

			<div class="tab-pane" id="email_to_moderators" style="border:none;padding:10px;">
				<span class="span3 y-offset">
					<b><?php echo JText::_('ADMIN_EMAIL_TO_MODERATORUSERS_SUBJECT');?></b>
				</span>
				<span class="span9 y-offset no-gutter">			
					<input type="text" name="moderatoremailsubject" value="<?php echo $this->row['moderatoremailsubject']; ?>" style="width:100%;">
				</span>
				<span class="span8 no-gutter" style="margin-right: 5px;">
					<b><?php echo JText::_('ADMIN_EMAIL_TO_MODERATORUSERS_BODY')." "; ?></b>
						<?php 
							echo $this->editor->display( 'moderatoremailbody',  $this->row['moderatoremailbody'] , '100%', '300', '75', '20',array("readmore","pagebreak")) ;
						?>
				</span>		
				<span class="span4 no-gutter">
					<br/>
					<?php echo JText::_('ADMIN_EMAIL_TO_MODERATORUSERS_DESC');?>
				</span>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php echo JHTML::_( 'form.token' ); ?>

	<input type="hidden" name="id" value="">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="controller" value="emails">
	<input type="hidden" name="option" value="com_registrationpro">
</form>
<div class="clearfix"></div>
</div>