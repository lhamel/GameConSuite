{if !$loginInfo.loggedin && $config.allow.buy_events}
<p style="padding: 2px; margin-top: 6px; background: #880000; color: #FFFFFF; font-weight: bolder; font-size: larger;">User not logged in.  Log in to be able to add tickets.</p>
{/if}
{include file="gcs/reg/eventDetailTicketDlg.tpl"}
<h1>Search Events</h1>

<form>
<input name="search" value="{$config.page.request.search|default:''|urlencode}" />
<input value="search" type="submit" />
</form>


<p>Browse Events by Category and Day.  Click the symbol <img src="{$constants.cart.buy.0.0}"> to select a ticket for the event.  The symbol <img src="{$constants.cart.buy.1.0}"> indicates a sold out event.</p>
<hr/>

{if isset($events)}
<div class="event_search_result">
{include file="gcs/event/list-detail.tpl"}
</div>
{else}
{if $showResults}
<p><i>No events found.</i></p>
{/if}
{/if}
