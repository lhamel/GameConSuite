<?php
require '../inc/inc.php';
$location = 'gcs/reg.php';
$title = $config['gcs']['sitetitle']. ' - Account Information'; // override with name further down

require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php

// must be logged in to view
if (!$auth->isLogged()) {
  header('HTTP/1.0 403 Forbidden');
  redirect('login.php');
  exit();
}

// collect parameters
$currUser = $auth->getCurrentUser();
$uid = $currUser['uid'];

// get the list of authorized users from association table
$year = @is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];
$id_member = $_GET['envelope'];
if (!isset($id_member) || !is_numeric($id_member)) {
  redirect('reg.php');
  exit;
}

if (!$associates->checkAuth($id_member)) {
  header('HTTP/1.0 403 Forbidden');
  echo "Unauthorized access";
  exit();
}

$sqlMember = <<< EOD
  select *
  from ucon_member
  where id_member=?
EOD;

$members = $db->getArray($sqlMember, array($id_member));
if (!is_array($members)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error.  Please report via contact form. '.$db->ErrorMsg());
} else if (count($members) != 1) {
  error_log("Attempted to access member id which doesn't exist: $id_member");
  die('No such member '.$id_member);
} else {
  $member = $members[0];
}


$sqlOrder = <<< EOD
  select O.*, E.*, R.*, O.i_quantity*O.i_price as linetotal
    , CONCAT(M.s_fname, " ", M.s_lname) as gamemaster
  from ucon_order as O
      left join ucon_event as E on (E.id_event=O.s_subtype and 
                          E.id_convention=O.id_convention and
                          O.s_type='Ticket')
      left join ucon_member as M on (M.id_member=E.id_gm)
      left join ucon_room as R on (E.id_room=R.id_room)
  where O.id_member=?
    and O.id_convention=?
EOD;


$sqlGm = <<< EOD
  select E.*, R.*,
    E.i_time + E.i_length as endtime,
    CONCAT(M.s_fname, " ", M.s_lname) as gamemaster,
    if(isNull(O.id_event), 0, quantity) as prereg
  from ucon_member as M, ucon_event as E 
    left join ucon_room as R on (E.id_room=R.id_room)
    left join (

select
id_event, I.description, sum(TI.quantity) as quantity
from ucon_transaction_item as TI, ucon_item as I, ucon_event as E
where
  E.id_convention=I.year
  and I.subtype=E.id_event%10000
  and TI.barcode=I.barcode
  and E.id_gm=?
  and I.year=?
group by id_event

  ) as O on (O.id_event=E.id_event)
  where E.id_gm=?
    and E.id_gm=M.id_member
    and E.id_convention=?
  order by E.e_day, E.i_time, id_event
EOD;
// NOTE: events are not required to be approved to show up here.

$orders = $db->getArray($sqlOrder, array($id_member, $year));
if (!is_array($orders)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error.  Please report via contact form. '.$db->ErrorMsg());
}


$events = $db->getArray($sqlGm, array($id_member, $year, $id_member, $year));
if (!is_array($events)) {
  error_log('SQL Error in '.$config['page']['location'].'. '.$db->ErrorMsg());
  die('SQL Error.  Please report via contact form. '.$db->ErrorMsg());
}



$first = $member['s_fname'];
$last = $member['s_lname'];
$full = $first . ($first && $last ? ' ' : '') . $last;
$member['name'] = '<a href="envelope.php?envelope='.$id_member.'">'.$full.'</a>';

function filterTickets($item) { return $item['s_type'] == 'Ticket'; }
function filterPayments($item) { return $item['s_type'] == 'Payment'; }
function filterNonPayments($item) { return $item['s_type'] != 'Payment'; }

$tickets = array_filter($orders, 'filterTickets');
$schedule = array_merge($tickets, $events);

function sortByTime($i1, $i2) {
  if ($i1['e_day'] != $i2['e_day']) {
    return $i1['e_day'] > $i2['e_day'];
  }
  if ($i1['i_time'] != $i2['i_time']) {
    return $i1['i_time'] > $i2['i_time'];
  }
  return 0;
}

function sortByTimestamp($i1, $i2) {
  return $i1['d_transaction'] > $i2['d_transaction'];
}

function formatItemDesc($orders, $fieldname) {
  global $constants;
  foreach ($orders as $k => $v) {
    $type = $v['s_type'];
    if ($v['s_type'] == "Ticket") {
      $title = formatSingleEventTitle($v['s_game'], $v['s_title']);
      $time = formatSingleEventTime($v['e_day'], $v['i_time']);
      $id = $v['id_event'];
      $orders[$k][$fieldname] = "$type: $title (#$id, $time)";
    } else if ($v['s_type'] == "Badge") {
      $subtype = $v['s_subtype'];
      $special = $v['s_special'];
      $orders[$k][$fieldname] = "$type: $subtype - $special";
    } else if ($v['s_type'] == 'Payment') {
      $subtype = $v['s_subtype'];
      $special = $v['s_special'];
      $orders[$k][$fieldname] = "***$type: $subtype - $special";
    } else {
      $subtype = $v['s_subtype'];
      $orders[$k][$fieldname] = "$type: $subtype";
    }
    //$orders[$k][$fieldname] .= $v['d_transaction'];
  }
  return $orders;
}

function formatRemoveBtn($orders, $fieldname) {
  global $constants,$id_member;
  foreach ($orders as $k => $v) {

//$config['allow']['buy_events'];
    if ($v['s_type'] != 'Payment') {
      $desc = $v['desc']."<br><br>Are you sure you want to remove this item?";
      $orderId = $v['id_order'];
      $url = "reg/_add.php?action=removeItem&id_member=$id_member&orderId=$orderId&desc=$v[desc]";
      $remove = "<a href=\"javascript:removeItemDialog('".addSlashes($desc)."', '".addSlashes($url)."')\"><img src=\"../images/remove.png\" width=\"10\"></a>";
      $orders[$k][$fieldname] = $remove;
    }
  }
  return $orders;
}


// remove time information from unapproved events
foreach ($events as $k => $v) {
  if (!$events[$k]['b_approval']) {
    $events[$k]['e_day'] = '';
    $events[$k]['i_time'] = '';
    $events[$k]['endtime'] = '';
  }
}
foreach ($schedule as $k => $v) {
  if (!$schedule[$k]['b_approval']) {
    $schedule[$k]['e_day'] = '';
    $schedule[$k]['i_time'] = '';
    $schedule[$k]['endtime'] = '';
  }
}

usort($schedule, 'sortByTime');
usort($orders, 'sortByTimestamp');
usort($events, 'sortByTime');

// Calculate the balance due
$balance = 0;
foreach ($orders as $k => $v) {
  $balance += $v['linetotal'];
  $orders[$k]['balance'] = '$'.number_format($balance,2);
}

// search for any unresolved payments pertaining to this member
$sql = 'select sum(f_amount) from ucon_incoming_paypal where id_member=? && b_used=0';
$pending = $db->getOne($sql, array($id_member));
if (!isset($pending)) {
  $pending = 0;
}

//echo "<pre style=\"text-align:left\">Member\n".print_r($member,1).'</pre>';
//echo "<pre style=\"text-align:left\">Order\n".print_r($orders,1).'</pre>';
//echo "<pre style=\"text-align:left\">Events\n".print_r($events,1).'</pre>';

//echo "<pre style=\"text-align:left\">Tickets\n".print_r($tickets,1).'</pre>';
//echo "<pre style=\"text-align:left\">Schedule\n".print_r($schedule,1).'</pre>';


include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/menu.php';

$smarty->assign('events', $members); // TODO bug fix, field is called events
//$smarty->assign('events', $events);

$actions = array();

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

// render the page
//echo '<pre>'.print_r($_SESSION,1).'</pre>'; exit;
$content = $smarty->fetch('gcs/reg/removeItemDlg.tpl');

if (isset($_REQUEST['update'])) {
  $ribbon = '<p class="ribbon">'.$_REQUEST['update'].'</p>';
} else {
  $ribbon = '<p></p>';
}

$content .= <<< EOD
<h2>Envelope: $full</h2>
$ribbon
EOD;



// List orders
$content .= '<h3>Preregistration Order</h3>';
//$payments = array_filter($orders, 'filterPayments');
//$nonpayments = array_filter($orders, 'filterNonPayments');
if (count($orders)>0) {

  $cols = array(
    'remove'=>'',
    'desc'=>'Description',
    'i_quantity'=>'Quantity',
    'i_price'=>'Unit Price',
    'linetotal'=>'Total',
    'balance'=>'Balance',
  );
  if (!$config['allow']['buy_events']) {
    unset($cols['remove']);
  }

  $items = formatItemDesc($orders, 'desc');
  $items = formatRemoveBtn($items, 'remove');
  $smarty->assign('events', $items);
  $smarty->assign('columns', $cols);
  $smarty->assign('columnsAlign', array(
    'i_quantity'=>'right',
    'i_price'=>'right',
    'linetotal'=>'right',
    'balance'=>'right',
  ));

  $content .= $smarty->fetch('gcs/common/general-table.tpl');
} else {
  $content .= "<p>No preregistration order yet</p>";
}



$content .= '<h3>Payments</h3>';
/*
if (count($payments)>0) {
  $items = formatItemDesc($payments, 'desc');
  $smarty->assign('events', $items);
  $smarty->assign('columns', array(
    //'id_order'=>'Order',
    'desc'=>'Description',
    //'i_quantity'=>'Quantity',
    //'i_price'=>'Unit Price',
    'linetotal'=>'Total',
  ));

  $content .= $smarty->fetch('gcs/common/general-table.tpl');
} else {
  $content .= "<p>No payments yet</p>";
}
*/
if ($balance-$pending > 0) {
  $smarty->assign('amount', ($balance-$pending));
  $smarty->assign('id_member', $id_member);

  $formatBalance = number_format($balance,2);
  $fDifference = number_format( ($balance-$pending), 2);
  $msgPending = '';
  if ($pending > 0) {
    $fPending = number_format($pending,2);
    $msgPending = "<p>Payments totaling <b>\$$fPending</b> have been received but not yet reconciled.</p>";
  }

  $paypalBtn = $smarty->fetch('gcs/reg/paypal.tpl');
  $content .= <<< EOD
<p>Our records show that you have a balance due.  <span style="font-weight:bolder">Please wait until you've got all your items in your cart before you check out!  Also wait for previous payments to be resolved before paying again.</span><!--'--></p>

<p>Balance due: <b>\$$formatBalance</b></p>

$msgPending

<table><tr>
  <th style="width:36%">Pay by credit card or PayPal</th>
  <th>Pay by check</th>
</tr><tr><td>

  <p>Make a credit card or PayPal payment for <b>\$$fDifference</b>: $paypalBtn</p>
  <p><b>Payments will not appear immediately!</b><br>Please give us 3 business days to update our records, then notify us of any discrepencies.</p>

</td><td>

<p>Alternately, you may submit a check by postal mail to our P.O. Box, however it will
  take longer for us to receive and process your payment.  Please make checks payable 
  to <b>{$config['gcs']['payments']['checkPayable']}</b> and include the name on your 
  registration with the check.  Never send cash through the mail!</p>

  <p>Attn: Registration<br/>
  {$config['gcs']['payments']['mailAddress']}</p>
</td></tr></table>
EOD;

} else if ($balance < 0) {
  $refund = number_format(-$balance,2);
  $content .= <<< EOD


<p>Refund owed: \$$refund</p>

<p>Our records show that you have a refund due.  Feel free to add additional items into your account.  Please contact us to request an early refund.</p>

EOD;
} else if ($pending > 0) {

  $fBalance = number_format($balance,2);
  $fPending = number_format($pending,2);

  $content .= <<< EOD
<p>Our records show pending payments from PayPal of <b>\$$fPending</b> and a balance due of <b>\$$fBalance.</b></p>

  <p><b>Payments will not appear immediately!</b><br>Please give us 3 business days to update our records, then notify us of any discrepencies.</p>



EOD;


} else {
  $content .= '<p>No payment due.</p>';
}



//if the attendee is a GM
if (count($events)>0) {

  // List GM events
  // $events = formatEventTitles($events, 'title');
  // $events = formatEventTimes($events, 'time');
  // $events = formatEventPlayers($events, 'players');
  // $smarty->assign('additional', array('prereg'=>'Prereg'));
  // $smarty->assign('events', $events);
  // $smarty->assign('columns', array(
  //   'id_event'=>'#',
  //   'title'=>'System/Title',
  //   'gamemaster'=>'Gamemaster',
  //   'players'=>'Players',
  //   'time'=>'Day/Time',
  //   'prereg'=>'Prereg',
  // ));
  // $smarty->assign('columnsAlign', array());
  $content .= '<h3>GM Events</h3>';
  // $content .= $smarty->fetch('gcs/common/general-table.tpl');

$content .= <<< EOD

    <!-- demo root element -->
    <div id="demo">
      <gamemaster-events
        :gmevents="gridData"
      >
      </gamemaster-events>
    </div>

EOD;

}



// List combined schedule
foreach ($schedule as $k=>$v) {

  if (isset($v['s_type']) && $v['s_type'] == "Ticket") {
    $schedule[$k]['quant_type'] = $v['i_quantity'].' Ticket';
  } else {
    $schedule[$k]['quant_type'] = 'GM';
  }
}
//echo "<pre style=\"text-align:left\">Schedule\n".print_r($schedule,1).'</pre>';
$schedule = formatEventTimes($schedule, 'time');
$schedule = formatEventTitles($schedule, 'title');
$columns = array(
  'time'=>'Time',
  'quant_type'=>'Type',
  'title'=>'System/Title',
  'gamemaster'=>'Gamemaster',
);
if ($config['allow']['see_location']) {
  $schedule = formatEventLocations($schedule, 'location');
  $columns['location'] = 'Location';
}
$smarty->assign('columns', $columns);
$smarty->assign('events', $schedule);
$content .= '<h3>Combined Schedule</h3>'.$smarty->fetch('gcs/common/general-table.tpl');


$content .= <<< EOD

<script type="text/x-template" id="gamemaster-events-template">
<div>

<table class="striped" border="0" cellspacing="0" cellpadding="1" width="100%">
<thead>
  <tr>
    <th>#</th>
    <th>System/Title</th>
    <th>Gamemaster</th>
    <th>Players</th>
    <th>Day/Time</th>
    <th>Prereg</th>
    <th></th>
  </tr>
</thead>
<tbody>

  <tr v-for="entry in formattedGmEvents">

<td>{{ entry.id }}</td>
<td>{{ entry.formatTitle }}</td>
<td>{{ entry.formatGM }}</td>
<td>{{ entry.formatPlayers }}</td>
<td>{{ entry.formatTime }}</td>
<td>{{ entry.prereg }}</td>
<td><button @click="currEvent = entry; showVTTDialog = true">Provide VTT</button></td>

  </tr>
</tbody>
</table>

<vtt-dialog v-if="showVTTDialog" :event="currEvent" @saveAndClose="saveEvent(); showVTTDialog = false" @close="showVTTDialog = false"></vtt-dialog>

</div>
</script>

    <script type="text/x-template" id="vtt-dialog-template">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">

              <div class="modal-header">
                <h3>Title: {{event.formatTitle}}</h3>
              </div>

              <div class="modal-body">

                <!-- event details -->
                <ul>
                <li>Event #{{event.id}}</li>
                <li>Price: \${{event.cost}}</li>
                <li>Seats: {{event.formatPlayers}}</li>
                <li>Time: {{event.formatTime}}</span></li>
                </ul>
                <hr>

                <p>Provide VTT Link (e.g. Roll20 or Zoom link)</p>
                <input id="vttlink" type="text" :value="event.vttLink">

                <p>Provide Other VTT Information</p>
                <textarea id="vttinfo">{{event.vttInfo}}</textarea>
              </div>

              <div class="modal-footer">
                <slot name="footer">
                  &nbsp;
                  <button class="modal-default-button" @click="\$emit('close')">
                    Cancel
                  </button>
                  <button class="modal-default-button" @click="\$emit('saveAndClose')">
                    OK
                  </button>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </script>


    <script>
      // register the grid component

      Vue.component("vtt-dialog", {
        template: "#vtt-dialog-template",
        props: {
          event: Object,
        },
      });

      Vue.component("gamemaster-events", {
        template: "#gamemaster-events-template",
        props: {
          gmevents: Array
        },
        data: function() {
        //   var sortOrders = {};
        //   this.columns.forEach(function(key) {
        //     sortOrders[key] = 1;
        //   });
          return {
            showVTTDialog: false,
            currEvent: Object,
            // sortKey: "",
            // sortOrders: sortOrders
          };
        },
        computed: {
          formattedGmEvents: function() {
            var events = this.gmevents;

            events.forEach(e => {
              e.formatTitle = this.\$options.filters.formatTitle(e);
              e.formatPlayers = this.\$options.filters.formatPlayers(e);
              e.formatGM = this.\$options.filters.formatGmObj(e.gm);
              e.formatTime = this.\$options.filters.formatTime(e);
            });
            return events;
          },
          filteredGmEvents: function() {
            var sortKey = this.sortKey;
            var order = this.sortOrders[sortKey] || 1;
            var events = this.gmevents;
            if (sortKey) {
              events = events.slice().sort(function(a, b) {
                a = a[sortKey];
                b = b[sortKey];
                return (a === b ? 0 : a > b ? 1 : -1) * order;
              });
            }
            return events;
          }
        },
        filters: {
          capitalize: function(str) {
            if (!str) { return str; }
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
          },
          formatSingleTime: function (time) {
            if (!time) return "";
            if (time < 12) return time+"a";
            else if (time == 12) return "12p";
            else if (time < 24) return (time-12)+"p";
            else if (time == 24) return "12a";
            else return (time-24)+"a";
          },
          formatTitle: function(eventObj) {
            if (!eventObj) {
              console.error("Missing eventObj");
            }
            let name = eventObj.game;
            if (eventObj.title) {
              name += (name ? ': ' : '') + eventObj.title
            }
            return name;
          },
          formatGmObj: function(obj) {
            if (!obj) { return '' };
            let name = obj.lastName;
            if (obj.firstName) {
              name = obj.firstName + ' ' + name;
            }
            if (obj.groupName) {
              name += ' (' + obj.groupName + ')';
            }
            return name;
          },
          formatPlayers: function(obj) {
            if (!obj) { return '' };
            let players = obj['maxplayers'];
            let minplayers = obj['minplayers'];
            if (minplayers && minplayers>0 && minplayers != players) {
              players = minplayers + " - " + players;
            }
            return players;
          },
          formatTime: function(eventObj) {
            let day = eventObj.day ? this.capitalize(eventObj.day) : '';
            let time = eventObj.time ? eventObj.time : '';
            let endtime = eventObj.endtime ? eventObj.endtime : '';
            return day + (time? ' ' + this.formatSingleTime(time) + '-'+this.formatSingleTime(endtime)+' EDT':'');
          }
        },
        methods: {
          saveEvent: function() {
            console.log("save click");

            // TODO save values back to event

          },
          sortBy: function(key) {
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
          }
        }
      });

      // bootstrap the demo
      var demo = new Vue({
        el: "#demo",
        data: {
          //searchQuery: "",
          gridColumns: ["id", "game", "minplayers", "maxplayers", "price" ],
          gridData: []
        },
        created: function() {


          // Request the token from previous login
          var jqxhr = $.get( "/GameConSuite/api/user/token")
            .done(function(data) {
              console.log(data);
              let t = (data);
              console.log( "retrieved token " + t );
              localStorage.setItem('token', t);

              // set up the bearer-token for all calls
              $.ajaxSetup({
                  beforeSend: function(xhr) {
                      xhr.setRequestHeader('Authorization', 'Bearer '+t);
                  }
              });

              // retrieve the GM events list
              var jqxhr = $.get( "/GameConSuite/api/user/envelope/{$id_member}/event")
                .done(function(data) {
                  console.log( "success" );
                  console.log( data );

                  data = data.slice().sort(function(a, b) {
                    a = a.day + a.time + a.id;
                    b = b.day + b.time + b.id;
                    return (a === b ? 0 : a > b ? 1 : -1);
                  });
                  demo.gridData = data;

                })
                .fail(function(data) {
                  console.log( "error" );
                  console.log( data );
                });


            })
            .fail(function(data) {
              console.log( "not logged in" );
            });

        }
      });
</script>


EOD;

$smarty->assign('content', $content);
$smarty->display('base.tpl');


