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

<script language="javascript" type="text/javascript">
		
	function chkFrm_validation(pressbutton) {
	
		var form = document.adminForm;
		
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
					
		// do field validation	(added by sdei on 19-Feb)
			if(!validateForm(form,false,false,false,false)){
				return false;
			}else{										
				return true;
			}									
		// end			
	}	
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" onsubmit="return chkFrm_validation();">
	<table cellspacing="4" cellpadding="1" class="adminform">
		<tr><td><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_NOTE');?></td></tr>
	</table><br />
			
	<table cellspacing="4" cellpadding="1" class="adminform">									
	<tr>
	<td>
		<table cellspacing="4" cellpadding="1" width="60%" border="0">									
			<tr>	
				<td valign="top" width="2px"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?></td>
				<td valign="top"><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_NAME'); ?>:</td>
				<td valign="top"><input type="text" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FEEDBACK_NAME_EMPTY'); ?>" size="42" name="fdname" value="<?php echo $_POST['fdname'] ?>"></td>
			</tr>

			<tr>
				<td valign="top">&nbsp;</td>
				<td valign="top"><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_ORGANIZATION_NAME');?>:</td>
				<td valign="top"><input type="text" size="42" name="fdorgname" value="<?php echo $_POST['fdorgname'] ?>"></td>
			</tr>

			<tr>
				<td valign="top"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?></td>
				<td valign="top"><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_EMAIL');?>:</td>
				<td valign="top"><input type="text" size="42" alt="email" emsg="<?php echo JText::_('ADMIN_SCRIPT_FEEDBACK_EMAIL_EMPTY'); ?>" name="fdmail" value="<?php echo $_POST['fdmail'] ?>"></td>
			</tr>

			<tr>
				<td valign="top"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?></td>
				<td valign="top"><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_TYPE');?></td>
				<td valign="top"><textarea alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FEEDBACK_TYPE_EMPTY'); ?>" cols="30" name="fdtype"><?php echo $_POST['fdtype'] ?></textarea></td>
			</tr>

			<tr>
				<td valign="top">&nbsp;</td>
				<td valign="top"><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_PRODUCT');?>:</td>
				<td valign="top"><input type="hidden" size="42" name="fdprod" value="<?php echo JText::_('PRODUCT_NAME').' '.JText::_('PRODUCT_VERSION');?>"><?php echo JText::_('PRODUCT_NAME').' '.JText::_('PRODUCT_VERSION');?></td>
			</tr>

			<tr>
				<td valign="top"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?></td>
				<td valign="top"><?PHP echo JText::_('ADMIN_LBL_FEEDBACK_MESSAGE');?>:</td>
				<td valign="top">				
				<textarea rows="6" cols="30" name="fdmsg" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FEEDBACK_MESSAGE_EMPTY'); ?>"><?php echo $_POST['fdmsg'] ?></textarea></td>
			</tr>
			
			<tr>
				<td valign="top">&nbsp;</td>
				<td>&nbsp;</td>
				<td valign="bottom" align="center">
					<input type="submit" value="Send Message">
					<input type="reset" value="Clear">
				</td>
			</tr>
			
			<tr>
				<td valign="top">&nbsp;</td>
				<td colspan="2"><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></td>
			</tr>
			
		</table>
	</td>
	</tr>
	</table>	
		
	
	<?php echo JHTML::_( 'form.token' ); ?>	
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="version" value="<?php echo JText::_('PRODUCT_NAME').' '.JText::_('PRODUCT_VERSION');?>" />
	<input type="hidden" name="controller" value="commons" />		
	<input type="hidden" name="task" value="sendFeedback" />			
</form>															