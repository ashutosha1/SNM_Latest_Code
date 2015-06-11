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

	<?php
		if($this->row->discount_name == "G") {
		$dname = "";
		$reset_function = 'resetform_groupdiscount();';
		$save_cmd = 'save_discount';
	?>
		<span class="span4 y-offset no-gutter">
			<?php echo JText::_('ADMIN_EVENTS_GROUP_NUMBER_TICKET');?>
		</span>
		<span class="span8 y-offset no-gutter">
			<input type="text" name="min_tickets" id="min_tickets" class="inputbox" size="8" value="<?php echo $this->row->min_tickets; ?>"/>
		</span>
		<br/>
	<?php
		} elseif ($this->row->discount_name == "E") {
			$dname = "e";
			$reset_function = 'resetform_earlydiscount();';
			$save_cmd = 'save_early';
			$cal_id = "early_discount_date".rand(0, 10000);
	?>
		<span class="span4 y-offset no-gutter">
			<?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_DATE');?>
		</span>
		<span class="span8 y-offset no-gutter">
			<div class="input-append">
				<input id="<?php echo $cal_id;?>" class="inputbox hasTooltip" type="text" maxlength="19" size="25" value="<?php echo $this->row->early_discount_date;?>" name="<?php echo $cal_id;?>" title="<?php echo $this->row->early_discount_date;?>" data-original-title="">
				<button id="<?php echo $cal_id;?>_img" class="btn" type="button"><i class="icon-calendar"></i></button>
			</div>
			<script language="javascript">addCalendar('<?php echo $cal_id;?>');</script>
		</span>
		<br/>	
	<?php
		}
	?>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_AMOUNT'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<input type="text" name="discount_amount" id="discount_amount" class="inputbox" size="8" value="<?php echo $this->row->discount_amount;?>" />
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_TYPE');?>
	</span>
	<span class="span8 y-offset no-gutter">
		<input style="margin-top:-5px;margin-right:4px;" type="radio" class="inputbox" id="discount_type" name="discount_type" value="P" <?php if($this->row->discount_type == 'P') echo "checked"; if($this->row->discount_type == '') echo "checked";?> />%
		<input style="margin-top:-5px;margin-left:15px;margin-right:4px;" type="radio" class="inputbox" id="discount_type" name="discount_type" value="A" <?php if($this->row->discount_type == 'A') echo "checked"; ?> />
		<?php echo $this->regpro_config['currency_sign'];?>
	</span>
	<br/>
	<span class="span12 y-offset no-gutter">
		<button class="btn btn-small btn-success" id="<?php echo $save_cmd;?>">Update</button>
		<input type="button" class="button btn btn-inverse" value="Reset" onclick="<?php echo $reset_function; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
	</span>
<script language="javascript">
	$('#<?php echo $save_cmd?>').click(function(e) {
		<?php echo $save_cmd?>(e);
	});
</script>