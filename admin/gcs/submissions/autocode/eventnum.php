<?php

require_once('../../../../inc/db/db.php');

require_once(dirname(__FILE__).'/../../../../config/config.php');
$year = $config['gcs']['year'];

// find all items already in the db to check before we add them
$sql = "update ucon_event set s_number=id_event MOD 10000 where id_convention=? and (isnull(s_number) or s_number='')";
$success = $db->Execute($sql, array($year));
if ($success === false) { echo "SQL Error: ".$db->ErrorMsg(); exit; }

echo "Updated!  Go Back!";
