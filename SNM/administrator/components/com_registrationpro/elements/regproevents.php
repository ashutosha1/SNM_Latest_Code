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

class JFormFieldRegproevents extends JFormField
{
	
	protected $type = 'RegproEvents';
   
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

       //build the list of categories
		$query = 'SELECT a.titel AS text, a.id AS value'
		. ' FROM #__registrationpro_dates AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.titel';
		$db->setQuery( $query );
		$all_events = $db->loadObjectList();
		
		if(is_array($all_events) && count($all_events) > 0) {		
			foreach($all_events as $ekey => $evalue)
			{
				$evalue->text = $evalue->text." (".$evalue->value.")";
			}	
		}
		
		$events		= array();
		//$events[] 	= JHTML::_('select.option',  '0', JText::_('Select Event'));		
		$events 	= array_merge( $events, $all_events);
		
		//return JHTML::_('select.genericlist',  $events, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
		
		return JHTML::_('select.genericlist',  $events, $this->name, trim($attr), 'value', 'text', $this->value );
	}

	/*var	$_name = 'RegproEvents';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();

       //build the list of categories
		$query = 'SELECT a.titel AS text, a.id AS value'
		. ' FROM #__registrationpro_dates AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.titel';
		$db->setQuery( $query );
		$all_events = $db->loadObjectList();
		
		if(is_array($all_events) && count($all_events) > 0) {		
			foreach($all_events as $ekey => $evalue)
			{
				$evalue->text = $evalue->text." (".$evalue->value.")";
			}	
		}
		
		$events		= array();
		//$events[] 	= JHTML::_('select.option',  '0', JText::_('Select Event'));		
		$events 	= array_merge( $events, $all_events);
				
		return JHTML::_('select.genericlist',  $events, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}*/
}
?>