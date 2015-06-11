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

//JToolBarHelper::cancel();
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');

$rows = array();
$rows = $this->rows;

$pays = array();
$pays = $this->pays;

$regs = array();
$regs = $this->regs;

$total_events_in_db = count($rows);
$total_events_in_db_published = 0;
$total_tickets_sold = 0;
$total_tickets_sum = 0;
$repeating_events = 0;
$expired_events = 0;
$archived_events = 0;
$nearest = array();

$tran = array();
$tran = $this->tran;
$trans_count = count($tran);
$trans_sum = 0;
$disc = 0;
//echo "<pre>";
//print_r($trans);
foreach($tran as $row) {
	$trans_sum = $trans_sum + $row['price'];
	$disc = $disc + $row['discount_amount'];
}

$trans_sum = $trans_sum - $disc;

if($total_events_in_db > 0) {
	foreach($rows as $row) {
		if($row['published'] == '1') {
			$total_events_in_db_published++;
			$nearest[] = $row['id']."|".$row['titel']."|".$row['dates']."|".$row['enddates'];
		}
		if($row['published'] == '-1') $archived_events++;
	}
}

$registrationproHelper = new registrationproHelper;
$registrationproAdmin = new  registrationproAdmin;
$regpro_config = $registrationproAdmin->config();
$regpro_config['joomlabase'] = JPATH_SITE;
$show_total_income = true;

$today_regs = 0;
$today_tax = 0;
$today_income = 0;

$arr = array();

foreach($this->data as $key=>$value) {
	$regdt = $registrationproHelper->getFormatdate($regpro_config['formatdate'], $value->uregdate + ($regpro_config['timezone_offset']*60*60));
	$regtd = $registrationproHelper->getFormatdate($regpro_config['formatdate'], date('Y-m-d'));
	if (!array_key_exists($regdt, $arr)) {
		$arr[$regdt] = array();
		$arr[$regdt]['cnt'] = 0;
		$arr[$regdt]['tax'] = 0;
		$arr[$regdt]['price'] = 0;
	}
	if($regdt == $regtd) $today_regs++;
	$arr[$regdt]['cnt']++;
	$arr[$regdt]['tax'] = $arr[$regdt]['tax'] + $value->tax_amount * 1;
	if((($value->final_price)*1) == 0) {
		$arr[$regdt]['price'] = $arr[$regdt]['price'] + $value->price;
	} else {
		$arr[$regdt]['price'] = $arr[$regdt]['price'] + $value->final_price;
	}
}

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// IMPORTANT! KEEP THIS UPDATED!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$REGPRO_VERSION1 = '3';
$REGPRO_VERSION2 = '1';
$REGPRO_VERSION3 = '1';

?>

	<script>$("#toolbar").parent().parent().parent().remove();</script>
	<!--script type="text/javascript">
	var regv1 = <?php //echo $REGPRO_VERSION1;?>;
	var regv2 = <?php //echo $REGPRO_VERSION2;?>;
	var regv3 = <?php //echo $REGPRO_VERSION3;?>;
	jQuery.ajax({
		url:"http://www.joomlashowroom.com/additional/regproupdater.php?check_version=1",
		dataType: 'jsonp',
		success:function(json){
			 if(json != '') {
				var ver = json.split("|"); 
				var vv = ver[0].split("."); 
				if ((regv1 < (vv[0]*1))||
					(regv2 < (vv[1]*1))||
					(regv3 < (vv[2]*1))) {
					window.open('http://www.joomlashowroom.com/additional/popups/newversion.html', 'New Version of Event Registration Pro AVAILABLE!', 'width=680, height=500, toolbar=0, menubar=0, location=0');  
					var ww = jQuery(window).width();
					if (ww > 1200) jQuery('#banner_right').css("display","inherit");
				}
			 }
		},
		error:function(){
			alert("Error");
		}      
	});
	</script-->
<style>
	.subhead-collapse,.btn-subhead{
		display: none !important;
	}
</style>
<div class="span10 y-offset">
	<h1 class="dashboard-title">
		<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_TITLE');?>
	</h1>
</div>
<div class="span10 y-offset">
	<div class="row-fluid y-offset">
		<div class="span9 y-offset">
			<div id="sidebar_top" class="well">
				<form name="adminForm" action="index.php?option=com_registrationpro" method="post">
					<?php
						$versioninfo = "";
						$className = 'registrationproHelper';
						if(class_exists($className)) {
							$registrationproHelper = new $className;
							if($registrationproHelper) {
								$versioninfo =  $registrationproHelper->getInfo();
								if($versioninfo && is_array($versioninfo) && count($versioninfo) > 0) {
									if($versioninfo['version_status'] == -1) {
										echo "<p><img src='".REGPRO_ADMIN_IMG_PATH."/upgrade1.png' border=0 />&nbsp;";
										echo JText::_('PRODUCT_VERSION_UPDATE_TEXT').'<a href="http://www.joomlashowroom.com" target="_blank">';
										echo '&nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-success" value="'.JText::_('PRODUCT_VERSION_UPDATE').'"/></a></p>';
										echo "<p class='text-center'>".JText::_('PRODUCT_INSTALLED_VERSION')." : ".$versioninfo['version_installed'];
										echo "<br/>".JText::_('PRODUCT_AVALIABLE_VERSION')." : ".$versioninfo['version_latest']."</p>";
									}else{
										echo "<p>".JText::_('PRODUCT_VERSION_UPTODATE')."<img style='margin-left:5px;margin-top:-5px;' src='".REGPRO_ADMIN_IMG_PATH."/tick.png' border='0' align='middle' /></p>";
										echo "<p class='text-center'>".JText::_('PRODUCT_INSTALLED_VERSION')." : ".$versioninfo['version_installed']."";
										echo "<br />".JText::_('PRODUCT_AVALIABLE_VERSION')." : ".$versioninfo['version_latest']."</p>";
									}
								}
							}
						}
					?>
					<input type="hidden" name="option" value="com_registrationpro" />
					<input type="hidden" name="controller" value="registrationpro"/>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
				</form>
			</div>
			
			<div class="span12 no-gutter y-offset">
				<?php
					$stat_chart_type = JRequest::getVar('stat_chart_type', 0);
					$stat_chart_period = JRequest::getVar('stat_chart_period', 0);
					if($stat_chart_period == 0) $chp = 'Today';
					if($stat_chart_period == 1) $chp = 'Current Month';
					if($stat_chart_period == 2) $chp = 'Last 30 Days';
					if($stat_chart_period == 3) $chp = 'Current Year';
				?>
				<div class="span4">
					<h4 class="dashboard-statistics-header">
						<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_STATISTICS');?>
						<?php echo $chp;?>
					</h4>
				</div>
				<div class="span8 no-gutter pull-right">
					<form name="stat_chart">
						<select class="inputbox dashborad-select-element" size="1" id="stat_chart_type" name="stat_chart_type">
							<option value="0" <?php if($stat_chart_type == 0) {echo "selected"; $cht = 'LineChart';}?>>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_LINE_CHART');?>
							</option>
							<option value="1" <?php if($stat_chart_type == 1) {echo "selected"; $cht = 'AreaChart';}?>>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_AREA_CHART');?>
							</option>
							<option value="2" <?php if($stat_chart_type == 2) {echo "selected"; $cht = 'SteppedAreaChart';}?>>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_STEPPED_AREA_CHART');?>
							</option>
						</select>
						<select class="inputbox dashborad-select-element" size="1" id="stat_chart_period" name="stat_chart_period">
							<option value="1" <?php if($stat_chart_period == 1) echo "selected";?>>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_STEPPED_CURRENT_MONTH');?>
							</option>
							<option value="3" <?php if($stat_chart_period == 3) echo "selected";?>>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_STEPPED_YEAR_TO_DATE');?>
							</option>
						</select>
						<input type="hidden" name="option" value="com_registrationpro" />
						<input type="hidden" name="stat_chart" value="1" />
					</form>
				</div>
				<script type="text/javascript">
				$('#stat_chart_type').change(function(e) {document.stat_chart.submit();});
				$('#stat_chart_period').change(function(e) {document.stat_chart.submit();});
				</script>
				<div class="clearfix"></div>
			</div>
			
			<div class="span12 no-gutter y-offset">
				<?php if(count($arr)>0) { ?>
					<div id="chart_div"></div>
				<?php } else { ?>
					<div class="statistics-error">
						<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_NO_STATISTICS');?>
					</div>
				<?php } ?>
				<script type="text/javascript" src="https://www.google.com/jsapi"></script>
				<script type="text/javascript">
					Array.prototype.reduce = undefined;
					google.load("visualization", "1", {packages:["corechart"]});
					google.setOnLoadCallback(drawChart);

					function drawChart() {
						var data = google.visualization.arrayToDataTable([
							<?php
								if ($show_total_income) {
									$header = "['Date', 'Number of Registrants', 'Tax Amount', 'Daily Amount', 'Total Registrants', 'Total Income'],";
								} else {
									$header = "['Date', 'Number of Registrants', 'Tax Amount', 'Daily Amount', 'Total Registrants'],";
								}
								echo $header."\n";
								$total_q = 0;
								$total_p = 0;
								$i = 0;
								$len = count($array);
								foreach($arr as $key=>$val) {
									$total_q = $total_q + $val['cnt'];
									$total_p = $total_p + $val['price'];
									if ($show_total_income) {
										$row = "['".$key."', ".$val['cnt'].", ".$val['tax'].", ".$val['price'].", ".$total_q.", ".$total_p."]";
									} else {
										$row = "['".$key."', ".$val['cnt'].", ".$val['tax'].", ".$val['price'].", ".$total_q."]";
									}
									echo $row;
									if ($i != $len - 1) echo ",";
									echo "\n";
									$i++;
								}
							?>
						]);
						var options = {
						legend: { position: 'bottom' },
							hAxis: {textStyle: {color: 'black', fontSize: 10}},
							vAxis: {textStyle: {color: 'black', fontSize: 10}},
							chartArea:{left:50,top:10,width:"100%",height:"65%"}
						};
						var chart = new google.visualization.<?php echo $cht;?>(document.getElementById('chart_div'));
						chart.draw(data, options);
					}
				</script>
			</div>
			<div class="span6 no-gutter pull-left">
				<div class="span12 pull-left no-gutter y-offset">
					<table id="table_dash">
						<tr>
							<td id="title" colspan=20>
								<?php echo JText::sprintf('COM_REGISTRATIONPRO_DASHBOARD_TODAYS_STATISTICS',$regtd);?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_REGISTRANTS');?>
							</td>
							<td>
								<?php echo $today_regs;?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_TAX_AMOUNT');?>
							</td>
							<td>
								<?php echo $today_tax;?>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_TODAYS_INCOME');?></td>
							<td><?php echo $today_income;?></td>
						</tr>
					</table>
				</div>
				<div class="span12 pull-left y-offset no-gutter">
					<table id="table_dash">
						<tr>
							<td id="title" colspan=20>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_COMMON_INFORMATION');?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_TOTAL_EVENTS_IN_SYSTEM');?>
							</td>
							<td>
								<?php echo $total_events_in_db;?>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_PUBLISHED_EVENTS');?></td>
							<td><?php echo $total_events_in_db_published;?></td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_ARCHIVE_EVENTS');?>
							</td>
							<td>
								<?php echo $archived_events;?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_TOTAL_TRANSACTION_COUNT');?>
							</td>
							<td>
								<?php echo $trans_count;?>
							</td>
						</tr>
					</table>
				</div>
				<div class="span12 pull-left no-gutter y-offset">
					<table id="table_dash">
					<tr>
						<td id="title" colspan=20>
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_LAST_TRANSACTION');?>
						</td>
					</tr>
					<tr>
						<td id="header">#/#</td><td id="header">
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_DATE_TIME');?>
						</td>
						<td id="header">
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_EVENT');?>
						</td>
						<td id="header">
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_ITEM_NAME');?>
						</td>
						<td id="header">
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_STATUS');?>
						</td>
						<td id="header">
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_PRICE');?>
						</td>
					</tr>
					<?php
						if(count($tran) > 0) {
							$cnt = 1;
							foreach($tran as $tr) {
								$evnt = '';
								$eid = 0;
								$boo = false;
								foreach($pays as $pay) {
									if($pay['id'] == $tr['p_id']) {
										$eid = $pay['regpro_dates_id'];
										$boo = true;
										break;
									}
								}
								if(!$boo) {
									foreach($regs as $reg) {
										if($reg['rid'] == $tr['reg_id']) {
											$eid = $reg['rdid'];
											break;
										}
									}
								}
								
								foreach($rows as $row) {
									if($row['id'] == $eid) {
										$evnt = $row['titel'];
										break;
									}
								}
								
								if(trim($evnt) == '') {
									$href = "Event Title not found";
								} else {
									$href = "<a href=\"index.php?option=com_registrationpro&controller=events&task=edit&cid[]=".$eid."\">".$evnt."</a>";
								}
								
								echo "<tr><td>$cnt</td><td>".$tr['payment_date']."</td><td>".$href."</td><td>".$tr['item_name']."</td><td>".$tr['payment_status']."</td>";
								if($tr['price'] != 0)
								{
									echo "<td>".($tr['price'] - $tr['event_discount_amount'])."</td></tr>";
								}else{
									echo "<td>".$tr['price']."</td></tr>";
								}
								$cnt++;
								if($cnt == 11) break;
							}
						}else{
							echo "<tr><td colspan=20>No Transactions found...</td></tr>";
						}
					?>
					</table>
				</div>
			</div>
			<div class="span5 y-offset">
				<table id="table_dash">
					<tr>
						<td id="title" colspan=20>
							<?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_NEXT_TEN_EVENTS');?>
						</td>
					</tr>
					<tr>
						<td id="header">#/#</td>
						<td id="header"><?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_EVENT_ID');?></td>
						<td id="header"><?php JText::_('COM_REGISTRATIONPRO_DASHBOARD_EVENT_TITLE');?></td>
						<td id="header"><?php echo JText::_('COM_REGISTRATIONPRO_DASHBOARD_EVENT_DATES');?></td>
					</tr>
					<?php
						$registrationproHelper = new registrationproHelper;
						$registrationproAdmin = new registrationproAdmin;
						$regpro_config = $registrationproAdmin->config();
						
						if(count($nearest) > 0) {
							$cnt = 1;
							foreach($nearest as $evnt) {
								list($eid, $ettl, $eds, $edf) = explode('|', $evnt);							
								jimport('joomla.utilities.date');
								$dtmp = new JDate($eds); $deds = $dtmp->format($regpro_config['formatdate']);
								$dtmp = new JDate($edf); $dedf = $dtmp->format($regpro_config['formatdate']);
								$dd = $deds." -<br />".$dedf;
						
								echo "<tr><td>$cnt</td><td>$eid</td><td><a href=\"index.php?option=com_registrationpro&controller=events&task=edit&cid[]=$eid\">$ettl</a></td><td>$dd</td></tr>";
								$cnt++;
								if($cnt == 11) break;
							}
						} else echo "<tr><td colspan=20>".JText::_('COM_REGISTRATIONPRO_DASHBOARD_NO_EVENT_FOUND')."</td></tr>";
					?>
				</table>
			</div>
			
			
		</div>	<!-- Closing the span8 div -->
		<div class="span3 y-offset regpro-logo-image">
			<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/cpanel_logo_dash.jpg" border="0" align="middle" class="regpro-images" /> <br/><br/>
			<form name="adminForm" action="index.php?option=com_registrationpro" method="post">
				<?php
					$versioninfo = "";
					$className = 'registrationproHelper';
					if(class_exists($className)) {
						$registrationproHelper = new $className;
						if($registrationproHelper) {
							$versioninfo =  $registrationproHelper->getInfo();
							if($versioninfo && is_array($versioninfo) && count($versioninfo) > 0) {
								if($versioninfo['version_status'] == -1) {
									echo "<p class='text-center'><img src='".REGPRO_ADMIN_IMG_PATH."/upgrade1.png'border='0'/><br/>".JText::_('PRODUCT_VERSION_UPDATE_TEXT');
									echo '<a href="http://www.joomlashowroom.com" target="_blank" class="btn btn-success btn-mini">';
									echo JText::_('PRODUCT_VERSION_UPDATE').'</a></p>';
									echo '<p>'.JText::_('PRODUCT_INSTALLED_VERSION');
									echo '<span class="badge badge-important pull-right">'.$versioninfo['version_installed'].'</span></p>';
									echo '<p>'.JText::_('PRODUCT_AVALIABLE_VERSION');
									echo '<span class="badge badge-warning pull-right">'.$versioninfo['version_latest'].'</span></p>';
								}else{
									echo '<p>'.JText::_('PRODUCT_VERSION_UPTODATE');
									echo "<img src='".REGPRO_ADMIN_IMG_PATH."/tick.png' border='0' class='pull-right' /></p>";
									echo '<p>'.JText::_('PRODUCT_INSTALLED_VERSION');
									echo '<span class="badge badge-success pull-right">'.$versioninfo['version_installed'].'</span></p>';
									echo '<p>'.JText::_('PRODUCT_AVALIABLE_VERSION');
									echo '<span class="badge badge-info pull-right">'.$versioninfo['version_latest'].'</span></p>';
								}
							}
						}
					} else {
						echo "<p class='text-center text-error'>Version Checker Error!";
						echo "<br/>RegistrationPro Helper Not Found!</p>";					
					}
				?>
					<input type="hidden" name="option" value="com_registrationpro" />
					<input type="hidden" name="controller" value="registrationpro"/>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
			</form>
			<?php 
				if ((!isset($total_events_in_db))||(($total_events_in_db == ''))||($total_events_in_db <= 0)) { 
					$option = JRequest::getCmd('option');
					$link = 'index.php?option='.$option.'&amp;controller=commons&amp;task=SampleData';
					echo "<a href=\"$link\" title=\"Add Sample Data into system's database\" class='btn btn-success'><b>Add Sample Data into system</b></a>";
				} 
			?>
			<!--a href="http://www.joomlashowroom.com" title="New Version Available!" target=_blank>
				<img src="http://www.joomlashowroom.com/additional/popups/banner_right.jpg" id="banner_right" style="display:none;">
			</a-->			
			<script type="text/javascript">
				var ww = $(window).width();
				if((ww > 0)&&(ww < 1200)){
					$('#sidebar_right').css("display","none");
					$('#sidebar_top').css("display","line");
				} else {
					$('#sidebar_right').css("display","inherit");
					$('#sidebar_top').css("display","none");
				}
			</script>
		</div>		<!-- Closing the aside div -->
		<div class="span12 no-gutter y-offset regpro-footer">
			<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer( );?>
		</div>
	</div>
</div>		<!-- Closing the span10 div -->

