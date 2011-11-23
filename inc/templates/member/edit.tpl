<noscript>
<p>This document contains programming that requires javascript to be enabled.  This page will not 
function properly without it.  Please enable javascript to submit your events!</p>
</noscript>

<form class="ucon_form" name="memberForm" action="{$form.submit}" method="post">
<input type="hidden" name="action" value="editMember" />
<table cellspacing="0" cellpadding="2">
    <col style="width: 58%" />
    <col style="width: 42%" />

    <tr>
        <th colspan="2">Name and contact</th>
    </tr>

    <tr>
        <td><span class="field_name">First / Last Name</span><br/>
            <input name="s_fname" type="text" value="{$member.s_fname}" {if $errors.s_fname} class="validation" {/if}/> /
            <input name="s_lname" type="text" value="{$member.s_lname}" {if $errors.s_lname} class="validation" {/if}/>
            {if $errors.s_fname}<span class="validation"><br/>*{$errors.s_fname}</span>{/if}
            {if $errors.s_lname}<span class="validation"><br/>*{$errors.s_lname}</span>{/if}
        </td>
        <td class="description">
          When you pick up your materials onsite we will check your ID to be 
          sure we give your materials to the correct person.
        </td>
    </tr>
    <tr>
        <td><span class="field_name">Email</span><br/>
            <input type="text" name="s_email" value="{$member.s_email}" {if $errors.s_email} class="validation" {/if} />
            {if $errors.s_email}<span class="validation"><br/>*{$errors.s_email}</span>{/if}
        </td>
        <td class="description">
        This is our primary method of communication.
        </td>
    </tr>
    <tr>
        <td><span class="field_name">Phone</span><br/>
            <input type="text" name="s_phone" value="{$member.s_phone}" {if $errors.s_phone} class="validation" {/if}/>
        </td>
        <td class="description">
        We will only contact you by phone if we have an urgent concern about your
        event.
        </td>
    </tr>

    <tr>
        <th colspan="2">Address</th>
    </tr>

    <tr>
        <td>
          <input type="radio" name="addrtype" value="us" {if $member.s_international == ""}checked{/if} onclick="document.getElementById('us').style.display='table-row';document.getElementById('international').style.display='none';"/> US Address
          <input type="radio" name="addrtype" value="international" {if $member.s_international != ""}checked{/if} onclick="document.getElementById('us').style.display='none';document.getElementById('international').style.display='table-row';"/> International Address
        </td>
        <td class="description">
        </td>
    </tr>

    <tr id="us" {if $member.s_international != ""}style="display:none;"{/if}>
        <td><span class="field_name">U.S. Address</span><br/>
            <input name="s_addr1" type="text" value="{$member.s_addr1}" {if $errors.s_addr1} class="validation" {/if}/><br/>
            <input name="s_addr2" type="text" value="{$member.s_addr2}" {if $errors.s_addr2} class="validation" {/if}/><br/>
            <br/>
            <span class="field_name">City, State, and Zip</span><br/>
            <input type="text" name="s_city" value="{$member.s_city}" size="10" {if $errors.s_city} class="validation" {/if}/>
            {html_options name=s_state options=$constants.members.statesPlusBlank selected=$member.s_state}
            <input type="text" name="s_zip" maxlength="10" size="10" value="{$member.s_zip}" {if $errors.s_zip} class="validation" {/if}/>

            {if $errors.s_addr1}<span class="validation"><br/>*{$errors.s_addr1}</span>{/if}
            {if $errors.s_addr2}<span class="validation"><br/>*{$errors.s_addr2}</span>{/if}
            {if $errors.s_city}<span class="validation"><br/>*{$errors.s_city}</span>{/if}
            {if $errors.s_state}<span class="validation"><br/>*{$errors.s_state}</span>{/if}
            {if $errors.s_zip}<span class="validation"><br/>*{$errors.s_zip}</span>{/if}
        </td>
        <td class="description">
            Occasionally we send out post cards to let you know about convention 
            events.  Please provide us with your updated address.
        </td>
    </tr>

    <tr id="international" {if $member.s_international == ""}style="display:none;"{/if}>
        <td><span class="field_name">International Address</span><br/>
          <textarea name="s_international" rows="3" cols="50">{$member.s_international}</textarea>
        </td>
        <td class="description">
            Occationally we send out post cards to let you know about convention 
            events.  Please provide us with your updated address.
        </td>
    </tr>

    <tr>
        <th colspan="2">Preferences</th>
    </tr>


  <tr>
    <td colspan="2">
      <input type="checkbox" name="b_volunteer" value="{$member.b_volunteer}"/> I am interested in volunteering!<br/>
      <input type="checkbox" name="b_email" value="{$member.b_email}"/> I'd like to sign up for email announcements
    </td>
  </tr>


  <tr>
    <td colspan="2" class="submit">
      <input type="submit" value="Review" />
    </td>
  </tr>
</table>

</form>

