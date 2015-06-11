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

class registrationproViewCategory extends JViewLegacy
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
		$repgor_config 	= $registrationproAdmin->config();
		$repgro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($repgor_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_scripts();		
		
		// Get data from the model
		$model	=  $this->getModel('category');
		$row    =  $this->get('Data');		
				
		//create the toolbar
		if($row->id){ 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_EDIT_CATEGORIES');
		} else { 
			$pagetitle = JText::_('ADMIN_LBL_CONTROPANEL_ADD_CATEGORIES');
		}		
		
		JToolBarHelper::title( $pagetitle, 'rcategories.png');
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		//JToolBarHelper::spacer();
		//JToolBarHelper::help( 'screen.registrationpro', true );

		// Get data from the model
		$model		=  $this->getModel('category');
		$row      	=  $this->get( 'Data');
		//echo "<pre>"; print_r($row); exit;

		//$total      = $this->get( 'Total');
		$pageNav 	=  $this->get( 'Pagination' );

		
		$Lists = array();
		
		// All category lists		
		$categories 		= array();
		$categories[] 		= JHTML::_('select.option',  '0', JText::_('ADMIN_CATEGORIES_SELECT_ONE'));
		$allcategories 		=  $this->get('AllCategories');
		$categories 		= array_merge( $categories, $allcategories);
		$Lists['allcategories']	=  JHTML::_('select.genericlist', $categories, 'parentid', 'class="inputbox" size="1"','value', 'text', $row->parentid);
									
		//publish unpublished filter									
		$Lists['state']	= JHTML::_('grid.state', $filter_state );
		
		// Access List
		//$Lists['access']	= JHTML::_('list.accesslevel',$row);
		$Lists['access'] = JHTML::_('access.assetgrouplist', 'access',$row->access);
								
		// search filter
		$Lists['search']= $search;

		// table ordering
		$Lists['order_Dir'] = $filter_order_Dir;
		$Lists['order'] = $filter_order;
		
		$published = ($row->id) ? $row->publishedcat : 1;
		$Lists['published'] = JHTML::_('select.booleanlist',  'publishedcat', 'class="inputbox"', $published );

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