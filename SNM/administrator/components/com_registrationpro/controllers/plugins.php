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

class registrationproControllerPlugins extends registrationproController {

	function __construct()
	{
		parent::__construct();		
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );			
	}
	
	function publish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = $this->getModel('plugins');
		//print_r ($model);
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('PUBLISHED_PAYMENT_PLUGIN');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=plugins', $msg );
	}
	
	function unpublish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('plugins');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('UNPUBLISHED_PAYMENT_PLUGIN');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=plugins', $msg );
	}
					
	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );		
		$this->setRedirect( 'index.php?option=com_registrationpro&view=plugins' );
	}
	
	function delete()
	{		
		$model 	= $this->getModel('plugins');
		$user	=JFactory::getUser();
						
		parent::display();
	}
	
	function remove()
	{
		global $option;

		$plugin_handler = new regProPlugins;
		$msg = $plugin_handler->deletePlugins();
		
		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=plugins', $msg );
	}		

	function edit()
	{	
		$array 		= JRequest::getVar('cid',  0, '', 'array');
		$pluginid 	= (int)$array[0];
						
		if($pluginid){
			// edit plugin
			$this->setRedirect('index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]='.$pluginid);				
		}else{
			// add new plugin
			$this->setRedirect('index.php?option=com_installer');				
		}
	}
	
	function newplugin()
	{
		JRequest::setVar( 'view', 'plugin' );
		JRequest::setVar( 'hidemainmenu', 1 );
		
		JRequest::setVar( 'layout', 'form' );
						
		parent::display();
	}		
}
?>