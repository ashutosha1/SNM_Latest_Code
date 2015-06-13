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

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div style="text-align:right">
	<a href="javascript:window.print();"><img src="<?php  echo REGPRO_ADMIN_IMG_PATH; ?>/printreport.png" border="0" title="Print Report" alt="Print Report" align="absmiddle"/></a>
</div>
<form action="index.php" method="post" name="adminForm">
<table width="96%" align="center" cellpadding="0" cellspacing="1" border="0" class="adminlist">
<tr>
	<td>
		<table width="100%" align="center" border="0" cellspacing="1" class="adminlist">
			<tr><td><b>Event Title:</b></td><td><?php echo $this->data[0]->titel; ?></td></tr>
			<tr><td><b>Where:</b></td><td><?php echo $this->data[0]->club; ?></td></tr>
			<tr><td><b>Date:</b></td><td><?php echo registrationproHelper::getFormatdate($this->regpro_config['formatdate'], $this->data[0]->dates),JText::_('EVENTS_FRONT_DATE_SEPARATOR'),registrationproHelper::getFormatdate($this->regpro_config['formatdate'], $this->data[0]->enddates); ?></td></tr>
			<tr><td><b>Time:</b></td><td><?php echo registrationproHelper::getFormatdate($this->regpro_config['formattime'], $this->data[0]->times),JText::_('EVENTS_FRONT_DATE_SEPARATOR'), registrationproHelper::getFormatdate($this->regpro_config['formattime'], $this->data[0]->endtimes); ?></td></tr>
		</table>
		<br />

	<table width="100%" border="0" cellspacing="1" class="adminlist">
		<tr style="background-color: #E0E0E0; font-weight: bold">
	  		<td colspan="7" align="left"><b><?php echo JText::_('ADMIN_USER_REGISTERS')." "; ?></b></td>
	 	</tr>

	   <tr style="text-align:left;vertical-align:middle">
	  		<th style="text-align:left;vertical-align:middle" width="70px"><b><?php echo JText::_('ADMIN_USER_REPORT_FIRST_NAME'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width="70px"><b><?php echo JText::_('ADMIN_USER_REPORT_LAST_NAME'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width="100px"><b><?php echo JText::_('ADMIN_USER_REPORT_EMAIL_ADDRESS'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width="85px"><b><?php echo JText::_('ADMIN_USER_REPORT_REG_DATE'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width="230px"><b><?php echo JText::_('ADMIN_USER_REPORT_FORM_DATA'); ?></b></th>
			<th style="text-align:center;vertical-align:middle" width="375px"><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_DATA');?></b></th>
			<th align="left" valign="middle" width="40px"><b><?php echo JText::_('ADMIN_USER_REPORT_STATUS'); ?></b></th>
		</tr>

	 <?php
	 $grand_tot = 0;
	 $tax_tot	= 0;
	 $price_tot	= 0;

	 foreach($this->data as $key=>$value)
	 { ?>
		 </tr>
			<td align="left" valign="top"><?php echo $this->data[$key]->firstname;?></td>
			<td align="left" valign="top"><?php echo $this->data[$key]->lastname;?></td>
			<td align="left" valign="top"><?php echo $this->data[$key]->email;?></td>
			<td align="left" valign="top"><?php //$regdt = strftime("%c",$this->data[$key]->uregdate + ($mosConfig_offset*60*60)); echo $regdt;
				$regdt = registrationproHelper::getFormatdate($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'], $this->data[$key]->uregdate);
				echo $regdt;
				?>
			</td>
			<td align="left" valign="top">
				<table border="0" cellpadding="2" cellspacing="0" width="100%">

				<?php
						$arrF = $this->data[$key]->params;
						$arrcount = count($arrF['firstname']);
						$arrFields = array_keys($arrF);

						foreach($arrFields as $k=>$v)
						{
							if(trim($arrF['firstname'][0][0]) == trim($this->data[$key]->firstname) && trim($arrF['lastname'][0][0]) == trim($this->data[$key]->lastname) && trim($arrF['email'][0][0]) == trim($this->data[$key]->email))
							{
								if($v != 'firstname' && $v != 'lastname' && $v != 'email'){
									$FieldTitle = str_replace("cb_","",$v);
									echo"<tr><td width='30%' style='vertical-align:top'>";
									echo ucfirst($FieldTitle),"</td><td width='5%' style='vertical-align:top'><b>:</b></td><td width='65%' style='vertical-align:top'>";

									for($i=0;$i<count($arrF[$v]);$i++)
									{
										$arrImpode = array();
										if(is_array($arrF[$v][$i])){
											$Fieldvalue = "";
											if($arrF[$v][$i][$i]){
												if($arrF[$v][$i][$i+1] == 'F'){
													$Fieldvalue = "<a href='".REGPRO_FORM_DOCUMENT_URL_PATH."/".$arrF[$v][$i][$i]."' target='_blank'>".$arrF[$v][$i][$i]."</a>";
												}else{
													$Fieldvalue = $arrF[$v][$i][$i];
												}
											}

											if($Fieldvalue){
												echo $Fieldvalue;// show values
											}else{
												echo "--NIL--";
											}
										}

										if(count($arrF[$v]) > 1){
											echo ", ";
										}
									}
									echo"</td></tr>";
								}
							}
						}
					?>
				</table>
			</td>

			<td valign="top" align="center">
				<table border="0" class="adminlist" cellspacing="1" width="100%">
					<tr>
						<td valign="top" width="20%"><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_NAME'); ?></b></td>
						<td valign="top" width="20%"><b><?php echo JText::_('ADMIN_USER_REPORT_COUPON_CODE'); ?></b></td>
						<td valign="top" align="right" width="20%"><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_PRICE');?></b></td>
						<td valign="top" align="right" width="20%"><b><?php echo JText::_('ADMIN_USER_REPORT_TAX');?></b></td>
						<td valign="top" align="right" width="20%"><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_TOTAL_PRICE');?></b></td>
					</tr>

					<?php
					if(count($this->productdata) > 0)
					{
						$grosstot = 0;
						$discount = 0;
						foreach($this->productdata as $k=>$v)
						{
							if($this->productdata[$k]->reg_id == $this->data[$key]->rid){
					?>
					<tr>
						<td valign="top">
							<?php
								if($this->productdata[$k]->item_name){
									echo $this->productdata[$k]->item_name;
								}else{
									echo "Free";
								}

								// added on 31-march-08 (get the ticket actual price by calculation with tax and gorss price)
								if(empty($this->productdata[$k]->price_without_tax) || $this->productdata[$k]->price_without_tax == 0.00){
									// calculating the acutal amount with help of gorss amount and tax percentage
									if(!empty($this->productdata[$k]->price)){
										$productprice = (100 * $this->productdata[$k]->price) / (100 + $this->productdata[$k]->tax);
										$this->productdata[$k]->price_without_tax = $productprice;
									}
								}
								//end
							?>
						</td>
						<td valign="top" align="left"><?php echo $this->productdata[$k]->coupon_code;?></td>
						<td valign="top" align="right"><?php echo $this->productdata[$k]->mc_currency." ".number_format($this->productdata[$k]->price_without_tax,2);?></td>
						<td valign="top" align="right">
							<?php
								if($this->productdata[$k]->tax_amount > 0.00){
									echo $this->productdata[$k]->mc_currency." ".number_format($this->productdata[$k]->tax_amount,2);
								}else{
									echo number_format($this->productdata[$k]->tax,2)."%";
								}
							?>
						</td>
						<td valign="top" align="right"><?php echo $this->productdata[$k]->mc_currency." ".number_format($this->productdata[$k]->price,2);?></td>

					</tr>

					<?php
								// added on 31-march-08
								$temp_tax 	= 0;
								$grosstot 	= $grosstot + $this->productdata[$k]->price;
								$price_tot 	=  $price_tot + $this->productdata[$k]->price_without_tax;
								if(!empty($this->productdata[$k]->tax))
									if($this->productdata[$k]->tax_amount > 0.00){
										$temp_tax 	= $this->productdata[$k]->tax_amount;
									}else{
										$temp_tax 	= ($this->productdata[$k]->price_without_tax * $this->productdata[$k]->tax)/100;
										//$tax_tot	= $tax_tot + $this->productdata[$k]->tax;
									}
									$tax_tot 	= $temp_tax 	+ $tax_tot;

								if($this->productdata[$k]->event_discount_type == "P"){
									$discount = $discount + $this->productdata[$k]->discount_amount;
								}else{
									$discount = $discount + $this->productdata[$k]->discount_amount + $this->productdata[$k]->event_discount_amount;
								}
							}
							// end
						}
						$grand_tot 		= $grand_tot + $grosstot;
						$discount_tot 	= $discount_tot + $discount;
						$final_total 	= $grosstot - $discount;
						if($final_total <= 0){
							$final_total = 0;
						}
					?>
					<tr>
						<td colspan="4" align="right"><b>Sub Total:</b></td>
						<td align="right"><b><?php echo $this->productdata[$k]->mc_currency." ".number_format($grosstot,2);?></b></td>
					</tr>
					<tr>
						<td colspan="4" align="right"><b>Total Discount:</b></td>
						<td  align="right"><b><?php echo $this->productdata[$k]->mc_currency." ".number_format($discount,2);?></b></td>
					</tr>
					<tr>
						<td colspan="4" align="right"><b>Final Total:</b></td>
						<td  align="right"><b><?php echo $this->productdata[$k]->mc_currency." ".number_format($final_total,2);?></b></td>
					</tr>
				<?php
					}else{
				?>
						<tr>
							<td valign="top" width="40px">Free</td>
							<td valign="top" align="right" width="20px"><?php $this->productdata[$k]->mc_currency." "; ?>0.00</td>
							<td valign="top" align="right" width="20px"><?php $this->productdata[$k]->mc_currency." "; ?>0.00</td>
							<td valign="top" align="right" width="40px"><?php $this->productdata[$k]->mc_currency." "; ?>0.00</td>
						</tr>
				<?php
					}
				?>
				</table>
			</td>

			<td align="left" valign="top">
				<?php
						switch($this->data[$key]->status)
						{
							case "0":
							echo "Pending";
							break;

							case "1":
							echo "Accepted";
							break;

							case "2":
							echo "Waiting";
							break;
						}
				?>
			</td>
		</tr>

	<?php
	}
		$grand_total_without_tax = 0;
		$grand_total_without_tax = $price_tot - $discount_tot;
		if($grand_total_without_tax <= 0){
			$grand_total_without_tax = 0;
		}

		$grand_total_with_tax = 0;
		$grand_total_with_tax = $grand_tot - $discount_tot;
		if($grand_total_with_tax <= 0){
			$grand_total_with_tax = 0;
		}

	?>
		<tr><td colspan="7" height="20">&nbsp;</td></tr>

		<tr>
			<td colspan="5">&nbsp;</td>
			<td>
				<table border="0" cellspacing="1" width="100%" class="adminlist">
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_EXCLU_TAX');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($price_tot,2); ?></b></td>
					</tr>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_DISCOUNT');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($discount_tot,2); ?></b></td>
					</tr>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_FINAL_TOTAL');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($grand_total_without_tax,2); ?></b></td>
					</tr>
					<tr><td colspan="2" style="text-align:right;vertical-align:top">&nbsp;</td></tr>

					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_INCL_TAX');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($grand_tot,2); ?></b></td>
					</tr>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_DISCOUNT');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($discount_tot,2); ?></b></td>
					</tr>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_FINAL_TOTAL');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($grand_total_with_tax,2); ?></b></td>
					</tr>
					<tr><td colspan="2" style="text-align:right;vertical-align:top">&nbsp;</td></tr>

					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_TAX');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($tax_tot,2); ?></b></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>

</td></tr>
</table>