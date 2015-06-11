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

class com_accessmanagerInstallerScript {

	function postflight($type, $parent){
		
		$database = JFactory::getDBO();	
		$db = JFactory::getDBO();
		$ds = DIRECTORY_SEPARATOR;
		
		$database->setQuery("CREATE TABLE IF NOT EXISTS #__accessmanager_map (
		`id` int(11) NOT NULL auto_increment,
		`group_id` int(11) NOT NULL,
		`level_id` int(11) NOT NULL,
		`level_title` varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
		)");
		$database->query();	

		$database->setQuery("CREATE TABLE IF NOT EXISTS #__accessmanager_rights (
		`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		`item` varchar(200) NOT NULL,
		`group` int(11) NOT NULL,
		`level` int(11) NOT NULL,
		`type` varchar(200) NOT NULL,
		`access` int(1) NOT NULL,
		PRIMARY KEY (`id`)
		)");
		$database->query();		
	
		$database->setQuery("CREATE TABLE IF NOT EXISTS #__accessmanager_parts (
		`id` int(11) NOT NULL auto_increment,
		`name` VARCHAR( 200 ) NOT NULL,
		`description` TINYTEXT NOT NULL,
		PRIMARY KEY  (`id`)
		)");
		$database->query();										
	
		//table for configuration
		$database->setQuery("CREATE TABLE IF NOT EXISTS #__accessmanager_config (
		`id` varchar(255) NOT NULL,
		`config` text NOT NULL,
		PRIMARY KEY  (`id`)  
		)");
		$database->query();			
		
		//check if config is empty, if so insert default config
		//$database->setQuery("SELECT config FROM #__accessmanager_config WHERE id='am' ");
		//$am_config = $database->loadObjectList();
		
		$am_config = '';
		$query = $db->getQuery(true);
		$query->select('config');
		$query->from('#__accessmanager_config');
		$query->where('id='.$db->q('am'));
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();			
		foreach($rows as $row){		
			$am_config = $row->config;
		}	
		
		if($am_config==''){			
			$configuration = '{"items_active":"","items_reverse_access":"true","items_multigroup_access_requirement":"one_group","items_message_type":"only_text","no_item_access_full_url":"index.php","truncate_article_title":"80","categories_active":"","category_reverse_access":"","category_multigroup_access_requirement":"one_group","modules_active":"","modules_reverse_access":"true","modules_multigroup_access_requirement":"one_group","use_componentaccess":"true","component_reverse_access":"","component_multigroup_access_requirement":"one_group","components_message_type":"only_text","no_component_access_url":"index.php","use_menuaccess":"","menu_reverse_access":"true","menu_multigroup_access_requirement":"one_group","menuaccess_message_type":"only_text","no_menu_access_url":"index.php","am_enabled":0,"version_checker":"true","content_access_together":"every_group","parts_active":"","parts_reverse_access":"","parts_multigroup_access_requirement":"one_group","parts_not_active":"as_access","mod_menu_override":"","based_on":"group","use_componentaccessbackend":"","componentbackend_default":"1","componentbackend_multigroup_access_requirement":"one_group","article_active":"","article_reverse_access":"","article_multigroup_access_requirement":"one_group","category_active":"","module_active":"","module_reverse_access":"","module_multigroup_access_requirement=one_group":"every_group","component_active":"","menuitem_active":"","menuitem_reverse_access":"","menuitem_multigroup_access_requirement":"one_group","part_active":"","part_reverse_access":"","part_multigroup_access_requirement":"one_group","componentbackend_active":"","module_multigroup_access_requirement":"one_group","level_sort":"ordering","display_category_column_article":"","modulebackend_active":"","modulebackend_default":"1","modulebackend_multigroup_access_requirement":"one_group","menuitembackend_active":"","menuitembackend_default":"1","menuitembackend_multigroup_access_requirement":"one_group","pluginbackend_active":"","pluginbackend_default":"1","pluginbackend_multigroup_access_requirement":"one_group","height_multiselect":"10","article_default_access":["1"],"category_default_access":["1"],"module_default_access":["1"],"menuitem_default_access":["1"],"display_import_message":"1","modulebackend_author_access":"","menuitembackend_author_access":"","adminmenumanager_active":"","adminmenumanager_reverse":"","adminmenumanager_multigroup_access_requirement":"one_group","adminmenumanager_default_access":["1"],"adminmenumanager_reverse_access":"","module_superadmins":"true","multiselector_bootstrap":"0","menuitem_superadmins":"","article_superadmins":"","category_superadmins":"","component_superadmins":"","part_superadmins":"","modulesadmin_active":"","modulesadmin_reverse_access":"true","modulesadmin_multigroup_access_requirement":"one_group","modulesadmin_superadmins":"true","modulesadmin_default_access":["1"],"menumodule_override":"1","contact_active":"true","contact_reverse_access":"true","contact_multigroup_access_requirement":"one_group","contact_superadmins":"true","contact_message_type":"only_text","no_contact_access_url":"","contact_default_access":["1"],"weblink_active":"true","weblink_reverse_access":"true","weblink_multigroup_access_requirement":"one_group","weblink_superadmins":"true","weblink_default_access":["1"]}';			
			
			//insert fresh config
			$database->setQuery( "INSERT INTO #__accessmanager_config SET id='am', config='$configuration' ");
			$database->query();	
		}else{
			//there is a config already
			//update if needed
			$new_config = '';
			$config_needs_updating = 0;				
			
			//added in version 1.1.0
			//if config is still string, change to json
			if(!strpos($am_config, '}')){				
				$params = explode("\n", $am_config);
				$am_config = '{';		
				for($n = 0; $n < count($params); $n++){	
					if($n){
						$am_config .= ',';
					}	
					$temp = explode('=',$params[$n]);
					$var = $temp[0];
					$value = '';
					
					if(count($temp)==2){
						$value = trim($temp[1]);						
						if($value=='false'){
							$value = '';
						}
						if($value=='true'){
							$value = 'true';
						}
						
					}
					
					$am_config .= '"'.$var.'":"'.$value.'"';						
				}
				$am_config .= '}';					
				$config_needs_updating = 1;
			}
			
			//added in version 1.1.0
			if(!strpos($am_config, '"level_sort":')){				
				$new_config .= ',"level_sort":"ordering","display_category_column_article":"","modulebackend_active":"","modulebackend_default":"1","modulebackend_multigroup_access_requirement":"one_group","menuitembackend_active":"","menuitembackend_default":"1","menuitembackend_multigroup_access_requirement":"one_group","pluginbackend_active":"","pluginbackend_default":"1","pluginbackend_multigroup_access_requirement":"one_group"';		
				$config_needs_updating = 1;				
			}
			
			//added in version 1.2.0
			if(!strpos($am_config, '"article_height_multiselect":')){				
				$new_config .= ',"height_multiselect":"10","article_default_access":["1"],"category_default_access":["1"],"module_default_access":["1"],"menuitem_default_access":["1"],"display_import_message":"","modulebackend_author_access":"","menuitembackend_author_access":""';		
				$config_needs_updating = 1;				
			}	
			
			//added in version 1.3.0
			if(!strpos($am_config, '"adminmenumanager_active":')){				
				$new_config .= ',"adminmenumanager_active":"","adminmenumanager_reverse":"","adminmenumanager_multigroup_access_requirement":"one_group","adminmenumanager_default_access":["1"],"adminmenumanager_reverse_access":"","module_superadmins":"true"';		
				$config_needs_updating = 1;				
			}	
			
			//added in version 2.0.0
			if(!strpos($am_config, '"multiselector_bootstrap":')){				
				$new_config .= ',"multiselector_bootstrap":"0"';		
				$config_needs_updating = 1;				
			}	
			
			//added in version 2.1.0
			if(!strpos($am_config, '"menuitem_superadmins":')){				
				$new_config .= ',"menuitem_superadmins":"1"';	
				$new_config .= ',"article_superadmins":"1"';
				$new_config .= ',"category_superadmins":"1"';	
				$new_config .= ',"component_superadmins":"1"';
				$new_config .= ',"part_superadmins":"1"';				
				$new_config .= ',"modulesadmin_active":""';
				$new_config .= ',"modulesadmin_reverse_access":"true"';
				$new_config .= ',"modulesadmin_multigroup_access_requirement":"one_group"';
				$new_config .= ',"modulesadmin_superadmins":"true"';
				$new_config .= ',"modulesadmin_default_access":["1"]';
				$config_needs_updating = 1;				
			}
			
			//added in version 2.2.0
			if(!strpos($am_config, '"menumodule_override":')){				
				$new_config .= ',"menumodule_override":"1"';	
				$new_config .= ',"contact_active":""';	
				$new_config .= ',"contact_reverse_access":""';	
				$new_config .= ',"contact_multigroup_access_requirement":"one_group"';	
				$new_config .= ',"contact_superadmins":"1"';	
				$new_config .= ',"contact_message_type":"only_text"';	
				$new_config .= ',"no_contact_access_url":"index.php"';	
				$new_config .= ',"contact_default_access":["1"]';	
				$new_config .= ',"weblink_active":""';	
				$new_config .= ',"weblink_reverse_access":""';	
				$new_config .= ',"weblink_multigroup_access_requirement":"one_group"';	
				$new_config .= ',"weblink_superadmins":"1"';				
				$new_config .= ',"weblink_default_access":["1"]';
				$config_needs_updating = 1;				
			}				
			
			if($config_needs_updating){
				$temp = trim($am_config);
				$config_lenght = strlen($temp);
				$open_ending = substr($temp, 0, $config_lenght-1);				
				$updated_config = $open_ending.$new_config.'}';				
				$database->setQuery( "UPDATE #__accessmanager_config SET config='$updated_config' WHERE id='am' ");
				$database->query();
			}		
		}
		
		//insert temp-rows in config table if not there already		
		//$database->setQuery("SELECT config FROM #__accessmanager_config WHERE id='temp_index' ");
		//$temp_index = $database->loadResult();	
		
		$temp_index = 0;
		$query = $db->getQuery(true);
		$query->select('config');
		$query->from('#__accessmanager_config');
		$query->where('id='.$db->q('temp_index'));
		$rows = $db->setQuery($query);				
		$rows = $db->loadObjectList();	
		foreach($rows as $row){		
			$temp_index = 1;
		}	
		
		if(!$temp_index){
			$database->setQuery( "INSERT INTO #__accessmanager_config SET id='temp_index' ");
			$database->query();
			$database->setQuery( "INSERT INTO #__accessmanager_config SET id='temp_rights' ");
			$database->query();
		}
		
		//install system plugin
		$plgSrc = JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_system'.$ds;
		$plgDst = JPATH_ROOT.$ds.'plugins'.$ds.'system'.$ds.'accessmanager'.$ds;
		if(!file_exists($plgDst)){
			mkdir($plgDst);	
		}
		$system_plugin_success = 0;
		$system_plugin_success = JFile::copy($plgSrc.'accessmanager.php', $plgDst.'accessmanager.php');
		JFile::copy($plgSrc.'accessmanager.xml', $plgDst.'accessmanager.xml');
		JFile::copy($plgSrc.'index.html', $plgDst.'index.html');
		
		if($system_plugin_success){
			echo '<p style="color: #5F9E30;">system plugin installed</p>';		
			//enable plugin
			$database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='accessmanager' AND folder='system' LIMIT 1 ");
			$rows = $database->loadObjectList();
			$system_plugin_id = 0;
			$system_plugin_enabled = 0;
			foreach($rows as $row){	
				$system_plugin_id = $row->extension_id;
				$system_plugin_enabled = $row->enabled;
			}
			if($system_plugin_id){
				//plugin is already installed
				//if(!$system_plugin_enabled){
					//publish plugin
					$database->setQuery( "UPDATE #__extensions SET enabled='1', access='1', ordering='-29000' WHERE extension_id='$system_plugin_id' ");
					$database->query();
				//}
			}else{
				//insert plugin and enable it
				$manifest_cache = '{"legacy":false,"name":"System - Access Manager","type":"plugin","creationDate":"febuari 2012","author":"Carsten Engel","copyright":"Copyright (C) 2012 Carsten Engel, pages-and-items","authorEmail":"-","authorUrl":"www.pages-and-items.com","version":"1.0.0","description":"Enforces various access restrictions as set in component Access-Manager. Don\'t forget to ENABLE this plugin. Make sure this plugin is first in the plugin order of system plugins.","group":""}';
				$manifest_cache = addslashes($manifest_cache);
				$database->setQuery( "INSERT INTO #__extensions SET name='System - Access Manager', type='plugin', element='accessmanager', folder='system', enabled='1', access='1', manifest_cache='$manifest_cache', ordering='-29000' ");
				$database->query();
			}
			echo '<p style="color: #5F9E30;">system plugin enabled</p>';		
		}else{
			echo '<p style="color: red;">system plugin not installed</p><p><a href="http://www.pages-and-items.com/extensions/access-manager/installation" target="_blank">download the system plugin</a> and install with the Joomla installer.</p>';
		}
		
		//install search plugin content
		$plgSrc = JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_search_content'.$ds;
		$plgDst = JPATH_ROOT.$ds.'plugins'.$ds.'search'.$ds.'contentaccessmanager'.$ds;
		if(!file_exists($plgDst)){
			mkdir($plgDst);	
		}
		$search_content_plugin_success = 0;
		$search_content_plugin_success = JFile::copy($plgSrc.'contentaccessmanager.php', $plgDst.'contentaccessmanager.php');
		JFile::copy($plgSrc.'contentaccessmanager.xml', $plgDst.'contentaccessmanager.xml');
		JFile::copy($plgSrc.'index.html', $plgDst.'index.html');
		$language_path = JPATH_ROOT.$ds.'administrator'.$ds.'language'.$ds.'en-GB'.$ds;
		JFile::copy($plgSrc.'language'.$ds.'en-GB.plg_search_contentaccessmanager.ini', $language_path.'en-GB.plg_search_contentaccessmanager.ini');
		JFile::copy($plgSrc.'language'.$ds.'en-GB.plg_search_contentaccessmanager.sys.ini', $language_path.'en-GB.plg_search_contentaccessmanager.sys.ini');		
		if($search_content_plugin_success){
			echo '<p style="color: #5F9E30;">content search plugin installed (only display searchresults use has access to)</p>';		
			//check if already installed
			$database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='contentaccessmanager' AND folder='search' LIMIT 1 ");
			$rows = $database->loadObjectList();
			$search_content_plugin_id = 0;
			$search_content_plugin_enabled = 0;
			foreach($rows as $row){	
				$search_content_plugin_id = $row->extension_id;
				$search_content_plugin_enabled = $row->enabled;
			}
			if($search_content_plugin_id){
				if(!$search_content_plugin_enabled){
					//plugin is already installed but not enabled so enable it			
					$database->setQuery( "UPDATE #__extensions SET enabled='1', access='1' WHERE extension_id='$search_content_plugin_id' ");
					$database->query();	
				}			
			}else{
				//insert plugin and enable it
				$manifest_cache = '{"legacy":false,"name":"plg_search_contentaccessmanager","type":"plugin","creationDate":"febuari 2012","author":"carsten Engel","copyright":"Copyright (C) 2012 Carsten Engel. Engelweb All rights reserved.","authorEmail":"","authorUrl":"www.pages-and-items.com","version":"1.0.0","description":"PLG_SEARCH_CONTENTACCESSMANAGER_XML_DESCRIPTION","group":""}';
				$manifest_cache = addslashes($manifest_cache);
				$params = '{"search_limit":"50","search_content":"1","search_archived":"0"}';
				$params = addslashes($params);
				$database->setQuery( "INSERT INTO #__extensions SET name='Search - Content Access Manager', type='plugin', element='contentaccessmanager', folder='search', enabled='1', access='1', manifest_cache='$manifest_cache', params='$params', ordering='0' ");
				$database->query();
			}
			echo '<p style="color: #5F9E30;">content search plugin enabled</p>';		
		}else{
			echo '<p style="color: red;">content search plugin not installed</p><p><a href="http://www.pages-and-items.com/extensions/access-manager/installation" target="_blank">download the content search plugin</a> and install with the Joomla installer.</p>';
		}
		//disable the Joomla core content search plugin
		$database->setQuery( "UPDATE #__extensions SET enabled='0' WHERE type='plugin' AND element='content' AND folder='search' ");
		$database->query();
		echo '<p style="color: #5F9E30;">Joomla content search plugin disabled</p>';
		
		
		//install search plugin categories
		$plgSrc = JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_search_categories'.$ds;
		$plgDst = JPATH_ROOT.$ds.'plugins'.$ds.'search'.$ds.'categoriesaccessmanager'.$ds;
		if(!file_exists($plgDst)){
			mkdir($plgDst);	
		}
		$search_categories_plugin_success = 0;
		$search_categories_plugin_success = JFile::copy($plgSrc.'categoriesaccessmanager.php', $plgDst.'categoriesaccessmanager.php');
		JFile::copy($plgSrc.'categoriesaccessmanager.xml', $plgDst.'categoriesaccessmanager.xml');
		JFile::copy($plgSrc.'index.html', $plgDst.'index.html');
		$language_path = JPATH_ROOT.$ds.'administrator'.$ds.'language'.$ds.'en-GB'.$ds;
		JFile::copy($plgSrc.'language'.$ds.'en-GB.plg_search_categoriesaccessmanager.ini', $language_path.'en-GB.plg_search_categoriesaccessmanager.ini');
		JFile::copy($plgSrc.'language'.$ds.'en-GB.plg_search_categoriesaccessmanager.sys.ini', $language_path.'en-GB.plg_search_categoriesaccessmanager.sys.ini');		
		if($search_categories_plugin_success){
			echo '<p style="color: #5F9E30;">categories search plugin installed (only display searchresults use has access to)</p>';		
			//check if already installed
			$database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='categoriesaccessmanager' AND folder='search' LIMIT 1 ");
			$rows = $database->loadObjectList();
			$search_categories_plugin_id = 0;
			$search_categories_plugin_enabled = 0;
			foreach($rows as $row){	
				$search_categories_plugin_id = $row->extension_id;
				$search_categories_plugin_enabled = $row->enabled;
			}
			if($search_categories_plugin_id){
				if(!$search_categories_plugin_enabled){
					//plugin is already installed but not enabled so enable it			
					$database->setQuery( "UPDATE #__extensions SET enabled='1', access='1' WHERE extension_id='$search_categories_plugin_id' ");
					$database->query();		
				}		
			}else{
				//insert plugin and enable it
				$manifest_cache = '{"legacy":false,"name":"plg_search_categoriesaccessmanager","type":"plugin","creationDate":"febuari 2012","author":"carsten Engel","copyright":"Copyright (C) 2012 Carsten Engel. Engelweb All rights reserved.","authorEmail":"","authorUrl":"www.pages-and-items.com","version":"1.0.0","description":"PLG_SEARCH_CATEGORIESACCESSMANAGER_XML_DESCRIPTION","group":""}';
				$manifest_cache = addslashes($manifest_cache);
				$params = '{"search_limit":"50","search_content":"1","search_archived":"1"}';
				$params = addslashes($params);
				$database->setQuery( "INSERT INTO #__extensions SET name='Search - Categories Access Manager', type='plugin', element='categoriesaccessmanager', folder='search', enabled='1', access='1', manifest_cache='$manifest_cache', params='$params', ordering='0' ");
				$database->query();
			}
			echo '<p style="color: #5F9E30;">categories search plugin enabled</p>';		
		}else{
			echo '<p style="color: red;">categories search plugin not installed</p><p><a href="http://www.pages-and-items.com/extensions/access-manager/installation" target="_blank">download the content search plugin</a> and install with the Joomla installer.</p>';
		}
		//disable the Joomla core content search plugin
		$database->setQuery( "UPDATE #__extensions SET enabled='0' WHERE type='plugin' AND element='categories' AND folder='search' ");
		$database->query();
		echo '<p style="color: #5F9E30;">Joomla categories search plugin disabled</p>';		
		
		//reset version checker session var
		$app = JFactory::getApplication();
		$app->setUserState( "com_accessmanager.latest_version_message", '' );
		
		//clean up deprecated files from previous install
		$deprecated_files = array();
		$deprecated_files[] = JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'models'.$ds.'config.php';
		$deprecated_files[] = JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'models'.$ds.'expired.php';		
		$deprecated_files[] = JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'models'.$ds.'support.php';
		$latest_version_css = 9;
		for($n = 1; $n < $latest_version_css; $n++){			
			$deprecated_files[] = JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'css'.$ds.'accessmanager'.$n.'.css';
		}		
		foreach($deprecated_files as $deprecated_file){
			if(file_exists($deprecated_file)){
				JFile::delete($deprecated_file);
			}
		}
		
		$this->display_install_page();	
						
	}
	
	function uninstall($installer){		
	
		$db = JFactory::getDBO();	
		$ds = DIRECTORY_SEPARATOR;
		
		//delete system plugin
		$plugin_php = JPATH_PLUGINS.$ds.'system'.$ds.'accessmanager'.$ds.'accessmanager.php';
		$plugin_xml = JPATH_PLUGINS.$ds.'system'.$ds.'accessmanager'.$ds.'accessmanager.xml';
		$system_plugin_success = 0;
		if(file_exists($plugin_php) && file_exists($plugin_xml)){
			$system_plugin_success = JFile::delete($plugin_php);
			JFile::delete($plugin_xml);
		}
		if($system_plugin_success){
			echo '<p style="color: #5F9E30;">system plugin succesfully uninstalled</p>';		
		}else{
			echo '<p style="color: red;">could not uninstall system plugin</p>';
		}   
		$db->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='system' AND element='accessmanager' LIMIT 1");
		$db->query();		
		
		//delete content search plugin
		$plugin_php = JPATH_PLUGINS.$ds.'search'.$ds.'contentaccessmanager'.$ds.'contentaccessmanager.php';
		$plugin_xml = JPATH_PLUGINS.$ds.'search'.$ds.'contentaccessmanager'.$ds.'contentaccessmanager.xml';
		$plugin_html = JPATH_PLUGINS.$ds.'search'.$ds.'contentaccessmanager'.$ds.'index.html';
		$language_path = JPATH_ROOT.$ds.'administrator'.$ds.'language'.$ds.'en-GB'.$ds;
		$plugin_language = $language_path.$ds.'en-GB.plg_search_contentaccessmanager.ini';
		$plugin_language_sys = $language_path.$ds.'en-GB.plg_search_contentaccessmanager.sys.ini';
		$content_plugin_success = 0;
		if(file_exists($plugin_php) && file_exists($plugin_xml)){
			$content_plugin_success = JFile::delete($plugin_php);
			JFile::delete($plugin_xml);
			JFile::delete($plugin_html);
			JFile::delete($plugin_language);
			JFile::delete($plugin_language_sys);
		}
		if($content_plugin_success){
			echo '<p style="color: #5F9E30;">content search plugin succesfully uninstalled</p>';		
		}else{
			echo '<p style="color: red;">could not uninstall content search plugin</p>';
		}   
		$db->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='search' AND element='contentaccessmanager' ");
		$db->query();	
		//enable the Joomla core content search plugin
		$db->setQuery( "UPDATE #__extensions SET enabled='1' WHERE type='plugin' AND element='content' AND folder='search' ");
		$db->query();
		echo '<p style="color: #5F9E30;">Joomla content search plugin enabled</p>';	
			
		//delete categories search plugin
		$plugin_php = JPATH_PLUGINS.$ds.'search'.$ds.'categoriesaccessmanager'.$ds.'categoriesaccessmanager.php';
		$plugin_xml = JPATH_PLUGINS.$ds.'search'.$ds.'categoriesaccessmanager'.$ds.'categoriesaccessmanager.xml';
		$plugin_html = JPATH_PLUGINS.$ds.'search'.$ds.'categoriesaccessmanager'.$ds.'index.html';	
		$plugin_language = $language_path.$ds.'en-GB.plg_search_categoriesaccessmanager.ini';
		$plugin_language_sys = $language_path.$ds.'en-GB.plg_search_categoriesaccessmanager.sys.ini';
		$categories_plugin_success = 0;
		if(file_exists($plugin_php) && file_exists($plugin_xml)){
			$categories_plugin_success = JFile::delete($plugin_php);
			JFile::delete($plugin_xml);
			JFile::delete($plugin_html);
			JFile::delete($plugin_language);
			JFile::delete($plugin_language_sys);
		}
		if($categories_plugin_success){
			echo '<p style="color: #5F9E30;">categories search plugin succesfully uninstalled</p>';		
		}else{
			echo '<p style="color: red;">could not uninstall categories search plugin</p>';
		}   
		$db->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='search' AND element='categoriesaccessmanager' ");
		$db->query();	
		//enable the Joomla core categories search plugin
		$db->setQuery( "UPDATE #__extensions SET enabled='1' WHERE type='plugin' AND element='categories' AND folder='search' ");
		$db->query();
		echo '<p style="color: #5F9E30;">Joomla categories search plugin enabled</p>';	
	
		//delete tables
		$tables_to_drop = array();
		$tables_to_drop[] = '#__accessmanager_config';
		$tables_to_drop[] = '#__accessmanager_map';
		$tables_to_drop[] = '#__accessmanager_parts';
		$tables_to_drop[] = '#__accessmanager_rights';
		for($n = 0; $n < count($tables_to_drop); $n++){
			$query = $db->getQuery(true);
			$query = 'DROP TABLE IF EXISTS '.$db->quoteName($tables_to_drop[$n]);
			$db->setQuery((string)$query);
			$db->query();
		}
		
		$this->display_uninstall_page();
    }
	
	function display_install_page(){
		?>
<div style="width: 800px; text-align: left; background: url(components/com_accessmanager/images/icon.png) 10px 0 no-repeat;">
	<h2 style="padding: 10px 0 10px 70px;">Access-Manager</h2>
	<div style="width: 1000px; overflow: hidden;">	
		<div style="width: 270px; float: left;">
			<p>
				Thank you for using Access-Manager.		
			</p>
			<p>
				<input type="button" value="Go to Access-Manager" onclick="document.location.href='index.php?option=com_accessmanager';" />				
			</p>
		</div>
		<div style="width: 380px; float: left;">
			<p>
				With Access-Manager you can set:
				<ul>
					<li>frontend viewing access for:
						<ul>
							<li>articles</li>
							<li>categories</li>
							<li>modules</li>
							<li>components</li>			
							<li>menu-items</li>			
							<li>parts of articles and templates</li>
							<li>admin menu items</li>			
						</ul>
					</li>
					<li>backend edit access for:
						<ul>
							<li>modules</li>							
							<li>components</li>	
							<li>menu-items</li>	
							<li>plugins</li>									
						</ul>
					</li>
				</ul>				
			</p>
			<p>
				Access-Manager uses Joomla's own usergroups and levels. 
			</p>					
		</div>
		<div style="width: 330px; float: left;">
			<p>
				Check <a href="http://www.pages-and-items.com" target="_blank">www.pages-and-items.com</a> for:
			<ul>
				<li><a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank">updates</a></li>
				<li><a href="http://www.pages-and-items.com/extensions/access-manager/faqs" target="_blank">FAQs</a></li>	
				<li><a href="http://www.pages-and-items.com/forum/37-access-manager" target="_blank">support forum</a></li>	
				<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
				<li><a href="http://www.pages-and-items.com/extensions/access-manager/update-notifications-for-access-manager" target="_blank">subscribe to RSS feed update notifications</a></li>			
			</ul>
			</p>	
			<p>
				Follow us on <a href="http://www.twitter.com/PagesAndItems" target="_blank">Twitter</a> (only update notifications).
			</p>
		</div>
	</div>
</div>
		<?php
	}
	
	function display_uninstall_page(){
		?>
<div style="width: 500px; text-align: left;">
	<h2 style="padding-left: 10px;">Access-Manager</h2>	
	<p>
		Thank you for having used Access-Manager.
	</p>
	<p>
		Why did you uninstall Access-Manager? Missing any features? <a href="http://www.pages-and-items.com/" target="_blank">Let us know</a>.		
	</p>	
	<p>
		Check <a href="http://www.pages-and-items.com/" target="_blank">www.pages-and-items.com</a> for:
		<ul>
			<li><a href="http://www.pages-and-items.com/extensions/access-manager" target="_blank">updates</a></li>
			<li><a href="http://www.pages-and-items.com/extensions/access-manager/faqs" target="_blank">FAQs</a></li>	
			<li><a href="http://www.pages-and-items.com/forum/37-access-manager" target="_blank">support forum</a></li>	
			<li><a href="http://www.pages-and-items.com/my-account/email-update-notifications" target="_blank">email notification service for updates and new extensions</a></li>	
			<li><a href="http://www.pages-and-items.com/extensions/access-manager/update-notifications-for-access-manager" target="_blank">subscribe to RSS feed update notifications</a></li>			
		</ul>
	</p>	
</div>
		<?php
	}
}

?>
