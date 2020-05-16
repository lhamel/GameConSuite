<?php
    $year = $config['gcs']['year'];
    $depth = $config['page']['depth'];
    $content = <<< EOD
        <h1>{$config['gcs']['name']} {$year} Events</h1>
        <p>Pre-registration for {$year} is not yet available.  We will announce on
        the email list when pre-registration is open!</p>

        <p style="text-align: center;">
        <img src="{$depth}/images/pic2003/planes.jpg" style="border: solid 1px;" alt="" />
        </p>
EOD;
    // render the page
    $smarty->assign('config', $config);
    $smarty->assign('constants', isset($constants) ? $constants : array());
    $smarty->assign('content', $content);
    $smarty->display('base.tpl');

