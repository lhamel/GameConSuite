{if $error|default:null}
<p style="background: #ff9999">{$error}</p>
{/if}

<h1>Search U-Con Events</h1>

Enter search term, GM last name, or barcode:

<div class="searchform">
 <form>
  <input type="hidden" name="action" value="search_member"/>
  <input type="text" id="search" name="search" value="{$REQUEST.search|default:''}" size="30" />
  <input type="submit" value="go" />
 </form>
</div>

{literal}
<script type="text/javascript">
<!--
$(function() {
  $('#search').focus();
});
-->
</script>
{/literal}

