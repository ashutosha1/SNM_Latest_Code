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

//echo "<pre>"; print_r($this->user_values);
?>
<div id="regpro">
<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();

$regpro_html->backbutton_toolbar();
?>

<script language="javascript" type="text/javascript">

function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
									
	// do field validation (added by sdei on 23-Jan)
	if(!validateForm(form,false,false,false,false)){
		
	}else{		
		submitform( pressbutton );
	}								
	// end			
}

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table border="0" cellspacing="0" cellpadding="4" align="center" class="adminform">
		<tr>
			<td>
				<table border="0" cellspacing="0" cellpadding="4" align="center">		 												
	<?php
		if(is_array($this->user_values) && !count($this->user_values) > 0){
			
		}else{
													
			foreach($this->user_values as $key=>$value)			
			{
				if($key == 'firstname'){
					$scriptvalidation = "alt='blank' emsg='".JText::_('ADMIN_SCRIPT_FIRSTNAME_NOT_EMPTY')."'";
					$mandatoryflag = JText::_('ADMIN_MANDATORY_SYMBOL');
				}elseif($key == 'lastname'){
					$scriptvalidation = "alt='blank' emsg='".JText::_('ADMIN_SCRIPT_LASTNAME_NOT_EMPTY')."'";
					$mandatoryflag = JText::_('ADMIN_MANDATORY_SYMBOL');
				}elseif($key == 'email'){
					$scriptvalidation = "alt='email' emsg='".JText::_('ADMIN_SCRIPT_EMAIL_NOT_VALID')."'";
					$mandatoryflag = JText::_('ADMIN_MANDATORY_SYMBOL');
				}else{
					$scriptvalidation 	= "";
					$mandatoryflag 		= "";
				}				
	?>		
				<tr>
					<td width="10%" style="text-align:right"><?php echo $mandatoryflag;  ?></td>
					<td width="10%"><?php echo ucfirst(str_replace("cb_","",$key));  ?></td> 
					<td style="width:10px"><img src="images/blank.png" border="0" width="0"  /></td>
					<td> <input type="text" name="form[<?php echo $key; ?>][][0]" value="<?php echo $value;?>" <?php echo $scriptvalidation; ?> size="35" class="regpro_inputbox"/>  </td>
				</tr>
													
	<?php 	} 
		}
	?>			
				<tr>
					<td colspan="3"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/blank.png" border="0" width="0"  /></td>
					<td><input class="regpro_button" type="submit" value="<?php echo JText::_('ADMIN_USERS_EDIT_SAVE'); ?>" /></td>
				</tr>
				</table>
			</td>
		</tr>				
		<tr><td height="8px"> <img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/blank.png" border="0" width="0"  /></td></tr>																												
		</table>
		<div><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></div>
					
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="users" />	
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="rid" value="<?php echo $this->row->rid; ?>" />
	<input type="hidden" name="rdid" value="<?php echo $this->event_id; ?>" />	
</form>		

<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>
</div>