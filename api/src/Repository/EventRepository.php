<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Event;
use OpenAPIServer\Model\EventPrivate;
use OpenAPIServer\Model\EventAdmin;
use OpenAPIServer\Model\Member;


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

    const ROLE_GENERAL = 1;
    const ROLE_PLAYER_OR_THE_GM = 2;
    const ROLE_ADMIN = 3;

    const PUBLIC_DB_FIELDS = ['id_event', 'id_convention', 'id_gm', 's_number', 's_title', 's_game', 's_desc', 's_desc_web', 'i_minplayers', 'i_maxplayers', 'i_agerestriction', 'e_exper', 'e_complex', 'i_length', 'e_day', 'i_time', 'i_cost', 'id_event_type', ];
    const LIMITED_DB_FIELDS = ['s_vttlink', 's_vttinfo', 'b_approval'];

    const COND_DB_FIELDS = ['id_room', 's_table'];

    const ADMIN_DB_FIELDS = ['s_comments', 's_setup', 's_table_type', 's_eventcom', 'b_edited', 'i_c1', 'i_c2', 'i_c3', 'i_actual', 'b_showed_up', 'd_updated', 'd_created', 'b_prize', 'i_profit', 's_note'] + self::COND_DB_FIELDS;

    // const FIELDS = [
    //     self::ROLE_GENERAL => self::PUBLIC_DB_FIELDS,
    //     self::ROLE_PLAYER_OR_THE_GM => self::PUBLIC_DB_FIELDS + self::LIMITED_DB_FIELDS,
    //     self::ROLE_ADMIN => self::PUBLIC_DB_FIELDS + self::LIMITED_DB_FIELDS + self::ADMIN_DB_FIELDS,
    // ];


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
    public function findPublicEventById(int $id): Event
    {
        $fields = self::PUBLIC_DB_FIELDS;
        if ($this->siteConfiguration['allow']['see_location']) {
            $fields = array_merge($fields, self::COND_DB_FIELDS);
        }

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
    public function findById(int $id): EventPrivate
    {
        $fields = array_merge(self::PUBLIC_DB_FIELDS, self::LIMITED_DB_FIELDS);
        if ($this->siteConfiguration['allow']['see_location']) {
            $fields = array_merge($fields, self::COND_DB_FIELDS);
        }

        $sql = 'select '.join(',', $fields).' from ucon_event where id_event=?';
        $result = $this->db->getAll($sql, [$id]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        if (count($result) == 0) {
            throw new OutOfBoundsException(sprintf('Event with id %d does not exist', $id, 0));
        }


        // map the data into the API model object
        return $this->createPrivateEvent($result[0]);
    }

    /** retrieve a list of PrivateEvents events according to their Ids */
    public function findIndexedEvents(array $eventIds) : array
    {
        if (count($eventIds) == 0) {
            return [];
        }

        //validate that all the eventIds are numeric
        foreach ($eventIds as $id) {
            if (!is_numeric($id)) {
                throw new \Exception("findIndexedEvents invalid Id provided $id");
            }
        }

        $fields = array_merge(self::PUBLIC_DB_FIELDS, self::LIMITED_DB_FIELDS);
        if ($this->siteConfiguration['allow']['see_location']) {
            $fields = array_merge($fields, self::COND_DB_FIELDS);
        }

        $sql = 'select '.join(',', $fields).' from ucon_event where id_event in ('.join(',',$eventIds).')';
        $result = $this->db->getArray($sql);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: \n$sql\n".$this->db->ErrorMsg());
        }

        // map the data into the API model object
        $events = [];
        foreach ($result as $row) {
            $id = $row['id_event'];
            $events[$id] = $this->createPrivateEvent($row);
        }

        return $events;
    }

    /** return a list of events belonging to the specified GM */
    public function findCurrentPrivateEventsByGM(int $idGm) : array
    {
        $fields = array_merge(self::PUBLIC_DB_FIELDS, self::LIMITED_DB_FIELDS);
        if ($this->siteConfiguration['allow']['see_location']) {
            $fields = array_merge($fields, self::COND_DB_FIELDS);
        }

        $idConvention = $this->siteConfiguration['gcs']['year'];


        $sql = 'select '.join(',', $fields).' from ucon_event where id_gm=? and id_convention=?';
        $result = $this->db->getAll($sql, [$idGm, $idConvention]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

// echo "<pre>\n".print_r($result,true)."\n";

        // map the data into the API model object
        $events = [];
        foreach ($result as $row) {
// echo "<pre>\n".print_r($row,true)."\n";
            $events[] = $this->createPrivateEvent($row);
        }

        return $events;
    }

    private function createPublicEvent(array $state, Event $e = null) : Event
    {

        // validate required fields
        $required = self::PUBLIC_DB_FIELDS;
        if ($this->siteConfiguration['allow']['see_location']) {
            $required = array_merge($required, self::COND_DB_FIELDS);
        }

        foreach ($required as $k) {
          if (!array_key_exists($k, $state)) {
            throw new \Exception("Event data missing required field $k ".print_r($state, true));
          }
        }

        if ($e == null) {
            $e = new Event();
        }
        $e->id = (int) $state['id_event'];
        $e->conventionId = (int) $state['id_convention'];
        $e->game = $state['s_game'];
        $e->title = $state['s_title'];

        $e->maxplayers = (int) $state['i_maxplayers'];
        $e->minplayers = (int) $state['i_minplayers'];
        $e->cost = (float) $state['i_cost'];
        $e->exper = $state['e_exper'];
        $e->complex = $state['e_complex'];

        // // TODO Validate
        $e->day = ucfirst(strtolower(''.$state['e_day']));
        $e->time = (float) $state['i_time'];
        $e->duration = (float) $state['i_length'];
        if (is_numeric($e->time) && $e->time > 0) {
            $e->endtime = $e->time + $e->duration;
        }

        // TODO check that these are in the correct order
        $d1 = $state['s_desc_web'];
        $d2 = $state['s_desc'];

        if (strlen($d1)>strlen($d2)) {
            $e->desclong = $d1;
            $e->descshort = $d2;
        } else {
            $e->desclong = $d2;
            $e->descshort = $d1;
        }

        // required fields
        $e->gm = $this->memberRepository->findPublicMemberById((int)$state['id_gm']);
        $e->category = $this->categoryRepository->findById((int)$state['id_event_type']);

        // TODO add tags

        // TODO allow this to be null depending on configuration
        if ($this->siteConfiguration['allow']['see_location']) {
            $e->table = isset($state['s_table']) ? $state['s_table'] : '';
            if (isset($state['id_room'])) {
                try {
                    $e->room = $this->roomRepository->findById((int)$state['id_room']);
                } catch (OutOfBoundsException $ex) {
                    // ok to not have a room assigned
                }
            }
        }

        return $e;
    }

    private function createPrivateEvent(array $state) : EventPrivate
    {
        // validate required fields
        $required = self::LIMITED_DB_FIELDS;
        foreach ($required as $k) {
          if (!array_key_exists($k, $state)) {
            throw new \Exception("Event data missing required field $k");
          }
        }

        $e = $this->createPublicEvent($state, new EventPrivate);

        // add VTT fields visible to GMs and players
        $e->vttLink = $state['s_vttlink'];
        $e->vttInfo = $state['s_vttinfo'];

        // only show schedule information if the event is approved
        if (!$state['b_approval']) {
            $e->day = null;
            $e->time = null;
            $e->endtime = null;
        }

        // TODO Validate

        return $e;
    }

}


