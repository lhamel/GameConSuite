<?php
require_once INC_PATH.'db/db.php';
require_once INC_PATH.'resources/cart/CartReader.php';

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

	case 'addItem':
	  $itemId = $_REQUEST['itemId'];
	  if (!is_numeric($itemId)) { echo "invalid item"; exit; }
	  $sql = "select * from ucon_prereg_items where is_public=1 and id_prereg_item=?";
	  $list = $db->getAll($sql, array($itemId));
	  if (!is_array($list)) { echo "Sql Error: ".$db->ErrorMsg(); exit; }
	  if (count($list) == 0) { echo "unknown item"; exit; }
	  $item = $list[0];
	  
	  $item = array('type'=>$item['itemtype'],
                  'subtype'=>$item['subtype'],
                  'price'=> $item['unit_price'],
                  'special'=>'',
                  'quantity'=>1);
	  $errorOrItem = $reader->addItem($item);
	  if (!is_array($errorOrItem)) $error = $errorOrItem;
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

  case 'addShirt':
    if (!$_REQUEST['size']) { $error = 'must specify size'; break; }
    $size = $_REQUEST['size'];

    // validate subtype and look up price
    $shirt = $db->getRow("select * from ucon_shirt_type where s_size=?", array($size));
    if (!$shirt) { $error = "invalid shirt type $size"; break; }
    // TODO cache shirt prices

    $item = array('type'=>'Shirt',
                  'subtype'=>$shirt['s_size'],
                  'price'=>$shirt['i_price'],
                  'quantity'=>1);
    $errorOrItem = $reader->addItem($item);
    if (!is_array($errorOrItem)) $error = $errorOrItem;
    break;

  case 'removeItem':
    $reader->removeItem($_REQUEST['cartId']); 
    break;

  case 'addPayment': 
    $item = array('method'=>'paypal', 'credit'=>'10');
    $itemOrError = $reader->addPayment($item);
    if (!is_array($itemOrError)) $error = $itemOrError;
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
                  $idEvent = $itemReference['event']['id_event'];
                  $eventName = $itemReference['event']['s_title'];
                  $game = $itemReference['event']['s_game'];
                  $eventName = $game . ($game && $eventName ? ': ' : '') . $eventName;

                  if ($ticketsLeft>0) {
                    $error = 'Only '.$ticketsLeft.' tickets available for '.$eventName;
                  } else {
                    $error = "No tickets available for $eventName.";
                  }
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

// after changes are processed, check that tickets would not be over-sold
// check that none of the changes resulted in an oversold problem
$items = $reader->getItems();
foreach ($items as $itemId => $ignore) {

  $itemReference =& $reader->getItemReference($itemId);
  if ($itemReference['type'] == 'Ticket') {

    // read the lastest event information, including ticket count
    $reader->addEventInformation($itemReference);
    if (isset($itemReference['event'])) {
      $itemQuantity = $itemReference['quantity'];
      $ticketsLeft = $itemReference['event']['tickets_left'];

      // check if there are enough tickets left, and reduce if necessary
      if ($ticketsLeft < $itemQuantity) {
        $idEvent = $itemReference['event']['id_event'];
        $eventName = $itemReference['event']['s_title'];
        $game = $itemReference['event']['s_game'];
        $eventName = $game . ($game && $eventName ? ': ' : '') . $eventName;

        if ($ticketsLeft>0) {
          $errMsg = 'Only '.$ticketsLeft.' tickets available for '.$eventName;
        } else {
          $errMsg = "No tickets available for #$idEvent $eventName.";
        }

        if ($error) {
          $error .= ' '.$errMsg;
        } else {
          $error = $errMsg;
        }
        $itemReference['quantity'] = intval($ticketsLeft);
      }
    }
  }
  // remove any items with zero quantity
  if ($itemReference['quantity'] <= 0) {
    $reader->removeItem($itemId);
  }

}


$reader->updateData();
$_SESSION['reg']['cart'] = $cart;
