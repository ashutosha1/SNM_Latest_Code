<?php
/**
 * @version		v1.5.13 registrationpro $
 * @package		registrationpro
 * @copyright	Copyright © 2009 - All rights reserved.
 * @license  	GNU/GPL
 * @author		JoomlaShowroom.com
 * @author mail	info@JoomlaShowroom.com
 * @website		www.JoomlaShowroom.com
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class registrationproControllerStat_Reports extends registrationproController {

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

	function report()
	{
		$user	=& JFactory::getUser();
		JRequest::setVar( 'view', 'stat_reports' );
		parent::display();
	}

	function print_report()
	{
		//echo "<pre>" ;PRINT_R($_REQUEST);die();
		if(isset($_REQUEST['print_rpt']) && $_REQUEST['print_rpt'] == 'Generate Report')
		{
			JRequest::setVar( 'view', 'stat_reports' );
			JRequest::setVar( 'layout', 'print_report');
			parent::display();
		}else if(isset($_REQUEST['print_exl']) && $_REQUEST['print_exl'] == 'Generate Excel Sheet Report'){
			$this->excel_report();
		}
	}
	
	function excel_report()
	{
		
		$model = $this->getModel('stat_reports');
		$data	= $model->getUserinfoForExcelReport();			// get users details
		//echo "<pre>";print_r(date('Y-m-d',$data[0]->uregdate));die;
		//echo "<pre>";print_r($data);die;
		if(count($data) > 0){
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
				
				if(!empty($data[$key]->titel)){

					if(count($columns) > 0){
						if(!in_array('titel',$columns)){
							$columns[] = 'titel';
						}
					}else{
						$columns[] = "titel";
					}
					$data1[$key]['titel'] = $data[$key]->titel;
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
							//print_r($colname);
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
				//echo '<pre>';print_r($arrFields);echo '</pre>';die;
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
						//$arrFinancialFields[] = 'Session fees';
						$arrFinancialFields[] = 'Additional field fees';
						$arrFinancialFields[] = 'Discount Amount';
						//$arrFinancialFields[] = 'Admin Discount';
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
								//$data1[$key]['Regdate'] = strftime("%c",$data[$key]->uregdate + ($mosConfig_offset*60*60));
								$registrationproHelper = new registrationproHelper;
								//$data1[$key]['Regdate'] = $registrationproHelper->getFormatdate($regproConfig['formatdate']." ".$regproConfig['formattime'],  $data[$key]->uregdate + ($regproConfig['timezone_offset']*60*60));
								$data1[$key]['Regdate'] = date('Y-m-d',$data[0]->uregdate);
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
							
							/*if(!empty($data[$key]->session_fees)){
								$data1[$key]['Session fees'] = $data[$key]->session_fees;
							}*/

							if(!empty($data[$key]->additional_field_fees)){
								$data1[$key]['Additional field fees'] = $data[$key]->additional_field_fees;
							}

							
							$adminDiscount = (!empty($data[$key]->AdminDiscount))?$data[$key]->AdminDiscount:0;
							if($data[$key]->discount_amount > 0.00){
								$data1[$key]['Discount Amount'] = number_format($data[$key]->discount_amount,2);
								$data1[$key]['Final Price'] 	= number_format($data[$key]->price + $data[$key]->additional_field_fees + $data[$key]->session_fees - $adminDiscount - $data[$key]->discount_amount,2);
							}else{
								$data1[$key]['Discount Amount'] = 0.00;
								$data1[$key]['Final Price'] 	= number_format($data[$key]->price + $data[$key]->additional_field_fees + $data[$key]->session_fees - $adminDiscount,2);
							}
							
							/*if(!empty($data[$key]->AdminDiscount)){
								$data1[$key]['Admin Discount'] = number_format($data[$key]->AdminDiscount,2);
								
							}*/
							// end
						}
					}
				}
			}
		
		//echo "<pre>";print_r($columns); echo "<pre>";print_r($columns1);die;
		//echo "<pre>";print_r($data1); exit;

		$columns = array_merge($columns,$columns1);

		#### Creating final array to export data into .xls format #####
		$data2 = array();

		foreach($data1 as $datakey => $datavalue)
		{
			//echo "<pre>"; print_r($data1); exit;
			foreach($columns as $colkey=>$colvalue)
			{
				if(array_key_exists($colvalue,$datavalue)){
					$data2[$datakey][$colvalue] = $data1[$datakey][$colvalue];
				}else{
					$data2[$datakey][$colvalue] = "\t";
				}
			}
		}
		
		//echo "<pre>";print_r($columns);
		//echo "<pre>";print_r($data2); 
 //exit;
		###### END #######
//die;
		//echo "<pre>"; print_r($data2); exit;
		$sorted_data = array();
		$final_array = array();
		
		
		
		/* Sort array elements */
		$title_array = array();
		foreach($data2 as $datakey => $datavalue)
		{
			$title = $datavalue['titel'];
			if(in_array($title,$title_array))
			{
				continue;
			}
			$title_array[] = $title;
			foreach($data2 as $key=>$val)
			{
				if($title == $val['titel'])
				{
					$sorted_data[] = $val;
				}
			}
			$final_array[] = $sorted_data;
			unset($sorted_data);
		}
		$data2 = $final_array;
		//echo "<pre>"; print_r($final_array);exit;
		###### Create .xls file  ########
		}
		if($data2){
			//echo "<pre>"; print_r($data2); exit;
			$flag = false;
			//$filename = $event_info->titel ."_Report.xls";
			$filename = "Stat_Report.xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			foreach($columns as $colkey=>$colvalue)
			{
				echo $colvalue."\t";
			}

			echo "\n";
			$title = '';
			$end_index = count($data2);
			//echo "<pre>"; print_r($data2); exit;
			$loop = 0;
			foreach($data2 as $datakey=>$datavalue)
			{
				
				foreach($datavalue as $kk=>$vv)
				{
					//echo '<pre>';print_r($vv);
					foreach($vv as $a=>$b)
					{
						$str = preg_replace("/\t/", " ", $b);

						// escape new lines
						$str = preg_replace("/\r?\n/", " ", $str);
						// convert 't' and 'f' to boolean values
						if($str == 't') $str = 'TRUE'; if($str == 'f') $str = 'FALSE';
						// force certain number/date formats to be imported as strings
						if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) { $str = "$str"; }
						// escape fields that include double quotes
						if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';


						//echo $str, "\t";
						$str = str_replace(array('€','„','“'),array('EUR','"','"'),$str);
						$str = utf8_decode($str);
						echo $str, "\t";
					}
					echo "\n";
				}
				$loop++;
				if($loop != count($data2))
				{
					echo "\n";
					foreach($columns as $colk=>$colv)
					{
						echo $colv."\t";
					}
				}
				echo "\n";
			}
		}else{
			header("Content-Disposition: attachment; filename=\"stat_report.xls\"");
			header("Content-Type: application/vnd.ms-excel");
		}
		###### END #######
		exit;
		
	}
}
?>
