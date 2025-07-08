<?php

date_default_timezone_set('Europe/London');


$api_key = "89a080bd7dc2ab27f1a258b55941616e";
$username = "DayTray";


$size = "medium";         
$bgcolor = "ffffff";      
$autorefresh = true;      
$color = "red";           
$delimiter = ($size == "tall") ? "<br>" : " &nbsp; "; 

// Send correct headers
header('X-Frame-Options: GOFORIT'); 
header('Content-Type: text/html; charset=utf-8');

// Load the track fetcher class
include __DIR__ . "/class.lastfm-nowplaying.php";

// Fetch track info
try {
    $np = new lastfm_nowplaying($api_key, $size);
    $track = $np->info($username);
} catch (Exception $e) {
    echo "Widget Error: " . $e->getMessage();
}
?>
