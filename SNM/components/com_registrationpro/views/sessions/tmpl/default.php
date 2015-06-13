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

$n = count($this->rows);
$i=0;
$k = 0;

$toggle_func 		= "sessioncheckAll";
$ordering_up_func 	= "session_uporder";
$ordering_down_func = "session_downorder";

?>
<table border="1" cellpadding="2" cellspacing="0" align="center" class="adminform">
	<tr>
	<td width="10px"><input type="checkbox" name="toggle" value="" onClick="<?php echo $toggle_func; ?>(<?php echo count( $this->rows); ?>);" /></td>
	<td width="250px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_TITLE'); ?></strong></td>										
	<td width="300px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION'); ?></strong></td>
	<td width="200px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_FEE'); ?></strong></td>			
	<td width="200px" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_DAY'); ?></strong></td>			
	<td width="5px" colspan="2" style="text-align:center"><strong><?php echo JText::_('ADMIN_EVENTS_SESSION_ORDER'); ?></strong></td>			
	</tr>

	<?php
	$n = count($this->rows);
	$i=0;
	$k = 0;
												
	foreach ($this->rows as $session)
	{						
		$pchecked 	= JHTML::_('grid.checkedout',   $session, $i );
	?>

	<tr> 												
		<td><?php echo $pchecked;?></td>
		<td style="text-align:center"><?php echo $session->title;?></td>
		<td style="text-align:center"><?php echo $session->description;?></td>
		<td style="text-align:center">									
			<?php
				if($session->feetype == 'A'){
					echo $this->regpro_config['currency_sign']."&nbsp;".$session->fee;
				}else{
					echo $session->fee."&nbsp;%";
				}
			?>
		</td>
		
		<td style="text-align:center">
			<!--<?php echo $session->weekday; ?> <br/> <?php echo registrationproHelper::getFormatdate($this->regpro_config['formatdate'], $session->session_date); ?>--> 
			<?php echo registrationproHelper::getFormatdate($this->regpro_config['session_dateformat'], $session->session_date); ?> <br/>
			<?php echo registrationproHelper::getFormatdate($this->regpro_config['session_timeformat'], $session->session_start_time); ?> -  <?php echo registrationproHelper::getFormatdate($this->regpro_config['formattime'], $session->session_stop_time); ?>
		</td>
		
		<td style="text-align:right">												
		<?php 
			if ($i > 0) { ?>
				<a href="javascript: void(0);" id="orderupsessions" onclick="return <?php echo $ordering_up_func;?>('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/uparrow.png" width="12" height="12" border="0" alt="orderup"> </a>
		  <?php	
			} ?>
		</td>
		<td style="text-align:left"><?php
			if ($i < $n-1) { ?>
				<a href="javascript: void(0);" id="orderdownsessions" onclick="return <?php echo $ordering_down_func;?>('cb<?php echo $i;?>');"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/downarrow.png" width="12" height="12" border="0" alt="orderdown"> </a>
		  <?php		
			}?>
		</td>
														
	</tr>
	<?php						
		$i++;																								
	}
	
	if(count($this->rows) <= 0){
		echo "<tr><td colspan='6' style='text-align:center'>".JText::_('ADMIN_NO_RECORD_FOUND')."</td></tr>";
	}
		
	if(intval($this->event_id) > 0){
		echo "<input type='hidden' name='event_id' value='".$this->event_id."' />";
	}
		
	?>	
										
</table>