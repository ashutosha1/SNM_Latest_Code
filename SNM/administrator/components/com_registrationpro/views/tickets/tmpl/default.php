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

$n = count($this->rows);
$i=0;
$k = 0;


if($this->ticket_type == "E"){
	$toggle_func 		= "paymentcheckAll";
	$ordering_up_func 	= "payment_uporder";
	$ordering_down_func = "payment_downorder";
	$cbname = "cb";
	$id1 = "edit_ticket";
	$id2 = "remove_ticket";
}else{
	$toggle_func 		= "paymentcheckAll_add";
	$ordering_up_func 	= "payment_uporder_add";
	$ordering_down_func = "payment_downorder_add";
	$cbname = "cb_add";
	$id1 = "edit_prod";
	$id2 = "remove_prod";
}

?>
<span class="span12 y-offset no-gutter">
<table class="table_tickets">
	<tr id="table_tickets_header">
	<td width="10px"><input type="checkbox" name="toggle" value="" onClick="<?php echo $toggle_func; ?>(<?php echo count($this->rows); ?>);" /></td>
	<td><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_NAME'); ?></strong></td>
	<td width="80px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_PRICE'); ?></strong></td>
	<td width="40px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TAX'); ?></strong></td>
	<td width="80px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL'); ?></strong></td>
	<td width="40px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_QTY'); ?></strong></td>
	<td width="80px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_START'); ?></strong></td>
	<td width="80px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_END'); ?></strong></td>
	<td colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_LIST_ORDER'); ?></strong></td>
	</tr>

<?php		
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
		$product = &$this->rows[$i];

		if($this->ticket_type == $product->type){														
?>
			<tr> 												
				<td><input id="<?php echo $cbname;?><?php echo $i;?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $product->id;?>" name="cid[]"></td>
				<td><?php echo $product->product_name;?></td>
				<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->product_price; ?></td>
				<td style="text-align:right"><?php echo $product->tax. '&nbsp;%';?></td>
				<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->total_price; ?></td>
				<td style="text-align:right"><?php echo $product->product_quantity; ?></td>
				<td style="text-align:right"><?php echo $product->ticket_start; ?></td>
				<td style="text-align:right"><?php echo $product->ticket_end; ?></td>
				<td width=20><?php
					if ($i > 0) { ?>
						<a href="javascript:void(0);" id="orderuppayments" onclick="return <?php echo $ordering_up_func;?>('<?php echo $cbname;?><?php echo $i;?>');"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
				  <?php	
					} ?>
				</td>
				<td width=20><?php
					if ($i < $n-1) { ?>
						<a href="javascript:void(0);" id="orderdownpayments" onclick="return <?php echo $ordering_down_func;?>('<?php echo $cbname;?><?php echo $i;?>');"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
				  <?php		
					}?>
				</td>
			</tr>
	<?php
		}												
	}
	
	if(count($this->rows) <= 0){
		echo "<tr><td colspan='9' style='text-align:center'>No Record Found</td></tr>";
	}
	
	if(intval($this->event_id) > 0){
		echo "<input type='hidden' name='regpro_dates_id' value='".$this->event_id."' />";
	}
	?>			
</table>
</span>