<?php
require_once '../../../../inc/db/db.php';
include_once '../../../../inc/resources/event/constants.php';
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];


define ('PRINT_PRE', true);
define ('ENTRY_LENGTH', 15);

/** convert a range of tables into a set of included table numbers */
function explodeRange($range) {
  $boundaries = explode('-', $range);
  if (count($boundaries) == 1) {
    $arr = array();
    $arr[] = (int) $boundaries[0];
    return $arr;
  } else {
    $start = (int) $boundaries[0];
    $end = (int) $boundaries[1];
    return createRange($start, $end, true);
  }
}
function createRange($start, $end, $inclusive) {
  $arr = array();
  for ($i=$start; $i<$end || ($inclusive && $i==$end); ++$i) {
    $arr[] = $i;
  }
  return $arr;
}


$sql = <<< EOD
  select id_event, s_number,
    id_event_type, s_game, s_title,
    i_minplayers, i_maxplayers, i_agerestriction,
    e_exper, e_complex,
    e_day, i_time, i_length, i_time+i_length as endtime,
    s_room, s_table,
    i_cost

  from ucon_event as E, ucon_room as R, ucon_member as M
  where E.id_room = R.id_room
    and E.id_gm = M.id_member
    and E.id_convention=?
    and E.b_approval=1
    and (NOT isNull(E.e_day))
    and (NOT isNull(E.i_time))
    #and E.id_room=18
      
  order by E.id_room, E.s_table, E.e_day, E.i_time
EOD;
$events = $db->GetAll($sql, array($year));
if (!is_array($events)) die ('SQL Error: ' + $db->ErrorMsg());

$lastRoomTable = '';
$tableContent = '';

foreach ($events as $event) {
  $id_event = $event['id_event'];
  $number = $event['s_number'];

  $event_type = $event['id_event_type'];
  $game = $event['s_game'];
  $title = $event['s_title'];

  $minplayers = $event['i_minplayers'];
  $maxplayers = $event['i_maxplayers'];
  $agerestriction = $event['i_agerestriction'];

  $exper = $event['e_exper'];
  $complex = $event['e_complex'];

  $day = $constants['events']['days'][$event['e_day']];
  $room = $event['s_room'];
  $tables = explodeRange($event['s_table']);

  // ensure the event shows up on multiple tables when appropriate
  foreach ($tables as $table) {
    if (!isset($data[$room][$table][$day])) {
      $data[$room][$table][$day] = array();
    }
    $data[$room][$table][$day][] = $event;
  }

  $tableContent = '';

}


foreach ($data as $room => $roomContents) {
  foreach ($roomContents as $table => $dayContents) {
    //move tables to right side
//    echo "<div style=\"float:left;width:3.0in;height:3.0in;\">&nbsp;</div>";
// capture echo content
ob_start();

    if ($table) echo "<p class=\"table\">$table</p>";
    echo "<p class=\"room\">$room</span></p>\n";
    echo "<table class=\"schedule\" cellspacing=\"0\">\n";
    ksort($dayContents); // a happy coincidence that alphabetical is correct order
    foreach ($dayContents as $day => $eventList) {
      echo "<tr><th colspan=\"3\">$day</th></tr>\n";
      foreach ($eventList as $event) {
        $start = $constants['events']['times'][$event['i_time']];
        $end = $constants['events']['times'][$event['endtime']];
        $number = $event['s_number'];
        $game = $event['s_game'];
        $title = $event['s_title'];
        $minplayers = $event['i_minplayers'];
        $maxplayers = $event['i_maxplayers'];
        $cost = $event['i_cost'];

        if ($game == "" || $game == $title) {
          $game = $title;
          $title = "";
        }

        echo <<< EOD
  <tr>
    <td class="event" style="width:.5in">{$start}-{$end}<br/>$number</td>
    <td class="event">$game<br/>$title</td>
    <!--<td class="event">$minplayers-$maxplayers Players</td>-->
    <td class="event">\${$cost}</td>
  </tr>

EOD;
      }
    }
    echo "</table>\n";

$output = ob_get_contents();
ob_end_clean();

  echo <<< EOD
<div class="page">
<table cellspacing="0" class="exterior">
<tr>
<td style="width:49%;" class="exterior-left">$output</td>
<td style="width:49%;" class="exterior-right">$output</td>
</tr>
</table>
</div>
EOD;

  }
}




$content = <<< EOD
<html>
<head>

<style>

div.page {
  page-break-before: always;
  page-break-inside: avoid;
}

h1 {
  font-family: Calibri, Helvetica, Arial, Sans Serif;
  width: 100%;
}

table.exterior { width: 100%; }
td.exterior-left { padding-right: 0.25in; }
td.exterior-right { padding-left: 0.25in; }
table.schedule { width: 100%; border-top: solid black 1px; }
table.schedule th { border-bottom: solid black 1px; background: #99ccff; font-size: 24pt; font-weight: bold; color: black; font-family: Calibri, Sans Serif; }
table.schedule td { border-bottom: solid black 1px; font-size: smaller; padding-left: 2px; padding-right: 2px; font-family: Calibri, Sans Serif;}

.table {
  font-family: Helvetica, Arial, sans-serif;
  font-size:64pt;
  font-weight:bold;
  text-align:center;
  margin:0px;
}

.room {
  font-family: Helvetica, Arial, sans-serif;
  font-size:28pt;
  font-weight:bold;
  text-align:center;
  margin:0px;
}

</style>

</head>
<body>

<table>
$tableContent
</table>

</body>
</html>
EOD;


print($content);


