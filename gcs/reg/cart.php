<?php
require_once '../../inc/inc.php';
$location = $config['page']['location'];
$year = YEAR;



// This guard is provided for when preregistration is closed.
// if you cannot view events or you cannot buy events...
if (!$config['allow']['view_events'] || !$config['allow']['buy_events']) {
        $content = "<h1>Register for {$config['gcs']['name']}!</h1>\n";
    if (!$config['allow']['buy_events']) {
                        $content .= "<p>Pre-registration for {$year} is closed.  See you soon!</p>";
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
    $smarty->assign('constants', $constants);
    $smarty->assign('content', $content);
    $smarty->display('base.tpl');
    exit;
}


if (!$config['allow']['buy_events']) {
  require_once INC_PATH.'smarty.php';
  require_once INC_PATH.'layout/menu.php';
  include '../events/_tabs.php';
  include '../events/_closed.php';
  exit;
}

//session_unset();
include_once dirname(__FILE__).'/_process.php';

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

// if an error was detected on the Process page, then show it
if (isset($error)) {
  $smarty->assign('error', $error);
}

// only allow user to continue if there are items to purchase
$actions = array('updateQuantity' => 'cart.php');
if ($reader->getTotalQuantity()>0) {
  $actions['continue'] = 'personal.php';
}


$smarty->assign('cart', $_SESSION['reg']['cart']);
$smarty->assign('config', $config);
//$smarty->assign('constants', $constants);
$smarty->assign('actions', $actions);
$content = $smarty->fetch('gcs/reg/cart.tpl');

include '../events/_tabs.php';

// render the page
$smarty->assign('content', $content);
$smarty->display('base.tpl');
