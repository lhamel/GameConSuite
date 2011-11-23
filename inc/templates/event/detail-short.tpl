{if $event.buy.icon}
  {if $event.buy.link}
    <a href="{$event.buy.link}"><img src="{$event.buy.icon}" border="0"/></a>
  {else}
    <img src="{$event.buy.icon}" border="0"/> 
  {/if}
{/if}
{if $actions.detail}<a href="{$actions.detail}{$event.id_event}">{/if}
{$event.s_number}{if $actions.detail}</a>{/if} {$event.s_game}{if $event.s_title and $event.s_title!=$event.s_game}: {$event.s_title}{/if}, GM: {if $actions.navigateMember}<a href="{$actions.navigateMember}{$event.id_gm}">{$event.gamemaster|trim}</a>{else}{$event.gamemaster|trim}{/if}, 
{$event.i_maxplayers} players, {$event.e_exper}-{$event.e_complex}, {$constants.events.days[$event.e_day]} {$constants.events.times[$event.i_time]}-{$constants.events.times[$event.endtime]}. 
{$event.s_desc|stripslashes} 
{if $event.i_cost>0}${$event.i_cost}{else}Free!{/if}
{if $config.allow.see_location} Room {$event.s_room}{if $event.s_table} Table {$event.s_table}{/if}{/if}


