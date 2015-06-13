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
<div id="regpro">
<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();

// form toolbar
$regpro_html->backbutton_toolbar();
?>

<script language="javascript" type="text/javascript">	
		
function submitbutton(pressbutton) {
	var form = document.adminForm;
	var flag = 0;

	if (pressbutton == 'cancelfield') {
		submitform( pressbutton );
		return;
	}
		
	// check defult calendar date format
		if(form.inputtype.value  == "calendar"){
			// regular expression to match required date format  
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
					//return false; 
				} 
			}														
		}
	// end

	// do field validation (added by sdei on 18-Feb-2008)
	if(!validateForm(form,false,false,false,false)){
			
	} else {
		if(flag != 1){			
			submitform( pressbutton );
		}
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
			
	if($selvalue == "select" || $selvalue == "multiselect" || $selvalue == "radio" || $selvalue == "multicheckbox"){
		document.getElementById("field_value").style.display = "";
	}else{
		document.getElementById("field_value").style.display = "none";
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
	if($selvalue == "confirm" && document.getElementById("inputtype").value == "text"){
		document.getElementById("all_text_fields").style.display = "";
	}else{
		document.getElementById("all_text_fields").style.display = "none";
	}
}

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table cellpadding="4" cellspacing="1" border="0" width="100%">	
	<tr>
		<td valign="top" align="left" width="100%">
			<table cellpadding="4" cellspacing="0" class="adminform">
				<tr>
					<td valign="top" width="2px"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?></td>
					<td valign="top" width="150px"><?php echo JText::_('ADMIN_FIELDS_NAME')." "; ?></td>
					<td valign="top">
										
					<input name="name" alt="blank" emsg="<?PHP echo JText::_('ADMIN_SCRIPT_FIELDS_IDENTIFICATION_EMPTY');?>" value="<?php echo $this->row->name; ?>" size="55" maxlength="50" <?php echo $this->disable; ?> class="regpro_inputbox"> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_NAME_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>											 
					</td>
				</tr>
				<tr>
					<td valign="top" width="2px"><?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?></td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_TITLE')." "; ?></td>
					<td valign="top"><input name="title" alt="blank" emsg="<?PHP echo JText::_('ADMIN_SCRIPT_FIELDS_TITLE_EMPTY');?>" value="<?php echo $this->row->title; ?>" size="55" maxlength="50" class="regpro_inputbox"> <span style="vertical-align:top"> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_TITLE_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_DESCRIPTION'); ?></td>
					<td valign="top"> <textarea name="description" style="width:250px; height:100px;" class="regpro_inputbox"><?php echo $this->row->description;?></textarea> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_DESCRIPTION_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
											
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_TYPE')." "; ?></td>
					<td valign="top"><?php echo $this->Lists['field_type'];?> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_TYPE_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
				
				<?php if($this->Lists['field_groups']){ ?>
				<tr id="field_groups">
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_GROUPS')." "; ?></td>
					<td valign="top"><?php echo $this->Lists['field_groups'];?> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_GROUPS_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
				<?php } ?>	
				
				<tr id="field_validations">
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_VALIDATION')." "; ?></td>
					<td valign="top"><?php echo $this->Lists['field_validations']; ?> 						
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_VALIDATION_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
				
				<?php if($this->row->inputtype == "text" && $this->row->validation_rule == "confirm" ){ 
							$display_type = "";
					  }else{
					  		$display_type = " style='display:none'";
					  }
				?>
				
				<?php if($this->Lists['all_text_fields']){ ?>
				<tr id="all_text_fields" <?php echo $display_type; ?>>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_CONFIRM_FIELD')." "; ?></td>
					<td valign="top"><?php echo $this->Lists['all_text_fields'];?> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_CONFIRM_FIELD_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
				<?php } ?>
				
				<?php if($this->row->inputtype == "radio" || $this->row->inputtype == "multicheckbox" ){ 
							$display_type = "";
					  }else{
					  		$display_type = " style='display:none'";
					  }
				?>
				<tr id="field_display_type" <?php echo $display_type; ?>>
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_DISPLAY_TYPE')." "; ?></td>
					<td valign="top"><?php echo $this->Lists['field_display_type'];?> 
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_DISPLAY_TYPE_DESC' );?>">						
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>				
				
				<tr id="field_value">
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_VALUES')." "; ?></td>
					<td valign="top"><input name="values" value="<?php echo $this->row->values; ?>" size="55" class="regpro_inputbox">				
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_VALUES_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</td>
				</tr>
				<tr id="field_defaulvalue">
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_DEFAULT')." "; ?></td>
					<td valign="top"><input name="default_value" value="<?php echo $this->row->default_value; ?>" size="55" class="regpro_inputbox">				
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_DEFAULT_CALENDAR_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>
					</td>
				</tr>
				<tr id="field_params">
					<td valign="top">&nbsp;</td>
					<td valign="top"><?php echo JText::_('ADMIN_FIELDS_PARAMS')." "; ?></td>
					<td valign="top"><input name="params" value="<?php echo $this->row->params; ?>" size="55" class="regpro_inputbox">						
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_FIELDS_PARAMS_DESC' );?>">
							<img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/info.png" align="absmiddle" border="0" align="absmiddle">
						</span>	
					</td>
				</tr>
				<tr>					
					<!-- added by sdei 21-nov-07 --> 						
					<td valign="top">&nbsp;</td>
					<td>&nbsp;</td>
					<td><input type="hidden" name="batch_display" value="1"  /></td>								
					<!-- end --> 						
				</tr>				
			</table>	
		</td>
		</tr>
		<tr>		
		<td width="100%" valign="top">
			<?php
				echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); 
			
				if($this->row->id){
					/* echo $tabs->startPane("elcategory-pane");
					echo $tabs->startPanel(JText::_('ADMIN_FIELDS_PREVIEW'),"elcatbasic-page"); */
					echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('ADMIN_FIELDS_PREVIEW'), true);		
			?>			
					<?php															
					$html = '<table  border="0" cellpadding="2" cellspacing="0" width="100%">
								<tr>';
					$html .= '<td align="right" valign="top">'.$this->row->title.'</td>';
					switch($this->row->inputtype){
						case 'text':
							$html.='<td valign="top"><input type="text" name="'.$this->row->name.'" value="'.$this->row->default_value.'" '.$this->row->params.' /></td>';
						break;
						case 'password':
							$html.='<td valign="top"><input type="password" name="'.$this->row->name.'" value="'.$this->row->default_value.'" '.$this->row->params.' /></td>';
						break;
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
							
							
							if($this->row->validation_rule == "mandatory"){
								$options .= '<option value="">'.JText::_('REGPRO_SELECT_ONE').'</option>';
							}
														
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
							$html .='<td valign="top">';
							$html .= JHTML::_('calendar'
							  , $this->row->default_value
							  , $this->row->name
							  , $this->row->name
							  , '%Y-%m-%d'
							  , array('class'=>$jsvalidation, 'size'=>'25',  'maxlength'=>'19', 'style'=>'vertical-align:top'));
														
							/*<input id="'.$this->row->name.'" name="'.$this->row->name.'" value="'.$this->row->default_value.'" size="15" maxlength="10" readonly> 
							<input class="button" value="..." onclick="return showCalendar(\''.$this->row->name.'\', \'%Y-%m-%d\');" type="reset"></td>';*/
						break;
						
						case 'file':
							$html .='<td valign="top"><input type="file" id="'.$this->row->name.'" name="'.$this->row->name.'" value="'.$this->row->default_value.'" size="15" maxlength="10"></td>';
						break;
						
						case 'country':
							$regpro_config	=& registrationproAdmin::config();
														
							$html.='<td class="regpro_vtop_aleft"><select name="form['.$row->name.']['.$count.']'.$position_html.'"'.$jsvalidation.' '.$row->params.' id="'.$row->name.'['.$count.']'.$position_html.'" >'.$regpro_config['countrylist'].'</select>';
							
						break;
						
						case 'state':
							$regpro_config	=& registrationproAdmin::config();
														
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
				echo JHtml::_('bootstrap.endTab');		
				}
				echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('ADMIN_FIELDS_SETTINGS'), true);	
				/* echo $tabs->startPane("elcategory-pane");
				echo $tabs->startPanel(JText::_('ADMIN_FIELDS_SETTINGS'),"elcatbasic-page");	 */
			?>
				<table  border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td><?php echo JText::_('ADMIN_FIELDS_PUBLISHED'); ?></td>
						<td><?php echo $this->Lists['published']; ?></td>
					</tr>
				</table>
			
			<?php
			echo JHtml::_('bootstrap.endTab');	
			echo JHtml::_('bootstrap.endTabSet');	
/* 				echo $tabs->endPanel();
				echo $tabs->endPane(); */
			?>
		</td>		
	</tr>
	<tr>
		<td> <input type="button" value="<?php echo JText::_('MY_FORM_FIELD_SAVE'); ?>" onclick="return submitbutton('add_field');" /> </td>
	</tr>
</table>
	<?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?>
		
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="forms" />	
	<input type="hidden" name="task" value="" />	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="form_id" value="<?php echo $this->row->form_id; ?>" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />						
</form>

<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>	

<script language="javascript">

function fieldload()
{
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
				if(document.getElementById("field_groups")){
					document.getElementById("field_groups").style.display = "";		
				}
	<?php }else{ ?>
				document.getElementById("field_validations").style.display = "";
				document.getElementById("field_defaulvalue").style.display = "";
				document.getElementById("field_params").style.display = "";	
				document.getElementById("field_value").style.display = "none";
				if(document.getElementById("field_groups")){
					document.getElementById("field_groups").style.display = "";
				}
	<?php }?>		
}

fieldload();

</script>

</div>