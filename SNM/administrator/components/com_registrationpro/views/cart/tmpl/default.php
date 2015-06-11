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
JHTML::_( 'behavior.modal' );
JHTML::_('behavior.calendar');

$document	= JFactory::getDocument();
		
//add css and js to document
$document->addScript(REGPRO_BASE_URL.'/assets/javascript/formcheck/lang/en.js');
$document->addScript(REGPRO_BASE_URL.'/assets/javascript/formcheck/formcheck.js');
$document->addStyleSheet(REGPRO_BASE_URL.'/assets/javascript/formcheck/theme/classic/formcheck.css');

//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_EVENTS_ADD_USER' ), 'newuser' );
JToolBarHelper::divider();
JToolBarHelper::back();
//echo "<pre>";print_r($this->cart);echo "</pre>";
?>

<script language="javascript">
		
	function remove_cart_item(tid) {							
		/*var box = $('ajaxmessagebox_frontend');
		var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});*/
	
		var f = document.cartform;									
		f.action.value 		= 'remove_cart_item';
		f.ticket_id.value 	= tid;
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
		});		*/						
	}
	
	function cart_qty_update(tid) {							
		/*var box = $('ajaxmessagebox_frontend');
		var fx = box.effects({duration: 1000, transition: Fx.Transitions.Quart.easeOut});*/
	
		var f = document.cartform;									
		f.action.value = 'update_cart_qty';
		f.ticket_id.value 	= tid;
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
		
		});*/								
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
		}*/															
	}
	
	function frm_cart_submit()
	{
		var f = document.cartform;
		var sub = "<?php echo $this->cart['grand_total'];?>";
		
		if(f.coupon_code.value == "" && f.admin_discount.value == ""){	
			alert("<?php echo JText::_('EVENT_CART_MSG_COUPON_EMPTY'); ?>");
			f.coupon_code.focus();
			return false;
		}
		
		/* Check if entered discount amount is number or not */
		if(f.admin_discount.value != "" && isNaN(f.admin_discount.value)){
			alert("<?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_DISCOUNT_AMOUNT'); ?>");
			f.admin_discount.focus();
			return false;
		}
		
		/* Check if discount amount is greater then total amount */
		if(parseInt(f.admin_discount.value) > parseInt(sub)){
			alert("<?php echo JText::sprintf('COM_REGISTRATIONPRO_ADMIN_INVALID_DISCOUNT_AMOUNT',$this->cart['currency_sign'].' '.$this->cart['grand_total']);?>");
			f.admin_discount.focus();
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
	
</script>

<script language="JavaScript">		
	function check(checkbox, submit) {
		if(checkbox.checked==true){
			submit.disabled = false;
		} else {
			submit.disabled = true;
		}
	}						
</script>
<div class="span10">
<?php
if(!$this->ajaxflag){
	//regpro_header_footer::regpro_header($this->regproConfig);
?>
<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>

<div class="regpro_outline" id="regpro_outline" style="border:0;padding: 15px;">
	<table width="100%" border="0">
  	<?php	
		if($this->regproConfig['showtitle']==1){
			fn_EventDetailsName($this->row, $this->regproConfig);
		}	
  	?>
	</table>
</div>

<table border="0" cellpadding="2" cellspacing="0" width="100%">	
	<tr>
		<td>
			<img src="<?php echo REGPRO_IMG_PATH; ?>/cart.png" border="0" align="absmiddle"/> <b> <?php echo JText::_('EVENT_CART_HEADING'); ?> </b>	
			<div style="text-align:center" id="ajaxmessagebox_frontend"></div>		
		</td>
	</tr>
</table>
<?php
}

if(!empty($this->cart['error_message']))
	echo "<div class='regpro_error'>",$this->cart['error_message'],"</div>";


if($this->cart) {
?>
	<div id="listcart">	
	<?php
	
		if($this->row->message != ""){
			echo '<div class="regpro_error">'.$this->row->message.'</div>'; // error message
		}
		
		// display event discount message if applied
		if(is_array($this->cart['ticktes'][0]->event_discount_id) && count($this->cart['ticktes'][0]->event_discount_id) > 0){
			echo '<div class="regpro_outline">';
			discount_message($this->cart, $this->regproConfig);
			echo '</div>';
		}
		// end
	
		// display cart
		tickets_cart($this->cart, $this->row, $this->regproConfig, $this->cart_form_action);
		
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
/*if(!$this->ajaxflag)
	regpro_header_footer::regpro_footer($this->regproConfig);*/

// Show event name	
function fn_EventDetailsName($row, $regproConfig){
	if($regproConfig['showtitle'] == 1) {
	?> 	  		
		<tr> <td colspan="3"><?php echo JText::_('EVENTS_NAME');?>&nbsp; <b><?php echo $row->titel; ?></b> </td> </tr>
	<?php
	} 	
}

function tickets_cart($cart, $row, $regproConfig, $from_action)
{		
	/*$session = &JFactory::getSession();		
	$cart =& $session->get('cart');*/
	//echo"<pre>";print_r($row);	
	//echo"<pre>";print_r($cart);
							
	$arr_qty = range(1, $regproConfig['quantitylimit']);
	$tkt_qty = 0;
	//echo"<pre>";print_r($arr_qty);

?>		
	<table border="0" cellpadding="2" cellspacing="0" width="100%">					
	<tr>
		<td>						
			<div class="regpro_outline" id="regpro_outline">
			<form name="cartform" id="cartform" action="<?php echo $from_action; ?>" method="post" onsubmit="return frm_cart_submit();">
			<table border="0" cellpadding="2" cellspacing="0" width="100%">					
				<tr>
					<td class="regpro_sectiontableheader"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TICKET_NAME'); ?></td>
					<td class="regpro_sectiontableheader" style="text-align:center"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_QTY'); ?></td>
					<td class="regpro_sectiontableheader" style="text-align:right"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_PRICE'); ?></td>
					<td class="regpro_sectiontableheader" style="text-align:right"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TAX'); ?></td>
					<td class="regpro_sectiontableheader" style="text-align:center"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_REMOVE'); ?></td>
					<td class="regpro_sectiontableheader" style="text-align:right"><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TOTAL'); ?></td>
				</tr>
			<?php 
			
		
				foreach($cart['ticktes'] as $tkey=>$tvalue)
				{
									
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
					<td class="regpro_vmiddle_acenter">							
						<select name="qty[<?php echo $cart['ticktes'][$tkey]->id; ?>]" onchange="return cart_qty_update(<?php echo $cart['ticktes'][$tkey]->id; ?>);" style="width:50px;">
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
						<a href="javascript: void(0);" onclick="return remove_cart_item(<?php echo $cart['ticktes'][$tkey]->id; ?>);"><img src="<?php echo REGPRO_IMG_PATH; ?>/trash.png" border="0" alt="<?php echo JText::_('EVENT_CART_TICKETS_BTN_REMOVE'); ?>" title="<?php echo JText::_('EVENT_CART_TICKETS_BTN_REMOVE'); ?>" /></a>
					</td>
					<td class="regpro_vmiddle_aright"><?php echo $cart['currency_sign'].number_format($cart['ticktes'][$tkey]->total_amount,2); ?></td>
				</tr>			
			<?php
				}
			?>
				<tr> <td colspan="6"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td> </tr>
				
				<tr>
					<td colspan="4" class="regpro_vtop_aleft">
						<?php echo JText::_('EVENT_CART_TICKETS_LBL_DISCOUNT_CODE'); ?> : 
						<input type="text" name="coupon_code" value="" size="15" alt="blank" ems="<?php echo JText::_('EVENT_CART_MSG_COUPON_EMPTY'); ?>" />
						
						<br/>
						<?php echo JText::_('COM_REGISTRATIONPRO_EVENT_CART_TICKETS_LBL_ADMIN_DISCOUNT_CODE');?>
						<input type="text" name="admin_discount" value="" size="15" alt="blank"/>
						<?php echo '<b>'.$cart['currency_sign'].'</b>'?>
						<br/>
						<input type="submit" class="btn btn-primary"value="<?php echo JText::_('EVENT_CART_TICKETS_BTN_UPDATE_CART'); ?>"/>				
					</td>	
					
						
					<td class="regpro_vtop_aright">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>
							</tr>
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
								if(!empty($cart['AdminDiscount'])){
							?>
							<tr>
								<td>
									<b><?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_DISCOUNT_LABEL'); ?> :</b>
								</td>
							</tr>
							<?php
								}
							?>
							<?php
								}elseif(!empty($cart['AdminDiscount'])){
							?>
							<tr>
								<td class="regpro_vtop_aright"><b><?php echo JText::_('EVENT_CART_TICKETS_LBL_SUBTOTAL'); ?> :</b></td>
							</tr>
							<tr>
								<td>
									<b><?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_DISCOUNT_LABEL'); ?> :</b>
								</td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>
							</tr>
							<tr>
								<td class="regpro_vtop_aright"><b><?php echo JText::_('EVENT_CART_TICKETS_HEAD_TOTAL'); ?> :</b> </td>
							</tr>
						</table>
					</td>
					<td class="regpro_vtop_aright">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>
							</tr>
							<?php
								if($cart['discount'] > 0){
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['sub_total']),2); ?></td>
							</tr>
							
							<tr>
								<td class="regpro_vtop_aright"><?php echo '- '. $cart['currency_sign'].number_format(str_replace(',', '', $cart['discount']),2); ?></td>
							</tr>
							<?php
								if(!empty($cart['AdminDiscount']))
								{
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo '- '. $cart['currency_sign'].$cart['AdminDiscount']; ?></td>
							</tr>
							<?php
								}
							?>
							<?php
								}elseif(!empty($cart['AdminDiscount'])){
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['sub_total']),2); ?></td>
							</tr>
							<tr>
								<td class="regpro_vtop_aright"><?php echo '- '. $cart['currency_sign'].$cart['AdminDiscount']; ?></td>
							</tr>
							<?php
								}else{
							?>
                            <?php						
									if($cart['group_discount'] > 0 || $cart['early_discount'] > 0){
							?>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format(str_replace(',', '', $cart['sub_total']),2); ?></td>
							</tr>
							
							<tr>
								<td class="regpro_vtop_aright"><?php echo ' - '.$cart['currency_sign'].number_format(str_replace(',', '', $cart['both_discounts']),2); ?></td>
							</tr>
							<?php
									}								
								}
							?>
							<tr>
								<td height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>
							</tr>
							<tr>
								<td class="regpro_vtop_aright"><?php echo $cart['currency_sign'].number_format($cart['grand_total'],2); ?></td>
							</tr>								
						</table>				
					</td>
				</tr>
				<tr>
					<td colspan="6" height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>
				</tr>														
			</table>						
				<input type="hidden" name="action" value="" />
				<input type="hidden" name="ticket_id" value="" />
				<input type="hidden" name="did" value="<?php echo $row->did; ?>" />
				<input type="hidden" name="hidemainmenu" value="1" />
			</form>			
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
		
		$database	=JFactory::getDBO();
		
		//echo "<pre>"; print_r($_POST);
		$database->setQuery("SELECT count(*) cnt FROM #__registrationpro_forms WHERE published=1 AND id = '$row->form_id'");
		$cnt = $database->loadResult();
	?>			
		<!--<form name="regproDetails" id="regproDetails" action="<?php echo $form_action; ?>" method="post" onSubmit="return validateForm(this,false,false,false,false);">-->
		<form name="regproDetails" id="regproDetails" class="fValidator-form" action="<?php echo $form_action; ?>" method="post" enctype="multipart/form-data">
		<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">				
		<?php
		
		// Get Cart Session
		/*$session 		=JFactory::getSession();
		$cart 			=& $session->get('cart');
		$cb_exists 		=& $session->get('cb_exists');*/
		// end
		
		if($cart) {
			//display form data here		
			if($cnt){
			
				//loop ticket first display batch fields
				$total_qty = 0;
											
				//there is check for ticket quantities			
				$total_qty = $cart['total_tqty'];
				
				// get form fields
				$form_model = new regpro_forms($row->form_id);				
				$fields 	= $form_model->getFields();
				$conditional_fields 	= $form_model->getConditionalField_lists();	
				
				$showMandatoryNotice = false; 														
				
				// Check CB Integration setting and get CB data (added by sdei on 11-Feb-08)					
					/*if($registrationproHelper->chkCB()){																																																															
						$fields		= $form_model->getCBfields();
						//	echo "<pre>";print_r($fields);
					}*/
				// end
				$registrationproHelper = new registrationproHelper;
				if($regproConfig['cbintegration'] == 1){								
					// Check CB Integration setting and get CB data
					if($registrationproHelper->chkCB()){																																																															
						$fields		= $form_model->getCBfields();
						$cb_exists	= 1;						
					}
				}elseif($regproConfig['cbintegration'] == 2) {
					// Check Joomsocial Integration setting and get joomfish fields data
					if($registrationproHelper->chkJoomsocial()){																																																															
						$fields		= $form_model->getJoomsocialfields();
						$cb_exists	= 1;						
					}
				}elseif($regproConfig['cbintegration'] == 3) {
					// Check Core joomla profiles plugins fields data
					if($registrationproHelper->chkCoreProfiles()){																																																															
						$fields		= $form_model->getCorefields();
						$cb_exists	= 1;						
					}
				}else{
					$cb_exists	= 0;
				}
				// end
						
				//echo "<pre>";print_r($fields); exit;
				//echo "<pre>";print_r($cart);
				//exit;
				if(!empty($total_qty)){	
					echo '<tr><td colspan="3" class="regpro_outline" id="regpro_outline">';
					echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">';
					echo '<tr><td colspan="3" class="regpro_sectiontableheader" style="text-align:center">'.JText::_('EVENT_CART_FORM_HEADING').'</td></tr>';
					echo '<tr><td colspan="3"><div id="errors"></div></td></tr>';
				}
				$regpro_forms_html = new regpro_forms_html;
				$formcounter = 0;							
				foreach($cart['ticktes'] as $tkey=>$tvalue){			
					if($cart['ticktes'][$tkey]->type == "E"){					
						
						if($cart['allowgroup'] == 1){
							echo '<tr><td colspan="3"><strong>'.ucwords($cart['ticktes'][$tkey]->product_name).'&nbsp;'.JText::_('EVENTS_REGISTRA_COMMON_FIELDS').'<strong></td></tr>';
							echo "<tr><td colspan='3' align='center'>";
							echo "<table border='0' width='100%' cellspacing='0' cellpadding='2'>";
							echo '<tr>';
							echo '<td height="4px" width="40%"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';								
							echo '<td height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';
							echo '</tr>';
							$k = 0;														
							foreach ($fields as $key=>$field){
								if ($field->validation_rule) {
									$showMandatoryNotice = true;
									$checkFields[$field->validation_rule][] = $field->name;
								}
								
								 $title_orignal[$cart['ticktes'][$tkey]->id][$formcounter][$field->name] = $field->title;							
								$regpro_forms_html->parseFields($field,$conditional_fields,'['.$cart["ticktes"][$tkey]->id.']',$row->form[$field->name][$payment_details->id][$i],$formcounter,$cart['ticktes'][$tkey]->id, $k);
							}
							echo '<input type="hidden" name="form[regpro_event_id][]['.$cart["ticktes"][$tkey]->id.']" value="'.$cart["ticktes"][$tkey]->regpro_dates_id.'"/>';
							echo '<input type="hidden" name="users_tickets[ticket_ids][][0]" value="'.$cart["ticktes"][$tkey]->id.'"/>';																	
							echo '<tr><td colspan="2" height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td></tr>';
							echo "</table></td></tr>";
							//break;
							
							$formcounter++;
							$k = 1 - $k;							
						}else{
							echo '<tr><td colspan="3"><strong>'.ucwords($cart['ticktes'][$tkey]->product_name).'&nbsp;'.JText::_('EVENTS_REGISTRA_COMMON_FIELDS').'<strong></td></tr>';
							echo "<tr><td colspan='3' align='center'>";
							echo "<table border='0' width='100%' cellspacing='0' cellpadding='2'>";
							
							$k = 0;
							for($i=0;$i<$cart['ticktes'][$tkey]->qty;$i++)	
							{	
								if($i > 0)
									echo "<tr><td colspan='2' class='regpro_reg_form_separator'><img src='".REGPRO_IMG_PATH."/blank.png' border='0' /></td></tr>";
									
								echo '<tr>';
								echo '<td height="4px" width="40%"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';								
								echo '<td height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td>';
								echo '</tr>';
															
								foreach ($fields as $key=>$field){
									if ($field->validation_rule) {
										$showMandatoryNotice = true;
										$checkFields[$field->validation_rule][] = $field->name;
									}
									 $title_orignal[$cart['ticktes'][$tkey]->id][$i][$field->name] = $field->title;	
									
									$regpro_forms_html->parseFields($field,$conditional_fields,'['.$cart["ticktes"][$tkey]->id.']',$row->form[$field->name][$payment_details->id][$i],$formcounter,$cart['ticktes'][$tkey]->id, $k);			
								}
								echo '<input type="hidden" name="form[regpro_event_id][]['.$cart["ticktes"][$tkey]->id.']" value="'.$cart["ticktes"][$tkey]->regpro_dates_id.'"/>';
								echo '<input type="hidden" name="users_tickets[ticket_ids][]['.$i.']" value="'.$cart["ticktes"][$tkey]->id.'"/>';																	
								echo '<tr><td colspan="2" height="4px"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></td></tr>';
								$formcounter++;
								$k = 1 - $k;
							}
								echo "</table>";
								echo "</td></tr>";
						}

					}
				}						
	
				if(!empty($total_qty)){
					if ($showMandatoryNotice){
						echo '<tr> <td colspan="3" class="mandatoryNotice">'.JText::_('EVENTS_REGISTRA_MANDATORY_NOTICE').'</td> </tr>';			
					}
					echo '</table></td></tr>';	
				}
				
				echo "<input type='hidden' name='quantity' value='".$total_qty."'>";							
				// show payment method list
				//if($cart['grand_total'] > 0){
				if($cart['free_event'] == 0){
					//$registrationproHelper->list_payment_methods();
				}
				// end			
				?>
					
			<tr>								
				<td width="33%" colspan="3">
				<?php if($row->allowgroup == 1){ ?>
					<input type="hidden" NAME="allowgroupregistration" id="allowgroupregistration" value="1">
				<?php } ?>					
					<input type="hidden" NAME="Itemid" value="<?php echo $Itemid ; ?>">
					<input type="hidden" NAME="rdid" value="<?php echo $row->did ; ?>">
					<input type="hidden" NAME="func" value="details">
					<input type="hidden" NAME="hidemainmenu" value="1" />
				</td>
			</tr>
			
			<tr>
					<td align="right"><?php //echo _EVENTS_REGISTRA_NOTIFY." "; ?></td>
					<td><input type="hidden" name="notify" checked value="1">
					<input type="hidden" name="step" value="3">
					<input type="hidden" name="did" value="<?php echo $row->did;?>"></td>
					<td>&nbsp;</td>
			</tr>
	
			<?php 								
				/*if(!empty($row->terms_conditions)){						
				 $link = JHTML::_('behavior.modal', 'a.termsmodal', array('size'=>array('x'=>400, 'y'=>800)));
				 $link .= '<a class="termsmodal" href="index.php?option=com_registrationpro&func=terms&id='.$row->did.'" rel="{handler: \'iframe\', size: {x: 600, y: 400}}">'.JText::_('EVENTS_TERMS_CONDITIONS_LINK_TITLE').'</a>';
				 								
				$hreftag = str_replace("<link>",$link, JText::_('EVENTS_TERMS_CONDITIONS_TEXT'));
			?>	
			<tr>
				<td colspan="3">
					<input type="checkbox" name="agrement" onClick="check(this, document.regproDetails.submit)" />
					<?php echo $hreftag; ?> 
				</td>
			</tr>
			<?php
				}else{
					$link =  '<font color="#FF0000" style="cursor:hand"><b>'.JText::_('EVENTS_TERMS_CONDITIONS_LINK_TITLE').'</b></font>';	
					$hreftag = str_replace("<link>",$link, JText::_('EVENTS_TERMS_CONDITIONS_TEXT'));*/
			?>
			<!--<tr>								
				<td colspan="3">
					<input type="checkbox" name="agrement" onClick="check(this, document.regproDetails.submit)" />&nbsp; <?php // echo $hreftag; ?>
				</td>								
			</tr>	-->		
			<?php
				//}		
			?>	
	
			<tr>
				<td colspan="3" height="4px"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></td>							
			</tr>
	
			<tr>
				<td align="left" colspan="2">&nbsp;<input type="submit" name="submit" class="button btn btn-primary" value="<?php echo JText::_('EVENTS_REGISTRA_BUTTON'); ?>"></td>									
				<td>&nbsp;</td>
			</tr>				
			</table>
	<?php
			}
		}
		
		$session = JFactory::getSession();		
		$title_orignal = $session->set('titles',$title_orignal);
	?>
		</form>		
<?php					
}

// notification box for early/group discount box
function discount_message($cart, $regproConfig)
{
	foreach($cart['ticktes'][0]->event_discount_id as $tkey => $tvalue)
	{
		foreach($cart['event_discounts'] as $dkey => $dvalue)
		{
			if($tvalue == $dvalue->id && $dvalue->discount_name == "G"){				
?>
				<div class="regpro_cart_event_discount"> 
					<?php echo JText::_('EVENTS_GROUP_DISCOUNT_MESSAGE')." "; ?>
					<?php 
						if($dvalue->discount_type == 'A'){
							echo $regproConfig['currency_sign'].$dvalue->discount_amount;
						}else{
							echo $dvalue->discount_amount." %";
						}
					?>				
				</div>
<?php
			}
			
			if($tvalue == $dvalue->id && $dvalue->discount_name == "E"){						
?>
				<div class="regpro_cart_event_discount"> <?php echo JText::_('EVENTS_EARLY_DISCOUNT_MESSAGE')." "; ?>
					<?php 
						if($dvalue->discount_type == 'A'){
							echo $regproConfig['currency_sign'].$dvalue->discount_amount;
						}else{
						  	echo $dvalue->discount_amount." %";
						}
					?>				
				</div>
<?php
			}	
		}
	}
}
?>
</div>