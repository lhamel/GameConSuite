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
session_start();
$_SESSION['admin']['current']['event'] = $event;

$year = $event['id_convention'];
$game = $event['s_game'];
$title = $event['s_title'];
$name = $title && $title != $game ? $game.": ".$title : $game;
$title = "Event: $name ($year) - U-Con Admin";


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

include_once INC_PATH.'resources/event/constants.php';
$trackTypes = $constants['events']['event_types'];
$trackTypes['selected'] = $event['id_event_type'];
$jsonData['eventTypeData'] = json_encode($trackTypes);
//echo '<pre>'.print_r($eventTypeData,1).'</pre>';

$roomTypes = $constants['events']['roomsWithBlank'];
$roomTypes['selected'] = $event['id_room'];
$jsonData['roomTypeData'] = json_encode($roomTypes);

$ageTypes = $constants['events']['ages'];
$ageTypes['selected'] = $event['i_agerestriction'];
$jsonData['agesData'] = json_encode($ageTypes);

// determine which tags are present
$sql = "select group_concat(tag) from ucon_event_tag as ET, ucon_tag as T "
."where ET.id_tag=T.id_tag and ET.id_event=?";
$tags = $db->getOne($sql, array($event['id_event']));
// if (!isset($tags)) {
//   echo 'Sql Error: ' . $db->ErrorMsg(); exit;
// }
$event['tags'] = str_replace(',', ', ', $tags);

$actions = array('viewMember'=>'admin/gcs/member/index.php?id_member=');
if ($event['s_title']=="DELETE" && $event['s_game']=="DELETE") {
  $actions['deleteEvent']='admin/gcs/event/delete.php?id_event='.$event['id_event'];
}


$smarty->assign('actions', $actions);
$smarty->assign('jsonData', $jsonData);

$smarty->assign('event', $event);

$condButtons = array(
  array('url'=>'approve.php?id_event='.$event['id_event'], 'label'=>'Approve/Unapprove'),
);


$buttons = array(
  array('url'=>'../submissions/eventnumbers.php?id_event='.$idEvent, 'label'=>'Set Numbers'),
  array('url'=>'copy.php?id_event='.$idEvent, 'label'=>'Copy Event'),
);

if (true) {
  $buttons = array_merge($condButtons,$buttons);
}


$smarty->assign('buttonBar', $buttons);

//$smarty->assign('template_source', '{html_options name=e_exper options=$constants.events.experience selected=$event.e_exper}');
//$selects['e_exper'] = $smarty->fetch('gcs/admin/eval.tpl');
//$smarty->assign('template_source', '{html_options name=e_complex options=$constants.events.complexity selected=$event.e_complex}');
//$selects['e_complex'] = $smarty->fetch('gcs/admin/eval.tpl');
//$smarty->assign('selects', $selects);
$content = '';
if (isset($_SESSION['admin']['current']['member'])) {
  $smarty->assign('currEvent', $_SESSION['admin']['current']['event']);
  $smarty->assign('currMember', $_SESSION['admin']['current']['member']);
  $content .= $smarty->fetch("gcs/admin/order/strip-add-ticket.tpl");
}
$content .= $smarty->fetch('gcs/admin/events/view.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
