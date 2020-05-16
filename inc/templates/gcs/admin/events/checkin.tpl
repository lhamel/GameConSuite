<h1>Checkin Event</h1>
<p>scan the barcode and enter the information.  This will overwrite any information in the database, if there was any at all</p>

<hr>

{if isset($ribbon)}
<p style="background: #009900; color: #ffffff; font-weight=bold">{$ribbon}</p>
{/if}


{if !$event}

  <form name="checkinForm" action="{$config.page.depth}{$config.page.location}" method="post">
    <p>Scan Barcode</p>
    <p><input type="text" id="scan" name="scan"/></p>
  </form>

  {literal}
  <script type="text/javascript">
  <!--
  $(function() {
    $('#scan').focus();
  });

  </script>
  {/literal}


{else}

{literal}
<style>
table.checkin {
  font-size: larger;
  margin-top: 12px;
  margin-bottom: 12px;
  border: solid black 2px;
  padding: 4px;
  background: #eeeeee;
  width: 100%;
}

td {
  padding: 1px 4px 1px 1px;
}

form.checkinForm, form.checkinForm input, form.checkinForm select, form.checkinForm button {
  font-size: 14pt;
}

.tablebutton {
  margin: 2px 2px 2px 2px;
}

</style>

<!-- note that values are hard coded here -->
<script type="text/javascript">
function updatePrize(val,cost) {
  var m = val*cost;
  var token = 0;
  if (m >= 6 && m <=14) { token = 1; }
  else if (m >=15 && m <=24) { token = 2; }
  else if (m >= 25) { token = 3; }
  $('#b_prize').val(token);
}


$('#i_actual').change( function() {
  var val = $('#i_actual').val();
  $('#b_prize').val(val);

});


</script>
{/literal}


<table class="checkin" cellspacing="0" cellpadding="0">
<tr><td align="right">Event#:</td><td>{$event.id_event}</td></tr>
<tr><td align="right">GM:</td><td>{$event.s_lname}</td></tr>
<tr><td align="right">Game:</td><td>{$event.s_game}{if $event.s_game && $event.s_title}: {/if}{$event.s_title}</td></tr>
<tr><td align="right">Time:</td><td>{$constants.events.days[$event.e_day]} {$constants.events.times[$event.i_time]}-{$constants.events.times[$event.endtime]}</td></tr>
<tr><td align="right">Table:</td><td>{$event.s_room} {$event.s_table}</td></tr>
<tr><td align="right">Cost:</td><td>${$event.i_cost}</td></tr>
</table>


<form class="checkinForm" name="checkinForm" action="{$config.page.depth}{$config.page.location}?id_event={$event.id_event}" method="post"> <!-- onSubmit="return false">-->
<input type="hidden" name="mobile" value="{$mobile}" />
<input type="hidden" name="action" value="{$actions.save}" />
<input type="hidden" name="id_event" value="{$event.id_event}" />
<input type="hidden" name="barcode" value="{$barcode}" />

<table cellspacing="0" cellpadding="2">

<!--
<tr valign="top">
<td align="left">Barcode</td>
<td><input type="text" name="barcode" {if $event}disabled="disabled"{/if} value="{$barcode}"/></td>
</tr>
-->

<tr valign="top">
<td align="left">GM Showed Up?</td>
<td><select name="b_showed_up">
<option value="1" {if $event.b_showed_up}selected="selected"{/if}>yes</option>
<option value="0" {if !$event.b_showed_up}selected="selected"{/if}>no</option>
</select>
</td>
</tr>

<tr valign="top">
<td align="left">Number of Players? (without GM)</td>
<td><input type="text" id="i_actual" name="i_actual" value="{$event.i_actual}" /></td>
</tr>
<tr valign="top">
<td align="left" colspan="2">
{for $i=0 to 6}
<button type="text" class="tabletbutton" onClick="$('#i_actual').val({$i});updatePrize({$i},{$event.i_cost});return false;">{$i}</button>
{/for}

</td>
</tr>


<tr valign="top">
<td align="left"># Prize Tokens (enter zero if none)</td>
<td><input type="text" id="b_prize" name="b_prize" value="{$event.b_prize}" />
{*
<select name="b_prize">
<option value="1" {if $event.b_prize}selected="selected"{/if}>yes</option>
<option value="0" {if !$event.b_prize}selected="selected"{/if}>no</option>
</select>
*}
</td>
</tr>
<tr valign="top">
<td align="left" colspan="2">
{for $i=0 to 3}
<button class="tabletbutton" onClick="$('#b_prize').val({$i});return false;">{$i}</button>
{/for}
<button onClick="updatePrize($('#i_actual').val(), {$event.i_cost})"><span style="font-size:10pt">manual</span></button>
</td>
</tr>


<tr valign="top">

      <td align="left" colspan="2">Any notes<br />
        <textarea style="width:100%;" name="s_note" rows="3" cols="40">{$event.s_note}</textarea></td>
      </tr>

        <tr><td align="center" colspan="2">
<button onClick="document.checkinForm.submit()">Check In</button>
{* <button name="cancel" onClick="location.href='{$config.page.depth}{$config.page.location}'">CANCEL</button> *}
        </td></tr>
      </table>
    </form>

{literal}
<script type="text/javascript">
<!--
$(function() {
  $('#i_actual').focus();
});

  $('#i_actual').keypress(function(event) {
    if (event.keyCode == 13 || event.which == 13) {
      document.checkinForm.submit();
      event.preventDefault();
    }
  });


-->
</script>
{/literal}


{/if}

