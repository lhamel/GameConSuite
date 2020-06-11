<?php

if ($_GET['action'] == 'moveEvent') {

  if (!is_numeric($_GET['id_event'])) die('bad input: id_event');
  if (!is_numeric($_GET['id_gm'])) die('bad input: id_gm');
  
	$id_event = $_GET['id_event'];
	$id_gm = $_GET['id_gm'];

	$sql = <<< EOD
    update ucon_event set
      id_gm=?, d_updated=d_updated
    where
      id_event=?
EOD;
  $ok = $db->Execute($sql, array($id_gm, $id_event));
  if (!ok) {
  	$error = $db->ErrorMsg();
  } else {
    redirect('compare.php?id_member='.$idMember.'&other='.$idMember2.'&year='.$year);
  }

} else if ($_GET['action'] == 'moveItem') {

  if (!is_numeric($_GET['target'])) die('bad input: target');
  if (!is_numeric($_GET['id_order'])) die('bad input: id_order');

  $id_member = $_GET['target'];
  $id_order = $_GET['id_order'];

  $sql = <<< EOD
    update ucon_order set
      id_member=?, d_transaction=d_transaction
    where
      id_order=?
EOD;
  $ok = $db->Execute($sql, array($id_member, $id_order));
  if (!$ok) {
    $error = $db->ErrorMsg();
  } else {
  	redirect('compare.php?id_member='.$idMember.'&other='.$idMember2.'&year='.$year);
  }

} else if ($_GET['action'] == 'moveOrderYear') {

  if (!is_numeric($_GET['source'])) die('bad input: source');
  if (!is_numeric($_GET['target'])) die('bad input: target');
  if (!is_numeric($_GET['year'])) die('bad input: year');

  $source = $_GET['source'];
  $target = $_GET['target'];
  $year = $_GET['year'];

  $sql = <<< EOD
    update ucon_order set
      id_member=?, d_transaction=d_transaction
    where
      id_member=? and id_convention=?
EOD;
  $ok = $db->Execute($sql, array($target, $source, $year));
  if (!$ok) {
    $error = $db->ErrorMsg();
  } else {
    redirect('compare.php?id_member='.$idMember.'&other='.$idMember2.'&year='.$year);
  }

} else if ($_GET['action'] == 'moveEventYear') {

  if (!is_numeric($_GET['source'])) die('bad input: source');
  if (!is_numeric($_GET['target'])) die('bad input: target');
  if (!is_numeric($_GET['year'])) die('bad input: year');

  $source = $_GET['source'];
  $target = $_GET['target'];
  $year = $_GET['year'];

  $sql = <<< EOD
    update ucon_event set
      id_gm=?, d_updated=d_updated
    where
      id_gm=? and id_convention=?
EOD;
  $ok = $db->Execute($sql, array($target, $source, $year));
  if (!$ok) {
    $error = $db->ErrorMsg();
  } else {
    redirect('compare.php?id_member='.$idMember.'&other='.$idMember2.'&year='.$year);
  }

} else if ($_GET['action']=='deleteMember') {
  // this is the action to delete a member row which has been merged
  if (!is_numeric($_GET['id_member'])) die('bad input: id_member');
  if (!is_numeric($_GET['other'])) die('bad input: other');
  $id_member = $_GET['id_member'];
  $other = $_GET['other'];

  // ensure this member has no events
  $sql = 'select count(*) as count from ucon_event where id_gm=?';
  $count = $db->getOne($sql, array($id_member));
  if ($count === false) { echo 'Sql Error: '.$db->ErrorMsg(); }
  if ($count > 0) {
    $error = 'Cannot delete due to GM events';
  } else {

    // ensure this member has no orders
    $sql = 'select count(*) as count from ucon_order where id_member=?';
    $count = $db->getOne($sql, array($id_member));
    if ($count === false) { echo 'Sql Error: '.$db->ErrorMsg(); }
    if ($count > 0) {
      $error = 'Cannot delete due to orders';
    } else {
      
      // delete the member
      $sql = 'delete from ucon_member where id_member=?';
      $ok = $db->Execute($sql, array($id_member));
      if (!$ok) { 
        $error = 'Sql Error: '.$db->ErrorMsg();
      } else {
        // redirect to the other
        redirect("ofmember.php?id_member=".$other);
        exit;
      }
    }
  
  }
  
}
