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

$n = count($this->rows);
$i=0;
$k = 0;


if($this->ticket_type == "E"){
	$toggle_func 		= "paymentcheckAll";
	$ordering_up_func 	= "payment_uporder";
	$ordering_down_func = "payment_downorder";
}else{
	$toggle_func 		= "paymentcheckAll_add";
	$ordering_up_func 	= "payment_uporder_add";
	$ordering_down_func = "payment_downorder_add";
}

?>
<table border="1" cellpadding="2" cellspacing="0" align="center" class="adminform" id="adminform" width="300px" style="height:auto">
	<tr>
	<td width="10px"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" /></td>
	<td width="100px"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_NAME'); ?></strong></td>
	<td width="80px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_PRICE'); ?></strong></td>
	<td width="60px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TAX'); ?></strong></td>
	<td width="60px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_TOTAL'); ?></strong></td>
	<td width="40px" style="text-align:right"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_TICKET_LIST_QTY'); ?></strong></td>
	<td width="5px" colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_PAYMENT_LIST_ORDER'); ?></strong></td>
	</tr>

<?php		
	//foreach ($this->rows as $product)
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
	{
		$product = $this->rows[$i];
		//echo "<pre>"; print_r($product); exit;
		
		if($this->ticket_type == $product->type){														
			$pchecked 	= JHTML::_('grid.checkedout',   $product, $i );
	?>
			<tr> 												
				<td><?php echo $pchecked;?></td>
				<td><?php echo $product->product_name;?></td>
				<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->product_price; ?></td>
				<td style="text-align:right"><?php echo $product->tax. '&nbsp;%';?></td>
				<td style="text-align:right"><?php echo $this->regpro_config['currency_sign'].'&nbsp;'.$product->total_price; ?></td>
				<td style="text-align:right"><?php echo $product->product_quantity; ?></td>
				
				<td style="text-align:right"><?php
					if ($i > 0) { ?>
						<a href="javascript: void(0);" id="orderuppayments" onclick="return <?php echo $ordering_up_func;?>('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
				  <?php	
					} ?>
				</td>
				<td style="text-align:left"><?php
					if ($i < $n-1) { ?>
						<a href="javascript: void(0);" id="orderdownpayments" onclick="return <?php echo $ordering_down_func;?>('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
				  <?php		
					}?>
				</td>
			</tr>
	<?php
		}												
	}
	
	if(count($this->rows) <= 0){
		echo "<tr><td colspan='6' style='text-align:center'>No Record Found</td></tr>";
	}
	
	if(intval($this->event_id) > 0){
		echo "<input type='hidden' name='regpro_dates_id' value='".$this->event_id."' />";
	}
	?>			
</table>