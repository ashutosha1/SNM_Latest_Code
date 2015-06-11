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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class registrationproViewField extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		JHTML::_('behavior.modal', 'a.modal');
		
		$layout = JRequest::getCmd('layout');
		
		if($layout == 'field') {				
			$this->fieldValues(); // show field values for conditional field feature	
		}else{

			//initialise variables
			$editor 	= JFactory::getEditor();
			$user 		= JFactory::getUser();
			$db 		= JFactory::getDBO();
			$document	= JFactory::getDocument();
			$registrationproAdmin = new registrationproAdmin;
			$repgro_config['joomlabase'] = JPATH_SITE;
					
			//echo "<pre>"; print_r($repgor_config); exit;
			
			//get vars
			$cid 			= JRequest::getInt( 'cid' );
			$task 			= JRequest::getVar( 'task' );
			$formid			= JRequest::getVar( 'form_id' );
			
			//add css and js to document
			$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();		
			
			// Get data from the model
			$model	=  $this->getModel('field');
			$row    =  $this->get('Data');
			$row->form_id = $formid;				
					
			//create the toolbar
			if($row->id){ 
				$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_EDIT_FIELD');
			} else { 
				$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_ADD_FIELD');
			}
						
			JToolBarHelper::title( $pagetitle, 'rforms.png' );
			JToolBarHelper::apply('add_field_apply');
			JToolBarHelper::spacer();
			JToolBarHelper::save('add_field');
			JToolBarHelper::spacer();
			JToolBarHelper::cancel('cancelfield');
			//JToolBarHelper::spacer();
			//JToolBarHelper::help( 'screen.registrationpro', true );
	
			// Get data from the model
			$model		=  $this->getModel();
			$row      	=  $this->get( 'Data');		
			//echo "<pre>"; print_r($row); exit;
	
			//$total      = $this->get( 'Total');
			$pageNav 	=  $this->get( 'Pagination' );
					
			//publish unpublished filter
			$Lists = array();
			$Lists['state']	= JHTML::_('grid.state', $filter_state );
							
			// Fields type List
			if($row->name == 'firstname' || $row->name == 'lastname' || $row->name == 'email'){
				$disable = "disabled";	
			}else{
				$disable = "";
			}
			
			// fieldtype list
			$field_type = array();		
			$field_type[] 	= JHTML::_('select.option', 'text', 'text');		
			$field_type[] 	= JHTML::_('select.option', 'password','password');
			$field_type[] 	= JHTML::_('select.option', 'textarea', 'textarea');
			$field_type[] 	= JHTML::_('select.option', 'select', 'select');
			$field_type[] 	= JHTML::_('select.option', 'multiselect', 'multiselect');
			$field_type[] 	= JHTML::_('select.option', 'radio', 'radio');
			$field_type[] 	= JHTML::_('select.option', 'checkbox', 'checkbox');
			$field_type[] 	= JHTML::_('select.option', 'multicheckbox', 'multicheckbox');
			$field_type[] 	= JHTML::_('select.option', 'file', 'file');
			$field_type[] 	= JHTML::_('select.option', 'calendar', 'calendar');
			$field_type[] 	= JHTML::_('select.option', 'country', 'country');	
			$field_type[] 	= JHTML::_('select.option', 'state', 'state');		
			$field_type[] 	= JHTML::_('select.option', 'groups', 'groups');
			$Lists['field_type'] = JHTML::_('select.genericlist', $field_type, 'inputtype', 'class="inputbox" size="1" onchange="return enable_field_rows(this.value);"'.$disable, 'value', 'text', $row->inputtype );
			
			// field validations list
			$field_validation = array();
			$field_validation[] = JHTML::_('select.option',  '0', 'none', 'id','title' ); 
			$field_validation[] = JHTML::_('select.option',  'email', 'email', 'id','title' ); 
			$field_validation[] = JHTML::_('select.option',  'number', 'number', 'id','title' );  
			$field_validation[] = JHTML::_('select.option',  'mandatory', 'mandatory', 'id','title' ); 
			$field_validation[] = JHTML::_('select.option',  'confirm', 'confirm', 'id','title' );
			$Lists['field_validations']= JHTML::_('select.genericlist', $field_validation, 'validation_rule', 'class="inputbox" size="1" onchange="return enable_confirm_field(this.value);" '.$disable,'id', 'title', $row->validation_rule);
							
			// All text fields for confirm validation
			$alltextfields 	= array();		
			$alltextfields[]= JHTML::_('select.option',  '', JText::_('ADMIN_SELECT_CONFIRM_FIELD'));
			$all_text_fields  	= $model->getAllfields($row->form_id);
			
			if(count($all_text_fields) > 0){
				$textFields = array_merge( $alltextfields, $all_text_fields); 
				$Lists['all_text_fields']	=  JHTML::_('select.genericlist', $textFields, 'confirm', 'class="inputbox" size="1"','value', 'text', $row->confirm);
			}
			
			// fields group list		
			$groups 		= array();		
			$groups[] 		= JHTML::_('select.option',  '0', JText::_('ADMIN_SELECT_FIELDS_GROUPS'));
			$field_groups 	= $model->getfields_groups($row->form_id); // get groups list
			//echo "<pre>"; print_r($field_groups); exit;				
			if(count($field_groups) > 0){
				$groups 		= array_merge( $groups, $field_groups);
				$Lists['field_groups']	=  JHTML::_('select.genericlist', $groups, 'groupid', 'class="inputbox" size="1"','value', 'text', $row->groupid );
			}
			
			// field display type
			$field_display_type = array();
			$field_display_type[] = JHTML::_('select.option',  '1', 'Horizontally ', 'id','title' );
			$field_display_type[] = JHTML::_('select.option',  '2', 'Vertically', 'id','title' );
			$Lists['field_display_type'] = JHTML::_('select.genericlist', $field_display_type, 'display_type', 'class="inputbox" size="1"','id', 'title', $row->display_type);
			
			
			// List checkbox,radio and selectbox fields for conditional fields feature
			$conditional_field 	= array();
			$conditional_field[] 	= JHTML::_('select.option',  '', JText::_('ADMIN_SELECT_CONDITIONAL_FIELDS'));
			$conditional_fields 	= $model->getconditionalfields($row->form_id); // get groups list	
			//if(count($conditional_fields) > 0){
				$conditional_fields 	= array_merge( $conditional_field, $conditional_fields);
				$Lists['conditional_fields'] =  JHTML::_('select.genericlist', $conditional_fields, 'conditional_field', 'class="inputbox" size="1"','value', 'text', $row->conditional_field );
			//}
			
			// Get conditional field values
			if(trim($row->conditional_field) != "" && trim($row->conditional_field_values) != "") {
				$conditional_field_values = $model->getFieldValues($row->form_id, $row->conditional_field);
				
				$arrdata			= array();
				$conditional_field_data	= array();
				
				if($conditional_field_values['inputtype'] == "state") {
					$arrdata = explode(",",REGPRO_STATES);
					foreach($arrdata as $key=>$value) {
						$conditional_field_data[trim($value)] = trim($value);				
					}
				}elseif($conditional_field_values['inputtype'] == "country"){
					$arrdata = explode(",",REGPRO_COUNTRIES);
					foreach($arrdata as $key=>$value) {
						$conditional_field_data[trim($value)] = trim($value);				
					}
				}else{
					$arrdata = explode(",",$conditional_field_values['values']);
					foreach($arrdata as $key=>$value) {
						$conditional_field_data[trim($value)] = trim($value);				
					}
				}	
			}
			// End	
			
			// fees type
			$fees_field_type = array();
			$fees_field_type[] = JHTML::_('select.option',  'A', 'Amount', 'id','title' );
			$fees_field_type[] = JHTML::_('select.option',  'P', 'Percentage %', 'id','title' );
			$Lists['fees_field_type'] = JHTML::_('select.genericlist', $fees_field_type, 'fees_type', 'class="inputbox" size="1"','id', 'title', $row->fees_type);					
			
			// search filter
			$Lists['search']= $search;
	
			// table ordering
			$Lists['order_Dir'] = $filter_order_Dir;
			$Lists['order'] = $filter_order;
							
			$Lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox" '.$disable, $row->published );
	
			$ordering = ($lists['order'] == 'id');
									
			//echo "<pre>"; print_r($Lists); exit;
	
			//assign data to template
			$this->assignRef('Lists'    , $Lists);
			$this->assignRef('row'      , $row);
			$this->assignRef('conditional_field_data' , $conditional_field_data);
			$this->assignRef('pageNav' 	, $pageNav);
			$this->assignRef('user'		, $user);
			$this->assignRef('template'	, $template);
			$this->assignRef('editor'   , $editor);
			$this->assignRef('repgro_config' , $repgro_config);
			$this->assignRef('task' , $task);	
			$this->assignRef('disable' , $disable);			
			
			parent::display($tpl);
		}
	}
	
	function fieldValues()
	{
		//initialise variables
		$editor 	= JFactory::getEditor();
		$user 		= JFactory::getUser();	
		$registrationproAdmin = new registrationproAdmin;				
		//echo "<pre>"; print_r($repgor_config); exit;
		
		//get vars
		$form_id 	= JRequest::getInt( 'formid');
		$field 		= JRequest::getVar( 'field');
		
		// Get data from the model
		$model		=  $this->getModel();
		$row      	= $model->getFieldValues($form_id, $field);		
		//echo "<pre>"; print_r($row); exit;
				
		$this->assignRef('row', $row);
		
		parent::display($tpl);				
	}
			
}
?>