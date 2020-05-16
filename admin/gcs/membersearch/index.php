<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/membersearch/index.php';
$title = 'Search Members';
$year = $config['gcs']['year'];
require_once INC_PATH.'db/db.php';

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
if ($search) {
  // determine search criteria from search string
  $a = explode (' ', $search);
  if (sizeof($a)>1) {
    $firstname = $db->qStr("%$a[0]%");
    $lastname = $db->qStr("%$a[1]%");
    $whereClause = "s_lname LIKE $lastname AND s_fname LIKE $firstname";
  } else {
    $qStr = $db->qStr("%$search%");
    $whereClause = "s_lname LIKE $qStr OR s_fname LIKE $qStr OR s_email LIKE $qStr";
  }

	// build statistics on the results
  $countSql = <<< EOD
    select count(id_member) as count
    from ucon_member
    where
      $whereClause
EOD;
	$all = $db->getAll($countSql);
	if (!is_array($all)) die('SQL Error (retrieving search stats): '.$db->ErrorMsg());
	if (count($all) > 1) die('SQL Error: more results than expected');
	$resultCount = count($all) == 0 ? 0 : $all[0]['count'];
	// TODO quit early if there are no results

	// if there are results, do search and display results
	if ($resultCount > 0) {
    $entriesPerPage = isset($_REQUEST['entriesPerPage']) ? $_REQUEST['entriesPerPage'] : 25;
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    
		$sql = <<< EOD
		  select M.id_member as `Id Number`,
		         concat('<a href="../member/index.php?id_member=',M.id_member,'">', s_lname, '</a>') as Last,
		         concat('<a href="../member/index.php?id_member=',M.id_member,'">', s_fname, '</a>') as First,
		         s_city as City,
		         s_state as State,
             if(isnull(id_convention), 'n/a', max(id_convention)) as 'Last Attended',
		         group_concat(DISTINCT O.s_subtype 
		                      ORDER BY O.id_convention DESC, id_order DESC 
		                      SEPARATOR ',<br>') as Roles
		  from ucon_member as M
		    left join ucon_order as O on (O.id_member=M.id_member and O.s_type='Badge')
		  where
		    $whereClause
		  group by s_lname, s_fname, M.id_member
		  order by s_lname, s_fname, M.id_member         
EOD;
	  $rs = $db->PageExecute($sql, $entriesPerPage, $page);
    if (!$rs) die ("SQL Error (retrieving member results): ".$db->ErrorMsg());

    //  data transform (add view link here instead of concat in query)
    $results = array();
    foreach($rs as $k => $row) {
    	$resultRow = array();
      foreach ($row as $entry) {
      	$resultRow[] = $entry;
      }
      $results[] = $resultRow;
    }

    // collect meta-information about the query
    $columnCount = $rs->FieldCount();
    $columnNames = array();
    for ($i=0; $i<$columnCount; ++$i) {
    	$field = $rs->FetchField($i);
    	$columnNames[] = $field->name;
    }
    $meta = array(
      'count'=>$resultCount,
      'totalPages'=>ceil($resultCount/$entriesPerPage),
      'page'=>$page,
      'pageUrl'=>$config['page']['basename'].'?search='.$_REQUEST['search'],
      'entriesPerPage'=>$entriesPerPage,
      'columnCount'=>$rs->FieldCount(),
      'columnNames'=>$columnNames,
    );

    //echo "<pre>results:\n".print_r($results,1).'</pre>';
    //echo "<pre>meta:\n".print_r($meta,1).'</pre>';
	}
	
}


include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';
include '_tabs.php';


// $smarty->assign('actions', array());
$smarty->assign('config', $config);
// $smarty->assign('constants', $constants);
$smarty->assign('title', $title);


// render the page
$smarty->assign('REQUEST', $_REQUEST);
$content = $smarty->fetch('gcs/admin/member/search-form.tpl');
if (isset($_REQUEST['search'])) {
	if ($resultCount == 0) {
		$content .= '<p>No members found</p>';
	} else {
    $smarty->assign('results', $results);
    $smarty->assign('meta', $meta);
    $content .= $smarty->fetch('gcs/common/pager.tpl');
	}
}

$smarty->assign('content', $content);
$smarty->display('base.tpl');

