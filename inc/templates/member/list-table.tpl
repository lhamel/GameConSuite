<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr>
{if $actions.addTicket}
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=add"></a></th>
{/if}
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=id_member">Number</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=s_lname">Last Name</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=s_fname">First Name</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order=s_state,s_city">State, City</a></th>
{foreach from=$additional key=dbField item=colHeader}
<th style="white-space: nowrap;"><a href="{$actions.list}&amp;order={$dbField}">{$colHeader}</a></th>
{/foreach}
</tr>

{* Assigning the colors of the rows *}
{assign var='class1' value='altcolor1'}   {* Even *}
{assign var='class2' value='altcolor2'}   {* Odd *}

{foreach from=$members key=k item=v}
{if $i++ is odd by 1}
   {assign var='class' value=$class1}
{else}
   {assign var='class' value=$class2}
{/if}
<tr valign="top">

{* conditional on number of tickets left *}
{if $actions.addTicket}
<td class="{$class}"><a href='{$actions.addTicket}{$v.id_event}'><img src="../../images/ticket_icon_add.png" height="15" alt="buy" border="0" /></a></td>
{/if}

<td class="{$class}"><a href="{$actions.detail}{$v.id_member}">{$v.id_member}</a></td>
<td class="{$class}">{$v.s_lname}</td>
<td class="{$class}">{$v.s_fname}</td>
<td class="{$class}">{$v.s_state}, {$v.s_city}</td>
{foreach from=$additional key=dbField item=colHeader}
<td class="{$class}">{$v.$dbField}</td>
{/foreach}
</tr>

{/foreach}

</table>
