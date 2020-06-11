{if isset($error)}
<p style="background: #ff9999">{$error}</p>
{/if}

{strip}
<p>Show details: {foreach key=key item=y from=$years}
    {if $key != 0} | {/if}<a href="{$actions.base}&year={$y}" {if $y==$year} class="selected"{/if} >
  {$y}
</a>{/foreach}
</p>
{/strip}


<h1>Compare Duplicates</h1>
<p>Find duplicate names and email addresses in the database</p>

<table style="width:100%;" cellspacing="0" cellpadding="0">
<tr>
  <td><a href="{$config.page.depth}{$actions.viewMember}{$member1.id_member}">{$member1.id_member}</a></td>
  <td></td>
  <td><a href="{$config.page.depth}{$actions.viewMember}{$member2.id_member}">{$member2.id_member}</a></td>
</tr>

<tr>
  <td>
    {assign var='member' value=$member1}
    {assign var='authorizations' value=$member1.auths} 
    {include file="gcs/admin/member/view.tpl"}
  </td>
  <td style="width:10px"></td>
  <td>
    {assign var='member' value=$member2}
    {assign var='authorizations' value=$member2.auths}
    {include file="gcs/admin/member/view.tpl"}
  </td>
</tr>

<tr>
  <td>
    <h2>GM Events</h2>
    {assign var='events' value=$member1.gm}
    {assign var='newGm' value=$member2.id_member}
    {include file="gcs/admin/member/gm-list.tpl"}

    <ul>
    {foreach from=$member1.gmYears item=year}
      <li><a href="{$actions.moveEventYear}&source={$member1.id_member}&target={$member2.id_member}&year={$year}">Move all {$year}</a></li>
    {/foreach}
    </ul>
  </td>
  <td></td>
  <td>
    <h2>GM Events</h2>
    {assign var='events' value=$member2.gm}
    {assign var='newGm' value=$member1.id_member}
    {include file="gcs/admin/member/gm-list.tpl"}

    <ul>
    {foreach from=$member2.gmYears item=year}
      <li><a href="{$actions.moveEventYear}&source={$member2.id_member}&target={$member1.id_member}&year={$year}">Move all {$year}</a></li>
    {/foreach}
    </ul>
  </td>
</tr>

<tr>
  <td>
    <h2>Order Information</h2>
    {assign var='cart' value=$member1.cart}
    {assign var='target' value=$member2.id_member}
    {include file="gcs/common/cart.tpl"}

    <ul>
    {foreach from=$member1.orderYears item=year}
      <li><a href="{$actions.moveOrderYear}&source={$member1.id_member}&target={$member2.id_member}&year={$year}">Move all {$year}</a></li>
    {/foreach}
    </ul>
  </td>
  <td></td>
  <td>
    <h2>Order Information</h2>
    {assign var='cart' value=$member2.cart}
    {assign var='target' value=$member1.id_member}
    {include file="gcs/common/cart.tpl"}

    <ul>
    {foreach from=$member2.orderYears item=year}
      <li><a href="{$actions.moveOrderYear}&source={$member2.id_member}&target={$member1.id_member}&year={$year}">Move all {$year}</a></li>
    {/foreach}
    </ul>
  </td>
</tr>

{if count($member2.gmYears) == 0 && count($member2.orderYears)==0}
<tr>
  <td></td>
  <td></td>
  <td><a class="button" href="{$config.page.depth}{$config.page.location}?action={$actions.deleteMember}&amp;id_member={$member2.id_member}&amp;other={$member1.id_member}">Delete This User</a></td>
</tr>
{/if}

</table>

<a href="{$config.page.depth}{$actions.compare}?id_member={$member2.id_member}&amp;other={$member1.id_member}">SWAP</a>
