<?php
include_once ('../../../inc/inc.php');
$year = $config['gcs']['year'];
include_once (INC_PATH.'db/db.php');
include INC_PATH.'resources/event/constants.php';

$items = $_REQUEST['item'];
if (count($items) == 0) {
  exit;
}

$records = array();
foreach ($items as $key => $item) {
  $records[] = "($item, $key)";
}
$recordsValue = implode(',', $records);

$sql = <<< EOD
insert into ucon_prereg_items (id_prereg_item, display_order)
values $recordsValue
ON DUPLICATE KEY update display_order=VALUES(display_order);
EOD;

echo '<pre>'.print_r($sql,1).'</pre>';

$ok = $db->Execute($sql);
if (!$ok) { echo "Sql Error: " . $db->ErrorMsg(); exit; }

