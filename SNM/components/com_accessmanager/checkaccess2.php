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


class accessmanagerAccessChecker{	

	protected $version_type = 'free';
	protected $am_config;
	protected $user_id;	
	protected $trial_valid = 1;	
	protected $is_super_user = 0;
	protected $fua_enabled = 0;
	protected $grouplevels = array();
	protected $backend_usergroups = array();
	
	function __construct(){
	
		$database = JFactory::getDBO();
		$ds = DIRECTORY_SEPARATOR;
		
		//if FUA is installed, make sure not to continue or site will crash
		if(file_exists(JPATH_ROOT.$ds.'plugins'.$ds.'system'.$ds.'frontenduseraccess'.$ds.'frontenduseraccess.php')){
			$database->setQuery("SELECT enabled "
			." FROM #__extensions "
			." WHERE element='frontenduseraccess' AND type='plugin' AND folder='system' "		
			);
			$rows = $database->loadObjectList();
			$fua_enabled = 0;
			foreach($rows as $row){
				$fua_enabled = $row->enabled;
			}
			$this->fua_enabled = $fua_enabled;			
		}
		
		//get config		
		$this->am_config = $this->get_config();
		
		//get user id			
		$user = JFactory::getUser();		
		$this->user_id = $user->get('id');
		
		//check if super user					
		$user_id = $this->user_id;		
		if($user_id){
			//only check if logged in
			$database->setQuery("SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id' AND group_id='8' LIMIT 1");
			$rows = $database->loadObjectList();		
			foreach($rows as $row){
				$this->is_super_user = true;
			}
		}	
		
		$this->grouplevels = $this->get_user_grouplevels();		
	}	
		
	function get_config(){	
			
		$database = JFactory::getDBO();			
		
		$database->setQuery("SELECT config "
		."FROM #__accessmanager_config "
		."WHERE id='am' "
		."LIMIT 1"
		);		
		$raw = $database->loadResult();	
		
		$registry = new JRegistry;
		$registry->loadString($raw);
		$config = $registry->toArray();
		
		//reformat redirect urls		
		$config['no_item_access_full_url'] = str_replace('[equal]','=',$config['no_item_access_full_url']);				
		$config['no_component_access_url'] = str_replace('[equal]','=',$config['no_component_access_url']);	
		$config['no_menu_access_url'] = str_replace('[equal]','=',$config['no_menu_access_url']);					
		$config['no_contact_access_url'] = str_replace('[equal]','=',$config['no_contact_access_url']);	
				
		return $config;			
	}
	
	function filter_menu_items($rows){
		
		//dont restrict anything
		return $rows;		
	}	
	
	function filter_categories($rows){
	
		//dont restrict anything
		return $rows;		
	}	
	
	function get_article_rights(){		
		static $articlerights;		
		if(!$articlerights){
			$articlerights = $this->get_rights('article');					
		}		
		return $articlerights;
	}
	
	function get_rights($type){		
		$database = JFactory::getDBO();		
		$based_on = $this->am_config['based_on'];			
		$grouplevels = $this->grouplevels;			
		$groups_string = implode(',', $grouplevels);
		$rows = array();
		if($groups_string!=''){
			$database->setQuery("SELECT item, `".$based_on."` "
			." FROM #__accessmanager_rights "
			." WHERE `$based_on` in ($groups_string) "		
			." AND type='$type' "
			);		
			$rows = $database->loadObjectList();
		}				
		$rights = array();
		$items = array();				
		foreach($rows as $row){
			if($based_on=='group'){
				$temp = $row->group;
			}else{					
				$temp = $row->level;
			}
			$rights[] = $row->item.'__'.$temp;
			$items[] = $row->item;
		}			
		$items = array_unique($items);		
		$rights_array = array();
		foreach($items as $item){
			$all_selected = 1;			
			foreach($grouplevels as $grouplevel){			
				$right = $item.'__'.$grouplevel;				
				if(!in_array($right, $rights)){	
					$all_selected = 0;	
					break;							
				}			
			}	
			if($this->am_config[$type.'_multigroup_access_requirement']=='every_group'){
				//every group
				if($this->am_config[$type.'_reverse_access']){
					//if in array, no access
					/*
					if($all_selected){
						$to_array = 1;
					}else{
						$to_array = 1;
					}
					*/
					$to_array = 1;
				}else{
					//if in array, has access
					if($all_selected){
						$to_array = 1;
					}else{
						$to_array = 0;
					}
				}
			}else{
				//just one group
				if($this->am_config[$type.'_reverse_access']){
					//if in array, no access
					if($all_selected){
						$to_array = 1;
					}else{
						$to_array = 0;
					}
				}else{
					//if in array, has access
					/*
					if($all_selected){
						$to_array = 1;
					}else{
						$to_array = 1;
					}
					*/
					$to_array = 1;
				}
			}
			if($to_array){
				$rights_array[] = $item;
			}			
		}
		return $rights_array;
	}
	
	function get_usergroups_from_database(){
		$database = JFactory::getDBO();
		
		static $am_usergroups;		
		
		if(!$am_usergroups){
			
			$user_id = $this->user_id;
			
			$am_usergroups = array();
			if(!$user_id){
				//user is not logged in
				$am_usergroups = array(10);
			}else{				
				$database->setQuery("SELECT group_id FROM #__accesmanager_userindex WHERE user_id='$user_id' LIMIT 1 ");		
				$rows_group = $database->loadObjectList();			
				$am_usergroups = 0;
				foreach($rows_group as $row){
					$temp = $row->group_id;
					$am_usergroups = $this->csv_to_array($temp);
				}
				
				if(!$am_usergroups){
					//user is logged in, but is not assigned to any usergroup, so make it 9
					$am_usergroups = array(9);
				}
			}
		}
		return $am_usergroups;		
	}
		
	function csv_to_array($csv){		
		$array = array();
		$temp = explode(',', $csv);
		for($n = 0; $n < count($temp); $n++){
			$value = str_replace('"','',$temp[$n]);
			$array[] = $value;
		}
		return $array;
	}
	
	function check_article_access($article_id, $category_id=0, $joomla_access_level=0){
	
		$database = JFactory::getDBO();
		$app = JFactory::getApplication();
		$return_item_access = 1;
		$user = JFactory::getUser();
		$groups = $user->getAuthorisedViewLevels();	
		
		if($this->fua_enabled){
			return true;
		}		
		
		//check article access
		$am_item_access = 1;	
		if($this->am_config['article_active']){				
			$am_item_access = $this->check_access($article_id, 'article');				
		}else{
			//get Joomla access
			if(!$joomla_access_level){
				$database->setQuery("SELECT access "
				."FROM #__content "
				."WHERE id='$article_id' "
				."LIMIT 1 "
				);
				$am_access_rows = $database->loadObjectList();				
				foreach($am_access_rows as $am_access_row){	
					$joomla_access_level = $am_access_row->access;	
				}
			}								
			if(!in_array($joomla_access_level, $groups)){
				$am_item_access = 0;
			}
		}
		
		//check category access
		$am_category_access = 1;
		if(!$category_id){						
			//get category id of item
			$database->setQuery("SELECT catid "
			."FROM #__content "
			."WHERE id='$article_id' "
			."LIMIT 1 "
			);
			$am_category_rows = $database->loadObjectList();
			$category_id = '';
			foreach($am_category_rows as $am_category_row){	
				$category_id = $am_category_row->catid;	
			}
		}
		
		//get category access Joomla
		$database->setQuery("SELECT access "
		."FROM #__categories "
		."WHERE id='$category_id' "
		."LIMIT 1 "
		);
		$am_access_rows = $database->loadObjectList();				
		foreach($am_access_rows as $am_access_row){	
			$cat_access_level = $am_access_row->access;	
		}
		if(!in_array($cat_access_level, $groups)){
			$am_category_access = 0;
		}				
		
		if($this->am_config['content_access_together']=='one_group'){
			//needs access from just one activated restriction type							
			if($am_item_access || $am_category_access){					
				$return_item_access = true;
			}else{
				$return_item_access = false;
			}
		}else{				
			//needs access for both restriction types						
			if($am_item_access && $am_category_access){					
				$return_item_access = true;
			}else{
				$return_item_access = false;
			}
		}
				
		return $return_item_access;
	}
	
	function check_access($item, $type){	
	
		$database = JFactory::getDBO();	
		$reversed = $this->am_config[$type.'_reverse_access'];
		$multigroups = $this->am_config[$type.'_multigroup_access_requirement'];	
		$access = 1;
		$check_super_user = 1;
		if($this->is_super_user){
			if($type=='menuitem' || $type=='article' || $type=='category' || $type=='component' || $type=='part'){
				if($this->am_config[$type.'_superadmins']){
					$check_super_user = 0;
				}
			}
		}
		//echo 'check_super_user'.$check_super_user;
		if($this->trial_valid && $this->am_config['am_enabled'] && $check_super_user){
		
			$based_on = $this->am_config['based_on'];			
			$groups_string = implode(',', $this->grouplevels);		
			$database->setQuery("SELECT `$based_on` "
			." FROM #__accessmanager_rights "
			." WHERE `$based_on` in ($groups_string) "		
			." AND item='$item' "
			." AND type='$type' "
			);
			$rights_rows = $database->loadObjectList();		
			$rights = array();
			foreach($rights_rows as $right){
				if($based_on=='level'){
					$temp_grouplevel = $right->level;
				}else{
					$temp_grouplevel = $right->group;
				}				
				$rights[] = $item.'__'.$temp_grouplevel;
			}		
			$access_array = array();
			foreach($this->grouplevels as $grouplevel){			
				$right = $item.'__'.$grouplevel;			
				$access_temp = 'yes';
				if($reversed){
					if(in_array($right, $rights)){	
						$access_temp = 'no';								
					}
				}else{
					if(!in_array($right, $rights)){	
						$access_temp = 'no';									
					}
				}			
				$access_array[] = $access_temp;
			}
			$access = true;				
			if($multigroups=='every_group'){
				if(in_array('no', $access_array)){
					$access = false;
				}else{
					$access = true;
				}
			}else{
				if(in_array('yes', $access_array)){
					$access = true;
				}else{
					$access = false;
				}				
			}
		}		
		return $access;
	}	
	
	function get_user_grouplevels($include_superuser = 0, $no_admin_exception = 0){
	
		$database = JFactory::getDBO();	
		$app = JFactory::getApplication();
		static $done;
		static $groups;
		static $levels;		
		
		if(!$done){	
			//groups
			$groups = array();			
			$backend_filter = '';
			if($app->isAdmin() && !$no_admin_exception){
				$this->get_backend_usergroups();
				$backend_usergroup_array = $this->backend_usergroups;
				$backend_usergroups = implode(',', $backend_usergroup_array);
				if($backend_usergroups!=''){
					$backend_filter = "AND m.group_id in ($backend_usergroups) ";
				}
			}						
			$database->setQuery("SELECT m.group_id "
			."FROM #__user_usergroup_map AS m "
			."WHERE m.user_id='$this->user_id' "
			.$backend_filter
			);	
			$groups = $database->loadColumn();		
			if(!$this->user_id){
				//user not logged in, return group 'public'
				$groups = array(1);
			}
			
			//levels
			$levels = JAccess::getAuthorisedViewLevels($this->user_id);
			$levels = array_unique($levels);				
		
			$done = 1;			
		}

		if($this->am_config['based_on']=='group' || ($app->isAdmin() && !$no_admin_exception)){
			if($this->is_super_user && $include_superuser){
				$groups[] = '8';
			}
			return $groups;
		}else{
			return $levels;
		}
		
	}
	
	function get_backend_usergroups(){
	
		$database = JFactory::getDBO();	
		
		//get main asset		
		$database->setQuery("SELECT rules FROM #__assets WHERE id=1 ");	
		$asset = $database->loadResult();
		
		//make into array
		$registry = new JRegistry;
		$registry->loadString($asset);
		$asset_array = $registry->toArray();
		
		//get configured backend groups	
		$temp = $asset_array['core.login.admin'];		
		for($n = 0; $n < count($temp); $n++){
			$row = each($temp);
			if($row['value']=='1'){							
				$this->set_backend_usergroup($row['key']);
			}
		}
	}
	
	function set_backend_usergroup($group){
	
		$database = JFactory::getDBO();	
			
		$this->backend_usergroups[] = $group;	

		//get child groups
		$database->setQuery("SELECT id "
		." FROM #__usergroups "
		." WHERE parent_id='$group' "		
		);
		$rows = $database->loadObjectList();
		foreach($rows as $row){	
			//recurse to get all children
			$this->set_backend_usergroup($row->id);
		}
	}	
	
	function filter_search_results($rows){	
		
		if(!$this->trial_valid || !$this->am_config['am_enabled']){
			//dont restrict anything
			return $rows;
		}else{	
			$temp_rows = array();			
			foreach($rows as $key => $row){	
				if(isset($row->href)){								
					$href = $row->href;
					$option = '';
					$menu_id = 0;
					$id = 0;
					$query = str_replace('index.php?', '', $href);
					$href_vars = explode('&', $query);								
					foreach($href_vars as $href_var){
						$temp = explode('=', $href_var);
						$key = $temp[0];
						$value = '';
						if(isset($temp[1])){
							$value = $temp[1];	
						}					
						if($key=='option'){
							$option = $value;							
						}
						if($key=='Itemid'){
							$menu_id = intval($value);							
						}	
						if($key=='id'){
							$temp_id = $value;
							if(strpos($temp_id, ':')){
								$pos_id = strpos($temp_id, ':');
								$id = intval(substr($temp_id, 0, $pos_id));	
							}else{
								$id = intval($temp_id);
							}						
						}				
					}	
					if($option==''){
						$option = 'com_content';
					}				
					$access = 1;
					
					if($this->am_config['component_active']){										
						//component	
						$access = $this->check_access($option, 'component');
					}	
					
					if($this->am_config['contact_active']){										
						//component	
						$access = $this->check_access($id, 'contact');
					}	
					
					if($this->am_config['weblink_active']){										
						//component	
						$access = $this->check_access($id, 'weblink');
					}										
					
					if($access){	
						$temp_rows[] = $row;
					}
				}
			}	
			$rows = $temp_rows;			
			return $rows;			
		}
	}
	
	function filter_search_results_finder($results){	
		
		if(!$this->trial_valid || !$this->am_config['am_enabled']){
			//dont restrict anything
			return $results;
		}else{	
			$temp_rows = array();			
			foreach($results as $key => $row){					
				if(isset($row->route)){									
					$href = $row->route;
					$option = 'com_content';//set as default as this seems to be missing in some articles in the finder table
					$menu_id = 0;
					$query = str_replace('index.php?', '', $href);
					$href_vars = explode('&', $query);
					$id = 0;
					$category_id = 0;				
					foreach($href_vars as $href_var){
						$temp = explode('=', $href_var);
						$key = $temp[0];
						$value = $temp[1];
						if($key=='id'){
							if(strpos($value, ':')){
								$temp2 = explode(':', $value);
								$value = $temp2[0];
							}
							$id = intval($value);						
						}
						if($key=='catid'){
							if(strpos($value, ':')){
								$temp2 = explode(':', $value);
								$value = $temp2[0];
							}
							$category_id = intval($value);							
						}
						if($key=='option'){
							$option = $value;							
						}
						if($key=='Itemid'){
							$menu_id = intval($value);							
						}					
					}	
					$access = 1;					
					if(strpos($href, 'option=com_content&view=article&id=') && $this->am_config['article_active']){
						//article view														
						$access = $this->check_article_access($id, $category_id);							
					}					
										
					if($access && $this->am_config['component_active']){										
						//component									
						$access = $this->check_access($option, 'component');
					}						
									
					if($access){	
						$temp_rows[] = $row;
					}
				}
			}	
			$results = $temp_rows;			
			return $results;			
		}
	}	
	
	function get_where($type, $rights){
		$where = '';
		if($this->am_config[$type.'_reverse_access']){
			$where .= ' NOT ';
		}
		$where .= ' IN ';					
		$where .= '(0';						
		for($n = 0; $n < count($rights); $n++){
			$where .= ','.$rights[$n];
		}
		$where .= ') ';
		
		if($where=='(0) '){
			$where = '';
		}
		return $where;
	}
	
	function where_articles(){	
		
		static $where_articles_done;
		static $where_articles;		
		
		//not for super-admins and only when valid trial
		if(($this->is_super_user && $this->am_config['article_superadmins']) || !$this->trial_valid){
			return '';
		}
		
		if(!$where_articles_done){
			$rights = $this->get_article_rights();
			$where_articles = $this->get_where('article', $rights);
			$where_articles_done = 1;
		}			
		return $where_articles;
	}
	
	function where_articles_categories($article_query_identifier, $category_query_identifier, $original_code_articles, $original_code_categories){			
		
		//not for super-admins and only when valid trial
		if(($this->is_super_user && $this->am_config['article_superadmins'] && $this->am_config['category_superadmins']) || !$this->trial_valid){
				
			return $article_query_identifier.'.id<>0';
		}		
		
		if($this->am_config['article_active']){	
			$where_articles = $article_query_identifier.'.id '.$this->where_articles();
		}else{
			$where_articles = $original_code_articles;
		}	
			
		$where_categories = $original_code_categories;
		
		$and_or = 'AND';
		if($this->am_config['content_access_together']=='one_group'){	
			$and_or = 'OR';
		}
		
		$where_articles_categories = '( '.$where_articles.' '.$and_or.' '.$where_categories.' )';		
							
		return $where_articles_categories;
	}
	
	function where_categories(){			
		
		return '';		
	}
	
	function where_modules(){
	
		return '';		
	}
	
	function get_is_super_user(){
		return $this->is_super_user;
	}
	
	function get_fua_enabled(){
		return $this->fua_enabled;
	}
	
	
	function where_menuitems(){
		
		return '';		
	}
	
	function where_adminmenumanager(){
		
		return '';		
	}
	
	function where_contacts(){
		
		static $where_contacts_done;
		static $where_contacts;		
		
		if(!$where_contacts_done){
			$rights = $this->get_contact_rights();
			$where_contacts = $this->get_where('contact', $rights);
			$where_where_contacts_done = 1;
		}			
		return $where_contacts;
	}
	
	function get_contact_rights(){
			
		static $contactrights;		
		if(!$contactrights){
			$contactrights = $this->get_rights('contact');					
		}		
		return $contactrights;
	}
	
	function where_weblinks(){
		
		static $where_weblinks_done;
		static $where_weblinks;		
		
		if(!$where_weblinks_done){
			$rights = $this->get_weblink_rights();
			$where_weblinks = $this->get_where('weblink', $rights);
			$where_where_weblinks_done = 1;
		}			
		return $where_weblinks;
	}
	
	function get_weblink_rights(){
			
		static $weblinkrights;		
		if(!$weblinkrights){
			$weblinkrights = $this->get_rights('weblink');					
		}		
		return $weblinkrights;
	}

	

}
?>