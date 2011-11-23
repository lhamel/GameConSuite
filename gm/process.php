<?php

// TODO temporary
if (!isset($_POST['action'])) {
  die('are you hacking?');
}

session_start();

if (!isset($_SESSION['gm'])) {
  // TODO maybe warn about cookies?
  header('location: display.php');
}

// check if this is the member form
if ($_POST['action'] == 'editMember') {
  $member = $_SESSION['gm']['member'];

  $member['s_fname'] = trim($_POST['s_fname']);
  $member['s_lname'] = trim($_POST['s_lname']);
  $member['s_email'] = trim($_POST['s_email']);
  $member['s_phone'] = trim($_POST['s_phone']);
  $member['s_addr1'] = trim($_POST['s_addr1']);
  $member['s_addr2'] = trim($_POST['s_addr2']);
  $member['s_city'] = trim($_POST['s_city']);
  $member['s_state'] = trim($_POST['s_state']);
  $member['s_zip'] = trim($_POST['s_zip']);
  $member['s_international'] = trim($_POST['s_international']);
  $member['b_volunteer'] = trim($_POST['b_volunteer']);
  $member['b_email'] = trim($_POST['b_email']);

  // save values in the session
  $_SESSION['gm']['member'] = $member;

} elseif ($_POST['action'] == 'editEvent') {
  $idx = $_POST['idx'];
  if (!is_numeric($idx)
      //|| $idx >= count($_SESSION['gm']['events'])
      || !isset($_SESSION['gm']['events'][$idx])) {
    die('are you hacking?');
  }

  $event = $_SESSION['gm']['events'][$idx];

  $event['id_event_type'] = $_POST['id_event_type'];
  $event['s_game'] = $_POST['s_game'];
  $event['s_title'] = $_POST['s_title'];
  $event['s_desc'] = $_POST['s_desc'];

  $event['s_comments'] = $_POST['s_comments'];
  $event['i_maxplayers'] = $_POST['i_maxplayers'];
  $event['i_minplayers'] = $_POST['i_minplayers'];
  $event['e_exper'] = $_POST['e_exper'];
  $event['e_complex'] = $_POST['e_complex'];
  $event['i_agerestriction'] = $_POST['i_agerestriction'];

  $event['i_length'] = $_POST['i_length'];
  $event['i_c1'] = $_POST['i_c1'];
  $event['i_c2'] = $_POST['i_c2'];
  $event['i_c3'] = $_POST['i_c3'];
  $event['s_setup'] = $_POST['s_setup'];
  $event['s_table_type'] = $_POST['s_table_type'];
  $event['s_eventcom'] = $_POST['s_eventcom'];

  // save values in the session
  $_SESSION['gm']['events'][$idx] = $event;
}

header('location: display.php');
