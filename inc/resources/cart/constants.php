<?php
// initialize constants
if (!isset($constants)) $constants = array();

//if (isset($_SESSION['cache']['constants']['cart'])) {
//  $constants['cart'] = $_SESSION['cache']['constants']['cart'];
//} else {
  $constants['cart'] = array();

  // access as $constants['cart']['images'][$full][$has]
  // the first boolean represents the case of a full event
  // the second boolean represent if the user has the event
  $constants['cart']['buy'][false] = array();
  $constants['cart']['buy'][false][false] = DEPTH.'images/ticket_icon_add.png';
  $constants['cart']['buy'][false][true] = DEPTH.'images/ticket_icon_check.png';
  $constants['cart']['buy'][true] = array();
  $constants['cart']['buy'][true][false] = DEPTH.'images/ticket_icon_star.png';
  $constants['cart']['buy'][true][true] = DEPTH.'images/ticket_icon_check_star.png';

//  $_SESSION['cache']['constants']['cart'] = $constants['cart'];
//}
