<script type="text/javascript">{literal}



    function showAddDialog(mid, url) {
      console.log("add authorization for " + mid);
      $( function() {
        $( "#addDialog" ).dialog({
          buttons: [
            { text: "OK", click: function() {
                var email = $( '#authEmail' ).val()
                var wholeUrl = url+'?action=add&email='+email+'&id_member='+mid;
                console.log ("Add auth user " + wholeUrl);

                $.get(wholeUrl, function(data, textStatus, jqXHR) {
                  if (data=='1') {
                    alert("Action succeeded! Reload to see changes");
                  } else {
                    alert("Action failed!");
                  }
                });

                $(this).dialog("close"); 
              } },
            { text: "Cancel", click: function() {
                $(this).dialog("close"); 
              } }
          ]
        });
      } );

    }

    function showRemoveDialog(uid, mid, email, url) {
      console.log("remove authorization for "+uid+" + " + mid, url);
      $('#emailDialog').text(email);
      $( function() {
        $( "#removeDialog" ).dialog({
          buttons: [
            { text: "OK", click: function() {
                console.log ("removing "+uid+" ("+email+")");

                $.get(url+'?action=remove&uid='+uid+'&id_member='+mid, function(data, textStatus, jqXHR) {
                  if (data=='1') {
                    alert("Action succeeded! Reload to see changes");
                  } else {
                    alert("Action failed!");
                  }
                });

                $(this).dialog("close");
              } },
            { text: "Cancel", click: function() { $(this).dialog("close"); } }
          ]
        });
      } );

    }


$(document).ready(function() {

    $('.editable').editable('ajax_edit.php', {
        indicator : 'Saving...',
        tooltip   : 'Click to edit...',
        placeholder : '<span style="color:#aaaaaa;">Click to edit</span>',
        //cancel    : 'Cancel',
        //submit    : 'OK',
        id : 'field',
        name : 'new',
        style : 'display: inline',
        onerror: function(settings, original, xhr) {
           original.reset();
           alert(xhr.responseText);
        },
    });

});{/literal}</script>

<div class="viewEvent">
<h2>{$member.s_fname} {$member.s_lname}</h2>

<table cellspacing="0" cellpadding="0">
<tr>
  <td class="left">First Name</td> 
  <td class="right"><div class="editable" id="s_fname-{$member.id_member}">{$member.s_fname}</div></td>
</tr>
<tr>
  <td class="left">Last Name</td> 
  <td class="right"><div class="editable" id="s_lname-{$member.id_member}">{$member.s_lname}</div></td>
</tr>
<tr>
  <td class="left">Address</td>
  <td class="right">
{if $member.s_international}
<div class="editable" id="s_international-{$member.id_member}">{$member.s_international}</div>
{else}
<span class="editable" id="s_addr1-{$member.id_member}">{$member.s_addr1}</span><br />
<span class="editable" id="s_addr2-{$member.id_member}">{$member.s_addr2}</span><br />
<span class="editable" id="s_city-{$member.id_member}">{$member.s_city}</span>, <span class="editable" id="s_state-{$member.id_member}">{$member.s_state}</span> <span class="editable" id="s_zip-{$member.id_member}">{$member.s_zip}</span><br />
{/if}
  </td>
</tr>
<tr>
  <td class="left">Email</td> 
  <td class="right"><div class="editable" id="s_email-{$member.id_member}">{$member.s_email}</div></td>
</tr>
<tr>
  <td class="left">Phone</td> 
  <td class="right"><div class="editable" id="s_phone-{$member.id_member}">{$member.s_phone}</div></td>
</tr>
<tr>
  <td class="left">Volunteer Checkbox?</td>
  <td class="right">{if $member.b_volunteer}yes{else}no{/if}</td>

</table>

</div>

<h2>Gamemaster Events</h2>
{if isset($events)}
	{include file="gcs/event/list-table.tpl"}
{else}
	None.
{/if}

<h2>Controlling Account Authorizations</h2>

<p>Login accounts which may access the member as an envelope</p>

{if empty($authorizations)}
<p><i>None.</i></p>
{else}
<ul>
{foreach from=$authorizations key=k item=auth}
  <li>{if isset($actions.showAuths)}<a href="{$actions.showAuths}?uid={$auth.uid}">{/if}{$auth.email}{if isset($actions.showAuths)}</a>{/if}{if isset($actions.modAuth)} <input type="button" value="remove" onClick="showRemoveDialog({$auth.uid},{$member.id_member},'{$auth.email}','{$actions.modAuth}')"/>{/if}</li>
{/foreach}
</ul>
{/if}

{if isset($actions.modAuth)}<input type="button" value="add" onClick="showAddDialog({$member.id_member},'{$actions.modAuth}')"/>{/if}

<div id="addDialog" title="Add Authorized User" style="display:none">
Add Authorized User
<input type="text" id="authEmail">

</div>

<div id="removeDialog" title="Remove Authorized User" style="display:none">
Remove Authorized User?
<span id="emailDialog"></span>
</div>


{if isset($yearsOfInterest)}
<h2>Years of Activity</h2>
<p>
{foreach from=$yearsOfInterest item=y}
<a href="?id_member={$member.id_member}&year={$y}">{$y}</a> 
{/foreach}
</p>

{/if}


