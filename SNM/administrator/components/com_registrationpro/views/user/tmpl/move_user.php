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
JToolBarHelper::title(JText::_( 'ADMIN_MOVE_USERS_TITLE' ), 'usersedit' );
JToolBarHelper::custom('save_move_users', 'save.png', 'save_f2.png', 'Save', false, true);
JToolBarHelper::divider();
JToolBarHelper::cancel('user_cancel','Cancel','cancel.png','Cancel');
//JToolBarHelper::divider();
//JToolBarHelper::help( 'screen.registrationpro', true );
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
					<td align="left" valign="top" width="30%"><b><?php echo JText::_('ADMIN_MOVE_USERS_SELECT_EVENT'); ?></b></td>	
					<td align="left" valign="top" width="70%"> <?php echo $this->Lists['events']; ?> </td>					
				</tr>		  			 
			</table> 
			
			<?php
				foreach($this->user_ids as $key=>$value){
					echo '<input type="hidden" name="user_ids[]" value="'.$value.'"/>';
				}		
			?>
			
			<?php echo JHTML::_( 'form.token' ); ?>	
			<input type="hidden" name="option" value="com_registrationpro" />
			<input type="hidden" name="controller" value="users" />	
			<input type="hidden" name="task" value="save_move_users" />
			<input type="hidden" name="rdid" value="<?php echo $this->event_id; ?>" />						  		
</form>