
<h1>{$header}</h1>
{if $directions}
<p>{$directions}</p>
{/if}

{include file="gcs/common/filters.tpl"}

{if $members && count($members)>0}

{include file="gcs/member/list-table.tpl"}

{elseif $subheaders}
  {foreach from=$subheaders item=section}
    <h2>{$section.header}</h2>
    {if $section.directions}
      <p>{$section.directions}</p>
    {/if}

    {if $section.events && count($section.events)>0}
      {assign var='events' value=$section.events}
      {include file="gcs/member/list-table.tpl"}
    {else}
      <p> None found</p>
    {/if}

  {/foreach}
{else}
  <p> None found</p>
{/if}


