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

class registrationproViewEmails extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;		
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
	
		JRequest::setVar( 'hidemainmenu', 1 ); //hide menu ???		
	
		//initialise variables
		$acl 		= JFactory::getACL();
		$editor 	= JFactory::getEditor();
		$document	= JFactory::getDocument();
		//$pane		= & JPane::getInstance('sliders');
		$user 		= JFactory::getUser();

		//get vars
		$cid 			= JRequest::getInt( 'cid' );

		//add css and js to document
		$document->addScript('../includes/js/joomla/popup.js');
		$document->addStyleSheet('../includes/js/joomla/popup.css');
		
		// Get data from the model
		$registrationproAdmin = new registrationproAdmin;
		$row 	= $registrationproAdmin->config();
		
		//echo "<pre>";  print_r($row); exit;
				
		//create the toolbar		
		JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_EDIT_EMAILS' ), 'settingsedit' );
		JToolBarHelper::spacer();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::save();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		//JToolBarHelper::spacer();
		//JToolBarHelper::help( 'screen.registrationpro', true );
												
		JHTML::_('behavior.modal', 'a.modal');

		//assign data to template		
		$this->assignRef('row'      	, $row);
		//$this->assignRef('pane'      	, $pane);
		$this->assignRef('editor'      	, $editor);	

		parent::display($tpl);
	}
}
?>