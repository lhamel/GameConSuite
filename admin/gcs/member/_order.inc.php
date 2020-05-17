<?php 

switch ($action) {
  case '':
    break;

  case 'addTicket':
    if (!isset($_REQUEST['id_event'])) {
      echo("addTicket requires id_event"); exit;
    }
    addTicket($id_member, $_REQUEST['id_event']);
    redirect('order.php?id_member='.$id_member);
    exit;

  case 'addItem':
    if (!is_numeric($_REQUEST['itemId'])) { echo 'requires itemId'; exit; }
    if (!is_numeric($_REQUEST['id_member'])) { echo 'requires id_member'; exit; }
    $sql = "select * from ucon_prereg_items where id_prereg_item=?";
    $records = $db->getAll($sql, array($_REQUEST['itemId']));
    if ($records === false) { echo 'Sql Error: '.$db->ErrorMsg(); exit; }
    if (count($records)!= 1) { echo 'bad item id'; exit; }
// 	  echo "records<pre>".print_r($records, 1)."</pre>";
    $item = array('type'=>$records[0]['itemtype'],
                  'subtype'=>$records[0]['subtype'],
                  'price'=> $records[0]['unit_price'],
                  'special'=>'',
                  'quantity'=>1);
	  //echo "item<pre>".print_r($item, 1)."</pre>"; //exit;
	  $errorOrItem = $reader->addItem($item);
	  if (!is_array($errorOrItem)) $error = $errorOrItem;
  
	  // save to the database
	  CartSerializer::saveToDatabase($db, $_REQUEST['id_member'], $year, $cart);
	  if (!isset($error)) {
      redirect('order.php?id_member='.$_REQUEST['id_member']);
	  }
    break;

  case 'removeItem':
    if (!isset($_REQUEST['cartId'])) { echo 'requires cartId'; exit; }
    if (!is_numeric($_REQUEST['id_member'])) { echo 'requires id_member'; exit; }
    
    $item = $reader->removeItem($_REQUEST['cartId']);

    $sql = "delete from ucon_order where id_order=? and id_convention=?";
    //echo "Item to remove:<pre>".print_r($item,1)."</pre>";
    $ok = $db->execute($sql, array($item['id_order'], $config['gcs']['year']));
    if (!$ok) { echo "SQL Error: ".$db->ErrorMsg(); exit; }

	  //CartSerializer::saveToDatabase($db, $_REQUEST['id_member'], $year, $cart);
    redirect('order.php?id_member='.$id_member);
    break;

  case 'updateQuantity':
    foreach ($_REQUEST['quantity'] as $itemId => $itemQuantity) {
      $itemReference =& $reader->getItemReference($itemId);
      if ($itemReference['type' == 'Badge']) {
        $error = 'Badges must have a quantity of one';
      } else if ($itemReference['type' == 'Payment']) {
        $error = 'Payments must have a quantity of one';
      } else if (!is_numeric($itemQuantity)) {
        $error = 'Ignoring non-integer quantities';
      } else {

        //echo "<pre>".print_r($itemReference,1)."</pre>"; exit;
        $sql = "update ucon_order set i_quantity=? where id_order=? and id_convention=?";
        $ok = $db->execute($sql, array($itemQuantity, $itemReference['id_order'], $config['gcs']['year']));
        if (!$ok) {
          echo "SQL Error: ".$db->ErrorMsg(); exit;
        } else {
          $success = 'Quantities updated';
        }

        $reader->addEventInformation($itemReference);

        // also check the number of tickets left
        if (isset($itemReference['event'])) {
        		$ticketsLeft = $itemReference['event']['tickets_left'];
        		if ($ticketsLeft < 0) {
        		  $eventName = $itemReference['event']['s_title'];
        		  $error = 'Event '.$eventName.' oversold by '.(-$ticketsLeft).' tickets';
        		}
        }

      }
    }
    break;

  case 'addPayment':
    if (!isset($_REQUEST['subtype'])) { echo 'addPayment requires subtype'; exit; }
    if (!isset($_REQUEST['amount'])) { echo 'addPayment requires amount'; exit; }
    if (!isset($_REQUEST['notes'])) { echo 'addPayment requires notes'; exit; }
    addPayment($id_member, $_REQUEST['subtype'], $_REQUEST['amount'], $_REQUEST['notes']);
    redirect('order.php?id_member='.$id_member);
}

unset($reader);
unset($cart);


function addTicket($id_member, $id_event) {
  if (!isset($id_event)) die("addTicket requires id_event");
  if (!isset($id_member)) die("addTicket requires id_member");

  global $db, $queries, $config;

  $sql = "select * from ucon_order where id_convention=? and id_member=? and s_type=? and s_subtype=?";
  $orders = $db->getAll($sql, array($config['gcs']['year'], $id_member, "Ticket", $id_event));
  if (!is_array($orders)) { echo "SQL Error: ".$db->ErrorMsg(); exit; }
  if (count($orders)>0) {
    return; // already a ticket
  }

  // get the information for the event
  $events = $db->getAll($queries['GET_EVENT'], array($id_event));
  if (!is_array($events)) die ("SQL Error: ".$db->ErrorMsg());
  $event = $events[0];

  $item = array(
    $config['gcs']['year'], // convention
    $id_member,
  	"Ticket",
    $id_event,
    1,
    $event['i_cost']
  );
  _addItem($item);
}

function addBadge($id_member, $type) {
  
}

function _addItem($item) {
  global $db;

  $sql = "insert into ucon_order (id_convention, id_member, s_type, s_subtype, i_quantity, i_price) values (?, ?, ?, ?, ?, ?)";
  $ok = $db->execute($sql, $item);
  if (!$ok) {
    echo "SQL Error: ".$db->ErrorMsg(); exit;
  }
}

function updateQuantity() {
}

function addPayment($id_member, $subtype, $amount, $notes) {
  global $db,$config;

  $sql = "insert into ucon_order set id_convention=?, id_member=?, s_type=?, s_subtype=?, s_special=?, i_price=?, i_quantity=1";
  $params = array($config['ucon']['year'], $id_member, 'Payment', $subtype, $notes, -$amount);
  $ok = $db->execute($sql, $params);
  if (!$ok) {
    echo "SQL Error: ".$db->ErrorMsg(); exit;
  }
}


