<?php
include_once ('../../inc/inc.php');
$location = 'gcs/events/expcomp.php';
$title = 'U-Con Gaming Convention, Ann Arbor Michigan';
$year = $config['gcs']['year'];

require_once INC_PATH.'smarty.php';
require_once INC_PATH.'layout/menu.php';


$titlestyle = 'style="margin-bottom: 0px; font-weight: bolder;"';
$liststyle = 'style="list-style: none; margin-top: 0px;"';

$boldstyle = 'style="font-weight: bolder;"';
$leftstyle = 'style="margin-left: 10px; font-style: italic;"';

$content = <<< EOD

<h2 style="margin: 0px;">Experience / Complexity</h2>

<div class="mainpain" style="text-align: left;">

<p style="margin: 0px;">Experience and complexity ratings were set by the GM at the time of event 
submission.  We are trying to standardize them to these levels:</p>


<div style="float: right; width: 50%; margin-left: 5px;">
<p $titlestyle>Complexity:</p>
<dl>
	<dt $boldstyle>Simple</dt>
	<dd $leftstyle>Games involve little or no strategic thinking and have a high luck factor</dd>
</dl>
<dl>
	<dt $boldstyle>Normal</dt>
	<dd $leftstyle>Games involve a little strategy and some luck</dd>
</dl>
<dl>
	<dt $boldstyle>Complex</dt>
	<dd $leftstyle>Significant strategic thinking needed and the effects of luck are minimized</dd>
</dl>
</div>

<p $titlestyle>Experience:</p>
<dl>
	<dt $boldstyle>No XP</dt>
	<dd $leftstyle>No experience needed</dd>
</dl>
<dl>
	<dt $boldstyle>Some XP</dt>
	<dd $leftstyle>Prior experience with this game or similar games recommended</dd>
</dl>
<dl>
	<dt $boldstyle>Lots XP</dt>
	<dd $leftstyle>Have played this specific game a few times</dd>
</dl>

</div>

EOD;


$smarty->assign('config', $config);
$smarty->assign('content', $content);
$smarty->display('base_reduced.tpl');
