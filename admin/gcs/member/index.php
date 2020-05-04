<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/member/index.php';
$title = 'Member Information'; // override with name further down

include INC_PATH.'auth.php';


$year = @is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$id_member = is_numeric($_GET['id_member']) ? $_GET['id_member'] : 0;
if ($id_member <= 0) {
  redirect($config['page']['depth'].'admin/gcs/membersearch/index.php');
}

include INC_PATH.'db/db.php';

$sqlMember = <<< EOD
  select *
  from ucon_member
  where id_member=?
EOD;

$sqlOrder = <<< EOD
  select *
  from ucon_order
  where id_member=?
    and id_convention=?
EOD;

$sqlGm = <<< EOD
  select E.*,
    E.i_time + E.i_length as endtime,
    CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
    if(isNull(O.id_order), 0, sum(O.i_quantity)) as prereg
  from ucon_member as M, ucon_event as E left join ucon_order as O on (O.s_subtype=E.id_event)
  where E.id_gm=?
    and E.id_gm=M.id_member
    and E.id_convention=?
  group by E.id_event, M.id_member
EOD;
if (isset($_REQUEST['order'])) {
  // FIXME better input checking
  $sqlGm .= " ORDER BY ".$_REQUEST['order'];
}

$members = $db->getArray($sqlMember, array($id_member));
if (!is_array($members)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error.  Please report via contact form. '.$db->ErrorMsg());
} else if (count($members) != 1) {
  error_log("Attempted to access member id which doesn't exist: $id_member");
  die('No such member '.$id_member);
} else {
  $member = $members[0];
}
session_start();
$_SESSION['admin']['current']['member'] = $member;

//$order = $db->getArray($
$events = $db->getArray($sqlGm, array($id_member, $year));
if (!is_array($events)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error.  Please report via contact form. '.$db->ErrorMsg());
}

// get the years of interest
$yearsFromOrder = $db->getAssoc("select id_convention, id_convention as id2 from ucon_order where id_member=? group by id_convention", array($id_member));
$yearsFromEvents = $db->getAssoc("select id_convention, id_convention as id2 from ucon_event where id_gm=? group by id_convention", array($id_member));
$yearsOfInterest = $yearsFromOrder+$yearsFromEvents;
//echo "<pre>from order: ".print_r($yearsFromOrder, 1)."</pre>";
//echo "<pre>from events: ".print_r($yearsFromEvents, 1)."</pre>";
//echo "<pre>from merged: ".print_r($yearsFromOrder+$yearsFromEvents,1)."</pre>";

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('member', $member);
$smarty->assign('events', $events);
$smarty->assign('yearsOfInterest', $yearsOfInterest);

$actions = array('detail'=>'../event/index.php?id_event=',
                 'list'=>'index.php?id_member='.$id_member,
                 'showAuths'=>'linkassoc.php',
                 'modAuth'=>'_linkassoc.php');

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);
$smarty->assign('additional', array('prereg'=>'Prereg'));

// render the page
$content = '';
if (isset($_SESSION['admin']['current']['event'])) {
  $smarty->assign('currEvent', $_SESSION['admin']['current']['event']);
  $smarty->assign('currMember', $_SESSION['admin']['current']['member']);
  $content .= $smarty->fetch("gcs/admin/order/strip-add-ticket.tpl");
}

// retrieve the member authorizations
$authorizations = $associates->listAuthorizations($id_member);
//echo "<pre>".print_r($authorizations,1)."</pre>";
$smarty->assign('authorizations', $authorizations);

//echo '<pre>'.print_r($_SESSION,1).'</pre>'; exit;
$content .= $smarty->fetch('gcs/admin/member/view.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');


