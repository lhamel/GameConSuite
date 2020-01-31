<?php
if (!isset($location))
{
  die('please set $location');
}
if (!isset($smarty))
{
  die('please set $smarty');
}
if (!isset($year)) {
  die('please set $year');
}

$menu = array();

$menu['ad'] = '';//showImage(WWW_PATH.'include/ads.ini');
$menu['depth'] = DEPTH;
$menu['basename'] = basename($location);

if (defined('ADMIN')) {
$menu['items'] = array(
  array('label'=>'Admin', 'children'=>array(
    array('label'=>'Admin Home', 'link'=>'admin/index.php'),
    array('label'=>'Legacy DB', 'link'=>'admin/legacy.php'),
    array('label'=>'Statistics', 'link'=>'admin/db/'),
    array('label'=>'Change Password', 'link'=>'admin/db/password.php'),
  )),
  array('label'=>'Events', 'children'=>array(
    array('label'=>'Search', 'link'=>'admin/gcs/eventsearch/index.php'),
    array('label'=>'Submissions', 'link'=>'admin/gcs/submissions/unscheduled.php'),
    array('label'=>'Publishing', 'link'=>'admin/gcs/publish/unedited.php'),
    array('label'=>'Schedule', 'link'=>'admin/gcs/schedule/browse.php'),
    array('label'=>'Results', 'link'=>'admin/gcs/eventcheckin/bybarcode.php'),
  )),
  array('label'=>'Members', 'children'=>array(
      array('label'=>'Search', 'link'=>'admin/gcs/membersearch/index.php'),
      array('label'=>'Registration', 'link'=>'admin/gcs/registration/index.php'),
      array('label'=>'Duplicates', 'link'=>'admin/gcs/memberduplicates/index.php'),
      array('label'=>'Add Member', 'link'=>'admin/db/member/new.php'),
  )),
  array('label'=>'Operations', 'children'=>array(
      array('label'=>'Purchase Items', 'link'=>'admin/gcs/config/purchaseitems.php'),
      array('label'=>'Materials', 'link'=>'admin/gcs/ops/materials.php'),
      array('label'=>'Popular Tickets', 'link'=>'admin/gcs/ops/popular.php'),
  )),
  array('label'=>'Fates', 'children'=>array(
    array('label'=>'Fates', 'link'=>'admin/db/fates'),
    array('label'=>'Conventions', 'link'=>'admin/db/convention.php'), 
    array('label'=>'Schedule', 'link'=>'admin/db/fates/scheduling/SlotsByCategory.php'),
    array('label'=>'Room Schedule', 'link'=>'admin/db/fates/scheduling/SlotsByRoom.php'),
    array('label'=>'Time Slot', 'link'=>'admin/db/fates/scheduling/RoomsByTime.php'),
    array('label'=>'Event Location', 'link'=>'admin/db/fates/scheduling/roompicture.php'),
  )),
  array('label'=>'Records', 'children'=>array(
    array('label'=>'All Preregistration', 'link'=>'admin/db/member/prereg.php'),
    array('label'=>'Gamemasters', 'link'=>'admin/db/member/prereg.php?type=gm'), 
    array('label'=>'Preregistered', 'link'=>'admin/db/member/prereg.php?type=pre'),
    array('label'=>'Event Running', 'link'=>'admin/gcs/record/record.php'),
    array('label'=>'Checkin By Barcode', 'link'=>'admin/gcs/eventcheckin/bybarcode.php'),
  )),
);
} else {
$menu['items'] = array(
  array('label'=>'General Info', 'children'=>array(
    array('label'=>'U-Con Website', 'url'=>'https://www.ucon-gaming.org'),
    array('label'=>'Subscribe to Email', 'url'=>'https://www.ucon-gaming.org/email/?p=subscribe&id=1'),
  )),
  array('label'=>'Event Submission', 'children'=>array(
    array('label'=>'Submit Events', 'link'=>'gcs/gm/submit.php'),
  )),
  array('label'=>'Registration', 'children'=>array(
    array('label'=>'Find Events', 'link'=>'gcs/events/browse.php'),
    array('label'=>'Register Now', 'link'=>'gcs/reg/register.php'),
    array('label'=>'Additional Options', 'link'=>'gcs/reg/additional.php'),
)),


);

  require_once(WWW_PATH.'inc/auth.php');

  // display menu for user not logged in
  if (!$auth->isLogged()) {
    $menu['items'][] = 
      array('label'=>'User Account', 'children'=>array(
        array('label'=>'Login', 'link'=>'gcs/login.php'),
        array('label'=>'Create Account', 'link'=>'gcs/create.php'),
      ));
  } else {

    $loginMenu = array(
        array('label'=>'*My Registration', 'link'=>'gcs/reg.php'),
        array('label'=>'Change Password', 'link'=>'gcs/changepw.php'),
        array('label'=>'Logout', 'link'=>'gcs/logout.php'),
      );

    $menu['items'][] =
      array('label'=>'User Account', 'children'=>$loginMenu);
  }

}


// figure out if the link is selected
foreach ($menu['items'] as $catKey => $category) {
  foreach ($category['children'] as $itemKey => $item) {
    //die( "<pre>".print_r($item, 1)."</pre>" );
    if (isset($item['link']) && ($item['link'] == $location)) {
      $menu['items'][$catKey]['children'][$itemKey]['selected'] = ' class="selected"';
    }
  }
}

$smarty->assign('menu', $menu);
unset($menu);
