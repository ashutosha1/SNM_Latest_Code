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

class registrationpro_coupons extends JTable{
	var $id 			= null;
	var $title			= null;
	var $code 			= null;
	var $discount 		= null;
	var $discount_type	= null;
	var $max_amount 	= null;	
	var $start_date 	= null;
	var $end_date		= null;
	var $published 		= null;	
	var $status 		= null;
	var $eventids 		= null;
	var $checked_out	= null;
	var $checked_out_time = null;	
	var $ordering 		= null;		
	
	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_coupons', 'id', $db );
	}			
}
?>