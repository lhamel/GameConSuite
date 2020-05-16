<?php
require_once '../../inc/inc.php';

$year = $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
$location = 'gcs/events/browse.php';
require_once INC_PATH.'layout/menu.php';
include_once INC_PATH.'auth.php';

include '_tabs.php';

// This guard is provided for when preregistration is closed.
if (!$config['allow']['view_events']) {
    include '_closed.php';
    exit;
}


require_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'resources/cart/constants.php';

/*
	Browse Events
	
	This file allows events to be browsed as they would 
	in the convention book.
	
		<root>/events/db/browse.php?category=BG

	From here the member may navigate to:
	
		search.php		->	search for events
		cart.php		->	currently selected items

*/
$location = 'events/browse.php';
$title = $config['gcs']['sitetitle'] . ' - Browse Events';

$actions = array('list'=>basename(__FILE__),
                 'filterDay'=>basename(__FILE__).'?day=',
                 'filterCategory'=>basename(__FILE__).'?category=',
                 'filterTag'=>basename(__FILE__).'?tag=',
                // 'detail'=>'view.php?id='
                 'navigateMember'=>'search.php?search=',
                );




$tagSql = <<< EOD
select * from ucon_tag where (not tag="") and id_tag in 
  (select id_tag from ucon_event_tag where id_event in 
    (select id_event from ucon_event where id_convention={$config['gcs']['year']}))
EOD;
$tags = $db->getAssoc($tagSql);
if ($tags === false) { echo "SQL Error (browse.php)".$db->ErrorMsg(); exit; }
$smarty->assign('tags', $tags);

$year = $config['gcs']['year'];
require_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'db/db.php';

if (count($_GET)==0) {
  // render the event results
  $smarty->assign('config', $config);
  $smarty->assign('constants', $constants);
  $smarty->assign('showResults', false);
  $smarty->assign('actions', $actions);
  $smarty->assign('loginInfo', $associates->getLoginInfo());

  $content = '';
  $message = $config['allow']['message'];
  if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";
  $content .= $smarty->fetch('gcs/reg/browse.tpl');

  // render the page
  $smarty->assign('content', $content);
  $smarty->display('base.tpl');
  exit;
}

//include 'session.php';
require_once INC_PATH.'/db/db.php';


// find all events to include in the page
$sql = <<< EOD
  select E.*, ET.*, R.*, concat(M.s_fname, " ", M.s_lname) as gamemaster, M.s_lname, M.s_fname,
    i_agerestriction,
    (i_time+i_length) as endtime 
  from ucon_member as M, ucon_event_type as ET, ucon_event as E
    left join ucon_room as R on R.id_room=E.id_room
  where id_convention=$year 
    and E.id_gm=M.id_member
    and E.id_event_type=ET.id_event_type
    and (not (e_day='' OR i_time=0 OR isNull(e_day) OR isNull(i_time)))
    and b_approval=1
EOD;
if ($_GET['category'] && is_numeric($_GET['category'])) {
	$sql .= " and E.id_event_type=".$_GET['category'];
}
if ($_GET['day'] && isset($constants['events']['days'][$_GET['day']])) {
  $sql .= " and E.e_day='".$_GET['day']."'";
}
if ($_GET['ages'] && is_numeric($_GET['ages'])) {
  $sql .= " and E.i_agerestriction=".$_GET['ages'];
}
if ($_GET['tags'] && is_numeric($_GET['tags'])) {
  $sql .= " and E.id_event in (select id_event from ucon_event_tag where id_tag=".$_GET['tags'].")";
}
$sql .= " order by ET.s_type, e_day, i_time, E.s_number";
$rs = $db->Execute($sql);
if (!$rs) die("sql error: " . $db->ErrorMsg());
//$events = array();
foreach($rs as $k=>$record) {
  $id = $record['id_event'];
  $events[$id] = $record;

  // if the events are not read only, default to icon which allows purchase
  if ($config['allow']['buy_events'])
  {
    $title = $events[$id]['s_title'];
    $game = $events[$id]['s_game'];
    $title = $game . ($title && $game ? ': ' : ''). $title; 

    $events[$id]['buy'] = array();
    if ($events[$id]['i_maxplayers'] > 0) {
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][false][false];
    } else {
      // if the event has zero ticket, mark it sold out
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][true][false];
    }

    $title=str_replace("'", "\'", $title);
    $events[$id]['buy']['link'] = "javascript:showEventTicketDialog($id, 'Event: $title','','_add.php?&action=addItem&itemId=$id')";//'../reg/cart.php?action=addTicket&id_event='.$id;
  }
}
if (isset($events) && count($events)>0) {
  $id_events = implode(array_keys($events), ', ');

  // this is where we add information about how many tickets are sold
if (!$config['allow']['live_data']) { #prereg
  $ticketSql = <<< EOD
    select s_subtype as id_event, sum(i_quantity) as tickets
    from ucon_order
    where s_type='Ticket'
      and id_convention=?
      and s_subtype in ($id_events)
    group by s_subtype
    order by s_subtype
EOD;
} else {
  $ticketSql = <<< EOD
select subtype as id_event, sum(TI.quantity) as tickets
from ucon_transaction_item as TI, ucon_item as I
where itemtype='Ticket'
  and TI.barcode=I.barcode
  and year=?
  and subtype in ($id_events)
group by subtype
order by subtype
EOD;
}
  $rs = $db->Execute($ticketSql, array($year));
  if ($rs === false) { echo 'SQL Error: '.$db->ErrorMsg(); exit;}
  foreach($rs as $record) {
    $id = $record['id_event'];
    $events[$id]['tickets'] = $record['tickets'];

    // is this event sold out?
    $full = ($record['tickets'] >= $events[$id]['i_maxplayers']);

    // is this event already in the cart?
    $has = $config['allow']['buy_events'] && isset($_SESSION[UCART]) && $_SESSION[UCART]->HasTicket($id);

    // if the event is full then change the icon and don't use a link
    if ($full || $has) {
      //$events[$id]['buy'] = array(); // erase previous information if it existed
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][$full][$has];
    }
  }
}

if ($auth->isLogged()) {
  // get envelopes for the current logged-in user
  $members = $associates->listAssociates();
} else {
  $members = array();
}
foreach ($members as $id => $v) {
  $first = $members[$id]['s_fname'];
  $last = $members[$id]['s_lname'];
  $full = $first . ($first && $last ? ' ' : '') . $last;
  $members[$id]['name'] = $full;
  $members[$id]['radio'] = '<span id="mem_'.$id.'">buy</span>';
}
$smarty->assign('members', $members);


// required for shopping cart images
$smarty->assign('cart_soldout', array(
                                  0=>$constants['cart']['buy'][true][false],
                                  1=>$constants['cart']['buy'][true][true]
                                ));
$smarty->assign('cart_avail', array(
                                  0=>$constants['cart']['buy'][false][false],
                                  1=>$constants['cart']['buy'][false][true]
                                ));

// render the event results
$smarty->assign('REQUEST', isset($_REQUEST) ? $_REQUEST : array());
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('events', isset($events) ? $events : array());
$smarty->assign('actions', $actions);
$smarty->assign('showResults', true);
$smarty->assign('loginInfo', $associates->getLoginInfo());

$content = '';
$message = $config['allow']['message'];
if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";
$content .= $smarty->fetch('gcs/reg/browse.tpl');

// render the page
$smarty->assign('content', $content);
$smarty->display('base.tpl');

