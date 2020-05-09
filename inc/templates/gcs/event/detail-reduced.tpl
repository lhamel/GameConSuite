{if !isset($actions)}
  {$actions = []}
{/if}
{if $event.buy.icon|default:false}
  {if $event.buy.link|default:false}
    <a href="{$event.buy.link}"><img src="{$event.buy.icon}" border="0"/></a>
  {else}
    <img src="{$event.buy.icon}" border="0"/> 
  {/if}
{/if}
{if $actions.detail|default:''}<a href="{$actions.detail}{$event.id_event}">{/if}
{$event.s_number}{if $actions.detail|default:''}</a>{/if} <strong>{$event.s_game}{if $event.s_title and $event.s_title!=$event.s_game}: {$event.s_title}{/if}</strong>, GM: {if $actions.navigateMember|default:''}<a href="{$actions.navigateMember|default:''}{$event.s_lname|default:''}">{$event.gamemaster|trim}</a>{else}{$event.gamemaster|trim}{/if}, 
{$event.i_maxplayers} players, <a href="{$config.page.depth}gcs/events/expcomp.php" target="_blank">{$constants.events.experience.display[$event.e_exper]}/{$constants.events.complexity.display[$event.e_complex]}</a>, {$constants.events.days[$event.e_day]} {$constants.events.times[$event.i_time]}-{$constants.events.times[$event.endtime]}. 
{if $event.s_desc_web}{$event.s_desc_web|stripslashes}{else}{$event.s_desc|stripslashes}{/if} 
{if $event.i_agerestriction}{$constants.events.agesNoBlank[$event.i_agerestriction]}{/if}
{if $config.allow.see_location} {$event.s_room}{if $event.s_table} Table {$event.s_table}{/if}.{/if}
 {if $event.i_cost>0}${$event.i_cost}{else}Free!{/if}

