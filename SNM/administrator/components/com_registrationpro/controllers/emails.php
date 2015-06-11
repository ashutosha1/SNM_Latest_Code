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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class registrationproControllerEmails extends registrationproController {

	function __construct()
	{
		parent::__construct();
		
			$this->registerTask( 'add', 'edit' );
			$this->registerTask( 'apply', 'save' );
			
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );				
		$this->setRedirect( 'index.php?option=com_registrationpro' );
	}


	function edit( )
	{	
		JRequest::setVar( 'view', 'emails' );
		JRequest::setVar( 'hidemainmenu', 1 );
								
		parent::display();
	}
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$task	= JRequest::getVar('task');

		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		
		//echo "<pre>";  print_r($post); exit;				
		
		$db =JFactory::getDBO();
		
		$flag = 1;	
		foreach($post as $key=>$value){		
      		$db->setQuery("UPDATE #__registrationpro_config SET setting_value='".addslashes($value)."' WHERE setting_name='".$key."'");
		  	if (!$db->query()) {
				$flag = 0;
		    	echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
		    	exit();
		  	}
      	}	
				
		if ($flag) {
			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option=com_registrationpro&controller=settings&view=emails&hidemainmenu=1&cid[]='.$returnid;
					break;

				default :
					$link = 'index.php?option=com_registrationpro';
					break;
			}
			$msg	= JText::_( 'ADMIN_EMAIL_SETTING_SAVE');
			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		} else {
			$msg 	= '';
			$link = 'index.php?option=com_registrationpro&view=emails';
		}

		$this->setRedirect( $link, $msg );
 	}
		
}
?>
