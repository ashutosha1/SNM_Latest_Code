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
JHTML::_( 'behavior.modal' );

//echo"<pre>";print_r($this->rows); exit;
?>
<div id="regpro">
<?php
$regpro_header_footer = new regpro_header_footer;
$regpro_header_footer->regpro_header($this->regpro_config);

// user toolbar
$regpro_html = new regpro_html;
$regpro_html->user_toolbar();

$regpro_html->myusers_toolbar();
$registrationproHelper = new registrationproHelper;
?>
<script language="javascript" type="text/javascript">		
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		
		var rdid = form.rdid.value;	
		
		if (pressbutton == 'remove' || pressbutton == 'accept_user' || pressbutton == 'pending_user') {	
			if(form.boxchecked.value > 0){			
				submitform( pressbutton );
				return;
			}else{
				alert("Please select the record first.");
			}
		}else if (pressbutton == 'event_report') {					
			//report_open("index.php?option=com_registrationpro&controller=myevents&task=event_report&tmpl=component&print=1&did="+rdid);
			report_open("index.php?option=com_registrationpro&view=myevent&layout=event_report&tmpl=component&print=1&cid="+rdid);
			return false;								
		}else if (pressbutton == 'excel_report') {
			//window.location = "index.php?option=com_registrationpro&controller=myevents&task=excel_report&did="+rdid;
			window.location = "index.php?option=com_registrationpro&controller=myevents&task=excel_report&cid="+rdid;
		}else if(pressbutton == 'add_user'){	
			window.location = "index.php?option=com_registrationpro&view=event&did="+rdid;
		}else {
			submitform( pressbutton );
		}
	}
	
	function report_open(url_add)
   	{
	   window.open(url_add,"","width=1200,height=800,menubar=no,status=no,location=no,toolbar=no,scrollbars=1");
   	}
	
	function paymentstatus(id,status,task)
	{		
		var form = document.adminForm;
		
		document.getElementById(id).checked = true;	
		form.payment_status.value = status;
		submitform(task);
	}

</script>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm"  id="adminForm">
<table>
	<tr>
		<td style="width:30%"> <?php echo JText::_('ADMIN_EVENTS_TITEL');?> </td>
		<td> <?php echo $this->eventInfo->titel; ?> </td>
	</tr>
	<tr>				
		<td> <?php echo JText::_('ADMIN_EVENTS_DATE'); ?> </td>
		<td> <?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $this->eventInfo->dates); ?> &nbsp; to &nbsp;	<?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $this->eventInfo->enddates); ?>
	</td>
</tr>
</table>
		
<!--<table class="adminform">
	<tr>
		<td width="100%">
			 <?php echo JText::_( 'SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		</td>
		<td nowrap="nowrap"><?php echo $this->lists['state']; ?></td>
	</tr>
</table>-->

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="2%"><?php echo JText::_( 'Num' ); ?></th>
			<th width="2%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
			<th width="13%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_USER_FIRST_NAME'), 'r.firstname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="13%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_USER_LAST_NAME'), 'r.lastname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="13%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_USER_EMAIL_ADDRESS'), 'r.email', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="13%"><?php echo JHTML::_('grid.sort', JText::_('ADMIN_EVENTS_DEFAULT_PRODUCT'), 'r.uregdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="7%"><?php echo JText::_( 'ADMIN_USER_REG_AMOUNT' ); ?></th>						
			<th width="7%"><?php echo JText::_( 'ADMIN_EVENTS_USERPENDING' ); ?></th>		
			<th width="7%"><?php echo JText::_( 'ADMIN_EVENTS_USERACCEPTED' ); ?></th>					
			<!--<th width="7%"><?php echo JText::_( 'ADMIN_EVENTS_USERWAITING' ); ?></th>-->
			<th width="6%"><?php echo JText::_( 'ADMIN_EVENTS_PAYMENT_STATUS' ); ?></th>		
			<!--<th width="7%"><?php echo JHTML::_('grid.sort',JText::_('ADMIN_EVENTS_USER_ADDED_BY'),'r.added_by',$this->lists['order_Dir'],$this->lists['order'] ); ?></th>-->			
			<th width="7%"><?php echo JText::_( 'ADMIN_EVENTS_USERTRANSACTION' ); ?></th>									
		    <th width="3%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'r.rid', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row 	= $this->rows[$i];
			//$link = JRoute::_('index.php?option=com_registrationpro&controller=users&task=edit&rcid[]='. $row->rid.'&rdid='.$this->eventid,false);			
			$link 	= JRoute::_('index.php?option=com_registrationpro&view=user&rcid='. $row->rid.'&rdid='.$this->eventid,false);			
			$checked 	= JHTML::_('grid.checkedout', $row, $i );
			if(strtolower($row->payment_status) == "pending"){
				$payment_status = 0;				
			}else{
				$payment_status = 1;
			}
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td align="center"> <input type="checkbox" id="cb<?php echo $i;?>" name="rcid[]" value="<?php echo $row->rid; ?>" onclick="Joomla.isChecked(this.checked);" /></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'ADMIN_EDIT_USER' );?>::<?php echo $row->firstname; ?>">
					<a href="<?php echo $link; ?>"> <?php echo $row->firstname; ?> </a>
				</span>			
			</td>
			<td align="left"><?php echo $row->lastname; ?> </td>
			<td align="left"><?php echo $row->email; ?></td>			
			<td align="left"> <?php echo $registrationproHelper->getFormatdate($this->regpro_config['formatdate'], $row->uregdate); ?>
			</td>
			<td align="center">
				<?php
					//if($row->price == 0.00){
					if($row->amount == 0.00){
						echo "Free";
					}else{
						if($row->event_discount_type == "P"){				
							$final_amount = number_format($row->amount - $row->tot_discount,2);
							if($final_amount > 0){
								echo $final_amount;
							}else{
								echo "0.00";
							}
						}else{
							$tamt = $row->tot_discount + $row->event_discount_amount;
							$final_amount = number_format($row->amount - $tamt,2);
							if($final_amount > 0){
								echo $final_amount;
							}else{
								echo "0.00";
							}
						}
					}
				?>
			</td>			
								
			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','pending_user')">
					<img src="<?php echo ($row->status==0) ? REGPRO_IMG_PATH.'/ball_green.png':REGPRO_IMG_PATH.'/ball_red.png';?>" width="16px" height="16px" border="0" alt="Pending" />
				</a>
			</td>
			
			<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','accept_user')">
					<img src="<?php echo ($row->status==1) ? REGPRO_IMG_PATH.'/ball_green.png':REGPRO_IMG_PATH.'/ball_red.png';?>" width="16px" height="16px" border="0" alt="Accepted" />
				</a>
			</td>
			
			<!--<td align="center">
				<a href="javascript: void(0);" onclick="return listItemTask('cb<?php echo $i;?>','waiting_user')">
					<img src="<?php echo ($row->status==2) ? REGPRO_ADMIN_IMG_PATH.'/tick.png':REGPRO_IMG_PATH.'/publish_x.png';?>" width="16px" height="16px" border="0" alt="Waiting" />
				</a>
			</td>-->
			<td align="center"> <a href="javascript: void(0);" onclick="return paymentstatus('cb<?php echo $i;?>',<?php echo $payment_status;?>,'changepaymentstatus')"> <?php echo ucfirst($row->payment_status); ?> </a></td>												
			<!--<td align="center"><?php echo ucfirst($row->added_by); ?></td>		-->	
			<td align="center">
				<?php
				//if($row->price == 0.00){
				if($row->amount == 0.00){
					echo "Free";
				}else{
				?>
				
				<!--<a href="index.php?option=com_registrationpro&controller=users&task=transaction_details&rid=<?php echo $row->rid; ?>" class="modal"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/transactions.png" border="0" alt="Details" />-->
				<!--<a href="index.php?option=com_registrationpro&view=user&layout=transaction&rid=<?php echo $row->rid; ?>" class="modal"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/transactions.png" border="0" alt="Details" />-->
				<!--<a href="index.php?option=com_registrationpro&controller=users&task=transaction_details&rid=<?php echo $row->rid; ?>" class="modal" rel="{handler: 'iframe'}"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/transactions.png" border="0" alt="Details" />-->
				
				<a href="index.php?option=com_registrationpro&view=user&layout=transaction&tmpl=component&rid=<?php echo $row->rid; ?>" class="modal" rel="{handler: 'iframe'}"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/transactions.png" border="0" alt="Details" />
												
				<!--<a href="javascript: void(0);" onclick="window.open('index.php?option=<?php echo $option.'&task=transaction_details&hidemainmenu=1&rdid='.$row->rid;?>','TransactionDetails','resizable,width=760,height=600,scrollbars');"><img src="<?php echo REGPRO_ADMIN_IMG_PATH; ?>/transactions.png" border="0" alt="Details" /></a>-->
				<?php
				} ?>
			</td>
			<td align="center"><?php echo $row->rid; ?></td>
		</tr>
		<?php $k = 1 - $k; } ?>

	</tbody>

	<tfoot>
		<tr>
			<td colspan="12">
				<div class="pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
			</td>
		</tr>
	</tfoot>

</table>
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
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
</form>

<?php
$regpro_header_footer->regpro_footer($this->regpro_config);
?>
</div>