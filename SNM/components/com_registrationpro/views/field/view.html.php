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
		global $mainframe, $Itemid;
		$registrationproHelper = new registrationproHelper;
		if(!$registrationproHelper->checkUserAccount()) {
			$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid, false);		
			$mainframe->redirect($link);
		}
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		//initialise variables
		$editor 	=  JFactory::getEditor();
		$user 		=  JFactory::getUser();
		$db 		=  JFactory::getDBO();
		$document	=  JFactory::getDocument();
		$registrationproAdmin =new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$repgro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($repgor_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'id' );
		$task 			= JRequest::getVar( 'task' );
		$formid			= JRequest::getVar( 'form_id',0,'','int' );
		
		$document	=  JFactory::getDocument();
		$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.config.js');
		$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.core.js');
		$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.lang-enUS.js');
		$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.validators.js');
		$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.controls.js');
		$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.datetime.js');
		$document->addScript(JURI::root().'/administrator/components/com_registrationpro/assets/javascript/recurrence.js');
		
		//add css and js to document
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());		
		
		// Get data from the model
		$model	=  $this->getModel('field');
		$row    =  $this->get('Data');
		$row->form_id = $formid;				
				
		//create the toolbar
		/*if($row->id){ 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_EDIT_FIELD');
		} else { 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_ADD_FIELD');
		}
		
		JToolBarHelper::title( '<img src="components/com_registrationpro/assets/images/users_small.png" align="absmiddle" border="0">' .$pagetitle, 'fieldsedit' );
		JToolBarHelper::apply('add_field_apply');
		JToolBarHelper::spacer();
		JToolBarHelper::save('add_field');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancelfield');
		JToolBarHelper::spacer();
		JToolBarHelper::help( 'screen.registrationpro', true );*/

		// Get data from the model
		$model		=  $this->getModel();
		$row      	=  $this->get( 'Data');		
		//echo "<pre>"; print_r($row); exit;

		//$total      = & $this->get( 'Total');
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
		$all_text_fields  	=  $model->getAllfields($row->form_id);
		
		if(count($all_text_fields) > 0){
			$textFields = array_merge( $alltextfields, $all_text_fields); 
			$Lists['all_text_fields']	=  JHTML::_('select.genericlist', $textFields, 'confirm', 'class="inputbox" size="1"','value', 'text', $row->confirm);
		}
		
		// fields group list		
		$groups 		= array();		
		$groups[] 		= JHTML::_('select.option',  '0', JText::_('ADMIN_SELECT_FIELDS_GROUPS'));
		$field_groups 	=  $model->getfields_groups($row->form_id); // get groups list
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
		
		// search filter
		$Lists['search']= $search;

		// table ordering
		$Lists['order_Dir'] = $filter_order_Dir;
		$Lists['order'] = $filter_order;
			//echo $row->published;die();			
		$Lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox" '.$disable, $row->published );

		$ordering = ($lists['order'] == 'id');
		
		JHTML::_('behavior.modal', 'a.modal');
		
		//echo "<pre>"; print_r($Lists); exit;

		//assign data to template
		$this->assignRef('Lists'    , $Lists);
		$this->assignRef('row'      , $row);
		$this->assignRef('pageNav' 	, $pageNav);
		$this->assignRef('user'		, $user);
		$this->assignRef('template'	, $template);
		$this->assignRef('editor'   , $editor);
		$this->assignRef('regpro_config' , $repgro_config);
		$this->assignRef('task' , $task);	
		$this->assignRef('disable' , $disable);	
		$this->assignRef('Itemid' , $Itemid);			
		
		parent::display($tpl);
	}		
}
?>