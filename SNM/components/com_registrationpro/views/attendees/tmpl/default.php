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

JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.modal' );

$registrationproHelper = new registrationproHelper;
if($this->eventInfo->shw_attendees == 1) {

?>
<!--<link rel="stylesheet" href="components/com_registrationpro/assets/css/regpro.css" type="text/css"  />-->

<table width="100%">
<tr><td>&nbsp;</td></tr>
	<tr>
		
		<td   class="componentheading"> <?php echo $this->eventInfo->titel; ?> </td>
	</tr>
    <tr><td>&nbsp;</td></tr>
	<tr>				
		<td><strong> <?php echo JText::_('ADMIN_EVENTS_DATE'); ?></strong> </td>
		<td><strong> <?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $this->eventInfo->dates);?> &nbsp; to &nbsp;	<?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $this->eventInfo->enddates); ?>
</strong>	</td>
</tr>
<tr><td>&nbsp;</td></tr>
</table>
 
<table class="attendeelist" cellspacing="1">
	<thead >
		<tr>
			<th width="2%"><?php echo JText::_( 'Num' ); ?></th>
			<th><?php echo JText::_('EVENTS_ATTENDDES_FIRST_NAME'); ?></th>
			<th><?php echo JText::_('EVENTS_ATTENDDES_LAST_NAME'); ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		if(count( $this->rows )>0){
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = $this->rows[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="left"><?php echo $row->firstname; ?> </td>
			<td align="left"><?php echo $row->lastname; ?> </td>
		</tr>
		<?php $k = 1 - $k; } }else{ ?>
		
		<tr><td align="center" colspan="5"><?php echo JText::_('EVENTS_ATTENDDES_NOT_FOUND'); ?></td></tr>
		<?php }?>
	</tbody>
    <thead>
    <th width="2%">&nbsp;</th>
	<th  >&nbsp;</th>
	<th >&nbsp;</th>
    </thead>
</table>

<?php
}else{
	echo JText::_('EVENTS_ATTENDDES_ACCESS_DENIED');
}
?>
