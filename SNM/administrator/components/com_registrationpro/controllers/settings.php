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

class registrationproControllerSettings extends registrationproController {

	function __construct() {
		parent::__construct();
		if(JRequest::getVar('task') == 'currencySymbol') {
			$this->currencySymbol();
		} else {
			$this->registerTask( 'add', 'edit' );
			$this->registerTask( 'apply', 'save' );
		}
	}

	function cancel() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_registrationpro' );
	}

	function publish() {
		$this->selectuser();
	}

	function unpublish() {
		$this->deselectuser();
	}

	function edit() {
		JRequest::setVar( 'view', 'settings' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model = $this->getModel('settings');
		$user = JFactory::getUser();
		parent::display();
	}

	function save()	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$task = JRequest::getVar('task');
		$post = JRequest::get( 'post', JREQUEST_ALLOWRAW);
		$post['user_ids'] 		 = serialize($post['user_ids']);
		$post['user_categories'] = serialize($post['user_categories']);
		$post['user_locations']  = serialize($post['user_locations']);
		$post['user_forms'] 	 = serialize($post['user_forms']);
		$post['user_groups'] 	 = serialize($post['user_groups']);

		$model = $this->getModel('settings');
		$db = JFactory::getDBO();

		$flag = 1;
		foreach($post as $key=>$value){
      		$db->setQuery("UPDATE #__registrationpro_config SET setting_value='".addslashes($value)."' WHERE setting_name='".$key."'");
		  	if (!$db->query()) {
				$flag = 0;
		    	echo "<script>alert('".$db->getErrorMsg()."');window.history.go(-1);</script>\n";
		    	exit();
		  	}
      	}

		$msg = '';
		$link = 'index.php?option=com_registrationpro&view=settings';
		if ($flag) {
			switch ($task) {
				case 'apply' :
					$link = 'index.php?option=com_registrationpro&controller=settings&view=settings&hidemainmenu=1&cid[]='.$returnid;
					break;

				default :
					$link = 'index.php?option=com_registrationpro';
					break;
			}
			$msg = JText::_( 'ADMIN_EVENTS_SETT_SAVE');
			$cache = JFactory::getCache('com_registrationpro');
			$cache->clean();
		}

		$this->setRedirect( $link, $msg );
 	}

	function selectuser() {
		$db = JFactory::getDBO();
		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item first' ) );
		}else{
			$model = $this->getModel('settings');
			$model->saveUserIds($cid, 1);
		}

		JRequest::setVar( 'layout', 'selectusers' );
		JRequest::setVar( 'tmpl', 'component' );

		parent::display();
	}

	function deselectuser()
	{
		$db =JFactory::getDBO();

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item first' ) );
		}else{
			$model = $this->getModel('settings');
			$model->saveUserIds($cid, 0);
		}

		JRequest::setVar( 'layout', 'selectusers' );
		JRequest::setVar( 'tmpl', 'component' );

		parent::display();
	}

	function moderator()
	{
		$db =JFactory::getDBO();

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item first' ) );
		}else{
			$model = $this->getModel('settings');
			$model->savemoderation($cid, 1);
		}

		JRequest::setVar( 'layout', 'selectusers' );
		JRequest::setVar( 'tmpl', 'component' );

		parent::display();
	}

	function unmoderator()
	{
		$db =JFactory::getDBO();

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item first' ) );
		}else{
			$model = $this->getModel('settings');
			$model->savemoderation($cid, 0);
		}

		JRequest::setVar( 'layout', 'selectusers' );
		JRequest::setVar( 'tmpl', 'component' );

		parent::display();
	}

	function currencySymbol(){
		$code = JRequest::getVar('cvalue', 'get');
		$model = $this->getModel('settings');
		$symbol = $model->getSymbol($code);
		echo $symbol;
		exit;
	}
}
?>
