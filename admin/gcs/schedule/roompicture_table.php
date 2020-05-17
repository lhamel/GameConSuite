<?php
require_once '../../../inc/inc.php';

if (!is_numeric($_GET['room'])) {
  redirect('roompicture.php');
}
$roomId =  $_GET['room'];

$year = isset($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$location = 'admin/gcs/schedule/roompicture.php';
require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/adminmenu.php';
$title = 'Schedule Overview';

include '_tabs.php';

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
    return range($start, $end);
  }
}



require_once INC_PATH.'db/db.php';
require_once INC_PATH.'resources/event/constants.php';



// look up events for the convention that have tickets
  $sql = <<< EOD
  select
    O.s_subtype as id_event,
    sum(O.i_quantity) as players
  from ucon_order as O
  where O.id_convention=?
    and O.s_type="Ticket"
  group by
    O.s_subtype
  having
    players>0
EOD;
$playerQuantities = $db->GetAssoc($sql, array($year));
if (!is_array($playerQuantities)) { echo "SQL Error: " . $db->ErrorMsg(); exit; }
//echo "<pre>playerQuantities".print_r($playerQuantities,1)."</pre>";


  $sql = <<< EOD
  select left(if (s_lname='',s_fname,s_lname),10) as gm,
        s_number,
        s_setup,
        id_room,
        s_table,
        e_day, i_time,
        i_length,
        id_event
  from ucon_member as M, ucon_event_type as ET, ucon_event as E
  where E.id_gm=M.id_member
    and E.id_event_type=ET.id_event_type
    and E.id_convention=?
    and E.id_room=?
  group by
    e_day, i_time, i_length,  
    s_abbr, id_event, i_minplayers, i_maxplayers
  order by e_day, i_time, s_table
EOD;
$rows = $db->GetAll($sql, array($year, $roomId));
if (!is_array($rows)) { echo "SQL Error: " . $db->ErrorMsg(); exit; }
$tableKeys = array();
$matrix = array();
$link = '../event/index.php?id_event=';
foreach ($rows as $row) {
	$day = $row['e_day'];
  $times = range($row['i_time'], $row['i_time']+$row['i_length']-1);
  $tables = explodeRange($row['s_table']);
  if (!isset($matrix[$day])) $matrix[$day] = array();
//  if (!isset($matrix[$day][$time])) $matrix[$day][$time] = array();
//  if (!isset($matrix[$day][$time][$roomId])) $matrix[$day][$time][$roomId] = "";

  $id = $row['id_event'];

  foreach ($times as $time) {
  	if (!isset($matrix[$day][$time])) $matrix[$day][$time] = array();
    foreach ($tables as $table) {
      if (!isset($matrix[$day][$time][$table])) $matrix[$day][$time][$table] = array();
      $matrix[$day][$time][$table][] = $row;
      $tableKeys[$table] = 1;
    }
//    $timeKeys[$day][$time] = 1;
  }
  $eventIds[] = $row['id_event'];
//  $dayKeys[$day] = 1;
}
//ksort($tableKeys);
//echo '<pre>'.print_r(count($tableKeys),1).'</pre>';

ksort($matrix);
foreach ($matrix as &$dayMatrix) {
  ksort($dayMatrix);
}

function canJoin($dir, $day, $time, $table) {
  switch($dir) {
    case 'left': return canJoinLong($day,$time,$table, $day,$time,$table-1);
    case 'right': return canJoinLong($day,$time,$table, $day,$time,$table+1);
    case 'top': return canJoinLong($day,$time,$table, $day,$time-1,$table);
    case 'bottom': return canJoinLong($day,$time,$table, $day,$time+1,$table);
  }
  return false;
}
function canJoinLong($day1, $time1, $table1, $day2, $time2, $table2) {
  if (!$time1 || !$time2) return false;
  global $matrix;
  $obj1 = $matrix[$day1][$time1][$table1];
  $obj2 = @$matrix[$day2][$time2][$table2];
  if (!is_array($obj1) || count($obj1)!=1) return false;
  if (!is_array($obj2) || count($obj2)!=1) return false;

  //echo '<pre>'.print_r($matrix[$day1][$time1],1).'</pre>';
  // echo "<hr>lookup $day2 $time2 $table2<hr>";
  //echo 'cmp: '.($obj1[0]['id_event']." == ".$obj2[0]['id_event'])."<br/>";
  //echo "<hr>HERE<HR>";
  return ($obj1[0]['id_event'] == $obj2[0]['id_event']);
}

// calculate how cells can be merged
$joinMatrix = array();
foreach ($matrix as $day => $matrixDay) {
  $joinMatrix[$day] = array();
  foreach ($matrixDay as $time => $matrixTime) {
    foreach ($matrixTime as $table => $eventList) {
//echo"<hr>$table<hr>";
	  	$eventCount = count($eventList);
			if ($eventCount == 0) {
	  		$joinMatrix[$day][$time][$table] = array('class' => 'none');
			} else if ($eventCount > 1) {
	      $joinMatrix[$day][$time][$table] = array('class' => 'multiple');
			} else {
	      $style = 'font-size:8pt;';
	      $style .= 'border-top: '.(canJoin('top', $day, $time, $table)?'0':'solid black 1px').';';
	      $style .= 'border-bottom: '.(canJoin('bottom', $day, $time, $table)?'0':'solid black 1px').';';
	      $style .= 'border-left: '.(canJoin('left', $day, $time, $table)?'0':'solid black 1px').';';
	      $style .= 'border-right: '.(canJoin('right', $day, $time, $table)?'0':'solid black 1px').';';
	      $joinMatrix[$day][$time][$table] = array('class' => 'single', 'style' => $style);
	    }
    }
	}
}

//echo "<pre style=\"text-align: left; font-size: 8pt;\">".print_r($joinMatrix,1)."</pre>";

//TODO remove/replace function "basename";
$actions = array('list'=>basename(__FILE__),
                 'filterDay'=>basename(__FILE__).'?day=',
                 'filterCategory'=>basename(__FILE__).'?category=',
                 'detail'=>'../event/index.php?id_event=',
                 'navigateMember'=>'../member/index.php?id_member=',
                 'showExpanded'=>(isset($_GET['expanded']) ? true : false),
                );






$filters = array(
  'room' => array(
    'label'=>'Room',
    'options'=>$constants['events']['rooms'],
  ),
  'year' => array(
    'label'=>'Year',
    'options'=>array_reverse(array_combine(range(2002,$config['gcs']['year']),range(2002,$config['gcs']['year'])), true),
    'noall'=>true,
    'default'=>$year,
  ),
);

// generate a URL with the other filter parameters
foreach ($filters as $k=>$filter) {
  $otherFilters = $filters;
  unset($otherFilters[$k]);
  $otherParams = array_keys($otherFilters);
  $fixed = '';
  foreach ($otherParams as $param) {
    $fixed .= '&'.$param.'='.$_GET[$param];
  }
  $filters[$k]['fixed'] = $fixed;
}

//$smarty->assign('tableList', array('test1', 'test2', 'test3', 'test4', 'test5', 'test6'));
ksort($tableKeys);
$smarty->assign('tableList', array_keys($tableKeys));
//echo "<pre style=\"text-align: left; font-size: 8pt;\">".print_r(array_keys($tableKeys),1)."</pre>";

// render the event results
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('actions', $actions);
$smarty->assign('filters', $filters);
$smarty->assign('matrix', $matrix);
$smarty->assign('joinMatrix', $joinMatrix);

$content = $smarty->fetch('gcs/common/filters.tpl');
$content .= $smarty->fetch('gcs/admin/schedule/roomtablebytime.tpl');

// render the page
$smarty->assign('content', $content);
$smarty->assign('width', 1580);
$smarty->display('base.tpl');

