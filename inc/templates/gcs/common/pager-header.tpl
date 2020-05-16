<div style="text-align:left;float:left;">
  {section name=foo loop=$meta.totalPages}
    {if $meta.page == $smarty.section.foo.iteration}
      {$smarty.section.foo.iteration}
    {else}
      <a href="{$meta.pageUrl}&page={$smarty.section.foo.iteration}">{$smarty.section.foo.iteration}</a>
    {/if}
  {/section}
</div>
<div style="text-align:right;">Page&nbsp;<b>{$meta.page}</b>&nbsp;of&nbsp;<b>{$meta.totalPages}</b>
