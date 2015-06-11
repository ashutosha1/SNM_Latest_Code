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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class registrationpro_dates extends JTable{
	var $id 				= null;
	var $parent_id			= 0;
	var $user_id			= null;
	var $locid 				= null;
	var $catsid 			= null;
	var $dates 				= null;
	var $times 				= null;
	var $enddates 			= null;
	var $endtimes 			= null;	
	var $titel 				= null;
	var $image 				= 0;
	var $pdfimage 			= 0;
	var $datdescription 	= null;
	var $shortdescription 	= null;
	var $datimage 			= null;
	var $sendername 		= null;
	var $sendermail 		= null;
	var $deliverip 			= null;
	var $deliverdate 		= null;
	var $published 			= 1;
	var $registra 			= null;
	var $unregistra 		= null;
	var $notifydate 		= null;
	var $checked_out 		= null;
	var $checked_out_time 	= null;
	var $access 			= 1;
	var $viewaccess 		= 1;
	var $regstart 			= null;
	var $regstarttimes 		= null;
	var $regstop 			= null;
	var $regstoptimes 		= null;	
	var $regstop_type		= null;
	var $reqactivation 		= null;
	var $status 			= null;
	var $form_id 			= null;
	var $max_attendance 	= null;
	var $terms_conditions 	= null;
	var $ordering 			= null;
	var $datarange			= null;
	var $allowgroup			= null;
	var $notifyemails		= null;
	var $shw_attendees 		= null;
	var $recurrence_id		= null;
	var $recurrence_type	= null;
	var $recurrence_number	= null;
	var $recurrence_weekday	= null;
	var $recurrence_counter	= null;
	var $gateway_account 	= null;
	var $force_groupregistration = null;
	var $payment_method 	= null;
	var $moderator_notify 	= null;
	var $moderating_status 	= null;
	var $metadescription 	= null;
	var $metakeywords 		= null;
	var $metarobots 		= null;
	var $session_page_header = null;
	var $enable_mailchimp	 = null;
	var $mailchimp_list		 = null;
	var $enable_create_user	 = null;
	var $enabled_user_group	 = null;

	function __construct( &$db ) {
		parent::__construct( '#__registrationpro_dates', 'id', $db );
	}
}
?>