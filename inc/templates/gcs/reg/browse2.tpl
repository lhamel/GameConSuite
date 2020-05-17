<h1>Browse Events</h1>

{include file="gcs/common/filters.tpl"}

<p>Browse Events by Category and Day.  Click the symbol <img src="{$constants.cart.buy.0.0}"> to select a ticket for the event.  The symbol <img src="{$constants.cart.buy.1.0}"> indicates a sold out event.</p>
<hr/>

{if isset($events)}
<div class="event_search_result">
{if $actions.showExpanded}
{include file="gcs/event/list-detail.tpl"}
{else}
{include file="gcs/event/list-table.tpl"}
{/if}
</div>
{else}
{if $showResults}
<p><i>No events found.</i></p>
{/if}
{/if}
