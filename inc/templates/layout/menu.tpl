<!-- begin sidebar -->
<ul class="menulist">
{section name=i loop=$menu.items}
{strip}
<li>{$menu.items[i].label|escape}<ul>
  {section name=j loop=$menu.items[i].children}
  {strip}
    <li><a href="{$menu.depth}{$menu.items[i].children[j].link}"{$menu.items[i].children[j].selected}>{$menu.items[i].children[j].label}</a></li>
  {/strip}
  {/section}
</ul></li>
{/strip}
{/section}
</ul>
<div class="ads">{$menu.ad}</a></div>
<!-- end sidebar -->
