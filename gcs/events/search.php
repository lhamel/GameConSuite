<?php
require_once '../../inc/inc.php';

$year = $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
$location = 'gcs/events/search.php';
require_once INC_PATH.'layout/menu.php';
include_once INC_PATH.'auth.php';
$title = $config['gcs']['sitetitle'] . ' - Browse Events';

include '_tabs.php';

// This guard is provided for when preregistration is closed.
if (!$config['allow']['view_events']) {
    include '_closed.php';
    exit;
}

$depth = DEPTH;

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

$actions = array('list'=>basename(__FILE__),
                 'filterDay'=>basename(__FILE__).'?day=',
                 'filterCategory'=>basename(__FILE__).'?category=',
                 //'detail'=>'view.php?id='
                 'navigateMember'=>'search.php?search=',
                );


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
  $content .= $smarty->fetch('gcs/reg/search.tpl');

  // render the page
  $smarty->assign('content', $content);
  $smarty->display('base.tpl');
  exit;
}

//include 'session.php';
require_once INC_PATH.'/db/db.php';

// TODO check that sort value is on a list
$sort = isset($_GET['sort']) ? 'order by '.$_GET['sort'] : 'order by e_day, i_time';
$sql = <<< EOD
select 
  CONCAT(s_fname, ' ', s_lname) as gamemaster,
  s_lname, s_fname, 
  id_event,
  s_title, s_game, 
  s_number, s_desc,
  e_day, i_time, (i_time+i_length) as endtime,
  i_cost,
  i_minplayers, i_maxplayers,
  e_exper, e_complex, i_agerestriction,
  s_table, R.s_room,
  ET.s_abbr, ET.s_type
#  s_title as title, s_game as game_system, 
#  s_number as event_number, s_desc as description,
#  e_day as day, i_time as start_time, (i_time+i_length) as end_time
from
  ucon_member as M, ucon_event_type as ET, ucon_event as E
  left join ucon_room as R on E.id_room=R.id_room
where
  id_convention=$year
  and M.id_member=E.id_gm
  and b_approval = 1
  and ET.id_event_type = E.id_event_type

  #and e_day = 
  #and start_time > 
  #and end_time > 
  and (s_lname like ?
    or s_fname like ?
    or s_title like ?
    or s_game like ?
    or s_desc like ?
    or s_number like ?)
  $sort
EOD;
//echo '<pre>'.print_r($sql,1).'</pre>';
// TODO allow to change sort

$search = $_GET['search'];
$parameters = array(
  "%$search%","%$search%","%$search%","%$search%","%$search%","%$search%",
);

$rs = $db->Execute($sql, $parameters);
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

/*
  {
    $events[$id]['buy'] = array();
    if ($events[$id]['i_maxplayers'] > 0) {
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][false][false];
      $events[$id]['buy']['link'] = '../reg/cart.php?action=addTicket&id_event='.$id;
    } else {
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][true][false];
    }
  }
*/
}
if (count($events)>0) {
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


//echo '<pre>'.print_r($constants,1).'</pre>';

// render the event results
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('events', $events);
$smarty->assign('actions', $actions);
$smarty->assign('showResults', true);
$smarty->assign('loginInfo', $associates->getLoginInfo());

$content = '';
$message = $config['allow']['message'];
if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";
$content .= $smarty->fetch('gcs/reg/search.tpl');

// render the page
$smarty->assign('content', $content);
$smarty->display('base.tpl');

