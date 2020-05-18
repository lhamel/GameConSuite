<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
$location = 'admin/gcs/event/index.php';

if (!isset($_GET['id_event'])) {
  redirect($config['page']['depth'].'admin/gcs/search/index.php');
}
$idEvent = $_GET['id_event'];

include_once (INC_PATH.'db/db.php');
$events = $db->getAll($queries['GET_EVENT'], array($idEvent));
if (!is_array($events)) die ("SQL Error: ".$db->ErrorMsg());
$event = $events[0];

$tickets = $db->getAll($queries['GET_EVENT_TICKETS'] . " ORDER BY id_order ASC", array($idEvent));
if (!is_array($tickets)) die ("SQL Error: ".$db->ErrorMsg());

$game = $event['s_game'];
$title = $event['s_title'];
$name = $title && $title != $game ? $game.": ".$title : $game;
$title = $config['gcs']['admintitle']." - Event: $name ($year)";


// display the checkin form

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

if (isset($ribbon)) {
  $smarty->assign('ribbon', $ribbon);
}

$smarty->assign('header', "Event: $name ($year)");
//$smarty->assign('directions', "These events need to be copy-edited.");

$actions = array('viewMember'=>'admin/gcs/member/index.php?id_member=');
$smarty->assign('actions', $actions);

$smarty->assign('event', $event);
$smarty->assign('tickets', $tickets);
$content = $smarty->fetch('gcs/admin/events/tickets.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
