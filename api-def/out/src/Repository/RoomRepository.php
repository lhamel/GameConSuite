<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Room;


/**
 * This class is situated between Entity layer (class Post) and access object layer (Persistence).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 */
class RoomRepository
{
    protected $db;

    public function __construct(\ADOConnection $db)
    {
        $this->db = $db;
    }

    // public function generateId(): PostId
    // {
    //     return PostId::fromInt($this->persistence->generateId());
    // }

    /** Retrieve the Event by its Event Id */
    public function findById(int $id): Room
    {
        $sql = "select id_room, s_room from ucon_room where id_room=?";
        $result = $this->db->getAll($sql, [$id]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

        if (count($result) == 0) {
            throw new OutOfBoundsException(sprintf('Room with id %d does not exist', $id, 0));
        }

        // map the data into the API model object
        $arr = $result[0];
        $room = new \OpenAPIServer\Model\Room((int)$arr['id_room'], $arr['s_room']);
        return $room;
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


