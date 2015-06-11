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

$registrationproHelper = new registrationproHelper;
JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.modal' );

//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_EVENTS_ADD_USER' ), 'newuser' );
JToolBarHelper::divider();
JToolBarHelper::back();
JToolBarHelper::divider();
//JToolBarHelper::help( 'screen.registrationpro', true );

//regpro_header_footer::regpro_header($this->regproConfig);

//Details of events start
if($this->row->message != "") echo '<div class="regpro_error">'.$this->row->message.'</div>'; // error message if forms value are blank

if($_REQUEST['detailshow'] != 'No'){ ?><div class="componentheading"><?php echo ucwords($this->row->titel);?> - <?php echo JText::_('EVENTS_DETAILS');?></div><?php }

?>

<script language="javascript">
	function checkchkbox() {
		var i,j=0,k=0;
		var count = document.regproDetails.elements.length;
		for(i=0;i<count;i++) {
			var element = document.regproDetails.elements[i];
			if(element.type == "checkbox" && element.id == "chkIDs") {
				j = eval(j+1);
				if(element.checked == false) { k = eval(k+1); }
			}
		}

		if(j == k) {
			alert("<?php echo JText::_('PLEASE_SELECT_REGISTRATION_OPTION');?>");
			if(document.getElementById("chkIDs")) document.getElementById("chkIDs").focus();
			return false;
		}
	}
</script>
<div class="span10">
<form name="regproDetails" id="regproDetails"  action="<?php echo $this->action; ?>" method="post">

<div class="regpro_outline" id="regpro_outline" style="padding: 10px;">
	<table width="100%" border="0" style="line-height: 3;">

  	<?php
	if($_REQUEST['detailshow'] != 'No'){

		if($this->regproConfig['showtitle'] == 1)    fn_EventDetailsTitle($this->row, $this->regproConfig);
		if($this->regproConfig['showtime'] == 1)     fn_EventDetailsWhen($this->row, $this->regproConfig);
		if($this->regproConfig['showcategory'] == 1) fn_EventDetailsCategory($this->row, $this->regproConfig);
		if($this->regproConfig['showlocation'] ==1 ) fn_EventDetailsWhere($this->row, $this->regproConfig);
		if($this->regproConfig['showtime'] == 1)     fn_EventdateDetails($this->row, $this->regproConfig);

		fn_EventDetailsMap($this->row, $this->regproConfig);
		fn_Separator();

		if($this->regproConfig['showevdesc'] == 1)   fn_EventDetailsDescription($this->row, $this->regproConfig);

	} else if($this->regproConfig['showtitle'] == 1) fn_EventDetailsName($this->row, $this->regproConfig);

	// event discount details
	if(is_array($this->row->event_discounts) && count($this->row->event_discounts) > 0){
		fn_tickets_discounts_details($this->row, $this->regproConfig);
	}

	// event tickets listing
	if($this->row->message == ''){
		if($this->tickets && $this->row->registra){
			fn_tickets_listing($this->row, $this->regproConfig, $this->tickets);
		}
	}
  	?>
		<tr><td colspan="2">&nbsp;</td></tr>
	</table>
</div>
<?php echo JHTML::_( 'form.token' );?>
</form>

<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH;?>/blank.png" border="0" /></div>

<?php
// Show event title
function fn_EventDetailsTitle($row, $regproConfig){
	if($regproConfig['showtitle'] == 1) {
	?>
		<tr><td colspan="3"><b><?php echo ucfirst($row->titel);?> <?php echo JText::_('EVENTS_DETAILS_TEXT');?></b></td></tr>
	<?php
	}
}

// Show event name
function fn_EventDetailsName($row, $regproConfig){
	if($regproConfig['showtitle'] == 1) {
	?>
		<tr><td colspan="3"><?php echo JText::_('EVENTS_NAME');?>&nbsp; <b><?php echo $row->titel; ?></b></td></tr>
	<?php
	}
}

// Show event start and end date
function fn_EventDetailsWhen($row, $regproConfig){
	if($regproConfig['showtime']){
	?>
		<tr>
			<td width="35%" valign="top"><?php echo JText::_('EVENTS_WHEN1');?></td>

			<td colspan="2">
			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
				$registrationproHelper = new registrationproHelper;

				$date = "0000-00-00";
				if($row->regstart != "0000-00-00") $date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->regstart);
				echo $date;

				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');

				// calculate the registration end date accroding to event start/end date
				$reg_enddate = date('Y-m-d',strtotime('-'.$row->regstop.'day', strtotime($row->enddates)));
				if($row->regstop_type == 0) $reg_enddate = date('Y-m-d',strtotime('-'.$row->regstop.'day', strtotime($row->dates)));

				$date = "0000-00-00";
				if($reg_enddate != "0000-00-00") $date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $reg_enddate);
				echo $date;
			?>
			</td>
		</tr>
		<?php
	}
}

// Show event location name
function fn_EventDetailsWhere($row, $regproConfig){
		?>
	<tr>
		<td><?php echo JText::_('EVENTS_WHERE');?></td>
		<td colspan="2">
		<?php
			if (( $regproConfig['showurl'] == 1) && (!empty($row->url))) {
				if(strtolower(substr($row->url, 0, 7)) == "http://") {
			?>
					<a href="<?php echo $row->url; ?>" target="_blank"> <?php echo $row->club; ?></a>
			<?php
				} else {
			?>
					<a href="http://<?php echo $row->url; ?>" target="_blank"> <?php echo $row->club; ?></a>
			<?php
				}
			} else echo $row->club;
			?> - <?php echo $row->city;  ?>
		</td>
	</tr>
	<?php
}

// Show event category name
function fn_EventDetailsCategory($row, $regproConfig){
?>
	<tr>
		<td> <?php echo JText::_('ADMIN_EVENTS_CAT_ID');?> </td>
		<td colspan="2"><?php echo $row->catname;?></td>
	</tr>
<?php
}

function fn_EventDateDetails($row, $regproConfig){
	if($regproConfig['showtime']){
		$detaillink = JRoute::_( 'index.php?option=com_registrationpro' );
	?>
		<tr>
			<td><?php echo JText::_('EVENTS_START_END_DATE');?></td>
			<td colspan="2">
				<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
			 $registrationproHelper = new registrationproHelper;
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates); //date( $regproConfig['formatdate'], strtotime( $row->dates ));
				echo $date;
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->enddates); //date( $regproConfig['formatdate'], strtotime( $row->enddates ));
				echo $date."  ";
				$endtime = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->endtimes); //date( $regproConfig['formattime'], strtotime( $row->endtimes ));
			?>
			</td>
		</tr>

		<tr>
			<td><?php echo JText::_('EVENTS_START_END_TIME');?></td>
			<td colspan="2">

			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php $registrationproHelper = new registrationproHelper;
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates); //date( $regproConfig['formatdate'], strtotime( $row->dates ));
				$time = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->times); //date( $regproConfig['formattime'], strtotime( $row->times ));
				echo " $time";
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->enddates); //date( $regproConfig['formatdate'], strtotime( $row->enddates ));
				$endtime = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->endtimes); //date( $regproConfig['formattime'], strtotime( $row->endtimes ));
				echo " $endtime";
			?>
			</td>
		</tr>
		<?php
	}
}

// Show event map location
function fn_EventDetailsMap($row, $regproConfig){

	if ($regproConfig['showmapserv']) {
?>
	<tr>
	  <td colspan="3">
			<?php
			//Link to map
			switch ($regproConfig['showmapserv']) {
				case 0:
				break;
				case 1:
			?>
					<a href="http://maps.google.com/maps?q=<?php echo $row->street; ?>+<?php echo $row->city ?>+<?php echo $row->plz ?>" title="<?php echo JText::_('EVENTS_MAP')." "; ?>" target="_blank"><?php echo JText::_('EVENTS_MAP')." "; ?></a>

			<?php
				break;
			}//switch end
			?>
	  </td>
	</tr>
	<?php
	}
}

// using as a separator
function fn_Separator(){
?>
	<tr> <td colspan="3">&nbsp;</td> </tr>
<?php
}

// Show event description
function fn_EventDetailsDescription($row, $regproConfig){

	if ($regproConfig['showevdesc'] == 1) {
	?>
		<tr>
			<td colspan="3"><b style="font-size:14px;"><?php echo JText::_('EVENTS_DESC');?></b></td>
		</tr>

		<tr>
			<td colspan="3">
				<?php
				if (($row->datdescription == "") || ($row->datdescription == "<br />")) {
					echo JText::_('EVENTS_DESCALERT');
				} else {
					$row->text = $row->datdescription;
					$row->title = $row->titel;
					echo $row->text;
				}
				?>
			</td>
		</tr>
	<?php
	}
}

// Show event provided url
function fn_EventDetailsUrl($row, $regproConfig){

}

// Show event detail image
function fn_EventDetailsImage($row, $regproConfig){
	global $mosConfig_live_site;

	if (!empty($row->datimage)) {

		$original = $mosConfig_live_site."/images/registrationpro/events/".$row->datimage;

		$thumb = $mosConfig_live_site."/images/registrationpro/events/small/".$row->datimage;

		if (file_exists(ELPATH."/../../images/registrationpro/events/small/".$row->datimage)) {
			$iminfo = @getimagesize("images/registrationpro/events/".$row->datimage);
			$widthev = $iminfo[0];
			$heightev = $iminfo[1];
		?>
			<a href="javascript:void window.open('<?php echo $original; ?>','Popup','width=<?php echo $widthev; ?>,height=<?php echo $heightev; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">

			<img src="<?php echo $thumb; ?>" align="right"></a>
		<?php
		} else { ?>
			<img src="<?php echo $original; ?>" align="right">
		<?php
		}
	} else {
		// nothing
	}
}

// Show event location details (street, city, state etc.)
function fn_EventDetailsLocation($row, $regproConfig){
	global $mosConfig_live_site, $Itemid;

	if ($regproConfig['showlocation']){
	?>
		<tr>
			<td  colspan="3"><b style="font-size:14px;"><?php echo JText::_('EVENTS_LOCAT');?></b></td>
		</tr>

		<tr>
			<td width="20%" colspan="1">Venue:</td>
			<td width="80%" colspan="2">
			<?php
				if (!empty($row->locimage)) {

					$originalloc = $mosConfig_live_site."/images/registrationpro/location/".$row->locimage;
					$thumbloc = $mosConfig_live_site."/images/registrationpro/location/small/".$row->locimage;

					if (file_exists(ELPATH."/../../images/registrationpro/location/small/".$row->locimage)) {
						$iminfoloc = @getimagesize("images/registrationpro/location/".$row->locimage);
						$widthloc = $iminfoloc[0];
						$heightloc = $iminfoloc[1];
						?>

						<a href="javascript:void window.open('<?php echo $originalloc; ?>','Popup','width=<?php echo $widthloc; ?>,height=<?php echo $heightloc; ?>,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,resizable=no');">

						<img src="<?php echo $thumbloc; ?>"></a>
						<?php
					} else { ?>
						<img src="<?php echo $originalloc; ?>" width="<?php echo $imagewidth; ?>" height="<?php echo $imagehight; ?>">
					<?php
					}
				} else {
					echo "&nbsp;";
				}
				?>

			<?php echo "<a href='".JRoute::_("index.php?option=com_registrationpro&Itemid=$Itemid&func=shlocevents&locatid=$row->locid")."'>".$row->club."</a>"; ?></td>

		</tr>

		<?php

		if ($regproConfig['showdetails'] == 1) {
		?>
		<tr>
			<td><?php echo JText::_('ADMIN_EVENTS_CLUBSTREET_LO');?></td>
			<td><?php echo $row->street; ?></td>
		</tr>

		<tr>
			<td><?php echo JText::_('ADMIN_EVENTS_CLUBPLZ_LO');?></td>
			<td><?php echo $row->plz; ?></td>
		</tr>

		<tr>
			<td width="20%"><?php echo JText::_('EVENTS_CITY');?></td>
			<td colspan="2"><?php echo $row->city; ?></td>
		</tr>

		<tr>
			<td><?php echo JText::_('EVENTS_COUNTRY_LO');?></td>
			<td><?php echo $row->country; ?></td>
			<td>
				<?php
				//Link zu map
				switch ($regproConfig['showmapserv']) {
					case 0:
					break;
					case 1:
						?>
						<a href="http://maps.google.com/maps?q=<?php echo $row->street; ?>+<?php echo $row->city ?>+<?php echo $row->plz ?>" title="<?php echo _EVENTS_MAP." "; ?>" target="_blank"><?php echo _EVENTS_MAP." "; ?></a>

				<?php
					break;
				}//switch ende
				?>
			</td>
		</tr>
		<?php
		}//showdetails ende

		if ($regproConfig['showlocation'] == 1) {
		?>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>

		<tr>
			<td  colspan="3"><b style="font-size:14px"><?php echo JText::_('EVENTS_LOCDESC');?></b></td>
		</tr>

		<tr>
			<td colspan="3">
				<?php
				if (empty ($row->locdescription)) {
					echo JText::_('EVENTS_DESCALERT');
				} else {
					//execute the Mambot
					$row->text = $row->locdescription;
					$row->title = $row->club;
					global $_MAMBOTS;
					$_MAMBOTS->loadBotGroup( 'content' );
					$results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$row, &$params, 0 ), true );
					echo $row->text;
				}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>

		<?php
		}
		//end showlocdescription
	}
}


// listing event tickets
function fn_tickets_listing($row, $regproConfig, $tickets)
{
	global $Itemid;

	$my =JFactory::getUser();

	$arr_qty = range(1, $regproConfig['quantitylimit']);

	if(!empty($tickets)){
		//echo "<pre>"; print_r($tickets); exit;

		// check cart session if ticket id is allready exist in the array for javascript validation
			$session =JFactory::getSession();
			$cart 	 = $session->get('cart');

			$event_tickets_avaliable = 1; // flag to display the continue button
			$script_flag = 0;
			if($cart['ticktes'] && is_array($cart['ticktes'])){

				foreach($cart['ticktes'] as $tkey => $tvalue)
				{
					foreach($tickets as $ttkey => $ttvalue)
					{
						if($tvalue->type == 'E'){
							if($tvalue->id == $ttvalue->id){
								$script_flag = 1;
							}
						}
					}
				}
			}
		// end
		?>

		<tr>
			<td colspan="3"><strong><?php echo JText::_('EVENTS_REGISTRA_CHOOSE_PRODUCTS');?></strong></td>
		</tr>

		<tr>
			<td colspan="3" class="regpro_outline" id="regpro_outline">
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_TICKETS_HEAD_SNO');?> </td>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_TICKETS_HEAD_QTY');?> </td>
						<td class="regpro_sectiontableheader" width="60%"> <?php echo  JText::_('EVENT_TICKETS_HEAD_TICKET_NAME');?> </td>
						<td class="regpro_sectiontableheader" width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_TICKETS_HEAD_TICKET_PRICE');?></td>
					</tr>
				<?php
					$flag_add = 0;
					$tkt_qty = 0;
					$ticket_avaliable_flag = 0;
					$event_ticket_total_records = 0;
					foreach ($tickets as $product){
						if($product->type == "E"){

							$event_ticket_total_records++; // count total event tickets record

							$ticket_avaliable_qty = 0;
							if($product->product_quantity > 0){
								// check if ticket quantity is avaliable or not
								$ticket_avaliable_qty = $product->product_quantity - $product->product_quantity_sold;

								$tkt_qty = range(1, $ticket_avaliable_qty);
							}else{
								$ticket_avaliable_qty = 1;
								$tkt_qty = $arr_qty;
							}

							if($ticket_avaliable_qty > 0){
				?>
						<tr>
							<td style="text-align:center; vertical-align:top">
								<input type="checkbox" id="chkIDs" name="product_id[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
							</td>
							<td style="text-align:center; vertical-align:top">
								<select name="product_qty[<?php echo $product->id; ?>]" id="product_qty" style="width:50px;">
									<?php
										foreach($tkt_qty as $qkey => $qvalue)
										{
									 ?>
											<option value="<?php echo $qvalue;?>"><?php echo $qvalue;?></option>
									 <?php
										}
									 ?>
								</select>
							</td>
							<td style="vertical-align:top">
								<?php echo $product->product_name;?>
								<?php
									 if($product->product_description)
										echo "<br />( ".$product->product_description." )";
								?>
							</td>
							<td style="text-align:right; vertical-align:top">
								<?php
									if($product->total_price==0)
									 echo  JText::_('EVENTS_REGISTRA_FREE');
									else
									 echo $regproConfig['currency_sign'],'&nbsp;',number_format($product->total_price,2);
								?>
							</td>
						</tr>
						<input type="hidden" name="productids[]" id="productids[]" value="<?php echo $product->id;?>" />
				<?php
							}else{
								$ticket_avaliable_flag++ ;
							}
						}else{
							$flag_add = 1;
						}
					}

					// if all tickets quantity is full, then display this message
					if($ticket_avaliable_flag ==  $event_ticket_total_records){
						$event_tickets_avaliable = 0;
				?>
						<tr><td colspan='4' style="text-align:center"><?php echo JText::_('EVENTS_REGISTRA_NO_TICKET_AVILIABLE');?></td></tr>
				<?php
					}
				?>
					</table>
			</td>
		</tr>

	<?php
		if($flag_add == 1){
	?>
		<tr>
			<td colspan="3"><strong><?php echo  JText::_('EVENTS_REGISTRA_CHOOSE_ADD_PRODUCTS');?></strong></td>
		</tr>
		<tr>
			<td colspan="3"  class="regpro_outline" id="regpro_outline">
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_SNO');?> </td>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_QTY');?> </td>
						<td class="regpro_sectiontableheader" width="60%"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_NAME');?> </td>
						<td class="regpro_sectiontableheader" width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_PRICE');?></td>
					</tr>
				<?php
					$tkt_qty = 0;
					$ticket_avaliable_flag = 0;
					$additional_ticket_total_records = 0;
					foreach ($tickets as $product){
						if($product->type == "A"){

							$additional_ticket_total_records++; // count total additional tickets record

							$ticket_avaliable_qty = 0;
							if($product->product_quantity > 0){
								// check if ticket quantity is avaliable or not
								$ticket_avaliable_qty = $product->product_quantity - $product->product_quantity_sold;

								$tkt_qty = range(1, $ticket_avaliable_qty);
							}else{
								$ticket_avaliable_qty = 1;
								$tkt_qty = $arr_qty;
							}

							if($ticket_avaliable_qty > 0){
	?>
					<tr>
						<td style="text-align:center; vertical-align:top">
							<input type="checkbox" id="chkIDs_add" name="product_id_add[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
						</td>
						<td style="text-align:center; vertical-align:top">
							<select name="product_qty_add[<?php echo $product->id; ?>]" style="width:50px;">
								<?php
									foreach($tkt_qty as $qkey => $qvalue)
									{
								 ?>
										<option value="<?php echo $qvalue;?>"><?php echo $qvalue;?></option>
								 <?php
									}
								 ?>
							</select>
						</td>
						<td style="vertical-align:top">
							<?php
								echo $product->product_name;

								if($product->product_description)
									echo "<br />( ".$product->product_description." )";
							?>
						</td>
						<td style="text-align:right; vertical-align:top">
							<?php
								if($product->total_price==0)
								 echo  JText::_('EVENTS_REGISTRA_FREE');
								else
								 echo $regproConfig['currency_sign'],'&nbsp;',number_format($product->total_price,2);
							?>
						</td>
					</tr>
						<input type="hidden" name="productids[]" id="productids[]" value="<?php echo $product->id;?>" />
				<?php
							}else{
								$ticket_avaliable_flag++ ;
							}
						}
					}
					// if all tickets quantity is full, then display this message
					if($ticket_avaliable_flag ==  $additional_ticket_total_records){
				?>
						<tr><td colspan='4' style="text-align:center"><?php echo JText::_('EVENTS_REGISTRA_NO_TICKET_AVILIABLE');?></td></tr>
				<?php
					}
				?>
				</table>
			</td>
		</tr>
	<?php } ?>
		<tr>
			<td colspan="3">
				<?php
						if(isset($row->form)){
							if(!empty($row->form)){
								foreach ($row->form as $form_field=>$form_field_value){
									echo '<input type="hidden" name="form['.$form_field.']" value="'.$form_field_value.'" />';
								}
							}
						}
					?>
			</td>
		</tr>

		<!-- Check event access and hide the register button -->
		<?php

		if($row->eventaccess > 0 && empty($my->id)){
			echo '<tr>
					<td colspan="3" class="regpro_error">';
			echo JText::_('EVENTS_REGISTRA_LOGIN');
			echo	'</td>
				  </tr>';

		}else{

		// Group registration option
		if($row->allowgroup == 1){ ?>
		<tr>
			<td colspan="3" class="regpro_outline" id="regpro_outline">
				<input type="checkbox" name="chkgroupregistration" id="chkgroupregistration" value="1" style="vertical-align:middle"/> <?php echo JText::_('REG_TYPE');?>
			</td>
		</tr>
		<?php } ?>
		<!-- End -->

		<?php if($event_tickets_avaliable > 0){ ?>
		<tr>
			<td colspan="3">
				<input type="hidden" NAME="option" value="com_registrationpro" />
				<input type="hidden" name="step" value="1" />
				<input type="hidden" name="detailshow" value="No" />
				<input type="hidden" name="did" value="<?php echo $row->did;?>" />
				<input type="hidden" NAME="Itemid" value="<?php echo $Itemid; ?>" />
				<input type="hidden" NAME="rdid" value="<?php echo $row->did; ?>" />
				<input type="hidden" NAME="func" value="details" />
				<input type="hidden" name="availablesheet" id="availablesheet" value="<?php echo $row->avaliable; ?>">
				<!--<input type="submit" class="regpro_button" name="submit" <?php echo $onclick; ?> value="<?php echo  JText::_('EVENTS_DETAIL_PAGE_BUTTON');?>" />-->
				<br/>
				<input type="submit" class="regpro_button btn btn-primary" name="submit" onclick="return checkchkbox(<?php echo $script_flag; ?>);" value="<?php echo  JText::_('EVENTS_DETAIL_PAGE_BUTTON');?>" />

			</td>
		</tr>
<?php
			}
		}
	}
}

// event discount details
function fn_tickets_discounts_details($row, $regproConfig)
{
	$registrationproHelper = new registrationproHelper;
	$current_date = $registrationproHelper->getCurrent_date("Y-m-d");

	// group registration discount details
	foreach($row->event_discounts as $key=>$value){

		if($value->discount_name == "G"){

			if($gcnt > 1){ }else{ $gcnt = 1;}

			if($gcnt == 1){
?>
		<tr>
			<td colspan="3"> <img src="<?php echo REGPRO_IMG_PATH; ?>/offer.png" border="0" align="absmiddle" /><strong><?php echo JText::_('EVENTS_GROUP_DISCOUNT');?></strong></td>
		</tr>

		<tr>
			<td colspan="3"  class="regpro_outline" id="regpro_outline">
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td class="regpro_sectiontableheader" width="5%" style="text-align:center"> # </td>
						<td class="regpro_sectiontableheader" width="65%" style="text-align:center"> <?php echo  JText::_('EVENTS_MIN_NUMBER_TICKETS');?> </td>
						<td class="regpro_sectiontableheader" align="right" width="30%"> <?php echo  JText::_('EVENTS_GROUP_DISCOUNT_AMOUNT_PER_TICKET');?> </td>
					</tr>
	<?php	} ?>

					<tr>
						<td class="regpro_event_discount" width="5%" style="text-align:center"> <?php echo $gcnt; ?> </td>
						<td class="regpro_event_discount" width="65%" style="text-align:center"> <?php echo $value->min_tickets;?> </td>
						<td class="regpro_event_discount" align="right" width="30%">
							<?php
								//echo number_format($value->discount_amount,2)." %";
								if($value->discount_type == 'P')
									echo number_format($value->discount_amount,2)." %";

								if($value->discount_type == 'A')
								  	echo $regproConfig['currency_sign']."&nbsp;".number_format($value->discount_amount,2);
							?>
						</td>
					</tr>
	<?php
			$gcnt = $gcnt+1;
		}
	}

	if($gcnt >= 1){
	?>
				</table>
			</td>
		</tr>

	<?php
	}

	// early registration discount details
	foreach($row->event_discounts as $key=>$value){

		//if($value->discount_name == "E" && $value->early_discount_date >= date('Y-m-d')){
		if($value->discount_name == "E" && $value->early_discount_date >= $current_date){

			if($ecnt > 1){ }else{ $ecnt = 1;}

			if($ecnt == 1){
	?>
		<tr>
			<td colspan="3"><img src="<?php echo REGPRO_IMG_PATH; ?>/offer.png" border="0" align="absmiddle" /> <strong><?php echo JText::_('EVENTS_EARLY_REGISTRATION_DISCOUNT');?></strong></td>
		</tr>

		<tr>
			<td colspan="3" class="regpro_outline" id="regpro_outline">
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td class="regpro_sectiontableheader" width="5%" style="text-align:center"> # </td>
						<td class="regpro_sectiontableheader" width="65%" style="text-align:center"> <?php echo  JText::_('EVENTS_EARLY_DISCOUNT_DATE');?> </td>
						<td class="regpro_sectiontableheader" align="right" width="30%"> <?php echo  JText::_('EVENTS_EARLY_DISCOUNT_AMOUNT_PER_TICKET');?> </td>
					</tr>

	<?php	} ?>

					<tr>
						<td class="regpro_event_discount" width="5%" style="text-align:center"> <?php echo $ecnt; ?> </td>
						<td class="regpro_event_discount" width="65%" style="text-align:center">
							<?php //echo $value->early_discount_date;
							$registrationproHelper = new registrationproHelper;
								echo $registrationproHelper->getFormatdate($regproConfig['formatdate'], $value->early_discount_date);
							?>
						</td>
						<td class="regpro_event_discount" width="30%" align="right">
							<?php
								//echo number_format($value->discount_amount,2)." %";
								 if($value->discount_type == 'P')
									echo number_format($value->discount_amount,2)." %";

								  if($value->discount_type == 'A')
								  	echo $regproConfig['currency_sign']."&nbsp;".number_format($value->discount_amount,2);
							?>
						</td>
					</tr>
<?php
			$ecnt = $ecnt+1;
		}
	}

	if($ecnt >= 1){
		?>
				</table>
			</td>
		</tr>
<?php
	}
}
?>
</div>