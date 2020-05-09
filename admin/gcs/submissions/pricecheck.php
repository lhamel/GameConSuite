<?php
include '../../../inc/inc.php';

/*
  Events listed on this page have a price which doesn't match the length'
*/

$location = 'admin/gcs/events/pricecheck.php';
$title = 'Price Check- U-Con Admin';


// unapproved events
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

$sql = <<< EOD
    SELECT E.*, CONCAT(M.s_fname, " ", M.s_lname) as gamemaster
      , E.i_time+E.i_length as endtime 
    FROM ucon_event as E, ucon_member as M 
    WHERE id_convention=? 
      AND E.id_gm=M.id_member
      AND (NOT (((1+i_length) DIV 2)*2 = i_cost or i_cost = 0))
      AND (NOT (i_length=5 and i_cost=4))
EOD;
$params = array($year);

if ($_GET['approved']) {
  $sql .= "\n    AND E.b_approval=?";
  $params[] = $_GET['approved']=='true' ? 1 : 0;
  //die ('<pre>'.print_r($params,1).'</pre>');
}
if (isset($_GET['order'])) {
  $sql .= ' ORDER BY '.$_GET['order'];
}


//die ('<pre>'.print_r($sql,1)."\n".print_r($params,1).'</pre>');
include INC_PATH.'db/db.php';
$events = $db->getArray($sql, $params);
if (!is_array($events)) die('SQL Error: '.$db->ErrorMsg());
//die ('<pre>'.print_r($events,1).'</pre>');

$actions = array(
  'detail' => '../event/index.php?id_event=',
  'list' => basename(__FILE__).'?year='.$year,
);

$filters = array(
  'approved' => array(
    'label'=>'Status',
    'options'=>array('false'=>'unapproved', 'true'=>'approved'),
  ),
);



include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('actions', $actions);
$smarty->assign('filters', $filters);
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);
$smarty->assign('events', $events);

$smarty->assign('header', "Price Check $year");
$smarty->assign('directions', 'These events do not meet the standard of $2 per 2 hours (or $4 for 5 hours).  Some of these selections may be on purpose, such as events longer than 6 hours, so be careful.');
$content = $smarty->fetch('gcs/admin/events/list.tpl');

$smarty->assign('content', $content);
$smarty->display('base.tpl');
