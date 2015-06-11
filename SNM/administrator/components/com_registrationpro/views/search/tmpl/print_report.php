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
defined('_JEXEC') or die('Restricted access');
?>

<div style="text-align:right;margin-right:50px;padding-top:20px;">
	<a href="javascript:window.print();"><img src="<?php  echo REGPRO_ADMIN_IMG_PATH; ?>/printreport.png" border="0" title="Print Report" alt="Print Report" align="absmiddle"/></a>
</div><br/>

<form action="index.php" method="post" name="adminForm" >
	<table class="adminlist table" style="margin-left:15px;width:97%;">
	<thead>
		<tr>
			<th width="2%" style="text-align:center;"><?php echo JText::_( 'Num' ); ?></th>
			<th width="20%"><?php echo JText::_('ADMIN_SEARCH_RESULT_EVENT_NAME'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_FIRSTNAME'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_LASTNAME'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_EMAIL'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_LOCATION'); ?></th>
			<th width="10%"><?php echo JText::_('ADMIN_SEARCH_RESULT_CATEGORY'); ?></th>
			<th width="10%"><?php echo JText::_( 'ADMIN_SEARCH_RESULT_EVENT_DATE' ); ?></th>
			<th><?php echo JText::_('ADMIN_EVENT_LIST_TICKETS_COLUMN');?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		$counter = 1;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center" style="text-align:center;"><?php echo $counter; ?></td>
			<td align="center"><?php echo htmlspecialchars($row->titel, ENT_QUOTES, 'UTF-8'); ?> </td>
			<td align="center"><?php echo htmlspecialchars($row->firstname, ENT_QUOTES, 'UTF-8');?> </td>
			<td align="center"><?php echo htmlspecialchars($row->lastname, ENT_QUOTES, 'UTF-8');?> </td>
			<td align="center"><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8');?> </td>
			<td align="center"><?php echo htmlspecialchars($row->club, ENT_QUOTES, 'UTF-8'); echo " ",htmlspecialchars($row->city, ENT_QUOTES, 'UTF-8');?></td>
			<td align="left"><?php echo htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8'); ?> </td>
			<td align="center"><?php $registrationproHelper = new registrationproHelper; echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->dates);?></td>
			<td align="center"><?php foreach($row->tickets as $key=>$val) echo $val->item_name."<br/>";?></td>
		</tr>
		<?php
			$k = 1 - $k;
			$counter++;
		}
		?>
		<tr><td colspan=20>&nbsp;</td></tr>
	</tbody>
 </table>
</form>