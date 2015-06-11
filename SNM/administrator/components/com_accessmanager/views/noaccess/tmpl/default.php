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
<style>

/*hide system messages on this page*/
#system-message{
	display: none;
}

</style>
<div style="text-align: center; margin-top: 300px;">
<?php echo JText::_($this->message); ?>.
<br /><a href="javascript:history.back()" style="text-decoration: underline;"><?php echo JText::_('COM_ACCESSMANAGER_BACK'); ?></a>
</div>