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

class registrationproControllerMycategories extends registrationproController {

	function __construct()
	{
		parent::__construct();		
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );			
	}
	
	function publish()
	{	
		global $Itemid;

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = $this->getModel('mycategories');
		//print_r ($model);
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_PUBL_CAT');
		$link	= JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);	

		$this->setRedirect( $link, $msg );
	}
	
	function unpublish()
	{
		global $Itemid;	

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('mycategories');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_UNPUBL_CAT');
		$link	= JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);

		$this->setRedirect($link, $msg );
	}
		
	function cancel()
	{
		global $Itemid;		
	
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );		

		$link	= JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);

		$this->setRedirect($link);
	}
	
	function delete()
	{		
		$model 	= $this->getModel('mycategories');
		$user	= JFactory::getUser();
						
		parent::display();
	}
	
	function remove()
	{
		global $option, $Itemid;

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('mycategories');

		$msg = $model->delete($cid).' '.JText::_('ADMIN_CATEGORIES_DEL');;

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
		
		$link	= JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);

		$this->setRedirect($link, $msg );
	}		

	function edit( )
	{	
		JRequest::setVar( 'view', 'mycategory' );
		JRequest::setVar( 'hidemainmenu', 1 );
		
		$user	= JFactory::getUser();
						
		parent::display();
	}
	
		
	function save()
	{
		global $Itemid;			

		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$user	= JFactory::getUser();

		$task		= JRequest::getVar('task');

		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		
				
		$post['background'] = str_replace("#","",$post['background']);
		//echo "<pre>"; print_r($post); exit;
		
		$post['user_id'] = $user->id;

		$model = $this->getModel('mycategory');
		
		if ($returnid = $model->store($post)) {

			switch ($task)
			{
				case 'apply' :
					$link = JRoute::_('index.php?option=com_registrationpro&controller=categories&view=mycategory&hidemainmenu=1&cid[]='.$returnid.'&Itemid='.$Itemid,false);
					break;

				default :
					$link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
					break;
			}
			$msg	= JText::_( 'ADMIN_CATEGORIES_SAVE');

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();

		} else {

			$msg 	= '';
			$link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
		}
		$this->setRedirect( $link, $msg );								
 	}		
	
	 /**
     * Handle the task 'orderup'
     * @access private     
     */
    function orderup()
    {
		global $Itemid;

        JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		//echo "<pre>"; print_r($_POST); exit;		
        $model = $this->getModel('mycategories');
        $model->move(-1,$cid[0]);
				
        $link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
    }


    /**
     * Handle the task 'orderdown'
     * @access private
     */
    function orderdown()
    {
		global $Itemid;	

        JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

       	$model = $this->getModel('mycategories');
        $model->move(1,$cid[0]);

        $link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
    }


    /**
     * Handle the task 'saveorder'
     * @access private
     */
    function saveorder()
    {
		global $Itemid;			

        JRequest::checkToken() or jexit( 'Invalid Token' );
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );
		
        JArrayHelper::toInteger($cid);
        JArrayHelper::toInteger($order);

        $model = $this->getModel('mycategories');
        $model->saveorder($cid, $order);

        $link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
    }
	
	function accesspublic() {
		global $Itemid;	

		$model = $this->getModel('mycategories');
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );		
		
        $model->saveaccess($cid[0], 0,"com_registrationpro");
				
		$link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
	}
	
	function accessregistered() {
		global $Itemid;	

		$model = $this->getModel('mycategories');
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );		
		
        $model->saveaccess($cid[0], 1,"com_registrationpro");
				
		$link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
	}
	
	function accessspecial() {
		global $Itemid;

		$model = $this->getModel('mycategories');
		
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );		
		
        $model->saveaccess($cid[0], 2,"com_registrationpro");
				
		$link = JRoute::_('index.php?option=com_registrationpro&view=mycategories&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
	}
}
?>
