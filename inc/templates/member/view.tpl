<table class="member_info" cellspacing="0" cellpadding="0">
<tr>
  <th>Name &amp; Address</th>
  <th>Contact Info</th>
</tr>
<tr>
<td class="address">
{$member.s_fname} {$member.s_lname}<br />
{if $member.s_international}
{$member.s_international}
{else}
{$member.s_addr1}<br />
{if $member.addr2}{$member.addr2}<br />{/if}
{$member.s_city}, {$member.s_state} {$member.s_zip}<br />
{/if}
</td>
<td class="phoneemail">
Email: {$member.s_email}<br />
Phone: {$member.s_phone}<br />
</td>
</tr>
</table>
