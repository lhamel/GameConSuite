<?php
include_once ('../../inc/inc.php');
include_once (INC_PATH.'db/db.php');

// This document must return the results in a standard format required by 
// JQuery UI Autocomplete (https://api.jqueryui.com/autocomplete/)

$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : '';
if ($term) {
  $sql = 'select distinct s_game as value, s_game from ucon_event where b_approval=1 and i_maxplayers>0 and id_convention != ? and s_game like ? order by s_game limit 20';
  $results = $db->getAssoc($sql, array($config['gcs']['year'], "%$term%"));
} else {
  $sql = <<< EOD
  select distinct s_game as value, s_game from ucon_event where b_approval=1 and i_maxplayers>0 and id_convention != ? and s_game!="" order by s_game limit 20';

EOD;
  $results = $db->getAssoc($sql, array($config['gcs']['year']));
}

if (!is_array($results)) { 
    header("500 Internal Server error");
    echo "SQL Error: ".$db->ErrorMsg(); exit; 
}

header("Content-type: application/json");
echo json_encode(array_keys($results));

// TODO generate a separate table with autocomplete options from a reputable source, like BGG.
