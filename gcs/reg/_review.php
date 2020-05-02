<?php
/**
 * This file contains the control flow checks for registration
 */
require_once INC_PATH.'db/db.php';
require_once INC_PATH.'resources/cart/CartReader.php';

// start session and check personal information


include '_personal.php';
if (isset($errors) && count($errors)>0) {
  header('location: personal.php');
  exit;
}

if (!isset($_SESSION['reg']['cart'])) {
  $cart = CartReader::createCartData();
} else {
  $cart = $_SESSION['reg']['cart'];
}

$reader = new CartReader($cart);
if ($reader->getTotalQuantity() < 1) {
  header('location: cart.php');
  exit;
}

// check ticket quantities again
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
        header('location: cart.php');
        exit;
      }
    }
  }
}


//  if there are no items in the cart ($0 items are okay)
//    send to item selection or display a warning that no items are in the cart
//  else
//    review order
//    submit button --> purchase page


