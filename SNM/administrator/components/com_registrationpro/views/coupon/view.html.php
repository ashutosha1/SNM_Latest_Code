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

class registrationproViewCoupon extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;
				
		//initialise variables
		$editor 	= JFactory::getEditor();
		$user 		= JFactory::getUser();
		$db 		= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;		
		$regpro_config	= $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
				
		//echo "<pre>"; print_r($regpro_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();		
		
		// Get data from the model
		$model	= $this->getModel('coupon');
		$row    = $this->get('Data');		
						
		// Get data from the model
		$model		= $this->getModel('coupon');
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
		$Lists['order'] 	= $filter_order;
		
		//echo $row->published; exit;
				
		$Lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );
		
		// event lists
		$events		= array();
		//$events[] 	= JHTML::_('select.option', '0', JText::_('EVENT_ADMIN_COUPONS_SELECT_ONE_EVENT'));
		$all_events	= $this->get( 'events' );
		$events 	= array_merge( $events, $all_events);
		$Lists['events'] =  JHTML::_('select.genericlist', $events, 'eventids[]', 'class="inputbox" multiple','value', 'text', explode(",",$row->eventids));

		$ordering = ($lists['order'] == 'id');
		
		JHTML::_('behavior.modal', 'a.modal');

		//assign data to template
		$this->assignRef('Lists'      	, $Lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('regpro_config' , $regpro_config);
		$this->assignRef('task' , $task);		
		
		parent::display($tpl);
	}
}
?>