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

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="2%" style="text-align:center;"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_FIRSTNAME'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_LASTNAME'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_EMAIL'); ?></th>		
		</tr>
	</thead>
	
	<tbody>
		<?php				
		$k = 0;
		$counter = 1;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];	
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td style="text-align:center;"> <?php echo $checked; ?> </td>
			<td align="left"><?php echo htmlspecialchars($row->firstname, ENT_QUOTES, 'UTF-8');?> </td>
			<td align="left"><?php echo htmlspecialchars($row->lastname, ENT_QUOTES, 'UTF-8');?> </td>
			<td align="left"><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8');?> </td>			
		</tr>
		<?php 
			$k = 1 - $k; 
			$counter++;
		} 
		?>
	
	</tbody>			
	</table>