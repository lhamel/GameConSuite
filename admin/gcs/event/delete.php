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


if ($event['id_convention'] != $year) {
  echo "Delete failed.  Can only delete events in the current year.";
  exit;
}

if ($event['s_title']!="DELETE" || $event['s_game']!="DELETE") {
  echo "Delete failed.  Title and game must be set to DELETE.";
  exit;
}


session_start();
unset($_SESSION['admin']['current']['event']);

$db->execute('delete from ucon_event where id_event=? and s_title="DELETE" and s_game="DELETE" and id_convention=?', array($idEvent, $year));

echo "Delete successful $idEvent. Go back.";

