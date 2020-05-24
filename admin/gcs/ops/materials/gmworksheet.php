<?php
require_once '../../../../inc/inc.php';
require_once '../../../../inc/db/db.php';
include '../../../../inc/resources/event/constants.php';

include_once '../../../../vendor/tecnickcom/tcpdf/tcpdf.php';

$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

require_once '_barcode.php';

// TODO: looks like there is a bug
$displayProperties = array(
    'width' => 0,
    'height'=> 0,
);


define("HALF_INCH", 72/2);

// needed for schedule
class EventInfo {}
$events = array();

$eventClause = (@is_numeric($_GET['id_event']) ? "id_event=".$_GET['id_event'] : "E.id_convention=$year and b_approval=1" );
$orderClause = (@is_numeric($_GET['id_event']) ? "s_subtype=".$_GET['id_event'] : "id_convention=$year" );


// query for list of events
$sql = <<< EOD
  select id_event, s_game, s_title, s_number, 
    e_day, i_time, i_length, i_time+i_length as endtime,
    i_maxplayers, i_minplayers, i_cost, 
    if(M.s_fname!="", CONCAT(M.s_fname, " ", M.s_lname), M.s_lname) as name,
    id_gm,
    s_room, s_table
  from
    ucon_member as M, ucon_event as E LEFT JOIN ucon_room as R ON E.id_room = R.id_room
  where M.id_member = E.id_gm and ($eventClause)
  order by s_lname, s_fname, e_day, i_time, s_number
EOD;
//echo "<pre>$sql</pre>";
$rs = $db->Execute($sql);
foreach ($rs as $row) {
  $event = new EventInfo();
  $event->row = $row;
  $event->memberIds = array();
  $events[$row['id_event']] = $event;
}

$sql = <<< EOD
  select s_subtype as id_event, O.id_member, i_quantity, s_lname
  from ucon_order as O, ucon_member as M
  where s_type="Ticket"
    and ($orderClause)
    and O.id_member=M.id_member
  order by id_member
EOD;
$rs = $db->Execute($sql);
foreach ($rs as $row) {
  for ($i=0; $i<$row['i_quantity']; ++$i)
  {
    $events[$row['id_event']]->memberIds[] =  '#'.$row['id_member'].' '.$row['s_lname'];
  }
}

//echo "<pre>".print_r($events,1)."</pre>"; exit;

$directions = <<< EOD
Please wait 10 minutes for player to arrive.  Ticketed players are given first priority, with "Play Games All Weekend"  ribbon and generic ticket holders filling in spots first come first serve.  Please collect each player's ticket or the equivalent in generics (listed above) and place them in this envelope.  Write the total number of paying players on the envelope.  When the event is over, please return this envelope with all the tickets to operations.  You must return all tickets in order to get your deposit back.

If your event is eligible for a prize, please select an event winner and bring them to registration.  See the convention book for rules regarding eligibility.
EOD;
//'

$dpi = 72;

set_time_limit(120);
$pdf = new TCPDF('P', 'pt', 'LETTER', true, 'UTF-8', false);
$pdf->AddFont('lithograph');

$pdf->SetCreator('Registration');
$pdf->SetAuthor('U-Con');
$pdf->SetTitle('U-Con GM Worksheets');
$pdf->SetSubject('GM Worksheets');

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
//$pdf->setLanguageArray($l);
$pdf->setPrintHeader(false);
$pdf->SetMargins(HALF_INCH, HALF_INCH, HALF_INCH);

// set font
$pdf->SetFont('helvetica', '', 10);
$pdf->SetFillColor(104,104,104);


//echo count($events); exit;

$colW = array(80, 210, 170, 80);
$pdf->SetTextColor(0,0,0);
$pdf->setCellPaddings(4, 2, 4, 0); // left top right bottom


foreach ($events as $event)
{
  $id = $event->row['id_event'];
  $number = $event->row['s_number'];
  $title = $event->row['s_title'];
  $system = $event->row['s_game'];
  $table = $event->row['s_table'];
  $room = $event->row['s_room'].($table? " $table" : "");
  $gm = $event->row['name'].' (#'.$event->row['id_gm'].')';

  if ($title =='') {
    $title = $system;
  } else if ($system !='' && $title != $system) {
    $title = $system.': '.$title;
  }


  $gmId = $event->row['id_gm'];

  $day = isset($event->row['e_day']) ? $constants['events']['days'][$event->row['e_day']] : '';
  $time = isset($event->row['i_time']) ? $constants['events']['times'][$event->row['i_time']]
        .'-'.$constants['events']['times'][$event->row['endtime']] : '';

  $minPlayers = $event->row['i_minplayers'];
  $maxPlayers = $event->row['i_maxplayers'];
  $players = ($minPlayers<$maxPlayers ? $minPlayers.'-'.$maxPlayers : $maxPlayers).' Players';

  $price = $event->row['i_cost'];
  $numgenerics = $price / $config['gcs']['generic_price'];
  if ($numgenerics == round($numgenerics)) {
    $generics = ' or '.$numgenerics.' generic tickets';
  } else {
    $generics = '';
  }

  $members = implode(', ', $event->memberIds);
  $memberCount = count($event->memberIds);


  $barcode = getTicketCode($year, $event->row);
  // $barcodeX = $displayProperties['width']-$barcodeWidth;
  // $barcodeY = $displayProperties['height']-$barcodeHeight-20;
  $barcodeWidth = 120;
  $barcodeHeight = 22;
  $barcodeXRes = 1; // minimum size of barcode line


  $pdf->AddPage();

  // print the title & barcode
  $pdf->SetFont('helvetica','B', 14);
  $pdf->Cell($colW[0]+$colW[1]+$colW[2]+$colW[3]-$barcodeWidth, 0, 'Gamemaster Event Worksheet', $border=0, $ln=0);
  $pdf->Write1DBarcode($barcode, 'C128B',
      '','', //$barcodeX,  $barcodeY,
      $barcodeWidth, $barcodeHeight, $barcodeXRes);
  $pdf->Ln();

  // number under barcode
  $pdf->Cell($colW[0]+$colW[1]+$colW[2]+$colW[3]-$barcodeWidth, 0, '', $border=0, $ln=0);
  $codeWidth = $pdf->GetStringWidth($barcode);
  $pdf->SetFont('helvetica','', 10);
  $pdf->Cell(0,0, $barcode, 0, 1, 'C'); // fill
  $pdf->Ln();


  // print the table of event info
  $pdf->SetFont('helvetica','', 10);

  $pdf->Cell($colW[0], 0, 'Event Code', 0, 0, 'R');
  $pdf->Cell($colW[1], 0, $number, 0, 0);
  $pdf->Cell($colW[2], 0, "# Players with specific tickets", 0, 0, 'R');
  $pdf->Cell($colW[3], 0, "_____________", 0, 1);


  $pdf->Cell($colW[0], 0, 'Event Name', 0, 0, 'R');
  $pdf->Cell($colW[1], 0, $title, 0, 1);
 

  $pdf->Cell($colW[0], 0, 'Gamemaster', 0, 0, 'R');
  $pdf->SetFont('helvetica','B', 10);
  $pdf->Cell($colW[1], 0, $gm, 0, 0);
  $pdf->SetFont('helvetica','', 10);
  $pdf->Cell($colW[2], 0, "# Players with generic tickets", 0, 0, 'R');
  $pdf->Cell($colW[3], 0, "_____________", 0, 1);

  $pdf->Cell($colW[0], 0, 'Ticket Price', 0, 0, 'R');
  $pdf->Cell($colW[1], 0, '$'.$price.$generics, 0, 1);

  $pdf->Cell($colW[0], 0, 'Schedule', 0, 0, 'R');
  $pdf->Cell($colW[1], 0, $day.' '.$time, 0, 0);
  $pdf->Cell($colW[2], 0, '# Players with "Play Games" ribbons', 0, 0, 'R');
  $pdf->Cell($colW[3], 0, "_____________", 0, 1);

  $pdf->Cell($colW[0], 0, 'Table(s)', 0, 0, 'R');
  $pdf->Cell($colW[1], 0, $room, 0, 1);

  $pdf->Cell($colW[0], 0, 'Players', 0, 0, 'R');
  $pdf->Cell($colW[1], 0, $players, 0, 0);
  $pdf->Cell($colW[2], 0, "Total Number of Players", 0, 0, 'R');
  $pdf->Cell($colW[3], 0, "_____________", 0, 1);


  // add whitespace before printing the preregistered players
  $pdf->Ln();

  $pdf->SetTextColor(255,255,255);
  $pdf->SetFont('helvetica','B', 10);
  $pdf->Cell(8.5*$dpi-2*HALF_INCH, 10, 'Preregistered Players', $border=1, $ln=1, $align='', $fill=1);
  $pdf->SetFont('helvetica','', 10);
  $pdf->SetTextColor(0,0,0);
  $pdf->MultiCell(8.5*$dpi-2*HALF_INCH, 80, "($memberCount) ".$members, $border=1, $align='L', $fill=0, $ln=1) ;
  $pdf->Cell(0, 0, "\n", 0, 1);
  $pdf->Cell(0, 0, "Please affix tickets below to match totals.  Use back if necessary.\n", 0, 1);



  if (isset($_GET['debugSingle'])) {
    break;
  }

}


$pdf->Output('gmworksheets.pdf', 'I');
