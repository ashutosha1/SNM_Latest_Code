<?php
/**
* @version v.3.2	
* @package Registration Pro category event
* @copyright (C) 2009 Joomlashowroom.com
* @license GPL 2009 Joomlashowroom.com 
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

error_reporting(E_ALL ^ E_NOTICE); // show all error execpt notice errors

//jimport( 'joomla.html.parameter.element' );

class JElementRegprocatlist extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Regprocatlist';	
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();
		
		$query = "SELECT id, catname"
		. "\n FROM #__registrationpro_categories"
		. "\n WHERE publishedcat = 1";
		
		$db->setQuery( $query );
		$eventcat = $db->loadObjectList();
		
		//echo "<pre>";print_r($options); exit;	
		
		//$event_status[] = JHTML::_('select.option',  '0', JText::_(_ADMIN_EVENTS_STATUS_0), 'id', 'title' );
		
		array_unshift($eventcat, JHTML::_('select.option', '0', '- Select Event Category -', 'id', 'catname'));

		return JHTML::_('select.genericlist',  $eventcat, ''.$control_name.'['.$name.']', 'class="'.$class.'"', 'id', 'catname', $value, $control_name.$name );
						
		//echo "<pre>";print_r($selectedvalues); exit;		
		
		//return JHTML::_('select.genericlist',   $eventcat, 'eventcat[]', 'class="inputbox" multiple="multiple"', 'id', 'catname', $selectedvalues, 'eventcat' );
	}
}///