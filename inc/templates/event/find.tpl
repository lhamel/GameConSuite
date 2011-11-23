{capture assign="content"}

<h1>Select Tickets for U-Con {$year}</h1>

<p>Search by game system, event title, description, or gamemaster to 
find events and select tickets.</p>

{* requires form to be on the model *}
{include file="gcs/event/eventSearch.tpl"}


        <div class="event_search" style="float: right; margin-left: 3px; margin-top: 3px;">
    <!-- legend -->
<table>
<tr><td colspan="2" align="center" style="border-bottom: solid black 1px; font-weight: bold;">Legend</td></tr>
<tr valign="top"><td><img src="../../images/ticket_icon_star.png" height="15" alt="sold out" border="0" /></td><td>Sold out</td></tr>
<tr valign="top"><td><img src="../../images/ticket_icon_check.png" height="15" alt="check" border="0" /></td><td>Ticket already selected</td></tr>

<tr valign="top"><td><img src="../../images/ticket_icon_add.png" height="15" alt="buy" border="0" /></td><td>Click to add ticket</td></tr>
</table>
<!-- end legend -->     </div>
    
        <h2>Search Results</h2>
        <p>These events meet the search criteria.  Click on the add button to 
        add a ticket to this event, or click the event name to view more 
        information about the event.</p>
    
        <div class="event_search_result">
{include file="gcs/event/result-header.tpl}
{include file="gcs/event/list-table.tpl}
{include file="gcs/event/result-header.tpl}
        </div>

{/capture}

{assign var="title" value="Find Events - U-Con Gaming Convention, Ann Arbor Michigan"}

{include file="base.tpl"}
