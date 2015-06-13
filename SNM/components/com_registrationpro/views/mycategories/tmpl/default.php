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
$regpro_html = new regpro_html;
// user toolbar
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
<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm">

<!--<table class="adminform">
	<tr>
		<td width="100%">
			 <?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap"><?php echo $this->lists['state']; ?></td>
	</tr>
</table>-->

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="2%"><?php echo JText::_( 'Num' ); ?></th>
			<th width="2%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="5%"><?php echo JText::_('ADMIN_CATEGORIES_COLOR'); ?></th>
			<th width="25%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_CATEGORIES_NAME'), 'c.catname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>			
			<th width="25%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_CATEGORIES_ACCESS'), 'c.access', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="20%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_CATEGORIES_PUBLISH'), 'c.publishedcat', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<!--<th width="16%"> 
				<a href="javascript: saveorder( <?php echo count( $this->rows )-1; ?> )">
					<img src="components/com_registrationpro/assets/images/filesave.png" border="0" width="16" height="16" alt="Save Order" align="middle" />
				</a> 
				<?php echo JHTML::_('grid.sort', JText::_( 'ADMIN_CATEGORIES_REORDER' ), 'c.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>-->											
		    <th width="5%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'c.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = $this->rows[$i];
			
			//$link 		= JRoute::_('index.php?option=com_registrationpro&controller=mycategories&task=edit&cid[]='. $row->id."&Itemid=".$this->Itemid,false);
			$link 		= JRoute::_('index.php?option=com_registrationpro&view=mycategory&id='. $row->id."&Itemid=".$this->Itemid,false);
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i );
			//$access 	= JHTML::_('grid.access',   $row, $i );
			$access 	= $row->groupname;
		 		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked;?></td>
			<td align="center"><div style="width:13px;height:13px;border:1px solid #666666;background:#<?php echo $row->background; ?>"></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_CATEGORY' );?>::<?php echo $row->catname; ?>">
					<a href="<?php echo $link; ?>"> <?php echo htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8'); ?> </a>
				</span>			
			</td>
			<td align="center"><?php echo $access; ?> </td>
			<td align="center">
				<?php
					$task = $row->publishedcat ? 'unpublish' : 'publish';
					$img = $row->publishedcat ? 'ball_green.png' : 'ball_red.png';
					$alt = $row->publishedcat ? 'Published' : 'Unpublished';
				?>
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="<?php echo REGPRO_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>				
			</td>			
			<!--<td>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
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
			</td>-->
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="7">
				<div class="pagination"><?php echo $this->pageNav->getListFooter(); ?></div>			
			</td>
		</tr>
	</tfoot>

</table>
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="view" value="mycategories" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="mycategories" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
</div>
<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>	


</div>