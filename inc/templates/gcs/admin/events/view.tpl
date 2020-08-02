{capture name="e_experField" assign="e_experField"}
{html_options name=e_exper options=$constants.events.experience.select selected=$event.e_exper}
{/capture}
{capture name="e_complexField" assign="e_complexField"}
{html_options name=e_complex options=$constants.events.complexity.select selected=$event.e_complex}
{/capture}
{capture name="id_roomField" assign="id_roomField"}
{html_options name=id_room options=$constants.events.roomsWithBlank}
{/capture}
{capture name="e_dayField" assign="e_dayField"}
{html_options name=e_day options=$constants.events.daysWithBlank selected=$event.e_day}
{/capture}
{capture name="i_timeField" assign="i_timeField"}
{html_options name=i_time options=$constants.events.timesWithBlank selected=$event.i_time}
{/capture}

<script type="text/javascript">{literal}

$(document).ready(function() {
    var postUrl = 'ajax_edit.php?id_event={/literal}{$event.id_event}{literal}';

    $('.editable').editable(postUrl, {
        indicator : 'Saving...',
        tooltip   : 'Click to edit...',
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
     $('.editableSpan').editable(postUrl, {
        indicator : 'Saving...',
        tooltip   : 'Click to edit...',
        //cancel    : 'Cancel',
        //submit    : 'OK',
        id : 'field',
        name : 'new',
        style : 'display: inline',
        width : '30px',
        onerror: function(settings, original, xhr) {
           original.reset();
           alert(xhr.responseText);
        },
    });
    $('.editableArea').editable(postUrl, { 
        id : 'field',
        name : 'new',
        type      : 'textarea',
        cancel    : 'Cancel',
        submit    : 'OK',
        indicator : 'Saving...',
        tooltip   : 'Click to edit...'
    });
    $('#id_event_type').editable(postUrl, {
        indicator : 'Saving...',
        tooltip   : 'Click to edit...',
        submit    : 'OK',
        id : 'field',
        name : 'new',
        style : 'display: inline',
        data : {/literal}{$jsonData.eventTypeData}{literal},
        type : 'select',
        onerror: function(settings, original, xhr) {
           original.reset();
           alert(xhr.responseText);
        },
    });

	$('#i_agerestriction').editable(postUrl, {
        indicator : 'Saving...',
        tooltip   : 'Click to edit...',
        submit    : 'OK',
        id : 'field',
        name : 'new',
        style : 'display: inline',
        data : {/literal}{$jsonData.agesData}{literal},
        type : 'select',
        onerror: function(settings, original, xhr) {
           original.reset();
           alert(xhr.responseText);
        },
        content : function(string, settings, original) {
            alert('content');
        }
    });

	$.editable.addInputType('expcomp', {
      element : function(settings, original) {
      	var expselect = $('{/literal}{strip}{$e_experField|regex_replace:"/[\r\t\n]/":" "}{/strip}{literal}');
    	var compselect = $('{/literal}{strip}{$e_complexField|regex_replace:"/[\r\t\n]/":" "}{/strip}{literal}');
        $(this).append(expselect);
        $(this).append(compselect);

        /* Hidden input to store value which is submitted to server. */
        var hidden = $('<input type="hidden">');
        $(this).append(hidden);
        return(hidden);
      },
      submit: function (settings, original) {
          var value = $("[name=e_exper]").val() + $("[name=e_complex]").val();
          $("input", this).val(value);
      },
      content : function(string, settings, original) {
          var expVal = string.substr(0,1);
          var compVal  = string.substr(1,1);
          $("[name=e_exper]", this).children().each(function() {
              if (expVal == $(this).val()) {
                  $(this).attr('selected', 'selected');
              }
          });
          $("[name=e_complex]", this).children().each(function() {
              if (compVal == $(this).val()) {
                  $(this).attr('selected', 'selected')
              }
          });
      }
    });
    $("#expcomp").editable(postUrl, {
      type: 'expcomp',
      id : 'field',
      name : 'new',
      submit: "OK",
      cancel    : 'Cancel',
      style : "display: inline",
      tooltip : "Click to edit..."
    });

    // set up the tagEditor for the tags field
    var tagsValue = "{/literal}{$event.tags}{literal}";
    var tagsArr = tagsValue.split(",");
    var prevValue = tagsValue;
    $('#tags').tagEditor({ 
      initialTags: tagsArr, 
      placeholder: 'Click to edit...',
      position: { collision: 'flip' }, // automatic menu position up/down
      autocomplete: { source: 'tags_ajax.php', minLength: 1 } ,
      onChange: function(field, editor, tags) {
        //alert("changed tags to "+tags);

        // TODO check to see if any of the tags are new and ask for confirmation

        // call script to save new value
        $.ajax({
          type: "POST",
          url: postUrl,
          data: {field: 'tags', new: tags},
          done: function(data, textStatus, jqXHR) {
            prevValue = tags;
          },
          fail: function(data, textStatus, jqXHR) {
            // TODO how do you restore the previous value????
            alert('failed: '+ data.errorMsg);

            // TODO why are failures being squashed?
          }
          // dataType: dataType
        });


      }
    });

    $.editable.addInputType('players', {
        element : function(settings, original) {
          var minInput = $('{/literal}<input id="i_minplayers" name="i_minplayers" value="{$event.i_minplayers}" type="text" size="3" />{literal}');
          var maxInput = $('{/literal}<input id="i_maxplayers" name="i_maxplayers" value="{$event.i_maxplayers}" type="text" size="3" />{literal}');

          $(this).append(minInput);
          $(this).append(' - ');
          $(this).append(maxInput);

          /* Hidden input to store value which is submitted to server. */
          var hidden = $('<input type="hidden">');
          $(this).append(hidden);
          return(hidden);
        },
        submit: function (settings, original) {
            var value = $("[name=i_minplayers]").val() + ' - ' + $("[name=i_maxplayers]").val();
            $("input", this).val(value);
        },
        content : function(string, settings, original) {
            var theSplit = string.split("-");
            var min = theSplit[0].trim();
            var max = theSplit[1].trim();
            $("#i_minplayers", this).attr('value', min);
            $("#i_maxplayers", this).attr('value', max);
        }
      });
      $("#players").editable('ajax_edit.php?id_event={/literal}{$event.id_event}{literal}', {
        type: 'players',
        id : 'field',
        name : 'new',
        submit: "OK",
        cancel    : 'Cancel',
        style : "display: inline",
        tooltip : "Click to edit..."
      });

      $.editable.addInputType('location', {
          element : function(settings, original) {
          	var roomselect = $('{/literal}{strip}{$id_roomField|regex_replace:"/[\r\t\n]/":" "}{/strip}{literal}');
            var tableInput = $('{/literal}<input id="s_table" name="s_table" value="{$event.s_table}" type="text" size="3" />{literal}');
            $(this).append(roomselect);
            $(this).append(' ');
            $(this).append(tableInput);
            /* Hidden input to store value which is submitted to server. */
            var hidden = $('<input type="hidden">');
            $(this).append(hidden);
            return(hidden);
          },
          submit : function(settings, original) {
              var roomName = $("[name=id_room] option:selected", this).text();
              var table = $("[name=s_table]", this).val();
              var idRoom = $("[name=id_room]", this).val();
              var value = roomName + "\n" + table + "\n" + idRoom;
              $("input", this).val(value);
          },
          content : function (string, settings, original) {
              var theSplit = string.split("\n");
              var roomName = theSplit[0].trim();
              var table = theSplit.length > 1 ? theSplit[1].trim() : "";
              $("#s_table", this).attr('value', table);
              $("[name=id_room]", this).children().each(function() {
                  if (roomName == $(this).text()) {
                      $(this).attr('selected', 'selected')
                  }
              });
          }
      });
      $("#location").editable('ajax_edit.php?id_event={/literal}{$event.id_event}{literal}', {
          type: 'location',
          id : 'field',
          name : 'new',
          submit: "OK",
          cancel    : 'Cancel',
          style : "display: inline",
          tooltip : "Click to edit..."
        });

      $.editable.addInputType('dayTime', {
          element : function(settings, original) {
            var daySelect = $('{/literal}{strip}{$e_dayField|regex_replace:"/[\r\t\n]/":" "}{/strip}{literal}');
            var timeSelect = $('{/literal}{strip}{$i_timeField|regex_replace:"/[\r\t\n]/":" "}{/strip}{literal}');
            var lengthInput = $('{/literal}<input id="i_length" name="i_length" value="{$event.i_length}" type="text" size="3" />{literal}');

          	var roomselect = $('{/literal}{strip}{$id_roomField|regex_replace:"/[\r\t\n]/":" "}{/strip}{literal}');
            $(this).append(daySelect);
            $(this).append(' ');
            $(this).append(timeSelect);
            //$(this).append(' hr<br/>');

            /* Hidden input to store value which is submitted to server. */
            var hidden = $('<input type="hidden">');
            $(this).append(hidden);
            return(hidden);
          },
          submit : function(settings, original) {
              var day = $("[name=e_day] option:selected", this).val();
              var time = $("[name=i_time]", this).val();
              //var length = $("#i_length", this).val();
              var value = day + "\n" + time;
              $("input", this).val(value);
          },
          content : function (string, settings, original) {
        	  string = string.replace(' ', '-');
              var theSplit = string.split("-");
              var day = theSplit[0].trim();
              var time = theSplit.length > 1 ? theSplit[1].trim() : "";
              $("[name=e_day]", this).children().each(function() {
                  if (day == $(this).text()) {
                      $(this).attr('selected', 'selected')
                  }
              });
              $("[name=i_time]", this).children().each(function() {
                  if (time == $(this).text()) {
                      $(this).attr('selected', 'selected')
                  }
              });
          }
      });
      $("#dayTime").editable('ajax_edit.php?id_event={/literal}{$event.id_event}{literal}', {
          type: 'dayTime',
          id : 'field',
          name : 'new',
          submit: "OK",
          cancel    : 'Cancel',
          style : "display: inline",
          tooltip : "Click to edit..."
      });

});{/literal}</script>

<div class="viewEvent">

<h2>{$event.s_game}{if $event.s_title && $event.s_title != $event.s_game} {$event.s_title}{/if}</h2>

<table cellspacing="0" cellpadding="0">
<tr><td>


<table cellspacing="0" cellpadding="0">
<tr>
  <td class="left">Event Number</td> 
  <td class="right"><div class="editable" id="s_number">{$event.s_number}</div></td>
</tr>
<tr>
  <td class="left">Gamemaster</td>
  <td class="right">
    <a href="{$config.page.depth}{$actions.viewMember}{$event.id_gm}">{$event.gamemaster}</a>
  </td>
</tr>
<tr>
  <td class="left">Event Track</td>
  <td class="right"><div class="editableSelect" id="id_event_type">{$event.track}</div></td>
</tr>
<tr>
  <td class="left">Game System</td>
  <td class="right"><div class="editable" id="s_game">{$event.s_game}</div></td>
</tr>
<tr>
  <td class="left">Title</td>
  <td class="right"><div class="editable" id="s_title">{$event.s_title}</div></td>
</tr>
<tr>
  <td class="left">Players</td>
  {strip}<td class="right">
    <div class="editableCustom" id="players">{$event.i_minplayers} - {$event.i_maxplayers}</div>
  </td>{/strip}
</tr>
<tr>
  <td class="left">{strip}
    <a href="javascript: beadWindow=window.open('{$config.page.depth}gcs/events/expcomp.php', 'exp_comp', 'width=600, height=400, left=213, top=200'); beadWindow.focus(); ">Exp/Complex</a>
  {/strip}</td>
  <td class="right"><div id="expcomp" class="editableCustom">{$constants.events.experience.display[$event.e_exper]}/{$constants.events.complexity.display[$event.e_complex]}</div></td>
</tr>
<tr>
  <td class="left">Age Guideline</td>
  <td class="right"><div id="i_agerestriction" class="editableSelect">{$constants.events.ages[$event.i_agerestriction]}</div></td>
</tr>
<tr>
  <td class="left">Long Description</td>
  <td class="right"><div class="editableArea" id="s_desc">{$event.s_desc|stripslashes}</div></td>
</tr>
<tr>
  <td class="left">Shortened Description (if different)</td>
  <td class="right"><div class="editableArea" id="s_desc_web">{$event.s_desc_web}</div></td>
</tr>
</table>

</td><td style="padding-left: 10px;">

<table cellspacing="0" cellpadding="0">
<tr>
  <td class="left">Status</td> 
  {strip}<td class="right">
	{if !$event.e_day || !$event.i_time}
	  <img src="{$config.page.depth}images/gcs/event/schedule.gif" height="12" title="unscheduled">
    {elseif !$event.s_number}
	  <img src="{$config.page.depth}images/gcs/event/number.jpg" height="12" title="requires number">
    {elseif !$event.b_approval}
	  <img src="{$config.page.depth}images/gcs/event/approval.gif" height="12" title="requires approval">
    {elseif !$event.id_room}
	  <img src="{$config.page.depth}images/gcs/event/door2.gif" height="12" title="requires location">
    {elseif !$event.b_edited}
	  <img src="{$config.page.depth}images/gcs/event/copyedit.gif" height="12" title="copyedit">
    {else}
	  <img src="{$config.page.depth}images/gcs/event/icon_check.png" height="12" title="complete">
    {/if}
  </td>{/strip}
</tr>
<tr>
  <td class="left">Price</td>
  <td class="right"><div id="i_cost" class="editable">{if $event.i_cost>0}${$event.i_cost}{else}Free!{/if}</div></td>
</tr>
<tr>
  <td class="left">Length</td>
  <td class="right"><div id="i_length" class="editable">{$event.i_length}</div></td>
</tr>
<tr>
  <td class="left">Day / Time</td>
  <td class="right">{strip}
    <div id="dayTime" class="editableCustom">
    {$constants.events.daysWithBlank[$event.e_day]} {$constants.events.timesWithBlank[$event.i_time]}-{$constants.events.timesWithBlank[$event.endtime]}
    </div>
  {/strip}</td>
</tr>
<tr>
  <td class="left">Room / Table</td>
  <td class="right"><div class="editableCustom" id="location">{$constants.events.roomsWithBlank[$event.id_room]}
{$event.s_table}</div></td>
</tr>
<tr>
  <td class="left">Tags</td>
  <td class="right"><input id="tags" name="tags" value="{* filled in by javascript *}" type="text" size="40" />
  <span style="color: gray; font-style: italic;">after editing press enter to save all tags</span>
  </td>
</tr>

{if isset($config.gcs.virtual_venue) && $config.gcs.virtual_venue}
<tr>
  <td class="left">VTT Link</td>
  <td class="right"><div id="s_vttlink" class="editable">{$event.s_vttlink}</div></td>
</tr>

<tr>
  <td class="left">VTT Info</td>
  <td class="right"><div class="editableArea" id="s_vttinfo">{$event.s_vttinfo|stripslashes}</div></td>
</tr>

<tr>
  <td class="left">Platform</td>
  <td class="right"><div class="editableArea" id="s_platform">{$event.s_platform|stripslashes}</div></td>
</tr>
{/if}

</table>


</td></tr></table>

{if $buttonBar}
<p class="buttonBar">
{foreach from=$buttonBar item=button}
  <a href="{$button.url}" class="button">{$button.label}</a>
{/foreach}
</p>
{/if}

<h3>Private Information</h3>
<table cellspacing="0" cellpadding="0"><tr><td>

<table cellspacing="0" cellpadding="0">
<tr>
  <td colspan="2" style="background-color: #cccccc; height: 5px;"></td>
</tr>
<tr>
  <td class="left">Submitted</td>
  <td class="right">{$event.d_created}</td>
</tr>
<tr>
  <td class="left">Last Updated</td>
  <td class="right">{$event.d_updated}</td>
</tr>
<tr>
  <td class="left"><a href="tickets.php?id_event={$event.id_event}">Tickets Sold</a></td>
  <td></td>
</tr>
</table>

</td><td style="padding-left: 10px; width:50%">

<table cellspacing="0" cellpadding="0" style="width:100%">
<tr>
  <td colspan="2" style="background-color: #cccccc; height: 5px;"></td>
</tr>
<tr>
  <td class="left">Choices</td>
  <td>{$constants.events.slots[$event.i_c1]}<br/>{$constants.events.slots[$event.i_c2]}<br/>{$constants.events.slots[$event.i_c3]}</td>
</tr>
<tr>
  <td class="left">Table Request</td>
  <td>{$event.s_setup} {$constants.events.tableTypesExtended[$event.s_table_type]}</td>
</tr>
<tr>
  <td class="left">Event<br/>Comments</td>
  <td>
    <div class="editableArea" id="s_comments">{$event.s_comments|stripslashes}</div>
  </td>
</tr>
<tr>
  <td class="left">Scheduling<br/>Comments</td>
  <td>
    <div class="editableArea" id="s_eventcom">{$event.s_eventcom|stripslashes}</div>
  </td>
</tr>
</table>

</td></tr></table>

</div>

{* {$event.s_tourntype}<br/> *}

<br/>
<b>Analysis:</b><br/>
Slot: {$event.i_slot}<br/>
Prize: {$event.b_prize}<br/>
Full: {$event.b_full}<br/>
Notes: {$event.s_note}<br/>
GM appeared: {$event.b_showed_up}<br/>
Prereg count: {$event.i_prereg}<br/>
Actual: {$event.i_actual}<br/>
Non-generics: {$event.i_real_tickets}<br/>
Tix on Board: {$event.i_remaining_tickets}<br/>

{if $actions.deleteEvent|default:0 && $event.s_title=="DELETE" && $event.s_game=="DELETE"}
<br/>
<a class="button" href="{$config.page.depth}{$actions.deleteEvent}">Delete Event</a>
{/if}

{* 
{include file="gcs/admin/events/schedule.tpl"}
*}
