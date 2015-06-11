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
$registrationproHelper = new registrationproHelper;
$db	= JFactory::getDBO();

$get = JRequest::get();
//echo "<pre>"; print_r($this->productdata); echo "</pre>";

$show_total_income = JRequest::getVar('show_total_income', 'on');
$show_published = JRequest::getVar('show_published', 'on');
$show_unpublished = JRequest::getVar('show_unpublished', 'on');
$show_archived = JRequest::getVar('show_archived', 'on');
$event_id = JRequest::getVar('event_id', 0);
$month = JRequest::getVar('month', date('n'));
$year = JRequest::getVar('year', date('Y'));
$cat = JRequest::getVar('cat', 0);
$pay = JRequest::getVar('payment_status', 0);
$chart_type = JRequest::getVar('chart_type', 0);
$cht = 'LineChart';
$dates = JRequest::getVar('dates', date('Y-m-').'01');
$datef = JRequest::getVar('datef', date('Y-m-d'));
$tsk = JRequest::getVar('tsk', '');

?>

<form action="index.php" method="post" name="adminForm">
<table width="98%" align="center" border="0" cellpadding="20">
<tr>
	<td>
	<table width="100%" border="0" cellspacing="1" class="adminlist table table-bordered" cellpadding="5">
		<tr style="background-color:#ddeeff;">
	  		<td colspan=6 align=left style="padding:5px;padding-left:15px;"><h4>Report on Event's Registrants. Period: <?php echo $get['dates']?>/<?php echo $get['datef']?></h4></td>
			<td style="padding:5px;padding-right:15px;text-align:center;background-color:#d0e0f4;"><a href="javascript:window.print();"><img src="<?php  echo REGPRO_ADMIN_IMG_PATH; ?>/printreport.png" border=0 title="Print Report" alt="Print Report" /></a></td>
	 	</tr>

	   <tr style="text-align:left; vertical-align:middle">
	  		<th style="text-align:left;vertical-align:middle" width=70><b><?php echo JText::_('ADMIN_USER_REPORT_FIRST_NAME'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width=70><b><?php echo JText::_('ADMIN_USER_REPORT_LAST_NAME'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width=100><b><?php echo JText::_('ADMIN_USER_REPORT_EMAIL_ADDRESS'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width=85><b><?php echo JText::_('ADMIN_USER_REPORT_REG_DATE'); ?></b></th>
			<th style="text-align:left;vertical-align:middle" width=230><b><?php echo JText::_('ADMIN_USER_REPORT_FORM_DATA'); ?></b></th>
			<th style="text-align:center;vertical-align:middle" width=375><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_DATA');?></b></th>
			<th style="text-align:center;vertical-align:middle" width=40><b><?php echo JText::_('ADMIN_USER_REPORT_STATUS'); ?></b></th>
		</tr>

	 <?php
	 $grand_tot = 0;
	 $tax_tot	= 0;
	 $price_tot	= 0;

	 foreach($this->data as $key=>$value) {
		$regdt = $registrationproHelper->getFormatdate($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'], $value->uregdate + ($this->regpro_config['timezone_offset']*60*60));
	 ?>
		 </tr>
			<td align="left" valign=top><?php echo $value->firstname;?></td>
			<td align="left" valign=top><?php echo $value->lastname;?></td>
			<td align="left" valign=top><?php echo $value->email;?></td>
			<td align="left" valign=top><?php echo $regdt;?></td>
			<td align="left" valign=top>
				<table border="0" cellpadding="2" cellspacing="0" width="100%" class="">
				<?php
						$arrF = $value->params;
						$arrcount = count($arrF['firstname']);
						$arrFields = array_keys($arrF);

						foreach($arrFields as $k=>$v) {
							if(trim($arrF['firstname'][0][0]) == trim($value->firstname) && trim($arrF['lastname'][0][0]) == trim($value->lastname) && trim($arrF['email'][0][0]) == trim($value->email)) {
								if($v != 'firstname' && $v != 'lastname' && $v != 'email'){
									$FieldTitle = str_replace("cb_","",$v);
									echo"<tr><td width='30%' style='vertical-align:top'>";
									echo ucfirst($FieldTitle),"</td><td width='5%' style='vertical-align:top'><b>:</b></td><td width='65%' style='vertical-align:top'>";
									for($i=0;$i<count($arrF[$v]);$i++) {
										$arrImpode = array();
										if(is_array($arrF[$v][$i])){
											$Fieldvalue = "";
											if($arrF[$v][$i][$i]){
												if($arrF[$v][$i][$i+1] == 'F'){
													$Fieldvalue = "<a href='".REGPRO_FORM_DOCUMENT_URL_PATH."/".$arrF[$v][$i][$i]."' target='_blank'>".$arrF[$v][$i][$i]."</a>";
												} else $Fieldvalue = $arrF[$v][$i][$i];
											}
											if ($Fieldvalue){ echo $Fieldvalue; }
											else echo "--NIL--";
										}
										if(count($arrF[$v]) > 1) echo ", ";
									}
									echo"</td></tr>";
								}
							}
						}
					?>
				</table>
			</td>

			<td style="padding:0px;">
				<table border=0 width="100%" style="margin:0px;padding:0px;">
					<tr>
						<td style="padding:2px;text-align:center;border-left:none;" valign=top width=20%><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_NAME'); ?></b></td>
						<td style="padding:2px;text-align:center;" valign=top width=20%><b><?php echo JText::_('ADMIN_USER_REPORT_COUPON_CODE'); ?></b></td>
						<td style="padding:2px;text-align:center;" valign=top width=20%><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_PRICE');?></b></td>
						<td style="padding:2px;text-align:center;" valign=top width=15%><b><?php echo JText::_('ADMIN_USER_REPORT_TAX');?></b></td>
						<td style="padding:2px;text-align:center;" valign=top width=25%><b><?php echo JText::_('ADMIN_USER_REPORT_PRODUCT_TOTAL_PRICE');?></b></td>
					</tr>

					<?php
					if(count($this->productdata) > 0) {
						$grosstot = 0;
						$discount = 0;
						foreach($this->productdata as $k=>$v) {
							if($v->reg_id == $value->rid){
					?>
					<tr>
						<td style="padding:2px;text-align:center;" valign=top>
							<?php
								if($v->item_name){ echo $v->item_name; } else echo "Free";
								if(empty($v->price_without_tax) || ((($v->price_without_tax)*1) == 0)){
									if(!empty($v->price)){
										$productprice = (100 * $v->price) / (100 + $v->tax);
										$v->price_without_tax = $productprice;
									}
								}
							?>
						</td>
						<td style="padding:2px;text-align:center;" valign=top><?php echo $v->coupon_code;?></td>
						<td style="padding:2px;text-align:center;" valign=top><?php echo $v->mc_currency." ".number_format($v->price_without_tax, 2);?></td>
						<td style="padding:2px;text-align:center;" valign=top>
							<?php
								if($v->tax_amount > 0.00){
									echo $v->mc_currency." ".number_format($v->tax_amount,2);
								}else echo number_format($v->tax,2)."%";
							?>
						</td>
						<td style="padding:2px;text-align:center;" valign=top"><?php echo $v->mc_currency." ".number_format($v->price,2);?></td>

					</tr>

					<?php
						$additional_field_fees_total = 0.00;
						if(is_array($v->additional_field_fees) && (count($v->additional_field_fees) > 0)) {
							foreach($v->additional_field_fees as $affkey => $affvalue) {
					?>
					<tr>
						<td style="padding:2px;" valign=top><?php echo $affvalue->additional_field_name; ?></td>
						<td style="padding:2px;" valign=top align="left">&nbsp;</td>
						<td style="padding:2px;" valign=top align="right"><?php echo $v->mc_currency." ".number_format($affvalue->additional_field_fees,2);?></td>
						<td style="padding:2px;" valign=top align="right">0.00%</td>
						<td style="padding:2px;" valign=top align="right"><?php echo $v->mc_currency." ".number_format($affvalue->additional_field_fees,2);?></td>
					</tr>
					<?php
								$additional_field_fees_total += $affvalue->additional_field_fees;
							}
						}
					?>

					<?php
						$session_fees_total = 0.00;
						if(is_array($v->session_fees) && (count($v->session_fees) > 0)) {
							foreach($v->session_fees as $skey => $svalue)
							{
					?>
					<tr>
						<td style="padding:2px;" valign=top><?php echo $svalue->sessionname; ?></td>
						<td style="padding:2px;" valign=top align="left">&nbsp;</td>
						<td style="padding:2px;" valign=top align="right"><?php echo $v->mc_currency." ".number_format($svalue->session_fees,2);?></td>
						<td style="padding:2px;" valign=top align="right">0.00%</td>
						<td style="padding:2px;" valign=top align="right"><?php echo $v->mc_currency." ".number_format($svalue->session_fees,2);?></td>
					</tr>
					<?php
								$session_fees_total += $svalue->session_fees;
							}
						}
					?>

					<?php
								// added on 31-march-08
								$temp_tax 	= 0;
								$grosstot 	= $grosstot + $v->price + $additional_field_fees_total + $session_fees_total;
								$price_tot 	=  $price_tot + $v->price_without_tax + $additional_field_fees_total + $session_fees_total;
								if(!empty($v->tax))
									if(($v->tax_amount * 1) > 0){
										$temp_tax = $v->tax_amount;
									}else $temp_tax = ($v->price_without_tax * $v->tax)/100;
									$tax_tot = $temp_tax + $tax_tot;

								if($v->event_discount_type == "P"){ $discount = $discount + $v->discount_amount; }
								else $discount = $discount + $v->discount_amount + $v->event_discount_amount;
							}
						}
						if(!empty($this->productdata[$key]->AdminDiscount))
						{
							$adminDiscount = $this->productdata[$key]->AdminDiscount;
							$totalAdminDiscount = $totalAdminDiscount+$adminDiscount;
						}else{
							$adminDiscount = 0;
						}
						$grand_tot = $grand_tot + $grosstot;
						$discount_tot = $discount_tot + $discount;
						$final_total 	= $grosstot - $discount - $adminDiscount;
						if($final_total <= 0) $final_total = 0;
					?>
					<tr>
						<td colspan="4" style="text-align:right;padding:2px;"><b>Sub Total:</b></td>
						<td style="text-align:right;padding:2px;"><b><?php echo $v->mc_currency." ".number_format($grosstot,2);?></b></td>
					</tr>
					<tr>
						<td colspan="4"style="text-align:right;padding:2px;"><b>Total Discount:</b></td>
						<td style="text-align:right;padding:2px;"><b><?php echo $v->mc_currency." ".number_format($discount,2);?></b></td>
					</tr>
					<?php
						if($adminDiscount > 0)
						{
					?>	
					<tr>
						<td colspan="4"style="text-align:right;"><b><?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_DISCOUNT_LABEL');?></b></td>
						<td style="text-align:right;">
							<b><?php echo $this->productdata[$k]->mc_currency." ".number_format($adminDiscount,2);?></b>
						</td>
					</tr>	
					<?php
						}
					?>
					<tr>
						<td colspan="4"style="text-align:right;padding:2px;"><b>Final Total:</b></td>
						<td style="text-align:right;padding:2px;"><b><?php echo $v->mc_currency." ".number_format($final_total,2);?></b></td>
					</tr>
				<?php }else{ ?>
						<tr>
							<td style="padding:2px;" valign=top width="40px">Free</td>
							<td style="padding:2px;" valign=top align="right" width="20px"><?php $v->mc_currency." "; ?>0.00</td>
							<td style="padding:2px;" valign=top align="right" width="20px"><?php $v->mc_currency." "; ?>0.00</td>
							<td style="padding:2px;" valign=top align="right" width="40px"><?php $v->mc_currency." "; ?>0.00</td>
						</tr>
				<?php } ?>
				</table>
			</td>

			<td style="text-align:center;vertical-align:middle">
				<?php
						switch($value->status) {
							case "0": echo "<font color=#662222>Pending</font>";  break;
							case "1": echo "<font color=#226622>Accepted</font>"; break;
							case "2": echo "<font color=#222266>Waiting</font>";  break;
						}
				?>
			</td>
		</tr>
		<tr><td colspan=20 bgcolor=#e8f0ff style="padding:0px;margin:0px;height:6px;border-top:none;"></td></tr>

	<?php
	}
		$grand_total_without_tax = 0;
		$grand_total_without_tax = $price_tot - $discount_tot - $totalAdminDiscount;
		if($grand_total_without_tax <= 0) $grand_total_without_tax = 0;

		$grand_total_with_tax = 0;
		$grand_total_with_tax = $grand_tot - $discount_tot - $totalAdminDiscount;
		if($grand_total_with_tax <= 0) $grand_total_with_tax = 0;

	?>
		<tr><td colspan="7" height="20">&nbsp;</td></tr>

		<tr>
			<td colspan="5">&nbsp;</td>
			<td>
				<table border="0" width="100%" class="table table-bordered">
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_EXCLU_TAX');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($price_tot,2); ?></b></td>
					</tr>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_DISCOUNT');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($discount_tot,2); ?></b></td>
					</tr>
					<?php
						if($totalAdminDiscount > 0)
						{
					?>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('COM_REGISTRATIONPRO_TOTAL_ADMIN_DISCOUNT_LABEL');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($totalAdminDiscount,2); ?></b></td>
					</tr>	
					<?php
						}
					?>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_FINAL_TOTAL');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($grand_total_without_tax,2); ?></b></td>
					</tr>
					<tr><td colspan="2" style="text-align:right; vertical-align:top">&nbsp;</td></tr>

					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_INCL_TAX');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($grand_tot,2); ?></b></td>
					</tr>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_TOTAL_DISCOUNT');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($discount_tot,2); ?></b></td>
					</tr>
					<?php
						if($totalAdminDiscount > 0)
						{
					?>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('COM_REGISTRATIONPRO_TOTAL_ADMIN_DISCOUNT_LABEL');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($totalAdminDiscount,2); ?></b></td>
					</tr>	
					<?php
						}
					?>
					<tr align="right">
						<td style="text-align:right"><b><?php echo JText::_('ADMIN_USER_REPORT_GRAND_FINAL_TOTAL');?></b></td>
						<td style="text-align:right"><b><?php echo $this->regpro_config['currency_sign']." ".number_format($grand_total_with_tax,2); ?></b></td>
					</tr>
					<tr><td colspan="2" style="text-align:right; vertical-align:top">&nbsp;</td></tr>

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
</table>>