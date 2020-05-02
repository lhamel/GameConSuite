<script type="text/javascript">{literal}

//$(document).ready(function() {

//  $( function() {
    function removeItemDialog(itemDesc, submitUrl) {
      console.log("show remove item dialog");
/*
      var title = "Remove Item from Envelope";

        try {
          $( "#removeItemDialog" ).dialog('option', 'title', title);
        } catch (e) {
          $( "#removeItemDialog" ).attr('title',title);
        }
        $( "#removeItemForm"   ).attr('action', submitUrl);
*/
        if (itemDesc) {
          $( "removeItemDesc" ) .show();
          $( "#removeItemDesc" ).html(itemDesc);
        } else {
          $( "removeItemDesc" ).hide();
        }

        $( "#removeItemDialog" ).dialog({
          width:350,
          buttons: [
            { text: "OK", click: function() {
                $( "#removeItemForm"   ).attr('action', submitUrl).submit();
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

<div id="removeItemDialog" title="Remove Item from Envelope" style="display:none">

<form id="removeItemForm" method="post" action="{$actions.addItem}" class="auth">
<div id="removeItemDesc" style="text-align:left"></div>
<!--<input type="hidden" name="action" value="removeItem">-->
<!--<input type="submit" value="Select Envelope">-->
</form>

</div>

