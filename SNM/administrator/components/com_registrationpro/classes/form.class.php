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

// Form fields class
class regpro_forms
{
	var $form_id;
	var $_data;
	var $_db;
	
	function __construct($frm_id)
	{
		$this->form_id 	= $frm_id;
		$this->_data 	= null;
				
		$this->_db = JFactory::getDBO();
	}
		
	function getFields()
	{
		if ($this->_loadData())
		{

		}
		else  $this->_initData();

		return $this->_data;
	}
	
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = "SELECT * FROM #__registrationpro_fields WHERE published=1 AND form_id = ".$this->form_id." AND batch_display=1 ORDER BY groupid, ordering";				

			$this->_db->setQuery($query);

			$this->_data = $this->_db->loadObjectList();

			return (boolean) $this->_data;
		}
		return true;
	}
	
	function getConditionalField_lists()
	{
		$query = "SELECT DISTINCT(conditional_field_name) FROM #__registrationpro_fields WHERE published=1 AND form_id = ".$this->form_id." AND conditional_field > 0";				
		$this->_db->setQuery($query);		
		$rows = $this->_db->loadAssocList();
		//echo "<pre>"; print_r($rows); exit;
				
		$clone_rows = array();
		if(count($rows) > 0) {
			foreach($rows as $key => $value) 
			{		
				$query = "SELECT name, conditional_field_name, conditional_field_values FROM #__registrationpro_fields WHERE published=1 AND conditional_field > 0 AND form_id = ".$this->form_id." AND conditional_field_name = '".$value['conditional_field_name']."'";								
				$this->_db->setQuery($query);		
				$rows1 = $this->_db->loadAssocList();
								
				//echo "<pre>"; print_r($rows1); exit;							
				if(count($rows1) > 0) {
					foreach($rows1 as $rkey => $rvalue) 
					{
						$rvalue['conditional_field_values'] = unserialize($rvalue['conditional_field_values']);				
						$clone_rows[$value['conditional_field_name']][$rkey] = $rvalue;			
					}
				}																									
			}		
		}
		
		//echo "<pre>"; print_r($clone_rows); exit;
				
		/*$query = "SELECT name, conditional_field_name, conditional_field_values FROM #__registrationpro_fields WHERE published=1 AND form_id = ".$this->form_id." AND conditional_field > 0";				
		$this->_db->setQuery($query);
		
		$rows = $this->_db->loadAssocList();
		$clone_rows = array();
		
		if(count($rows) > 0) {
			foreach($rows as $key => $value) 
			{
				$value['conditional_field_values'] = unserialize($value['conditional_field_values']);				
				$clone_rows[$value['name']] = $value;				
			}		
		}*/
		//echo "<pre>"; print_r($rows); //exit;
				
		return $clone_rows;
	}
	
	function getCBfields()
	{
		global $mosConfig_absolute_path, $mosConfig_allowUserRegistration, $mainframe, $ueConfig;
		
		$formId = $this->form_id;
						
		$my =JFactory::getUser(); // get user details
		
		//check if community builder component exists	
		$cb_fields = array();
					
		// check if CB is used for login and user registration with website
		$arrCB = array();
		//echo"<pre>";print_r($my);			
		
		// get CB fields value for prefilled values					
		$strqry = "SELECT jl.*, cbl.* FROM #__users as jl LEFT JOIN #__comprofiler as cbl ON jl.id = cbl.user_id WHERE jl.id = ". $my->id ." AND jl.block = 0 AND cbl.approved = 1 AND cbl.confirmed = 1"; 
			
			$this->_db->setQuery($strqry);
			$userdata = $this->_db->loadObjectList();
			$userdata = $userdata[0];
			$arrCB['userdata'] =  $userdata;					
			//echo "<pre>";print_r($arrCB['userdata']);
		// end
						
		// get CB fields to display on form according to formId					
		//$strqry1 = "SELECT rcbf.*, cbf.* FROM #__registrationpro_cbfields as rcbf LEFT JOIN #__comprofiler_fields as cbf  ON rcbf.cbfield_id = cbf.fieldid WHERE rcbf.form_id = $formId AND cbf.published = 1 AND cbf.name <> '' ";
	
			$strqry1 = "SELECT rcbf.*, cbf.* FROM #__registrationpro_cbfields as rcbf"
				. "\n LEFT JOIN #__comprofiler_fields as cbf  ON rcbf.cbfield_id = cbf.fieldid "
				. "\n INNER JOIN #__comprofiler_tabs AS t ON ( (cbf.tabid = t.tabid) AND (t.fields = 1) ) "				
				. "\n WHERE rcbf.form_id = $formId AND cbf.published = 1 AND cbf.name <> '' "
				. "\n ORDER BY t.ordering, cbf.ordering";						
			
			$this->_db->setQuery($strqry1);
			$cbfields = $this->_db->loadObjectList();															
			$arrCB['cbfields'] =  $cbfields;
			//echo "<pre>";print_r($arrCB['cbfields']); exit;
		// end						
			
		// get CB fields values for (select box, radio button, checkbox etc)
			$strqry2 = "SELECT rcbf.*, cbfv.* FROM #__registrationpro_cbfields as rcbf LEFT JOIN #__comprofiler_field_values as cbfv  ON rcbf.cbfield_id = cbfv.fieldid WHERE rcbf.form_id = $formId AND cbfv.fieldvalueid <> ''"; 
			
			$this->_db->setQuery($strqry2);
			$cbfield_values = $this->_db->loadObjectList();															
			$arrCB['cbfield_values'] =  $cbfield_values;																							
			//echo "<pre>";print_r($arrCB['cbfield_values']);
		// end													
			
		//return $arrCB;		
		
		$cbfields 		= $arrCB['cbfields']; 		// CB fields array
		$udata			= $arrCB['userdata']; 		// CB fields values with user info array
		$cbfieldsvalues = $arrCB['cbfield_values']; 	// CB fields values (selectbox etc.) array														
			
		// get registration pro fields		
		$fields = $this->getFields();
		$fieldscount 	= count($fields); 	// set counter for inserting new array index in fields array
		// end
																						
		// merge CB fields information with $fields array	
		foreach ($cbfields as $cbkey=>$cbvalues){
			$flag = 0;																				
			foreach ($fields as $key=>$field){								
				$key1 = $cbfields[$cbkey]->name;
				// remove cb_ prefix from field name to compare with Regpro fields
					//$cbfields[$cbkey]->name = str_replace("cb_","",$cbfields[$cbkey]->name);
				// end
																		
				if(strtolower($fields[$key]->name) == strtolower($cbfields[$cbkey]->name)){													
					$fields[$key]->default_value = $udata->$key1;
					// remove fields from CB array if its already exist in regpro fields array		
					//unset($CB_Data['cbfields'][$cbkey]); 
					// end													
					$flag = 1;
				}
				
				// create array of regpro fields values of (multicheckbox, radios, selectbox, multiselectbox)																																						
				/*if($fields[$key]->inputtype == "multicheckbox" || $fields[$key]->inputtype == "radio" || $fields[$key]->inputtype == "select" || $fields[$key]->inputtype =="multiselect")
				{
					$expvalues = explode(",",$fields[$key]->values);
					//echo "<pre>"; print_r($expvalues); exit;
					$fields[$key]->values = $expvalues;
				}*/
			}
			//echo "<pre>"; print_r($fields); exit;	
			
			if($flag != 1){											
				@$fields[$fieldscount]->id 				= $cbfields[$cbkey]->id;
				$fields[$fieldscount]->cbfeild_id 		= $cbfields[$cbkey]->cbfield_id;
				$fields[$fieldscount]->form_id 			= $cbfields[$cbkey]->form_id;
				$fields[$fieldscount]->name 			= $cbfields[$cbkey]->name;
				$fields[$fieldscount]->title 			= getLangDefinition($cbfields[$cbkey]->title);
				$fields[$fieldscount]->description 		= $cbfields[$cbkey]->description;
				$fields[$fieldscount]->inputtype		= $cbfields[$cbkey]->type;
				$fields[$fieldscount]->default_value 	= ($key1)? $udata->$key1 :'';
				$fields[$fieldscount]->params 			= '';
				$fields[$fieldscount]->validation_rule	= ($cbfields[$cbkey]->required)? "mandatory" :'';												

				$fields[$fieldscount]->ordering 		= $cbfields[$cbkey]->ordering;
				$fields[$fieldscount]->published 		= $cbfields[$cbkey]->published;
				$fields[$fieldscount]->batch_display 	= 1;
				
				// add values of checkbox, radio button and select boxes
				$arr_field_values = array();
				foreach($cbfieldsvalues as $cbvkey=>$cbvvalue)
				{
					if($cbfields[$cbkey]->id == $cbfieldsvalues[$cbvkey]->id && $cbfields[$cbkey]->cbfield_id == $cbfieldsvalues[$cbvkey]->cbfield_id)
					{														
						//$fields[$fieldscount]->values[$cbfieldsvalues[$cbvkey]->fieldtitle] = $cbfieldsvalues[$cbvkey]->fieldtitle;
						//echo "<pre>"; print_r($cbfieldsvalues);
						//$fields[$fieldscount]->values .= $cbfieldsvalues[$cbvkey]->fieldtitle.",";
						array_push($arr_field_values,$cbfieldsvalues[$cbvkey]->fieldtitle);						
					}
				}
				
				if(count($arr_field_values) > 0){
					$fields[$fieldscount]->values = implode(",",$arr_field_values);
				}
														
				$fieldscount = $fieldscount+1;																
			}										
		}		
		//end
		//exit;
		//echo "<pre>"; print_r($fields); exit;		
		return $fields;
	}
	
	
	function getJoomsocialfields()
	{
		global $mosConfig_absolute_path, $mosConfig_allowUserRegistration, $mainframe, $ueConfig;
		
		$formId = $this->form_id;
						
		$my =JFactory::getUser(); // get user details
		
		//check if joomsocial component exists	
		$joomsocialfields = array();
					
		// check if joomsocial is used for login and user registration with website
		$arrJS = array();
		//echo"<pre>";print_r($my);			
		
		// get joomsocial fields value for prefilled values					
			$strqry = "SELECT * FROM #__community_fields_values WHERE user_id = ". $my->id;			
			$this->_db->setQuery($strqry);
			$userdata = $this->_db->loadObjectList();			
			$arrJS['userdata'] =  $userdata;					
			//echo "<pre>";print_r($arrJS['userdata']); exit;
		// end
						
		// get CB fields to display on form according to formId	
			$strqry1 = "SELECT rcbf.id as rcbfid,rcbf.form_id, rcbf.joomfishfield_id, jsf.* FROM #__registrationpro_cbfields as rcbf"
				. "\n LEFT JOIN #__community_fields as jsf  ON rcbf.joomfishfield_id = jsf.id "		
				. "\n WHERE rcbf.form_id = $formId AND jsf.published = 1 AND jsf.name <> '' "
				. "\n ORDER BY jsf.ordering";						
			
			$this->_db->setQuery($strqry1);
			$joomsocialfields = $this->_db->loadObjectList();															
			$arrJS['joomsocialfields'] =  $joomsocialfields;
			//echo "<pre>";print_r($arrCB['joomsocialfields']); exit;
		// end													
		
		//echo "<pre>"; print_r($arrJS); exit;
		
		$joomsocialfields 	= $arrJS['joomsocialfields']; 		// joomsocial fields array
		$udata				= $arrJS['userdata']; 				// joomsocial fields values with user info array
			
		// get registration pro fields		
		$fields = $this->getFields();
		$fieldscount 	= count($fields); 	// set counter for inserting new array index in fields array
		// end
		
		//echo "<pre>"; print_r($joomsocialfields); exit;
																						
		// merge joomsocial fields information with $fields array	
		foreach ($joomsocialfields as $jskey=>$jsvalues){
			$flag = 0;
			
			
			// get value from user profile to fill in the field
			$default_value = "";
			foreach($udata as $ukey => $uvalue)
			{
				if($jsvalues->id == $uvalue->field_id){
					$default_value = trim($uvalue->value);
					break;
				}
			}
			// end
																						
			foreach ($fields as $key=>$field){
				if(strtolower($fields[$key]->name) == strtolower($joomsocialfields[$jskey]->name)){													
					$fields[$key]->default_value = $default_value;
					// remove fields from CB array if its already exist in regpro fields array		
					//unset($CB_Data['cbfields'][$jskey]); 
					// end													
					$flag = 1;
				}								
			}
			//echo "<pre>"; print_r($fields); exit;	
			
			if($flag != 1){											
				$fields[$fieldscount]->id 				= $joomsocialfields[$jskey]->id;
				$fields[$fieldscount]->jsfeild_id 		= $joomsocialfields[$jskey]->joomfishfield_id;
				$fields[$fieldscount]->form_id 			= $joomsocialfields[$jskey]->form_id;
				$fields[$fieldscount]->name 			= $joomsocialfields[$jskey]->name;
				$fields[$fieldscount]->title 			= $joomsocialfields[$jskey]->name;
				$fields[$fieldscount]->description 		= $joomsocialfields[$jskey]->tips;				
				$fields[$fieldscount]->default_value 	= $default_value;
				$fields[$fieldscount]->params 			= '';
				$fields[$fieldscount]->validation_rule	= ($joomsocialfields[$jskey]->required)? "mandatory" :'';												

				$fields[$fieldscount]->ordering 		= $joomsocialfields[$jskey]->ordering;
				$fields[$fieldscount]->published 		= $joomsocialfields[$jskey]->published;
				$fields[$fieldscount]->batch_display 	= 1;
								
				// assign field type according to registration form fields												
				if($joomsocialfields[$jskey]->type == "date" || $joomsocialfields[$jskey]->type == "birthdate"){
					$fields[$fieldscount]->inputtype	= "calendar";
				}elseif($joomsocialfields[$jskey]->type == "country" || $joomsocialfields[$jskey]->type == "singleselect"){
					$fields[$fieldscount]->inputtype	= "select";
				}elseif($joomsocialfields[$jskey]->type == "group"){
					$fields[$fieldscount]->inputtype	= "groups";
				}elseif($joomsocialfields[$jskey]->type == "checkbox"){
					$fields[$fieldscount]->inputtype	= "multicheckbox";
				}elseif($joomsocialfields[$jskey]->type == "email"){
					$fields[$fieldscount]->inputtype	= "text";
					$fields[$fieldscount]->validation_rule	= "email";				
				}else{
					$fields[$fieldscount]->inputtype	= $joomsocialfields[$jskey]->type;
				}
				// end
				
				// add values of checkbox, radio button and select boxes
				$arr_field_values = array();				
				if($joomsocialfields[$jskey]->options)
				{										
					$arr_field_values = explode("\n",$joomsocialfields[$jskey]->options);
					//echo "<pre>"; print_r($arr_field_values); exit;
					$fields[$fieldscount]->values = implode(",",$arr_field_values);
				}
				// end							
														
				$fieldscount = $fieldscount+1;																
			}										
		}		
		//end
		//echo "<pre>"; print_r($fields); exit;		
		return $fields;
	}
	
	function getCorefields()
	{		
		$formId = $this->form_id;
		
		
		// get user fields values				
		$my =JFactory::getUser(); // get user details
		
		if (isset($my->id)) {

			// Load the profile data from the database.
			$db = JFactory::getDbo();
			$db->setQuery(
				'SELECT profile_key, profile_value FROM #__user_profiles' .
				' WHERE user_id = '.(int) $my->id." AND profile_key LIKE 'profile.%'" .
				' ORDER BY ordering'
			);
			$results = $db->loadRowList();
										
			// Check for a database error.
			if ($db->getErrorNum())
			{
				$this->_subject->setError($db->getErrorMsg());
				return false;
			}
	
			// Merge the profile data.
			@$data->profile = array();
	
			foreach ($results as $v)
			{
				$k = str_replace('profile.', '', $v[0]);
				$data->profile[$k] = $v[1];
			}
		}
		
		//echo "<pre>"; print_r($data->profile); echo "<pre>"; print_r($my); exit;
		// end
		
		
		$strqry1 = "SELECT form_id, corefield_id FROM #__registrationpro_cbfields WHERE form_id = $formId AND corefield_id <> '' ORDER BY id";									
		$this->_db->setQuery($strqry1);
		$profilefields = $this->_db->loadObjectList();															
		$arrCF['Profilefields'] =  $profilefields;
		
		
		$Corefields 	= $arrCF['Profilefields']; 		// core fields array
		$udata			= $data->profile; 				// core fields values with user info array
		
		//echo "<pre>"; print_r($Corefields); exit;
				
		// get registration pro fields		
		$fields 		= $this->getFields();
		$fieldscount 	= count($fields); 	// set counter for inserting new array index in fields array
		// end
						
		$plugin = JPluginHelper::getPlugin('user', 'profile');
		
		$pluginParams = new JRegistry();
		$pluginParams->loadString($plugin->params);
		//$param = $pluginParams->get('paramName', 'defaultValue');
		
		//echo $pluginParams->get('register-require_address1');	
		
		$corefields = array();	
		$i=0;
		
		
		// Create user first and last name	
			$arrname = explode(" ",$my->name);
			//echo $my->name; //echo "<pre>"; print_r($arrname); exit;
			$strlname = "";
			if(is_array($arrname) && count($arrname) > 1) {
				$j = 0;
				foreach($arrname as $fkey => $fvalue){
					if($j == 0){
						$fname = $fvalue;
					}else{
						$strlname .= $fvalue." ";
					}
					$j++;
				}								
				$lname = $strlname;
			}else{
				$fname = $arrname[0];
				$lname = "";
			}									
			
		// end
		
		// Create firstname, lastname and email fields for fill user data
		$corefields[$i]['title'] = "First Name";
		$corefields[$i]['name']  = "First Name";
		$corefields[$i]['identification'] = "firstname";			
		$corefields[$i]['inputtype'] = "text";
		$corefields[$i]['form_id'] = $formId;
		$corefields[$i]['default_value'] = trim($fname);
		$i++;
		
		$corefields[$i]['title'] = "Last Name";
		$corefields[$i]['name']  = "Last Name";
		$corefields[$i]['identification'] = "lastname";			
		$corefields[$i]['inputtype'] = "text";
		$corefields[$i]['form_id'] = $formId;
		$corefields[$i]['default_value'] = trim($lname);
		$i++;
		
		$corefields[$i]['title'] = "Email";
		$corefields[$i]['name']  = "Email";
		$corefields[$i]['identification'] = "email";			
		$corefields[$i]['inputtype'] = "text";
		$corefields[$i]['form_id'] = $formId;
		$corefields[$i]['default_value'] = trim($my->email);
		$i++;
		// end
		
		foreach($Corefields as $ckey => $cvalue)
		{		
			$cvalue->corefield_id = trim($cvalue->corefield_id);
			
			if ($pluginParams->get('register-require_'.$cvalue->corefield_id, 1) > 0) {
				$ftitle =  "";
				//$fname 	=  "";
				
				$cvalue->corefield_id = trim($cvalue->corefield_id);
				
				if($cvalue->corefield_id == 'address1'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_ADDRESS1");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_ADDRESS1");
				}elseif($cvalue->corefield_id == 'address2'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_ADDRESS2");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_ADDRESS2");
				}elseif($cvalue->corefield_id == 'city'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_CITY");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_CITY");
				}elseif($cvalue->corefield_id == 'region'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_REGION");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_REGION");
				}elseif($cvalue->corefield_id == 'country'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_COUNTRY");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_COUNTRY");
				}elseif($cvalue->corefield_id == 'postal_code'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_POSTALCODE");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_POSTALCODE");
				}elseif($cvalue->corefield_id == 'phone'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_PHONE");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_PHONE");
				}elseif($cvalue->corefield_id == 'website'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_WEBSITE");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_WEBSITE");
				}elseif($cvalue->corefield_id == 'favoritebook'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_FAVORITEBOOK");
					$finputtype = "text";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_FAVORITEBOOK");
				}elseif($cvalue->corefield_id == 'aboutme'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_ABOUTME");
					$finputtype = "textarea";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_ABOUTME");
				}elseif($cvalue->corefield_id == 'tos'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_TOS");
					$finputtype = "radio";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_TOS");
				}elseif($cvalue->corefield_id == 'dob'){
					$ftitle =  JText::_("ADMIN_PROFILE_FIELDS_DOB");
					$finputtype = "calendar";
					//$fname 	=  JText::_("ADMIN_PROFILE_FIELDS_DOB");
				}
				
					
				$corefields[$i]['title'] = $ftitle;
				$corefields[$i]['name']  = $ftitle;
				$corefields[$i]['identification'] = $cvalue->corefield_id;			
				$corefields[$i]['inputtype'] = $finputtype;
				$corefields[$i]['form_id'] = $formId;
							
				// set default value										
				if(is_array($udata) && count($udata) > 0) {
					foreach($udata as $ukey => $uvalue)
					{
						if($ukey == $corefields[$i]['identification']){							
							//$corefields[$i]['default_value'] = $uvalue;
							$corefields[$i]['default_value'] = str_replace('"','',$uvalue);
							break;
						}else{
							$corefields[$i]['default_value'] = '';
						}																					
					}
				}
				// end
				
				// check if field is mandatory						
				if($pluginParams->get('register-require_'.$cvalue->corefield_id, 1) == 2) {
					$corefields[$i]['validation_rule'] = 'mandatory';				
				}else{
					$corefields[$i]['validation_rule'] = '';
				}
				// end
				$i++;
			}
		}
		
		//echo "<pre>"; print_r($corefields); exit;			
		
		// removce fields if already exists in main form fields
		foreach ($fields as $key=>$field){
			foreach($corefields as $ckey => $cvalue)
			{
				if(strtolower($fields[$key]->name) == strtolower($corefields[$ckey]['identification'])){													
					$fields[$key]->default_value = $corefields[$ckey]['default_value'];
					// remove fields from corefields array if its already exist in regpro fields array		
					unset($corefields[$ckey]); 
					// end																		
				}
			}							
		}
		// end				
		
		if(is_array($corefields) && count($corefields) > 0) {
		
			foreach($corefields as $corekey => $corevalue)
			{						
				$fields[$fieldscount]->id 				= $fieldscount;				
				$fields[$fieldscount]->form_id 			= $corefields[$corekey]['form_id'];
				$fields[$fieldscount]->name 			= $corefields[$corekey]['name'];
				$fields[$fieldscount]->title 			= $corefields[$corekey]['title'];
				$fields[$fieldscount]->description 		= "";
				$fields[$fieldscount]->inputtype		= $corefields[$corekey]['inputtype'];
				$fields[$fieldscount]->default_value 	= $corefields[$corekey]['default_value'];
				$fields[$fieldscount]->params 			= $corefields[$corekey]['params'];
				$fields[$fieldscount]->validation_rule	= $corefields[$corekey]['validation_rule'];												
		
				$fields[$fieldscount]->ordering 		= "";
				$fields[$fieldscount]->published 		= 1;
				$fields[$fieldscount]->batch_display 	= 1;
				
				$fieldscount++;
			}
		}
		
		
		/*echo "<pre>"; print_r($fields); exit;
		
		echo "<pre>"; print_r($plugin); 
		echo "<pre>"; print_r($param); exit;*/
		
		return $fields;
		
	}
	
	/*function getJoomsocialProfileTypes()
	{				
		
		$path	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'fields' . DS . 'customfields.xml';
		
		$parser	=JFactory::getXMLParser( 'Simple' );
		$parser->loadFile( $path );
		$fields	= $parser->document->getElementByPath( 'fields' );
		$data	= array();
		
		foreach( $fields->children() as $field )
		{
			$type	= $field->getElementByPath( 'type' );
			$name	= $field->getElementByPath( 'name' );
			$data[ $type->data() ]	= $name->data();
		}
		$types	= $data;
				
		return $types;
	}	*/
}


// start form and fields html class
Class regpro_forms_html 
{	
	/* Parses fields automatically generated by the form */
	function parseFields($row, $conditional_fields, $position='',$default_value,$count=0,$payment_id,$k){
			
			$doc =JFactory::getDocument();						
			//echo "<pre>"; print_r($conditional_fields); //exit;
			
			// check field is conditional or not						
			if($row->conditional_field > 0) {
				$hide_field = "style='display:none;'";
				//$disable_field = "disabled='disabled'";
			}else{
				$hide_field = "";
				$disable_field = "";
			}												
			// end
			
			if($position!='') {
				$position_html = $position;
			}else{
				$position_html = '';
			}
									
			if($default_value!='')$row->default_value = $default_value;
						
			if(!empty($row->description)){					
				// check if mentioned in laguage file
					if(defined($row->description)) $row->description = constant($row->description);			
				// end	
			
				$field_description .= '<span class="editlinktip hasTip" style="vertical-align:middle" title="'.$row->description.'"><img src="'.REGPRO_ADMIN_IMG_PATH.'/info.png" border="0" align="absmiddle"></span>&nbsp;';
			}else{
				$field_description = "";	
			}	
			
			$html = '<tr class="row'.$k.'" id="'.$row->name.$count."-rows".'" '.$hide_field.'>';

			if($row->inputtype == "groups"){
				$html .= '<td class="regpro_field_groups">'.$field_description.' '.$row->title.'</span> <img src="'.REGPRO_IMG_PATH.'/down.png" border="0" align="absmiddle"/></td> <td class="regpro_field_groups">&nbsp;';
			}else{									
				$html .= '<td class="regpro_vtop_aright" style="text-align:right;width:25%;">'.$field_description.(($row->validation_rule) ? JText::_('ADMIN_MANDATORY_SYMBOL'):'')." ".$row->title.'</td>';
			
			switch($row->inputtype){
			
				case 'groups':					
					$html .='<td class="regpro_field_groups">&nbsp;';
					//$html .='';										
					break;

				case 'text':	
				
					// check if mentioned in laguage file
							if(defined($row->default_value)) $row->default_value = constant($row->default_value);			
					// end				
						
					// automatic fill values if return back to registrtion page if occur any error
					if($_POST){
						if(is_array($_POST['form'])){
							foreach($_POST['form'] as $postkey=>$postvalue)
							{
								if($postkey == $row->name){
									$row->default_value = $_POST['form'][$postkey][$count][$payment_id];
								}
							}
						}
					}
					// end
						
														
					if($row->validation_rule == "mandatory"){
						$jsvalidation = "class=validate['required']";
					}else if($row->validation_rule == "email"){
						$jsvalidation = "class=validate['required','email']";
					}else if($row->validation_rule == "number"){
						$jsvalidation = "class=validate['required','number']";
					}else if($row->validation_rule == "confirm"){
						$jsvalidation = "class=validate['required','=".$row->confirm.'['.$count.']'.$position_html."']";
					}else{
						$jsvalidation = '';
					}									
					
					//$html.='<td class="regpro_vtop_aleft"><input type="text" class="regpro_inputbox" '.$jsvalidation.' name="form['.$row->name.']['.$count.']'.$position_html.'" value="'.$row->default_value.'" id="'.$row->name.$position.'" '.$row->params.' />';									
					$html.='<td class="regpro_vtop_aleft"><input type="text" '.$jsvalidation.' name="form['.$row->name.']['.$count.']'.$position_html.'" value="'.$row->default_value.'" id="'.$row->name.'['.$count.']'.$position_html.'" '.$row->params.' />';	

				break;


				case 'password':
				
					// check if mentioned in laguage file
							if(defined($row->default_value)) $row->default_value = constant($row->default_value);			
					// end
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){
										$row->default_value = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end
					
					if($row->validation_rule == "mandatory"){
						$jsvalidation = "class=validate['required']";
					}else if($row->validation_rule == "email"){
						$jsvalidation = "class=validate['required','email']";
					}else if($row->validation_rule == "number"){
						$jsvalidation = "class=validate['required','number']";
					}else if($row->validation_rule == "confirm"){
						$jsvalidation = "class=validate['required','=".$row->confirm.'['.$count.']'.$position_html."']";
					}else{
						$jsvalidation = '';
					}
										
					$html.='<td class="regpro_vtop_aleft"><input type="password" '.$jsvalidation.' name="form['.$row->name.']['.$count.']'.$position_html.'" value="'.$row->default_value.'" id="'.$row->name.'['.$count.']'.$position_html.'" '.$row->params.' />';

				break;


				case 'radio':

					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){								
										//$row->default_value = $_POST['form'][$postkey][$count][$count];
										 $chkPostVal = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end
					
					if($row->validation_rule == "mandatory"){						
						$jsvalidation = "class=validate['required']";
					}else{
						$jsvalidation = '';
					}					
					
					// checked option (comes in post data)
						if($chkPostVal == $row->default_value)
							$default_value = "checked";
						else
							$default_value = "";
					//
					
					$html.='<td class="regpro_vtop_aleft">';															

					$radio_values 	= explode(",",$row->values);
					$default_value 	= explode(",",$row->default_value);
					
					//echo "<pre>"; print_r($row->default_value); echo "<pre>"; print_r($row->values); exit;
					//echo "<pre>"; print_r($row); exit;
					
					$temp_sel_values = array();										
					// If this is fees field
					if($row->fees_field == 1 && $row->fees != ""){																
						$fee_values  = explode(',',$row->fees);
																	
						foreach ($radio_values as $skey => $svalue)
						{																								
							$temp_sel_values[$fee_values[$skey]] = $svalue;
						}
						//$sel_values = $temp_sel_values;						
					}else{
						foreach ($radio_values as $skey => $svalue)
						{
							$temp_sel_values[$svalue] = $svalue;
						}
					}
					// end
					$radio_values = $temp_sel_values;
					
					//echo "<pre>"; print_r($radio_values); exit;
					
					
					$k=0;
					$z = 1;
					foreach($radio_values as $key => $value)					
					{	
						// check if mentioned in laguage file
							if(defined($value)) $value = constant($value);			
						// end	
						
						$checked = "";
						if($_POST && is_array($chkPostVal)){ 	// selected values after posting the form
							for($i=0;$i<count($chkPostVal);$i++)
							{								
								if($chkPostVal[$i][$count] == $value)
									$checked = "checked";					
							}														
						}elseif(is_array($default_value)){ // selected values as default values
							foreach($default_value as $defvalue)
							{	
								// check if mentioned in laguage file
									if(defined($defvalue)) $defvalue = constant($defvalue);									
								// end							
								if(trim($defvalue) == trim($value))
									$checked = "checked";					
							}							
						}	
						
						
					// add conditional field script
						if(is_array($conditional_fields) && count($conditional_fields) > 0) {
							$get_keys = array_keys($conditional_fields);
							if(in_array($row->name,$get_keys)) {
								$element_id 	= $row->name.'['.$count.']'.$position_html.'['.$z.']';
								
								$js_script = 'window.addEvent("domready", function(){
									$("'.$element_id.'").addEvent("click", function(){
										//alert("hello");
										//alert($("'.$element_id.'").value);
										//alert($("'.$element_id.'").getProperty("checked"));
																																												
										var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';									
										
										for (var i=0;i < arr_cond_fields.length; i++)
										{										
											var showfieldrow= arr_cond_fields[i]["name"]+"'.$count.'"+"-rows";
											var showfield 	= arr_cond_fields[i]["name"]+"'.'['.$count.']'.$position_html.'";																				
																				
											var obj = arr_cond_fields[i]["conditional_field_values"];	
											
											if($("'.$element_id.'").getProperty("checked") == true) {
												if(obj.contains($("'.$element_id.'").value)) {
													$(showfieldrow).setStyle("display","");
													$(showfield).set("disabled", false);
												}else{
													$(showfield).set("disabled", true);
													$(showfieldrow).setStyle("display","none");
												}
											}																																												
										}																																			
									});																																								
								});';
								
								$doc->addScriptDeclaration($js_script);
							}
						}															
					// end
						
											
						
						$html.='<input type="radio" '.$jsvalidation.' name="form['.$row->name.']['.$count.']'.$position_html.'" value="'.$value.'" id="'.$row->name.'['.$count.']'.$position_html.'['.$z.']'.'" '.$checked.' '.$row->params.' '.$disable_field.'/>&nbsp;'.$value.'&nbsp;&nbsp;';
					
						if($row->display_type == 2){
							$html.= "<br />";
						}
						
						$z++;												
					}	 
					
					//$html.='<td class="regpro_vtop_aleft"><input type="radio"'.$jsvalidation.' name="form['.$row->name.']'.$position_html.$count.'" value="'.$row->default_value.'" id="'.$row->name.$position.$count.'" '.$row->params.' '.$default_value.' />';

				break;

				case 'checkbox':
				
					// check if mentioned in laguage file
							if(defined($row->default_value)) $row->default_value = constant($row->default_value);			
					// end
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){								
										//$row->default_value = $_POST['form'][$postkey][$count][$count];																	
										 $chkPostVal = $_POST['form'][$postkey];
									}
								}
							}
						}
					// end
				
					if($row->validation_rule == "mandatory"){
						$jsvalidation = "class=validate['required']";
					}else{
						$jsvalidation = '';
					}
															
					$checked = "";				
					if($_POST && is_array($chkPostVal)){ 	// Checked Defualt values after posting the form
						for($i=0;$i<count($chkPostVal);$i++)
						{								
							//echo $chkPostVal[$i][$count]," -- ",$count,"<br>";
							if($chkPostVal[$i][$count] == $row->default_value)
								$checked = "checked";					
						}														
					}
					
					// add conditional field script
					if(is_array($conditional_fields) && count($conditional_fields) > 0) {
						$get_keys = array_keys($conditional_fields);
						if(in_array($row->name,$get_keys)) {
							//$jsfunction = "onchange='return '";
							$element_id 	= $row->name.'['.$count.']'.$position_html;
							
							$js_script = 'window.addEvent("domready", function(){

								var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';	

								C_ValidateForm( $("'.$element_id.'") , arr_cond_fields );
								
								$("'.$element_id.'").addEvent("click", function(){
									
									var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';	

									C_ValidateForm( $(this), arr_cond_fields );
								});																																								
							});
							function C_ValidateForm( cOBJ, arr_cond_fields ){
									//alert($("'.$element_id.'").value);
									//alert($("'.$element_id.'").getProperty("checked"));										
									
									for (var i=0;i < arr_cond_fields.length; i++)
									{										
										var showfieldrow= arr_cond_fields[i]["name"]+"'.$count.'"+"-rows";
										var showfield 	= arr_cond_fields[i]["name"]+"'.'['.$count.']'.$position_html.'";																				
																	
										var obj = arr_cond_fields[i]["conditional_field_values"];	
										
										if( cOBJ.getProperty("checked") == true) {
											$(showfieldrow).setStyle("display","");
											
											j("[id^=\""+showfield+"\"]").attr("disabled", false);
											/* if(document.getElementById(showfield)){
												$(showfield).set("disabled", false);
											} */
										}else{
											
											j("[id^=\""+showfield+"\"]").attr("disabled", true);
											/* if(document.getElementById(showfield))
											{
												$(showfield).set("disabled", true);
											} */
											$(showfieldrow).setStyle("display","none");											
										}	
										
									}																													
							}';
							
							$doc->addScriptDeclaration($js_script);
						}
					}															
					// end
					
					// If this is fees field
					if($row->fees_field == 1 && $row->fees != ""){																
						$row->default_value  = $row->fees;
					}
																											
					$html.='<td class="regpro_vtop_aleft"><input type="checkbox" '.$jsvalidation.' name="form['.$row->name.']['.$count.']'.$position_html.'" value="'.$row->default_value.'" id="'.$row->name.'['.$count.']'.$position_html.'" '.$row->params.' '.$checked.' '.$disable_field.'/>';

				break;
				
				
				case 'multicheckbox':
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){								
										//$row->default_value = $_POST['form'][$postkey][$count][$count];																	
										 $chkPostVal = $_POST['form'][$postkey];
									}
								}
							}
						}
					// end
					
					if($row->validation_rule == "mandatory"){						
						$jsvalidation = "class=validate['required']";
					}else{
						$jsvalidation = '';
					}									
					
					$html.='<td class="regpro_vtop_aleft">';
					
					$check_values  = explode(',',$row->values);
					$default_val = explode(',',$row->default_value);										
					
					
					$temp_sel_values = array();										
					// If this is fees field
					if($row->fees_field == 1 && $row->fees != ""){																
						$fee_values  = explode(',',$row->fees);
																	
						foreach ($check_values as $skey => $svalue)
						{																								
							$temp_sel_values[$fee_values[$skey]] = $svalue;
						}
						//$sel_values = $temp_sel_values;						
					}else{
						foreach ($check_values as $skey => $svalue)
						{
							$temp_sel_values[$svalue] = $svalue;
						}
					}
					// end
					$check_values = $temp_sel_values;	
					
					//$row->default_value = explode(",",$row->default_value);
					//$row->values 		= explode(",",$row->values);
					
					//echo "<pre>"; print_r($row->default_value); echo "<pre>"; print_r($row->values); exit;															
					
					$k=0;
					$z = 1;
					foreach($check_values as $key => $value)					
					{	
						// check if mentioned in laguage file
							if(defined($value)) $value = constant($value);			
						// end
						
						$checked = "";
						if($_POST && is_array($chkPostVal)){ 	// selected values after posting the form
							for($i=0;$i<count($chkPostVal);$i++)
							{								
								if($chkPostVal[$i][$count] == $value)
									$checked = "checked";					
							}														
						}elseif(is_array($default_val)){ // selected values as default values
							foreach($default_val as $defvalue)
							{		
								// check if mentioned in laguage file
									if(defined($defvalue)) $defvalue = constant($defvalue);									
								// end						
								if(trim($defvalue) == trim($value))
									$checked = "checked";					
							}							
						}
						
						// add conditional field script
						if(is_array($conditional_fields) && count($conditional_fields) > 0) {
							$get_keys = array_keys($conditional_fields);
							if(in_array($row->name,$get_keys)) {
								$element_id 	= $row->name.'['.$count.']'.$position_html.'['.$z.']';
								
								$js_script = 'window.addEvent("domready", function(){
									$("'.$element_id.'").addEvent("click", function(){
										//alert("hello");
										//alert($("'.$element_id.'").value);
										//alert($("'.$element_id.'").getProperty("checked"));
																																												
										var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';									
										
										for (var i=0;i < arr_cond_fields.length; i++)
										{										
											var showfieldrow= arr_cond_fields[i]["name"]+"'.$count.'"+"-rows";
											var showfield 	= arr_cond_fields[i]["name"]+"'.'['.$count.']'.$position_html.'";																				
																				
											var obj = arr_cond_fields[i]["conditional_field_values"];	
											
											if($("'.$element_id.'").getProperty("checked") == true) {
												if(obj.contains($("'.$element_id.'").value)) {
													$(showfieldrow).setStyle("display","");
													$(showfield).set("disabled", false);
												}
											}else{																																		
												
												if(obj.contains($("'.$element_id.'").value)) {
													$(showfield).set("disabled", true);
													$(showfieldrow).setStyle("display","none");
												}																																														
											}																																													
										}																																			
									});																																								
								});';
								
								$doc->addScriptDeclaration($js_script);
							}
						}															
						// end																	
												
						$html.='<input type="checkbox" '.$jsvalidation.' name="form['.$row->name.']['.$count.'][]'.$position_html.'" value="'.$key.'" id="'.$row->name.'['.$count.']'.$position_html.'['.$z.']'.'" '.$row->params.' '.$checked.' '.$disable_field.'/>&nbsp;'.$value.'&nbsp;&nbsp;';
						
						if($row->display_type == 2){
							$html.= "<br />";
						}
						$z++;					
					}									
				break;

				case 'textarea':
				
					// check if mentioned in laguage file
							if(defined($row->default_value)) $row->default_value = constant($row->default_value);			
					// end
					
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){								
										$row->default_value = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end

					if($row->validation_rule == "mandatory"){
						$jsvalidation = "class=validate['required']";
					}else{
						$jsvalidation = '';
					}									
					
					$html.='<td class="regpro_vtop_aleft"><textarea name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' id="'.$row->name.'['.$count.']'.$position_html.'" '.$row->params.'>'.$row->default_value.'</textarea>';


				break;

				case 'select':
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){																									
										 $chkPostVal = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end															

					$options = '';
					
					$sel_values  = explode(',',$row->values);
					$default_val = explode(',',$row->default_value);	
					
					$temp_sel_values = array();										
					// If this is fees field
					if($row->fees_field == 1 && $row->fees != ""){																
						$fee_values  = explode(',',$row->fees);
						$i=0;										
						foreach ($sel_values as $skey => $svalue)
						{
							$temp_sel_values[$svalue] = $fee_values[$i];
							$i++; 
						}
						//$sel_values = $temp_sel_values;	
						// end
					$sel_values = $temp_sel_values;														

					if($row->validation_rule == "mandatory"){						
						$jsvalidation = "class=validate['required']";
						$options .= '<option value="">'.JText::_('REGPRO_SELECT_ONE').'</option>';
					}else{
						$jsvalidation = '';
					}
					foreach ($sel_values as $value => $key){
					
						// check if mentioned in laguage file
							if(defined($value)) $value = constant($value);									
						// end
											
						$selected = "";												
						if($_POST && $chkPostVal == $value){ // selected default after posting the value of form
							$selected = "selected";
						}elseif(is_array($default_val)){ // selected values as default values
							foreach($default_val as $defvalue)
							{
								// check if mentioned in laguage file
									if(defined($defvalue)) $defvalue = constant($defvalue);									
								// end			
								if(trim($defvalue) == trim($value))
									$selected = "selected";			
							}
						}
							
						$options .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';						
					}			
						
					}else{
						foreach ($sel_values as $skey => $svalue)
						{
							$temp_sel_values[$svalue] = $svalue;
						}
						// end
					$sel_values = $temp_sel_values;														

					if($row->validation_rule == "mandatory"){						
						$jsvalidation = "class=validate['required']";
						$options .= '<option value="">'.JText::_('REGPRO_SELECT_ONE').'</option>';
					}else{
						$jsvalidation = '';
					}
					foreach ($sel_values as $value => $key){
					
						// check if mentioned in laguage file
							if(defined($value)) $value = constant($value);									
						// end
											
						$selected = "";												
						if($_POST && $chkPostVal == $value){ // selected default after posting the value of form
							$selected = "selected";
						}elseif(is_array($default_val)){ // selected values as default values
							foreach($default_val as $defvalue)
							{
								// check if mentioned in laguage file
									if(defined($defvalue)) $defvalue = constant($defvalue);									
								// end			
								if(trim($defvalue) == trim($value))
									$selected = "selected";			
							}
						}
							
						$options .= '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';						
					}			
					
					}
					
										
					// add conditional field script
					if(is_array($conditional_fields) && count($conditional_fields) > 0) {
						$get_keys = array_keys($conditional_fields);
						if(in_array($row->name,$get_keys)) {
							//$jsfunction = "onchange='return '";
							$element_id 	= $row->name.'['.$count.']'.$position_html;
							
							$js_script = 'window.addEvent("domready", function(){
								$("'.$element_id.'").addEvent("change", function(){
								
									var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';									
									
									for (var i=0;i < arr_cond_fields.length; i++)
									{										
										var showfieldrow= arr_cond_fields[i]["name"]+"'.$count.'"+"-rows";
										var showfield 	= arr_cond_fields[i]["name"]+"'.'['.$count.']'.$position_html.'";																				
																			
										var obj = arr_cond_fields[i]["conditional_field_values"];	
																									
										if(obj.contains($("'.$element_id.'").value)) {
											$(showfieldrow).setStyle("display","");
											$(showfield).set("disabled", false);
										}else{
											$(showfield).value = "";
											$(showfield).set("disabled", true);
											$(showfieldrow).setStyle("display","none");
										}										
									}																										
								});																																								
							});';
							
							$doc->addScriptDeclaration($js_script);
						}
					}															
					// end																	
																							
					$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" '.$disable_field.'>'.$options.'</select>';

				break;
				
				case 'multiselect':
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){																									
										 $chkPostVal = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end

					$options = '';			

					$sel_values  = explode(',',$row->values);
					$default_val = explode(',',$row->default_value);	
					
					//echo "<pre>"; print_r($sel_values);
					//echo "<pre>"; print_r($default_val); exit;

					foreach ($sel_values as $value){												
						
						// check if mentioned in laguage file
							if(defined($value)) $value = constant($value);
							if(defined($default_value)) $default_value = constant($default_value);			
						// end
						
						$selected = "";
												
						if($_POST && $chkPostVal == $value){ // selected default after posting the value of form
							$selected = "selected";
						}elseif(is_array($default_val)){ // selected values as default values
							foreach($default_val as $defvalue)
							{			
								// check if mentioned in laguage file
									if(defined($defvalue)) $defvalue = constant($defvalue);									
								// end					
								if(trim($defvalue) == trim($value))
									$selected = "selected";			
							}
						}
							
						$options .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';						
					}

					if($row->validation_rule == "mandatory"){
						$jsvalidation = "class=validate['required']";
					}else{
						$jsvalidation = '';
					}
																							
					$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.'][]'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" multiple>'.$options.'</select>';

				break;
				
				case 'calendar':
				
					// check if mentioned in laguage file
							if(defined($row->default_value)) $row->default_value = constant($row->default_value);			
					// end
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){								
										$row->default_value = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end

					if($row->validation_rule == "mandatory"){
						$jsvalidation = "validate['required']";
					}else{
						$jsvalidation = 'inputbox';
					}			
					
					
					/*echo JHTML::_('calendar'
							  , $row->default_value
							  , $row->name.'['.$count.']'.$position_html
							  , 'form['.$row->name.']['.$count.']'.$position_html
							  , '%Y-%m-%d'
							  , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));*/

					
					//$button_js =  'showCalendar(\''.$row->name.'['.$count.']'.$position_html.'\', \'%Y-%m-%d\');';
					
					if(!empty($row->params))
						parse_str( $row->params, $parameters ); 
					else
						 $parameters = array();
					
					if(count($parameters) > 0){
						foreach($parameters as $k=>$parameter){
							$parameters[$k] = addslashes(trim(trim($parameter,'"'),"'"));
						}
					}
					
					$parameters = array_merge( array('class'=>$jsvalidation, 'size'=>'25',  'maxlength'=>'19', 'style'=>'vertical-align:top','readonly'=>'true'), $parameters );
					
					if(!empty($row->params))
						parse_str( $row->params, $parameters ); 
					else
						 $parameters = array();
					
					if(count($parameters) > 0){
						foreach($parameters as $k=>$parameter){
							$parameters[$k] = addslashes(trim(trim($parameter,'"'),"'"));
						}
					}
					
					$parameters = array_merge( array('class'=>$jsvalidation, 'size'=>'25',  'maxlength'=>'19', 'style'=>'vertical-align:top','readonly'=>'true'), $parameters );
					
					$html .='<td class="regpro_vtop_aleft">'.
							JHTML::_('calendar'
							  , $row->default_value
							  , 'form['.$row->name.']['.$count.']'.$position_html
							  , $row->name.'['.$count.']'.$position_html
							  , '%Y-%m-%d'
							  , $parameters)
										
							.'<input type="hidden" name="regpro_cal_field" value="form['.$row->name.']['.$count.']'.$position_html.'" />';

				
				break;
				
				case 'file':
				
					// automatic fill values if return back to registrtion page if occur any error			
						if($_POST){
							if(is_array($_POST['form'])){
								foreach($_POST['form'] as $postkey=>$postvalue)
								{
									if($postkey == $row->name){								
										$row->default_value = $_POST['form'][$postkey][$count][$payment_id];
									}
								}
							}
						}
					// end

					if($row->validation_rule == "mandatory"){
						//$jsvalidation = 'alt="file|'.REGPRO_FORM_INVALID_EXTENSIONS.'" emsg="Please upload valid '.$row->title.' ('.REGPRO_FORM_INVALID_EXTENSIONS.')"';
						$jsvalidation = "class=validate['required']";
					}else{
						$jsvalidation = '';
					}					
					
					$html .='<td class="regpro_vtop_aleft"><input type="file" '.$jsvalidation.' id="'.$row->name.'['.$count.']'.$position_html.'" name="form['.$row->name.']['.$count.']'.$position_html.'"/>';
				
				break;
				
				case 'country':
									
					$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
					
					// add conditional field script
					if(is_array($conditional_fields) && count($conditional_fields) > 0) {
						$get_keys = array_keys($conditional_fields);
						if(in_array($row->name,$get_keys)) {
							//$jsfunction = "onchange='return '";
							$element_id 	= $row->name.'['.$count.']'.$position_html;
							
							$js_script = 'window.addEvent("domready", function(){
								$("'.$element_id.'").addEvent("change", function(){
								
									var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';									
									
									for (var i=0;i < arr_cond_fields.length; i++)
									{										
										var showfieldrow= arr_cond_fields[i]["name"]+"'.$count.'"+"-rows";
										var showfield 	= arr_cond_fields[i]["name"]+"'.'['.$count.']'.$position_html.'";																				
																			
										var obj = arr_cond_fields[i]["conditional_field_values"];	
																									
										if(obj.contains($("'.$element_id.'").value)) {
											$(showfieldrow).setStyle("display","");
											$(showfield).set("disabled", false);
										}else{
											$(showfield).value = "";
											$(showfield).set("disabled", true);
											$(showfieldrow).setStyle("display","none");
										}										
									}																										
								});																																								
							});';
							
							$doc->addScriptDeclaration($js_script);
						}
					}															
					// end
					

					$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" '.$disable_field.'>'.$regpro_config['countrylist'].'</select>';													
																			
				break;
				
				case 'state':
				
					$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
					
					// add conditional field script
					if(is_array($conditional_fields) && count($conditional_fields) > 0) {
						$get_keys = array_keys($conditional_fields);
						if(in_array($row->name,$get_keys)) {
							//$jsfunction = "onchange='return '";
							$element_id 	= $row->name.'['.$count.']'.$position_html;
							
							$js_script = 'window.addEvent("domready", function(){
								$("'.$element_id.'").addEvent("change", function(){
								
									var arr_cond_fields = '.json_encode($conditional_fields[$row->name]).';									
									
									for (var i=0;i < arr_cond_fields.length; i++)
									{										
										var showfieldrow= arr_cond_fields[i]["name"]+"'.$count.'"+"-rows";
										var showfield 	= arr_cond_fields[i]["name"]+"'.'['.$count.']'.$position_html.'";																				
																			
										var obj = arr_cond_fields[i]["conditional_field_values"];	
																									
										if(obj.contains($("'.$element_id.'").value)) {
											$(showfieldrow).setStyle("display","");
											$(showfield).set("disabled", false);
										}else{
											$(showfield).value = "";
											$(showfield).set("disabled", true);
											$(showfieldrow).setStyle("display","none");
										}										
									}																										
								});																																								
							});';
							
							$doc->addScriptDeclaration($js_script);
						}
					}															
					// end
					

					$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" '.$disable_field.'>'.$regpro_config['statelist'].'</select>';	
					
				break;	
												
				default:
				
					// check if mentioned in laguage file
							if(defined($row->default_value)) $row->default_value = constant($row->default_value);			
					// end
				
					if($row->validation_rule == "mandatory"){
						$jsvalidation = "class=validate['required']";
					}else if($row->validation_rule == "email"){
						$jsvalidation = "class=validate['required','email']";
					}else if($row->validation_rule == "number"){
						$jsvalidation = "class=validate['required','number']";
					}else{
						$jsvalidation = '';
					}
										
					$html.='<td class="regpro_vtop_aleft"><input type="text" '.$jsvalidation.' name="form['.$row->name.']['.$count.']'.$position_html.'" value="'. $row->default_value.'" id="'.$row->name.'['.$count.']'.$position_html.'" '.$row->params.' />';
				
				
			}

			//$html .= '<td valign="top" width="25%">'.$row->description.'</td>';
			//$html .= '<td valign="top" class="regpro_vtop_aleft">';
			
			/*if(!empty($row->description)){
				$html .= '&nbsp;<span class="editlinktip hasTip" style="vertical-align:middle" title="'.$row->description.'"><img src="'.REGPRO_ADMIN_IMG_PATH.'/info.png" border="0" align="absmiddle"></span>';
			}*/
			
			}
			
			$html .= '</td></tr>';

			echo $html;
	}
										
}

?>