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

require_once(REGPRO_ADMIN_BASE_PATH.'/classes/PDF/Utils.php');
require_once(REGPRO_ADMIN_BASE_PATH.'/classes/PDF/Label.php');
require_once(REGPRO_ADMIN_BASE_PATH.'/classes/PDF/Badge.php');		

class registrationproControllerBadge extends registrationproController {

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
	
	function badge()
	{
		//echo "<pre>"; print_r($_POST); exit;
		//$model 	= $this->getModel('events');
		$user	=JFactory::getUser();
						
		JRequest::setVar( 'view', 'badge' );				
		parent::display();
	}	
	
	function change_event()
	{		
		//echo "test"; exit;
		//echo "<pre>"; print_r($_POST); exit;
		$model	= $this->getModel('badge');	
		$rows    = $model->getData();
		
		//echo "<pre>"; print_r($rows); exit;
						
		JRequest::setVar( 'view', 'badge' );	
		JRequest::setVar( 'layout', 'users');				
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
			header("Content-Type: application/vnd.ms-excel"); 
			
			$columns = array();
			$columns[] = JText::_('ADMIN_SEARCH_CSV_EVENT_NAME');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_FIRSTNAME');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_LASTNAME');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_EMAIL');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_LOCATION');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_CATEGORY');
			$columns[] = JText::_('ADMIN_SEARCH_CSV_EVENTDATE');
								
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
				
				echo "\n";
			}		
		}else{
			echo  JText::_('ADMIN_SEARCH_CSV_NORECORD');
		}
		###### END #######
		exit;		
	}			
	
	/*function generate_badge()
	{
	
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/config/lang/eng.php');
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/tcpdf.php');
		
		// create new PDF document
		//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		$format = array(86,55);
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $format, true, 'UTF-8', false);
		
		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
	
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		
		$pdf->SetMargins(3, 3, 3);
		
		//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(FALSE, 0);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set default font subsetting mode
		$pdf->setFontSubsetting(true);
		
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();										
		
		//echo "<pre>"; print_r($_POST); exit;
		
		$table_style = 'style="border-spacing:1px; border-width:1px 1px 1px 1px; border-style:none; border-color:#cccccc; border-collapse:separate; background-color:#cccccc; color:#000000;"';
		$th_style = 'style="padding:1px; background-color:#cccccc; font-family:Arial,Georgia,Serif; color:#000000;"';
		$td_style = 'style="padding:2px; background-color:#ffffff; font-family:Arial,Georgia,Serif; color:#000000;"';
						 
		$text = '<table cellpadding="0" style="width:100%;"> 
					<tr>
						<td style="width:100%;">
							<table cellpadding="2" style="width:100%;">
								<tr> <td style="font-family:Courier-Bold; font-size:18px; color:#cccccc;">Sushil Kumar</td> </tr>
								<tr> <td style="font-family:Helvetica-Bold;">Sr.Software</td> </tr>
								<tr> <td style="font-family:Georgia;">Chandigarh</td> </tr>
							</table>
						</td>						
					</tr>										
				</table>';
		
		
		$html = $text;
		
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');	
		
		//$pdf->AddPage();		
		//$pdf->writeHTML($text1, true, false, true, false, '');
		
		
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('example_001.pdf', 'I');
														
		//registrationproHelper::Badgepdf($text);
		exit;
	}*/		
	
	function generate_badge()
	{
		$database	=JFactory::getDBO();
		/*require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/config/lang/eng.php');
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/tcpdf.php');*/
		
		/*require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/PDF/Utils.php');
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/PDF/Badge.php');		
		require_once(REGPRO_ADMIN_BASE_PATH.'/classes/tcpdf/PDF/Label.php');*/	
		
		//echo "<pre>"; print_r($_POST); exit;	
		
		$where = array();
		
		if($_POST['event'] > 0) {
			$eventid = $_POST['event'];
		}
		
		if($_POST['users'] == 'S') {
			$userids = 	implode(',',$_POST['cid']);				
			$where[] = 'r.rid IN ('.$userids.')';
		}
				
		$where[] = 'e.id ='.$eventid;
		$where[] = 'e.locid = l.id';
		$where[] = 'e.id = r.rdid';
				
		$where 	 = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		$ordering = "";
		$ordering = " ORDER BY r.".$_POST['sortby'];
				
		$query = "SELECT e.titel as event, e.dates as event_start, l.club as location, r.firstname, r.lastname, r.email, CONCAT_WS(' ',r.firstname,r.lastname) as fullname, r.params FROM #__registrationpro_dates as e, #__registrationpro_locate as l, #__registrationpro_register as r ".$where.$ordering;
		$database->setQuery($query);
		$rows = $database->loadAssocList();	
		//echo "<pre>"; print_r($rows); exit;
		
		foreach($rows as $k => $v)
		{
			$custom_fields = unserialize($v['params']);
			//echo "<pre>"; print_r($custom_fields); exit;
			
			foreach($custom_fields as $ck => $cv)
			{
				//echo $ck,"<br/>";				
				if(strtolower(str_replace(" ","",$ck)) != "firstname" && strtolower(str_replace(" ","",$ck)) != "lastname" && strtolower(str_replace(" ","",$ck)) != "email") {			
			 		
					//echo "<pre>"; print_r($custom_fields); exit;
					//echo $ck;				
					$rows[$k][$ck] = $cv[0][0];
				}
			}
		}
		//exit;
		//echo "<pre>"; print_r($rows); exit;
		
    	$eventBadgeClass = new CRM_Event_Badge_Logo5395();

		//echo "<pre>"; print_r($rows); exit;
		
		//$rowss[] = array('event_title'=>'Event Name', 'event_start_date'=>'2-12-2012', 'first_name'=>'First Name', 'last_name'=>'Last Name', 'current_employer'=>'Current Enpoyer');
	
		//echo "<pre>"; print_r($rowss); exit;
	
		$eventBadgeClass->run($rows);
		
	}
			
}


class CRM_Event_Badge_Logo5395 extends CRM_Event_Badge {
  function __construct() {
    parent::__construct();
    // A4
  /*  $pw           = 210;
    $ph           = 297;
    $h            = 59.2;
    $w            = 85.7;
    $this->format = array(
      'name' => 'Avery 5395', 'paper-size' => 'A4', 'metric' => 'mm', 'lMargin' => 13.5,
      'tMargin' => 3, 'NX' => 2, 'NY' => 4, 'SpaceX' => 15, 'SpaceY' => 8.5,
      'width' => $w, 'height' => $h, 'font-size' => 12,
    );*/
	
	$pw           = 210;
    $ph           = 297;
    $h            = 53.2;
    $w            = 85.7;
	
	$this->format = array(
      'name' => 'Avery 5395', 'paper-size' => 'A4', 'metric' => 'mm', 'lMargin' => 18,
      'tMargin' => 35, 'NX' => 2, 'NY' => 4, 'SpaceX' => 3, 'SpaceY' => 3,
      'width' => $w, 'height' => $h, 'font-size' => 12,
    );
	
    $this->lMarginLogo = 20;
    $this->tMarginName = 20;
    //      $this->setDebug ();
  }

  public function generateLabel($participant) 
  {
	$count = array();
	// check how many lines needs to draw
	if(is_array($_POST['fields'])) {
		foreach($_POST['fields'] as $k => $value) {			
			if($value == '0') {
				unset($_POST['fields'][$k]);
			}else{
				$count[] = $k;
			}
		}
	}
				  
    $x = $this->pdf->GetAbsX();
    $y = $this->pdf->GetY();

	$this->pdf->SetFont($_POST['fonts'][0],'', $_POST['fontsizes'][0]);
	$this->pdf->MultiCell($this->pdf->width, 0, $participant[$_POST['fields'][0]], $this->border, $_POST['align'][0], 0, 1, $x, $y, true, 0, false, true, 13.3, 'M', true);

	$this->pdf->SetFont($_POST['fonts'][1],'', $_POST['fontsizes'][1]);
	$this->pdf->MultiCell($this->pdf->width, 0, $participant[$_POST['fields'][1]], $this->border, $_POST['align'][1], 0, 1, $x, $this->pdf->getY(), true, 0, false, true, 13.3, 'M', true);

    $this->pdf->SetFont($_POST['fonts'][2],'', $_POST['fontsizes'][2]);
	$this->pdf->MultiCell($this->pdf->width, 10, $participant[$_POST['fields'][2]], $this->border, $_POST['align'][2], 0, 1, $x, $this->pdf->getY(), true, 0, false, true, 13.3, 'M', true);
	
    $this->pdf->SetFont($_POST['fonts'][3],'', $_POST['fontsizes'][3]);
    $this->pdf->MultiCell($this->pdf->width, 0, $participant[$_POST['fields'][3]], $this->border, $_POST['align'][3], 0, 1, $x, $this->pdf->getY(), true, 0, false, true, 13.3, 'M', true);
	
	$this->pdf->SetFont($_POST['fonts'][4],'', $_POST['fontsizes'][4]);
    $this->pdf->MultiCell($this->pdf->width, 0, $participant[$_POST['fields'][4]], $this->border, $_POST['align'][4], 0, 1, $x, $this->pdf->getY(), true, 0, false, true, 13.3, 'M', true);
  }
}

?>
