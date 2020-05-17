{literal}<style>
td,th { font-size: 10pt; padding-left: 2px; padding-right: 2px; border-bottom: solid lightgray 1px;}
th { border: solid gray 1px; }
td.multiple {
  /*font-size:6pt;*/
  background: #ff8888;
}
td.single {
  /*font-size:6pt;*/
  background: #eeeeee;
}
</style>{/literal}

<table border="0" cellpadding="0" cellspacing="0">

{* header row *}
<tr><th>Time</th>
{foreach from=$tableList item=table}
  <th style="text-align:center;">{if $table}{$table}{else}none{/if}</th>
{/foreach}
</tr>


{* todo use data structure to avoid losing rows *}
{foreach from=$matrix key=dayKey item=matrixDay}
{if $dayKey}
<tr><td></td><th colspan="{$tableList|@count}">{$constants.events.days[$dayKey]}</th></tr>
{/if}
  {foreach from=$matrixDay key=timeKey item=matrixTime}
  <tr><th>{if $timeKey}{$constants.events.times[$timeKey]}{else}none{/if}</th>
    {foreach from=$tableList item=tableName}
      <td class="{if isset($joinMatrix[$dayKey][$timeKey][$tableName].class)}{$joinMatrix[$dayKey][$timeKey][$tableName].class}{/if}" {if isset($joinMatrix[$dayKey][$timeKey][$tableName].style)}style="{$joinMatrix[$dayKey][$timeKey][$tableName].style}"{/if}>
        {if isset($matrixTime[$tableName])}
          {foreach from=$matrixTime[$tableName] item=event}
          <a href="../event/index.php?id_event={$event.id_event}">
              {$event.s_number}<br/>
              {$event.gm}&nbsp;({$event.s_setup})
          </a>
          {/foreach}
        {/if}
      </td>
    {/foreach}
  </tr>
  {/foreach}
{/foreach}
</table>
