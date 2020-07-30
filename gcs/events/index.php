<?php
require_once '../../inc/inc.php';

$year = $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
$location = 'gcs/events/index.php';
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


$location = 'events/browse.php';
$title = $config['gcs']['sitetitle'] . ' - Browse Events';

$actions = array('list'=>basename(__FILE__),
                 'filterDay'=>basename(__FILE__).'?day=',
                 'filterCategory'=>basename(__FILE__).'?category=',
                 'filterTag'=>basename(__FILE__).'?tag=',
                // 'detail'=>'view.php?id='
                 'navigateMember'=>'search.php?search=',
                );

// $year = $config['gcs']['year'];
// require_once INC_PATH.'resources/event/constants.php';


// // render the event results
// $smarty->assign('REQUEST', isset($_REQUEST) ? $_REQUEST : array());
$smarty->assign('config', $config);
// $smarty->assign('constants', $constants);
// $smarty->assign('actions', $actions);
// $smarty->assign('loginInfo', $associates->getLoginInfo());

$content = '';
$message = $config['allow']['message'];
if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";


$buyEventsEnabled = $config['allow']['buy_events'] ? 'true':'false';
$searchParam = isset($_GET['search']) ? $_GET['search'] : '';

$content .= <<< EOD

    <!-- demo root element -->
    <div id="demo">

<h1>Browse Events</h1>
<form @submit.prevent>
Keyword: <input v-model="filterSearch" @change="updateSearch" name="search">
</form>

<filter-selection label="Day" :options="filterOptions.day" :selected="filterSelections.day" @update="updateDay"></filter-selection>
<filter-selection label="Category" :options="filterOptions.category" :selected="filterSelections.category" @update="updateCategory"></filter-selection>
<filter-selection label="Ages" :options="filterOptions.age" :selected="filterSelections.age" @update="updateAge"></filter-selection>
<filter-selection label="Tags" :options="filterOptions.tag" :selected="filterSelections.tag" @update="updateTag"></filter-selection>

<hr>

      <filter-event v-if="filteredEvents.length"
        :filter-events="filteredEvents"
        :members="members"
        :event-formatter="eventFormatter"
        :api-url="baseUrl"
        :prereg-open="preregOpen"
        @ticket-add="updateTicketInfo"
      >
      </filter-event>

    </div>


<script type="text/x-template" id="filter-selection-template">
<p>
<!-- <filter-selection label="Day" :options="optionsDay" :selected="selectDay" @update="selectDay"></filter=selection> -->
  {{label}}:

  <a v-if="selected==null" href="#" @click.prevent="\$emit('update', null)" class="selected">All</a>
  <a v-else                 href="#" @click.prevent="\$emit('update', null)"                 >All</a>


<!-- TODO check if selected -->
  
  <span v-for="(label, index) in options">
    |
    <a v-if="selected==index" href="#" @click.prevent="\$emit('update', index)" class="selected">{{label}}</a>
    <a v-else                 href="#" @click.prevent="\$emit('update', index)"                 >{{label}}</a>
  </span>

<!--
    <span>Day: <a href="index.php?day=&amp;ages=&amp;tags=&amp;category=4" class="selected">All</a> | <a href="index.php?day=FRI&amp;category=4&amp;tags=&amp;ages=">Friday</a> | <a href="index.php?day=SAT&amp;category=4&amp;tags=&amp;ages=">Saturday</a> | <a href="index.php?day=SUN&amp;category=4&amp;tags=&amp;ages=">Sunday</a></span>
-->

</p>
</script>




<script type="text/x-template" id="filter-event-template">
<div>

  <h3>Results</h3>

  <div v-for="(daygroup, day) in eventsByDayAndTime">
    <h2 style="text-align:center;font-size:1.6em;border:solid gray 1px;background:navy;color:white">{{day}}</h2>
    <div v-for="(timegroup, time) in daygroup">
      <p style="font-weight:bold;font-size:larger;border-bottom:solid black 1px; background-color:#fff0a0">{{day}} {{timegroup[0].formatStartTime}} ET</p>
      <div v-for="entry in timegroup">
        <filter-event-entry :event="entry" :members='members' :api-url='apiUrl' @showExpCompDialog="showExpCompDialog=true" @showEventDialog="currEvent=entry; showEventDialog=true"></filter-event-entry>
      </div>
    </div>
  </div>

  <exp-comp-dialog v-if="showExpCompDialog" @close="showExpCompDialog=false"></exp-comp-dialog>
  <view-event-dialog :preregOpen="preregOpen" :event="currEvent" :members='members' :api-url='apiUrl' v-if="showEventDialog" @close="showEventDialog=false" @ticketAdd="ticketAdd"></view-event-dialog>

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
    GM: <a :href="'{$config['page']['depth']}gcs/events/index.php?search='+event.gm.lastName">{{event.formatGM}}</a>,
    {{event.maxplayers}} seats, 
    <a href="#" @click.prevent="\$emit('showExpCompDialog')">{{event.formatExper}}/{{event.formatComplex}}</a>,

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

<div v-if="preregOpen">
  <div v-if="members">
    <p style="text-align:left;margin-bottom:0px;margin-top:6px;">Select envelopes to receive tickets:</p>
    <div v-for="member in members">
      <member-ticket-status :member='member' :event='event' :api-url='apiUrl' @ticketAdd="ticketAdd"></member-ticket-status>
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

<div v-else>
<p>Registration is currently closed</p>
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
          apiUrl: String,
          preregOpen: Boolean
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
              let d = this.eventFormatter.formatDay(day);
              result[d] = this.groupBy(dayList, 'time');
            }

            // let result = this.groupBy(this.filterEvents, 'time');
            return result;
          }
        },
        methods: {
          showEvent: function(event) {
            currEvent = event;
            showEventDialog = true;
          },
          groupBy: function (arr, property) {
            if (!arr) { return arr; }
            return arr.reduce(function(memo, x) {
              if (!memo[x[property]]) { memo[x[property]] = []; }
              memo[x[property]].push(x);
              return memo;
            }, {});
          },
          ticketAdd: function(member, ticket){
            console.log('filter-event');
            this.\$emit('ticket-add', member, ticket);
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
          event: Object,
          apiUrl: String
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
            var self = this;

            console.log ("add tickets");

            let url = this.apiUrl+"api/user/envelope/"+this.member.id+"/cart";
            console.log(url);

            let body = {
              "type": "Ticket",
              "subtype": this.event.id,
              "quantity": 1
            };

            $.ajax({
              type: "POST",
              url: url,
              data: JSON.stringify(body),
              contentType: 'application/json',
              dataType: 'json'
            })
              .done(function(data) {
                console.log( "member-ticket-status - succeeded adding ticket" );
                console.log( self.member );
                console.log( data );

                // TODO convert alert to dialog
                //alert("Added ticket #"+data.subtype);

                // TODO on success, adjust the tickets value
                self.\$emit('ticketAdd', self.member, data);

              })
              .fail(function(data) {

                // TODO display the error message
                //alert(data);

                // probably not logged in
                console.log( "error" );
                console.log( data.responseText );
                alert(data.responseText);
              });

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

      Vue.component("filter-selection", {
        template: "#filter-selection-template",
        props: {
          label: String,
          options: Object,
          selected: String
        }
      });

      Vue.component("view-event-dialog", {
        template: "#view-event-dialog-template",
        props: {
          event : Object,
          members: Array,
          apiUrl: String,
          preregOpen: Boolean
        },
        methods: {
          ticketAdd: function(member, ticket) {
            console.log("view-event-dialog - ticket add, passing upward")

            this.\$emit('ticketAdd', member, ticket);
          }
        }
      });


      // bootstrap the demo
      var demo = new Vue({
        el: "#demo",
        data: function () {
          return {
            filterSearch: '',
            filterOptions: {},
            filterSelections: {},
            eventFormatter: eventFormatter,
            baseUrl: '{$config['page']['depth']}',
            filteredEvents: [],
            members: [],
            preregOpen: {$buyEventsEnabled},
          };
        },
        methods: {
          updateTicketInfo: function(member, ticket) {
            console.log("ticket added");

            // TODO add the ticket to the member's ticket list
            let m = this.members.find(t => t.id==member.id);
            m.tickets.push(ticket);

            // TODO add the ticket quanitity to the event prereg count, or reload to check for sold-out status
            let e = this.filteredEvents.find(t => t.id==ticket.subtype);
            // TODO manipulate the prereg count or refresh for sold-out status
            if (e.fill >= 0) {
              e.fill += ticket.quantity;
              if (e.fill >= e.maxplayers) { e.soldout=true; }
            } else {
              // TODO fetch info
            }

          },
          updateDay: function(selectedIndex) {
            this.updateFilter('day', selectedIndex);
          },
          updateCategory: function(selectedIndex) {
            this.updateFilter('category', selectedIndex);
          },
          updateAge: function(selectedIndex) {
            this.updateFilter('age', selectedIndex);
          },
          updateTag: function(selectedIndex) {
            this.updateFilter('tag', selectedIndex);
          },
          updateSearch: function() {
            // Note: the field doesn't matter, we're trying to trigger the re-query
            this.updateFilter('search', this.filterSearch);
          },
          updateFilter: function(field, selectedIndex) {
            Vue.set(this.filterSelections, field, selectedIndex);

            var self = this;

            function makeParam(val) {
              return val ? val : '';
            }

            // retrieve the filtered events list
            let url = this.baseUrl+"api/public/event?search=" + makeParam(this.filterSearch) 
                    + "&day=" + makeParam(this.filterSelections.day)
                    + "&category=" + makeParam(this.filterSelections.category)
                    + "&ages=" + makeParam(this.filterSelections.age)
                    + "&tags=" + makeParam(this.filterSelections.tag);

            var jqxhr = $.get( url )
              .done(function(data) {
                console.log( "retrieved filtered events" );
                // console.log( data );

                var events = data;
                events.forEach(e => {
                  e.formatTitle = eventFormatter.formatTitle(e);
                  e.formatPlayers = eventFormatter.formatPlayers(e);
                  e.formatGM = eventFormatter.formatGmObj(e.gm);
                  e.formatStartTime = eventFormatter.formatSingleTime(e.time);
                  e.formatTime = eventFormatter.formatTime(e);
                  e.formatAges = eventFormatter.formatAges(e);
                  e.formatExper = eventFormatter.formatExper(e.exper);
                  e.formatComplex = eventFormatter.formatComplex(e.complex);
                });
                self.filteredEvents = events;

              })
              .fail(function(data) {
                console.log( "error" );
                console.log( data );
              });


          }
        },
        created: function() {
          var self = this;

          // retrieve the event constants
          var jqxhr = $.get( self.baseUrl+"api/system/constants/events")
            .done(function(data) {
              console.log( "retrieved event constants" );
              console.log( data );
              eventFormatter.constants = data;

              // fill in options for filtering
              // this.filterOptions = [];
              // console.log(data.days);
              Vue.set(self.filterOptions, 'day', data.days);
              Vue.set(self.filterOptions, 'category', data.eventType);
              Vue.set(self.filterOptions, 'age', data.ages);
              //data.tagsInUse.sort( (a,b) => (a > b) ? 1 : (a==b) ? 0 : -1 );
              Vue.set(self.filterOptions, 'tag', data.tagsInUse);

              // if the user is logged in, retrieve information about tickets for all their members
              var jqxhr = $.get( self.baseUrl+"api/user/tickets")
                .done(function(data) {
                  console.log( "retrieve members" );
                  // console.log( data );

                  data.forEach(m => {
                    m.formatName = eventFormatter.formatGmObj(m);
                  });

                  self.members = data;

                })
                .fail(function(data) {
                  // probably not logged in
                  console.log( "error" );
                  console.log( data );
                  self.members = null;
                });

                // manually trigger a search if one was provided by URL
                let searchInput = '{$searchParam}';
                if (searchInput) {
                  self.filterSearch = searchInput;
                  self.updateSearch();
                }

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

