<head>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.8.21/themes/base/jquery-ui.css" type="text/css" media="all" />
</head>

<style type="text/css">{literal}
  #sortable { 
    list-style-type: none; 
    margin: 0; 
    padding: 0; 
    width: 60%; 
  }
  #sortable li { 
    margin: 0 3px 3px 3px; 
    padding: 0.4em; 
    padding-left: 1.5em; 
    font-size: 1.4em; 
    height: 18px; 
  }
  #sortable li span { 
    position: absolute; 
    margin-left: -1.3em; 
  }
  #purchaseitems th {
    padding-right: 10px;
  }
{/literal}</style>


<script type="text/javascript">{literal}

$(document).ready(function() {

    $("#sortable").sortable({
        placeholder: 'ui-state-highlight',
        stop: function(i) {
            placeholder: 'ui-state-highlight'
            $.ajax({
                type: "GET",
                url: "{/literal}{$reorderLink}{literal}",
                data: $("#sortable").sortable("serialize")});
        }
    });

    $('.editable').editable('{/literal}{$updateLink}{literal}', {
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
    
});{/literal}</script>

<p>Edit or reorder items available for purchase.</p>

<table id="purchaseitems">
  <thead>
    <tr>
      <th></th>
      <th>Description (Visible)</th>
      <th>Unit Price (Visible)</th>
      <th>Barcode (last 6 digits)</th>
      <th>Itemtype</th>
      <th>Subtype</th>
      <th>Public?</th>
    </tr>
  </thead>
  <tbody id="sortable">
  {foreach from=$items item=item}
  <tr id='item_{$item.id_prereg_item}' class='ui-state-default'>
    <td class='ui-icon ui-icon-arrowthick-2-n-s'></td>
    <td class="editable" id="description-{$item.id_prereg_item}">{$item.description}</td>
    <td class="editable" id="unit_price-{$item.id_prereg_item}">{$item.unit_price}</td>
    <td class="editable" id="barcode-{$item.id_prereg_item}">{$item.barcode}</td>
    <td class="editable" id="itemtype-{$item.id_prereg_item}">{$item.itemtype}</td>
    <td class="editable" id="subtype-{$item.id_prereg_item}">{$item.subtype}</td>
    <td class="editable" id="is_public-{$item.id_prereg_item}">{$item.is_public}</td>
  </tr>
  {/foreach}
  </tbody>
</table>

