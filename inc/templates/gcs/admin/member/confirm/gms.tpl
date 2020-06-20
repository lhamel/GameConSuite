GM Events
----------------------------------------
{foreach from=$events key=k item=event}
{if !$event.b_approval}TENTATIVE - {/if}{$constants.events.daysWithBlank[$event.e_day]} {$constants.events.timesWithBlank[$event.i_time]} - {$event.s_game} {$event.s_title} (#{$event.s_number})
{/foreach}

