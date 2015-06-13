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

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder');

class registrationproViewMyHistory extends JViewLegacy
{
	function display($tpl = null) {
		global $mainframe, $option, $Itemid;
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$registrationproHelper = new registrationproHelper;
		
		$model = $this->getModel();
		$history = $this->get('History');
						
		$this->assignRef('user', $user);
		$this->assignRef('rows', $history);
		$this->assignRef('regproConfig', $regpro_config);	
		
		parent::display($tpl);
	}
}
?>