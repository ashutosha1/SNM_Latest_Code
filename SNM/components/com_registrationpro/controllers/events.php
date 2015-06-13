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

jimport('joomla.application.component.controller');

class registrationproControllerEvents extends registrationproController
{
	function __construct()
	{
		parent::__construct();								
	}
	
	function unauthorize()
	{		
		JRequest::setVar( 'layout', 'unauthorize');		
		echo JText::_('REGISTRATION_REQUIRED_MESSAGE');
	}
	
	function rssfeed()
	{
		// get component config settings
		$regpro_config	=& registrationproAdmin::config();
	
		$myfeed = new Regpro_RSS();
		
		$myfeed->SetChannel(REGPRO_RSS_CHANNEL_URL, JText::_('REGPRO_RSS_CHANNEL_TITLE'), JText::_('REGPRO_RSS_CHANNEL_DESCRIPTION'), JText::_('REGPRO_RSS_CHANNEL_LANGUAGE'), JText::_('REGPRO_RSS_CHANNEL_CREATOR'), JText::_('REGPRO_RSS_CHANNEL_COPYRIGHT'), JText::_('REGPRO_RSS_CHANNEL_SUBJECT'));	
			
		$myfeed->SetImage(REGPRO_RSS_IMAGE_URL);
		
		$model 	= $this->getModel('events');		
		$rows 	= $model->getEventForRss();
		//echo "<pre>"; print_r($rows); exit;						
		
		foreach($rows as $row)
		{
			
			$date    	= registrationproHelper::getFormatdate($regpro_config['formatdate'], $row->dates);
			$time 		= registrationproHelper::getFormatdate($regpro_config['formattime'], $row->times);
			$enddate 	= registrationproHelper::getFormatdate($regpro_config['formatdate'], $row->enddates);
			$endtime 	= registrationproHelper::getFormatdate($regpro_config['formattime'], $row->endtimes);
			
			$description = "";
			$description = 	$date." ".$time." ".JText::_('EVENTS_FRONT_DATE_SEPARATOR')." ".$enddate." ".$endtime."<br />";
			
			if(!empty($row->shortdescription)){
				$description .=  $row->shortdescription."<br />";				
				//$description .= str_replace("\r\n","",$row->shortdescription)."<br />";
			}
			
			if(!empty($row->club)){
				$description .=  $row->club."<br />";
			}
						
			if(!empty($row->street)){
				$description .=  $row->street."<br />";
			}
			
			if(!empty($row->city)){
				$description .=  $row->city." ";
			}
			
			if(!empty($row->plz)){
				$description .=  $row->plz;
			}
			
			if(!empty($row->country)){
				$description .=  " (".$row->country.").";
			}			
			
			$temp_link = trim(REGPRO_RSS_ITEM_URL."/index.php?option=com_registrationpro&amp;view=event&amp;did=".$row->id);
			$http_txt  = substr($temp_link,0,6);
			$link = $http_txt.str_replace("//","/",substr($temp_link,6));
						
			$myfeed->SetItem($link, $row->titel, $description);
		} 
		
						
		echo $myfeed->output(); exit;
	}		
	
	//*************************************************** Event Report section  ******************************************//
	function event_report(){
		//echo "<pre>"; print_r($_REQUEST); exit;		
		JRequest::setVar( 'view', 'event' );						
		JRequest::setVar( 'layout', 'event_report');	
		
		parent::display();					
	}
	
	function excel_report()
	{															
		$model = $this->getModel('event');
		
		$event_info = registrationproHelper::getEventInfo($model->_id); // get event details
		//echo"<pre>";print_r($event_info); exit;
		
		$data		= $model->getUserinfoForExcelReport();			// get users details
		//echo"<pre>";print_r($data); exit;
		
		// add user form data
		if($data){	
			$data1 = array();		
			$columns = array();
			$columns1 = array();	
			$j = 0;
			$z = 0;
		
			foreach($data as $key=>$value)
			{											
				if(!empty($data[$key]->reg_id)){	
				
					if(count($columns) > 0){
						if(!in_array('Registration id',$columns)){											
							$columns[] = 'Registration id';
						}
					}else{
						$columns[] = "Registration id";
					}								
					$data1[$key]['Registration id'] = $data[$key]->reg_id;
				}
			
			
				$data[$key]->params = unserialize($data[$key]->params);
				//echo"<pre>";print_r($data[$key]->params);
												
				$arrF = $data[$key]->params;
				$arrcount = count($arrF['firstname']);
				
				$arrFields   =array();				
				$arrFields1 = array_keys($arrF);
				foreach($arrFields1 as $k1=>$v1){
					if($v1=='firstname'|| $v1=='lastname' || $v1=='email'){
						$arrFields[] =$v1;
					}
				}
																
				$arrFields1 = array_keys($arrF);
				foreach($arrFields1 as $k1=>$v1){
					if($v1!='firstname'|| $v1!='lastname' || $v1!='email'){
						$arrFields[] =$v1;
					}
				}
																
				// add user form data			
				foreach($arrFields as $k=>$v)
				{			
					if(trim($arrF['firstname'][0][0]) == trim($data[$key]->firstname) && trim($arrF['lastname'][0][0]) == trim($data[$key]->lastname) && trim($arrF['email'][0][0]) == trim($data[$key]->email))
					{						
						$FieldTitle = str_replace("cb_","",$v);						
						// creat the columns title
							$colname = ucfirst($FieldTitle);
						// end
						
						// create the columns
						if(count($columns) > 0){
							if(!in_array($colname,$columns)){											
								$columns[] = $colname;
							}
						}else{
							$columns[] = $colname;
						}
						// end
												
						$data1temp = "";
						for($i=0;$i<count($arrF[$v]);$i++)
						{
							$arrImpode = array();
							if(is_array($arrF[$v])){
								$Fieldvalue = "";
								
								if($arrF[$v][$i][$i+1] == 'F'){ // check if user has uploaded any file
									$Fieldvalue = REGPRO_FORM_DOCUMENT_URL_PATH."/".$arrF[$v][$i][$i];
								}else{										
									if($arrF[$v][$i][$i]){													
										$Fieldvalue = $arrF[$v][$i][$i];
									}
								}
								
								// add data in orignal data array					
								if(count($arrF[$v]) > 1){
									$data1temp .= $Fieldvalue.", ";
								}else{
									$data1temp =  $Fieldvalue;
								}
								// end								
							}
							$data1[$key][$colname] = $data1temp;														
						}																																									
					}		
				}
								
				// add user financial data
				foreach($arrFields as $k=>$v)
				{			
					if(trim($arrF['firstname'][0][0]) == trim($data[$key]->firstname) && trim($arrF['lastname'][0][0]) == trim($data[$key]->lastname) && trim($arrF['email'][0][0]) == trim($data[$key]->email))
					{				
						// add a new coloums
						$arrFinancialFields = array();
						/*$arrFinancialFields[] = "Order id";
						$arrFinancialFields[] = "Registration id";*/
						$arrFinancialFields[] = "regdate";
						$arrFinancialFields[] = 'Payment Status';
						$arrFinancialFields[] = 'Payment Method';			  
						$arrFinancialFields[] = 'Event Ticket Name';
						$arrFinancialFields[] = 'Price';
						$arrFinancialFields[] = 'Tax';					
						$arrFinancialFields[] = 'Total Price(Including Tax)';
						$arrFinancialFields[] = 'Coupon Code';				
						$arrFinancialFields[] = 'Discount Amount';
						$arrFinancialFields[] = 'Final Price';
						// end	
						
						foreach($arrFinancialFields as $k=>$v)
						{
							$FieldTitle = $v;					
							// creat the columns title
								$colname1 = ucfirst($FieldTitle);
							// end
							
							// create the columns
							if(count($columns1) > 0){
								if(!in_array($colname1,$columns1)){											
									$columns1[] = $colname1;
								}
							}else{
								$columns1[] = $colname1;
							}
							// end	
							
							// add registration date data
							if(!empty($data[$key]->uregdate)){
								$data1[$key]['Regdate'] = strftime("%c",$data[$key]->uregdate + ($mosConfig_offset*60*60));
							}else{
								$data1[$key]['Regdate'] = "NIL";
							}	
																												
							// add user financial data
							
							/*if(!empty($data[$key]->id)){
								$data1[$key]['Order id'] = $data[$key]->id;
							}
							
							if(!empty($data[$key]->id)){
								$data1[$key]['Registration id'] = $data[$key]->reg_id;
							}*/
							
							if(!empty($data[$key]->payment_status)){
								$data1[$key]['Payment Status'] = $data[$key]->payment_status;
							}
							if(!empty($data[$key]->payment_method)){
								$data1[$key]['Payment Method'] =$data[$key]->payment_method;
							}												
							if(!empty($data[$key]->item_name)){
								$data1[$key]['Event Ticket Name'] =$data[$key]->item_name;
							}						
							if(!empty($data[$key]->price_without_tax)){
								//$data1[$key]['Price'] = number_format($data[$key]->price_without_tax,2);
								if(empty($data[$key]->price_without_tax) || $data[$key]->price_without_tax == 0.00){									
									// calculating the acutal amount with help of gorss amount and tax percentage
									if(!empty($data[$key]->price)){											
										$productprice = (100 * $data[$key]->price) / (100 + $data[$key]->tax);
										$data1[$key]['Price'] = number_format($productprice,2);
									}
								}else{
									$data1[$key]['Price'] = number_format($data[$key]->price_without_tax,2);
								}
							}
							
							if(!empty($data[$key]->tax)){
								$data1[$key]['Tax'] = $data[$key]->tax.'%';
							}else{
								$data1[$key]['Tax'] = '0%';
							}
																									
							if(!empty($data[$key]->price)){
								$data1[$key]['Total Price(Including Tax)'] = number_format($data[$key]->price,2);
							}
							
							if(!empty($data[$key]->coupon_code)){
								$data1[$key]['Coupon Code'] = $data[$key]->coupon_code;
							}
													
							if($data[$key]->discount_amount > 0.00){
								$data1[$key]['Discount Amount'] = number_format($data[$key]->discount_amount,2);
								$data1[$key]['Final Price'] 	= number_format($data[$key]->final_price,2);
							}else{
								$data1[$key]['Discount Amount'] = 0.00;
								$data1[$key]['Final Price'] 	= number_format($data[$key]->price,2);
							}
							// end
						}
					}
				}																
			}
		}					
		
		//echo "<pre>";print_r($columns); echo "<pre>";print_r($columns1);
		//echo "<pre>";print_r($data1); exit;
		
		$columns = array_merge($columns,$columns1);
					
		#### Creating final array to export data into .xls format #####		
		$data2 = array();
		
		foreach($data1 as $datakey => $datavalue)
		{
			foreach($columns as $colkey=>$colvalue)
			{	
				if(array_key_exists($colvalue,$datavalue)){				
					$data2[$datakey][$colvalue] = $data1[$datakey][$colvalue];			
				}else{
					$data2[$datakey][$colvalue] = "";
				}
			}	
		}		
		###### END #######
		
		///echo "<pre>"; print_r($data2); exit;
		
		###### Create .xls file  ########
		if($data2){
			$flag = false; 
			$filename = $event_info->titel ."_Report.xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel"); 
		
			foreach($columns as $colkey=>$colvalue)
			{
				echo $colvalue."\t";
			}
			
			echo "\n";
			
			foreach($data2 as $datakey=>$datavalue)
			{	
				if(is_array($data2[$datakey]))
				{
					foreach($data2[$datakey] as $k=>$v)
					{				
						$show = preg_replace("[\n\r]", " ", $data2[$datakey][$k]);
						
						echo $show, "\t";
					}
				}
					
				echo "\n";
			}		
		}
		###### END #######
		exit;
	}
}
?>