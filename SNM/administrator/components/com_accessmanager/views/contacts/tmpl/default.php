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

$ds = DIRECTORY_SEPARATOR;

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

$selected = 'selected="selected"'; 	
	
//make javascript array from categories
$javascript_array_items = 'var items = new Array(';
$first = true;
foreach($this->items as $item){		
	if($first){
		$first = false;
	}else{
		$javascript_array_items .= ',';
	}
	$javascript_array_items .= "'".$item->id."'";
}	
$javascript_array_items .= ');';
		
?>
<script language="javascript" type="text/javascript">

<?php echo $javascript_array_items."\n"; ?>

function select_all(usergroup_id, select_all_id){
	action = document.getElementById(select_all_id).checked;	
	for (i = 0; i < items.length; i++){
		box_id = items[i]+'__'+usergroup_id;
		hidden_id = items[i]+'__'+usergroup_id+'__hidden';
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
	if (task=='contacts_apply'){				
		submitform('contacts_save');		
	}
	if (task=='contacts_save'){	
		document.getElementById('save_and_close').value = '1';			
		submitform('contacts_save');		
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
<form name="adminForm" id="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=contacts'); ?>">	
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
						<span class="am_pagetitle"><?php echo JText::_('COM_CONTACT_SUBMENU_CONTACTS'); ?></span>
						<?php echo JText::_('COM_ACCESSMANAGER_FRONTEND').' '.$this->helper->am_strtolower(JText::_('COM_CONTACT_SUBMENU_CONTACTS').' '.JText::_('JFIELD_ACCESS_LABEL')); ?>.
					</p>
					<?php	
							
					//message if item access is not activated		
					if($this->controller->am_config['contact_active']==false){				
						echo '<p class="am_red">'.JText::_('JNO').' '.JText::_('COM_CONTACT_SUBMENU_CONTACTS').' '.$this->helper->am_strtolower(JText::_('JFIELD_ACCESS_LABEL')).' '.$this->helper->am_strtolower(JText::_('JENABLED')).'. '.JText::_('COM_ACCESSMANAGER_ACTIVATE_IN_PANEL_UNDERNEATH').'.</p>';
					}					
					
					if($this->helper->joomla_version >= '3.0'){
						echo '<div class="sliders_joomla3">';
					}
					
					echo JHtml::_('sliders.start','config-contact-access', array('useCookie'=>1, 'show'=>-1, 'display'=>-1, 'startOffset'=>-1));
					echo JHtml::_('sliders.panel',JText::_('COM_ACCESSMANAGER_CONFIG'), 'config');			
					?>	
					<div class="border_bottom">
						<table class="adminlist am_table">
							<tr>		
								<td width="300">
									<?php echo $this->helper->am_strtolower(JText::_('COM_CONTACT_SUBMENU_CONTACTS')).' '.$this->helper->am_strtolower(JText::_('JFIELD_ACCESS_LABEL')).' '.$this->helper->am_strtolower(JText::_('JENABLED')); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="contact_active" value="true" <?php if($this->controller->am_config['contact_active']){echo 'checked="checked"';} ?> />
								</td>
								<td>					
									<?php 
										echo JText::_('COM_ACCESSMANAGER_FRONTEND').' ';
										echo $this->helper->am_strtolower(JText::_('COM_CONTACT_FIELD_CONFIG_INDIVIDUAL_CONTACT_DISPLAY')).' ';
										echo $this->helper->am_strtolower(JText::_('JFIELD_ACCESS_LABEL')).'.'; 
										echo '<br />'.JText::_('COM_ACCESSMANAGER_FALLBACK').'.';
									?>	
								</td>
							</tr>	
							<tr>		
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_REVERSE_ACCESS'); ?>
								</td>
								<td>
									<input type="checkbox" class="checkbox" name="contact_reverse_access" value="true" <?php if($this->controller->am_config['contact_reverse_access']){echo 'checked="checked"';} ?> />
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
									<label style="white-space: nowrap;"><input type="radio" name="contact_multigroup_access_requirement" value="one_group" class="radio" <?php if($this->controller->am_config['contact_multigroup_access_requirement']=='one_group'){echo 'checked="checked"';} ?> /><?php 
									if($this->controller->am_config['based_on']=='level'){
										echo JText::_('COM_ACCESSMANAGER_ONE_LEVEL'); 
									}else{
										echo JText::_('COM_ACCESSMANAGER_ONE_GROUP'); 
									}
									?></label><br />					
									<label style="white-space: nowrap;"><input type="radio" name="contact_multigroup_access_requirement" value="every_group" class="radio" <?php if($this->controller->am_config['contact_multigroup_access_requirement']=='every_group'){echo 'checked="checked"';} ?> /><?php 					
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
									<input type="checkbox" class="checkbox" name="contact_superadmins" value="true" <?php if($this->controller->am_config['contact_superadmins']){echo 'checked="checked"';} ?> />
								</td>
								<td>
									<?php echo JText::_('JACTION_ADMIN_GLOBAL').' '.JText::_('COM_ACCESSMANAGER_ALWAYS_HAVE_ACCESS'); ?>.												
								</td>
							</tr>			
							<tr>		
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_CONTACTS_MESSAGE_TYPE'); ?><br />(option=com_contact&amp;view=contact)
								</td>
								<td colspan="2">					
									<label><input type="radio" name="contact_message_type" value="alert" class="radio" <?php if($this->controller->am_config['contact_message_type']=='alert'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_COMPONENTS_MESSAGE_TYPE_ALERT'); ?></label><br />					
									<label><input type="radio" name="contact_message_type" value="only_text" class="radio" <?php if($this->controller->am_config['contact_message_type']=='only_text'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_MESSAGE_TYPE_ONLY_TEXT'); ?></label><br />
									<label><input type="radio" name="contact_message_type" value="redirect" class="radio" <?php if($this->controller->am_config['contact_message_type']=='redirect'){echo 'checked="checked"';} ?> /><?php echo JText::_('COM_ACCESSMANAGER_REDIRECT_TO_URL'); ?>:</label>									
									<input type="text" name="no_contact_access_url" class="long_text_field" value="<?php echo $this->controller->am_config['no_contact_access_url']; ?>" />
									<br />
									<label><input type="radio" name="contact_message_type" value="login" class="radio" <?php if($this->controller->am_config['contact_message_type']=='login'){echo 'checked="checked"';} ?> /><?php echo $this->controller->am_strtolower(JText::_('JLOGIN')); ?></label>
								</td>
							</tr>					
							<tr>		
								<td>
									<?php echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS'); ?>
								</td>
								<td colspan="2">
									<?php	
										//contact_default_access		
										echo $this->helper->display_multigrouplevel_select_config('contact', $this->controller->am_config, 1);				
									?>					
								
									<?php 
										echo JText::_('COM_ACCESSMANAGER_DEFAULT_ACCESS_INFO').' '; 
										echo $this->controller->am_strtolower(JText::_('COM_CONTACT_FIELD_CONFIG_INDIVIDUAL_CONTACT_DISPLAY')); 
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
					
								//legend and message if reverse access	
								$this->controller->reverse_access_warning('contact_reverse_access');	
								
								//search bar
								$sortfields = JHtml::_('select.options', $this->getSortFields(), 'value', 'text', $listOrder);	
								echo $this->helper->search_toolbar(1, 1, 1, 1, $this->state->get('filter.search'), $sortfields, $listDirn, $this->pagination->getLimitBox());
								
								if($this->helper->joomla_version < '3.0'){
								?>									
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
					echo $this->controller->accesslevel_selector(0, 1); 
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
					$label = ucfirst(JText::_('COM_ACCESSMANAGER_NAME')).' '; 			
					echo JHTML::_('grid.sort', $label, 'a.name', $listDirn, $listOrder); 			
					?>	
					&nbsp;	
					<?php 
					$label = ucfirst(JText::_('JFIELD_ORDERING_LABEL')).' '; 			
					echo JHTML::_('grid.sort', $label, 'a.ordering', $listDirn, $listOrder); 			
					?>
				</th>
				<?php			
					$this->controller->loop_accesslevels($this->am_grouplevels);			
				?>		
				
			</tr>
				
			<?php
									
				$k = 1;		
				
				//row with select_all checkboxes
				echo '<tr class="row1">';
				echo '<td>&nbsp;</td>';
				echo '<td class="nowrap">'.JText::_('COM_ACCESSMANAGER_SELECTALL').'</td>';		
				foreach($this->am_grouplevels as $am_accesslevel){
					echo '<td style="text-align:center;"><input type="checkbox" name="checkall[]" value="" id="checkall_'.$am_accesslevel->id.'" onclick="select_all('.$am_accesslevel->id.',this.id);" /></td>';
				}
				echo '</tr>';
				
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
					if($item->published=='0'){
						$has_superscript = 'has_superscript';
					}
					echo '<td class="'.$has_superscript.'">';
					echo $item->name;
					if($item->published=='0'){
						echo '<sup class="am_superscript">1</sup>';
					}		
					echo '</td>';					
					foreach($this->am_grouplevels as $am_accesslevel){
						$checked = '';
						$checked_hidden = '';
						if (in_array($item->id.'__'.$am_accesslevel->id, $this->access_contacts)) {
							$checked = 'checked="checked"';
							$checked_hidden = '1';
						}
						echo '<td style="text-align:center;"><input type="hidden" name="item_access_hidden[]" id="'.$item->id.'__'.$am_accesslevel->id.'__hidden" value="'.$item->id.'__'.$am_accesslevel->id.'__hidden__'.$checked_hidden.'" /><input type="checkbox" name="item_access[]" id="'.$item->id.'__'.$am_accesslevel->id.'" onclick="toggle_right(\''.$item->id.'__'.$am_accesslevel->id.'__hidden\');" value="'.$item->id.'__'.$am_accesslevel->id.'" '.$checked.' /></td>';
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
		</table>
	</div>
</form>
<?php
$this->controller->display_footer();
?>