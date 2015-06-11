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
 *
 * 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class registrationproController extends JControllerLegacy
{
	/**
	 * Custom Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Display the view
	 */
	function display($cachable = false, $urlparams = false)
	{
		$user 		=  JFactory::getUser();
		$document	=  JFactory::getDocument();				
		
		$document->addStyleSheet('components/com_registrationpro/assets/css/regpro_admin.css');
		
		$view	= JRequest::getVar( 'view', '', '', 'string', JREQUEST_ALLOWRAW );

		if($view == "") {
			/* JSubMenuHelper::addEntry( JText::_( 'COMPONENT_NAME' ), JRoute::_('index.php?option=com_registrationpro'),($view == ''));
			if ($user->get('gid') > 24) { JSubMenuHelper::addEntry( JText::_( 'CONFIGURATION' ), 'index.php?option=com_registrationpro&view=settings&task=edit', ($view == 'seasons'));}
			JSubMenuHelper::addEntry( JText::_( 'EVENT_MANAGER' ), 'index.php?option=com_registrationpro&view=events', ($view == 'events'));
			JSubMenuHelper::addEntry( JText::_( 'EVENT_CATEGORIES' ), 'index.php?option=com_registrationpro&view=categories', ($view == 'categories'));
			JSubMenuHelper::addEntry( JText::_( 'REGISTRATION_FORMS' ), 'index.php?option=com_registrationpro&view=forms', ($view == 'forms'));
			JSubMenuHelper::addEntry( JText::_( 'CONGURE_EMAILS' ), 'index.php?option=com_registrationpro&view=emails', ($view == 'emails'));
			JSubMenuHelper::addEntry( JText::_( 'LOCATION_MANGER' ), 'index.php?option=com_registrationpro&view=locations', ($view == 'locations'));
			JSubMenuHelper::addEntry( JText::_( 'ARCHIVE_MANAGER' ), 'index.php?option=com_registrationpro&view=archives', ($view == 'archive'));
			JSubMenuHelper::addEntry( JText::_( 'DISCOUNT_COUPONS' ), 'index.php?option=com_registrationpro&view=coupons', ($view == 'coupons'));
			JSubMenuHelper::addEntry( JText::_( 'PAYMENT_PLUGINS' ), 'index.php?option=com_registrationpro&view=plugins', ($view == 'plugins'));
			JSubMenuHelper::addEntry( JText::_( 'SEARCH_MANAGER' ), 'index.php?option=com_registrationpro&view=search', ($view == 'search')); */
		}
		
		// send reminder emails
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	= $registrationproAdmin->config();		
		if($regpro_config['disable_remiders'] == 0) {
			$registrationproHelper = new registrationproHelper;
			$registrationproHelper->reminder();
		}

		$document->addScript('components/com_registrationpro/assets/javascript/jquery-1.9.1.min.js');
		$document->addScript('components/com_registrationpro/assets/javascript/ace-elements.min.js');
		$document->addScript('components/com_registrationpro/assets/javascript/ace.min.js');
		
		parent::display();
	}
	
	public function updateParentId()
	{
		$db	= JFactory::getDBO();
		$query = 'UPDATE #__registrationpro_dates SET parent_id = 0 WHERE parent_id = id';
		$db->setQuery($query);
		if($db->query())
		{
			$msg = $db->getAffectedRows().' records successfully updated';
		}else{
			$msg = 'No record found';
		}
		$this->setRedirect('index.php?option=com_registrationpro',$msg);
	}
}
?>