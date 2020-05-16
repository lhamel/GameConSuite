{if $error|default:''}
<p style="background: #ff9999">{$error}</p>
{/if}

<h1>Search Membership</h1>

<p class="small">Enter the first or last name to search for a member.</p>

<div class="searchform">
 <form>
  <input type="hidden" name="action" value="search_member"/>
  First name, Last name, Both (in order), or email address<br/>
  <input type="text" name="search" value="{$REQUEST.search|default:''}" size="30" />
  <input type="submit" value="go" />
 </form>
</div>
