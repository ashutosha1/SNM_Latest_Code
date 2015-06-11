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

class registrationproViewLocation extends JViewLegacy
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
		$repgro_config 	=  $registrationproAdmin->config();
		$repgro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($repgor_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();
		$http_scheme = "http";
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
		    $http_scheme = "https";
		}
		$document->addScript($http_scheme.'://maps.google.com/maps/api/js?sensor=false&libraries=places&language=en-AU');		
		
		// Get data from the model
		$model	= $this->getModel('location');
		$row    = $this->get('Data');		
				
		//create the toolbar
		if($row->id){ 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_EDIT_LOCATION');
		} else { 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_ADD_LOCATION');
		}
		JToolBarHelper::title( $pagetitle, 'locationsedit' );
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		//JToolBarHelper::spacer();
		//JToolBarHelper::help( 'screen.registrationpro', true );

		// Get data from the model
		$model		= $this->getModel('location');
		$row      	= $this->get( 'Data');
		//echo "<pre>"; print_r($row); exit;

		//$total      = $this->get( 'Total');
		$pageNav 	= $this->get( 'Pagination' );
				
		//publish unpublished filter
		$Lists = array();
		$Lists['state']	= JHTML::_('grid.state', $filter_state );
		
		// Access List
		//$Lists['access']	= JHTML::_('list.accesslevel',$row);
		$Lists['access']	= JHtml::_('access.assetgrouplist','access',$row->access);
								
		// search filter
		$Lists['search']= $search;

		// table ordering
		$Lists['order_Dir'] = $filter_order_Dir;
		$Lists['order'] = $filter_order;
		
		$published = ($row->id) ? $row->publishedloc : 1;
		$Lists['published'] = JHTML::_('select.booleanlist',  'publishedloc', 'class="inputbox"', $published );

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
		$this->assignRef('task' , $task);		
		
		parent::display($tpl);
	}
}
?>