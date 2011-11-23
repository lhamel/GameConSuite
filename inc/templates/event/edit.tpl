
<h1>Please Describe Your Event</h1>

<form class="ucon_form" name="eventForm" action="{$form.submit}" method="post">
<input type="hidden" name="action" value="editEvent" />
<input type="hidden" name="idx" value="{$event.idx}" />
<table cellspacing="0" cellpadding="0">
    <col style="width: 35%" />
    <col style="width: 65%" />


    <tr>
        <th colspan="2">Game or Event Information</th>
    </tr>

    <tr>
        <td><span class="field_name">Event Type/Track</span><br />
            {html_options name=id_event_type options=$constants.events.eventTypesWithBlank selected=$event.id_event_type}
            {if $errors.id_event_type}<span class="validation"><br/>*{$errors.id_event_type}</span>{/if}
        </td>
        <td class="description">The selected event type determines in which category
        your event will be listed in the program book and on the website.</td>
    </tr>

    <tr>
        <td><span class="field_name">Game System</span><br />
          <input type="text" name="s_game" value="{$event.s_game|stripslashes}" {if $errors.s_game} class="validation" {/if}/>
            {if $errors.s_game}<span class="validation"><br/>*{$errors.s_game}</span>{/if}
        </td>
        <td class="description">The name of the game or game system to be used in
        the event.</td>
    </tr>

    <tr>
        <td><span class="field_name">Event Title</span><br />
          <input type="text" name="s_title" value="{$event.s_title|stripslashes}" {if $errors.s_title} class="validation" {/if}/>
            {if $errors.s_title}<span class="validation"><br/>*{$errors.s_title}</span>{/if}
        </td>
        <td class="description">The title is the name of the event. In the case of
        board games, you may wish to leave this blank to simply use the game
        system name as the title.</td>
    </tr>

    <tr>
        <td colspan="2"><span class="field_name">Description of your game</span>
        <span class="description">shown online and in the program book</span><br />
        <textarea style="width:100%;" name="s_desc" rows="3" cols="40"
        {if $errors.s_desc} class="validation" {/if}>{$event.s_desc|stripslashes}</textarea>
        {if $errors.s_desc}<span class="validation"><br/>*{$errors.s_desc}</span>{/if}
    </tr>

    <tr>
        <td colspan="2"><span class="field_name">Comments for event
        coordinator.</span> <span class="description">If 
        you want to run this event multiple times or it is part of a larger 
        tournament, explain here.</span><br />
        <textarea style="width:100%;" name="s_comments" rows="3" cols="40">{$event.s_comments|stripslashes}</textarea>
        {if $errors.s_comments}<span class="validation"><br/>*{$errors.s_comments}</span>{/if}
    </tr>


    <tr>
        <th colspan="2">Players</th>
    </tr>

    <tr>
        <td><span class="field_name">Maximum / minimum number of players</span><br />
          <input name="i_maxplayers" type="text" value="{$event.i_maxplayers}" size="3" {if $errors.i_maxplayers} class="validation" {/if}/> /
          <input name="i_minplayers" type="text" value="{$event.i_minplayers}" size="3" {if $errors.i_minplayers} class="validation" {/if}/>
            {if $errors.i_maxplayers}<span class="validation"><br/>*{$errors.i_maxplayers}</span>{/if}
            {if $errors.i_minplayers}<span class="validation"><br/>*{$errors.i_minplayers}</span>{/if}
        </td>
        <td class="description">Please indicate the number of players for
        your event.</td>
    </tr>


    <tr>
        <td><span class="field_name">Experience / Complexity</span><br />
          {html_options name=e_exper options=$constants.events.experience selected=$event.e_exper} /
          {html_options name=e_complex options=$constants.events.complexity selected=$event.e_complex}
          {if $errors.e_exper}<span class="validation"><br/>*{$errors.e_exper}</span>{/if}
          {if $errors.e_complex}<span class="validation"><br/>*{$errors.e_complex}</span>{/if}
        </td>

        <td class="description">Indicate how much experience you want players to
        have with the rules and how complex the rules are. Please consult the
        <a href="{$menu.depth}/events/db/expcomp.php">guidelines</a> if you are unsure
        what to choose.</td>
    </tr>

    <tr>
        <td><span class="field_name">Age recommendations</span><br />
          {html_options name=i_agerestriction options=$constants.events.ages selected=$event.i_agerestriction}
          {if $errors.i_agerestriction}<span class="validation"><br/>*{$errors.i_agerestriction}</span>{/if}
        </td>
        <td class="description"></td>
    </tr>


    <tr>
        <th colspan="2">Scheduling</th>
    </tr>

    <tr>
        <td><span class="field_name">Length of event</span><br />
          <input name="i_length" type="text" value="{$event.i_length}" size="2" {if $errors.i_length} class="validation" {/if}/> hours
          {if $errors.i_length}<span class="validation"><br/>*{$errors.i_length}</span>{/if}
        </td>
        <td class="description">Please indicate how long your event will run.  Please 
        note that for most events the length of the event will determine the 
        ticket price.</td>
    </tr>

    <tr>
        <td><span class="field_name">Scheduling preferences</span><br />
        1) {html_options name=i_c1 options=$constants.events.slots selected=$event.i_c1} first choice<br />
        2) {html_options name=i_c2 options=$constants.events.slots selected=$event.i_c2} second choice<br />
        3) {html_options name=i_c3 options=$constants.events.slots selected=$event.i_c3} third choice
            {if $errors.i_c1}<span class="validation"><br/>*{$errors.i_c1}</span>{/if}
            {if $errors.i_c2}<span class="validation"><br/>*{$errors.i_c2}</span>{/if}
            {if $errors.i_c3}<span class="validation"><br/>*{$errors.i_c3}</span>{/if}
        </td>
        <td class="description">Select your preferred time slots.  We will do our best 
        to fit your event into one of these slots, however is limited. 
        For minimal overlap with other events we make the following recommendations:<br/>
        &#187; 6 hour events: 2pm<br/>
        &#187; 4 hour events: 10am, 11am, 2pm, 3pm, 4pm, 7pm, 8pm<br/>
        &#187; 3 hour events: 10am, 11am, 12pm, 2pm, 3pm, 4pm, 5pm, 7pm, 8pm<br/>
        &#187; 2 hour events: anytime
        </td>
    </tr>

    <tr>
        <td><span class="field_name">Number of tables / preferred type</span><br />
          <input name="s_setup" type="text" value="{$event.s_setup|stripslashes}" size="2" {if $errors.s_setup} class="validation" {/if}/> /
          {html_options name=s_table_type options=$constants.events.table_types selected=$event.s_table_type}
          {if $errors.s_setup}<span class="validation"><br/>*{$errors.s_setup}</span>{/if}
          {if $errors.s_table_type}<span class="validation"><br/>*{$errors.s_table_type}</span>{/if}
        </td>
        <td class="description">Various tables types are available, but the 
        selected table type may be used to determine in which room your event 
        will be scheduled.</td>
    </tr>


    <tr>
        <td colspan="2"><span class="field_name">Comments regarding scheduling constraints (not shown to players)</span><br />
          <textarea style="width:100%;" name="s_eventcom" rows="3" cols="40"
          {if $errors.s_eventcom} class="validation" {/if}>{$event.s_eventcom|stripslashes}</textarea>
          {if $errors.s_eventcom}<span class="validation"><br/>*{$errors.s_eventcom|stripslashes}</span>{/if}
        </td>
    </tr>
    <tr>
        <td colspan="2" class="submit">
          <input type="submit" value="Review" />
        </td>
    </tr>

</table>
</form>
