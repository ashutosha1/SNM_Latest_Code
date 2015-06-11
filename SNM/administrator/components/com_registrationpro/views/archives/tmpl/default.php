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

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_LIST_EVENTS' ), 'events' );

//JToolBarHelper::unarchiveList('unarchive');
JToolBarHelper::spacer();
JToolBarHelper::spacer();
JToolBarHelper::cancel('archive_cancel');
JToolBarHelper::spacer();
//JToolBarHelper::help( 'screen.registrationpro', true );

//echo"<pre>";print_r($this->rows); exit;

$btnsCode = "<div id=\"toolbar-unarchive\" class=\"btn-wrapper\"><button class=\"btn btn-small\" onclick=\"unarchiveEvent();\"><span class=\"icon-archive\"></span>&nbsp;Unarchive</button></div>";
$btnsCode .= "<div class=\"btn-group spacer\"></div>";
$btnsCode .= "<div id=\"toolbar-delete\" class=\"btn-wrapper\"><button class=\"btn btn-small\" onclick=\"deleteEvent();\"><span class=\"icon-delete\"></span>&nbsp;Delete</button></div>";
?>

<script type="text/javascript">

$('#toolbar').append('<?php echo $btnsCode;?>');

function unarchiveEvent() {
	if(document.adminForm.boxchecked.value==0){
		alert('<?php echo JText::_('NO_CHECKS_IN_LIST');?>');
	} else {
		for (var i = 1; i < 10000; i++) {
			var cb = document.getElementById('cb' + i);
			if ((cb) && (!cb.checked)) {
				var $hasRepeats = false;
				for (var $a = 0; $a < 200; $a++) {
					var $ii = (i * 10000 + $a) * 1;
					var cba = document.getElementById('cb' + $ii);
					if (cba) {
						if (cba.checked) {
							$hasRepeats = true;
							break;
						}
					}
				}
				if($hasRepeats){
					alert('<?php echo JText::_('UNARCHIVE_EVENT_FROM_LIST_CHILDREN_ERROR');?>');
					break;
				}
			}
		}
		if(!$hasRepeats) Joomla.submitbutton('unarchive');
	}
}

function deleteEvent() {
	if(document.adminForm.boxchecked.value==0){
		alert('<?php echo JText::_('NO_CHECKS_IN_LIST');?>');
	} else {
		for (var i = 1; i < 10000; i++) {
			var cb = document.getElementById('cb' + i);
			if ((cb) && (cb.checked)) {
				var $hasRepeats = false;
				for (var $a = 0; $a < 200; $a++) {
					var $ii = (i * 10000 + $a) * 1;
					var cba = document.getElementById('cb' + $ii);
					if (cba) {
						if (!cba.checked) {
							$hasRepeats = true;
							break;
						}
					}
				}
				if($hasRepeats){
					alert('<?php echo JText::_('DELETE_ARCHIVED_EVENT_FROM_LIST_CHILDREN_ERROR');?>');
					break;
				}
			}
		}
		if(!$hasRepeats) Joomla.submitbutton('remove_archive');
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
<table class="table table-striped" cellspacing="1">
	<thead>
		<tr>
			<th width="4%"><?php echo JText::_( 'S.No.' ); ?></th>
			<th width="4%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="16%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_TITEL_LI_EV'), 'a.titel', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_DATE_START'), 'a.dates', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_DATE_END'), 'a.enddates', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="15%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CLUB_LI_EV'), 'a.locid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="16%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CAT_LI_EV'), 'a.catsid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="15%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CITY_LI_LO'), 'l.city', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		    <!--th width="6%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th-->
			<th width="10%" nowrap="nowrap">ID</th>
		</tr>
	</thead>

	<tbody class="ui-sortable">
		<?php
		$k = 0;
		$count = 1;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$count++;
			$row = &$this->rows[$i];
			$ii = $row->id;
			if(($row->parent_id > 0)&&($row->ordering >= 10000)) $ii = ($row->parent_id * 10000 + $count)*1;
			$checked = JHTML::_('grid.checkedout', $row, $ii );
   		?>
		<tr class="<?php echo "row$k"; ?> dndlist-sortable">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked;?></td>
			<td align="left"><?php echo htmlspecialchars($row->titel, ENT_QUOTES, 'UTF-8'); ?> </td>
			<td align="center"><?php echo $row->dates; ?> </td>
			<td align="center"><?php echo $row->enddates; ?></td>
			<td align="left"><?php echo htmlspecialchars($row->club, ENT_QUOTES, 'UTF-8');?></td>
			<td align="left"> <?php echo htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8'); ?> </td>
			<td align="left"><?php echo htmlspecialchars($row->city, ENT_QUOTES, 'UTF-8'); ?></td>

			<?php
				if(($row->parent_id > 0)&&($row->ordering >= 10000)){
					$iid = "<div style=\"color:#C00;font-style:bold;font-weight:700;\">Child of ".$row->parent_id."</div>";
				} else $iid = "<div style=\"color:#0C0;font-style:bold;font-weight:700;\">Main ".$row->id."</div>";
			?>
			<td align="center"><?php echo $iid; ?></td>
		</tr>
		<?php $k = 1 - $k;
		}
		?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="15">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

</table>
</div>
<div class="span10 regpro_footer">
	<?php $registrationproAdmin = new registrationproAdmin;	echo $registrationproAdmin->footer( ); ?>
</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="view" value="archives" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="events" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>