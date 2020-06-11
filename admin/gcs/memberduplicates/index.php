<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/memberduplicates/index.php';
$title = 'Search Duplicates'; // override with name further down

$year = $config['gcs']['year'];
// 


include INC_PATH.'db/db.php';


// find names with duplicates
$sql = "select concat(s_fname,' ',s_lname) as value, group_concat(id_member) as id_member, count(id_member) as count from ucon_member group by s_fname, s_lname having count>1";
$nameWithDuplicates = $db->getArray($sql);
// echo "<pre>".print_r($nameWithDuplicates,1)."</pre>";


// find emails with duplicates
$sql = "select s_email as value, group_concat(id_member) as id_member, count(id_member) as count from ucon_member where (s_email!='' and not isNull(s_email)) group by s_email having count>1";
$emailsWithDuplicates = $db->getArray($sql);
if (!is_array($emailsWithDuplicates)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error. '.$db->ErrorMsg());
}
// echo "<pre>".print_r($emailsWithDuplicates,1)."</pre>";

$members = array_merge($nameWithDuplicates, $emailsWithDuplicates);

foreach ($members as $k => $v) {
  $ids = explode(',',$v['id_member']);
  $value = $members[$k]['value'];

  $first = $ids[0];
  $second = $ids[1];

  $members[$k]['ids'] = $ids;
  $members[$k]['value'] = '<a href="'.$config['page']['depth'].'admin/gcs/memberduplicates/ofmember.php?id_member='.$first.'">'.$value.'</a>';
  $members[$k]['compare'] = '<a href="'.$config['page']['depth'].'admin/gcs/memberduplicates/compare.php?id_member='.$first.'&other='.$second.'">compare</a>';
}
//echo "<pre>".print_r($members[0],1)."</pre>";


$cols = [
  'value' => 'Basis',
  'count' => 'Count',
  'compare' => '',
];


//include INC_PATH.'resources/event/constants.php';
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
