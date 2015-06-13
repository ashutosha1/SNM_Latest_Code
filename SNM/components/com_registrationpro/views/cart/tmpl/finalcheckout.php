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

// no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal' );
JHTML::_('behavior.calendar');

//add css and js to document
$registrationproHelper = new registrationproHelper;
$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
?>

<script language="JavaScript">
	function check(checkbox, submit) {
		if(document.finalcheckout.agrement.checked == true){
			submit.disabled = false;
		} else {
			submit.disabled = true;
			return false;
		}
	}

	 function onformsubmit()
	 {
	 	if(document.finalcheckout.selPaymentOption.value == ""){
			alert("<?php echo Jtext::_("EVENTS_IS_PAID_SELECT_PAYMENT_METHOD"); ?>");
			document.finalcheckout.selPaymentOption.focus();
			return false;
		}
	 }

</script>
</script>
<!--  CODE TO DISPLAY PAGE HEADING -- -->
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php if ($this->escape($this->params->get('page_heading'))) : ?>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php else : ?>
		<?php echo $this->escape($this->params->get('page_title')); ?>
	<?php endif; ?>
</h1>
<?php endif; ?>
<!-- ********************************************************************** -->
<div id="regpro">
<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regproConfig);

if($this->cart) {
?>
	<div id="listorder">
		<div id="order_page_title"> <?php echo JText::_('FINAL_CHECKOUT_TITLE'); ?> </div>
	<?php

		if(!empty($this->cart['error_message']))
			echo "<div class='alert alert'>",$this->cart['error_message'],"</div>";

		if(!empty($this->cart['success_message']))
			echo "<div class='alert alert-success'>",$this->cart['success_message'],"</div>";

		if($this->row->message != ""){
			echo '<div class="regpro_error">'.$this->row->message.'</div>'; // error message
		}

		// display event discount message if applied
		if(is_array($this->cart['event_discounts']) && count($this->cart['event_discounts'] > 0)){
			discount_message($this->cart, $this->regproConfig);
		}

		// display cart
		tickets_cart($this->cart, $this->row, $this->regproConfig, $this->cart_form_action);

		// display forms
		manage_registration_form($this->cart, $this->row, $this->regproConfig, $this->action);
	?>
	</div>
<?php
}else{
?>
	<div style="width:100%; text-align:center"> <?php echo JText::_('FINAL_CHECKOUT_CART_EMPTY'); ?> </div>
<?php
}
?>

<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>

<?php
	$regpro_header_footer->regpro_footer($this->regproConfig);
?>
</div>

<?php
// Show event name
function fn_EventDetailsName($row, $regproConfig){$registrationproHelper = new registrationproHelper;
	if($regproConfig['showtitle'] == 1) {
	?>
		<tr> <td colspan="3"><?php echo JText::_('EVENTS_NAME');?>&nbsp; <b><?php echo $row->titel; ?></b> </td> </tr>
	<?php
	}
}

function tickets_cart($cart, $row, $regproConfig, $from_action)
{	$registrationproHelper = new registrationproHelper;
	global $Itemid;
	$arr_qty = range(1, $regproConfig['quantitylimit']);
	$tkt_qty = 0;

?>
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<td>
			<div class="regpro_outline" id="regpro_outline">
			<table width="100%" class="regprocart">
				<thead>
				<tr>
					<th><?php echo JText::_('FINAL_CHECKOUT_TICKET_NAME'); ?></th>
					<th style="text-align:center"><?php echo JText::_('FINAL_CHECKOUT_TICKET_QTY'); ?></th>
					<th style="text-align:right"><?php echo JText::_('FINAL_CHECKOUT_TICKET_PRICE'); ?></th>
					<th style="text-align:right"><?php echo JText::_('FINAL_CHECKOUT_TICKET_TAX'); ?></th>
					<th style="text-align:right"><?php echo JText::_('FINAL_CHECKOUT_TICKET_TOTAL'); ?></th>
				</tr>
				</thead>
				<tbody>
			<?php
			// loop to arrange all the tickets event wise
			foreach($cart['eventids'] as $ekey => $evalue){
				echo "<tr><td colspan='5' class='regpro_eventoncart'><div id='regpro_eventoncart'>";
				echo ucfirst($registrationproHelper->getEventName($evalue));
				echo "</div></td></tr>";

				// event tickets listing
				$session_flag = 0;
				foreach($cart['ticktes'] as $tkey=>$tvalue)
				{
					if($evalue == $tvalue->regpro_dates_id) {

						// check if session exists with current event or not
						if(is_array($tvalue->sessions) && count($tvalue->sessions) >0 ) {
							$session_flag = 1;
						}

						if ( $tvalue->qty > 1 && $showbox == 1) $showbox = 1;else $showbox = 0;

						if($tvalue->product_quantity > 0){
							// check if ticket quantity is avaliable or not
							$ticket_avaliable_qty = $tvalue->product_quantity - $tvalue->product_quantity_sold;

							$tkt_qty = range(1, $ticket_avaliable_qty);
						}else{
							$tkt_qty = $arr_qty;
						}
			?>

				<tr>
					<td class="regpro_vmiddle_aleft"><?php echo $cart['ticktes'][$tkey]->product_name; ?></td>
					<td class="regpro_vmiddle_acenter"> <?php echo $cart['ticktes'][$tkey]->qty; ?> </td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['ticktes'][$tkey]->product_price,2); ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['ticktes'][$tkey]->tax_price,2); ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['ticktes'][$tkey]->total_amount,2); ?></td>
				</tr>

			<?php
					// Add Session records
						if(is_array($tvalue->sessions) && count($tvalue->sessions) > 0 && $session_flag == 1 ) {
							foreach($tvalue->sessions as $skey => $svalue)
							{
								$session_fees = $svalue->fee * $cart['ticktes'][$tkey]->qty;
			?>
				<tr>
					<td class="regpro_vmiddle_aleft"><?php echo $svalue->title; ?></td>
					<td class="regpro_vmiddle_acenter"><?php echo $cart['ticktes'][$tkey]->qty; ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($svalue->fee,2); ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign']; ?>0.00</td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($session_fees,2); ?></td>
				</tr>
			<?php
							}
						}
					}
				}
			?>

			<?php
				// check additional form fields exists
				if(count($cart['additional_formfield_fees']) > 0 && is_array($cart['additional_formfield_fees'])) {
			?>
				<tr>
					<td colspan="5"><div id='regpro_eventoncart'> <?php echo JText::_('FINAL_CHECKOUT_ADDITIONAL_FEES_FIELDS'); ?> </div></td>
				</tr>
			<?php

					// form fields fees listing
					foreach($cart['additional_formfield_fees'] as $affkey => $affvalue)
					{

						if($evalue == $affvalue['event_id']) {
							$total_form_fees = $cart['additional_formfield_fees'][$affkey]['amount'] * $total_form_fees = $cart['additional_formfield_fees'][$affkey]['qty'];
			?>
				<tr>
					<td class="regpro_vmiddle_aleft"><?php echo $cart['additional_formfield_fees'][$affkey]['field_name']; ?></td>
					<td class="regpro_vmiddle_acenter"> <?php echo $cart['additional_formfield_fees'][$affkey]['qty']; ?> </td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['additional_formfield_fees'][$affkey]['amount'],2); ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign']."0.00"; ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($total_form_fees,2); ?></td>
				</tr>
				</tbody>
			<?php
						}
					}
				}
			}
			?>
				<tr>
					<td colspan="3" class="regpro_vtop_aleft"> 	<img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" height="2px;"/> </td>

					<td class="regpro_vtop_aright">
						<?php
							if($cart['group_discount'] > 0 || $cart['early_discount'] > 0 || $cart['discount'] > 0){
						?>
							<div class="regpro_vtop_aright"><?php echo JText::_('FINAL_CHECKOUT_SUBTOTAL'); ?> :</b></div>
							<div class="regpro_vtop_aright"><?php echo JText::_('FINAL_CHECKOUT_DISCOUNTS'); ?> :</b></div>
						<?php
							}
						?>
							<div class="regpro_vtop_aright"><?php echo JText::_('FINAL_CHECKOUT_TOTAL'); ?> :</b></div>

					</td>
					<td class="regpro_vtop_aright">
							<?php
								// add additional form fields fees
								$sub_total = str_replace(",","",$cart['sub_total']) + str_replace(",","",$cart['additional_formfield_fees_total']);

								if($cart['discount'] > 0){
							?>
									<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(",","",$sub_total),2); ?></div>
									<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(",","",$cart['discount']),2); ?></div>
							<?php
								}else{
							?>
                            <?php
									if($cart['group_discount'] > 0 || $cart['early_discount'] > 0){
							?>
										<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(",","",$sub_total),2); ?></div>
										<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(",","",$cart['both_discounts']),2); ?></div>
							<?php
									}
								}
							?>

							<?php
								// add additional form fields fees
								$grand_total = $cart['grand_total'] + $cart['additional_formfield_fees_total'];
							?>

							<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format($grand_total,2); ?></div>
					</td>
				</tr>
			</table>
			</div>
			</div>

		</td>
	</tr>
	</table>
<?php
}

// Manage registration form
function manage_registration_form($cart, $row, $regproConfig, $form_action)
{
	global $Itemid;
	$registrationproHelper = new registrationproHelper;
	$database	= JFactory::getDBO();
	$my	= JFactory::getUser();
?>
		<!--<form name="finalcheckout" id="finalcheckout" class="fValidator-form" action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data">-->

		<form name="finalcheckout" id="finalcheckout" action="<?php echo $form_action; ?>" method="post">

		<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="regpro_form">
		<?php

		if($cart) {
			//display form data here
				//loop ticket first display batch fields
				$total_qty = 0;

				//there is check for ticket quantities
				$total_qty = $cart['total_tqty'];

				if(!empty($total_qty)){
					echo '<tr><td colspan="3" class="regpro_outline" id="regpro_outline">';
					echo '<table border="0" width="100%" cellspacing="0" cellpadding="0" class="regproform">';
					echo '<tr><th colspan="3" class="regpro_sectiontableheader" style="text-align:center">'.JText::_('EVENT_CART_FORM_HEADING').'</th></tr>';
					//echo '<tr><th colspan="3"><div id="errors"></div></th></tr>';
				}

				######### Start Registration forms #################

				if(is_array($cart['form_data']['finalcheckout_form']) && count($cart['form_data']['finalcheckout_form']) > 0 && is_array($cart['form_data']['users_tickets']['ticket_ids']) && count($cart['form_data']['users_tickets']['ticket_ids']) > 0) {

					$session = JFactory::getSession();
					$form_title = $session->get('titles');

					// loop to arrange all the tickets event wise
					foreach($cart['eventids'] as $ekey => $evalue){
						$k = 0;
						$counter = 0;
						foreach($cart['ticktes'] as $tkey=>$tvalue){
							if($evalue == $tvalue->regpro_dates_id && $tvalue->type == 'E') {
								// get event date
								$event_date = $registrationproHelper->getEventInfo($evalue);
								$event_date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $event_date->dates);
								echo '<tr><td colspan="2" style="border:none;"><strong>'.ucwords($tvalue->product_name).'&nbsp;'.JText::_('EVENTS_REGISTRA_COMMON_FIELDS').'<strong></td>';
								echo "<td style='text-align:right;'>".JText::_('EVENT_CART_FORM_EVENT_DATE')."&nbsp;".$event_date."</td></tr>";
								echo "<tr><td colspan='3' align='center'>";
								//echo "<table border='0' width='100%' cellspacing='0' cellpadding='2'>";

								$alterclass=1;	// color code counter

								foreach($cart['form_data']['finalcheckout_form']['firstname'] as $ffkey => $ffvalue)
								{
									// code to differentiate forms by bg colors
									if($alterclass%2==0){
										$style="style='background-color:#EEEEEE;border:none;'";
									}else{
										$style="style='background-color:#F9F9F9;border:none;'";
									}
									$j = 0;

									echo "<table width='100%' ".$style." >";
									foreach($cart['form_data']['finalcheckout_form'] as $fkey => $fvalue)
									{
										if($cart['form_data']['finalcheckout_form']['regpro_event_id'][$ffkey][$tvalue->id] == $evalue)	 {
											if($fkey != "regpro_event_id") {
												// Check If additional form fees is multicheckbox
												if(is_array($fvalue[$ffkey]) && count($fvalue[$ffkey] > 0) && is_array($fvalue[$ffkey][0]) && count($fvalue[$ffkey][0]) > 0){
													$arrfieldvalues = array();
													foreach($fvalue[$ffkey] as $arrfkey => $arrfvalue) $arrfieldvalues[] = $arrfvalue[$tvalue->id];
													$field_value = implode(", ",$arrfieldvalues);
												}else{
													$field_value = $fvalue[$ffkey][$tvalue->id];
												}

										if(!empty($form_title[$tvalue->id]['0'][$fkey])){
											echo "<tr style='border:none !important;'><td style='text-align:right;border:none !important; padding: 3px;width:40%;'>".$form_title[$tvalue->id]['0'][$fkey]."</td>";
												echo "<td style='width:5px;padding: 3px;border:none !important; '>&nbsp;</td>";
												echo "<td align='left' style='border:none !important;padding: 3px; '>".$field_value."</td></tr>";

										}
												$k = 1 - $k;
												$j++;
											}
										}
										$counter++;
									}
								echo "</table>";
								$alterclass++;
								}

								echo "</td></tr>";
								echo "<tr><td colspan='3'><img src='".REGPRO_IMG_PATH."/blank.png' border='0' /></td></tr>";
							}

						}
					}
				}

				######### End Registration forms #################



				if(!empty($total_qty)){
					if ($showMandatoryNotice){
						echo '<tr> <td colspan="3" class="mandatoryNotice">'.JText::_('EVENTS_REGISTRA_MANDATORY_NOTICE').'</td> </tr>';
					}
					echo '</table></td></tr>';
				}

				echo "<input type='hidden' name='quantity' value='".$total_qty."'>";
				// show payment method list
				if($cart['free_event'] == 0){
					$regpro_html = new regpro_html;
					if($regproConfig['multiple_registration_button'] == 1) {
						$regpro_html->list_payment_methods();
					}else{
						$regpro_html->list_event_payment_methods($cart['event_payment_method']);
					}
				}
				?>

			<tr>
				<td width="33%" colspan="3">
				<?php if($row->allowgroup == 1){ ?>
					<input type="hidden" NAME="allowgroupregistration" id="allowgroupregistration" value="1">
				<?php } ?>
					<input type="hidden" NAME="Itemid" value="<?php echo $Itemid ; ?>">
					<input type="hidden" NAME="rdid" value="<?php echo $cart['eventid']; ?>">
					<input type="hidden" NAME="func" value="details">
					<input type="hidden" name="notify" checked value="1">
					<input type="hidden" name="step" value="3">
					<input type="hidden" name="did" value="<?php echo $cart['eventid'];?>">
				</td>
			</tr>

			<?php

				if($regproConfig['event_terms_and_conditions'] == 1){

					if(!empty($row->terms_conditions)){
						$eventids = implode(",",$cart['eventids']);
						$href = REGPRO_SITE_URL.'index.php?option=com_registrationpro&view=event&tmpl=component&layout=terms_and_conditions&eventids='.$eventids;
						$href = JRoute::_($href);
						$link =  '<a href="void(0);" onclick="window.open(\''.$href.'\',\'\',\'width=500, height=500, scrollbars=1,resizable=0,menubar=0,toolbar=0,status=0\'); return false;">'.JText::_('EVENTS_TERMS_CONDITIONS_LINK_TITLE').'</a>';

						$hreftag = str_replace("<link>",$link, JText::_('EVENTS_TERMS_CONDITIONS_TEXT'));
			?>
			<tr>
				<td colspan="3" style="padding:2px 2px 2px 2px;">
					<!--&nbsp;<input type="checkbox" name="agrement" onClick="check(this, document.finalcheckout.submit)" />&nbsp; <?php echo $hreftag; ?>-->
					<label class="checkbox">
						<input type="checkbox" name="agrement" onClick="check(this, document.finalcheckout.submit)"> <?php echo $hreftag; ?>
					</label>
				</td>
			</tr>
			<?php
					}else{
						$link =  '<font color="#FF0000" style="cursor:hand"><b>'.JText::_('EVENTS_TERMS_CONDITIONS_LINK_TITLE').'</b></font>';
						$hreftag = str_replace("<link>",$link, JText::_('EVENTS_TERMS_CONDITIONS_TEXT'));
			?>
			<tr>
				<td colspan="3" style="padding:2px 2px 2px 2px;">
					<!--&nbsp;<input type="checkbox" name="agrement" onClick="check(this, document.finalcheckout.submit)" />&nbsp; <?php echo $hreftag; ?>-->
					<label class="checkbox">
						<input type="checkbox" name="agrement" onClick="check(this, document.finalcheckout.submit)"> <?php echo $hreftag; ?>
					</label>

				</td>
			</tr>
			<?php
					}
				}
			?>

			<tr>
				<td align="left" colspan="3" style="padding:2px 2px 2px 2px;">
					<?php if($regproConfig['event_terms_and_conditions'] == 1){ ?>

						<button type="submit" name="submit" class="btn btn-primary regpro_button" disabled="disabled" onclick="return onformsubmit();">
						  <i class="icon-circle-arrow-right icon-white"></i> <?php echo JTEXT::_('EVENTS_REGISTRA_BUTTON'); ?>
						</button>

						<!--<input type="submit" name="submit" class="regpro_button" onclick="return onformsubmit();" disabled="disabled" value="<?php echo JText::_('EVENTS_REGISTRA_BUTTON'); ?>">	-->
					<?php }else{ ?>

						<button type="submit" name="submit" class="btn btn-primary regpro_button" onclick="return onformsubmit();">
						  <i class="icon-circle-arrow-right icon-white"></i> <?php echo JTEXT::_('EVENTS_REGISTRA_BUTTON'); ?>
						</button>

						<!--<input type="submit" name="submit" class="regpro_button" onclick="return onformsubmit();" value="<?php echo JText::_('EVENTS_REGISTRA_BUTTON'); ?>">-->
					<?php }?>
				</td>
			</tr>
			</table>
	<?php
		}
	?>
		</form>
<?php
}

// notification box for early/group discount box
function discount_message($cart, $regproConfig)
{	$registrationproHelper = new registrationproHelper;
	foreach($cart['ticktes'] as $tkey => $tvalue)
	{
		if(count($tvalue->event_discount_id) > 0){
			foreach($tvalue->event_discount_id as $tdkey => $tdvalue)
			{
				foreach($cart['event_discounts'] as $dkey => $dvalue)
				{
					if($tdvalue == $dvalue->id && $dvalue->discount_name == "G"){
?>						<div class="regpro_outline">
						<div class='alert alert-success'>
							<?php
								echo sprintf(JText::_('EVENTS_GROUP_DISCOUNT_MESSAGE'),$tvalue->product_name);
								//echo JText::_('EVENTS_GROUP_DISCOUNT_MESSAGE')." ".$tvalue->product_name;
							?>
							<?php
								if($dvalue->discount_type == 'A'){
									echo $regproConfig['currency_sign'].$dvalue->discount_amount;
								}else{
									echo $dvalue->discount_amount." %";
								}
							?>
						</div>
						</div>
<?php
					}

					if($tdvalue == $dvalue->id && $dvalue->discount_name == "E"){
?>
						<div class="regpro_outline">
						<div class='alert alert-success'>
							<?php echo sprintf(JText::_('EVENTS_EARLY_DISCOUNT_MESSAGE'),$tvalue->product_name); //echo JText::_('EVENTS_EARLY_DISCOUNT_MESSAGE')." "; ?>
							<?php
								if($dvalue->discount_type == 'A'){
									echo $regproConfig['currency_sign'].$dvalue->discount_amount;
								}else{
									echo $dvalue->discount_amount." %";
								}
							?>
						</div>
						</div>
<?php
					}
				}
			}
		}
	}
}
?>