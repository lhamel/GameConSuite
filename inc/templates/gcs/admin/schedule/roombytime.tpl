<table border="1" cellspacing="0" width="100%">
<tr><th></th><th>None</th>
{foreach from=$constants.events.rooms key=roomId item=roomName}
  <th><a href="roompicture_table.php?room={$roomId}&year={$year}">{$roomName}</a></th>
{/foreach}
</tr>

{* todo use data structure to avoid losing rows *}
{foreach from=$matrix key=dayKey item=matrixDay}
{if $dayKey}
<tr><td></td><th>{$constants.events.days[$dayKey]}</th><th colspan="{$constants.events.rooms|@count}"></th></tr>
{/if}
  {foreach from=$matrixDay key=timeKey item=matrixTime}
  <tr><th>{if $timeKey}{$constants.events.times[$timeKey]}{else}none{/if}</th>
      <td>
      {if isset($matrixTime[0])}{$matrixTime[0]}{/if}
      </td>
    {foreach from=$constants.events.rooms key=roomId item=roomName}
      <td>
      {if isset($matrixTime[$roomId])}{$matrixTime[$roomId]}{/if}
      </td>
    {/foreach}
  </tr>
  {/foreach}
{/foreach}


</table>