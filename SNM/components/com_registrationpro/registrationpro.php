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

	defined('_JEXEC') or die('Restricted access');

	define('DS','/');
	error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
	global $option, $mainframe, $Itemid,$regpro_mail;
			
	$option 	= "com_registrationpro";
	$mainframe 	= JFactory::getApplication();
	$Itemid 	= JRequest::getInt('Itemid');
	$regpro_mail = new JMail;

	$jlang =JFactory::getLanguage();
	$jlang->load('com_registrationpro', JPATH_SITE, 'en-GB', true);
	$jlang->load('com_registrationpro', JPATH_SITE, $jlang->getDefault(), true);
	$jlang->load('com_registrationpro', JPATH_SITE, null, true);

	require_once (JPATH_COMPONENT.DS.'controller.php');
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'registrationpro_constant.php');
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'helper.php');
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'admin.class.php');
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'plugins.regpro.class.php'); 	// add plugins class file
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'calendar.class.php'); 			// add calender class file
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'form.class.php'); 				// add forms and fields class file
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'registration.class.php'); 		// save event registration class file
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'regpro_rss.class.php');		// Rss feed class file
	require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'regpro_api.class.php');		// api class
	require_once (JPATH_COMPONENT.DS.'registrationpro.html.php'); 								// add header footer class file

	// Set the helper directory
	JHTML::addIncludePath( JPATH_COMPONENT.DS.'helpers' );

	// Require specific controller if requested
	if($controller = JRequest::getWord('controller')) {
		$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
		if (file_exists($path)) {
			require_once $path;
		} else $controller = '';
	}

	// Create the controller
	$classname    = 'registrationproController'.$controller;
	$controller   = new $classname();

	// Perform the Request task
	$controller->execute(JRequest::getVar('task'));
	$controller->redirect();
?>