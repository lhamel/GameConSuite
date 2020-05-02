{*
  Parameters:
    $columns array containing a matching key for field to be included and a label for the header field

*}
{if isset($header)}
<h1>{$header}</h1>
{/if}
{if isset($directions)}
<p>{$directions}</p>
{/if}

<table border="0" cellspacing="0" cellpadding="1" width="100%">
<tr>
{foreach from=$columns key=dbField item=colHeader}
<th style="white-space: nowrap; {if isset($colWidth.$dbField)}width:{$colWidth.$dbField}{/if}">{$colHeader}</th>
{/foreach}

</tr>

{* Assigning the colors of the rows *}
{assign var='class1' value='altcolor1'}   {* Even *}
{assign var='class2' value='altcolor2'}   {* Odd *}

{* Provided for backwards compatibility *}
{if empty($tableItems)}
{assign var='tableItems' value=$events}
{/if}
{assign var="i" value=0}
{foreach from=$tableItems key=k item=v}
{if $i++ is odd by 1}
   {assign var='class' value=$class1}
{else}
   {assign var='class' value=$class2}
{/if}
<tr valign="top">

{* list additional fields if requested *}
{foreach from=$columns key=dbField item=colHeader}
<td class="{$class}" {if isset($columnsAlign.$dbField)}style="text-align:{$columnsAlign.$dbField};"{/if}>{$v.$dbField}</td>
{/foreach}

</tr>

{/foreach}

</table>
