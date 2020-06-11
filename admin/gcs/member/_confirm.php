<?php
require_once '../../../inc/inc.php';
require_once INC_PATH.'resources/event/constants.php';
$year = $config['gcs']['year'];
$location = 'admin/gcs/member/confirm.php';
require_once INC_PATH.'smarty.php';
$id_member = $_GET['id_member'];

include INC_PATH.'db/db.php';
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


$actions = array();
$smarty->assign('actions', $actions);
$smarty->assign('constants', $constants);
$smarty->assign('config', $config);
$smarty->assign('member', $member);

function sortByTime($e1, $e2) {
  if (!isset($e1['event']) || !isset($e2['event'])) {
    return 0;
  }
  $i1 = $e1['event'];
  $i2 = $e2['event'];
  if (!is_array($i1)) return 1;
  if (!is_array($i2)) return -1;
  if ($i1['e_day'] != $i2['e_day']) {
    return $i1['e_day'] > $i2['e_day'];
  }
  if ($i1['i_time'] != $i2['i_time']) {
    return $i1['i_time'] > $i2['i_time'];
  }
  return 0;
}


function getReg() {
  global $db, $id_member, $year, $smarty;
//   $sqlOrder = 'select * from ucon_order where id_convention=? and id_member=?';
//   $items = $db->getAll($sqlOrder, array($year, $id_member));
//   $smarty->assign('items', $items);
  require_once INC_PATH.'resources/cart/CartReader.php';
  require_once INC_PATH.'resources/cart/CartSerializer.php';
  $cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
  uasort($cart['items'], 'sortByTime');
  $smarty->assign('cart', $cart);
  return $smarty->fetch('gcs/admin/member/confirm/purchases.tpl');
}

function getPersonal() {
  global $smarty;
  return $smarty->fetch('gcs/admin/member/confirm/personal.tpl');
}

function getGM() {
  global $db, $id_member, $year, $smarty;
  $sqlGm = <<< EOD
    select E.*,
      E.i_time + E.i_length as endtime,
      CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
      if(isNull(O.id_order), 0, sum(O.i_quantity)) as prereg
    from ucon_member as M, ucon_event as E left join ucon_order as O on (O.s_subtype=E.id_event)
    where E.id_gm=?
      and E.id_gm=M.id_member
      and E.id_convention=?
    group by E.id_event, M.id_member
    order by e_day, i_time
EOD;
  $events = $db->getArray($sqlGm, array($id_member, $year));
  if ($events===false) { echo 'Sql Error: '.$db->ErrorMsg(); exit; }
  if($events) { /* if there are any events */
    $smarty->assign('events', $events);
    return $smarty->fetch('gcs/admin/member/confirm/gms.tpl');
  } else {
    return '';
  }
}

function getOwe() {
  global $db, $id_member, $year, $smarty;
  require_once INC_PATH.'resources/cart/CartReader.php';
  require_once INC_PATH.'resources/cart/CartSerializer.php';
  $cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
  $reader = new CartReader($cart);
  $total = $reader->getTotal();
  $paid = $reader->getPaid();
  $smarty->assign('total', $total);
  $smarty->assign('paid', $paid);
  $smarty->assign('owes', ($total-$paid));
  return $smarty->fetch('gcs/admin/member/confirm/owes.tpl');
}

function getOweData() {
  global $db, $id_member, $year, $smarty;
  require_once INC_PATH.'resources/cart/CartReader.php';
  require_once INC_PATH.'resources/cart/CartSerializer.php';
  $cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
  $reader = new CartReader($cart);
  $total = $reader->getTotal();
  $paid = $reader->getPaid();
  return array('total'=>$total, 'paid'=>$paid, 'owes'=>$total-$paid);
}

function getSig() {
  $result = <<< EOD
Thank you for your support of U-Con!
U-Con Staff
https://www.ucon-gaming.org/

EOD;
  return $result;
}

function getOrderSummary() {
  $oweData = getOweData();
  $total = $oweData['total'];
  $paid = $oweData['paid'];
  $owes = $oweData['owes'];
  if ($paid>0) {
      $paidPos = number_format($paid,2);
      $payments = "We have received payments totaling \$$paidPos.";
  } elseif ($paid<0) {
      $payments = "We have sent a refund totaling \$$paid.";
  } else {
      $payments = "We have not received any payments.";
  }

  global $config;
  $regEmail = $config['email']['registration'];

  if ($owes>0) {
    $shortOwes = "\$$owes is still due.";
    if (isset($config['email']['paypal'])) {
      $paypalAddr = $config['email']['paypal'];
      $shortOwes .= <<< EOD

Please submit payment of \$$owes via paypal to {$paypalAddr}.
If this is not possible, please contact {$regEmail}
to make other arrangements.
EOD;
    }
  } elseif ($owes==0) {
    $shortOwes = "";
  }

  $msg = <<< EOD
$payments $shortOwes

Please review your order for correctness.  Report any problems to
$regEmail


EOD;
  return $msg;
}


$type = $_REQUEST['type'];
switch ($type) {
  case 'sig':
    $result = getSig();
break;
case 'paid':
  $result = getOrderSummary();
  $result .= getPersonal();
  $result .= getGM();
  $result .= getReg();
  $result .= getOwe();
  $result .= getSig();
  break;
case 'sched':
case 'reqt':
  $result = $_REQUEST['type'];
  break;
case 'reg':
  $result = getReg();
  break;
case 'per':
  $result = getPersonal();
  break;
case 'gms':
  $result = getGM();
  break;
case 'owe':
  $result = getOwe();
  break;
default:
  $result = $_REQUEST['type'];
}

echo $result;
