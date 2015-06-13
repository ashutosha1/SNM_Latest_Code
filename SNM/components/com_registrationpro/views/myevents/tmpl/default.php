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

//echo"<pre>";print_r($this->rows); exit;
$ordering = ($this->lists['order'] == 'c.ordering' || $this->lists['order'] == 'a.ordering');
?>

<div id="regpro">

<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);
$regpro_html = new regpro_html;
// user toolbar
$regpro_html->user_toolbar();

// user toolbar2
$regpro_html->events_toolbar();
?>

<script language="javascript">

function submitbutton(pressbutton) {
	var form = document.adminForm;
			
	if (pressbutton == 'remove' || pressbutton == 'publish' || pressbutton == 'unpublish' || pressbutton == 'copy') {
	
		if(form.boxchecked.value > 0){			
			submitform( pressbutton );
			return;
		}else{
			alert("Please select the record first.");
		}
	}else{
		submitform( pressbutton );
		return;
	}
}
</script>
<div class="regpro_outline" id="regpro_outline">
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
			<th width="15%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_TITEL_LI_EV'), 'a.titel', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="7%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_DATE_START'), 'a.dates', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="7%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_DATE_END'), 'a.enddates', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_CLUB_LI_EV'), 'a.locid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<!--<th width="10%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_CAT_LI_EV'), 'a.catsid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_CITY_LI_LO'), 'l.city', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>-->			
			<th width="7%"><?php echo JText::_( 'MY_EVENTS_ADD_USER' ); ?></th>
			<th width="7%"><?php echo JText::_( 'MY_EVENTS_REGCOUNT' ); ?></th>			
			<th width="5%"><?php echo JHTML::_('grid.sort', JText::_('MY_EVENTS_STATUS'), 'a.status', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>			
			<th width="5%"><?php echo JText::_( 'MY_EVENTS_PUBLISH_LI_EV' ); ?></th>			
			<!--<th width="10%">
				<?php if ($ordering) {?> 
				<a href="javascript: saveorder( <?php echo count( $this->rows )-1; ?> )">
					<img src="components/com_registrationpro/assets/images/filesave.png" border="0" width="16" height="16" alt="Save Order" align="middle" />
				</a> 
				<?php } ?>
				
				<?php echo JHTML::_('grid.sort', JText::_( 'MY_LIST_ORDER' ), 'a.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>		-->										
		    <th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php				
		$k = 0;
		$registrationproHelper = new registrationproHelper;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = $this->rows[$i];
			//$link 		= JRoute::_('index.php?option=com_registrationpro&controller=myevents&task=edit&cid[]='. $row->id."&Itemid=".$this->Itemid,false);
			//$link 		= JRoute::_('index.php?option=com_registrationpro&controller=myevents&task=edit&cid='. $row->id."&Itemid=".$this->Itemid,false);
			//$link 		= JRoute::_('index.php?option=com_registrationpro&controller=myevents&task=edit&id='. $row->id."&Itemid=".$this->Itemid,false);
			//$link 		= JRoute::_('index.php?option=com_registrationpro&view=myevent&id='. $row->id."&Itemid=".$this->Itemid,false);
			$link 		= JRoute::_('index.php?option=com_registrationpro&view=myevent&cid='. $row->id."&Itemid=".$this->Itemid,false);
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
			//$published 	= JHTML::_('grid.published', $row, $i );	
			
			if($row->moderating_status == 0){
				$new_user_link = "#";
			}else{			
				$new_user_link = JRoute::_("index.php?option=com_registrationpro&view=event&did=".$row->id."&Itemid=".$this->Itemid,false);
			}
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked;?></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_EVENT' );?>::<?php echo $row->titel; ?>">
					<a href="<?php echo $link; ?>"> <?php echo htmlspecialchars($row->titel, ENT_QUOTES, 'UTF-8'); ?> </a>
				</span>			
			</td>
			<td align="center"><?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->dates); ?> </td>
			<td align="center"><?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->enddates); ?></td>
			<td align="left"><?php echo htmlspecialchars($row->club, ENT_QUOTES, 'UTF-8');?></td>
			<!--<td align="left"> <?php echo htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8'); ?> </td>
			<td align="left"><?php echo htmlspecialchars($row->city, ENT_QUOTES, 'UTF-8'); ?></td>-->
			<td align="center"><a href="<?php echo $new_user_link;?>">New User</a></td>
			<td align="center">
			<?php 
				if ($row->registra == 1) {
					$eobj = $this->getModel();
					$nrregusers_array = $eobj->getRegistered($row->id); 	
					//$nrregusers_array = $this->getModel()->getRegistered($row->id); 											
					$nrregusers = 0;
					$text = '';
					$nrstatus = array();
					
					//echo"<pre>";print_r($nrregusers_array); exit;
					foreach ($nrregusers_array as $pid=>$qty){
						if(is_int($qty)) $nrregusers+=$qty; 
						else $nrstatus = $qty;
					} 
					
					if(!empty($nrstatus)){
						foreach($nrstatus as $status=>$qty){
							$text .= JText::_('ADMIN_EVENTS_REGISTRATION_STATUS_'.$status) . ': '.$qty.'<br/>';
						}
						$text .= JText::_('ADMIN_EVENTS_REGISTRATION_STATUS_TOTAL'). ': '.$nrregusers. (($row->max_attendance!=0) ? ' / '.$row->max_attendance : '');
					}
					$linkreg 	= JRoute::_('index.php?option=com_registrationpro&view=users&rdid='.$row->id.'&hidemainmenu=1',false);
				?>

					<a href="<?php echo $linkreg; ?>" title="Edit Users"> <?php echo $text; ?> </a>
				<?php
				}else {
				?>
					<span class="editlinktip hasTip" title="Registration disabled">												
						<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/publish_x.png" width="16px" height="16px" border="0" />
					</span>	
				<?php
				}
				?>
			</td>
			<td align="center">
				<?php				
					if($row->moderating_status == 0){
						$row->published = 0;
						echo JText::_('ADMIN_EVENTS_NOT_APPROVED');
					}else{
						echo JText::_('ADMIN_EVENTS_STATUS_'.$row->status);	
					}
											
				?>
			</td>			
			<td align="center">
				<?php
					if(!$row->moderating_status){
						$task = '';
						$img = 'moderation.png';
						$alt = 'Need Moderation';
					}else{
						$task = $row->published? 'unpublish' : 'publish';
						$img = $row->published ? 'ball_green.png' : 'ball_red.png';
						$alt = $row->published ? 'Published' : 'Unpublished';
					}
				?>				
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><img src="<?php echo REGPRO_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>		
			</td>
			
			<!--<td class="order">								
				<span><?php echo $this->pageNav->orderUpIcon( $i, ($row->catsid == $this->rows[$i-1]->catsid), 'orderupevents', 'Move Up', $ordering); ?></span>
				<span><?php echo $this->pageNav->orderDownIcon( $i, $n, ($row->catsid == $this->rows[$i+1]->catsid), 'orderdownevents', 'Move Down', $ordering ); ?></span>			
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>							
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />	
			</td>-->
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot>
		<tr>
			<td colspan="15">
				<div class="pagination"><?php echo $this->pageNav->getListFooter(); ?></div>				
			</td>
		</tr>
	</tfoot>

</table>
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="view" value="myevents" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="myevents" />
	<input type="hidden" name="user_id" value="<?php echo $this->user->id; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>
</div>
<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>

</div>