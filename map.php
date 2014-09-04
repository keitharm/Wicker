<?php
require_once("Wicker.php");
require_once("Scan.class.php");

$scan = Scan::fromDB(1);
$statement = $wicker->db->con()->prepare("SELECT * FROM `aps` WHERE `scan_id` = ? GROUP BY `latitude` ORDER BY `id`");
$statement->execute(array(1));

$a = 0;
while ($info = $statement->fetchObject()) {
    $coords[$a]["lat"]  = $info->latitude;
    $coords[$a]["long"] = $info->longitude;
    $array_lat[]        = $info->latitude;
    $array_long[]       = $info->longitude;
    $a++;
}
$avg_lat  = array_avg($array_lat);
$avg_long = array_avg($array_long);

$coord_data = <<<COORD
var latlng = new google.maps.LatLng({$avg_lat}, {$avg_long});
var myOptions = {
    zoom: 14,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
};
var map = new google.maps.Map(document.getElementById("map_canvas"),
    myOptions);
COORD;
$coord_data .= "\n";

$a = 0;
$multiple = true;
foreach ($coords as $coord) {

    $statement = $wicker->db->con()->query("SELECT * FROM `aps` WHERE `scan_id` = 1 AND `essid` <> ' ' AND `latitude` = " . $coord['lat'] . " AND `longitude` = " . $coord['long']);
    while ($info = $statement->fetchObject()) {
    if (!$multiple) {
        $lat   = $info->latitude;
        $long  = $info->longitude;
    } else {
        $lat  = spread($info->latitude);
        $long = spread($info->longitude);
    }
        
        $essid = htmlspecialchars($info->essid, ENT_QUOTES);
        $a++;
$coord_data .= <<<COORD
var point{$a} = new google.maps.LatLng({$lat}, {$long});
var marker{$a} = new google.maps.Marker({
    position:point{$a},
    map:map,
    title:'{$essid}',
    draggable:false,
})
COORD;
        $coord_data .= "\n";
    }
}
echo $coord_data;

function array_avg($array) {
    return round(array_sum($array)/count($array), 7);
}

function array_med($array) {
    sort($array);
    $count = count($array); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $array[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $array[$middleval];
        $high = $array[$middleval+1];
        $median = (($low+$high)/2);
    }
    return $median;
}

function spread($val) {
    (double)$val += (double)(randSign() . ".000" . mt_rand(0, 1000));
    return $val;
}

function randSign() {
    if (mt_rand(0, 1)) {
        return null;
    }
    return "-";
}
?>
