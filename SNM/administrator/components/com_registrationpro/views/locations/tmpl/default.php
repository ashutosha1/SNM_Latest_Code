<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright � 2009 - All rights reserved.
* @license  	GNU/GPL		
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('formbehavior.chosen', 'select');
//echo"<pre>";print_r($this->rows); exit;
?>

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
				<th width="2%"><?php echo JText::_( 'S.No.' ); ?></th>
				<th><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CLUB_LI_LO'), 'l.club', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CLUBHOME_LI_LO'), 'l.url', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>			
				<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CITY_LI_LO'), 'l.city', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_PUBLISH_LI_EV'), 'l.publishedloc', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
				<th> 
					<a href="javascript: saveorder( <?php echo count( $this->rows )-1; ?> )">
						<img src="components/com_registrationpro/assets/images/filesave.png" border="0" width="16" height="16" alt="Save Order" align="middle" />
					</a> 
					<?php echo JHTML::_('grid.sort', JText::_( 'ADMIN_EVENTS_ORDER_LI_LO' ), 'l.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>											
				<th nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'l.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			</tr>
		</thead>
	
	<tbody class="ui-sortable">
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
			
			$link 		= 'index.php?option=com_registrationpro&amp;controller=locations&amp;task=edit&amp;cid[]='. $row->id;
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i );
			//$access 	= JHTML::_('grid.access',   $row, $i );
			
			//$ordering = ($this->lists['ordering'] == 'l.ordering');
			//$badge = $this->settings->tbadge_folder.$row->tbadge;
   		?>
		<tr class="<?php echo "row$k"; ?> dndlist-sortable">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked;?></td>			
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_LOCATION' );?>::<?php echo $row->club; ?>">
					<a href="<?php echo $link; ?>"> <?php echo htmlspecialchars($row->club, ENT_QUOTES, 'UTF-8'); ?> </a>
				</span>			
			</td>
			<td align="left"><?php echo $row->url; ?> </td>
			<td align="left"><?php echo $row->city; ?></td>
			<td align="center" style="text-align:center;">
				<?php
					$task = $row->publishedloc ? 'unpublish' : 'publish';
					$img = $row->publishedloc ? 'publish_g.png' : 'publish_x.png';
					$alt = $row->publishedloc ? 'Published' : 'Unpublished';
					if($alt == "Published"){
						$class = "btn btn-micro active hasTooltip";
					}else{
						$class = "btn btn-micro hasTooltip";
					}
				?>
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"class="<?php echo $class;?>"><img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>				
			</td>			
			<td>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center; width:15px;" />
				<span>
				<?php	if ($i > 0) { ?>
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','orderup')">
					<img src="components/com_registrationpro/assets/images/uparrow.png" width="16px" height="16px" border="0" alt="orderup">
					</a>					
				<?php	}  ?>
				</span>
			    <span>
				<?php	if ($i < $n-1) { ?>
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','orderdown')">
					<img src="components/com_registrationpro/assets/images/downarrow.png" width="16px" height="16px" border="0" alt="orderdown">
					</a>
				<?php	}?>	
				</span>
			</td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

</table>
</div>
<div class="span10">
	<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer( ); ?>
</div>
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="view" value="locations" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="locations" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>