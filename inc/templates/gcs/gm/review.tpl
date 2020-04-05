<h2>Review Submission</h2>

<p style="background:#FFCCCC;margin-left:20px;margin-right:20px;padding:2px;border: solid red 2px;">Your event is not yet saved.  Please review your events information.  When you are satisfied, click the &quot;submit&quot; button.</p>

<table class="ucon_form" cellspacing="0" cellpadding="0">
    <col style="width: 50%" />
    <col style="width: 50%" />

    <tr>
        <th colspan="2">Event to submit</th>
    </tr>

{assign var=eventCount value=$events|@count}
{assign var=count value=0}
{foreach from=$events key=k item=event}
    {assign var=count value=$count+1}
    {cycle values='left,right' assign='column'} 
    {if $column=='left'}<tr>{/if}
        <td><span class="field_name">Event #{$count}</span><br />
            <div class="userdata">
            {$event.s_game|stripslashes}: {$event.s_title|stripslashes}<br/>
            {if $event.i_minplayers and $event.i_minplayers!=$event.i_maxplayers}{$event.i_minplayers}-{/if}{$event.i_maxplayers} Players<br/>
            {$event.i_length} hour{if $event.i_length>1}s{/if}<br/>
            </div>
            <div class="eventButtons">
				<a href="{$userActions.editEvent}{$k}" class="button">edit</a> 
            </div>
        </td>
    {if $column=='right'}
    </tr>
    {elseif $count==$eventCount}{* if this is the last event *}
        <td>
        </td>
    </tr>
    {/if}
{/foreach}


</table>


<!-- Display Payment Info -->
<h3>Payment</h3>

<p>The Gamemaster Badge represents a $10 deposit.  When registration is available, please purchase a $10 Gamemaster badge.  We will refund this money after your events are completed.  Or, we welcome donations!</p>

<hr />
<p>
  <a href="{$userActions.submitAll}" class="button">Submit Event</a>
  <a href="{$userActions.clearAll}" class="button">Clear All</a>
</p>

