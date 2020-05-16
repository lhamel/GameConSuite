<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr>
{if $actions.addTicket|default:''}
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=add"></a></th>
{/if}
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=s_game,s_title">System/Title</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=s_lname,s_fname">Gamemaster</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=i_maxplayers">Players</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=e_day,i_time">Day / Time</a></th>
{* list additional fields if requested *}
{foreach from=$additional|default:array() key=dbField item=colHeader}
<th style="white-space: nowrap;">{$colHeader}</th>
{/foreach}

</tr>

{* Assigning the colors of the rows *}
{assign var='class1' value='altcolor1'}   {* Even *}
{assign var='class2' value='altcolor2'}   {* Odd *}

{assign var='i' value=0}
{foreach from=$events key=k item=v}
{if $i++ is odd by 1}
   {assign var='class' value=$class1}
{else}
   {assign var='class' value=$class2}
{/if}
<tr valign="top">

{* conditional on number of tickets left *}
{if $actions.addTicket|default:''}
<td class="{$class}"><a href='{$actions.addTicket}{$v.id_event}'><img src="../../images/ticket_icon_add.png" height="15" alt="buy" border="0" /></a></td>
{/if}

<td class="{$class}">{if $actions.detail|default:''}<a href="{$actions.detail}{$v.id_event}">{/if}{$v.s_game}
{if $v.s_game!=$v.s_title and $v.s_title!=''}
  {$v.s_title}
{/if}
{if $actions.detail}</a>{/if}
</td>
<td class="{$class}">{strip}
{if $actions.navigateMember|default:''}
  <a href="{$actions.navigateMember}{$v.id_gm}">{$v.gamemaster}</a>
{else}
  {$v.gamemaster}
{/if}
{/strip}</td>
<td class="{$class}">{strip}
{if $v.i_minplayers != $v.i_maxplayers && $v.i_minplayers>0}
  {$v.i_minplayers} - {$v.i_maxplayers}
{else}
  {$v.i_maxplayers}
{/if}
{/strip}</td>


<td class="{$class}">{$constants.events.daysWithBlank[$v.e_day|default:'']} {$constants.events.timesWithBlank[$v.i_time|default:'']}-{$constants.events.timesWithBlank[$v.endtime|default:'']}</td>

{* list additional fields if requested *}
{foreach from=$additional|default:array() key=dbField item=colHeader}
<td class="{$class}">{$v.$dbField}</td>
{/foreach}

</tr>

{/foreach}

</table>
