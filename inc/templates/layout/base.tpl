<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US" dir="ltr">
<head>
    <title>{$title|default : "U-Con Gaming Convention, Ann Arbor Michigan"}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta name="keywords" content="ucon, ann arbor, gaming convention, games, role-playing, collectable card games, ccgs, rpgs, rpga, magic, magic the gathering, auction, university of michigan, uofm, convention, miniatures, historicals, board games, card games" />
    <meta name="description" content="Affordable gaming convention in Ann Arbor, MI featuring a large variety of games, exhibitors hall and auction. Games include Role-playing, RPGA, CCGs, board, miniatures, historicals, card games." />
    <link rel="stylesheet" href="{$config.page.depth}css/style.css" type="text/css"/>

    <link rel="SHORTCUT ICON" href="{$config.page.depth}favicon.ico" />
    <script src="{$config.page.depth}js/jquery-1.4.4.min.js" type="text/javascript" ></script>
    <script src="{$config.page.depth}js/jquery.jeditable.js" type="text/javascript" ></script>
    <script src="{$config.page.depth}js/jquery.jeditable.checkbox.js" type="text/javascript" ></script>
    {*
    <script src="http://www.google-analytics.com/urchin.js" type="text/javascript" />
    <script type="text/javascript">
      _uacct = "UA-2024659-1";
      urchinTracker();
    </script>
    *}
</head>

<body><div class="mainpane" {if $width}style="width:{$width}px"{/if}>

<div class="mainbar">

<table cellspacing="0" cellpadding="0">
{include file="layout/header.tpl"}
<tr>
<td class="sidebar"{if $tabs} rowspan="2"{/if}>
{include file="layout/menu.tpl"}
</td>
{strip}
{if $tabs}
<td class="tabs" colspan="2"><ul class="horizontal">
{foreach item=item from=$tabs}
  <li{if $item.link==$config.page.location} class="selected"{/if}>
    <a href="{$config.page.depth}{$item.link}{$item.querystring}">{$item.label}</a>
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

<div class="footer">
{include file="layout/footer.tpl" title="User Info"}
</div>

</div>

</body>
</html>
