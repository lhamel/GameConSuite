<?php
require_once '../../../../config/config.php';
require_once '../../../../inc/db/db.php';
include_once '../../../../inc/resources/event/constants.php';
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

// header("Content-type: text/plain");


$now = date("Y-m-d");// central time

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=events-$now.csv");
header("Pragma: no-cache");
header("Expires: 0");



$sql = <<< EOD
  select E.*, M.s_fname, M.s_lname, R.s_room
#    id_event, s_number,
#    id_event_type, s_game, s_title,
#    i_minplayers, i_maxplayers, i_agerestriction,
#    e_exper, e_complex,
#    e_day, i_time, i_length, 
    ,i_time+i_length as endtime
#    s_room, s_table,
#    i_cost
    ,concat("'",s_table) as s_table

  from ucon_event as E, ucon_room as R, ucon_member as M
  where E.id_room = R.id_room
    and E.id_gm = M.id_member
    and E.id_convention=?
    #and E.b_approval=1
    #and (NOT isNull(E.e_day))
    #and (NOT isNull(E.i_time))
    #and E.id_room=18

  order by E.e_day, E.i_time, E.id_room, E.s_table
EOD;
$events = $db->GetAll($sql, array($year));
if (!is_array($events)) die ('SQL Error: ' + $db->ErrorMsg());

$cols = [
    'id_event', 's_number',
    's_lname',

    'id_event_type', 's_game', 's_title',
    'i_minplayers', 'i_maxplayers', 'i_agerestriction',
    'e_exper', 'e_complex',
    'e_day', 'i_time', 'i_length', 'endtime',
    's_room', 's_table',
    'i_cost',

    'b_approval', 'b_edited',
    's_platform', 's_vttlink', 's_vttinfo',
    's_desc', 's_desc_web'

];


// print headers
foreach($cols as $col) {
    echo ($col).',';
}
echo "\n";

// print rows
foreach ($events as $event) {
    foreach($cols as $col) {
        echo '"'.str_replace('"',"'",$event[$col]).'",';
    }
    echo "\n";
}
