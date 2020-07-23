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
    
        search.php      ->  search for events
        cart.php        ->  currently selected items

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
// $smarty->assign('events', isset($events) ? $events : array());
$smarty->assign('actions', $actions);
// $smarty->assign('showResults', true);
$smarty->assign('showResults', false);
$smarty->assign('loginInfo', $associates->getLoginInfo());

$content = '';
$message = $config['allow']['message'];
if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";
$content .= $smarty->fetch('gcs/reg/browse.tpl');


$content .= <<< EOD

    <!-- demo root element -->
    <div id="demo">

      <h3>Results</h3>
      <filter-event
        :filter-events="filteredEvents"
        :members="members"
        :event-formatter="eventFormatter"
      >
      </filter-event>

    </div>




<script type="text/x-template" id="filter-event-template">
<div>

  <div v-for="(daygroup, day) in eventsByDayAndTime">

    <h2 style="text-align:center;font-size:1.6em;border:solid gray 1px;background:navy;color:white">{{day}}</h2>

    <div v-for="(timegroup, time) in daygroup">

      <p style="font-weight:bold;font-size:larger;border-bottom:solid black 1px; background-color:#fff0a0">{{day}} {{timegroup[0].formatStartTime}} ET</p>

      <div v-for="entry in timegroup">
        <filter-event-entry :event="entry" :members='members' @showExpCompDialog="showExpCompDialog=true" @showEventDialog="currEvent=entry; showEventDialog=true"></filter-event-entry>
      </div>
    </div>


  </div>



  <exp-comp-dialog v-if="showExpCompDialog" @close="showExpCompDialog=false"></exp-comp-dialog>
  <view-event-dialog :event="currEvent" :members='members' v-if="showEventDialog" @close="showEventDialog=false"></view-event-dialog>

</div>
</script>


<script type="text/x-template" id="filter-event-entry-template">
  <p style="line-height:20px">
    <button class="fa-button" style="float:left" @click="\$emit('showEventDialog')">
      <span v-if="event.soldout">
        <i class="fas fa-star"></i>
      </span>
      <span v-else>
        <i class="fas fa-search"></i>
      </span>
      <span style="font-size:smaller">VIEW</span>
    </button>

    {{event.id}}
    <strong>{{event.formatTitle}}</strong>,
    GM: <a :href="'{$config['page']['depth']}gcs/events/search.php?search='+event.gm.lastName">{{event.formatGM}}</a>,
    {{event.maxplayers}} seats, 
    <a href="#" @click.prevent="\$emit('showExpCompDialog')">{{event.exper}}/{{event.complex}}</a>,

    {{event.formatTime}}.
    {{event.desclong}}
    {{event.formatAges}}

    <span v-if="event.room">{{event.room.label}}<span v-if="event.table"> Table {{event.table}}</span>.</span>
    <span v-if="event.cost">\${{event.cost}}</span><span v-else>Free!</span>
  </p>
</script>


    <script type="text/x-template" id="exp-comp-dialog-template">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">

              <div class="modal-header">
                <h3>Experience / Complexity</h3>
              </div>

              <div class="modal-body">

                <p>Experience and complexity ratings were set by the GM at the time of event 
                submission.  GMs are provided with these guidelines:</p>

                <table>
                <tr>

                <td>
                  <p style="font-weight: bolder;">Experience:</p>
                  <dl>
                    <dt style="font-weight: bolder;">No XP</dt>
                    <dd style="margin-left: 0px; font-style: italic;">No experience needed</dd>
                  </dl>
                  <dl>
                    <dt style="font-weight: bolder;">Some XP</dt>
                    <dd style="margin-left: 0px; font-style: italic;">Prior experience with this game or similar games recommended</dd>
                  </dl>
                  <dl>
                    <dt style="font-weight: bolder;">Lots XP</dt>
                    <dd style="margin-left: 0px; font-style: italic;">Have played this specific game a few times</dd>
                  </dl>
                </td>

                <td>
                  <p style="margin-bottom: 0px; font-weight: bolder;">Complexity:</p>
                  <dl>
                    <dt style="font-weight: bolder;">Simple</dt>
                    <dd style="margin-left: 0px; font-style: italic;">Games involve little or no strategic thinking and have a high luck factor</dd>
                  </dl>
                  <dl>
                    <dt style="font-weight: bolder;">Normal</dt>
                    <dd style="margin-left: 0px; font-style: italic;">Games involve a little strategy and some luck</dd>
                  </dl>
                  <dl>
                    <dt style="font-weight: bolder;">Complex</dt>
                    <dd style="margin-left: 0px; font-style: italic;">Significant strategic thinking needed and the effects of luck are minimized</dd>
                  </dl>
                </td>

                </tr>
                </table>

              </div>

              <div class="modal-footer">
                <slot name="footer">
                  &nbsp;
                  <button class="modal-default-button" @click="\$emit('close')">
                    Close
                  </button>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </script>


    <script type="text/x-template" id="view-event-dialog-template">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">

              <div class="modal-header">
                <h3>Event: #{{event.id}}</h3>
              </div>

              <div class="modal-body">

<p>
<strong>{{event.formatTitle}}</strong>
</p>

  <table><tr>
  <td style="width:150px">
    {{event.formatTime}}<br>
    Players: {{event.formatPlayers}}<br>
    <span v-if="event.fill>=0">
      &nbsp;&nbsp;Sold: {{event.fill}}<br>
      &nbsp;&nbsp;Available: {{event.maxplayers-event.fill}}
    </span>
  </td>
  <td>
    <span v-if="event.cost">\${{event.cost}}</span><span v-else>Free!</span><br>
    GM: {{event.formatGM}}<br>
    <span v-if="event.formatAges">{{event.formatAges}}<br></span>
    <span v-if="event.room">{{event.room.label}}<span v-if="event.table"> Table {{event.table}}</span><br></span>
    <span v-if="event.formatTags">{{event.formatTags}}</span>
  </td>
  </tr></table>

  <!--<p>{{event.desclong}}</p>-->

  <div v-if="event.soldout" class="eventStatusMarker" style="margin-top:8px">
    <i class="fas fa-star"></i>
    <span style="font-size:smaller">SOLD OUT</span>
  </div>
  <div v-else class="eventStatusMarker" style="margin-top:8px">
    <i class="fas fa-calendar"></i>
    <span style="font-size:smaller">AVAILABLE</span>
  </div>

  <p>
  </p>

<div v-if="!event.soldout">
  <div v-if="members">
    <p style="text-align:left;margin-bottom:0px;margin-top:6px;">Select envelopes to receive tickets:</p>
    <div v-for="member in members">
      <member-ticket-status :member='member' :event='event'></member-ticket-status>
    </div>
  <p style="text-align:left;margin-bottom:2px;">Items will be added to specified envelopes.  See "My Registration" to view each envelope.</p>
  </div>

  <div v-else>
    <div class="eventStatusMarker" style="margin-top:8px">
      <i class="fas fa-user"></i>
      <span style="font-size:smaller">Log in to add tickets</span>
    </div>
  </div>
</div>

              </div>

              <div class="modal-footer">
                <slot name="footer">
                  &nbsp;
                  <button class="modal-default-button" @click="\$emit('close')">
                    Close
                  </button>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </script>


<script type="text/x-template" id="member-ticket-status-template">
<div>
      <span v-if="hasTicket">
        <button class="fa-button" @click="confirmRemoveTicket">
          <span v-if="event.soldout">
              <i class="fas fa-star"></i>
          </span>
          <i class="fas fa-check"></i>
          <span style="font-size:smaller">HAS</span>
        </button>
      </span>
      <span v-if="event.soldout && !hasTicket">
        <button class="fa-button" disabled>
          <i class="fas fa-star"></i>
          <span style="font-size:smaller">FULL</span>
        </button>
      </span>
      <span v-if="canAdd">
        <button class="fa-button" @click="addTicket">
          <i class="fas fa-calendar-plus"></i>
          <span style="font-size:smaller">ADD</span>
        </button>
      </span>

      <!--<i class="fas fa-envelope-open-text"></i>-->
      <a :href="'../envelope.php?envelope='+member.id">{{member.formatName}}</a>
  </p>
</div>
</script>


    <script src="{$config['page']['depth']}js/gcs/events.js"></script>
    <script>

      Vue.component("filter-event", {
        template: "#filter-event-template",
        props: {
          eventFormatter: Object,
          filterEvents: Array,
          members: Array,
        },
        data: function() {
          return {
            showExpCompDialog: false,
            showEventDialog: false,
            currEvent: Object,
          };
        },
        computed: {
          eventsByDayAndTime: function() {

            let result = {};

            let byDay = this.groupBy(this.filterEvents, 'day');
            for (const [day, dayList] of Object.entries(byDay)) {
              console.log (this.eventFormatter);
              let d = this.eventFormatter.formatDay(day);
              result[d] = this.groupBy(dayList, 'time');
            }

            // let result = this.groupBy(this.filterEvents, 'time');
            console.log(result);
            return result;
          }
        },
        methods: {
          showEvent: function(event) {
            currEvent = event;
            showEventDialog = true;
          },
          groupBy: function (arr, property) {
            return arr.reduce(function(memo, x) {
              if (!memo[x[property]]) { memo[x[property]] = []; }
              memo[x[property]].push(x);
              return memo;
            }, {});
          }
        }
      });


      Vue.component("filter-event-entry", {
        template: "#filter-event-entry-template",
        props: {
          event: Object,
          members: Array,
        },
        data: function() {
          return {
            currEvent: Object,
            showExpCompDialog: false,
            showEventDialog: false,
          };
        }
      });

      Vue.component("member-ticket-status", {
        template: '#member-ticket-status-template',
        props: {
          member: Object,
          event: Object
        },
        computed: {
          hasTicket: function() {
            console.log(this.member.tickets);
            let r = this.member.tickets.find(t => t.subtype==this.event.id);
            // console.log(this.event.id);
            // console.log(r);
            return r;
          },
          canAdd: function() {
            return !this.event.soldout && !this.hasTicket;
          }
        },
        methods: {
          addTicket: function() {

            // allow the user to add a ticket for the person
            // var jqxhr = $.get( self.baseUrl+"api/user/tickets")
            //   .done(function(data) {
            //     console.log( "retrieve members" );
            //     console.log( data );

            //     demo.members = data;
                // on success, adjust the tickets value

            //   })
            //   .fail(function(data) {
            //     // probably not logged in
            //     console.log( "error" );
            //     console.log( data );
            //     demo.members = null;
            //   });




          },
          confirmRemoveTicket: function() {

          },
          removeTicket: function() {

          }
        }

      });




      Vue.component("exp-comp-dialog", {
        template: "#exp-comp-dialog-template",
        props: {
        },
      });

      Vue.component("view-event-dialog", {
        template: "#view-event-dialog-template",
        props: {
          event : Object,
          members: Array,
        },
      });


      // bootstrap the demo
      var demo = new Vue({
        el: "#demo",
        data: function () {
          return {
            eventFormatter: eventFormatter,
            baseUrl: '{$config['page']['depth']}',
            filteredEvents: [],
            members: null
          };
        },
        created: function() {
          var self = this;

          // retrieve the event constants
          var jqxhr = $.get( self.baseUrl+"api/system/constants/events")
            .done(function(data) {
              console.log( "retrieved event constants" );
              console.log( data );
              eventFormatter.constants = data;

              // retrieve the filtered events list
                var jqxhr = $.get( self.baseUrl+"api/public/event?day={$_GET['day']}&category={$_GET['category']}&ages={$_GET['ages']}&tags={$_GET['tags']}")
                  .done(function(data) {
                    console.log( "retrieved filtered events" );
                    console.log( data );

                    var events = data;
                    events.forEach(e => {
                      e.formatTitle = eventFormatter.formatTitle(e);
                      e.formatPlayers = eventFormatter.formatPlayers(e);
                      e.formatGM = eventFormatter.formatGmObj(e.gm);
                      e.formatStartTime = eventFormatter.formatSingleTime(e.time);
                      e.formatTime = eventFormatter.formatTime(e);
                      e.formatAges = eventFormatter.formatAges(e);
                    });

                    // data = data.slice().sort(function(a, b) {
                    //   a = a.day + a.time + a.id;
                    //   b = b.day + b.time + b.id;
                    //   return (a === b ? 0 : a > b ? 1 : -1);
                    // });
                    demo.filteredEvents = events;

                  })
                  .fail(function(data) {
                    console.log( "error" );
                    console.log( data );
                  });

                // if the user is logged in, retrieve information about tickets for all their members
                var jqxhr = $.get( self.baseUrl+"api/user/tickets")
                  .done(function(data) {
                    console.log( "retrieve members" );
                    console.log( data );

                    data.forEach(m => {
                      m.formatName = eventFormatter.formatGmObj(m);
                    });

                    demo.members = data;

                  })
                  .fail(function(data) {
                    // probably not logged in
                    console.log( "error" );
                    console.log( data );
                    demo.members = null;
                  });


            })
            .fail(function(data) {
              console.log( "error" );
              console.log( data );
            });

  

        }
      });
</script>





EOD;



// render the page
$smarty->assign('content', $content);
$smarty->display('base.tpl');

