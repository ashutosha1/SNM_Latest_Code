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
$ordering = 1;
?>

<script language="javascript" type="text/javascript">

Joomla.submitbutton = function(pressbutton){
	var form = document.adminForm;
	if (pressbutton == 'cancel' || pressbutton == 'datimup') {
		submitform( pressbutton );
		return;
	}

	if(!validateForm(form,false,false,false,false)){} else submitform( pressbutton );
}

</script>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<span class="span12 y-offset no-gutter">
		<p class="pull-right"><?php echo JText::_('ADMIN_MANDATORY_FIELDS_NOTE'); ?></p>
	</span>
	<div class="span6 y-offset no-gutter" id="form-area">
		<span class="span4 y-offset no-gutter">
			<?php echo JText::_('ADMIN_MANDATORY_SYMBOL')." ".JText::_('ADMIN_FORMS_TITLE'); ?>
		</span>
		<span class="span8 y-offset no-gutter">
			<input type="text"name="title" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FORM_TITLE_EMPTY'); ?>" value="<?php echo $this->row->title; ?>" size="55" maxlength="50">
		</span>
		<br/>
		<span class="span4 y-offset no-gutter">
			<?php echo JText::_('ADMIN_MANDATORY_SYMBOL')." ".JText::_('ADMIN_FORMS_NAME'); ?>
		</span>
		<span class="span8 y-offset no-gutter">
			<input type="text"name="name" alt="blank" emsg="<?php echo JText::_('ADMIN_SCRIPT_FORM_ID_EMPTY'); ?>" value="<?php echo $this->row->name; ?>" size="55" maxlength="50">
		</span>
		<br/>
		<span class="span4 y-offset no-gutter">
			<?php echo JText::_('ADMIN_FORMS_THANKYOU'); ?>
		</span>
		<span class="span8 y-offset no-gutter">
			<?php 
				echo $this->editor->display( 'thankyou',  stripslashes($this->row->thankyou) , '100%', '200', '75', '20', array('pagebreak','readmore') );
			?>
		</span>
		<br/>
		<span class="span12 y-offset no-gutter">
			<?php echo JText::_('ADMIN_FORMS_THANKYOU_DESC'); ?>
		</span>
	</div>
	
	<div class="span6 y-offset no-gutter" id="field-area">
		<?php
			echo JHtml::_('sliders.start', 'content-sliders-form', array('useCookie'=>1));
			echo JHtml::_('sliders.panel', JText::_('ADMIN_FIELDS_HEAD'), 'elcatbasic-page');
		?>
			<span class="span12 y-offset no-gutter text-center">
				<input type="button" name="addmorefield" value="<?php echo JText::_('ADMIN_FORM_TAB_ADD_MORE_FIELDS');?>" onclick=" javascript:submitbutton('edit_field');" class="btn btn-small btn-success"/>
			</span>
			<span class="span12 y-offset no-gutter">
				<table cellpadding="2" cellspacing="1" border="0" class="table table-striped">
					<thead>
						<tr>
							<th> 
								<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this)" />
							</th>
							<th align="left"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
							<th align="left"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
							<th align="left"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
							<th align="left"><?php echo JText::_('ADMIN_FIELDS_PUBLISH'); ?></th>
							<th>
								<?php echo JText::_('ADMIN_FIELDS_ORDER'); ?> 
								<a href="javascript: saveorder( <?php echo count($this->row->fields)-1; ?>,'savefieldsorder' )">
									<img src="components/com_registrationpro/assets/images/filesave.png" border="0" width="16" height="16" alt="Save Order" />
								</a> 
							</th>
							<th> <?php echo JText::_('ADMIN_FIELDS_ID'); ?> </th>
						</tr>
					</thead>
					<?php 
						$count	= 0;
						$i = 0;
						$ordering =array();
						
						foreach($this->fields as $field_row) $ordering[$field_row->id] = $field_row->ordering;
						
						@$max_order = max($ordering);
						$max_order_id = array_search($max_order, $ordering);
						@$min_order = min($ordering);
						$min_order_id = array_search($min_order, $ordering);
						$m = 0;
						$cnt = 0;

						foreach($this->fields as $field_row) {
							$input	= JHTML::_('grid.id', $count, $field_row->id);
							$link 	= 'index.php?option=com_registrationpro&controller=forms&task=edit_field&hidemainmenu=1&form_id='.$this->row->id.'&cid='. $field_row->id;
							@$checked 	= JHTML::_('grid.checkedout', $field_row, $i );
							$published 	= JHTML::_('grid.published', $field_row, $i );

							$link_disable = 0;
							if($field_row->name == 'firstname' || $field_row->name == 'lastname' || $field_row->name == 'email') $link_disable = 1;

							if($field_row->inputtype == 'groups') {
					?>
							<tr>
								<td  style="background-color: #EEEEEE;">
									<?php echo $input; ?>
								</td>
								<td style="background-color: #EEEEEE;">
									<strong><?php echo JText::_('GROUPS');?>
										<span id="name<?php echo $field_row->id; ?>">
											<?php echo $field_row->title;?>
										</span>
									</strong>
									<div style="clear: both;"></div>
								</td>
								<td> <?php echo $field_row->name;?> </td>
								<td> <?php echo $field_row->inputtype;?> </td>
								<td>
									<?php
										$task = $field_row->published ? 'unpublishfield' : 'publishfield';
										$img = $field_row->published ? 'publish_g.png' : 'publish_x.png';
										$alt = $field_row->published ? 'Published' : 'Unpublished';
									?>

									<a href="javascript: void(0);" <?php if(!$link_disable) { ?> onclick="return listItemTask('cb<?php echo $cnt;?>','<?php echo $task;?>')<?php } ?>"><img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
								</td>
								<?php if($m!=0){ ?>
								<td style="text-align:center;">
									<?php 
									if($min_order_id != $field_row->id){  ?>
										<a href="javascript: void(0);" onClick="return document.adminForm.group_id.value=<?php echo $field_row->groupid; ?>, listItemTask('cb<?php echo $count;?>','orderupfield');">
										<img src="components/com_registrationpro/assets/images/uparrow.png" width="16px" height="16px" border="0" alt="orderup">
										</a>
										<?php		} ?>
										<?php	//	if ($i < $n-1) {
											if($max_order_id != $field_row->id){	?>
										<a href="javascript: void(0);" onClick="return document.adminForm.group_id.value=<?php echo $field_row->groupid; ?>, listItemTask('cb<?php echo $count;?>','orderdownfield')">
										<img src="components/com_registrationpro/assets/images/downarrow.png" width="16px" height="16px" border="0" alt="orderdown">
										</a>
										<?php		} ?>
										<input type="text" name="order[]" size="5" value="<?php echo $field_row->ordering; ?>" class="text_area" style="text-align: center;width=20px;" />
								</td>
								<?php }else{ ?>
									<td style="text-align:center;">&nbsp;<input type="text" name="order[]" size="5" value="<?php echo $field_row->ordering; ?>" class="text_area" style="text-align: center;width=20px;" /></td>

							<?php
								} ?>
								<td  style="text-align:center"><?php echo $field_row->id; ?></td>
							</tr>
					<?php
								$m++;
								$i	= 0;

							} else if($field_row->inputtype != 'groups') {
								++$i;
					?>
							<tr class="row<?php echo $i%2;?>" id="rowid<?php echo $field_row->id;?>">
								<td>
									<?php echo $input; ?>
								</td>
								<td> <a href="<?php echo $link; ?>"> <?php echo $field_row->title;?> </a> </td>
								<td> <?php echo $field_row->name;?> </td>
								<td> <?php echo $field_row->inputtype;?> </td>
								<td>
									<?php
										$task = $field_row->published ? 'unpublishfield' : 'publishfield';
										$img = $field_row->published ? 'publish_g.png' : 'publish_x.png';
										$alt = $field_row->published ? 'Published' : 'Unpublished';
										if($alt == "Published"){
											$class = "btn btn-micro active hasTooltip";
										}else{
											$class = "btn btn-micro hasTooltip";
										}
									?>

									<a href="javascript: void(0);" <?php if(!$link_disable) { ?> onclick="return listItemTask('cb<?php echo $cnt;?>','<?php echo $task;?>')<?php } ?>"class="<?php echo $class;?>"><img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
								</td>
								<td style="text-align:center;">
									<?php
										if($min_order_id != $field_row->id){	?>
											<a href="javascript: void(0);" onClick="return document.adminForm.group_id.value=<?php echo $field_row->groupid; ?>, listItemTask('cb<?php echo $count;?>','orderupfield');">
											<img src="components/com_registrationpro/assets/images/uparrow.png" width="16px" height="16px" border="0" alt="orderup">
											</a>
											<?php		} ?>
											<?php	//	if ($i < $n-1) {
												if($max_order_id != $field_row->id){	?>
											<a href="javascript: void(0);" onClick="return document.adminForm.group_id.value=<?php echo $field_row->groupid; ?>, listItemTask('cb<?php echo $count;?>','orderdownfield')">
											<img src="components/com_registrationpro/assets/images/downarrow.png" width="16px" height="16px" border="0" alt="orderdown">
											</a>
											<?php		} ?>
											<br/><input type="text" name="order[]" size="5" value="<?php echo $field_row->ordering; ?>" class="text_area" style="text-align: center;width:20px " />
								</td>
								<td><?php echo $field_row->id; ?></td>
							</tr>
					<?php

							}
							$count++;
							$cnt++;
						} ?>
					</tr>
				</table>
			</span>
			<span class="span12 y-offset no-gutter">
				<?php
					if(count($this->row->cb_fields) > 0){ //check Community builder existance
						echo JHtml::_('sliders.panel', JText::_('ADMIN_CB_FIELDS_HEAD'), 'elcataccess-page');
						if(!empty($this->row->cb_fields)){
				?>
							<table cellpadding="2" cellspacing="1" border="0" class="adminform table table-striped">
							<thead>
								<tr>
									<th width="7">&nbsp;</th>
									<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
									<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
									<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
									<th style="text-align:center" class="title"><?php echo JText::_('ADMIN_CB_FIELDS_PUBLISH'); ?></th>
								</tr>
							</thead>
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
										$img = $cb_field->is_regpro ? 'publish_g.png' : 'publish_x.png';
										$alt = $cb_field->is_regpro ? 'Added' : 'Removed';
									?>

									<td style="text-align:center"><a href="javascript: void(0);" onclick="document.getElementById('adminForm').action='index.php?option=<?php echo $option;?>&task=<?php echo $task;?>&cid=<?php echo $cb_field->fieldid;?>&form_id=<?php echo $row->id; ?>';document.adminForm.task.value='<?php echo $task;?>';document.adminForm.submit();"><img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" /></a>
									</td>
								</tr>
							<?php
								}
							?>
						</table>
					<?php
							}
						}
						if(count($this->row->jomsocial_fields) > 0){ //check Joosocial fields
							echo JHtml::_('sliders.panel', JText::_('ADMIN_JOOMSOCIAL_FIELDS_HEAD'), 'elcataccess-page');
							if(!empty($this->row->jomsocial_fields)){	
					?>
								<table cellpadding="2" cellspacing="1" border="0" class="adminform table table-striped">
									<thead>
										<tr>
											<th width="7">&nbsp;</th>
											<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
											<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
											<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
											<th style="text-align:center" class="title"><?php echo JText::_('ADMIN_CB_FIELDS_PUBLISH'); ?></th>
										</tr>
									</thead>
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
											$img = $jomsocial_field->is_regpro ? 'publish_g.png' : 'publish_x.png';
											$alt = $jomsocial_field->is_regpro ? 'Added' : 'Removed';
										?>

										<td style="text-align:center">
											<a href="javascript: void(0);" onclick="document.getElementById('adminForm').action='index.php?option=<?php echo $option;?>&task=<?php echo $task;?>&cid=<?php echo $jomsocial_field->id;?>&form_id=<?php echo $row->id; ?>';document.adminForm.task.value='<?php echo $task;?>';document.adminForm.submit();">
												<img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" />
											</a>
										</td>
									</tr>
								<?php
									}
								?>
							</table>
					<?php
						}
					}

					if(count($this->row->profile_fields) > 0){ //check core fields
						echo JHtml::_('sliders.panel', JText::_('ADMIN_COREPROFILE_FIELDS_HEAD'), 'elcataccess-page');
						if(!empty($this->row->profile_fields)){
				?>
					<table cellpadding="2" cellspacing="1" border="0" class="adminform table table-striped">
						<thead>
							<tr>
								<th width="7">&nbsp;</th>
								<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TITLE'); ?></th>
								<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_NAME'); ?></th>
								<th align="left" class="title"><?php echo JText::_('ADMIN_FIELDS_TYPE'); ?></th>
								<th style="text-align:center" class="title"><?php echo JText::_('ADMIN_CB_FIELDS_PUBLISH'); ?></th>
							</tr>
						</thead>
						<?php
							foreach($this->row->profile_fields as $profile_field){
						?>
							<tr>
								<td>&nbsp;</td>
								<td align="left" class="title">
									<?php echo $profile_field['title']; ?>
								</td>
								<td align="left" class="title"><?php echo $profile_field['identification']; ?></td>
								<td align="left" class="title"><?php echo $profile_field['inputtype']; ?></td>
								<?php
									if(!isset($profile_field['is_regpro'])) $profile_field['is_regpro'] = 0;

									$task = $profile_field['is_regpro'] ? 'unpublishprofilefield' : 'publishprofilefield';
									$img = $profile_field['is_regpro'] ? 'publish_g.png' : 'publish_x.png';
									$alt = $profile_field['is_regpro'] ? 'Added' : 'Removed';
								?>

								<td style="text-align:center">
									<a href="javascript: void(0);" onclick="document.getElementById('adminForm').action='index.php?option=<?php echo $option;?>&task=<?php echo $task;?>&cid=<?php echo $profile_field['identification'];?>&form_id=<?php echo $row->id; ?>';document.adminForm.task.value='<?php echo $task;?>';document.adminForm.submit();">
										<img src="components/com_registrationpro/assets/images/<?php echo $img;?>" width="16px" height="16px" border="0" alt="<?php echo $alt;?>" />
									</a>
								</td>
							</tr>
						<?php
							}
						?>
					</table>
				<?php
						}
					}
					echo JHtml::_('sliders.end');
				?>
		</span>
	</div>
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
</form>
</div>