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

class registrationproViewPlugins extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;
		
		$option = JRequest::getCMD('option'); // use this instead of global $option

		//initialise variables
		$user 		= JFactory::getUser();
		$db 		= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$registrationproAdmin = new  registrationproAdmin; $regpro_config 	= $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;		
		
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.plugins.filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.plugins.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.plugins.filter_state', 'filter_state', '*', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.plugins.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
				
		// Get data from the model		
		$rows      	= $this->get( 'Data');
		$pageNav 	= $this->get( 'Pagination' );
		
		foreach($rows as $key => $value)
		{
			// get params definitions
			/*$params = new JParameter($value->params);
			$rows[$key]->params = $params; */
			$pluginParams = new JRegistry;
			$rows[$key]->params = $pluginParams->loadString($value->params);						
		}
				
		//echo "<pre>"; print_r($rows);				
		$lists 				= array();		
		$filters 			= array();		
		$lists['filter'] 	= JHTML::_('select.genericlist',   $filters, 'filter', 'class="inputbox" size="1"','value', 'text', $filter); 	
		
		//publish unpublished filter
		$lists['state']		= JHTML::_('grid.state', $filter_state );
		
		// search filter
		$lists['search']	= $search;
				
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;				

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);		
		$this->assignRef('template'		, $template);
		$this->assignRef('settings'     , $regpro_config);
		
		parent::display($tpl);
	}
}
?>