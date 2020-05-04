<?php

$location = 'gcs/reg.php';
$title = 'Account Information'; // override with name further down

require_once __DIR__.'/../../../inc/inc.php';
require_once __DIR__.'/../../../inc/auth.php'; // TODO remove after movin

$year = $config['gcs']['year'];
include_once (INC_PATH.'db/db.php');
include INC_PATH.'resources/member/constants.php';


$uid = isset($_GET['uid']) ? $_GET['uid'] : '';
$id_member = $_GET['id_member'];
$action = $_GET['action'];

// if no UID is specified, try to go by email address
if (!is_numeric($uid) || $uid<0) {
  $email = $_GET['email'];
  $uid = $auth->getUID($email);
  //echo "got $uid from $email";

  if (!$uid) {
    //header('HTTP/1.1 400 Bad Request');
    echo "0";
    //echo ("could not find user $email");
    exit;
  }
}

// Parameter checks
if (!is_numeric($uid) || $uid<0) { header($_SERVER["SERVER_PROTOCOL"].' 422 bad parameter'); echo 0; exit; }
if (!is_numeric($id_member) || $id_member<=0) { header($_SERVER["SERVER_PROTOCOL"].' 422 bad parameter'); echo 0; echo "valid member required ($id_member)"; exit; }

// check to see if 
$user = $auth->getUser($uid);
//echo "<pre>".print_r($user,1)."</pre>";

$members = $associates->listAssociates($uid);
//echo "<pre>".print_r($members,1)."</pre>";

$isPresent = isset($members[$id_member]);
//echo "<p>isPresent: $isPresent</p>";

switch($action) {
  case 'add':
    if ($isPresent) { header($_SERVER["SERVER_PROTOCOL"].' 422 bad parameter'); echo 0; exit; }
    $associates->associate($id_member, $uid);
    break;

  case 'remove':
    if (!$isPresent) { header($_SERVER["SERVER_PROTOCOL"].' 422 bad parameter'); echo 0; exit; }
    $associates->disassociate($id_member, $uid);
    break;

  default:
    header($_SERVER["SERVER_PROTOCOL"].' 422 bad parameter'); 
    exit;
}

// success
echo '1';

