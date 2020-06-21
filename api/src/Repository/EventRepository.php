<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Event;
use OpenAPIServer\Model\FormatEvent;
use OpenAPIServer\Model\PublicEvent;
use OpenAPIServer\Model\PublicMember;


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

    private $publicDbfields = ['id_event', 'id_convention', 'id_gm', 's_number', 's_title', 's_game', 's_desc', 's_desc_web', 'i_minplayers', 'i_maxplayers', 'i_agerestriction', 'e_exper', 'e_complex', 'i_length', 'e_day', 'i_time', 'id_room', 's_table', 'i_cost', 'id_event_type', 'id_room' ];

//     private $findGMsQuery = <<< EOD
//         select id_member, s_lname, s_fname, s_group
//         from ucon_member as M
//         where (s_lname LIKE ? or s_fname LIKE ? or s_group LIKE ?)
// EOD;
    private $findEventsQuery = <<< EOD
        select *
        from ucon_event as E, ucon_member as M
        where id_convention=?
          and E.id_gm = M.id_member
          and (s_game LIKE ? or s_desc LIKE ? or s_number LIKE ? or s_lname LIKE ? or s_fname LIKE ? or s_group LIKE ?)
          and b_approval=1
EOD;

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

    /** Retrieve a list of events that match the filtere parameters
     *  Note: category, ages, and tags are actually their identifiers
     */
    public function findPublicEvents($idConvention, $search, $day, $categoryId, $ages, $tags /*, string $sort */) : array
    {
        $wildcard = '%'.$search.'%';

        // search for matching GMs and gather a list

        // $result = $this->db->getAll($this->findGMsQuery, [$wildcard, $wildcard, $wildcard]);
        // if (!is_array($result)) {
        //     throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        // }

        // search for matching events, including those with GMs listed above
        // filter for all filter parameters

        // TODO need to retrieve all the GM information can cache it for retrieval!!


        $result = $this->db->getAll($this->findEventsQuery, [$idConvention, $wildcard, $wildcard, $wildcard, $wildcard, $wildcard, $wildcard]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        // cache GMs in the MemberRepository
        $gmIds = [];
        foreach ($result as $row) {
            $idGm = $row['id_gm'];
            $gmIds[$idGm] = $idGm;
        }

        // The members repo much cache the GMs to prevent spawning 100s of Members
        $this->memberRepository->cachePublicMembers($gmIds);


        // TODO use a full-text search???
        $events = [];
        foreach ($result as $row) {
            $events[] = $this->createPublicEvent($row);
        }

        return $events;
    }

    /** Retrieve the Event by its Event Id as long as it is approved and belongs in the current convention */
    public function findPublicEventById(int $id): PublicEvent
    {
        $sql = 'select '.join(',', $this->publicDbfields).' from ucon_event where id_event=? and b_approval=1 and id_convention=?';
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

    public function saveEventVTT(Event $event)
    {
        if (!isset($event->id)) {
            throw new Exception("Can only save VTT information to event that already exists");
        }

        $year = $this->siteConfiguration['gcs']['year'];

        $sql = "update ucon_event set s_vttlink=?, s_vttinfo=? where id_event=? and id_convention=?";
        $params = [$event->vttLink, $event->vttInfo, $event->id, $year];
        $ok = $this->db->execute($sql, $params);
        if (!$ok) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }
        return true;
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


    private function createPublicEvent(array $state) : FormatEvent
    {

        // validate required fields
        $required = [
          'id_event', 's_game', 's_title','s_table', 'i_minplayers', 'i_maxplayers', 'e_day', 'i_time', 's_desc_web', 's_desc', 'i_cost'
        ];

        foreach ($required as $k) {
          if (!array_key_exists($k, $state)) {
            throw new \Exception("Event data missing required field $k ".print_r($state, true));
          }
        }

        $e = new FormatEvent();
        $e->id = $state['id_event'];
        $e->table = $state['s_table'];
        $e->maxplayers = $state['i_maxplayers'];
        $e->minplayers = $state['i_minplayers'];
        $e->price = $state['i_cost'];

        // TODO format the title
        $title = trim($state['s_title']);
        $game = trim($state['s_game']);
        if (isset($title) && isset($game)) {
            $e->formatTitle = $title.': '.$game;
        } elseif (isset($title)) {
            $e->formatTitle = $title;
        } else {
            $e->formatTitle = $game;
        }

        // // TODO Validate
        $e->day = ucfirst(strtolower(''.$state['e_day']));
        // $e->time = $state['i_time'];

        // TODO check that these are in the correct order
        $d1 = $state['s_desc_web'];
        $d2 = $state['s_desc'];

        if (strlen($d1)>strlen($d2)) {
            $e->desclong = $d1;
            // $e->descshort = $d2;
        } else {
            $e->desclong = $d2;
            // $e->descshort = $d1;
        }

        // // TODO if member info is in the query, attach it, otherwise look it up (?)

        // required fields
        $gm = $this->memberRepository->findPublicMemberById((int)$state['id_gm']);
        $cat = $this->categoryRepository->findById((int)$state['id_event_type']);

        // format the GM name
        $e->gmName = PublicMember::formatName($gm);

        //  select the category field
        $e->categoryName = $cat->label;

        // TODO allow this to be null depending on configuration
        try {
            $room = $this->roomRepository->findById((int)$state['id_room']);
            $e->roomName = $room->label;
        } catch (OutOfBoundsException $ex) {
            // ok to not have a room assigned
        }

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


