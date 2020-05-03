<?php
require_once '../../inc/inc.php';
$year = $config['gcs']['year'];

//
// initialize smarty
require_once INC_PATH.'smarty.php';
$location = "gcs/gm/submit.php";
require_once INC_PATH.'layout/menu.php';
$smarty->assign('config', $config);
$smarty->assign('js', array($config['page']['depth'] . "js/gcs/gm.js"));


if (!$config['allow']['submit_events']) {
  $year = $config['gcs']['year'];
  $depth = $config['page']['depth'];
  $content = <<< EOD
        <h1>U-Con {$year} Events</h1>
        <p>Event submission for {$year} is closed.</p>

        <p style="text-align: center;">
        <img src="{$depth}/images/pic2003/planes.jpg" style="border: solid 1px;" alt="" />
        </p>
EOD;

   // render the page
  $smarty->assign('config', $config);
  $smarty->assign('constants', $constants);

  $smarty->assign('content', $content);
  $smarty->display('base.tpl');
  exit;
}

//
// If not logged in, redirect to login form
if (!$auth->isLogged()) {
  redirect('../login.php');
  exit();
}


//
// set up constants for the forms
require_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'resources/member/constants.php';
$smarty->assign('constants', $constants);
$smarty->assign('form', array('submit'=>'process.php'));
$smarty->assign('slotDurations', json_encode($constants['events']['slotDurations']));


//
// create the session object if it doesn't exist yet
if (!isset($_SESSION['gm'])) {
  $_SESSION['gm'] = array();
}
$gm = $_SESSION['gm'];
//echo "<pre>".print_r($_SESSION['gm'],1)."</pre>";
//echo "<pre>".print_r($gm,1)."</pre>";

// look out for events which should be removed
if (isset($_GET['action']) && $_GET['action'] == "clear") {
  unset($_SESSION['gm']);
  redirect('submit.php');
  exit;
}

require_once INC_PATH.'resources/member/db.php';


if (!isset($gm['member']) || !is_numeric($gm['member']) || 
    ((isset($_GET['action']) && $_GET['action'] == 'editMember'))) {
  // initialize member
  $gm['member'] = array();

  // get the list of authorized users from association table
  $currUser = $auth->getCurrentUser();
  $uid = $currUser['uid'];
  $members = $associates->listAssociates($uid);

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

    $first = $members[$id]['s_fname'];
    $last = $members[$id]['s_lname'];
    $full = $first . ($first && $last ? ' ' : '') . $last;
    $members[$id]['name'] = $full;
    $members[$id]['radio'] = '<input type="radio" name="id_member" value="'.$id.'">';
  }
  $smarty->assign('events', $members);
  $smarty->assign('columns', array(
    'radio'=>'',
    'name'=>'Name',
    //'numevents'=>'Events (submitted/approved)',
  ));

  // render the page
  $content = <<< EOD
<h2>Create Event - Select Gamemaster</h2>
<p>Select the Gamemaster for the event to be submitted.</p>

<p>Due to the Children's Online Privacy Protection Act we cannot allow children under 13 to register as a Gamemaster. Please have a parent or guardian submit the event under his or her name. We simply cannot list the names of children under 13 on our website. If you have questions about this policy, please contact the events coordinator. By pushing the 'Review' button, you are signifying that the registrant named is at least 13 years of age.</p>

EOD;
//' fix vim parsing

if (empty($members)) {

$content .= <<< EOD
<p>No envelopes are found.  Please go to <b>My Registration</b> to create an envelope so one can be selected here!</p>

EOD;

} else {

$table = $smarty->fetch('gcs/common/general-table.tpl');
$content .= <<< EOD
<form method="post" action="process.php" style="width:200px" class="auth">
<input type="hidden" name="action" value="selectMember">
$table
<input type="submit" value="Select GM">
</form>

EOD;

}

//  $content .= '<div style="text-align:center;width:200px" class="auth">'.$smarty->fetch('gcs/common/general-table.tpl').'</div>';
  $smarty->assign('content', $content);
  $smarty->display('base.tpl');


} elseif (!isset($gm['events'])) {

  // initailize events
  $_SESSION['gm']['events'] = array();

  // initailize first event
  $idx = 0;
  $_SESSION['gm']['events'][$idx] = array();
  $_SESSION['gm']['events'][$idx]['idx'] = $idx;
  $event = $_SESSION['gm']['events'][$idx];

  // display event form
  $smarty->assign('event', $event);
  $smarty->assign('content', $smarty->fetch('gcs/gm/edit.tpl'));
  $smarty->display('base.tpl');

} elseif(isset($_GET['action']) && $_GET['action'] == 'add') {

  // initailize the new event
  $idx = count($gm['events']);
  //while (isset ($_SESSION['gm']['events'][$idx])) {
  //  ++$idx; // fast forward until an empty slot is identified
  //}
  $_SESSION['gm']['events'][$idx] = array();
  $_SESSION['gm']['events'][$idx]['idx'] = $idx;
  $event = $_SESSION['gm']['events'][$idx];

  if (count($gm['events']) > 1) {
    $smarty->assign('userActions', array('cancelAddEvent'=>'display.php?action=removeEvent&amp;removeIdx='.$idx));
  }

  // display the event form without validation
  $smarty->assign('event', $event);
  $smarty->assign('content', $smarty->fetch('gcs/gm/edit.tpl'));
  $smarty->display('base.tpl');


} elseif (isset($_GET['action']) && $_GET['action'] == "removeConfirm") {

    $userActions = array('removeEvent' => 'display.php?action=clear',
        'removeCancel'=>'display.php');
    $smarty->assign('userActions', $userActions);
    $smarty->assign('event', $_SESSION['gm']['events'][0]);
    $smarty->assign('content', $smarty->fetch('gcs/gm/delete-confirm.tpl'));
    $smarty->display('base.tpl');

} elseif (isset($_GET['idx']) && is_numeric($_GET['idx'])) {
  $idx = $_GET['idx'];
  $event = $_SESSION['gm']['events'][$idx];

  // display selected event form with validation
  require_once INC_PATH.'resources/event/db.php';
  $smarty->assign('errors', validateEvent($event));
  $smarty->assign('event', $event);
  $smarty->assign('content', $smarty->fetch('gcs/gm/edit.tpl'));
  $smarty->display('base.tpl');

} else {
  require_once INC_PATH.'resources/event/db.php';

  foreach ($_SESSION['gm']['events'] as $idx => $event) {
    if (count($errors = validateEvent($event)) > 0) {
      // display selected event form with validation
      $smarty->assign('errors', $errors);
      $smarty->assign('event', $event);
      $smarty->assign('content', $smarty->fetch('gcs/gm/edit.tpl'));
      $smarty->display('base.tpl');
      exit;
    }
  }

  $userActions = array('addEvent' => 'display.php?action=add',
                       'editEvent' => 'display.php?idx=', // add idx
                       'clearAll' => 'display.php?action=removeConfirm&idx=', // add idx
                       'editMember' => 'display.php?action=editMember',
                       'submitAll' => 'submitAll.php');
  $smarty->assign('userActions', $userActions);

  //$smarty->assign('member', $_SESSION['gm']['member']);
  //$smarty->assign('memberView', $smarty->fetch('gcs/member/view.tpl'));

  // display the review form with the submit button
  $smarty->assign('events', $_SESSION['gm']['events']);
  $smarty->assign('eventCount', count($_SESSION['gm']['events']));
  $smarty->assign('content', $smarty->fetch('gcs/gm/review.tpl'));
  $smarty->display('base.tpl');

}

