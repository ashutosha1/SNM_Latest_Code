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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper

jimport('joomla.application.component.helper');

class registrationproHelper
{
	function showArray($arr) {
		if($arr) {
			echo "<pre>\n";
			print_r($arr);
			echo "</pre>\n";
		}
	}

}
?>