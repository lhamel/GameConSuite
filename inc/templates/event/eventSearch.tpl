<form class="event_search">
<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tr>
<td align="right">Search Text:</td>

<td><input type="text" name="s_game" value="" /></td>
<td align="right">Day:</td>
<td>
  {html_options name=e_day options=$options.daysWithBlank selected=$form.e_day}
</td>

</tr>

<tr>

<td align="right">Category:</td>
<td>
  {html_options name=s_event_type options=$options.eventTypesWithBlank selected=$form.s_event_type}
</td>

<td align="right">Starts on or after:</td>
<td>
  {html_options name=start_time options=$options.timesWithBlank selected=$form.start_time}
</td>
</tr>

<tr><td></td><td></td>
<td align="right">Ends on or before:</td>
<td>
  {html_options name=end_time options=$options.timesWithBlank selected=$form.end_time}
</td>
</tr>

<tr>
</tr>



<tr><td colspan="4" align="center">

Exclude Sold Out Events:
<input type="checkbox" name="sold_out" value="1"  />
<input type="submit" value="Search" /></td></tr>

</table>
</form>
