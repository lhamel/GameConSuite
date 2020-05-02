<?php
include_once '../../inc/inc.php';
include_once INC_PATH.'db/db.php';
$year = $config['ucon']['year'];

// if you cannot view events or you cannot buy events...
if (!$config['allow']['view_events'] || !$config['allow']['buy_events']) {
  http_response_code(403);
  echo "Registration is not available for $year.  Please refresh the page.";
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
if (count($id_members)!=1) {
  header('HTTP/1.0 400 Bad Request');
  echo "Must select one member";
  exit;
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

  case 'addTicket':
    $id_member = $id_members[0];

    $itemId = $_REQUEST['itemId'];
    if (!is_numeric($itemId)) { echo "Bad request.  Item required."; http_response_code(400); exit; }

    /// look up the item to be added
    $sql = "select * from ucon_event where id_event=? and id_convention=? and b_approval=1";
    $list = $db->getAll($sql, array($itemId, $year));
    if (!is_array($list)) { echo "Sql Error: ".$db->ErrorMsg(); exit; }
    if (count($list) == 0) { echo "unknown item"; exit; }
    $preregItem = $list[0];
//echo "<pre>".print_r($preregItem,1)."</pre>"; exit;

    // look up if the item ia already sold out
    $sql = "select sum(i_quantity) as count from ucon_order where s_subtype=? and s_type='Ticket' and id_convention=?";
    $quantitySoldArr = $db->getAll($sql, array($itemId, $year));
    if (!is_array($quantitySoldArr)) { echo "Sql Error: ".$db->ErrorMsg(); exit; }
    $quantitySold = $quantitySoldArr[0]['count'];
//echo "<pre>".print_r($preregItem,1)."</pre>"; echo "<pre>".print_r($quantitySold,1)."</pre>"; exit;

    if ($quantitySold >= $preregItem['i_maxplayers']) {
      http_response_code("400");
      echo "Event sold out";
      exit;
    }

    // insert the item into the order table
    $cart = CartSerializer::loadFromDatabase($db, $id_member, $year);
    $reader = new CartReader($cart);

    // check to see if item is already in cart
    $item = $reader->findItem( array('type'=>'Ticket', 'subtype'=>$itemId) );
    if ($item) {
      http_response_code(400);
      echo "Member already has this ticket";
      exit;
    }
//echo "<pre>".print_r($item,1)."</pre>"; exit;


    $item = array('type'=>'Ticket',
                  'subtype'=>$itemId,
                  'price'=> $preregItem['i_cost'],
                  'special'=>'',
                  'quantity'=>1);
    $errorOrItem = $reader->addItem($item);
    if (!is_array($errorOrItem)) {
      $error = $errorOrItem;
      http_error_code(500);
      echo "<pre>";
      echo $itemId."\n";
      echo $errorOrItem."\n";
      echo print_r($item,1)."\n";
      exit;
    }

    // if no errors occurred, save members
    CartSerializer::saveToDatabase($db, $id_member, $year, $cart);

    //echo "Success";

    $li = $associates->getLoginInfo();
    echo json_encode($li['ticketSelection']);

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
