<?php
/**
* @package Access-Manager (com_accessmanager)
* @version 2.2.1
* @copyright Copyright (C) 2012 - 2014 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

class accessmanagerViewPart extends JViewLegacy{

	function display($tpl = null){
	
		$controller = new accessmanagerController();	
		$this->assignRef('controller', $controller);
		
		$helper = new accessmanagerHelper();
		//$this->assignRef('helper', $helper);	
		
		//toolbar		
		JToolBarHelper::custom( 'part_save', 'save.png', 'save_f2.png', JText::_('JSAVE'), false, false );
		JToolBarHelper::custom( 'cancel', 'cancel.png', 'cancel_f2.png', JText::_('JTOOLBAR_CANCEL'), false, false );
		
		$sub_task = JRequest::getVar('sub_task', '');
		$this->assignRef('sub_task', $sub_task);
		
		if($helper->joomla_version >= '3.0'){
			//sidebar
			$this->add_sidebar($helper);	
		}	
		
		//set header
		$id = intval(JRequest::getVar('id', '', 'get'));
		$subtitle = '';
		if($id){ 			
			$subtitle = JText::_('COM_ACCESSMANAGER_PART_EDIT');
		}else{
			$subtitle =  JText::_('COM_ACCESSMANAGER_PART_NEW'); 
		}		
		JToolBarHelper::title('Access Manager :: '.$subtitle, 'am_icon');	
		
		parent::display($tpl);
	}
	
	function add_sidebar($helper){
	
		JHtmlSidebar::setAction('index.php?option=com_accessmanager&view=part');	
				
		$helper->add_submenu();			
		
		$this->sidebar = JHtmlSidebar::render();
	}
}
?>