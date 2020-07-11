<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/ops/popular.php';
$title = 'Popular Events';
$year = (isset($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : $config['gcs']['year'];

include INC_PATH.'resources/event/constants.php';

// Parameters
$minimumTicketsSold = 1;
$trackId = isset($_GET['category']) ? $_GET['category'] : '';


$conIdsSql = <<< EOD
select id_convention as k, id_convention from ucon_event group by id_convention
UNION
select id_convention as k, id_convention from ucon_convention;
EOD;
$conIds = $db->getAssoc($conIdsSql);
if ($conIds === false) { echo "SQL Error (browse.php::".__LINE__.")".$db->ErrorMsg(); exit; }
$year = $config['gcs']['year'];
$conIds[$year] = $year;
arsort($conIds);


$filters = array(
  'category' => array(
    'label'=>'Category',
    'options'=>$constants['events']['event_types'],
  ),
  'year' => array(
    'label'=>'Year',
    'options'=>$conIds,
    'noall'=>true,
    'default'=>$year,
  ),
);

include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';
include '_tabs.php';

$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('filters', $filters);

$content = '<h1>Popular Events</h1>';
$content .= $smarty->fetch('gcs/common/filters.tpl');

if (!$trackId) {
  $content .= '<p>Select an event category.</p>';
  $smarty->assign('content', $content);
  $smarty->display('base.tpl');
  exit;
}


$sql = <<< EOD
  select E.*, M.*,
    concat(M.s_fname, " ", M.s_lname) as gamemaster, 
    i_time+i_length as endtime, 
    SUM(O.i_quantity) as tix
  from ucon_event as E, ucon_member as M, ucon_order as O
  where E.id_event=O.s_subtype
    and E.id_event_type=?
    and E.id_convention=?
    and E.id_gm = M.id_member
  group by E.id_event
  having tix >= ?
  order by tix DESC
EOD;

include_once INC_PATH.'db/db.php';
$events = $db->getAll($sql, array($trackId, $year, $minimumTicketsSold));
if (!is_array($events)) die('SQL Error: '.$db->ErrorMsg());

$actions = array(
  'detail' => '../event/index.php?id_event=',
  'navigateMember' => '../member/index.php?id_member='
);
$smarty->assign('actions', $actions);
$smarty->assign('events', $events);

$additionalFields = array('tix'=>'Tickets');
$smarty->assign('additional', $additionalFields);

$content .= $smarty->fetch('gcs/event/list-table.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
