Ticket Purchases
----------------------------------------
{foreach from=$cart.items key=k item=item}
{if isset($item.event)}
{$item.event.e_day} {$constants.events.times[$item.event.i_time]} - {$item.event.s_game}{if $item.event.s_title!=$item.event.s_game}: {$item.event.s_title}{/if} ({$item.event.id_event}, {$item.event.s_number}) (${$item.price|string_format:"%.2f"} x{$item.quantity}) ${$item.quantity*$item.price|string_format:"%.2f"}
{/if}
{/foreach}

Other Purchases
----------------------------------------
{foreach from=$cart.items key=k item=item}
{if !isset($item.event)}
{$item.subtype} {$item.type}: {if $item.special}{$item.special} {/if}(${$item.price|string_format:"%.2f"} x{$item.quantity}) ${$item.quantity*$item.price|string_format:"%.2f"}
{/if}
{/foreach}

