
<h1>{$header}</h1>
{if $directions}
<p>{$directions}</p>
{/if}

{include file="gcs/common/filters.tpl"}

{if $events|default:'' && count($events)>0}

{include file="gcs/event/list-table.tpl"}

{elseif $subheaders|default:array()}
  {foreach from=$subheaders item=section}
    <h2>{$section.header}</h2>
    {if $section.directions|default:''}
      <p>{$section.directions}</p>
    {/if}

    {if $section.events|default:0 && count($section.events)>0}
      {assign var='events' value=$section.events}
      {include file="gcs/event/list-table.tpl"}
    {else}
      <p> No events found</p>
    {/if}

  {/foreach}
{else}
  <p> No events found</p>
{/if}


