<?php
require_once dirname(__FILE__) . '/constants.php';

// only process if a post is detected
if (!isset ($_POST))
	die('no data provided');

function hasValue($field) {
	return isset ($field) && $field != "";
}

function isPresent($value, $list) {
	foreach ($list as $item) {
		if ($item == $value) {
			return true;
		}
	}
	return false;
}

if (!hasValue($_POST['s_lname'])) {
	$validation['s_lname'] = "Last name required";
}

if (!hasValue($_POST['s_fname'])) {
	$validation['s_fname'] = "First name required";
}

if (!hasValue($_POST['s_email'])) {
	$validation['s_email'] = "Please specify an email address";
}

if (!hasValue($_POST['s_international'])) {
	if (!hasValue($_POST['s_addr1'])) {
		$validation['s_addr1'] = "Please specify your street address.";
	}

	if (!hasValue($_POST['s_city'])) {
		$validation['s_city'] = "Please specify your street address.";
	}

	if (!hasValue($_POST['s_state']) || !isset ($constants['members']['states'][$_POST['s_state']])) {
		$validation['s_state'] = "Please specify your state.";
	}

	if (!hasValue($_POST['s_zip'])) {
		$validation['s_zip'] = "Please specify your zip code.";
	}

}

// TODO validate format of email, phone number, zip or zip+4