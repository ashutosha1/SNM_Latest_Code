<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

if($this->helper->joomla_version >= '3.0'){
	//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
	//JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	JHtml::_('formbehavior.chosen', 'select');
}

?>

<script language="javascript" type="text/javascript">

<?php

//make javascript array from categories
$javascript_items_array = 'var items_array = new Array(';
$first = true;
foreach($this->items as $item){		
	if($first){
		$first = false;
	}else{
		$javascript_items_array .= ',';
	}
	$javascript_items_array .= "'".$item->id."'";
}	
$javascript_items_array .= ');';

echo $javascript_items_array."\n";

?>

function select_all(usergroup_id, select_all_id){
	new_value = document.getElementById(select_all_id).value;	
	if(new_value==''){
		right = 'i';
	}else if(new_value=='1'){
		right = 'a';
	}else if(new_value=='0'){
		right = 'd';
	}		
	for (i = 0; i < items_array.length; i++){
		select_id = items_array[i]+'__'+usergroup_id;			
		document.getElementById(select_id).value = new_value;
		document.getElementById('img_'+select_id).className = 'am_'+right;
		document.getElementById('target_'+select_id).innerHTML = '';
	}	
	document.getElementById(select_all_id).value = 'none';
}

function on_select_change(id){	
	temp = document.getElementById(id).value;	
	if(temp==''){
		right = 'i';
	}else if(temp=='1'){
		right = 'a';
	}else if(temp=='0'){
		right = 'd';
	}
	document.getElementById('img_'+id).className = 'am_'+right;
	document.getElementById('target_'+id).innerHTML = '';
}

Joomla.submitbutton = function(task){		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='menuitemsbackend_apply'){				
		submitform('menuitemsbackend_save');		
	}
	if (task=='menuitemsbackend_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('menuitemsbackend_save');		
	}
}

Joomla.orderTable = function(){
	if(document.getElementById("sortTable")){
		sort_table = document.getElementById("sortTable").value;
	}else{
		sort_table = document.adminForm.filter_order.value;
	}
	if(document.getElementById("directionTable")){
		direction_table = document.getElementById("directionTable").value;
	}else{
		direction_table = document.adminForm.filter_order_Dir.value;
	}	
	Joomla.tableOrdering(sort_table, direction_table, '');	
}

</script>
<form name="adminForm" id="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=menuitemsbackend'); ?>">	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="save_and_close" id="save_and_close" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />	
	<?php echo JHTML::_( 'form.token' ); ?>	
	
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<div class="clr"> </div><!-- needed for some admin templates -->
		<table id="am_subheader">
			<tr>
				<td>
					<p>
						<span class="am_pagetitle"><?php echo JText::_('COM_ACCESSMANAGER_MENUITEM_ACCESS'); ?></span>
						<?php echo JText::_('COM_ACCESSMANAGER_MENUITEM_INFO'); ?>.
						<?php echo JText::_('COM_ACCESSMANAGER_DIRECT_ACCESS_BLOCKED'); ?>.
						<?php echo JText::_('COM_ACCESSMANAGER_DISPLAYED_ONLY_BACKEND_GROUPS'); ?>.
						<?php echo JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('COM_ACCESSMANAGER_ALWAYS_HAVE_ACCESS'); ?>.
					</p>
					<?php
								
					//message if item access is not activated		
					if($this->controller->am_config['menuitembackend_active']==false){				
						echo '<p class="am_red">'.JText::_('COM_ACCESSMANAGER_NO_ACTIVE_MENUITEMBACKEND').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
					}	
					
					if($this->helper->joomla_version >= '3.0'){
						echo '<div class="sliders_joomla3">';
					}
					
					echo JHtml::_('sliders.start','config-menuitembackend-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
					echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
					?>
					<div class="border_bottom">
						<table class="adminlist am_table">
							<tr>		
								<td width="300">
									<?php echo JText::_('COM_ACCESSMANAGER_USE_MENUITEM_ACCESS'); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="menuitembackend_active" value="true" <?php if($this->controller->am_config['menuitembackend_active']){echo 'checked="checked"';} ?> />
								</td>
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_MENUITEM_INFO'); ?>.
									<?php echo JText::_('COM_ACCESSMANAGER_DIRECT_ACCESS_BLOCKED'); ?>.
									<br />
									<?php echo JText::_('COM_ACCESSMANAGER_DISABLED_NO_RIGHTS'); ?>.
								</td>
							</tr>			
							<tr>		
								<td>					
									<?php 
										echo JText::_('COM_ACCESSMANAGER_DEFAULT_TOPGROUP'); 									
									?>
								</td>
								<td>
									<label style="white-space: nowrap;"><input type="radio" name="menuitembackend_default" value="1" class="radio" <?php if($this->controller->am_config['menuitembackend_default']=='1'){echo 'checked="checked"';} ?> />
									<img src="components/com_accessmanager/images/right_allow.gif" alt="allowed" /> 
									<?php 				
										echo JText::_('COM_ACCESSMANAGER_ALLOWED'); 				
									?>
									</label><br />					
									<label style="white-space: nowrap;"><input type="radio" name="menuitembackend_default" value="0" class="radio" <?php if($this->controller->am_config['menuitembackend_default']=='0'){echo 'checked="checked"';} ?> /> 
									<img src="components/com_accessmanager/images/right_deny.gif" alt="denied" />  
									<?php 
										echo JText::_('COM_ACCESSMANAGER_DENIED');				
									?>
									</label>
								</td>
								<td>
									<?php
										echo JText::_('COM_ACCESSMANAGER_DEFAULT_TOPGROUP_INFO').' \''.JText::_('COM_ACCESSMANAGER_INHERITED').'\''; 
									?>.					
								</td>
							</tr>
							<tr>		
								<td>					
									<?php echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT'); ?>
								</td>
								<td>
									<label style="white-space: nowrap;"><input type="radio" name="menuitembackend_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['menuitembackend_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
									
										echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
									
									?></label><br />					
									<label style="white-space: nowrap;"><input type="radio" name="menuitembackend_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['menuitembackend_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
									
										echo JText::_('COM_ACCESSMANAGER_EVERY_GROUP'); 
									
									?></label>
								</td>
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT_INFO'); ?>.					
								</td>
							</tr>	
							<tr>		
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_AUTHOR_ACCESS'); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="menuitembackend_author_access" value="true" <?php if($this->controller->am_config['menuitembackend_author_access']){echo 'checked="checked"';} ?> />
								</td>
								<td><?php
								echo JText::_('COM_ACCESSMANAGER_AUTHOR_ACCESS_INFO').' '.JText::_('COM_ACCESSMANAGER_MENUITEM');
								?>.
								</td>
							</tr>						
						</table>
					</div>	
					<?php 
					echo JHtml::_('sliders.end'); 					
					if($this->helper->joomla_version >= '3.0'){
						echo '</div>';
					}
					?>						
					<table style="width: 100%;">
						<tr>
							<td>
								<br />	
								<?php 							
								//message in free version that these restrictions will not work in free version
								$this->controller->not_in_free_version();	
								
								//search bar	
								$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);	
								echo $this->helper->search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());					
								
								if($this->helper->joomla_version < '3.0'){				
								?>
																
									&nbsp;			
									<select name="filter_type" class="inputbox" onchange="this.form.submit()">
										<option value="all"> - <?php echo JText::_('COM_ACCESSMANAGER_SELECT_MENU_TYPE');?> - </option>
										<?php echo JHtml::_('select.options', $this->get_menu_type_options(), 'value', 'text', $this->state->get('filter.type'), true);?>
									</select>
									<!--
									&nbsp;
									<select name="filter_access" class="inputbox" onchange="this.form.submit()">
										<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
										<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
									</select>
									-->
									&nbsp;
									<select name="filter_published" class="inputbox" onchange="this.form.submit()">
										<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
										<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
									</select>
									&nbsp;
									<select name="filter_language" class="inputbox" onchange="this.form.submit()">
										<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
										<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
									</select>
								<?php
								}
								?>
							</td>
							<td class="align_right">
								<?php echo JText::_('COM_ACCESSMANAGER_DISPLAY_USERGROUPS'); ?>:
								<br />
								<button onclick="usergroups_to_cookie();this.form.submit();"><?php echo JText::_('COM_ACCESSMANAGER_GO'); ?></button>
							</td>
						</tr>
					</table>			
				</td>
				<td id="td_accesslevel_selector">			
					<?php 
					echo $this->controller->accesslevel_selector('backend', false, true); 
					?>
				</td>
			</tr>
		</table>			
		<table class="adminlist table table-striped" style="width: 100%;">
			<tr>		
				<th style="text-align: center; width: 10px;">
					id			
				</th>
				<th align="left" class="nowrap">
					<?php 
					$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
					echo JHTML::_('grid.sort', $label, 'm.title', $listDirn, $listOrder); 			
					?>	
					&nbsp;	
					<?php 
					$label = ucfirst(JText::_('JFIELD_ORDERING_LABEL')).' '; 			
					echo JHTML::_('grid.sort', $label, 'm.lft', $listDirn, $listOrder); 			
					?>							
				</th>
				<?php					
					$this->controller->loop_accesslevels($this->am_grouplevels);			
				?>
			</tr>
				
			<?php		
						
			//row with select_all checkboxes
			echo '<tr class="row0">';
			echo '<td>&nbsp;</td>';
			echo '<td align="left" class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';		
			foreach($this->am_grouplevels as $usergroup){
				echo '<td style="text-align:center;">';		
					echo $this->helper->get_access_select_all($usergroup->id);
				echo '</td>';
			}
			echo '</tr>';
		
			$k = 1;	
			$counter = 0;
			foreach($this->items as $item){
				if($k==1){
					$k = 0;
				}else{
					$k = 1;
				}
				echo '<tr class="row'.$k.'">';					
				echo '<td class="column_ids">'.$item->id.'</td>';
				$has_superscript = '';
				if($item->published=='0' || $item->type=='alias'){
					$has_superscript = ' has_superscript';
				}
				echo '<td class="indent-'.(intval(($item->level-1)*15)+4).$has_superscript.'">';	
				echo $item->title;
				if($item->published=='0'){
					echo '<sup class="am_superscript">1</sup>';
				}
				if($item->type=='alias'){
					echo '<sup class="am_superscript">2</sup>';
				}		
				echo '</td>';	
				foreach($this->am_grouplevels as $accesslevel){
					/*
					$checked = '';
					$checked_hidden = '';
					if (in_array($item->id.'__'.$accesslevel->id, $this->access_menuitems)){
						$checked = 'checked="checked"';
						$checked_hidden = '1';
					}
					echo '<td align="center"><input type="hidden" name="menu_access_hidden[]" id="'.$item->id.'__'.$accesslevel->id.'__hidden" value="'.$item->id.'__'.$accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="menu_access[]" id="'.$item->id.'_0_'.$accesslevel->id.'" onclick="toggle_right(\''.$item->id.'__'.$accesslevel->id.'__hidden\');" value="'.$item->id.'__'.$accesslevel->id.'" '.$checked.' /></td>';
					*/
					$access = '';
					$needle_1 = $item->id.'__'.$accesslevel->id.'__1';				
					if(in_array($needle_1, $this->access_menuitems)){
						$access = '1';
					}
					$needle_0 = $item->id.'__'.$accesslevel->id.'__0';
					if(in_array($needle_0, $this->access_menuitems)){
						$access = '0';
					}
					
					echo '<td align="center" class="access_selects">';									
						echo $this->helper->get_access_select($item->id.'__'.$accesslevel->id, $access, $this->access_menuitems, 'menuitembackend', $this->controller->am_config);				
					echo '</td>';
				}
				echo '</tr>';
				if($counter==7){
					echo '<tr><th>&nbsp;</th><th>&nbsp;</th>';		
					$this->controller->loop_accesslevels($this->am_grouplevels);
					echo '</tr>';
					$counter = 0;
				}
				$counter = $counter+1;			
			}
			
			
			
			?>	
		</table>
		<table class="adminlist">
			<tfoot>
				<tr>
					<td>
					<?php 
						echo $this->pagination->getListFooter();
					?>
					</td>
				</tr>
			</tfoot>
		</table>
		<table>
			<tr>
				<td class="am_red">1
				</td>
				<td>=
				</td>
				<td><?php echo JText::_('COM_ACCESSMANAGER_NOT_PUBLISHED_B'); ?>.
				</td>
			</tr>
			<tr>
				<td class="am_red">2
				</td>
				<td>=
				</td>
				<td><?php echo JText::_('COM_ACCESSMANAGER_ALIAS_WARNING'); ?>.
				</td>
			</tr>
		</table>
	</div>
</form>
<?php
$this->controller->display_footer();
?>