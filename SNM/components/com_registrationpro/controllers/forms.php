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

class registrationproControllerForms extends registrationproController {

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

		$model = $this->getModel('forms');
		//print_r ($model);
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_PUBL_FORM');

		$link	= JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);		
		$this->setRedirect($link, $msg );
	}
	
	function unpublish()
	{
		global $Itemid;
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('forms');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_EVENTS_SUC_UNPUBL_FORM');

		$link	= JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);		
		$this->setRedirect($link, $msg );		
	}
	
	function cancel()
	{
		global $Itemid;
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );	
		
		$link	= JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);		
		$this->setRedirect($link, $msg );
	}
	
	function delete()
	{		
		$model 	= $this->getModel('forms');
		$user	= JFactory::getUser();
						
		parent::display();
	}
	
	function copy()
	{
		JRequest::setVar( 'view', 'form' );
		JRequest::setVar( 'hidemainmenu', 1 );
		
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

		$model = $this->getModel('forms');

		$msg = $model->delete($cid).' '.JText::_('ADMIN_FORMS_DEL');;

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();


		$link	= JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);		
		$this->setRedirect($link, $msg );				
	}

	function edit( )
	{	
		JRequest::setVar( 'view', 'form' );
		JRequest::setVar( 'hidemainmenu', 1 );
									
		parent::display();
	}
	
		
	function save()
	{
		global $Itemid;
		
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
				
		$task		= JRequest::getVar('task');
		$copy		= JRequest::getVar('copy', 0);
		$form_id	= JRequest::getVar('form_id',0,'','int');

		//$post = JRequest::get( 'post' );
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);				
		//echo "<pre>"; print_r($post); exit;

		$model = $this->getModel('form');
		
		if ($returnid = $model->store($post)) {
			
			if($copy)
			{
				$field_model = $this->getModel('field');
				$field_model->copyfields($form_id, $returnid); 		// copy registration form fields
				$field_model->copycbfields($form_id, $returnid); 	// copy CB form fields
			}else{
				// (Add default fields for event registration)
				$field_rows = $model->getDefaultFieldsCount($returnid);			
				if($field_rows > 0){
					// do nothing
				}else{
					$field_model = $this->getModel('field');
					$field_model->adddefaultfields($returnid);
				}
			}
			
			switch ($task)
			{
				case 'apply' :
					$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&view=form&hidemainmenu=1&cid[]='.$returnid.'&Itemid='.$Itemid,false);
					break;

				default :
					$link = JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);
					break;
			}
			$msg	= JText::_( 'ADMIN_FORMS_SAVE');

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();

		} else {

			$msg 	= '';
			$link = JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);
		}
		$this->setRedirect( $link, $msg );								
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

        $model = $this->getModel('forms');
        $model->saveorder($cid, $order);

        $link = JRoute::_('index.php?option=com_registrationpro&view=forms&Itemid='.$Itemid,false);
        $this->setRedirect( $link );
    }
	
	
	//*************************************************** Fields section  ******************************************//
	
	function add_field()
	{
		global $Itemid;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );				

		$task 	= JRequest::getVar('task');
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		
		$formid = JRequest::getVar('form_id', 0,'','int');
										
		// Save ticket information
		$model = $this->getModel('field');
					
		$post['form_id'] = $formid;
		
		//echo "<pre>"; print_r($post); exit;
		
		// check duplicate fields for firstname, lastname , email
		if($post['name'] == 'firstname' || $post['name'] == 'lastname' || $post['name'] == 'email'){			
			//$link 	= JRoute::_("index.php?option=com_registrationpro&controller=forms&task=edit&cid[]=".$formid.'&Itemid='.$Itemid,false);
			$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$formid.'&Itemid='.$Itemid,false);
			$msg	= JText::_( 'ADMIN_FIELDS_NOT_DUPLICATE');
		}else{				
			if ($returnid = $model->store($post)) {			
				switch ($task)
				{
					case 'apply' :
						$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit_field&form_id='.$formid.'&cid='.$returnid.'&Itemid='.$Itemid,false);
						break;
	
					default :
						$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$formid.'&Itemid='.$Itemid,false);
						break;
				}
				$msg	= JText::_('ADMIN_FIELDS_SAVE');
	
				$cache = JFactory::getCache('com_registrationpro');
				$cache->clean();
				
			} else {
				$msg	= '';			
			}
		}
		// End
								
        $this->setRedirect( $link, $msg );
	}
	
	function add_field_apply()
	{
		global $Itemid;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );			

		$task 	= JRequest::getVar('task');
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		
		$formid = JRequest::getVar('form_id', 0,'','int');
		
		//echo "<pre>"; print_r($post); exit;
										
		// Save ticket information
		$model = $this->getModel('field');			
		$post['form_id'] = $formid;
		
		// check duplicate fields for firstname, lastname , email
		if($post['name'] == 'firstname' || $post['name'] == 'lastname' || $post['name'] == 'email'){			
			$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit_field&form_id='.$formid.'&cid='.$post['id'].'&Itemid='.$Itemid,false);
			$msg	= JText::_( 'ADMIN_FIELDS_NOT_DUPLICATE');
		}else{
		
			if($post['validation_rule'] == "confirm" && $post['inputtype'] != "text"){
				$post['validation_rule'] = "0";
			}
						
			if ($returnid = $model->store($post)) {
			
				$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit_field&form_id='.$formid.'&cid='.$returnid.'&Itemid='.$Itemid,false);
					
				$msg	= JText::_('ADMIN_FIELDS_SAVE');
	
				$cache = JFactory::getCache('com_registrationpro');
				$cache->clean();				
			} else {
				$msg	= '';			
			}
		}
		// End
        $this->setRedirect( $link, $msg );
	}
	
	function edit_field()
	{				
		JRequest::setVar( 'id', 0);	
		JRequest::setVar( 'view', 'field' );
		JRequest::setVar( 'hidemainmenu', 1 );						
		parent::display();
	}
	
	function cancelfield()
	{
		global $Itemid;
	
		JRequest::checkToken() or die( 'Invalid Token' );						
		
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$post['form_id'].'&Itemid='.$Itemid,false);

		$this->setRedirect($link);
	}

	function remove_field()
	{	
		global $Itemid;
				
		JRequest::checkToken() or jexit( 'Invalid Token' );
			
		JRequest::setVar( 'view', 'form' );

		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id= JRequest::getVar( 'form_id', 0, 'post','int');
		
		//echo "<pre>";print_r($cid); exit;

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('field');

		$msg = $model->delete($cid, $form_id);

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();
								
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);	
		$this->setRedirect($link, $msg);

	}
	
	function publishfield()
	{	
		global $Itemid;
	
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id= JRequest::getVar( 'form_id', 0, 'post','int');

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = $this->getModel('field');
		//print_r ($model);
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_FIELDS_SUC_PUBL_FIELD');

		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);	
		$this->setRedirect($link, $msg);
	}
	
	function unpublishfield()
	{
		global $Itemid;
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id= JRequest::getVar( 'form_id', 0, 'post','int');

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('field');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_FIELDS_SUC_UNPUBL_FIELD');

		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);		
		$this->setRedirect($link, $msg);
	}
	
	function orderupfield()
	{
		global $Itemid;
	
		JRequest::checkToken() or jexit( 'Invalid Token' );					
		
		/*$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id = JRequest::getVar( 'form_id', 0, 'post');
		$group_id = JRequest::getVar( 'group_id', 0, 'post');
		
		//echo "<pre>"; print_r($_POST); exit;
						
        $model = $this->getModel('field');
       	//$model->move(-1,$cid[0],"form_id = ".$form_id);
		
		$model->move(-1,$cid[0],'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid);
				       				
		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;		
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');		
		$this->setRedirect($link, $msg);*/
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');
		
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$form_id 	= JRequest::getVar( 'form_id', 0, 'post','int');
		$group_id 	= JRequest::getVar( 'group_id', 0, 'post','int');

		if (isset( $cid[0] ))
		{
			$row = & JTable::getInstance('registrationpro_fields','');
			$row->load( (int) $cid[0] );
			$row->move(-1, 'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid);

			$cache =  JFactory::getCache('com_registrationpro');
			$cache->clean();
		}
		
		//$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);	
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);	
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');		
		$this->setRedirect($link, $msg);
	}
	
	function orderdownfield()
	{
		global $Itemid;
	
		JRequest::checkToken() or jexit( 'Invalid Token' );		
						
		/*$cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id = JRequest::getVar( 'form_id', 0, 'post');
		$group_id = JRequest::getVar( 'group_id', 0, 'post');
			
        $model = $this->getModel('field');
        //$model->move(1,$cid[0],"form_id = ".$form_id);
		
		$model->move(1,$cid[0],'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid);
			       												
		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;		
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');
		$this->setRedirect($link, $msg);*/
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');
		
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$form_id 	= JRequest::getVar( 'form_id', 0, 'post','int');
		$group_id 	= JRequest::getVar( 'group_id', 0, 'post','int');

		if (isset( $cid[0] ))
		{
			$row = & JTable::getInstance('registrationpro_fields','');
			$row->load( (int) $cid[0] );
			$row->move(1, 'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid);

			$cache =  JFactory::getCache('com_registrationpro');
			$cache->clean();
		}
		
		//$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);	
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');		
		$this->setRedirect($link, $msg);
	}
	
	function savefieldsorder()
	{		
		global $Itemid;
		
		JRequest::checkToken() or jexit( 'Invalid Token' );
        $cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id = JRequest::getVar( 'form_id', 0, 'post','int');
        $order 	 = JRequest::getVar( 'order', array(), 'post', 'array' );
		
        JArrayHelper::toInteger($cid);
        JArrayHelper::toInteger($order);

        $model = $this->getModel('field');
        $model->saveorder($cid, $order);

        //$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');
		$this->setRedirect($link, $msg);		
	}
	
	//*************************************************** CB Fields section  ******************************************//
				
	function publishcbfield()
	{
		global $Itemid, $mainframe;
		
		$cid 	 = JRequest::getVar( 'cid', 0,'','int');
		$form_id = JRequest::getVar( 'form_id', 0,'','int');
		
		if (!$cid) {		
			$action = $publishcat ? 'publish' : 'unpublish';			
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";			
			exit;
		}
		
		if(!isset($form_id)){		
			exit;	
		}
		
		//echo "<pre>";print_r($_REQUEST); exit;
		
		$model = $this->getModel('field');
        $model->publishcbfield($cid, $form_id, 1);				
		
		//$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);		
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);
		$msg = JText::_('ADMIN_CBFIELDS_SUC_PUBL_FIELD');
		$this->setRedirect($link, $msg);																							
	}
	
	function unpublishcbfield()
	{
		global $mainframe, $Itemid;
		
		$cid 	 = JRequest::getVar( 'cid', 0,'','int');
		$form_id = JRequest::getVar( 'form_id', 0,'','int');		
		
		if (!$cid) {		
			$action = $publishcat ? 'publish' : 'unpublish';			
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";			
			exit;
		}
		
		if(!isset($form_id)){		
			exit;	
		}				
		
		$model = $this->getModel('field');
        $model->publishcbfield($cid, $form_id,0);					
		
		//$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);	
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);
		$msg = JText::_('ADMIN_CBFIELDS_SUC_UNPUBL_FIELD');
		$this->setRedirect($link, $msg);																							
	}
	
	//*************************************************** Joomsocial Fields section  ******************************************//
				
	function publishjoosocialfield()
	{
		global $mainframe, $Itemid;
		
		$cid 	 = JRequest::getVar( 'cid', 0,'','int');
		$form_id = JRequest::getVar( 'form_id', 0,'','int');
		
		if (!$cid) {		
			$action = $publishcat ? 'publish' : 'unpublish';			
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";			
			exit;
		}
		
		if(!isset($form_id)){		
			exit;	
		}
		
		//echo "<pre>";print_r($_REQUEST); exit;
		
		$model = $this->getModel('field');
        $model->publishjoosocialfield($cid, $form_id, 1);				
		
		//$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);		
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);
		$msg = JText::_('ADMIN_JSFIELDS_SUC_PUBL_FIELD');
		$this->setRedirect($link, $msg);																							
	}
	
	function unpublishjoosocialfield()
	{
		global $mainframe, $Itemid;
		
		$cid 	 = JRequest::getVar( 'cid', 0,'','int');
		$form_id = JRequest::getVar( 'form_id', 0,'','int');		
		
		if (!$cid) {		
			$action = $publishcat ? 'publish' : 'unpublish';			
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";			
			exit;
		}
		
		if(!isset($form_id)){		
			exit;	
		}				
		
		$model = $this->getModel('field');
        $model->publishjoosocialfield($cid, $form_id,0);					
		
		//$link = JRoute::_('index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id.'&Itemid='.$Itemid,false);		
		$link = JRoute::_('index.php?option=com_registrationpro&view=form&id='.$form_id.'&Itemid='.$Itemid,false);
		$msg = JText::_('ADMIN_JSFIELDS_SUC_UNPUBL_FIELD');
		$this->setRedirect($link, $msg);																							
	}
}
?>
