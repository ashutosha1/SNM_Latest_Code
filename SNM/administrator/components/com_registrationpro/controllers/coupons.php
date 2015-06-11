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

class registrationproControllerCoupons extends registrationproController {

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

		$model = $this->getModel('coupons');
		//print_r ($model);
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('EVENT_ADMIN_COUPONS_MSG_PUBLHISED');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=coupons', $msg );
	}
	
	function unpublish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('coupons');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('EVENT_ADMIN_COUPONS_MSG_UNPUBLISHED');

		$this->setRedirect( 'index.php?option=com_registrationpro&view=coupons', $msg );
	}
		
	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );		
		$this->setRedirect( 'index.php?option=com_registrationpro&view=coupons' );
	}
	
	function delete()
	{		
		$model 	= $this->getModel('coupons');
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

		$model = $this->getModel('coupons');

		$msg = $model->delete($cid).' '.JText::_('EVENT_ADMIN_COUPONS_MSG_DELETED');;

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=coupons', $msg );
	}		

	function edit( )
	{
		JRequest::setVar( 'view', 'coupon' );
		JRequest::setVar( 'hidemainmenu', 1 );
		
		$user	=JFactory::getUser();
						
		parent::display();
	}
	
		
	function save()
	{
		global $mainframe;
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$task		= JRequest::getVar('task');
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
				
		$model = $this->getModel('coupon');
		
		// check coupon code already exist or not
		$chkflag = $model->check_code_exists($post['code'],$post['id']); 		
		if($chkflag){
			$link	= "index.php?option=com_registrationpro&view=coupon&hidemainmenu=1&cid[]=".$post['id'];
			$msg	= JText::_('EVENT_ADMIN_COUPONS_MSG_ALREADY_EXISTS');
			$mainframe->redirect($link, $msg); 
			exit;
		}
		
		
		// convert events array in comman separted value to store in database
		$post['eventids'] = implode(",",$post['eventids']);
		
		if ($returnid = $model->store($post)) {
			switch ($task)
			{
				case 'apply' :
					$link = 'index.php?option=com_registrationpro&controller=locations&view=coupon&hidemainmenu=1&cid[]='.$returnid;
					break;					
				default :
					$link = 'index.php?option=com_registrationpro&view=coupons';
					break;
			}
			$msg	= JText::_( 'EVENT_ADMIN_COUPONS_MSG_SAVED');

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		} else {
			$msg 	= '';
			$link = 'index.php?option=com_registrationpro&view=coupons';
		}
		$this->setRedirect( $link, $msg );								
 	}		 
}
?>
