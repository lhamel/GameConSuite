{if $actions.ajax}
<script type="text/javascript">{literal}

var alterPriceDialog;

$(document).ready(function() {
    $('.editable').editable('{/literal}{$actions.ajax}&id_member={$member.id_member}{literal}', {
        indicator : 'Saving...',
        tooltip   : 'Click to edit...',
        //cancel    : 'Cancel',
        //submit    : 'OK',
        id : 'field',
        name : 'new',
        style : 'display: inline',
        onerror: function(settings, original, xhr) {
           original.reset();
           alert(xhr.responseText);
        },
    });

    var allFields = $( [] ).add( $("#pw") ).add ( $("#price") );

    function changePrice() {
      console.log("changePrice");
      orderId = $("#itemOrderId").val();
      price = $("#price").val();
      pw = $("#pw").val();

      data = orderId+ "|" + price + "|" + pw;
      console.log(data);
      var url = "{/literal}{$actions.alterPrice}{literal}";
      $.post(url, data, function(response) {
        $('#unitPrice'+orderId).html('$'+response);
        console.log('success ' + response);
      }).fail(function(xhr){
        console.log(xhr);
        alert("Failed: " + xhr.responseText);
      });
      dialog.dialog("close");
    }


    var dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 350,
      width: 400,
      modal: true,
      buttons: {
        "Change Price": changePrice,
        Cancel: function() {
          //form[0].reset();
          allFields.removeClass( "ui-state-error" );
          dialog.dialog( "close" );
        }
      },
      close: function() {
      }
    });

    form = dialog.find("form").on("submit", function ( event ) {
    });

    alterPriceDialog = function(itemOrderId) {
      console.log("alterPriceDialog: "+itemOrderId);
      $('#itemOrderId').val(itemOrderId);
      $('#price').val("");
      dialog.dialog("open");
    }

});
{/literal}</script>
{/if}

{strip}{if $error|default:0}
<p style="background: #ff6666; font-weight: bold; padding: 3px;">
Error: {$error}
<p>
{/if}
{/strip}
{strip}{if $success|default:''}
<p style="background: #66ff66; font-weight: bold; padding: 3px;">
{$success}
<p>
{/if}
{/strip}

{if $actions.updateQuantity}
<form action="{$actions.updateQuantity}{if $member}?id_member={$member.id_member}{/if}" method="POST">
<input type="hidden" name="action" value="updateQuantity" />
{/if}

<table class="cart" cellspacing="0" cellpadding="0">
<colgroup>
  <col class="removeCol">
  <col class="itemsCol">
  <col class="quantityCol">
  <col class="priceCol">
  <col class="totalCol">
  {if $actions.custom|default:0}<col class="{$actions.custom.css}">{/if}
</colgroup>
<tr>
  <th class="removeCol"></th>
  <th class="itemsCol">Item</th>
  <th class="quantityCol">Quantity</th>
  <th class="priceCol">Unit Price</th>
  <th class="totalCol">Total</th>
  {if $actions.custom|default:0}<th class="{$actions.custom.css}">{$actions.custom.title}</th>{/if}
</tr>
{foreach from=$cart.items key=k item=item}
<tr class="item">{* TODO move action info to template *}
  <td class="removeCol">{strip}
  {if $actions.updateQuantity && $item.type!="Payment"}
  <a href="{$actions.updateQuantity}?{if $member}id_member={$member.id_member}&{/if}action=removeItem&cartId={$k}"><img src="{$config.page.depth}images/gcs/reg/remove.png" title="remove from cart"></a>
  {/if}
  {/strip}</td>
  <td class="itemsCol">{$item.type} - {strip}
    {if $item.event|default:0}
      {$item.event.s_game}
      {if $item.event.s_title!=$item.event.s_game}: {$item.event.s_title}{/if} {if $actions.viewEvent}<a href="{$actions.viewEvent}{$item.subtype}">#{$item.subtype}</a>{else}#{$item.subtype}{/if}
    {else}
      {$item.subtype}
    {/if}
  {/strip}{if $item.type=='Badge'}<br/><span {if $actions.ajax}class="editable"{/if} id="special-{$item.id_order}">{$item.special}</span>{/if}
  </td>
  <td class="quantityCol">{strip}
{if $item.type=='Payment'}
{elseif $actions.updateQuantity && $item.type!='Badge'}
  <input class="numeric" type="text" name="quantity[{$k}]" value="{$item.quantity}" size="2"/>
{else}
  {$item.quantity}
{/if}
  {/strip}</td>
  <td class="priceCol">{if $item.type!='Payment'}

{if $actions.alterPrice}<a href="javascript:alterPriceDialog({$item.id_order})" id="unitPrice{$item.id_order}" name="unitPrice{$item.id_order}">{/if}
${$item.price|string_format:"%.2f"}
{if $actions.alterPrice}</a>{/if}

{/if}</td>
  <td class="totalCol">${$item.quantity*$item.price|string_format:"%.2f"}</td>
  {if $actions.custom|default:0}
    <td class="{$actions.custom.css}">
      {if $actions.custom.url}<a href="{$actions.custom.url}&target={$target}&id_order={$item.id_order}">{/if}
      {$actions.custom.text}
      {if $actions.custom.url}</a>{/if}
    </td>
  {/if}
</td>
{/foreach}

<tr class="itemTotal">
  <td class="itemsCol" colspan="2">Total for Items</td>
  <td class="quantityCol">{strip}
{if $actions.updateQuantity}
  <input type="submit" class="button" value="Update Quantity"/>
{/if}
  {/strip}</td>
  <td class="priceCol"></td>
  <td class="totalCol">${$cart.itemTotal|string_format:"%.2f"}</td>
</tr>

{foreach from=$cart.payments key=k item=item}
<tr class="payment">
  <td class="removeCol"></td>
  <td class="itemsCol">{$item.method} / {$item.notes}</td>
  <td class="quantityCol"></td>
  <td class="priceCol"></td>
  <td class="totalCol">${$item.credit|string_format:"%.2f"}</td>
</td>
{/foreach}

{if $cart.paymentTotal}
<tr class="paymentTotal">
  <td class="itemsCol" colspan="2">Total Paid</td>
  <td class="quantityCol"></td>
  <td class="priceCol"></td>
  <td class="totalCol">${$cart.paymentTotal|string_format:"%.2f"}
</tr>
{/if}

<tr class="due">
  <td colspan="4">Due{if $cart.paymentTotal>$cart.itemTotal} (refund){/if}</td>
  <td class="due">${$cart.itemTotal-$cart.paymentTotal|string_format:"%.2f"}</td>
</tr>

</table>
</form>

{if $actions.addPayment}

<h2>Add Payment</h2>
<form method="GET" action="{$actions.addPayment}">
  <input type="hidden" name="id_member" value="{$member.id_member}" />
  <input type="hidden" name="action" value="addPayment" />
  payment type <input name="subtype" type="text" />
  brief notes <input name="notes" type="text" />
  amount <input name="amount" type="text" />
  <input type="submit" value="add payment"/>
<form>

{/if}

{if $actions.alterPrice}
<div id="dialog-form" title="Alter Price">
  <p class="validateTips">Please enter the password and the new price:</p>

  <form id="priceForm">
    <fieldset>
      <input id="itemOrderId" type="hidden" name="itemOrderId" />

      <label for="pw">Password</label>
      <input type="password" name="pw" id="pw" class="text ui-widget-content ui-corner-all">

      <label for="price">New Price</label>
      <input type="text" name="price" id="price" value="" class="text ui-widget-content ui-corner-all">

      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>
{/if}


