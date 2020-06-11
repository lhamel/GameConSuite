<?php
include '../../../inc/inc.php';
$title = 'U-Con - List of Registered Gamemasters Without Badges';
$location = 'admin/gcs/badges/gamemasters.php';

/*
  The purpose of gamemasters.php is to provide a listing of gamemasters who
  do not have a GM badge assigned to them.  Even if they are not paid for,
  they must be printed.
*/

$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

include INC_PATH.'db/db.php';

// TODO compute GM list once and pass with query string
$sql = <<< EOD
  select M.* #, if(isNull(O.id_convention), 'n/a', max(O.id_convention)) as lastAttended
    , group_concat(DISTINCT OC.s_subtype) as badgetypes, if(OC.id_member,count(*),0) as badgecount,
    gmhrs
  from ucon_member as M
    left join ucon_order as OC on (OC.id_member=M.id_member and OC.id_convention=? and OC.s_type='Badge')
    , (select id_gm, sum(i_length) as gmhrs from ucon_event where id_convention=? group by id_gm) as EL
  where M.id_member in (select id_gm from ucon_event where id_convention=?)
    and EL.id_gm=M.id_member
  group by M.id_member

EOD;
if (isset($_GET['order']))
  $sql .= ' order by '.$_GET['order'];
else
  $sql .= ' order by s_lname, s_fname, M.id_member';

$members = $db->getArray($sql, array($year,$year,$year));
if (!isset($members)) die('SQL Error: '.$db->ErrorMsg());


$actions = array(
  'detail' => '../member/index.php?id_member=',
  'list' => basename(__FILE__).'?year='.$year,
);

include INC_PATH.'resources/member/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';


$filters = array(
  'year' => array(
    'label'=>'Year',
    'options'=>array_reverse(array_combine(range(2002,$config['gcs']['year']),range(2002,$config['gcs']['year'])), true),
    'noall'=>true
  ),
);
$smarty->assign('filters', $filters);
$smarty->assign('REQUEST', $_REQUEST);

$additionalFields = array('badgecount'=>'Badge Count', 'badgetypes'=>'Badge Types', 'gmhrs'=>'GM Hours');
$smarty->assign('additional', $additionalFields);

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);
$smarty->assign('members', $members);

$smarty->assign('header', "Gamemasters $year");
$smarty->assign('directions', "These gamemasters have events listed under their names.");

// render the page
$content = $smarty->fetch('gcs/admin/member/list.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');


