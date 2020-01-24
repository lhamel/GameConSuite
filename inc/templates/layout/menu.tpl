<!-- begin sidebar -->
<ul class="menulist">
{section name=i loop=$menu.items}
{strip}
<li>{$menu.items[i].label}<ul>
  {section name=j loop=$menu.items[i].children}
  {strip}
    <li>
      {if $menu.items[i].children[j].url|default:'' || $menu.items[i].children[j].link}
      <a href="{if $menu.items[i].children[j].url|default:''}{$menu.items[i].children[j].url}{else}{$menu.depth}{$menu.items[i].children[j].link}{/if}"{$menu.items[i].children[j].selected|default:false}>
      {/if}
        {$menu.items[i].children[j].label}
      {if $menu.items[i].children[j].url|default:'' || $menu.items[i].children[j].link}
      </a>
      {/if}
    </li>
  {/strip}
  {/section}
</ul></li>
{/strip}
{/section}
</ul>
<div class="ads">{$menu.ad}</a></div>
<!-- end sidebar -->
