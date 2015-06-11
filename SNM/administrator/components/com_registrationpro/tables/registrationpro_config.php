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

class registrationpro_config extends JTable
{

	var $id = null;
	var $setting_name = null;
	var $setting_value = null;

	function __construct(&$db)
		{
		parent::__construct( '#__registrationpro_config', 'id', $db );
		}
	
}
?>