{assign var=currentCategory value=zzz}
{assign var=currentDay value=zzz}
{foreach from=$events key=k item=event}
{strip}{if $event.s_abbr neq $currentCategory}
{assign var=currentCategory value=$event.s_abbr}
{assign var=currentDay value=zzz}
{assign var=currentHour value=0}
<h2>{$event.s_type}</h2>
{/if}{/strip}
{strip}{if $event.e_day neq $currentDay}
{assign var=currentDay value=$event.e_day}
{assign var=currentHour value=0}
<p style="font-weight:bold;font-size:larger;">{$constants.events.days[$event.e_day]}</h3>
{/if}{/strip}
{if $event.i_time neq $currentHour}
{assign var=currentHour value=$event.i_time}
<p style="font-weight:bold;border-bottom:solid black 1px;">{$constants.events.times[$event.i_time]}</h3>
{/if}
<p>{include file="gcs/event/detail-short.tpl"}</p>
{/foreach}
