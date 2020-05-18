<?php
require_once '../../../inc/inc.php';
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
$location = 'admin/gcs/schedule/browse.php';
require_once INC_PATH.'layout/adminmenu.php';

include '_tabs.php';


require_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'resources/cart/constants.php';


$location = 'admin/gcs/schedule/browse.php';
$title = $config['gcs']['admintitle']." - Browse Events";


$paramDay = isset($_REQUEST['day']) ? $_REQUEST['day'] : '';
$paramCategory = isset($_REQUEST['category']) ? $_REQUEST['category'] : '';
$paramAges = isset($_REQUEST['ages']) ? $_REQUEST['ages'] : '';
$paramTags = isset($_REQUEST['tags']) ? $_REQUEST['tags'] : '';
$paramRoom = isset($_REQUEST['room']) ? $_REQUEST['room'] : '';
$paramApproval = isset($_REQUEST['approval']) ? $_REQUEST['approval'] : '';
// $paramDay = isset() ?  : '';


$actions = array('list'=>basename(__FILE__).'?day='.$paramDay.'&category='.$paramCategory.'&room='.$paramRoom.'&year='.$year.'&approval='.$paramApproval.'&ages='.$paramAges.'&tags='.$paramTags,
                 'filterDay'=>basename(__FILE__).'?day=',
                 'filterCategory'=>basename(__FILE__).'?category=',
                 'detail'=>'../event/index.php?id_event=',
                 'navigateMember'=>'../member/index.php?id_member=',
                 'showExpanded'=>(isset($_GET['expanded']) ? true : false),
                );


require_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'db/db.php';

$tagSql = <<< EOD
select * from ucon_tag where (not tag="") and id_tag in
  (select id_tag from ucon_event_tag where id_event in
    (select id_event from ucon_event where id_convention={$config['gcs']['year']}))
EOD;
$tags = $db->getAssoc($tagSql);
if ($tags === false) { echo "SQL Error (browse.php)".$db->ErrorMsg(); exit; }

$filters = array(
  'day' => array(
    'label'=>'Day',
    'options'=>$constants['events']['days'],
  ),
  'category' => array(
    'label'=>'Category',
    'options'=>$constants['events']['event_types'],
  ),
  'room' => array(
    'label'=>'Room',
    'options'=>$constants['events']['rooms'],
  ),
  'tags' => array(
    'label'=>'Tag',
    'options'=>$tags
  ),
  'year' => array(
    'label'=>'Year',
    'options'=>array_reverse(array_combine(range(2002,$config['gcs']['year']),range(2002,$config['gcs']['year'])), true),
    'noall'=>true,
    'default'=>$year,
  ),
  'approval' => array(
    'label'=>'Approved',
    'options'=>array('1'=>'Approved','0'=>'Unapproved'),
  ),
  'ages'=> array(
     'label'=>'Ages',
     'options'=>$constants['events']['agesNoBlank'],
  ),
);

// generate a URL with the other filter parameters
foreach ($filters as $k=>$filter) {
  $otherFilters = $filters;
  unset($otherFilters[$k]);
  $otherParams = array_keys($otherFilters);
  $fixed = '';
  foreach ($otherParams as $param) {
    $fixed .= '&'.$param.'='. @$_GET[$param];
  }
  $filters[$k]['fixed'] = $fixed;
}




if (count($_GET)==0) {
  // render the event results
  $smarty->assign('config', $config);
  $smarty->assign('constants', $constants);
  $smarty->assign('showResults', false);
  $smarty->assign('actions', $actions);
  $smarty->assign('filters', $filters);
  $content = $smarty->fetch('gcs/reg/browse2.tpl');

  // render the page
  $smarty->assign('content', $content);
  $smarty->display('base.tpl');
  exit;
}

//include 'session.php';
require_once INC_PATH.'/db/db.php';


// find all events to include in the page
$sql = <<< EOD
  select E.*, ET.*, R.*, concat(M.s_fname, " ", M.s_lname) as gamemaster,
    (i_time+i_length) as endtime 
  from ucon_member as M, ucon_event_type as ET, ucon_event as E
    left join ucon_room as R on R.id_room=E.id_room
  where id_convention=$year 
    and E.id_gm=M.id_member
    and E.id_event_type=ET.id_event_type
EOD;
if ($_GET['category'] && is_numeric($_GET['category'])) {
	$sql .= " and E.id_event_type=".$_GET['category'];
}
if ($_GET['day'] && isset($constants['events']['days'][$_GET['day']])) {
  $sql .= " and E.e_day='".$_GET['day']."'";
}
if ($_GET['room'] && isset($constants['events']['rooms'][$_GET['room']])) {
  $sql .= " and E.id_room='".$_GET['room']."'";
}
if ($_GET['tags'] && is_numeric($_GET['tags'])) {
  $sql .= " and E.id_event in (select id_event from ucon_event_tag where id_tag=".$_GET['tags'].")";
}
if ($_GET['ages'] && is_numeric($_GET['ages'])) {
  $sql .= " and E.i_agerestriction=".$_GET['ages'];
}
if ($_GET['approval'] && is_numeric($_GET['approval'])) {
  $sql .= " and E.b_approval=".$_GET['approval'];
}
$sql .= " order by ".(isset($_GET['order']) ? $_GET['order'] : "ET.s_type, E.e_day, E.i_time, E.s_number");
$rs = $db->Execute($sql);
if (!$rs) die("sql error: " . $db->ErrorMsg());
//$events = array();
foreach($rs as $k=>$record) {
  $id = $record['id_event'];
  $events[$id] = $record;

  // TODO if there is a person selected to whom we can add this event...
  if ($config['allow']['buy_events'])
  {
    $events[$id]['buy'] = array();
    if ($events[$id]['i_maxplayers'] > 0) {
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][false][false];
      $events[$id]['buy']['link'] = '../reg/cart.php?action=addTicket&id_event='.$id;
    } else {
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][true][false];
    }
  }
}
if (count($events)>0) {
  $id_events = implode(array_keys($events), ', ');

  // add information about how many tickets are sold
  $ticketSql = <<< EOD
    select s_subtype as id_event, sum(i_quantity) as tickets
    from ucon_order
    where s_type='Ticket'
      and id_convention=$year
      and s_subtype in ($id_events)
    group by s_subtype
    order by s_subtype
EOD;
  $rs = $db->Execute($ticketSql);
  foreach($rs as $record) {
    $id = $record['id_event'];
    $events[$id]['tickets'] = $record['tickets'];

    // is this event sold out?
    $full = ($record['tickets'] >= $events[$id]['i_maxplayers']);

    // is this event already in the cart?
    $has = $config['allow']['buy_events'] && isset($_SESSION[UCART]) && $_SESSION[UCART]->HasTicket($id);

    // if the event is full then change the icon and don't use a link
    if ($full || $has) {
      $events[$id]['buy'] = array(); // erase previous information if it existed
      $events[$id]['buy']['icon'] = $constants['cart']['buy'][$full][$has];
    }
  }
}

$additionalFields = array( 's_room'=>'Room', 's_table'=>'Table');


// render the event results
$smarty->assign('REQUEST', $_REQUEST);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('events', $events);
$smarty->assign('actions', $actions);
$smarty->assign('filters', $filters);
$smarty->assign('showResults', true);
$smarty->assign('additional', $additionalFields);
$content = $smarty->fetch('gcs/reg/browse2.tpl');

// render the page
$smarty->assign('content', $content);
$smarty->display('base.tpl');

