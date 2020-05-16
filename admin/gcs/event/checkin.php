<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
$location = 'admin/gcs/event/checkin.php';

if (!isset($_REQUEST['id_event'])) {
  redirect($config['page']['depth'].'admin/gcs/search/index.php');
}
$idEvent = $_REQUEST['id_event'];
if (!is_numeric($idEvent)) die ('id_event must numeric');
$mobile = isset($_REQUEST['mobile']) && $_REQUEST['mobile'];


include_once (INC_PATH.'db/db.php');

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
	$sql = <<< EOD
    update ucon_event set 
      b_showed_up = ?,
      i_actual = ?,
      b_prize = ?,
      s_note = ?
    where id_event = ?
EOD;
  $ok = $db->Execute($sql, array($_REQUEST['b_showed_up'], 
                                 is_numeric($_REQUEST['i_actual']) ? $_REQUEST['i_actual'] : null,
                                 //null, //i_real_tickets
                                 //null, //i_remaining_tickets
                                 is_numeric($_REQUEST['b_prize']) ? $_REQUEST['b_prize'] : null,
                                 $_REQUEST['s_note'],
                                 $idEvent));
  if (!$ok) { 
  	die('SQL Error: '.$db->ErrorMsg());
  } else {
  	$ribbon = "Saved.";
  }
}


$events = $db->getAll($queries['GET_EVENT'], array($idEvent));
if (!is_array($events)) echo 'SQL Error: '.$db->ErrorMsg();
$event = $events[0];

if ($event['id_room']) {
  // retrieve the room from the database
  $event['s_room'] = $db->getOne('select s_room from ucon_room where id_room=?', $event['id_room']);
}

if ($event['id_gm']) {
  //$event['s_fname'] = $db->getOne('select s_fname from ucon_member where id_member=?', $event['id_gm']);
  $event['s_lname'] = $db->getOne('select s_lname from ucon_member where id_member=?', $event['id_gm']);
}

$year = $event['id_convention'];
$game = $event['s_game'];
$title = $event['s_title'];
$name = $title && $title != $game ? $game.": ".$title : $game;
$title = $config['gcs']['admintitle']." - Event: $name ($year)";

// also get the item's barcode for display
$barcodes = $db->getAll($queries['GET_EVENT_BARCODE'], array($idEvent));
if (!is_array($barcodes)) echo 'SQL Error: '.$db->ErrorMsg();
$barcode = $barcodes[0]['barcode'];

// display the checkin form
$actions = array(
  'save' => 'save'
);

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

if (isset($ribbon)) {
  $smarty->assign('ribbon', $ribbon);
}

$smarty->assign('header', "Event: $name ($year)");
//$smarty->assign('directions', "These events need to be copy-edited.");

$smarty->assign('barcode', $barcode);
$smarty->assign('event', $event);
if ($mobile) {
  $smarty->assign('mobile', $mobile);
  $smarty->assign('width', 920);
}
$content = $smarty->fetch('gcs/admin/events/checkin.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
