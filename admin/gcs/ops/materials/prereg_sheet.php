<?php
require_once('../../../../inc/db/db.php');
require_once '../../../../inc/resources/event/constants.php';

include_once '../../../../vendor/tecnickcom/tcpdf/tcpdf.php';

$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$dpi = 72;
$pageWidth=8.5*$dpi;
$pageHeight=11*$dpi;

function itemSort($a, $b) {
  if ($a['s_type']!=$b['s_type']) {
    return $a['s_type'] > $b['s_type'];
  }
  //if ($a['s_type']=='Ticket') {
  //}

  return $a['s_subtype'] > $b['s_subtype'];
}

class Member {}

if (isset($_GET['id_member']) && is_numeric($_GET['id_member'])) {
  $idString = $_GET['id_member'];
} else {
  $sql = <<< EOD
select id_gm as id_member from ucon_event where id_convention=?
union
select id_member from ucon_order where id_convention=?
EOD;
  $rs = $db->Execute($sql, array($year, $year));
  $ids = array();
  foreach ($rs as $row) {
    $id = $row['id_member'];
    $ids[$id] = $id; 
  }
  sort($ids);
  //echo "<pre>".print_r($ids,1)."</pre>";
  //exit;
  $idString = implode(',', $ids);
}

$sql = <<< EOD
  select * 
  from ucon_member as M
  where (id_member in ($idString))
  order by s_lname, s_fname
EOD;
//echo "<pre>$sql</pre>";
$rs = $db->Execute($sql);
$members = array();
foreach ($rs as $row) {
  $m = new Member();
  $m->row = $row;
  $m->events = array();
  $m->items = array();
  $m->total = 0;
  $members[$row['id_member']] = $m;
}

$sql = <<< EOD
  select O.id_member, O.id_order, O.s_type, O.s_special, O.i_price, O.i_quantity,
    O.s_subtype
    ,E.s_number,trim(E.s_title) as s_title,trim(E.s_game)as s_game,E.i_time,E.e_day
  from ucon_order as O left join ucon_event as E on (O.s_subtype=E.id_event and O.s_type='Ticket')
  where O.id_convention=?
    and (id_member in ($idString))
  order by id_member, (i_price<0), s_type, E.e_day, E.i_time
EOD;
//echo "<pre>$sql</pre>"; exit;
$rs = $db->Execute($sql, array($year));
if ($rs === null) { echo "SQL Error: ".$db->ErrorMsg(); }
foreach ($rs as $row) {

  // Reformat the ticket title
  if ($row['s_type']=='Ticket') {
    $title = '#'.$row['s_number'].' ';
    if ($row['s_game'])                    { $title .= $row['s_game']; }
    if ($row['s_title'] && $row['s_game']) { $title .= ': '; }
    if ($row['s_title'])                   { $title .= $row['s_title']; }


    $time = isset($row['i_time']) ? $constants['events']['times'][$row['i_time']] : '';
    $day = isset($row['e_day']) ? $constants['events']['daysAbbreviated'][ $row['e_day'] ] : '';
    $title .= " ($day $time)";

    $row['s_subtype'] = $title;
  }

  $members[$row['id_member']]->items[] = $row;
  $members[$row['id_member']]->total += $row['i_price']*$row['i_quantity'];
}

$sql = <<< EOD
  select * 
  from ucon_event as E
  where id_convention=?
    and b_approval=1
    and (id_gm in ($idString))
  order by id_gm, e_day, i_time
EOD;
$rs = $db->Execute($sql, array($year));
foreach ($rs as $row) {
  $members[$row['id_gm']]->events[] = $row;
}

// $sql = <<< EOD
//   select id_member, sum(i_price*i_quantity) as refund
//   from ucon_order
//   where id_convention=$year
//     and s_type='Badge'
//     and s_subtype='Gamemaster'
//     and (id_member in ($idString))
// EOD;
// //echo "<pre>$sql</pre>"; exit;
// $rs = $db->Execute($sql);
// if (!isset($rs)) { echo "<pre>SQL Error: ".$db->ErrorMsg()."\n$sql</pre>"; exit; }
// //echo "<pre>".print_r($rs,1)."</pre>";
// foreach ($rs as $row) {
//   if ($row['id_member'] != '') {
//     $members[$row['id_member']]->gmrefund = $row['refund'];
//   }
// }

//echo "<pre>".print_r($members, 1)."</pre>"; exit;

$pdf = new TCPDF('P', 'pt', 'LETTER', true, 'UTF-8', false);

$pdf->AddFont('lithograph');

// set document information

$pdf->SetCreator('Registration');
$pdf->SetAuthor('U-Con');
$pdf->SetTitle('U-Con Signature Cards');
$pdf->SetSubject('Signature Cards');
//$pdf->SetKeywords('');

//set margins
$pdf->SetMargins(.5*$dpi, .5*$dpi, .5*$dpi);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 7);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(true, $dpi/2);


$cellWidth = 7*$dpi;

$signatureLine = 'X___________________________________________________________';
$signatureWidth = $cellWidth-$dpi;
$border = 1;
$noborder = 0;
$mainFontSize=11;

//echo "<pre>count: ".count($members)."<pre>";
  
foreach ($members as $member) {

  $pdf->AddPage('', array($pageWidth, $pageHeight));
  
  $memberName = $member->row['s_fname'];
  if (strlen(trim($member->row['s_lname']))>0) {
    $memberName = $member->row['s_lname'].', '.$memberName;
  }
  $memberName .= " (#".$member->row['id_member'].")";
  $balance = $member->total;

  $pdf->SetFont('helvetica','B', 13);
  $pdf->SetFillColor(255,255,255);
  $pdf->Cell($cellWidth-$dpi*1.5, 14, $memberName, $noborder, 0, 'L', 1, '', 0);

  $pdf->SetFont('helvetica','B', 13);
  if ($balance > 0) {
    $pdf->SetFillColor(255, 127, 127);
  } else if ($balance == 0) {
    $pdf->SetFillColor(255, 255, 255);
  } else {
    $pdf->SetFillColor(255, 255, 191);
  }
  $pdf->Cell($dpi*1.5, 14, 'Balance: $'.number_format($balance,2), $noborder, 1, 'R', 1, '', 0);

  $pdf->SetFont('helvetica','', 10);

  if (count($member->events)>0) {
    $pdf->Cell($cellWidth, 16, '', null, 1);

    $pdf->SetFont('helvetica','B', 12);
    $pdf->SetFillColor(221,221,221);
    $pdf->Cell($cellWidth, 14, 'GM Events', $border, 1, 'C', 1, '', 0);
    $pdf->SetFont('helvetica','', 11);

    $pdf->SetFillColor(255, 255, 255);
    foreach ($member->events as $event) {
      $name = $event['s_game'];
      if ($event['s_title']!=$event['s_game']) {
        if ($event['s_game'] && $event['s_title']) $name .= ': ';
        $name .= $event['s_title'];
      }

      $startTime = $event['i_time'].'00';
      $endTime = ($event['i_time']+$event['i_length']).'00';
      if ($endTime == '2400') $endTime = '0000';
      if ($endTime == '2500') $endTime = '0100';
      $time = $event['e_day'].' '.$startTime.'-'.$endTime;

      $startTime = isset($event['i_time']) ? $constants['events']['times'][$event['i_time']] : '';
      $endTime = isset($event['i_time']) ? $constants['events']['times'][ ($event['i_time']+$event['i_length']) ] : '';
      $day = isset($event['e_day']) ? $constants['events']['daysAbbreviated'][ $event['e_day'] ] : '';
      $time = $day.' '.$startTime.'-'.$endTime;

      $pdf->Cell($dpi*1, 10, $event['s_number'], $border, 0, 'C', 1, '', 0);
      $pdf->Cell($dpi*4.5, 10, $name, $border, 0, 'L', 1, '', 0);
      $pdf->Cell($dpi*1.5, 10, $time, $border, 1, 'L', 1, '', 0);
      //$pdf->Cell($dpi*.5, 10, ' /    / ', $border, 1, 'C', 1, '', 0);
    }
  }

  $pdf->Cell($cellWidth, 16, '', null, 1);

  $pdf->SetFont('helvetica','B', 13);
  $pdf->SetFillColor(221,221,221);
  $pdf->Cell($cellWidth, 10, 'Preregistration', $border, 1, 'C', 1, '', 0);
  //$pdf->SetFont('helvetica','', 10);

  $pdf->SetFillColor(255, 255, 255);
  $count = 0;  $countItems = count($member->items);
  $currItems = $member->items;

  //usort($currItems, 'itemSort');
  foreach ($currItems as $item) {
    $count++;
    $type = $item['s_type'];
    $line = str_replace(' (comped)','',trim($item['s_subtype']));
    if (strlen($item['s_special'])>0) {
      $line .= ': '.$item['s_special'];
    }
    $quantity = $item['i_quantity'];
    $pdf->SetFont('helvetica','', 8);
    $itemPrice = '$'.number_format($item['i_price'],2);
    $itemTotal = '$'.number_format($item['i_price']*$quantity,2);

    $pdf->SetFont('helvetica','', $mainFontSize);
    $pdf->Cell($dpi*.20, 12, $count, $border, 0, 'C', 1, '', 0);
    $pdf->Cell($dpi*.80, 12, $type, $border, 0, 'L', 1, '', 0);

    $pdf->SetFont('helvetica','', $mainFontSize);
    if ($item['s_type']=='Shirt' || ($item['s_type']=='Misc' && $item['s_subtype']!='Generic Ticket')) {
      $pdf->SetFont('helvetica','B', $mainFontSize);
      $pdf->SetFillColor(255, 255, 0);
    }
    $pdf->Cell($cellWidth-$dpi*3, 12, $line, $border, 0, 'L', 1, '', 0);

    $pdf->SetFont('helvetica','', $mainFontSize);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(127,127,127);
    $pdf->Cell($dpi*.8, 12, $itemPrice, $border, 0, 'R', 1, '', 0);
    $pdf->SetTextColor(0,0,0);
    
    if ($quantity==2) {
      $pdf->SetFont('helvetica','B', $mainFontSize);
      $pdf->SetFillColor(255, 255, 0);
    } else if ($quantity>2) {
      $pdf->SetFont('helvetica','B', $mainFontSize);
      $pdf->SetFillColor(255, 200, 0);
    } else {
      $pdf->SetTextColor(127,127,127);
    }
    $pdf->Cell($dpi*.4, 12, 'x'.$quantity, $border, 0, 'R', 1, '', 0);

    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('helvetica','', $mainFontSize);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell($dpi*.8, 12, $itemTotal, $border, 
               ($count==$countItems ? 2 : 1), 'R', 1, '', 0);
    
//    echo "<pre>".print_r($item,1)."</pre>";
//    exit;
  }

  $pdf->SetFont('helvetica','B', $mainFontSize);
  if ($balance > 0) {
    $pdf->SetFillColor(255, 127, 127);
  } else if ($balance == 0) {
    $pdf->SetFillColor(255, 255, 255);
  } else {
    $pdf->SetFillColor(255, 255, 191);
  }
  $pdf->Cell($dpi*.8, 12, '$'.number_format($balance,2), $border, 1, 'R', 1, '', 0);

/*
  $pdf->SetFillColor(255, 255, 255);
  $pdf->SetFont('helvetica','', 8);
  $pdf->Cell($signatureWidth, 15, $signatureLine, $noborder, 1, 'L', 1, '', 0);
  $pdf->SetFont('helvetica','', 6);
  $pdf->Cell($signatureWidth, 6, ' Preregistration/GM Events Pickup', $noborder, 1, 'L', 1, '', 0);

  if ($balance < 0) {
    $refund = number_format(-$balance,2);
    $pdf->SetFont('helvetica','', 8);
    $pdf->Cell($signatureWidth, 15, $signatureLine, $noborder, 1, 'L', 1, '', 0);
    $pdf->SetFont('helvetica','', 6);
    $pdf->Cell($signatureWidth, 6, " Attendee Refund Recieved (\${$refund})", $noborder, 1, 'L', 1, '', 0);
  }

  if (isset($member->gmrefund)) {
    $refund = number_format($member->gmrefund,2);
    $pdf->SetFont('helvetica','', 8);
    $pdf->Cell($signatureWidth, 15, $signatureLine, $noborder, 1, 'L', 1, '', 0);
    $pdf->SetFont('helvetica','', 6);
    $pdf->Cell($signatureWidth, 6, " GM Refund Recieved (\${$refund})", $noborder, 1, 'L', 1, '', 0);
  }
*/
}

$pdf->Output('example_015.pdf', 'I');

