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
JHtml::_('formbehavior.chosen', 'select');
JToolBarHelper::title(JText::_( 'ADMIN_BADGES' ) , 'rbadges.png' );
JToolBarHelper::cancel();

jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');
$db	=JFactory::getDBO();

$search 	= $this->data['search'];
$event	 	= $this->data['event'];
$start_date = $this->data['start_date'];
$end_date 	= $this->data['end_date'];
$firstname 	= $this->data['firstname'];
$lastname 	= $this->data['lastname'];
$email 		= $this->data['email'];

?>

<style>.active{border:solid #000000;}</style>

<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function(pressbutton) {
			var form = document.adminForm;

			if (pressbutton == 'print_report') {
				report_open("index.php?option=com_registrationpro&controller=search&task=print_report&tmpl=component&print=1&hidemainmenu=1");
				return false;
			}

			if (pressbutton == 'csv_report') {
				report_open("index.php?option=com_registrationpro&controller=search&task=csv_report");
				return false;
			}

			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			submitform( pressbutton );
		}

		function report_open(url_add) {
	   		window.open(url_add,"","width=1200,height=800,menubar=no,status=no,location=no,toolbar=no,scrollbars=1");
   		}

		function resetform() {
			var form = document.adminForm;
			form.event.value = "";
			form.sortby.value = "";
			form.badgestyles.value = "";
			document.getElementById('users_A').checked;
			form.reset.value = "1";
			form.submit();
		}

		function selected_user(flag) {
			if(flag == "S") {
				document.getElementById('users_row').style.display = "";
			} else document.getElementById('users_row').style.display = "none";
		}

		function event_change() {
			var form = document.adminForm;
			form.task.value = "";
			form.submit();
		}

		function form_submit() {
			var form = document.adminForm;
			if(form.task.value == 'cancel') {
				form.submit();
			} else {
				if(form.event.value == 0) {
					alert("Please select event first.");
					return false;
				}
				form.task.value = "generate_badge";
				form.submit();
			}
		}

		function clk_align(element, cnt) {
			if(element.name == 'Aleft') {
				document.getElementById("Aleft["+cnt+"]").className='alignIcon active';
				document.getElementById("Acenter["+cnt+"]").className='alignIcon';
				document.getElementById("Aright["+cnt+"]").className='alignIcon';
				document.getElementById("align"+cnt).value='L';
			}

			if(element.name == 'Acenter') {
				document.getElementById("Aleft["+cnt+"]").className='alignIcon';
				document.getElementById("Acenter["+cnt+"]").className='alignIcon active';
				document.getElementById("Aright["+cnt+"]").className='alignIcon';
				document.getElementById("align"+cnt).value='C';
			}

			if(element.name == 'Aright') {
				document.getElementById("Aleft["+cnt+"]").className='alignIcon';
				document.getElementById("Acenter["+cnt+"]").className='alignIcon';
				document.getElementById("Aright["+cnt+"]").className='alignIcon active';
				document.getElementById("align"+cnt).value='R';
			}
		}
		
		function checkAll(n) {
			var f = document.adminForm;
			var c = f.toggle.checked;
			var n2 = 0;
			for (i=0; i < n; i++) {
				cb = eval( 'f.cb' + i );
				if (cb) {
					cb.checked = c;
					n2++;
				}
			}
			if (c) {
				f.boxchecked.value = n2;
			} else f.boxchecked.value = 0;
		}

</script>
<div class="span10">
	<span class="span12" id="ajaxmessagebox"></span>
<form action="" method="post" name="adminForm" id="adminForm" onsubmit="return form_submit();">
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_EVENT_NAME');?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['events']; ?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_USERS');?>
	</span>
	<span class="span8 y-offset no-gutter">
		<input style="margin-top:-5px;margin-right:8px;" type="radio" name="users" id="users_A" value="A" onclick="return selected_user('A');" checked="checked" /><?php echo JText::_('ADMIN_BADGES_ALL_USERS'); ?>
		<input style="margin-top:-5px;margin-right:8px;margin-left:20px;" type="radio" name="users" id="users_S" value="S" onclick="return selected_user('S');"/><?php echo JText::_('ADMIN_BADGES_SELECTED_NAME'); ?>
	</span>
	<br/>
	<span class="span12 y-offset no-gutter" id="users_row" style="display:none;">
		<div id="list_users" style="overflow:auto;max-height:400px;">
			<table class="tableBadgesUsers" id="tableBadgesUsers">
				<thead>
					<tr>
						<th width="4%"><input type="checkbox" name="toggle" checked value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
						<th width="32%"><?php echo JText::_('ADMIN_SEARCH_RESULT_FIRSTNAME'); ?></th>
						<th width="32%"><?php echo JText::_('ADMIN_SEARCH_RESULT_LASTNAME'); ?></th>
						<th width="32%"><?php echo JText::_('ADMIN_SEARCH_RESULT_EMAIL'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$k = 0;
					$counter = 1;
					for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
						$row = &$this->rows[$i];
						$checked = JHTML::_('grid.checkedout',   $row, $i );
					?>
					<tr>
						<td> <?php echo $checked; ?> </td>
						<td><?php echo htmlspecialchars($row->firstname, ENT_QUOTES, 'UTF-8');?> </td>
						<td><?php echo htmlspecialchars($row->lastname, ENT_QUOTES, 'UTF-8');?> </td>
						<td><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8');?> </td>
					</tr>
					<?php
						$k = 1 - $k;
						$counter++;
					}
					?>
				</tbody>
			</table>
		</div>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_SORTBY'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['sortby']; ?>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_STYLES'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['badgestyles']; ?>
	</span>
	<div class="clearfix"></div>
	<br/>
	<span class="span12 y-offset no-gutter">
		<b><?php echo JText::_('ADMIN_BADGES_CUSTOMIZE_BADGE_LAYOUT'); ?></b>
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_FIRSTLINE'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['fields']; ?>
		<?php echo $this->Lists['fonts']; ?>
		<?php echo $this->Lists['fontsizes']; ?>
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_left.gif"   border=0 id="Aleft[0]"   onclick="return clk_align(this,0);" class="alignIcon active" name="Aleft">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_center.gif" border=0 id="Acenter[0]" onclick="return clk_align(this,0);" class="alignIcon" name="Acenter">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_right.gif"  border=0 id="Aright[0]"  onclick="return clk_align(this,0);" class="alignIcon" name="Aright">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_SECONDLINE'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['fields']; ?>
		<?php echo $this->Lists['fonts']; ?>
		<?php echo $this->Lists['fontsizes']; ?>
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_left.gif"   border=0 id="Aleft[1]"   onclick="return clk_align(this,1);" class="alignIcon active" name="Aleft">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_center.gif" border=0 id="Acenter[1]" onclick="return clk_align(this,1);" class="alignIcon" name="Acenter">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_right.gif"  border=0 id="Aright[1]"  onclick="return clk_align(this,1);" class="alignIcon" name="Aright">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_THIRDLINE'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['fields']; ?>
		<?php echo $this->Lists['fonts']; ?>
		<?php echo $this->Lists['fontsizes']; ?>
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_left.gif"   border=0 id="Aleft[2]"   onclick="return clk_align(this,2);" class="alignIcon active" name="Aleft">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_center.gif" border=0 id="Acenter[2]" onclick="return clk_align(this,2);" class="alignIcon" name="Acenter">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_right.gif"  border=0 id="Aright[2]"  onclick="return clk_align(this,2);" class="alignIcon" name="Aright">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_FORTHLINE'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['fields']; ?>
		<?php echo $this->Lists['fonts']; ?>
		<?php echo $this->Lists['fontsizes']; ?>
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_left.gif"   border=0 id="Aleft[3]"   onclick="return clk_align(this,3);" class="alignIcon active" name="Aleft">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_center.gif" border=0 id="Acenter[3]" onclick="return clk_align(this,3);" class="alignIcon" name="Acenter">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_right.gif"  border=0 id="Aright[3]"  onclick="return clk_align(this,3);" class="alignIcon" name="Aright">
	</span>
	<br/>
	<span class="span4 y-offset no-gutter">
		<?php echo JText::_('ADMIN_BADGES_FORTHLINE'); ?>
	</span>
	<span class="span8 y-offset no-gutter">
		<?php echo $this->Lists['fields']; ?>
		<?php echo $this->Lists['fonts']; ?>
		<?php echo $this->Lists['fontsizes']; ?>
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_left.gif"   border=0 id="Aleft[3]"   onclick="return clk_align(this,3);" class="alignIcon active" name="Aleft">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_center.gif" border=0 id="Acenter[3]" onclick="return clk_align(this,3);" class="alignIcon" name="Acenter">
		<img src="<?php echo REGPRO_ADMIN_IMG_PATH;?>/align_right.gif"  border=0 id="Aright[3]"  onclick="return clk_align(this,3);" class="alignIcon" name="Aright">
	</span>
	<br/>
	<span class="span12 y-offset no-gutter">
		<input type="submit" value="<?php echo JText::_('ADMIN_GENERATE_BADGE'); ?>" class="btn btn-small btn-success"/>
		<input type="reset" id="reset" value="<?php echo JText::_('ADMIN_BADGE_RESET_BUTTON'); ?>"class="btn btn-small btn-inverse"/>
	</span>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="badge" />
	<input type="hidden" name="view" value="badge" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="align[0]" id="align0" value="L" />
	<input type="hidden" name="align[1]" id="align1" value="L" />
	<input type="hidden" name="align[2]" id="align2" value="L" />
	<input type="hidden" name="align[3]" id="align3" value="L" />
	<input type="hidden" name="reset" value="0" />
	<input type="hidden" name="l" value="0" />
	<input type="hidden" name="c" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->Lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->Lists['order_Dir']; ?>" />
</form>
</div>
<div class="span10">
	<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer();?>
</div>
<script language="javascript" type="text/javascript">checkAll(<?php echo $counter;?>);</script>