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

/*
// no direct access
defined('_JEXEC') or die('Restricted access');

regpro_header_footer::regpro_header($this->regproConfig);

//Details of events start
if($this->row->message != ""){
	echo '<div class="regpro_error">'.$this->row->message.'</div>'; // error message if forms value are blank
}

if($_REQUEST['detailshow'] != 'No'){
?>
<table width="100%" border="0">
	<tr>
		<td>
			<span class='componentheading'><?php echo ucwords($this->row->titel); ?> - <?php echo JText::_('EVENTS_DETAILS');?></span>
		</td>
	</tr>
</table>
<?php
}

//echo "<pre>"; print_r($this->row);

?>

<script language="javascript">
	function checkchkbox()
	{
		var i,j=0,k=0;
		var count = document.regproDetails.elements.length;
		for(i=0;i<count;i++)
		{
			var element = document.regproDetails.elements[i];
			if(element.type == "checkbox" && element.id == "chkIDs"){
				j = eval(j+1);
				if(element.checked == false){
					k = eval(k+1);
				}
			}
		}

		if(j == k){
			alert("Please select registration options.");
			if(document.getElementById("chkIDs"))
				document.getElementById("chkIDs").focus();
			return false;
		}
	}
</script>

<form name="regproDetails" id="regproDetails"  action="<?php echo $this->action; ?>" method="post" onSubmit="return validateForm(this,false,false,false,false);">

<div class="regpro_outline" id="regpro_outline">
	<table width="100%" border="0">

  	<?php
	if($_REQUEST['detailshow'] != 'No'){

		if($this->regproConfig['showtitle']==1){
			fn_EventDetailsTitle($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showtime']==1){
			fn_EventDetailsWhen($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showcategory']==1){
		 	fn_EventDetailsCategory($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showlocation']==1){
			fn_EventDetailsWhere($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showtime']==1){
			fn_EventdateDetails($this->row, $this->regproConfig);
		}

		fn_EventDetailsMap($this->row, $this->regproConfig);

		fn_Separator();

		if($this->regproConfig['showevdesc']==1){
			fn_EventDetailsDescription($this->row, $this->regproConfig);
		}

	}else{
		if($this->regproConfig['showtitle']==1){
			fn_EventDetailsName($this->row, $this->regproConfig);
		}
	}

	// event tickets listing
	if($this->tickets && $this->row->registra){
		fn_tickets_listing($this->row, $this->regproConfig, $this->tickets);
	}

  	?>
	</table>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
</form>

<div style="height:4px;"><img src="components/com_registrationpro/images/blank.png" border="0" /></div>

<?php
regpro_header_footer::regpro_footer($this->regproConfig);

// Show event title
function fn_EventDetailsTitle($row, $regproConfig){
	if($regproConfig['showtitle'] == 1) {
	?>
		<tr> <td colspan="3"> <br /><b><?php echo ucfirst($row->titel); ?> <?php echo JText::_('EVENTS_DETAILS_TEXT');?> </b> </td> </tr>
	<?php
	}
}

// Show event name
function fn_EventDetailsName($row, $regproConfig){
	if($regproConfig['showtitle'] == 1) {
	?>
		<tr> <td colspan="3"><?php echo JText::_('EVENTS_NAME');?>&nbsp; <b><?php echo $row->titel; ?></b> </td> </tr>
	<?php
	}
}

// Show event start and end date
function fn_EventDetailsWhen($row, $regproConfig){
	if($regproConfig['showtime']){
	?>
		<tr>
			<td width="35%" valign="top"><?php echo JText::_('EVENTS_WHEN1')." " ;?></td>

			<td colspan="2">
			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php

				if($row->regstart != "0000-00-00"){
					$date = date($regproConfig['formatdate'], strtotime($row->regstart));
				}else{
					$date = "0000-00-00";
				}

				echo $date;

				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');

				if($row->regstop != "0000-00-00"){
					$date = date($regproConfig['formatdate'], strtotime($row->regstop));
				} else {
					$date = "0000-00-00";
				}

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
		<td><?php echo JText::_('EVENTS_WHERE')." " ;?></td>
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
			} else {
				echo $row->club;
			}
			?> - <?php echo $row->city;  ?>
		</td>
	</tr>
	<?php
}

// Show event category name
function fn_EventDetailsCategory($row, $regproConfig){
?>
	<tr>
		<td> <?php echo JText::_('ADMIN_EVENTS_CAT_ID')." " ;?> </td>
		<td colspan="2"> <?php echo $row->catname;?> </td>
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
				$date = date( $regproConfig['formatdate'], strtotime( $row->dates ));
				echo $date;
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = date( $regproConfig['formatdate'], strtotime( $row->enddates ));
				echo $date."  ";
				$endtime = date( $regproConfig['formattime'], strtotime( $row->endtimes ));
			?>
			</td>
		</tr>

		<tr>
			<td><?php echo JText::_('EVENTS_START_END_TIME');?></td>
			<td colspan="2">

			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
				$date = date( $regproConfig['formatdate'], strtotime( $row->dates ));
				$time = date( $regproConfig['formattime'], strtotime( $row->times ));
				echo " $time";
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = date( $regproConfig['formatdate'], strtotime( $row->enddates ));
				$endtime = date( $regproConfig['formattime'], strtotime( $row->endtimes ));
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
			<td colspan="3"><b style="font-size:14px;"><?php echo JText::_('EVENTS_DESC'); ?></b></td>
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
			<td  colspan="3"><b style="font-size:14px;"><?php echo JText::_('EVENTS_LOCAT'); ?></b></td>
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
			<td><?php echo JText::_('ADMIN_EVENTS_CLUBSTREET_LO'); ?></td>
			<td><?php echo $row->street; ?></td>
		</tr>

		<tr>
			<td><?php echo JText::_('ADMIN_EVENTS_CLUBPLZ_LO'); ?></td>
			<td><?php echo $row->plz; ?></td>
		</tr>

		<tr>
			<td width="20%"><?php echo JText::_('EVENTS_CITY');?></td>
			<td colspan="2"><?php echo $row->city; ?></td>
		</tr>

		<tr>
			<td><?php echo JText::_('EVENTS_COUNTRY_LO'); ?></td>
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

	$arr_qty = range(1, $regproConfig['quantitylimit']);

	if(!empty($tickets)){
		?>
		<tr>
			<td colspan="3"><strong><?php echo JText::_('EVENTS_REGISTRA_CHOOSE_PRODUCTS');?></strong></td>
		</tr>

		<tr>
			<td colspan="3" class="regpro_outline" id="regpro_outline">
				<table border="0" cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_TICKETS_HEAD_SNO'); ?> </td>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_TICKETS_HEAD_QTY');?> </td>
						<td class="regpro_sectiontableheader" width="60%"> <?php echo  JText::_('EVENT_TICKETS_HEAD_TICKET_NAME');?> </td>
						<td class="regpro_sectiontableheader" width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_TICKETS_HEAD_TICKET_PRICE');?></td>
					</tr>
				<?php
					$flag_add = 0;
					foreach ($tickets as $product){
						if($product->type == "E"){
				?>
						<tr>
							<td style="text-align:center; vertical-align:top">
								<input type="checkbox" id="chkIDs" name="product_id[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
							</td>
							<td style="text-align:center; vertical-align:top">
								<select name="product_qty[<?php echo $product->id; ?>]">
									<?php
										foreach($arr_qty as $qkey => $qvalue)
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
							$flag_add = 1;
						}
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
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_SNO'); ?> </td>
						<td class="regpro_sectiontableheader" width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_QTY');?> </td>
						<td class="regpro_sectiontableheader" width="60%"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_NAME');?> </td>
						<td class="regpro_sectiontableheader" width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_PRICE');?></td>
					</tr>
				<?php

					foreach ($tickets as $product){
						if($product->type == "A"){
	?>
					<tr>
						<td style="text-align:center; vertical-align:top">
							<input type="checkbox" id="chkIDs_add" name="product_id_add[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
						</td>
						<td style="text-align:center; vertical-align:top">
							<select name="product_qty_add[<?php echo $product->id; ?>]">
								<?php
									foreach($arr_qty as $qkey => $qvalue)
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
						}
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

		<!-- Group registration option  -->
		<?php if($row->allowgroup == 1){ ?>
		<tr>
			<td colspan="3" class="regpro_outline" id="regpro_outline">
				<input type="checkbox" name="chkgroupregistration" id="chkgroupregistration" value="1" style="vertical-align:middle"/> <?php echo JText::_('REG_TYPE');?>
			</td>
		</tr>
		<?php } ?>
		<!-- End -->

		<tr>
			<td colspan="3">
				<input type="hidden" NAME="option" value="com_registrationpro" />
				<input type="hidden" name="step" value="1" />
				<input type="hidden" name="detailshow" value="No" />
				<input type="hidden" name="did" value="<?php echo $row->did;?>" />
				<input type="hidden" NAME="Itemid" value="<?php echo $Itemid; ?>" />
				<input type="hidden" NAME="rdid" value="<?php echo $row->did; ?>" />
				<input type="hidden" NAME="func" value="details" />
				<input type="submit" class="button" name="submit" onclick="return checkchkbox();" value="<?php echo  JText::_('EVENTS_REGISTRA_BUTTON');?>" />
			</td>
		</tr>
<?php
	}
}
*/
?>