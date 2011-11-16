<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:        modifier
 *
 * Name:        commify
 *
 * Purpose:     format numbers by inserting thousands seperators
 *              this is basically a wrapper for number_format
 *              with additional processing to retain the original
 *              digits after the decimal point (if any)
 *
 * Input:       string: number to be formatted
 *              decimals: [optional] number of decimal places
 *              dec_point: [optional] decimal point character
 *              thousands_sep: [optional] thousands seperator
 *
 * Examples:    {$number|commify}    12345.288 => 12,345.288
 *              {$number|commify:2}    12345.288 => 12,345.29
 *
 * Install:     Drop into the plugin directory as modifier.commify.php.
 *
 * Author:      James Brown <james [at] hmpg [dot] net>
 * -------------------------------------------------------------
 */
 
function smarty_modifier_commify($string, $decimals=-1, $dec_point='.', $thousands_sep=',')
{
    if ($decimals == -1)
    {
        if (preg_match('/\.\d+/', $string))
            return number_format($string) . preg_replace('/.*(\.\d+).*/', '$1', $string);
        else
            return number_format($string);
    }
    else
        return number_format($string, $decimals, $dec_point, $thousands_sep);
}
?>