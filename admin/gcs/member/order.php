<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/member/index.php';
$title = 'Order'; // override with name further down

$year = @is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$id_member = is_numeric($_REQUEST['id_member']) ? $_REQUEST['id_member'] : 0;
if ($id_member <= 0) {
  redirect($config['page']['depth'].'admin/gcs/membersearch/index.php');
}

include INC_PATH.'db/db.php';
require_once INC_PATH.'resources/cart/CartReader.php';
require_once INC_PATH.'resources/cart/CartSerializer.php';

if (isset($_REQUEST['action'])) {
  $action = $_REQUEST['action'];
  $cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
  $reader = new CartReader($cart);
  $reader->updateData();

  require __DIR__.'/_order.inc.php';
}

$sqlMember = 'select * from ucon_member where id_member=?';
$members = $db->getArray($sqlMember, array($id_member));
if (!is_array($members)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error.  Please report via contact form.');
} else if (count($members) != 1) {
  error_log("Attempted to access member id which doesn't exist: $id_member");
  die('No such member '.$id_member);
} else {
  $member = $members[0];
}


$cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
//  echo "<pre>".print_r($cart,1)."</pre>";
$reader = new CartReader($cart);
$reader->updateData();


// create the complete list of event identifiers
$idEvents = array();
$badgeTypes = array();
$badgeLabels = array();
foreach ($cart['items'] as $item) {
  if ($item['type']=='Ticket') {
    for ($i=0; $i<$item['quantity']; ++$i) {
      $idEvents[] = $item['event']['id_event'];
    }
  } else if ($item['subtype'] == 'Generic Ticket') {
    for ($i=0; $i<$item['quantity']; ++$i) {
      $idEvents[] = 'generic';
    }
  } else if ($item['type']=='Badge') {
    for ($i=0; $i<$item['quantity']; ++$i) {
      $badgeTypes[]=$item['subtype'];
      $badgeLabels[]=$item['special'];
    }
  }
}

include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';

include '_tabs.php';

$smarty->assign('allTicketIds', implode(',',$idEvents));
$smarty->assign('allBadgeTypes', implode(',',$badgeTypes));
$smarty->assign('allBadgeLabels', implode(',',$badgeLabels));
$smarty->assign('member', $member);
$smarty->assign('cart', $cart);

if (isset($error) && $error) {
  $smarty->assign('error', $error);
}
if (isset($success) && $success) {
  $smarty->assign('success', $success);
}

$actions = array('ticketdetail'=>'../event/index.php',
                 'updateQuantity'=>'order.php',
                 'ajax'=>'_order_ajax.php?action=edit',
                 'addPayment'=>'order.php',
                 'alterPrice'=>'_order_price.php',
                 'viewEvent'=>'../event/index.php?id_event=');
$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

// render the page
$content = $smarty->fetch('gcs/admin/member/order.tpl');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

