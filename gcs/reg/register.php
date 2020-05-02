<?php
require_once '../../inc/inc.php';
include_once INC_PATH.'auth.php';

$location = 'gcs/reg/register.php';
$year = $config['gcs']['year'];
require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';

// if you cannot view events or you cannot buy events...
if (!$config['allow']['view_events'] || !$config['allow']['buy_events']) {
    $content = '';
    $message = $config['allow']['message'];
    if ($message) $content .= "<p style=\"margin-top:6px;padding-left:2px;background:navy;color:#fff;font-weight:bold;font-size:14pt;\">$message</p>";
    $content .= "<h1>Register for U-Con!</h1>\n";
    if ($config['allow']['view_events'] && $config['allow']['see_location']) {
        $dates = $config['gcs']['dates']['all'];
        $content .= "<p>Pre-registration for {$year} is closed.  You may register onsite $dates.  See you soon!</p>";
    } else {
        $content .= "<p>Pre-registration for {$year} is not yet available.  We will announce on \n"
                 ."the email list when pre-registration is open!</p>\n";
    }
    $depth = $config['page']['depth'];
    $content .= <<< EOD
        <p style="text-align: center;">
        <img src="{$depth}/images/pic2003/crazylarpers.jpg" style="border: solid 1px;" alt="" />
        </p>
EOD;
    // render the page
    include '../events/_tabs.php';
    $smarty->assign('config', $config);
    //$smarty->assign('constants', $constants);
    $smarty->assign('content', $content);
    $smarty->display('base.tpl');
    exit;
}

// from this point all users must be logged in
if (!$auth->isLogged()) {
  header('HTTP/1.0 403 Forbidden');
  redirect('../login.php');
  exit();
}


//require_once INC_PATH.'resources/event/constants.php';
//require_once INC_PATH.'resources/cart/constants.php';

require_once INC_PATH.'db/db.php';


$members = $associates->listAssociates($uid);
foreach ($members as $id => $v) {
  // get a count of events (approved and unapproved) for each member
  $sql = "select concat(count(id_event), '/', sum(b_approval)) as numevents "
       . " from ucon_event where id_gm=? and id_convention=?";
  $result = $db->getAll($sql, array($id, $year));
  if (is_array($result)) {
    $members[$id] += $result[0];
  } else {
    $members[$id] += array('numevents'=>"0/0");
  }

  $first = $members[$id]['s_fname'];
  $last = $members[$id]['s_lname'];
  $full = $first . ($first && $last ? ' ' : '') . $last;
  $onclick = "$('input#badgeName').val('".$full."');$('#selectMemberOkBtn').button('enable');";
  $members[$id]['name'] = $full;
  $members[$id]['radio'] = '<input type="radio" name="id_member" value="'.$id.'" onClick="'.$onclick.'">';
}
$smarty->assign('members', $members);

$badgeNameForm = <<< EOD
<p style="text-align:left">
      <label for="badgeName">Name on Badge</label>
      <input type="text" name="badgeName" id="badgeName" value="" class="text ui-widget-content ui-corner-all">
</p>
EOD;


//$actions = array('javascriptFn'=>'createBadge');
$actions = array('addItem'=>'_add.php?&action=addItem',
                 'useItemDlg'=>1);
$smarty->assign('additionalFormContent', $badgeNameForm);

if ($_REQUEST['action']=='addBadge') {
  include_once dirname(__FILE__).'/_process.php';
//echo "redirecting..."; exit;
  redirect($config['page']['depth']."gcs/reg/cart.php");
  exit;
}

$sql = "select * from ucon_prereg_items where ((itemtype='Badge')) and is_public=1 order by display_order";
include_once (INC_PATH.'db/db.php');
$list = $db->getAll($sql, array());
if (!is_array($list)) {
  echo "Sql Error: ".$db->ErrorMsg(); exit;
}

$smarty->assign('items', $list);
$smarty->assign('actions', $actions);
if (isset($error)) {
  $smarty->assign('error', $error);
}

$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$content = <<< EOD
<h1>Select Badge</h1>

<p>Select a badge to add to your cart. You can add additional badges
        by returning to this page.</p>

<script type="text/javascript">
  var createBadge;

  $(function() {
    var dialog, form,
 
      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      name = $( "#badgeName" ),
      allFields = $( [] ).add( name ),
      tips = $( ".validateTips" );
 
    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
 
    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }
 
    function addUser() {
      var valid = true;
      allFields.removeClass( "ui-state-error" );
 
      valid = valid && checkLength( name, "username", 3, 50);
 
      valid = valid && checkRegexp( name, /^[a-z]([0-9a-z_\s])+$/i, "Username may consist of a-z, 0-9, underscores, spaces and must begin with a letter." );
 
      if ( valid ) {
        console.log('submitting');
        $('#badgeForm').submit();
        console.log('submitted');
        //dialog.dialog( "close" );
      }
      return valid;
    }
 
    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "Add to cart": addUser,
        Cancel: function() {
          form[0].reset();
          allFields.removeClass( "ui-state-error" );
          dialog.dialog( "close" );
        }
      },
      close: function() {
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      //event.preventDefault();
      //addUser();
    });
 
    createBadge = function(type) {
      console.log('creating badge ' + type);
      $('#badgeType').val(type);
      dialog.dialog( "open" );
    }
  });

</script>

<div id="dialog-form" title="Create a badge">
  <p class="validateTips">Please enter the name to appear on the badge.</p>
 
  <form id="badgeForm" method="GET" action="{$config['page']['basename']}">
    <fieldset>
      <input type="hidden" name="action" value="addBadge" />
      <input id="badgeType" type="hidden" name="badgeType" />
      <label for="badgeName">Name</label>
      <input type="text" name="badgeName" id="badgeName" value="" class="text ui-widget-content ui-corner-all">
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>


EOD;
$content .= $smarty->fetch('gcs/reg/additional.tpl');

include '../events/_tabs.php';

// render the page
$smarty->assign('title', 'Register - U-Con Gaming Convention, Ann Arbor Michigan');
$smarty->assign('content', $content);
$smarty->display('base.tpl');

