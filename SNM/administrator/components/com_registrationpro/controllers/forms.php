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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class registrationproControllerForms extends registrationproController {

	function __construct() {
		parent::__construct();
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );
	}

	function publish() {
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		$model = $this->getModel('forms');
		if(!$model->publish($cid, 1)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		$total = count( $cid );
		$msg = $total.' '.JText::_('ADMIN_EVENTS_SUC_PUBL_FORM');
		$this->setRedirect( 'index.php?option=com_registrationpro&view=forms', $msg );
	}

	function unpublish() {
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );

		$model = $this->getModel('forms');
		if(!$model->publish($cid, 0)) echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";

		$total = count( $cid );
		$msg = $total.' '.JText::_('ADMIN_EVENTS_SUC_UNPUBL_FORM');
		$this->setRedirect( 'index.php?option=com_registrationpro&view=forms', $msg );
	}

	function cancel() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_registrationpro' );
	}

	function delete() {
		$model = $this->getModel('forms');
		$user = JFactory::getUser();
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
		global $option;

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$total = count( $cid );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('forms');

		$msg = $model->delete($cid).' '.JText::_('ADMIN_FORMS_DEL');;

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$this->setRedirect( 'index.php?option=com_registrationpro&view=forms', $msg );
	}

	function edit( )
	{
		JRequest::setVar( 'view', 'form' );
		JRequest::setVar( 'hidemainmenu', 1 );

		parent::display();
	}


	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$task = JRequest::getVar('task');
		$copy = JRequest::getVar('copy', 0);
		$form_id = JRequest::getVar('form_id',0);
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$model = $this->getModel('form');

		if ($returnid = $model->store($post)) {

			if($copy) {
				$field_model = $this->getModel('field');
				$field_model->copyfields($form_id, $returnid); 		// copy registration form fields
				$field_model->copycbfields($form_id, $returnid); 	// copy CB form fields
			} else {
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
					$link = 'index.php?option=com_registrationpro&controller=forms&view=form&hidemainmenu=1&cid[]='.$returnid;
					break;

				default :
					$link = 'index.php?option=com_registrationpro&view=forms';
					break;
			}
			$msg	= JText::_( 'ADMIN_FORMS_SAVE');

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();

		} else {

			$msg 	= '';
			$link = 'index.php?option=com_registrationpro&view=forms';
		}
		$this->setRedirect( $link, $msg );
 	}

    /**
     * Handle the task 'saveorder'
     * @access private
     */
    function saveorder()
    {
        JRequest::checkToken() or jexit( 'Invalid Token' );
        $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
        $order = JRequest::getVar( 'order', array(), 'post', 'array' );

        JArrayHelper::toInteger($cid);
        JArrayHelper::toInteger($order);

        $model = $this->getModel('forms');
        $model->saveorder($cid, $order);

        $link = 'index.php?option=com_registrationpro&view=forms';
        $this->setRedirect( $link );
    }


	//*************************************************** Fields section  ******************************************//

	function add_field() {
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$task = JRequest::getVar('task');
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$formid = JRequest::getVar('form_id', 0);

		// Save ticket information
		$model = $this->getModel('field');
		$post['form_id'] = $formid;

		if($post['conditional_field_values'] != "") $post['conditional_field_values'] = serialize($post['conditional_field_values']);
		if($post['conditional_field'] != "") $post['conditional_field_name'] = $model->getFieldName($post['conditional_field']);

		if($post['fees_field']) {
			$post['fees_field'] = 1;
		} else {
			$post['fees_field'] = 0;
			$post['fees'] = "";
			$post['fees_type'] = "A";
		}

		// check duplicate fields for firstname, lastname , email
		if($post['name'] == 'firstname' || $post['name'] == 'lastname' || $post['name'] == 'email'){
			$link = "index.php?option=com_registrationpro&controller=forms&task=edit&cid[]=".$formid;
			$msg = JText::_( 'ADMIN_FIELDS_NOT_DUPLICATE');
		} else {
			if ($returnid = $model->store($post)) {
				switch ($task)
				{
					case 'apply' :
						$link = 'index.php?option=com_registrationpro&controller=forms&task=edit_field&hidemainmenu=1&form_id='.$formid.'&cid='.$returnid;
						break;

					default :
						$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&cid[]='.$formid;
						break;
				}
				$msg = JText::_('ADMIN_FIELDS_SAVE');

				$cache = JFactory::getCache('com_registrationpro');
				$cache->clean();

			} else $msg	= '';
		}
        $this->setRedirect( $link, $msg );
	}

	function add_field_apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$task 	= JRequest::getVar('task');
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);

		$formid = JRequest::getVar('form_id', 0);

		$model = $this->getModel('field');

		if($post['conditional_field_values'] != "") {
			$post['conditional_field_values'] = serialize($post['conditional_field_values']);
		}

		if($post['conditional_field'] != ""){
			$post['conditional_field_name'] = $model->getFieldName($post['conditional_field']);
		}

		if($post['fees_field']) {
			$post['fees_field'] = 1;
		}else{
			$post['fees_field'] = 0;
			$post['fees'] = "";
			$post['fees_type'] = "A";
		}

		// Save ticket information
		$post['form_id'] = $formid;

		// check duplicate fields for firstname, lastname , email
		if($post['name'] == 'firstname' || $post['name'] == 'lastname' || $post['name'] == 'email'){
			$link = 'index.php?option=com_registrationpro&controller=forms&task=edit_field&hidemainmenu=1&form_id='.$formid.'&cid='.$post['id'];
			$msg	= JText::_( 'ADMIN_FIELDS_NOT_DUPLICATE');
		}else{

			if($post['validation_rule'] == "confirm" && $post['inputtype'] != "text") $post['validation_rule'] = "0";

			if ($returnid = $model->store($post)) {
				$link = 'index.php?option=com_registrationpro&controller=forms&task=edit_field&hidemainmenu=1&form_id='.$formid.'&cid='.$returnid;
				$msg	= JText::_('ADMIN_FIELDS_SAVE');
				$cache = JFactory::getCache('com_registrationpro');
				$cache->clean();
			} else $msg	= '';
		}
        $this->setRedirect( $link, $msg );
	}

	function edit_field()
	{
		JRequest::setVar( 'view', 'field' );
		JRequest::setVar( 'hidemainmenu', 1 );
		parent::display();
	}

	function cancelfield()
	{
		JRequest::checkToken() or die( 'Invalid Token' );
		$post 	= JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$post['form_id'];
		$this->setRedirect($link);
	}

	function remove_field()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		JRequest::setVar( 'view', 'form' );

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id = JRequest::getVar( 'form_id', 0, 'post');

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('field');

		$msg = $model->delete($cid, $form_id);

		$cache = JFactory::getCache('com_registrationpro');
		$cache->clean();

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$this->setRedirect($link, $msg);

	}

	function publishfield()
	{
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id= JRequest::getVar( 'form_id', 0, 'post');

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

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$this->setRedirect($link, $msg);
	}

	function unpublishfield()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id= JRequest::getVar( 'form_id', 0, 'post');

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('field');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}

		$total = count( $cid );
		$msg 	= $total.' '.JText::_('ADMIN_FIELDS_SUC_UNPUBL_FIELD');

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$this->setRedirect($link, $msg);
	}

	function orderupfield()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$form_id 	= JRequest::getVar( 'form_id', 0, 'post');
		$group_id 	= JRequest::getVar( 'group_id', 0, 'post');

		if (isset( $cid[0] ))
		{
			$row = & JTable::getInstance('registrationpro_fields','');
			$row->load( (int) $cid[0] );
			if($row->inputtype=='groups'){
				 $row->moveFields(-1, 'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid,$form_id);
			}else{
				$row->moveFieldsInter(-1, 'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid,$form_id);
			}

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		}

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');
		$this->setRedirect($link, $msg);
	}

	function orderdownfield()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_registrationpro'.DS.'tables');

		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$form_id 	= JRequest::getVar( 'form_id', 0, 'post');
		$group_id 	= JRequest::getVar( 'group_id', 0, 'post');

		if (isset( $cid[0] ))
		{
			$row = & JTable::getInstance('registrationpro_fields','');
			$row->load( (int) $cid[0] );
			if($row->inputtype=='groups'){
				 $row->moveDownFields(-1, 'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid,$form_id);
			}else{
				$row->moveFieldsDownInter(-1, 'form_id = '.(int)$form_id.' AND groupid ='.(int) $row->groupid,$form_id);
			}

			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		}

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');
		$this->setRedirect($link, $msg);
	}

	function savefieldsorder()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
        $cid 	 = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$form_id = JRequest::getVar( 'form_id', 0, 'post');
        $order 	 = JRequest::getVar( 'order', array(), 'post', 'array' );

        JArrayHelper::toInteger($cid);
        JArrayHelper::toInteger($order);

        $model = $this->getModel('field');
        $model->saveorder($cid, $order);

        $link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_FIELDS_SAVE_ORDERING');
		$this->setRedirect($link, $msg);
	}

	// function call by ajax for conditional fields
	function getFieldValues()
	{
		JRequest::setVar( 'view', 'field' );
		JRequest::setVar( 'layout', 'field');
		parent::display();
	}

	//*************************************************** CB Fields section  ******************************************//

	function publishcbfield()
	{
		global $mainframe;

		$cid 	 = JRequest::getVar( 'cid');
		$form_id = JRequest::getVar( 'form_id', 0);

		if (!$cid) {
			$action = $publishcat ? 'publish' : 'unpublish';
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
			exit;
		}

		if(!isset($form_id)) exit;

		$model = $this->getModel('field');
        $model->publishcbfield($cid, $form_id, 1);
		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_CBFIELDS_SUC_PUBL_FIELD');
		$this->setRedirect($link, $msg);
	}

	function unpublishcbfield()
	{
		global $mainframe;

		$cid 	 = JRequest::getVar( 'cid');
		$form_id = JRequest::getVar( 'form_id', 0);

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

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_CBFIELDS_SUC_UNPUBL_FIELD');
		$this->setRedirect($link, $msg);
	}

	//*************************************************** Joomsocial Fields section  ******************************************//

	function publishjoosocialfield()
	{
		global $mainframe;

		$cid 	 = JRequest::getVar( 'cid');
		$form_id = JRequest::getVar( 'form_id', 0);

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

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_JSFIELDS_SUC_PUBL_FIELD');
		$this->setRedirect($link, $msg);
	}

	function unpublishjoosocialfield()
	{
		global $mainframe;

		$cid 	 = JRequest::getVar( 'cid');
		$form_id = JRequest::getVar( 'form_id', 0);

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

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_JSFIELDS_SUC_UNPUBL_FIELD');
		$this->setRedirect($link, $msg);
	}


	//*************************************************** Joomla core fiels section  ******************************************//

	function publishprofilefield()
	{
		global $mainframe;

		$cid 	 = JRequest::getVar( 'cid');
		$form_id = JRequest::getVar( 'form_id', 0);

		//echo "<pre>"; print_r($_REQUEST); exit;

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
        $model->publishprofilefield($cid, $form_id, 1);

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_JCOREFIELDS_SUC_PUBL_FIELD');
		$this->setRedirect($link, $msg);
	}

	function unpublishprofilefield()
	{
		global $mainframe;

		$cid 	 = JRequest::getVar( 'cid');
		$form_id = JRequest::getVar( 'form_id', 0);

		if (!$cid) {
			$action = $publishcat ? 'publish' : 'unpublish';
			echo "<script> alert('Select an item to $action'); window.history.go(-1);</script>\n";
			exit;
		}

		if(!isset($form_id)){
			exit;
		}

		$model = $this->getModel('field');
        $model->publishprofilefield($cid, $form_id,0);

		$link = 'index.php?option=com_registrationpro&controller=forms&task=edit&hidemainmenu=1&cid[]='.$form_id;
		$msg = JText::_('ADMIN_JCORE_SUC_UNPUBL_FIELD');
		$this->setRedirect($link, $msg);
	}
}
?>
