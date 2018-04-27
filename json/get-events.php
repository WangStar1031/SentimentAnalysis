<?php

require_once('../config/config.php');
require(DOCUMENT_ROOT . '/class/connection.class.php');
require(DOCUMENT_ROOT . '/class/sql.class.php');
//$objConnection = new connection();
//$objConnection->sec_session_start();
$objSql = new Sql();
$start = substr($_GET['start'], 0, 10);
$end = substr($_GET['end'], 0, 10);
//echo $objSql->Query('2','WHERE DataOraSchedulazione > '.$start.' OR DataOraSchedulazione < '.$end .' ORDER BY DataOraSchedulazione ASC');
$input_arrays = $objSql->SelectArray($objSql->Query('2', 'WHERE InizioElaborazione > ' . $start . ' OR InizioElaborazione < ' . $end . ' ORDER BY InizioElaborazione ASC'));

//--------------------------------------------------------------------------------------------------
// This script reads event data from a JSON file and outputs those events which are within the range
// supplied by the "start" and "end" GET parameters.
//
// An optional "timezone" GET parameter will force all ISO8601 date stings to a given timezone.
//
// Requires PHP 5.2.0 or higher.
//--------------------------------------------------------------------------------------------------
// Require our Event class and datetime utilities
require ('../json/utilis.php');
//require dirname(__FILE__) . '/utils.php';
// Short-circuit if the client did not give us a date range.
/*
  if (!isset($_GET['start']) || !isset($_GET['end'])) {
  die("Please provide a date range.");
  }
 */
// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timezone, like "2013-12-29".
// Since no timezone will be present, they will parsed as UTC.
$range_start = parseDateTime($_GET['start']);
$range_end = parseDateTime($_GET['end']);

// Parse the timezone parameter if it is present.
$timezone = null;
if (isset($_GET['timezone'])) {
    $timezone = new DateTimeZone($_GET['timezone']);
}

// Read and parse our events JSON file into an array of event data arrays.
//$json = file_get_contents(dirname(__FILE__) . '../json/event.json');
//$json = file_get_contents('../json/event.json');
//$input_arrays = json_decode($json, true);
//print_r($input_arrays);
// Accumulate an output array of event data arrays.
$output_arrays = array();
foreach ($input_arrays as $array) {
    if ($array <> '') {
        // Convert the input array into a useful Event object
        $event = new Event($array, $timezone);

        // If the event is in-bounds, add it to the output
        if ($event->isWithinDayRange($range_start, $range_end)) {
            $output_arrays[] = $event->toArray();
        }
    }
}

// Send JSON to the client.
echo json_encode($output_arrays);
