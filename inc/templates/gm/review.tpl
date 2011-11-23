<h2>Review Submission</h2>

<p style="background:#FFCCCC;margin-left:20px;margin-right:20px;padding:2px;border: solid red 2px;">Your submission is not yet complete.  Please review your personal information, events, and add more events if desired.  When you have added all your events, click the &quot;submit&quot; button.</p>

<table class="ucon_form" cellspacing="0" cellpadding="0">
    <col style="width: 50%" />
    <col style="width: 50%" />


    <tr>
        <th colspan="2">Registration</th>
    </tr>

    <tr>
        <td><span class="field_name">Name</span><br />
            <div class="userdata">
              {$member.s_fname} {$member.s_lname}<br/>
            </div>
        </td>
        <td><span class="field_name">Email</span><br />
            <div class="userdata">
              {$member.s_email}
            </div>
        </td>
    </tr>

    <tr>
        <td><span class="field_name">Address</span><br />
            <div class="userdata">
              {if $member.s_international}
                {$member.s_international}
              {else}
                  {$member.s_addr1}<br/>
                  {if $member.s_addr2}{$member.s_addr2}<br/>{/if}
                  {$member.s_city} {$member.s_state}, {$member.s_zip}
              {/if}
            </div>
        </td>
        <td><span class="field_name">Phone</span><br />
            <div class="userdata">
              {$member.s_phone}
            </div>
        </td>
    </tr>

    <tr>
        <td colspan="2" class="submit">
            <a href="{$userActions.editMember}" class="button">Edit Personal Information</a>
        </td>
    </tr>

    <tr>
        <th colspan="2">Submitted Events</th>
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
				<a href="{$userActions.deleteEvent}{$k}" class="button">remove</a> 
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


    <tr>
        <td colspan="2" class="submit">
            <a href="{$userActions.addEvent}" class="button">Add another event</a>
        </td>
    </tr>

</table>


<!-- Display Payment Info -->
<h3>Payment</h3>

<p>Game Master Badge: $10 (to be refunded after events are run).  Instructions for payment are provided with your confirmation email.</p>

<p>We run on a very tight budget each year.  
Please consider donating your GM Badge deposit to keep us 
running.</p>

<hr />
<p>
  <a href="{$userActions.addEvent}" class="button">Add another event</a> 
  <a href="{$userActions.submitAll}" class="button">Submit {$eventCount} Event{if $eventCount>0}s{/if}</a>
</p>

