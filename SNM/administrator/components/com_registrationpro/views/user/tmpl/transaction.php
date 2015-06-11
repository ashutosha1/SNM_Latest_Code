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
JHTML::_('behavior.tooltip');
$registrationproHelper = new registrationproHelper;
//echo "<pre>"; print_r($this->regpro_config); exit;
?>

<table cellpadding="5" cellspacing="0" border="0" style="width:100%;">
	<tr>
		<td style="text-align:left">
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
			<tr>
				<th width="33%" class="title" align=left>
					<?php
						echo (($this->details->transaction[0]->first_name||$this->details->transaction[0]->last_name) ? ucfirst($this->details->transaction[0]->first_name).' '.ucfirst($this->details->transaction[0]->last_name) : '');
						echo (($this->details->urname) ? ' - '.$this->details->urname : '');
					?>
				</th>
				<th width="33%" class="title" align=center>
					<?php
					echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'], $this->details->transaction[0]->payment_date); //date($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'],strtotime($this->details->transaction[0]->payment_date));
					?>
				</th>
				<th width="33%" class="title" align=right><?php echo JText::_('REGPRO_TRANSACTION_PAYMENT_METHOD')." : ",$this->details->transaction[0]->payment_method;?></th>
			</tr>

			<tr>
				<td colspan="3">
					<?php
						if($this->details->transaction[0]->payment_method == 'payoffline') {
							echo '<table border="0" width="100%">';
							if($this->details->transaction[0]->payer_email) echo "<tr><td><b>".JText::_('TRANS_PAYER_EMAIL')."</b> :</td><td> <a href='mailto:".$this->details->transaction[0]->payer_email."'>".$this->details->transaction[0]->payer_email."</a></td></tr>";
							echo '<tr><td valign="top" width="30%">';
							echo "<b>".JText::_('REGPRO_OFFLINE_INSTRUCTIONS')."</b></td><td>",$this->details->transaction[0]->offline_payment_details,"</td></tr></table><br/>";
						} else {
							echo '<table border="0" width="100%" cellspacing="0" cellpadding="0" class="table table-bordered">';
							if($this->details->transaction[0]->payment_status) echo "<tr><td><b>".JText::_('TRANS_PAYMENT_STATUS')."</b> :</td><td width='80%'>",$this->details->transaction[0]->payment_status,"</td></tr>";
							if($this->details->transaction[0]->txn_id) echo "<tr><td><b>".JText::_('TRANS_TXN_ID')."</b> :</td><td width='80%'>",$this->details->transaction[0]->txn_id,"</td></tr>";
							echo "</table>";
						}
					?>
				</td>
			</tr>
			<?php
				if(!empty($this->details->products)){
			?>
			<tr>
				<td colspan="3">
					<table border="0" cellpadding="2" cellspacing="0" width="100%" class="table table-bordered">

						<tr>
							<td><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_NAME'); ?></b></td>
							<td align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_DISCOUNT'); ?></b></td>
							<td align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_PRICE'); ?></b></td>
							<td align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_TAX'); ?></b></td>
							<td align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_PRICE'); ?></b></td>
						</tr>
			<?php
					foreach($this->details->transaction as $key=>$value){

						$price_wihout_tax = $registrationproHelper->GetTicketPriceWithoutTax($this->details->transaction[$key]->price, $this->details->transaction[$key]->tax);

						echo '<tr><td>'.(($this->details->transaction[$key]->price!=0) ? $this->details->transaction[$key]->item_name:JText::_('EVENTS_REGISTRA_FREE')).'</td>';
						echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($this->details->transaction[$key]->discount_amount,2).'</td>';
						echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($this->details->transaction[$key]->price_without_tax,2).'</td>';
						echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($this->details->transaction[$key]->tax_amount,2).'</td>';
						echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($this->details->transaction[$key]->price,2).'</td></tr>';

						$subtotal += $this->details->transaction[$key]->price;
						$discount += $this->details->transaction[$key]->discount_amount;
					}

					// Additional form field fees
					if(is_array($this->additional_form_fees) && count($this->additional_form_fees) > 0) {
						foreach($this->additional_form_fees as $akey=>$avalue){
							echo '<tr><td>'.$avalue->additional_field_name.'</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency.'0.00</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($avalue->additional_field_fees,2).'</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency.'0.00</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($avalue->additional_field_fees,2).'</td></tr>';

							$subtotal += $avalue->additional_field_fees;
						}
					}

					// session fees
					if(is_array($this->session_fees) && count($this->session_fees) > 0) {
						foreach($this->session_fees as $skey=>$svalue){
							echo '<tr><td>'.$svalue->sessionname.'</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency.'0.00</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($svalue->session_fees,2).'</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency.'0.00</td>';
							echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($svalue->session_fees,2).'</td></tr>';

							$subtotal += $svalue->session_fees;
						}
					}
					if(!empty($this->details->transaction[0]->AdminDiscount)){
						$adminDiscount = $this->details->transaction[0]->AdminDiscount;
					}else{
						$adminDiscount = 0;
					}
					$total = $subtotal - $discount - $adminDiscount;

					if($total <= 0){
						$total = 0;
					}

			?>

					<tr>
						<td colspan="4" align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_SUB_TOTAL');?></b></td>
						<td align="right"><?php echo $this->details->transaction[0]->mc_currency.' '.number_format($subtotal,2); ?></td>
					 </tr>
					<tr>
						<td colspan="4" align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_DISCOUNT');?></b></td>
						<td align="right"><?php echo $this->details->transaction[0]->mc_currency.' '.number_format($discount,2); ?></td>
					</tr>
					<?php
						if($adminDiscount !=0){
					?>
					<tr>
						<td colspan="4" align="right"><b><?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_DISCOUNT_LABEL');?></b></td>
						<td align="right"><?php echo $this->details->transaction[0]->mc_currency.' '.number_format($adminDiscount,2); ?></td>
					</tr>
					<?php
						}
					?>
					<tr>
						<td colspan="4" align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_FINAL_PRICE');?></b></td>
						<td align="right"><?php echo $this->details->transaction[0]->mc_currency.' '.number_format($total,2) ?></td>
					</tr>

					</table>
				</td>
			</tr>
			<?php
				}
			?>
		</table>
		</td>
	</tr>
</table>