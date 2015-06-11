<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL		
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

class registrationproViewForm extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		//initialise variables
		$editor 	= JFactory::getEditor();
		$user 		= JFactory::getUser();
		$db 		= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$repgro_config = $registrationproAdmin->config();;
		$repgro_config['joomlabase'] = JPATH_SITE;
		JRequest::setVar('limit',0);		
		//echo "<pre>"; print_r($repgor_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();		
								
		// Get data from the model
		$model	=  $this->getModel('form');
		$row    =  $this->get('Data');
		$row->fields 	=  $model->getFields($row->id);
		$row->cb_fields = array();
		$row->jomsocial_fields = array();
		$row->profile_fields = array();
		if($row->id == ""){ 
			$row->id = 0;
		}
		// check CB existance
		//$row->cb_fields =$model->getCBFields($row->id); $row->jomsocial_fields =$model->getJoomsocialFields($row->id); exit;
		if($repgro_config['cbintegration'] == 1 ) {
			if($registrationproHelper->chkCB()){
				$row->cb_fields = $model->getCBFields($row->id);
			}
		}elseif($repgro_config['cbintegration'] == 2){
			if($registrationproHelper->chkJoomsocial()){		
				$row->jomsocial_fields = $model->getJoomsocialFields($row->id);
			}
		}elseif($repgro_config['cbintegration'] == 3) {
			// Check Core joomla profiles plugins fields data
			if($registrationproHelper->chkCoreProfiles()){																																																															
				$row->profile_fields = $model->getCorefields($row->id);
			}
		}		
		
		//echo "<pre>"; print_r($row); exit;			
				
		//create the toolbar
		if($row->id){ 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_EDIT_FORM');
		} else { 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_ADD_FORM');
		}
		JToolBarHelper::title( $pagetitle, 'formsedit' );
		
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();	
		JToolBarHelper::addNew('edit_field','Add Field');
		JToolBarHelper::spacer();
		JToolBarHelper::editList('edit_field', 'Edit Field');
		JToolBarHelper::spacer();
		JToolBarHelper::publishList('publishfield', 'Publish Field');
		JToolBarHelper::spacer();
		JToolBarHelper::unpublishList('unpublishfield', 'Unpublish Field');
		JToolBarHelper::spacer();		
		JToolBarHelper::deleteList('Selected records data will be lost and cannot be undone!', 'remove_field', 'Remove Field' );JToolBarHelper::spacer();	
		//JToolBarHelper::help( 'screen.registrationpro', true );
		JToolBarHelper::spacer();		
		JToolBarHelper::cancel();
		// Get data from the model
		$model		=  $this->getModel();
		$row      	=  $this->get( 'Data');
		//echo "<pre>"; print_r($row); exit;
		$fields		= $model->getFieldsNew($row->id,true);
		$pagination	= $model->getPagination($row->id);
		// Create the pagination object
		//$fieldtotal = $model->getFieldTotal($row->id);
		//$pageNav 	= $model->getPagination($row->id);
			
		//publish unpublished filter
		$Lists = array();
		$Lists['state']	= JHTML::_('grid.state', $filter_state );
		
		// Access List
		//$Lists['access']	= JHTML::_('access.level',$row);
		$Lists['access']	= JHtml::_('access.assetgrouplist','access',$row->access);

		// search filter
		$Lists['search']= $search;

		// table ordering
		$Lists['order_Dir'] = $filter_order_Dir;
		$Lists['order'] = $filter_order;

		$ordering = ($lists['order'] == 'id');
		
		JHTML::_('behavior.modal', 'a.modal');

		//assign data to template
		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('repgro_config' , $repgro_config);
		$this->assignRef('task' 		, $task);	
		$this->assignRef( 'fields' 		, $fields );
		$this->assignRef( 'pagination'	, $pagination );	
		
		parent::display($tpl);
	}
	
	function field_group_name($groupid)
	{
		// Get data from the model
		$model		=  $this->getModel('form');
		$group_name =  $model->getGroupName($groupid);
		
		return $group_name;
	}
}
?>