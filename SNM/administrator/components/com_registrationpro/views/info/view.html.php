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

class registrationproViewInfo extends JViewLegacy
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
		$registrationproAdmin = new registrationproAdmin;
		$repgro_config 	=  $registrationproAdmin->config();
		$repgro_config['joomlabase'] = JPATH_SITE;

		//echo "<pre>"; print_r($repgor_config); exit;

		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );

		//create the toolbar
		JToolBarHelper::title( JText::_('LICENSE INFO'),'');
		//JToolBarHelper::help( 'screen.registrationpro', true );

		// Get data from the model
		JHTML::_('behavior.modal', 'a.modal');

		//assign data to template
		$this->assignRef('row'      	, $row);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('template'		, $template);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('repgro_config', $repgro_config);
		$this->assignRef('task' 		, $task);

		parent::display($tpl);
	}
}
?>