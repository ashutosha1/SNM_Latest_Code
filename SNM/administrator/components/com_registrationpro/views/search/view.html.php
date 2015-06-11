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

class registrationproViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		$option = "com_registrationpro";
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
						
		//initialise variables
		$db				=JFactory::getDBO();
		$editor 		=JFactory::getEditor();
		$user 			=JFactory::getUser();
		$document		=JFactory::getDocument();
		$registrationproAdmin = new  registrationproAdmin; $regpro_config 	= $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;		
		//echo "<pre>"; print_r($repgor_config); exit;
		
		//echo "<pre>"; print_r($_POST); exit;
				
		// get filter vlaues
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.search_filter_order', 'filter_order', '', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.search_filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		//$search 			= $db->escape( trim(JString::strtolower( $search ) ) );	
		$event	 			= $mainframe->getUserStateFromRequest( $option.'.search_event', 'event', '', 'int' );	
		$start_date 		= $mainframe->getUserStateFromRequest( $option.'.search_start_date', 'start_date', '', 'string' );
		$end_date 			= $mainframe->getUserStateFromRequest( $option.'.search_end_date', 'end_date', '', 'string' );
		$firstname 			= $mainframe->getUserStateFromRequest( $option.'.search_firstname', 'firstname', '', 'string' );
		//$firstname 			= $db->escape( trim(JString::strtolower( $firstname ) ) );
		$lastname 			= $mainframe->getUserStateFromRequest( $option.'.search_lastname', 'lastname', '', 'string' );
		//$lastname 			= $db->escape( trim(JString::strtolower( $lastname ) ) );
		$email	 			= $mainframe->getUserStateFromRequest( $option.'.search_email', 'email', '', 'string' );
		//$email 				= $db->escape( trim(JString::strtolower( $email ) ) );
		
		//echo "hello"; exit;
				
		$data 				= array ();
		$data['search'] 	= $search;
		$data['firstname']	= $firstname;
		$data['lastname'] 	= $lastname;
		$data['email'] 		= $email;
		$data['event'] 		= $event;
		$data['start_date'] = $start_date;
		$data['end_date'] 	= $end_date;
		
		$reset				= JRequest::getVar('reset','POST');
				
		if($reset == 1){			
			$location	 	= $mainframe->getUserStateFromRequest( $option.'.search_location', 'l', '', 'array' );
			$category	 	= $mainframe->getUserStateFromRequest( $option.'.search_category','c', '', 'array' );
			//echo "<pre>"; print_r($location);
		}else{	
			$location	 	= $mainframe->getUserStateFromRequest( $option.'.search_location', 'location', '', 'array' );
			$category	 	= $mainframe->getUserStateFromRequest( $option.'.search_category', 'category', '', 'array' );
		}				
		// end							
		
		//echo "<pre>"; print_r($location); echo "<pre>"; print_r($category); exit;
		
		//get vars
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();		
		
		// Get data from the model
		/*$model	= $this->getModel('search');
		$rows    = $this->get('Data');*/
		
		
		$layout = JRequest::getCmd('layout');
		
		if($layout == 'print_report'){														
			// Get data from the model
			$model	= $this->getModel('search');
			$model->setState('limit', 0);
			$model->setState('limitstart', 0);
			$rows    = $this->get('Data');
		
			$this->print_report($rows); // show print report window
			
		}else{
						
			// Get data from the model
			$model	= $this->getModel('search');
			$rows    = $this->get('Data');
												
			//echo "<pre>"; print_r($row); exit;
	
			//$total      = $this->get( 'Total');
			$pageNav 	= $this->get( 'Pagination' );
						
			$Lists = array();
			$Lists['events']	= JHTML::_('grid.state', $filter_state );
			
			// Locations list
			$events 		= array();	
			$events[] 		= JHTML::_('select.option', '', JText::_(ADMIN_SEARCH_EVENT_SELECT_ONE));
			$all_events 	= $this->get( 'Events' );
			$events 		= array_merge( $events, $all_events);
			$Lists['events']	=  JHTML::_('select.genericlist', $events, 'event', 'class="inputbox" size="1" ','value', 'text', $event);
			
			// Locations list
			$locations 		= array();
			//$locations[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_SEARCH_LOCATION_SELECT_ONE));
			$all_locations 	= $this->get( 'Locations' );
			$locations 		= array_merge( $locations, $all_locations);
			$Lists['locations']	=  JHTML::_('select.genericlist', $locations, 'location[]', 'class="inputbox" multiple','value', 'text', $location );
		
			// Categories Lists
			$categories 	= array();
			//$categories[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_SEARCH_CATEGORY_SELECT_ONE));
			$all_categories	= $this->get( 'Categories' );
			$categories 	= array_merge( $categories, $all_categories);
			$Lists['categories'] = JHTML::_('select.genericlist', $categories, 'category[]', 'class="inputbox" multiple','value', 'text', $category );
														
			if (!$filter_order) {
				$filter_order = 'a.dates';
			}
	
			// table ordering
			$Lists['order_Dir'] = $filter_order_Dir;
			$Lists['order'] = $filter_order;
										
			//echo "<pre>"; print_r($Lists); exit;
	
			//assign data to template
			$this->assignRef('data'      	, $data);
			$this->assignRef('Lists'      	, $Lists);
			$this->assignRef('rows'      	, $rows);
			$this->assignRef('pageNav' 		, $pageNav);
			$this->assignRef('user'			, $user);
			$this->assignRef('template'		, $template);
			$this->assignRef('editor'      	, $editor);
			$this->assignRef('regpro_config' , $regpro_config);
			$this->assignRef('task' 		, $task);		
			
			parent::display($tpl);
		}
	}
	
	function print_report($rows)
	{
		global $mainframe;
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	=  $registrationproAdmin->config();
		
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('rows' , $rows);
		
		parent::display($tpl);
	}
}
?>