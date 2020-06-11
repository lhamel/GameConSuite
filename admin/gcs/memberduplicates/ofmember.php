<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/member/ofmember.php';
$title = 'Search Duplicates'; // override with name further down

$year = $config['gcs']['year'];
$idMember = is_numeric($_GET['id_member']) ? $_GET['id_member'] : 0;
if ($idMember <= 0) {
  redirect($config['page']['depth'].'admin/gcs/memberduplicates/index.php');
}

include INC_PATH.'db/db.php';

// dget the details for the current member
$sql = "select id_member, s_email, s_lname, s_fname from ucon_member where id_member=?";
$member = $db->getArray($sql, [$idMember]);
if (!is_array($member)) {
  error_log('SQL Error at '.__LINE__.'. '.$db->ErrorMsg());
  die('SQL Error. '.$db->ErrorMsg());
}
list($member) = $member;
//echo "<pre>".print_r($member,1)."</pre>";



$sql = <<< EOD
  select id_member, s_fname, s_lname, s_email
  from ucon_member
  where s_email = ?
    or (s_lname = ? and s_fname = ?)
  order by s_lname, s_fname, id_member
EOD;

$members = $db->getArray($sql, [ $member['s_email'], $member['s_lname'], $member['s_fname'] ]);
if (!is_array($members)) {
  $msg = 'SQL Error at '.(__LINE__-2).'. '.$db->ErrorMsg();
  error_log($msg);
  echo $sql.'<hr>'.$msg;
  exit;
}

foreach ($members as $k => $v) {
  $first = $idMember;
  $second = $v['id_member'];

  if ($first != $second) {
    $members[$k]['compare'] = '<a href="'.$config['page']['depth'].'admin/gcs/memberduplicates/compare.php?id_member='.$first.'&other='.$second.'">compare</a>';
  } else {
    $members[$k]['compare'] = '';
  }
}

$cols = [
  's_lname' => 'Last',
  's_fname' => 'First',
  's_email' => 'Email',
  'compare' => '',
];


include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('duplicates', $members);
$smarty->assign('cols', $cols);

$smarty->assign('config', $config);
//$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

// render the page
$content = $smarty->fetch('gcs/admin/member/duplicates-list.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
