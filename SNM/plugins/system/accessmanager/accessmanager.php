<?php
/**
* @package plugin Access-Manager (plugin for component Access-Manager)
* @version 1.0.0
* @copyright Copyright (C) 2012 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$ds = DIRECTORY_SEPARATOR;

if(file_exists(JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_system'.$ds.'plugin_system.php')){			
	require_once(JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_system'.$ds.'plugin_system.php');
}

?>