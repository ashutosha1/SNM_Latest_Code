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

class registrationpro_transactions extends JTable {
	var $id 					= null;
	var $reg_id 				= null;
	var $p_id  					= null;
	var $type					= null;
	var $payment_method 		= null;
	var $mc_gross 				= null;
	var $address_status 		= null;
	var $payer_id 				= null;
	var $tax 					= null;
	var $tax_amount				= null;
	var $address_street 		= null;
	var $payment_date 			= null;
	var $payment_status 		= null;
	var $charset 				= null;
	var $address_zip 			= null;
	var $first_name 			= null;
	var $address_country_code 	= null;
	var $address_name 			= null;
	var $notify_version 		= null;
	var $custom 				= null;
	var $payer_status 			= null;
	var $address_country 		= null;
	var $address_city 			= null;
	var $quantity 				= null;
	var $quantity_gross 		= null;
	var $verify_sign 			= null;
	var $payer_email 			= null;
	var $txn_id 				= null;
	var $payment_type 			= null;
	var $last_name 				= null;
	var $address_state 			= null;
	var $pending_reason 		= null;
	var $receiver_email 		= null;
	var $txn_type 				= null;
	var $item_name 				= null;
	var $mc_currency 			= null;
	var $item_number 			= null;
	var $residence_country 		= null;
	var $test_ipn 				= null;
	var $coupon_code			= null;
	var $discount_type			= null;
	var $discount				= null;
	var $price_without_tax 		= null;
	var $price 					= null;
	var $final_price			= null;
	var $payment_gross 			= null;
	var $shipping 				= null;
	var $accesskey 				= null;
	var $cart_order_id 			= null;
	var $order_number 			= null;
	var $payer_phone 			= null;
	var $ip_country 			= null;
	var $md5key 				= null;
	var $AdminDiscount			= null;

	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_transactions', 'id', $db );
	}
}
?>