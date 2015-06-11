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

class registrationpro_forms extends JTable{
	var $id 				= null;
	var $user_id			= null;
	var $name 				= null;
	var $title 				= null;
	var $thankyou 			= null;
	var $published 			= 1;
	var $checked_out 		= null;
	var $checked_out_time 	= null;	

	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_forms', 'id', $db );
	}
}
?>