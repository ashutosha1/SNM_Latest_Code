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

/* JHTML::_( 'behavior.mootools' ); 

JHTML::_('behavior.modal', 'a.modal'); */

if($this->row->discount_name == "G"){
	$reset_function = "resetform_groupdiscount()";
}else{
	$reset_function = "resetform_earlydiscount()";
}
?>

<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform" width="300px" style="height:auto">

	
	<?php
		if($this->row->discount_name == "G"){
	?>
	<tr>
		<td valign="top" width="150px"><?php echo JText::_('ADMIN_EVENTS_GROUP_NUMBER_TICKET');?> </td>
		<td> <input  type="text" name="min_tickets" id="min_tickets" class="inputbox" size="8" value="<?php echo $this->row->min_tickets; ?>"/> </td>									
	</tr>
	<?php
		}elseif($this->row->discount_name == "E"){
	?>
	<tr>
		<td valign="top" width="150px"><?php echo JText::_('ADMIN_EVENTS_EARLY_DISCOUNT_DATE');?> </td>
		<td> 
			<!--<input id="early_discount_date" name="early_discount_date" value="<?php echo $this->row->early_discount_date; ?>" size="15" maxlength="10" readonly> 
			<input class="button" value="..." onclick="return showCalendar('early_discount_date', '%Y-%m-%d');" type="reset">-->
		<?php	
			/* echo JHTML::_('calendar'
						  , $this->row->early_discount_date
						  , 'early_discount_date'
						  , 'early_discount_date'
						  , '%Y-%m-%d'
						  , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); */
		?><input id="datepicker" name="early_discount_date" value="<?php echo $this->row->early_discount_date; ?>" size="15" maxlength="10" />
		</td>									
	</tr>
	<?php
		}
	?>
	
	<tr>
		<td valign="top"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_AMOUNT'); ?></td>
		<td> <input type="text" name="discount_amount" id="discount_amount" class="inputbox" size="8" value="<?php echo $this->row->discount_amount;?>" /> </td>
	</tr>
										
	<tr>
		<td valign="top"><?php echo JText::_('ADMIN_EVENTS_GROUP_DISCOUNT_TYPE');?></td>
		<td> 
			<input type="radio" class="inputbox" id="discount_type" name="discount_type" value="P" <?php if($this->row->discount_type == 'P') echo "checked"; if($this->row->discount_type == '') echo "checked";?> /> %
			<input type="radio" class="inputbox" id="discount_type" name="discount_type" value="A" <?php if($this->row->discount_type == 'A') echo "checked"; ?> /> <?php echo $this->regpro_config['currency_sign'];?>
		</td>
	</tr>
	
	<tr>
		<td valign="top">&nbsp;</td>
		<td>										
			<input type="submit" class="button" id="add" value="add" />
			<input type="button" class="button" value="reset" onclick="<?php echo $reset_function; ?>;" />
			<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
		</td>
	</tr>							
</table>