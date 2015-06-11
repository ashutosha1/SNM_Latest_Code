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

// regpro api class
class regpro_api
{

	function __construct()
	{

	}
	
	function &getInstance(){
		 $instance;
		if(!$instance){
			$instance = new regpro_api();
		}
		return $instance;
	}
	
	
	/**
	 * Used to trigger plugin
	 * @param	string	eventName
	 * @param	array	params to pass to the function
	 * 
	 * returns	Array	An array of object that the caller can then manipulate later.	 	 	 	 
	 **/	 	
	function triggerEvent($event, $arrayParams = null)
	{
		JPluginHelper::importPlugin( 'regpro' );
		
		$mainframe  = JFactory::getApplication();
		$dispatcher = JDispatcher::getInstance();
		$content 	  =  array();

		// Avoid problem with php 5.3
		if(is_null($arrayParams)){
			$arrayParams = array();
		}
		
		switch( $event )
		{
			case 'onEventCreate':
				$content = $dispatcher->trigger($event, array( $arrayParams ));
			break;
		
			case 'onRegistrationAccepted':
				$content = $dispatcher->trigger($event, array( $arrayParams ));
			break;
		}
		//echo "<pre>"; print_r($content); exit;
		return $content;
	}				
}
?>