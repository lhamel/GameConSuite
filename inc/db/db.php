<?php
require_once(__DIR__.'/../../config/config.php');
require_once(__DIR__.'/../../vendor/adodb/adodb-php/adodb.inc.php');
require_once(__DIR__.'/../../vendor/adodb/adodb-php/adodb-active-record.inc.php');

$db = NewADOConnection($config['db']['main_db_conn']);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

ADOdb_Active_Record::SetDatabaseAdapter($db);


$queries['GET_EVENT'] = <<< EOD
  SELECT E.*, 
    TRIM(CONCAT(M.s_fname, " ", M.s_lname)) as gamemaster, 
    E.i_time+E.i_length as endtime,
    T.s_abbr as track
  from ucon_event as E, ucon_member as M, ucon_event_type as T
  where E.id_gm=M.id_member
    and E.id_event_type=T.id_event_type
    and E.id_event=?
EOD;

$queries['GET_EVENT_BARCODE'] = <<< EOD
  select barcode
  from ucon_item
  where itemtype="Ticket" and subtype=?
EOD;

$queries['GET_EVENT_TICKETS'] = <<< EOD
  select M.*, O.*
  from ucon_member as M, ucon_order as O
  where M.id_member = O.id_member
    and O.s_subtype=?
EOD;

$queries['GET_MEMBER'] = <<< EOD
  select *
  from ucon_member
  where id_member=?
EOD;

$queries['GET_GM_EVENTS'] = <<< EOD
  select E.*,
    E.i_time+E.i_length as endtime
  from ucon_event as E
  where E.id_gm=?
    and id_convention=?
  order by e_day, i_time, s_game, s_title
EOD;

$queries['GET_MEMBER_ORDER'] = <<< EOD
  select *
  from ucon_order
  where id_member=?
    and id_convention=?
EOD;

