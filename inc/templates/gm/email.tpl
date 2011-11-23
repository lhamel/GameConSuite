{$config.gcs.con.name} {$config.ucon.year} Event Submission
{* above is the subject *}

Thank you for registering for {$config.gcs.con.name} {$config.ucon.year}!

This is the confirmation email for the events you have 
offered to run.  If you have received this email in error, 
please contact {$config.email.registration} 
and we will correct the situation.

To make corrections to your events, please email 
{$config.email.registration}.  If you need to cancel your 
events for any reason, please contact the same email 
address.

Please submit a $10 GM deposit either by paypal (to 
*{$config.gcs.payments.paypalEmail}*) or print this email and mail it 
along with a check (payable to *{$config.gcs.payments.checkPayable*)*) to the 
address below:

  U-Con Events
  PO Box 4491
  Ann Arbor, MI 48106-4491

* Please do not send cash through the mail!

We appreciate your continued support of U-Con!

--The U-Con Staff
http://www.ucon-gaming.org/

{foreach key=idx item=event from=$events}

Event {$event.gmEventNumber} (#{$event.id_event})
------------------------------------------------------------
Title:         {$event.s_title|stripslashes}
System:        {$event.s_game|stripslashes}
Type:          {$event.s_event_type} ({$event.id_event_type})
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
