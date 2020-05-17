<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
$location = 'admin/gcs/event/index.php';

if (!isset($_GET['id_event'])) {
  redirect($config['page']['depth'].'admin/gcs/search/index.php');
}
$id_event = $_GET['id_event'];

include_once (INC_PATH.'db/db.php');
$events = $db->getAll($queries['GET_EVENT'], array($id_event));
if (!is_array($events)) die ("SQL Error: ".$db->ErrorMsg());
$event = $events[0];


if ($event['id_convention'] != $config['gcs']['year']) {
  echo "Aprove failed.  Can only approve events in the current year. ".$event['id_convention'];
  exit;
}

$approval = $event['b_approval'];
$newApproval = ($approval==1) ? 0 : 1; // update approval value

session_start();

$db->execute('update ucon_event set b_approval=? where id_event=? and id_convention=?', array($newApproval, $id_event, $config['gcs']['year']));

redirect('index.php?id_event='.$id_event);

echo "Approval successful. Go back.";

