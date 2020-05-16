<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" >
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="{$config.email.paypal}" />
<input type="hidden" name="item_name" value="{$config.gcs.name} {$config.ucon.year} Registration #{$id_member}" />
<input type="hidden" name="item_number" value="Member #{$id_member}" />
<input type="hidden" name="amount" value="{$amount|string_format:"%.2f"}" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="lc" value="US" />
<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
<input type="hidden" name="add" value="1" />
</form>

