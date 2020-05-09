<?php
/*
	Set a list of event numbers
*/
include '../../../inc/inc.php';
$location = 'admin/gcs/events/eventnumbers.php';
$title = 'Assign Event Numbers';

include_once (INC_PATH.'db/db.php');


if (isset($_POST['action']))
{
  $stmt = $db->Prepare("update ucon_event set s_number=?, b_approval=? where id_event=?");
  $numbers = $_POST['number'];
  foreach ($numbers as $id => $number)
  {
    if (!is_numeric($id)) die("bad id");
    $data = array(
      $number,
      (isset($_POST['approved'][$id]) ? 1 : 0),
      $id,
    );
    $ok = $db->execute($stmt, $data);
    if (!ok) die($db->ErrorMsg());
  }

  redirect('eventnumbers.php?id_event='.$_POST['id_event']);
}

if (!isset($_GET['id_event'])) {
  redirect('unscheduled');
  exit;
}

// check input
if(!is_numeric($_GET['id_event']))
{
  die("bad id");
}

$events = $db->getAll($queries['GET_EVENT'], array($_GET['id_event']));
if (!is_array($events)) die ("SQL Error: ".$db->ErrorMsg());
$event = $events[0];

// TODO set current event in the session
// echo "<pre>EVENT\n".print_r($event, 1)."</pre>";

$day = $event['e_day'];
$time = $event['i_time'];
$type = $event['id_event_type'];
$year = $event['id_convention']; //$config['ucon']['year'];
$sql = <<< EOD
  select id_event, s_number, s_game, s_title, id_gm, b_approval, 
    concat(M.s_fname, " ", M.s_lname) as gm
  from ucon_event as E, ucon_member as M
  where e_day=?
    and i_time=?
    and id_event_type=?
    and M.id_member=E.id_gm
    and E.id_convention=?
  order by s_number, E.id_gm, id_event;
EOD;
$events = $db->getAll($sql, array($day, $time, $type, $year));
// echo "<pre>EVENTS\n".print_r($events, 1)."</pre>";


$title = "U-Con - Assign Numbers for $type $day {$time}00";
include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';


$smarty->assign('event', $event);
$smarty->assign('events', $events);
$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

$content = $smarty->fetch('gcs/admin/events/eventnumbers.tpl');

$smarty->assign('content', $content);
$smarty->display('base.tpl');
