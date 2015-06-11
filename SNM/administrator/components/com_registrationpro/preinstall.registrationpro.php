<?php 
/**
 * @version		v.3.2 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  GNU/GPL		
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
 *
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!class_exists('JURI')) {
	jimport( 'joomla.environment.uri' );
}

$link = JRoute::_('index.php?option=com_registrationpro&install=1');

// check if joomla version is 1.6 or higher
$version = substr(JVERSION,0,3); 

if($version == "1.5"){
	echo "<font color='#FF0000' size='+1'>This version of Event Registration Pro is not compatible with Joomla 1.5. Please visit www.JoomlaShowroom.com for information regarding the correct version for Joomla 1.5.</font>";	
}else{

	$lang =JFactory::getLanguage();
	$lang->load( 'com_registrationpro', JPATH_ROOT . DS . 'administrator' );
	
	$message = JText::sprintf('REGPRO_PREINSTALLER_TEXT', $link);
	
	$html = '
		<div style="width: 100%">		
			<fieldset>
				<div id="regpro_log" style="overflow: auto; max-height: 300px">
					<h2>' . $message . '</h2>
				</div>
			</fieldset>
		</div>
	';
	
	echo $html;
}

?>