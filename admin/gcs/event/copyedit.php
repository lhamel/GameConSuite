<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
$location = 'admin/gcs/event/copyedit.php';


/* while taking the id though the URL might be hazardous, I'm allowing it 
 * because this is the admin section */
if (!is_numeric($_REQUEST['id_event'])) {
  die("required id_event");
}
$idEvent = $_REQUEST['id_event'];
include_once (INC_PATH.'db/db.php');

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'copyedit_save') {
  $title = $_REQUEST['s_title'];
  $game = $_REQUEST['s_game'];
  $desc = $_REQUEST['s_desc'];
  $descShort = $_REQUEST['s_desc_web'];
  $sql = 'update ucon_event set s_title=?, s_game=?, s_desc=?, s_desc_web=? where id_event=?';
  $ok = $db->Execute($sql, array($title, $game, $desc, $descShort, $idEvent));
  if (!$ok) {
    die("<pre>SQL Error: \n".print_r($sql,1)."\n\nError: ".$db->ErrorMsg);
  } else {
  	$status = 'Saved.';
  }
} else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'copyedit_approve') {
  $value = $_REQUEST['status'];
  $sql = 'update ucon_event set b_edited=? where id_event=?';
  $ok = $db->Execute($sql, array( ($value?1:0), $idEvent));
  if (!$ok) {
    die("<pre>SQL Error: \n".print_r($sql,1)."\n\nError: ".$db->ErrorMsg);
  } else {
  	$status = $value ? 'Marked completed.' : 'Marked not-edited.';
  }
}

$events = $db->getAll($queries['GET_EVENT'], array($idEvent));
if (!is_array($events)) die ("SQL Error: ".$db->ErrorMsg());
$event = $events[0];

$year = $event['id_convention'];
$game = $event['s_game'];
$title = $event['s_title'];
$name = ($title && ($title != $game)) ? $game.": ".$title : $game;


$title = $config['gcs']['admintitle']." - Event: $name ($year)";
include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

$smarty->assign('event', $event);
$content = '<h1>Copy Edit</h1>';
if (isset($status)) $content .= "<p style=\"background: #99ff99;\">$status</p>";
if (isset($error)) $content .= "<p style=\"background: #ff9999;\">$error</p>";
$content .= $smarty->fetch('gcs/admin/events/copyedit.tpl');
$content .= '<hr/>';
$content .= '<h3>Preview Event Entry</h3><p>';
$content .= $smarty->fetch('gcs/event/detail-short.tpl').'</p>';

$content .= '<h3>Short version</h3><p>';
$content .= $smarty->fetch('gcs/event/detail-reduced.tpl').'</p>';


$basename = $config['page']['basename'];
if (!$event['b_edited']) {
  $content .= <<< EOD
<p><a href="{$basename}?id_event=$idEvent&amp;action=copyedit_approve&amp;status=1"
class="button">Mark Copy-Edit Complete In Database</a></p>
EOD;
} else {
  $content .= <<< EOD
<p><a href="{$basename}?id_event=$idEvent&amp;action=copyedit_approve&amp;status=0"
class="button">Unmark Copy-Edit In Database</a></p>
EOD;
}


$smarty->assign('content', $content);
$smarty->display('base.tpl');
