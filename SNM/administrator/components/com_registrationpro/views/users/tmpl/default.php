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
JHTML::_('behavior.tooltip');
JHTML::_( 'behavior.modal' );

//create the toolbar
JToolBarHelper::title( JText::_( 'ADMIN_LBL_CONTROPANEL_REGISTERD_USERS_LIST' ), 'users' );
JToolBarHelper::custom( 'add_user', 'new.png', 'new.png', 'Add', 0, 1);
JToolBarHelper::divider();
JToolBarHelper::editList();
JToolBarHelper::divider();
JToolBarHelper::custom( 'move_user', 'move.png', 'move_f2.png', 'Move' );
JToolBarHelper::divider();
JToolBarHelper::deleteList('Selected records data will be lost and cannot be undone!', 'remove', 'Remove');
JToolBarHelper::divider();
JToolBarHelper::custom( 'accept_user', 'apply.png', 'apply_f2.png', 'Accept' );
JToolBarHelper::divider();
JToolBarHelper::custom( 'waiting_user', 'publish.png', 'publish_f2.png', 'Waiting' );
JToolBarHelper::divider();
JToolBarHelper::custom( 'pending_user', 'unpublish.png', 'unpublish_f2.png', 'Pending' );
JToolBarHelper::divider();
JToolBarHelper::custom( 'pending_paymentstatus', 'unpublish.png', 'unpublish_f2.png', 'Payment unpaid' );
JToolBarHelper::divider();
JToolBarHelper::custom( 'completed_paymentstatus', 'apply.png', 'apply_f2.png', 'Payment Paid' );
JToolBarHelper::divider();
JToolBarHelper::custom( 'email_to_all', 'send.png', 'send.png', 'Email To All', 0, 1);
JToolBarHelper::divider();
JToolBarHelper::custom( 'email_to_selected', 'send.png', 'send_f2.png', 'Email To Selected' );
JToolBarHelper::divider();
 JToolBarHelper::custom( 'event_report', 'preview.png', 'preview.png', 'Event Report', 0, 1);
JToolBarHelper::divider(); 
JToolBarHelper::custom( 'excel_report', 'html.png', 'xml.png', 'Export to excel', 0, 1);
JToolBarHelper::divider();
JToolBarHelper::cancel();
JToolBarHelper::divider();
$registrationproHelper = new registrationproHelper;
//echo"<pre>";print_r($this->rows); exit;
?>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
		var rdid = form.rdid.value;
		if (pressbutton == 'event_report') {
			report_open("index.php?option=com_registrationpro&controller=events&task=event_report&tmpl=component&print=1&hidemainmenu=1&cid[]="+rdid);
			return false;
		}else if (pressbutton == 'excel_report') {
			window.location = "index.php?option=com_registrationpro&controller=events&task=excel_report&cid[]="+rdid;
		}else if(pressbutton == 'add_user'){
			window.location = "index.php?option=com_registrationpro&view=newuser&did="+rdid;
		}else {
			submitform( pressbutton );
		}
	}

	function report_open(url_add) {
	   window.open(url_add,"","width=1200,height=800,menubar=no,status=no,location=no,toolbar=no,scrollbars=1");
   	}

	function paymentstatus(id,status,task) {
		var form = document.adminForm;
		document.getElementById(id).checked = true;
		form.payment_status.value = status;
		submitform(task);
	}

	function attendedstatus(id,status,task) {
		var form = document.adminForm;
		document.getElementById(id).checked = true;
		form.attended_status.value = status;
		submitform(task);
	}
</script>
<div class="span10">
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div id="search_div" class="span12 no-gutter">
	<div class="span8">
		<?php
			echo '<b class="x-offset">'.JText::_('COM_REGISTRATIONPRO_SEARCH_IN').'</b>'.$this->lists['filter'];
		?>
		<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="input-medium search-query" onChange="document.adminForm.submit();" />
		<button onclick="this.form.submit();" class="btn">
			<?php echo JText::_('Search');?>
		</button>
		<button onclick="this.form.getElementById('search').value='';this.form.submit();"class="btn">
			<?php echo JText::_('COM_REGISTRATIONPRO_RESET'); ?>
		</button>	
	</div>
	<div class="span2 no-gutter pull-right">
		<?php echo '<span class="pull-right">'.$this->lists['state'].'</span>'; ?>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
</div>
<div class="span12 no-gutter">
<table class="adminlist userTable table table-striped" cellspacing="1" border="0" width="100%">
	<thead>
		<tr>
			<th width=2% style="text-align:center;"><?php echo JText::_( 'S.No.' ); ?></th>
			<th width=2% style="text-align:center;"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width=13% style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_USER_FIRST_NAME'), 'r.firstname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width=13% style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_USER_LAST_NAME'), 'r.lastname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width=13% style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_USER_EMAIL_ADDRESS'), 'r.email', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width=13% style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_DEFAULT_PRODUCT'), 'r.uregdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width=7% style="text-align:center;"><?php echo JText::_( 'ADMIN_USER_REG_AMOUNT' ); ?></th>
			<th width=6% style="text-align:center;"><?php echo JText::_( 'ADMIN_EVENTS_ATTEND_STATUS' ); ?></th>
			<th width=7% style="text-align:center;"><?php echo JText::_( 'ADMIN_EVENTS_USERPENDING' ); ?></th>
			<th width=5% style="text-align:center;"><?php echo JText::_( 'ADMIN_EVENTS_USERACCEPTED' ); ?></th>
			<th width=5% style="text-align:center;"><?php echo JText::_( 'ADMIN_EVENTS_USERWAITING' ); ?></th>
			<th width=6% style="text-align:center;"><?php echo JText::_( 'ADMIN_EVENTS_PAYMENT_STATUS' ); ?></th>
			<th width=7% style="text-align:center;"><?php echo JHTML::_('grid.sort',JText::_('ADMIN_EVENTS_USER_ADDED_BY'),'r.added_by',$this->lists['order_Dir'],$this->lists['order'] ); ?></th>
			<th width=5% style="text-align:center;"><?php echo JText::_( 'ADMIN_EVENTS_USERTRANSACTION' ); ?></th>
		    <th width=3% style="text-align:center;" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'r.rid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i];
			$link = 'index.php?option=com_registrationpro&amp;controller=users&amp;task=edit&amp;rcid[]='. $row->rid.'&amp;rdid='.$this->eventid;
			$payment_status = 1;
			if(strtolower($row->payment_status) == "pending") $payment_status = 0;
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"> <input type="checkbox" id="cb<?php echo $i;?>" name="rcid[]" value="<?php echo $row->rid; ?>" onclick="Joomla.isChecked(this.checked);" /></td>
			<td align="left"><span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_USER' );?>::<?php echo $row->firstname; ?>"><a href="<?php echo $link; ?>"><?php echo $row->firstname; ?></a></span></td>
			<td align="left"><?php echo $row->lastname; ?> </td>
			<td align="left"><?php echo $row->email; ?></td>
			<td align="left" style="font-size:12px;"><?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate']." ".$this->regpro_config['formattime'], $row->uregdate + ($this->regpro_config['timezone_offset']*60*60));?></td>
			<td align="center">
				<?php
					if(!empty($row->AdminDiscount))
					{
						$addDiscount = $row->AdminDiscount;
					}else{
						$addDiscount = 0;
					}
					if(($row->amount*1) == 0){
						echo "Free";
					}else{
						if($row->event_discount_type == "P"){
							$final_amount = number_format($row->amount - $row->tot_discount - $addDiscount,2);
							if($final_amount > 0){
								echo $this->regpro_config['currency_sign'].' '.$final_amount;
							}else echo "0.00";
						}else{
							$tamt = $row->tot_discount + $row->event_discount_amount;
							$final_amount = number_format($row->amount - $tamt - $addDiscount,2);
							if($final_amount > 0){
								echo $this->regpro_config['currency_sign'].' '.$final_amount;
							}else echo "0.00";
						}
					}
				?>
			</td>
			<td align="center">
				<a href="javascript: void(0);" onclick="return attendedstatus('cb<?php echo $i;?>',<?php echo $row->attended;?>,'changeattendstatus')">
					<img src="<?php echo ($row->attended==1) ? REGPRO_ADMIN_IMG_PATH.'/tick.png':REGPRO_ADMIN_IMG_PATH.'/publish_x.png';?>" width="16px" height="16px" border="0" alt="Attended" />
				</a></td>
			</td>
			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','pending_user')">
					<img src="<?php echo ($row->status==0) ? REGPRO_ADMIN_IMG_PATH.'/tick.png':REGPRO_ADMIN_IMG_PATH.'/publish_x.png';?>" width="16px" height="16px" border="0" alt="Pending" />
				</a>
			</td>

			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','accept_user')">
					<img src="<?php echo ($row->status==1) ? REGPRO_ADMIN_IMG_PATH.'/tick.png':REGPRO_ADMIN_IMG_PATH.'/publish_x.png';?>" width="16px" height="16px" border="0" alt="Accepted" />
				</a>
			</td>

			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','waiting_user')">
					<img src="<?php echo ($row->status==2) ? REGPRO_ADMIN_IMG_PATH.'/tick.png':REGPRO_ADMIN_IMG_PATH.'/publish_x.png';?>" width="16px" height="16px" border="0" alt="Waiting" />
				</a>
			</td>
			<td align="center"> <a href="javascript: void(0);" onclick="return paymentstatus('cb<?php echo $i;?>',<?php echo $payment_status;?>,'changepaymentstatus')"> <?php echo ucfirst($row->payment_status); ?> </a></td>
			<td align="center"><?php echo ucfirst($row->added_by); ?></td>
			<td align="center">
				<?php
				if(($row->amount * 1) == 0){
					echo "Free";
				}else{
				?>
				<a href="index.php?option=com_registrationpro&view=user&layout=transaction&tmpl=component&rid=<?php echo $row->rid; ?>" class="modal" rel="{handler:'iframe'}"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/transactions.png" border="0" alt="Details" />
				<?php
				} ?>
			</td>
			<td align="center"><?php echo $row->rid; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot><tr><td colspan="15"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

</table>
</div>
<p class="copyright"><?php $registrationproAdmin = new registrationproAdmin; echo $registrationproAdmin->footer();?></p>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_registrationpro" />
	<input type="hidden" name="view" value="users" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="users" />
	<input type="hidden" name="rdid" value="<?php echo $this->eventid; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="payment_status" value="0" />
	<input type="hidden" name="attended_status" value="0" />
</form>

</div>