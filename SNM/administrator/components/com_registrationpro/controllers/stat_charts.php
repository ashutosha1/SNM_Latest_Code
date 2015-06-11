<?php
/**
 * @version		v1.5.13 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class registrationproControllerStat_Charts extends registrationproController {

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

	function report() {
		$user	=& JFactory::getUser();
		JRequest::setVar( 'view', 'stat_charts' );
		parent::display();
	}

	function print_report()
	{
		JRequest::setVar( 'view', 'stat_charts' );
		JRequest::setVar( 'layout', 'default');
		parent::display();
	}
}
?>
