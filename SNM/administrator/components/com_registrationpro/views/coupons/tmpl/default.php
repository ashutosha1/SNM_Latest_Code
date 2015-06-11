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
JHtml::_('formbehavior.chosen', 'select');
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
			<th style="text-align:center;" width="2%"><?php echo JText::_( 'S.No.' ); ?></th>
			<th style="text-align:center;" width="2%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th style="text-align:left;" width="15%"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_TITLE_HEADING'), 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;" width="10%"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_START_DATE_HEADING'), 'a.start_date', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;" width="10%"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_END_DATE_HEADING'), 'a.end_date', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;" width="15%"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_CODE_HEADING'), 'a.code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_DISCOUNT_HEADING'), 'a.discount', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_LIMIT_AMOUNT_HEADING'), 'a.max_amount', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_STATUS_HEADING'), 'a.status', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('EVENT_ADMIN_COUPONS_PUBLISHED_HEADING'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th style="text-align:center;" width="2%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tbody class="ui-sortable">
		<?php
			$k = 0;
			for($i=0; $i < count($this->rows); $i++) {
				$row = &$this->rows[$i];
				$datum 		= date( $formatdate, strtotime( $row->dates )) . ' ' . date( $formattime, strtotime( $row->times ));
				$zeit 		= date( $formatdate, strtotime( $row->enddates )) . ' ' . date( $formattime, strtotime( $row->endtimes ));
				$link 		= 'index.php?option=com_registrationpro&amp;controller=coupons&amp;task=edit&amp;cid[]='. $row->id;
				$checked 	= JHTML::_('grid.checkedout',   $row, $i );
				$published 	= JHTML::_('grid.published', $row, $i );
			?>

			<tr class="<?php echo "row$k"; ?> dndlist-sortable">
				<td style="text-align:center;"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
				<td style="text-align:center;"> <?php echo $checked; ?> </td>
				<td style="text-align:left;"><a href="<?php echo $link; ?>" title="Edit Event"><?php echo $row->title; ?></a> </td>
				<td style="text-align:center"><?php echo $row->start_date; ?></td>
				<td style="text-align:center"><?php echo $row->end_date; ?></td>
				<td style="text-align:center"><?php echo $row->code; ?></td>
				<td style="text-align:center">
					<?php
						if($row->discount_type == 'A'){
							echo $this->regpro_config['currency_sign'],$row->discount;
						}else echo $row->discount,"%";
					?>
				</td>
				<td style="text-align:center"><?php echo $row->max_amount; ?></td>
				<td style="text-align:center"><?php echo $row->coupon_status; ?></td>
				<td align="center"  style="text-align:center;"><?php echo $published; ?></td>
				<td align="center"><?php echo $row->id; ?></td>
				<?php $k = 1 - $k; ?>
			</tr>
			<?php
			}
		?>
	</tbody>
	<tr><td colspan="11"><?php echo $this->pageNav->getListFooter(); ?></td></tr>
</table>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_registrationpro" />
<input type="hidden" name="view" value="coupons" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="coupons" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>

<div class="span10">
	<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer( ); ?>
</div>