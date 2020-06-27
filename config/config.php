<?php
if (!isset($config)) $config = array();

// database configuration
$config['db']['main_db_conn'] = 'mysqli://root:@localhost/ucon_db';
$config['db']['auth_db_conn'] = "mysql:host=localhost;dbname=ucon_auth";
$config['db']['auth_db_user'] = "root";
$config['db']['auth_db_pass'] = "";




$config['gcs']['year'] = 2019;
$config['gcs']['venueId'] = 3; //Eagle Crest


$now = date("Y-m-d-H");// central time

$config['allow']['submit_events'] = true; // enables accepting GMs
$config['allow']['view_events'] = true; ($now > '2018-09-16-20');   // enables read access to events
$config['allow']['buy_events'] = false; ($now < '2018-11-01-00');    // enables use of the shopping cart
$config['allow']['see_location'] = false; ($now > '2018-11-01-00');  // enabled gms and all users to see location information, including VTT info from the GM for ticketed players
$config['allow']['live_data'] = false; // enables the use of cash register data to determine sold-out icon
$config['allow']['message'] = ''; //<span style="font-size:18pt">Pregistration Open!  More events to be added later.</span> ';



// $config['ucon']['baseUrl'] = "https://www.ucon-gaming.org/reg";

$config['email']['registration'] = 'reg2019@ucon-gaming.org';
// $config['email']['webmaster'] = 'webmaster@ucon-gaming.org';

// comment this out to disable the paypal address in the administrative confirmation email
$config['email']['paypal'] = 'sendpaypal@ucon-gaming.org';


$config['gcs']['generic_price'] = 2; // $2



$config['gcs']['header']['description'] = <<< EOD
Role Playing, Collectible Card Games, Board Games,
Miniatures, Train Games, Live Action RPGs, Video Games,
Exhibitors' Hall, Auction, Specialty Tracks, and much more!
EOD;

$config['gcs']['name'] = "GameConSuite";
$config['gcs']['sitetitle'] = $config['gcs']['name'].", Games for All";
$config['gcs']['admintitle'] = $config['gcs']['name']." Admin";
$config['gcs']['website'] = "http://www.ucon-gaming.org/";
$config['gcs']['location'] = "Ypsilanti, Michigan";
$config['gcs']['dates']['all'] = 'November 22-24, 2019';
$config['gcs']['dates']['friday'] = "Nov 22";
$config['gcs']['dates']['saturday'] = "Nov 23";
$config['gcs']['dates']['sunday'] = "Nov 24";

$config['gcs']['tagline'] = "Role Playing, Card Games, Board Games, Miniatures, LARPs, Specialty Tracks, Exhibitors' Hall, Auction, and much more!";



/* Meta information for the site, appears in the headers of every page */
$config['gcs']['meta']['defaultTitle'] = "U-Con Gaming Convention, Ann Arbor Michigan";
$config['gcs']['meta']['keywords'] = "ucon, ann arbor, gaming convention, games, role-playing, collectable card games, ccgs, rpgs, rpga, magic, magic the gathering, auction, university of michigan, uofm, convention, miniatures, historicals, board games, card games";
$config['gcs']['meta']['description'] = "Affordable gaming convention in Ann Arbor, MI featuring a large variety of games, exhibitors hall and auction. Games include Role-playing, RPGA, CCGs, board, miniatures, historicals, card games.";

/** Payment configuration */
$config['gcs']['payments']['paypalEmail'] = "ucon-paypal@ucon-gaming.org";
$config['gcs']['payments']['checkPayable'] = "U-Con Gaming Club";
$config['gcs']['payments']['mailAddress'] = "U-Con Gaming Convention<br/>PO Box 130242<br/>Ann Arbor, MI 48113-0242";


//
// PHPMailer settings

// From line of email
$config['phpmailer']['email'] = '';
$config['phpmailer']['fromname'] = 'Customer service';

// Mail host username and password
$config['phpmailer']['username'] = '';
$config['phpmailer']['password'] = '';

// Mail host connection information
$config['phpmailer']['host'] = '';
$config['phpmailer']['port'] = 465;
$config['phpmailer']['smtpsecure'] = 'ssl';
$config['phpmailer']['smtpauth'] = true;

