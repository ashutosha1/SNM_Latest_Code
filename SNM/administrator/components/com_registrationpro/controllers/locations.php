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

class registrationproControllerLocations extends registrationproController {

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

		$model = $this->getModel('locations');
		//print_r ($model);
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_PUBL_LO');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=locations', $msg );
	}
	
	function unpublish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('locations');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_UNPUBL_LO');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=locations', $msg );
	}
		
	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );		
		$this->setRedirect( 'index.php?option=com_registrationpro&view=locations' );
	}
	
	function delete()
	{		
		$model 	= $this->getModel('locations');
		$user	=JFactory::getUser();
						
		parent::display();
	}
	
	function remove()
	{
		global $option;

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('locations');

		$msg = $model->delete($cid).' '.JText::_('ADMIN_EVENTS_DELYES_LO');;

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=locations', $msg );
	}		

	function edit( )
	{	
		JRequest::setVar( 'view', 'location' );
		JRequest::setVar( 'hidemainmenu', 1 );
		
		$user	=JFactory::getUser();
						
		parent::display();
	}
	
		
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$task		= JRequest::getVar('task');

		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
				
		//echo "<pre>"; print_r($post); exit;

		$model = $this->getModel('location');
		
		if ($returnid = $model->store($post)) {

			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option=com_registrationpro&controller=locations&view=location&hidemainmenu=1&cid[]='.$returnid;
					break;

				default :
					$link = 'index.php?option=com_registrationpro&view=locations';
					break;
			}
			$msg	= JText::_( 'ADMIN_EVENTS_SAVE_LO');

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();

		} else {

			$msg 	= '';
			$link = 'index.php?option=com_registrationpro&view=locations';
		}
		$this->setRedirect( $link, $msg );								
 	}		
	
	 /**
     * Handle the task 'orderup'
     * @access private     
     */
    function orderup()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		//echo "<pre>"; print_r($_POST); exit;		
        $model = $this->getModel('locations');
        $model->move(-1,$cid[0]);
				
        $link = 'index.php?option=com_registrationpro&view=locations';
        $this->setRedirect( $link );
    }


    /**
     * Handle the task 'orderdown'
     * @access private
     */
    function orderdown()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

       	$model = $this->getModel('locations');
        $model->move(1,$cid[0]);

        $link = 'index.php?option=com_registrationpro&view=locations';
        $this->setRedirect( $link );
    }    
}
?>
