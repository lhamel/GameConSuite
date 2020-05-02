{include file="gcs/reg/selectMemberDlg.tpl"}
{if $error|default:0}{strip}
<p style="background: #FF6666;">{$error}</p>
{/strip}{/if}

<table cellspacing="0" cellpadding="2" style="width: 50%">
  <tr><th colspan="2">Select Item</th></tr>

  {foreach from=$items key=k item=item}
<tr>
  <td>
  {strip}
    {if $actions.addItem|default:0 && $actions.useItemDlg|default:0}
      <a href="javascript:showSelectMemberDialog('Select Envelopes for Item','Select each member to receive {$item.description|escape: 'quotes'|escape: 'html'}','{$actions.addItem}&itemId={$item.id_prereg_item}')">
    {elseif $actions.addItem|default:0 && !($actions.useItemDlg|default:0)}
      <a href="{$actions.addItem}&itemId={$item.id_prereg_item}">
    {else}
      <a href="javascript:{$actions.javascriptFn}({$item.id_prereg_item})">
    {/if}
  <img src="{$config.page.depth}images/gcs/reg/add-cart-20.png" style="vertical-align:text-bottom;"/>
  </a>{/strip}
  {$item.description} (${$item.unit_price|string_format:"%.2f"})<br/>
  </td>
</tr>
  {/foreach}

<tr><td colspan="2">
</td></tr>
</table>
</form>
