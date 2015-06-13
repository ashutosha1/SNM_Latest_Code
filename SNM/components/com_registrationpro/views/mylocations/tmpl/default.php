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

//echo"<pre>";print_r($this->rows); exit;
?>
<div id="regpro">
<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();

// form toolbar
$regpro_html->mycategories_toolbar();
?>
<script language="javascript" type="text/javascript">		
	function submitbutton(pressbutton) {
		var form = document.adminForm;
								
		if (pressbutton == 'remove' || pressbutton == 'publish' || pressbutton == 'unpublish') {	
			if(form.boxchecked.value > 0){			
				submitform( pressbutton );
				return;
			}else{
				alert("<?php echo JText::_('MY_EVENTS_SELECT_RECORD_FIRST'); ?>");
			}
		}else {
			submitform( pressbutton );
		}
	}		
</script>
<div id="regpro_outline" class="regpro_outline">
<form action="index.php" method="post" name="adminForm" id="adminForm">

<!--table class="adminform">
	<tr>
		<td width="100%">
			 <?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap"><?php echo $this->lists['state']; ?></td>
	</tr>
</table-->

<table class="table table-striped" cellspacing="1">
	<thead>
		<tr>
			<th width="2%"><?php echo JText::_( 'Num' ); ?></th>
			<th width="2%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="25%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CLUB_LI_LO'), 'l.club', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="25%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CLUBHOME_LI_LO'), 'l.url', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>			
			<th width="20%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_CITY_LI_LO'), 'l.city', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="15%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_PUBLISH_LI_EV'), 'l.publishedloc', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="6%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'l.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	
	<tbody class="ui-sortable">
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = $this->rows[$i];
			
			$link 		= JRoute::_('index.php?option=com_registrationpro&view=mylocation&id='. $row->id."&Itemid=".$this->Itemid,false);
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
			<td align="center">
				<?php
					$task = $row->publishedloc ? 'unpublish' : 'publish';
					$img = $row->publishedloc ? 'ball_green.png' : 'ball_red.png';
					$alt = $row->publishedloc ? 'Published' : 'Unpublished';
				?>
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="<?php echo REGPRO_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>				
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

<p class="copyright">
	<?php echo $regpro_header_footer->regpro_footer($this->regpro_config); ?>
</p>
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="view" value="locations" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="locations" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
</div>