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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class registrationproControllerSearch extends registrationproController {

	function __construct()
	{
		parent::__construct();		
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'apply', 'save' );			
	}
	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );		
		$this->setRedirect( 'index.php?option=com_registrationpro' );
	}
	
	function search()
	{
		//echo "<pre>"; print_r($_POST); exit;
		//$model 	= $this->getModel('events');
		$user	=JFactory::getUser();
						
		JRequest::setVar( 'view', 'search' );				
		parent::display();
	}	
	
	function print_report()
	{								
		JRequest::setVar( 'view', 'search' );		
		JRequest::setVar( 'layout', 'print_report');		
		parent::display();
	}
	
	function csv_report()
	{
		// Get data from the model
		$model	= $this->getModel('search');
		$model->setState('limit', 0);
		$model->setState('limitstart', 0);
		$rows    = $model->getData();
		
		//echo "<pre>"; print_r($rows); exit;
		###### Create .xls file  ########
		if($rows){
			
			$filename = "Report.xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/csv.ms-excel"); 
			
			$columns = array();
			$columns[] = JText::_('ADMIN_SEARCH_CSV_EVENT_NAME');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_FIRSTNAME');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_LASTNAME');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_EMAIL');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_LOCATION');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_CATEGORY');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_EVENTDATE');
			$columns[] = JText::_('ADMIN_EVENT_LIST_TICKETS_COLUMN');
								
			foreach($columns as $colkey=>$colvalue)
			{
				echo $colvalue."\t";
			}
			
			echo "\n";
			
			foreach($rows as $datakey=>$datavalue)
			{								
				echo preg_replace("[\n\r]", " ", $datavalue->titel);						
				echo "\t";									
				echo preg_replace("[\n\r]", " ", $datavalue->firstname);						
				echo "\t";									
				echo preg_replace("[\n\r]", " ", $datavalue->lastname);						
				echo "\t";									
				echo preg_replace("[\n\r]", " ", $datavalue->email);						
				echo "\t";									
				echo preg_replace("[\n\r]", " ", $datavalue->club).", ".preg_replace("[\n\r]", " ", $datavalue->city);						
				echo "\t";									
				echo preg_replace("[\n\r]", " ", $datavalue->catname);						
				echo "\t";									
				echo preg_replace("[\n\r]", " ", $datavalue->dates);						
				echo "\t";
				foreach($datavalue->tickets as $key=>$val):
					$ticket .= $val->item_name.', ';
				endforeach;
				$ticket[strlen($ticket)-2] = '';
				echo preg_replace("[\n\r]", " ", $ticket);
				unset($ticket);
				echo "\t";
				echo "\n";
			}exit;		
		}else{
			echo  JText::_('ADMIN_SEARCH_CSV_NORECORD');
		}
		###### END #######
		exit;	
	}				
}
?>
