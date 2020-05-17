<h1>{$member.s_fname} {$member.s_lname}</h1>

<h2>Order</h2>
<div class="order">

{include file="gcs/common/cart.tpl"}


<hr/>
<script type="text/javascript">

var constants = {literal}{{/literal}
  'baseUrl' : '{$config.ucon.baseUrl}',
{literal}};{/literal}

{literal}
var printer;
function Printer() {
  var self = this;
  self._wsUri = "wss://localhost:8001/";

  self.printBadge = function(badgeType,label) {
    console.log("connecting to printer");
    var websocket = new WebSocket(self._wsUri);
    websocket.onopen = function(evt) {
      console.log("connected");

      var url = constants.baseUrl + "/admin/gcs/ops/prereg/badge.php?line1=" + encodeURIComponent(label) + "&type=" + encodeURIComponent(badgeType);
      var message = {
          'orderNumber': '12345',
          'url': url
      }
      console.log("printing: " + url);
      websocket.send(JSON.stringify(message));
      websocket.close();
    };
    websocket.onclose = function(evt) {
      console.log("disconnected");
    };
    websocket.onmessage = function(evt) { console.log("message: " + message); };
    websocket.onerror = function(evt) {
      console.log("printer connection error: " + evt.data);
    };

  }

  self.printOne = function(idEvent) {
    return self.printAll('','',idEvent);
  }

  self.printSummary = function(idMember) {
    return self.printAll('','','', idMember);
  }

  // Note: IDs is comma-sep event ids, badge labels and types are also comma sep
  self.printAll = function(badgelabels,badgetypes,ids,idMember) {
    console.log("connecting to printer");
    var websocket = new WebSocket(self._wsUri);
    websocket.onopen = function(evt) { 
      console.log("connected");

      if(idMember) {
        var url = constants.baseUrl + "/admin/gcs/ops/prereg/summary.php?id_member=" + idMember;
        var message = {
          'orderNumber': '12345',
          'url': url
        }
        console.log("printing: " + url);
        websocket.send(JSON.stringify(message));
      }

      if(ids) {
        var url = constants.baseUrl + "/admin/gcs/ops/prereg/tickets.php?ticketToPrint=" + ids;
        var message = {
          'orderNumber': '12345',
          'url': url
        }
        console.log("printing: " + url);
        websocket.send(JSON.stringify(message));
      }

      if (badgetypes) {
        var url = constants.baseUrl + "/admin/gcs/ops/prereg/badge.php?line1=" + encodeURIComponent(badgelabels) + "&type=" + encodeURIComponent(badgetypes);
        var message = {
          'orderNumber': '12345',
          'url': url
        }
        console.log("printing: " + url);
        websocket.send(JSON.stringify(message));
      }

      websocket.close();
    };

    websocket.onclose = function(evt) { 
      console.log("disconnected");
    };
    websocket.onmessage = function(evt) { console.log("message: " + message); }; 
    websocket.onerror = function(evt) { 
      console.log("printer connection error: " + evt.data); 
    };
  }
}

$(function() {
  printer = new Printer();
});
{/literal}</script>


<table class="cart" border="0" cellspacing="0" style="width:80%">
  <caption>Preregistration Printing via Label Printer</caption>
  <!-- column elements -->
  <colgroup>
		<col class="printCol">
		<col class="itemsCol">
		<col class="quantityCol">
		<col class="priceCol">
		{if $actions.custom|default:0}<col class="{$actions.custom.css}">{/if}
  </colgroup>

    <thead>
	    <tr>
			  <th class="printCol"></th>
			  <th class="itemsCol">Item</th>
			  <th class="quantityCol">Quantity</th>
        <th class="priceCol">Price</th>
	    </tr>
    </thead>

    <tbody>

      <tr class="altcolor2" style="border-bottom: solid black 1px;">
        <td style="padding-left: 0; padding-bottom: 5px; padding-top: 4px; padding-right: 5px">
          <button class="button" onclick="printer.printSummary({$member.id_member});">PRINT</a>
        </td>
        <td class="itemsCol">Summary</td>
        <td class="quantityCol"></td>
        <td class="priceCol"></td>
      </tr>

    {foreach from=$cart.items item=item}{if $item.type == 'Ticket' || $item.subtype == "Generic Ticket" || $item.type == "Badge"}
      {cycle values='altcolor1,altcolor2' assign='rowAlternation'} 

    <tr class="{$rowAlternation}" style="border-bottom: solid black 1px;">
      <td style="padding-left: 0; padding-bottom: 5px; padding-top: 4px; padding-right: 5px">{strip}
{if $item.type=="Badge"}
        <button class="button" onclick="printer.printBadge('{$item.subtype}','{$item.special}');">PRINT</a>
{else}
        <button class="button" onclick="printer.printOne({if $item.event.id_event}{$item.event.id_event}{else}'generic'{/if});">PRINT</a>
{/if}
      {/strip}</td>
		  <td class="itemsCol">{$item.type} - {strip}
		    {if $item.event|default:0}
		      {$item.event.s_game}
		      {if $item.event.s_title!=$item.event.s_game}: {$item.event.s_title} #{$item.subtype}{/if}
		    {else}
		      {$item.subtype}
		    {/if}
		  {/strip}{if $item.type=='Badge'}<br/><span {if $actions.ajax}class="editable"{/if} id="special-{$item.id_order}">{$item.special}</span>{/if}
		  </td>
      <td class="quantityCol">{$item.quantity}</td>
      <td class="priceCol">${$item.price}</td>
    </tr>
    {/if}{/foreach}

    </tbody>
</table>

{strip}
<button onclick="printer.printAll('{$allBadgeLabels}','{$allBadgeTypes}','{$allTicketIds}','{$member.id_member}')">Print All</button>
{/strip}
</div>
