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

class registrationpro_event_discount extends JTable{
	var $id 				= null;
	var $event_id			= null;
	var $discount_name		= null;
	var $discount_amount	= null;
	var $discount_type 		= null;	
	var $min_tickets 		= null;
	var $early_discount_date = null;
	var $published 			= null;	
	var $checked_out		= null;
	var $checked_out_time 	= null;	
	var $ordering 			= null;		
	
	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_event_discount', 'id', $db );
	}			
}
?>