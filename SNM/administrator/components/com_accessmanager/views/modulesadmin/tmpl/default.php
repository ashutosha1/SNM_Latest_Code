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

$selected = 'selected="selected"'; 
	
$am_modules_array = array();
foreach($this->items as $am_module){
	$am_module_id = $am_module->id;
	$am_module_title = $am_module->title;	
	$am_module_leveltitle = $am_module->leveltitle;	
	$am_module_published = $am_module->published;
	$am_modules_array[] = array($am_module_id, $am_module_title, $am_module_leveltitle, $am_module_published);	
}	

//make javascript array from components
$javascript_array_modules = 'var modules = new Array(';
for($n = 0; $n < count($am_modules_array); $n++){	
	if($n==0){
		$first = false;
	}else{
		$javascript_array_modules .= ',';
	}
	$javascript_array_modules .= "'".$am_modules_array[$n][0]."'";
}	
$javascript_array_modules .= ');';

//echo $javascript_array_modules;
//echo $n;
?>
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_modules."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;		
	for (i = 0; i < modules.length; i++){
		box_id = modules[i]+'__'+usergroup_id;
		hidden_id = modules[i]+'__'+usergroup_id+'__hidden';
		if(action==true){
			document.getElementById(box_id).checked = true;
			document.getElementById(hidden_id).value = hidden_id+'__1';
		}else{
			document.getElementById(box_id).checked = false;
			document.getElementById(hidden_id).value = hidden_id+'__';
		}
	}	
}

function toggle_right(hidden_field_id){
	field = document.getElementById(hidden_field_id);
	if(field.value==hidden_field_id+'__1'){
		field.value = hidden_field_id+'__';
	}else{
		field.value = hidden_field_id+'__1';
	}
}

Joomla.submitbutton = function(task){		
	if (task=='back'){			
		document.location.href = 'index.php?option=com_accessmanager&view=panel';		
	}
	if (task=='modulesadmin_apply'){				
		submitform('modulesadmin_save');		
	}
	if (task=='modulesadmin_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('modulesadmin_save');		
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
<form name="adminForm" id="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=modulesadmin'); ?>">	
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
						<span class="am_pagetitle"><?php echo $this->helper->am_strtolower(JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION')).' '.JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'); ?></span>
						<?php echo JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION').' '.$this->helper->am_strtolower(JText::_('COM_ACCESSMANAGER_MODULES_INFO')); ?>.
						<?php echo JText::_('COM_ACCESSMANAGER_DISPLAYED_ONLY_BACKEND_GROUPS'); ?>.
					</p>
					<?php
								
					//message if item access is not activated		
					if($this->controller->am_config['modulesadmin_active']==false){				
						echo '<p class="am_red">'.$this->helper->am_strtolower(JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION')).' '.JText::_('COM_ACCESSMANAGER_NO_MODULES_ACTIVE').'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
					}	
					
					if($this->helper->joomla_version >= '3.0'){
						echo '<div class="sliders_joomla3">';
					}
					
					echo JHtml::_('sliders.start','config-modulesadmin-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
					echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
					?>	
					<div class="border_bottom">
						<table class="adminlist am_table">		
							<tr>		
								<td width="300">
									<?php echo JText::_('COM_ACCESSMANAGER_USE_MODULEACCESS'); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="modulesadmin_active" value="true" <?php if($this->controller->am_config['modulesadmin_active']){echo 'checked="checked"';} ?> />
								</td>
								<td><?php
								echo JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION').' ';
								echo $this->helper->am_strtolower(JText::_('COM_ACCESSMANAGER_MODULES_INFO')).'.';
								echo '<br />'.JText::_('COM_ACCESSMANAGER_FALLBACK').'.';
								?>
								</td>
							</tr>
							<tr>		
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="modulesadmin_reverse_access" value="true" <?php if($this->controller->am_config['modulesadmin_reverse_access']){echo 'checked="checked"';} ?> />
								</td>
								<td>
									<?php 
									if($this->controller->am_config['based_on']=='level'){
										echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS_INFO_L'); 
									}else{
										echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS_INFO'); 
									}
									
									?>.					
								</td>
							</tr>
							<tr>		
								<td>					
									<?php 
									if($this->controller->am_config['based_on']=='level'){
										echo JText::_('COM_ACCESSMANAGER_MULTILEVEL_ACCESS_REQUIREMENT'); 
									}else{
										echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT'); 
									}					
									?>
								</td>
								<td>
									<label style="white-space: nowrap;"><input type="radio" name="modulesadmin_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['modulesadmin_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
									if($this->controller->am_config['based_on']=='level'){
										echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
									}else{
										echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
									}
									?></label><br />					
									<label style="white-space: nowrap;"><input type="radio" name="modulesadmin_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['modulesadmin_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
									if($this->controller->am_config['based_on']=='level'){
										echo JText::_('COM_ACCESSMANAGER_EVERY_LEVEL'); 
									}else{
										echo JText::_('COM_ACCESSMANAGER_EVERY_GROUP'); 
									}
									?></label>
								</td>
								<td>
									<?php 					
									if($this->controller->am_config['based_on']=='level'){
										echo JText::_('COM_ACCESSMANAGER_MULTILEVEL_ACCESS_REQUIREMENT_INFO'); 
									}else{
										echo JText::_('COM_ACCESSMANAGER_MULTIGROUP_ACCESS_REQUIREMENT_INFO'); 
									}
									?>.					
								</td>
							</tr>
							<tr>		
								<td>
									<?php echo $this->controller->am_strtolower(JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('JGRID_HEADING_ACCESS')); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="modulesadmin_superadmins" value="true" <?php if($this->controller->am_config['modulesadmin_superadmins']){echo 'checked="checked"';} ?> />
								</td>
								<td>
									<?php echo JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('COM_ACCESSMANAGER_ALWAYS_HAVE_ACCESS'); ?>.													
								</td>
							</tr>		
							<tr>		
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS'); ?>
								</td>
								<td colspan="2">
									<?php			
										echo $this->helper->display_multigrouplevel_select_config('modulesadmin', $this->controller->am_config, true, true);				
									?>					
								
									<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS_INFO_MODULE'); ?>.					
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
									//legend and message if reverse access	
									$this->controller->reverse_access_warning('modulesadmin_reverse_access');
									
									//message in free version that these restrictions will not work in free version
									$this->controller->not_in_free_version();
									
									//search bar		
									echo $this->helper->search_toolbar(1, 0, 1, 1, $this->state->get('filter.search'), 0, $listDirn, $this->pagination->getLimitBox());	
									if($this->helper->joomla_version < '3.0'){			
									?>									
									&nbsp;				
									<select name="filter_state" class="inputbox" onchange="this.form.submit()">
										<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
										<?php echo JHtml::_('select.options', $this->getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
									</select>
									&nbsp;							
									<select name="filter_position" class="inputbox" onchange="this.form.submit()">
										<option value=""> - <?php echo JText::_('COM_ACCESSMANAGER_SELECT_POSITION');?> - </option>
										<?php echo JHtml::_('select.options', $this->getPositions(1), 'value', 'text', $this->state->get('filter.position'));?>
									</select>
									&nbsp;
									<select name="filter_module" class="inputbox" onchange="this.form.submit()">
										<option value=""> - <?php echo JText::_('COM_ACCESSMANAGER_SELECT_MODULE');?> - </option>
										<?php echo JHtml::_('select.options', $this->getModules(1), 'value', 'text', $this->state->get('filter.module'));?>
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
								<?php
									if($this->controller->am_config['based_on']=='level'){		
										echo JText::_('COM_ACCESSMANAGER_DISPLAY_LEVELS');
									}else{
										echo JText::_('COM_ACCESSMANAGER_DISPLAY_USERGROUPS');
									}
								?>:
								<br />
								<button onclick="usergroups_to_cookie();this.form.submit();"><?php echo JText::_('COM_ACCESSMANAGER_GO'); ?></button>
							</td>
						</tr>
					</table>			
				</td>
				<td id="td_accesslevel_selector">
					<?php 
					echo $this->controller->accesslevel_selector(1, 1, 0); 
					?>
				</td>
			</tr>
		</table>	
		<table class="adminlist table table-striped" style="width: 100%;">
			<tr>		
				<th style="text-align: center;">
					id			
				</th>	
				<th align="left">
					<?php 
						$label = ucfirst(JText::_('JFIELD_TITLE_DESC')).' '; 			
						echo JHTML::_('grid.sort', $label, 'm.title', $listDirn, $listOrder); 			
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
				foreach($this->am_grouplevels as $am_accesslevel){
					echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
				}
				echo '</tr>';
						
				$k = 1;		
				$counter = 0;	
				for($n = 0; $n < count($am_modules_array); $n++){			
					echo '<tr class="row'.$k.'"><td class="column_ids">'.$am_modules_array[$n][0].'</td>';
					$has_superscript = '';
					if($am_modules_array[$n][3]=='0'){
						$has_superscript = ' class="has_superscript"';
					}
					echo '<td'.$has_superscript.'>'.$am_modules_array[$n][1];
					if($am_modules_array[$n][3]=='0'){
						echo '<sup class="am_superscript">1</sup>';
					}
					echo '</td>';						
					foreach($this->am_grouplevels as $am_accesslevel){
						$checked = '';
						$checked_hidden = '';
						if (in_array($am_modules_array[$n][0].'__'.$am_accesslevel->id, $this->access_modules)) {
							$checked = 'checked="checked"';					
							$checked_hidden = '1';
						}
						echo '<td class="center"><input type="hidden" name="modulesadmin_access_hidden[]" id="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'__hidden" value="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="modulesadmin_access[]" id="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'__hidden\');" value="'.$am_modules_array[$n][0].'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
					}
					echo '</tr>';
					if($k==1){
						$k = 0;
					}else{
						$k = 1;
					}			
					if($counter==7){
						echo '<tr><th colspan="2">&nbsp;</th>';					
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
		</table>
	</div>
</form>
<?php
$this->controller->display_footer();
?>