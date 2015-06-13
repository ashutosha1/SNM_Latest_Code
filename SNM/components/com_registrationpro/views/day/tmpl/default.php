<script language="javascript" type="text/javascript">
	function tableOrdering( order, dir, task ) {
		var form = document.adminForm;
		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( task );
	}
</script>

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
JHTML::_('behavior.tooltip');

$collapsestatus = 0;
if ($this->regproConfig['collapse_categories']) $collapsestatus = $this->regproConfig['collapse_categories'];

$database = JFactory::getDBO();
$colspan = 1;
?>
<div id="regpro">

<?php $regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regproConfig); ?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm">

<?php
	if(trim($this->regproConfig['introtext']) != ""){
?>
<table width="100%" border="0" class="eventlisting">
	<tr> <td> <?php echo $this->regproConfig['introtext']; ?>	</td></tr>
</table>
<?php
	}
?>
<div style="height:2px;"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0" /></div>
<div class="regpro_outline" id="regpro_outline">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="eventlisting">
	<thead>
	<tr>

		<?php
			$show_poster = trim($this->regproConfig['show_poster']) * 1;
			if($show_poster == 1) {
				$colspan++;
		?>
		<th width=50 style="text-align:center">
			<?php echo JText::_('EVENTS_FRONT_POSTER'); ?>
		</th>
		<?php
			}
		?>

		<th width="18%"><?php echo JHTML::_('grid.sort', '<b>'. JText::_('EVENTS_FRONT_TITLE') .'<b/>', 'a.titel', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>

		<?php
			if($this->regproConfig['showeventdates'] == 1 || $this->regproConfig['showeventtimes'] == 1){
				$colspan++;
		?>
		<th width="20%" style="text-align:center">
			<?php echo JHTML::_('grid.sort','<b>'. JText::_('EVENTS_FRONT_DATE').'<b/>', 'a.dates', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<?php
			}
		?>

		<?php
			if($this->regproConfig['showlocationcolumn'] == 1){
				$colspan++;
		?>
		<th  width="16%" style="text-align:center"><?php echo JText::_('EVENTS_FRONT_LOCATION'); ?></th>
		<?php }?>

		<?php if($this->regproConfig['showshortdescriptioncolumn'] == 1){
				$colspan++;
		?>
		<th width="18%"><?php echo JText::_('EVENTS_FRONT_SHORTDESC'); ?></th>
		<?php }?>

		<?php
			if($this->show_attendance_column){
				$colspan++;
		?>
		<th width="18%" style="text-align:center"><?php echo JText::_('EVENTS_FRONT_MAXATTN'); ?></th>
		<?php }?>

		<?php
			if($this->regproConfig['showpricecolumn'] == 1){
				$colspan++;
		?>
		<th width="10%" style="text-align:right"><?php echo JText::_('EVENTS_FRONT_PRICE'); ?></th>
		<?php }?>

	</tr>
	</thead>
	<tbody>

	<?php
	$k = 0;
	$flagcatid = "";
	$registrationproHelper = new registrationproHelper;

	for ($i=0, $n=count($this->rows); $i < $n; $i++) {
		$row = $this->rows[$i];
		$datum = '';
		if($row->enddates!=$row->dates){
			//format for more than 1 day events
			if($this->regproConfig['showeventdates'] == 1){
				$datum = JText::_('EVENTS_START').': '.$registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $row->dates).' ';
			}

			if($this->regproConfig['showeventtimes'] == 1){
				if($this->regproConfig['showeventdates'] == 1){
					$datum .= $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->times);
				}else{
					$datum .= JText::_('EVENTS_START').': '.$registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->times);
				}
			}

			if($this->regproConfig['showeventdates'] == 1){
				$datum .= '<br/>'.JText::_('EVENTS_END'). ': '.$registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $row->enddates). ' ';
			}

			if($this->regproConfig['showeventtimes'] == 1){
				if($this->regproConfig['showeventdates'] == 1){
					$datum .= $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->endtimes);
				}else{
					$datum .= '<br/>'.JText::_('EVENTS_END'). ': '.$registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->endtimes);
				}
			}
		}else{
			//format for 1 day events
			if($this->regproConfig['showeventdates'] == 1) $datum = JText::_('EVENTS_DATE'). ': ' .$registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $row->dates). ' <br/>';
			if($this->regproConfig['showeventtimes'] == 1) $datum .= JText::_('EVENTS_START'). ': '. $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->times). ' <br/>';
			if($this->regproConfig['showeventtimes'] == 1) $datum .= JText::_('EVENTS_END'). ': ' 	.$registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->endtimes). ' <br/>';
		}

		$status = 1; //if event open

		$background = '';
		$database->setQuery("select background from #__registrationpro_categories where id = '".(int)$row->catid."'");
		$background = $database->loadResult();
		if($background!='') $background = 'style="background-color:#'.$background.'"';
	?>
		<?php
		if($flagcatid != $row->catid){
			//check config setting for collapse / expand the categories
			$flagcollapse = 0;
			if($regproConfig['collapse_categories'] == 1) $flagcollapse = 1;
		?>
			<tr <?php echo $background;?>>
				<td colspan="20" <?php echo $background;?>>
					<img id="img<?php echo $row->id;?>" src="<?php echo REGPRO_IMG_PATH; ?>/minus.jpg" border="1" onclick="return fn_expand(this,'<?php echo REGPRO_IMG_PATH."/"?>',<?php echo $n;?>,<?php echo $row->id;?>);" <?php if($flagcollapse == 1) ?>  "javascript: return fn_expand(this,'<?php echo REGPRO_IMG_PATH."/"?>',<?php echo $n;?>,<?php echo $row->id;?>);"  />&nbsp;<?php echo $row->catname;?>
				</td>
			</tr>

		<?php
			$trids = "img".$row->id."tr";
		}?>

		<?php
			$tempTrids = $trids.$i;
		?>
			<tr id="<?php echo $tempTrids; ?>" class="<?php echo "row$k"; ?>">
		<?php
			$detaillink = JRoute::_('index.php?option=com_registrationpro&amp;view=event&amp;Itemid='. $this->Itemid .'&amp;did='.$row->id);
		?>

		<?php
		$colspan = '';
		if($show_poster == 1) {
			include_once 'administrator/components/com_registrationpro/helpers/tools.php';
			$imgPrefixSystem = JURI::root() . "images/regpro/system/";
			$imgPrefixEvents = JURI::root() . "images/regpro/events/";
			$imgCurr = getImageName($row->id, $row->user_id);
			$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
			$imgName = '';
			if($row->poster) $imgName = $imgPrefixEvents . $imgCurr . getUniqFck();
			if($imgName !='') {
				echo "<td valign=\"top\" align=\"center\" style=\"vertical-align:top;text-align:center;\">\n";
				echo "<img class=\"editlinktip hasTip\" title=\"<img src='".$imgName."'>\" id=\"event_img\" src=\"".$imgName."\" width=50>\n";
				echo "</td>\n";
			} else $colspan = "colspan=2";
		}
		?>

		<td valign="top" <?php echo $colspan;?>><a href="<?php echo $detaillink ; ?>"><?php echo $row->titel; ?></a></td>

		<?php if($this->regproConfig['showeventdates'] == 1 || $this->regproConfig['showeventtimes'] == 1){	?>
			<td valign="top" align="center"><?php echo $datum; ?></td>
		<?php } ?>

		<?php if($this->regproConfig['showlocationcolumn'] == 1){	?>
			<td valign="top" align="left">
				<?php
					$location = "";

					if (( $this->regproConfig['showurl'] == 1) && (!empty($row->url))) {
						if(strtolower(substr($row->url, 0, 7)) == "http://") {
							$location .= '<a href="'.$row->url.'" target="_blank">'.$row->club.'</a>';
						} else $location .= '<a href="http://'.$row->url.'" target="_blank">'.$row->club.'</a>';
					} else $location .= $row->club;

					$location .= ", ";
					if(trim($row->street) != "") $location .= $row->street.", ";

					$location .= $row->city." ";
					if(trim($row->plz) != "") $location .= $row->plz;
					$location .= " (".$row->country.")";
					echo $location;
				?>
			</td>
		<?php } ?>

		<?php if($this->regproConfig['showshortdescriptioncolumn'] == 1){?>
			<td valign="top"><?php echo str_replace("&nbsp;","",$row->shortdescription); ?><a href="<?php echo $detaillink ; ?>"><?php echo JText::_('READ_MORE'); ?></a></td>
		<?php } ?>

			<?php if($this->show_attendance_column){?>
			<td class="regpro_vtop_aright">
				<?php
					if($row->max_attendance==0){ ?>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' style="text-align:center">
							<?php if($this->regproConfig['maxseat'] == 1){ ?>
								<Tr>
									<td style="width:70%"><?php echo JText::_('UNLIMITED_ATTENDENCE'); ?></td>
									<td style="width:5%"> <img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0"  /> </td>
									<td style='text-align:left'><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0"  /></td>
								</Tr>
							<?php } ?>

							<?php if($this->regproConfig['registeredseat'] == 1){ ?>
								<Tr>
									<td style="width:70%"><?php echo JText::_('TOTAL_REGISTERED'); ?> </td>
									<td style="width:5%"> <img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0"  /> </td>
									<td style='text-align:left'><?php echo $row->registered; ?></td>
								</Tr>
							<?php } ?>
						 </table>
				<?php
					}else{
				?>
						<table cellpadding='0' cellspacing='0' border='0' width='100%' style="text-align:center">
						<?php
							if($this->regproConfig['maxseat'] == 1){
								if($row->registered >= $row->max_attendance){
						?>
								<Tr>
									<td colspan="3"> <img src="<?php echo REGPRO_IMG_PATH; ?>/sold.gif" border="0" /></td>
								</Tr>
						<?php
								}else{
						?>
								<Tr>
									<td style="width:70%"> <?php echo JText::_('TOTAL_ATTENDENCE'); ?></td>
									<td style="width:5%"><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0"  /> </td>
									<td style='text-align:left'><?php echo $row->max_attendance; ?></td>
								</Tr>
						<?php
								}
							}
						?>

						<?php
							if($this->regproConfig['registeredseat'] == 1){
								if($row->registered < $row->max_attendance){
						?>
								<Tr>
									<td><?php echo JText::_('TOTAL_REGISTERED'); ?></td>
									<td><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0"  /> </td>
									<td style='text-align:left'><?php echo $row->registered; ?></td>
								</Tr>
						<?php
								}
							}
						?>

						<?php
							if($this->regproConfig['pendingseat'] == 1){
								if($row->registered < $row->max_attendance){
						?>
								<Tr>
									<td><?php echo JText::_('TOTAL_AVALIABLE'); ?></td>
									<td><img src="<?php echo REGPRO_IMG_PATH; ?>/blank.png" border="0"  /></td>
									<td style='text-align:left'><?php echo $row->avaliable; ?></td>
								</Tr>
						<?php
								}
							}
						?>

						</table>
				<?php
					}
				?>

			</td>
			<?php } ?>

			<?php
			if($this->regproConfig['showpricecolumn'] == 1){
			?>
			<td class="regpro_vtop_aright">
			<?php
				if($row->showprice == 1){
					$values = 0;
					$currency = '';
					$currency = $this->regproConfig['currency_sign'];
					foreach($row->price as $opt) $values += $opt->total_price;
					if($values > 0){
						foreach($row->price as $tprice) echo '<span class="editlinktip hasTip" style="vertical-align:middle" title="::'.$tprice->product_description.'">'.$currency.' '.number_format($tprice->total_price,2).'</span><br />';
					} else echo JText::_('FREE_EVENT');
				} else echo "&nbsp;";
			?>
			</td>
			<?php
			} ?>
		</tr>

	<?php

	$k = 1 - $k;
	$flagcatid = $row->catid;

	} ?>

	<?php
		if(count($this->rows) < 1){
	?>
			<tr><td colspan=20 style="text-align:center"><?php echo JText::_('NO_EVENT_RECORD'); ?></td></tr>
	<?php
		}
	?>
	</tbody>

	<tfoot>
		<tr><td colspan=20><div class="pagination"><?php echo $this->pageNav->getListFooter(); ?></div></td></tr>
	</tfoot>

</table>

<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="viewcache" value="0" />
<input type="hidden" name="task" value="" />
</form>
</div>

<script language="javascript" type="text/javascript">

	// function to collapse /  expand according to config settings
	function fn_listonload(collapse, imgpath, trcount)
	{
		var cntimg = window.document.images.length;	 // get total images on page
		var imgid="";

		for(k=0;k<cntimg;k++)
		{
			// check if id exist of images
			if(window.document.images[k].id != ""){
				imgid = window.document.images[k].id; // assign image id

				if(window.document.images[k].id.substring(0,3) == "img"){

					for(i=0;i<=trcount;i++)
					{
						var trid = imgid + "tr" + i; // make row ids to hide / show.

						if(document.getElementById(trid)){
							if(collapse == 1){
								document.getElementById(imgid).src = imgpath + 'plus.jpg';
								document.getElementById(trid).style.display = 'none';
							}else{
								document.getElementById(imgid).src = imgpath + 'minus.jpg';
								document.getElementById(trid).style.display = '';
							}
						}
					}
				}
				// end
			}
		}

	}
	// end

	fn_listonload(<?php echo $collapsestatus; ?>,'<?php echo REGPRO_IMG_PATH."/"; ?>', <?php echo count($this->rows); ?> );

	function fn_expand(imgid,imgpath,trcount,id)
	{
		var imid = imgid.id;
		var i;
		for(i=0;i<trcount;i++)
		{
			var trid = "img" + id + "tr" + i;

			if(document.getElementById(trid)){
				if(document.getElementById(trid).style.display == 'none'){
					document.getElementById(imid).src = imgpath + 'minus.jpg';
					document.getElementById(trid).style.display = '';
				}else{
					document.getElementById(imid).src = imgpath + 'plus.jpg';
					document.getElementById(trid).style.display = 'none';
				}
			}
		}
	}
</script>


<?php
$regpro_header_footer->regpro_footer($this->regproConfig);
?>

</div>