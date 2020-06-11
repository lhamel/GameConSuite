
<h1>Find Duplicates</h1>
<p>Find duplicate names and email addresses in the database</p>

{if $duplicates && count($duplicates)>0}

<table>
<tr>
  {foreach from=$cols key=k item=col}
    <th>{$col}</th>
  {/foreach}
</tr>

{foreach from=$duplicates item=row}
<tr>
  {foreach from=$cols key=k item=col}
    <td>{$row.$k}</td>
  {/foreach}

{*

  <td>
    <a href="{$config.page.depth}{$actions.ofmember}{$row.ids[0]}">
      {if isset($row.s_email)}{$row.s_email}{/if}
      {if isset($row.s_lname)}{$row.s_lname}{/if}
    </a>
  </td>

  <td>
    {foreach from=$row.ids item=id}
      <a href="{$config.page.depth}{$actions.ofmember}{$id}">{$id}</a>
    {/foreach}
  </td>

  <td><a href="{$config.page.depth}{$actions.ofmember}{$row.id_member}">{$row.id_member}</a></td>
  <td>{if $row.s_lname}{$row.s_lname}, {/if}{$row.s_fname}</td>
  <td>{$row.s_email}</td>
  <td><a href="{$config.page.depth}{$actions.ofmember}{$row.id_member2}">{$row.id_member2}</a></td>
  <td>{if $row.s_lname2}{$row.s_lname2}, {/if}{$row.s_fname2}</td>
  <td>{$row.s_email2}</td>
  <td><a href="{$config.page.depth}{$actions.compare}?id_member={$row.id_member}&amp;other={$row.id_member2}">Compare</a></td>
*}
</tr>
{/foreach}

</table>

{else}
  <p> No duplicates found</p>
{/if}


