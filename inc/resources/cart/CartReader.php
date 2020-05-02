<?php

class CartReader
{
    private $cart;
    private $nextBadgeId = 0;

    function CartReader(&$cart) {
    	$this->cart = &$cart;
    }

    function getTotalQuantity() {
        $count = 0;
        foreach ($this->cart['items'] as $k => $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    function getTotal() {
        $total = 0;
        foreach ($this->cart['items'] as $k => $item) {
            $total += $item['quantity']*$item['price'];
        }
        return $total;
    }

    function getPaid() {
        $paid = 0;
        foreach ($this->cart['payments'] as $k => $item) {
            $paid += $item['credit'];
        }
        return $paid;
    }

    function getItems() {
    	return $this->cart['items'];
    }

    function &getItemReference($id) {
      return $this->cart['items'][$id];
    }

    function addItem($item) {
        $error = $this->validateItem($item);
        if ($error) {
        	return $error;
        } else if (isset($id)) {
        	return 'must not set id';
        }

        $foundItem = $this->findItem($item);
        if (isset($foundItem)) {
          $foundId = $foundItem['id'];
          ++$this->cart['items'][$foundId]['quantity'];
          return $this->cart['items'][$foundId];
        }

        // add some extra information for displaying if this is a ticket
        $this->addEventInformation($item);

        // TODO for admin recover removed items

        $id = $this->generateId($item);
        $item['id'] = $id;
        $this->cart['items'][$id] = $item;
//        echo '<pre>'.print_r($this->cart['items'],1).'</pre>';
        return $item;
    }

    /**
     * Check for copies of this item already in the cart
     * @param $item the item with the set type & subtype
     */
    function findItem($target) {
      $type = $target['type'];
      $subtype = $target['subtype'];

      if ($type == 'Badge' || $type == 'Payment') {
        return; // these items can't have quantity
      }

      foreach ($this->cart['items'] as $item) {
        if ($item['type'] == $type && $item['subtype'] == $subtype) {
          return $item;
        }
      }
      return; // found nothing, so return nothing
    }

    function updateEventInformation() {
    	foreach ($this->cart['items'] as $k => $v) {
    		$this->addEventInformation($this->cart['items'][$k]);
    	}
    }

    function addEventInformation(&$item) {
        if ($item['type'] == 'Ticket') {
            global $db;
            $id = $item['subtype'];
            $event = $db->getArray("select * from ucon_event where id_event=?", array($id));
            $item['event'] = $event[0];

            $item['event']['tickets_sold'] = 0;
            $soldCount = $db->getRow("select s_subtype, sum(i_quantity) as sold from ucon_order where s_type='Ticket' and s_subtype=?", array($id));
            if (!is_array($soldCount)) die('sql error: '.$db->ErrorMsg());
            if ($soldCount['sold']>0) {
            	$item['event']['tickets_sold'] = $soldCount['sold'];
            }
            $item['event']['tickets_left'] = $item['event']['i_maxplayers'] - $item['event']['tickets_sold'];
        }
    }

    private function generateId($item) {
    	if ($item['type'] == 'Badge') {
            while(true)
            {
                // search for an ID not taken
                $id = 'Badge|'. (++$this->nextBadgeId);
            	if (!isset($this->cart['items'][$id])) {
            		return $id;
            	}
            }
    		return ;
    	} else {
    		return $item['type'].'|'.$item['subtype'];
    	}
    }

    function removeItem($id) {
        $item = $this->cart['items'][$id];
        // TODO for admin move item to removed
        unset($this->cart['items'][$id]);
        return $item;
    }

    function validateItem($item) {
        $error = $this->validateSingleItem($item);
        if ($error) return $error;

        if ($item['type'] == 'Badge') {
        	return $this->validateBadge($item);
        } else if ($item['type'] == 'Ticket') {
            return $this->validateTicket($item);
        } else {
            return '';
        }
    }

    private function validateSingleItem($item) {
        if (!is_numeric($item['quantity'])) {
            return 'invalid quantity: '.$item['quantity'];
        } else if ($item['quantity'] < 0) {
            return 'invalid quantity:'.$item['quantity'];
        } else if (!is_numeric($item['price'])) {
            return 'invalid price: '.$item['price'];
        //} else if ($item['price'] < 0) {
        //    return 'invalid price: '.$item['price'];
        } else if (!isset($item['subtype'])) {
            return 'invalid subtype: '.$item['subtype'];
        } else if (!isset($item['type'])) {
            return 'invalid type: '.$item['type'];
        } else {
            return '';
        }
    }

    private function validateBadge($item) {
        if ($item['quantity'] != 1) {
            return 'Each badge has a quantity of one';
        } else if (!isset($item['special'])) {
            return 'Badges require a name designated by special';
        } else {
        	return '';
        }
    }

    private function validateTicket($item) {
        global $db;
        $id = $item['subtype'];
        $events = $db->getArray("select * from ucon_event where id_event=?", array($id));
        if (isset($events) and isset($events[0]))
            return '';
        else
            return "event id " . $item['subtype'] . " not found: " . $db->ErrorMsg();
    }

    function addPayment($item) {
        if ($error = $this->validatePayment($item)) {
            return $error;
        }

        if (isset($item['debit'])) {
            if (!isset($item['credit'])) {
                $item['credit'] = 0;
            }
            $item['credit'] -= $item['debit'];
            unset ($item['debit']);
        }

        $id = count($this->cart['payments']);
        $item['id'] = $id;
        $this->cart['payments'][$id] = $item;
        //echo '<pre>'.print_r($this->cart['items'],1).'</pre>';
        return $item;
    }

    private function validatePayment($item) {
      if (!isset($item['credit']) && !isset($item['debit'])) {
        return 'Payment requires field credit or debit';
      }
      if (!isset($item['method'])) {
        return 'Payment requires field method';
      }
      if (!isset($item['notes'])) {
        return 'Payment requires field notes';
      }
      // else if ($item['method']!='paypal' 
      //           && $item['method']!='check' 
      //           && $item['method']!='cash'
      //           && $item['method']!='staff discount'
      //           && $item['method']!='other discount') {
      //  return 'Payment method must be paypal, cash, or check';
      //}
      return '';
    }

    function updateData() {
      $this->cart['itemTotal'] = $this->getTotal();
      $this->cart['paymentTotal'] = $this->getPaid();
      $this->cart['due'] = $this->getTotal()-$this->getPaid();
    }

    static function createCartData() {
      $cart = array(
        'items' => array(),
        //'removed' => array(),
        'payments' => array(),
        'itemTotal' => 0,
        'paymentTotal' => 0,
        'due' => 0,
      );
      return $cart;
    }
}

