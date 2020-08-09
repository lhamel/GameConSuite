<?php
require_once INC_PATH.'db/db.php';


function validateEvent($event) {
  $errors = array();
  if (!isset($event['s_game']) || !$event['s_game']) $errors['s_game'] = 'Please indicate the game system';
  if (!isset($event['i_minplayers']) || !$event['i_minplayers']) $errors['i_minplayers'] = 'Please indicate the minimum number of players';
  if (!isset($event['i_maxplayers']) || !$event['i_maxplayers']) $errors['i_maxplayers'] = 'Please indicate the maximum number of players';
  if (!isset($event['id_event_type']) || !$event['id_event_type']) $errors['id_event_type'] = 'Please indicate the event category';
  if (!isset($event['i_length']) || !$event['i_length']) $errors['i_length'] = 'Please indicate the length of your game in hours';

  if (isset($event['i_maxplayers']) && $event['i_maxplayers']!='' && !is_numeric($event['i_maxplayers'])) {
    $errors['i_maxplayers'] = 'The number of players must be a number';
  } else if (isset($event['i_minplayers']) && $event['i_minplayers']!='' && !is_numeric($event['i_minplayers'])) {
    $errors['i_minplayers'] = 'The number of players must be a number';
  }

  if (!isset($event['i_agerestriction']) || !$event['i_agerestriction']) {
    $errors['i_agerestriction'] = 'Please indicate the recommended minimum age';
  }

  // TODO validate this another way  
  if(isset($event['i_maxplayers']) && isset($event['i_minplayers']) && is_numeric($event['i_maxplayers']) && is_numeric($event['i_minplayers'])
     && $event['i_minplayers']>$event['i_maxplayers']) $errors['i_minplayers'] = 'The minimum number of players should be equal to or lower than the maximum.'; 

  if (isset($event['i_length']) && (!is_numeric($event['i_length']) || $event['i_length'] <= 0)) {
    $errors['i_length'] = 'The number of hours must be a number';
  }

  if (isset($event['s_desc']) && $event['s_desc']) {
    $charLimit = 200;
    $length = strlen($event['s_desc']);
    if ($length > $charLimit) {
      $errors['s_desc'] = "Please reduce your description to $charLimit characters (currently $length)";
    }
  }

  return $errors;
}

function saveEvents($idGm, &$events) {
  global $config;

  // save a new event
  if (isset($event['id_event'])
          && is_numeric($event['id_event']) 
          && $event['id_event']>=0) {

    $sql = <<< EOD
        update ucon_event set
        d_updated=?, id_convention=?, id_gm=?,
        id_event_type=?, s_game=?, s_title=?, s_desc=?,
        s_comments=?, i_maxplayers=?, i_minplayers=?, e_exper=?, e_complex=?, i_agerestriction=?,
        i_length=?, i_c1=?, i_c2=?, i_c3=?, s_setup=?, s_table_type=?, s_eventcom=?, s_platform
EOD;

  } else {

    $sql = <<< EOD
        insert into ucon_event
        (d_created, id_convention, id_gm,
        id_event_type, s_game, s_title, s_desc,
        s_comments, i_maxplayers, i_minplayers, e_exper, e_complex, i_agerestriction,
        i_length, i_c1, i_c2, i_c3, s_setup, s_table_type, s_eventcom, s_platform)
        values (?, ?, ?,
        ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?)
EOD;
  }

  global $db;
  $stmt = $db->Prepare($sql);

  foreach($events as $idx => $event) {
    $prepareArgs = array(
      null,
      $config['gcs']['year'],
      (int) $idGm,
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
      $event['s_platform'],
    );
    $ok = $db->Execute($stmt, $prepareArgs);

    if (!$ok) die('sql error: '.$db->ErrorMsg());

    $events[$idx]['gmEventNumber'] = $idx+1;
    $events[$idx]['id_event'] = $db->Insert_ID();
  }

}
