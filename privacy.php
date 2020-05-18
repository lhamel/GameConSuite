<?php
define('DEPTH','');
require_once 'inc/inc.php';
$year = $config['gcs']['year'];
$location = 'privacy.php';

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

ob_start();
?>
<h1>Privacy Policy</h1>

<p>This website keeps record of names and addresses of those who have registered to attend this convention.  We do not store credit card or PayPal information, nor does this site ask for such information. Records of all online payments are kept with PayPal.  We will not sell or trade your personal information to third parties.</p>

<a id="contact"><b>Questions?</b></a>

<p>Should you have other questions or concerns about our privacy policy, please contact us via main website.</p>

<?php
$content = ob_get_contents();
ob_clean();


$smarty->assign('config', $config);
$smarty->assign('content', $content);
$smarty->display('base.tpl');


