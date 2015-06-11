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

if($this->row->type == "E"){
	$calc_function 	= "calculate_tot_amt()";
	$reset_function = "reset_form()";
	$total_amount_span_id = "totval";
	$datePickerName = "";
	$save_cmd = "save_ticket";
	$curr_sign = "curr_sign";
	$name = " Ticket Name :";
}else{
	$calc_function 	= "calculate_tot_amt_add()";
	$reset_function = "reset_form_add()";
	$total_amount_span_id = "totval_add";
	$datePickerName = "_prod";
	$save_cmd = "save_ticket_add";
	$curr_sign = "curr_sign_add";
	$name = " Product Name :";
}

?>
<script type="text/javascript">
window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });
</script>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . $name; ?>
</span>
<span class="span8 y-offset no-gutter">
	<input  type="text" name="product_name" id="product_name" class="inputbox" size="20" value="<?php echo $this->row->product_name;?>"/>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_PRICE');?>
</span>
<span class="span8 y-offset no-gutter">
	<input id="add_price" type="text" name="product_price" id="product_price" class="inputbox" size="8" onblur="<?php echo $calc_function; ?>;" value="<?php echo $this->row->product_price; ?>" style="float:left;" />
	<div id="<?php echo $curr_sign?>"><b><?php echo $this->regpro_config['currency_sign'];?></b></div>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_TAX'); ?>
</span>
<span class="span8 y-offset no-gutter">
	<input id="tax" type="text" class="inputbox" name="tax" id="tax" size="4" onblur="<?php echo $calc_function; ?>;" value="<?php echo $this->row->tax;?>"/>
	<b style="font-size:18px;font-style:bold;font-weight:700;margin-left:5px;">%</b>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	&nbsp;
</span>
<span class="span8 y-offset no-gutter">
	<span id="<?php echo $total_amount_span_id; ?>">
		<?php echo "Total Price with Taxes : ".$this->row->total_price;?> 
		<?php echo $this->regpro_config['currency_sign'];?>
	</span>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_QTY');?>
</span>
<span class="span8 y-offset no-gutter">
	<input type="text" name="product_quantity" id="product_quantity" class="inputbox" size="8px" value="<?php echo $this->row->product_quantity;?>"/>
	<img class="editlinktip hasTip" title="<?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_EVENTS_PAYMENT_TICKET_QTY_EDIT_TEXT');?>" src="components/com_registrationpro/assets/images/info_icon_24x24.png" border="0" width="24" height="24" style="vertical-align:top !important" />
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_DESC');?>
</span>
<span class="span8 y-offset no-gutter">
	<textarea name="product_description" id="product_description" class="inputbox" cols="20" rows="2">
		<?php echo $this->row->product_description;?>
	</textarea>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_START_DATE');?>
</span>
<span class="span8 y-offset no-gutter">
	<?php 
		echo JHTML::_('calendar', $this->row->ticket_start, 'ticket_start', 'ticket_start'.$datePickerName, '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true'));	
	?>
</span>
<br/>
<span class="span4 y-offset no-gutter">
	<?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_TICKET_END_DATE');?>
</span>
<span class="span8 y-offset no-gutter">
	<?php 
		echo JHTML::_('calendar', $this->row->ticket_end, 'ticket_end', 'ticket_end'.$datePickerName, '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19','readonly'=>'true')); 
	?>
</span>
<br/>
<span class="span12 y-offset no-gutter">
	<button class="btn btn-small btn-success" id="<?php echo $save_cmd;?>">Update</button>
	<input type="button" class="button btn btn-small btn-inverse" value="Reset" onclick="<?php echo $reset_function; ?>;" />
	<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
</span>

<script language="javascript">
	$('#<?php echo $save_cmd;?>').click(function(e) {
		<?php echo $save_cmd;?>(e);
	});
</script>