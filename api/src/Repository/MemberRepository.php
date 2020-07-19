<?php declare(strict_types=1);

namespace OpenAPIServer\Repository;

use OutOfBoundsException;
use OpenAPIServer\Model\Member;
use OpenAPIServer\Model\MemberPrivate;


/**
 * This class is situated between Entity layer (class Post) and access object layer (Persistence).
 *
 * Repository encapsulates the set of objects persisted in a data store and the operations performed over them
 * providing a more object-oriented view of the persistence layer
 *
 * Repository also supports the objective of achieving a clean separation and one-way dependency
 * between the domain and data mapping layers
 */
class MemberRepository
{
    protected $db;

    const PUBLIC_DB_FIELDS = ['id_member', 's_lname', 's_fname', 's_group'];
    const PRIVATE_DB_FIELDS = ['s_email', 's_addr1', 's_addr2', 's_city', 's_state', 's_zip', 's_international', 's_phone'];

    const FIELD_MAP = ['id_member'=>'id', 's_lname'=>'lastName', 's_fname'=>'firstName', 's_group'=>'groupName', 's_email'=>'email', 's_addr1'=>'addr1', 's_addr2'=>'addr2', 's_city'=>'city', 's_state'=>'state', 's_zip'=>'zip', 's_international'=>'international', 's_phone'=>'phone'];

    /** Array of public members cached for performance purpose */
    private $cachePublicMembers = [];


    public function __construct(\ADOConnection $db)
    {
        $this->db = $db;
    }

    // public function generateId(): PostId
    // {
    //     return PostId::fromInt($this->persistence->generateId());
    // }



    /** Retrieve the Event by its Event Id */
    public function findPublicMemberById(int $id): Member
    {
        if (array_key_exists($id, $this->cachePublicMembers)) {
            return $this->cachePublicMembers[$id];
        }

        $sql = 'select '.join(',', self::PUBLIC_DB_FIELDS).' from ucon_member where id_member=?';
        $result = $this->db->getAll($sql, [$id]);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$db->ErrMsg());
        }

        if (count($result) == 0) {
            throw new OutOfBoundsException(sprintf('Member with id %d does not exist', $id, 0));
        }

        // map the data into the API model object
        $m = $result[0];
        $member = new Member((int)$m['id_member'], $m['s_lname'], $m['s_fname'], $m['s_group']);
        return $member;
    }

    public function findPrivateMembersByIds(array $memberIds) {
        // validate the list contains only members
        foreach ($memberIds as $id) {
            if (!is_numeric($id)) {
                throw new \Exception('All identifiers must be numeric.  Error: ('.$id.')');
            }
        }

        $fields = array_merge(self::PUBLIC_DB_FIELDS, self::PRIVATE_DB_FIELDS);
        $idList = join(',',$memberIds);

        $sql = 'select '.join(',', $fields)." from ucon_member where id_member in ($idList)";
        $result = $this->db->getAll($sql);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        // map the data into the API model object
        $members = [];
        foreach ($result as $m) {
            $mId = (int)$m['id_member'];
            $newMember = new Member($mId, $m['s_lname'], $m['s_fname'], $m['s_group']);
            // foreach ($m as $k => $v) {
            //     $field = self::FIELD_MAP[$k];
            //     $newMember->$field = $v;
            // }
            $members[] = $newMember;
        }

        return $members;
    }


    // /** Retrieve the Event by its Event Id */
    // public function findById(int $id): Member
    // {
    //     // pull data from the roomRepository
    //     $fields = ['id_member', 's_lname', 's_fname', 's_email', 's_group'];

    //     $sql = 'select '.join(',', $fields).' from ucon_member where id_member=?';
    //     $result = $this->db->getAll($sql, [$id]);
    //     if (!is_array($result)) {
    //         throw new \Exception("SQL Error: ".$db->ErrMsg());
    //     }

    //     if (count($result) == 0) {
    //         throw new OutOfBoundsException(sprintf('Member with id %d does not exist', $id, 0));
    //     }

    //     // map the data into the API model object
    //     $m = $result[0];
    //     $member = new \OpenAPIServer\Model\Member((int)$m['id_member'], $m['s_lname'], $m['s_fname'], $m['s_group']);
    //     return $member;
    // }

    /** Load the cache with a bulk of members to prevent bulk calls to DB */
    public function cachePublicMembers(array $gmIds)
    {
        $idList = join(',',$gmIds);

        $sql = 'select '.join(',', self::PUBLIC_DB_FIELDS)." from ucon_member where id_member in ($idList)";
        $result = $this->db->getAll($sql);
        if (!is_array($result)) {
            throw new \Exception("SQL Error: ".$this->db->ErrorMsg());
        }

        // map the data into the API model object
        foreach ($result as $m) {
            $mId = (int)$m['id_member'];
            $member = new Member($mId, $m['s_lname'], $m['s_fname'], $m['s_group']);
            $this->cachePublicMembers[$mId] = $member;
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


