<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
include_once (INC_PATH.'db/db.php');
include INC_PATH.'resources/event/constants.php';

$field = $_REQUEST['field'];
//$oldValue = $_REQUEST['old'];
$newValue = $_REQUEST['new'];

list($field, $id) = explode('-', $field);

// echo "<pre>".print_r($dbField)."</pre>"; exit;

//echo $newValue;
//exit;

//$paramsCheck = array($oldValue,$idEvent);
$paramsUpdate = array($newValue, $id);

$sqlArray = array(
  'description'=>array(
    'check' => 'select description from ucon_prereg_items where description=? and id_prereg_item=?',
    'update' => 'update ucon_prereg_items set description=? where id_prereg_item=?',
  ),
  'unit_price'=>array(
    'check' => 'select unit_price from ucon_prereg_items where unit_price=? and id_prereg_item=?',
    'update' => 'update ucon_prereg_items set unit_price=? where id_prereg_item=?',
  ),
  'barcode'=>array(
    'check' => 'select barcode from ucon_prereg_items where barcode=? and id_prereg_item=?',
    'update' => 'update ucon_prereg_items set barcode=? where id_prereg_item=?',
  ),
  'itemtype'=>array(
    'check' => 'select itemtype from ucon_prereg_items where itemtype=? and id_prereg_item=?',
    'update' => 'update ucon_prereg_items set itemtype=? where id_prereg_item=?',
  ),
  'subtype'=>array(
    'check' => 'select subtype from ucon_prereg_items where subtype=? and id_prereg_item=?',
    'update' => 'update ucon_prereg_items set subtype=? where id_prereg_item=?',
  ),
  'is_public'=>array(
    'check' => 'select is_public from ucon_prereg_items where is_public=? and id_prereg_item=?',
    'update' => 'update ucon_prereg_items set is_public=? where id_prereg_item=?',
  ),
);

// special cases

if (isset($sqlArray[$field])) {
	$sqlCheck = $sqlArray[$field]['check'];
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

