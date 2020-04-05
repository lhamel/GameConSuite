{*
  A form which use useful for auth actions.

  Default: username & password
  "change" -> no username, password & confirm fields
  "reset-request" -> username only
  "reset" -> password & confirm field only
  "create" -> username, password, & comfirm fields
 *}
{$header}

{if $ribbon}
<p style="background: #990000; color: #ffffff; font-weight=bold">{$ribbon}</p>
{/if}

<form method="post" action="{$action}">

<p>Activation Key<br/>
<input type="text" name="s_key"/></p>

<p><input type="submit" value="Activate Account"/></p>

</form>

{if $resendAction}
<p><a href="{$resendAction}">Resend activation key</a></p>
{/if}

