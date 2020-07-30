<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OpenAPIServer\Model\CartItem;
use OutOfBoundsException;


/**
 * This class is situated between Entity layer (class Post) and access object layer (Persistence).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 */
class TicketRepository
{
    protected $db;
    protected $siteConfiguration;

    public function __construct(\ADOConnection $db)
    {
        $this->db = $db;
        $this->siteConfiguration = $GLOBALS['config']; // TODO pass through dependency injection
    }

    /** Retrieve a list of ticket counts for the list of specified events
     *  @param $idEvents an array containing integer identifiers of relevant events
     */
    public function findCurrentTicketCountByEvents($idEvents) : array
    {
        // validate parameter
        foreach ($idEvents as $id) {
            if (!is_numeric($id)) {
                throw new \Exception("Bad input, expecting only integers");
            }
        }

        $id_events = implode(',',$idEvents);

        // find sum of ticket quantities in prereg data
        $sql = <<< EOD
select s_subtype as id_event, sum(i_quantity) as count
from ucon_order as O
where id_convention=?
  and s_type = 'Ticket'
  and s_subtype in ($id_events)
group by s_subtype
EOD;
        $preregOrders = $this->db->getAssoc($sql, [ $this->siteConfiguration['gcs']['year'] ]);
        if (!is_array($preregOrders)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }


        // find sum of ticket quantities in cash reg data (excluding prereg recorded here)
        $sql = <<< EOD
select subtype as id_event, sum(TI.quantity) as count
from ucon_transaction_item as TI, ucon_item as I
where itemtype='Ticket'
  and TI.barcode=I.barcode
  and year=?
  and subtype in ($id_events)
  and TI.special != "prereg"
group by subtype
EOD;
        $onsiteOrders = $this->db->getAssoc($sql, [ $this->siteConfiguration['gcs']['year'] ]);
        if (!is_array($onsiteOrders)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        // add the onsite orders to the prereg orders
        foreach ($onsiteOrders as $id => $onsiteCount) {
            if (!isset($preregOrders[$id])) {
                // if theres no prereg order, create an empty one
                $preregOrders[$id] = 0;
            }

            // add the onsite orders to any previous orders
            $preregOrders[$id] += $onsiteOrders[$id];
        }

        return $preregOrders;
    }

    /* find the tickets that belong this member, and fill in the event information */
    public function findMemberTickets($memberId) {

        // find sum of ticket quantities in prereg data
        $sql = <<< EOD
select id_order as id, id_convention as conventionId, id_member as memberId, s_type as type, s_subtype as subtype, i_quantity as quantity, i_price as price
from ucon_order as O
where id_convention=?
  and s_type = 'Ticket'
  and id_member=?
EOD;
        $preregOrders = $this->db->getArray($sql, [ $this->siteConfiguration['gcs']['year'], $memberId ]);
        if (!is_array($preregOrders)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        return $preregOrders;
    }

    /**
     *  Use this method to add a cart item to the database. The caller is responsible for validating the item, 
     *  including checking for limited quantities and ensuring fields are filled in.  The year will be overwritten 
     *  with the current year.
     *  @return the cart item, including the newly inserted ID
     */
    public function addVerifiedCartItem(CartItem $cartItem)
    {
        // TODO convert input to cart object
        $cartItem->conventionId = $this->siteConfiguration['gcs']['year'];
        $params = [
            $cartItem->conventionId,
            $cartItem->memberId,
            $cartItem->type, 
            $cartItem->subtype,
            $cartItem->quantity,
            $cartItem->price,
            isset($cartItem->special) ? $cartItem->special : ''
        ];

        $sql = "insert into ucon_order set id_convention=?, id_member=?, s_type=?, s_subtype=?, i_quantity=?, i_price=?, s_special=?";
        $ok = $this->db->execute($sql, $params);
        if (!$ok) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }

        $cartItem->id = $this->db->GetOne('SELECT LAST_INSERT_ID()');
        return $cartItem;
    }


    public function updateVerifiedCartItemQuantity($memberId, $orderId, $quantity)
    {
        if ( !is_numeric($memberId) || !is_numeric($orderId) || !is_numeric($quantity) || $quantity>0 ) {
            throw new \Exception("Bad input to method");
        }

        $conventionId = $this->siteConfiguration['gcs']['year'];

        $params = [ $orderId, $memberId, $conventionId ];
        $sql = "select * from ucon_order where id_order=? and id_member=? and id_convention=?";
        $matches = $this->db->getAssoc($sql, $params);
        if (!is_array($matches)) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }
        if (count($ok) == 0) {
            return false; // nothing to update
        }

        $params = [ $quantity, $orderId, $memberId, $conventionId ];
        $sql = "update ucon_order set i_quantity=? where id_order=? and id_member=? and id_convention=?";
        $ok = $this->db->execute($sql, $params);
        if (!$ok) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }

        return $ok;
    }


    public function updateVerifiedCartItemCart($memberId, $orderId, $price)
    {
        if ( !is_numeric($memberId) || !is_numeric($orderId) || !is_numeric($quantity) || $quantity>0 ) {
            throw new \Exception("Bad input to method");
        }

        $conventionId = $this->siteConfiguration['gcs']['year'];

        $params = [ $orderId, $memberId, $conventionId ];
        $sql = "select * from ucon_order where id_order=? and id_member=? and id_convention=?";
        $matches = $this->db->getAssoc($sql, $params);
        if (!is_array($matches)) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }
        if (count($ok) == 0) {
            return false; // nothing to update
        }

        $params = [ $price, $orderId, $memberId, $conventionId ];
        $sql = "update ucon_order set i_price=? where id_order=? and id_member=? and id_convention=?";
        $ok = $this->db->execute($sql, $params);
        if (!$ok) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }

        return $ok;
    }


    /**
     *  Use this method to remove a cart item to the database. The caller is responsible for validating the item, 
     *  including checking for limited quantities and ensuring fields are filled in.  The year will be overwritten 
     *  with the current year.
     */
    public function removeVerifiedCartItem($memberId, $orderId)
    {

        $conventionId = $this->siteConfiguration['gcs']['year'];
        $params = [ $orderId, $memberId, $conventionId ];

        $sql = "select * from ucon_order where id_order=? and id_member=? and id_convention=?";
        $matches = $this->db->getAssoc($sql, $params);
        if (!is_array($matches)) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }
        if (count($matches) == 0) {
            return false; // nothing to delete
        }

        $sql = "delete from ucon_order where id_order=? and id_member=? and id_convention=?";
        $ok = $this->db->execute($sql, $params);
        if (!$ok) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }

        return $ok;
    }

    public function lookupCartItemUnitPrice(CartItem $cartItem)
    {
        if( 'Ticket' == $cartItem->type) {
            // check that ticket is available
            $event = $this->eventRepo->findById((int)$cartItem->subtype);
            return $event->cost;
        }

        $params = [ $cartItem->type, $cartItem->subtype ];
        $sql = "select * from ucon_prereg_items where itemtype=? and subtype=?";
        $matches = $this->db->getAll($sql, $params);
        if (!is_array($matches)) {
            throw new \Exception($this->db->ErrorMsg()."\n$sql");
        }
        if (count($matches) == 0) {
            throw new \Exception("Unknown item ".$cartItem->type);
        }
        if (count($matches) > 1) {
            throw new \Exception("Database integrety error: Multiple matches to item unexpected ($cartItem->type, $cartItem->subtype)");
        }
        $item = $matches[0];
        return $item['unit_price'];
    }

}


