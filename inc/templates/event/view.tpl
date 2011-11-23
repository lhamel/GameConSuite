<div class="viewEvent">

<h2>{$event.s_game}{if $event.s_title && $event.s_title != $event.s_game} {$event.s_title}{/if}</h2>

<table cellspacing="0" cellpadding="0">
<tr>
  <td class="left">Event Number</td> 
  <td class="right">{$event.s_number}</td>
</tr>
<tr>
  <td class="left">Gamemaster</td>
  <td class="right">{$event.gamemaster}</td>
</tr>
<tr>
  <td class="left">Price</td>
  <td class="right">{if $event.i_cost>0}${$event.i_cost}{else}Free!{/if}</td>
</tr>
<tr>
  <td class="left">Time</td>
  <td class="right">
    {$constants.events.days[$event.e_day]} {$constants.events.times[$event.i_time]}-{$constants.events.times[$event.endtime]}
    {*<br/>{$event.s_room} {$event.s_table}*}
  </td>
</tr>
<tr>
  <td class="left">Players</td>
  <td class="right">{$event.i_minplayers}-{$event.i_maxplayers}</td>
</tr>
<tr>
  <td class="left">Exp/Complex</td>
  <td class="right">{$event.e_exper}{$event.e_complex}</td>
</tr>
<tr>
  <td class="left">Age Guideline</td>
  <td class="right">{$constants.events.ages[$event.i_agerestriction]}</td>
</tr>
<tr>
  <td colspan="2"><p>{$event.s_desc|stripslashes}</p></td>
</tr>  
</table>



</div>
