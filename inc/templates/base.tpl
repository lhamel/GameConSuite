<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US" dir="ltr">
<head>
    <title>{$title|default : $config.gcs.meta.defaultTitle}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="keywords" content="{$config.gcs.meta.keywords}" />
    <meta name="description" content="{$config.gcs.meta.description}" />
    <link rel="stylesheet" href="{$config.page.depth}css/style.css" type="text/css"/>

    <link rel="SHORTCUT ICON" href="{$config.page.depth}favicon.ico" />
    <script src="{$config.page.depth}js/jquery-1.4.4.min.js" type="text/javascript" ></script>
    <script src="{$config.page.depth}js/jquery.jeditable.js" type="text/javascript" ></script>
    <script src="{$config.page.depth}js/jquery.jeditable.checkbox.js" type="text/javascript" ></script>
</head>

<body><div class="mainpane" {if isset($width)}style="width:{$width}px"{/if}>

<body>

{if $mobile|default:false}
<style>
table td {
  font-size: 18pt;
}
</style>
{/if}

<div class="mainpane" {if $width|default:0}style="width:{$width}px"{/if}>
<div class="mainbar">

<table cellspacing="0" cellpadding="0">
{if !($mobile|default:false)}
{include file="layout/header.tpl"}
{/if}
<tr>
{if !($mobile|default:false)}
<td class="sidebar"{if $tabs|default:false} rowspan="2"{/if}>
{include file="layout/menu.tpl"}
</td>
{/if}
{strip}
{if $tabs|default:'' && !($mobile|default:false)}
<td class="tabs" colspan="2"><ul class="horizontal">
{foreach item=item from=$tabs}
  <li{if $item.link==$config.page.location} class="selected"{/if}>
    <a href="{$config.page.depth}{$item.link}{$item.querystring|default:''}">{$item.label}</a>
  </li>
{/foreach}
</ul></td></tr><tr>
{/if}
{/strip}
<td class="content" colspan="2">
<!-- begin content -->
{$content}
<!-- end content -->
</td>

</tr></table></div>

{if !($mobile|default:false)}
<div class="footer">
{include file="layout/footer.tpl" title="User Info"}
</div>
{/if}

</div>


{foreach item=item from=$js|default:array()}
  <script src="{$item}"></script>
{/foreach}
</body>
</html>
