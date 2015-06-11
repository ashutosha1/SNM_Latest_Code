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

?>
<form name="adminForm" id="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_accessmanager&view=tools'); ?>">
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>		

<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<div class="clr"> </div><!-- needed for some admin templates -->
		<div class="pi_wrapper_nice">
		<p>
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS_INFO'); ?>.
		</p>
		<p>
			<label>
				<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_article_access" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_category_access" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_module_access" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" checked="checked" name="import_menuitem_access" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_MENU_ACCESS'); ?>
			</label>
			<br />
			<br />
			<select name="import_rights_to">
				<option value="level">
					<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_ACCESSLEVELS'); ?>
				</option>
				<option value="group" selected="selected">
					<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_GROUPS'); ?>
				</option>
			</select>
			<?php 	
				echo JText::_('COM_ACCESSMANAGER_ACCESS_CURRENTLY_BASED_ON').': '; 
				if($this->controller->am_config['based_on']=='group'){
					echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); 
				}else{
					echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); 
				}
			?>
			<br />
			<br />
			<input type="button" value="<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS'); ?>" onclick="Joomla.submitbutton('import_access_settings')" />
		</p>
		</div>
		<div class="pi_wrapper_nice">
		<p>
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS_AMM').' '.JText::_('COM_ACCESSMANAGER_ADMINMENUMANAGER_ACCESS').' '.JText::_('COM_ACCESSMANAGER_FROM'); ?> component <a href="http://www.pages-and-items.com/extensions/admin-menu-manager" target="_blank">Admin-Menu-Manager</a>.
		</p>
		<p>
			<select name="import_rights_to_amm">
				<option value="level">
					<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_ACCESSLEVELS'); ?>
				</option>
				<option value="group" selected="selected">
					<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_TO_GROUPS'); ?>
				</option>
			</select>
			<?php 	
				echo JText::_('COM_ACCESSMANAGER_ACCESS_CURRENTLY_BASED_ON').': '; 
				if($this->controller->am_config['based_on']=='group'){
					echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); 
				}else{
					echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); 
				}
			?>
			<br />
			<br />
			<input type="button" value="<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS'); ?>" onclick="Joomla.submitbutton('import_access_settings_amm')" />
		</p>			
		</div>
		<div class="pi_wrapper_nice">
		<p>
			<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS_AMM').' '.JText::_('COM_ACCESSMANAGER_ACCESSRIGHTS').' '.JText::_('COM_ACCESSMANAGER_FROM'); ?> component <a href="http://www.pages-and-items.com/extensions/frontend-user-access" target="_blank">Frontend-User-Access</a>.
			<br />
			<a href="http://www.pages-and-items.com/extensions/access-manager/faqs?faqitem=install_import_fua_rights" target="_blank"><?php echo JText::_('COM_ACCESSMANAGER_READ_MORE'); ?></a>
		</p>
		<p>
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_article_access_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_ITEM_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_category_access_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_CATEGORY_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_module_access_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_MODULE_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_component_access_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_COMPONENT_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_menuitem_access_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_MENU_ACCESS'); ?>
			</label>
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_part_access_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_PART_ACCESS'); ?>
			</label>
			<br />
			<br />
			<label>
				<input type="checkbox"  class="checkbox" value="true" name="import_parts_fua" /> 
				<?php echo JText::_('COM_ACCESSMANAGER_IMPORT').' '.JText::_('COM_ACCESSMANAGER_PARTS'); ?>				
			</label>
			<br />
			<?php echo JText::_('COM_ACCESSMANAGER_PARTS_IMPORT'); ?> {fua_part id=3} content {/fua_part} > {am_part id=3} content {/am_part}
			<br />
			<table class="am_table">
				<tr>
					<th width="230">
						<?php echo 'Frontend-User-Access '.JText::_('COM_ACCESSMANAGER_USERGROUPS'); ?>
					</th>
					<th>
						<?php echo 'Joomla '; 
							if($this->controller->am_config['based_on']=='group'){
								echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); 
							}else{
								echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); 
							}
						?>
						 (<?php 	
							echo JText::_('COM_ACCESSMANAGER_ACCESS_CURRENTLY_BASED_ON').': '; 
							if($this->controller->am_config['based_on']=='group'){
								echo JText::_('COM_ACCESSMANAGER_USERGROUPS'); 
							}else{
								echo JText::_('COM_ACCESSMANAGER_ACCESSLEVELS'); 
							}
						?>)					
					</th>
				</tr>
				
			<?php 
			$temp = $this->get_fua_groups();
			$table_exists = $temp[0];
			$fua_usergroups = $temp[1];
			if(!count($fua_usergroups)){
				//there are no groups				
				echo '<tr><td>';				
				if(!$table_exists){
					echo JText::_('COM_ACCESSMANAGER_USERGROUPS').' '.JText::_('COM_ACCESSMANAGER_NO_TABLE_FOUND');
					echo '<br />';
					echo '<a href="http://www.pages-and-items.com/extensions/access-manager/faqs?faqitem=install_import_fua_rights" target="_blank">'.JText::_('COM_ACCESSMANAGER_READ_MORE').'</a>';
				}else{
					echo 'Frontend-User-Access ';
					echo JText::_('COM_ACCESSMANAGER_HAS_NO_GROUPS');
				}
				echo '</td><td>';
				echo $this->grouplevels_selector;
				echo '</td></tr>';
			}else{
				//there are groups
				for($n = 0; $n < count($fua_usergroups); $n++){
					?>
					<tr>
						<td class="nowrap">
							<input type="hidden" name="fua_groups[]" value="<?php echo $fua_usergroups[$n][0]; ?>" />
							<?php 
							echo $fua_usergroups[$n][1]; 
							if($fua_usergroups[$n][0]==9){
								echo ' ('.JText::_('COM_ACCESSMANAGER_LOGGEDIN_NOT_ASSIGNED').')';
							}
							?>					
						</td>
						<td>
							<?php echo $this->grouplevels_selector; ?>
						</td>
					</tr>
					<?php
				}
			}
			?>				
			</table>
			<br /><br />
			<input type="button" value="<?php echo JText::_('COM_ACCESSMANAGER_IMPORT_ACCESS'); ?>" onclick="Joomla.submitbutton('import_access_rights_fua')" />
		</p>			
		</div>
		
		<div class="pi_wrapper_nice">
		<p>
			<?php echo JText::_('COM_ACCESSMANAGER_TOOLFUAGROUPS').' ('.JText::_('COM_ACCESSMANAGER_FROM'); ?> component <a href="http://www.pages-and-items.com/extensions/frontend-user-access" target="_blank">Frontend-User-Access</a>).
		</p>
		<p>
			<table class="am_table">
				<tr>
					<th width="230">
						<?php echo 'Frontend-User-Access '.JText::_('COM_ACCESSMANAGER_USERGROUPS'); ?>
					</th>
					<th>
						<?php echo 'Joomla '.JText::_('COM_ACCESSMANAGER_USERGROUPS'); ?>						 			
					</th>
				</tr>
				
			<?php 
			$temp = $this->get_fua_groups();
			$table_exists = $temp[0];
			$fua_usergroups = $temp[1];
			if(!count($fua_usergroups)){
				//there are no groups				
				echo '<tr><td>';				
				if(!$table_exists){
					echo JText::_('COM_ACCESSMANAGER_USERGROUPS').' '.JText::_('COM_ACCESSMANAGER_NO_TABLE_FOUND');					
				}else{
					echo 'Frontend-User-Access ';
					echo JText::_('COM_ACCESSMANAGER_HAS_NO_GROUPS');
				}
				echo '</td><td>';
				echo $this->groups_selector;
				echo '</td></tr>';
			}else{
				//there are groups
				
				?>
				<tr>
					<td class="nowrap">						
						<?php 						
						echo '<select name="fuagroup_select" class="chzn-done">';
						echo '<option value="0">';
						echo ' - '.JText::_('COM_USERS_BATCH_GROUP').' - ';
						echo '</option>';
						for($n = 0; $n < count($fua_usergroups); $n++){
							echo '<option value="'.$fua_usergroups[$n][0].'">';
							echo $fua_usergroups[$n][1];
							if($fua_usergroups[$n][0]==9){
								echo ' ('.JText::_('COM_ACCESSMANAGER_LOGGEDIN_NOT_ASSIGNED').')';
							}
							echo '</option>';
						}
						echo '</select>';
						?>	
										
					</td>
					<td>
						<?php echo $this->groups_selector; ?>
					</td>
				</tr>
				<?php
				
			}
			?>				
			</table>
			<br />
			<br />
			<input type="button" value="<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>" onclick="Joomla.submitbutton('assign_users_from_fua_groups')" />
		</p>			
		</div>
	</div>	
</form>
<?php
$this->controller->display_footer();
?>