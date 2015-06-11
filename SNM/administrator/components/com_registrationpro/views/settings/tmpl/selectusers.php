<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<style>body{height:auto !important;}</style>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="note" style="width:95%;text-align:center;margin-left:auto;margin-right:auto;margin-top:15px;">
	<?php echo JText::_( "For Checked Items" ); ?> :&nbsp;&nbsp;&nbsp;
	<button class="btn btn-small btn-success" onclick="document.getElementById('task').value='selectuser';document.adminForm.submit();"> <?php echo JText::_( "ADMIN_EVENTS_SETT_USERS_STATUS_SELECT" ); ?></button>
	<button class="btn btn-small btn-danger" onclick="document.getElementById('task').value='deselectuser';document.adminForm.submit();"> <?php echo JText::_( "ADMIN_EVENTS_SETT_USERS_STATUS_DESELECT" ); ?></button>
</div>

<div style="background-color:#def;padding-top:14px;padding-left:10px;padding-right:10px;padding-bottom:1px;margin-top:20px;margin-bottom:10px;">
<table class="adminform_search" style="width:100%;border:none;">
	<tr>
		<td align=left>Search :&nbsp;&nbsp;
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();" class="btn btn large" style="margin-top:-3px;"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"class="btn btn large" style="margin-top:-3px;"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
	</tr>
</table>
</div>

<table class="adminlist" cellspacing=1 width=100%>
	<thead>
		<tr style="border-bottom:1px #ccc solid;height:30px;vertical-align:middle;">
			<th width="3%"><?php echo JText::_( 'Num' ); ?></th>
			<th width="4%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="20%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_SETT_USERS_USERNAME'), 'username', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="27%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_SETT_USERS_NAME'), 'name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="27%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_SETT_USERS_EMAIL'), 'email', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="14%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_SETT_USERS_STATUS'), 'published', $this->lists['order_Dir'], $this->lists['order'] );?></th>
		    <th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
			$link 		= 'index.php?option=com_registrationpro&amp;controller=events&amp;task=edit&amp;cid[]='. $row->id;
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
			$published 	= JHTML::_('grid.published', $row, $i );
   		?>
		<tr class="<?php echo "row$k"; ?>" style="height:30px;vertical-align:middle;">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked;?></td>
			<td align="center"><?php echo htmlspecialchars($row->username, ENT_QUOTES, 'UTF-8');?></td>
			<td align="center"><?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8');?></td>
			<td align="center"> <?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8'); ?> </td>
			<td align="center">
				<?php
					$task = $row->published ? 'unpublish' : 'publish';
					$img = $row->published ? 'publish_g.png' : 'publish_x.png';
					$alt = $row->published ? 'Published' : 'Unpublished';
				?>
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
			</td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>
		<tr><td colspan=20 style="height:4px;"></td></tr>
		<tr><td colspan=20 style="background-color:#def;height:6px;"></td></tr>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

</table>

<div class="note" style="width:95%;text-align:center;margin-left:auto;margin-right:auto;margin-top:15px;">
	<?php echo JText::_( "For Checked Items" ); ?> :&nbsp;&nbsp;&nbsp;
	<button class="btn btn-small btn-success" onclick="document.getElementById('task').value='selectuser'; document.adminForm.submit();"> <?php echo JText::_( "ADMIN_EVENTS_SETT_USERS_STATUS_SELECT" ); ?></button>
	<button class="btn btn-small btn-danger" onclick="document.getElementById('task').value='deselectuser'; document.adminForm.submit();"> <?php echo JText::_( "ADMIN_EVENTS_SETT_USERS_STATUS_DESELECT" ); ?></button>
</div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_registrationpro" />
<input type="hidden" name="view" value="settings" />
<input type="hidden" name="layout" value="selectusers" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="controller" value="settings" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="tmpl" value="component" />
</form>