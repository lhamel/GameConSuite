<?php
// initialize constants
if (!isset($constants)) $constants = array();

require_once dirname(__FILE__).'/../../db/db.php';

if (isset($_SESSION['cache']['constants']['events'])) {
	$constants['events'] = $_SESSION['cache']['constants']['events'];
} else {
	$constants['events'] = array();

	$blank = array(''=>'');
	
	// event types
	$eventTypes = new ADOdb_Active_Record('ucon_event_type');
  	$array = $eventTypes->Find('(not s_abbr="VG") order by s_type');
  	$constants['events']['event_types'] = array();
  	foreach ($array as $entry) {
	    $constants['events']['event_types'][$entry->id_event_type] = $entry->s_type;
  	}
  	$constants['events']['eventTypesWithBlank'] = $blank + $constants['events']['event_types'];

  	// rooms
  	$rooms = new ADOdb_Active_Record('ucon_room');
  	$array = $rooms->Find('');
  	$constants['events']['rooms'] = array();
  	foreach ($array as $entry) {
	    $constants['events']['rooms'][$entry->id_room] = $entry->s_room;
  	}

  	// room selection list contains only rooms at the current venue
  	// TODO redo for selected venue
  	$clause = $config['ucon']['venueId'] ? 'id_venue='.$config['ucon']['venueId'] : null;
  	$array = $rooms->Find($clause);
  	$constants['events']['roomsWithBlank'] = $blank + array();
  	foreach ($array as $entry) {
	    $constants['events']['roomsWithBlank'][$entry->id_room] = $entry->s_room;
  	}

  	// table types
  	$constants['events']['table_types'] = array(
	    '' => 'Any', 
	    'Square 6X6' => 'Square 6x6', 
	    'Rectangle 3x6' => 'Rectangle 3x6', 
	    '5 ft. Round' => "Round 5'"
  	);
    $constants['events']['tableTypesExtended'] = $constants['events']['table_types'] 
      + array(
        0 => 'Any', 
        1 => 'Square 6x6', 
        2 => 'Rectangle 3x6', 
        3 => "Round 5'"
      );

  	// age types
  	$constants['events']['ages'] = array(
	    '' => 'None', 
	    13 => 'Kid Friendly', 
	    18 => 'Mature Content',
  	);

  	$constants['events']['days'] = array(
	    'FRI' => 'Friday',
	    'SAT' => 'Saturday',
	    'SUN' => 'Sunday'
  	);
  	$constants['events']['daysWithBlank'] = $blank + $constants['events']['days'];


  	// all times available
  	for ($i = 7; $i <= 11; ++ $i)
	    $times[$i] = $i."a";
  	$times[12] = "12p";
  	for ($i = 13; $i <= 23; ++ $i)
	    $times[$i] = ($i -12)."p";
  	$times[24] = '12a';
  	$times[25] = '1a';
        $times[26] = '2a';
        $times[27] = '3a';
        $times[28] = '4a';
  	$constants['events']['times'] = $times;
  	$constants['events']['timesWithBlank'] = $blank + $constants['events']['times'];

	// formatted times
  	for ($i = 7; $i <= 11; ++ $i)
	    $format_times[$i] = $i.":00 AM";
  	$format_times[12] = "12:00 PM";
  	for ($i = 13; $i <= 23; ++ $i)
	    $format_times[$i] = ($i -12).":00 PM";
  	$format_times[24] = '12:00 AM';
  	$format_times[25] = '1:00 AM';
  	$constants['events']['format_times'] = $format_times;
  
  	// a list of game slots
  	$constants['events']['slots'] = array(
	    0=>'Anytime',
	    10=>'Friday 10am **',
	    12=>'Friday 12pm',
	    15=>'Friday 3pm **',
	    17=>'Friday 5pm',
	    19=>'Friday 7pm',
	    20=>'Friday 8pm **',

	    (24+9)=>'Saturday 9 am',
	    (24+10)=>'Saturday 10 am **',
	    (24+12)=>'Saturday 12 pm',
	    (24+15)=>'Saturday 3 pm **',
	    (24+17)=>'Saturday 5 pm',
	    (24+20)=>'Saturday 8 pm **',

	    (48+10)=>'Sunday 10 am **',
	    (48+12)=>'Sunday 12 pm',
	    (48+15)=>'Sunday 3 pm **',
  	);
  
  	$constants['events']['experience']
	    = array('1' => '1 - none', 
            	'2' => '2',
            	'3' => '3', 
            	'4' => '4',
            	'5' => '5 - lots'
  	);

  	$constants['events']['complexity']
    	= array('A' => 'A - easy',
            	'B' => 'B',
            	'C' => 'C',
            	'D' => 'D',
            	'E' => 'E - hard'
  	);

  	$constants['events']['search_unscheduled'] 
	    = array('exclude' => 'Exclude Unscheduled',
            	'include' => 'Include All',
            	'only' => 'Unscheduled Only'
  	);

        $_SESSION['cache']['constants']['events'] = $constants['events'];
}
