<?php
include_once '../../inc/inc.php';
include_once INC_PATH.'db/db.php';
$year = $config['ucon']['year'];

// if you cannot view events or you cannot buy events...
if (!$config['allow']['view_events'] || !$config['allow']['buy_events']) {
        $content = "<h1>Register for U-Con!</h1>\n";
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
    include INC_PATH.'smarty.php';
    include '../events/_tabs.php';
    $smarty->assign('config', $config);
    $smarty->assign('constants', $constants);
    $smarty->assign('content', $content);
    $smarty->display('base.tpl');
    exit;
}




$action = $_REQUEST['action'];
if (!isset($action)) {
  echo "Bad Request.  Action required.";
  http_response_code(400);
  exit;
}


$id_members = $_REQUEST['id_member'];
if (is_numeric($id_members)) {
  // if it's a number, convert it to an array
  $id_members = array($id_members);
}


// TODO Check that member is authorized to edit these envelopes!
include_once INC_PATH.'auth.php';

// must be logged in and an authorized user
if (!$auth->isLogged()) {
  header('HTTP/1.0 403 Forbidden');
  redirect('login.php');
  exit();
}
$currUser = $auth->getCurrentUser();
$uid = $currUser['uid'];
$year = @is_numeric($_GET['year']) ? $_GET['year'] : YEAR;
$members = $associates->listAssociates($uid);
foreach ($id_members as $id_member) {
  if (!isset($members[$id_member])) {
      header('HTTP/1.0 403 Forbidden');
      echo "Unauthorized access";
      exit;
  }
}




/**
foreach($id_members as $id_member) {
  echo "id_member $id_member<br>";
}
exit;
*/


include_once INC_PATH.'resources/cart/CartReader.php';
include_once INC_PATH.'resources/cart/CartSerializer.php';

switch($action) {

  case 'addItem': 
    if (empty($id_members)) { 
      redirect("additional.php?error=Must select at least one person");
      exit;
    }
    $itemId = $_REQUEST['itemId'];
    if (!is_numeric($itemId)) { echo "Bad request.  Item required."; http_response_code(400); exit; }

    // check if item exists
    $sql = "select * from ucon_prereg_items where is_public=1 and id_prereg_item=?";
    $list = $db->getAll($sql, array($itemId));
    if (!is_array($list)) { echo "Sql Error: ".$db->ErrorMsg(); exit; }
    if (count($list) == 0) { echo "unknown item"; exit; }
    $preregItem = $list[0];

    // insert the item into the order table
    $carts = array();
    foreach ($id_members as $id_member) {
      $carts[$id_member] = CartSerializer::loadFromDatabase($db, $id_member, $year);
      $reader = new CartReader($carts[$id_member]);

      $name = $_REQUEST['badgeName'];
      if (!isset($name)) { $name = ''; };

      $item = array('type'=>$preregItem['itemtype'],
                    'subtype'=>$preregItem['subtype'],
                    'price'=> $preregItem['unit_price'],
                    'special'=>$name,
                    'quantity'=>1);
      $errorOrItem = $reader->addItem($item);
      if (!is_array($errorOrItem)) {
        $error = $errorOrItem;
//echo "<pre>";
//echo $itemId."\n";
//echo $errorOrItem."\n";
//echo print_r($item,1)."\n";
        redirect('additional.php?error='.$error);
      }
    }

    // if no errors occurred, save all members
    foreach ($id_members as $id_member) {
      CartSerializer::saveToDatabase($db, $id_member, $year, $carts[$id_member]);
    }

    redirect('../envelope.php?envelope='.$id_members[0].'&update=Added '.$preregItem['itemtype']);

    break;

  case 'removeItem':
    if (count($id_members)!=1) { header('HTTP/1.0 400 Bad Request'); echo "Action applies to a single member"; exit; }
    if (!isset($_REQUEST['orderId'])) { header('HTTP/1.0 400 Bad Request'); echo 'requires orderId'; exit; }
    if (!is_numeric($_REQUEST['orderId'])) { header('HTTP/1.0 400 Bad Request'); echo 'requires orderId'; exit; }
    if (!is_numeric($id_members[0])) { header('HTTP/1.0 400 Bad Request'); echo "Action applies to a single member"; exit; }

    //$cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
    //$reader = new CartReader($cart);
    //$item = $reader->removeItem($_REQUEST['cartId']);
    $sql = "delete from ucon_order where id_member=? and  id_order=? and id_convention=?";

    //echo "<pre>".print_r($cart,1)."</pre>";
    //echo "Item to remove:<pre>".print_r($item,1)."</pre>";
    $params = array($id_members[0], $_REQUEST['orderId'], $config['ucon']['year']);
    //echo "<pre>".print_r($params,1)."</pre>";
    $ok = $db->execute($sql, $params);
    if (!$ok) { header('HTTP/1.0 500 Internal Error'); echo "SQL Error: ".$db->ErrorMsg(); exit; }

    redirect('../envelope.php?envelope='.$id_members[0].'&update=Removed '.urlencode(htmlentities($_REQUEST['desc'])));

    break;
  default:
    echo "Bad Request.  Action ($action) unknown.";
    http_response_code(400);
    exit;
}



exit;






session_start();

if (!isset($_SESSION['reg']['cart'])) {
  $cart = CartReader::createCartData();
} else {
  $cart = $_SESSION['reg']['cart'];
}
//  echo "<pre>".print_r($cart,1)."</pre>";
$reader = new CartReader($cart);

// make alterations
switch ($_REQUEST['action']) {
  case '': // fall through
    break;

	case 'addTicket':
    if (!$_REQUEST['id_event']) { $error = 'must specify event id'; break; }
    $idEvent = $_REQUEST['id_event'];
    
    // look up event to validate and get the price
    $event = $db->getRow("select * from ucon_event where id_event=?", array($idEvent));
    if (!$event) { $error = "invalid event $idEvent"; break; }
    // TODO don't look up the event from the database a 2nd time!

    $item = array('type'=>'Ticket',
                  'subtype'=>$idEvent,
                  'price'=>$event['i_cost'],
                  'quantity'=>1);
    $errorOrItem = $reader->addItem($item);
    if (!is_array($errorOrItem)) $error = $errorOrItem;

    //echo "<pre>".print_r($errorOrItem,1)."</pre>";
    break;


  case 'addBadge':
    if (!$_REQUEST['badgeType']) { $error = 'must specify badgeType'; break; }
    if (!$_REQUEST['badgeName']) { $error = 'must specify badgeName'; break; }
    $subtype = $_REQUEST['badgeType'];
    $name = $_REQUEST['badgeName'];
    // validate Subtype and lookup price
    $badge = $db->getRow("select * from ucon_prereg_items where ((itemtype='Badge')) and is_public=1 and id_prereg_item=?", array($subtype));
//    $badge = $db->getRow("select * from ucon_badge_type where s_type=?", array($subtype));

    if (!$badge) { $error = "invalid badge type $subtype"; break; }
    // TODO cache badge prices

    $item = array('type'=>'Badge',
                  'subtype'=>$badge['subtype'],
                  'price'=> $badge['unit_price'],
                  'special'=>$name,
                  'quantity'=>1);
    $errorOrItem = $reader->addItem($item);
    if (!is_array($errorOrItem)) $error = $errorOrItem;
    break;

  case 'removeItem':
    $reader->removeItem($_REQUEST['cartId']); 
    break;

  case 'updateQuantity':
  	foreach ($_REQUEST['quantity'] as $itemId => $itemQuantity) {
      $itemReference =& $reader->getItemReference($itemId);
      if ($itemReference['type' == 'Badge']) {
      	$error = 'Badges must have a quantity of one';
      } else if (is_numeric($itemQuantity)) {

      	$reader->addEventInformation($itemReference);
//      	echo '<pre>'.print_r($itemReference,1).'</pre>';

      	// also check the number of tickets left
      	if (isset($itemReference['event'])) {
      		$ticketsLeft = $itemReference['event']['tickets_left'];
      		if ($ticketsLeft < $itemQuantity) {
      			$eventName = $itemReference['event']['s_title'];
      			$error = 'Only '.$ticketsLeft.' tickets available for '.$eventName;
      		}
      		$itemQuantity = min($itemQuantity, $ticketsLeft);
          $itemReference['quantity'] = intval($itemQuantity);
        } else {
          $itemReference['quantity'] = intval($itemQuantity);
        }

      } else {
      	$error = 'Ignoring non-integer quantities';
      }
  	}
  	break;

  default:
    die('unknown action: '.$_REQUEST['action']);
}

$reader->updateData();
