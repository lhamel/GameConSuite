<?php
include_once '../../inc/inc.php';
include_once INC_PATH.'db/db.php';
$year = $config['ucon']['year'];

if (!$config['allow']['view_events']) {
  echo "Bad request.  Viewing events disabled";
  http_response_code(400);
  exit;
}

$id_event = $_REQUEST['id_event'];
if (!isset($id_event)) {
  echo "Bad request.  id_event required.";
  http_response_code(400);
  exit;
}


$sqlEvent = <<< EOD
select 
  id_event, id_convention,
  id_gm, id_room, id_event_type,
  s_number, s_game, s_title,  s_desc,
  i_minplayers, i_maxplayers,
  i_agerestriction, e_exper, e_complex,
  e_day, i_time, i_length, s_table,
  i_cost, b_approval
 
from ucon_event 
where id_event=?
  and b_approval=1
EOD;
$sqlGm = "select s_fname, s_lname from ucon_member where id_member=?";

$event = $db->getRow($sqlEvent, array($id_event));
if ($event === false) { echo "SQL error: ".$db->errorMsg(); exit; } 

$gm = $db->getRow($sqlGm, array($event['id_gm']));
if ($gm === false) { echo "SQL error: ".$db->errorMsg(); exit; }

if ($config['allow']['see_location']) {
  $room = $db->getRow("select * from ucon_room where id_room=?", array($event['id_room']));
  $event['room'] = $room;
} else {
  unset($event['id_room']);
}


$sqlTags = "select T.id_tag, tag from ucon_tag as T, ucon_event_tag as ET where ET.id_event=? and ET.id_tag=T.id_tag";
$tags = $db->getAssoc($sqlTags, array($id_event));
if ($tags === false) { echo "SQL error: ".$db->errorMsg(); exit; }
$tags = array_values($tags);

// count the number of tickets sold

$type = $db->getRow("select id_event_type, s_abbr, s_type from ucon_event_type where id_event_type=?", array($event['id_event_type']));

if ($config['allow']['live_data']) {
$sql = <<< EOD
select sum(TI.quantity) as tickets
from ucon_transaction_item as TI, ucon_item as I
where itemtype='Ticket'
  and TI.barcode=I.barcode
  and subtype=?
order by subtype
EOD;
  $tix = $db->getOne($sql, array($id_event));
  if ($tix === false) { echo "SQL error: ".$db->errorMsg(); exit; }
} else {
  $tix = $db->getOne("select sum(i_quantity) from ucon_order where s_type='Ticket' and s_subtype=?", array($id_event));
  if ($tix === false) { echo "SQL error: ".$db->errorMsg(); exit; }
}
//$tix = $db->getOne("",array($id_event));

// formatting


$event['gamemaster'] = $gm;
$event['tixSold'] = $tix;
$event['type'] = $type;
$event['tags'] = $tags;
$event['format_gamemaster'] = $gm['s_fname'].' '.$gm['s_lname'];

unset($event['id_gm']);

include DEPTH.'inc/resources/event/constants.php';
$event['format_time'] = formatSingleEventTime($event['e_day'], $event['i_time'], $event['i_time']+$event['i_length']);
$event['format_title'] = formatSingleEventTitle($event['s_game'], $event['s_title']);
$event['format_players'] = formatSingleEventPlayers($event);

echo json_encode($event, JSON_NUMERIC_CHECK);



