<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<form class="adminForm">
<div id="cpanel">
<?php 
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_accessmanager'.DS.'views'.DS.'frontend'.DS.'tmpl'.DS.'frontend.php');
?>
</div>
<?php 
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_accessmanager'.DS.'views'.DS.'panel'.DS.'tmpl'.DS.'footnote.php');
?>
</form>
<?php
$this->controller->display_footer();
?>