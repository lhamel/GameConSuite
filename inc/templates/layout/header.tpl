<!-- begin header -->
{if $config.maintenance.message|default:''}
<tr class="header">
  <td class="header" style="text-align:center;background:navy;color:#ff8;font-weight:bold;;font-size:larger" colspan="3">
    {$config.maintenance.message}
  </td>
</tr>
{/if}
<tr class="header">
  <td class="header">
    <a href="{$config.page.depth}index.php"><img src="{$config.page.depth}images/theme/logo.png" alt="{$config.logo.alt|default:''}" class="logo"/></a>
  </td>
  <td class="header">
    <a href="{$config.page.depth}index.php"><img src="{$config.page.depth}images/theme/title.png" alt="{$config.logo.alt|default:''}" /></a>
<!--    <br/><span style="margin: 0px; font-size: 12pt; color: #dd3333; font-weight: bold;">**New Location: Metropolitan Hotel, Romulus Michigan</span> -->
  </td>
  <td class="header" style="width: 330px; text-align: center; font-size: smaller; font-weight: bold; ">
    <span style="margin: 0px; font-size: 12pt; color: #dd3333;">{$config.gcs.dates.all}, {$config.gcs.location}</span>
<br/>
{$config.gcs.tagline}
  </td>
<!-- end header -->
