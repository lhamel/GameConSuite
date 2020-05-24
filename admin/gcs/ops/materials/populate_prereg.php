<?php
define('TICKET_CODE', '02');

require_once('../../../../inc/inc.php');
require_once('../../../../inc/db/db.php');

$year = (isset($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : $config['gcs']['year'];

define('REGISTER', 'PREREG-'.$year);
define('MAX_RAND', 2147483647);

// seed with microseconds
function make_seed()
{
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}


// delete old transaction where registerId=PREREG
$sql = <<< EOD
  select id_transaction 
  from ucon_transaction 
  where id_register=?
EOD;
  $xacts = $db->getAll($sql, array(REGISTER));
  if ($xacts === false) { header("HTTP/1.0 400 Bad Request"); echo "SQL Error (1): " . $db->ErrorMsg(); exit; }
  if (count($xacts)>0) {
    $transactionId = $xacts[0]['id_transaction'];
    $suc = $db->Execute('delete from ucon_transaction_item where id_transaction=?', array($transactionId));
    if ($suc === false) { header("HTTP/1.0 400 Bad Request"); echo "SQL Error (2): " . $db->ErrorMsg(); exit; }
    $suc = $db->Execute('delete from ucon_transaction where id_transaction=?', array($transactionId));
    if ($suc === false) { header("HTTP/1.0 400 Bad Request"); echo "SQL Error (3): " . $db->ErrorMsg(); exit; }
    if (count($xacts)>1) {
      echo("multiple transactions found, removed $transactionId");
      exit;
    }
  }

  // create a transaction ID with registerId=PREREG
  
  // generate a GUID for transaction id
  mt_srand(make_seed());
  $transactionId = mt_rand(0, MAX_RAND);

  // if there is at least one item, start a transaction
  $sql = "insert into ucon_transaction set id_transaction=?, id_register=?";
  $suc = $db->Execute($sql, array($transactionId, REGISTER));
  if ($suc === false) { header("HTTP/1.0 400 Bad Request"); echo "SQL Error (4): " . $db->ErrorMsg(); exit; }

  // copy the aggregate prereg sales into this transaction
$sql = <<< EOD
insert into ucon_transaction_item
select ? as id_transaction, barcode, 'prereg' as special, sum(i_quantity) as quantity, 0 as price
from ucon_order as O, ucon_item as I
where O.id_convention=?
and O.s_type='Ticket' and I.itemtype='Ticket'
    and O.s_subtype=I.subtype
    group by barcode, s_subtype;
EOD;
  $suc = $db->Execute($sql, array($transactionId, $year));
  if ($suc === false) { header("HTTP/1.0 400 Bad Request"); echo "SQL Error (5): " . $db->ErrorMsg(); exit; }

?>
Done.
