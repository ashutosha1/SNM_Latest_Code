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

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class registrationpro_sessions extends JTable{
	var $id 			    = null;
	var $event_id		    = null;
	var $title			    = null;
	var $description	    = null;
	var $fee 			    = null;	
	var $feetype 		    = null;
	var $weekday 		    = null;
	var $session_date 	    = null;
	var $session_start_time = null;
	var $session_stop_time  = null;	
	var $page_header	    = null;
	var $published 		    = null;	
	var $ordering 		    = null;		
	
	function __construct( &$db ) {
		parent::__construct( '#__registrationpro_sessions', 'id', $db );
	}			
}
?>