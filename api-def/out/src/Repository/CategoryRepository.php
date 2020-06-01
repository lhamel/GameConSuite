<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Category;


/**
 * This class is situated between Entity layer (class Post) and access object layer (Persistence).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 */
class CategoryRepository
{
    protected $db;

    private $publicFields = ['id_event_type', 's_abbr', 's_type', 'i_order'];

    private $cache;

    public function __construct(\ADOConnection $db)
    {
        $this->db = $db;
    }

    // public function generateId(): PostId
    // {
    //     return PostId::fromInt($this->persistence->generateId());
    // }

    /** Retrieve the Event by its Event Id */
    public function findById(int $id): Category
    {
        $this->populateCache();

        if (array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }

        throw new OutOfBoundsException(sprintf('EventType with id %d does not exist', $id, 0));

        // $sql = 'select '.join(',', $this->publicFields).' from ucon_event_type where id_event_type=?';
        // $result = $this->db->getAll($sql, [$id]);
        // if (!is_array($result)) {
        //     throw new \Exception("SQL Error: ".$db->ErrMsg());
        // }

        // if (count($result) == 0) {
        //     throw new OutOfBoundsException(sprintf('EventType with id %d does not exist', $id, 0));
        // }

        // // map the data into the API model object
        // $arr = $result[0];
        // $category = new \OpenAPIServer\Model\Category((int) $arr['id_event_type'], $arr['s_abbr'], $arr['s_type'], (int) $arr['i_order']);

        // $this->cache[$id] = $category;

        // return $category;
    }

    private function populateCache() {
        if (isset($this->cache)) {
            return;
        }

        $sql = 'select '.join(',', $this->publicFields).' from ucon_event_type';
        $result = $this->db->getAll($sql);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

        foreach($result as $arr)
        {
            $id = (int)$arr['id_event_type'];
            $category = new \OpenAPIServer\Model\Category($id, $arr['s_abbr'], $arr['s_type'], (int) $arr['i_order']);
            $this->cache[$id] = $category;
        }

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


