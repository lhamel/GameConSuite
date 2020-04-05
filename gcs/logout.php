<?php
/**
 *  Respond to a request to create an account
 */
require_once __DIR__.'/../inc/inc.php';
require_once __DIR__.'/../inc/auth.php'; // TODO remove after moving to inc.php

$year = $config['gcs']['year'];

// if the user is currently logged in, this is an error
if ($auth->isLogged()) {
    $s = $auth->logout($auth->getSessionHash());
}

redirect('login.php');


