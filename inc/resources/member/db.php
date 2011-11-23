<?php
require_once dirname(__FILE__).'/../../inc.php';


function validateMember($member) {
  $errors = array();
  if (!$member['s_fname']) $errors['s_fname'] = 'Please indicate your first name';
  if (!$member['s_lname']) $errors['s_lname'] = 'Please indicate your last name';
  if (!$member['s_email']) $errors['s_email'] = 'Please indicate your email';

  if (!$member['s_international']) {
    if (!$member['s_addr1']) $errors['s_addr1'] = 'Please indicate your address';
    if (!$member['s_city']) $errors['s_city'] = 'Please indicate your city';
    if (!$member['s_state']) $errors['s_state'] = 'Please indicate your state';
    if (!$member['s_zip']) $errors['s_zip'] = 'Please indicate your zip';
  }

  // TODO validate format for email, phone, zip, state
  return $errors;
}

function saveMember($member) {
    require_once INC_PATH.'db/db.php';

    $dbMember = new ADOdb_Active_Record('ucon_member');

    // save a new member
    if (isset($member['id_member'])
          && is_numeric($member['id_member']) 
          && $member['id_member']>=0) {

        $dbMember->id_member = $member['id_member'];

    	  $ok = $dbMember->load('id_member=?', $member['id_member']);
        if (!$ok) die("sql error: " . $dbMember->ErrorMsg());
    } else {
    	$dbMember->d_created = null;
      $dbMember->b_post = 0;
      $dbMember->b_volunteer = 0;
      $dbMember->b_email = 0;
    }
    
    
    $fieldsToCopy = array('s_lname', 's_fname', 
        's_addr1', 's_addr2', 's_city', 's_state', 's_zip',
        's_international',
        's_phone', 's_email',
        //'b_volunteer', 'b_email', 'b_post'
        );
    
    foreach ($fieldsToCopy as $field) {
      if (isset($member[$field])) {
        $dbMember->$field = $member[$field];
      }
    }

    $ok = $dbMember->Save();
    if (!$ok) die("sql error: " . $dbMember->ErrorMsg());

    //echo "member: ".$dbMember->id_member.'<br/>';
    return $dbMember->id_member;
}
