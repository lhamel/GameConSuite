<?php
require_once '../../inc/inc.php';
/**
 * This file contains the control flow checks for registration
 */

session_start();

include_once INC_PATH.'resources/member/db.php';

// the user has not yet started editing...
if (!isset($_SESSION['reg']['member'])) {
	$member = array();
  $_SESSION['reg']['member'] = $member;

  // not really an error, but we have to make sure user enters information
  // TODO need a better way to represent this.
  $error = 'Please complete required fields';
}

$errors = validateMember($_SESSION['reg']['member']);
