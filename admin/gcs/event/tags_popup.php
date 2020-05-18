<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
$location = 'admin/gcs/event/popup_edittags.php';
include INC_PATH.'smarty.php';

$id_event = $_REQUEST['id_event'];
if (!isset($id_event)) die("error: id_event required");

include_once (INC_PATH.'db/db.php');

// if the event id was posted, save the tags
if (isset($_POST['id_event'])) {

  // save the page and execute a window.close();
  //echo print_r($_POST['tag'],1);

  $tagIds = array();
  $tags = isset($_POST['tag']) ? $_POST['tag'] : array();
  foreach ($tags as $key => $value) {
    $keyLen = strlen($key);
    if (substr($key, $keyLen-1, 1) == "a") {
      $tagIds[] = substr($key, 0, $keyLen-2); // everything but "-a"
    } else {
      $sql = "insert into ucon_tag set tag=?";
      $db->execute($sql, array($value));
      // if it fails then a uniqueness constraint was violated, so just continue
      $sql = "select id_tag from ucon_tag where tag=?";
      $insertId = $db->getOne($sql, array($value));
      if (!isset($insertId)) { echo "<br>SQL Error: " . $db->ErrorMsg(); exit; }
      //echo "<br>".print_r($insertId,1);
      $tagIds[] = $insertId['id_tag'];
    }

  }
  //echo "<br>".print_r($tagIds,1);

  // TODO finally, associate the tags with the event (ucon_event_tag)
  
  $sql = "select group_concat(id_tag) from ucon_event_tag where id_event=?";
  $currentTags = $db->getOne($sql, array($id_event));
  $currentTags = explode(',', $currentTags);
  //echo "<br>currentTags: ".print_r($currentTags,1);

  $insertTags = array_diff($tagIds, $currentTags);
  $removedTags = array_diff($currentTags, $tagIds);
  //echo "<br>insertTags: ".print_r($insertTags,1);
  //echo "<br>removedTags: ".print_r($removedTags,1);

  // insert the new tags
  $insertQuery = $db->prepare("insert into ucon_event_tag set id_event=?, id_tag=?");
  foreach ($insertTags as $tagId) {
    $success = $db->execute($insertQuery, array($id_event, $tagId));
    if (!$success) { echo "Sql Error: ".$db->ErrorMsg(); exit; }
  }

  // remove tags not included
  $removeQuery = $db->prepare("delete from ucon_event_tag where id_event=? and id_tag=?");
  foreach ($removedTags as $tagId) {
    $success = $db->execute($removeQuery, array($id_event, $tagId));
    if (!$success) { echo "Sql Error: ".$db->ErrorMsg(); exit; }
  }

  // close the popup window
  $tags = isset($_POST['tag']) ? $_POST['tag'] : array();
  $tagNames = str_replace("'", "/'", implode(', ', $tags));
  $content = <<< EOD
<script type="text/javascript">
  window.opener.$('#tags').text('$tagNames');
  window.close();
</script>
EOD;
  $smarty->assign('content', $content);
  $smarty->display('base_reduced.tpl');
  exit();
}

// determine which tags are present
$sql = "select ET.id_tag, T.tag from ucon_event_tag as ET, ucon_tag as T "
      ."where ET.id_tag=T.id_tag and ET.id_event=?";
$tags = $db->getArray($sql, array($id_event));
if (!is_array($tags)) { echo 'Sql Error: ' . $db->ErrorMsg(); exit; }
//echo "<br>tags: ".print_r($tags,1); exit;

$smarty->assign('id_event', $id_event);
$smarty->assign('tags', $tags);
$smarty->assign('config', $config);
$content = $smarty->fetch('gcs/admin/events/edit-tags.tpl');
$smarty->assign('content', $content);
$smarty->display('base_reduced.tpl');

