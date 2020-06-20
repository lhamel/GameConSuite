Name:    {$member.s_fname} {$member.s_lname}
{if $member.s_international}
{$member.s_international}
{else}
Address: {$member.s_addr1}
{if $member.s_addr2}         {$member.s_addr2}{/if}
         {$member.s_city}, {$member.s_state} {$member.s_zip}
{/if}
Phone:   {$member.s_phone}
Email:   {$member.s_email}

