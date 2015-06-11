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
//create the toolbar
JToolBarHelper::title(JText::_( 'Search' ) , 'rsearch.png' );
JToolBarHelper::custom( 'print_report', 'preview.png', 'preview.png', 'Print Report', 0, 1);
JToolBarHelper::spacer();
JToolBarHelper::custom( 'csv_report', 'html.png', 'xml.png', 'Csv Report', 0, 1);
JToolBarHelper::spacer();
JToolBarHelper::cancel();
//JToolBarHelper::spacer();
//JToolBarHelper::help( 'screen.registrationpro', true );

// Load pane behavior
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');
$db	=JFactory::getDBO();

// get filter vlaues
$search     = $this->data['search'];
$event      = $this->data['event'];
$start_date = $this->data['start_date'];
$end_date   = $this->data['end_date'];
$firstname  = $this->data['firstname'];
$lastname   = $this->data['lastname'];
$email      = $this->data['email'];

?>

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
		window.open(url_add,"","width=1000,height=600,menubar=no,status=no,location=no,toolbar=no,scrollbars=1");
	}

	function resetform() {
		var form = document.adminForm;
		form.search.value = "";
		form.event.value = "";
		form.firstname.value = "";
		form.lastname.value = "";
		form.email.value = "";
		form.start_date.value = "";
		form.end_date.value = "";
		form.reset.value = "1";
		form.submit();
	}
</script>
<div class="span10">
<form action="" method="post" name="adminForm" id="adminForm">
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_KEYWORDS')." "; ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<input type="text" name="search" id="search" value="<?php echo $search; ?>"/>
		&nbsp;<b style="verticle-align: middle;">in</b>&nbsp;
		<?php echo $this->Lists['events']; ?>
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_DATE_RANGE'); ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<?php 
			echo JHTML::_('calendar', $start_date, 'start_date', 'start_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
		?>
		<?php echo '<b style="verticle-align: middle;">'.JText::_('ADMIN_SEARCH_DATE_RANGE_TO').'</b>';?>
		<?php 
			echo JHTML::_('calendar', $end_date, 'end_date', 'end_date', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19'));
		?>
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_FIRSTNAME'); ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<input type="text" name="firstname" id="firstname" value="<?php echo $firstname; ?>" />
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_LASTNAME'); ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<input type="text" name="lastname" id="lastname" value="<?php echo $lastname; ?>" />
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_EMAIL'); ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<input type="text" name="email" id="email" value="<?php echo $email; ?>" />
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_LOCATION'); ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<?php echo $this->Lists['locations']; ?>
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		<?php echo JText::_('ADMIN_SEARCH_CATEGORY'); ?>
	</span>
	<span class="span9 y-offset no-gutter">
		<?php echo $this->Lists['categories']; ?>
	</span>
	<br/>
	<span class="span3 y-offset no-gutter">
		&nbsp;
	</span>
	<span class="span9 y-offset no-gutter">
		<input type="submit" value="<?php echo JText::_('ADMIN_SEARCH_EVENT_BUTTON');?>"class="btn btn-success"/>
		<input type="button" onclick="return resetform();" value="<?php echo JText::_('ADMIN_SEARCH_EVENT_RESET_BUTTON'); ?>"class="btn btn-inverse"/>
	</span>
	<div class="clearfix"></div>
	<br/><br/>
	<span class="span12 y-offset no-gutter">
		<table class="table table-striped" cellspacing="1">
			<thead>
				<tr>
					<th><?php echo JText::_( 'S.No.' ); ?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_EVENT_NAME'),'a.titel',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_FIRSTNAME'),'r.firstname',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_LASTNAME'),'r.lastname',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_EMAIL'),'r.email',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_LOCATION'),'l.club',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_CATEGORY'),'c.catname',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JHTML::_('grid.sort', JText::_('ADMIN_SEARCH_RESULT_EVENT_DATE'),'a.dates',$this->Lists['order_Dir'],$this->Lists['order'] );?></th>
					<th><?php echo JText::_('ADMIN_EVENT_LIST_TICKETS_COLUMN');?></th>
				</tr>
			</thead>

			<tbody class="ui-sortable">
				<?php
				$k = 0;
				for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					$event_link = 'index.php?option=com_registrationpro&amp;controller=events&amp;task=edit&amp;cid[]='. $row->id;
					$firstname_link = 'index.php?option=com_registrationpro&view=users&rdid='. $row->id;
					$lastname_link = 'index.php?option=com_registrationpro&view=users&rdid='. $row->id;
					$email_link = 'index.php?option=com_registrationpro&view=users&rdid='. $row->id;
					$location_link = 'index.php?option=com_registrationpro&controller=locations&task=edit&cid[]='. $row->loc_id;
					$category_link = 'index.php?option=com_registrationpro&controller=categories&task=edit&cid[]='. $row->cat_id;
				?>
				<tr class="<?php echo "row$k"; ?>  dndlist-sortable">
					<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
					<td align="left">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_EVENT' );?>::<?php echo $row->titel; ?>">
							<a href="<?php echo $event_link; ?>"> <?php echo htmlspecialchars($row->titel, ENT_QUOTES, 'UTF-8'); ?> </a>
						</span>
					</td>
					<td align="left"><a href="<?php echo $firstname_link; ?>"><?php echo htmlspecialchars($row->firstname, ENT_QUOTES, 'UTF-8');?></a></td>
					<td align="left"><a href="<?php echo $lastname_link; ?>"><?php echo htmlspecialchars($row->lastname, ENT_QUOTES, 'UTF-8');?></a></td>
					<td align="left"><a href="<?php echo $email_link; ?>"><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8');?> </a></td>
					<td align="left"><a href="<?php echo $location_link; ?>"><?php echo htmlspecialchars($row->club, ENT_QUOTES, 'UTF-8'); echo " ",htmlspecialchars($row->city, ENT_QUOTES, 'UTF-8');?> </a> </td>
					<td align="left"><a href="<?php echo $category_link; ?>"><?php echo htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8'); ?></a></td>
					<td width="10%"align="center"><?php $registrationproHelper = new registrationproHelper; echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->dates);?></td>
					<td width="15%">
						<ul>
						<?php foreach($row->tickets as $key=>$val) echo "<li>".$val->item_name."</li>";	?>
						</ul>
					</td>
				</tr>
				<?php $k = 1 - $k; } ?>

			</tbody>
			<tfoot>
				<tr>
					<td colspan="15">
						<?php echo $this->pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</span>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="controller" value="search" />
	<input type="hidden" name="task" value="search" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="reset" value="0" />
	<input type="hidden" name="l" value="0" />
	<input type="hidden" name="c" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->Lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->Lists['order_Dir']; ?>" />
</form>
</div>
<div class="span10">
	<?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer( );?>
</div>