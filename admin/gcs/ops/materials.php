<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/ops/materials.php';
$title = 'Registration Materials';
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : $config['gcs']['year'];

ob_start();
?>
<h1>Materials</h1>
<p>These pages generate the materials needed for ops.  Add ?year=&lt;year&gt; to the end of any URL.</p>

<h2>Upon Event Schedule Completion</h2>
<ul>
  <li><a href="materials/gmworksheet.php">GM Worksheets</a>
  <li><a href="materials/tableSchedules.php">Table Schedules</a>
</ul>

<h2>Upon Registration Completion</h2>
<ul>
  <li><a href="materials/prereg_sheet.php">Prereg Sheet</a>
  <li><a href="materials/combined.php">4x6 Reg Cards</a>
</ul>

<h2>Downloads for Cash Register</h2>
<ul>
  <li><a href="materials/populate_items.php">Ensure all onsite purchase items are in the table.</a>
  <li><a href="materials/populate_prereg.php">Account for all prereg tickets in cash register</a>
  <!--<li><a href="materials/update_single.php?id_event=">Select and update a single event in cash register</a>-->
</ul>

<?php
$content = ob_get_contents();
ob_clean();

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/adminmenu.php';
include '_tabs.php';
$smarty->assign('config', $config);
$smarty->assign('content', $content);
$smarty->display('base.tpl');

