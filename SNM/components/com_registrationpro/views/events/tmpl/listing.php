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
$database = JFactory::getDBO();
global $mainframe, $option;
?>

<script language="javascript" type="text/javascript">
	function tableOrdering( order, dir, task ) {
		var form = document.adminForm;
		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( task );
	}

	function regprosorting (val) {
		var form = document.adminForm;
		Joomla.tableOrdering(val,'<?php echo $this->lists['order_Dir'];?>','');
	}

	function regprosortingorder(order) {
		var form = document.adminForm;
		Joomla.tableOrdering('<?php echo $this->lists['order'];?>',order,'');
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
$regpro_header_footer->regpro_header($this->regproConfig); ?>

<!--<form action="<?php echo $this->action; ?>" method="post" name="adminForm">-->
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">

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

<div class="sorting_box">
	<?php // echo JText::_('EVENTS_FRONT_TITLE')." : ".$Lists['sort'];
		// echo $Lists['sort'];
	?>
	<div class="control-group">
		<div class="controls">
				<div class="btn-group">
					<button class="btn <?php if($this->lists['order'] == "a.dates") echo "active"; ?>" type="button" onclick="regprosorting('a.dates');">Date</button>
					<button class="btn <?php if($this->lists['order'] == "l.club") echo "active"; ?>" type="button" onclick="regprosorting('l.club');">Location</button>
					<button class="btn <?php if($this->lists['order'] == "a.titel") echo "active"; ?>" type="button" onclick="regprosorting('a.titel');">Title</button>
					<button class="btn <?php if($this->lists['order'] == "c.catname") echo "active"; ?>" type="button" onclick="regprosorting('c.catname');">Category</button>
				</div>
				<div class="btn-group">
					<button class="btn <?php if($this->lists['order_Dir'] == "ASC") echo "active"; ?>" type="button" onclick="regprosortingorder('ASC');"><i class="icon-arrow-up"></i></button>
					<button class="btn <?php if($this->lists['order_Dir'] == "DESC") echo "active"; ?>" type="button" onclick="regprosortingorder('DESC');"><i class="icon-arrow-down"></i></button>
				</div>
		</div>
	</div>
</div>

<?php
	//echo"<pre>";print_r($this->rows); exit;
	$k = 0;
	$registrationproHelper  = new registrationproHelper;
	for ($i=0, $n=count($this->rows); $i < $n; $i++) {
		$row = $this->rows[$i];
		$rowclass = ++$k%2 ? "odd" : "even";
		$event_start_date = "";
		$event_start_time = "";
		$event_end_date	  = "";
		$event_end_time	  = "";

		$event_title	  = $row->titel;
		$detail_link 	  = JRoute::_('index.php?option=com_registrationpro&view=event&Itemid='. $this->Itemid .'&did='.$row->id,false);
		$event_cat_name	  = $row->catname;
		$catgory_page_link	= JRoute::_('index.php?option=com_registrationpro&view=category&Itemid='. $this->Itemid .'&id='.$row->catid,false);
		$event_short_description = $row->shortdescription;

		if($row->enddates!=$row->dates){
			//format for more than 1 day events
			if($this->regproConfig['showeventdates'] == 1){
				$event_start_date = $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $row->dates);
			}

			if($this->regproConfig['showeventtimes'] == 1){
				if($this->regproConfig['showeventdates'] == 1){
					$event_start_time = $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->times);
				}else{
					$event_start_time = $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->times);
				}
			}

			if($this->regproConfig['showeventdates'] == 1){
				$event_end_date = $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $row->enddates);
			}

			if($this->regproConfig['showeventtimes'] == 1){
				if($this->regproConfig['showeventdates'] == 1){
					$event_end_time = $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->endtimes);
				}else{
					$event_end_time = $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->endtimes);
				}
			}
		}else{
			//format for 1 day events
			if($this->regproConfig['showeventdates'] == 1){
				$event_start_date = $registrationproHelper->getFormatdate($this->regproConfig['formatdate'], $row->dates);
			}
			if($this->regproConfig['showeventtimes'] == 1){
				$event_start_time = $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->times);
			}
			if($this->regproConfig['showeventtimes'] == 1){
				$event_end_time = $registrationproHelper->getFormatdate($this->regproConfig['formattime'], $row->endtimes);
			}
		}

		// Get event location
		$location = "";

		if (( $this->regproConfig['showurl'] == 1) && (!empty($row->url))) {
			if(strtolower(substr($row->url, 0, 7)) == "http://") {
				$location .= '<a href="'.$row->url.'" target="_blank">'.$row->club.'</a>';
			} else {
				$location .= '<a href="'.$row->url.'" target="_blank">'.$row->club.'</a>';
			}
		} else {
			$location .= $row->club;
		}

		$location .= ", ";

		if(trim($row->street) != ""){
			$location .= $row->street.", ";
		}

		$location .= $row->city." ";

		if(trim($row->plz) != ""){
			$location .= $row->plz;
		}

		$location .= " (".$row->country.")";

		// get category color
		$background = '';
		$database->setQuery("select background from #__registrationpro_categories where id = '".(int)$row->catid."'");
		$background = $database->loadResult();

		$cat_link_color = $registrationproHelper->checkColor($background);

		if($background!='') {
			$background = 'style="background-color:#'.$background.'; color:'.$cat_link_color.';"';
		}else{
			$background = 'style="background-color:#FFFFFF"; color:'.$cat_link_color.';"';
		}

		// check if event is free or paid
		$price_flag = 0;
		foreach($row->price as $opt){
			$price_flag += $opt->total_price;
		}
		if($price_flag > 0){
			$event_price = JText::_('PAID_EVENT');
		}else{
			$event_price = JText::_('FREE_EVENT');
		}

		// Seats left
		if($row->max_attendance==0) {
			$seats_left			= JText::_('EVENTS_PLACES_UNLIMITED');
		}else {
			if($row->registered < $row->max_attendance){
				$seats_left	= JText::_('EVENTS_PLACES_LEFT').$row->avaliable;
			}else{
				$seats_left	= JText::_('EVENTS_PLACES_LEFT')."0";
			}
		}

		// Seats Registered
		$seats_filled	= JText::_('EVENTS_PLACES_FILLED').$row->registered;

		// Seats Maximum
		if($row->max_attendance==0){
			$seats_total = JText::_('EVENTS_PLACES_UNLIMITED');
		}else{
			$seats_total = JText::_('EVENTS_PLACES_TOTAL').$row->max_attendance;
		}

?>
		<div class="row-fluid row-<?php echo $rowclass; ?>">
			<div class="span12">

				<!-- Event Title -->
				<div style="display:inline">
					<?php
					$show_poster = trim($this->regproConfig['show_poster']) * 1;
					if($show_poster == 1) {
						include_once 'administrator/components/com_registrationpro/helpers/tools.php';
						$imgPrefixSystem = JURI::root() . "images/regpro/system/";
						$imgPrefixEvents = JURI::root() . "images/regpro/events/";
						$imgCurr = getImageName($row->id, $row->user_id);
						$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
						$imgName = '';
						if($row->image) {
							$tmpName = $imgPrefixEvents . $imgCurr;
							$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();
						}
						if($imgName != '') {
							echo "<div>\n";
							echo "<a href='".$imgName."'>\n";
							echo "<img border=0 title=\"Zoom\" id=\"event_img\" src=\"".$imgName."\" width=100 style=\"float:left;margin-right:15px;margin-bottom:10px;border:none;\">\n";
							echo "</a>\n";
							echo "</div>\n";
						}
					}
					?>
					<div class="listing_event_title">
						<a href="<?php echo $detail_link; ?>"><?php echo ucfirst($event_title); ?></a>
					</div>
					<div>
						&nbsp;<span class="smalllisting">in</span> <a class="label" <?php echo $background; ?> href="<?php echo $catgory_page_link; ?>"><?php echo ucfirst($event_cat_name); ?></a>
					</div>
				</div>

				<!-- Start Event Date -->
				<?php
					if($this->regproConfig['showeventdates'] == 1 || $this->regproConfig['showeventtimes'] == 1){
				?>

				<div class="smalllisting">
						<?php
							if($event_start_date != ""){
								echo $event_start_date;
								if($event_start_time != ""){
									echo "&nbsp;<span>".$event_start_time."</span>";
								}
							}else{
								if($event_start_time != ""){
									echo "&nbsp;".$event_start_time;
								}
							}

						?>

						<?php
							if($event_end_date != ""){
								echo '-';
								echo $event_end_date;
								if($event_end_time != ""){
									echo "&nbsp;<span>".$event_end_time."</span>";
								}
							}else{
								if($event_end_time != ""){
									echo '-';
									echo "&nbsp;".$event_end_time;
								}
							}
						?>
				</div>

				<?php
					}
				?>
				<!-- End Event Date -->

				<!-- Start Event Location -->
				<?php
				if($this->regproConfig['showlocationcolumn'] == 1){
				?>
				<div class="smalllisting">
					<?php echo JText::_('EVENTS_FRONT_VENUE')." : ".$location;  ?>
				</div>
				<?php
					}
				?>
				<!-- Event short Description -->
				<?php if($this->regproConfig['showshortdescriptioncolumn'] == 1){ ?>
				<div class="smalllisting">
					<?php echo $event_short_description; ?>
					<a href="<?php echo $detail_link ; ?>"> <span class="btn btn-primary"> <?php echo JText::_('READ_MORE'); ?> </span> </a>
				</div>
				<?php } ?>

				<div id="events_tr_bottom">
				<span class="pull-left">
				<?php $plugin_handler = new regProPlugins;				
			$res = $plugin_handler->getSocialsettings('events','bottom');
			//print_r($res);
			$pageurl = $_SERVER['REQUEST_URI'];
			$pageurl[0] = '';
			$pageurl = trim($pageurl);
			$pageurl = urlencode(JURI::root().$pageurl);
			$leftText = $res["share_text"];
			echo '<div>'.$leftText.'</div>';
			array_pop($res);
			echo '<div>';
			if(count($res) >0)
			{
				include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tools.php';
				$imgPrefixSystem = JURI::root() . "images/regpro/system/";
				$imgPrefixEvents = JURI::root() . "images/regpro/events/";
				 $imgCurr = getImageName($this->row->id, $this->row->user_id);
				if($row->image !== '0') {
					$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();;					
				} else {
					$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
				}
				$s_desc = strip_tags($this->row->shortdescription);
				foreach($res as $k=>$v)
				{
					switch($k){
						case "l_facebook" :
						// Facebook
							echo sprintf($v,$pageurl,urlencode($this->row->titel), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
						case "l_twitter" :
						// Twitter
							echo sprintf($v,$pageurl,urlencode($this->row->titel), urlencode($imgName),$pageurl,urlencode($s_desc) );
						break;
						case "l_linkedin" :
							// Linkedin
							echo sprintf($v,$pageurl,urlencode($this->row->titel),urlencode($s_desc));
						break;
						case "l_googlePlus" :
						// Google +
							echo sprintf($v,$pageurl,urlencode($this->row->titel), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
						default  :
							echo sprintf($v,$pageurl,urlencode($this->row->titel), urlencode($imgName),$pageurl,urlencode($s_desc));
						break;
					}
						
						
					
				}
			}
			echo '</div>';?>
				</span>
					<?php
					if($row->showattendance == 1) {
						if($row->max_attendance==0){

							// Show Max seats
							if($this->regproConfig['maxseat'] == 1){
							 ?>
									<span class="pull-right"><?php echo $seats_total; ?> </span>
									<span class="pull-right">|</span>
							 <?php
							}

							// Show filled seats
							if($this->regproConfig['registeredseat'] == 1){
							?>
									<span class="pull-right"><?php echo $seats_filled; ?> </span>
									<span class="pull-right">|</span>
							 <?php
							}
						}else{

							// show Avaliable seats
							if($this->regproConfig['pendingseat'] == 1){
							?>
									<span class="pull-right"><?php echo $seats_left; ?> </span>
									<span class="pull-right">|</span>
							<?php
							}
							// Show Max seats
							if($this->regproConfig['maxseat'] == 1){
							 ?>
									<span class="pull-right"><?php echo $seats_total; ?> </span>
									<span class="pull-right">|</span>
							 <?php
							}

							// Show filled seats
							if($this->regproConfig['registeredseat'] == 1){
							?>
									<span class="pull-right"><?php echo $seats_filled; ?> </span>
									<span class="pull-right">|</span>
							<?php
							}
						}
					}

					// show event price
					if($this->regproConfig['showpricecolumn'] == 1){
					?>
					<span class="pull-right"> <i class="icon-tags"></i><?php echo $event_price; ?> </span>
					<?php
					}
					?>
				</div>
			</div>
		</div>
<?php
	}

	if(count($this->rows) < 1){
?>
		<div style="text-align:center"><?php echo JText::_('NO_EVENT_RECORD'); ?></div>
<?php
	}
?>
<div class="pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="viewcache" value="0" />
<input type="hidden" name="task" value="" />
</form>
</div>
</div>


<?php $regpro_header_footer->regpro_footer($this->regproConfig);?>