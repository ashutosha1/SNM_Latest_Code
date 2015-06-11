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
$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);	
$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
$lang->load('com_modules', JPATH_ADMINISTRATOR, null, false);	

?>
<div class="pi_wrapper_nice">
<fieldset <?php if($this->helper->joomla_version >= '3.0'){echo 'class="panels_joomla3"';} ?> style="border: 0;">
	<legend>
		<?php echo JText::_('COM_ACCESSMANAGER_ACCESS_VIEWING'); ?> 		
	</legend>
	<div style="float: left;">
		<div class="icon">						
			<a href="index.php?option=com_accessmanager&view=articles">							
				<span class="panel<?php if($this->controller->am_config['article_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/articles.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('JGLOBAL_ARTICLES')); ?></span>
			</a>			
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=categories">
				<span class="panel<?php 
				if($this->controller->am_config['category_active']){ echo '_active';}
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/categories.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('JCATEGORIES')); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=modules">
				<span class="panel<?php 
				if($this->controller->am_config['module_active']){ echo '_active';} 
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
			<a href="index.php?option=com_accessmanager&view=components">
				<span class="panel<?php if($this->controller->am_config['component_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/components.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('MOD_MENU_COMPONENTS')); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=menuaccess">
				<span class="panel<?php 
				if($this->controller->am_config['menuitem_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">
					<img src="components/com_accessmanager/images/panels/menu.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS').' / '.JText::_('COM_MODULES_HEADING_PAGES')); ?></span>
			</a>
		</div>
	</div>	
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=contacts">
				<span class="panel<?php if($this->controller->am_config['contact_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/contacts.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('COM_CONTACT')); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=weblinks">
				<span class="panel<?php if($this->controller->am_config['weblink_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/weblinks.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('COM_WEBLINKS')); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=parts">
				<span class="panel<?php if($this->controller->am_config['part_active']){ echo '_active';} ?>">
					<img src="components/com_accessmanager/images/panels/parts.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo JText::_('COM_ACCESSMANAGER_PARTS'); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=adminmenumanager">
				<span class="panel<?php 
				if($this->controller->am_config['adminmenumanager_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">				
					<img src="components/com_accessmanager/images/panels/adminmenumanager.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo JText::_('COM_ACCESSMANAGER_ADMIN').' '.$this->helper->am_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS')); ?></span>
			</a>
		</div>
	</div>
	<div style="float: left;">
		<div class="icon">
			<a href="index.php?option=com_accessmanager&view=modulesadmin">
				<span class="panel<?php 
				if($this->controller->am_config['modulesadmin_active']){ echo '_active';} 
				if($this->controller->am_version_type=='free'){ echo '_notinfreeversion';}
				?>">				
					<img src="components/com_accessmanager/images/panels/modules.png" alt="" />
				</span>
				<span class="valign_panel_text"><?php echo $this->helper->am_strtolower(JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION')).' '.JText::_('COM_ACCESSMANAGER_MODULES'); ?></span>
			</a>
		</div>
	</div>
</fieldset>
</div>