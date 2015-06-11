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
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder');

class registrationproViewRegistrationpro extends JViewLegacy
{
	function display($tpl = null) {
		jimport('joomla.html.pane');
		$document = JFactory::getDocument();
		$user = JFactory::getUser();
		JToolBarHelper::title( JText::_( 'COMPONENT_NAME' ), 'registrationpro.png' );
		//if (JFactory::getUser()->authorise('core.admin', 'com_registrationpro')) JToolBarHelper::preferences('com_registrationpro');
		$this->assignRef('pane', $pane);
		$this->assignRef('user', $user);

		global $mainframe, $option;

		$option = JRequest::getCMD('option');
		$user   = JFactory::getUser();
		$db     = JFactory::getDBO();
		
		$query = "SELECT * FROM #__registrationpro_dates ORDER BY dates";
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		$this->assignRef('rows', $rows);
		
		$query = "SELECT * FROM #__registrationpro_payment";
		$db->setQuery($query);
		$pays = $db->loadAssocList();
		$this->assignRef('pays', $pays);
		
		$query = "SELECT * FROM #__registrationpro_register";
		$db->setQuery($query);
		$regs = $db->loadAssocList();
		$this->assignRef('regs', $regs);
		
		//$query = "SELECT * FROM #__registrationpro_transactions ORDER BY payment_date DESC";
		$query  = "SELECT t.*,dt.event_discount_amount FROM #__registrationpro_transactions AS t ";
		$query .= " LEFT JOIN #__registrationpro_event_discount_transactions AS dt ";
		$query .= " ON dt.trans_id = t.id ORDER BY t.payment_date DESC";
		$db->setQuery($query);
		$tran = $db->loadAssocList();
		//echo "<pre>";print_r($tran);die;
		$this->assignRef('tran', $tran);

		$model = $this->getModel('registrationpro');
		$data = $model->getEventReportData();
		$productdata = $model->getData();
		
		foreach($productdata as $pkey => $pvalue){
			if($pvalue->event_discount_amount > 0){
				if($pvalue->event_discount_type == 'P'){
					$event_discounted_amount_price 	= 0;
					$actual_price_without_per 		= 0;
					$actual_price_without_per 		= ($pvalue->price * 100) / (100 - $pvalue->event_discount_amount);
					$event_discounted_amount_price 	= $actual_price_without_per * $pvalue->event_discount_amount / 100;
					$pvalue->discount_amount		+= $event_discounted_amount_price;
					$pvalue->price 			 		= $actual_price_without_per;
				}
			}
		}
		$this->assignRef('data', $data);
		$this->assignRef('productdata', $productdata);
		
		parent::display($tpl);
	}

	function quickiconButton( $link, $image, $text, $modal = 0 ) {
		$lang = JFactory::getLanguage();
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<?php
				if ($modal == 1) {
					JHTML::_('behavior.modal');
				?>
					<a href="<?php echo $link.'&amp;tmpl=component'; ?>" style="cursor:pointer" class="modal" rel="{handler:'iframe',size:{x:650,y:400}}">
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>">
				<?php
				}
					echo JHTML::_('image', 'administrator/components/com_registrationpro/assets/images/'.$image, $text );
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}
?>