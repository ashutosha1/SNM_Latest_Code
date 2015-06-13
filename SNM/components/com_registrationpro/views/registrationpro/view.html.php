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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class registrationproViewRegistrationpro extends JViewLegacy
{		
	function display($tpl = null){
		global $mainframe, $Itemid;
				
		$link = JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid,false);
		//$link 	= str_replace("&amp;", "&", $link);
		$mainframe->redirect($link);									
						
		//parent::display($tpl);
	}
							
}
?>