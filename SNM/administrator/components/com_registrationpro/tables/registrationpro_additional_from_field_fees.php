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

class registrationpro_additional_from_field_fees extends JTable{
	var $id 					= null;
	var $reg_id 				= null;
	var $additional_field_name	= null;
	var $additional_field_fees 	= null;
	var $type 					= null;	
	
	function __construct( &$db )
	{
		parent::__construct( '#__registrationpro_additional_from_field_fees', 'id', $db);
	}			
}
?>