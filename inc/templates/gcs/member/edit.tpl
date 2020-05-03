<noscript>
<p>This document contains programming that requires javascript to be enabled.  This page will not 
function properly without it.  Please enable javascript to submit your events or register for the convention!</p>
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
        <td><span class="field_name">*First / Last Name</span><br/>
            <input name="s_fname" type="text" value="{$member.s_fname|default:''}" {if $errors.s_fname|default:false} class="validation" {/if}/> /
            <input name="s_lname" type="text" value="{$member.s_lname|default:''}" {if $errors.s_lname|default:false} class="validation" {/if}/>
            {if isset($errors.s_fname)}<span class="validation"><br/>*{$errors.s_fname}</span>{/if}
            {if isset($errors.s_lname)}<span class="validation"><br/>*{$errors.s_lname}</span>{/if}
        </td>
        <td class="description">
          When you pick up your materials onsite we will check your ID to be 
          sure we give your materials to the correct person.
        </td>
    </tr>
{*
    <tr>
        <td><span class="field_name">Group Name (if applicable)</span><br/>
            <input name="s_group" type="text" value="{$member.s_group}" {if $errors.s_group} class="validation" {/if}/>
            {if isset($errors.s_group)}<span class="validation"><br/>*{$errors.s_group}</span>{/if}
        </td>
        <td class="description">
          Are you registering your events as part of a group? If so, please enter the group here.
          Otherwise please leave this blank.
        </td>
    </tr>
*}
    <tr>
        <td><span class="field_name">*Email</span><br/>
            <input type="text" name="s_email" value="{$member.s_email|default:''}" {if $errors.s_email|default:false} class="validation" {/if} />
            {if isset($errors.s_email)}<span class="validation"><br/>*{$errors.s_email}</span>{/if}
        </td>
        <td class="description">
        This is our primary method of communication.
        </td>
    </tr>
    <tr>
        <td><span class="field_name">Phone</span><br/>
            <input type="text" name="s_phone" value="{$member.s_phone|default:''}" {if $errors.s_phone|default:false} class="validation" {/if}/>
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
          <input type="radio" name="addrtype" value="us" {if $member.s_international|default:false}checked{/if} onclick="document.getElementById('us').style.display='table-row';document.getElementById('international').style.display='none';"/> US Address
          <input type="radio" name="addrtype" value="international" {if $member.s_international|default:false}checked{/if} onclick="document.getElementById('us').style.display='none';document.getElementById('international').style.display='table-row';"/> International Address
        </td>
        <td class="description">
        </td>
    </tr>

    <tr id="us" {if $member.s_international|default:false}style="display:none;"{/if}>
        <td><span class="field_name">*U.S. Address</span><br/>
            <input name="s_addr1" type="text" value="{$member.s_addr1|default:''}" {if $errors.s_addr1|default:false} class="validation" {/if}/><br/>
            <input name="s_addr2" type="text" value="{$member.s_addr2|default:''}" {if $errors.s_addr2|default:false} class="validation" {/if}/><br/>
            <br/>
            <span class="field_name">*City, State, and Zip</span><br/>
            <input type="text" name="s_city" value="{$member.s_city|default:''}" size="10" {if $errors.s_city|default:false} class="validation" {/if}/>
            {if isset($errors.s_state)}
              {html_options name=s_state class=validation options=$constants.members.statesPlusBlank selected=$member.s_state|default:''}
            {else}
              {html_options name=s_state options=$constants.members.statesPlusBlank selected=$member.s_state|default:''}
            {/if}
            <input type="text" name="s_zip" maxlength="10" size="10" value="{$member.s_zip|default:''}" {if $errors.s_zip|default:false} class="validation" {/if}/>

            {if isset($errors.s_addr1)}<span class="validation"><br/>*{$errors.s_addr1|default:''}</span>{/if}
            {if isset($errors.s_addr2)}<span class="validation"><br/>*{$errors.s_addr2|default:''}</span>{/if}
            {if isset($errors.s_city)}<span class="validation"><br/>*{$errors.s_city|default:''}</span>{/if}
            {if isset($errors.s_state)}<span class="validation"><br/>*{$errors.s_state|default:''}</span>{/if}
            {if isset($errors.s_zip)}<span class="validation"><br/>*{$errors.s_zip|default:''}</span>{/if}
        </td>
        <td class="description">
            Occasionally we send out post cards to let you know about convention 
            events.  Please provide us with your updated address.
        </td>
    </tr>

    <tr id="international" {if $member.s_international|default:'' == ""}style="display:none;"{/if}>
        <td><span class="field_name">International Address</span><br/>
          <textarea name="s_international" rows="3" cols="50">{$member.s_international|default:''}</textarea>
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
      <input type="checkbox" name="b_volunteer" value="1" {if $member.b_volunteer|default:false}checked{/if}/> I am interested in volunteering!<br/>
      <input type="checkbox" name="b_email" value="1" {if $member.b_email|default:false}checked{/if}/> I'd like to sign up for email announcements
    </td>
  </tr>


  <tr>
    <td colspan="2" class="submit">
      <input type="submit" value="Review" />
    </td>
  </tr>
</table>

</form>

