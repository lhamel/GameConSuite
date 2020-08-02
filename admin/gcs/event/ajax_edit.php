<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
include_once (INC_PATH.'db/db.php');
include INC_PATH.'resources/event/constants.php';

$idEvent = $_REQUEST['id_event'];
$field = $_REQUEST['field'];
//$oldValue = $_REQUEST['old'];
$newValue = isset($_REQUEST['new']) ? $_REQUEST['new'] : ''; // new value could be empty
if (is_array($newValue)) { $newValue = implode(',', $newValue); }

//echo $newValue;
//exit;

//$paramsCheck = array($oldValue,$idEvent);
$paramsUpdate = array($newValue, $idEvent);

$sqlArray = array(
  's_number'=>array(
    'check' => 'select s_number from ucon_event where s_number=? and id_event=?',
    'update' => 'update ucon_event set s_number=? where id_event=?',
  ),
  's_game'=>array(
    'check' => 'select s_game from ucon_event where s_game=? and id_event=?',
    'update' => 'update ucon_event set s_game=? where id_event=?',
  ),
  's_title'=>array(
    'check' => 'select s_title from ucon_event where s_title=? and id_event=?',
    'update' => 'update ucon_event set s_title=? where id_event=?',
  ),
  's_desc'=>array(
    'check' => 'select s_desc from ucon_event where s_desc=? and id_event=?',
    'update' => 'update ucon_event set s_desc=? where id_event=?',
  ),
  's_desc_web'=>array(
    'check' => 'select s_desc_web from ucon_event where s_desc_web=? and id_event=?',
    'update' => 'update ucon_event set s_desc_web=? where id_event=?',
  ),
  'id_event_type'=>array(
    'check' => 'select id_event_type from ucon_event where id_event_type=? and id_event=?',
    'update' => 'update ucon_event set id_event_type=? where id_event=?',
    'returnValue' => isset($constants['events']['event_types'][$newValue]) ? $constants['events']['event_types'][$newValue] : '',
  ),
//  'i_minplayers'=>array(
//    'update' => 'update ucon_event set i_minplayers=? where id_event=?',
//    //'return' => 'select s_abbr as value from ucon_event_type where id_event_type=?',
//  ),
//  'i_maxplayers'=>array(
//    'update' => 'update ucon_event set i_maxplayers=? where id_event=?',
//    //'return' => 'select s_abbr as value from ucon_event_type where id_event_type=?',
//  ),
  'i_agerestriction'=>array(
    'update' => 'update ucon_event set i_agerestriction=? where id_event=?',
    'returnValue' => isset($constants['events']['ages'][$newValue]) ? $constants['events']['ages'][$newValue] : '',
  ),
  'i_length'=>array(
    'update' => 'update ucon_event set i_length=? where id_event=?',
    //'return' => 'select s_abbr as value from ucon_event_type where id_event_type=?',
  ),
  's_comments'=>array(
    'update'=>'update ucon_event set s_comments=? where id_event=?',
  ),
  's_eventcom'=>array(
    'update'=>'update ucon_event set s_eventcom=? where id_event=?',
  ),
  's_platform'=>array(
    'check' => 'select s_platform from ucon_event where s_platform=? and id_event=?',
    'update'=> 'update ucon_event set s_platform=? where id_event=?',
  ),
);

// special cases
if ($field == "expcomp") {
  $exp = substr($newValue, 0,1);
  $comp = substr($newValue, 1,1);
  //die('die'.$newValue);
  $ok = $db->Execute("update ucon_event set e_exper=?, e_complex=? where id_event=?", array($exp,$comp,$idEvent));
  if (!$ok) {
    $msg = $db->ErrorMsg();
    echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
    exit;
  }
  echo $constants['events']['experience']['display'][$exp].'/'.$constants['events']['complexity']['display'][$comp];
  exit;
} else if ($field == "players") {
  $split = explode('-',$newValue);
  $min = trim($split[0]);
  $max = trim($split[1]);
//  die("die $min / $max");
  if (!is_numeric($min) || !is_numeric($max)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo("{ 'success':'false', 'errorMsg':'Number of Players must be numeric.' }");
    exit;
  }
  $ok = $db->Execute("update ucon_event set i_minplayers=?, i_maxplayers=? where id_event=?", array($min,$max,$idEvent));
  if (!$ok) {
    $msg = $db->ErrorMsg();
    echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
    exit;
  }
  echo $newValue;
  exit;
} else if ($field == "location") {
  $split = explode("\n",$newValue);
  $roomName = trim($split[0]);
  $s_table = trim($split[1]);
  $roomIdsByName = array_flip($constants['events']['roomsWithBlank']);
  $roomId = $roomName=="" ? null : $roomIdsByName[$roomName];
  //$id_room = trim($split[2])=="" ? null : trim($split[2]);
  // die("die $roomName / $s_table / $id_room");
  if ($roomId != null && !is_numeric($roomId)) {
    header('HTTP/1.1 500 Internal Server Error');
    echo("{ 'success':'false', 'errorMsg':'invalid room $roomId' }");
    exit;
  }
  $ok = $db->Execute("update ucon_event set id_room=?, s_table=? where id_event=?", array($roomId,$s_table,$idEvent));
  if (!$ok) {
    $msg = $db->ErrorMsg();
    echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
    exit;
  }
  echo "{$roomName}\n{$s_table}";
  exit;
} else if ($field=='dayTime') {
  $split = explode("\n",$newValue);
  $day = trim($split[0]);
  $time = trim($split[1]);
  if (!$day) $day = null;
  if (!$time) $time = null;

  if ($day != null && !isset($constants['events']['days'][$day])) {
    header('HTTP/1.1 500 Internal Server Error');
    echo("{ 'success':'false', 'errorMsg':'invalid day $newValue' }");
    exit;
  }
  if ($time != null && !isset($constants['events']['times']["$time"])) {
    header('HTTP/1.1 500 Internal Server Error');
    echo("{ 'success':'false', 'errorMsg':'invalid time $newValue' }");
    exit;
  }

  $ok = $db->Execute("update ucon_event set e_day=?, i_time=? where id_event=?", array($day, $time, $idEvent));
  if (!$ok) {
    $msg = $db->ErrorMsg();
    echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
    exit;
  }

  $length = 0;
  $returnResults = $db->GetAll('select i_length as value from ucon_event where id_event=?', array($idEvent));
  if (!is_array($returnResults) || count($returnResults)<1) {
          echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
          exit;
  } else {
    $length = $returnResults[0]['value']; // success
  }

  $endtime = isset($constants['events']['timesWithBlank'][$time+$length]) ? $constants['events']['timesWithBlank'][$time+$length] : '';
  $day = $constants['events']['daysWithBlank'][$day];
  $time = $constants['events']['timesWithBlank'][$time];
  echo "{$day} {$time}-{$endtime}";
  exit;
} else if ($field=="i_cost") {
  $cost = str_replace('$', '', $newValue);
  $cost = str_ireplace('Free', '0.00', $cost);
  if (!$cost) $cost = '0.00';
  if (!is_numeric($cost) || $cost < 0) {
    header('HTTP/1.1 500 Internal Server Error');
    echo("{ 'success':'false', 'errorMsg':'invalid cost $newValue' }");
    exit;
  }
  $ok = $db->Execute("update ucon_event set i_cost=? where id_event=?", array($cost,$idEvent));
  if (!$ok) {
    $msg = $db->ErrorMsg();
    echo("{ 'success':'false', 'errorMsg':'SQL Error on update: $msg' }");
    exit;
  }
  if ($cost == 0) {
    echo "Free!";
  } else {
    $cost = number_format($cost,2);
    echo "\$$cost";
  }
  exit;
} else if ($field=='tags') {

  // default values empty
  $tagsArr = array();
  $assocArray = array();

  // look up ID values for any tags referenced
  if ($newValue != '') {
    $tagsArr = explode(',', $newValue);
    $quotedTags = '"'.implode('","', $tagsArr).'"';
    // identify tag IDs
    // NOTE that tagEditor automatically converts tags to lower case, need to match that behavior in comparisons, tagEditor behavior can be changed with parameters
    $sql = "select id_tag, LCASE(tag) from ucon_tag where tag in ($quotedTags)";
    $assocArray = $db->GetAssoc($sql);
    if (!is_array($assocArray)) { echo "SQL Error: ".$db->ErrorMsg(); exit;}

    // if the input array size doesn't match the query result, then unknown tags were stripped
    // and the modification should be halted
    if (count($tagsArr) != count($assocArray)) {
      $foundValues = array_values($assocArray);
      // echo "<br>tagsArr: ".print_r($tagsArr,1);
      // echo "<br>foundValues: ".print_r($foundValues,1);

      $stripped = array_diff($tagsArr, $foundValues);
      $unknownTags = implode(",", $stripped);
      // echo "<br>stripped: ".print_r($stripped,1);
      // echo "<br>unknownTags: ".print_r($unknownTags,1);

      header('HTTP/1.1 400 Bad Input');
      echo("{ 'success':'false', 'errorMsg':'did not save unknown tags $unknownTags' }");
      exit;
    }
  }

  // Note that any unknown tags are stripped here.  In the past I used automatic 
  // insertion, but I think that will cause a lot of unnecessary data.

  // Automatic insertion of new tags disabled
  // $tagIds = array();
  // $tags = isset($_POST['tags']) ? $_POST['tags'] : array();
  // foreach ($tags as $key => $value) {
  //   $keyLen = strlen($key);
  //   if (substr($key, $keyLen-1, 1) == "a") {
  //     $tagIds[] = substr($key, 0, $keyLen-2); // everything but "-a"
  //   } else {
  //     $sql = "insert into ucon_tag set tag=?";
  //     $db->execute($sql, array($value));
  //     // if it fails then a uniqueness constraint was violated, so just continue
  //     $sql = "select id_tag from ucon_tag where tag=?";
  //     $insertId = $db->getOne($sql, array($value));
  //     if (!isset($insertId)) { echo "<br>SQL Error: " . $db->ErrorMsg(); exit; }
  //     //echo "<br>".print_r($insertId,1);
  //     $tagIds[] = $insertId['id_tag'];
  //   }

  // }
  //echo "<br>".print_r($tagIds,1);



  // echo "<pre>".print_r($assocArray, 1)."</pre>";

  // discover the currently associated tags
  $sql = "select group_concat(id_tag) from ucon_event_tag where id_event=?";
  $currentTags = $db->getOne($sql, array($idEvent));
  $currentTags = explode(',', $currentTags);
  // echo "<br>currentTags: ".print_r($currentTags,1);

  // discovered which tags were added and removed
  $tagIds = array_keys($assocArray);
  $insertTags = array_diff($tagIds, $currentTags);
  $removedTags = array_diff($currentTags, $tagIds);
  // echo "<br>insertTags: ".print_r($insertTags,1);
  // echo "<br>removedTags: ".print_r($removedTags,1);


  //
  // finally, associate the tags with the event (ucon_event_tag)

  // insert the new tags
  $insertQuery = $db->prepare("insert into ucon_event_tag set id_event=?, id_tag=?");
  foreach ($insertTags as $tagId) {
    $success = $db->execute($insertQuery, array($idEvent, $tagId));
    if (!$success) {
      header('HTTP/1.1 500 Internal Server Error');
      echo "Sql Error: ".$db->ErrorMsg();
      exit;
    }
  }

  // remove tags not included
  $removeQuery = $db->prepare("delete from ucon_event_tag where id_event=? and id_tag=?");
  foreach ($removedTags as $tagId) {
    $success = $db->execute($removeQuery, array($idEvent, $tagId));
    if (!$success) {
      header('HTTP/1.1 500 Internal Server Error');
      echo "Sql Error: ".$db->ErrorMsg();
      exit;
    }
  }

  // This result should match the input, as unknown tags result in an error, see above
  echo implode(",",$assocArray);
  exit;
} 

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
        // FIXME $paramsUpdate has 2 parameters but queries were designed for 1
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
  header("HTTP/1.1 400 Bad Request");
	echo("{ 'success':'false', 'errorMsg':'unknown field $field' }");
	exit;
}

