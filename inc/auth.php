<?php
require_once(__DIR__.'/../config/config.php');
require_once __DIR__.'/../vendor/autoload.php';

use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

$dbh = new PDO($config['db']['auth_db_conn'], $config['db']['auth_db_user'], $config['db']['auth_db_pass']);

$authconfig = new PHPAuthConfig($dbh);
$auth = new PHPAuth($dbh, $authconfig);


/**
 * Provide a convenient method for checking authorized members
 */
class Associates
{

    /**
     * @var $db
     */
    protected $db;

    /**
     * @var PHPAuth $auth
     */
    protected $auth;

    /**
     * Initiates the Associates checks
     *
     * @param $db
     * @param $auth
     */
    public function __construct($db, PHPAuth $auth)
    {
        $this->db = $db;
        $this->auth = $auth;
    }

    /**
     * List the member associates this user is authorized to view
     *
     * @param $uid the user ID (default: current user)
     */
    public function listAssociates($uid = NULL) {
        $db = $this->db;

        // if not specified, use the current user
        if ($uid == NULL) {
            $uid = $this->auth->getCurrentUser()['uid'];
        }

        $sql = "select AM.id_member as id2, M.id_member, M.s_lname, M.s_fname, s_email \n"
             . " from ucon_auth_member as AM, ucon_member as M \n"
             . " where M.id_member=AM.id_member and AM.uid=?";
        $result = $db->getAssoc($sql, array($uid));
        if (!is_array($result)) { 
            echo "SQL: $sql<br>SQL Error: ".$db->errorMsg();
            exit;
        }
        return $result;
    }

    /**
     *
     */
    public function listAuthorizations($id_member) {
        $db = $this->db;

        // if not specified, use the current user
        if ($id_member == NULL || $id_member <= 0) {
            return array(); // empty array
        }

        $sql = "select AM.uid as id2, AM.uid as uid \n"
             . " from ucon_auth_member as AM \n"
             . " where AM.id_member=?";
        $result = $db->getAssoc($sql, array($id_member));
        if (!is_array($result)) {
            echo "SQL: $sql<br>SQL Error: ".$db->errorMsg();
            exit;
        }

        global $auth;
        foreach ($result as $k=>$uid) {
          $result[$k] = $auth->getUser($uid);
        }
        return $result;
    }

    /**
     * Check if this user is authorized to view this member
     *
     * @param $id_member the member id to be viewed
     * @param $uid the user ID (default: current user)
     */
    public function checkAuth($id_member, $uid = NULL) {
        $associates = $this->listAssociates($uid);
        return ($id_member > 0 && isset($associates[$id_member]));
    }

    /**
     * Check if this user is authorized to view this member
     *
     * @param $id_member the member id to be viewed
     * @param $uid the user ID (default: current user)
     */
    public function blockAuth($id_member, $uid = NULL) {
        if (!$this->checkAuth($id_member, $uid)) {
          header('HTTP/1.0 403 Forbidden');
          echo ("Forbidden: not authorized to view this member");
          exit;
        }
    }

    /**
     * Associate the selected member with the UID
     */
    public function associate($id_member, $uid) {

        $params = array($uid, $id_member);

        // remove any previous associations
        $sql = "delete from ucon_auth_member where uid=? and id_member=?";
        $ok = $this->db->execute($sql, $params);
        if (!$ok) { echo "SQL Error (associate1): ".$this->db->ErrorMsg(); }

        // add the new association
        $sql = "insert into ucon_auth_member set uid=?, id_member=?";
        $ok = $this->db->execute($sql, $params);
        if (!$ok) { echo "SQL Error (associate2):".$this->db->ErrorMsg(); }

    }

    /**
     * Remove association for the selected member with the UID
     */
    public function disassociate($id_member, $uid) {

        // remove any previous associations
        $params = array($uid, $id_member);
        $sql = "delete from ucon_auth_member where uid=? and id_member=?";
        $this->db->execute($sql, $params);

    }

    public function getLoginInfo() {
        $uid = $this->auth->getCurrentUser()['uid'];
        $members = $this->listAssociates($uid);
        $ticketSelection = array();

        foreach ($members as $id=>$v) {
            $ticketSelection[$id] = array();
        }

        if(count($members)>0) {
            $db = $this->db;
            global $year;
            $memberIds = implode(',', array_keys($members));
            $sql = "select id_member as id, s_subtype as tix, i_quantity from ucon_order "
                  ."where id_member in ($memberIds) "
                  ."and id_convention=? "
                  ."and s_type='Ticket' ";
            $result = $db->getAll($sql, array($year));
            if (!is_array($result)) { echo "SQL Error (getLoginInfo): ".$db->ErrorMsg(); exit; }
//echo "<pre>$sql\n\n".print_r($result,1)."</pre>";exit;

            foreach ($result as $v) {
              $ticketSelection[$v['id']][$v['tix']] = $v['i_quantity'];
            }
        }

        $result = array('loggedin' => ($uid===NULL?0:1),
                        'members' => $members,
                        'ticketSelection' => $ticketSelection);
//echo "<pre>".print_r($result,1)."</pre>";exit;
        return $result;
    }

}

require_once __DIR__.'/../inc/db/db.php';
$associates = new Associates($db, $auth);

