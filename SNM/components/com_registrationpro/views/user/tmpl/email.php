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

//create the toolbar
JToolBarHelper::title( '<img src="'.REGPRO_ADMIN_IMG_PATH.'/emails_small.png" align="absmiddle" border="0">' .JText::_( 'ADMIN_SEND_EMAIL_TO_REGISTERUSERS' ), 'usersedit' );
JToolBarHelper::custom('send_email_to_user', 'send.png', 'send_f2.png', 'Send', false, true);
JToolBarHelper::divider();
JToolBarHelper::cancel('user_cancel','Cancel','cancel.png','Cancel');
JToolBarHelper::divider();
JToolBarHelper::help( 'screen.registrationpro', true );
//JToolBarHelper::preview();

//echo "<pre>"; print_r($this->user_values);

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

			<table width="100%" border="0" cellpadding="4" cellspacing="0" class="adminForm">				
				<tr align="center" valign="middle">	
					<td align="left" valign="top" width="10%"><b><?php echo JText::_('ADMIN_EMAIL_TO_REGISTERUSERS_SUBJECT'); ?></b></td>	
					<td align="left" valign="top" width="40%">
						<input type="text" name="emailtoregistersubject" value="<?php echo $this->regpro_config['emailtoregistersubject']; ?>" size="60">
					</td>		
					<td align="left" valign="top" rowspan="2"><?php echo JText::_('ADMIN_EMAIL_TO_REGISTERUSERS_DESC'); ?></td>
				</tr>
		  
			  <tr align="center" valign="middle">	
				<td align="left" valign="top"><b><?php echo JText::_('ADMIN_EMAIL_TO_REGISTERUSERS_BODY'); ?></b></td>	
				<td align="left" valign="top">	
				<?php	
				 // parameters : areaname, content, hidden field, width, height, rows, cols	
				echo $this->editor->display( 'emailtoregisterbody', $this->regpro_config['emailtoregisterbody'], '100%;', '280', '70', '70' , array('pagebreak','readmore') );								
				?></td>
			  </tr>
			</table>  

			<?php
				foreach($this->emailids as $key=>$value){
					echo '<input type="hidden" name="emailIds[]" value="'.$this->emailids[$key]->email.'"/>';
				}		
			?>
			
			<?php echo JHTML::_( 'form.token' ); ?>	
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="users" />	
			<input type="hidden" name="task" value="send_email_to_users" />
			<input type="hidden" name="rdid" value="<?php echo $this->event_id; ?>" />						  		
</form>