<?php

class CartSerializer
{
	function CartSerializer() {
	}

  static function saveToDatabase($db, $id_member, $year, &$cart) {
    // load the previous version of the cart
    $origCart = CartSerializer::loadFromDatabase($db, $id_member, $year);
    // echo "<pre>orig\n".print_r($origCart,1)."</pre>";
    // echo "<pre>replacement\n".print_r($cart,1)."</pre>";

    $added = CartSerializer::findAdded($origCart, $cart);
    $removed = CartSerializer::findRemoved($origCart, $cart);
    $updated = CartSerializer::findUpdated($origCart, $cart);

    // handle added
    $sql = <<< EOD
insert into ucon_order 
  (id_member, id_convention, s_type, s_subtype, i_price, i_quantity, s_special) 
  values (?,?,?,?,?,?,?)
EOD;
    $stmt = $db->Prepare($sql);
    foreach ($added as $item) {
      $row = array(
        $id_member,
        $year,
        $item['type'],
        $item['subtype'],
        $item['price'],
        $item['quantity'],
        $item['special'] ? $item['special'] : ''
      );
      //echo "<pre>".print_r($row,1)."</pre>";
      
      $ok = $db->execute($stmt, $row);
      if (!$ok) die($db->ErrorMsg());
    }

    // handle updated
    $stmt = $db->Prepare('update ucon_order set i_quantity=? where id_order=?');
    foreach ($updated as $item) {
      $row = array(
      $item['quantity'],
      $item['id_order']
      );
      //echo "<pre>".print_r($row,1)."</pre>";
    
      $ok = $db->execute($stmt, $row);
      if (!ok) die($db->ErrorMsg());
    }

    // handle removed
    $stmt = $db->Prepare('delete ucon_order where id_order=?');
    foreach ($removed as $item) {
      $row = array(
      $item['id_order']
      );
      //echo "<pre>".print_r($row,1)."</pre>";
    
      $ok = $db->execute($stmt, $row);
      if (!$ok) die($db->ErrorMsg());
    }

  }

  /** 
   * Creates a cart suitable for adding the session
   * @param $db
   * @param $id_member
   */
  static function loadFromDatabase($db, $id_member, $year) {
    $cart = CartReader::createCartData();
    $reader = new CartReader($cart);

    $sql = 'select * from ucon_order where id_member=? and id_convention=?';
    $results = $db->getArray($sql, array($id_member, $year));
    if (!is_array($results)) die('SQL Error: '.$db->ErrorMsg());
    foreach ($results as $row) {
	if ($row['s_type'] == 'Payment') {
          $item = array('method'=>$row['s_subtype'], 'notes'=>$row['s_special']);
          if ($row['i_price']<0) {
            $item['debit']=$row['i_price'];
          } else {
            $item['credit']=-$row['i_price'];
          }
          $errorOrItem = $reader->addPayment($item);
        } else {
          $item = array(
            'id_order' => $row['id_order'],
            'type' => $row['s_type'],
            'subtype' => $row['s_subtype'],
            'price' => $row['i_price'],
            'quantity' => $row['i_quantity'],
            'special' => $row['s_special'],
            'itemTotal' => $row['i_price']*$row['i_quantity']
          );
    	  $errorOrItem = $reader->addItem($item);
        }
      if (!is_array($errorOrItem)) echo "<pre>$errorOrItem</pre>";
    }
    
    return $cart;
  }

  function updateExistingCart($db, $id_member, $year, $origCart, $modifiedCart) {
  	
  }
  
  /**
  * Return an array of items which are in $newCart but not in $oldCart
  * @param unknown_type $oldCart
  * @param unknown_type $newCart
  */
  static function findAdded($oldCart, $newCart) {
    return CartSerializer::setSubtract($newCart['items'], $oldCart['items']);
  }

  /**
  * Return an array of items which are in $oldCart but not in $newCart
  * @param unknown_type $oldCart
  * @param unknown_type $newCart
  */
  static function findRemoved($oldCart, $newCart) {
    return CartSerializer::setSubtract($oldCart['items'], $newCart['items']);
  }

  /**
  * Return an array of items which are in both $newCart and $oldCart but the quantity was updated
  * @param unknown_type $oldCart
  * @param unknown_type $newCart
  */
  static function findUpdated($oldCart, $newCart) {
    $result = array();
    foreach ($oldCart['items'] as $k => $item) {
      if (isset($newCart['items'][$k])) {
        if ($item['type'] != 'Payment' && $item['type'] != 'Discount') {
          if ($newCart['items'][$k]['quantity'] != $item['quantity']) {
            // note this is a naive ID comparison
            $result[$k] = $newCart['items'][$k];
          }
        }
      }
    }
    return $result;
  }

  static function setSubtract($target, $minus) {
    $result = array();
    foreach ($target as $k => $item) {
      if (!isset($minus[$k])
          && $item['type'] != 'Payment' && $item['type'] != 'Discount') { // note this is a naive ID comparison
        $result[$k] = $item;
      }
    }
    return $result;
  }

}
