U-Con {$config.gcs.year} Event Submission
{* above is the subject *}

Thank you for registering for U-Con {$config.gcs.year}!

This is the confirmation email for the events you have 
offered to run.  If you have received this email in error, 
please contact {$config.email.registration} 
and we will correct the situation.

To make corrections to your events, please email 
{$config.email.registration}.  If you need to cancel your 
events for any reason, please contact the same email 
address.

{* 
Please submit a $10 GM deposit either by paypal (to 
*{$config.email.paypal}*) or print this email and mail it 
along with a check (payable to *U-Con Gaming Club*) to the 
address below:

  U-Con Events
  PO Box 130242
  Ann Arbor, MI 48113-0242

* Please do not send cash through the mail!

*}We appreciate your continued support of U-Con!

--The U-Con Staff
http://www.ucon-gaming.org/

Member Information
------------------------------------------------------------
Name:          {$member.s_fname|default:''} {$member.s_lname|default:''}
Phone:         {$member.s_phone|default:''}
Email:         {$member.s_email|default:''}
Address: 
{if isset($member.s_international)}
{$member.s_international}
{else}
{$member.s_addr1}
{if isset($member.s_addr2) && $member.s_addr2}

{$member.s_addr2}{/if}
{$member.s_city}, {$member.s_state} {$member.s_zip}
{/if}

{foreach key=idx item=event from=$events}{assign var=event_type_abbr value=$event.id_event_type}

Event {$event.gmEventNumber} (#{$event.id_event})
------------------------------------------------------------
Title:         {$event.s_title|default:''|stripslashes}
System:        {$event.s_game|default:''|stripslashes}
Type:          {$constants.events.event_types.$event_type_abbr}
Desc:
{$event.s_desc}

Players:       {$event.i_minplayers}-{$event.i_maxplayers}
Exp/Complex:   {$event.e_exper}{$event.e_complex}
Age Guideline: {$constants.events.ages[$event.i_agerestriction]}
Comments:
{$event.s_comments|stripslashes}

Length:        {$event.i_length} hours
Table Type:    {$event.s_table_type|stripslashes}

Scheduling Comments:
{$event.s_eventcom|stripslashes}

Schedule Preferences:
1: {$constants.events.slots[$event.i_c1]}
2: {$constants.events.slots[$event.i_c2]}
3: {$constants.events.slots[$event.i_c3]}
{/foreach} 
