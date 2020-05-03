<h2>Review Submission</h2>

<p style="background:#FFCCCC;margin-left:20px;margin-right:20px;padding:2px;border: solid red 2px;">Please review the information you provided below.  Click "Go Back" to edit the information or "Create New Envelope" to finish and save it.</p>

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
        <td colspan="2">
            <a href="{$userActions.edit}" class="button">Go Back</a>
            <a href="{$userActions.save}" class="button">Create New Envelope</a>
        </td>
    </tr>


</table>


