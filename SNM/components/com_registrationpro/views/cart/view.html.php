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

class registrationproViewCart extends JViewLegacy
{		
	protected $params;
	function display($tpl = null){
				
		global $mainframe, $Itemid;
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			//echo "<pre>";print_r($menu);
			$this->params->def('page_heading', $menu->title);
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_FINDER_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);
		$layout = JRequest::getCmd('layout');
		
		if($layout == 'finalcheckout'){ // show final checkout page
	
			$this->finalcheckout();
			
		}else{
						
			$session = JFactory::getSession();
			$cart 	 = $session->get('cart');
			//echo "<pre>";print_r($cart);die;
			// check if registration form data exists to filled form values	
			if($cart) {		
				if(count($cart['form_data']['form']) > 0 && is_array($cart['form_data']['form'])) {			
					$_POST['form'] = $cart['form_data']['form'];
				}	
			}		
			//echo "<pre>";print_r($post_form_data); exit;
	
			// if cart session is expired
			/*if(!$cart){
				$link = JRoute::_("index.php?option=com_registrationpro&view=events");
				$msg = "Cart session has been expired";
				$mainframe->redirect($link, $msg);
			}*/
				
			//echo "<pre>"; print_r($_POST); exit;	
												
			// get component config settings
			$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
			$my 			= JFactory::getUser();
			$gid 			= (int) $my->get('aid', 0);
							
			//add css and js to document
			//registrationproHelper::add_regpro_frontend_scripts();						
			
			$row 	  = JRequest::getVar('row'); // get event details
			$ajaxflag = JRequest::getVar('ajaxflag');
			//echo $row->eventaccess;die;
			// check event access settings (registered, special)
			if($row->eventaccess > 1){		
				if(empty($my->id)){
					if($row->eventaccess == 2){
						$msg = JText::_('EVENTS_REGISTRA_SPECIAL_LOGIN');	
					}
					else{
						$msg = JText::_('EVENTS_REGISTRA_LOGIN');
					}
					$link = JRoute::_("index.php?option=com_registrationpro&controller=cart&task=login_required&did=".$row->did);			
					$mainframe->redirect($link, $msg);
				}elseif($row->eventaccess == 2 && $gid < 2){
					$msg = JText::_('EVENTS_REGISTRA_SPECIAL_LOGIN');
					$link = JRoute::_("index.php?option=com_registrationpro&controller=cart&task=login_required&did=".$row->did);			
					$mainframe->redirect($link, $msg);
				}											
			}
			// end
									
			//echo "<pre>"; print_r($cart); exit;
																									
			$cart_action 	= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=update_cart");		
			//$action 		= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=save_registration&Itemid=$Itemid");
			$action 		= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=final_checkout&Itemid=$Itemid");
			
			$this->assignRef('cart_form_action', $cart_action);																																										
			$this->assignRef('action', $action);
			$this->assignRef('row', $row);
			$this->assignRef('cart', $cart);
			$this->assignRef('my', $my);
			$this->assignRef('regproConfig', $regpro_config);
			$this->assignRef('ajaxflag', $ajaxflag);
			$this->assignRef('Itemid', $Itemid);
			
			/*$document	=  JFactory::getDocument();
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/lib/jquery.js');	
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/lib/jquery.metadata.js');	
			$document->addScript(JURI::root().'/components/com_registrationpro/assets/javascript/jquery.validate.js');	*/
			
			parent::display($tpl);
		}
	}
	
	
	function finalcheckout()
	{
		global $mainframe, $Itemid;
		
		// get component config settings
		$registrationproAdmin = new registrationproAdmin;
			$regpro_config	= $registrationproAdmin->config();
		$my 			= JFactory::getUser();
		$gid 			= (int) $my->get('aid', 0);
		
		$session 		= JFactory::getSession();
		$cart 	 		= $session->get('cart');
		$registration_data_session	= $session->get('registration_data_session');
		
		$action 		= JRoute::_("index.php?option=com_registrationpro&controller=cart&task=save_registration&Itemid=$Itemid");
		$carturl		= JRoute::_("index.php?option=com_registrationpro&view=cart&Itemid=$Itemid");
		
		$row 	  = JRequest::getVar('row'); // get event details
		//echo "<pre>";print_r($cart);die;
		$this->assignRef('row', $row);	
		$this->assignRef('cart', $cart);
		$this->assignRef('action', $action);
		$this->assignRef('carturl', $carturl);
		$this->assignRef('registration_data_session', $registration_data_session);
		$this->assignRef('regproConfig', $regpro_config);	
		$this->assignRef('Itemid', $Itemid);
		
		parent::display($tpl);				
	}
	
	function checktermsandconditions($eventids){
		global $mainframe, $Itemid;
		
		$db =  JFactory::getDBO();
		
		// Get events terms and conditions	
		$query = "SELECT terms_conditions FROM #__registrationpro_dates WHERE id in  (".implode(",",$eventids).") ORDER BY ordering";	
		$db->setQuery($query);
		$event_terms = $db->loadObjectList();
		
		$cflag = 0;
		
		if(is_array($event_terms) && count($event_terms) > 0) {
			foreach($event_terms as $key => $value){
				if(trim($value->terms_conditions) != ""){
					$cflag = 1;
				}
			}
		}
		
		//	echo "<pre>"; print_r($event_terms); exit;
		
		if($cflag == 1){
			return 1;
		}else{
			return 0;
		}											
	}							
}
?>