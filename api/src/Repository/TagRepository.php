<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Tag;


/**
 * This class is situated between Entity layer (class Post) and access object layer (Persistence).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 */
class TagRepository
{
    protected $db;

    //private $publicFields = ['id_event_type', 's_abbr', 's_type', 'i_order'];

    private $cache;
    private $siteConfiguration;

    public function __construct(\ADOConnection $db)
    {
        $this->db = $db;
        $this->siteConfiguration = $GLOBALS['config']; // TODO pass through dependency injection
    }

    // public function generateId(): PostId
    // {
    //     return PostId::fromInt($this->persistence->generateId());
    // }

    public function getCurrentTagsInUse(): array
    {
        $this->populateCache();
        return $this->cache;       
    }

    /** Retrieve the Event by its Event Id */
    public function findById(int $id): Tag
    {
        $this->populateCache();

        if (array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }

        throw new OutOfBoundsException(sprintf('EventType with id %d does not exist', $id, 0));
    }

    private function populateCache() {
        if (isset($this->cache)) {
            return;
        }

        $conventionId = $this->siteConfiguration['gcs']['year'];

        $sql = 'select T.id_tag, T.tag from ucon_tag T, ucon_event_tag ET, ucon_event E '
             . 'where E.id_convention=? and E.id_event=ET.id_event and ET.id_tag=T.id_tag '
             . 'order by T.tag';
        $result = $this->db->getAll($sql, [$conventionId]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

        $this->cache = [];
        foreach($result as $arr)
        {
            $id = (int)$arr['id_tag'];
            $this->cache[$id] = $arr['tag'];
        }
    }

}


