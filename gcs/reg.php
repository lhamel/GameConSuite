<?php
include '../inc/inc.php';

$location = 'gcs/reg.php';
$title = 'Account Information'; // override with name further down

require_once __DIR__.'/../inc/inc.php';
require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php

// must be logged in to view
if (!$auth->isLogged()) {
  header('HTTP/1.0 403 Forbidden');
  redirect('login.php');
  exit();
}

// collect parameters
$currUser = $auth->getCurrentUser();
$uid = $currUser['uid'];

// get the list of authorized users from association table
$year = @is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$members = $associates->listAssociates($uid);

$balances = array();
if (count($members)>0) {
  $memberIds = join(',', array_keys($members)); // comma sep list of keys
  $sql = "select id_member, sum(i_quantity*i_price) from ucon_order "
       . "where id_convention=? and id_member in ($memberIds) group by id_member";
  $balances = $db->getAssoc($sql, array($year));
  if (!is_array($balances)) {
    echo "SQL Error: ".$db->errorMsg();
    exit;
  }
}

foreach ($members as $id => $v) {
  // get a count of events (approved and unapproved) for each member
  $sql = "select concat(count(id_event), '/', sum(b_approval)) as numevents "
       . " from ucon_event where id_gm=? and id_convention=?";
  $result = $db->getAll($sql, array($id, $year));
  if (is_array($result)) {
    $members[$id] += $result[0];
  } else {
    $members[$id] += array('numevents'=>"0/0");
  }

  // TODO get a count of badges, tickets, and other items for each member
  $sql = "select concat(sum(if(s_type='Badge',1,0)),'/',sum(if(s_type='Ticket',1,0)),'/',sum(if(s_type='Shirt' or s_type='Misc',1,0))) as items "
       . " from ucon_order where id_member=? and id_convention=?";
  $result = $db->getAll($sql, array($id, $year));
  if (is_array($result)) {
    $members[$id] += $result[0];
  } else {
    $members[$id] += array('order'=>"none: ".$db->errorMsg());
  }

  $first = $members[$id]['s_fname'];
  $last = $members[$id]['s_lname'];
  $full = $first . ($first && $last ? ' ' : '') . $last;
  $members[$id]['name'] = '<a href="envelope.php?envelope='.$id.'">'.$full.'</a>';
  $members[$id]['balance'] = '$' . (isset($balances[$id]) ? $balances[$id] : "0.00");
}

//echo '<pre>'.print_r($members,1).'</pre>';

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/menu.php';

$smarty->assign('events', $members); // TODO bug fix, field is called events
//$smarty->assign('events', $events);

$actions = array();

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);
$smarty->assign('columns', array(
  'name'=>'Name',
  's_email'=>'Email',
  'numevents'=>'Events (submitted/approved)',
  'items'=>'Prereg (badge/ticket/other)',
  'balance'=>'Balance',
));
$smarty->assign('columnsAlign', array(
  'balance'=>'right',
));

// render the page
//echo '<pre>'.print_r($_SESSION,1).'</pre>'; exit;
$content = <<< EOD
<h2>Envelopes</h2>
<p>U-Con will print an envelope for each person listed below who has preregistrations items which have been earned or purchased.  Click on the name of the person to edit their registration.</p>
EOD;

$content .= '<p class="auth">'.$smarty->fetch('gcs/common/general-table.tpl').'</p>';
$content .= '<p><a href="envelope_edit.php" class="button">Add Envelope</a></p>';


$smarty->assign('content', $content);
$smarty->display('base.tpl');


