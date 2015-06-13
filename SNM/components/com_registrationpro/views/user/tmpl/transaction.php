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

//echo "<pre>"; print_r($this->regpro_config); exit;

?>

<table cellpadding="0" cellspacing="0" border="2" width="100%" align="left">
	<tr>
		<td style="text-align:left">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">

			<tr>
				<th width="30%" class="title"><?php echo (($this->details->transaction[0]->first_name||$this->details->transaction[0]->last_name) ? ucfirst($this->details->transaction[0]->first_name).' '.ucfirst($this->details->transaction[0]->last_name) : ''); echo (($this->details->urname) ? ' - '.$this->details->urname : '');?></th>				
				<th width="35%" class="title" align="right">
					<?php 
						//echo $this->details->transaction[0]->payment_date;
					echo registrationproHelper::getFormatdate($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'], $this->details->transaction[0]->payment_date); //date($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'],strtotime($this->details->transaction[0]->payment_date));
					?>
				</th>
				<th width="35%" class="title" align="right"><?php echo JText::_('REGPRO_TRANSACTION_PAYMENT_METHOD')." : ",$this->details->transaction[0]->payment_method;?></th>
			</tr>

			<tr>
				<td colspan="3">
					<?php											
						if($this->details->transaction[0]->payment_method == 'payoffline')
						{
							echo '<table border="0" width="100%">';
							
							if($this->details->transaction[0]->payer_email)
								echo "<tr><td><b>".JText::_('TRANS_PAYER_EMAIL')."</b> :</td><td> <a href='mailto:".$this->details->transaction[0]->payer_email."'>".$this->details->transaction[0]->payer_email."</a></td></tr>";						
								
							echo '<tr><td valign="top" width="30%">';
							echo "<b>".JText::_('REGPRO_OFFLINE_INSTRUCTIONS')." </b></td><td>",$this->details->transaction[0]->offline_payment_details,"</td></tr>
							</table><br/>";
							
						}else{
							echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
							
							if($this->details->transaction[0]->payment_status)
								echo "<tr><td width='20%'><b>".JText::_('TRANS_PAYMENT_STATUS')."</b> :</td><td>",$this->details->transaction[0]->payment_status,"</td></tr>";
						
						/*	if($this->details->transaction[0]->payer_id)
								echo "<tr><td><b>".JText::_('TRANS_PAYER_ID')."</b> :</td><td>",$this->details->transaction[0]->payer_id,"</td></tr>";
							
							if($this->details->transaction[0]->payer_status)
								echo "<tr><td><b>".JText::_('TRANS_PAYER_STATUS')."</b> :</td><td>". $this->details->transaction[0]->payer_status,"</td></tr>";								
							
							if($this->details->transaction[0]->address_street)
								echo "<tr><td><b>".JText::_('TRANS_ADDRESS')."</b> :</td><td>",$this->details->transaction[0]->address_name."<br/>".$this->details->transaction[0]->address_street,"</td></tr>";
								
							if($this->details->transaction[0]->address_city)
								echo "<tr><td><b>".JText::_('TRANS_ADDRESS_CITY')."</b> :</td><td>",$this->details->transaction[0]->address_city,"</td></tr>";
								
							if($this->details->transaction[0]->address_zip)
								echo "<tr><td><b>".JText::_('TRANS_ADDRESS_ZIP')."</b> :</td><td>",$this->details->transaction[0]->address_zip,"</td></tr>";
							if($this->details->transaction[0]->address_state)
								echo "<tr><td><b>".JText::_('TRANS_ADDRESS_STATE')."</b> :</td><td>",$this->details->transaction[0]->address_state,"</td></tr>";
								
							if($this->details->transaction[0]->address_country)
								echo "<tr><td><b>".JText::_('TRANS_ADDRESS_COUNTRY')."</b> :</td><td>",$this->details->transaction[0]->address_country,"</td></tr>";
								
							if($this->details->transaction[0]->payer_email)
								echo "<tr><td><b>".JText::_('TRANS_PAYER_EMAIL')."</b> :</td><td> <a href='mailto:".$this->details->transaction[0]->payer_email."'>".$this->details->transaction[0]->payer_email."</a></td></tr>";*/
								
							if($this->details->transaction[0]->txn_id)
								echo "<tr><td><b>".JText::_('TRANS_TXN_ID')."</b> :</td><td>",$this->details->transaction[0]->txn_id,"</td></tr>";

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
					<table border="1" cellpadding="2" cellspacing="0" width="100%" class="adminlist" style="border:1px solid #CCCCCC">

						<tr>
							<td><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_NAME'); ?></b></td>
							<td align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_DISCOUNT'); ?></b></td>
							<td align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_PRICE'); ?></b></td>
						</tr>
			<?php
					foreach($this->details->transaction as $key=>$value){									
						echo '<tr><td>'.(($this->details->transaction[$key]->price!=0) ? $this->details->transaction[$key]->item_name:JText::_('EVENTS_REGISTRA_FREE')).'</td>';
						echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($this->details->transaction[$key]->discount_amount,2).'</td>';
						echo '<td align="right">'.$this->details->transaction[0]->mc_currency." ".number_format($this->details->transaction[$key]->price,2).'</td></tr>';

						$subtotal += $this->details->transaction[$key]->price;
						$discount += $this->details->transaction[$key]->discount_amount;	
						
						// apply event discount
						//$discount += $this->details->transaction[$key]->event_discount_amount;					
					}
					
					$total = $subtotal - $discount;
					
					if($total <= 0){
						$total = 0;
					}
					
			?>
					
					<tr>
						<td colspan="2" align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_SUB_TOTAL');?></b></td>
						<td align="right"><?php echo $this->details->transaction[0]->mc_currency.' '.number_format($subtotal,2); ?></td>
					 </tr>
					<tr>
						<td colspan="2" align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_TOTAL_DISCOUNT');?></b></td>
						<td align="right"><?php echo $this->details->transaction[0]->mc_currency.' '.number_format($discount,2); ?></td>
					</tr>					
					<tr>
						<td colspan="2" align="right"><b><?php echo JText::_('REGPRO_TRANSACTION_PRODUCT_FINAL_PRICE');?></b></td>
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