<?php
define('DEPTH','');
require_once 'inc/inc.php';
$year = $config['gcs']['year'];
$location = 'security.php';

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

if (isset($_GET['cookiesdisabled']))
        $special = '<p><font style="color: maroon">We are unabled to save your
                        information because cookies have been disabled.</font></p>';
else
        $special = '';


ob_start();
?>

<h1>Security Information</h1>

<?php echo $special; ?>

<p>This website uses session cookies in order to process registration and ticket
orders.  Session cookies are small pieces of information stored on your
computer during the process of preparing your order.  They are automatically
removed when your web browser is closed, and thus cannot be used to track
users between sessions.  If your browser is configured not to accept session
cookies, you should still be able to browse the site, however you will be
unable to register or purchase tickets.</p>
<?php
$content = ob_get_contents();
ob_clean();


$smarty->assign('config', $config);
$smarty->assign('content', $content);
$smarty->display('base.tpl');

