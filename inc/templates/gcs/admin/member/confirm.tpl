<script type="text/javascript">{literal}
<!--
function insert(type) {
	$.ajax({
    url: "{/literal}{$actions.insert}{literal}",
    data: "type="+type,
    success: function(data) {
      var orig = $('#message').val();
      $('#message').val(orig+data);
    },
    errors: function() {
      alert('Error ' + type);
      /* $('#message').text = data; */
    },
    //dataType: 'text/plain'
  });
}
//-->
</script>{/literal}
{if $error|default:''}{strip}
<p style="background: #FF6666;">{$error}</p>
{/strip}{/if}
<h1>Send Confirmation Email</h1>

<p>Use this area to compose the email.  Click the buttons 
to add additional text to the end of the text area.</p>

<table>
<tr>
<td>
<form style="margin:0" method="POST" action="{$actions.submit}">
<input type="hidden" name="id_member" value="{$id_member}"/>
Subject (if different): <input type="text" name="subject" value=""/><br/>
From (if different): <input type="text" name="from" value=""/><br/>
CC (if different): <input type="text" name="cc" value=""/><br/>
<textarea id="message" style="font-family: Courier New; font-size: 9pt;" name="message" cols="80" rows="35">{$msg|default:''}</textarea><br/>
<input type="submit" value="Send Email"/><br/>
</form>
</td>
<td>
Full Templates:<br/>
<input type="button" onclick="insert('paid')" value="Payment Confirmation"/><br/>
<!--<input type="button" onclick="insert('sched')" value="GM Confirmation"/><br/>-->
<!--<input type="button" onclick="insert('reqt')" value="Request GM Deposit"/><br/>-->
<br/>
Sections<br/>
<input type="button" onclick="insert('per')" value="Personal Info"/><br/>
<input type="button" onclick="insert('gms')" value="GM Events"/><br/>
<input type="button" onclick="insert('reg')" value="Registration"/><br/>
<input type="button" onclick="insert('owe')" value="Owes"/><br/>
<input type="button" onclick="insert('sig')" value="Signature"/><br/>

</td>
</tr>
</table>
