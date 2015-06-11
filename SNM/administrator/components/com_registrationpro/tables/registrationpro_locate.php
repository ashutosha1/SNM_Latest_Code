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

class registrationpro_locate extends JTable
{
	var $id 			= null;
	var $user_id 		= null;
	var $club 			= null;
	var $url 			= null;
	var $street 		= null;
	var $plz 			= null;
	var $city 			= null;
	var $country 		= null;
	var $latitude 		= null;
	var $longitude 		= null;
	var $locdescription = null;
	var $locimage 		= null;
	var $sendernameloc 	= null;	
	var $sendermailloc 	= null;
	var $deliveriploc 	= null;
	var $deliverdateloc	= null;
	var $publishedloc 	= null;
	var $checked_out	= null;
	var $checked_out_time = null;	
	var $ordering 		= null;
	
		
	function __construct(&$db)
		{
		parent::__construct( '#__registrationpro_locate', 'id', $db );
		}
	
}
?>