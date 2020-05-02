<?php
require_once '../../inc/inc.php';
include_once INC_PATH.'auth.php';

$year = $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
$location = 'gcs/reg/additional.php';
require_once INC_PATH.'layout/menu.php';

// if you cannot view events or you cannot buy events...
if (!$config['allow']['view_events'] || !$config['allow']['buy_events']) {
    $content = '';
    $message = $config['allow']['message'];
    if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";
    $content .= "<h1>Register for U-Con!</h1>\n";
    if ($config['allow']['view_events'] && $config['allow']['see_location']) {
        $dates = $config['gcs']['dates']['all'];
        $content .= "<p>Pre-registration for {$year} is closed.  You may register onsite $dates.  See you soon!</p>";
    } else {
        $content .= "<p>Pre-registration for {$year} is not yet available.  We will announce on \n"
                 ."the email list when pre-registration is open!</p>\n";
    }
    $depth = $config['page']['depth'];
    $content .= <<< EOD
        <p style="text-align: center;">
        <img src="{$depth}/images/pic2003/crazylarpers.jpg" style="border: solid 1px;" alt="" />
        </p>
EOD;
    // render the page
    include '../events/_tabs.php';
    $smarty->assign('config', $config);
    //$smarty->assign('constants', $constants);
    $smarty->assign('content', $content);
    $smarty->display('base.tpl');
    exit;
}

// must be logged in to view
if (!$auth->isLogged()) {
  header('HTTP/1.0 403 Forbidden');
  redirect('../login.php');
  exit();
}


//require_once INC_PATH.'resources/event/constants.php';
//require_once INC_PATH.'resources/cart/constants.php';

require_once INC_PATH.'db/db.php';

$actions = array('addItem'=>'_add.php?&action=addItem',
                 'useItemDlg'=>1);

//if ($_REQUEST['action']=='addItem') {
//  include_once dirname(__FILE__).'/_process.php';
//  redirect($config['page']['depth']."gcs/reg/cart.php");
//  exit;
//}

$sql = "select * from ucon_prereg_items where (NOT (itemtype='Badge')) and is_public=1 order by display_order";
include_once (INC_PATH.'db/db.php');
$list = $db->getAll($sql, array());
if (!is_array($list)) {
  echo "Sql Error: ".$db->ErrorMsg(); exit;
}

if ($auth->isLogged()) {
  // get envelopes for the current logged-in user
  $members = $associates->listAssociates();
} else {
  $members = array();
}
foreach ($members as $id => $v) {
  $first = $members[$id]['s_fname'];
  $last = $members[$id]['s_lname'];
  $full = $first . ($first && $last ? ' ' : '') . $last;
  $members[$id]['name'] = $full;
  $onclickaction = "$('#selectMemberOkBtn').button('enable');";
  $members[$id]['radio'] = '<input type="checkbox" name="id_member[]" onclick="'.$onclickaction.'" value="'.$id.'">';
}

$smarty->assign('members', $members);
//echo "<pre>".print_r($members,1)."</pre>";

$smarty->assign('items', $list);
$smarty->assign('actions', $actions);
if (isset($_REQUEST['error'])) {
  $smarty->assign('error', $_REQUEST['error']);
}

$smarty->assign('config', $config);
//$smarty->assign('constants', $constants);
$content = <<< EOD
<h1>Select Additional Items</h1>
<p>Select an item to add to your order. You can add additional items by returning to 
this page. Once these items are in your order, you can modify the quanity for each.</p>
EOD;
$content .= $smarty->fetch('gcs/reg/additional.tpl');

include '../events/_tabs.php';

// render the page
$smarty->assign('title', 'Register - U-Con Gaming Convention, Ann Arbor Michigan');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

