<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Event;
use OpenAPIServer\Model\PublicEvent;


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
    protected $categoryRepository;
    protected $roomRepository;
    protected $memberRepository;
    protected $siteConfiguration;

    public function __construct(\ADOConnection $db, CategoryRepository $categoryRepository, MemberRepository $memberRepository, RoomRepository $roomRepository)
    {
        $this->db = $db;
        $this->memberRepository = $memberRepository;
        $this->categoryRepository = $categoryRepository;
        $this->roomRepository = $roomRepository;
        $this->siteConfiguration = $GLOBALS['config']; // TODO pass through dependency injection
    }

    // public function generateId(): PostId
    // {
    //     return PostId::fromInt($this->persistence->generateId());
    // }


    /** Retrieve the Event by its Event Id as long as it is approved and belongs in the current convention */
    public function findPublicEventById(int $id): PublicEvent
    {
        $fields = ['id_event', 'id_convention', 'id_gm', 's_number', 's_title', 's_game', 's_desc', 's_desc_web', 'i_minplayers', 'i_maxplayers', 'i_agerestriction', 'e_exper', 'e_complex', 'i_length', 'e_day', 'i_time', 'id_room', 's_table', 'i_cost', 'id_event_type', 'id_room' ];

        $sql = 'select '.join(',', $fields).' from ucon_event where id_event=? and b_approval=1 and id_convention=?';
        $result = $this->db->getAll($sql, [$id, $this->siteConfiguration['gcs']['year']]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        if (count($result) == 0) {
            throw new OutOfBoundsException(sprintf('Event with id %d does not exist', $id, 0));
        }


        // map the data into the API model object
        return $this->createPublicEvent($result[0]);
    }

    /** Retrieve the Event by its Event Id */
    public function findById(int $id): Event
    {
        $fields = ['id_event', 'id_convention', 'id_gm', 's_number', 's_title', 's_game', 's_desc', 's_desc_web', 'i_minplayers', 'i_maxplayers', 'i_agerestriction', 'e_exper', 'e_complex', 'i_length', 'e_day', 'i_time', 'id_room', 's_table', 'i_cost', 'id_event_type', 'id_room'];

        $sql = 'select '.join(',', $fields).' from ucon_event where id_event=?';
        $result = $this->db->getAll($sql, [$id]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

        if (count($result) == 0) {
            throw new OutOfBoundsException(sprintf('Event with id %d does not exist', $id, 0));
        }


        // map the data into the API model object
        return $this->createEvent($result[0]);
    }


    private function createPublicEvent(array $state) : PublicEvent
    {

        // validate required fields
        $required = [
          'id_event', 's_game', 's_title','s_table', 'i_minplayers', 'i_maxplayers', 'e_day', 'i_time', 's_desc_web', 's_desc', 'i_cost'
        ];

        foreach ($required as $k) {
          if (!isset($state[$k])) {
            throw new \Exception("Event data missing required field $k");
          }
        }

        $e = new PublicEvent();
        $e->id = $state['id_event'];
        $e->game = $state['s_game'];
        $e->title = $state['s_title'];
        $e->table = $state['s_table'];
        $e->maxplayers = $state['i_maxplayers'];
        $e->minplayers = $state['i_minplayers'];
        $e->price = $state['i_cost'];


        // TODO Validate
        $e->day = $state['e_day'];
        $e->time = $state['i_time'];

        // TODO check that these are in the correct order
        $e->desclong = $state['s_desc_web'];
        $e->descshort = $state['s_desc'];

        // required fields
        $gm = $this->memberRepository->findPublicMemberById((int)$state['id_gm']);
        $cat = $this->categoryRepository->findById((int)$state['id_event_type']);

        // TODO allow this to be null depending on configuration
        $room = $this->roomRepository->findById((int)$state['id_room']);

        // TODO add price information

        $e->gm = $gm;
        $e->category = $cat;
        $e->room = $room;

        return $e;
    }

    private function createEvent(array $state) : Event
    {
        // TODO reduce duplication

        // validate required fields
        $required = [
          'id_event', 's_game', 's_title','s_table', 'i_minplayers', 'i_maxplayers', 'e_day', 'i_time', 's_desc_web', 's_desc', 'i_cost'
        ];

        foreach ($required as $k) {
          if (!isset($state[$k])) {
            throw new \Exception("Event data missing required field $k");
          }
        }

        $e = new Event();
        $e->id = $state['id_event'];
        $e->game = $state['s_game'];
        $e->title = $state['s_title'];
        $e->table = $state['s_table'];
        $e->maxplayers = $state['i_maxplayers'];
        $e->minplayers = $state['i_minplayers'];
        $e->price = $state['i_cost'];

        // TODO Validate
        $e->day = $state['e_day'];
        $e->time = $state['i_time'];

        // TODO check that these are in the correct order
        $e->desclong = $state['s_desc'];
        $e->descshort = $state['s_desc_web'];

        // required fields
        $gm = $this->memberRepository->findPublicMemberById((int)$state['id_gm']);
        $cat = $this->categoryRepository->findById((int)$state['id_event_type']);

        // TODO allow this to be null depending on configuration
        $room = $this->roomRepository->findById((int)$state['id_room']);


        $e->gm = $gm;
        $e->category = $cat;
        $e->room = $room;

        return $e;
    }

    // /** @var \OpenAPIServer\Model\Tag[] $tags */
    // public $tags;

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


