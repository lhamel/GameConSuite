<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Event;


/**
 * This class is situated between Entity layer (class Post) and access object layer (Persistence).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 */
class EventRepository
{
    protected $db;
    protected $eventTypeRepository;
    protected $roomRepository;
    protected $memberRepository;

    public function __construct(\ADOConnection $db, EventTypeRepository $eventTypeRepository, MemberRepository $memberRepository /*, RoomRepository $roomRepository*/)
    {
        $this->db = $db;
        $this->memberRepository = $memberRepository;
        $this->eventTypeRepository = $eventTypeRepository;
        // $this->roomRepository = $roomRepository;
    }

    // public function generateId(): PostId
    // {
    //     return PostId::fromInt($this->persistence->generateId());
    // }

    /** Retrieve the Event by its Event Id */
    public function findById(int $id): Event
    {
        // pull data from the roomRepository
        $fields = ['id_event', 'id_convention', 'id_gm', 's_number', 's_title', 's_game', 's_desc', 's_desc_web', 'i_minplayers', 'i_maxplayers', 'i_agerestriction', 'e_exper', 'e_complex', 'i_length', 'e_day', 'i_time', 'id_room', 's_table', 'i_cost', 'id_event_type', 'id_room'];

        $sql = 'select '.join(',', $fields).' from ucon_event where id_event=?';
        $result = $this->db->getAll($sql, [$id]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

        if (count($result) == 0) {
            throw new OutOfBoundsException(sprintf('Event with id %d does not exist', $id, 0));
        }

        // required fields
        $gm = $this->memberRepository->findById((int)$result[0]['id_gm']);
        $et = $this->eventTypeRepository->findById((int)$result[0]['id_event_type']);

        // optional fields
        // $room = $this->roomRepository->findById($result['id_room']);

        // map the data into the API model object
        $event = \OpenAPIServer\Model\Event::fromState($result[0]);
        $event->gm = $gm;
        $event->et = $et;
        // $event->room = $room;

        return $event;


        //return Room::fromState($arrayData);

    }

    // public function save(Post $post)
    // {
    //     $this->persistence->persist([
    //         'id' => $post->getId()->toInt(),
    //         'statusId' => $post->getStatus()->toInt(),
    //         'text' => $post->getText(),
    //         'title' => $post->getTitle(),
    //     ]);
    // }
}


