{assign var=eventCount value=$events|@count}
{assign var=count value=0}
{if $eventCount > 0}

<table style="width:100%">
  <tr>
    <th>Event</th>
    <th>Time</th>
    {if isset($actions.moveEvent) && isset($newGm)}<th></th>{/if}
  </tr>

{foreach from=$events key=k item=event}
    {assign var=count value=$count+1}
    {cycle values='1,2' assign='alt'} 
    <tr class="altrow{$alt}">
        <td>{strip}
          <a href="{$actions.eventView}{$event.id_event}">
            {$event.s_game}{if $event.s_title && $event.s_title != $event.s_game} {$event.s_title}{/if}
          </a>
        {/strip}</td>
        <td>{strip}
            {$constants.events.daysWithBlank[$event.e_day]} {$constants.events.timesWithBlank[$event.i_time]}-{$constants.events.timesWithBlank[$event.endtime]}
        {/strip}</td>
        {if $actions.moveEvent && $newGm}
            <td><a href="{$actions.moveEvent}&id_gm={$newGm}&id_event={$event.id_event}">Move Event</a></td>
        {/if}
    </tr>
{/foreach}
</table>

{else}
  Nothing found
{/if}
