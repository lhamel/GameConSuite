<?php
include_once ('../../../inc/inc.php');
include_once (INC_PATH.'db/db.php');

// This document must return the results in a standard format required by 
// JQuery UI Autocomplete (https://api.jqueryui.com/autocomplete/)

$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : '';
if ($term) {
  $term = "%$term%";
  $sql = 'select tag as value, tag as label from ucon_tag where tag like ? order by tag';
  $results = $db->getAssoc($sql, array($term));
} else {
  $term = "%$term%";
  $sql = 'select tag as value, tag as label from ucon_tag where tag like ? order by tag';
  $results = $db->getAssoc($sql, array($term));
}

if (!is_array($results)) { 
    header("500 Internal Server error");
    echo "SQL Error: ".$db->ErrorMsg(); exit; 
}

header("Content-type: application/json");
echo json_encode(array_keys($results));
