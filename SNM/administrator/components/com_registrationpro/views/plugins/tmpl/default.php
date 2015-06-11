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

//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_LIST_PLUGINS' ), 'plugins' );
JToolBarHelper::addNew();
JToolBarHelper::spacer();
JToolBarHelper::publishList();
JToolBarHelper::spacer();
JToolBarHelper::unpublishList();
JToolBarHelper::spacer();
JToolBarHelper::editList();
//JToolBarHelper::spacer();
//JToolBarHelper::help( 'screen.registrationpro', true );

?>

<script language="javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		submitform(pressbutton);
	}
</script>
<div class="span10">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<?php $k = 1; $iii = 0;	?>
		 <table width="100%"  border="0" class="table table-striped">
			 <thead>
				<tr style="vertical-align:top;">
					<th style="text-align:center;" width=50><b>S.No.</b></th>
					<th style="text-align:center;" width=30 nowrap><input type="checkbox" name="toggle" onclick="Joomla.checkAll(this)"/></th>
					<th style="text-align:left" class="title"><b><?php echo JText::_('MANAGE_PLUGINS_PLUGIN_NAME'); ?></b></th>
					<th style="text-align:left" class="title"><b><?php echo JText::_('MANAGE_PLUGINS_PLUGIN_FILE_NAME'); ?></b></th>
					<th style="text-align:center"><b><?php echo JText::_('MANAGE_PLUGINS_PLUGIN_PUB'); ?></b></th>
				</tr>
			</thead>
			<tbody class="ui-sortable">
			<?php
				$k = 0;
				for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					$checked   = JHTML::_('grid.checkedout', $row, $i );
					$link      = 'index.php?option=com_plugins&task=plugin.edit&extension_id='. $row->id;
					$published = JHTML::_('grid.published', $row, $i );
			?>
				<tr class="row<?php echo $k; ?> dndlist-sortable">
					<td style="text-align:center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
					<td style="text-align:center"><?php echo $checked;?></td>
					<td style="text-align:left"><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
					<td style="text-align:left"><?php echo $row->element; ?></td>
					<td style="text-align:center">
						<?php
							$task  = $row->enabled ? 'unpublish' : 'publish';
							$img   = $row->enabled ? 'publish_g.png' : 'publish_x.png';
							$alt   = $row->enabled ? 'Published' : 'Unpublished';
							$class = 'btn btn-micro hasTooltip';
							if($alt == "Published") $class = 'btn btn-micro active hasTooltip';
						?>
						<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" class="<?php echo $class;?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
					</td>
				</tr>
			<?php
					$k = 1 - $k;
				}
			?>
			</tbody>
		</table>
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_registrationpro" />
		<input type="hidden" name="view" value="plugins" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="plugins" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
	</form>
</div>
<div class="span10 regpro_footer">
	<?php $registrationproAdmin = new registrationproAdmin;	echo $registrationproAdmin->footer(); ?>
</div>