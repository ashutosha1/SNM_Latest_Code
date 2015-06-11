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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class registrationproViewEvent extends JViewLegacy
{
	function display($tpl = null)
	{
		global $mainframe;

		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');

		$layout = JRequest::getCmd('layout');

		if($layout == 'event_report'){
			$this->event_report(); // show event report
		}else{

			//initialise variables
			$editor 		= JFactory::getEditor();
			$user 			= JFactory::getUser();
			$db 			= JFactory::getDBO();
			$document		= JFactory::getDocument();
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();

			//get vars
			$cid 			= JRequest::getInt( 'cid' );
			$task 			= JRequest::getVar( 'task' );

			//add css and js to document
			$document	= JFactory::getDocument();
			$document->addScript('components/com_registrationpro/assets/javascript/CalendarPopup.js');
			$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();

			// Get data from the model
			$model	= $this->getModel('event');

			$row = $this->get('Data');
			
			$row->products        = $model->getTickets($row->id);
			$row->event_discounts = $model->getEvent_discounts($row->id);
			$row->event_sessions  = $model->getEvent_sessions($row->id);
			$userGroups = $model->getUserGroups();

			// Access List
			$Lists = array();
			$Lists['access']	= JHtml::_('access.assetgrouplist','access',$row->access);
			$Lists['viewaccess'] = JHtml::_('access.assetgrouplist', 'viewaccess',$row->viewaccess); //JHTML::_('list.accesslevel',$row);

			// Event status list
			$event_status   = array();
			$event_status[] = JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_STATUS_0));
			$event_status[] = JHTML::_('select.option',  '1', JText::_(ADMIN_EVENTS_STATUS_1));
			$event_status[] = JHTML::_('select.option',  '2', JText::_(ADMIN_EVENTS_STATUS_2));
			$event_status[] = JHTML::_('select.option',  '3', JText::_(ADMIN_EVENTS_STATUS_3));
			$event_status[] = JHTML::_('select.option',  '4', JText::_(ADMIN_EVENTS_STATUS_4));
			$event_status[] = JHTML::_('select.option',  '5', JText::_(ADMIN_EVENTS_STATUS_5));
			$Lists['event_status'] = JHTML::_('select.genericlist', $event_status, 'status', 'class="inputbox" size="1"','value', 'text', $row->status);

			// Locations list
			$locations 		= array();
			$locations[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_LOC));
			$all_locations 	= $this->get( 'Locations' );
			$locations 		= array_merge( $locations, $all_locations);
			$Lists['locations']	=  JHTML::_('select.genericlist', $locations, 'locid', 'class="inputbox" size="1" alt="select" emsg="'. JText::_('EVENTS_DEL_LOCAT_EMPT') .'"','value', 'text', $row->locid );

			// Categories Lists
			$categories 	= array();
			$categories[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_CAT));
			$all_categories	= $this->get( 'Categories' );
			$categories 	= array_merge( $categories, $all_categories);
			$Lists['categories'] = JHTML::_('select.genericlist', $categories, 'catsid', 'class="inputbox" size="1" alt="select" emsg="'. JText::_('EVENTS_DEL_CATEG_EMPT') .'"','value', 'text', $row->catsid );

			// Forms Lists
			$forms 		= array();
			$forms[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_REGFORM));
			$all_forms	= $this->get( 'Forms' );
			$forms 		= array_merge( $forms, $all_forms);
			$Lists['forms'] = JHTML::_('select.genericlist', $forms, 'form_id', 'class="inputbox" size="1" alt="select" emsg="'.  JText::_('EVENTS_REGISTER_FORM') .'"', 'value', 'text', $row->form_id );

			// notifydate_types Lists
			$regstop_type			= array();
			$regstop_type[] 		= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_REGSTOPFROM_STARTDATE));
			$regstop_type[] 		= JHTML::_('select.option',  '1', JText::_(ADMIN_EVENTS_REGSTOPFROM_ENDDATE));
			$Lists['regstop_type'] 	= JHTML::_('select.genericlist', $regstop_type, 'regstop_type', 'class="inputbox" size="1"', 'value', 'text', $row->regstop_type);

			// get payment plugins
			$registrationproHelper = new registrationproHelper;
			$payment_methods = $registrationproHelper->getPaymentMethods();
			if(!is_array($payment_methods)){
				$payment_methods	= array();
			}
			$payment_type			= array();
			//$payment_type[] 		= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_PAYMENT_METHOD));
			$payment_plugins		= array_merge( $payment_type, $payment_methods);

			if(!$row->payment_method) {
				$payment_method = $payment_plugins;
			}else{
				$payment_method = explode(",",$row->payment_method);
			}


			$payment_condition = "";
			if($regpro_config['multiple_registration_button'] != 1){
				$payment_condition = 'alt="selectm|1|*" emsg="'.  JText::_('EVENTS_REGISTER_SELECT_PAYMENT') .'"';
			}

			$Lists['payment_method'] 	= JHTML::_('select.genericlist', $payment_plugins, 'payment_method[]', 'class="inputbox" multiple '.$payment_condition, 'value', 'text', $payment_method);
			//echo "<pre>"; print_r($Lists); exit;

			//assign data to template
			$this->assignRef('Lists', $Lists);
			$this->assignRef('row'	, $row);

			// check mootools 1.2 version pluign is enable or not
			//$upgrade_mootools_enabled = registrationproHelper::check_mootools_version();

			//assign data to template
			$this->assignRef('user'			, $user);
			$this->assignRef('template'		, $template);
			$this->assignRef('userGroups'	, $userGroups);
			$this->assignRef('editor'      	, $editor);
			$this->assignRef('regpro_config', $regpro_config);
			$this->assignRef('task' 		, $task);
			$this->assignRef('upgrade_mootools', $upgrade_mootools_enabled);

			parent::display($tpl);
		}
	}

	function event_report()
	{
		global $mainframe;

		//initialise variables
		$editor 		= JFactory::getEditor();
		$user 			= JFactory::getUser();
		$db 			= JFactory::getDBO();
		$document		= JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();

		//echo "<pre>"; print_r($regpro_config); exit;

		//get vars
		$cid 			= JRequest::getInt( 'cid' );
		$task 			= JRequest::getVar( 'task' );

		//add css and js to document
		$registrationproHelper = new registrationproHelper; $registrationproHelper->add_regpro_scripts();

		// Get data from the model
		$model	= $this->getModel('event');

		$layout = JRequest::getCmd('layout');

		$data 			= $model->getEventReportData();
		$productdata 	= $model->getEventTransactionData();
		$additional_form_fees 	= $model->getAdditionalFormFeesTransactionData();
		$session_fees 			= $model->getSessionsfeesTransactionData();
		//echo "<pre>"; print_r($data); exit;
		// apply event discount upon user tickets
		foreach($productdata as $pkey => $pvalue)
		{
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

			// assign additional form fields fees
			if(count($additional_form_fees) > 0 && is_array($additional_form_fees) && $pvalue->p_type == "E"){
				foreach($additional_form_fees as $akey => $avalue)
				{
					if($avalue->reg_id == $pvalue->reg_id) {
						$pvalue->additional_field_fees[] = $avalue;
					}
				}
			}
			// end

			// assign session fees
			if(count($session_fees) > 0 && is_array($session_fees) && $pvalue->p_type == "E"){
				foreach($session_fees as $skey => $svalue)
				{
					if($svalue->reg_id == $pvalue->reg_id) {
						$pvalue->session_fees[] = $svalue;
					}
				}
			}
			// end
		}
		// end

		//echo "<pre>"; print_r($productdata); exit;
		$this->assignRef('data', $data);
		$this->assignRef('productdata', $productdata);
		$this->assignRef('rdid', $model->_id);

		//assign data to template
		$this->assignRef('user'			, $user);
		$this->assignRef('template'		, $template);
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('regpro_config', $regpro_config);
		$this->assignRef('task' 		, $task);

		parent::display($tpl);
	}

}

?>