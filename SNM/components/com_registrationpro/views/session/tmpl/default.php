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

$calc_function 	= "calculate_tot_amt()";
$reset_function = "resetform()";
$total_amount_span_id	= "totval";
// echo "<pre>"; print_r($this->row); exit;
?>

<table border="0" cellpadding="2" cellspacing="0" align="center" class="adminform"  width="300px" style="height:auto">		
	<tr>
		<td style="vertical-align:top; text-align:right;" width="150px;"><?php echo JText::_('ADMIN_EVENTS_SESSION_HEADER'); ?></td>			
		<td>		
			<textarea name="session_page_header" id="session_page_header" class="inputbox" style="width:450px; height:120px;"><?php echo $this->row->session_page_header; ?></textarea>		
			<?php 
				// parameters : areaname, content, hidden field, width, height, rows, cols							 
				//echo $this->editor->display( 'page_header',  $this->row->page_header , '70%', '150', '75', '20', array('pagebreak', 'readmore')) ;
			?>
		</td>
	</tr>					
	
	<tr>	
		<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_TITLE'); ?></td>
		<td style="vertical-align:top;"> <input type="text" name="title" id="title" class="inputbox" value="<?php echo $this->row->title; ?>"  style="width:450px;"  /> </td>	
	</tr>
	
	<tr>	
		<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_DESCRIPTION'); ?></td>
		<td style="vertical-align:top;"> <textarea name="description" id="description" class="inputbox" style="width:450px; height:80px;"><?php echo $this->row->description; ?></textarea> </td>	
	</tr>
	
	<!--<tr>	
		<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_WEEKDAY'); ?></td>
		<td style="vertical-align:top;"><?php echo $this->Lists['weekdays']; ?> </td>
	</tr>-->
	
	<tr>
		<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_DATE'); ?> </td>
		<td style="vertical-align:top;">
			<?php
			/* echo JHTML::_('calendar'
					  , $this->row->session_date
					  , 'session_date'
					  , 'session_date'
					  , '%Y-%m-%d'
					  , array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); */
			?><input id="datepicker1" name="session_date" value="<?php echo $this->row->session_date; ?>" size="15" maxlength="10" />
			<b>( <?php echo JText::_('ADMIN_EVENTS_SESSION_DATE_NOTICE'); ?> )</b>
		</td>
	</tr>
	
	<tr>	
		<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_TIME'); ?></td>
		<td style="vertical-align:top;">
			<div style="float:left;"><input type="text" name="session_start_time" id="session_start_time" class="inputbox" size="5" maxlength="5" value="<?php echo $this->row->session_start_time; ?>" ></div>
			<div style="float:left; margin-left:5px; margin-right:5px;"><?php echo JText::_('ADMIN_EVENTS_SESSION_TIME_SAPERATOR'); ?></div>
			<div style="float:left;"><input type="text" name="session_stop_time" id="session_stop_time" class="inputbox" size="5" maxlength="5" value="<?php echo $this->row->session_stop_time; ?>" ></div>
			<div style="float:left; margin-left:5px; margin-right:5px;"><b>( <?php echo JText::_('ADMIN_EVENTS_SESSION_TIME_NOTICE'); ?> )</b></div> 
		</td>
	</tr>			
	
	<tr>
		<td style="vertical-align:top; text-align:right;"><?php echo JText::_('ADMIN_EVENTS_SESSION_FEE'); ?></td>
		<td style="vertical-align:top;"> <input type="text" name="fee" id="fee" class="inputbox" value="<?php echo $this->row->fee; ?>" size="8" maxlength="10" /> </td>		
	</tr>
	
	
																																					
	<tr>
		<td valign="top">&nbsp;</td>
		<td valign="top">										
			<input type="submit" class="button" id="add" value="add" />
			<input type="button" class="button" value="reset" onclick="resetform_session();" />
			<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
		</td>
	</tr>							
</table>