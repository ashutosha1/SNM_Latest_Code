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
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT); // show all error execpt notice errors
// no direct access
defined('_JEXEC') or die('Restricted access');

function getEventName($id)
{
	$db = JFactory::getDBO();
	
	$db->setQuery("SELECT titel FROM #__registrationpro_dates WHERE id='".(int) $id."' LIMIT 1");
	
	$eventname = $db->loadResult();
	
	$arr_replace_chars = array(" ",";","'",",","\"","`",".");
	
	$eventname = str_replace($arr_replace_chars,"-",$eventname);
	
	$eventname = html_entity_decode ($eventname);	
	
	return $eventname;
}

function registrationproBuildRoute(&$query)
{

	$segments = array();
	
	//echo "<pre>"; print_r($query);
		
	if (isset($query['view'])){
				
		switch($query['view']) {			
			case 'event':			
				$segments[] = $query['view'];
				$segments[] = $query['did'];				
				$segments[] = getEventName($query['did']);
				unset($query['did']);
				break;
			default :
				$segments[] = $query['view'];			
		}
		
		unset($query['view']);		
	}
			
	if (isset($query['controller'])){
		$segments[] = $query['controller'];
		unset($query['controller']);
		
		
		if (isset($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}
	
	if(isset($query['tmpl']))
	{
		/*$segments[] = $query['tmpl'];
		unset($query['tmpl']);*/	
		if($query['layout'] != 'printevent') {		
			$segments[] = $query['tmpl'];
			unset($query['tmpl']);	
		}			
	}
	
	if(isset($query['did']))
	{
		$segments[] = $query['did'];
		unset($query['did']);				
	}
	
	if(isset($query['shw_attendees']))
	{		
		$segments[] = $query['shw_attendees'];
		unset($query['shw_attendees']);				
	}
	
	if(isset($query['registerid']))
	{
		$segments[] = $query['registerid'];
		unset($query['registerid']);					
	}
	
	if(isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);					
	}
	
	if(isset($query['cid']))
	{
		$segments[] = $query['cid'];
		unset($query['cid']);					
	}
									
	//echo "<pre>"; print_r($segments);

	return $segments;
}

function registrationproParseRoute($segments)
{

	$vars = array();

	//echo "<pre>"; print_r($segments); exit;
	
	// Count route segments
	$count = count($segments);

	if ($count > 0){
		// task
		$view = $segments[0];
		$vars["view"] = $view;
		
		if(!$vars["view"]) {
			$controller = $segments[0];
			$task 		= $segments[1];	
		}	
				
		switch 	($view){
			case "events":
				$vars['view']="events";
				break;
				
			case "event":
				$vars['view'] = "event";		
				$vars['did']= $segments[1];		
				$vars['eventname']= $segments[2];				
												
				if(count($segments) > 3){
					$vars['shw_attendees']= $segments[3];
				}
				
				break;
				
			case "cart";	
				$vars['view'] ="cart";
				$vars['controller'] = $segments[0];				
				$vars['task'] = $segments[1];
				
				if(count($segments) > 2){
					$vars['did']= $segments[2];
				}
				
				if(count($segments) > 3){
					$vars['registerid']= $segments[3];	
				}													
				break;
				
			case "attendees":
				$vars['view']="attendees";				
				$vars['tmpl']= $segments[1];
				$vars['did']= $segments[2];												
				break;
			
			case "category":
				$vars['view']="category";								
				$vars['id']= $segments[1];												
				break;
				
			case "day":
				$vars['view']="day";								
				$vars['id']= $segments[1];												
				break;	
			
			case "myevent":
				$vars['view'] 	= "myevent";		
				$vars['cid']	= $segments[1];										
				break;
			
			case "form":
				$vars['view'] 	= "form";		
				$vars['id']		= $segments[1];										
				break;
				
			case "mycategory":
				$vars['view'] 	= "mycategory";		
				$vars['id']		= $segments[1];										
				break;
				
			case "mylocation":
				$vars['view'] 	= "mylocation";		
				$vars['id']		= $segments[1];										
				break;											
			
			case "field":
				$vars['view'] 	= "field";		
				$vars['id']		= $segments[1];										
				break;	
		}		
		
		if(!$vars["view"]) {
			switch 	($controller){
				case "cart":
					switch($task){
						case "cart":
						$vars["controller"] = $segments[0];
						$vars["task"]		= $segments[1];
						
						if(count($segments) > 2){
							$vars['did']= $segments[2];
						}
						
						if(count($segments) > 3){
							$vars['registerid']= $segments[3];	
						}						
						break;
					}				
					break;			
			}
		}					
	}
				
	//echo "<pre>"; print_r($vars);

	return $vars;
}
?>