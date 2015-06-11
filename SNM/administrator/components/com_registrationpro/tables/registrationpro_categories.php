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

class registrationpro_categories extends JTable
{
	var $id 			  = null;
	var $parentid		  = null;
	var $user_id 		  = null;
	var $catname 		  = null;
	var $catdescription   = null;
	var $image 			  = null;
	var $background 	  = null;
	var $publishedcat 	  = null;
	var $checked_out 	  = null;
	var $checked_out_time = null;
	var $access 		  = 1;
	var $ordering 		  = null;
	
	function __construct(&$db) {
		parent::__construct( '#__registrationpro_categories', 'id', $db );
	}
}
?>