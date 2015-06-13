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

?>
<style>
.share-btn a {
    margin: 2px;
}
td .a_social  {
    margin: 2px;
}
</style>

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

<?php
	/* Code added on 26-06-2014 By Sushil Support */
	$status = array(
		'1'		=> JText::_('COM_REGISTRATIONPRO_EVENT_STATUS_CLOSED'),
		'2'		=> JText::_('COM_REGISTRATIONPRO_EVENT_STATUS_CANCLED'),
		'3'		=> JText::_('COM_REGISTRATIONPRO_EVENT_STATUS_RESCHEDULE'),
		'4'		=> JText::_('COM_REGISTRATIONPRO_EVENT_STATUS_FULL')
	);
	
	/**********************************************/
?>
<div id="regpro">
<?php
$registrationproHelper = new registrationproHelper;
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regproConfig);

// check if user have access to view the event details
if($this->event_view_access) {

//Details of events start
if($this->row->message != "" && $this->row->registra){
	echo '<div class="alert alert-error">'.$this->row->message.'</div>'; // error message if forms value are blank
}

//if($_REQUEST['detailshow'] != 'No'){
if($this->regproConfig['showtitle'] == 1){
?>
<div class="span12">
	<div class='span8 regpro_detailsheading'>
		<?php echo ucwords($this->row->titel); ?> - <?php echo JText::_('EVENTS_DETAILS');?>
	</div>
</div>
<?php
}else {
?>
<img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" height="3px;" />
<?php
}

$this->row->datdescription = $this->row->text;
?>

<script language="javascript">
	function checkchkbox(script)
	{
		var total_products_qty = 0;

		if(script == 0){
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

					// This condition for calcualte the total selected tickets for maxattendance validation
					if(element.id == "chkIDs" && element.checked == true){
						total_products_qty = parseInt(total_products_qty) + parseInt(document.regproDetails.elements["product_qty["+element.value+"]"].value);
					}
					// end
				}
			}

			if(j == k){
				alert("<?php echo JText::_('PLEASE_SELECT_REGISTRATION_OPTION'); ?>");
				if(document.getElementById("chkIDs"))
					document.getElementById("chkIDs").focus();
				return false;
			}
		}else{

			var i,j=0,k=0;
			var count = document.regproDetails.elements.length;

			for(i=0;i<count;i++)
			{
				var element = document.regproDetails.elements[i];
				if(element.type == "checkbox"){
					if(element.id == "chkIDs" || element.id == "chkIDs_add"){
						j = eval(j+1);
						if(element.checked == false){
							k = eval(k+1);
						}

						// This condition for calcualte the total selected tickets for maxattendance validation
						if(element.id == "chkIDs" && element.checked == true){
							total_products_qty = parseInt(total_products_qty) + parseInt(document.regproDetails.elements["product_qty["+element.value+"]"].value);
						}
						// end
					}
				}
			}

			if(j == k){
				alert("<?php echo JText::_('PLEASE_SELECT_ANY_OPTION'); ?>");
				if(document.getElementById("chkIDs"))
					document.getElementById("chkIDs").focus();
				return false;
			}
		}

		// Max attendance validation check
		var availabelSheet	= document.getElementById("availablesheet").value;

		if(availabelSheet!='U'){
			if(total_products_qty > availabelSheet && availabelSheet > 0)
			{
				alert(availabelSheet+'<?php echo JText::_('TOTAL_AVALIABLE_SEATS'); ?>');
				return false;
			}
        }
		// End
	}
</script>

<form name="regproDetails" id="regproDetails"  action="<?php echo $this->action; ?>" method="post">

<div class="regpro_outline" id="regpro_outline">
	<table width="100%" border="0" class="adminlist">
	<tbody>
	<tr>
		<td colspan="3" align="right">
			<a onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" href="index.php?option=com_registrationpro&view=event&layout=printevent&did=<?php echo $this->row->did;?>&tmpl=component">
				<button type="button" class="btn"><i class="icon-print"></i><?php echo JText::_('EVENTS_PRINT_EVENT'); ?></button>
			</a>

			<a href="<?php echo JRoute::_("index.php?option=com_registrationpro&view=event&Itemid".$this->Itemid."&did=".$this->row->did);?>" title="<?php echo JText::_('EVENTS_ADD_TO_OUTLOOK'); ?>" class="addthisevent">
				<i class="icon-calendar"></i><?php echo JText::_('EVENTS_ADD_TO_OUTLOOK'); ?>
				<span class="_url"><?php echo JRoute::_("index.php?option=com_registrationpro&view=event&Itemid".$this->Itemid."&did=".$this->row->did);?></span>
				<span class="_start"><?php echo $this->row->dates; ?> <?php echo $this->row->times; ?></span>
				<span class="_end"><?php echo $this->row->enddates; ?> <?php echo $this->row->endtimes; ?></span>
				<span class="_zonecode"><?php echo $this->get_Addthisevent_timezone_value($this->regproConfig['timezone_offset']); ?></span>
				<span class="_summary"><?php echo substr($this->row->titel, 0, 100); ?></span>
				<span class="_description"><?php echo strip_tags(substr($this->row->datdescription,0,300)); ?></span>
				<span class="_location"><?php echo $this->row->city.' - '.$this->row->country.' - '.$this->row->street; ?></span>
				<span class="_date_format">YYYY/MM/DD</span>
			</a>

		</td>
      </tr>

  	<?php
	if($_REQUEST['detailshow'] != 'No'){

		if($this->regproConfig['showtime']==1 && $this->row->registra){
			fn_EventDetailsWhen($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showcategory']==1){
		 	fn_EventDetailsCategory($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showlocation']==1){
			fn_EventDetailsWhere($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showeventdates']==1){
			fn_EventStartEnd_Dates($this->row, $this->regproConfig);
		}

		if($this->regproConfig['showeventtimes']==1){
			fn_EventStartEnd_Times($this->row, $this->regproConfig);
		}
		fn_EventInstructor($this->row);
		if($this->regproConfig['show_max_seats_on_details_page']==1 && $this->row->registra){
			fn_EventMaxSeats($this->row, $this->regproConfig);
		}

		if($this->regproConfig['show_avaliable_seats_on_details_page']==1 && $this->row->registra){
			fn_EventAvailableSeats($this->row, $this->regproConfig);
		}

		if($this->regproConfig['show_registered_seats_on_details_page']==1 && $this->row->registra){
			fn_EventRegisteredSeats($this->row, $this->regproConfig);
		}

		if($this->row->shw_attendees==1 &&  $this->row->registra){
			fn_EventAttendees($this->row);
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
	?>
	</tbody>
	</table>

	<table width="100%" border="0">	
	<?php
		if(!array_key_exists($this->row->status,$status))
		{	
			// event discount details
			if(is_array($this->this_event_discounts) && count($this->this_event_discounts) > 0){
				fn_tickets_discounts_details($this->this_event_discounts, $this->regproConfig);
			}
		
			// event tickets listing
			if($this->tickets && $this->row->registra && $this->row->message == ""){
				if($this->formStatus != 0){
					fn_tickets_listing($this->row, $this->regproConfig, $this->tickets);
				}else{
					echo JText::_('FORMS_DISABLED'); 
				}
			}
		}else{
			echo "<div class='alert alert-info' style='text-align:center;'>";
				echo $status[$this->row->status];
			echo "</div>";
		}
	
  	?>
	</table>

</div>
<?php echo JHTML::_( 'form.token' ); ?>
</form>

<div style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>

<?php

}else{
	echo '<div style="height:4px;"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></div>';
	echo '<div class="regpro_error">'.JText::_('EVENTS_REGISTRA_LOGIN_FOR_DETAILS').'</div>';
	echo '<div style="height:4px;"><img src="'.REGPRO_IMG_PATH.'/blank.png" border="0" /></div>';
}

$regpro_header_footer->regpro_footer($this->regproConfig);

// Show event name
function fn_EventDetailsName($row, $regproConfig){$registrationproHelper = new registrationproHelper;
	if($regproConfig['showtitle'] == 1) {
	?>
		<tr> <td colspan="3"><?php echo JText::_('EVENTS_NAME');?>&nbsp; <b><?php echo $row->titel; ?></b> </td> </tr>
	<?php
	}
}

// Show event start and end date
function fn_EventDetailsWhen($row, $regproConfig){$registrationproHelper = new registrationproHelper;
	if($regproConfig['showtime']){
	?>
		<tr style="color:<?php echo $regproConfig['message_color']; ?>">
			<td width="35%" class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_WHEN1')." " ;?></td>

			<td colspan="2" class="regpro_vtop_aleft">
			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
				$date = "0000-00-00";
				if($row->regstart != "0000-00-00"){
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'], $row->regstart." ".$row->regstarttimes); //date($regproConfig['formatdate'], strtotime($row->regstart));
				}
				echo $date;
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR')." ";

				$date = "0000-00-00";
				if($row->regstop != "0000-00-00"){
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'], $row->regstop." ".$row->regstoptimes); //date($regproConfig['formatdate'], strtotime($reg_enddate));
				}
				echo $date;
			?>
			</td>
		</tr>
		<?php
	}
}

// Show event location name
function fn_EventDetailsWhere($row, $regproConfig){$registrationproHelper = new registrationproHelper;
		?>
	<tr>
		<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_WHERE')." " ;?></td>
		<td colspan="2" class="regpro_vtop_aleft">
		<?php
			if (( $regproConfig['showurl'] == 1) && (!empty($row->url))) {
				if(strtolower(substr($row->url, 0, 7)) == "http://") {
			?>
					<a href="<?php echo $row->url; ?>" target="_blank"> <?php echo $row->club; ?></a>
			<?php
				} else {
			?>
					<a href="<?php echo $row->url; ?>" target="_blank"> <?php echo $row->club; ?></a>
			<?php
				}
			} else echo $row->club;
			echo ", ";

			if(trim($row->street) != "") echo $row->street.", ";
			echo $row->city;

			if(trim($row->plz) != "") echo " ".$row->plz;

			echo " (".$row->country.").";
			if(trim($row->locdescription) != ""){
				echo "<br />";
				echo $row->locdescription;
			}

			?>
		</td>
	</tr>
	<?php
}

// Show event category name
function fn_EventDetailsCategory($row, $regproConfig){$registrationproHelper = new registrationproHelper;
?>
	<tr>
		<td class="regpro_vtop_aleft"> <?php echo JText::_('EVENTS_CATEGORY_NAME')." " ;?> </td>
		<td colspan="2" class="regpro_vtop_aleft"> <?php echo $row->catname;?> </td>
	</tr>
<?php
}

function fn_EventDateDetails($row, $regproConfig){$registrationproHelper = new registrationproHelper;
	if($regproConfig['showtime']){
		$detaillink = JRoute::_( 'index.php?option=com_registrationpro' );
	?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_START_END_DATE');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
				<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
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
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_START_END_TIME');?></td>
			<td colspan="2" class="regpro_vtop_aleft">

			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
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

function fn_EventStartEnd_Dates($row, $regproConfig){	$registrationproHelper = new registrationproHelper;
?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_START_END_DATE');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
			<?php
				if($row->enddates!=$row->dates){
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates); //date( $regproConfig['formatdate'], strtotime( $row->dates ));
					echo $date;
					echo JText::_('EVENTS_FRONT_DATE_SEPARATOR')."&nbsp;";
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->enddates); //date( $regproConfig['formatdate'], strtotime( $row->enddates ));
					echo $date;
				}else{
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates); //date( $regproConfig['formatdate'], strtotime( $row->dates ));
					echo $date;
				}
			?>
			</td>
		</tr>
<?php
}

function fn_EventStartEnd_Times($row, $regproConfig){	$registrationproHelper = new registrationproHelper;
?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_START_END_TIME');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
			<?php
				$time = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->times); //date( $regproConfig['formattime'], strtotime( $row->times ));
				echo $time;
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR')."&nbsp;";
				$endtime = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->endtimes); //date( $regproConfig['formattime'], strtotime( $row->endtimes ));
				echo $endtime;
			?>
			</td>
		</tr>
<?php
}

function fn_EventMaxSeats($row, $regproConfig){	$registrationproHelper = new registrationproHelper;
?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_MAX_SEATS');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
			<?php
				if($row->max_attendance==0){
					echo JText::_('UNLIMITED_ATTENDENCE');
				}else{
					echo $row->max_attendance;
				}
			?>
			</td>
		</tr>
<?php
}
function fn_EventInstructor($row){
		if($row->instructor!=""){
		?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('INSTRUCTOR_NAME');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
			<?php
				echo $row->instructor;
			?>
			</td>
		</tr>
<?php
	}else{
		echo '';
	}
}

function fn_EventAvailableSeats($row, $regproConfig){
?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_AVAILABLE_SEATS');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
			<?php
				if($row->avaliable > 0){
					echo $row->avaliable;
				}elseif($row->max_attendance == 0){
					echo JText::_('UNLIMITED_ATTENDENCE');
				}else{
					echo 0;
				}
			?>
			</td>
		</tr>
<?php
}

function fn_EventRegisteredSeats($row, $regproConfig){
?>
		<tr>
			<td class="regpro_vtop_aleft"><?php echo JText::_('EVENTS_REGISTERED_SEATS');?></td>
			<td colspan="2" class="regpro_vtop_aleft">
			<?php
				if($row->registered > 0){
					echo $row->registered;
				}else{
					echo 0;
				}
			?>
			</td>
		</tr>
<?php
}

# Ravi changes here for showing attendees---
function fn_EventAttendees($row){
	 JHTML::_('behavior.modal', 'a.modal');
?>

           <tr>
               <!-- <td colspan="3"><img src="<?php echo REGPRO_IMG_PATH; ?>/list.png" border="0" style="vertical-align:middle;" />
               <?php /*?> <a href="index2.php?option=com_registrationpro&view=attendees&did=<?php echo $_GET['did'];?>" title="View Attendees" class="modal">View Attendees</a><?php */?>
                <a href="index.php?option=com_registrationpro&view=attendees&tmpl=component&did=<?PHP echo $_GET['did'];?>" title="View Attendees" class="modal" rel="{handler: 'iframe', size: {x: 500, y: 400}}" style="position:relative;"><?php echo JText::_('EVENTS_VIEW_ATTENDEES_LIST'); ?></a>
                </td>-->
				<td>&nbsp;</td>
				<td colspan="2" style="text-align:left;">
					<a href="index.php?option=com_registrationpro&view=attendees&tmpl=component&did=<?PHP echo $row->did;?>" title="View Attendees" class="modal" rel="{handler: 'iframe', size: {x: 500, y: 400}}" style="position:relative; width:120px; margin-left: 0;left: 0;">
						<button type="button" class="btn">
						  <i class="icon-hand-right"></i><?php echo JText::_('EVENTS_VIEW_ATTENDEES_LIST'); ?>
						</button>
					</a>
				</td>

           </tr>
<?PHP
}
# ------------- End ------------------------
// Show event map location
function fn_EventDetailsMap($row, $regproConfig){

	if ($regproConfig['showmapserv']) {
?>
	<tr>
	  <td colspan="3" class="regpro_vtop_aleft">
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
			<td colspan="3" class="regpro_vtop_aleft"><b style="font-size:14px;"><?php echo JText::_('EVENTS_DESC'); ?></b></td>
		</tr>

		<tr>
			<td colspan="3" class="regpro_vtop_aleft">
				<?php
				$show_poster = trim($regproConfig['show_poster']) * 1;
				if($show_poster == 1) {
					include_once 'administrator/components/com_registrationpro/helpers/tools.php';
					$imgPrefixSystem = JURI::root() . "images/regpro/system/";
					$imgPrefixEvents = JURI::root() . "images/regpro/events/";
					$imgCurr = getImageName($row->id, $row->user_id);
					$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
					$imgName = '';
					if($row->poster !== '0') {
						$tmpName = $imgPrefixEvents . $imgCurr;
						$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();
					}
					if($imgName != '') {
						echo "<a href='".$imgName."'>";
						//echo "<img border=0 class=\"editlinktip hasTip\" title=\"<img src='".$imgName."'>\" id=\"event_img\" src=\"".$imgName."\" width=100 style=\"float:left;margin-right:15px;margin-bottom:10px;border:none;\">\n";
						echo "<img border=0 title=\"Zoom\" id=\"event_img\" src=\"".$imgName."\" width=100 style=\"float:left;margin-right:15px;margin-bottom:10px;border:none;\">\n";
						echo "</a>";
					}
				}
				?>

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
	
		$plugin_handler = new regProPlugins;				
			$res = $plugin_handler->getSocialsettings('event','bottom');
			//print_r($res);
			$pageurl = $_SERVER['REQUEST_URI'];
			$pageurl[0] = '';
			$pageurl = trim($pageurl);
			$pageurl = urlencode(JURI::root().$pageurl);
			$leftText = $res['share_text'];
			
			array_pop($res);
			
			if(count($res) >0)
			{
				echo "<tr>";
				echo '<td colspan="3">';
				echo $leftText. " ";
				include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tools.php';
				$imgPrefixSystem = JURI::root() . "images/regpro/system/";
				$imgPrefixEvents = JURI::root() . "images/regpro/events/";
				 $imgCurr = getImageName($row->id, $row->user_id);
				if($row->image !== '0') {
					$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();;					
				} else {
					$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
				}
				$s_desc = strip_tags($row->shortdescription);
				foreach($res as $k=>$v)
				{
					switch($k){
						case "l_facebook" :
						// Facebook
							echo sprintf($v,$pageurl,urlencode($row->titel), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
						case "l_twitter" :
						// Twitter
							echo sprintf($v,$pageurl,urlencode($row->titel), urlencode($imgName),$pageurl,urlencode($s_desc) );
						break;
						case "l_linkedin" :
							// Linkedin
							echo sprintf($v,$pageurl,urlencode($row->titel),urlencode($s_desc));
						break;
						case "l_googlePlus" :
						// Google +
							echo sprintf($v,$pageurl,urlencode($row->titel), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
						default  :
							echo sprintf($v,$pageurl,urlencode($row->titel), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
					}
						
						
					
				}
				echo "</td>";
				echo "</tr>";
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
			<td  colspan="3" class="regpro_vtop_aleft"><b style="font-size:14px;"><?php echo JText::_('EVENTS_LOCAT'); ?></b></td>
		</tr>

		<tr>
			<td width="20%" colspan="1" class="regpro_vtop_aleft">Venue:</td>
			<td width="80%" colspan="2" class="regpro_vtop_aleft">
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

	$my = JFactory::getUser();

	$arr_qty = range(1, $regproConfig['quantitylimit']);

	if(!empty($tickets)){
		$session = JFactory::getSession();
		$cart 	 = $session->get('cart');

		$script_flag = 0;
		$event_tickets_avaliable = 1; // flag to display the continue button
		if(@$cart['ticktes'] && is_array(@$cart['ticktes'])){
			foreach($cart['ticktes'] as $tkey => $tvalue) {
				foreach($tickets as $ttkey => $ttvalue) {
					if($tvalue->type == 'E'){
						if($tvalue->id == $ttvalue->id){
							$script_flag = 1;
							$ttvalue->cart_qty = $tvalue->qty; // for minus ticket qty from cart session to calulate avaliable ticket
						}
					}

					if($tvalue->type == 'A'){
						if($tvalue->id == $ttvalue->id){
							$ttvalue->cart_qty = $tvalue->qty; // for minus ticket qty from cart session to calulate avaliable ticket
						}
					}
				}
			}
		}
		
		?>

		<tr><td><strong><?php echo JText::_('EVENTS_REGISTRA_CHOOSE_PRODUCTS');?></strong></td></tr>

		<tr>
			<td class="regpro_outline" id="regpro_outline">
				<table border="0" width="100%" class="table">
				    <thead>
						<tr>
							<th width="15%" style="text-align:center"> <?php echo  JText::_('EVENT_TICKETS_HEAD_SNO'); ?> </th>
							<th width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_TICKETS_HEAD_QTY');?> </th>
							<th width="55%"> <?php echo  JText::_('EVENT_TICKETS_HEAD_TICKET_NAME');?> </th>
							<th width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_TICKETS_HEAD_TICKET_PRICE');?></th>
						</tr>
					</thead>
					
				<?php
					$registrationproHelper = new registrationproHelper;
					$current_date = $registrationproHelper->getCurrent_date("Y-m-d");
					$flag_add = 0;
					$tkt_qty = 0;
					$ticket_avaliable_flag = 0;
					$event_ticket_total_records = 0;
					$k = 0;
					
					/* Added by Sushil check total tickets sold to available */
					$total_ttikctes =0;
					$sold_ttikctes = 0;
					
					//echo "<pre>";print_r($tickets);
					foreach ($tickets as $product){
						if($product->id != 0)
						{
							if($product->type == "E"){
								$total_ttikctes = $total_ttikctes+$product->product_quantity;
								$sold_ttikctes = $sold_ttikctes+$product->product_quantity_sold;
							}
						}else{
							$total_ttikctes = 1;
						}
					}
					
					$tt_sold= false;
					if($total_ttikctes == 0){
						$tt_sold= false;
					}elseif($total_ttikctes == $sold_ttikctes ){
						$tt_sold= true;
					}
					
					/* Added by Sushil check total tickets sold to available */
					if(!$tt_sold){ //Added by Sushil check total tickets sold to available
					
						foreach ($tickets as $product){
							if($product->type == "E"){
								$event_ticket_total_records++; // count total event tickets record
								$ticket_avaliable_qty = 0;
								if($product->product_quantity > 0){
									$ticket_avaliable_qty = $product->product_quantity - $product->product_quantity_sold - $product->cart_qty;
									$tkt_qty = range(1, $ticket_avaliable_qty);
								}else{
									$ticket_avaliable_qty = 1;
									$tkt_qty = $arr_qty;
								}
								$ticket = 0;
								if(!empty($product->ticket_start)) $ticket = 1;
								if($ticket == 0 ){
									if($ticket_avaliable_qty > 0){
					?>
									
									<tbody>
									<tr class="<?php echo "row$k"; ?>">
										<td style="text-align:center; vertical-align:top">
											<input type="checkbox" id="chkIDs" name="product_id[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
										</td>
										<td style="text-align:center; vertical-align:top">
											<select name="product_qty[<?php echo $product->id; ?>]" id="product_qty" style="width:55px;">
												<?php foreach($tkt_qty as $qkey => $qvalue) { ?>
													<option value="<?php echo $qvalue;?>"><?php echo $qvalue;?></option>
												<?php }	?>
											</select>
										</td>
										<td style="vertical-align:top">
											<?php echo $product->product_name;?>
											<?php if($product->product_description) echo "<br />( ".$product->product_description." )";?>
										</td>
										<td style="text-align:right; vertical-align:top">
											<?php
												if($product->total_price==0) echo  JText::_('EVENTS_REGISTRA_FREE');
												else echo $regproConfig['currency_sign'],'&nbsp;',number_format($product->total_price,2);
											?>
										</td>
									</tr>
									<input type="hidden" name="productids[]" id="productids[]" value="<?php echo $product->id;?>" />
							<?php
								}else $ticket_avaliable_flag++ ;
							} else {
								if($product->ticket_start <= $current_date && $product->ticket_end >= $current_date && $ticket_avaliable_qty > 0){
							?>					
									<tbody>
									<tr class="<?php echo "row$k"; ?>">
										<td style="text-align:center; vertical-align:top">
											<input type="checkbox" id="chkIDs" name="product_id[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
										</td>
										<td style="text-align:center; vertical-align:top">
											<select name="product_qty[<?php echo $product->id; ?>]" id="product_qty" style="width:55px;">
												<?php
													foreach($tkt_qty as $qkey => $qvalue) {	?>
														<option value="<?php echo $qvalue;?>"><?php echo $qvalue;?></option>
												<?php
													}
												?>
											</select>
										</td>
										<td style="vertical-align:top">
											<?php echo $product->product_name;?>
											<?php if($product->product_description) echo "<br />( ".$product->product_description." )";	?>
										</td>
										<td style="text-align:right; vertical-align:top">
											<?php
												if($product->total_price==0) echo  JText::_('EVENTS_REGISTRA_FREE');
												else echo $regproConfig['currency_sign'],'&nbsp;',number_format($product->total_price,2);
											?>
										</td>
									</tr>
									<input type="hidden" name="productids[]" id="productids[]" value="<?php echo $product->id;?>" />
				<?php
								}else $ticket_avaliable_flag++ ;
							}
							} else $flag_add = 1;
							$k = 1 - $k;
						}
					}
					if($ticket_avaliable_flag ==  $event_ticket_total_records){
						$event_tickets_avaliable = 0;
				?>
						<tr><td colspan='4' style="text-align:center"><?php echo JText::_('EVENTS_REGISTRA_NO_TICKET_AVILIABLE'); ?></td></tr>
				<?php
					}
				?>
					</tbody>
					</table>
			</td>
		</tr>

	<?php if($flag_add == 1) { ?>
	
		<tr><td><strong><?php echo  JText::_('EVENTS_REGISTRA_CHOOSE_ADD_PRODUCTS');?></strong></td></tr>
		<tr>
			<td class="regpro_outline" id="regpro_outline">
				<table border="0" cellpadding="3" cellspacing="1" width="100%" class="table">
                    <thead>
						<tr>
							<th width="15%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_SNO'); ?> </th>
							<th width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_QTY');?> </th>
							<th width="55%"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_NAME');?> </th>
							<th width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_PRICE');?></th>
						</tr>
					</thead>
				<?php
					$flag_add = 0;
					$tkt_qty = 0;
					$ticket_avaliable_flag = 0;
					$additional_ticket_total_records = 0;
					$k = 0;
					/* Added by sushil check total tickets sold to available */
					$total_ttikctes =0;
					$sold_ttikctes = 0;
					foreach ($tickets as $product){
						if($product->type == "A"){
							$total_ttikctes = $total_ttikctes+$product->product_quantity;
							$sold_ttikctes = $sold_ttikctes+$product->product_quantity_sold;
						}
					}
					$tt_sold= false;
					if($total_ttikctes == $sold_ttikctes ){
						$tt_sold= true;
					}
					/* Added by sushil check total tickets sold to available */
					if(!$tt_sold){ //Added by sushil check total tickets sold to available
						foreach ($tickets as $product){
							if($product->type == "A"){
								$additional_ticket_total_records++; // count total additional tickets record
								$ticket_avaliable_qty = 0;
								if($product->product_quantity > 0){
									$ticket_avaliable_qty = $product->product_quantity - $product->product_quantity_sold - $product->cart_qty;
									$tkt_qty = range(1, $ticket_avaliable_qty);
								}else{
									$ticket_avaliable_qty = 1;
									$tkt_qty = $arr_qty;
								}
								if(!empty($product->ticket_start)){	$products = 1; } else $products = 0;
								if($products == 0){
									if($ticket_avaliable_qty > 0){
		?>								<thead>
										<tr>
										<th width="15%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_SNO'); ?> </th>
										<th width="10%" style="text-align:center"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_QTY');?> </th>
										<th width="55%"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_NAME');?> </th>
										<th width="20%" style="text-align:right"> <?php echo  JText::_('EVENT_ADD_TICKETS_HEAD_TICKET_PRICE');?></th>
									</tr>
									</thead>
									<tbody>
									<tr class="<?php echo "row$k"; ?>">
										<td style="text-align:center; vertical-align:top">
											<input type="checkbox" id="chkIDs_add" name="product_id_add[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
										</td>
										<td style="text-align:center; vertical-align:top">
											<select name="product_qty_add[<?php echo $product->id; ?>]" style="width:55px;">
												<?php
													foreach($tkt_qty as $qkey => $qvalue){
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
												if($product->product_description) echo "<br />( ".$product->product_description." )";
											?>
										</td>
										<td style="text-align:right; vertical-align:top">
											<?php
												if($product->total_price==0) echo JText::_('EVENTS_REGISTRA_FREE');
												else echo $regproConfig['currency_sign'],'&nbsp;',number_format($product->total_price,2);
											?>
										</td>
									</tr>
										<input type="hidden" name="productids[]" id="productids[]" value="<?php echo $product->id;?>" />
								<?php
								}else $ticket_avaliable_flag++ ;
							}else{
								if($product->ticket_start <= $current_date && $product->ticket_end >= $current_date && $ticket_avaliable_qty > 0){
					?>				
									<tbody>
									<tr class="<?php echo "row$k"; ?>">
										<td style="text-align:center; vertical-align:top">
											<input type="checkbox" id="chkIDs_add" name="product_id_add[<?php echo $product->id;?>]" value="<?php echo $product->id;?>">
										</td>
										<td style="text-align:center; vertical-align:top">
											<select name="product_qty_add[<?php echo $product->id; ?>]" style="width:55px;">
												<?php foreach($tkt_qty as $qkey => $qvalue) { ?>
													<option value="<?php echo $qvalue;?>"><?php echo $qvalue;?></option>
												 <?php } ?>
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
												if($product->total_price==0) echo  JText::_('EVENTS_REGISTRA_FREE');
												else echo $regproConfig['currency_sign'],'&nbsp;',number_format($product->total_price,2);
											?>
										</td>
									</tr>
									<input type="hidden" name="productids[]" id="productids[]" value="<?php echo $product->id;?>" />
				<?php
									}else $ticket_avaliable_flag++ ;
								}
							}
							$k = 1 - $k;
						}
					}
					if($ticket_avaliable_flag ==  $additional_ticket_total_records){
				?>
						<tr><td colspan='4' style="text-align:center"><?php echo JText::_('EVENTS_REGISTRA_NO_TICKET_AVILIABLE'); ?></td></tr>
				<?php
					}
				?>
				</tbody>
				</table>
			</td>
		</tr>
	<?php }

		if(isset($row->form)){
	?>
		<tr>
			<td>
				<?php
				if(!empty($row->form))
					foreach ($row->form as $form_field=>$form_field_value)
						echo '<input type="hidden" name="form['.$form_field.']" value="'.$form_field_value.'" />';
				?>
			</td>
		</tr>
	<?php
		}
	?>

		<?php
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		$loginmsg = JText::_('EVENTS_REGISTRA_LOGIN');
		$event_access = 1;
		if($row->eventaccess > 0){
			if(in_array($row->eventaccess, $groups)){
				$event_access = 1;
			}else{
				$event_access = 0;
				if($row->eventaccess == 3) $loginmsg = JText::_('EVENTS_REGISTRA_SPECIAL_LOGIN');
			}
		}else $event_access = 1;

		if($event_access == 0){
			echo '<tr><td colspan="3" class="alert alert-error">';
			echo $loginmsg;
			echo '</td></tr>';
		}else{
		if($row->allowgroup == 1){
			if($row->force_groupregistration == 1){
		?>
				<input type="hidden" name="chkgroupregistration" id="chkgroupregistration" value="1" />
		<?php
			}else{
		?>
			<tr><td style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /> </td></tr>
			<tr><td><input type="checkbox" name="chkgroupregistration" id="chkgroupregistration" value="1" style="vertical-align:middle"/> <?php echo JText::_('REG_TYPE');?></td></tr>
			<tr><td style="height:4px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /> </td></tr>
		<?php
			}
		}
		?>

		<?php if($event_tickets_avaliable > 0){ ?>
		<tr>
			<td><br />
				<input type="hidden" NAME="option" value="com_registrationpro" />
				<input type="hidden" name="step" value="1" />
				<input type="hidden" name="detailshow" value="No" />
				<input type="hidden" name="did" value="<?php echo $row->did;?>" />
				<input type="hidden" NAME="Itemid" value="<?php echo $Itemid; ?>" />
				<input type="hidden" NAME="rdid" value="<?php echo $row->did; ?>" />
				<input type="hidden" NAME="func" value="details" />
				<input type="hidden" name="availablesheet" id="availablesheet" value="<?php echo $row->avaliable; ?>">
				<button type="submit" name="submit" class="btn btn-primary regpro_button" onclick="return checkchkbox(<?php echo $script_flag; ?>);">
				<i class="icon-hand-right icon-white"></i> <?php echo JTEXT::_('EVENTS_DETAIL_PAGE_BUTTON'); ?>
				</button>
			</td>
		</tr>
<?php
			}
		}
	}
}

// event discount details
function fn_tickets_discounts_details($this_event_discounts, $regproConfig)
{
	$jdate 	= JFactory::getDate(); // get date object

	//$current_date = $jdate->toFormat('%Y-%m-%d');
	$registrationproHelper = new registrationproHelper;
	$current_date = $registrationproHelper->getCurrent_date("Y-m-d");

	// group registration discount details
	foreach($this_event_discounts as $key=>$value){

		if($value->discount_name == "G"){

			if($gcnt > 1){ }else{ $gcnt = 1;}

			if($gcnt == 1){
?>
		<tr>
			<td class="label label-warning">  <img src="<?php echo REGPRO_IMG_PATH; ?>/offer.png" border="0" align="absmiddle" /><strong><?php echo JText::_('EVENTS_GROUP_DISCOUNT'); ?></strong></td>
		</tr>

		<tr>
			<td class="regpro_outline" id="regpro_outline">
				<table border="0" width="100%" class="table">
					<thead>
					<tr>
						<th width="5%" style="text-align:center"> # </th>
						<th width="65%" style="text-align:center"> <?php echo  JText::_('EVENTS_MIN_NUMBER_TICKETS');?> </th>
						<th align="right" width="30%"> <?php echo  JText::_('EVENTS_GROUP_DISCOUNT_AMOUNT_PER_TICKET');?> </th>
					</tr>
					</thead>
	<?php	} ?>
					<tbody>
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
				</tbody>
				</table>
			</td>
		</tr>

	<?php
	}

	// early registration discount details
	foreach($this_event_discounts as $key=>$value){

		//if($value->discount_name == "E" && $value->early_discount_date >= date('Y-m-d')){
		if($value->discount_name == "E" && $value->early_discount_date >= $current_date){

			if($ecnt > 1){ }else{ $ecnt = 1;}

			if($ecnt == 1){
	?>
		<tr>
			<td class="label label-warning" ><img src="<?php echo REGPRO_IMG_PATH; ?>/offer.png" border="0" align="absmiddle" /> <strong><?php echo JText::_('EVENTS_EARLY_REGISTRATION_DISCOUNT');?></strong></td>
		</tr>

		<tr>
			<td class="regpro_outline" id="regpro_outline">
				<table border="0" width="100%" class="table">
					<thead>
					<tr>
						<th width="5%" style="text-align:center"> # </th>
						<th width="65%" style="text-align:center"> <?php echo  JText::_('EVENTS_EARLY_DISCOUNT_DATE');?> </th>
						<th align="right" width="30%"> <?php echo  JText::_('EVENTS_EARLY_DISCOUNT_AMOUNT_PER_TICKET');?> </th>
					</tr>
					</thead>

	<?php	} ?>
					<tbody>
					<tr>
						<td class="regpro_event_discount" width="5%" style="text-align:center"> <?php echo $ecnt; ?> </td>
						<td class="regpro_event_discount" width="65%" style="text-align:center">
							<?php
								//echo $value->early_discount_date;
								echo $registrationproHelper->getFormatdate($regproConfig['formatdate'], $value->early_discount_date); //date($regproConfig['formatdate'],strtotime($value->early_discount_date))
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
				</tbody>
				</table>
			</td>
		</tr>
<?php
	}
}
?>
</div>

<!-- AddThisEvent -->
<script type="text/javascript" src="http://js.addthisevent.com/atemay.js"></script>

<!-- AddThisEvent Settings -->
<script type="text/javascript">
addthisevent.settings({
    license   : "ayfvx5ug7zyj6nrl0m4v",
    mouse     : false,
    css       : true,
    outlook   : {show:true, text:"Outlook Calendar"},
    google    : {show:true, text:"Google Calendar"},
    yahoo     : {show:true, text:"Yahoo Calendar"},
    hotmail   : {show:true, text:"Hotmail Calendar"},
    ical      : {show:true, text:"iCal Calendar"},
    facebook  : {show:false, text:"Facebook Event"},
    callback  : ""
});
</script>