<?php

// FIXME there is probably a better way to include this using autoload
require_once __DIR__.'/../inc/auth.php';
require_once __DIR__.'/../config/config.php';


// capture action from parameters
if (!isset($_REQUEST['action'])) {
    header('HTTP/1.1 400 BAD REQUEST');
    echo "Action not specified";
    exit;
}
$action = $_REQUEST['action'];

// parse the body into a json object
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array


// check action
switch($action) {
    case "login":

        if ($input == null) {
            header('HTTP/1.1 400 BAD REQUEST');
            echo "Invalid JSON";
           exit;
        }

        if ($inputJSON=="") {
            header('HTTP/1.1 400 BAD REQUEST');
            echo "Auth action requires body";
            exit;
        }
        $user = $input['user'];
        $pass = $input['pass'];
        //if (isset($input['email'])) { $user = $input['email']; }

        // test credentials
        $ret = $auth->login($user, $pass, 1);

        if ($ret['error']) {
            header('HTTP/1.1 401 UNAUTHORIZED');
            echo json_encode($ret);
            // TODO add information about how to resolve login errors
            exit;
        }

        // check to see if there are associated members
        $hash = $ret['hash'];
        $uid = $auth->getSessionUID($hash);
        $members = $associates->listAssociates($uid);

        // clean up the members to remove unneeded fields
        $mems = array();
        foreach ($members as $k => $v) {
            $m = array("id_member"=>$v['id_member']);
            $m['s_lname'] = $v['s_lname'];
            $m['s_fname'] = $v['s_fname'];
            $m['s_email'] = $v['s_email'];
            $mems[] = $m;
        }
        $ret['members'] = $mems;

        //$return['encryptpass'] = openssl_encrypt($pass);
        //$return['methods'] = openssl_get_cipher_methods();

        echo json_encode($ret);
        exit;

    case "logout":
        $ret = $auth->logout($auth->getCurrentSessionHash());
        echo json_encode($ret);
        exit;

    case "schedule":

        $members = $associates->listAssociates();
        $mIds = join(",", array_keys($members));

// TODO abstract this
$sql = <<< EOD
select O.id_member, 
  E.id_event, E.s_title, E.s_game, s_desc, s_desc_web, i_minplayers, i_maxplayers, i_agerestriction, e_exper, e_complex, i_length, e_day, i_time, s_room, s_table, i_cost,
      GM.s_fname, GM.s_lname
from ucon_order as O, ucon_member as GM, ucon_event as E left join ucon_room as R on (E.id_room=R.id_room)
where O.id_convention=?
  and O.s_type="Ticket" 
  and O.s_subtype = E.id_event
  and O.id_member in ($mIds)
  and E.id_gm = GM.id_member
order by e_day, i_time, id_member
EOD;
$tickets = $db->getAll($sql, array($config['gcs']['year']));
if (!is_array($tickets)) {
    echo "SQL error: ".$db->ErrorMsg()."<br>$sql";
}

        // initialize the array of members
        $returnVal = array();
        foreach ($members as $k => $v) {
            $returnVal[$k] = array();
        }

        // add an entry for each ticket
        foreach ($tickets as $v) {
            $id_member = $v['id_member'];
            unset($v['id_member']);
            $id_event = $v['id_event'];
            $returnVal[$id_member][$id_event] = $v;
        }

        echo json_encode($returnVal);
        exit;

}

header('HTTP/1.1 400 BAD REQUEST');
echo "Action unknown";
exit;

