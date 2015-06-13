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
	
$ordering = 1;
?>

<div id="regpro">

<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();

// form toolbar
$regpro_html->myforms_fields_toolbar();
?>

<script language="javascript" type="text/javascript">

function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'remove_field' || pressbutton == 'publishfield' || pressbutton == 'unpublishfield') {	
		if(form.boxchecked.value > 0){			
			submitform( pressbutton );
			return;
		}else{
			alert("<?php echo JText::_('MY_EVENTS_SELECT_RECORD_FIRST'); ?>");
		}
	}else if(!validateForm(form,false,false,false,false)){
	
	}else{			
		submitform( pressbutton );	
	}								
	// end			
}

</script>
<div id="regpro_outline" class="regpro_outline">
<form action="index.php" method="post" name="adminForm" id="adminForm" class='myForm'>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="vertical-align:top">
			<table class="adminform" cellpadding="2" cellspacing="1" border="0" width="96%">
				<tr>
					<td valign="top" width="2px">
						<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_('ADMIN_FORMS_TITLE')." "; ?>					
					</td>
					<td valign="top">
						<input name="title" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FORM_TITLE_EMPTY'); ?>" value="<?php echo $this->row->title; ?>" size="55" maxlength="50" class="regpro_inputbox">
					</td>
				</tr>
				<tr>
					<td valign="top">
						<?php echo JText::_('ADMIN_MANDATORY_SYMBOL'); ?>
						<?php echo JText::_('ADMIN_FORMS_NAME'); ?>
					</td>
					<td valign="top"><input name="name" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FORM_ID_EMPTY'); ?>" value="<?php echo $this->row->name; ?>" size="55" maxlength="50" class="regpro_inputbox"></td>
				</tr>
				
				<tr>
					<td colspan="2">
						<?php echo JText::_('ADMIN_FORMS_THANKYOU'); ?>
					<?php 
					// parameters : areaname, content, hidden field, width, height, rows, cols
					echo $this->editor->display( 'thankyou',  stripslashes($this->row->thankyou) , '100%', '200', '75', '20', array('pagebreak','readmore') ) ;
					?>
					</td>							
				</tr>
					
				<tr>
					<td valign="top">&nbsp;</td>
					<td colspan="2"><?php echo JText::_('ADMIN_FORMS_THANKYOU_DESC'); ?></td>
				</tr>
				<tr>
					<td colspan="3"><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></td>
				</tr>				
			</table>			
		</td>
	</tr>
	<tr>
		<td width="96%" style="vertical-align:top">		
			<?php
			/* $tabs = JPane::getInstance('sliders', array('allowAllClose' => true));
				echo $tabs->startPane("elcategory-pane");
				echo $tabs->startPanel("Basic","elcatbasic-page"); */	
			echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); 
			echo JHtml::_('bootstrap.addTab', 'myTab', 'general', 'Basic', true);			
			
			?>
					<div id="my-form-field">
					<table cellpadding="2" cellspacing="1" border="0" class="adminform" >						
					<tr>
						<th colspan="6">
						    <div style="text-align:center;">
								<input type="button" name="addmorefield" value="ADD MORE FORM FIELDS" onclick=" javascript:submitbutton('edit_field');" class="regpro_button"/>
							</div>
						</th>
					</tr> 				      	
					<tr>
						<th>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
						<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
						<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
						<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_PUBLISH'); ?></th>
						<!--<th width="2%"> Order </th>	-->		
						<th width="15%" class="title" style="text-align:right">
							<?php echo JText::_('ADMIN_FIELDS_ORDER'); ?> 
							<a href="javascript: saveorder( <?php echo count($this->row->fields)-1; ?>,'savefieldsorder' )">
								<img src="<?php echo REGPRO_IMG_PATH; ?>/filesave.png" border="0" width="16" height="16" alt="Save Order" />
							</a> 
							</th>
						<th width="10%" class="title" style="text-align:center"> <?php echo JText::_('ADMIN_FIELDS_ID'); ?> </th>
					
					</tr>	
					<?php
					$k = 0;
					$registrationproHelper = new registrationproHelper;
					$grpid = 0;
					for ($i=0, $n=count($this->row->fields); $i < $n; $i++) {
						$field_row = $this->row->fields[$i];
						
						// Groups headings						
						if($grpid != $field_row->groupid){
							$groupname = $this->field_group_name($field_row->groupid);
							echo "<tr><td colspan='8' style='text-align:center;'><b>".$groupname."<b/></td> </tr>";
						}
																								
						$link 	= 'index.php?option=com_registrationpro&view=field&id='.$field_row->id.'&form_id='.$this->row->id;											
					
						$checked 	= JHTML::_('grid.checkedout', $field_row, $i );
						$published 	= JHTML::_('grid.published', $field_row, $i );
						
						if($field_row->name == 'firstname' || $field_row->name == 'lastname' || $field_row->name == 'email'){
							$link_disable = 1;
						}else{
							$link_disable = 0;
						}
							
					?>
						<tr class="<?php echo "row$k"; ?>">
							<td width="7"> <?php echo $checked; ?> </td>
							<td> <a href="<?php echo $link; ?>"> <?php echo $field_row->title;?> </a> </td>
							<td> <?php echo $field_row->name;?> </td>
							<td> <?php echo $field_row->inputtype;?> </td>									
							<td>
								<?php
									$task = $field_row->published ? 'unpublishfield' : 'publishfield';
									$img = $field_row->published ? 'ball_green.png' : 'ball_red.png';
									$alt = $field_row->published ? 'Published' : 'Unpublished';																		
								?>
								
								<a href="javascript: void(0);" <?php if(!$link_disable) { ?> onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')<?php } ?>"><img src="<?php echo REGPRO_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>								
							</td>
																					
							<td style="text-align:right">
								<span><?php echo $registrationproHelper->regpro_orderUpIcon( $i, ($field_row->groupid == $this->row->fields[$i-1]->groupid), 'orderupfield', 'Move Up', $ordering,"document.adminForm.group_id.value=".$field_row->groupid); ?></span>
								<span><?php echo $registrationproHelper->regpro_orderDownIcon( $i, $n, ($field_row->groupid == $this->row->fields[$i+1]->groupid), 'orderdownfield', 'Move Down', $ordering,"document.adminForm.group_id.value=".$field_row->groupid); ?></span>							
								<input type="text" name="order[]" size="5" value="<?php echo $field_row->ordering; ?>" class="regpro_inputbox" style="text-align: center" />
							</td>
							<td  style="text-align:center"><?php echo $field_row->id; ?></td>
							
							<?php $k = 1 - $k; 
							$grpid = $field_row->groupid;
					} ?>
						</tr>    
					</table>
					</div>
					<?php 
					echo JHtml::_('bootstrap.endTab');				
					if(count($this->row->cb_fields) > 0){ //check Community builder existance 	
						echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('ADMIN_CB_FIELDS_HEAD', true)); 
						if(!empty($this->row->cb_fields)){							
				?>	
					<div style="overflow:auto; height:600px">				
					<table cellpadding="2" cellspacing="1" border="0" class="adminform">
						<tr>
							<th width="7">&nbsp;</th>
							<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
							<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
							<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
							<th style="text-align:center" class="title"><?php echo JText::_('ADMIN_CB_FIELDS_PUBLISH'); ?></th>
						</tr>								
	
						<?php
							foreach($this->row->cb_fields as $cb_field){
						?>
							<tr>
								<td>&nbsp;</td>
								<td align="left" class="title">
									<?php
										if(defined($cb_field->title))
											echo constant($cb_field->title)." "; 
										else
											echo $cb_field->title." ";
									?>
								</td>
								<td align="left" class="title"><?php echo $cb_field->name." "; ?></td>
								<td align="left" class="title"><?php echo $cb_field->type." "; ?></td>
								<?php
									if(!isset($cb_field->is_regpro)) $cb_field->is_regpro = 0;
	
									$task = $cb_field->is_regpro ? 'unpublishcbfield' : 'publishcbfield';
									$img = $cb_field->is_regpro ? 'ball_green.png' : 'ball_red.png';
									$alt = $cb_field->is_regpro ? 'Added' : 'Removed';
								?>
								
								<td style="text-align:center"><a href="javascript: void(0);" onclick="document.getElementById('adminForm').action='index.php?option=<?php echo $option;?>&task=<?php echo $task;?>&cid=<?php echo $cb_field->fieldid;?>&form_id=<?php echo $row->id; ?>';document.adminForm.task.value='<?php echo $task;?>';document.adminForm.submit();"><img src="<?php echo REGPRO_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
								</td>
							</tr>
						<?php
							}
						?>
					</table>
					</div>				
				<?php
						}
						echo JHtml::_('bootstrap.endTab');					
					}
				
					if(count($this->row->jomsocial_fields) > 0){ //check Joosocial fields 	
						//echo $tabs->startPanel(JText::_('ADMIN_JOOMSOCIAL_FIELDS_HEAD'),"elcataccess-page");
						echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('ADMIN_JOOMSOCIAL_FIELDS_HEAD', true)); 
						if(!empty($this->row->jomsocial_fields)){							
				?>	
					<div style="overflow:auto; height:600px">				
					<table cellpadding="2" cellspacing="1" border="0" class="adminform">
						<tr>
							<th width="7">&nbsp;</th>
							<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
							<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
							<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
							<th style="text-align:center" class="title"><?php echo JText::_('ADMIN_CB_FIELDS_PUBLISH'); ?></th>
						</tr>								
	
						<?php
							foreach($this->row->jomsocial_fields as $jomsocial_field){
						?>
							<tr>
								<td>&nbsp;</td>
								<td align="left" class="title">
									<?php echo $jomsocial_field->name." "; ?>
								</td>
								<td align="left" class="title"><?php echo $jomsocial_field->name." "; ?></td>
								<td align="left" class="title"><?php echo $jomsocial_field->type." "; ?></td>
								<?php
									if(!isset($jomsocial_field->is_regpro)) $jomsocial_field->is_regpro = 0;
	
									$task = $jomsocial_field->is_regpro ? 'unpublishjoosocialfield' : 'publishjoosocialfield';
									$img = $jomsocial_field->is_regpro ? 'ball_green.png' : 'ball_red.png';
									$alt = $jomsocial_field->is_regpro ? 'Added' : 'Removed';
								?>
								
								<td style="text-align:center"><a href="javascript: void(0);" onclick="document.getElementById('adminForm').action='index.php?option=<?php echo $option;?>&task=<?php echo $task;?>&cid=<?php echo $jomsocial_field->id;?>&form_id=<?php echo $row->id; ?>';document.adminForm.task.value='<?php echo $task;?>';document.adminForm.submit();"><img src="<?php echo REGPRO_IMG_PATH; ?>/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
								</td>
							</tr>
						<?php
							}
						?>
					</table>
					</div>				
				<?php
						}
						echo JHtml::_('bootstrap.endTab');						
					}	
					/* echo $tabs->endPanel();
							echo $tabs->endPane(); */
							echo JHtml::_('bootstrap.endTabSet');									
				?>			
		</td>
	</tr>		
	<tr>
			<td> <input type="button" value="<?php echo JText::_('MY_FORM_SAVE'); ?>" onclick="return submitbutton('save');" /> </td>
		</tr>
</table>

<?php echo JHTML::_( 'form.token' ); ?>	
<input type="hidden" name="option" value="com_registrationpro" />
<input type="hidden" name="controller" value="forms" />	
<input type="hidden" name="task" value="" />
<?php if($this->task != "copy") {?>	
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<?php }else{?>
<input type="hidden" name="copy" value="1" />
<?php }?>
<input type="hidden" name="form_id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="group_id" value="" />
<input type="hidden" name="boxchecked" value="0" />	
<input type="hidden" name="user_id" value="<?php echo $this->user->id; ?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />	

</form>
</div>
<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>	

</div>