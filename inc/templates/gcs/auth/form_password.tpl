{*
  A form which use useful for auth actions.

  Default: username & password
  "change" -> no username, just password & confirm fields
  "reset-request" -> username only
  "reset" -> password & confirm field only
  "create" -> username, password, & comfirm fields
 *}
{$header}

{if isset($ribbon)}
<p style="background: #990000; color: #ffffff; font-weight=bold">{$ribbon}</p>
{/if}

<form method="post" action="{$action}">

{if $type != "change" && $type != "reset"}
<p>Email<br/>
<input type="text" name="s_email" size="35"/></p>
{/if}

{if $type == "reset"}
<p>Reset Key<br/>
<input type="password" name="s_resetkey" size="40"/></p>
{/if}

{if $type == "change"}
<p>Current Password<br/>
<input type="password" name="s_oldpass" size="40"/></p>
{/if}

{if $type != "reset-request"}
<p>{if $type == "change" || $type == "reset"}New {/if}Password<br/>
<input type="password" id="s_pass" name="s_pass" size="40"/> <img id="check" src="{$config.page.depth}images/checkmark.gif" style="visibility:hidden;margin:0"></p>
{if $type == "create" || $type == "change" || $type == "reset"}
<p><meter max="4" id="password-strength-meter"></meter>
<span id="password-strength-text"></span></p>

{* https://css-tricks.com/password-strength-meter/ *}
<script src="{$config.page.depth}gcs/zxcvbn.js"></script>
<script>{literal}

var strength = {
  0: "Worst",
  1: "Bad",
  2: "Weak",
  3: "Good",
  4: "Strong"
}

var password = document.getElementById('s_pass');
var meter = document.getElementById('password-strength-meter');
var text = document.getElementById('password-strength-text');
var check = document.getElementById('check');

password.addEventListener('input', function() {
  var val = password.value;
  var result = zxcvbn(val);

  // Update the password strength meter
  meter.value = result.score;

  // Update the text indicator
  if (val !== "") {
    text.innerHTML = strength[result.score];
  } else {
    text.innerHTML = "";
  }
  check.style.visibility = (result.score>=3) ? 'visible' : 'hidden';
  
});
{/literal}
</script>
{/if}

{if $type == "reset" || $type == "create" || $type == "change"}
<p>Reenter password<br/>
<input type="password" id="p2" name="s_pass2" size="40"/> <img id="check2" src="{$config.page.depth}images/checkmark.gif" style="visibility:hidden;margin:0"></p>
<script>{literal}

var p2 = document.getElementById('p2');

function compareFn() {
  var val1 = password.value;
  var val2 = p2.value;

  check2.style.visibility = (val1==val2 && val1!=='') ? 'visible' : 'hidden';
}

password.addEventListener('input', compareFn);
p2.addEventListener('input', compareFn);


{/literal}</script>

{/if}
{/if}

<p><input type="submit" value="{if $type == "reset"}Reset password{elseif $type == "reset-request"}Send reset email{elseif $type == "change"}Change password{elseif $type == "create"}Create account{else}Login{/if}"/></p>

</form>

{if $forgotAction}
<p><a href="{$forgotAction}">Forgot password?</a></p>
{/if}

{if $createAction}
<p><a href="{$createAction}">Create account?</a></p>
{/if}

{if isset($resendAction)}
<p><a href="{$resendAction}">Resend activation key</a></p>
{/if}

