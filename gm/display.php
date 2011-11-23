<?php
require_once '../inc/inc.php';
$year= YEAR;

//
// initialize smarty
require_once INC_PATH.'smarty.php';
$location = "gm/submit.php";
require_once INC_PATH.'menu.php';
$smarty->assign('UCON', $UCON);
$smarty->assign('config', $config);

//
// set up constants for the forms
require_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'resources/member/constants.php';
$smarty->assign('constants', $constants);
$smarty->assign('form', array('submit'=>'process.php'));


session_start();

//
// create the session object if it doesn't exist yet
if (!isset($_SESSION['gm'])) {
  $_SESSION['gm'] = array();
}
$gm = $_SESSION['gm'];
//echo "<pre>".print_r($_SESSION['gm'],1)."</pre>";
//echo "<pre>".print_r($gm,1)."</pre>";

// look out for events which should be removed
if ($_GET['action'] == "removeEvent" 
    && isset($_GET['removeIdx']) 
    && isset($_SESSION['gm']['events'][$_GET['removeIdx']])) {
  $idx = $_GET['removeIdx'];
  unset($_SESSION['gm']['events'][$idx]);
  // fall through to normal behavior
}

require_once INC_PATH.'resources/member/db.php';


if (!isset($gm['member'])) {
  // initialize member
  $gm['member'] = array();

  // display member form
  $smarty->assign('member', $gm['member']);
  $smarty->assign('content', $smarty->fetch('gm/personal.tpl'));
  $smarty->display('base.tpl');

} elseif (
    (isset($_GET['action']) && $_GET['action'] == 'editMember')
    || count($errors = validateMember($gm['member']))>0) {
  // assign validation errors to smarty
  $smarty->assign('errors', $errors);

  // display member form
  $smarty->assign('member', $gm['member']);
  $smarty->assign('content', $smarty->fetch('gm/personal.tpl'));
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
  $smarty->assign('content', $smarty->fetch('event/edit.tpl'));
  $smarty->display('base.tpl');

} elseif(isset($_GET['action']) && $_GET['action'] == 'add') {

  // initailize the new event
  $idx = count($gm['events']);
  $_SESSION['gm']['events'][$idx] = array();
  $_SESSION['gm']['events'][$idx]['idx'] = $idx;
  $event = $_SESSION['gm']['events'][$idx];

  // display the event form without validation
  $smarty->assign('event', $event);
  $smarty->assign('content', $smarty->fetch('event/edit.tpl'));
  $smarty->display('base.tpl');


} elseif ($_GET['action'] == "removeConfirm" 
      && isset($_GET['idx']) 
      && isset($_SESSION['gm']['events'][$_GET['idx']])) {
    $idx = $_GET['idx'];

    $userActions = array('removeEvent' => 'display.php?action=removeEvent&removeIdx=',
        'removeCancel'=>'display.php');
    $smarty->assign('userActions', $userActions);
    $smarty->assign('event', $_SESSION['gm']['events'][$idx]);
    $smarty->assign('content', $smarty->fetch('gm/delete-confirm.tpl'));
    $smarty->display('base.tpl');

} elseif (isset($_GET['idx']) && is_numeric($_GET['idx'])) {
  $idx = $_GET['idx'];
  $event = $_SESSION['gm']['events'][$idx];

  // display selected event form with validation
  require_once INC_PATH.'resources/event/db.php';
  $smarty->assign('errors', validateEvent($event));
  $smarty->assign('event', $event);
  $smarty->assign('content', $smarty->fetch('event/edit.tpl'));
  $smarty->display('base.tpl');

} else {
  require_once INC_PATH.'resources/event/db.php';

  foreach ($_SESSION['gm']['events'] as $idx => $event) {
    if (count($errors = validateEvent($event)) > 0) {
      // display selected event form with validation
      $smarty->assign('errors', $errors);
      $smarty->assign('event', $event);
      $smarty->assign('content', $smarty->fetch('event/edit.tpl'));
      $smarty->display('base.tpl');
      exit;
    }
  }

  $userActions = array('addEvent' => 'display.php?action=add',
                       'editEvent' => 'display.php?idx=', // add idx
                       'deleteEvent' => 'display.php?action=removeConfirm&idx=', // add idx
                       'editMember' => 'display.php?action=editMember',
                       'submitAll' => 'submitAll.php');
  $smarty->assign('userActions', $userActions);

  $smarty->assign('member', $_SESSION['gm']['member']);
  $smarty->assign('memberView', $smarty->fetch('member/view.tpl'));

  // display the review form with the submit button
  $smarty->assign('events', $_SESSION['gm']['events']);
  $smarty->assign('eventCount', count($_SESSION['gm']['events']));
  $smarty->assign('content', $smarty->fetch('gm/review.tpl'));
  $smarty->display('base.tpl');

}

