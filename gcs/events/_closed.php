<?php
    $year = $config['gcs']['year'];
    $depth = $config['page']['depth'];

    $submissionContent = '';
    if ($config['allow']['submit_events']) {
        $submissionContent = '<p>Event Submission is open!  <a href="../gm/submit.php">Click here to submit your events (login required).</a></p>';
    }

    $content = <<< EOD
        <h1>{$config['gcs']['name']} {$year} Events</h1>
        <p>Pre-registration for {$year} is not yet available.  We will announce on
        the email list when pre-registration is open!</p>

        {$submissionContent}
EOD;
    // render the page
    $smarty->assign('config', $config);
    $smarty->assign('constants', isset($constants) ? $constants : array());
    $smarty->assign('content', $content);
    $smarty->display('base.tpl');

