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

//include languages. Reuse or die ;-)#
//load here instead of in view because this template can also get loaded via the control panel view
$lang = JFactory::getLanguage();
//$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);	
$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
//$lang->load('com_modules', JPATH_ADMINISTRATOR, null, false);	

?>
<div class="pi_wrapper_nice">
<fieldset <?php if($this->helper->joomla_version >= '3.0'){echo 'class="panels_joomla3"';} ?> style="border: 0;">
	<legend>
		<?php echo JText::_('COM_ACCESSMANAGER_ACCESS_EDITTING'); ?>		
	</legend>	
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=modulesbackend">
				<span class="panel<?php 
				if($this->controller->am_config['modulebackend_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/modules.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo JText::_('COM_ACCESSMANAGER_MODULES'); ?></span>
			</a>
		</div>
	</div>	
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=componentsbackend">
				<span class="panel<?php if($this->controller->am_config['componentbackend_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/components.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('MOD_MENU_COMPONENTS')); ?></span>
			</a>
		</div>
	</div>	
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=menuitemsbackend">
				<span class="panel<?php 
				if($this->controller->am_config['menuitembackend_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/menu.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS')); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=pluginsbackend">
				<span class="panel<?php 
				if($this->controller->am_config['pluginbackend_active']){ echo '_active';} 				
				?>">
					<img src="components/com_accessmanager/images/panels/plugins.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo JText::_('COM_ACCESSMANAGER_PLUGINS'); ?></span>
			</a>
		</div>
	</div>
</fieldset>
</div>