<?php
require_once '../../../inc/inc.php';
$year = (isset($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : $config['gcs']['year'];
$location = 'admin/gcs/schedule/roompicture.php';
require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/adminmenu.php';
$title = 'Schedule Overview';

include '_tabs.php';


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
  select
        id_room,
        e_day, i_time, i_length,

        s_abbr as s_event_type,
        id_event,

        i_minplayers, i_maxplayers
  from ucon_member as M, ucon_event_type as ET, ucon_event as E
  where E.id_gm=M.id_member
    and E.id_event_type=ET.id_event_type
    and E.id_convention=?
  group by
    e_day, i_time, i_length, id_room, 
    s_abbr, id_event, i_minplayers, i_maxplayers
  order by e_day, i_time, id_room, s_table
EOD;
$rows = $db->GetAll($sql, array($year));
if (!is_array($rows)) { echo "SQL Error: " . $db->ErrorMsg()."<p>SQL:<br>$sql</p>"; exit; }
$matrix = array();
$link = '../event/index.php?id_event=';
foreach ($rows as $row) {
	$day = $row['e_day'];
  $time = $row['i_time'];
  $roomId = $row['id_room'] ? $row['id_room'] : 0;
  if (!isset($matrix[$day])) $matrix[$day] = array();
  if (!isset($matrix[$day][$time])) $matrix[$day][$time] = array();
  if (!isset($matrix[$day][$time][$roomId])) $matrix[$day][$time][$roomId] = "";

  $id = $row['id_event'];
  $type = substr($row['s_event_type'], 0, 1);
  $style = (isset($playerQuantities[$id]) && $playerQuantities[$id]>$row['i_minplayers']) ? ' style="color: green;"' : '';
  if ($time>0 && $row['i_length']>0) {
    for ($i = 0; $i<$row['i_length']; ++$i) {
    	$hour = $time + $i;
      if (!isset($matrix[$day][$hour])) { $matrix[$day][$hour] = array(); } // eliminate warning about undefined offset
      if (!isset($matrix[$day][$hour][$roomId])) { $matrix[$day][$hour][$roomId] = ''; } // eliminate warning about undefined offset
      $matrix[$day][$hour][$roomId] .= "<a href=\"{$link}{$id}\" {$style}>$type</a>";
    }
  } else {
      $matrix[$day][''][$roomId] .= "<a href=\"{$link}{$id}\" {$style}>$type</a>";
  }
}

ksort($matrix);
foreach ($matrix as &$dayMatrix) {
  ksort($dayMatrix);
}

//echo "<pre style=\"text-align: left; font-size: 8pt;\">".print_r($matrix,1)."</pre>";


$actions = array('list'=>basename(__FILE__),
                 'filterDay'=>basename(__FILE__).'?day=',
                 'filterCategory'=>basename(__FILE__).'?category=',
                 'detail'=>'../event/index.php?id_event=',
                 'navigateMember'=>'../member/index.php?id_member=',
                 'showExpanded'=>(isset($_GET['expanded']) ? true : false),
                );






$filters = array(
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



// render the event results
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('actions', $actions);
$smarty->assign('filters', $filters);
$smarty->assign('matrix', $matrix);
$smarty->assign('year', $year);

$content = $smarty->fetch('gcs/common/filters.tpl');
$content .= $smarty->fetch('gcs/admin/schedule/roombytime.tpl');

// render the page
$smarty->assign('content', $content);
$smarty->assign('width', 1500);
$smarty->display('base.tpl');

