<script type="text/javascript">{literal}

//$(document).ready(function() {

//  $( function() {
    function showSelectMemberDialog(title, itemDesc, submitUrl) {
      console.log("show select member dialog");

        try {
          $( "#selectMemberDialog" ).dialog('option', 'title', title);
        } catch (e) {
          $( "#selectMemberDialog" ).attr('title',title);
        }
        $( "#selectMemberForm"   ).attr('action', submitUrl);

        if (itemDesc) {
          $( "selectMemberDesc" ) .show();
          $( "#selectMemberDesc" ).html(itemDesc);
        } else {
          $( "selectMemberDesc" ).hide();
        }

        $( "#selectMemberDialog" ).dialog({
          width: 400,
          buttons: [
            { id: "selectMemberOkBtn", disabled: "true", text: "OK", click: function() {
                $( "#selectMemberForm"   ).attr('action', submitUrl).submit();
                $(this).dialog("close");
              } },
            { text: "Cancel", click: function() {
                $(this).dialog("close");
              } }
          ]
        });

    }
//  });

//});

{/literal}</script>

<div id="selectMemberDialog" title="Select" style="display:none">

{assign var='tableItems' value=$members}
{assign var='columns' value=['radio'=>'','name'=>'']}
{assign var='columnsAlign' value=['radio'=>'left','name'=>'left']}

{if empty($members)}
<p>No envelops are found.  Please go to My Registration to create an envelope so one can be selected here!</p>
{else}
<div id="selectMemberDesc" style="text-align:left"></div>
<form id="selectMemberForm" method="post" action="{$actions.addItem}" style="width:250px" class="auth">
<!--<input type="hidden" name="action" value="selectMember">-->
{include file='gcs/common/general-table.tpl'}
<!--<input type="submit" value="Select Envelope">-->
{$additionalFormContent|default:''}
</form>

<p style="text-align:left;margin-bottom:2px;">Items will be added to specified envelopes.  See "My Registration" to view each envelope.</p>
{/if}

</div>

