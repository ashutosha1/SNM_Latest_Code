<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.2.1.1586
 * @date		2013-11-02
 */

defined('JPATH_BASE') or die;

?>

<div class="container-fluid">
<?php

foreach ($this->form->getFieldset($this->currentFieldset->name) as $field)
{
	$tip = empty($field->tip) ? '' : ' title="' . htmlspecialchars($field->label . '::' . $field->tip, ENT_COMPAT, 'UTF-8') . '"';
?>

<div class="control-group">

	<div class="controls">
		<?php echo $field->input; ?>
	</div>
</div>
<?php
}
?>
</div>
