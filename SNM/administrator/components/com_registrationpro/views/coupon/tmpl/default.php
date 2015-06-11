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
JHtml::_('formbehavior.chosen', 'select');
// Load pane behavior
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');

//create the toolbar
$pagetitle = JText::_('EVENT_ADMIN_COUPONS_LBL_ADD');
if($this->row->id) $pagetitle = JText::_('EVENT_ADMIN_COUPONS_LBL_EDIT');

JToolBarHelper::title( $pagetitle, 'couponsedit' );
JToolBarHelper::apply();
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel();
JToolBarHelper::spacer();

?>
<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		if(!validateForm(form,false,false,false,false)){
		} else { submitform( pressbutton );}
	}
</script>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<span class="span12 y-offset">
		<b class="pull-right"><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE');?></b>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') ." ". JText::_('EVENT_ADMIN_COUPONS_LBL_TITLE'); ?>
	</span>
	<span class="span6">
		<input type="text"name="title" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_COUPONS_TITLE_EMPTY'); ?>" value="<?php echo $this->row->title; ?>" maxlength="50">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') ." ". JText::_('EVENT_ADMIN_COUPONS_LBL_START_DATE'); ?>
	</span>
	<span class="span6">
		<?php echo JHTML::_('calendar', $this->row->start_date, 'start_date', 'start_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19')); ?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') ." ". JText::_('EVENT_ADMIN_COUPONS_LBL_END_DATE'); ?>
	</span>
	<span class="span6">
		<?php echo JHTML::_('calendar', $this->row->end_date, 'end_date', 'end_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19')); ?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') ." ". JText::_('EVENT_ADMIN_COUPONS_LBL_CODE'); ?>
	</span>
	<span class="span6">
		<input type="text"name="code" alt="alnum|1|all|1|0|-_" emsg="<?php echo JText::_('ADMIN_SCRIPT_COUPONS_CODE_EMPTY'); ?>" value="<?php echo $this->row->code; ?>" size=55 maxlength=50 style="float:left;">
		<img src="components/com_registrationpro/assets/images/info_icon_24x24.png" style="margin-top:2px;" border="0" width="24" height="24" alt="More Info" class="editlinktip hasTip" title="<?php echo JText::_('EVENT_ADMIN_COUPONS_LBL_CODE_DESCRIPTION'); ?>"/>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') ." ". JText::_('EVENT_ADMIN_COUPONS_LBL_DISCOUNT'); ?>
	</span>
	<span class="span6">
		<input type="text"name="discount" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_COUPONS_DISCOUNT_EMPTY'); ?>" value="<?php echo $this->row->discount; ?>" size="10" maxlength="10">
		&nbsp;<input type="radio" name="discount_type" value="A" style="margin-top:-7px;" <?php if($this->row->discount_type == 'A') echo "checked"; if($this->row->discount_type == '') echo "checked"; ?> />
		<b style="font-size:16px;"><?php echo $this->regpro_config['currency_sign'];?></b>
		&nbsp;&nbsp;<input type="radio" name="discount_type" value="P" style="margin-top:-7px;" <?php if($this->row->discount_type == 'P') echo "checked"; ?>/>
		<b style="font-size:16px;">%</b>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('EVENT_ADMIN_COUPONS_LBL_LIMIT_AMOUNT'); ?>
	</span>
	<span class="span6">
		<input type="text"name="max_amount" value="<?php echo $this->row->max_amount; ?>" size="10" maxlength="10">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('EVENT_ADMIN_COUPONS_LBL_EVENTS'); ?>
	</span>
	<span class="span6">
		<?php echo $this->Lists['events'];?>
			<img src="components/com_registrationpro/assets/images/info_icon_24x24.png" style="margin-top:2px;" border="0" width="24" height="24" alt="More Info" class="editlinktip hasTip" title="<?php echo JText::_('EVENT_ADMIN_COUPONS_LBL_EVENTS_NOTE'); ?>" />
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_EVENTS_PUBLI'); ?>
	</span>
	<span class="span6">
		<fieldset class="radio btn-group btn-group-yesno">
			<?php echo $this->Lists['published'];?>
		</fieldset>
	</span>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="coupons" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>
</div>