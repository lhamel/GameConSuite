<?php
define('TICKET_CODE', '02');

require_once('../../../../inc/db/db.php');

require_once(__DIR__.'/../../../../config/config.php');
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$sql = <<< EOD
select count(*) as count
from ucon_item
where year = $year
EOD;
$rs = $db->Execute($sql);
foreach($rs as $row) {
  $count = $row['count'];
}

if ($count == 0) {
  $twoDigitYear = substr($year, 2, 2);
  $lastyear = $year-1;
  $sql = <<< EOD
  insert into ucon_item 
  select concat($twoDigitYear,right(barcode,6)) as barcode, $year as year, itemtype, subtype, special, description, price, quantity, i_commonsheet
  from ucon_item
  where year=$lastyear and itemtype != "Ticket"
EOD;
//echo "<hr>$sql<hr>"; exit;
  $rs = $db->Execute($sql);
  echo "Copied items other than tickets from $lastyear to $year<br>";
}

//
// find all items already in the db to check before we add them
class UConItem extends ADOdb_Active_Record { var $_table = 'ucon_item'; }
$sql = <<< EOD
select *
from ucon_item
where year=$year
EOD;
$rs = $db->Execute($sql);
$items = array();
foreach($rs as $row) {
  $barcode = $row['barcode'];
  $items[$barcode] = $row;
}


//
// get all events and insert ones which do not already have barcodes
$sql = <<< EOD
select *
from ucon_event as E, ucon_member as M
where E.id_convention=$year
  and E.id_gm=M.id_member
#  and E.b_approval=1
#  and E.s_number != '' and (not isnull(E.s_number))
EOD;
$rs = $db->Execute($sql);
foreach($rs as $row) {
  $barcode = substr($year, 2, 2).TICKET_CODE.($row['id_event']%10000);
  //echo "Barcode ($barcode) - ";
  if (isset($items[$barcode])) {
    // check that s_number didn't change
    $savedEventNumber = $items[$barcode]['special'];
    //echo "compare $savedEventNumber and {$row['s_number']}<br>";
    //if ($savedEventNumber != $row['s_number']) {
      $newItem = new ADOdb_Active_Record('ucon_item');
      $newItem->load("barcode='$barcode'");
      $newItem->year = $year;
      $newItem->itemtype = 'Ticket';
      $newItem->subtype = $row['id_event'];
      $newItem->special = $row['s_number'];
      $newItem->description = $row['s_number'].' - '.$row['s_title'];
      $newItem->price = $row['i_cost'];
      $newItem->quantity = $row['i_maxplayers'];
      $newItem->save();

      $title = $row['s_title'];
      if ($title == '') {$title = $row['s_game']; }
      echo "Updated event ".$row['s_number']." (".$row['id_event'].") ".$title."<br>";
    //}
  } else {
    $newItem = new ADOdb_Active_Record('ucon_item');
    $newItem->barcode = $barcode;
    $newItem->year = $year;
    $newItem->itemtype = 'Ticket';
    $newItem->subtype = $row['id_event'];
    $newItem->special = $row['s_number'];
    $newItem->description = $row['s_number'].' - '.$row['s_title'];
    $newItem->price = $row['i_cost'];
    $newItem->quantity = $row['i_maxplayers'];
    $newItem->save();

    $title = $row['s_title'];
    if ($title == '') {$title = $row['s_game']; }
    echo "Added event ".$row['s_number']." (".$row['id_event'].") ".$title."<br>";
  }
}

?>
Done.
