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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class registrationpro_register extends JTable
{
	var $rid 		= null;
	var $rdid 		= null;
	var $uid 		= null;
	var $urname 	= null;
	var $uregdate 	= null;
	var $uip 		= null;
	var $status 	= null;
	var $temp_params = null;
	var $params 	= null;
	var $notify 	= null;
	var $notified 	= null;
	var $products 	= null;
	var $firstname 	= null;
	var $lastname  	= null;
	var $email	   	= null;
	var $active	   	= null;
	var $added_by 	= null;
	var $group_added_by = null;
	var $attended = null;

	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_register', 'id', $db );
	}
	
	/** check for existing email id */
	function check_existing_email($eventids = array(), $checkemail)
	{								
		//$query = "SELECT email FROM #__registrationpro_register WHERE rdid= $eventid and active=1 AND email='".$checkemail."'";
		$query = "SELECT email FROM #__registrationpro_register WHERE rdid in (".implode(",",$eventids).") and active=1 AND email='".$checkemail."'";
		$this->_db->setQuery($query);
		$existing_email = $this->_db->loadResult();
		
		if ($existing_email) {			
			$arrReturn['error_message']	 = JText::_('EVENTS_REGISTRA_ALLRE')."<br/>";					
			$arrReturn['existing_email'] = $existing_email;
			return $arrReturn;           
		}						
	}	
}
?>