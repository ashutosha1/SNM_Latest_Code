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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal' );
JHTML::_('behavior.calendar');
$registrationproHelper = new registrationproHelper;
$registrationproHelper->add_regpro_frontend_scripts(array('regpro'),array());

$document	=  JFactory::getDocument();

//add css and js to document
$document->addScript(REGPRO_BASE_URL.'/assets/javascript/formcheck/lang/en.js');
$document->addScript(REGPRO_BASE_URL.'/assets/javascript/formcheck/formcheck.js');
$document->addStyleSheet(REGPRO_BASE_URL.'/assets/javascript/formcheck/theme/classic/formcheck.css');
?>

<script language="javascript">

	var $fvalidate_flag = 0;

	function remove_cart_session_item(sid,eid) {
		var f = document.cartform;
		f.action.value 		= 'remove_cart_session_item';
		f.session_id.value 	= sid;
		f.event_id.value 	= eid;
		f.submit();
	}


	function remove_cart_item(tid,eid) {
		/*var box = $('ajaxmessagebox_frontend');
		var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});*/

		var f = document.cartform;
		f.action.value 		= 'remove_cart_item';
		f.ticket_id.value 	= tid;
		f.event_id.value 	= eid;
		f.submit();

		/*var el = $('cartform');
		el.send({
			update: 'listcart',
			onRequest: function() {
				box.style.display="block";
				box.setHTML('<?php echo JText::_('EVENT_CART_TICKETS_MSG_DELETE_ITEM'); ?>');
			},
			onComplete: function() {
				fx.start({
				}).chain(function() {
					box.setHTML('<?php echo JText::_('EVENT_CART_TICKETS_MSG_DELETED'); ?>');
					this.start.delay(1000, this, {'opacity' : 0});
				}).chain(function() {
					box.style.display="none";
					this.start.delay(0100, this, {'opacity' : 1});
				});
			},
			onFailure: function() {
				alert('ajax request fail!');
			}
		});	*/
	}

	function cart_qty_update(tid,eid) {
		/*var box = $('ajaxmessagebox_frontend');
		var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});*/

		var f = document.cartform;
		f.action.value = 'update_cart_qty';
		f.ticket_id.value 	= tid;
		f.event_id.value 	= eid;
		f.submit();


		/*var el = $('cartform');
		el.send({
			update: 'listcart',
			onRequest: function() {
				box.style.display="block";
				box.setHTML('<?php echo JText::_('EVENT_CART_TICKETS_MSG_QTY_UPDATE'); ?>');
			},
			onComplete: function() {
				fx.start({
				}).chain(function() {
					box.setHTML('<?php echo JText::_('EVENT_CART_TICKETS_MSG_QTY_UPDATED'); ?>');
					this.start.delay(1000, this, {'opacity' : 0});
				}).chain(function() {
					box.style.display="none";
					this.start.delay(0100, this, {'opacity' : 1});
				});
			},
			onFailure: function() {
				alert('ajax request fail!');
			}

		});	*/
	}

	function update_cart() {
		/*var box = $('ajaxmessagebox_frontend');
		var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});*/

		var f = document.cartform;
		f.action.value = 'update_cart';
		f.submit();

		/*if(!validateForm(f,false,false,false,false)){
			return false;
		}else{
			var el = $('cartform');
			el.send({
				update: 'listcart',
				onRequest: function() {
					box.style.display="block";
					box.setHTML('<?php echo JText::_('EVENT_CART_TICKETS_MSG_UPDATE_CART'); ?>');
				},
				onComplete: function() {
					fx.start({
					}).chain(function() {
						box.setHTML('<?php echo JText::_('EVENT_CART_TICKETS_MSG_UPDATED'); ?>');
						this.start.delay(1000, this, {'opacity' : 0});
					}).chain(function() {
						box.style.display="none";
						this.start.delay(0100, this, {'opacity' : 1});
					});
				},
				onFailure: function() {
					alert('ajax request fail!');
				}
			});
		}		*/
	}

	function frm_cart_submit()
	{
		var f = document.cartform;

		if(f.coupon_code.value == ""){
			alert("<?php echo JText::_('EVENT_CART_MSG_COUPON_EMPTY'); ?>");
			return false;
		}

		update_cart();
		return false;
	}


	/*window.addEvent("domready", function() {
    	var regproDetailsValidator = new fValidator("regproDetails");
	 });*/

	 window.addEvent('domready', function(){
		new FormCheck('regproDetails');
	 });

	 function onformsubmit()
	 {

	 	/*alert($fvalidate_flag);

		if ($fvalidate_flag == 0) {
			//alert($fvalidate_flag);
			document.getElementById("mandatory_note").style.display = "block";
			return;
		}else{
			//alert($fvalidate_flag);
			document.getElementById("mandatory_note").style.display = "none";
		}*/
	 }
</script>
</script>
<script language="JavaScript">
	function check(checkbox, submit) {
		if(document.regproDetails.agrement.checked == true){
			submit.disabled = false;
		} else {
			submit.disabled = true;
			//alert("Please check terms and condition");
			return false;
		}
	}
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
//if(!$this->ajaxflag){
	$regpro_header_footer->regpro_header($this->regproConfig);
?>
<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>
<div> <img src="<?php echo REGPRO_IMG_PATH; ?>/cart.png" border="0" align="absmiddle"/> <b> <?php echo JText::_('EVENT_CART_HEADING'); ?> </b> </div>

<div style="text-align:center" id="ajaxmessagebox_frontend"></div>
<?php
//}

if($this->cart) {

	if(!empty($this->cart['error_message']))
		echo "<div class='alert alert'>",$this->cart['error_message'],"</div>";

	if(!empty($this->cart['success_message']))
		echo "<div class='alert alert-success'>",$this->cart['success_message'],"</div>";

?>
	<div id="listcart">
	<?php

		if($this->row->message != ""){
			echo '<div class="alert alert">'.$this->row->message.'</div>'; // error message
		}

		// display event discount message if applied
		//if(is_array($this->cart['ticktes'][0]->event_discount_id) && count($this->cart['ticktes'][0]->event_discount_id) > 0){
		if(is_array($this->cart['event_discounts']) && count($this->cart['event_discounts'] > 0)){
			//echo '<div class="regpro_outline">';
			discount_message($this->cart, $this->regproConfig);
			//echo '</div>';
		}
		// end

		// display cart
		tickets_cart($this->cart, $this->row, $this->regproConfig, $this->cart_form_action);

		//check terms and conditions for all events
		$this->row->terms_conditions = $this->checktermsandconditions($this->cart['eventids']);

		// display forms
		manage_registration_form($this->cart, $this->row, $this->regproConfig, $this->action);
	?>
	</div>
<?php
}else{
?>
	<div style="width:100%; text-align:center"> <?php echo JText::_('EVENT_CART_MSG_EMPTY'); ?> </div>
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
	/*$session = JFactory::getSession();
	$cart =& $session->get('cart');*/
	//echo"<pre>";print_r($row);
	//echo"<pre>";print_r($cart);	exit;
	if($regproConfig['multiple_registration_button']==0){
		$multiple_registration = 0;
	}
	$arr_qty = range(1, $regproConfig['quantitylimit']);
	$tkt_qty = 0;
	//echo"<pre>";print_r($arr_qty);

?>		<div  class="regpro_outline" width="100%">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<td>
			<form name="cartform" id="cartform" action="<?php echo $from_action; ?>" method="post" onsubmit="return frm_cart_submit();">
			<table width="100%" class="regprocart" border="0">
				<thead>
				<tr>
					<th><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TICKET_NAME'); ?></th>
					<th style="text-align:center"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_QTY'); ?></th>
					<th style="text-align:right"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_PRICE'); ?></th>
					<th style="text-align:right"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TAX'); ?></th>
					<th style="text-align:center"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_REMOVE'); ?></th>
					<th style="text-align:right"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TOTAL'); ?></th>
				</tr>
				</thead>
				<tbody>
			<?php
			// loop to arrange all the tickets event wise
			foreach($cart['eventids'] as $ekey => $evalue){
				//echo "<tr><td colspan='6'> <img src='".REGPRO_IMG_PATH."/blank.png' border='0'/> </td></tr>";
				echo "<tr><td colspan='6' class='regpro_eventoncart'><div id='regpro_eventoncart'>";
				echo ucfirst($registrationproHelper->getEventName($evalue));
				//echo "<img src='".REGPRO_IMG_PATH."/event_down.png' border='0' />";
				echo "</div></td></tr>";
				$session_flag = 0;
				foreach($cart['ticktes'] as $tkey=>$tvalue)
				{

					if($evalue == $tvalue->regpro_dates_id) {

						// check if session exists with current event or not
						if(is_array($tvalue->sessions) && count($tvalue->sessions) >0 ) {
							$session_flag = 1;
						}

						if ( $tvalue->qty > 1 && $showbox == 1) $showbox = 1;else $showbox = 0;

						/* if($tvalue->product_quantity > 0){
							// check if ticket quantity is avaliable or not
							$ticket_avaliable_qty = $tvalue->product_quantity - $tvalue->product_quantity_sold;

							$tkt_qty = range(1, $ticket_avaliable_qty);
						}else{
							$tkt_qty = $arr_qty;
						} */
						if($tvalue->product_quantity > 0){
							// check if ticket quantity is avaliable or not
							$ticket_avaliable_qty = $tvalue->product_quantity - $tvalue->product_quantity_sold;

							$tkt_qty = range(1, $ticket_avaliable_qty);
						}else{
							if($multiple_registration==0 && $tvalue->qty > 1){
								$tkt_qty = range(1, $tvalue->qty);
							}else{
								$tkt_qty = $arr_qty;
								}
						}

			?>

				<tr>
					<td class="regpro_vmiddle_aleft"><?php echo $cart['ticktes'][$tkey]->product_name; ?></td>
					<td class="regpro_vmiddle_acenter">
					<select name="qty[<?php echo $cart['ticktes'][$tkey]->id; ?>]" onchange="return cart_qty_update(<?php echo $cart['ticktes'][$tkey]->id; ?>);" style="width:55px;">
							<?php
								foreach($tkt_qty as $qkey => $qvalue)
								{
									if($cart['ticktes'][$tkey]->qty == $qvalue){
										$selected = "selected";
									}else{
										$selected = "";
									}
							 ?>
									<option value="<?php echo $qvalue;?>" <?php echo $selected; ?>><?php echo $qvalue;?></option>
							 <?php
								}
							 ?>
						</select>
					</td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['ticktes'][$tkey]->product_price,2); ?></td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['ticktes'][$tkey]->tax_price,2); ?></td>
					<td class="regpro_vmiddle_acenter">
						<!--<a href="javascript: void(0);" onclick="return remove_cart_item(<?php echo $cart['ticktes'][$tkey]->id; ?>,<?php echo $cart['ticktes'][$tkey]->regpro_dates_id; ?>);" class="btn"> <i class="icon-trash"></i></a>-->

						<button type="button" class="btn regpro_button" onclick="return remove_cart_item(<?php echo $cart['ticktes'][$tkey]->id; ?>,<?php echo $cart['ticktes'][$tkey]->regpro_dates_id; ?>);">
							<i class="icon-trash"></i>
						</button>

					</td>
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
					<td class="regpro_vmiddle_acenter">

					<!--<a href="javascript: void(0);" onclick="return remove_cart_session_item(<?php echo $svalue->id; ?>,<?php echo $svalue->event_id; ?>);"><img src="<?php echo REGPRO_IMG_PATH; ?>/trash.png" border="0" alt="<?php echo JText::_('EVENT_CART_TICKETS_BTN_REMOVE'); ?>" title="<?php echo JText::_('EVENT_CART_TICKETS_BTN_REMOVE'); ?>" /></a>-->

					<button type="button" class="btn regpro_button" onclick="return remove_cart_session_item(<?php echo $svalue->id; ?>,<?php echo $svalue->event_id; ?>);">
						<i class="icon-trash"></i>
					</button>

					</td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($session_fees,2); ?></td>
				</tr>
			<?php
							}
						}
					}
				}
			?>
				</tbody>
			<?php
			}
			?>
				<!--<tr> <td colspan="6"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" height="2px;"/></td> </tr>-->

				<tr>
					<td colspan="4" class="regpro_vtop_aleft">
						<?php
						if($regproConfig['enable_discount_code'] == 1) {
							echo JText::_('EVENT_CART_TICKETS_LBL_DISCOUNT_CODE'); ?> :
						<input type="text" class="regpro_inputbox" name="coupon_code" value=""id="coupon_code"/>
						<br/>
						<button type="submit" class="btn btn-small btn-primary regpro_button" style="float:right;">
						  <i class="icon-refresh icon-white"></i> <?php echo JTEXT::_('EVENT_CART_TICKETS_BTN_UPDATE_CART'); ?>
						</button>

						<!--<input type="submit" class="btn btn-primary regpro_button" value="<?php echo JText::_('EVENT_CART_TICKETS_BTN_UPDATE_CART'); ?>"/>-->
						<?php
						}else{
						?>
							<img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" />
						<?php
						} ?>
					</td>

					<td class="regpro_vtop_aright">
						<!--<table border="0" width="100%" cellpadding="0" cellspacing="0">
							<?php
								if($cart['group_discount'] > 0 || $cart['early_discount'] > 0 || $cart['discount'] > 0){
							?>
							<tr>
								<td class="regpro_vtop_aright"><b><?php echo JText::_('EVENT_CART_TICKETS_LBL_SUBTOTAL'); ?> :</b></td>
							</tr>

							<tr>
								<td class="regpro_vtop_aright"><b><?php echo JText::_('EVENT_CART_TICKETS_LBL_DISCOUNT'); ?> :</b></td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td class="regpro_vtop_aright"><b><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TOTAL'); ?> :</b></td>
							</tr>
						</table>-->

						<?php
							if($cart['group_discount'] > 0 || $cart['early_discount'] > 0 || $cart['discount'] > 0){
						?>
							<div class="regpro_vtop_aright"><?php echo JText::_('EVENT_CART_TICKETS_LBL_SUBTOTAL'); ?> :</b></div>
							<div class="regpro_vtop_aright"><?php echo JText::_('EVENT_CART_TICKETS_LBL_DISCOUNT'); ?> :</b></div>
						<?php
							}
						?>
							<div class="regpro_vtop_aright"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TOTAL'); ?> :</b></div>

					</td>
					<td class="regpro_vtop_aright">
						<!--<table border="0" width="100%" cellpadding="0" cellspacing="0">
							<?php
								if($cart['discount'] > 0){
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format($cart['sub_total'],2); ?></td>
							</tr>

							<tr>
								<td class="regpro_vtop_aright"><?php echo '-'. $cart['currency_sign'].number_format($cart['discount'],2); ?></td>
							</tr>
							<?php
								}else{
							?>
                            <?php
									if($cart['group_discount'] > 0 || $cart['early_discount'] > 0){
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format($cart['sub_total'],2); ?></td>
							</tr>

							<tr>
								<td class="regpro_vtop_aright"><?php echo ' - '.$cart['currency_sign'].number_format($cart['both_discounts'],2); ?></td>
							</tr>
							<?php
									}
								}
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format($cart['grand_total'],2); ?></td>
							</tr>
						</table>-->

						<?php
								if($cart['discount'] > 0){
							?>
									<div class="regpro_vtop_aright">
										<?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['sub_total']),2); ?>
									</div>
									<div class="regpro_vtop_aright">
										<?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['discount']),2); ?>
									</div>
							<?php
								}else{
							?>
                            <?php
									if($cart['group_discount'] > 0 || $cart['early_discount'] > 0){
							?>
										<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['sub_total']),2); ?></div>
										<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['both_discounts']),2); ?></div>
							<?php
									}
								}
							?>

							<div class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format($cart['grand_total'],2); ?></div>
					</td>
				</tr>
			</table>
				<input type="hidden" name="action" value="" />
				<input type="hidden" name="session_id" value="" />
				<input type="hidden" name="ticket_id" value="" />
				<input type="hidden" name="event_id" value="" />
				<input type="hidden" name="did" value="<?php echo $row->did; ?>" />
				<input type="hidden" NAME="Itemid" value="<?php echo $Itemid ; ?>">
			</form>
		</td>
	</tr>
	</table>
	</div>
	<?php
	if($regproConfig['multiple_registration_button'] == 1) { ?>
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<td style="text-align:center">
				<div class="regpro_multipleeventtext"> <?php echo JTEXT::_('EVENT_ADD_ANOTHER_EVENT_TICKET_ABOVE_TEXT'); ?></div>
				<?php $addTicketLink = JRoute::_('index.php?option=com_registrationpro&view=events&Itemid='.$Itemid); ?>
				<button class="btn regpro_button" onclick="window.location='<?php echo $addTicketLink;?>'">
				  <i class="icon-plus-sign"></i> <?php echo JTEXT::_('EVENT_ADD_ANOTHER_EVENT_TICKET'); ?>
				</button>
				<!--<img src="<?php echo REGPRO_IMG_PATH; ?>/add_event.png" border="0" align="absmiddle" /> <input type="button" value="<?php echo JTEXT::_('EVENT_ADD_ANOTHER_EVENT_TICKET'); ?>" class="btn btn-primary regpro_button" onclick="window.location='<?php echo $addTicketLink;?>'" />-->
		</td>
	</tr>
	</table>
<?php
	}
}

// Manage registration form
function manage_registration_form($cart, $row, $regproConfig, $form_action)
{
		global $Itemid;
		$registrationproHelper = new registrationproHelper;
		$database	= JFactory::getDBO();
		$my	= JFactory::getUser();
	?>
		<!--<form name="regproDetails" id="regproDetails" action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data" onSubmit="return validateForm(this,false,false,false,false);">-->
		<form name="regproDetails" id="regproDetails" class="fValidator-form" action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data">
		<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>
		<div style="width: 100%" id="regpro_outline">
		<table border="0" width="100%" class="regproform table table-bordered">
		<?php
			if($cart) {
				//display form data here
				//loop ticket first display batch fields
				$total_qty = 0;
				//there is check for ticket quantities
				$total_qty = $cart['total_tqty'];
				//echo "<pre>";print_r($fields);
				//echo "<pre>";print_r($cart);
				//exit;
				if(!empty($total_qty)){
					echo '<tr>';
						echo '<th colspan="3" class="regpro_sectiontableheader" style="text-align:center">'.JText::_('EVENT_CART_FORM_HEADING').'</th>';
					echo '</tr>';
					//echo '<tr><th colspan="3"><div id="errors"></div></th></tr>';
				}
					######### Start Registration forms #################
					//if(is_array($cart['groupregistrations']) && count($cart['groupregistrations']) > 0 ){  //If group registration exists
					$formcounter = 0;
					//$frm = 0;
					// loop to arrange all the tickets event wise
				foreach($cart['eventids'] as $ekey => $evalue){
						//$formcounter = 0;
						foreach($cart['ticktes'] as $tkey=>$tvalue){
							if($cart['ticktes'][$tkey]->type == "E" && $evalue == $cart['ticktes'][$tkey]->regpro_dates_id){
								$groupregistration = 0;

								// get form id to get the form and fields data for every ticket
								$formid = $registrationproHelper->getEventFormId($cart['ticktes'][$tkey]->regpro_dates_id);

								// get form and fields according to event id
								$form_model 			= new regpro_forms($formid);
								$fields 				= $form_model->getFields();
								$conditional_fields 	= $form_model->getConditionalField_lists();

								// get event date
								$event_date = $registrationproHelper->getEventInfo($cart['ticktes'][$tkey]->regpro_dates_id);
								$event_date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $event_date->dates);

								$showMandatoryNotice = false;
								if($regproConfig['cbintegration'] == 1){
									// Check CB Integration setting and get CB data
									if($registrationproHelper->chkCB() && !empty($my->id)){
										$fields		= $form_model->getCBfields();
										$cb_exists	= 1;
									}
								}elseif($regproConfig['cbintegration'] == 2) {
									// Check Joomsocial Integration setting and get joomfish fields data
									if($registrationproHelper->chkJoomsocial() && !empty($my->id)){																																$fields		= $form_model->getJoomsocialfields();
										$cb_exists	= 1;
									}
								}elseif($regproConfig['cbintegration'] == 3) {
									// Check Core joomla profiles plugins fields data
									if($registrationproHelper->chkCoreProfiles() && !empty($my->id)){																																$fields		= $form_model->getCorefields();
										$cb_exists	= 1;
									}
								}else{
									$cb_exists	= 0;
								}
								// end

								if(is_array($cart['groupregistrations']) && count($cart['groupregistrations']) > 0 ){
									foreach($cart['groupregistrations'] as $gkey => $gvalue){
										if($gvalue == $cart['ticktes'][$tkey]->regpro_dates_id){
											$groupregistration = 1;
										}
									}
								}
								$regpro_forms_html = new regpro_forms_html;
								//echo "<pre>";print_r($conditional_fields); exit;

								//if($cart['allowgroup'] == 1){
								if($groupregistration == 1){
									echo '<tr>';
										echo '<td colspan="2">';
											echo '<strong>';
												echo ucwords($cart['ticktes'][$tkey]->product_name).'&nbsp;';
												echo JText::_('EVENTS_REGISTRA_COMMON_FIELDS');
											echo '<strong>';
										echo '</td>';
										echo '<td>';
											echo '<span style="float: right">';
											echo JText::_('EVENT_CART_FORM_EVENT_DATE')."<br/>".$event_date;
											echo '</span>';
										echo "</td>";
									echo "</tr>";
									//echo "<tr><td colspan='2' align='center'>";
									//echo "<table border='2px' width='100%' cellspacing='0' cellpadding='2'>";
									//echo '<tr>';
									//echo '<td height="4px" width="40%"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';
									//echo '<td height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';
									//echo '</tr>';
									$k = 0;
									foreach ($fields as $key=>$field){
										if ($field->validation_rule) {
											$showMandatoryNotice = true;
											$checkFields[$field->validation_rule][] = $field->name;
										}

										// get the title of CB filed and assign to form element for display proper titie in reports and confirmation emails
										if($field->cbfeild_id > 0){
											$field->name = getLangDefinition($field->title);
										}
										// end

										if($cart["ticktes"][$tkey]->id <= 0){
											$cart["ticktes"][$tkey]->id = date('jnis');
										}
										$title_orignal[$cart['ticktes'][$tkey]->id][$formcounter][$field->name] = $field->title;
										$regpro_forms_html->parseFields($field,$conditional_fields,'['.$cart["ticktes"][$tkey]->id.']',$row->form[$field->name][$payment_details->id][$i],$formcounter,$cart['ticktes'][$tkey]->id,$k);
									}
									echo '<input type="hidden" name="form[regpro_event_id][]['.$cart["ticktes"][$tkey]->id.']" value="'.$cart["ticktes"][$tkey]->regpro_dates_id.'"/>';
									echo '<input type="hidden" name="users_tickets[ticket_ids][][0]" value="'.$cart["ticktes"][$tkey]->id.'"/>';
									//echo '<tr><td colspan="2" height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td></tr>';
									//echo "</table>";
									//echo "</td></tr>";
									//break;

									$formcounter++;
									$k = 1 - $k;
								}else{
									echo '<tr>';
										echo '<td colspan="2">';
											echo '<strong>';
												echo ucwords($cart['ticktes'][$tkey]->product_name).'&nbsp;';
												echo JText::_('EVENTS_REGISTRA_COMMON_FIELDS');
											echo '<strong>';
										echo '</td>';
										echo "<td style='text-align:right;'>";
											echo JText::_('EVENT_CART_FORM_EVENT_DATE')."<br/>".$event_date;
										echo "</td>";
									echo "</tr>";
									echo "<tr>";
										echo "<td>";
											$k = 0;
											for($i=0;$i<$cart['ticktes'][$tkey]->qty;$i++){
												if($i > 0)
													//echo "<tr><td colspan='2' class='well well-small'><img src='".REGPRO_IMG_PATH."/blank.png' border='0' /></td></tr>";

													//echo '<tr>';
													//echo '<td height="2px" width="40%"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';
													//echo '<td height="2px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';
													//echo '</tr>';
												$session = JFactory::getSession();
												foreach ($fields as $key=>$field){
													if ($field->validation_rule) {
														$showMandatoryNotice = true;
														$checkFields[$field->validation_rule][] = $field->name;
													}

													// get the title of CB filed and assign to form element for display proper titie in reports and confirmation emails
													if($field->cbfeild_id > 0){
														$field->name = getLangDefinition($field->title);
													}
													// end
													//echo $i.'-->'.$field->name;
													 $title_orignal[$cart['ticktes'][$tkey]->id][$i][$field->name] = $field->title;
													$regpro_forms_html->parseFields($field,$conditional_fields,'['.$cart["ticktes"][$tkey]->id.']',$row->form[$field->name][$payment_details->id][$i],$formcounter,$cart['ticktes'][$tkey]->id,$k);

													//regpro_forms_html::parseFields($field,'[]['.$cart["ticktes"][$tkey]->id.']',$row->form[$field->name][$payment_details->id][$i],$formcounter,$cart['ticktes'][$tkey]->id);
													//regpro_forms_html::parseFields($field,'['.$formcounter.']['.$cart["ticktes"][$tkey]->id.']',$row->form[$field->name][$payment_details->id][$i],$formcounter,$cart['ticktes'][$tkey]->id);
												}
												// $title_orignal=array();
												echo '<input type="hidden" name="form[regpro_event_id][]['.$cart["ticktes"][$tkey]->id.']" value="'.$cart["ticktes"][$tkey]->regpro_dates_id.'"/>';
												echo '<input type="hidden" name="users_tickets[ticket_ids][]['.$i.']" value="'.$cart["ticktes"][$tkey]->id.'"/>';
												//echo '<tr><td colspan="2" height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td></tr>';
												$formcounter++;

												$k = 1 - $k;
											}
										echo "</td>";
									echo "</tr>";
								}
							}
						}
					$frm++;
				}
				############# End Registration forms section #################

				if(!empty($total_qty)){
					if ($showMandatoryNotice){
						echo '<tr>';
							echo '<td class="mandatoryNotice" colspan="3">'.JText::_('EVENTS_REGISTRA_MANDATORY_NOTICE').'</td>';
						echo '</tr>';
					}
				}
				echo "<input type='hidden' name='quantity' value='".$total_qty."'>";
				// show payment method list
				//if($cart['grand_total'] > 0){
				/*if($cart['free_event'] == 0){
					if($regproConfig['multiple_registration_button'] == 1) {
						regpro_html::list_payment_methods();
					}else{
						regpro_html::list_event_payment_methods($cart['event_payment_method']);
					}
				}*/
				// end
			}
		?>
				<?php if($row->allowgroup == 1){ ?>
					<input type="hidden" NAME="allowgroupregistration" id="allowgroupregistration" value="1">
				<?php } ?>
					<input type="hidden" NAME="Itemid" value="<?php echo $Itemid ; ?>">
					<input type="hidden" NAME="rdid" value="<?php echo $cart['eventid']; ?>">
					<input type="hidden" NAME="func" value="details">
					<input type="hidden" name="notify" checked value="1">
					<input type="hidden" name="step" value="3">
					<input type="hidden" name="did" value="<?php echo $cart['eventid'];?>">
			<!--<tr>
				<td colspan="3" height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>
			</tr>-->


			<tr>
				<td align="center" colspan="3" style="padding:2px 2px 2px 2px;">
					<br/>
					<button type="submit" name="submit" class="btn btn-primary regpro_button" onclick="return onformsubmit();">
						  <i class="icon-circle-arrow-right icon-white"></i> <?php echo JTEXT::_('EVENTS_REGISTRA_BUTTON'); ?>
					</button>
					<br/><br/>
				</td>
			</tr>
		</table>
	<?php
		//}

		$session = JFactory::getSession();
		$title_orignal = $session->set('titles',$title_orignal);
		//echo "<pre>"; $cart1 =& $session->get('titles');print_r($cart1);
	?>
		</div>
		<div style="clear: both;"></div>
		</form>
<?php
}


// notification box for early/group discount box
function discount_message($cart, $regproConfig) {
	$registrationproHelper = new registrationproHelper;
	$current = $registrationproHelper->getCurrent_date('Y-m-d');
	foreach($cart['ticktes'] as $tkey => $tvalue)
	{
		if(count($tvalue->event_discount_id) > 0){
			foreach($tvalue->event_discount_id as $tdkey => $tdvalue)
			{
				foreach($cart['event_discounts'] as $dkey => $dvalue)
				{
					if($tdvalue == $dvalue->id && $dvalue->discount_name == "G"){
					?>
						<div class="alert alert-success">
						<?php
							echo sprintf(JText::_('EVENTS_GROUP_DISCOUNT_MESSAGE'),$tvalue->product_name);

							if($dvalue->discount_type == 'A'){
								echo $regproConfig['currency_sign'].$dvalue->discount_amount;
							} else echo $dvalue->discount_amount." %";
						?>
						</div>
					<?php
					}

					if($tdvalue == $dvalue->id && $dvalue->discount_name == "E" && $dvalue->early_discount_date > $current){
					?>
						<div class="regpro_outline">
							<div class="alert alert-success">
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
<?php                }	
				}
			}
		}
	}
}
?>