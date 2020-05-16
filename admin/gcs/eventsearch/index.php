<?php
include '../../../inc/inc.php';
$location = 'admin/gcs/eventsearch/index.php';
$title = $config['gcs']['admintitle']." - Event Search";
$year = @is_numeric($_REQUEST['year']) ? $_REQUEST['year'] : $config['gcs']['year'];
include_once INC_PATH.'resources/event/constants.php';
require_once INC_PATH.'db/db.php';


if (@$_REQUEST['search']) {
  // if this is a barcode, try to go straight to the event
  $barcodeSql = "select subtype from ucon_item where itemtype=\"Ticket\" and barcode=?";
  $barcodeMatches = $db->getAll($barcodeSql, array($_REQUEST['search']));
  if (!is_array($barcodeMatches)) { echo "SQL ERROR: " . $db->ErrorMsg(); exit; }
  if (count($barcodeMatches)>0) {
    // the item's subtype is the event id
    redirect('../event/index.php?id_event='.$barcodeMatches[0]['subtype']);
    exit;
  }

  // determine search criteria from search string
  $searchTerms = explode(' ', $_REQUEST['search']);
  $searchFields = array('M.s_lname', 'M.s_fname', 'E.s_title', 'E.s_game', 'E.s_desc', 'E.s_number', 'T.tag');
	
  $whereClauses = array();
	foreach ($searchTerms as $searchTerm) {
    $orClauses = array();
		foreach ($searchFields as $fieldName) {
	 	  $orClauses[] = '('.$fieldName.' LIKE "%'.$searchTerm.'%")';
	  }
	  $whereClauses[] = '('.implode(' OR ', $orClauses).')';
	}
	$whereClause = implode(' AND ', $whereClauses);

	// build statistics on the results
  $countSql = <<< EOD
    select count(E.id_event) as count
    from ucon_member as M, ucon_event as E
      left join ucon_event_tag as ET on E.id_event=ET.id_event
      left join ucon_tag as T on (ET.id_tag=T.id_tag)
    where
      E.id_gm=M.id_member
      and ($whereClause)
      and E.id_convention=$year
EOD;
  //echo '<pre>'.print_r($countSql, 1).'</pre>';
	$all = $db->getAll($countSql);
	if (!is_array($all)) die('SQL Error (retrieving search stats): '.$db->ErrorMsg());
	if (count($all) > 1) die('SQL Error: more results than expected');
	$resultCount = count($all) == 0 ? 0 : $all[0]['count'];
	// TODO quit early if there are no results

	// if there are results, do search and display results
	if ($resultCount > 0) {
    $entriesPerPage = @is_numeric($_REQUEST['entriesPerPage']) ? $_REQUEST['entriesPerPage'] : 25;
    $page = @is_numeric($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    
		$sql = <<< EOD
		  select 
             concat('<a href="../member/index.php?id_member=',M.id_member,'">', s_fname, ' ', s_lname, '</a>') as GM,
             E.id_event as `Id Number`,
             E.s_number as `Event Number`,
		         if(s_game!=s_title,concat(s_game, ': ', s_title), s_title) as Title,
		         e_day as Day,
		         i_time as Time,
		         '' as Status,
		         b_approval,
		         b_edited,
             id_room,
		         i_time+i_length as Endtime
		  from ucon_member as M, ucon_event as E
                    left join ucon_event_tag as ET on E.id_event=ET.id_event
                    left join ucon_tag as T on (ET.id_tag=T.id_tag)
		  where
	        E.id_gm=M.id_member
	        and ($whereClause)
	        and E.id_convention=$year
	      group by E.id_event
		  order by s_lname, s_fname, M.id_member         
EOD;
    //echo '<pre>'.print_r($sql, 1).'</pre>';
	  $rs = $db->PageExecute($sql, $entriesPerPage, $page);
    if (!$rs) die ("SQL Error (retrieving member results): ".$db->ErrorMsg());

    //  data transform (add view link here instead of concat in query)
    $results = array();
    foreach($rs as $k => $row) {
    	$resultRow = array();
      foreach ($row as $j => $entry) {
        $resultRow[$j] = $entry;
      }

      // determine the status in the pipeline, show the icon with link
      if ($resultRow['Day'] == '' || $resultRow['Time'] == '') {
        $resultRow['Status'] = '<img src="'.$config['page']['depth'].'images/gcs/event/schedule.gif" height="12">';
      } else if ($resultRow['Event Number'] == '') {
        $resultRow['Status'] = '<img src="'.$config['page']['depth'].'images/gcs/event/number.jpg" height="12">';
      } else if ($resultRow['b_approval'] == 0) {
      	$resultRow['Status'] = '<img src="'.$config['page']['depth'].'images/gcs/event/approval.gif" height="12">';
      } else if (!$resultRow['id_room']) {
        $resultRow['Status'] = '<img src="'.$config['page']['depth'].'images/gcs/event/door2.gif" height="12">';
      } else if ($resultRow['b_edited'] == 0) {
        $resultRow['Status'] = '<img src="'.$config['page']['depth'].'images/gcs/event/copyedit.gif" height="12">';
      } else {
        $resultRow['Status'] = '<img src="'.$config['page']['depth'].'images/gcs/event/icon_check.png" height="12">';
      }

      $resultRow['Event Number'] = '<a href="../event/index.php?id_event='. $resultRow['Id Number'] .'">'.$resultRow['Event Number'];
      $resultRow['Title'] = '<a href="../event/index.php?id_event='. $resultRow['Id Number'] .'">'.$resultRow['Title'];

      $resultRow['Day'] = @$constants['events']['days'][ $resultRow['Day'] ];
      $resultRow['Time'] = @$constants['events']['times'][ $resultRow['Time'] ]
                           .'-'.@$constants['events']['times'][ $resultRow['Endtime'] ];

      unset($resultRow['Endtime']);
      unset($resultRow['b_edited']);
      unset($resultRow['b_approval']);
      unset($resultRow['id_room']);
      $results[] = $resultRow;
    }

    // collect meta-information about the query
    $columnCount = 7; // show the first 6 columns
    $columnNames = array();
    for ($i=0; $i<$columnCount; ++$i) {
    	$field = $rs->FetchField($i);
      $columnNames[] = $field->name;
    }

    $meta = array(
      'count'=>$resultCount,
      'totalPages'=>ceil($resultCount/$entriesPerPage),
      'page'=>$page,
      'pageUrl'=>$config['page']['basename'].'?search='.urlencode($_REQUEST['search']),
      'entriesPerPage'=>$entriesPerPage,
      'columnCount'=>$columnCount,
      'columnNames'=>$columnNames,
    );

    //echo "<pre>results:\n".print_r($results,1).'</pre>';
    //echo "<pre>meta:\n".print_r($meta,1).'</pre>';
	}
	
}


include INC_PATH.'smarty.php';
include INC_PATH.'layout/adminmenu.php';
include '_tabs.php';


//$smarty->assign('actions', $actions);
$smarty->assign('config', $config);
$smarty->assign('constants', $constants);
$smarty->assign('title', $title);


// render the page
$smarty->assign('REQUEST', $_REQUEST);
$content = $smarty->fetch('gcs/admin/events/search-form.tpl');
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

