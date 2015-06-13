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
jimport( 'joomla.filesystem.folder');

class registrationproViewForms extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option, $Itemid;
		
		$uri = JFactory::getURI();
		$registrationproHelper = new registrationproHelper;
		if(!$registrationproHelper->checkUserAccount()) {
			$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid, false);		
			$mainframe->redirect($link);
		}

		//initialise variables
		$user 		=  JFactory::getUser();
		$db 		=  JFactory::getDBO();
		$document	=  JFactory::getDocument();
		$registrationproAdmin =new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;
		
		//add css and js to document
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());			
				
		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.forms.filter_order', 'filter_order', 'id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.forms.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'.forms.filter_state', 'filter_state', '*', 'word' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.forms.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$search 			= $db->escape( trim(JString::strtolower( $search ) ) );
		$template			= $mainframe->getTemplate();
		
		// Get data from the model
		$rows      	=  $this->get( 'Data');
		//echo "<pre>"; print_r($rows); exit;

		$total      =  $this->get( 'Total');
		$pageNav 	= $this->get( 'Pagination' );

		//publish unpublished filter
		$lists['state']	= JHTML::_('grid.state', $filter_state );

		$filters 		= array();
		
		// search filter
		$lists['search'] = $search;

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;

		$ordering = ($lists['order'] == 'id');
				

		//assign data to template
		$this->assign('action', str_replace('&', '&amp;', $uri->toString()));
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('regpro_config', $regpro_config);
		$this->assignRef('Itemid',$Itemid);
		
		parent::display($tpl);
	}
}
?>