

<table width="100%">
<thead><tr><th colspan="{$meta.columnCount}">
  {include file="gcs/common/pager-header.tpl"}
</th></tr></thead>
<tbody>

<!-- Column headers -->
<tr>
{foreach from=$meta.columnNames key=colKey item=colName}
  <th>{$colName}</th>
{/foreach}
</tr>

<!-- Row Data -->
{foreach from=$results item=rowItem}
  {cycle values='1,2' assign='alt'} 
  <tr valign="top">
  {foreach from=$rowItem key=colKey item=colValue}
    <td class=\"altcolor{$alt}\">{$colValue}</td>
  {/foreach}
  </tr>
{/foreach}

</tbody>
<tfoot><tr><th colspan="{$meta.columnCount}">
  {include file="gcs/common/pager-header.tpl"}
</th></tr></tfoot>
</table>
