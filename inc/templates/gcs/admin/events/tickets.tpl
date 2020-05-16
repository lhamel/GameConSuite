<h2>Tickets for {$event.s_game}{if $event.s_title && $event.s_title != $event.s_game} {$event.s_title}{/if}</h2>

<table>
<col id="name"/>
<col id="email"/>
<col id="quantity"/>
<tr>
  <th>Name</th>
  <th>Email</th>
  <th>Quantity</th>
  <th>Timestamp</th>
</tr>

{foreach from=$tickets item=ticket}
<tr>
  <td><a href="{$config.page.depth}{$actions.viewMember}{$ticket.id_member}">{$ticket.s_lname}</a></td>
  <td>{$ticket.s_email}</td>
  <td>{$ticket.i_quantity}</td>
  <td>{$ticket.d_transaction}</td>
</tr>
{/foreach}


</table>


