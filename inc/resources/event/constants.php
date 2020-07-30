<?php
// initialize constants
if (!isset($constants)) $constants = array();

require_once dirname(__FILE__).'/../../db/db.php';

@session_start();
unset($_SESSION['cache']['constants']['events']);
if (isset($_SESSION['cache']['constants']['events'])) {
  $constants['events'] = $_SESSION['cache']['constants']['events'];
} else {
  $constants['events'] = array();

  $blank = array(''=>'');
  
  // event types
  $eventTypes = new ADOdb_Active_Record('ucon_event_type');
    $array = $eventTypes->Find('(not s_abbr="VG") and (not s_abbr="CG") order by s_type');
    $constants['events']['event_types'] = array();
    foreach ($array as $entry) {
      $constants['events']['event_types'][$entry->id_event_type] = $entry->s_type;
    }
    $constants['events']['eventTypesWithBlank'] = $blank + $constants['events']['event_types'];

    // rooms
    $rooms = new ADOdb_Active_Record('ucon_room');
    $array = $rooms->Find('id_venue='.$config['gcs']['venueId']);
    $constants['events']['rooms'] = array();
    foreach ($array as $entry) {
      $constants['events']['rooms'][$entry->id_room] = $entry->s_room;
    }

    // room selection list contains only rooms at the current venue
    $array = $rooms->Find('id_venue='.$config['gcs']['venueId']);
    $constants['events']['roomsWithBlank'] = $blank + array();
    foreach ($array as $entry) {
      $constants['events']['roomsWithBlank'][$entry->id_room] = $entry->s_room;
    }

    // TODO move table types to database configuration
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

  // TODO move ages to database configuration
   // age types
  $constants['events']['ages'] = array(
            0 => '',
            7 => 'Family Friendly (7+)',
            13 => 'General Audience (13+)',
            18 => 'Adults Only (18+)',
            19 => 'Mature Content (18+)',
        );
  $constants['events']['agesNoBlank'] = $constants['events']['ages'];
  unset($constants['events']['agesNoBlank'][0]);
        $constants['events']['agesInBook'] = array(
            0 => '',
            7 => '(Ages 7+)',
            13 => '(Ages 13+)',
            18 => '(Adults 18+)',
            19 => '(Mature 18+)',
        );


    $constants['events']['days'] = array(
      'FRI' => 'Friday',
      'SAT' => 'Saturday',
      'SUN' => 'Sunday'
    );
    $constants['events']['daysWithBlank'] = $blank + $constants['events']['days'];
    $constants['events']['daysAbbreviated'] = array(
        'FRI' => 'Fri',
        'SAT' => 'Sat',
        'SUN' => 'Sun'
    );
    $constants['events']['daysAbbreviatedWithBlank'] = $blank + $constants['events']['daysAbbreviated'];

    // all times available
    for ($i = 7; $i <= 11; ++ $i) {
      $times["$i"] = $i."a";
      //$times["$i.5"] = $i.":30a";
    }
    $times["12"] = "12p";
    //$times["12.5"] = "12:30p";
    for ($i = 13; $i <= 23; ++ $i) {
      $times["$i"] = ($i -12)."p";
      //$times["$i.5"] = ($i-12).":30p";
    }
    $times["24"] = '12a';
    $times["25"] = '1a';
    $times["26"] = '2a';
    $times["27"] = '3a';
    $times["28"] = '4a';
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
        // a list of game slots
        $mainSlots = array(
            0=>'Anytime',
            9=>'Friday 9am',
            14=>'Friday 2pm',
            20=>'Friday 8pm',

            (24+9)=>'Saturday 9am',
            (24+14)=>'Saturday 2pm',
            (24+20)=>'Saturday 8pm',

            (48+9)=>'Sunday 9am',
            (48+14)=>'Sunday 2pm',
        );

        $constants['events']['slots4'] = $mainSlots + array(
            (48+10)=>'Sunday 10am',
        );

        $constants['events']['slots5'] = $mainSlots + array(
            14=>'Friday 2pm',
            19=>'Friday 7pm',
            (24+13)=>'Saturday 1pm',
            (24+19)=>'Saturday 7pm',
            (48+13)=>'Sunday 1pm',
        );

        $constants['events']['slots6'] = $mainSlots + array(
            14=>'Friday 2pm',
            18=>'Friday 6pm',
            (24+13)=>'Saturday 1pm',
            (24+18)=>'Saturday 6pm',
            (48+12)=>'Sunday 12pm',
            (48+13)=>'Sunday 1pm',
        );

        $constants['events']['slots2'] = $mainSlots + array(
            11=>'Friday 11am',
            17=>'Friday 5pm',
            22=>'Friday 10pm',
            (24+11)=>'Saturday 11am',
            (24+16)=>'Saturday 4pm',
            (24+22)=>'Saturday 10pm',
            (48+11)=>'Sunday 11am',
            (48+16)=>'Sunday 4pm',
        );
 
        $constants['events']['slots3'] = $mainSlots + array(
            10=>'Friday 10am',
            15=>'Friday 3pm',
            21=>'Friday 9pm',
            (24+10)=>'Saturday 10am',
            (24+15)=>'Saturday 3pm',
            (24+21)=>'Saturday 9pm',
            (48+10)=>'Sunday 10am',
            (48+15)=>'Sunday 3pm',
        );
        $constants['events']['slots'] = 
          $constants['events']['slots2'] +
          $constants['events']['slots3'] +
          $constants['events']['slots4'] +
          $constants['events']['slots5'] +
          $constants['events']['slots6'];
        ksort($constants['events']['slots']);
  //ksort($constants['events']['slotsall']);
        ksort($constants['events']['slots2']);
        ksort($constants['events']['slots3']);
        ksort($constants['events']['slots4']);
        ksort($constants['events']['slots5']);
        ksort($constants['events']['slots6']);
//$constants['events']['slots'] = $constants['events']['slotsall'];

        
        $slotDurations = array();
        foreach(array(2,3,4,5,6) as $duration) {
          foreach($constants['events']['slots'.$duration] as $id => $value) {
            if(isset($slotDurations[$id]))
              array_push($slotDurations[$id],$duration);
            else
              $slotDurations[$id] = array($duration);
          }
        }
        $constants['events']['slotDurations'] = $slotDurations;

        $constants['events']['complexity']['select']
            = array('A' => 'Simple',
                    'C' => 'Average',
                    'E' => 'Complex');
        $constants['events']['experience']['select']
            = array('1' => 'No XP',
                    '3' => 'Some XP',
                    '5' => 'Lots XP');

    $constants['events']['experience']['display']
      = $constants['events']['experience']['select'] + array(
              '2' => 'Some XP',
              '4' => 'Lots XP',
    );

    $constants['events']['complexity']['display']
          = $constants['events']['complexity']['select'] + array(
              'B' => 'Average',
              'D' => 'Complex',
    );

    $constants['events']['search_unscheduled'] 
      = array('exclude' => 'Exclude Unscheduled',
              'include' => 'Include All',
              'only' => 'Unscheduled Only'
    );

        $_SESSION['cache']['constants']['events'] = $constants['events'];
}

function formatSingleEventTime($day, $time, $endtime = NULL) {
  global $constants;
  return $constants['events']['daysAbbreviatedWithBlank'][$day].' '
       . $constants['events']['timesWithBlank'][$time]
       . ($endtime ? '-'. $constants['events']['times'][$endtime] : '');
}


/**
 * @param events list of events to format
 * @param fieldname the fieldname under each event where new value should be stored
 */
function formatEventTimes($events, $fieldname) {
  global $constants;
  foreach ($events as $k => $v) {
    $day = $v['e_day'];
    $time = $v['i_time'];
    $endtime = NULL;
    if (is_numeric($v['i_time']) && is_numeric($v['i_length'])) {
      $endtime = $v['i_time']+$v['i_length'];
    }
    $events[$k][$fieldname] = formatSingleEventTime($day, $time, $endtime);
  }
  return $events;
}

function formatSingleEventTitle($system, $title) {
    if ($system == $title) {
      return $title;
    } else if ($title && $system) {
      return "$system: $title";
    } else if ($title) {
      return $title;
    } else {
      return $system;
    }
}


function formatEventTitles($events, $fieldname) {
  global $constants;
  foreach ($events as $k => $v) {
    $system = $v['s_game'];
    $title = $v['s_title'];
    $events[$k][$fieldname] = formatSingleEventTitle($system, $title);
  }
  return $events;
}

function formatSingleEventPlayers($event) {
  $min = $event['i_minplayers'];
  $max = $event['i_maxplayers'];
  $value = $max;
  if ($min != $max) {
    $value = "$min - $max";
  }
  return $value;
}

function formatEventPlayers($events, $fieldname) {
  global $constants;
  foreach ($events as $k => $v) {
    $min = $v['i_minplayers'];
    $max = $v['i_maxplayers'];
    $value = $max;
    if ($min != $max) {
      $value = "$min - $max";
    }
    $events[$k][$fieldname] = $value;
  }
  return $events;
}

function formatEventLocations($events, $fieldname) {
  global $constants;
  foreach ($events as $k => $v) {
    $room = $v['s_room'];
    $table = $v['s_table'];
    $events[$k][$fieldname] = "$room $table";
  }
  return $events;
}

// Replace the field of $events[$fieldname] with a linkified URL
function linkifyField($events, $fieldname, $baseUrl, $params) {
  foreach ($events as $k => $v) {
    $orig = $v[$fieldname];

    // organize parameters
    $query = array();
    foreach ($params as $p => $field) {
      if (isset($v[$field])) {
        $query[$p] = $v[$field];
      } else {
        $query[$p] = $field;
      }
    }

    $url = $baseUrl.'?'.http_build_query($query);
    $events[$k][$fieldname] = "<a href=\"$url\">$orig</a>";
  }
  return $events;
}


