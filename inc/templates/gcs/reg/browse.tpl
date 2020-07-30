

{if !isset($REQUEST)}{assign var="REQUEST" value=array('tags'=>'', 'day'=>'', 'ages'=>'', 'category'=>'')}{/if}
{if !$loginInfo.loggedin && $config.allow.buy_events}
<p style="padding: 2px; margin-top: 6px; background: #880000; color: #FFFFFF; font-weight: bolder; font-size: larger;">User not logged in.  Log in to be able to add tickets.</p>
{/if}
<h1>Browse Events</h1>

<form>
<input name="search" value="{$config.page.request.search|default:''|urlencode}" />
<input value="search" type="submit" />
</form>

{strip}
<p>Day: <a href="{$actions.list}?day=&amp;ages={$REQUEST.ages}&amp;tags={$REQUEST.tags}&amp;category={$REQUEST.category}"{if ''==$REQUEST.day} class="selected"{/if}>All</a>
{foreach key=key item=item from=$constants.events.days} | <a href="{$actions.list}?day={$key}&amp;category={$REQUEST.category}&amp;tags={$REQUEST.tags}&amp;ages={$REQUEST.ages}"{if $key==$REQUEST.day} class="selected"{/if}>{$item}</a>{/foreach}
</p>
{/strip}

{strip}
<p>Category: <a href="{$actions.list}?day={$REQUEST.day}&amp;ages={$REQUEST.ages}&amp;tags={$REQUEST.tags}&amp;category="{if ''==$REQUEST.category} class="selected"{/if}>All</a>
{foreach key=key item=item from=$constants.events.event_types} | <a href="{$actions.list}?day={$REQUEST.day}&amp;ages={$REQUEST.ages}&amp;tags={$REQUEST.tags}&amp;category={$key}"{if $key==$REQUEST.category} class="selected"{/if}>{$item}</a>{/foreach}
</p>
{/strip}

{strip}
<p>Ages: <a href="{$actions.list}?day={$REQUEST.day}&amp;category={$REQUEST.category}&amp;tags={$REQUEST.tags}&amp;ages="{if ''==$REQUEST.ages} class="selected"{/if}>All</a>
{foreach key=key item=item from=$constants.events.agesNoBlank} | <a href="{$actions.list}?day={$REQUEST.day}&amp;category={$REQUEST.category}&amp;tags={$REQUEST.tags}&amp;ages={$key}"{if $key==$REQUEST.ages} class="selected"{/if}>{$item}</a>{/foreach}
</p>
{/strip}

{strip}
<p>Tags: <a href="{$actions.list}?day={$REQUEST.day}&amp;category={$REQUEST.category}&amp;ages={$REQUEST.ages}&amp;tags="{if ''==$REQUEST.tags} class="selected"{/if}>All</a>
{foreach key=key item=item from=$tags} | <a href="{$actions.list}?day={$REQUEST.day}&amp;category={$REQUEST.category}&amp;ages={$REQUEST.ages}&amp;tags={$key}"{if $key==$REQUEST.tags} class="selected"{/if}>{$item}</a>{/foreach}
</p>
{/strip}

<hr/>

{if isset($events)}
<div class="event_search_result">
{if $action.showTable|default:false}
{include file="gcs/event/list-table.tpl"}
{else}
{include file="gcs/event/list-detail.tpl"}
{/if}
</div>
{else}
{if $showResults}
<p><i>No events found.</i></p>
{/if}
{/if}
