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

defined('_JEXEC') or die('Restricted access');
JHtml::_('formbehavior.chosen', 'select');
?>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		if(!validateForm(form,false,false,false,false)){
		} else { submitform( pressbutton );}
	}

	var cp = new ColorPicker();
	var cp = new ColorPicker('window');

	function pickColor(color) {
		document.getElementById('background').value = color;
		document.getElementById('background').style.background = color;
	}
</script>

<script language="JavaScript">cp.writeDiv()</script>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#category_details" data-toggle="tab"><?php echo  JText::_('ADMIN_CATEGORIES_DETAILS'); ?> </a></li>
		<li><a href="#category_other_settings" data-toggle="tab"><?php echo  JText::_('ADMIN_CATEGORIES_OTHER_SETTINGS'); ?> </a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="category_details">
			<span class="span4 y-offset">
				<b><?php echo JText::_('ADMIN_MANDATORY_SYMBOL') . JText::_('ADMIN_CATEGORIES_NAME'); ?></b>
			</span>
			<span class="span6 y-offset no-gutter">
				<input type="text"name="catname" alt="blank" emsg="<?PHP echo JText::_('ADMIN_SCRIPT_CATEGORY_NAME_EMPTY'); ?>" value="<?php echo $this->row->catname; ?>" size="55" maxlength="50">
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<b><?php echo JText::_('ADMIN_CATEGORIES_PARENT'); ?></b>
			</span>
			<span class="span6 y-offset no-gutter">
				<?php echo $this->Lists['allcategories'] ?>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<b><?php echo JText::_('ADMIN_CATEGORIES_DESCR_LO'); ?></b>
			</span>
			<span class="span6 y-offset no-gutter">
				<?php 
					echo $this->editor->display('catdescription', stripslashes($this->row->catdescription),'80%;', '200', '20', '40',array('pagebreak', 'readmore'));
				?>
			</span>
		</div>

		<div class="tab-pane" id="category_other_settings">
			<span class="span4 y-offset no-gutter">
				<b><?php echo JText::_('ADMIN_CATEGORIES_PUBLI'); ?></b>
			</span>
			<span class="span6 y-offset no-gutter">
				<fieldset class="radio btn-group btn-group-yesno">
					<?php echo $this->Lists['published'];?>
				</fieldset>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<b><?php echo JText::_('ADMIN_CATEGORIES_BACKGROUND'); ?></b>
			</span>
			<span class="span6 y-offset no-gutter">
				<input type="text" id="background" name="background" value="#<?php echo $this->row->background; ?>" size="8" maxlength="7" style="background:#<?php echo $this->row->background; ?>" />
				<a href="#" onClick="cp.show('pick');return false;" name="pick" id="pick" title="<?php echo JText::_('ADMIN_EVENTS_SETT_MSG_COLOR_PICK'); ?>">
					<img style="margin-top:-7px;margin-left:5px;" src="components/com_registrationpro/assets/images/icon_colorpicker_24x24.png" width=24 height=24 border=0>
				</a>
			</span>
			<br/>
			<span class="span4 y-offset no-gutter">
				<b><?php echo JText::_('ADMIN_CATEGORIES_ACCESS'); ?></b>
			</span>
			<span class="span2 y-offset no-gutter">
				<?php echo $this->Lists['access']; ?>
			</span>
		</div>
		<div class="clearfix"></div>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="categories" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
</div>