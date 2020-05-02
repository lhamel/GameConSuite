<script type="text/javascript">

var soldOut = ["{$cart_soldout.0}", "{$cart_soldout.1}"];
var avail = ["{$cart_avail.0}", "{$cart_avail.1}"];
var cartImg = [avail, soldOut];

// TODO load members and ticket selections
var currentEvent = null;
var ticketSelection = {$loginInfo.ticketSelection|json_encode};


{literal}

//$(document).ready(function() {

    function requestAddTicket(idEvent, idMember) {
      console.log("request Add Ticket "+idEvent + "/" + idMember);
      var jqxhr = $.get( "../reg/_addTicket.php", {itemId: idEvent, id_member:idMember, action:'addTicket'})
        .done(function(data) {
          console.log( 'ticket added successfully' );

          // replace ticket selection
          ticketSelection = jQuery.parseJSON(data);
          console.log(ticketSelection);

          // refresh the ticket selections
          refreshEvent();
        })
        .fail(function(data) {
          console.log( "error: from ../reg/_addTicket.php");
          console.log( data );
          //alert("Error: "+data.responseText);
          $("#eventtable").html('<p style="background:navy;font-weight:bolder;color:#fff">'+data.responseText+'</p>');
        })
        .always(function() {
          console.log( "finished" );
        });

        // update ticketSelection

        // refresh display


// reg/_addTicket.php?action=addTicket&itemId=7381&id_member=7031

    }

    function refreshEvent() {
      console.log("refreshEvent()");

      // reset dialog display
      $('#fetching').show();
      $('#eventtable').hide();
      $('#soldout').hide();
      for(var mId in ticketSelection) {
        $('#mem_'+mId).html('');
      }

      // retrieve lastest info and display
      var id = currentEvent;
      var jqxhr = $.get( "_event.php", {id_event: id})
        .done(function(data) {
          // clear the form and set values
          var event = jQuery.parseJSON(data);
          var soldout = event.i_maxplayers <= event.tixSold;
          var id = event.id_event;

          for (var k in event) {
            //console.log(k + " " + event[k]);
            $("#event_"+k).html(event[k]);
          };

          $('#eventtable').show();
          if (soldout) { $('#soldout').show(); }
          $('#fetching').hide();

          // construct the buttons and click state
          console.log("setting.icons..");
          for(var mId in ticketSelection) {
            console.log("has: mem_" + mId + " " + ticketSelection);
            var has = ticketSelection[mId][id];
            has = (has && has > 0) ? 1 : 0;

            var onclick = '';
            if (!has && !soldout) {
              onclick = ' onclick="requestAddTicket('+id+','+mId+')"';
            }
            $('#mem_'+mId).html('<img src="'+cartImg[(soldout?1:0)][has]+'" '+onclick+'>');
          }

          console.log( "second success" );
        })
        .fail(function(data) {
          console.log( "error: "+data );
        })
        .always(function() {
          console.log( "finished" );
        });

    }

//  $( function() {
    function showEventTicketDialog(id, title, itemDesc, submitUrl) {
      console.log("show event details and ticket selection dialog");

      currentEvent = id;
      refreshEvent();

      try {
        $( "#eventTicketDialog" ).dialog('option', 'title', title);
      } catch (e) {
        $( "#eventTicketDialog" ).attr('title',title);
      }

      $( "#eventTicketDialog" ).dialog({
        width: 600,
        buttons: [
          { text: "Done", click: function() {
              $(this).dialog("close");
            } }
        ]
      });

    }
//  });

//});

{/literal}</script>

<div id="eventTicketDialog" title="Select" style="display:none">

<p id="fetching">loading...</p>

<!-- event details -->
<table id="eventtable" style="width:100%; text-align: left; vertical-align:top; ">
<tr>
  <td style="width:80%">#<span id="event_id_event"></span></td>
  <td>$<span id="event_i_cost"></span></td>
</tr>
<tr>
  <td><span id="event_format_title" style="font-weight: bolder;"></span></td>
  <td><span id="event_format_players"></span> players</td>
<tr>
  <td id="event_format_gamemaster"></td>
  <td><span id="event_format_time"></span></td>
</tr>
</table>

<p id="soldout" style="background: #800000; color: white; font-weight: bolder;">Sold out</p>

{assign var='tableItems' value=$members}
{assign var='columns' value=['radio'=>'','name'=>'']}
{assign var='columnsAlign' value=['radio'=>'left','name'=>'left']}

{if $loginInfo.loggedin}
{if empty($members)}
<p>No envelopes are found.  Please go to My Registration to create an envelope so one can be selected here!</p>
{else}

<p style="text-align:left;margin-bottom:0px;margin-top:6px;">Select envelopes to receive tickets:</p>
{*<form id="selectMemberForm" method="post" action="{$actions.addItem}" style="width:250px" class="auth">*}
<!--<input type="hidden" name="action" value="selectMember">-->
{include file='gcs/common/general-table.tpl'}
<!--<input type="submit" value="Select Envelope">-->
{$additionalFormContent}
{*</form>*}

<p style="text-align:left;margin-bottom:2px;">Items will be added to specified envelopes.  See "My Registration" to view each envelope.</p>
{/if}
{else}
<p style="background:#000080; color:white; font-weight:bolder;">Log in to add tickets.</p>
{/if}

</div>

