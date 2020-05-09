<h3>Edit Event Description</h3>
<form name="formname" action="copyedit.php?id_event={$event.id_event}" method="post"><input
	type="hidden" name="action" value="copyedit_save" /> <input
	type="hidden" name="id_event" value="{$event.id_event}" />
<table width="100%" cellspacing="0" cellpadding="2">
	<tr valign="top">
		<td align="left">Event Title</td>
		<td><input type="text" name="s_title" value="{$event.s_title}"
			size="60" /></td>
	</tr>
	<tr valign="top">
		<td align="left">Game System</td>
		<td><input type="text" name="s_game" value="{$event.s_game}" size="60" /></td>
	</tr>
	<tr valign="top">
		<td align="left" colspan="2">Description*<br />
		<textarea style="width: 100%;" name="s_desc" rows="6" cols="102">{$event.s_desc}</textarea>
		</td>
	</tr>
        <tr valign="top">
                <td align="left" colspan="2">Description (short)* (not used if empty)<br />
                <textarea name="s_desc_web" rows="2" cols="102">{$event.s_desc_web}</textarea>
                </td>
        </tr>
	<tr>
		<td align="center" colspan="2"><input class="button" type="submit"
			name="submit" value="Save To Database" /></td>
	</tr>
</table>

</form>

