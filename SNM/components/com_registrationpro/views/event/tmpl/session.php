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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal' );

?>

<div id="regpro">
<?php 
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);
$registrationproHelper = new registrationproHelper; ?>
<form name="regproSessions" id="regproSessions"  action="<?php echo $this->action; ?>" method="post">
<?php
if($this->event_session_header != ""){
?>
<div class='regpro_detailsheading'><?php echo JText::_('EVENTS_SESSION_DETAILS');?></div>
<div class='well' id="event_session_header"><?php echo $this->event_session_header; ?></div>
<?php
}
?>
<table cellpadding="4" cellspacing="0" border="0" width="100%" class="regprocommon">		
	<?php 
		if(count($this->event_session_dates) > 0) {
			$i = 1;
			foreach($this->event_session_dates as $dkey => $dvalue)
			{					
	?>	
	<tr>
		<td colspan="4" class="session_date"> 
			<?php $date = $registrationproHelper->getFormatdate($this->regpro_config['session_dateformat'], $dvalue['session_date']);
				echo $date;			
			?> 
		</td>
	</tr>			
	<?php			
				$k = 0;
				foreach($this->event_sessions as $ekey => $evalue)
				{
					if($dvalue['session_date'] == $evalue['session_date']) {
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td style="text-align:right;">
			<input type="checkbox" name="sessions[]" id="sessions[]" value="<?php echo $evalue['id']; ?>" />
		</td>
		<td><?php echo $evalue['title']; ?></td>
		<td><?php echo $this->regpro_config['currency_sign'].$evalue['fee']; ?></td>
		<td>
			Time : 
			<?php echo $registrationproHelper->getFormatdate($this->regpro_config['session_timeformat'], $evalue['session_start_time']); ?> - <?php echo $registrationproHelper->getFormatdate($this->regpro_config['session_timeformat'], $evalue['session_stop_time']); ?>
		</td>
	</tr>
	<?php
						if(trim($evalue['description']) != "") {
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td>&nbsp;</td>
		<td colspan="3"><?php echo $evalue['description']; ?></td>
	</tr>									
	<?php
						}
						$k = 1 - $k;
					}
				}
				$i++;
			}
		}
	?>		
	
	<tr>	
		<td colspan="4">
			<button name="back" type="button" class="btn btn-primary regpro_button" onclick="history.back();">
					<i class="icon-arrow-left icon-white"></i> <?php echo JTEXT::_('EVENTS_SESSION_PAGE_PREVIOUS_BUTTON'); ?>
			</button>
			
			<button type="submit" name="submit" class="btn btn-primary regpro_button">
					<i class="icon-hand-right icon-white"></i> <?php echo JTEXT::_('EVENTS_SESSION_PAGE_CONTINUE_BUTTON'); ?>
			</button>
						
			<!--<input type="button" value="Previous" class="btn btn-primary regpro_button" onclick="javascript:back();" />												
			<input type="submit" value="Submit" class="btn btn-primary regpro_button" />-->
		</td>
	</tr>		
</table>

<input type="hidden" NAME="option" value="com_registrationpro" />
<input type="hidden" name="step" value="1" /> 																				
<input type="hidden" name="did" value="<?php echo $this->eventid;?>" />
<input type="hidden" NAME="Itemid" value="<?php echo $this->Itemid; ?>" />
<input type="hidden" NAME="rdid" value="<?php echo $this->eventid; ?>" />

<!--------------------------- group registratoin   --------------------->
<?php
	if($_POST['chkgroupregistration']) {
?>
<input type="hidden" name="chkgroupregistration" value="<?php echo $_POST['chkgroupregistration']; ?>" />
<?php
	}
?>

<!--------------------------- Product id   --------------------->
<?php 
	if(is_array($_POST['product_id']) && count($_POST['product_id']) > 0) {
		foreach($_POST['product_id'] as $pidkey => $pidvalue){
?>
<input type="hidden" name="product_id[<?php echo $pidkey; ?>]" value="<?php echo $pidvalue; ?>" /> 	
<?php
		}
		
		if(is_array($_POST['product_qty']) && count($_POST['product_qty']) > 0) {
		foreach($_POST['product_qty'] as $pqtykey => $pqtyvalue){
?>
<input type="hidden" name="product_qty[<?php echo $pqtykey; ?>]" value="<?php echo $pqtyvalue; ?>" /> 	
<?php
		}
		}		
	}
?>

<!--------------------------- Product qty   --------------------->

<?php 
	/*if(is_array($_POST['product_qty']) && count($_POST['product_qty']) > 0) {
		foreach($_POST['product_qty'] as $pqtykey => $pqtyvalue){
?>
<input type="hidden" name="product_qty[]" value="<?php echo $pqtyvalue; ?>" /> 	
<?php
		}
	}*/
?>

<!--------------------------- all Product ids   --------------------->

<?php 
	if(is_array($_POST['productids']) && count($_POST['productids']) > 0) {
		foreach($_POST['productids'] as $pidskey => $pidsvalue){
?>
<input type="hidden" name="productids[]" value="<?php echo $pidsvalue; ?>" /> 	
<?php
		}
	}
?>

<!--------------------------- Additional Product id   --------------------->

<?php 
	if(is_array($_POST['product_id_add']) && count($_POST['product_id_add']) > 0) {
		foreach($_POST['product_id_add'] as $pidaddkey => $pidaddvalue){
?>
<input type="hidden" name="product_id_add[<?php echo $pidaddkey; ?>]" value="<?php echo $pidaddvalue; ?>" /> 	
<?php
		}
		if(is_array($_POST['product_qty_add']) && count($_POST['product_qty_add']) > 0) {
		foreach($_POST['product_qty_add'] as $pqtyaddkey => $pqtyaddvalue){
?>
<input type="hidden" name="product_qty_add[<?php echo $pqtyaddkey; ?>]" value="<?php echo $pqtyaddvalue; ?>" /> 	
<?php
		}
		}					
	}
?>

<!--------------------------- Additional Product qty   --------------------->

<?php 
	/*if(is_array($_POST['product_qty_add']) && count($_POST['product_qty_add']) > 0) {
		foreach($_POST['product_qty_add'] as $pqtyaddkey => $pqtyaddvalue){
?>
<input type="hidden" name="product_qty_add[]" value="<?php echo $pqtyaddvalue; ?>" /> 	
<?php
		}
	}*/
?>

</form>
<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>
</div>