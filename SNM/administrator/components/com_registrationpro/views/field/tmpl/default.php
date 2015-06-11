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
//JHtml::_('formbehavior.chosen', 'select');
?>

<script language="javascript" type="text/javascript">

Joomla.submitbutton = function(pressbutton) {
	var form = document.adminForm;
	var flag = 0;

	if (pressbutton == 'cancelfield') {
		submitform( pressbutton );
		return;
	} else{
		if(form.fees_field.checked){
			var counted = checkfeecount();
			if(counted==1){
				alert('Count for fee value and fees not same');
				return false;
			}
			else if(counted==2){
				alert('Blank value can not be set for fee field or fee values');
				return false;
			}
		}
	}

		if(form.inputtype.value  == "calendar"){
			re = /^(\d{4})\-(\d{1,2})\-(\d{1,2})$/;
			if(form.default_value.value != '') {
				if(regs = form.default_value.value.match(re)) {
					if(regs[2] < 1 || regs[2] > 12) {
						alert("Invalid value for month: " + regs[2]);
						flag = 1;
						form.default_value.focus();
					}
					if(regs[3] < 1 || regs[3] > 31) {
						alert("Invalid value for day: " + regs[3]);
						flag = 1;
						form.default_value.focus();
					}
				} else {
					alert("Invalid date format: " + form.default_value.value);
					flag = 1;
					form.default_value.focus();
				}
			}
		}

	if(!validateForm(form,false,false,false,false)){
	} else {
		if(flag != 1) submitform( pressbutton );
	}
}

function enable_field_rows($selvalue)
{
	if($selvalue == "groups"){
		document.getElementById("field_validations").style.display = "none";
		document.getElementById("field_defaulvalue").style.display = "none";
		document.getElementById("field_params").style.display = "none";

		if(document.getElementById("field_groups")){
			document.getElementById("field_groups").style.display = "none";
		}
	}else{
		document.getElementById("field_validations").style.display = "";
		document.getElementById("field_defaulvalue").style.display = "";
		document.getElementById("field_params").style.display = "";

		if(document.getElementById("field_groups")){
			document.getElementById("field_groups").style.display = "";
		}
	}

	if($selvalue == "select" || $selvalue == "checkbox" || $selvalue == "radio" || $selvalue == "multicheckbox" ){
		document.getElementById("field_value").style.display = "";
		document.getElementById("fees_field1").style.display = "";
		document.getElementById("fees_field2").style.display = "";
		document.getElementById("fees_field3").style.display = "";
	}else{
		document.getElementById("field_value").style.display = "none";
		document.getElementById("fees_field1").style.display = "none";
		document.getElementById("fees_field2").style.display = "none";
		document.getElementById("fees_field3").style.display = "none";
	}

	if($selvalue == "radio" || $selvalue == "multicheckbox"){
		document.getElementById("field_display_type").style.display = "";
	}else{
		document.getElementById("field_display_type").style.display = "none";
	}

	if($selvalue == "text" && document.getElementById("validation_rule").value == "confirm") {
		document.getElementById("all_text_fields").style.display = "";
	}else{
		if(document.getElementById("validation_rule").value == "confirm"){
			document.getElementById("validation_rule").value = "";
		}
		document.getElementById("all_text_fields").style.display = "none";
	}

}

function enable_confirm_field($selvalue)
{
	if($selvalue == "confirm" && (document.getElementById("inputtype").value == "text" || document.getElementById("inputtype").value == "password")){
		document.getElementById("all_text_fields").style.display = "";
	}else{
		document.getElementById("all_text_fields").style.display = "none";
	}
}

function CheckIsSpeciailCharExist(obj) {
	var integers = obj.match(/[^\d.,-]+/g, '');
	if(integers!=null){
		alert('Enter numeric values only');
		var newval = obj.slice(0,-1);
		document.getElementById('fees').value = newval;
		document.getElementById('fees').focus();
		return false;
	}
}

function checkfeecount(){
	var n1 = document.getElementById('fvals').value;
	var a1 = n1.split(",");
	var l1 = a1.length;
	for(var j=0;j<l1;j++){
		if(a1[j]==""){
			return 2;
		}
	}
	var n2 = document.getElementById('fees').value;
	var a2 = n2.split(",");
	var l2 = a2.length;
	for(var j1=0;j1<l2;j1++){
		if(a2[j1]==""){
			return 2;
		}
	}
	if(l1!=l2){ return 1; } else { return 0; }
}
</script>
<div class="span10 y-offset no-gutter">
<form action="" method="post" name="adminForm" id="adminForm">
	<span class="span12 y-offset">
		<p class="pull-right"><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></p>
	</span>
	<div class="span8 y-offset">
		<span class="span5 y-offset no-gutter">
			<?php echo JText::_('ADMIN_MANDATORY_SYMBOL')." ".JText::_('ADMIN_FIELDS_NAME'); ?>
		</span>
		<span class="span7 y-offset no-gutter">
			<input type="text"name="name" alt="blank" emsg="<?PHP echo JText::_('ADMIN_SCRIPT_FIELDS_IDENTIFICATION_EMPTY');?>" value="<?php echo $this->row->name; ?>" size="55" maxlength="50" <?php echo $this->disable; ?> >
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_NAME_DESC' );?>">
				<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
			</span>
		</span>
		<br/>
		<span class="span5 y-offset no-gutter">
			<?php echo JText::_('ADMIN_MANDATORY_SYMBOL')." ".JText::_('ADMIN_FIELDS_TITLE'); ?>
		</span>
		<span class="span7 y-offset no-gutter">
			<input type="text"name="title" alt="blank" emsg="<?PHP echo JText::_('ADMIN_SCRIPT_FIELDS_TITLE_EMPTY');?>" value="<?php echo $this->row->title; ?>" size="55" maxlength="50"> 
			<span style="vertical-align:top">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_TITLE_DESC' );?>">
					<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
				</span>
			</span>
		</span>
		<br/>
		<span class="span5 y-offset no-gutter">
			<?php echo JText::_('ADMIN_FIELDS_DESCRIPTION'); ?>
		</span>
		<span class="span7 y-offset no-gutter">
			<textarea name="description" cols="50" rows="5"><?php echo $this->row->description;?></textarea>
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_DESCRIPTION_DESC' );?>">
				<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
			</span>
		</span>
		<br/>
		<span class="span5 y-offset no-gutter">
			<?php echo JText::_('ADMIN_FIELDS_TYPE'); ?>
		</span>
		<span class="span7 y-offset no-gutter">
			<?php echo $this->Lists['field_type'];?>
			<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_TYPE_DESC' );?>">
				<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
			</span>
		</span>
		
		<?php if($this->disable == "") { ?>
				<span class="span5 y-offset no-gutter">
					<?php echo JText::_('ADMIN_FIELDS_CONDITIONAL_FIELD'); ?>
				</span>
				<span class="span7 y-offset no-gutter">
					<?php echo $this->Lists['conditional_fields'];?>
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_CONDITIONAL_FIELD_DESC' );?>">
						<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
					</span>
				</span>
				<br/>
				<?php if(trim($this->row->conditional_field) != "" && trim($this->row->conditional_field_values) != "") { ?>
							<span class="span12 y-offset no-gutter" id="tr_conditional_result">
								<span class="span12 y-offset no-gutter" id="td_conditional_result">
									<?php
										$fieldvalues = unserialize($this->row->conditional_field_values);
										if(!is_array($fieldvalues) && count($fieldvalues) <= 0){
									?>
											<span class="span12 y-offset no-gutter">
												<?php echo JText::_('ADMIN_NO_RECORD_FOUND'); ?>
											</span>
									<?php
									}else{
										foreach($this->conditional_field_data as $fdkey => $fdvalue)
										{
											$con_checked = "";
											foreach($fieldvalues as $fkey => $fvalue)
											{
												if($fdvalue == $fvalue) {
													$con_checked = "checked";
													break;
												}
											}
									?>
										<span class="span12 y-offset no-gutter">
											<span class="span5 y-offset no-gutter">
												&nbsp;
											</span>
											<span class="span7 y-offset no-gutter">
												<input type="checkbox" name="conditional_field_values[]" value="<?php echo $fdkey; ?>" <?php echo $con_checked; ?> />
												<?php echo ucfirst($fdvalue); ?>
											</span>
										</span>
									<?php
										}
									}
									?>
								</span>
							</span>
				<?php
					}else{								
				?>
				<span class="span5 y-offset no-gutter">
					<?php //echo JText::_('ADMIN_FIELDS_CONDITIONAL_FIELD'); ?>
				</span>				
				<span class="span7 y-offset no-gutter" id="tr_conditional_result" style="display:none;">
					<span valign="top">&nbsp;</span>
					<span valign="top">&nbsp;</span>
					<span valign="top" id="td_conditional_result">&nbsp;</span>					
				</span>
				<?php 
					}
				}
				?>
				
				<?php if($this->Lists['field_groups']){ ?>
					<span class="span12 y-offset no-gutter" id="field_groups">
						<span class="span5 y-offset no-gutter">
							<?php echo JText::_('ADMIN_FIELDS_GROUPS'); ?>
						</span>
						<span class="span7 y-offset no-gutter">
							<?php echo $this->Lists['field_groups'];?>
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_GROUPS_DESC' );?>">
								<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
							</span>
						</span>
					</span>
				<?php } ?>
				<span class="span12 y-offset no-gutter" id="field_validations">
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_VALIDATION'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<?php echo $this->Lists['field_validations']; ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_VALIDATION_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				<?php
					$display_type = " style='display:none'";
					if(($this->row->inputtype == "text" || $this->row->inputtype == "password" ) && ($this->row->validation_rule == "confirm" )) $display_type = "";
				?>
				<?php if($this->Lists['all_text_fields']){ ?>
							<span class="span12 y-offset no-gutter" id="all_text_fields" <?php echo $display_type; ?>>
								<span class="span5 y-offset no-gutter">
									<?php echo JText::_('ADMIN_FIELDS_CONFIRM_FIELD'); ?>
								</span>
								<span class="span7 y-offset no-gutter">
									<?php echo $this->Lists['all_text_fields'];?>
									<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_CONFIRM_FIELD_DESC' );?>">
										<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
									</span>
								</span>
							</span>
				<?php } ?>
				<?php
					$display_type = " style='display:none'";
					if($this->row->inputtype == "radio" || $this->row->inputtype == "multicheckbox" ) $display_type = "";
				?>
				<span class="span12 y-offset no-gutter" id="field_display_type" <?php echo $display_type; ?>>
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_DISPLAY_TYPE'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<?php echo $this->Lists['field_display_type'];?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_DISPLAY_TYPE_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				<span class="span12 y-offset no-gutter" id="field_value">
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_VALUES'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<input type="text"name="values" value="<?php echo $this->row->values; ?>" id="fvals" size="55">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_VALUES_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				<span class="span12 y-offset no-gutter" id="field_defaulvalue">
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_DEFAULT'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<input type="text" name="default_value" value="<?php echo $this->row->default_value; ?>" size="55">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_DEFAULT_CALENDAR_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				
				<!-- Fees fields  -->
				<?php
					$display_type = " style='display:none'";
					if($this->row->inputtype == "radio" || $this->row->inputtype == "checkbox" || $this->row->inputtype == "select" || $this->row->inputtype == "multicheckbox") $display_type = "";

					$fees_field_checked = "";
					$fees_disbled = 'disabled="disabled"';
					if($this->row->fees_field == 1){
						$fees_field_checked = "checked";
						$fees_disbled = "";
					}

				?>
				<span class="span12 y-offset no-gutter" id="fees_field1" <?php echo $display_type; ?>>
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_FEES_FIELD')." "; ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<input type="checkbox" id="fees_field" name="fees_field" value="1" <?php echo $fees_field_checked; ?>>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_FEES_FIELD_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				<span class="span12 y-offset no-gutter" id="fees_field2" <?php echo $display_type; ?>>
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_FEES'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<input type="text" id="fees" name="fees" onkeyup="javascript:CheckIsSpeciailCharExist(this.value)" value="<?php echo $this->row->fees; ?>" size="55" <?php echo $fees_disbled; ?>>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_FEES_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				<span class="span12 y-offset no-gutter" id="fees_field3" <?php echo $display_type; ?>>
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_FEE_TYPE'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<?php echo $this->Lists['fees_field_type']; ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_FEE_TYPE_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span>
				<!--span class="span12 y-offset no-gutter" id="fees_field3" <?php //echo $display_type; ?>>
					<span class="span5 y-offset no-gutter">
						<?php// echo JText::_('ADMIN_FIELDS_FEE_TYPE'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<?php //echo $this->Lists['fees_field_type']; ?>
						<span class="editlinktip hasTip" title="<?php //echo JText::_( 'ADMIN_FIELDS_FEE_TYPE_DESC' );?>">
							<img src="<?php //echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
				</span-->
				<!-- End fees fields -->
				<span class="span12 y-offset no-gutter" id="field_params">
					<span class="span5 y-offset no-gutter">
						<?php echo JText::_('ADMIN_FIELDS_PARAMS'); ?>
					</span>
					<span class="span7 y-offset no-gutter">
						<input type="text"name="params" value="<?php echo $this->row->params; ?>" size="55">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_PARAMS_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</span>
					<input type="hidden" name="batch_display" value="1"  />
				</span>
	</div>
	<div class="span4 y-offset no-gutter">
		<?php
			echo JHtml::_('sliders.start', 'content-sliders-category', array('useCookie'=>1));
			if($this->row->id){
				echo JHtml::_('sliders.panel', JText::_('ADMIN_FIELDS_PREVIEW'), 'elcatbasic-page');
		?>
		<?php
		$html = '<table  border="0" cellpadding="2" cellspacing="0" width="100%"><tr>';
		$html .= '<td align="right" valign="top">'.$this->row->title.'</td>';
		switch($this->row->inputtype){
			case 'text':     $html.='<td valign="top"><input type="text" name="'.$this->row->name.'" value="'.$this->row->default_value.'" '.$this->row->params.' /></td>'; break;
			case 'password': $html.='<td valign="top"><input type="password" name="'.$this->row->name.'" value="'.$this->row->default_value.'" '.$this->row->params.' /></td>'; break;
			case 'radio':
				$options = '';
				$this->row->values = explode(',',$this->row->values);
				$this->row->default_value = explode(',',$this->row->default_value);
				$html.='<td valign="top">';

				if(is_array($this->row->values) && count($this->row->values > 0))
				{
					foreach ($this->row->values as $value){
						if(is_array($this->row->default_value) && count($this->row->default_value > 0))
						{
							foreach($this->row->default_value as $dvalue)
							{
								if(trim($value) == trim($dvalue)){
									$default = "checked";
								}else{
									$default = "";
								}
							}
						}
						$html.= '<input type="radio" name="'.$this->row->name.'" value="'.$value.'" '.$this->row->params.' '.$default.' /> '.$value;
						if($this->row->display_type == 2){
							$html.= "<br />";
						}
					}
				}
				$html.='</td>';
			break;
			case 'checkbox':
				$html.='<td valign="top"><input type="checkbox" name="'.$this->row->name.'" value="'.$this->row->default_value.'" '.$this->row->params.' /></td>';
			break;
			case 'multicheckbox':
				$options = '';
				$this->row->values = explode(',',$this->row->values);
				$this->row->default_value = explode(',',$this->row->default_value);
				$html.='<td valign="top">';

				if(is_array($this->row->values) && count($this->row->values > 0))
				{
					foreach ($this->row->values as $value){

						$default = "";
						if(is_array($this->row->default_value) && count($this->row->default_value > 0))
						{
							foreach($this->row->default_value as $dvalue)
							{
								if(trim($dvalue) == trim($value)){
									$default = "checked";
								}
							}
						}

						$html.= '<input type="checkbox" name="'.$this->row->name.'" value="'.$value.'" '.$this->row->params.' '.$default .'/> '.$value;
						if($this->row->display_type == 2){
							$html.= "<br />";
						}
					}
				}

				$html.='</td>';
			break;
			case 'textarea':
				$html.='<td valign="top"><textarea name="'.$this->row->name.'" '.$this->row->params.'>'.$this->row->default_value.'</textarea></td>';
			break;
			case 'select':
				$options = '';
				$this->row->values 			= explode(',',$this->row->values);
				$this->row->default_value 	= explode(',',$this->row->default_value);
				if($this->row->validation_rule == "mandatory") $options .= '<option value="">'.JText::_('REGPRO_SELECT_ONE').'</option>';
				if(is_array($this->row->values) && count($this->row->values > 0)) {
					foreach ($this->row->values as $value) {
						$default = "";
						if(is_array($this->row->default_value) && count($this->row->default_value > 0)) {
							foreach($this->row->default_value as $dvalue) {
								if(trim($dvalue) == trim($value)) $default = "selected";
							}
						}
						$options .= '<option value="'.$value.'" '.$default.'>'.$value.'</option>';
					}
				}
				$html.='<td valign="top"><select name="'.$this->row->name.'" '.$this->row->params.'>'.$options.'</select></td>';
			break;

			case 'multiselect':
				$options = '';
				$this->row->values 			= explode(',',$this->row->values);
				$this->row->default_value 	= explode(',',$this->row->default_value);

				if(is_array($this->row->values) && count($this->row->values > 0))
				{
					foreach ($this->row->values as $value){

						$default = "";
						if(is_array($this->row->default_value) && count($this->row->default_value > 0))
						{
							foreach($this->row->default_value as $dvalue)
							{
								if(trim($dvalue) == trim($value)){
									$default = "selected";
								}
							}
						}

						$options .= '<option value="'.$value.'" '.$default.'>'.$value.'</option>';
					}
				}

				$html.='<td valign="top"><select name="'.$this->row->name.'" '.$this->row->params.' multiple>'.$options.'</select></td>';
			break;

			case 'calendar':
				$html .='<td valign="top"><input id="'.$this->row->name.'" name="'.$this->row->name.'" value="'.$this->row->default_value.'" size="15" maxlength="10" readonly>
				<input class="button" value="..." onclick="return showCalendar(\''.$this->row->name.'\', \'%Y-%m-%d\');" type="reset"></td>';
			break;

			case 'file':
				$html .='<td valign="top"><input type="file" id="'.$this->row->name.'" name="'.$this->row->name.'" value="'.$this->row->default_value.'" size="15" maxlength="10"></td>';
			break;

			case 'country':
				$registrationproAdmin = new registrationproAdmin;
				$regpro_config	= $registrationproAdmin->config();

				$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" >'.$regpro_config['countrylist'].'</select>';

			break;

			case 'state':
				$registrationproAdmin = new registrationproAdmin;
				$regpro_config	= $registrationproAdmin->config();

				$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" >'.$regpro_config['statelist'].'</select>';
			break;

		}


		$html .= '<td valign="top">';

		if(trim($this->row->description) != ""){
			$html .='<span class="editlinktip hasTip" title="'.$this->row->description.'">
					<img src="'. REGPRO_ADMIN_IMG_PATH.'/info.png" align="absmiddle" border="0" align="absmiddle" />
				 </span>';
		}

		$html .= '</td>';
		$html .= '</tr></table>';

		echo $html;
		?>
<?php
	}

	echo JHtml::_('sliders.panel', JText::_('ADMIN_FIELDS_SETTINGS'), 'elcatbasic-page');
?>
	<table  border="0" cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<td><?php echo JText::_('ADMIN_FIELDS_PUBLISHED'); ?></td>
			<td>
				<fieldset class="radio btn-group btn-group-yesno">
					<?php echo $this->Lists['published']; ?>
				</fieldset>
			</td>
		</tr>
	</table>

<?php echo JHtml::_('sliders.end');	?>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="forms" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="is_conditional" value="0" />
	<input type="hidden" name="form_id" value="<?php echo $this->row->form_id; ?>" />
</form>
</div>
<script language="javascript">

$('#fees_field').change(function() {
	if($('#fees_field').prop('checked') == true) {
		$('#fees').removeAttr('disabled');
	} else {
		$('#fees').attr('disabled', 'disabled');
	}
});

function fieldload() {
	<?php if($this->row->inputtype == "groups"){ ?>
			document.getElementById("field_validations").style.display = "none";
			document.getElementById("field_defaulvalue").style.display = "none";
			document.getElementById("field_params").style.display = "none";
			document.getElementById("field_value").style.display = "none";
			if(document.getElementById("field_groups")){
				document.getElementById("field_groups").style.display = "none";
			}
	<?php }elseif($this->row->inputtype == "select" || $this->row->inputtype == "multiselect" || $this->row->inputtype == "radio" || $this->row->inputtype == "multicheckbox"){ ?>
			document.getElementById("field_validations").style.display = "";
			document.getElementById("field_defaulvalue").style.display = "";
			document.getElementById("field_params").style.display = "";
			document.getElementById("field_value").style.display = "";
			if(document.getElementById("field_groups")) document.getElementById("field_groups").style.display = "";
	<?php }else{ ?>
			document.getElementById("field_validations").style.display = "";
			document.getElementById("field_defaulvalue").style.display = "";
			document.getElementById("field_params").style.display = "";
			document.getElementById("field_value").style.display = "none";
			if(document.getElementById("field_groups"))	document.getElementById("field_groups").style.display = "";
	<?php }?>
}

fieldload();

$('#conditional_field').change(function() {
	var formid = 0;
	var field = "";
	field = $('#conditional_field').val();

	jQuery.ajax({
		url: "index.php?option=com_registrationpro&controller=forms&tmpl=component&task=getFieldValues&formid=" + <?php echo $this->row->form_id; ?> + "&field=" + field,
		type: "GET",
		processData: false,
		contentType: false,
		beforeSend: function() {
			var $tr_cond = document.getElementById("tr_conditional_result");
			$tr_cond.setStyle('display','');
			var $td_cond = document.getElementById("td_conditional_result");
			$td_cond.set('html','');
			$td_cond.addClass('loading');
		},
		success: function (res) {
			var $td_cond = document.getElementById("td_conditional_result");
			$td_cond.removeClass('loading');
			$td_cond.set('html', res);
		},
		error: function () {
			var $td_cond = document.getElementById("td_conditional_result");
			$td_cond.removeClass('loading');
			$td_cond.set('text', 'Sorry, your request failed :(');
		}
	});
});

</script>