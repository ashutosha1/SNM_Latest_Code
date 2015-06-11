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

//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_EDIT_USER_INFO' ), 'usersedit' );
JToolBarHelper::apply();
JToolBarHelper::spacer();
JToolBarHelper::save();
JToolBarHelper::spacer();
JToolBarHelper::cancel('user_cancel','Cancel','cancel.png','Cancel');
JToolBarHelper::spacer();
//JToolBarHelper::help( 'screen.registrationpro', true );

?>

<script language="javascript" type="text/javascript">

function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	if(!validateForm(form,false,false,false,false)){
	}else{
		submitform( pressbutton );
	}
}

</script>
<div class="span10">
	<span class="span12 y-offset no-gutter">
		<?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?>
	</span>
	
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php
		if(is_array($this->user_values) && !count($this->user_values) > 0) {
		}else{
			foreach($this->user_values as $key=>$value) {
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
					$scriptvalidation = "";
					$mandatoryflag = "";
				}
	?>
				<span class="span3 no-gutter y-offset">
					<?php echo $mandatoryflag . " " . ucfirst(str_replace("cb_", "", $key)); ?>
				</span>
				<span class="span9 no-gutter y-offset">
					<input type="text" name="form[<?php echo $key; ?>][][0]" value="<?php echo $value;?>" <?php echo $scriptvalidation;?> size="50"/>
				</span>
	<?php 	}
		}
	?>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="users" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="rid" value="<?php echo $this->row->rid; ?>" />
	<input type="hidden" name="rdid" value="<?php echo $this->event_id; ?>" />
</form>
</div>