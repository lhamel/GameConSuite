<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/memberduplicates/compare.php';
$title = 'Search Duplicates'; // override with name further down

include INC_PATH.'auth.php';


$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$idMember = is_numeric($_GET['id_member']) ? $_GET['id_member'] : 0;
if ($idMember <= 0) {
  redirect($config['page']['depth'].'admin/gcs/memberduplicates/index.php');
}

$idMember2 = isset($_GET['other']) && is_numeric($_GET['other']) ? $_GET['other'] : 0;
if ($idMember <= 0) {
  redirect($config['page']['depth'].'admin/gcs/memberduplicates/index.php');
}

include INC_PATH.'db/db.php';

if (isset($_GET['action'])) {
  include '_merge.php'; //dirname(__FILE__).
}


$member1s = $db->GetAll($queries['GET_MEMBER'], [$idMember] );
if (!is_array($member1s) || count($member1s) != 1) die ("SQL Error: ".$db->ErrorMsg());
$member1 = $member1s[0];

$member2s = $db->GetAll($queries['GET_MEMBER'], [$idMember2] );
if (!is_array($member2s) || count($member1s) != 1) die ("SQL Error: ".$db->ErrorMsg());
$member2 = $member2s[0];

$gm1s = $db->GetAll($queries['GET_GM_EVENTS'], [$idMember, $year] );
if (!is_array($gm1s)) die ("SQL Error: ".$db->ErrorMsg());
$member1['gm'] = $gm1s;

$gm2s = $db->GetAll($queries['GET_GM_EVENTS'], [$idMember2, $year] );
if (!is_array($gm2s)) die ("SQL Error: ".$db->ErrorMsg());
$member2['gm'] = $gm2s;

require_once INC_PATH.'resources/cart/CartReader.php';
require_once INC_PATH.'resources/cart/CartSerializer.php';
$member1['cart'] = CartSerializer::loadFromDatabase($db, $idMember, $year);
$member2['cart'] = CartSerializer::loadFromDatabase($db, $idMember2, $year);

// default empty structures
$member1['gmYears'] = array();
$member2['gmYears'] = array();
$member1['orderYears'] = array();
$member2['orderYears'] = array();


$member1['auths'] = $associates->listAuthorizations($idMember);
$member2['auths'] = $associates->listAuthorizations($idMember2);
//echo "<pre>".print_r($authorizations,1)."</pre>";



$orderYears = $db->Prepare('select distinct id_convention as year from ucon_order where id_member=?');
$years = $db->GetAll($orderYears, array($idMember));
$years2 = $db->GetAll($orderYears, array($idMember2));
foreach($years as $y) {
  $member1['orderYears'][] = $y['year'];
}
foreach($years2 as $y) {
  $member2['orderYears'][] = $y['year'];
}
sort($member1['orderYears']);
sort($member2['orderYears']);

$orderYears = $db->Prepare('select distinct id_convention as year from ucon_event where id_gm=?');
$years = $db->GetAll($orderYears, array($idMember));
$years2 = $db->GetAll($orderYears, array($idMember2));
foreach($years as $y) {
  $member1['gmYears'][] = $y['year'];
}
foreach($years2 as $y) {
  $member2['gmYears'][] = $y['year'];
}
sort($member1['gmYears']);
sort($member2['gmYears']);

$allYears = array_unique(array_merge(
    $member1['gmYears'], 
    $member2['gmYears'], 
    $member1['orderYears'], 
    $member2['orderYears']
));
rsort($allYears);

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('member1', $member1);
$smarty->assign('member2', $member2);

$base = 'compare.php?id_member='.$idMember.'&other='.$idMember2.'&year='.$year;
$actions = array(
  'viewMember'=>'admin/gcs/member/index.php?id_member=',
  'compare'=>'admin/gcs/memberduplicates/compare.php',
  'base'=>$base,
  'moveEvent'=>'compare.php?action=moveEvent&id_member='.$idMember.'&other='.$idMember2.'&year='.$year,
  'moveOrderYear'=>'compare.php?action=moveOrderYear&id_member='.$idMember.'&other='.$idMember2.'&year='.$year,
  'moveEventYear'=>'compare.php?action=moveEventYear&id_member='.$idMember.'&other='.$idMember2.'&year='.$year,
  'deleteMember'=>'deleteMember', // id_member must be specified in the template
  'custom'=>array(
    'css'=>'actionCol',
    'title'=>'',
    'text'=>'Move',
    'url'=>'compare.php?action=moveItem&id_member='.$idMember.'&other='.$idMember2.'&year='.$year,
  ),
);

$smarty->assign('years', $allYears);
$smarty->assign('year', $year);

if (isset($error)) $smarty->assign('error', $error);
$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

// render the page
$content = $smarty->fetch('gcs/admin/member/duplicates-compare.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');
