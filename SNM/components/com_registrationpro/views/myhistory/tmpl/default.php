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
JHTML::_('behavior.tooltip');
?>

<div id="regpro" class="events_history">

	<h3><?php echo JText::_('EVENTS_HISTORY_TITLE'); ?></h3>

	<?php
		$uid = $this->user->get('id');
		if($uid == 0) {
			echo "<div style=\"margin:50px 30px;\">\n";
			echo "<p><b>You are Not Logged In to the system.</b></p>\n";
			echo "<p>To see your Events Registration History, you need to Login first...</p>\n";
			echo "</div>\n";
		} else {
			$ev_cnt = count($this->rows);
			if($ev_cnt == 0) {
				echo "<div style=\"margin:30px 30px;\">\n";
				echo "<p>You have not been registered to any event.</p>\n";
				echo "</div>\n";
			} else {
			?>

			<div class="regpro_outline" id="regpro_outline">
			<table class="table table-striped" width=100% border=0 cellspacing=0 cellpadding=0>
			<thead>
			<tr>
				<th width=4% style="text-align:center;"><?php echo JText::_('EVENTS_HISTORY_TITLE_NUM'); ?></th>
				<th width=20% style="text-align:center;"><?php echo JText::_('EVENTS_HISTORY_TITLE_DATE'); ?></th>
				<th width=21%><?php echo JText::_('EVENTS_HISTORY_TITLE_EVENT'); ?></th>
				<th width=15% style="text-align:center;"><?php echo JText::_('EVENTS_HISTORY_TITLE_EVENT_DATES'); ?></th>
				<th width=20% style="text-align:center;"><?php echo JText::_('EVENTS_HISTORY_TITLE_ON_NAME'); ?></th>
				<th width=20% style="text-align:center;"><?php echo JText::_('EVENTS_HISTORY_TITLE_ON_EMAIL'); ?></th>
			</tr>
			</thead>
			<tbody>
			
			<?php
				$cnt = 1;
				foreach($this->rows as $row) {
					$ev_id = $row['rdid'];
					$ev_regdate = date("Y-m-d H:i:s", $row['uregdate']);
					
					$ev_title = 'N/A';
					if(@isset($row['titel']) && ($row['titel'] != '-')) $ev_title = $row['titel'];
					
					$ev_dates = 'N/A';
					if(@isset($row['dates']) && @isset($row['times'])) $ev_dates = $row['dates'] . ' ' . $row['times'];
					if(@isset($row['enddates']) && @isset($row['endtimes']) && ($row['enddates'] != '-') && ($row['endtimes'] != '-')) {
						$ev_dates = $ev_dates . ' - ' . $row['enddates'] . ' ' . $row['endtimes'];
					}
					
					$ev_name = $row['firstname'] . ' ' . $row['lastname'];
					$ev_email = $row['email'];
					
					echo "<tr>\n";
					echo "	<td style=\"text-align:center;\">$cnt</td>\n";
					echo "	<td style=\"text-align:center;\">$ev_regdate</td>\n";
					echo "	<td>$ev_title</td>\n";
					echo "	<td style=\"text-align:center;font-size:10px;line-height:12px;\">$ev_dates</td>\n";
					echo "	<td style=\"text-align:center;\">$ev_name</td>\n";
					echo "	<td style=\"text-align:center;\">$ev_email</td>\n";
					echo "</tr>\n";
					$cnt++;
				}
			?>
			
			</tbody>
			</table>
			
			<?php // echo "<pre>"; print_r($this->rows); echo "</pre>";
			}
		}
	?>
	
	<?php
		$regpro_header_footer = new regpro_header_footer;
		$regpro_header_footer->regpro_footer($this->regpro_config);
	?>
	
</div>