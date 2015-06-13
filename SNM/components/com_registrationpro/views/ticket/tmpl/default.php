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

//JHTML::_( 'behavior.mootools' );

if($this->row->type == "E"){
	$calc_function 	= "calculate_tot_amt()";
	$reset_function = "resetform()";
	$total_amount_span_id	= "totval";
	$id_identifier = "";
}else{
	$calc_function 	= "calculate_tot_amt_add()";
	$reset_function = "resetform_add()";
	$total_amount_span_id	= "totval_add";
	$id_identifier = "_add";
}

?>

<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform" width="300px" style="height:auto">
	<tr>
		<td width="100px" valign="top">
			<?php
				//this is where we show the add new product fields
				echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_NAME');
			?>
		</td>									
		<td><input  type="text" name="product_name" id="product_name" class="inputbox" size="20" value="<?php echo $this->row->product_name;?>"/><br /></td>
	</tr>

	<tr>
		<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_PRICE');?> </td>
		<td> <input id="add_price" type="text" name="product_price" id="product_price" class="inputbox" size="8" onblur="<?php echo $calc_function; ?>;" value="<?php echo $this->row->product_price; ?>" /> <?php echo $this->regpro_config['currency_sign'];?></td>									
	</tr>
	
	<tr>
		<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_TAX'); ?></td>
		<td><input id="tax" type="text" class="inputbox" name="tax" id="tax" size="4" onblur="<?php echo $calc_function; ?>;" value="<?php echo $this->row->tax;?>"/>&nbsp;%&nbsp; &nbsp;&nbsp; <span id="<?php echo $total_amount_span_id; ?>"><?php echo "Total:",$this->row->total_price;?></span></td>
	</tr>
	
	<tr>
		<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_QTY');?></td>
		<td valign="top"> <input type="text" name="product_quantity" id="product_quantity" class="inputbox" size="8px" value="<?php echo $this->row->product_quantity;?>"  /></td>
	</tr>
										
	<tr>
		<td valign="top"><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_DESC');?></td>
		<td> <textarea name="product_description" id="product_description" class="inputbox" cols="20" rows="2"><?php echo $this->row->product_description;?></textarea></td>
	</tr>
	
	<tr>
		<td id="label"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_START_DATE');?></td>
		<td><?php echo JHTML::_('calendar', $this->row->ticket_start, 'ticket_start', 'ticket_start'.$id_identifier , '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));?></td>
	</tr>
	
	<tr>
		<td id="label"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_END_DATE');?></td>
		<td><?php echo JHTML::_('calendar', $this->row->ticket_end, 'ticket_end', 'ticket_end'.$id_identifier , '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25', 'readonly'=>'true', 'maxlength'=>'19'));?></td>
	</tr>
	
	<tr>
		<td valign="top">&nbsp;</td>
		<td>										
			<input type="submit" class="button" id="add" value="Update" />
			<input type="button" class="button" value="reset" onclick="<?php echo $reset_function; ?>;" />
			<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
		</td>
	</tr>							
</table>