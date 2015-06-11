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
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_LIST_FORMS' ), 'forms' );
JToolBarHelper::addNew();
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::spacer();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::editList();
JToolBarHelper::spacer();
JToolBarHelper::custom( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
JToolBarHelper::spacer();
JToolBarHelper::deleteList('Selected records data will be lost and cannot be undone!', 'remove', 'Remove');
JToolBarHelper::spacer();
JToolBarHelper::cancel();
//JToolBarHelper::spacer();
//JToolBarHelper::help( 'screen.registrationpro', true );

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
<table class="table table-striped" cellspacing="1" width="100%">
	<thead>
		<tr>
			<th width="5%"><?php echo JText::_( 'S.No.' ); ?></th>
			<th width="5%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="30%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_FORMS_TITLE'), 'f.title', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="30%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_FORMS_NAME'), 'f.name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="15%" style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_FORMS_PUBLISHED'), 'f.published', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		    <th width="15%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'f.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>

	<tbody class="ui-sortable">
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];

			$link 		= 'index.php?option=com_registrationpro&amp;controller=forms&amp;task=edit&amp;cid[]='. $row->id;
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
			$published 	= JHTML::_('grid.published', $row, $i );

			$ordering = ($this->lists['ordering'] == 'a.ordering');
			//$badge = $this->settings->tbadge_folder.$row->tbadge;
   		?>
		<tr class="<?php echo "row$k"; ?> dndlist-sortable">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked;?></td>

			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_FORM' );?>::<?php echo $row->title; ?>">
					<a href="<?php echo $link; ?>"> <?php echo htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8'); ?> </a>
				</span>
			</td>

			<td align="left"><?php
				echo $row->name;
				?>
			</td>

			<td align="center" style="text-align:center;"><?php echo $published; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot>
		<tr>
			<td colspan="6">
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
	<input type="hidden" name="view" value="forms" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="forms" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>