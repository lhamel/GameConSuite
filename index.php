<?php
require_once 'inc/inc.php';
$year = YEAR;
$location = 'index.php';

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'menu.php';

ob_start();
?>
<div style="float: right; width:270px; margin-left: 5px; margin-bottom: 5px;">
<img src="images/thecube.jpg" style=" border: solid 0px;" alt="" /><br/>
<p>Follow us on twitter <a href="http://twitter.com/ucongames/">@ucongames</a> 
or on <a href="http://www.facebook.com/group.php?gid=2210769945">facebook</a></p>


</div>


<?php
$content = ob_get_contents();
ob_clean();


$smarty->assign('config', $config);
$smarty->assign('content', $content);
$smarty->display('layout/base.tpl');

