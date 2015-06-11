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

class registrationproViewStat_Charts extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		$option = @JRequest::getCMD('option');
		$db				= JFactory::getDBO();
		$editor 		= JFactory::getEditor();
		$user 			= JFactory::getUser();
		$document		= JFactory::getDocument();
		$registrationproAdmin = new  registrationproAdmin;
		$regpro_config = $registrationproAdmin->config();
		$regpro_config['joomlabase'] = JPATH_SITE;

		$month		 		= $mainframe->getUserStateFromRequest( $option.'.report_month', 'month', '', 'int' );
		$year	 			= $mainframe->getUserStateFromRequest( $option.'.report_year', 'year', '', 'int' );
		$payment_status		= $mainframe->getUserStateFromRequest( $option.'.payment_status', 'payment_status', '', 'int' );

		$data 			= array ();
		$data['month'] 	= $month;
		$data['year'] 	= $year;

		$task 			= JRequest::getVar( 'task' );

		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_scripts();

		$layout = JRequest::getCmd('layout');

		if($layout == 'print_report'){
			$this->print_report(); // show print report window
		}else{
			$pageNav = @$this->get( 'Pagination' );
			$Lists = array();

			// Months list
			$months 			= array();
			$months[] 			= JHTML::_('select.option', '1', JText::_(ADMIN_REPORT_JANUARY));
			$months[] 			= JHTML::_('select.option', '2', JText::_(ADMIN_REPORT_FEBRUARY));
			$months[] 			= JHTML::_('select.option', '3', JText::_(ADMIN_REPORT_MARCH));
			$months[] 			= JHTML::_('select.option', '4', JText::_(ADMIN_REPORT_APRIL));
			$months[] 			= JHTML::_('select.option', '5', JText::_(ADMIN_REPORT_MAY));
			$months[] 			= JHTML::_('select.option', '6', JText::_(ADMIN_REPORT_JUNE));
			$months[] 			= JHTML::_('select.option', '7', JText::_(ADMIN_REPORT_JULY));
			$months[] 			= JHTML::_('select.option', '8', JText::_(ADMIN_REPORT_AUGUST));
			$months[] 			= JHTML::_('select.option', '9', JText::_(ADMIN_REPORT_SEPTEMBER));
			$months[] 			= JHTML::_('select.option', '10', JText::_(ADMIN_REPORT_OCTOBER));
			$months[] 			= JHTML::_('select.option', '11', JText::_(ADMIN_REPORT_NOVEMBER));
			$months[] 			= JHTML::_('select.option', '12', JText::_(ADMIN_REPORT_DECEMBER));
			$Lists['months']	=  JHTML::_('select.genericlist', $months, 'month', 'class="inputbox" size="1" alt="blank" emsg="'.JText::_('ADMIN_REPORT_ENTER_MONTH').'"','value', 'text');
			
			$listM = array();
			$listM[] = JText::_(ADMIN_REPORT_JANUARY);
			$listM[] = JText::_(ADMIN_REPORT_FEBRUARY);
			$listM[] = JText::_(ADMIN_REPORT_MARCH);
			$listM[] = JText::_(ADMIN_REPORT_APRIL);
			$listM[] = JText::_(ADMIN_REPORT_MAY);
			$listM[] = JText::_(ADMIN_REPORT_JUNE);
			$listM[] = JText::_(ADMIN_REPORT_JULY);
			$listM[] = JText::_(ADMIN_REPORT_AUGUST);
			$listM[] = JText::_(ADMIN_REPORT_SEPTEMBER);
			$listM[] = JText::_(ADMIN_REPORT_OCTOBER);
			$listM[] = JText::_(ADMIN_REPORT_NOVEMBER);
			$listM[] = JText::_(ADMIN_REPORT_DECEMBER);

			$query = "SELECT catsid, id, titel, dates, enddates FROM #__registrationpro_dates WHERE status>=0 ORDER BY catsid, titel";
			$db->setQuery($query);
			$listE = $db->loadRowList();
			
			$query = "SELECT DATE_FORMAT(dates, '%Y') FROM #__registrationpro_dates GROUP BY DATE_FORMAT(dates, '%Y')";
			$db->setQuery($query);
			$tmp = $db->loadRowList();
			$listY = array();
			if(count($tmp) > 0) foreach ($tmp as $tt) $listY[] = $tt[0];
			
			$query = "SELECT MIN(DATE_FORMAT(dates, '%Y')) FROM #__registrationpro_dates";
			$db->setQuery($query);
			$minY = $db->loadResult() * 1;
			
			$query = "SELECT MAX(DATE_FORMAT(dates, '%Y')) FROM #__registrationpro_dates";
			$db->setQuery($query);
			$maxY = $db->loadResult() * 1;
			
			$curY = date('Y') * 1;
			
			$years 	= array();
			for ($i = $minY; $i<=$maxY; $i++) $years[] = JHTML::_('select.option', $i.'', $i.'');
			$Lists['years']	=  JHTML::_('select.genericlist', $years, 'year', 'class="inputbox" size="1" alt="blank" emsg="'.JText::_('ADMIN_REPORT_ENTER_YEAR').'"','value', 'text');

			// Payment status
			$payment_status 		 = array();
			$payment_status[] 		 = JHTML::_('select.option', '', JText::_("ADMIN_REPORT_PAYMENT_STATUS_SELECT_ONE"));
			$payment_status[] 		 = JHTML::_('select.option', '1', 'Accepted');
			$payment_status[] 		 = JHTML::_('select.option', '2', 'Pending');
			$Lists['payment_status'] =  JHTML::_('select.genericlist', $payment_status, 'payment_status', 'class="inputbox" size="1"','value', 'text');
			
			$listP = array();
			$listP[] = 'Accepted';
			$listP[] = 'Pending';

			// Categories Lists
			$categories 	= array();
			$categories[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_CAT));
			$all_categories	= @registrationproHelper::getAllCategory();
			$categories 	= array_merge( $categories, $all_categories);
			$Lists['categories'] = JHTML::_('select.genericlist', $categories, 'cat', 'class="inputbox" size="1"','value', 'text');
			
			//assign data to template
			$this->assignRef('Lists'      	, $Lists);
			$this->assignRef('template'		, $template);
			$this->assignRef('regpro_config' , $regpro_config);
			$this->assignRef('task' 		, $task);
			$this->assignRef('listE' 		, $listE);
			$this->assignRef('listY' 		, $listY);
			$this->assignRef('listM' 		, $listM);
			$this->assignRef('listP' 		, $listP);
			$this->assignRef('listC' 		, $all_categories);
			
			$tsk = JRequest::getVar('tsk', '');
			if(($tsk != '')&&($tsk == 'show_chart')) {

				$model		 = $this->getModel('stat_charts');
				$data 		 = $model->getEventReportData();
				$productdata = $model->getData();

				foreach($productdata as $pkey => $pvalue) {
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
			
			}
			parent::display($tpl);
		}
	}
}
?>