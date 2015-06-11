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
jimport('joomla.application.component.model');

class registrationpro_payment extends JTable
{
	var $id 				= null;
	var $regpro_dates_id 	= null;
	var $product_name 		= null;
	var $product_description = null;
	var $product_price 		= null;
	var $tax 				= null;
	var $total_price 		= null;
	var $shipping 			= null;
	var $ordering 			= null;
	var $type 				= "E";
	var $product_quantity	= null;
	var $ticket_start		= null;
	var $ticket_end			= null;
	
	function __construct(&$db) {
		parent::__construct( '#__registrationpro_payment', 'id', $db );
	}
	
}
?>