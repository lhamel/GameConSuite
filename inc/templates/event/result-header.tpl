<table cellspacing="0" cellpadding="2" width="100%">
  <tr>
    <td class="searchResultHeader">
      {if $search.currentPage==1}previous{else}<a href="{$form.submit}?search={$form.search}&amp;e_day={$form.e_day}&amp;s_event_type={$form.s_event_type}&amp;start_time={$form.start_time}&amp;end_time={$form.end_time}&amp;page=1">previous</a>{/if}
      {php} for ($i=0; $i<$search.totalPages; $i++) { {/php}
        {if $i == $search.currentPage}
          <span style="font-weight: bolder;">{$i}</span>
        {else}
<a href="{$form.submit}?search={$form.search}&amp;e_day={$form.e_day}&amp;s_event_type={$form.s_event_type}&amp;start_time={$form.start_time}&amp;end_time={$form.end_time}&amp;page=1">{$i}</a>
        {/if}
      {php} } {/php}
<a href="{$form.submit}?search={$form.search}&amp;e_day={$form.e_day}&amp;s_event_type={$form.s_event_type}&amp;start_time={$form.start_time}&amp;end_time={$form.end_time}&amp;page={$search.currentPage+1}">next</a>
    </td>
    <td align="right">
      Page&nbsp;<b>$search.currentPage</b>&nbsp;of&nbsp;<b>$search.totalPages</b>
    </td>
  </tr>
</table>
