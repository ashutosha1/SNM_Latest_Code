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
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldRegproLocations extends JFormField
{

	protected $type = 'RegproLocations';
   
   // protected function fetchElement($type, $value, &$node, $control_name)
   protected function getInput()
   {	
		// Initialize variables.
		$session = JFactory::getSession();
		$options = array();
		
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
		$attr .= ' disabled="disabled"';
		}
		
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		
		$db = &JFactory::getDBO();

       //build the list of locations
		$query = 'SELECT a.club AS text, a.id AS value'
		. ' FROM #__registrationpro_locate AS a'
		. ' WHERE a.publishedloc = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$all_locations = $db->loadObjectList();
		
		$locations		= array();
		$locations[] 	= JHTML::_('select.option',  '0', JText::_('Select Location'));		
		$locations 		= array_merge( $locations, $all_locations);
						
		return JHTML::_('select.genericlist',  $locations, $this->name, trim($attr), 'value', 'text', $this->value );
		//return JHTML::_('select.genericlist',  $locations, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}
	
	
	
	/*var	$_name = 'RegproLocations';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();

       //build the list of categories
		$query = 'SELECT a.club AS text, a.id AS value'
		. ' FROM #__registrationpro_locate AS a'
		. ' WHERE a.publishedloc = 1'
		. ' ORDER BY a.ordering';
		$db->setQuery( $query );
		$all_locations = $db->loadObjectList();
		
		$locations		= array();
		$locations[] 	= JHTML::_('select.option',  '0', JText::_('Select Location'));		
		$locations 	= array_merge( $locations, $all_locations);
				
		return JHTML::_('select.genericlist',  $locations, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}*/
}
?>