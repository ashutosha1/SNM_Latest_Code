<?php
defined('_JEXEC') or die('Restricted access');
$regproConfig = $this->regproConfig;
$row = $this->row;

$document = JFactory::getDocument();
$document->setTitle($this->row->titel);
?>

<table cellspacing="0" cellpadding="0" width="95%" align="center" border="0">
<tr>
	<td colspan="3">
		<img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" height="4px;" />
	</td>
</td>
<tr>
	<td colspan="3">
<div style="text-align:right;">
<a href="#" onclick="window.print();return false;" ><img src="<?php echo REGPRO_IMG_PATH; ?>/event_print.png" alt="<?php echo JText::_('MENU_EVENT_PRINT'); ?>" /></a>
</div>
<h1><div class='regpro_detailsheading'><?php echo ucwords($this->row->titel); ?> - <?php echo JText::_('EVENTS_DETAILS');?></div></h1>
	</td>
</tr>

<?php $registrationproHelper = new registrationproHelper;
if($regproConfig['showtime']){
	?>		
		<tr>
			<td width="35%" valign="top"><?php echo JText::_('EVENTS_WHEN1')." " ;?></td>

			<td colspan="2">
			<?php // fn_EventDetailsImage($row, $regproConfig);?>
			<?php										
				if($row->regstart != "0000-00-00"){	
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'], $row->regstart." ".$row->regstarttimes);
				}else{ 	
					$date = "0000-00-00";
				}
					
				echo $date;		
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
								
				if($row->regstop != "0000-00-00"){
					//$date = registrationproHelper::getFormatdate($regproConfig['formatdate'], $reg_enddate);
					$date = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'], $row->regstop." ".$row->regstoptimes);
				} else { 
					$date = "0000-00-00";
				}
				
				echo $date;
			?>
			</td>
		</tr>
		<?php
	}?>
	<tr>
		<td><?php echo JText::_('EVENTS_WHERE')." " ;?></td>
		<td colspan="2" class="regpro_vtop_aleft">
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
			
			echo ", ";
			
			if(trim($row->street) != ""){
				echo $row->street.", ";
			}
															
			echo $row->city;  
			
			if(trim($row->plz) != ""){
				echo " ".$row->plz;
			}
			
			echo " (".$row->country.").";
			
			if(trim($row->locdescription) != ""){
				echo "<br />";
				echo $row->locdescription;
			}
						
			?>
		</td>
	</tr>	
	<tr>
			<td><?php echo JText::_('EVENTS_START_END_DATE');?></td>	
			<td colspan="2">				
			<?php
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates);
				echo $date;				
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->enddates);
				echo $date;
			?>
			</td>
		</tr>
	<tr>
			<td><?php echo JText::_('EVENTS_START_END_TIME');?></td>	
			<td colspan="2">				
			<?php				
				$time = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->times);
				echo $time;
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$endtime =  $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->endtimes);	
				echo $endtime;
			?>
			</td>
		</tr>

		<?php
			if($this->regproConfig['showevdesc']==1){
			fn_EventDetailsDescription($this->row, $this->regproConfig);
		}	
	?>
	
</table>
	<?php	

function fn_EventDateDetails($row, $regproConfig){
	$registrationproHelper = new registrationproHelper;
	if($regproConfig['showtime']){
		$detaillink = JRoute::_( 'index.php?option=com_registrationpro' );
	?>
		<tr>
			<td><?php echo JText::_('EVENTS_START_END_DATE');?></td>	
			<td colspan="2">
				<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
				$date =  $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates);
				echo $date;				
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->enddates);
				echo $date."  ";
				$endtime = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->endtimes);
			?>
			</td>
		</tr>

		<tr>
			<td><?php echo JText::_('EVENTS_START_END_TIME');?></td>	
			<td colspan="2">

			<?php fn_EventDetailsImage($row, $regproConfig);?>
			<?php
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->dates);		
				$time = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->times);
				echo " $time";
				echo JText::_('EVENTS_FRONT_DATE_SEPARATOR');
				$date = $registrationproHelper->getFormatdate($regproConfig['formatdate'], $row->enddates);
				$endtime = $registrationproHelper->getFormatdate($regproConfig['formattime'], $row->endtimes);	
				echo " $endtime";
			?>
			</td>
		</tr>
		<?php
	}
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
			if($imgName != '') echo "<tr><td colspan=20 align=center><img border=0 id=\"event_img\" src=\"".$imgName."\" style=\"margin-top:15px;margin-bottom:15px;\" width=400></td></tr>\n";
		}
		?>
	<?php	
	}
}
//exit;
?>