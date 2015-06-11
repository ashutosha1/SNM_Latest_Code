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
JHtml::_('formbehavior.chosen', 'select');
//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_LIST_EVENTS' ), 'events' );
JToolBarHelper::addNew();
JToolBarHelper::spacer();
JToolBarHelper::editList();
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::spacer();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
//JToolBarHelper::spacer();
//JToolBarHelper::archiveList();
JToolBarHelper::spacer();
JToolBarHelper::custom( 'event_excel', 'copy.png', 'xml.png', 'Events Report', 0, 1);
JToolBarHelper::spacer();
JToolBarHelper::help( 'screen.registrationpro', true );
JToolBarHelper::spacer();

$ordering = ($this->lists['order'] == 'c.ordering' || $this->lists['order'] == 'a.ordering');

$btnsCode = "<div id=\"toolbar-archive\" class=\"btn-wrapper\"><button class=\"btn btn-small\" onclick=\"archiveEvent();\"><span class=\"icon-archive\"></span>&nbsp;Archive</button></div>";
$btnsCode .= "<div class=\"btn-group spacer\"></div>";
$btnsCode .= "<div id=\"toolbar-delete\" class=\"btn-wrapper\"><button class=\"btn btn-small\" onclick=\"deleteEvent();\"><span class=\"icon-delete\"></span>&nbsp;Delete</button></div>";
?>

<script type="text/javascript">

$('#toolbar').append('<?php echo $btnsCode;?>');

function archiveEvent() {
	if(document.adminForm.boxchecked.value==0){
		alert('<?php echo JText::_('NO_CHECKS_IN_LIST');?>');
	} else {
		for (var i = 0; i < 200; i++) {
			var cb = document.getElementById('cb' + i);
			if ((cb) && (cb.checked)) {
				var $hasRepeats = false;
				for (var $a = 0; $a < 200; $a++) {
					var $ii = ((i + 1) * 1000 + $a) * 1;
					var cba = document.getElementById('cb' + $ii);
					if (cba) {
						if (!cba.checked) {
							$hasRepeats = true;
							break;
						}
					}
				}
				if($hasRepeats){
					alert('<?php echo JText::_('ARCHIVE_EVENT_FROM_LIST_CHILDREN_ERROR');?>');
					break;
				}
			}
		}
		if(!$hasRepeats) Joomla.submitbutton('archive');
	}
}

function deleteEvent() {
	if(document.adminForm.boxchecked.value==0){
		alert('<?php echo JText::_('NO_CHECKS_IN_LIST');?>');
	} else {
		for (var i = 0; i < 200; i++) {
			var cb = document.getElementById('cb' + i);
			if ((cb) && (cb.checked)) {
				var $hasRepeats = false;
				for (var $a = 0; $a < 200; $a++) {
					var $ii = ((i + 1) * 1000 + $a) * 1;
					var cba = document.getElementById('cb' + $ii);
					if (cba) {
						if (!cba.checked) {
							$hasRepeats = true;
							break;
						}
					}
				}
				if($hasRepeats){
					alert('<?php echo JText::_('DELETE_EVENT_FROM_LIST_CHILDREN_ERROR');?>');
					break;
				}
			}
		}
		if(!$hasRepeats) Joomla.submitbutton('remove');
	}
}

</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="search_div" class="span10">
	<div class="span8">
		<?php
			echo '<b class="x-offset">'.JText::_('COM_REGISTRATIONPRO_SEARCH_IN').'</b>'.$this->lists['filter'];
		?>
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="input-medium search-query" onChange="document.adminForm.submit();" />
		<button onclick="this.form.submit();" class="btn">
			<?php echo JText::_('Search');?>
		</button>
		<button onclick="this.form.getElementById('search').value='';this.form.submit();"class="btn">
			<?php echo JText::_('COM_REGISTRATIONPRO_RESET'); ?>
		</button>	
	</div>
	<div class="span2 no-gutter pull-right">
		<?php echo '<span class="pull-right">'.$this->lists['state'].'</span>'; ?>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="span10">
	<div class="row-fluid">
		<div class="span12">
			<table class="table table-striped events_table">
				<thead>
					<tr id="table_events_header">
						<th>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="hidden-phone hidden-tablet">
							<?php echo JText::_('COM_REGISTRATIONPRO_EVENT_DISPLAY_COLUMN_IMAGE');?>
						</th>
						<th>
							<?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_TITEL_LI_EV'), 'a.titel', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th>
							<?php echo JHTML::_('grid.sort', JText::_('Dates'), 'a.dates', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th>
							<?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CLUB_LI_EV'), 'a.locid', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th class="hidden-phone">
							<?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CAT_LI_EV'), 'a.catsid', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th>
							<?php echo JText::_( 'ADMIN_EVENT_LIST_TICKETS_COLUMN' ); ?>
						</th>
						<th class="hidden-phone">
							<?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_STATUS'), 'a.status', $this->lists['order_Dir'], $this->lists['order'] ); ?>
						</th>
						<th><?php echo JText::_('COM_REGISTRATIONPRO_EVENT_DISPLAY_COLUMN_PUBLISHED');?></th>
						<th class="hidden-phone">
							<?php if ($ordering) {?>
								<a href="javascript: saveorder( <?php echo count( $this->rows )-1; ?> )">
									<img src="components/com_registrationpro/assets/images/filesave.png" border="0" width="16" height="16" alt="Save Order" align="middle" />
								</a>
							<?php 
								} echo JHTML::_('grid.sort', JText::_( 'ADMIN_LIST_ORDER' ), 'a.ordering', $this->lists['order_Dir'], $this->lists['order'] );
							?>
						</th>
						<th>
							<?php 
								echo JHTML::_('grid.sort', JText::_( 'COM_REGISTRATIONPRO_EVENT_ID' ), 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); 
							?>
						</th>
					</tr>
				</thead>
				<tbody class="ui-sortable">
					<?php
						$main_order_cnt = 0;
						$kids = array();
						$rows = array();
						$maxKey = 0; 
						foreach($this->rows as $key => $obj) {
								$rows[] = $this->rows[$key];
						}
						$maxKey++;

						//echo "<pre>";print_r($rows);echo "</pre>";
		
						$k = 0;
						for ($i=0, $n = count($rows); $i < $n; $i++) {
							$row = $rows[$i];
							
							if($row && isset($row)) {
							$link = 'index.php?option=com_registrationpro&amp;controller=events&amp;task=edit&amp;cid[]='. $row->id;
							$checked = JHTML::_('grid.checkedout', $row, $i );
							$published = JHTML::_('grid.published', $row, $i );
						?>
						<tr class="<?php echo "row$k"; ?>  dndlist-sortable">
							<td style="text-align:center;" id="checks_column">
								<?php echo $checked;?>
							</td>
							<td class="hidden-phone hidden-tablet">
								<?php
									include_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tools.php';
									$imgPrefixSystem = JURI::root() . "images/regpro/system/";
									$imgPrefixEvents = JURI::root() . "images/regpro/events/";
									$imgCurr = getImageName($row->id, $row->user_id);
									if($row->image !== '0') {
										$imgName = $imgPrefixEvents . $imgCurr . getUniqFck();;					
									} else {
										$imgName = $imgPrefixSystem . "noimage_200x200.jpg".getUniqFck();
									}

									echo "<img class=\"editlinktip hasTip thumbnail\" title=\"<img src='".$imgName."'>\" id=\"event_img\" src=\"".$imgName."\" width=60>\n";

									$short_descr = trim($row->shortdescription);
									if (strlen($short_descr) > 200) $short_descr = substr($short_descr, 0, 200) . " ...";

									$kids_count = count($kids[$row->id]);
								?>
							</td>
							<td>
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_EVENT' );?>::<?php echo $row->titel; ?>">
									<a href="<?php echo $link; ?>">
										<?php echo htmlspecialchars(stripslashes($row->titel), ENT_QUOTES, 'UTF-8'); ?>
									</a>
								</span>
								<br/>
								<div id="events_shortdescription">
									<?php echo $short_descr; ?>
									<br/>
									<?php
										if($row->published > 0 && $row->status < 5)
										{
									?>
											<a href="<?php echo JURI::root().'index.php?option=com_registrationpro&view=event&did='.$row->id;?>" class="btn btn-success btn-mini" target="_blank">
												Preview
											</a>
									<?php
										}
									?>
								</div>
							</td>
							<td style="text-align:center;line-height:14px;">
								<?php
								   $rowid = $row->id;
									$registrationproHelper = new registrationproHelper;
									$dt_start = $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->dates);
									$dt_end = $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->enddates);
									if($dt_start !== $dt_end) 
									{
										echo $star.$registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->dates);
										echo "<br/>-<br/>";
										echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->enddates);
									}else{
										echo $dt_start;
									}
									if($row->total_kids > 0)
									{
										echo "<br/><a id=\"show_kids_$rowid\" class=\"btn btn-small btn-success btn-more text-center\" href=\"javascript:showKids($rowid);\">".$row->total_kids." more</a>";
									}
								?>
							</td>
							<td style="text-align:center;"><?php echo htmlspecialchars($row->club, ENT_QUOTES, 'UTF-8');?></td>
							<td style="text-align:center;" class="hidden-phone"><?php echo htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8'); ?></td>
							<td id="tickets_column" style="text-align:center;">
								<?php
									$sales = 0;
									if(!empty($row->tickets)){
										echo "<table id='eventTicketsShow'>\n";
										echo "<tr>\n";
											echo "<th>".JText::_('ADMIN_EVENT_LIST_TICKETS_NAME_COLUMN')."</th>\n";
											echo "<th width=45 style=\"text-align:center;\">".JText::_('ADMIN_EVENT_LIST_SOLD_COLUMN')."</th>\n";
										echo "</tr>\n";
										foreach($row->tickets as $key=>$val) {
											$clr = '';
											$prod = "Tickets";
											if (trim($val->type) == "A") {
												$clr = ' id="product_row"';
												$prod = "Products";
											}
											echo "<tr valign=top".$clr.">\n";
												echo "<td valign=top>".$val->product_name."</td>\n";
												$tickets_sold = $val->product_quantity_sold * 1;
												if($val->product_quantity != 0){
													$tickets_left = ($val->product_quantity * 1) - ($val->product_quantity_sold * 1);
												} else {
													$tickets_left = "<span id='infinity_symbol'>&infin;</span>";
												}
												echo "<td style=\"text-align:center;color:#06a;vertical-align:top;\" valign=top>\n";
												echo "<span class=\"editlinktip hasTip\" title=\"$prod Sold : ".$tickets_sold."<br />$prod Left : ".$tickets_left."\">".$tickets_sold." / ".$tickets_left."</span>\n";
												echo "</td>\n";
											echo "</tr>\n";
											if($val->product_quantity_sold != 0) $sales += $val->product_quantity_sold * $val->total_price;
										}
										
										echo "<tr><td colspan=2 style=\"background-color:#ccc;padding:0px;margin:0px;height:0px;\"></td></tr>";
										echo "<tr><td stye=\"text-align:left;\">";
										echo "<div style=\"margin-bottom:6px;font-style:bold;font-weight:700;margin-top:2px;\">Total: ".$this->regpro_config['currency_sign'].' '.$row->sales."</div>\n";

										$eobj =$this->getModel();
										$nrregusers_array = $eobj->getRegistered($row->id);
										$nrregusers = 0;
										$text = '';
										$nrstatus = array();

										foreach ($nrregusers_array as $pid => $qty){
											if(is_int($qty)) $nrregusers += $qty;
											else $nrstatus = $qty;
										}

										if(!empty($nrstatus)){
											foreach($nrstatus as $status => $qty){
												$text .= JText::_('ADMIN_EVENTS_REGISTRATION_STATUS_'.$status) . ': '.$qty.'<br/>';
											}
											$text .= JText::_('ADMIN_EVENTS_REGISTRATION_STATUS_TOTAL'). ': '.$nrregusers. (($row->max_attendance!=0) ? ' / '.$row->max_attendance : '');
										}
										$linkreg = 'index.php?option=com_registrationpro&view=users&rdid='.$row->id.'&hidemainmenu=1';
										if ((trim($text) != '')&&(trim($linkreg) != ''))
										echo "<a href=\"$linkreg\" title=\"Edit Users\">$text</a>\n";
										echo '</td>';
								?>
										<td style="text-align:center;" align=center>
											<?php if ($row->registra == 1) { ?>
												<span class="editlinktip hasTip" title="Add new user">
													<a href="index.php?option=com_registrationpro&view=newuser&hidemainmenu=1&&did=<?php echo $row->id;?>">
													<img src="components/com_registrationpro/assets/images/icon_events_add.png" width=16 height=16 border=0 />
													</a>
												</span>
											<?php } else { ?>
												<span class="editlinktip hasTip" title="Registration disabled">
													<img src="components/com_registrationpro/assets/images/icon_events_noreg.png" width=16 height=16 border=0 />
												</span>
										</td>
											<?php
											}
										echo "</tr>\n";
										echo "</table>\n";
								}else{
									echo "<div id=\"no_tickets\" style=\"line-height:15px;margin-bottom:5px;\">There are no tickets assigned to this event</div>\n";
									if ($row->registra == 1) { ?>
									<span class="editlinktip hasTip" title="Add new user">
										<a href="index.php?option=com_registrationpro&view=newuser&hidemainmenu=1&&did=<?php echo $row->id;?>">
										<img src="components/com_registrationpro/assets/images/icon_events_add.png" width=16 height=16 border=0 />
										</a>
									</span>
									<?php } else { ?>
									<span class="editlinktip hasTip" title="Registration disabled">
										<img src="components/com_registrationpro/assets/images/icon_events_noreg.png" width=16 height=16 border=0 />
									</span>
									<?php
									}
								}
							?>
						</td>

						<?php
							$clr = "#080";
							if(($row->status == 1)||($row->status == 2)) $clr = "#800";
							echo "<td class='hidden-phone'style=\"text-align:center;color:$clr\"><b>".JText::_('ADMIN_EVENTS_STATUS_'.$row->status)."</b></td>\n";
						?>

						<td class="text-center">
							<?php
								if(!$row->moderating_status){
									$task = 'publish';
									$img = 'moderation.png';
									$alt = 'Need Moderation';
								} else {
									$task = $row->published ? 'unpublish' : 'publish';
									$img = $row->published ? 'publish_g.png' : 'publish_x.png';
									$alt = $row->published ? 'Published' : 'Unpublished';
									if($alt == 'Published'){
										$class = 'btn btn-micro active hasTooltip';
									} else $class = 'btn btn-micro hasTooltip';
								}
							?>
							<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" class="<?php echo $class;?>">
								<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/<?php echo $img;?>" width="16px" height="16px" border="0" title="<?php echo $alt;?>" alt="<?php echo $alt;?>" />
							</a>
						</td>

						<td class="order hidden-phone"style="text-align:center;margin:0px;padding:7px;">
							<table id="table_order">
							<?php
							  //echo '<pre>'; print_r ($this->pageNav); die;
								$upBtn = $this->pageNav->orderUpIcon( $i, ($row->catsid == $this->rows[$i-1]->catsid), 'orderupevents', 'Move Up', $ordering);
								if(strlen($upBtn) > 20) { echo "<tr><td>".$upBtn."";}
								if($i >=0) {
									$dnBtn = $this->pageNav->orderDownIcon( $i, $n, ($row->catsid == $this->rows[$i+1]->catsid), 'orderdownevents', 'Move Down', $ordering );
									if(strlen($dnBtn) > 20) { echo "".$dnBtn."</td></tr>";}
								}
							?>
							<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
							<tr>
								<td >
									<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align:center;width:25px;margin:0px;padding:2px;" />
								</td>
							</tr>
							<?php
								
							?>
							</table>
						</td>
						<td>
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php
						$k = 1 - $k;
						}
					}
					?>
					
			</tbody>
		</table>
			
		</div>
		<div class="span12 no-gutter text-center">
			<?php
				$foo = @$this->pageNav->getListFooter();
				if(!empty($foo)){
					echo $foo;
				}
			?>
		</div>
	</div> <!-- Closing the div of row-fluid -->
</div>
 <!--added by sushil on 4-09-2014 -->
<script language="javascript">

function showKids(rowid) {
var totalChild = '';
jQuery.ajax({
  url: "index.php?option=com_registrationpro&controller=events",
  type :"POST",
  data:{
	    task:"getChildEvents",pid:rowid
		},
  beforeSend: function() {
		totalChild = jQuery('#show_kids_'+rowid).html();
         jQuery('#show_kids_'+rowid).html(' <img id="loading-image" src="components/com_registrationpro/assets/images/1.gif"/>');
   },
  success:function(msg){
		jQuery('#show_kids_'+rowid).closest("tr").after(msg);
		 
		var htmlOld = totalChild;
		//jQuery('#show_kids_'+rowid).html('');
		jQuery('#show_kids_'+rowid).html('<?php echo JText::_('COM_REGISTRATIONPRO_ADMIN_EVENT_HIDE_CHILD_EVENTS');?>').attr('href','javascript:removeKids('+rowid+', "'+htmlOld+'");');
		var JTooltips = new Tips($$('.hasTip'),
       { maxTitleChars: 50, fixed: false}); 
	
	}
}); 
}

function removeKids(rowid , htmlOld){
	jQuery('[id^="kidrow_'+rowid+'_"]').remove();
	jQuery('#show_kids_'+rowid).html( htmlOld ).attr('href','javascript:showKids('+rowid+');');
}

</script>
<div class="span10 y-offset regpro-footer">
	<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer( ); ?>
</div>


<?php echo JHTML::_( 'form.token' ); ?>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_registrationpro" />
<input type="hidden" name="view" value="events" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="events" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

</form>