<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

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
select id_order, id_convention, id_member, s_subtype, i_quantity, i_price
from ucon_order as O
where id_convention=?
  and s_type = 'Ticket'
  and id_member=?
EOD;
        $preregOrders = $this->db->getAssoc($sql, [ $this->siteConfiguration['gcs']['year'], $memberId ]);
        if (!is_array($preregOrders)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        return $preregOrders;
    }

}


