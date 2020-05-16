<?php
require_once '../inc/inc.php';

// possible actions
$year = (isset($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : $config['gcs']['year'];

require_once INC_PATH.'smarty.php';
$location = 'admin/index.php';
require_once INC_PATH.'layout/adminmenu.php';

$content = <<< EOD
<h1>{$config["gcs"]["admintitle"]} Dashboard</h1>

EOD;


require_once INC_PATH.'db/db.php';

$params = array($year);
$exh = $db->GetAssoc("select id_member, count(id_order) from ucon_order where s_type='Exhibit' and id_convention=? group by id_member", $params);

if (!is_array($exh)) { echo "SQL Error: ".$db->ErrorMsg(); }
$exh = array_keys($exh);
$exhIds = implode(',',$exh);
if ($exhIds == "") { $exhIds = "-1"; }


$lengthCalculation = "length(E.s_game)+length(E.s_title)+length(if(length(E.s_desc_web)>0,E.s_desc_web,E.s_desc))";

$stats = array(
  array(
    'label'=>"Total number of events for $year",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year",
    'breakoutsql'=>"select s_abbr as type, count(*) as count 
                    from ucon_event as E, ucon_event_type as ET 
                    where E.id_event_type=ET.id_event_type and id_convention=$year group by s_abbr"
  ),
  array(
    'label'=>"Needs <a href=\"gcs/submissions/unscheduled.php\">scheduling</a>",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year and (isNull(e_day) or isNull(i_time))",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and (isNull(e_day) or isNull(i_time))
                    group by s_abbr"
  ),
  array(
    'label'=>"Needs <a href=\"gcs/submissions/numbers.php\">event number</a>",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year and (isNull(s_number) || s_number='') and not (isNull(e_day) or isNull(i_time))",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and (isNull(s_number) || s_number='') and not (isNull(e_day) or isNull(i_time))
                    group by s_abbr"
  ),
  array(
    'label'=>"Needs <a href=\"gcs/submissions/unapproved.php\">approval</a>",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year and b_approval=0 and not (isNull(s_number) || s_number='') and not (isNull(e_day) or isNull(i_time))",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and b_approval=0 and not (isNull(s_number) || s_number='') and not (isNull(e_day) or isNull(i_time))
                    group by s_abbr"
  ),
  array(
    'label'=>"Needs <a href=\"gcs/publish/homeless.php\">room assignment</a>",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year and isNull(id_room)",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and isNull(id_room)
                    group by s_abbr"
  ),
  array(
    'label'=>"Needs <a href=\"gcs/publish/unedited.php\">copy edit</a>",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year and b_edited=0",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and b_edited=0
                    group by s_abbr"
  ),
  array(
    'label'=>"Events <a href=\"gcs/publish/long.php\">over length</a>",
    'sql'=>"select count(*) as count from ucon_event as E where id_convention=$year and $lengthCalculation>300",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and $lengthCalculation>300
                    group by s_abbr",
  ),
  array(
    'label'=>"Fully complete",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year and b_approval=1 and b_edited=1 and (not isNull(id_room)) and not (isNull(s_number) || s_number='') and not (isNull(e_day) or isNull(i_time))",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year and b_approval=1 and b_edited=1 and (not isNull(id_room)) and not (isNull(s_number) || s_number='') and not (isNull(e_day) or isNull(i_time))
                    group by s_abbr"
  ),
);

$stats2 = array(
  array(
    'label'=>"Total number of unmerged accounts running events",
    'sql'=>"select count(id_member) as count from (select distinct id_gm as id_member from ucon_event where id_convention=$year) as A",
    //'breakoutsql'=>"select s_abbr as type, count(*) as count
    //                from ucon_event as E, ucon_event_type as ET
    //                where E.id_event_type=ET.id_event_type and id_convention=$year group by s_abbr"
  ),
  array(
    'label'=>"Total number of unmerged accounts with items",
    'sql'=>"select count(id_member) as count from (select distinct id_member from ucon_order where id_convention=$year) as A",
  ),
  array(
    'label'=>"Total number of badges",
    'sql'=>"select sum(i_quantity) as count from ucon_order where s_type='BADGE' and id_convention=$year",
  ),
  array(
    'label'=>'Unreconciled Payments',
    'sql'=>"SELECT sum(f_amount) as count FROM ucon_incoming_paypal where d_timestamp like \"$year%\"  and b_used=0",
  ),
  array(
    'label'=>"Total $ amount of purchases (not vendors)",
    'sql'=>"select sum(i_quantity*i_price) as count from ucon_order where (not s_type='EXHIBIT') and id_convention=$year and i_price > 0",
  ),
  array(
    'label'=>"Total $ amount of payments (includes vendors)",
    'sql'=>"select -sum(i_quantity*i_price) as count from ucon_order where id_convention=$year and s_type=\"Payment\" and not (s_subtype like \"%discount%\")",
  ),
  array(
    'label'=>"Total $ amount of payments (excluding vendors)",
    'sql'=>"select -sum(i_quantity*i_price) as count from ucon_order where id_convention=$year and s_type=\"Payment\" and not (s_subtype like \"%discount%\") and id_member not in ($exhIds)",
  ),
  array(
    'label'=>"Total $ amount of vendor payments",
    'sql'=>"select -sum(i_quantity*i_price) as count from ucon_order where id_convention=$year and s_type=\"Payment\" and not (s_subtype like \"%discount%\") and id_member in ($exhIds)",
  ),
);

$stats3 = array(
  array(
    'label'=>"Preregistration Badges",
    'sql'=>"select sum(i_quantity) as count from ucon_order where id_convention=$year and s_type=\"Badge\"",
    'breakoutsql'=>"select s_subtype as type, sum(i_quantity) as count
                    from ucon_order 
                    where id_convention=$year and s_type=\"Badge\" group by s_subtype"
  ),
  array(
    'label'=>"Onsite Badges",
    'sql'=>"select sum(TI.quantity) as count from ucon_transaction_item as TI, ucon_item as I where year=$year and itemtype=\"Badge\" and not(subtype=\"Gamemaster Refund\") and TI.barcode=I.barcode",
    'breakoutsql'=>"select subtype as type, sum(TI.quantity) as count
                    from ucon_transaction_item as TI, ucon_item as I
                    where year=$year and itemtype=\"Badge\" and TI.barcode=I.barcode group by subtype"
  ),
);

$statsTicketSales = array(
  array(
    'label'=>"Possible Tickets",
    'sql'=>"select sum(i_maxplayers) as count
              FROM ucon_event_type as ET, ucon_event as E 
              WHERE E.id_convention=$year and E.id_event_type=ET.id_event_type",
    'breakoutsql'=>"select s_abbr as type, sum(i_maxplayers) as count
                    from ucon_event_type as ET, ucon_event as E 
                    where ET.id_event_type=E.id_event_type and E.id_convention=$year group by s_abbr"
  ),
  array(
    'label'=>"Preregistration Purchased Tickets",
    'sql'=>"select concat(sum(i_quantity),' (',round(100*sum(i_quantity)/sum,1),'%)') as count
           from ucon_event as E, ucon_order as O, (select sum(i_maxplayers) as sum from ucon_event where id_convention=$year) as MAXPLAYERS
           where O.id_convention=$year and O.s_type=\"Ticket\" and E.id_event=O.s_subtype",
    'breakoutsql'=>"select s_abbr as type, concat(sum(i_quantity),' (',round(100*sum(i_quantity)/sum, 1),'%)') as count
                    from ucon_event_type as ET, ucon_event as E, ucon_order as O
                      ,(select id_event_type, sum(i_maxplayers) as sum from ucon_event where id_convention=$year group by id_event_type) as MAXPLAYERS
                    where O.id_convention=$year and O.s_type=\"Ticket\" and E.id_event=O.s_subtype and ET.id_event_type=E.id_event_type 
                      and ET.id_event_type=E.id_event_type and MAXPLAYERS.id_event_type=E.id_event_type 
                    group by s_abbr"
  ),
  array(
    'label'=>"Prereg+Onsite Tickets",
    'sql'=>"select sum(TI.quantity) as count from ucon_transaction_item as TI, ucon_item as I where year=$year and itemtype=\"Ticket\" and TI.barcode=I.barcode",
    'breakoutsql'=>"select s_abbr as type, sum(TI.quantity) as count
                    from ucon_transaction_item as TI, ucon_item as I, ucon_event as E, ucon_event_type as ET 
                    where year=$year and itemtype=\"Ticket\" and TI.barcode=I.barcode and E.id_event=I.subtype and ET.id_event_type=E.id_event_type 
                    group by s_abbr"
  ),
  array(
    'label'=>"<a href=\"gcs/ops/popular.php\">Sold Out Events</a>",
    'sql'=>"select count(id_event) as count
            from (
              SELECT E.id_event, s_abbr, sum(i_quantity) as actual, E.i_maxplayers as maximum
              FROM ucon_order as T, ucon_event as E, ucon_event_type as ET
              WHERE T.id_convention=$year and T.s_type=\"Ticket\" and T.s_subtype=E.id_event and E.id_event_type=ET.id_event_type
                AND E.id_convention=T.id_convention
                AND E.i_maxplayers > 0
              GROUP BY E.id_event, E.i_maxplayers
              HAVING sum(i_quantity)>=maximum
            ) as SOLD_OUT_EVENTS",
    'breakoutsql'=>"select s_abbr as type, count(id_event) as count
                    from (
                      SELECT E.id_event, s_abbr, sum(i_quantity) as actual, E.i_maxplayers as maximum
                      FROM ucon_order as T, ucon_event as E, ucon_event_type as ET
                      WHERE T.id_convention=$year and T.s_type=\"Ticket\" and T.s_subtype=E.id_event and E.id_event_type=ET.id_event_type
                        AND E.id_convention=T.id_convention
                        AND E.i_maxplayers > 0
                      GROUP BY E.id_event, E.i_maxplayers
                      HAVING sum(i_quantity)>=maximum
                    ) as SOLD_OUT_EVENTS
                    group by s_abbr"
  ),
  array(
    'label'=>"<a href=\"gcs/ops/popular.php\">Oversold Events</a>",
    'sql'=>"select count(id_event) as count
            from (
              SELECT E.id_event, s_abbr, sum(i_quantity) as actual, E.i_maxplayers as maximum
              FROM ucon_order as T, ucon_event as E, ucon_event_type as ET
              WHERE T.id_convention=$year and T.s_type=\"Ticket\" and T.s_subtype=E.id_event and E.id_event_type=ET.id_event_type
                AND E.id_convention=T.id_convention
                AND E.i_maxplayers > 0
              GROUP BY E.id_event, E.i_maxplayers
              HAVING sum(i_quantity)>maximum
            ) as SOLD_OUT_EVENTS",
    'breakoutsql'=>"select s_abbr as type, count(id_event) as count
                    from (
                      SELECT E.id_event, s_abbr, sum(i_quantity) as actual, E.i_maxplayers as maximum
                      FROM ucon_order as T, ucon_event as E, ucon_event_type as ET
                      WHERE T.id_convention=$year and T.s_type=\"Ticket\" and T.s_subtype=E.id_event and E.id_event_type=ET.id_event_type
                        AND E.id_convention=T.id_convention
                        AND E.i_maxplayers > 0
                      GROUP BY E.id_event, E.i_maxplayers
                      HAVING sum(i_quantity)>maximum
                    ) as SOLD_OUT_EVENTS
                    group by s_abbr"
  ),
);

$statsEventAttend = array(
  array(
    'label'=>"Total number of events for $year",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year",
    'breakoutsql'=>"select s_abbr as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year group by s_abbr"
  ),
  array(
    'label'=>"Events That Ran",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and i_actual>0",
    'breakoutsql'=>"select s_abbr as type, count(id_event) as count 
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and i_actual>0 group by s_abbr"
  ),
  array(
    'label'=>"Events with no result data",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and isnull(i_actual) and NOT (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\")",
    'breakoutsql'=>"select s_abbr as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and isnull(i_actual) and NOT (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\") group by s_abbr"
  ),
  array(
    'label'=>"Events that did not run",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and i_actual=0 and NOT (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\")",
    'breakoutsql'=>"select s_abbr as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and i_actual=0 and not (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\") group by s_abbr"
  ),
  array(
    'label'=>"Events that were cancelled",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\")",
    'breakoutsql'=>"select s_abbr as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\") group by s_abbr"
  ),
);


$slotFormula = "concat(e_day,ceiling((i_time-if(i_time=20,7,8))/4))"; //CEILING((B14-IF(B14=20,7,8))/4,1)
$statsEventAttendBySlot = array(
  array(
    'label'=>"Total number of events for $year",
    'sql'=>"select count(*) as count from ucon_event where id_convention=$year",
    'breakoutsql'=>"select $slotFormula as type, count(*) as count
                    from ucon_event as E, ucon_event_type as ET
                    where E.id_event_type=ET.id_event_type and id_convention=$year group by $slotFormula"
  ),
  array(
    'label'=>"Events That Ran",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and i_actual>0",
    'breakoutsql'=>"select $slotFormula as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and i_actual>0 group by $slotFormula"
  ),
  array(
    'label'=>"Events with no result data",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and isnull(i_actual) and NOT (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\")",
    'breakoutsql'=>"select $slotFormula as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and isnull(i_actual) and NOT (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\") group by $slotFormula"
  ),
  array(
    'label'=>"Events that did not run",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and i_actual=0 and NOT (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\")",
    'breakoutsql'=>"select $slotFormula as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and i_actual=0 and not (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\") group by $slotFormula"
  ),
  array(
    'label'=>"Events that were cancelled",
    'sql'=>"select count(id_event) as count from ucon_event where id_convention=$year and (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\")",
    'breakoutsql'=>"select $slotFormula as type, count(id_event) as count
                    from ucon_event as E, ucon_event_type as ET
                    where id_convention=$year and E.id_event_type=ET.id_event_type and (s_title like \"%CANCEL%\" or s_game like \"%CANCEL%\") group by $slotFormula"
  ),
);





$content .= "<h2>Event Scheduling Statistics</h2>\n";
//$content .= "<p>";

function executeQueries($db, $stats) {
$table = array();
foreach ($stats as $k => $q) {
  $table[$k] = array();

  //echo "<pre>".print_r($q,1)."</pre>";
  $ADODB_GETONE_EOF = -1;
  $result = $db->GetOne($q['sql']);
  //$content .= $q['label'].": ".$result;//." (".$q['sql'].")";
  if ($result < 0) {
    $table[$k]['err'] = "<span style='color:red'>".$db->ErrorMsg()." /".$q['sql']."</span>";
  }

  $table[$k]['label'] = $q['label'];
  $table[$k]['all'] = $result;

  if (isset($q['breakoutsql'])) {
    $breakoutResult = $db->GetAll($q['breakoutsql']);
    if (!is_array($breakoutResult)) {
      $table[$k]['err'] = "<span style='color:red'>".$db->ErrorMsg()." /".$q['sql']."</span>";
    }

    foreach ($breakoutResult as $row) {
      $table[$k][$row['type']] = $row['count'];
    }
  }

  //$content .= "<br/>";
}
return $table;
}

$table = executeQueries($db, $stats);
$table2 = executeQueries($db, $stats2);
$table3 = executeQueries($db, $stats3);
$table4 = executeQueries($db, $statsTicketSales);
$table5 = executeQueries($db, $statsEventAttend);
$table6 = executeQueries($db, $statsEventAttendBySlot);

// $cols: associative array of keys to header label
// $data: 2d array
function printTable($cols, $data) {
  $content = "<table style='width:100%'><tr>";
  foreach ($cols as $col=>$colLabel) {
    $content .= "<th>$colLabel</th>";
  }
  $content .= "</tr>";
  foreach ($data as $row) {
    $content .= "<tr>";
    foreach ($cols as $col=>$colLabel) {
      $value = isset($row[$col]) ? $row[$col] : '';
      $content .="<td style='border: solid gray 1px'>$value</td>";
    }
    $content .= "</tr>";
  }
  $content .= "</table>";
  return $content;
}


//$content .= "</p>";


//$content .= "<pre>".print_r($table,1)."</pre>";

$cols = array('label'=>'Statistic','all'=>"Total",'BG'=>'BG/CG','RP'=>'RPG','MN'=>'Mini','OP'=>'Organized','EV'=>"Special",'err'=>'');
$content .= printTable($cols, $table);


$content .= "<h2>Prereg Statistics</h2>\n";
$cols2 = array('label'=>'Statistics','all'=>'Total','err'=>'');
$content .= printTable($cols2, $table2);

$content .= "<h2>Tickets</h2>\n";
$cols4 = array('label'=>'Statistics','all'=>'Total','BG'=>'BG/CG','RP'=>'RPG','MN'=>'Mini','OP'=>'Organized','EV'=>"Special",'err'=>'');
$content .= printTable($cols4, $table4);

//$content .= "<pre>".print_r($table4,1)."</pre>";

$content .= "<h2>Attendance</h2>\n";
$cols3 = array('label'=>'Statistics','all'=>'Total','Weekend'=>'Weekend','Friday'=>'Fri','Saturday'=>'Sat','Sunday'=>'Sun','Child Weekend'=>'Child','Gamemaster'=>'GM','Gamemaster (comped)'=>'GM-comp','Gamemaster Refund'=>'(GM-refund)','Volunteer'=>'Vol','Volunteer*'=>'Vol*','Staff'=>'Staff','Vendor'=>'Vend','Exhibitor'=>'Exh','Guest of Honor'=>'GoH','Special Guest'=>'Guest','Industry Insider'=>'Insider','Visitor'=>'Vis','Dealer'=>'Dealer','err'=>'');
$content .= printTable($cols3, $table3);
//echo "<pre>".print_r($table3,1)."</pre>";

$content .= "<h2>Event Attendance By Category</h2>\n";
$cols5 = array('label'=>'Statistics','all'=>'Total','BG'=>'BG/CG','RP'=>'RPG','MN'=>'Mini','OP'=>'Organized','EV'=>"Special",'err'=>'');
$content .= printTable($cols5, $table5);

$content .= "<h2>Event Attendance By Starting Timeslot</h2>\n";
$cols6 = array('label'=>'Statistics','all'=>'Total',''=>'Unsched','FRI1'=>'Fri 9-12', 'FRI2'=>'Fri 1-4', 'FRI3'=>'Fri 5-7', 'FRI4'=>'Fri 8+', 'SAT1'=>'Sat 9-12','SAT2'=>'Sat 1-4', 'SAT3'=>'Sat 5-7', 'SAT4'=>'Sat 8+', 'SUN1'=>'Sun 9-12', 'SUN2'=>'Sun 1-4', 'SUN3'=>'Sun 5-7');
$content .= printTable($cols6, $table6);
//$content .= "<pre>".print_r($table6,1)."</pre>";


//$smarty->assign('name', 'Ned');
$smarty->assign('config', $config);
$smarty->assign('content', $content);
$smarty->display('base.tpl');


