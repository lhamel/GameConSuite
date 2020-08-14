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


include INC_PATH.'resources/event/constants.php';
include INC_PATH.'smarty.php';
include INC_PATH.'layout/menu.php';

$actions = array();

$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);

if (isset($_REQUEST['update'])) {
  $ribbon = '<p class="ribbon">'.$_REQUEST['update'].'</p>';
} else {
  $ribbon = '<p></p>';
}


// render the page
$content = <<< EOD


    <!-- demo root element -->
    <div id="demo">

      <h2>Envelope: {{fullName}}</h2>
      $ribbon

      <h3>Preregistration Order</h3>
      <prereg-order
        :cart="cartData"
        :event-formatter="eventFormatter"
        :base-url="baseUrl"
        @update-schedule="updateMemberSchedule"
      >
      </prereg-order>

      <h3>Payments</h3>
      <pay-balance
        :cart="cartData"
        :pending-payment-amount="pendingPaymentAmount"
      >
      </pay-balance>

      <h3>GM Events</h3>
      <gamemaster-events
        :gmevents="gridData"
        :event-formatter="eventFormatter"
        :base-url="baseUrl"
      >
      </gamemaster-events>

      <h3>Combined Schedule</h3>
      <schedule-list
        :schedule="scheduleData"
        :event-formatter="eventFormatter"
        :base-url="baseUrl"
      >
      </schedule-list>
    </div>


<script type="text/x-template" id="prereg-order-template">
<div>

<table class="striped" border="0" cellspacing="0" cellpadding="1" width="100%">
<thead>
  <tr>
    <th style="white-space: nowrap;">Description</th>
    <th class="numeric" style="white-space: nowrap;">Quantity</th>
    <th class="numeric" style="white-space: nowrap;">Unit Price</th>
    <th class="numeric" style="white-space: nowrap;">Total</th>
    <!--<th style="white-space: nowrap;">Balance</th>-->
    <th style="white-space: nowrap;">&nbsp;</th><!-- spacer -->
    <th style="white-space: nowrap;"></th>
  </tr>
</thead>

<tbody>
  <tr v-for="entry in formatCart">

    <td>
      <span v-if="entry.type == 'Badge'">
        {{entry.type}}: {{entry.subtype}} - {{entry.special}}
      </span>
      <span v-if="entry.type == 'Ticket'">
        {{entry.type}}: {{entry.event.formatTitle}} (#{{entry.event.id}}, {{entry.event.formatStartTime}})
      </span>
      <span v-if="entry.type == 'Payment'">
        ***Payment Applied: {{entry.subtype}} {{entry.special}}
      </span>
      <span v-if="entry.type != 'Badge' && entry.type != 'Ticket' && entry.type != 'Payment'">
        {{entry.type}}: {{entry.subtype}}
      </span>
    </td>

    <td class="numeric">{{entry.quantity}}</td>
    <td class="numeric">{{(entry.price*1.0).toFixed(2)}}</td>
    <td class="numeric">\${{(entry.quantity * entry.price).toFixed(2)}}</td>

    <!-- running balance
    <td></td> -->

    <!-- spacer -->
    <td></td>

    <td>
      <button v-if="entry.type!='Payment'" class="fa-button" style="white-space: nowrap;" @click="currEntry=entry;showConfirmDialog=true">
        <span v-if="entry.type=='Ticket'">
          <i class="fas fa-calendar-times"></i> <span style="font-size:smaller">RELEASE</span>
        </span>
        <span v-else>
          <i class="fas fa-trash"></i> <span style="font-size:smaller">REMOVE</span>
        </span>
      </button>
    </td>
  </tr>

  <tr class="cart-balance">
    <td></td>
    <td></td>
    <td class="numeric">Total</td>
    <td class="numeric"><strong>\${{balance}}</strong></td>
    <!--<td class="numeric">Balance</td>-->
    <td></td>
    <td></td>
  </tr>

</tbody>
</table>

<confirmation-dialog v-if="showConfirmDialog" title="Confirm item should be removed?" :description="currEntry.type+': '+(currEntry.type=='Ticket'? currEntry.event.formatTitle+' #'+currEntry.event.id : currEntry.subtype)" @close="showConfirmDialog=false" @confirm="showConfirmDialog=false;removeItem(currEntry)"></confirmation-dialog>

</div>
</script>

<script type="text/x-template" id="pay-balance-template">
<div>

<p v-if="pendingPaymentAmount>0">A payment for \${{pendingPaymentAmount.toFixed(2)}} is pending.  It may take up to 3 business days for us to credit your account.  Meanwhile, the amount has already been deducted from your balance below.</p>


<p v-if="balance==0">No payment due.</p>


<div v-if="balance<0">
<p>Our records show that you have a refund due.  Feel free to add additional items into your account.  Please contact us to request an early refund.</p>

<p>Refund owed: \${{balance*-1}}</p>

</div>


<div v-if="balance>0">

<p>Our records show that you have a balance due.  <span style="font-weight:bolder">Please wait until you've got all your items in your cart before you check out!</span><!--'--></p>

<p>Balance due: <b>\${{balance}}</b></p>

<table><tbody><tr>
  <th style="width:46%">Pay by credit card or PayPal</th>
  <th>Pay by check</th>
</tr><tr><td>

  <p>Make a credit card or PayPal payment for <b>\${{balance}}</b>: </p><form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="{$config['email']['paypal']}">
<input type="hidden" name="item_name" value="{$config['gcs']['name']} {$config['gcs']['year']} Registration #{$id_member}">
<input type="hidden" name="item_number" value="Member #{$id_member}">
<input type="hidden" name="amount" :value="balance">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="lc" value="US">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but01.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<input type="hidden" name="add" value="1">
</form>

<p></p>
  <p><b>Payments will not appear immediately!</b><br>Please give us 3 business days to update our records, then notify us of any discrepencies.</p>

</td><td>

<p>Alternately, you may submit a check by postal mail to our P.O. Box, however it will
  take longer for us to receive and process your payment.  Please make checks payable 
  to <b>U-Con Gaming Club</b> and include the name on your 
  registration with the check.  Never send cash through the mail!</p>

  <p>Attn: Registration<br>
  U-Con Gaming Convention<br>PO Box 130242<br>Ann Arbor, MI 48113-0242</p>
</td></tr></tbody></table>

</div>

</div>
</script>

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
<td>{{ entry.fill }}</td>
<td><button @click="currEvent = entry; showVTTDialog = true">Provide VTT</button></td>

  </tr>
</tbody>
</table>

<vtt-dialog v-if="showVTTDialog" :eventData="currEvent" v-bind:edit="true" @close="showVTTDialog = false" @saveAndClose="saveEvent"></vtt-dialog>

</div>
</script>


    <script type="text/x-template" id="confirmation-dialog-template">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">

              <div class="modal-header">
                <h3>{{title}}</h3>
              </div>

              <div class="modal-body">
                <p>{{description}}</p>
              </div>

              <div class="modal-footer">
                <slot name="footer">
                  &nbsp;
                  <button class="modal-default-button" @click="\$emit('close')">
                    Cancel
                  </button>
                  <button class="modal-default-button" @click="\$emit('confirm', payload)">
                    OK
                  </button>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </script>



    <script type="text/x-template" id="vtt-dialog-template">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-container">

              <div class="modal-header">
                <h3>Title: {{eventData.formatTitle}}</h3>
              </div>

              <div class="modal-body">

                <!-- event details -->
                <ul>
                <li>Event #{{eventData.id}}</li>
                <li>Price: \${{eventData.cost}}</li>
                <li>Seats: {{eventData.formatPlayers}}</li>
                <li>Time: {{eventData.formatTime}}</span></li>
                </ul>
                <hr>

                <div v-if="edit">
                <p>Provide <em>Virtual Table Top (VTT)</em> link (e.g. Roll20 or Zoom link)</p>
                <input id="vttlink" type="text" v-model="vttLink">
                <p>Provide additional instructions or information for players, including platforms as well as required accounts and software.</p>
                <textarea id="vttinfo" v-model="vttInfo"></textarea>
                </div>
                <div v-else>
                <p>This is the information which your GM provided for connecting.  Note: not reviewed by staff; please report any abuse to staff.</p>
                <hr>
                <a :href="vttLink">{{vttLink}}</a>
                <p>{{vttInfo}}</p>
                </div>
              </div>

              <div class="modal-footer">
                <slot name="footer">
                  &nbsp;
                  <span v-if="edit">
                  <button class="modal-default-button" @click="\$emit('close')">
                    Cancel
                  </button>
                  <button class="modal-default-button" @click="\$emit('saveAndClose', vttLink, vttInfo)">
                    OK
                  </button>
                  </span>
                  <span v-else>
                  <button class="modal-default-button" @click="\$emit('close')">
                    Close
                  </button>
                  </span>
                </slot>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </script>

    <script type="text/x-template" id="schedule-list-template">
<div>
<table class="striped" border="0" cellspacing="0" cellpadding="1" width="100%">
<thead>
  <tr>
    <th>Time</th>
    <th>Type</th>
    <th>System/Title</th>
    <th>Gamemaster</th>
    <th>Location</th>
  </tr>
</thead>
<tbody>
  <tr v-for="entry in formatSchedule">
    <td>{{entry.event.formatTime}}</td>
    <td><span v-if="entry.ticket">{{entry.ticket.quantity}}&nbsp;Ticket</span><span v-else>GM</span></td>
    <td>{{entry.event.formatTitle}}</td>
    <td>{{entry.event.formatGM}}</td>
    <td>
      <span v-if="entry.event.room">{{entry.event.room.label}} {{entry.event.table}}</span>
      <span v-if="entry.event.vttLink"><button @click="currEvent = entry.event; showVTTDialog = true">See VTT</button></span>
      <span v-else-if="entry.event.vttInfo"><button @click="currEvent = entry.event; showVTTDialog = true">See VTT</button></span>
    </td>
  </tr>
</tbody>
</table>

<vtt-dialog v-if="showVTTDialog" :eventData="currEvent" v-bind:edit="false" @close="showVTTDialog = false"></vtt-dialog>

</div>
    </script>


    <script src="{$config['page']['depth']}js/gcs/events.js"></script>
    <script>

      Vue.component("prereg-order",{
        template: "#prereg-order-template",
        props: {
          cart: Array,
          eventFormatter: Object,
          baseUrl: String,
        },
        data: function() {
          return {
            showConfirmDialog: false,
            currEntry: null,
          };
        },
        computed: {
          balance : function() {
            let s = this.cart;
            let b = 0;
            s.forEach(e => {
              console.log(e);
              b += e.quantity*e.price;
            });
            return b.toFixed(2);
          },
          formatCart : function() {
            let ef = this.eventFormatter;
            let s = this.cart;
            console.log('formatCart');
            console.log(s);
            s.forEach(e => {
              if (e.event) {
                e.event.formatTitle = ef.formatTitle(e.event);
                e.event.formatStartTime = ef.formatDay(e.event.day).substring(0,3) + ' ' + ef.formatSingleTime(e.event.time);
              }
            });
            return s;
          }
        },
        methods:
        {
          removeItem : function(entry)
          {
            console.log("remove item ")
            console.log(entry);

            if (entry.type=="Payment") {
              alert("Error: payments cannot be remove by this method");
            }


            let self = this;

            // remove entry with call to API
            $.ajax({
               type: 'DELETE',
               url: this.baseUrl+"api/user/envelope/{$id_member}/cart/" + entry.id,
               contentType: 'application/json',
            })
              .done(function(data) {
                console.log( "success" );
                console.log( data );

                // self.showConfirmDialog = false;

                // update the cart and schedule
                self.\$emit('update-schedule');
              })
              .fail(function(data) {
                console.log( "error" );
                console.log( data );
                alert("Error: removal failed");
              });

          }
        }
      });

      Vue.component("pay-balance",{
        template: "#pay-balance-template",
        props: {
          cart: Array,
          pendingPaymentAmount: Number,
        },
        computed: {
          balance : function() {
            let s = this.cart;
            let b = -this.pendingPaymentAmount;
            s.forEach(e => {
              console.log(e);
              b += e.quantity*e.price;
            });
            return b.toFixed(2);
          },
        }
      });

      Vue.component("schedule-list", {
        template: "#schedule-list-template",
        props: {
          schedule: Array,
          eventFormatter: Object,
        },
        data : function () {
          return {
            showVTTDialog: false,
            currEvent : null,
          };
        },
        computed: {
          formatSchedule :function() {
            let s = this.schedule;
            s.forEach(e => {
              e.event.formatTitle = this.eventFormatter.formatTitle(e.event);
              e.event.formatPlayers = this.eventFormatter.formatPlayers(e.event);
              e.event.formatGM = this.eventFormatter.formatGmObj(e.event.gm);
              e.event.formatTime = this.eventFormatter.formatTime(e.event);
            });
            console.log(s);
            return s;
          }
        }
      });

      Vue.component("confirmation-dialog", {
        template: "#confirmation-dialog-template",
        props: {
          title : String,
          description : String,
          payload: Object,
        },
      });


      Vue.component("vtt-dialog", {
        template: "#vtt-dialog-template",
        props: {
          eventData : Object,
          edit: Boolean,
        },
        data: function () {
          return {
            vttLink: this.eventData.vttLink,
            vttInfo: this.eventData.vttInfo,
          }
        },
      });

      Vue.component("gamemaster-events", {
        template: "#gamemaster-events-template",
        props: {
          gmevents: Array,
          eventFormatter: Object,
          baseUrl: String
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
              e.formatTitle = this.eventFormatter.formatTitle(e);
              e.formatPlayers = this.eventFormatter.formatPlayers(e);
              e.formatGM = this.eventFormatter.formatGmObj(e.gm);
              e.formatTime = this.eventFormatter.formatTime(e);
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
        methods: {
          saveEvent: function(vttLinkValue, vttInfoValue) {
            console.log("save click");
            console.log(this.currEvent.id);
            console.log(vttLinkValue);
            console.log(vttInfoValue);

            this.currEvent.vttLink = vttLinkValue;
            this.currEvent.vttInfo = vttInfoValue;
            var self = this;

            let patch = {
              'vttLink' : vttLinkValue,
              'vttInfo' : vttInfoValue
            }

            // TODO save values back to event with call to API
            $.ajax({
               type: 'PATCH',
               url: this.baseUrl+"api/user/envelope/{$id_member}/event/" + this.currEvent.id,
               data: JSON.stringify(patch),
               processData: false,
               contentType: 'application/json',

               done: function(data) {
                console.log( "success" );
                console.log( data );
                this.showVTTDialog = false;
               },
               fail: function(data) {
                console.log( "error" );
                console.log( data );
                alert("Error: values can not be saved");
                },
            })
              .done(function(data) {
                console.log( "success" );
                console.log( data );

                self.showVTTDialog = false;
              })
              .fail(function(data) {
                console.log( "error" );
                console.log( data );
                alert("Error: values can not be saved");
              });

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
          gridData: [],
          cartData: [],
          fullName: '',
          pendingPaymentAmount: 0,
          scheduleData: [],
          eventFormatter: eventFormatter,
          baseUrl: '{$config['page']['depth']}',
        },
        methods:
        {
          updateMemberSchedule : function() {

            // retrieve items int the cart
            var jqxhr = $.get( this.baseUrl+"api/user/envelope/{$id_member}/cart")
              .done(function(data) {
                console.log( "success" );
                console.log( data );

                demo.cartData = data.items;
                demo.fullName = self.eventFormatter.formatGmObj(data.member);
                demo.pendingPaymentAmount = Number(data.pendingPaymentAmount);
              })
              .fail(function(data) {
                console.log( "error" );
                console.log( data );
              });

            // retrieve the combined schedule
            var jqxhr = $.get( this.baseUrl+"api/user/envelope/{$id_member}/schedule")
              .done(function(data) {
                console.log( "success" );
                console.log( data );
                demo.scheduleData = data;
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

              // retrieve the GM events list
              var jqxhr = $.get( self.baseUrl+"api/user/envelope/{$id_member}/event")
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

                self.updateMemberSchedule();

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


