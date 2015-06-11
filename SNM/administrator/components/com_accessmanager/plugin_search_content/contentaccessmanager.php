<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die;

$ds = DIRECTORY_SEPARATOR;

if(file_exists(JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_search_content'.$ds.'plugin_search_content.php')){			
	require_once(JPATH_ADMINISTRATOR.$ds.'components'.$ds.'com_accessmanager'.$ds.'plugin_search_content'.$ds.'plugin_search_content.php');
}

?>