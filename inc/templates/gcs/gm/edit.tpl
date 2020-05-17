<script type="text/javascript">{literal}

$(document).ready(function() {

  function updateCount() {
    var max = 200;
    var len = $('#s_desc').val().length;
    console.log('count: '+len);
    var char = max - len;
    $('#charNum').text(char + ' characters left');
    if (char >= 0) {
      $('#charNum').text(char + ' characters left');
    } else {
      $('#charNum').html('<b>' + char + ' characters left (over limit)</b>');
    }
  }

  $('#s_desc').keyup(updateCount);
  updateCount();

  $('#s_game').autocomplete({source: "autocomplete_game.php" });

});

{/literal}</script>



<h1>Please Describe Your Event</h1>

<p>Event submissions may be edited for content, style, clairity and brevity. Events may be declined for any reason (including space or scheduling limitations).  Please read the instructions to ensure an accurate events submission, and contact us via our website in case you notice any discrepency or issue with your event listing.</p>

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
        <td><span class="field_name">*Event Type/Track</span><br />
            {if isset($errors.id_event_type)}<span class="validation">
              {html_options name=id_event_type class=validation options=$constants.events.eventTypesWithBlank selected=$event.id_event_type|default:''}
              <br/>*{$errors.id_event_type}</span>
            {else}
            {html_options name=id_event_type options=$constants.events.eventTypesWithBlank selected=($event.id_event_type|default:'')}
            {/if}
        </td>
        <td class="description">The selected event type determines in which category
        your event will be listed in the program book and on the website.</td>
    </tr>

    <tr>
        <td><span class="field_name">*Game System</span><br />
          <input type="text" id="s_game" name="s_game" value="{$event.s_game|default:''|stripslashes}" {if isset($errors.s_game)} class="validation" {/if}/>
            {if isset($errors.s_game)}<span class="validation"><br/>*{$errors.s_game}</span>{/if}
        </td>
        <td class="description">The name of the game or game system to be used in
        the event.</td>
    </tr>

    <tr>
        <td><span class="field_name">Event Title</span><br />
          <input type="text" name="s_title" value="{$event.s_title|default:''|stripslashes}" {if isset($errors.s_title)} class="validation" {/if}/>
            {if isset($errors.s_title)}<span class="validation"><br/>*{$errors.s_title}</span>{/if}
        </td>
        <td class="description">The title is the name of the event. In the case of
        board games, you may wish to leave this blank to simply use the game
        system name as the title.</td>
    </tr>

    <tr>
        <td colspan="2"><span class="field_name">Short description of your game</span>
        <span class="description">provided in the convention book (limited to 200 characters)</span><br />
        <textarea style="width:100%;" id="s_desc" name="s_desc" rows="3" cols="40"
        {if isset($errors.s_desc)} class="validation" {/if}>{$event.s_desc|default:''|stripslashes}</textarea>
        <span id="charNum"></span>
        {if isset($errors.s_desc)}<span class="validation"><br>*{$errors.s_desc}</span>{/if}
    </tr>

    <tr>
        <td colspan="2"><span class="field_name">Longer description of your game</span>
        <span class="description">provided with your event online (no character limit, leave blank to use short description)</span><br />
        <textarea style="width:100%;" name="s_desc_web" rows="3" cols="40"
        {if isset($errors.s_desc_web)} class="validation" {/if}>{$event.s_desc_web|default:''|stripslashes}</textarea>
        {if isset($errors.s_desc_web)}<span class="validation">*{$errors.s_desc_web}</span>{/if}
    </tr>

    <tr>
        <td colspan="2"><span class="field_name">Comments for event
        coordinator.</span> <span class="description">Please note here if you think your event 
        might qualify as part of one of our special tracks (e.g. OSR, Fate, Tekumel, etc.).
        Also if you want to run this event multiple times or it is part of a larger 
        tournament, explain here.</span><br />
        <textarea style="width:100%;" name="s_comments" rows="3" cols="40">{$event.s_comments|default:''|stripslashes}</textarea>
        {if isset($errors.s_comments)}<span class="validation"><br/>*{$errors.s_comments}</span>{/if}
    </tr>


    <tr>
        <th colspan="2">Players</th>
    </tr>

    <tr>
        <td><span class="field_name">*Maximum / minimum number of players</span><br />
          <input name="i_maxplayers" type="text" value="{$event.i_maxplayers|default:''}" size="3" {if isset($errors.i_maxplayers)} class="validation" {/if}/> /
          <input name="i_minplayers" type="text" value="{$event.i_minplayers|default:''}" size="3" {if isset($errors.i_minplayers)} class="validation" {/if}/>
            {if isset($errors.i_maxplayers)}<span class="validation"><br/>*{$errors.i_maxplayers}</span>{/if}
            {if isset($errors.i_minplayers)}<span class="validation"><br/>*{$errors.i_minplayers}</span>{/if}
        </td>
        <td class="description">Please indicate the number of players for
        your event.</td>
    </tr>


    <tr>
        <td><span class="field_name">Experience / Complexity</span><br />
          {html_options name=e_exper options=$constants.events.experience.select selected=$event.e_exper|default:''} /
          {html_options name=e_complex options=$constants.events.complexity.select selected=$event.e_complex|default:''}
          {if isset($errors.e_exper)}<span class="validation"><br/>*{$errors.e_exper}</span>{/if}
          {if isset($errors.e_complex)}<span class="validation"><br/>*{$errors.e_complex}</span>{/if}
        </td>

        <td class="description">Indicate how much experience you want players to
        have with the rules and how complex the rules are. Please consult the
        <a href="{$menu.depth}gcs/events/expcomp.php">guidelines</a> if you are unsure
        what to choose.</td>
    </tr>

    <tr>
        <td><span class="field_name">*Age recommendations</span><br />
          {if isset($errors.i_agerestriction)}
            {html_options name=i_agerestriction class=validation options=$constants.events.ages selected=$event.i_agerestriction|default:''}
            <span class="validation"><br/>*{$errors.i_agerestriction}</span>
          {else}
            {html_options name=i_agerestriction options=$constants.events.ages selected=$event.i_agerestriction|default:''}
          {/if}
        </td>
        <td class="description">Indicate the preferred ages of participants.  This is an indication to players 
        and you may use some discretion when you accept players at your event.</td>
    </tr>


    <tr>
        <th colspan="2">Scheduling</th>
    </tr>

    <tr>
        <td><span class="field_name">*Length of event</span><br />
          <input name="i_length" id="length-of-event" type="text" value="{$event.i_length|default:''}" size="2" {if isset($errors.i_length)} class="validation" {/if}/> hours
          {if isset($errors.i_length)}<span class="validation"><br/>*{$errors.i_length}</span>{/if}
        </td>
        <td class="description">Please indicate how long your event will run.  Please 
        note that for most events the length of the event will determine the 
        ticket price.</td>
    </tr>

    <tr>
        <td><span class="field_name">Scheduling preferences</span><br />
        1) {html_options name=i_c1 options=$constants.events.slots selected=$event.i_c1|default:''} first choice<br />
        2) {html_options name=i_c2 options=$constants.events.slots selected=$event.i_c2|default:''} second choice<br />
        3) {html_options name=i_c3 options=$constants.events.slots selected=$event.i_c3|default:''} third choice
            {if isset($errors.i_c1)}<span class="validation"><br/>*{$errors.i_c1}</span>{/if}
            {if isset($errors.i_c2)}<span class="validation"><br/>*{$errors.i_c2}</span>{/if}
            {if isset($errors.i_c3)}<span class="validation"><br/>*{$errors.i_c3}</span>{/if}
        </td>
        <td class="description">Select your preferred time slots.  We will do our best 
        to fit your event into one of these slots, however is limited. 
        Our standard slots are 9am, 2pm, and 8pm.
        <!--For minimal overlap with other events we make the following recommendations:<br/>
        &#187; 6 hour events: 2pm<br/>
        &#187; 4 hour events: 10am, 11am, 2pm, 3pm, 4pm, 7pm, 8pm<br/>
        &#187; 3 hour events: 10am, 11am, 12pm, 2pm, 3pm, 4pm, 5pm, 7pm, 8pm<br/>
        &#187; 2 hour events: anytime -->
        </td>
    </tr>

    <tr>
        <td><span class="field_name">Number of tables / preferred type</span><br />
          <input name="s_setup" type="text" value="{$event.s_setup|default:''|stripslashes}" size="2" {if isset($errors.s_setup)} class="validation" {/if}/> /
          {html_options name=s_table_type options=$constants.events.table_types selected=$event.s_table_type|default:''}
          {if isset($errors.s_setup)}<span class="validation"><br/>*{$errors.s_setup}</span>{/if}
          {if isset($errors.s_table_type)}<span class="validation"><br/>*{$errors.s_table_type}</span>{/if}
        </td>
        <td class="description">Various tables types are available, but the 
        selected table type may be used to determine in which room your event 
        will be scheduled.</td>
    </tr>


    <tr>
        <td colspan="2"><span class="field_name">Comments regarding scheduling constraints (not shown to players)</span><br />
          <textarea style="width:100%;" name="s_eventcom" rows="3" cols="40"
          {if isset($errors.s_eventcom)} class="validation" {/if}>{$event.s_eventcom|default:''|stripslashes}</textarea>
          {if isset($errors.s_eventcom)}<span class="validation"><br/>*{$errors.s_eventcom|stripslashes}</span>{/if}
        </td>
    </tr>
    <tr>
        <td colspan="2" class="submit">
          {if isset($userActions.cancelAddEvent)}
          <input type="button" onclick="window.location.href='{$userActions.cancelAddEvent}'" value="Back" />{/if}
          <input type="submit" value="Continue" />
        </td>
    </tr>

</table>
</form>



<script type="text/javascript">
var slotDurations = {$slotDurations};
</script>
