<?php
include '../../../inc/inc.php';

/*
  Events listed on this page have been scheduled but not approved by 
  administrators.  Only approved events appear in the search result for 
  attendees.
*/

$location = 'admin/gcs/events/numbers.php';
$title = $config['gcs']['admintitle']." - Event Number Management";


// unapproved events
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'ids';
//echo 'mode:'.$mode;

// all
$sql1 = <<< EOD
select E1.*
from ucon_event as E1, ucon_event as E2, ucon_member as M
where
  E1.id_gm=M.id_member
  and E1.id_convention=? and E2.id_convention=E1.id_convention
  and E1.s_number!="" and E2.s_number!=""
  and E1.s_number=E2.s_number and E1.id_event != E2.id_event
EOD;

// typedaytime
$sql2 = <<< EOD
select E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
      E.i_time+E.i_length as endtime
from ucon_event as E, ucon_member as M
where E.id_gm=M.id_member
  and id_convention=?
  and s_number!=""
  and right(left(s_number, 5), 2) != left(e_day,2)
EOD;

// typedaytime
$sql3 = <<< EOD
select E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
      E.i_time+E.i_length as endtime 
from ucon_event as E, ucon_member as M
where
  E.id_gm=M.id_member
  and id_convention=?
  and s_number!=""
  and right(left(s_number, 7), 2) != i_time
EOD;

// all
$sql4 = <<< EOD
select E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
      E.i_time+E.i_length as endtime
from ucon_event as E, ucon_member as M
where
  E.id_gm=M.id_member
  and E.id_convention=?
  and E.s_number=""
  and E.e_day != "" and E.i_time != 0
EOD;

$sql5 = <<< EOD
select E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
      E.i_time+E.i_length as endtime
from ucon_event as E, ucon_member as M
where
  E.id_gm=M.id_member
  and E.id_convention=?
  and (E.id_event % 10000) != E.s_number
EOD;


//if (isset($_GET['order']))
//  $sql .= ' ORDER BY '.$_GET['order'];

$order = '';
if (isset($_GET['order'])) {
  $order = ' ORDER BY '.$_GET['order'];
}

include INC_PATH.'db/db.php';
$events1 = $db->getArray($sql1.$order, array($year));
if ($mode=='typedaytime') {
  $events2 = $db->getArray($sql2.$order, array($year));
  $events3 = $db->getArray($sql3.$order, array($year));
}
$events4 = $db->getArray($sql4.$order, array($year));
if ($mode=='ids') {
  $events5 = $db->getArray($sql5.$order, array($year));
}

$actions = array(
  'detail' => 'eventnumbers.php?id_event=',
  'list' => basename(__FILE__).'?year='.$year,
);

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$subheaders = array(
  array(
    'header'=>'Duplicate Event Number',
    'events'=>$events1,
  ),
);

if ($mode=='typedaytime') {
$subheaders = array_merge($subheaders, array(
  array(
    'header'=>'Event number shows the incorrect day',
    'events'=>$events2,
  ),
  array(
    'header'=>'Event number shows the incorrect time',
    'events'=>$events3,
  ),
));
}
if ($mode=='ids') {
$subheaders = array_merge($subheaders, array(
  array(
    'header'=>'Event number not equal to last 4 digits of event id',
    'events'=>$events5,
  ),
));
}

$subheaders = array_merge($subheaders, array(
  array(
    'header'=>'Event missing its event number',
    'events'=>$events4,
  ),
));


$smarty->assign('numberAlgorithms', array(
    'autocode/eventnum.php?alg=4digitid'=>'Four Digit Event Id',
    //'eventcode'=>'Code (BG-SA10-01)',
  ));

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

$smarty->assign('header', "Event Number Management $year");
$smarty->assign('directions', "Find incorrect or duplicate event numbers.");
$smarty->assign('subheaders', $subheaders);
$content = 'Select number format: <a href="numbers.php?mode=ids">1234</a>, <a href="numbers.php?mode=typedaytime">BG-FR09-01</a>';
$content .= $smarty->fetch('gcs/admin/events/list.tpl');
$content .= $smarty->fetch('gcs/admin/events/numbers.tpl');


$smarty->assign('content', $content);
$smarty->display('base.tpl');

