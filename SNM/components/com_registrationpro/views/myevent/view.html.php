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

class registrationproViewMyevent extends JViewLegacy
{		
	function display($tpl = null)
	{
		global $mainframe, $Itemid;		
		$registrationproHelper = new registrationproHelper;
		if(!$registrationproHelper->checkUserAccount()) {
			$link 	= JRoute::_("index.php?option=com_registrationpro&view=events&Itemid=".$Itemid, false);		
			$mainframe->redirect($link);
		}
		
		// Load pane behavior
		jimport('joomla.html.pane');
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		
		$layout = JRequest::getCmd('layout');	
		
		if($layout == 'event_report'){
		
			$this->event_report(); // show event report
			
		}else{

			//initialise variables
			$editor 		=  JFactory::getEditor();
			$user 			=  JFactory::getUser();
			$db 			=  JFactory::getDBO();
			$document		=  JFactory::getDocument();
			$registrationproAdmin =new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
					
			//echo "<pre>"; print_r($regpro_config); exit;														
			//get vars
			$cid 			= JRequest::getInt( 'cid' );
			$task 			= JRequest::getVar( 'task' );
			
			$document	=  JFactory::getDocument();
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.config.js');
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.core.js');
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.lang-enUS.js');
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.validators.js');
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.controls.js');
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/fvalidate/fValidate.datetime.js');
			$document->addScript(JURI::root().'/administrator/components/com_registrationpro/assets/javascript/CalendarPopup.js');
			$document->addScript(JURI::root().'/administrator/components/com_registrationpro/assets/javascript/recurrence.js');
			
			//add css and js to document
			//$registrationproHelper->add_regpro_scripts();	
			$registrationproHelper = new registrationproHelper;			
			$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());
			
			// Get data from the model
			$model	=  $this->getModel('myevent');							
			
			
			$row    				=  $this->get('Data');
			$row->products 			=  $model->getTickets($row->id);
			$row->event_discounts 	=  $model->getEvent_discounts($row->id);	
			$row->event_sessions 	=  $model->getEvent_sessions($row->id);		
			//echo "<pre>"; print_r($row); exit;							
									
			//publish unpublished filter
			$Lists = array();
			//$Lists['state']	= JHTML::_('grid.state', $filter_state );
				
			// Access List
			//$Lists['access']	= JHTML::_('list.accesslevel',$row->access);
			
			$Lists['access'] = JHTML::_('access.assetgrouplist', 'access',$row->access);
			
			$Lists['viewaccess'] = JHTML::_('access.assetgrouplist', 'viewaccess',$row->viewaccess); //JHTML::_('list.accesslevel',$row);
	
			// Event status list
			$event_status   = array();							
			$event_status[] = JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_STATUS_0));
			$event_status[] = JHTML::_('select.option',  '1', JText::_(ADMIN_EVENTS_STATUS_1));
			$event_status[] = JHTML::_('select.option',  '2', JText::_(ADMIN_EVENTS_STATUS_2));
			$event_status[] = JHTML::_('select.option',  '3', JText::_(ADMIN_EVENTS_STATUS_3));
			$event_status[] = JHTML::_('select.option',  '4', JText::_(ADMIN_EVENTS_STATUS_4));		
			$Lists['event_status'] = JHTML::_('select.genericlist', $event_status, 'status', 'class="inputbox" size="1"','value', 'text', $row->status);
					
			// Locations list
			$locations 		= array();
			$locations[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_LOC));
			$all_locations 	=  $this->get( 'Locations' );
			$locations 		= array_merge( $locations, $all_locations);
			$Lists['locations']	=  JHTML::_('select.genericlist', $locations, 'locid', 'class="inputbox" size="1" alt="select" emsg="'. JText::_('EVENTS_DEL_LOCAT_EMPT') .'"','value', 'text', $row->locid );
		
			// Categories Lists
			$categories 	= array();
			$categories[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_CAT));
			$all_categories	=  $this->get( 'Categories' );
			$categories 	= array_merge( $categories, $all_categories);
			$Lists['categories'] = JHTML::_('select.genericlist', $categories, 'catsid', 'class="inputbox" size="1" alt="select" emsg="'. JText::_('EVENTS_DEL_CATEG_EMPT') .'"','value', 'text', $row->catsid );
			
			// Forms Lists
			$forms 		= array();
			$forms[] 	= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_SEL_REGFORM));
			$all_forms	=  $this->get( 'Forms' );
			$forms 		= array_merge( $forms, $all_forms);
			$Lists['forms'] = JHTML::_('select.genericlist', $forms, 'form_id', 'class="inputbox" size="1" alt="select" emsg="'.  JText::_('EVENTS_REGISTER_FORM') .'"', 'value', 'text', $row->form_id );
			
			// notifydate_types Lists
			$regstop_type			= array();
			$regstop_type[] 		= JHTML::_('select.option',  '0', JText::_(ADMIN_EVENTS_REGSTOPFROM_STARTDATE));
			$regstop_type[] 		= JHTML::_('select.option',  '1', JText::_(ADMIN_EVENTS_REGSTOPFROM_ENDDATE));			
			$Lists['regstop_type'] 	= JHTML::_('select.genericlist', $regstop_type, 'regstop_type', 'class="inputbox" size="1"', 'value', 'text', $row->regstop_type);
			
			// get payment plugins
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
			//$upgrade_mootools_enabled = $registrationproHelper->check_mootools_version();			
			
			//assign data to template
			$this->assignRef('user'			, $user);
			$this->assignRef('template'		, $template);
			$this->assignRef('editor'      	, $editor);
			$this->assignRef('regpro_config', $regpro_config);
			$this->assignRef('task' 		, $task);	
			//$this->assignRef('upgrade_mootools' , $upgrade_mootools_enabled);
			$this->assignRef('Itemid'		,$Itemid);	
			
			parent::display($tpl);
		}
	}
	
	
	
	function event_report()
	{
		global $mainframe, $Itemid;
				
		//initialise variables
		$editor 		=  JFactory::getEditor();
		$user 			=  JFactory::getUser();
		$db 			=  JFactory::getDBO();
		$document		=  JFactory::getDocument();
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config 	=  $registrationproAdmin->config();
				
		//echo "<pre>"; print_r($regpro_config); exit;
		
		//get vars
		$cid 			= JRequest::getInt( 'id' );
		$task 			= JRequest::getVar( 'task' );
		
		//add css and js to document
		$registrationproHelper = new registrationproHelper;
		$registrationproHelper->add_regpro_frontend_scripts(array('regpro','regpro_calendar'),array());		
		
		// Get data from the model
		$model	=  $this->getModel('myevent');
				
		$layout = JRequest::getCmd('layout');		
					
		$data 			= $model->getEventReportData();
		$productdata 	= $model->getEventTransactionData();			
	
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
		$this->assignRef('Itemid'		,$Itemid);	
		
		parent::display($tpl);			
	}
		
}

?>