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
JHtml::_('formbehavior.chosen', 'select');
?>

<script language="javascript" type="text/javascript">	


</script>

<table cellpadding="4" cellspacing="0" border="0"class="adminlist">

	<?php
		if(!is_array($this->row)){
	?>
	
	<tr><td colspan="2"><?php echo JText::_('ADMIN_NO_RECORD_FOUND'); ?></td> </tr>
	<?php
		}else{
			$arrdata	= array();
			$data 		= array();
			
			if($this->row['inputtype'] == "state") {
				$arrdata = explode(",",REGPRO_STATES);
				foreach($arrdata as $key=>$value) {
					$data[trim($value)] = trim($value);				
				}
			}elseif($this->row['inputtype'] == "country"){
				$arrdata = explode(",",REGPRO_COUNTRIES);
				foreach($arrdata as $key=>$value) {
					$data[trim($value)] = trim($value);				
				}
			}else{
				$arrdata = explode(",",$this->row['values']);
				foreach($arrdata as $key=>$value) {
					$data[trim($value)] = trim($value);				
				}
			}			
			//echo "<pre>"; print_r($data); exit;				
			
			if(is_array($data) && count($data) > 0) {
				foreach($data as $dkey => $dvalue)
				{
	?>
			<tr>
				<td valign="top" width="10%"> <input type="checkbox" name="conditional_field_values[]" value="<?php echo $dkey; ?>" /> </td>
				<td valign="top" width="90%"><?php echo ucfirst($dvalue); ?></td>		
			</tr>	
	
	<?php
				}
			}
		}
	?>
					
</table>			