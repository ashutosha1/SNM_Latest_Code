<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.access.access');

class plgSystemAccessmanager extends JPlugin{

	protected $version_type = 'free';	
	protected $am_config;
	protected $user_id;
	protected $is_super_user = 0;
	protected $option;
	protected $view;
	protected $layout;
	protected $login_url = '';
	protected $trial_valid = 1;
	protected $inherited_right = '';
	protected $access_script;	
	protected $fua_enabled = 0;
	protected $helper;
	public $subject;
	public $config;
	
	function plgSystemAccessmanager(& $subject, $config){
	
		$database = JFactory::getDBO();	
		$app = JFactory::getApplication();
		$ds = DIRECTORY_SEPARATOR;
		parent::__construct($subject, $config);		
		
		$this->subject = $subject;
		$this->config = $config;
		
		require_once(JPATH_ROOT.$ds.'components'.$ds.'com_accessmanager'.$ds.'checkaccess2.php');
		$this->access_script = new accessmanagerAccessChecker();
		
		if($app->isAdmin()){
			require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'helpers'.$ds.'accessmanager.php');
			$this->helper = new accessmanagerHelper();
		}
		
		//get config		
		$this->am_config = $this->access_script->get_config();
		
		//get user id
		$user = JFactory::getUser();		
		$this->user_id = $user->get('id');	
		
		$this->is_super_user = $this->access_script->get_is_super_user();
		$this->fua_enabled = $this->access_script->get_fua_enabled();			
			
		//dirty workaround to prevent site dying when used together with any of 
		//these plugins which load the module helper outside the plugin class
		if(file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'advancedmodules'.$ds.'advancedmodules.php') || 
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'metamod'.$ds.'metamod.php') ||
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'plg_jamenuparams'.$ds.'plg_jamenuparams.php') ||
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'plg_gkextmenu'.$ds.'plg_gkextmenu.php') ||
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'jat3'.$ds.'jat3.php') ||
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'j3'.$ds.'j3.php') ||
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'t3'.$ds.'t3.php') ||
		file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'nnframework'.$ds.'nnframework.php')
		){	
			if(JRequest::getVar('option', '')=='com_search' && file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'nnframework'.$ds.'nnframework.php')){
				$this->onAfterRoute();//to deal with the nnframework override on searches				
			}		
			$this->onAfterInitialise();			
		}
		
		$uri = JFactory::getURI();
		$request_url = $uri->toString();
		$return_url = base64_encode($request_url);	
		$this->login_url = JURI::root().'index.php?option=com_users&view=login&return='.$return_url;				
	}
	
	function onAfterRender(){	
	
		$app = JFactory::getApplication();
		$this->option = JRequest::getVar('option', '');		
		$this->view = JRequest::getVar('view', '');	
		$this->layout = JRequest::getVar('layout', '');	
		
		if($this->fua_enabled){
			return true;
		}
		
		if($this->am_config['am_enabled']){
			if(!$app->isAdmin()){	
				if($this->trial_valid){										
					$this->check_component_access_frontend();					
					$this->check_article_view_access();	
					$this->check_contact_access();			
				}							
			}else{				
				$this->check_component_access_backend();
				$this->check_plugin_access_backend();				
			}			
		}
		//parts access and dropdown menu
		$this->work_on_buffer();
	}	

	function work_on_buffer(){
		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();		
		$buffer = JResponse::getBody();	
		
		if(!$app->isAdmin() && $this->am_config['am_enabled']){
			//frontend
				
			//check for any parts to process for performance			
			$pos = strpos($buffer, '{am_part');
			if($pos){				
				$regex = "/{am_part(.*?){\/am_part}/is";			
				preg_match_all($regex, $buffer, $matches);						
				$regex_id = "/id=(.*?)}/is";
				$part_tags = array_unique($matches[1]);
				
				foreach($part_tags as $part_tag){
				
					//take it apart
					$whole_tag = '{am_part'.$part_tag.'{/am_part}';	
					$tag_array = explode('{else}', $part_tag);
					$first_bit = $tag_array[0];
					preg_match_all($regex_id, $first_bit, $matches);					
					$part_id = $matches[1][0];
					$id_bit = 'id='.$part_id.'}';
					$content_with_access = str_replace($id_bit, '', $first_bit);
					$content_no_access = '';
					if(isset($tag_array[1])){
						$content_no_access = $tag_array[1];
					}												
					
					//check if parts restrictions is enabled
					if(!$this->am_config['part_active']){
						//parts restrictions is not enabled
						//check in config what to do
						if($this->am_config['parts_not_active']=='as_access'){
							//show as if user has access
							$buffer = str_replace($whole_tag, $content_with_access, $buffer);	
						}elseif($this->am_config['parts_not_active']=='as_no_access'){
							//show as if user has no access
							$buffer = str_replace($whole_tag, $content_no_access, $buffer);	
						}elseif($this->am_config['parts_not_active']=='nothing'){
							//take complete tag out
							$buffer = str_replace($whole_tag, '', $buffer);	
						}
						//when option is code, do no replacing at all										
					}else{
						//parts restrictions is enabled	
					
						$has_access_part = true;
						if(!$this->access_script->check_access($part_id, 'part', $this->am_config['part_multigroup_access_requirement'], $this->am_config['part_reverse_access'])){	
							$has_access_part = false;
						}
						
						//replace tag with access or no access content
						if($has_access_part || ($this->is_super_user && $this->am_config['part_superadmins'])){
							//show content with access						
							$buffer = str_replace($whole_tag, $content_with_access, $buffer);														
						}else{
							//show content no access
							$buffer = str_replace($whole_tag, $content_no_access, $buffer);
						}
					
					}				
					
				}
			}//end parts restrictions	
			
			
			if($this->option=='com_content' && $this->view=='form' && $this->am_config['article_active']){					
				$buffer = $this->display_multigrouplevel_select($buffer, 'article');
			}						
						
		}else{
			//backend
			
			//dropdown menu access manager
			if($this->option=='com_accessmanager'){	
				//get com_users lang
				$lang = JFactory::getLanguage();
				$lang->load('com_users', JPATH_ADMINISTRATOR, null, false);	
				$lang->load('mod_menu', JPATH_ADMINISTRATOR, null, false);	
				$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);	
				$lang->load('com_modules', JPATH_ADMINISTRATOR, null, false);	
						
				$regex = "/\<ul id=\"submenu\"(.*?)\<\/ul\>/is";						
				preg_match_all($regex, $buffer, $matches);					
				if(isset($matches[1][0])){				
					$ori = $matches[1][0];					
					$accessvievving = array();		
					$accessvievving[$this->helper->am_strtolower(JText::_('JGLOBAL_ARTICLES'))] = 'articles';
					$accessvievving[$this->helper->am_strtolower(JText::_('JCATEGORIES'))] = 'categories';
					$accessvievving[JText::_('COM_ACCESSMANAGER_MODULES')] = 'modules';
					$accessvievving[$this->helper->am_strtolower(JText::_('MOD_MENU_COMPONENTS'))] = 'components';
					$accessvievving[JText::_($this->helper->am_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS').' / '.JText::_('COM_MODULES_HEADING_PAGES')))] = 'menuaccess';					
					$accessvievving[$this->helper->am_strtolower(JText::_('COM_CONTACT'))] = 'contacts';
					$accessvievving[$this->helper->am_strtolower(JText::_('COM_WEBLINKS'))] = 'weblinks';
					$accessvievving[JText::_('COM_ACCESSMANAGER_PARTS')] = 'parts';
					$accessvievving[JText::_('COM_ACCESSMANAGER_ADMIN').' '.$this->helper->am_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS'))] = 'adminmenumanager';					
					$accessvievving[$this->helper->am_strtolower(JText::_('COM_USERS_CONFIG_FIELD_USERACTIVATION_OPTION_ADMINACTIVATION')).' '.JText::_('COM_ACCESSMANAGER_MODULES')] = 'modulesadmin';							
					$accessedit = array();	
					$accessedit[JText::_('COM_ACCESSMANAGER_MODULES')] = 'modulesbackend';					
					$accessedit[$this->helper->am_strtolower(JText::_('MOD_MENU_COMPONENTS'))] = 'componentsbackend';	
					$accessedit[$this->helper->am_strtolower(JText::_('COM_MENUS_SUBMENU_ITEMS'))] = 'menuitemsbackend';					
					$accessedit[JText::_('COM_ACCESSMANAGER_PLUGINS')] = 'pluginsbackend';
					$new = '';
					if($this->helper->joomla_version >= '3.0'){
						$new .= ' class="nav nav-list">';
						//add css for indent
						//can not do this the nice way as buffer is already sent
						$new .= '<style>#submenu li:hover ul, #submenu li.sfhover ul{left: 175px; margin-top: -5px;}</style>';
					}else{
						$new .= '>';
					}									
					$new .= '<li';
					if($this->view=='panel'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if($this->view=='panel'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=panel">'.JText::_('COM_ACCESSMANAGER_CPANEL').'</a>';
					$new .= '</li>';
					$new .= '<li';
					if($this->view=='configuration'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if($this->view=='configuration'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=configuration">'.JText::_('COM_ACCESSMANAGER_CONFIG').'</a>';
					$new .= '</li>';
					$new .= '<li';
					if($this->view=='users'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if($this->view=='users'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=users">'.JText::_('COM_ACCESSMANAGER_USERS').'</a>';
					$new .= '</li>';
					$new .= '<li';
					if(in_array($this->view, $accessvievving) || $this->view=='accessvievving' || $this->view=='part'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if(in_array($this->view, $accessvievving) || $this->view=='accessvievving' || $this->view=='part'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=accessvievving">'.JText::_('COM_ACCESSMANAGER_ACCESS_VIEWING').'</a>';
					$new .= '<ul>';
					for($n = 0; $n < count($accessvievving); $n++){
						$row = each($accessvievving);
						$new .= '<li';
						if($this->view==$row['value']){
							$new .= ' class="on"';
						}
						//selected for subsubmenu
						if($this->view=='part' && $row['value']=='parts'){
							$new .= ' class="on"';
						}
						$new .= '>';
						$new .= '<a';
						if($this->view==$row['value']){
							$new .= ' class="active"';
						}
						if($this->view=='part' && $row['value']=='parts'){
							$new .= ' class="active"';
						}
						$new .= ' style="background-image: url(components/com_accessmanager/images/menu/accessmanager_'.$row['value'].'.png);"';
						$new .= ' href="index.php?option=com_accessmanager&view='.$row['value'].'">'.$row['key'].'</a>';
						$new .= '</li>';
					}
					$new .= '</ul>';
					$new .= '</li>';
					$new .= '<li';
					if(in_array($this->view, $accessedit) || $this->view=='accessedit'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if(in_array($this->view, $accessedit) || $this->view=='accessedit'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=accessedit">'.JText::_('COM_ACCESSMANAGER_ACCESS_EDITTING').'</a>';
					$new .= '<ul>';
					for($n = 0; $n < count($accessedit); $n++){
						$row = each($accessedit);
						$new .= '<li';
						if($this->view==$row['value']){
							$new .= ' class="on"';
						}
						$new .= '>';
						$new .= '<a';
						if($this->view==$row['value']){
							$new .= ' class="active"';
						}
						$new .= ' style="background-image: url(components/com_accessmanager/images/menu/accessmanager_'.$row['value'].'.png);"';
						$new .= ' href="index.php?option=com_accessmanager&view='.$row['value'].'">'.$row['key'].'</a>';
						$new .= '</li>';
					}
					$new .= '</ul>';
					$new .= '</li>';
					$new .= '<li';
					if($this->view=='tools'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if($this->view=='tools'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=tools">'.JText::_('COM_ACCESSMANAGER_TOOLS').'</a>';
					$new .= '</li>';
					$new .= '<li';
					if($this->view=='info'){
						$new .= ' class="active"';
					}
					$new .= '>';
					$new .= '<a';
					if($this->view=='info'){
						$new .= ' class="active"';
					}
					$new .= ' href="index.php?option=com_accessmanager&view=info">'.JText::_('COM_ACCESSMANAGER_INFO').'</a>';
					$new .= '</li>';				
			
					$buffer = str_replace($ori, $new, $buffer);
				}
			}			
			//end dropdown menu access manager			
			
			if($this->am_config['am_enabled']){
				if($this->option=='com_content' && $this->view=='article' && $this->am_config['article_active']){				
					$buffer = $this->display_multigrouplevel_select($buffer, 'article');
				}	
				
				if($this->option=='com_pagesanditems' && $this->view=='item' && $this->am_config['article_active']){				
					$buffer = $this->display_multigrouplevel_select($buffer, 'piarticle');				
				}
				
				if($this->option=='com_contact' && $this->view=='contact' && $this->am_config['contact_active']){							
					$buffer = $this->display_multigrouplevel_select($buffer, 'contact');				
				}
				
				if($this->option=='com_weblinks' && $this->view=='weblink' && $this->am_config['weblink_active']){							
					$buffer = $this->display_multigrouplevel_select($buffer, 'weblink');				
				}						
			}
		}	
		//write buffer
		JResponse::setBody($buffer);	 
	}	
	
	function display_multigrouplevel_select($buffer, $type){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			$id = JRequest::getVar('id', '');
			if($type=='piarticle'){
				$id = JRequest::getVar('itemId', '');
				$type = 'article';
			}
		}else{
			$id = JRequest::getVar('a_id', '');//articles from the frontend
			if($type=='piarticle'){
				$id = JRequest::getVar('item_id', '');
				$type = 'article';
			}
		}	
			
		//get grouplevels with rights for item (reverse or not)
		$groups_levels = array();
		if($id){			
			$based_on = $this->am_config['based_on'];		
			$database->setQuery("SELECT `".$based_on."` "
			." FROM #__accessmanager_rights "
			." WHERE item='$id' "		
			." AND type='$type' "
			);		
			$groups_levels = $database->loadColumn();
		}else{
			//new item so get default access for that type
			$groups_levels = $this->am_config[$type.'_default_access'];
		}
		
		//include superusers
		$where_not_superuser = "WHERE a.id<>'8' ";
		if($type=='adminmenumanager' || $type=='menuitem' || $type=='article' || $type=='category' || $type=='component' || $type=='part' || $type=='modulesadmin'){
			$where_not_superuser = '';
		}
		
		//only backend users
		$where_only_backend = '';
		if($type=='adminmenumanager' || $type=='modulesadmin'){
			$this->helper->get_backend_usergroups();
			$groups_backend = $this->helper->backend_usergroups;					
			$backend_usergroups = implode(',', $groups_backend);			
			$where_only_backend = "WHERE a.id in (".$backend_usergroups.") ";			
		}		
		
		//get groupslevels
		if($this->am_config['based_on']=='level'){	
			$level_sort = $this->am_config['level_sort'];		
			$database->setQuery("SELECT id, title "
			."FROM #__viewlevels "		
			."ORDER BY $level_sort "
			);	
		}else{						
			$database->setQuery("SELECT a.id as id, a.title as title, COUNT(DISTINCT b.id) AS hyrarchy "
			."FROM #__usergroups AS a "
			."LEFT JOIN #__usergroups AS b ON a.lft > b.lft AND a.rgt < b.rgt "
			.$where_not_superuser	
			.$where_only_backend		
			."GROUP BY a.id "		
			."ORDER BY a.lft ASC "		
			);			
		}	
		$accesslevels = $database->loadObjectList();
		
		//make select
		$height = $this->am_config['height_multiselect'];
		if($height=='all'){	
			$height = count($accesslevels);
		}
		$new_select = '<select name="am_levelsgroups_access[]" multiple="multiple" size="'.$height.'"';
		if(!$this->am_config['multiselector_bootstrap']){
			$new_select .= ' class="chzn-done"';
		}
		$new_select .= '>';
		foreach($accesslevels as $accesslevel){
			$new_select .= '<option value="'.$accesslevel->id.'"';
			if(in_array($accesslevel->id, $groups_levels)){
				$new_select .= ' selected="selected"';
			}
			$new_select .= '>';
			if($this->am_config['based_on']=='group' && !$this->am_config['multiselector_bootstrap']){	
				$new_select .= str_repeat('- ',$accesslevel->hyrarchy);	
			}						
			$new_select .= $accesslevel->title;						
			$new_select .= '</option>';
		}
		$new_select .= '</select>';	
		
		//get it all in there
		$regex = "/id=\"jform_access-lbl\"(.*?)id=\"jform_access\"/is";	
		if($type=='adminmenumanager'){			
			$regex = "/td colspan=\"2\"(.*?)name=\"access\"/is";	
		}		
		preg_match_all($regex, $buffer, $matches);			
		if(isset($matches[0][0])){
	
			$old = $matches[0][0];			
			$new = str_replace(' id="jform_access"',' style="display: none;" id="jform_access" class="chzn-done"', $old);
			if($type=='adminmenumanager'){
				$new = str_replace(' name="access"',' style="display: none;" name="access" class="chzn-done"', $old);
			}
			if($this->am_config[$type.'_reverse_access']){
				$lang = JFactory::getLanguage();
				$lang->load('com_accessmanager', JPATH_ADMINISTRATOR, null, false);
				if($this->am_config['based_on']=='group'){
					$message = 'COM_ACCESSMANAGER_REVERSE_MESSAGE_GROUPS';
				}else{
					$message = 'COM_ACCESSMANAGER_REVERSE_MESSAGE_LEVELS';
				}
				$right_after = '</label>';
				if($type=='adminmenumanager'){			
					$right_after = 'td colspan="2">';	
				}
				$br = '';
				if($type=='adminmenumanager'){			
					$br = '<br /><br />';						
				}					
				if($app->isAdmin()){
					$new = str_replace($right_after, $right_after.'<span style="float: left; width: auto; margin: 5px 5px 5px 0;">'.JText::_($message).'</span><label>&nbsp;</label>'.$br, $new);	
					
								
				}else{
					$new = str_replace($right_after, $right_after.'<span style="display: inline-block; padding-left: 2px;">'.JText::_($message).'</span></div><div class="formelm"><label>&nbsp;</label>'.$br, $new);
				}
			}			
			$new = str_replace('<select', $new_select.'<select', $new);			
			$buffer = str_replace($old, $new, $buffer);
		}
		
		return $buffer;
	}
	
	function check_component_access_backend(){
	
		$app = JFactory::getApplication();	
		
		if($this->am_config['am_enabled']
			&& $this->am_config['componentbackend_active'] 
			&& !$this->is_super_user 
			&& $this->option!='com_login' 
			&& $this->option!='com_cpanel' 
			&& !($this->option=='com_accessmanager' && $this->view=='noaccess')
			){	
			if(!$this->check_access_backend($this->option, 'componentbackend', $this->am_config['componentbackend_multigroup_access_requirement'])){									
				$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component&type=component', false);										
				$app->redirect($url);
			}
		}		
	}
	
	function check_plugin_access_backend(){
	
		$app = JFactory::getApplication();	
		
		if($this->am_config['am_enabled']
			&& $this->am_config['pluginbackend_active'] 
			&& !$this->is_super_user 
			&& $this->option=='com_plugins'	&& $this->view=='plugin'	
			){	
			$id = intval(JRequest::getVar('extension_id', ''));				
			if(!$this->check_access_backend($id, 'pluginbackend', $this->am_config['pluginbackend_multigroup_access_requirement'])){									
				$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component&type=plugin', false);										
				$app->redirect($url);
			}
		}		
	}
	
	function check_access_backend($item, $type, $multigroups){
	
		$database = JFactory::getDBO();	
		
		$groups = $this->access_script->get_user_grouplevels('group');			
		$database->setQuery("SELECT `group`, access "
		." FROM #__accessmanager_rights "		
		." WHERE item='$item' "		
		." AND type='$type' "
		);
		$rights_rows = $database->loadObjectList();		
		$rights = array();
		foreach($rights_rows as $right){					
			$rights[] = $item.'__'.$right->group.'__'.$right->access;
		}
		$access_array = array();
		foreach($groups as $group_row){			
			$temp = '';
			foreach($rights_rows as $right_row){
				if($right_row->group==$group_row){
					$temp = $right_row->access;
					break;
				}				
			}
			if($temp==''){
				//get inherited access
				$groups_backend = $this->get_all_grouplevels('backend');				
				$this->inherited_right = '';
				$this->get_inherited_right_backend($item, $group_row, $rights, $groups_backend, $type);
				$temp = $this->inherited_right;
			}
			$access_array[] = $temp;
		}							
		if($multigroups=='every_group'){
			if(in_array('0', $access_array)){
				$access = false;
			}else{
				$access = true;
			}
		}else{
			if(in_array('1', $access_array)){
				$access = true;
			}else{
				$access = false;
			}				
		}		
		return $access;
	}
	
	function get_inherited_right_backend($item, $group, $rights, $groups, $type){		
		
		//if parent is public, set to default
		if($type=='modulebackend' && $group=='1'){
			$this->inherited_right = $this->am_config['modulebackend_default'];			
			return;
		}
		if($type=='componentbackend' && $group=='1'){
			$this->inherited_right = $this->am_config['componentbackend_default'];			
			return;
		}
		if($type=='menuitembackend' && $group=='1'){
			$this->inherited_right = $this->am_config['menuitembackend_default'];			
			return;
		}
		if($type=='pluginbackend' && $group=='1'){
			$this->inherited_right = $this->am_config['pluginbackend_default'];			
			return;
		}
		
		//get parent group
		$parent = 'no';
		foreach($groups as $row){	
			if($row->id==$group){
				$parent = $row->parent_id;
				break;
			}
		}
		
		//check access for this item in parent group
		$access = '';
		$needle_1 = $item.'__'.$group.'__1';				
		if(in_array($needle_1, $rights)){
			$access = '1';
		}		
		$needle_0 = $item.'__'.$group.'__0';
		if(in_array($needle_0, $rights)){
			$access = '0';
		}		
		
		//recurse or parse		
		if($access=='' && $parent!='no'){
			//parent is also inheriting so go level up			
			$this->get_inherited_right_backend($item, $parent, $rights, $groups, $type);
		}else{					
			$this->inherited_right = $access;
		}		
	}
	
	function get_all_grouplevels($backend=0){
		
		$database = JFactory::getDBO();
		
		$where = '';
		if($this->am_config['based_on']=='group' || $backend){
			$where .= "WHERE id<>'8' ";
		}
		if($this->am_config['based_on']=='level' && !$backend){			
			$database->setQuery("SELECT id, title "
			."FROM #__viewlevels "
			.$where
			."ORDER BY title "
			);	
		}else{
			$database->setQuery("SELECT id, title, parent_id "
			."FROM #__usergroups "
			.$where			
			."ORDER BY title "
			);	
		}
		$grouplevels = $database->loadObjectList();		
		return $grouplevels;
	}		
	
	function check_article_view_access(){
		
		$app = JFactory::getApplication();			
		
		//get vars			
		$item_id_temp = JRequest::getVar('id', '');	
		if(strpos($item_id_temp, ':')){
			$pos_item_id = strpos($item_id_temp, ':');
			$item_id = intval(substr($item_id_temp, 0, $pos_item_id));	
		}else{
			$item_id = intval($item_id_temp);	
		}					
			
		//start checking item full view access		
		if(
		$this->option=='com_content' &&
		($this->view=='article' && ($this->layout=='default' || $this->layout=='')) &&
		($this->am_config['article_active'] || $this->am_config['category_active']) &&
		(!$this->is_super_user || ($this->is_super_user && !$this->am_config['article_superadmins']) || ($this->is_super_user && !$this->am_config['category_superadmins'])) 
		){				
		
			//if no access
			if(!$this->access_script->check_article_access($item_id)){								
				if($this->am_config['items_message_type']=='alert'){
					//javascript alert	
					$this->do_alert();
				}elseif($this->am_config['items_message_type']=='redirect'){
					//redirect
					$url = JURI::root().$this->am_config['no_item_access_full_url'];																		
					$app->redirect($url);	
				}elseif($this->am_config['items_message_type']=='login'){						
					$url = $this->login_url;
					$app->redirect($url, JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));		
				}else{								
					//white page with message													
					$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component', false);										
					$app->redirect($url);									
				}				
			}				
							
				
		}//end if checking article in full view
			
	}
	
	function do_alert(){	
		$lang = JFactory::getLanguage();
		$lang->load('com_accessmanager', JPATH_ROOT, null, false);	
		$message = addslashes(JText::_('COM_ACCESSMANAGER_NO_ACCESS_PAGE'));			
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo "<script>alert('".html_entity_decode($message)."'); window.history.go(-1); </script>";
		exit('<html><body><noscript>'.$message.'</noscript></body></html>');
	}	
	
	function check_component_access_frontend(){	
			
		$app = JFactory::getApplication();	
			
		if($this->am_config['am_enabled']
			&& $this->am_config['component_active'] 
			&& (!$this->is_super_user || ($this->is_super_user && !$this->am_config['component_superadmins']))				
			&& !($this->option=='com_accessmanager' && $this->view=='noaccess')
			){						
			if(!$this->access_script->check_access($this->option, 'component', $this->am_config['component_multigroup_access_requirement'], $this->am_config['component_reverse_access'])){								
				if($this->am_config['components_message_type']=='alert'){					
					$this->do_alert();	
				}elseif($this->am_config['components_message_type']=='redirect'){							
					$url = JURI::root().$this->am_config['no_component_access_url'];
					$app->redirect($url);
				}elseif($this->am_config['components_message_type']=='login'){							
					$url = $this->login_url;
					$app->redirect($url, JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));	
				}else{					
					$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component', false);						
					$app->redirect($url);						
				}
			}				
		}		
	}		
			
	function onAfterRoute(){	
		
		static $on_after_route;		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$ds = DIRECTORY_SEPARATOR;
		$option = JRequest::getVar('option', '');
		$view = JRequest::getVar('view', '');
		$task = JRequest::getVar('task', '');
		
		if($this->fua_enabled || !$this->am_config['am_enabled']){
			return true;
		}

		
		if(!$on_after_route){
			
			if(!$app->isAdmin() && 
				(!$this->is_super_user || ($this->is_super_user && !$this->am_config['article_superadmins']) || ($this->is_super_user && !$this->am_config['component_superadmins']) || ($this->is_super_user && !$this->am_config['contact_superadmins']) || ($this->is_super_user && !$this->am_config['weblink_superadmins'])) &&
				($this->am_config['article_active'] || $this->am_config['component_active'] || $this->am_config['contact_active'] || $this->am_config['weblink_active'])
			){			
				
				$declare_array = array();
				
				
				if($this->am_config['article_active']){	
					
					//model articles
					$file = 'components'.$ds.'com_content'.$ds.'models'.$ds.'articles.php';
					$code_replace = array();	
					//get cat-access out (when it gets integrated in the core)
					//http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=8103&tracker_item_id=27819		
					$code_old = '$query->where(\'c.access IN (\'.$groups.\')\');';																		
					$code_new = '';									
					$code_replace[] = array($code_old, $code_new);	
					
					//get cat-access out, as from joomla 3.1.1 the code changed
					$code_old = '->where(\'c.access IN (\' . $groups . \')\');';																		
					$code_new = '';									
					$code_replace[] = array($code_old, $code_new);
												
					//where by articles and categories
					$code_old = '$query->where(\'a.access IN (\'.$groups.\')\');';																			
					$code_new = '$query->where(\' '.$this->access_script->where_articles_categories('a', 'c', 'a.access IN (\'.$groups.\')', 'c.access IN (\'.$groups.\')').' \');';	
					$code_replace[] = array($code_old, $code_new);
					
					//where by articles and categories, as from joomla 3.1.1 the code changed					
					$code_old = '$query->where(\'a.access IN (\' . $groups . \')\')';						
					$code_replace[] = array($code_old, $code_new);						
					
					//no secondairy filtering
					$code_old = 'if ($access) {';																		
					$code_new = 'if(1==1){';									
					$code_replace[] = array($code_old, $code_new);					
					$declare_array[] = array($file, $code_replace);						
					
					
					//model featured 
					if($option=='com_content' && $view=='featured'){				
						$file = 'components'.$ds.'com_content'.$ds.'models'.$ds.'featured.php';
						$code_replace = array();		
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';						
						$code_new = '';					
						$code_replace[] = array($code_old, $code_new);	
						//extra for joomla 1.6
						$code_old = 'require_once dirname(__FILE__) . DS . \'articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);						
						//and they changed it again in joomla 2.5.4
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);	
						//and they changed it again in joomla 3.0.0
						$code_old = 'require_once __DIR__ . \'/articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);	
						$declare_array[] = array($file, $code_replace);
					}
					
					//model archive 
					if($option=='com_content' && $view=='archive'){
						$file = 'components'.$ds.'com_content'.$ds.'models'.$ds.'archive.php';
						$code_replace = array();		
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';						
						$code_new = '';					
						$code_replace[] = array($code_old, $code_new);	
						//extra for joomla 1.6
						$code_old = 'require_once dirname(__FILE__) . DS . \'articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);
						//and they changed it again in joomla 2.5.4
						$code_old = 'require_once dirname(__FILE__) . \'/articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);
						//and they changed it again in joomla 3.0.0
						$code_old = 'require_once __DIR__ . \'/articles.php\';';					
						$code_new = '';						
						$code_replace[] = array($code_old, $code_new);	
						$declare_array[] = array($file, $code_replace);	
					}
				}//end if article and category access active
								
				//search
				if($option=='com_search'){										
					$file = 'components'.$ds.'com_search'.$ds.'models'.$ds.'search.php';
					$code_replace = array();
					//filter components and menuitems						
					$code_old = '$this->_total	= count($rows);';				
					$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();				
					$rows = $accessmanagerAccessChecker->filter_search_results($rows);'.$code_old;						
					$code_replace[] = array($code_old, $code_new);			
					$declare_array[] = array($file, $code_replace);			
				}			
				
				if($option=='com_finder'){
					$file = 'components'.$ds.'com_finder'.$ds.'models'.$ds.'search.php';
					$code_replace = array();
					//rip access filter out of query
					$code_old = '$query->where($db->quoteName(\'l.access\') . \' IN (\' . $groups . \')\');';				
					$code_new = '';					
					$code_replace[] = array($code_old, $code_new);	
					//add filter
					$code_old = '// Switch to a non-associative array.';				
					$code_new = '$accessmanagerAccessChecker = new accessmanagerAccessChecker();				
								$results = $accessmanagerAccessChecker->filter_search_results_finder($results);';
					$code_replace[] = array($code_old, $code_new);
					//add var to count results
					$code_old = 'protected $requiredTerms = array();';				
					$code_new = $code_old.'public $number_of_results;';
					$code_replace[] = array($code_old, $code_new);
					//parse number of results 
					$code_old = '$this->store($store, $results);';				
					$code_new = '$this->number_of_results = count($results);'.$code_old;
					$code_replace[] = array($code_old, $code_new);
					//return number of results
					$code_old = '$store = $this->getStoreId(\'getTotal\');';				
					$code_new = 'return $this->number_of_results;'.$code_old;
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);
				}
				
				//contacts
				if($option=='com_contact' && ($view=='category' || $view=='featured')){	
					if($view=='category'){
						$file = 'components'.$ds.'com_contact'.$ds.'models'.$ds.'category.php';
					}else{
						$file = 'components'.$ds.'com_contact'.$ds.'models'.$ds.'featured.php';
					}					
					$code_replace = array();										
					$code_old = '$query->where(\'a.access IN (\'.$groups.\')\');';				
					$code_new = '$query->where(\' a.id '.$this->access_script->where_contacts().' \');';									
					$code_replace[] = array($code_old, $code_new);	
					//and different for j3
					$code_old = '->where(\'a.access IN (\' . $groups . \')\')';	
					$code_new = '->where(\' a.id '.$this->access_script->where_contacts().' \')';
					$code_replace[] = array($code_old, $code_new);			
					$declare_array[] = array($file, $code_replace);			
				}	
				
				//weblinks
				if($option=='com_weblinks' && $view=='category'){						
					$file = 'components'.$ds.'com_weblinks'.$ds.'models'.$ds.'category.php';										
					$code_replace = array();										
					$code_old = '$query->where(\'a.access IN (\'.$groups.\')\');';				
					$code_new = '$query->where(\' a.id '.$this->access_script->where_weblinks().' \');';									
					$code_replace[] = array($code_old, $code_new);
					//and different for j3
					$code_old = '->where(\'a.access IN (\' . $groups . \')\');';
					$code_new = '->where(\' a.id '.$this->access_script->where_weblinks().' \');';	
					$code_replace[] = array($code_old, $code_new);			
					$declare_array[] = array($file, $code_replace);			
				}
							
				
				$this->declare_methods($declare_array);
			}
			
			if($app->isAdmin()){
				//backend
				
				$declare_array = array();
				
				if(!$this->is_super_user){
					
					//pluginbackend
					if($this->am_config['pluginbackend_active'] && $option=='com_plugins' && ($view=='plugins' || $view=='')){					
						$file = 'administrator'.$ds.'components'.$ds.'com_plugins'.$ds.'models'.$ds.'plugins.php';
						$code_old = 'return $query;';				
						$code_new = '$query->where(\'a.extension_id '.$this->where_plugin_backend().'\');'.$code_old;
						$code_replace[] = array($code_old, $code_new);
						$declare_array[] = array($file, $code_replace);					
					}
				
				}					
				
				$this->declare_methods($declare_array);
				
			}	
			
			$on_after_route = 1;
		}	
	}	
	
	function where_plugin_backend(){		
		$database = JFactory::getDBO();		
		//not for super-admins and only when valid trial
		if($this->is_super_user || !$this->trial_valid){
			return ' NOT IN (0)';
		}		
		$rights = $this->helper->get_access_rights_backend('pluginbackend');
		$database->setQuery("SELECT extension_id FROM #__extensions WHERE type='plugin' ");
		$items = $database->loadColumn();
		return $this->get_where_backend('pluginbackend', $rights, $items);
	}	
	
	function get_where_backend($type, $rights, $items){
		$groups = $this->access_script->get_user_grouplevels('group');				
				
		$rights_array = array();
		foreach($rights as $right){
			$temp = explode('__', $right);			
			$rights_array[] = array($temp[0], $temp[1], $temp[2]);//item group access		
		}	
		
		$items_no_access = array();
		foreach($items as $item){
			$access_array = array();
			foreach($groups as $group_row){			
				$temp = '';
				foreach($rights_array as $right){
					if($right[0]==$item && $right[1]==$group_row){
						$temp = $right[2];
						break;
					}				
				}
				if($temp==''){
					//get inherited access
					$groups_backend = $this->get_all_grouplevels('backend');				
					$this->inherited_right = '';
					$this->get_inherited_right_backend($item, $group_row, $rights, $groups_backend, $type);
					$temp = $this->inherited_right;
				}
				$access_array[] = $temp;
			}							
			if($this->am_config[$type.'_multigroup_access_requirement']=='every_group'){
				if(in_array('0', $access_array)){
					$access = false;
				}else{
					$access = true;
				}
			}else{
				if(in_array('1', $access_array)){
					$access = true;
				}else{
					$access = false;
				}				
			}
			if(!$access){				
				$items_no_access[] = $item;
			}
		}				
		$where = ' NOT IN (0';						
		for($n = 0; $n < count($items_no_access); $n++){
			$where .= ','.$items_no_access[$n];
		}
		$where .= ') ';				
		return $where;
	}
   
	function onAfterInitialise(){
	
		$ds = DIRECTORY_SEPARATOR;	
		$version = new JVersion;	
		
		if($this->fua_enabled){
			return true;
		}
  
   		static $onAfterInitialise;			
		
		if(!$onAfterInitialise){
		
			$app = JFactory::getApplication();
			$database = JFactory::getDBO();	
			
			if(!$this->am_config['am_enabled']){
				return true;
			}				
			
			$declare_array = array();
				
			if(!in_array('JModuleHelper', get_declared_classes())){				
				
				//modules	
				//check if we need to override the advanced module manager or MetaMod
				$am_order = 0;
				if(file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'advancedmodules'.$ds.'advancedmodules.php') || file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'metamod'.$ds.'metamod.php')){
					
					//check which order the AM system plugin has
					$database->setQuery("SELECT ordering "
					." FROM #__extensions "
					." WHERE element='accessmanager' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$am_order = $row->ordering;
					}
				}	
				
				//advanced module manager
				$advanced_module_manager_published = 0;
				$advanced_module_manager_order = 0;	
				if(file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'advancedmodules'.$ds.'advancedmodules.php')){
					//check if enabled and which order
					$database->setQuery("SELECT enabled, ordering "
					." FROM #__extensions "
					." WHERE element='advancedmodules' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$advanced_module_manager_published = $row->enabled;
						$advanced_module_manager_order = $row->ordering;
					}				
				}				
				
				//MetaMod
				//seems not to be for 1.7 leave in for a while to make sure it does not surprise me when making a comeback
				$metamod_published = 0;
				$metamod_order = 0;	
				if(file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'metamod'.$ds.'metamod.php')){
					//check if enabled and which order
					$database->setQuery("SELECT enabled, ordering "
					." FROM #__extensions "
					." WHERE element='metamod' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$metamod_published = $row->enabled;
						$metamod_order = $row->ordering;
					}				
				}
				
				//jat3				
				$jat3_enabled = 0;				
				if(file_exists(JPATH_PLUGINS.$ds.'system'.$ds.'jat3'.$ds.'jat3.php')){				
					//check if enabled and which order
					$database->setQuery("SELECT enabled "
					." FROM #__extensions "
					." WHERE element='jat3' AND folder='system' "
					." LIMIT 1 "
					);
					$rows = $database->loadObjectList();					
					foreach($rows as $row){					
						$jat3_enabled = $row->enabled;						
					}				
				}				
				
				$got_module_helper = 0;
				if($jat3_enabled){	
					//if metamod is enabled AND FUA is loaded first in order
					//load the metamod helper and alter it
					$file = 'plugins'.$ds.'system'.$ds.'jat3'.$ds.'jat3'.$ds.'core'.$ds.'joomla'.$ds.'modulehelper.php';
					$got_module_helper = 1;
				}
				if(!$got_module_helper && $advanced_module_manager_published && ($am_order < $advanced_module_manager_order)){	
					//if advanced_module_manager is enabled AND AM is loaded first in order
					//load the advanced module managers module helper and alter it
					
					$file = 'plugins'.$ds.'system'.$ds.'advancedmodules'.$ds.'modulehelper.php';
					$got_module_helper = 1;					
				}
				if(!$got_module_helper && $metamod_published && ($am_order < $metamod_order)){	
					//if metamod is enabled AND AM is loaded first in order
					//load the metamod helper and alter it
					$file = 'plugins'.$ds.'system'.$ds.'metamod'.$ds.'modulehelper.php';
					$got_module_helper = 1;
				}
				if(!$got_module_helper){
					if(file_exists(JPATH_ROOT.$ds.'libraries'.$ds.'joomla'.$ds.'application'.$ds.'module'.$ds.'helper.php')){
						//joomla 2.5
						$file = 'libraries'.$ds.'joomla'.$ds.'application'.$ds.'module'.$ds.'helper.php';
					}else{
						//joomla 3.0
						if(file_exists(JPATH_ROOT.$ds.'libraries'.$ds.'legacy'.$ds.'module'.$ds.'helper.php')){
							$file = 'libraries'.$ds.'legacy'.$ds.'module'.$ds.'helper.php';	
						}else{
							//as of joomla 3.2
							$file = 'libraries'.$ds.'cms'.$ds.'module'.$ds.'helper.php';	
						}					
					}
				}
				
				$code_replace = array();
				
				$code_old = 'require $path;';
				$end_code = '						
					}else{				
						require $path;
					}';	
				if(!$app->isAdmin()){					
					//frontend				
									
					$code_new = '
					if(strpos($path, \'mod_articles_categories.php\')){
						$list = modArticlesCategoriesHelper::getList($params);
						if (!empty($list)) {
							$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
							$startLevel = reset($list)->getParent()->level;
							require JModuleHelper::getLayoutPath(\'mod_articles_categories\', $params->get(\'layout\', \'default\'));
						}					
					}elseif(strpos($path, \'mod_articles_category.php\')){
						// Prep for Normal or Dynamic Modes
						$mode = $params->get(\'mode\', \'normal\');
						$idbase = null;
						switch($mode)
						{
							case \'dynamic\':
								$option = JRequest::getCmd(\'option\');
								$view = JRequest::getCmd(\'view\');
								if ($option === \'com_content\') {
									switch($view)
									{
										case \'category\':
											$idbase = JRequest::getInt(\'id\');
											break;
										case \'categories\':
											$idbase = JRequest::getInt(\'id\');
											break;
										case \'article\':
											if ($params->get(\'show_on_article_page\', 1)) {
												$idbase = JRequest::getInt(\'catid\');
											}
											break;
									}
								}
								break;
							case \'normal\':
							default:
								$idbase = $params->get(\'catid\');
								break;
						}
						$cacheid = md5(serialize(array ($idbase,$module->module)));
						$cacheparams = new stdClass;
						$cacheparams->cachemode = \'id\';
						$cacheparams->class = \'modArticlesCategoryHelper\';
						$cacheparams->method = \'getList\';
						$cacheparams->methodparams = $params;
						$cacheparams->modeparams = $cacheid;					
						$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
						if (!empty($list)) {
							$grouped = false;
							$article_grouping = $params->get(\'article_grouping\', \'none\');
							$article_grouping_direction = $params->get(\'article_grouping_direction\', \'ksort\');
							$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
							$item_heading = $params->get(\'item_heading\');					
							if ($article_grouping !== \'none\') {
								$grouped = true;
								switch($article_grouping){
									case \'year\':
									case \'month_year\':
										$list = modArticlesCategoryHelper::groupByDate($list, $article_grouping, $article_grouping_direction, $params->get(\'month_year_format\', \'F Y\'));
										break;
									case \'author\':
									case \'category_title\':
										$list = modArticlesCategoryHelper::groupBy($list, $article_grouping, $article_grouping_direction);
										break;
									default:
										break;
								}
							}
							require JModuleHelper::getLayoutPath(\'mod_articles_category\', $params->get(\'layout\', \'default\'));
						}				
					';	
					if($version->RELEASE < '3.2'){
						$code_new .= '}elseif(strpos($path, \'mod_articles_latest.php\')){
							$list = modArticlesLatestHelper::getList($params);
							$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
							require JModuleHelper::getLayoutPath(\'mod_articles_latest\', $params->get(\'layout\', \'default\'));
							';
					}						
					$code_new .= '		
					}elseif(strpos($path, \'mod_related_items.php\')){
						$cacheparams = new stdClass;
						$cacheparams->cachemode = \'safeuri\';
						$cacheparams->class = \'modRelatedItemsHelper\';
						$cacheparams->method = \'getList\';
						$cacheparams->methodparams = $params;
						$cacheparams->modeparams = array(\'id\'=>\'int\',\'Itemid\'=>\'int\');					
						$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);					
						if (!count($list)) {
							return;
						}					
						$moduleclass_sfx = htmlspecialchars($params->get(\'moduleclass_sfx\'));
						$showDate = $params->get(\'showDate\', 0);					
						require JModuleHelper::getLayoutPath(\'mod_related_items\', $params->get(\'layout\', \'default\'));								
					';					
															
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					//same but then for joomla 2.5 ('require' changed to 'include')
					$code_old = 'include $path;';					 
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					$declare_array[] = array($file, $code_replace);				
				
				
				
					//module articles archive
					/*
					this module does not filter for access at all, not on article and categories
					and does not have a query identifier, so AM does no filtering till Joomla does					
					*/				
				
					//module articles categories				
					$file = 'modules'.$ds.'mod_articles_categories'.$ds.'helper.php';				
					$code_replace = array();	
					$code_old = '$items = $category->getChildren();';	
					$code_new = $code_old;							
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);			
						
					//module articles category				
					$file = 'modules'.$ds.'mod_articles_category'.$ds.'helper.php';				
					$code_replace = array();	
					//articles already filtered in articles model
					//module does not filter for categories, so leave that till Joomla fixes this
					$code_old = 'if ($access || in_array($item->access, $authorised)) {';	
					$code_new = 'if (1==1) {';				
					$code_replace[] = array($code_old, $code_new);
					$declare_array[] = array($file, $code_replace);						
					
					///module articles latest
					//only for 2.5 and 3.0 and 3.1
					$version = new JVersion;
					if($version->RELEASE < '3.2'){				
						$file = 'modules'.$ds.'mod_articles_latest'.$ds.'helper.php';				
						$code_replace = array();
						//take out second filter		
						$code_old = 'if ($access || in_array($item->access, $authorised)) {';					
						$code_new = 'if (1==1) {';						
						$code_replace[] = array($code_old, $code_new); 					
						$declare_array[] = array($file, $code_replace);
					}
				
					/*
					//module articles news	
					//article and category filtering is already done in the article model				
					*/
					
					/*	
					//module articles popular
					//article and category filtering is already done in the article model
					*/	
					
					//module related items		
					$file = 'modules'.$ds.'mod_related_items'.$ds.'helper.php';				
					$code_replace = array();		
					$code_old = '$query->where(\'a.access IN (\' . $groups . \')\');';					
					$code_new = '$query->where(\' '.$this->access_script->where_articles_categories('a', 'cc', 'a.access IN (\'.$groups.\')', 'cc.access IN (\'.$groups.\')').' \');';											
					$code_replace[] = array($code_old, $code_new); 					
					$declare_array[] = array($file, $code_replace);						
						
				}else{
					//backend					
					
					if(file_exists(JPATH_ROOT.$ds.'administrator'.$ds.'modules'.$ds.'mod_adminmenumanager'.$ds.'helper.php') && $this->am_config['adminmenumanager_active']){
						//need to override WITH adminmenumodule
						$code_new = '
						$ds = DIRECTORY_SEPARATOR;
						if(strpos($path, \'mod_adminmenumanager.php\') && file_exists(JPATH_ROOT.$ds.\'administrator\'.$ds.\'components\'.$ds.\'com_adminmenumanager\'.$ds.\'controller.php\')){													
							//require_once(JPATH_ROOT.$ds.\'administrator\'.$ds.\'modules\'.$ds.\'mod_adminmenumanager\'.$ds.\'helper.php\');							
							$adminmenumanagermenuhelper = new ModAdminMenuManagerHelper();
							$amm_menuitems = $adminmenumanagermenuhelper->get_menu_items($params);
							$class_sfx = htmlspecialchars($params->get(\'class_sfx\'));	
							$adminmenumanagerdisable = $params->get(\'adminmenumanagerdisable\');
							require JModuleHelper::getLayoutPath(\'mod_adminmenumanager\', \'default\');
							
						';
					}else{
						//overwrite WITHOUT adminmenumodule override
						$code_new = '
						if(\'pigs\'==\'fly\'){							
						';
					
					}
					
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					//same but then for joomla 2.5 ('require' changed to 'include')
					$code_old = 'include $path;';					 
					$code_replace[] = array($code_old, $code_new.$end_code); 			
					$declare_array[] = array($file, $code_replace);	
					
					
					//module adminmenumanager	
					$file = 'administrator'.$ds.'modules'.$ds.'mod_adminmenumanager'.$ds.'helper.php';		
					if(file_exists(JPATH_ROOT.$ds.$file) && $this->am_config['adminmenumanager_active']){								
						$code_replace = array();		
						$code_old = 'if(!($amm_config[\'super_user_sees_all\'] && in_array(8, $groups_array))){
			$query->where($access_column.\' IN (\'.$groups_levels.\')\');
		}';					
						$code_new = '$query->where(\' id '.$this->access_script->where_adminmenumanager().' \');';																
						$code_replace[] = array($code_old, $code_new); 						
						//amm free version
						$code_replace = array();		
						$code_old = '$query->where(\'published=1\');';					
						$code_new = $code_old.'$query->where(\' id '.$this->access_script->where_adminmenumanager().' \');';																
						$code_replace[] = array($code_old, $code_new); 					
						$declare_array[] = array($file, $code_replace);	
					}
					
				}				
				  
			}
			$this->declare_methods($declare_array);
			$onAfterInitialise = 1;
		}		
	}
   
	function declare_methods($declare_array){
		$ds = DIRECTORY_SEPARATOR;				
		for($n = 0; $n < count($declare_array); $n++){					
			$file = JPATH_ROOT.$ds.$declare_array[$n][0];
			if(file_exists($file)){					
				$handle = fopen($file, 'r');
				$code = fread($handle, filesize($file));
				$code = str_replace('<?php', '', $code);
				$code = str_replace('?>', '', $code);			
				$code_replace = $declare_array[$n][1];
				for($p = 0; $p < count($code_replace); $p++){					
					$code = str_replace($code_replace[$p][0], $code_replace[$p][1], $code);											
				}								
				eval($code);				
			}			
		}
	}
	
	//save article rights	
	function onContentAfterSave($context, $article, $isNew){	
		$ds = DIRECTORY_SEPARATOR;
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'helpers'.$ds.'accessmanager.php');
		$this->helper = new accessmanagerHelper();		
		if($this->am_config['article_active'] && ($context=='com_content.article' || $context=='com_content.form')){	
			//frontend and backend	
			$temp = $article->id;
			$temp2 = $this->am_config['based_on'];				
			$this->save_rights($article->id, 'article', $this->am_config['based_on']);	
			$this->helper->clear_article_cache();			
		}		
	}
	
	function save_rights($item, $type, $based_on, $groupslevels=0){
		
		$db = JFactory::getDBO();
		
		//get rights from select if not parsed as var
		if(!$groupslevels){		
			$groupslevels = JRequest::getVar('am_levelsgroups_access', null, 'post', 'array');
		}
			
		if($groupslevels){
			//delete all current rights for this item			
			$query = $db->getQuery(true);
			$query->delete();
			$query->from('#__accessmanager_rights');
			$query->where('item='.$db->q($item));
			$query->where('type='.$db->q($type));
			$query->where($db->quoteName($based_on).'<>'.$db->q('0'));
			$db->setQuery((string)$query);
			$db->query();			
			
			//add rights 		
			foreach($groupslevels as $grouplevel){
				$query = $db->getQuery(true);
				$query->insert('#__accessmanager_rights');
				$query->set('item='.$db->q($item));
				$query->set($db->quoteName($based_on).'='.$db->q($grouplevel));	
				$query->set('type='.$db->q($type));			
				$db->setQuery((string)$query);
				$db->query();				
			}
		}
	}
	
	function check_contact_access(){		
		
		if($this->option=='com_contact' && $this->view=='contact'){	
			
			$app = JFactory::getApplication();
			$contact_id_temp = JRequest::getVar('id', '');			
			if(strpos($contact_id_temp, ':')){
				$pos_contact_id = strpos($contact_id_temp, ':');
				$contact_id = intval(substr($contact_id_temp, 0, $pos_contact_id));	
			}else{
				$contact_id = intval($contact_id_temp);	
			}		
		
			if($contact_id && $this->am_config['contact_active'] && (!$this->is_super_user || ($this->is_super_user && !$this->am_config['contact_superadmins']))){			
				if(!$this->access_script->check_access($contact_id, 'contact', $this->am_config['contact_multigroup_access_requirement'], $this->am_config['contact_reverse_access'])){								
					if($this->am_config['contact_message_type']=='alert'){					
						$this->do_alert();							
					}elseif($this->am_config['contact_message_type']=='only_text'){							
						$url = JRoute::_('index.php?option=com_accessmanager&view=noaccess&tmpl=component', false);										
						$app->redirect($url);		
					}elseif($this->am_config['contact_message_type']=='redirect'){						
						$url = JURI::root().$this->am_config['no_contact_access_url'];								
						$app->redirect($url);		
					}elseif($this->am_config['contact_message_type']=='login'){																		
						$url = $this->login_url;								
						$app->redirect($url, JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'));	
					}
				}		
			}
		}		
	}
	
	
	
	
	
	
}

?>