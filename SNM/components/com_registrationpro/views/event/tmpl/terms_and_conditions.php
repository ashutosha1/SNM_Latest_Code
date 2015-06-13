<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<table width="95%" align="center" border="0" cellpadding="0" cellspacing="0">

	<?php foreach($this->rows as $key => $value){ ?>
		<?php
			if(count($this->rows) > 1 && trim($value->terms_conditions) != "") {
		?>
		<tr><td class="regpro_terms_head"><?php echo JText::_("EVENTS_TERMS_CONDTIONS_EVENT_NAME").":- ".$value->titel; ?></td></tr>
		<?php
			}
		?>
		<tr><td><?php echo $value->terms_conditions; ?></td></tr>
		<tr><td>&nbsp;</td></tr>
	<?php
		}
	?>
</table>