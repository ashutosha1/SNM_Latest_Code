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

jimport('joomla.application.component.controller');

class registrationproController extends JControllerLegacy
{
	/*** Custom Constructor */
	function __construct()
	{
		parent::__construct();				
	}
	
	function display($cachable = false, $urlparams = false)
	{	
		global $mainframe;
						
		$document = JFactory::getDocument();
		$view	= JRequest::getVar( 'view', '', '', 'string', JREQUEST_ALLOWRAW );
		
		// get component config setting
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();
		$registrationproHelper = new registrationproHelper;						
		// if user registration required
		if($regpro_config['require_registration']){																
			$registrationproHelper->check_user_login();
		}
		
		// clean past events
		$registrationproHelper->clean_events();
		
		// send reminder emails
		if($regpro_config['disable_remiders'] == 0) {
			$registrationproHelper->reminder();
		}
								
		parent::display();
	}		
}	
?>