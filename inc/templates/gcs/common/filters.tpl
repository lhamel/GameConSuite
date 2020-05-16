{strip}
{foreach key=filterKey item=filter from=$filters|default:array()}
<p>{$filter.label}: {if !($filter.noall|default:false)}<a href="{$config.page.basename}?{$filterKey|default:''}={$filter.fixed|default:''}"{if ''==$REQUEST.$filterKey|default:'' && !($filter.default|default:'')} class="selected"{/if}>All</a>{/if}
  {foreach key=optionKey item=option from=$filter.options} | <a href="{$config.page.basename}?{$filterKey}={$optionKey}{$filter.fixed|default:''}" 
  {if $filter.default|default:''}
    {if $optionKey==$filter.default } class="selected"{/if}
  {else}  
    {if $optionKey==($REQUEST.$filterKey|default:'') && ($REQUEST.$filterKey|default:'')!=''} class="selected"{/if}
  {/if}
   >
{$option}
</a>{/foreach}
</p>
{/foreach}
{/strip}
