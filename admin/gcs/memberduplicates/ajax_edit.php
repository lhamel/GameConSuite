<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
include_once (INC_PATH.'db/db.php');
include INC_PATH.'resources/member/constants.php';

$fieldInput = $_REQUEST['field'];
//$oldValue = $_REQUEST['old'];
$newValue = $_REQUEST['new'];

list($field, $idMember) = explode('-', $fieldInput);

//echo $newValue;
//exit;

//$paramsCheck = array($oldValue,$idMember);
$paramsUpdate = array($newValue, $idMember);

$sqlArray = array(
  's_fname' => array(
    'update' => 'update ucon_member set s_fname=? where id_member=?',
  ),
  's_lname' => array(
    'update' => 'update ucon_member set s_lname=? where id_member=?',
  ),
  's_email' => array(
    'update' => 'update ucon_member set s_email=? where id_member=?',
  ),
  's_phone' => array(
    'update' => 'update ucon_member set s_phone=? where id_member=?',
  ),
  's_international' => array(
    'update' => 'update ucon_member set s_international=? where id_member=?',
  ),
  's_addr1' => array(
    'update' => 'update ucon_member set s_addr1=? where id_member=?',
  ),
  's_addr2' => array(
    'update' => 'update ucon_member set s_addr2=? where id_member=?',
  ),
	's_city' => array(
    'update' => 'update ucon_member set s_city=? where id_member=?',
  ),
  's_state' => array(
    'update' => 'update ucon_member set s_state=? where id_member=?',
  ),
  's_zip' => array(
    'update' => 'update ucon_member set s_zip=? where id_member=?',
  ),
);


if (isset($sqlArray[$field])) {
	//$sqlCheck = $sqlArray[$field]['check'];
  //$oldResults = $db->GetAll($sqlCheck, $paramsCheck);
	//if (!is_array($oldResults)) {
	//  echo("{ 'success':'false', 'errorMsg':'SQL Error on check: $msg' }");
	//  exit;
	//}
	//if (count($oldResults)>0) {
    $sqlUpdate = $sqlArray[$field]['update'];
  	$ok = $db->Execute($sqlUpdate, $paramsUpdate);
  	if (!$ok) {
  		$msg = $db->ErrorMsg();
      echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
      exit;
  	} else {
  	  if (isset($sqlArray[$field]['returnValue'])) {
  	    echo $sqlArray[$field]['returnValue'];
  	    exit;
      } else if (isset($sqlArray[$field]['return'])) {
        $sqlReturn = $sqlArray[$field]['return'];
        $returnResults = $db->GetAll($sqlReturn, $paramsUpdate);
        if (!is_array($returnResults) || count($returnResults)<1) {
          echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
          exit;
        } else {
          echo $returnResults[0]['value']; // success
          exit;
        }
      } else {
        echo $newValue; // success
        exit;
      }
  	}
  	//}

} else { // unknown field
	echo("{ 'success':'false', 'errorMsg':'unknown field $field' }");
	exit;
}
