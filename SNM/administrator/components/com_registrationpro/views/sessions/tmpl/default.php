<?php
/**
* @version		v.3.2 registrationpro $
* @package		registrationpro
* @copyright	Copyright © 2009 - All rights reserved.
* @license  	GNU/GPL		
* @author		JoomlaShowroom.com
* @author mail	info@JoomlaShowroom.com
* @website		www.JoomlaShowroom.com
*
*/

defined('_JEXEC') or die('Restricted access');
?>
<span class="span12 y-offset">
	<a class="toolbar btn btn-small btn-success pull-right" id="editlink_session" href="javascript:void(0);">
		<?php echo JText::_('ADMIN_EVENTS_SESSION_EDIT');?>
	</a>
	<a class="toolbar btn btn-small btn-danger pull-right" id="removelink_session" href="javascript:void(0);">
		<?php echo JText::_('ADMIN_EVENTS_SESSION_REMOVE');?>
	</a>
</span>
<span class="span12 y-offset no-gutter">
<table class="table_tickets">
	<tr id="table_tickets_header">
	<td width="10px"><input type="checkbox" name="toggle" value="" onClick="sessioncheckAll(<?php echo count( $this->rows); ?>);" /></td>
	<td width="250px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_TITLE');?></strong></td>
	<td width="300px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION');?></strong></td>
	<td width="200px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_FEE');?></strong></td>
	<td width="200px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DAY');?></strong></td>
	<td width="5px" colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_ORDER');?></strong></td>
	</tr>

	<?php
		$n = count($this->rows);
		$i = 0;
		$k = 0;
		foreach ($this->rows as $session) {
		?>
			<tr>
				<td><input id="cb_ss<?php echo $i?>" type="checkbox" onclick="Joomla.isChecked(this.checked);" value="<?php echo $session->id?>" name="cid[]"></td>
				<td style="text-align:center"><?php echo $session->title;?></td>
				<td style="text-align:center"><?php echo $session->description;?></td>
				<td style="text-align:center">
					<?php
						if($session->feetype == 'A') {
							echo $this->regpro_config['currency_sign']."&nbsp;".$session->fee;
						} else echo $session->fee."&nbsp;%";
					?>
				</td>

				<td style="text-align:center">
					<?php
					$registrationproHelper = new registrationproHelper;
					echo $registrationproHelper->getFormatdate($this->regpro_config['session_dateformat'], $session->session_date);?> <br/>
					<?php echo $registrationproHelper->getFormatdate($this->regpro_config['session_timeformat'], $session->session_start_time);?> - <?php echo $registrationproHelper->getFormatdate($this->regpro_config['formattime'], $session->session_stop_time);?>
				</td>

				<td style="text-align:right">
				<?php if($i>0) echo "<a href=\"javascript:void(0);\" id=\"orderupsessions\" onclick=\"return session_uporder('cb_ss".$i."');\"><img src=\"".REGPRO_ADMIN_IMG_PATH."/uparrow.png\" width=12 height=12 border=0></a>";?>
				</td>
				<td style="text-align:left">
				<?php if($i<($n-1)) echo "<a href=\"javascript:void(0);\" id=\"orderdownsessions\" onclick=\"return session_downorder('cb_ss".$i."');\"><img src=\"".REGPRO_ADMIN_IMG_PATH."/downarrow.png\" width=12 height=12 border=0></a>";?>
				</td>

			</tr>
		<?php
			$i++;
		}

		if(count($this->rows) <= 0) echo "<tr><td colspan=20 style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
		if(intval($this->event_id) > 0) echo "<input type='hidden' name='event_id' value='".$this->event_id."' />";
	?>
</table>
</span>