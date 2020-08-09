<?php
include_once ('../../../inc/inc.php');

if (!is_numeric($_GET['id_event'])) {
  die("must specifify id_event");
}


include_once (INC_PATH.'db/db.php');
$events = $db->getAll($queries['GET_EVENT'], array($_GET['id_event']));
if (!is_array($events)) die ("SQL Error: ".$db->ErrorMsg());
$event = $events[0];
// echo "<pre>EVENT".print_r($event,1)."</pre>"; exit;

$sql = <<< EOD
    insert into ucon_event
    (d_created, id_convention, id_gm,
    id_event_type, s_game, s_title, s_desc,
    s_comments, i_maxplayers, i_minplayers, e_exper, e_complex, i_agerestriction,
    i_length, i_c1, i_c2, i_c3, s_setup, s_table_type, s_eventcom, s_desc_web, s_platform)
    values (?, ?, ?,
    ?, ?, ?, ?,
    ?, ?, ?, ?, ?, ?,
    ?, ?, ?, ?, ?, ?, ?, ?, ?)
EOD;


$prepareArgs = array(
  null, // d_created
  $config['gcs']['year'], // id_convention
  (int) $event['id_gm'], // id_gm

  (int) $event['id_event_type'],
  $event['s_game'],
  $event['s_title'],
  $event['s_desc'],

  $event['s_comments'],
  (int) $event['i_maxplayers'],
  (int) $event['i_minplayers'],
  $event['e_exper'],
  $event['e_complex'],
  (int) $event['i_agerestriction'],

  (int) $event['i_length'],
  (int) $event['i_c1'],
  (int) $event['i_c2'],
  (int) $event['i_c3'],
  $event['s_setup'],
  $event['s_table_type'],
  $event['s_eventcom'],
  $event['s_desc_web'],
  $event['s_platform'],
);
$ok = $db->Execute($sql, $prepareArgs);
if (!$ok) die('sql error: '.$db->ErrorMsg());

$newId = $db->Insert_ID();
redirect('index.php?id_event='.$newId);
