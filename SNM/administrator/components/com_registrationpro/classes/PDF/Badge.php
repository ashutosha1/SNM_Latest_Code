<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
*/

class CRM_Event_Badge {
  function __construct() {
    $this->style        = array('width' => 0.1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,2', 'color' => array(0, 0, 200));
    $this->format       = '5160';
    $this->imgExtension = 'png';
    $this->imgRes       = 300;
    $this->event        = NULL;
    $this->setDebug(FALSE);
  }

  function setDebug($debug = TRUE) {
    if (!$debug) {
      $this->debug = FALSE;
      $this->border = 0;
    }
    else {
      $this->debug = TRUE;
      $this->border = "LTRB";
    }
  }

  public function run(&$participants) {
    // fetch the 1st participant, and take her event to retrieve its attributes
    $participant = reset($participants);
    $eventID     = $participant['event_id'];
    $this->event = self::retrieveEvent($eventID);
    //call function to create labels
    self::createLabels($participants);
    CRM_Utils_System::civiExit(1);
  }

  protected function retrieveEvent($eventID) {
   /* $bao = new CRM_Event_BAO_Event();
    if ($bao->get('id', $eventID)) {
      return $bao;
    }
    return NULL; */
	return 1;
  }

  function getImageFileName($eventID, $img = FALSE) {
    global $civicrm_root;
    $path = "CRM/Event/Badge";
    if ($img == FALSE) {
      return FALSE;
    }
    if ($img == TRUE) {
      $img = get_class($this) . "." . $this->imgExtension;
    }

   // $config = CRM_Core_Config::singleton();
    $imgFile = $config->customTemplateDir . "/$path/$eventID/$img";
    if (file_exists($imgFile)) {
      return $imgFile;
    }
    $imgFile = $config->customTemplateDir . "/$path/$img";
    if (file_exists($imgFile)) {
      return $imgFile;
    }

    $imgFile = "$civicrm_root/templates/$path/$eventID/$img";
    if (file_exists($imgFile)) {
      return $imgFile;
    }
    $imgFile = "$civicrm_root/templates/$path/$img";
    if (!file_exists($imgFile) && !$this->debug) {
      return FALSE;
    }

    // not sure it exists, but at least will display a meaniful fatal error in debug mode
    return $imgFile;
  }

  function printBackground($img = FALSE) {
    $x = $this->pdf->GetAbsX();
    $y = $this->pdf->GetY();
    if ($this->debug) {
      $this->pdf->Rect($x, $y, $this->pdf->width, $this->pdf->height, 'D', array('all' => array('width' => 1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,10', 'color' => array(255, 0, 0))));
    }
    $img = $this->getImageFileName($this->event->id, $img);
    if ($img) {
      $imgsize = getimagesize($img);
      $f = $this->imgRes / 25.4;
      $w = $imgsize[0] / $f;
      $h = $imgsize[1] / $f;
      $this->pdf->Image($img, $this->pdf->GetAbsX(), $this->pdf->GetY(), $w, $h, strtoupper($this->imgExtension), '', '', FALSE, 72, '', FALSE, FALSE, $this->debug, FALSE, FALSE, FALSE);
    }
    $this->pdf->SetXY($x, $y);
  }

  public function generateLabel($participant) {
    $txt = "{$this->event['title']}
{$participant['first_name']} {$participant['last_name']}
{$participant['current_employer']}";

    $this->pdf->MultiCell($this->pdf->width, $this->pdf->lineHeight, $txt);
  }

  function pdfExtraFormat() {}

  function createLabels(&$participants) {

    $this->pdf = new CRM_Utils_PDF_Label($this->format, 'mm');
    $this->pdfExtraFormat();
    $this->pdf->Open();
    $this->pdf->setPrintHeader(FALSE);
    $this->pdf->setPrintFooter(FALSE);
    $this->pdf->AddPage();
    $this->pdf->SetGenerator($this, "generateLabel");

    foreach ($participants as $participant) {
      $this->pdf->AddPdfLabel($participant);    
	}
	
    $this->pdf->Output($participants[0]['event'] . '.pdf', 'D');
	exit;
  }
}

