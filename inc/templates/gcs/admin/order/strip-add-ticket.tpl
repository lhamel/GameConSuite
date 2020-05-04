<div class="ticketstripe">
<a href="../member/order.php?action=addTicket&id_member={$currMember.id_member}&id_event={$currEvent.id_event}" class="button">Add ticket</a> 
<a href="../event/index.php?id_event={$currEvent.id_event}">{$currEvent.s_title}</a> to 
<a href="../member/index.php?id_member={$currMember.id_member}">{if $currMember.s_fname}{$currMember.s_fname} {/if}{$currMember.s_lname}</a>

</div>