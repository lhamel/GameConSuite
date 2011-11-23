<?php
require_once dirname(__FILE__).'/../../db/db.php';

// initialize constants
if (!isset($constants)) $constants = array('members'=>array());

if (!isset($_SESSION['cache'])) {
  $_SESSION['cache'] = array('constants'=>array());
}
if (!isset($_SESSION['cache']['constants'])) {
  $_SESSION['cache']['constants'] = array();
}


if (isset($_SESSION['cache']['constants']['members'])) {
	$constants['members'] = $_SESSION['cache']['constants']['members'];
} else {
  // initialize constants
  if (!isset($constants)) $constants = array();
	
  $constants['members'] = array();

	$constants['members']['states'] = array();
	$rs = $db->Execute("select s_ab, s_state from ucon_state order by s_state");
	foreach($rs as $k=>$record) {
		$constants['members']['states'][$record['s_ab']] = ucwords(strtolower($record['s_state']));
	}
	$constants['members']['statesPlusBlank'] = array(''=>'') + $constants['members']['states'];

	// keep the constants for next time
	$_SESSION['cache']['constants']['members'] = $constants['members'];
}
