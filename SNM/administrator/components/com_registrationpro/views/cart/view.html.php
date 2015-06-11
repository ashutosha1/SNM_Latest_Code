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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class registrationproViewCart extends JViewLegacy
{		
	function display($tpl = null){
		global $mainframe, $Itemid;
		
		$layout = JRequest::getCmd('layout');
		
		if($layout == 'finalcheckout'){ // show final checkout page
	
			$this->finalcheckout();
			
		}else{
		
			$session = JFactory::getSession();		
			$cart 	 = $session->get('cart');
	
			// if cart session is expired
			if(!$cart){
				$link = JRoute::_("index.php?option=com_registrationpro&view=events");
				$msg = "Cart session has been expired";
				$mainframe->redirect($link, $msg);
			}
				
			//echo "<pre>"; print_r($_POST); exit;	
												
			// get component config settings
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
			$my 			=JFactory::getUser();
							
			//add css and js to document
			//registrationproHelper::add_regpro_frontend_scripts();						
			
			$row 	  = JRequest::getVar('row'); // get event details
			$ajaxflag = JRequest::getVar('ajaxflag');
			//echo "<pre>"; print_r($row); exit;
																									
			$cart_action 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=update_cart");		
			//$action 		= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=save_registration&Itemid=$Itemid");
			$action 		= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=final_checkout&hidemainmenu=1");
			
			$this->assignRef('cart_form_action', $cart_action);																																										
			$this->assignRef('action', $action);
			$this->assignRef('row', $row);
			$this->assignRef('cart', $cart);
			$this->assignRef('my', $my);
			$this->assignRef('regproConfig', $regpro_config);
			$this->assignRef('ajaxflag', $ajaxflag);
			$this->assignRef('Itemid', $Itemid);
			
			parent::display($tpl);
		}
	}
	
	function finalcheckout()
	{
		global $mainframe, $Itemid;
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
		$regpro_config	= $registrationproAdmin->config();
		$my 			=JFactory::getUser();
		$gid 			= (int) $my->get('aid', 0);
		
		$session 		= JFactory::getSession();
		$cart 	 		= $session->get('cart');
		$registration_data_session	= $session->get('registration_data_session');
		
		$row = $this->getEventDet($cart['eventid']);
		
		$action 		= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=save_registration&hidemainmenu=1");
		$carturl		= JRoute::_("index.php?option=com_registrationpro&view=cart&hidemainmenu=1");
		
		$this->assignRef('row', $row);	
		$this->assignRef('cart', $cart);
		$this->assignRef('action', $action);
		$this->assignRef('carturl', $carturl);
		$this->assignRef('registration_data_session', $registration_data_session);
		$this->assignRef('regproConfig', $regpro_config);	
		$this->assignRef('Itemid', $Itemid);
		
		parent::display($tpl);				
	}	
		function getEventDet($id)
	{
		$db=JFactory::getDBO();
		// Lets load the content if it doesn't already exist
										
			$query = "SELECT a.id AS did, a.dates, a.titel, a.times, a.endtimes, a.enddates, a.endtimes, a.datdescription, a.datimage, a.registra, a.unregistra, a.locid, a.catsid, a.max_attendance, a.regstart, a.regstop, a.form_id, a.terms_conditions, a.access as eventaccess, a.allowgroup, a.shw_attendees, a.regstop_type, a.force_groupregistration,a.enable_mailchimp, a.mailchimp_list,a.enable_create_user,a.enabled_user_group,"
					. "\n l.id as lid, l.club, l.city, l.url, l.locdescription, l.locimage, l.city, l.plz, l.street, l.country,"
					. "\n c.id as cid, c.catname, c.image, c.catdescription, c.access"
					. "\n FROM #__registrationpro_dates AS a"
					. "\n LEFT JOIN #__registrationpro_locate AS l ON a.locid = l.id"
					. "\n LEFT JOIN #__registrationpro_categories AS c ON c.id = a.catsid WHERE a.published = 1 AND a.id=".$id;
			
			$db->setQuery($query);
			$data = $db->loadObject();									
			$data = $this->_additionalsDet($data,$id);
		
		return $data;
	}		
	
	function _additionalsDet($rows,$id)
	{		
		$db=JFactory::getDBO();	
		$query = "SELECT * FROM #__registrationpro_event_discount WHERE event_id = ".$id;	
		$db->setQuery($query);
		$rows->event_discounts = $db->loadObjectList();
					
		return $rows;
	}
	
}
?>