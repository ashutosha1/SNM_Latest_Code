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

$ds = DIRECTORY_SEPARATOR;

?>
<form class="adminForm">
	<?php if (!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>	
	<div id="j-main-container"<?php echo empty($this->sidebar) ? '' : ' class="span10"'; ?>>
		<div class="clr"> </div><!-- needed for some admin templates -->
		<div id="cpanel">
		<?php 
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'views'.$ds.'accessvievving'.$ds.'tmpl'.$ds.'accessvievving.php');
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'views'.$ds.'accessedit'.$ds.'tmpl'.$ds.'accessedit.php');
		?>
		</div>
		<?php 
		require_once(JPATH_ROOT.$ds.'administrator'.$ds.'components'.$ds.'com_accessmanager'.$ds.'views'.$ds.'panel'.$ds.'tmpl'.$ds.'footnote.php');
		?>
	</div>
</form>
<?php
$this->controller->display_footer();
?>