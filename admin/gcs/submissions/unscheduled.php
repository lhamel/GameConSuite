<?php
require '../../../inc/inc.php';
include '../../../inc/db/db.php';
/*
  Events listed on this page have been scheduled but not approved by 
  administrators.  Only approved events appear in the search result for 
  attendees.
*/

$location = 'admin/gcs/submissions/unscheduled.php';
$title = 'Find Unscheduled Events - U-Con Admin';


// unapproved events
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$sql = <<< EOD
  select E.*, concat(M.s_fname, " ", M.s_lname) as gamemaster
  from ucon_event as E, ucon_member as M
  where id_convention=?
    and E.id_gm=M.id_member
    and (e_day='' OR i_time=0 OR isNull(e_day) OR isNull(i_time))
EOD;
$params = array($year);
if (isset($_GET['id_event_type']) && is_numeric($_GET['id_event_type'])) {
  $sql .= "\n    AND E.id_event_type=?";
  $params[] = $_GET['id_event_type'];
}
if (isset($_GET['order']))
  $sql .= ' ORDER BY '.$_GET['order'];

include INC_PATH.'db/db.php';
$events = $db->getArray($sql, $params);

$actions = array(
  'detail' => '../event/index.php?id_event=',
  'list' => basename(__FILE__).'?year='.$year,
  'addTicket' => '', // no ticket purchases
  'navigateMember' => '', // no navigating to members
);

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

$filters = array(
  'id_event_type' => array(
    'label'=>'Event Type',
    'options'=>$constants['events']['event_types'],
  ),
);

include '_tabs.php';

$smarty->assign('actions', $actions);
$smarty->assign('filters', $filters);
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);
$smarty->assign('events', $events);

$smarty->assign('header', "Unscheduled Events $year");
$smarty->assign('directions', "These events have not been scheduled.");

// render the page
$content = $smarty->fetch('gcs/admin/events/list.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

