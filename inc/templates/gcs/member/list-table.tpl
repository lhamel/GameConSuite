<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr>
{if $actions.addTicket|default:''}
<th style="white-space: nowrap;"><a href="{$actions.list|default:''}&amp;order=add"></a></th>
{/if}
<th style="white-space: nowrap;"><a href="{$actions.list|default:''}&amp;order=id_member">Number</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list|default:''}&amp;order=s_lname">Last Name</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list|default:''}&amp;order=s_fname">First Name</a></th>
<th style="white-space: nowrap;"><a href="{$actions.list|default:''}&amp;order=s_state,s_city">State, City</a></th>
{foreach from=$additional|default:array() key=dbField item=colHeader}
<th style="white-space: nowrap;"><a href="{$actions.list|default:''}&amp;order={$dbField}">{$colHeader}</a></th>
{/foreach}
</tr>

{* Assigning the colors of the rows *}
{assign var='class1' value='altcolor1'}   {* Even *}
{assign var='class2' value='altcolor2'}   {* Odd *}

{assign var='i' value=0}
{foreach from=$members key=k item=v}
{if $i++ is odd by 1}
   {assign var='class' value=$class1}
{else}
   {assign var='class' value=$class2}
{/if}
<tr valign="top">

{* conditional on number of tickets left *}
{if $actions.addTicket|default:''}
<td class="{$class}"><a href='{$actions.addTicket|default:''}{$v.id_event}' {if $v.ReducedSalience|default:false}style="color:#999"{/if}><img src="../../images/ticket_icon_add.png" height="15" alt="buy" border="0" /></a></td>
{/if}

<td class="{$class}"><a href="{$actions.detail|default:''}{$v.id_member}" {if $v.ReducedSalience|default:false}style="color:#999"{/if}>{$v.id_member}</a></td>
<td class="{$class}" {if $v.ReducedSalience|default:false}style="color:#999"{/if}>{$v.s_lname}</td>
<td class="{$class}" {if $v.ReducedSalience|default:false}style="color:#999"{/if}>{$v.s_fname}</td>
<td class="{$class}" {if $v.ReducedSalience|default:false}style="color:#999"{/if}>{$v.s_state}, {$v.s_city}</td>
{foreach from=$additional|default:array() key=dbField item=colHeader}
<td class="{$class}" {if $v.ReducedSalience|default:false}style="color:#999"{/if}>{$v.$dbField}</td>
{/foreach}
</tr>

{/foreach}

</table>
