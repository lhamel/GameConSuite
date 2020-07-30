<?php
if (!isset($smarty)) {
  require_once INC_PATH.'smarty.php';
}

if (!$config['allow']['view_events']) {
  return;
}

$tabs = array(
  array('link'=>'gcs/events/index.php', 'label'=>'Find Tickets'),
  array('link'=>'gcs/reg/additional.php', 'label'=>'Generics, Shirts, &amp; Misc'),
  array('link'=>'gcs/reg/register.php', 'label'=>'Badges'),
//  array('link'=>'gcs/reg/cart.php', 'label'=>'<img src="'.BASE_PATH.'images/gcs/reg/cart-16.png" style="vertical-align:text-bottom;"/> Cart'),
);
$smarty->assign('tabs', $tabs);
