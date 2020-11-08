<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
include_once (INC_PATH.'db/db.php');
include INC_PATH.'resources/event/constants.php';

$idEvent = isset($_REQUEST['id_event']) ? $_REQUEST['id_event'] : null;
$reason = isset($_REQUEST['reason']) ? $_REQUEST['reason'] : '';


// TODO move this securely into API

// check authorization
// - assumed because this is in admin interface (not true for API)


//
// get the event
$sql = "select * from ucon_event where id_event=? and id_convention=?";
$params = [$idEvent, $year];
$events = $db->getAll($sql, $params);
if (!is_array($events)) { echo "SQL Error: " . $db->ErrorMsg() . "\n\n$sql"; exit; }

if (count($events) == 0) {
    http_response_code(404);
    echo "Event not found";
    exit;
}

if (count($events) != 1) { echo "Bad Query, expected 1 result: " . "\n\n$sql"; exit; }
$event = $events[0];


$game = $event['s_game'];
$title = $event['s_title'];
$originalTitle = $game.( ($game && $title) ? ': ' : '').$title;


//
// update the title, set # players to 0, remove room and table assignment

// - Add cancelled to the title of the event if it's not already there
$CANCELLED = ' - CANCELLED';
if (strpos($title, $CANCELLED) === false && strpos($game, $CANCELLED) === false) {
    if ($title != '') {
        $title .= $CANCELLED;
    } else {
        $game .= $CANCELLED;
    }
}

$sql = "update ucon_event set s_game=?, s_title=?, i_maxplayers=0, i_minplayers=0, id_room=null, s_table=null where id_event=?";
$params = [$game, $title, $idEvent];
$ret = $db->execute($sql, $params);
if (!is_array($events)) { echo "SQL Error: " . $db->ErrorMsg() . "\n\n$sql"; exit; }


//
// - Add tag "cancelled" to the event

// find the cancelled tag
$sql = "select id_tag from ucon_tag where tag=?";
$params = ['CANCELLED'];
$idTag = $db->getOne($sql, $params);
if ($idTag === false)  { echo "SQL Error: " . $db->ErrorMsg() . "\n\n$sql"; exit; }

// see if the tag has already been applied
$sql = "select id_event, id_tag from ucon_event_tag where id_event=? and id_tag=?";
$tags = $db->getAll($sql, [$idEvent, $idTag]);
if (!is_array($tags))  { echo "SQL Error: " . $db->ErrorMsg() . "\n\n$sql"; exit; }
$exists = (count($tags)>0);

if (!$exists) {
    $sql = "insert into ucon_event_tag set id_event=?, id_tag=?";
    $ret = $db->execute($sql, [$idEvent, $idTag]);
    if ($ret===false)  { echo "SQL Error: " . $db->ErrorMsg() . "\n\n$sql"; exit; }
}



// TODO!!! - Update the cash register

// - Find & email the ticketed players

// find the players
$sql = 'select M.id_member, M.s_email from ucon_order as O, ucon_member as M where O.id_member=M.id_member and id_convention=? and s_subtype=?';
$tickets = $db->getAll($sql, [$year, $idEvent]);
if (!is_array($tickets))  { echo "SQL Error: " . $db->ErrorMsg() . "\n\n$sql"; exit; }

$emails = [];
foreach($tickets as $t) {
    $emails[] = $t['s_email'];
}


// instead of sending the email, we prepare a template which can be set to the players
echo "<pre>Emails:\n";
if (count($emails) == 0) { echo "none\n"; }
foreach($emails as $e) {
    echo $e."\n";
}

if ($reason != '') {
    $reason = "\n".$reason."\n";
}


echo <<< EOD



Dear Ticket Holder,

We regret to inform you that event 

\t#{$idEvent} {$originalTitle}

has been regretfully cancelled.  Your ticket may be returned or exchanged.  At
At your convenience, please return to our website to update your ticket selection.

{$config['gcs']['baseUrl']}
{$reason}
Sincerely,
U-Con Events Team







Note: this message is only appropriate for use during prereg.
</pre>
EOD;


  // $header = "Cc:".$cc."\r\n";
  // $header .="From:".$from."\r\n";

  // //mail($toEmail, $subject, $message, $header);
  // require_once INC_PATH.'mail.php';
  // gcs_mail($toEmail, $subject, $message, array('cc'=>$cc, 'from'=>$from));




